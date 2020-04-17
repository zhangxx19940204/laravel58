/*
Navicat MySQL Data Transfer

Source Server         : localhost_laravel58
Source Server Version : 50726
Source Host           : localhost:3306
Source Database       : laravel58

Target Server Type    : MYSQL
Target Server Version : 50726
File Encoding         : 65001

Date: 2020-04-17 15:30:53
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for email_config
-- ----------------------------
DROP TABLE IF EXISTS `email_config`;
CREATE TABLE `email_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `email_address` varchar(64) DEFAULT NULL,
  `email_password` varchar(128) DEFAULT NULL,
  `host_port` varchar(48) DEFAULT NULL,
  `move_folder` varchar(128) DEFAULT NULL,
  `create_date` timestamp NULL DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  `status` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `email_config_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `admin_users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COMMENT='user_mail_config_info';
