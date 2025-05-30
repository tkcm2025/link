<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($data['pageTitle'] ?? '权限管理'); ?> - 链接检测管理系统</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
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
        <h2><?php echo htmlspecialchars($data['pageTitle'] ?? '权限管理'); ?></h2>
        <!-- Add button for 'Create Permission' later if manual creation is allowed -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>权限名称</th>
                    <th>权限代码 (Code)</th>
                    <th>描述</th>
                    <th>创建时间</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data['permissions'])): ?>
                    <?php foreach ($data['permissions'] as $permission): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($permission['id']); ?></td>
                            <td><?php echo htmlspecialchars($permission['name']); ?></td>
                            <td><?php echo htmlspecialchars($permission['code']); ?></td>
                            <td><?php echo htmlspecialchars($permission['description'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($permission['created_at']); ?></td>
                            <td class="actions">
                                <a href="<?php echo BASE_URL; ?>/index.php?controller=permission&action=show&id=<?php echo $permission['id']; ?>" class="view">查看</a>
                                <!-- Permissions are often system-defined, so Edit/Delete might be restricted -->
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align:center;">暂无权限数据</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="footer">
        <p>&copy; <?php echo date('Y'); ?> 链接检测管理系统</p>
    </div>
</body>
</html>
