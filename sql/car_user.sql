/*
Navicat MySQL Data Transfer

Source Server         : localhost_laravel58
Source Server Version : 50726
Source Host           : localhost:3306
Source Database       : laravel58

Target Server Type    : MYSQL
Target Server Version : 50726
File Encoding         : 65001

Date: 2020-04-17 15:30:46
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for car_user
-- ----------------------------
DROP TABLE IF EXISTS `car_user`;
CREATE TABLE `car_user` (
  `id` int(11) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `username` varchar(64) DEFAULT '',
  `phone` varchar(36) DEFAULT NULL,
  `email` varchar(64) DEFAULT NULL,
  `frame` varchar(64) DEFAULT '' COMMENT '车架',
  `engine_number` varchar(64) DEFAULT '' COMMENT '发动机号',
  `vin_number` varchar(64) DEFAULT '' COMMENT 'VIN码',
  `brand` varchar(128) DEFAULT '' COMMENT '品牌',
  `models` varchar(64) DEFAULT '' COMMENT '车型',
  `mileage` int(8) DEFAULT NULL COMMENT '里程数',
  `licence_number` varchar(20) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL COMMENT '管理者的用户id',
  `extra_info` json DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
