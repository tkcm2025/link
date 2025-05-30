<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($data['pageTitle']) ? htmlspecialchars($data['pageTitle']) : '仪表盘'; ?> - 链接检测管理系统</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f4; }
        header { background-color: #007bff; color: white; padding: 10px 20px; text-align: center; }
        nav { background-color: #333; padding: 10px; text-align: center;}
        nav a { color: white; margin: 0 15px; text-decoration: none; }
        nav a:hover { text-decoration: underline; }
        .container { padding: 20px; }
        .footer { text-align: center; padding: 10px; background-color: #ddd; position: fixed; bottom: 0; width: 100%;}
    </style>
</head>
<body>
    <header>
        <h1>链接检测管理系统</h1>
    </header>
    <nav>
            <a href="<?php echo BASE_URL; ?>/index.php?controller=home&action=index" class="active">仪表盘</a>
            <a href="<?php echo BASE_URL; ?>/index.php?controller=project&action=index">项目管理</a>
            <a href="<?php echo BASE_URL; ?>/index.php?controller=link&action=index">链接管理</a>
            <a href="<?php echo BASE_URL; ?>/index.php?controller=user&action=index">用户管理</a>
            <a href="<?php echo BASE_URL; ?>/index.php?controller=role&action=index">角色管理</a>
            <a href="<?php echo BASE_URL; ?>/index.php?controller=permission&action=index">权限管理</a>
            <a href="<?php echo BASE_URL; ?>/index.php?controller=setting&action=index">系统配置</a>
            <a href="<?php echo BASE_URL; ?>/index.php?controller=auth&action=logout" style="float:right;">退出登录</a>
    </nav>
    <div class="container">
        <h2><?php echo isset($data['pageTitle']) ? htmlspecialchars($data['pageTitle']) : '仪表盘'; ?></h2>
        <p><?php echo isset($data['welcomeMessage']) ? htmlspecialchars($data['welcomeMessage']) : '欢迎使用系统!'; ?></p>
        <!-- Dashboard content will go here -->
    </div>
    <div class="footer">
        <p>&copy; <?php echo date('Y'); ?> 链接检测管理系统</p>
    </div>
</body>
</html>
