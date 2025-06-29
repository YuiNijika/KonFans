-- 创建用户表
CREATE TABLE IF NOT EXISTS `{prefix}users` (
    `uid` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT '用户 ID',
    `name` VARCHAR(255) NOT NULL UNIQUE COMMENT '用户名',
    `password` VARCHAR(255) NOT NULL COMMENT '密码哈希值',
    `email` VARCHAR(255) NOT NULL UNIQUE COMMENT '邮箱地址',
    `group` VARCHAR(255) NOT NULL DEFAULT 'member' COMMENT '用户组'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户信息表';

-- 创建留言表
CREATE TABLE IF NOT EXISTS `{prefix}messages` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT '留言ID',
    `name` VARCHAR(255) NOT NULL COMMENT '留言者姓名',
    `email` VARCHAR(255) NOT NULL COMMENT '留言者邮箱',
    `content` TEXT NOT NULL COMMENT '留言内容',
    `ip` VARCHAR(45) NOT NULL COMMENT '留言者IP',
    `user_agent` TEXT COMMENT '用户浏览器UA',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '留言时间',
    `parent_id` INT UNSIGNED DEFAULT NULL COMMENT '父留言ID, NULL表示是顶级留言',
    CONSTRAINT `fk_parent_id` FOREIGN KEY (`parent_id`) REFERENCES `{prefix}messages` (`id`) 
        ON DELETE CASCADE
        ON UPDATE RESTRICT,  -- 添加更新约束
    INDEX `idx_parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT='留言板表';

-- 创建社团成员表
CREATE TABLE IF NOT EXISTS `{prefix}members` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT '成员ID',
    `name` VARCHAR(255) NOT NULL COMMENT '成员昵称',
    `email` VARCHAR(255) NOT NULL COMMENT '成员邮箱',
    `gender` ENUM('male','female','secret') NOT NULL DEFAULT 'secret' COMMENT '性别',
    `url` VARCHAR(255) COMMENT '成员个人主页',
    `bio` TEXT COMMENT '成员简介',
    `ip` VARCHAR(45) COMMENT '用户IP地址',
    `user_agent` TEXT COMMENT '用户浏览器UA',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '加入时间',
    `display` ENUM('public', 'private') NOT NULL DEFAULT 'public' COMMENT '显示状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='社团成员表';