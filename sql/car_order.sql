/*
Navicat MySQL Data Transfer

Source Server         : localhost_laravel58
Source Server Version : 50726
Source Host           : localhost:3306
Source Database       : laravel58

Target Server Type    : MYSQL
Target Server Version : 50726
File Encoding         : 65001

Date: 2020-04-17 15:30:39
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for car_order
-- ----------------------------
DROP TABLE IF EXISTS `car_order`;
CREATE TABLE `car_order` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `status` tinyint(2) DEFAULT NULL COMMENT '0：已取消 1：进行中 2.已完成',
  `total_price` varchar(10) DEFAULT NULL COMMENT '车主id',
  `car_user_id` int(11) NOT NULL COMMENT '车主的id',
  `user_id` int(11) NOT NULL COMMENT '添加人的ID',
  `information` json DEFAULT NULL COMMENT '订单的配件信息',
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
