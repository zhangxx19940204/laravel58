/*
Navicat MySQL Data Transfer

Source Server         : localhost_laravel58
Source Server Version : 50726
Source Host           : localhost:3306
Source Database       : laravel58

Target Server Type    : MYSQL
Target Server Version : 50726
File Encoding         : 65001

Date: 2020-04-17 15:31:06
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for email_pass
-- ----------------------------
DROP TABLE IF EXISTS `email_pass`;
CREATE TABLE `email_pass` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email_account` varchar(128) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
