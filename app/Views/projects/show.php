<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($data['pageTitle'] ?? '查看项目'); ?> - 链接检测管理系统</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <style>
        .details-section { background-color: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); margin-top: 20px;}
        .details-section p { margin-bottom: 10px; }
        .details-section strong { display: inline-block; width: 120px; }
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
        <a href="<?php echo BASE_URL; ?>/index.php?controller=permission&action=index">权限管理</a>
        <a href="<?php echo BASE_URL; ?>/index.php?controller=project&action=index" class="active">项目管理</a>
        <a href="<?php echo BASE_URL; ?>/index.php?controller=auth&action=logout" style="float:right;">退出登录</a>
    </nav>

    <div class="container">
        <h2><?php echo htmlspecialchars($data['pageTitle'] ?? '查看项目'); ?></h2>
        <div class="details-section">
            <p><strong>ID:</strong> <?php echo htmlspecialchars($data['project']['id']); ?></p>
            <p><strong>项目名称:</strong> <?php echo htmlspecialchars($data['project']['name']); ?></p>
            <p><strong>描述:</strong> <?php echo nl2br(htmlspecialchars($data['project']['description'] ?? 'N/A')); ?></p>
            <p><strong>状态:</strong> <?php echo $data['project']['status'] ? '启用' : '禁用'; ?></p>
            <p><strong>创建人:</strong> <?php echo htmlspecialchars($data['project']['created_by_username']); ?> (ID: <?php echo htmlspecialchars($data['project']['created_by']); ?>)</p>
            <p><strong>创建时间:</strong> <?php echo htmlspecialchars($data['project']['created_at']); ?></p>
            <p><strong>最后更新时间:</strong> <?php echo htmlspecialchars($data['project']['updated_at']); ?></p>
            <br>
            <!-- Placeholder for associated links list -->
            <h3>项目内链接:</h3>
                <?php if (!empty($data['links'])): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>链接名称</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['links'] as $link): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($link['id']); ?></td>
                            <td><?php echo htmlspecialchars($link['name']); ?></td>
                            <td><?php echo $link['status'] ? '启用' : '禁用'; ?></td>
                            <td class="actions">
                                <a href="<?php echo BASE_URL; ?>/index.php?controller=link&action=show&id=<?php echo $link['id']; ?>" class="view">查看</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>此项目下暂无链接。</p>
        <?php endif; ?>
        <p><a href="<?php echo BASE_URL; ?>/index.php?controller=link&action=index&project_id=<?php echo $data['project']['id']; ?>">管理此项目下的所有链接</a></p>
            <br>
            <a href="<?php echo BASE_URL; ?>/index.php?controller=project&action=index">返回项目列表</a>
            <!-- Add Edit link later -->
        </div>
    </div>
    <div class="footer">
        <p>&copy; <?php echo date('Y'); ?> 链接检测管理系统</p>
    </div>
</body>
</html>
