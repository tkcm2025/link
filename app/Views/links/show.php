<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($data['pageTitle'] ?? '查看链接'); ?> - 链接检测管理系统</title>
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
        <a href="<?php echo BASE_URL; ?>/index.php?controller=project&action=index">项目管理</a>
        <a href="<?php echo BASE_URL; ?>/index.php?controller=link&action=index" class="active">链接管理</a>
        <a href="<?php echo BASE_URL; ?>/index.php?controller=user&action=index">用户管理</a>
        <a href="<?php echo BASE_URL; ?>/index.php?controller=role&action=index">角色管理</a>
        <a href="<?php echo BASE_URL; ?>/index.php?controller=permission&action=index">权限管理</a>
        <a href="<?php echo BASE_URL; ?>/index.php?controller=setting&action=index">系统配置</a>
        <a href="<?php echo BASE_URL; ?>/index.php?controller=auth&action=logout" style="float:right;">退出登录</a>
    </nav>

    <div class="container">
        <h2><?php echo htmlspecialchars($data['pageTitle'] ?? '查看链接'); ?></h2>
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($_SESSION['flash_message']); unset($_SESSION['flash_message']); ?></div>
        <?php endif; ?>
        <div class="details-section">
            <p><strong>ID:</strong> <?php echo htmlspecialchars($data['link']['id']); ?></p>
            <p><strong>链接名称:</strong> <?php echo htmlspecialchars($data['link']['name']); ?></p>
            <p><strong>所属项目:</strong> <a href="<?php echo BASE_URL; ?>/index.php?controller=project&action=show&id=<?php echo $data['link']['project_id']; ?>"><?php echo htmlspecialchars($data['link']['project_name']); ?></a></p>
            <p><strong>审核链接:</strong> <a href="<?php echo htmlspecialchars($data['link']['review_url']); ?>" target="_blank"><?php echo htmlspecialchars($data['link']['review_url']); ?></a></p>
            <p><strong>落地页链接:</strong> <a href="<?php echo htmlspecialchars($data['link']['landing_url']); ?>" target="_blank"><?php echo htmlspecialchars($data['link']['landing_url']); ?></a></p>
            <p><strong>状态:</strong> <?php echo $data['link']['status'] ? '启用' : '禁用'; ?></p>
            <p><strong>最后检测时间:</strong> <?php echo htmlspecialchars($data['link']['last_check_time'] ?? '从未'); ?></p>
            <p><strong>创建人:</strong> <?php echo htmlspecialchars($data['link']['created_by_username']); ?> (ID: <?php echo htmlspecialchars($data['link']['created_by']); ?>)</p>
            <p><strong>创建时间:</strong> <?php echo htmlspecialchars($data['link']['created_at']); ?></p>
            <p><strong>最后更新时间:</strong> <?php echo htmlspecialchars($data['link']['updated_at']); ?></p>
            
            <p><a href="<?php echo BASE_URL; ?>/index.php?controller=link&action=check&id=<?php echo $data['link']['id']; ?>" class="button-like">立即检测此链接</a></p>
            
            <br>
            <h3>最近检测记录:</h3>
        <?php 
        // $records would be passed from controller if loaded
        // For now, let's simulate or assume it's not loaded yet for this step
        // $detectionRecordModel = new \App\Models\DetectionRecord($GLOBALS['config']);
        // $records = $detectionRecordModel->findByLinkId($data['link']['id']);
        // For now, we'll just show a placeholder until records are actively loaded and passed to this view.
        if (!empty($data['records'])): 
        ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>类型</th>
                        <th>URL</th>
                        <th>状态码</th>
                        <th>检测时间</th>
                        <th>API响应 (部分)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['records'] as $record): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($record['id']); ?></td>
                            <td><?php echo ($record['type'] == 1) ? '审核链接' : '落地页'; ?></td>
                            <td><?php echo htmlspecialchars(substr($record['url'], 0, 50)) . (strlen($record['url']) > 50 ? '...' : ''); ?></td>
                            <td><?php echo htmlspecialchars($record['error_code']); ?></td>
                            <td><?php echo htmlspecialchars($record['created_at']); ?></td>
                            <td><pre><?php echo htmlspecialchars(substr($record['api_response'], 0, 100)); ?>...</pre></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>暂无检测记录。</p>
        <?php endif; ?>
            <br>
            <a href="<?php echo BASE_URL; ?>/index.php?controller=link&action=index<?php echo $data['link']['project_id'] ? '&project_id=' . $data['link']['project_id'] : ''; ?>">返回链接列表</a>
            <!-- Add Edit link later -->
        </div>
    </div>
    <div class="footer">
        <p>&copy; <?php echo date('Y'); ?> 链接检测管理系统</p>
    </div>
</body>
</html>
