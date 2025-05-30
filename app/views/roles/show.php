<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($data['pageTitle'] ?? '查看角色'); ?> - 链接检测管理系统</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <style>
        .details-section { background-color: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); margin-top: 20px;}
        .details-section p { margin-bottom: 10px; }
        .details-section strong { display: inline-block; width: 120px; }
        .permissions-list { list-style-type: none; padding-left: 0; }
        .permissions-list li { background-color: #e9ecef; margin-bottom: 5px; padding: 8px; border-radius: 3px; }
    </style>
</head>
<body>
    <header>
        <h1>链接检测管理系统</h1>
    </header>
    <nav>
        <a href="<?php echo BASE_URL; ?>/index.php?controller=home&action=index">仪表盘</a>
        <a href="<?php echo BASE_URL; ?>/index.php?controller=user&action=index">用户管理</a>
        <a href="<?php echo BASE_URL; ?>/index.php?controller=role&action=index" class="active">角色管理</a>
        <a href="<?php echo BASE_URL; ?>/index.php?controller=permission&action=index">权限管理</a>
        <a href="<?php echo BASE_URL; ?>/index.php?controller=project&action=index">项目管理</a>
        <a href="<?php echo BASE_URL; ?>/index.php?controller=auth&action=logout" style="float:right;">退出登录</a>
    </nav>

    <div class="container">
        <h2><?php echo htmlspecialchars($data['pageTitle'] ?? '查看角色'); ?></h2>
        <div class="details-section">
            <p><strong>ID:</strong> <?php echo htmlspecialchars($data['role']['id']); ?></p>
            <p><strong>角色名称:</strong> <?php echo htmlspecialchars($data['role']['name']); ?></p>
            <p><strong>描述:</strong> <?php echo htmlspecialchars($data['role']['description'] ?? 'N/A'); ?></p>
            <p><strong>创建时间:</strong> <?php echo htmlspecialchars($data['role']['created_at']); ?></p>
            
            <h3>关联权限:</h3>
            <?php if (!empty($data['permissions'])): ?>
                <ul class="permissions-list">
                    <?php foreach ($data['permissions'] as $permission): ?>
                        <li><?php echo htmlspecialchars($permission['name']); ?> (<?php echo htmlspecialchars($permission['code']); ?>)</li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>此角色暂未关联任何权限。</p>
            <?php endif; ?>
            <br>
            <a href="<?php echo BASE_URL; ?>/index.php?controller=role&action=index">返回角色列表</a>
            <!-- Add Edit link later -->
        </div>
    </div>
    <div class="footer">
        <p>&copy; <?php echo date('Y'); ?> 链接检测管理系统</p>
    </div>
</body>
</html>
