<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception as PHPMailerException;
use App\Models\NotificationLog; // To log email attempts

class EmailService {
    private $mailer;
    private $config;
    private $notificationLogModel;
    private $dbConnection; // For NotificationLog model

    const TYPE_EMAIL = 1;

    public function __construct($config, $dbConnection) {
        $this->config = $config;
        $this->dbConnection = $dbConnection; // Store DB connection for the log model

        $this->mailer = new PHPMailer(true); // true enables exceptions

        // Server settings from config/config.php (which should eventually pull from system_configs table)
        $emailConfig = $this->config['email'] ?? [];
        
        if (empty($emailConfig['host']) || empty($emailConfig['username']) || empty($emailConfig['password'])) {
            error_log("EmailService: SMTP configuration is incomplete. Email sending will likely fail.");
            // Not throwing an exception here to allow the rest of the app to function if email is not critical path
            // but logging is important.
        }

        try {
            // $this->mailer->SMTPDebug = SMTP::DEBUG_SERVER; // Enable verbose debug output for development
            $this->mailer->isSMTP();
            $this->mailer->Host       = $emailConfig['host'] ?? 'smtp.qq.com';
            $this->mailer->SMTPAuth   = true;
            $this->mailer->Username   = $emailConfig['username'] ?? ''; // Your QQ email or other SMTP username
            $this->mailer->Password   = $emailConfig['password'] ?? ''; // Your QQ SMTP password or other SMTP password
            $this->mailer->SMTPSecure = isset($emailConfig['encryption']) ? $emailConfig['encryption'] : PHPMailer::ENCRYPTION_SMTPS; // ssl or tls
            $this->mailer->Port       = $emailConfig['port'] ?? 465; // 465 for SSL, 587 for TLS
            $this->mailer->CharSet    = PHPMailer::CHARSET_UTF8;

            // Sender
            $fromAddress = $emailConfig['from_address'] ?? $emailConfig['username'];
            $fromName = $emailConfig['from_name'] ?? 'Link Detection System';
            $this->mailer->setFrom($fromAddress, $fromName);

        } catch (PHPMailerException $e) {
            error_log("EmailService PHPMailer Configuration Error: {$this->mailer->ErrorInfo}");
        }
        
        // Initialize NotificationLog model here
        $this->notificationLogModel = new NotificationLog($this->config); // Pass app config
        $this->notificationLogModel->db = $this->dbConnection; // Set the db connection
    }

    public function sendEmail($toEmail, $subject, $htmlBody, $userId, $detectionRecordId = null) {
        $logData = [
            'user_id' => $userId,
            'type' => self::TYPE_EMAIL,
            'content' => "Subject: {$subject}\nBody: {$htmlBody}", // Store a summary or full content
            'status' => 0, // Default to failure
            'error_message' => null,
            'detection_record_id' => $detectionRecordId
        ];

        if (empty($this->config['email']['host'])) { // Check if SMTP is configured
            $logData['error_message'] = 'Email sending skipped: SMTP host not configured.';
            $this->notificationLogModel->create($logData);
            error_log($logData['error_message']);
            return false;
        }
        
        try {
            $this->mailer->clearAddresses(); // Clear previous recipient
            $this->mailer->addAddress($toEmail);

            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body    = $htmlBody;
            $this->mailer->AltBody = strip_tags($htmlBody); // Plain text version

            $this->mailer->send();
            
            $logData['status'] = 1; // Success
            $this->notificationLogModel->create($logData);
            return true;
        } catch (PHPMailerException $e) {
            $logData['error_message'] = "Message could not be sent. Mailer Error: {$this->mailer->ErrorInfo}";
            $this->notificationLogModel->create($logData);
            error_log("Email sending failed to {$toEmail}: {$this->mailer->ErrorInfo}");
            return false;
        }
    }
}
