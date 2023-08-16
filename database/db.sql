/*
Navicat MySQL Data Transfer

Source Server         : bingwuhu
Source Server Version : 80031
Source Host           : localhost:3306
Source Database       : myblog

Target Server Type    : MYSQL
Target Server Version : 80031
File Encoding         : 65001

Date: 2023-08-15 18:50:40
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for t_admin
-- ----------------------------
DROP TABLE IF EXISTS `t_admin`;
CREATE TABLE `t_admin` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(20) COLLATE utf8mb4_general_ci NOT NULL COMMENT '账户',
  `password` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '账户密码',
  `avatar` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '头像',
  `salt` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '密码盐',
  `nickname` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '昵称',
  `code` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '认证码',
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '邮箱',
  `auth` enum('1','0') COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0' COMMENT '1：已认证，0：未认证',
  `status` enum('1','0') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '1' COMMENT '0：禁用，1：正常',
  `is_super` enum('1','0') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '0' COMMENT '0：普通成员，1：管理员',
  `createtime` int DEFAULT NULL COMMENT '创建时间',
  `updatetime` int DEFAULT NULL COMMENT '更新时间',
  `deletetime` int DEFAULT NULL COMMENT '软删除',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_admin` (`username`,`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table structure for t_article
-- ----------------------------
DROP TABLE IF EXISTS `t_article`;
CREATE TABLE `t_article` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) COLLATE utf8mb4_general_ci NOT NULL COMMENT '标题',
  `abstract` text COLLATE utf8mb4_general_ci COMMENT '概要',
  `tags` varchar(100) COLLATE utf8mb4_general_ci NOT NULL COMMENT '标签',
  `content` longtext COLLATE utf8mb4_general_ci,
  `is_commend` enum('1','0') COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0' COMMENT '0：不推荐，1：推荐',
  `cate_id` int DEFAULT NULL COMMENT '所属导航id',
  `createtime` int DEFAULT NULL COMMENT '创建时间',
  `updatetime` int DEFAULT NULL COMMENT '修改时间',
  `deletetime` int DEFAULT NULL COMMENT '软删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table structure for t_cate
-- ----------------------------
DROP TABLE IF EXISTS `t_cate`;
CREATE TABLE `t_cate` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `catename` varchar(25) COLLATE utf8mb4_general_ci NOT NULL COMMENT '导航名称',
  `sort` int DEFAULT NULL COMMENT '排序',
  `createtime` int DEFAULT NULL COMMENT '添加时间',
  `updatetime` int DEFAULT NULL COMMENT '修改时间',
  `deletetime` int DEFAULT NULL COMMENT '软删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table structure for t_comment
-- ----------------------------
DROP TABLE IF EXISTS `t_comment`;
CREATE TABLE `t_comment` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `content` text COLLATE utf8mb4_general_ci COMMENT '评论内容',
  `article_id` int DEFAULT NULL COMMENT '评论文章',
  `member_id` int DEFAULT NULL COMMENT '评论用户',
  `createtime` int DEFAULT NULL COMMENT '评论时间',
  `updatetime` int DEFAULT NULL COMMENT '修改时间',
  `deletetime` int DEFAULT NULL COMMENT '软删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table structure for t_emails
-- ----------------------------
DROP TABLE IF EXISTS `t_emails`;
CREATE TABLE `t_emails` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '邮箱地址',
  `admin_id` int unsigned NOT NULL COMMENT '关联的管理员ID',
  `expire` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '有效期',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_email` (`email`),
  KEY `fk_emails_admin` (`admin_id`),
  CONSTRAINT `fk_emails_admin` FOREIGN KEY (`admin_id`) REFERENCES `t_admin` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table structure for t_member
-- ----------------------------
DROP TABLE IF EXISTS `t_member`;
CREATE TABLE `t_member` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(20) COLLATE utf8mb4_general_ci NOT NULL COMMENT '会员账户',
  `password` varchar(20) COLLATE utf8mb4_general_ci NOT NULL COMMENT '会员密码',
  `nickname` varchar(20) COLLATE utf8mb4_general_ci NOT NULL COMMENT '会员昵称',
  `email` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '邮箱',
  `createtime` int DEFAULT NULL COMMENT '注册时间',
  `updatetime` int DEFAULT NULL COMMENT '更新时间',
  `deletetime` int DEFAULT NULL COMMENT '软删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table structure for t_system
-- ----------------------------
DROP TABLE IF EXISTS `t_system`;
CREATE TABLE `t_system` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `webname` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `copyright` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `createtime` int DEFAULT NULL COMMENT '设置时间',
  `updatetime` int DEFAULT NULL COMMENT '修改时间',
  `deletetime` int DEFAULT NULL COMMENT '软删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
