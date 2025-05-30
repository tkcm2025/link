<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($data['pageTitle'] ?? '项目管理'); ?> - 链接检测管理系统</title>
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
        <a href="<?php echo BASE_URL; ?>/index.php?controller=permission&action=index">权限管理</a>
        <a href="<?php echo BASE_URL; ?>/index.php?controller=project&action=index" class="active">项目管理</a>
        <a href="<?php echo BASE_URL; ?>/index.php?controller=setting&action=index">系统配置</a>
        <a href="<?php echo BASE_URL; ?>/index.php?controller=auth&action=logout" style="float:right;">退出登录</a>
    </nav>

    <div class="container">
        <h2><?php echo htmlspecialchars($data['pageTitle'] ?? '项目管理'); ?></h2>
        <!-- Add button for 'Create Project' later -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>项目名称</th>
                    <th>描述</th>
                    <th>状态</th>
                    <th>创建人</th>
                    <th>创建时间</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data['projects'])): ?>
                    <?php foreach ($data['projects'] as $project): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($project['id']); ?></td>
                            <td><?php echo htmlspecialchars($project['name']); ?></td>
                            <td><?php echo htmlspecialchars($project['description'] ?? 'N/A'); ?></td>
                            <td><?php echo $project['status'] ? '启用' : '禁用'; ?></td>
                            <td><?php echo htmlspecialchars($project['created_by_username']); ?></td>
                            <td><?php echo htmlspecialchars($project['created_at']); ?></td>
                            <td class="actions">
                                <a href="<?php echo BASE_URL; ?>/index.php?controller=project&action=show&id=<?php echo $project['id']; ?>" class="view">查看</a>
                                <!-- Add Edit/Delete links later -->
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align:center;">暂无项目数据</td>
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
