<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($data['pageTitle'] ?? '用户管理'); ?> - 链接检测管理系统</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css"> <!-- Assuming a basic style.css -->
    <style>
        /* Basic table styling - can be moved to style.css */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .actions a { margin-right: 5px; text-decoration: none; }
    </style>
</head>
<body>
    <?php 
    // A simple way to include a shared header, or manage this via a layout system later
    // For now, let's assume a simple header similar to home/index.php
    // Or include a dedicated header partial if created: include BASE_PATH . '/app/views/layouts/header.php'; 
    ?>
    <header>
        <h1>链接检测管理系统</h1>
    </header>
    <nav>
        <a href="<?php echo BASE_URL; ?>/index.php?controller=home&action=index">仪表盘</a>
        <a href="<?php echo BASE_URL; ?>/index.php?controller=project&action=index">项目管理</a>
        <a href="<?php echo BASE_URL; ?>/index.php?controller=link&action=index">链接管理</a>
        <a href="<?php echo BASE_URL; ?>/index.php?controller=user&action=index" class="active">用户管理</a>
        <a href="<?php echo BASE_URL; ?>/index.php?controller=role&action=index">角色管理</a>
        <a href="<?php echo BASE_URL; ?>/index.php?controller=permission&action=index">权限管理</a>
        <a href="<?php echo BASE_URL; ?>/index.php?controller=auth&action=logout" style="float:right;">退出登录</a>
    </nav>

    <div class="container">
        <h2><?php echo htmlspecialchars($data['pageTitle'] ?? '用户管理'); ?></h2>
        <!-- Add button for 'Create User' later -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>用户名</th>
                    <th>邮箱</th>
                    <th>电话</th>
                    <th>状态</th>
                    <th>最后登录</th>
                    <th>注册时间</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data['users'])): ?>
                    <?php foreach ($data['users'] as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['id']); ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></td>
                            <td><?php echo $user['status'] ? '启用' : '禁用'; ?></td>
                            <td><?php echo htmlspecialchars($user['last_login'] ?? '从未'); ?></td>
                            <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                            <td class="actions">
                                <a href="<?php echo BASE_URL; ?>/index.php?controller=user&action=show&id=<?php echo $user['id']; ?>">查看</a>
                                <!-- Add Edit/Delete links later -->
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align:center;">暂无用户数据</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php 
    // Simple footer: include BASE_PATH . '/app/views/layouts/footer.php'; 
    ?>
    <div class="footer" style="text-align: center; padding: 10px; background-color: #ddd; position: fixed; bottom: 0; width: 100%;">
        <p>&copy; <?php echo date('Y'); ?> 链接检测管理系统</p>
    </div>
</body>
</html>
