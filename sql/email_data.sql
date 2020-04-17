/*
Navicat MySQL Data Transfer

Source Server         : localhost_laravel58
Source Server Version : 50726
Source Host           : localhost:3306
Source Database       : laravel58

Target Server Type    : MYSQL
Target Server Version : 50726
File Encoding         : 65001

Date: 2020-04-17 15:31:00
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for email_data
-- ----------------------------
DROP TABLE IF EXISTS `email_data`;
CREATE TABLE `email_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(34) NOT NULL,
  `phone` varchar(22) NOT NULL,
  `from` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT '',
  `data_date` datetime DEFAULT NULL,
  `from_mail` varchar(68) DEFAULT NULL COMMENT '发件人',
  `mail_title` varchar(255) DEFAULT NULL,
  `mail_date` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `mail_content` text NOT NULL,
  `econfig_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `econfig_id` (`econfig_id`),
  CONSTRAINT `email_data_ibfk_1` FOREIGN KEY (`econfig_id`) REFERENCES `email_config` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='邮箱数据集合';
