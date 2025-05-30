<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($data['pageTitle'] ?? '系统配置'); ?> - 链接检测管理系统</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <style>
        .settings-form .form-group { margin-bottom: 20px; }
        .settings-form label { font-weight: bold; display: block; margin-bottom: 5px; }
        .settings-form input[type="text"],
        .settings-form input[type="number"],
        .settings-form input[type="password"],
        .settings-form select,
        .settings-form textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .settings-form textarea { min-height: 100px; }
        .settings-form .description { font-size: 0.9em; color: #666; margin-top: 5px;}
        .settings-form fieldset { border: 1px solid #ddd; padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .settings-form legend { font-weight: bold; padding: 0 10px; }
    </style>
</head>
<body>
    <header><h1>链接检测管理系统</h1></header>
    <nav>
        <a href="<?php echo BASE_URL; ?>/index.php?controller=home&action=index">仪表盘</a>
        <a href="<?php echo BASE_URL; ?>/index.php?controller=project&action=index">项目管理</a>
        <a href="<?php echo BASE_URL; ?>/index.php?controller=link&action=index">链接管理</a>
        <a href="<?php echo BASE_URL; ?>/index.php?controller=user&action=index">用户管理</a>
        <a href="<?php echo BASE_URL; ?>/index.php?controller=role&action=index">角色管理</a>
        <a href="<?php echo BASE_URL; ?>/index.php?controller=permission&action=index">权限管理</a>
        <a href="<?php echo BASE_URL; ?>/index.php?controller=setting&action=index" class="active">系统配置</a>
        <a href="<?php echo BASE_URL; ?>/index.php?controller=auth&action=logout" style="float:right;">退出登录</a>
    </nav>

    <div class="container">
        <h2><?php echo htmlspecialchars($data['pageTitle'] ?? '系统配置'); ?></h2>

        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['flash_message']['type']; ?>">
                <?php echo htmlspecialchars($_SESSION['flash_message']['text']); unset($_SESSION['flash_message']); ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>/index.php?controller=setting&action=save" method="POST" class="settings-form">
            <?php $s = $data['settings']; // shortcut ?>

            <div class="form-group">
                <label for="boce_api_key">Boce.com API密钥 (boce_api_key)</label>
                <input type="text" id="boce_api_key" name="boce_api_key" value="<?php echo htmlspecialchars($s['boce_api_key'] ?? ''); ?>">
                <p class="description"><?php echo htmlspecialchars($s['boce_api_key_description'] ?? '用于微信链接检测的API Key。'); ?></p>
            </div>

            <fieldset>
                <legend>邮件接口配置 (email_config)</legend>
                <?php $ec = is_array($s['email_config']) ? $s['email_config'] : json_decode($s['email_config'] ?? '{}', true); ?>
                <div class="form-group">
                    <label for="email_config_host">SMTP 服务器地址</label>
                    <input type="text" id="email_config_host" name="email_config_host" value="<?php echo htmlspecialchars($ec['host'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="email_config_port">SMTP 端口</label>
                    <input type="number" id="email_config_port" name="email_config_port" value="<?php echo htmlspecialchars($ec['port'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="email_config_username">SMTP 用户名</label>
                    <input type="text" id="email_config_username" name="email_config_username" value="<?php echo htmlspecialchars($ec['username'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="email_config_password">SMTP 密码 (留空表示不修改)</label>
                    <input type="password" id="email_config_password" name="email_config_password" value="">
                </div>
                 <div class="form-group">
                    <label for="email_config_encryption">加密方式 (ssl or tls)</label>
                    <select id="email_config_encryption" name="email_config_encryption">
                        <option value="ssl" <?php echo (($ec['encryption'] ?? 'ssl') === 'ssl') ? 'selected' : ''; ?>>SSL</option>
                        <option value="tls" <?php echo (($ec['encryption'] ?? '') === 'tls') ? 'selected' : ''; ?>>TLS</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="email_config_from">发件人邮箱</label>
                    <input type="text" id="email_config_from" name="email_config_from" value="<?php echo htmlspecialchars($ec['from_address'] ?? $ec['from'] ?? ''); ?>">
                </div>
                 <div class="form-group">
                    <label for="email_config_from_name">发件人名称</label>
                    <input type="text" id="email_config_from_name" name="email_config_from_name" value="<?php echo htmlspecialchars($ec['from_name'] ?? 'Link Detection System'); ?>">
                </div>
            </fieldset>

            <div class="form-group">
                <label for="sms_config">短信接口配置 (sms_config) <small>- JSON格式</small></label>
                <textarea id="sms_config" name="sms_config"><?php echo htmlspecialchars(is_array($s['sms_config']) ? json_encode($s['sms_config'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : ($s['sms_config'] ?? '')); ?></textarea>
                <p class="description"><?php echo htmlspecialchars($s['sms_config_description'] ?? '阿里云等短信服务商的配置信息。'); ?></p>
            </div>

            <div class="form-group">
                <label for="wechat_config">微信通知配置 (wechat_config) <small>- JSON格式</small></label>
                <textarea id="wechat_config" name="wechat_config"><?php echo htmlspecialchars(is_array($s['wechat_config']) ? json_encode($s['wechat_config'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : ($s['wechat_config'] ?? '')); ?></textarea>
                <p class="description"><?php echo htmlspecialchars($s['wechat_config_description'] ?? '微信公众号通知接口的配置信息。'); ?></p>
            </div>
            
            <div class="form-group">
                <label for="detection_frequency">检测频率(秒) (detection_frequency)</label>
                <input type="number" id="detection_frequency" name="detection_frequency" value="<?php echo htmlspecialchars($s['detection_frequency'] ?? '3600'); ?>">
                <p class="description"><?php echo htmlspecialchars($s['detection_frequency_description'] ?? '自动检测任务的频率，单位为秒。'); ?></p>
            </div>

            <div class="form-group">
                <label for="theme_color">主题颜色 (theme_color)</label>
                <input type="text" id="theme_color" name="theme_color" value="<?php echo htmlspecialchars($s['theme_color'] ?? 'blue'); ?>">
                <p class="description"><?php echo htmlspecialchars($s['theme_color_description'] ?? '系统后台的主题颜色。'); ?></p>
            </div>

            <button type="submit">保存配置</button>
        </form>
    </div>
    <div class="footer"><p>&copy; <?php echo date('Y'); ?> 链接检测管理系统</p></div>
</body>
</html>
