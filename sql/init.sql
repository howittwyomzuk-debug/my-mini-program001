-- 诚信资料网数据库初始化脚本

-- 创建数据库
CREATE DATABASE IF NOT EXISTS eshop DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE eshop;

-- 用户表
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `level` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0:游客 1:普通会员 2:VIP',
  `expire_time` int(11) DEFAULT 0 COMMENT '会员过期时间',
  `money` decimal(10,2) DEFAULT 0.00 COMMENT '余额',
  `create_time` int(11) NOT NULL,
  `login_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 分类表
CREATE TABLE IF NOT EXISTS `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `sort` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 文章/资料表
CREATE TABLE IF NOT EXISTS `articles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cat_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `summary` text,
  `content` longtext NOT NULL,
  `price` decimal(10,2) DEFAULT 0.00 COMMENT '单篇价格，0表示会员免费',
  `is_paid` tinyint(1) DEFAULT 1 COMMENT '是否付费',
  `click` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cat_id` (`cat_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 用户购买记录表
CREATE TABLE IF NOT EXISTS `user_buy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `article_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `buy_time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 订单表
CREATE TABLE IF NOT EXISTS `orders` (
  `id` varchar(32) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` tinyint(1) NOT NULL COMMENT '1:会员充值 2:单篇购买',
  `price` decimal(10,2) NOT NULL,
  `status` tinyint(1) DEFAULT 0 COMMENT '0:未支付 1:已支付',
  `pay_time` int(11) DEFAULT NULL,
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 插入初始分类
INSERT INTO `category` (`id`, `name`, `sort`, `status`) VALUES
(1, '主题班会', 1, 1),
(2, '班主任经验', 2, 1),
(3, '教学资料', 3, 1),
(4, 'PPT模板', 4, 1);
?>