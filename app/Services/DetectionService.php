<?php

namespace App\Services;

use App\Models\DetectionRecord;
use App\Models\Link;
use App\Models\User; // To get user email
use App\Models\NotificationConfig; // To get user notification preferences
use App\Services\EmailService; // To send emails

class DetectionService {
    private $config;
    private $boceApiKey;
    private $detectionRecordModel;
    private $linkModel;
    private $userModel; // For fetching user details (email)
    private $notificationConfigModel; // For user preferences
    private $emailService; // For sending emails
    private $dbConnection; // To pass to services/models

    const API_ENDPOINT = 'https://api.boce.com/v3/task/create/wechat';
    const TYPE_REVIEW_URL = 1;
    const TYPE_LANDING_URL = 2;
    const DB_ERR_NORMAL = 1;
    const DB_ERR_BLOCKED = 2;
    const DB_ERR_DETECTION_FAILED = 3;
    const DB_ERR_NON_WECHAT_LINK = 4;
    const DB_ERR_API_CALL_FAILED = -1;

    public function __construct($config, $dbConnection) {
        $this->config = $config;
        $this->dbConnection = $dbConnection; // Store for passing to other services/models

        $this->boceApiKey = $this->config['api']['boce_api_key'] ?? (defined('BOCE_API_KEY') ? BOCE_API_KEY : null);
        if (empty($this->boceApiKey)) {
            error_log("Boce API Key is not configured in DetectionService.");
        }

        // Initialize models, passing dbConnection to their constructors (via config wrapper or directly)
        $this->detectionRecordModel = new DetectionRecord($config);
        $this->detectionRecordModel->db = $this->dbConnection;
        
        $this->linkModel = new Link($config);
        $this->linkModel->db = $this->dbConnection;

        $this->userModel = new User($config); // Assuming User model exists
        $this->userModel->db = $this->dbConnection;

        $this->notificationConfigModel = new NotificationConfig($config);
        $this->notificationConfigModel->db = $this->dbConnection;
        
        // Initialize EmailService
        $this->emailService = new EmailService($config, $this->dbConnection);
    }

    public function checkLink($linkId, $reviewUrl, $landingUrl, $linkName = 'N/A', $linkCreatorUserId = null) {
        if (empty($this->boceApiKey)) {
            error_log("Cannot perform detection: Boce API Key is missing.");
            return ['error' => 'Boce API Key not configured'];
        }

        $results = [];
        $detectionRecordReviewId = null;
        $detectionRecordLandingId = null;

        // 1. Check Review URL
        $reviewCheckResult = $this->performCheckInternal($linkId, $reviewUrl, self::TYPE_REVIEW_URL, $linkName, $linkCreatorUserId);
        $results['review_url_check'] = $reviewCheckResult['status_report'];
        if(isset($reviewCheckResult['detection_record_id'])) $detectionRecordReviewId = $reviewCheckResult['detection_record_id'];


        // 2. Check Landing URL
        $landingCheckResult = $this->performCheckInternal($linkId, $landingUrl, self::TYPE_LANDING_URL, $linkName, $linkCreatorUserId);
        $results['landing_url_check'] = $landingCheckResult['status_report'];
        if(isset($landingCheckResult['detection_record_id'])) $detectionRecordLandingId = $landingCheckResult['detection_record_id'];


        $this->linkModel->updateLastCheckTime($linkId);
        return $results;
    }

