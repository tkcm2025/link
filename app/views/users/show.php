<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($data['pageTitle'] ?? '查看用户'); ?> - 链接检测管理系统</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <style>
        .user-details { background-color: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); margin-top: 20px;}
        .user-details p { margin-bottom: 10px; }
        .user-details strong { display: inline-block; width: 120px; }
    </style>
</head>
<body>
    <?php // include BASE_PATH . '/app/views/layouts/header.php'; ?>
    <header>
        <h1>链接检测管理系统</h1>
    </header>
    <nav>
        <a href="<?php echo BASE_URL; ?>/index.php?controller=home&action=index">仪表盘</a>
        <a href="<?php echo BASE_URL; ?>/index.php?controller=user&action=index">用户管理</a>
        <a href="<?php echo BASE_URL; ?>/index.php?controller=auth&action=logout" style="float:right;">退出登录</a>
    </nav>

    <div class="container">
        <h2><?php echo htmlspecialchars($data['pageTitle'] ?? '查看用户'); ?></h2>
        <div class="user-details">
            <p><strong>ID:</strong> <?php echo htmlspecialchars($data['user']['id']); ?></p>
            <p><strong>用户名:</strong> <?php echo htmlspecialchars($data['user']['username']); ?></p>
            <p><strong>邮箱:</strong> <?php echo htmlspecialchars($data['user']['email']); ?></p>
            <p><strong>电话:</strong> <?php echo htmlspecialchars($data['user']['phone'] ?? 'N/A'); ?></p>
            <p><strong>微信ID:</strong> <?php echo htmlspecialchars($data['user']['wechat_id'] ?? 'N/A'); ?></p>
            <p><strong>状态:</strong> <?php echo $data['user']['status'] ? '启用' : '禁用'; ?></p>
            <p><strong>最后登录时间:</strong> <?php echo htmlspecialchars($data['user']['last_login'] ?? '从未'); ?></p>
            <p><strong>注册时间:</strong> <?php echo htmlspecialchars($data['user']['created_at']); ?></p>
            <br>
            <a href="<?php echo BASE_URL; ?>/index.php?controller=user&action=index">返回用户列表</a>
            <!-- Add Edit link later -->
        </div>
    </div>

    <?php // include BASE_PATH . '/app/views/layouts/footer.php'; ?>
     <div class="footer" style="text-align: center; padding: 10px; background-color: #ddd; position: fixed; bottom: 0; width: 100%;">
        <p>&copy; <?php echo date('Y'); ?> 链接检测管理系统</p>
    </div>
</body>
</html>
