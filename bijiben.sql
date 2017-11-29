/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50720
 Source Host           : localhost
 Source Database       : bijiben

 Target Server Type    : MySQL
 Target Server Version : 50720
 File Encoding         : utf-8

 Date: 11/29/2017 14:57:07 PM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `think_ad`
-- ----------------------------
DROP TABLE IF EXISTS `think_ad`;
CREATE TABLE `think_ad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adname` varchar(50) NOT NULL DEFAULT '',
  `adid` varchar(30) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `typeid` smallint(10) NOT NULL DEFAULT '0',
  `normbody` text,
  `url` varchar(255) NOT NULL DEFAULT '',
  `count` int(11) NOT NULL DEFAULT '0',
  `imgurl` varchar(255) NOT NULL DEFAULT '',
  `lang` char(10) NOT NULL DEFAULT 'cn',
  `ctime` varchar(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `adid` (`adid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `think_ad_block`
-- ----------------------------
DROP TABLE IF EXISTS `think_ad_block`;
CREATE TABLE `think_ad_block` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `identifier` varchar(128) NOT NULL,
  `lang` char(10) NOT NULL DEFAULT '',
  `position` varchar(128) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `jump_url` varchar(255) NOT NULL DEFAULT '',
  `content` text,
  `ctime` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `identifier` (`identifier`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `think_ad_slide`
-- ----------------------------
DROP TABLE IF EXISTS `think_ad_slide`;
CREATE TABLE `think_ad_slide` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `typeid` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `height` int(11) NOT NULL DEFAULT '0',
  `width` int(11) NOT NULL DEFAULT '0',
  `linkurl` varchar(255) NOT NULL DEFAULT '',
  `picurl` varchar(255) NOT NULL DEFAULT '',
  `sortslide` int(11) NOT NULL DEFAULT '0',
  `product_title` varchar(255) NOT NULL DEFAULT '',
  `product_description` varchar(255) NOT NULL DEFAULT '',
  `ctime` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `typeid` (`typeid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `think_admin`
-- ----------------------------
DROP TABLE IF EXISTS `think_admin`;
CREATE TABLE `think_admin` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `nickname` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `pwd` varchar(64) NOT NULL,
  `status` smallint(5) NOT NULL DEFAULT '0',
  `remark` varchar(255) NOT NULL,
  `logintime` int(10) NOT NULL DEFAULT '0',
  `ip` varchar(255) NOT NULL,
  `addtime` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `nickname` (`nickname`),
  KEY `role_id` (`role_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='网站后台管理员表';

-- ----------------------------
--  Records of `think_admin`
-- ----------------------------
BEGIN;
INSERT INTO `think_admin` VALUES ('1', '1', 'admin', '', 'c2a1f2717ec7c61b5d5d0ab5d98252ed', '1', '', '1511860835', '127.0.0.1', '1507622633');
COMMIT;

-- ----------------------------
--  Table structure for `think_app`
-- ----------------------------
DROP TABLE IF EXISTS `think_app`;
CREATE TABLE `think_app` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `appname` varchar(255) DEFAULT '',
  `appid` varchar(128) NOT NULL,
  `secret_key` varchar(255) NOT NULL DEFAULT '0',
  `content` varchar(255) DEFAULT NULL,
  `ctime` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `appid` (`appid`),
  KEY `status` (`status`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `think_app`
-- ----------------------------
BEGIN;
INSERT INTO `think_app` VALUES ('2', 'android应用名称', '2017100986524496', '56b8285d6789f2890554885788eb0c64', '应用名称', '1511852619', '1511860345', '1');
COMMIT;

-- ----------------------------
--  Table structure for `think_app_log`
-- ----------------------------
DROP TABLE IF EXISTS `think_app_log`;
CREATE TABLE `think_app_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `to_upgrade_id` int(11) NOT NULL DEFAULT '0',
  `from_version_id` int(11) NOT NULL,
  `from_version_code` varchar(128) NOT NULL,
  `to_version_id` int(11) NOT NULL,
  `to_version_code` varchar(128) NOT NULL DEFAULT '',
  `app_id` int(11) NOT NULL DEFAULT '0',
  `ctime` int(10) NOT NULL,
  `clientType` tinyint(1) NOT NULL DEFAULT '0',
  `system` varchar(128) DEFAULT '',
  `client_device` varchar(128) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `upgrade_id` (`to_upgrade_id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `think_app_log`
-- ----------------------------
BEGIN;
INSERT INTO `think_app_log` VALUES ('3', '24', '2', '10', 'v1.0', '12', 'v1.0', '2', '1511927343', '1', '', ''), ('4', '24', '2', '10', 'v1.0', '12', 'v1.0', '2', '1511927374', '1', '', ''), ('5', '24', '2', '10', 'v1.0', '12', 'v1.0', '2', '1511927377', '1', '', ''), ('6', '24', '2', '10', 'v1.0', '12', 'v1.0', '2', '1511927379', '1', '', ''), ('7', '24', '2', '10', 'v1.0', '12', 'v1.0', '2', '1511927381', '1', '', ''), ('8', '24', '2', '10', 'v1.0', '12', 'v1.0', '2', '1511927382', '1', '', ''), ('9', '5', '2', '10', 'v1.0', '12', 'v1.0', '2', '1511937153', '1', 'mac 10.12', 'iphnee'), ('10', '5', '2', '10', 'v1.0', '12', 'v1.0', '2', '1511937155', '1', 'mac 10.12', 'iphnee'), ('11', '5', '2', '10', 'v1.0', '12', 'v1.0', '2', '1511937156', '1', 'mac 10.12', 'iphnee'), ('12', '5', '2', '10', 'v1.0', '12', 'v1.0', '2', '1511937156', '1', 'mac 10.12', 'iphnee'), ('13', '5', '2', '10', 'v1.0', '12', 'v1.0', '2', '1511937157', '1', 'mac 10.12', 'iphnee'), ('14', '5', '2', '10', 'v1.0', '12', 'v1.0', '2', '1511937157', '1', 'mac 10.12', 'iphnee'), ('15', '5', '2', '10', 'v1.0', '12', 'v1.0', '2', '1511937158', '1', 'mac 10.12', 'iphnee'), ('16', '5', '2', '10', 'v1.0', '12', 'v1.0', '2', '1511937158', '1', 'mac 10.12', 'iphnee');
COMMIT;

-- ----------------------------
--  Table structure for `think_app_upgrade`
-- ----------------------------
DROP TABLE IF EXISTS `think_app_upgrade`;
CREATE TABLE `think_app_upgrade` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_id` int(11) NOT NULL DEFAULT '0' COMMENT '客户端id',
  `version_id` int(11) DEFAULT '0' COMMENT '大版本id',
  `version_code` varchar(11) DEFAULT NULL,
  `upgrade_type` tinyint(1) DEFAULT NULL COMMENT '1Android app升级，2，硬件升级',
  `download_url` varchar(255) DEFAULT NULL,
  `content` text COMMENT '升级提示',
  `status` tinyint(2) DEFAULT NULL,
  `ctime` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `app_id` (`app_id`),
  KEY `upgrade_type` (`upgrade_type`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `think_app_upgrade`
-- ----------------------------
BEGIN;
INSERT INTO `think_app_upgrade` VALUES ('2', '2', '12', 'v1.0', '1', 'http://www.baidu.com', '升级说明升级说明升级说明升级说明升级说明', '1', '1511863091', null);
COMMIT;

-- ----------------------------
--  Table structure for `think_auth_group`
-- ----------------------------
DROP TABLE IF EXISTS `think_auth_group`;
CREATE TABLE `think_auth_group` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `title` char(100) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `rules` varchar(500) NOT NULL DEFAULT '',
  `ramark` varchar(255) NOT NULL DEFAULT '',
  `admin_num` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `think_auth_group`
-- ----------------------------
BEGIN;
INSERT INTO `think_auth_group` VALUES ('1', '超级管理员', '1', '', '如果禁用', '1');
COMMIT;

-- ----------------------------
--  Table structure for `think_auth_group_access`
-- ----------------------------
DROP TABLE IF EXISTS `think_auth_group_access`;
CREATE TABLE `think_auth_group_access` (
  `uid` mediumint(8) unsigned NOT NULL,
  `group_id` mediumint(8) unsigned NOT NULL,
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
  KEY `uid` (`uid`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `think_auth_rule`
-- ----------------------------
DROP TABLE IF EXISTS `think_auth_rule`;
CREATE TABLE `think_auth_rule` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(80) NOT NULL DEFAULT '',
  `pid` int(11) NOT NULL DEFAULT '0',
  `title` char(20) NOT NULL DEFAULT '',
  `type` tinyint(1) NOT NULL DEFAULT '1',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `condition` char(100) NOT NULL DEFAULT '',
  `level` tinyint(1) NOT NULL DEFAULT '0',
  `sort` int(10) NOT NULL DEFAULT '0',
  `remark` varchar(255) DEFAULT NULL,
  `icon` varchar(64) DEFAULT '',
  `ctime` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=148 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `think_auth_rule`
-- ----------------------------
BEGIN;
INSERT INTO `think_auth_rule` VALUES ('1', '', '0', '系统设置', '1', '1', '', '1', '0', '', 'fa fa-cog', '0'), ('2', '', '0', '会员管理', '1', '1', '', '1', '10', '', 'fa fa-user', '1479181812'), ('3', '', '0', '内容管理', '1', '1', '', '1', '30', '', '', '1479181812'), ('4', '', '0', '订单管理', '1', '0', '', '1', '20', '', '', '1479181812'), ('5', '', '0', '模块管理', '1', '1', '', '1', '40', '', '', '1479181812'), ('6', '', '1', '系统设置', '1', '1', '', '4', '0', '', 'fa fa-cog', '1479194416'), ('7', 'jzadmin/Site/index', '6', '系统参数设置', '1', '1', '', '2', '10', '', '', '1479194570'), ('8', '', '1', '数据库备份', '1', '1', '', '4', '20', '', 'fa fa-database', '1479194708'), ('9', 'jzadmin/Database/index', '8', '备份数据库', '1', '1', '', '2', '0', '', '', '1479194755'), ('10', 'jzadmin/Database/restore', '8', '还原数据库', '1', '1', '', '2', '0', '', '', '1479194805'), ('11', '', '5', '广告栏目', '1', '1', '', '4', '30', '', '', '1479194899'), ('12', 'jzadmin/Ad/index', '11', '常用广告管理', '1', '1', '', '2', '0', '', '', '1479194924'), ('13', 'jzadmin/ad.block/index', '11', '碎片广告管理', '1', '1', '', '2', '0', '', '', '1479194947'), ('14', '', '1', '权限管理', '1', '1', '', '4', '10', '', '', '1479195234'), ('15', 'jzadmin/Admin/index', '14', '管理员列表', '1', '1', '', '2', '0', '', '', '1479195259'), ('17', 'jzadmin/Admin/rule', '14', '权限规则', '1', '1', '', '2', '20', '', '', '1479195335'), ('18', '', '4', '订单栏目', '1', '1', '', '4', '0', '', '', '1479195762'), ('19', 'jzadmin/Order/index', '18', '订单列表', '1', '1', '', '2', '0', '', '', '1479195778'), ('27', 'jzadmin/Index/index', '6', '后台首页显示', '1', '1', '', '2', '0', '', '', '1479196000'), ('29', 'jzadmin/admin/add_rule', '17', '添加规则', '1', '1', '', '3', '0', '', '', '1479452956'), ('30', 'jzadmin/admin/edit_rule', '17', '修改规则', '1', '1', '', '3', '0', '', '', '1479455674'), ('31', 'jzadmin/admin/group', '14', '角色管理', '1', '1', '', '2', '10', '', '', '1479561434'), ('32', 'jzadmin/admin/add_group', '31', '添加角色', '1', '1', '', '3', '0', '', '', '1480305029'), ('33', 'jzadmin/admin/edit_group', '31', '修改角色', '1', '1', '', '3', '0', '', '', '1480305123'), ('34', 'jzadmin/admin/delete_group', '31', '删除角色', '1', '1', '', '3', '0', '', '', '1480305172'), ('35', 'jzadmin/admin/changrole', '31', '设置权限', '1', '1', '', '3', '0', '', '', '1480305202'), ('36', 'jzadmin/Lang/index', '6', '语言列表管理', '1', '1', '', '2', '20', '', '', '1482203962'), ('37', 'jzadmin/Ad/add', '12', '添加广告', '1', '1', '', '3', '0', '', '', '1482477400'), ('38', 'jzadmin/Ad/edit', '12', '修改广告', '1', '1', '', '3', '0', '', '', '1482484067'), ('39', 'jzadmin/Ad/del', '12', '删除广告', '1', '1', '', '3', '0', '', '', '1482669183'), ('40', 'jzadmin/Ad/index', '12', '查看常用广告', '1', '1', '', '3', '0', '', '', '1482669317'), ('41', 'jzadmin/Slide/index', '12', '幻灯片列表', '1', '1', '', '3', '0', '', '', '1482669406'), ('42', 'jzadmin/Slide/add', '12', '添加幻灯片', '1', '1', '', '3', '0', '', '', '1482669492'), ('43', 'jzadmin/Slide/edit', '12', '修改幻灯片', '1', '1', '', '3', '0', '', '', '1482669516'), ('44', 'jzadmin/Slide/del', '12', '删除幻灯片', '1', '1', '', '3', '0', '', '', '1482669549'), ('45', 'jzadmin/ad.block/add', '13', '添加碎片广告', '1', '1', '', '3', '0', '', '', '1482669654'), ('46', 'jzadmin/ad.block/edit', '13', '修改碎片广告', '1', '1', '', '3', '0', '', '', '1482669693'), ('47', 'jzadmin/ad.block/del', '13', '删除碎片广告', '1', '1', '', '3', '0', '', '', '1482669721'), ('50', '', '2', '会员管理', '1', '1', '', '4', '0', '', '', '1482672168'), ('51', 'jzadmin/User/index', '50', '会员列表管理', '1', '1', '', '2', '0', '', '', '1482672402'), ('52', 'jzadmin/user.level/index', '50', '会员等级管理', '1', '1', '', '2', '10', '', '', '1482672485'), ('104', 'jzadmin/Site/add', '7', '添加系统参数', '1', '1', '', '3', '0', '', '', '1492524055'), ('105', 'jzadmin/Site/email', '7', '邮箱设置', '1', '1', '', '3', '0', '', '', '1492524101'), ('106', 'jzadmin/Site/sms', '7', '短信设置', '1', '1', '', '3', '0', '', '', '1492524122'), ('107', 'jzadmin/Site/watermark', '7', '水印设置', '1', '1', '', '3', '0', '', '', '1492524145'), ('108', 'jzadmin/Lang/add', '36', '添加语言', '1', '1', '', '3', '0', '', '', '1492524205'), ('109', 'jzadmin/Lang/edit', '36', '修改语言', '1', '1', '', '3', '0', '', '', '1492524223'), ('110', 'jzadmin/Lang/dellang', '36', '删除语言', '1', '1', '', '3', '0', '', '', '1492524243'), ('111', 'jzadmin/Lang/updateStatus', '36', '便携式更改语言状态', '1', '1', '', '3', '0', '', '', '1492524272'), ('112', 'jzadmin/Lang/setlang', '36', '修改语言参数', '1', '1', '', '3', '0', '', '', '1492524294'), ('113', 'jzadmin/Lang/addparam', '36', '添加语言参数', '1', '1', '', '3', '0', '', '', '1492524312'), ('118', 'jzadmin/Admin/add', '15', '添加管理员', '1', '1', '', '3', '0', '', '', '1492524516'), ('119', 'jzadmin/Admin/edit', '15', '修改管理员', '1', '1', '', '3', '0', '', '', '1492524537'), ('120', 'jzadmin/Admin/delete_admin', '15', '删除管理员', '1', '1', '', '3', '0', '', '', '1492524558'), ('121', 'jzadmin/Admin/delete_rule', '17', '删除规则', '1', '1', '', '3', '0', '', '', '1492524610'), ('122', 'jzadmin/Database/export', '9', '保存备份', '1', '1', '', '3', '0', '', '', '1492524738'), ('123', 'jzadmin/Database/optimize', '10', '优化表', '1', '1', '', '3', '0', '', '', '1492524777'), ('124', 'jzadmin/Database/repair', '10', '修复表', '1', '1', '', '3', '0', '', '', '1492524799'), ('125', 'jzadmin/Database/del', '10', '删除备份文件', '1', '1', '', '3', '0', '', '', '1492524826'), ('126', 'jzadmin/Database/import', '10', '提交还原数据库', '1', '1', '', '3', '0', '', '', '1492524864'), ('127', 'jzadmin/User/edit', '51', '修改用户资料', '1', '1', '', '3', '0', '', '', '1492524942'), ('128', 'jzadmin/User/ajax_user_levelid_email', '51', 'Ajax搜素用户', '1', '1', '', '3', '0', '', '', '1492524987'), ('129', 'jzadmin/user.level/add_level', '52', '添加会员等级', '1', '1', '', '3', '0', '', '', '1492525054'), ('130', 'jzadmin/user.level/edit_level', '52', '修改会员等级', '1', '1', '', '3', '0', '', '', '1492525133'), ('131', 'jzadmin/user.level/delete_level', '52', '删除会员等级', '1', '1', '', '3', '0', '', '', '1492525193'), ('132', 'jzadmin/user.template/add', '100', '添加模板', '1', '1', '', '3', '0', '', '', '1492525234'), ('133', 'jzadmin/user.template/edit', '100', '修改模板', '1', '1', '', '3', '0', '', '', '1492525252'), ('134', 'jzadmin/user.template/del', '100', '删除模板', '1', '1', '', '3', '0', '', '', '1492525273'), ('100', 'jzadmin/user.template/index', '50', '消息模板管理', '1', '1', '', '2', '20', '', '', '1490625155'), ('135', '', '3', '笔记栏目管理', '1', '1', '', '4', '0', '', '', '1508813037'), ('136', 'jzadmin/note.notebook/index', '135', '笔记本管理', '1', '1', '', '2', '0', '', '', '1508813081'), ('137', 'jzadmin/note.note/index', '135', '笔记列表管理', '1', '1', '', '2', '0', '', '', '1508813104'), ('138', 'jzadmin/App/index', '3', '应用管理栏目', '1', '1', '', '4', '30', '', '', '1511840344'), ('139', 'jzadmin/app.app/index', '138', '应用管理列表', '1', '1', '', '2', '0', '', '', '1511840385'), ('140', 'jzadmin/app.upgrade/index', '138', '应用升级管理', '1', '1', '', '2', '10', '', '', '1511840532'), ('141', 'jzadmin/app.app/add', '139', '添加', '1', '1', '', '3', '0', '', '', '1511851216'), ('142', 'jzadmin/app.app/edit', '139', '修改', '1', '1', '', '3', '0', '', '', '1511851238'), ('143', 'jzadmin/app.app/del', '139', '删除', '1', '1', '', '3', '0', '', '', '1511851260'), ('144', 'jzadmin/app.upgrade/add', '140', '添加', '1', '1', '', '3', '0', '', '', '1511859499'), ('145', 'jzadmin/app.upgrade/edit', '140', '修改', '1', '1', '', '3', '0', '', '', '1511859522'), ('146', 'jzadmin/app.upgrade/del', '140', '删除', '1', '1', '', '3', '0', '', '', '1511859549'), ('147', 'jzadmin/app.upgrade/applog', '140', '日志', '1', '1', '', '3', '0', '', '', '1511864348');
COMMIT;

-- ----------------------------
--  Table structure for `think_devicetoken`
-- ----------------------------
DROP TABLE IF EXISTS `think_devicetoken`;
CREATE TABLE `think_devicetoken` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `udid` varchar(128) NOT NULL,
  `appid` varchar(64) NOT NULL,
  `isyueyu` tinyint(2) NOT NULL DEFAULT '0',
  `system` varchar(128) NOT NULL,
  `devicetoken` varchar(128) NOT NULL,
  `ctime` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `think_email_verify`
-- ----------------------------
DROP TABLE IF EXISTS `think_email_verify`;
CREATE TABLE `think_email_verify` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(32) NOT NULL DEFAULT '',
  `type` smallint(5) NOT NULL DEFAULT '1',
  `verify` int(6) NOT NULL DEFAULT '0',
  `status` tinyint(2) NOT NULL DEFAULT '0',
  `return_status` varchar(255) NOT NULL DEFAULT '',
  `userip` varchar(64) NOT NULL DEFAULT '',
  `ctime` int(10) NOT NULL DEFAULT '0',
  `check_time` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `think_lang`
-- ----------------------------
DROP TABLE IF EXISTS `think_lang`;
CREATE TABLE `think_lang` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '',
  `mark` varchar(30) NOT NULL DEFAULT '',
  `flag` varchar(100) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `path` varchar(30) NOT NULL DEFAULT '',
  `domain` varchar(30) NOT NULL DEFAULT '',
  `listorder` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `think_lang`
-- ----------------------------
BEGIN;
INSERT INTO `think_lang` VALUES ('1', '中文', 'cn', '', '1', '', '', '50');
COMMIT;

-- ----------------------------
--  Table structure for `think_lang_param`
-- ----------------------------
DROP TABLE IF EXISTS `think_lang_param`;
CREATE TABLE `think_lang_param` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang_id` tinyint(1) NOT NULL DEFAULT '0',
  `module_type` char(10) NOT NULL DEFAULT 'index',
  `mark` char(10) NOT NULL DEFAULT '',
  `field` varchar(128) NOT NULL DEFAULT '',
  `value` varchar(500) NOT NULL DEFAULT '',
  `alisa` varchar(255) NOT NULL DEFAULT '',
  `ctime` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `lang_id` (`lang_id`)
) ENGINE=MyISAM AUTO_INCREMENT=45 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `think_lang_param`
-- ----------------------------
BEGIN;
INSERT INTO `think_lang_param` VALUES ('1', '1', 'api', 'cn', 'PLEASE_INPUT_ACCOUNT', '请输入账号', '请输入账号', '1507627984'), ('2', '1', 'api', 'cn', 'MAILBOX_FORMAT_ERROR', '邮箱格式错误', '邮箱格式错误', '1507629034'), ('3', '1', 'api', 'cn', 'MAILBOX_HAS_REGISTERED', '邮箱已注册', '邮箱已注册', '1507629103'), ('4', '1', 'api', 'cn', 'PASSWORD_FORMAT_ERROR', '密码格式错误', '密码格式错误', '1507629154'), ('5', '1', 'api', 'cn', 'NICKNAME_EMPTY', '昵称为空', '昵称为空', '1507629192'), ('6', '1', 'api', 'cn', 'NICKNAME_ALREADY_EXISTS', '昵称已注册', '昵称已注册', '1507629241'), ('7', '1', 'api', 'cn', 'REG_HAS_FAILED', '注册失败', '注册失败', '1507629396'), ('8', '1', 'api', 'cn', 'MOBILE_FORMAT_ERROR', '手机号码格式错误', '手机号码格式错误', '1507705768'), ('9', '1', 'api', 'cn', 'SMS_FORMAT_ERROR', '发送类型错误', '发送类型错误', '1507705876'), ('10', '1', 'api', 'cn', 'SMS_SUCCESS', '发送成功', '发送成功', '1507706067'), ('11', '1', 'api', 'cn', 'MOBILE_HAS_REGISTERED', '手机号码已经注册', '手机号码已经注册', '1507706360'), ('12', '1', 'api', 'cn', 'VERIFY_ERROR', '验证码错误', '验证码错误', '1507706604'), ('13', '1', 'api', 'cn', 'LOGIN_WAS_SUCCESSFUL', '注册成功', '注册成功', '1507706764'), ('14', '1', 'api', 'cn', 'MOBILE_ALREADY_USERED', '手机号码可以使用', '手机号码可以使用', '1507707111'), ('15', '1', 'api', 'cn', 'MAILBOX_ALREADY_USERED', '邮箱可以注册', '邮箱可以注册', '1507707250'), ('16', '1', 'api', 'cn', 'PLEASE_ENTER_LOGIN', '请登录', '请登录', '1507707536'), ('17', '1', 'api', 'cn', 'MAILBOX_NOT_REGISTERED', '邮箱未注册', '邮箱未注册', '1507708827'), ('18', '1', 'api', 'cn', 'ACCOUNT_IS_DISABLED', '账号已禁用', '账号已禁用', '1507708883'), ('19', '1', 'api', 'cn', 'VERIFY_CODE_CORRECT', '验证码正确', '验证码正确', '1507886902'), ('20', '1', 'api', 'cn', 'ENTER_INPUT_EMAIL_MOBILE', '请输入手机号码或者邮箱', '请输入手机号码或者邮箱', '1508136908'), ('21', '1', 'api', 'cn', 'ACCOUNT_ERROR', '账号错误', '账号错误', '1508138717'), ('22', '1', 'api', 'cn', 'MOIBLE_NOT_REG', '手机号码未注册', '手机号码未注册', '1508139025'), ('23', '1', 'api', 'cn', 'MODIFY_SUCCESS', '修改成功', '修改成功', '1508139815'), ('24', '1', 'api', 'cn', 'PARAM_ERROR', '参数错误', '参数错误', '1508140868'), ('25', '1', 'api', 'cn', 'PASSWORD_ERROR', '密码错误', '密码错误', '1508140963'), ('26', '1', 'api', 'cn', 'MODIFY_FAILURE', '修改失败', '修改失败', '1508141138'), ('27', '1', 'api', 'cn', 'NOTEBOOK_IS_EMPTY', '名称为空', '名称为空', '1508143368'), ('28', '1', 'api', 'cn', 'SAVE_SUCCESSFULLY', '保存成功', '保存成功', '1508143504'), ('29', '1', 'api', 'cn', 'SAVE_FAILED', '保存失败', '保存失败', '1508143528'), ('30', '1', 'api', 'cn', 'DELETE_SUCCESSFULLY', '删除成功', '删除成功', '1508144149'), ('31', '1', 'api', 'cn', 'DELETE_FAILED', '删除失败', '删除失败', '1508144174'), ('32', '1', 'api', 'cn', 'NOTEBOOK_HAS_NOTE', '笔记本下有笔记，请勿删除', '笔记本下有笔记，请勿删除', '1508144276'), ('33', '1', 'api', 'cn', 'GET_SUCCESS', '请求成功', '请求成功', '1508144364'), ('34', '1', 'api', 'cn', 'GET_FAILURES', '请求失败', '请求失败', '1508144402'), ('35', '1', 'api', 'cn', 'ORDER_NO', '暂无数据', '暂无数据', '1508144885'), ('36', '1', 'api', 'cn', 'PLEASE_SELECT_FILE', '请选择文件', '请选择文件', '1508222769'), ('37', '1', 'api', 'cn', 'UPLOAD_SUCCESS', '上传成功', '上传成功', '1508231448'), ('38', '1', 'api', 'cn', 'UPLOAD_FAILED', '上传失败', '上传失败', '1508231459'), ('39', '1', 'api', 'cn', 'LOGIN_SUCCESS', '登录成功', '登录成功', '1508317596'), ('40', '1', 'api', 'cn', 'INSUFFICIENT_STORAGE_SPACE', '存储空间不足', '存储空间不足', '1508470787'), ('41', '1', 'api', 'cn', 'NOTE_DISABLED', '笔记已禁用', '笔记已禁用', '1508832356'), ('42', '1', 'api', 'cn', 'VERSION_IDVERSION_CODE', '版本id或者版本码为空', '版本id或者版本码为空', '1511923431'), ('43', '1', 'api', 'cn', 'APPLICATION_ID_EMPTY', '应用id不能为空', '应用id不能为空', '1511923837'), ('44', '1', 'api', 'cn', 'NO_UPGRADE_INFORMATION', '暂无升级信息', '暂无升级信息', '1511925038');
COMMIT;

-- ----------------------------
--  Table structure for `think_note`
-- ----------------------------
DROP TABLE IF EXISTS `think_note`;
CREATE TABLE `think_note` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `notebook_id` int(11) NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `label_num` int(11) NOT NULL DEFAULT '0',
  `thumb_id` int(11) NOT NULL DEFAULT '0',
  `file_id` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `ctime` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `notebook_id` (`notebook_id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `think_note`
-- ----------------------------
BEGIN;
INSERT INTO `think_note` VALUES ('9', '10', '24', '测试1', '2', '0', '10', '1', '1508485876'), ('11', '10', '24', '测试3', '2', '0', '10', '1', '1508485876');
COMMIT;

-- ----------------------------
--  Table structure for `think_note_label`
-- ----------------------------
DROP TABLE IF EXISTS `think_note_label`;
CREATE TABLE `think_note_label` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `note_id` int(11) DEFAULT '0',
  `name` varchar(255) DEFAULT '',
  `ctime` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `note_id` (`note_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `think_note_label`
-- ----------------------------
BEGIN;
INSERT INTO `think_note_label` VALUES ('11', '11', '标签4', '1508485876'), ('10', '11', '标签3', '1508485876'), ('9', '9', '标签2', '1508485876'), ('8', '9', '标签1', '1508485876');
COMMIT;

-- ----------------------------
--  Table structure for `think_notebook`
-- ----------------------------
DROP TABLE IF EXISTS `think_notebook`;
CREATE TABLE `think_notebook` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `quantity` int(10) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `ctime` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `think_notebook`
-- ----------------------------
BEGIN;
INSERT INTO `think_notebook` VALUES ('10', '24', 'bijibenname', '2', '1', '1508485876'), ('11', '24', '笔记本2', '0', '1', '1508485876');
COMMIT;

-- ----------------------------
--  Table structure for `think_oss_log`
-- ----------------------------
DROP TABLE IF EXISTS `think_oss_log`;
CREATE TABLE `think_oss_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `typeid` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 头像,1笔记,3缩略图',
  `object_name` varchar(255) NOT NULL DEFAULT '',
  `oss_url` varchar(255) NOT NULL DEFAULT '',
  `local_url` varchar(255) NOT NULL DEFAULT '',
  `requestheaders_host` varchar(255) NOT NULL DEFAULT '',
  `request_id` varchar(128) NOT NULL DEFAULT '',
  `extension` char(10) NOT NULL DEFAULT '',
  `size` float(10,0) NOT NULL DEFAULT '0',
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `think_session`
-- ----------------------------
DROP TABLE IF EXISTS `think_session`;
CREATE TABLE `think_session` (
  `session_id` varchar(255) NOT NULL,
  `uid` int(11) NOT NULL DEFAULT '0',
  `session_expire` int(11) NOT NULL,
  `session_data` longtext,
  `ip` varchar(64) DEFAULT '',
  PRIMARY KEY (`session_id`),
  UNIQUE KEY `session_id` (`session_id`),
  KEY `ip` (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `think_session`
-- ----------------------------
BEGIN;
INSERT INTO `think_session` VALUES ('35d29090e6b1f7018eaab99013038ef4', '23', '1516260967', 'a:3:{s:3:\"uid\";s:2:\"23\";s:8:\"username\";s:8:\"YWxhbg==\";s:12:\"access_token\";s:32:\"35d29090e6b1f7018eaab99013038ef4\";}', '127.0.0.1'), ('31ab7d04dfa3934a480ce5310d2cc892', '24', '1516261401', 'a:3:{s:3:\"uid\";i:24;s:8:\"username\";s:8:\"YWxhbg==\";s:12:\"access_token\";s:32:\"31ab7d04dfa3934a480ce5310d2cc892\";}', '127.0.0.1'), ('e6ce35aac7070328df6a7db93d2e73ec', '0', '1519631753', '', '127.0.0.1'), ('26a9ec7f25f5b83d1bc5b93069922d37', '0', '1519631768', '', '127.0.0.1'), ('839369a3a359f7a599488ea68af91884', '0', '1519631771', '', '127.0.0.1'), ('8dcbd63b12ca3a82ab202af051b20590', '0', '1519631838', '', '127.0.0.1');
COMMIT;

-- ----------------------------
--  Table structure for `think_site`
-- ----------------------------
DROP TABLE IF EXISTS `think_site`;
CREATE TABLE `think_site` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `groupid` tinyint(1) NOT NULL DEFAULT '0',
  `varname` varchar(32) NOT NULL DEFAULT '',
  `info` varchar(65) NOT NULL DEFAULT '',
  `value` text,
  `lang` char(10) NOT NULL DEFAULT '',
  `input_type` char(20) NOT NULL DEFAULT '',
  `mark` varchar(255) NOT NULL DEFAULT '',
  `html_text` varchar(255) NOT NULL DEFAULT '',
  `ctime` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `varname` (`varname`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `think_site`
-- ----------------------------
BEGIN;
INSERT INTO `think_site` VALUES ('1', '3', 'EMAIL_VERIFY_TIME', '邮箱验证间隔', '60', 'cn', 'text', '单位秒(s)', '', '1507715744'), ('2', '3', 'Mobile_verify_time', '短信发送间隔', '60', 'cn', 'text', '单位(秒)', '', '1507716654'), ('3', '3', 'registration_gift_space', '注册赠送空间', '500', 'cn', 'text', '单位(MB)', '', '1507887413'), ('4', '3', 'Space_exchange_ratio', '空间存储兑换单位', '1024', 'cn', 'text', '', '', '1508471487');
COMMIT;

-- ----------------------------
--  Table structure for `think_sms_verify`
-- ----------------------------
DROP TABLE IF EXISTS `think_sms_verify`;
CREATE TABLE `think_sms_verify` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mobile` varchar(20) NOT NULL DEFAULT '',
  `type` smallint(5) NOT NULL DEFAULT '1',
  `verify` int(6) NOT NULL DEFAULT '0',
  `status` tinyint(2) NOT NULL DEFAULT '0',
  `return_status` varchar(255) NOT NULL DEFAULT '',
  `userip` varchar(64) NOT NULL DEFAULT '',
  `ctime` int(10) NOT NULL DEFAULT '0',
  `check_time` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mobile` (`mobile`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `think_sms_verify`
-- ----------------------------
BEGIN;
INSERT INTO `think_sms_verify` VALUES ('3', '15013352853', '1', '651061', '1', 'a:3:{s:6:\"status\";i:1;s:3:\"msg\";s:2:\"OK\";s:6:\"verify\";s:6:\"651061\";}', '127.0.0.1', '1508484776', '1508485168');
COMMIT;

-- ----------------------------
--  Table structure for `think_user`
-- ----------------------------
DROP TABLE IF EXISTS `think_user`;
CREATE TABLE `think_user` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(60) NOT NULL DEFAULT '',
  `mobile` varchar(60) NOT NULL DEFAULT '',
  `nickname` varchar(60) NOT NULL DEFAULT '',
  `password` varchar(32) NOT NULL DEFAULT '',
  `faceurl` varchar(255) NOT NULL DEFAULT '',
  `level_id` smallint(5) NOT NULL DEFAULT '1',
  `money` int(10) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `lang` char(10) NOT NULL DEFAULT 'cn',
  `client_type` tinyint(1) NOT NULL DEFAULT '0',
  `usertype` tinyint(1) NOT NULL DEFAULT '0',
  `reg_time` int(10) NOT NULL DEFAULT '0',
  `login_time` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`),
  KEY `email` (`email`),
  KEY `mobile` (`mobile`),
  KEY `nickname` (`nickname`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `think_user`
-- ----------------------------
BEGIN;
INSERT INTO `think_user` VALUES ('24', '', '15013352853', 'alan5', '1A5282CC70E7856904A5616CCA8C187C', '8', '1', '0', '1', 'cn', '1', '1', '1508485168', '1508485401');
COMMIT;

-- ----------------------------
--  Table structure for `think_user_authorized`
-- ----------------------------
DROP TABLE IF EXISTS `think_user_authorized`;
CREATE TABLE `think_user_authorized` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `type` smallint(5) NOT NULL,
  `unionid` varchar(128) NOT NULL DEFAULT '',
  `openid` varchar(128) NOT NULL,
  `access_token` varchar(128) NOT NULL,
  `nickname` varchar(50) NOT NULL,
  `ctime` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `openid` (`openid`),
  KEY `unionid` (`unionid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `think_user_disk`
-- ----------------------------
DROP TABLE IF EXISTS `think_user_disk`;
CREATE TABLE `think_user_disk` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `total_disk_space` varchar(64) NOT NULL DEFAULT '0',
  `used_disk_space` varchar(64) NOT NULL DEFAULT '0',
  `default_disk_space` varchar(64) NOT NULL DEFAULT '0',
  KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `think_user_disk`
-- ----------------------------
BEGIN;
INSERT INTO `think_user_disk` VALUES ('24', '524288000', '562246', '524288000');
COMMIT;

-- ----------------------------
--  Table structure for `think_user_disk_log`
-- ----------------------------
DROP TABLE IF EXISTS `think_user_disk_log`;
CREATE TABLE `think_user_disk_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `typeid` tinyint(1) NOT NULL DEFAULT '0',
  `space_size` varchar(64) NOT NULL DEFAULT '0',
  `remark` varchar(255) NOT NULL DEFAULT '',
  `note_id` int(11) NOT NULL DEFAULT '0',
  `ctime` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `think_user_disk_log`
-- ----------------------------
BEGIN;
INSERT INTO `think_user_disk_log` VALUES ('2', '24', '3', '524288000', '新增空间:524288000,=500.000M,注册默认赠送', '0', '1508485168'), ('3', '24', '1', '281123', '使用空间:281123,=0.268M,上传笔记消耗空间', '0', '1508485932'), ('4', '24', '2', '281123', '新增空间:281123,=0.268M,删除文件释放空间', '0', '1508830902'), ('5', '24', '2', '200', '新增空间:200,=0.000M,删除文件释放空间', '0', '1510301081'), ('6', '24', '1', '281123', '使用空间:281123,=0.268M,上传笔记消耗空间', '0', '1510816369'), ('7', '24', '2', '10', '新增空间:10,=0.000M,删除文件释放空间', '0', '1510816806');
COMMIT;

-- ----------------------------
--  Table structure for `think_user_info`
-- ----------------------------
DROP TABLE IF EXISTS `think_user_info`;
CREATE TABLE `think_user_info` (
  `uid` int(11) NOT NULL,
  `userip` varchar(32) NOT NULL DEFAULT '',
  `qq` varchar(32) NOT NULL DEFAULT '',
  `sex` tinyint(1) NOT NULL DEFAULT '0',
  `country_id` smallint(5) NOT NULL,
  `province` smallint(5) NOT NULL DEFAULT '0',
  `city` smallint(5) NOT NULL DEFAULT '0',
  `city_name` varchar(255) NOT NULL DEFAULT '',
  `district` smallint(5) NOT NULL DEFAULT '0',
  `twon` smallint(5) NOT NULL DEFAULT '0',
  `address` varchar(255) NOT NULL DEFAULT '',
  `birth` int(11) NOT NULL DEFAULT '0',
  `device_name` varchar(128) NOT NULL DEFAULT '',
  `update_time` int(11) NOT NULL DEFAULT '0',
  `description` varchar(255) NOT NULL DEFAULT '',
  UNIQUE KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `think_user_info`
-- ----------------------------
BEGIN;
INSERT INTO `think_user_info` VALUES ('24', '127.0.0.1', '', '0', '0', '0', '0', '', '0', '0', '', '0', 'iphone se', '1508900515', '');
COMMIT;

-- ----------------------------
--  Table structure for `think_user_level`
-- ----------------------------
DROP TABLE IF EXISTS `think_user_level`;
CREATE TABLE `think_user_level` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `level_value` int(10) NOT NULL DEFAULT '0',
  `level_name` varchar(128) NOT NULL DEFAULT '',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount` smallint(5) NOT NULL DEFAULT '0',
  `description` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `lang` char(10) DEFAULT 'cn',
  `condition` tinyint(1) NOT NULL DEFAULT '0',
  `ctime` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `level_value` (`level_value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `think_user_template`
-- ----------------------------
DROP TABLE IF EXISTS `think_user_template`;
CREATE TABLE `think_user_template` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `temp_title` varchar(255) NOT NULL DEFAULT '' COMMENT '节点名称',
  `temp_key` varchar(128) NOT NULL COMMENT '应用名称',
  `content_key` text NOT NULL COMMENT '内容key',
  `title_key` varchar(255) NOT NULL DEFAULT '' COMMENT '标题key',
  `send_email` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否发送邮件',
  `send_message` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否发送消息',
  `tip_message` varchar(255) NOT NULL DEFAULT '',
  `type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '信息类型：1 表示用户发送的 2表示是系统发送的',
  `lang` char(10) NOT NULL DEFAULT 'cn',
  `ctime` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `temp_key` (`temp_key`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `think_user_template`
-- ----------------------------
BEGIN;
INSERT INTO `think_user_template` VALUES ('1', '用户注册邮件', 'SEND_EMAIL_REG', '<p><strong>尊敬的:{name},您好</strong><br/>感谢您使用服务，邮箱验证邮件已经发送,您只需在app输入验证码：<br/><strong>{verify}</strong></p><p>即可验证邮箱。<br/>如果在操作过程中有什么问题可以联系我们,联系我们,谢谢！<br/><br/></p>', '邮箱验证邮件已发送 ', '1', '0', '验证码{verify},示用户名{name} ', '2', 'cn', '1507709641'), ('2', '找回密码邮件', 'SEND_EMAIL_FIND_PASSWORD', '<p><strong>尊敬的:{name},您好</strong><br/>您的密码找回要求已经得到验证,您只需在APP客户端输入验证码：<br/><strong>{verify}</strong></p><p>输入新的密码后提交，之后您即可使用新的密码登录了。<br/>如果在操作过程中有什么问题可以联系我们的\r\n,谢谢！<br/><br/></p>', '找回密码邮件已发送', '1', '0', '验证码{verify},示用户名{name} ', '2', 'cn', '1507709738');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
