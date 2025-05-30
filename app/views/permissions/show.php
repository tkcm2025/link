<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($data['pageTitle'] ?? '查看权限'); ?> - 链接检测管理系统</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
     <style>
        .details-section { background-color: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); margin-top: 20px;}
        .details-section p { margin-bottom: 10px; }
        .details-section strong { display: inline-block; width: 150px; }
    </style>
</head>
<body>
    <header>
        <h1>链接检测管理系统</h1>
    </header>
    <nav>
        <a href="<?php echo BASE_URL; ?>/index.php?controller=home&action=index">仪表盘</a>
        <a href="<?php echo BASE_URL; ?>/index.php?controller=user&action=index">用户管理</a>
        <a href="<?php echo BASE_URL; ?>/index.php?controller=role&action=index">角色管理</a>
        <a href="<?php echo BASE_URL; ?>/index.php?controller=permission&action=index" class="active">权限管理</a>
        <a href="<?php echo BASE_URL; ?>/index.php?controller=project&action=index">项目管理</a>
        <a href="<?php echo BASE_URL; ?>/index.php?controller=auth&action=logout" style="float:right;">退出登录</a>
    </nav>

    <div class="container">
        <h2><?php echo htmlspecialchars($data['pageTitle'] ?? '查看权限'); ?></h2>
        <div class="details-section">
            <p><strong>ID:</strong> <?php echo htmlspecialchars($data['permission']['id']); ?></p>
            <p><strong>权限名称:</strong> <?php echo htmlspecialchars($data['permission']['name']); ?></p>
            <p><strong>权限代码 (Code):</strong> <?php echo htmlspecialchars($data['permission']['code']); ?></p>
            <p><strong>描述:</strong> <?php echo htmlspecialchars($data['permission']['description'] ?? 'N/A'); ?></p>
            <p><strong>创建时间:</strong> <?php echo htmlspecialchars($data['permission']['created_at']); ?></p>
            <br>
            <a href="<?php echo BASE_URL; ?>/index.php?controller=permission&action=index">返回权限列表</a>
        </div>
    </div>
    <div class="footer">
        <p>&copy; <?php echo date('Y'); ?> 链接检测管理系统</p>
    </div>
</body>
</html>