    private function performCheckInternal($linkId, $url, $type, $linkName, $linkCreatorUserId) {
        $statusReport = ['status' => 'skipped', 'message' => 'URL is empty.', 'db_error_code' => null];
        $detectionRecordId = null;

        if (empty($url)) {
            return ['status_report' => $statusReport, 'detection_record_id' => $detectionRecordId];
        }

        $host = parse_url($url, PHP_URL_HOST);
        if (!$host) {
            $detectionRecordId = $this->saveDetectionRecord($linkId, $type, $url, self::DB_ERR_API_CALL_FAILED, 'Invalid URL, cannot parse host.');
            $statusReport = ['status' => 'failed', 'message' => 'Invalid URL.', 'db_error_code' => self::DB_ERR_API_CALL_FAILED];
            return ['status_report' => $statusReport, 'detection_record_id' => $detectionRecordId];
        }

        $requestUrl = self::API_ENDPOINT . '?key=' . $this->boceApiKey . '&host=' . $host;
        $apiResponseJson = null;
        $dbErrorCode = self::DB_ERR_API_CALL_FAILED;

        try {
            $apiResponseJson = @file_get_contents($requestUrl);
            if ($apiResponseJson === false) throw new \Exception("Failed to fetch data from API: " . $requestUrl);
            $apiResponse = json_decode($apiResponseJson, true);
            if (json_last_error() !== JSON_ERROR_NONE) throw new \Exception("Failed to decode API JSON. Error: " . json_last_error_msg());

            if (isset($apiResponse['error_code'])) {
                if ($apiResponse['error_code'] == 0) {
                    if (isset($apiResponse['data'][0]['status'])) {
                        $domainStatus = $apiResponse['data'][0]['status'];
                        switch ($domainStatus) {
                            case 1: $dbErrorCode = self::DB_ERR_NORMAL; break;
                            case 2: $dbErrorCode = self::DB_ERR_BLOCKED; break;
                            case 3: $dbErrorCode = self::DB_ERR_DETECTION_FAILED; break;
                            case 4: $dbErrorCode = self::DB_ERR_NON_WECHAT_LINK; break;
                            default: $dbErrorCode = self::DB_ERR_API_CALL_FAILED; break;
                        }
                    } else { $dbErrorCode = self::DB_ERR_API_CALL_FAILED; error_log("Boce API Warning: 'data[0].status' missing for host: {$host}");}
                } else { $dbErrorCode = self::DB_ERR_API_CALL_FAILED; error_log("Boce API Error for host {$host}: Code {$apiResponse['error_code']} - {$apiResponse['error']}"); }
            } else { $dbErrorCode = self::DB_ERR_API_CALL_FAILED; error_log("Boce API Error: 'error_code' missing for host: {$host}");}
        } catch (\Exception $e) {
            error_log("Exception during Boce API call for host {$host}: " . $e->getMessage());
            $apiResponseJson = $apiResponseJson ?? $e->getMessage();
            $dbErrorCode = self::DB_ERR_API_CALL_FAILED;
        }

        $detectionRecordId = $this->saveDetectionRecord($linkId, $type, $url, $dbErrorCode, $apiResponseJson);
        $statusReport = ['status' => 'completed', 'db_error_code' => $dbErrorCode, 'api_response' => $apiResponseJson];

        // Trigger notification if needed
        if ($linkCreatorUserId && ($dbErrorCode == self::DB_ERR_BLOCKED || $dbErrorCode == self::DB_ERR_NON_WECHAT_LINK)) {
            $this->triggerNotification($linkCreatorUserId, $linkId, $linkName, $url, $type, $dbErrorCode, $detectionRecordId);
        }
        return ['status_report' => $statusReport, 'detection_record_id' => $detectionRecordId];
    }

    private function saveDetectionRecord($linkId, $type, $url, $dbErrorCode, $apiResponse) {
        $recordData = [
            'link_id' => $linkId,
            'type' => $type,
            'url' => $url,
            'error_code' => $dbErrorCode,
            'api_response' => is_string($apiResponse) ? $apiResponse : json_encode($apiResponse, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        ];
        return $this->detectionRecordModel->create($recordData);
    }
    
    private function triggerNotification($userId, $linkId, $linkName, $checkedUrl, $linkType, $dbErrorCode, $detectionRecordId) {
        $user = $this->userModel->findById($userId); // Assumes findById exists and returns email
        $config = $this->notificationConfigModel->findByUserId($userId);

        if (!$user || empty($user['email'])) {
            error_log("Notification: User or user email not found for user_id: {$userId}");
            return;
        }

        $urlTypeName = ($linkType == self::TYPE_REVIEW_URL) ? '审核链接' : '落地页链接';
        $statusName = ($dbErrorCode == self::DB_ERR_BLOCKED) ? '域名被封' : '非微信官方链接';

        if ($config && $config['email_enabled']) {
            // Check scenario preferences (e.g., 'blocked_link', 'non_wechat_link')
            // For simplicity, let's assume a general 'link_issue' scenario for now
            $sendEmail = false;
            if (isset($config['scenarios_decoded']['link_blocked']) && ($dbErrorCode == self::DB_ERR_BLOCKED || $dbErrorCode == self::DB_ERR_NON_WECHAT_LINK) ) {
                 $sendEmail = $config['scenarios_decoded']['link_blocked']; // Example scenario key
            } else if (empty($config['scenarios_decoded'])) { 
                // If no specific scenarios, send for any configured notification type if enabled (legacy behavior)
                // Or default to not sending if scenarios are expected. For now, let's be explicit.
                // This part needs refinement based on how `scenarios` are structured.
                // Let's assume for now: if email_enabled is true, and it's a blocking error, we try to send.
                // The issue states: "当error_code为2或者4时，发送给用户发送邮件"
                // "通知配置表增加场景勾选存储" - this implies scenarios like "on_block", "on_api_fail" etc.
                // For now, if email_enabled = 1, we send for error_code 2 or 4.
                 $sendEmail = true; 
            }


            if ($sendEmail) {
                $subject = "链接检测系统通知：链接 '{$linkName}' 可能存在问题";
                $body = "<p>您好,</p>";
                $body .= "<p>系统检测到您的链接 '<strong>{$linkName}</strong>' ({$urlTypeName}) 存在问题:</p>";
                $body .= "<p><ul>";
                $body .= "<li>检测URL: " . htmlspecialchars($checkedUrl) . "</li>";
                $body .= "<li>状态: <strong>{$statusName} (Code: {$dbErrorCode})</strong></li>";
                $body .= "<li>检测时间: " . date('Y-m-d H:i:s') . "</li>";
                $body .= "</ul></p>";
                $body .= "<p>请登录系统查看详情。</p>";
                $body .= "<p>链接检测管理系统</p>";

                $this->emailService->sendEmail($user['email'], $subject, $body, $userId, $detectionRecordId);
            }
        }
        // Add SMS and WeChat notification logic here later
    }
}
