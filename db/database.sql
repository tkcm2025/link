-- 链接检测管理系统数据库结构
-- 用户表
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL COMMENT '用户名',
  `password` varchar(255) NOT NULL COMMENT '密码',
  `email` varchar(100) NOT NULL COMMENT '邮箱',
  `phone` varchar(20) DEFAULT NULL COMMENT '电话',
  `wechat_id` varchar(50) DEFAULT NULL COMMENT '微信ID',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态：1启用，0禁用',
  `last_login` datetime DEFAULT NULL COMMENT '最后登录时间',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户表';

-- 权限表
CREATE TABLE `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '权限名称',
  `code` varchar(50) NOT NULL COMMENT '权限代码',
  `description` varchar(255) DEFAULT NULL COMMENT '描述',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='权限表';

-- 角色表
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '角色名称',
  `description` varchar(255) DEFAULT NULL COMMENT '描述',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='角色表';

-- 角色权限关联表
CREATE TABLE `role_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL COMMENT '角色ID',
  `permission_id` int(11) NOT NULL COMMENT '权限ID',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_permission` (`role_id`,`permission_id`),
  KEY `permission_id` (`permission_id`),
  CONSTRAINT `role_permission_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_permission_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='角色权限关联表';

-- 用户角色关联表
CREATE TABLE `user_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `role_id` int(11) NOT NULL COMMENT '角色ID',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_role` (`user_id`,`role_id`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `user_role_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_role_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户角色关联表';

-- 项目表
CREATE TABLE `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '项目名称',
  `description` varchar(255) DEFAULT NULL COMMENT '项目描述',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态：1启用，0禁用',
  `created_by` int(11) NOT NULL COMMENT '创建人ID',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='项目表';

-- 链接表
CREATE TABLE `links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL COMMENT '项目ID',
  `name` varchar(100) NOT NULL COMMENT '链接名称',
  `review_url` varchar(255) NOT NULL COMMENT '审核链接',
  `landing_url` varchar(255) NOT NULL COMMENT '落地页链接',
  `last_check_time` datetime DEFAULT NULL COMMENT '最后检测时间',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态：1启用，0禁用',
  `created_by` int(11) NOT NULL COMMENT '创建人ID',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `links_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `links_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='链接表';

-- 检测记录表
CREATE TABLE `detection_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `link_id` int(11) NOT NULL COMMENT '链接ID',
  `type` tinyint(1) NOT NULL COMMENT '类型：1审核链接，2落地页链接',
  `url` varchar(255) NOT NULL COMMENT '检测的URL',
  `error_code` int(11) NOT NULL COMMENT '错误代码：1正常,2拦截(域名被封),3检测失败,4拦截(非微信官方链接),-1 API调用失败',
  `api_response` text COMMENT 'API返回的原始数据',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `link_id` (`link_id`),
  CONSTRAINT `detection_records_ibfk_1` FOREIGN KEY (`link_id`) REFERENCES `links` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='检测记录表';

-- 通知配置表
CREATE TABLE `notification_configs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `email_enabled` tinyint(1) DEFAULT '0' COMMENT '邮件通知是否启用',
  `sms_enabled` tinyint(1) DEFAULT '0' COMMENT '短信通知是否启用',
  `wechat_enabled` tinyint(1) DEFAULT '0' COMMENT '微信通知是否启用',
  `scenarios` varchar(255) DEFAULT NULL COMMENT '场景配置，JSON格式',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `notification_configs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='通知配置表';

-- 通知记录表
CREATE TABLE `notification_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `type` tinyint(1) NOT NULL COMMENT '通知类型：1邮件，2短信，3微信',
  `content` text NOT NULL COMMENT '通知内容',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态：1成功，0失败',
  `error_message` varchar(255) DEFAULT NULL COMMENT '错误信息',
  `detection_record_id` int(11) DEFAULT NULL COMMENT '关联的检测记录ID',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `detection_record_id` (`detection_record_id`),
  CONSTRAINT `notification_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `notification_logs_ibfk_2` FOREIGN KEY (`detection_record_id`) REFERENCES `detection_records` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='通知记录表';

-- 操作记录表
CREATE TABLE `operation_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `module` varchar(50) NOT NULL COMMENT '操作模块',
  `action` varchar(50) NOT NULL COMMENT '操作动作',
  `ip` varchar(50) NOT NULL COMMENT 'IP地址',
  `user_agent` varchar(255) DEFAULT NULL COMMENT '用户代理',
  `data` text COMMENT '操作数据',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `operation_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='操作记录表';

-- 重置密码表
CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `token` varchar(255) NOT NULL COMMENT '重置令牌',
  `expires_at` datetime NOT NULL COMMENT '过期时间',
  `used` tinyint(1) DEFAULT '0' COMMENT '是否已使用',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='重置密码表';

-- 系统配置表
CREATE TABLE `system_configs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(50) NOT NULL COMMENT '配置键',
  `value` text COMMENT '配置值',
  `description` varchar(255) DEFAULT NULL COMMENT '描述',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系统配置表';

-- 插入初始数据
-- 初始管理员账号
INSERT INTO `users` (`id`, `username`, `password`, `email`, `phone`, `wechat_id`, `status`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@example.com', '13800138000', 'admin_wechat', 1, NULL, NOW(), NOW());

-- 初始角色
INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, '超级管理员', '拥有所有权限', NOW(), NOW()),
(2, '普通管理员', '拥有部分管理权限', NOW(), NOW()),
(3, '普通用户', '普通用户权限', NOW(), NOW());

-- 初始权限
INSERT INTO `permissions` (`id`, `name`, `code`, `description`, `created_at`, `updated_at`) VALUES
(1, '用户管理', 'user_manage', '管理用户', NOW(), NOW()),
(2, '角色管理', 'role_manage', '管理角色', NOW(), NOW()),
(3, '项目管理', 'project_manage', '管理项目', NOW(), NOW()),
(4, '链接管理', 'link_manage', '管理链接', NOW(), NOW()),
(5, '检测管理', 'detection_manage', '管理检测', NOW(), NOW()),
(6, '通知管理', 'notification_manage', '管理通知', NOW(), NOW()),
(7, '系统配置', 'system_config', '管理系统配置', NOW(), NOW()),
(8, '日志查看', 'log_view', '查看操作日志', NOW(), NOW());

-- 超级管理员权限
INSERT INTO `role_permission` (`role_id`, `permission_id`, `created_at`) VALUES
(1, 1, NOW()),
(1, 2, NOW()),
(1, 3, NOW()),
(1, 4, NOW()),
(1, 5, NOW()),
(1, 6, NOW()),
(1, 7, NOW()),
(1, 8, NOW());

-- 普通管理员权限
INSERT INTO `role_permission` (`role_id`, `permission_id`, `created_at`) VALUES
(2, 3, NOW()),
(2, 4, NOW()),
(2, 5, NOW()),
(2, 6, NOW()),
(2, 8, NOW());

-- 初始用户角色
INSERT INTO `user_role` (`user_id`, `role_id`, `created_at`) VALUES
(1, 1, NOW());

-- 初始系统配置
INSERT INTO `system_configs` (`key`, `value`, `description`, `created_at`, `updated_at`) VALUES
('boce_api_key', '', 'Boce.com API密钥', NOW(), NOW()),
('email_config', '{"host":"smtp.qq.com","port":465,"username":"","password":"","from":""}', '邮件接口配置', NOW(), NOW()),
('sms_config', '{"access_key_id":"","access_key_secret":"","sign_name":"","template_code":""}', '短信接口配置', NOW(), NOW()),
('wechat_config', '{"app_id":"","app_secret":"","template_id":""}', '微信通知配置', NOW(), NOW()),
('detection_frequency', '3600', '检测频率(秒)', NOW(), NOW()),
('theme_color', 'blue', '主题颜色', NOW(), NOW());
