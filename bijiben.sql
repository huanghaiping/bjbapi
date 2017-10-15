/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50714
Source Host           : localhost:3306
Source Database       : bijiben

Target Server Type    : MYSQL
Target Server Version : 50714
File Encoding         : 65001

Date: 2017-10-15 22:38:29
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for thinki_user_authorized
-- ----------------------------
DROP TABLE IF EXISTS `thinki_user_authorized`;
CREATE TABLE `thinki_user_authorized` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of thinki_user_authorized
-- ----------------------------

-- ----------------------------
-- Table structure for think_ad
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
-- Records of think_ad
-- ----------------------------

-- ----------------------------
-- Table structure for think_admin
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
-- Records of think_admin
-- ----------------------------
INSERT INTO `think_admin` VALUES ('1', '1', 'admin', '', 'c2a1f2717ec7c61b5d5d0ab5d98252ed', '1', '', '1507876333', '127.0.0.1', '1507622633');

-- ----------------------------
-- Table structure for think_ad_block
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
-- Records of think_ad_block
-- ----------------------------

-- ----------------------------
-- Table structure for think_ad_slide
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
-- Records of think_ad_slide
-- ----------------------------

-- ----------------------------
-- Table structure for think_auth_group
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
-- Records of think_auth_group
-- ----------------------------
INSERT INTO `think_auth_group` VALUES ('1', '超级管理员', '1', '', '如果禁用', '1');

-- ----------------------------
-- Table structure for think_auth_group_access
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
-- Records of think_auth_group_access
-- ----------------------------

-- ----------------------------
-- Table structure for think_auth_rule
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
) ENGINE=MyISAM AUTO_INCREMENT=135 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of think_auth_rule
-- ----------------------------
INSERT INTO `think_auth_rule` VALUES ('1', '', '0', '系统设置', '1', '1', '', '1', '0', '', 'fa fa-cog', '0');
INSERT INTO `think_auth_rule` VALUES ('2', '', '0', '会员管理', '1', '1', '', '1', '10', '', 'fa fa-user', '1479181812');
INSERT INTO `think_auth_rule` VALUES ('3', '', '0', '内容管理', '1', '1', '', '1', '30', '', '', '1479181812');
INSERT INTO `think_auth_rule` VALUES ('4', '', '0', '订单管理', '1', '1', '', '1', '20', '', '', '1479181812');
INSERT INTO `think_auth_rule` VALUES ('5', '', '0', '模块管理', '1', '1', '', '1', '40', '', '', '1479181812');
INSERT INTO `think_auth_rule` VALUES ('6', '', '1', '系统设置', '1', '1', '', '4', '0', '', 'fa fa-cog', '1479194416');
INSERT INTO `think_auth_rule` VALUES ('7', 'jzadmin/Site/index', '6', '系统参数设置', '1', '1', '', '2', '10', '', '', '1479194570');
INSERT INTO `think_auth_rule` VALUES ('8', '', '1', '数据库备份', '1', '1', '', '4', '20', '', 'fa fa-database', '1479194708');
INSERT INTO `think_auth_rule` VALUES ('9', 'jzadmin/Database/index', '8', '备份数据库', '1', '1', '', '2', '0', '', '', '1479194755');
INSERT INTO `think_auth_rule` VALUES ('10', 'jzadmin/Database/restore', '8', '还原数据库', '1', '1', '', '2', '0', '', '', '1479194805');
INSERT INTO `think_auth_rule` VALUES ('11', '', '5', '广告栏目', '1', '1', '', '4', '30', '', '', '1479194899');
INSERT INTO `think_auth_rule` VALUES ('12', 'jzadmin/Ad/index', '11', '常用广告管理', '1', '1', '', '2', '0', '', '', '1479194924');
INSERT INTO `think_auth_rule` VALUES ('13', 'jzadmin/ad.block/index', '11', '碎片广告管理', '1', '1', '', '2', '0', '', '', '1479194947');
INSERT INTO `think_auth_rule` VALUES ('14', '', '1', '权限管理', '1', '1', '', '4', '10', '', '', '1479195234');
INSERT INTO `think_auth_rule` VALUES ('15', 'jzadmin/Admin/index', '14', '管理员列表', '1', '1', '', '2', '0', '', '', '1479195259');
INSERT INTO `think_auth_rule` VALUES ('17', 'jzadmin/Admin/rule', '14', '权限规则', '1', '1', '', '2', '20', '', '', '1479195335');
INSERT INTO `think_auth_rule` VALUES ('18', '', '4', '订单栏目', '1', '1', '', '4', '0', '', '', '1479195762');
INSERT INTO `think_auth_rule` VALUES ('19', 'jzadmin/Order/index', '18', '订单列表', '1', '1', '', '2', '0', '', '', '1479195778');
INSERT INTO `think_auth_rule` VALUES ('27', 'jzadmin/Index/index', '6', '后台首页显示', '1', '1', '', '2', '0', '', '', '1479196000');
INSERT INTO `think_auth_rule` VALUES ('29', 'jzadmin/admin/add_rule', '17', '添加规则', '1', '1', '', '3', '0', '', '', '1479452956');
INSERT INTO `think_auth_rule` VALUES ('30', 'jzadmin/admin/edit_rule', '17', '修改规则', '1', '1', '', '3', '0', '', '', '1479455674');
INSERT INTO `think_auth_rule` VALUES ('31', 'jzadmin/admin/group', '14', '角色管理', '1', '1', '', '2', '10', '', '', '1479561434');
INSERT INTO `think_auth_rule` VALUES ('32', 'jzadmin/admin/add_group', '31', '添加角色', '1', '1', '', '3', '0', '', '', '1480305029');
INSERT INTO `think_auth_rule` VALUES ('33', 'jzadmin/admin/edit_group', '31', '修改角色', '1', '1', '', '3', '0', '', '', '1480305123');
INSERT INTO `think_auth_rule` VALUES ('34', 'jzadmin/admin/delete_group', '31', '删除角色', '1', '1', '', '3', '0', '', '', '1480305172');
INSERT INTO `think_auth_rule` VALUES ('35', 'jzadmin/admin/changrole', '31', '设置权限', '1', '1', '', '3', '0', '', '', '1480305202');
INSERT INTO `think_auth_rule` VALUES ('36', 'jzadmin/Lang/index', '6', '语言列表管理', '1', '1', '', '2', '20', '', '', '1482203962');
INSERT INTO `think_auth_rule` VALUES ('37', 'jzadmin/Ad/add', '12', '添加广告', '1', '1', '', '3', '0', '', '', '1482477400');
INSERT INTO `think_auth_rule` VALUES ('38', 'jzadmin/Ad/edit', '12', '修改广告', '1', '1', '', '3', '0', '', '', '1482484067');
INSERT INTO `think_auth_rule` VALUES ('39', 'jzadmin/Ad/del', '12', '删除广告', '1', '1', '', '3', '0', '', '', '1482669183');
INSERT INTO `think_auth_rule` VALUES ('40', 'jzadmin/Ad/index', '12', '查看常用广告', '1', '1', '', '3', '0', '', '', '1482669317');
INSERT INTO `think_auth_rule` VALUES ('41', 'jzadmin/Slide/index', '12', '幻灯片列表', '1', '1', '', '3', '0', '', '', '1482669406');
INSERT INTO `think_auth_rule` VALUES ('42', 'jzadmin/Slide/add', '12', '添加幻灯片', '1', '1', '', '3', '0', '', '', '1482669492');
INSERT INTO `think_auth_rule` VALUES ('43', 'jzadmin/Slide/edit', '12', '修改幻灯片', '1', '1', '', '3', '0', '', '', '1482669516');
INSERT INTO `think_auth_rule` VALUES ('44', 'jzadmin/Slide/del', '12', '删除幻灯片', '1', '1', '', '3', '0', '', '', '1482669549');
INSERT INTO `think_auth_rule` VALUES ('45', 'jzadmin/ad.block/add', '13', '添加碎片广告', '1', '1', '', '3', '0', '', '', '1482669654');
INSERT INTO `think_auth_rule` VALUES ('46', 'jzadmin/ad.block/edit', '13', '修改碎片广告', '1', '1', '', '3', '0', '', '', '1482669693');
INSERT INTO `think_auth_rule` VALUES ('47', 'jzadmin/ad.block/del', '13', '删除碎片广告', '1', '1', '', '3', '0', '', '', '1482669721');
INSERT INTO `think_auth_rule` VALUES ('50', '', '2', '会员管理', '1', '1', '', '4', '0', '', '', '1482672168');
INSERT INTO `think_auth_rule` VALUES ('51', 'jzadmin/User/index', '50', '会员列表管理', '1', '1', '', '2', '0', '', '', '1482672402');
INSERT INTO `think_auth_rule` VALUES ('52', 'jzadmin/user.level/index', '50', '会员等级管理', '1', '1', '', '2', '10', '', '', '1482672485');
INSERT INTO `think_auth_rule` VALUES ('104', 'jzadmin/Site/add', '7', '添加系统参数', '1', '1', '', '3', '0', '', '', '1492524055');
INSERT INTO `think_auth_rule` VALUES ('105', 'jzadmin/Site/email', '7', '邮箱设置', '1', '1', '', '3', '0', '', '', '1492524101');
INSERT INTO `think_auth_rule` VALUES ('106', 'jzadmin/Site/sms', '7', '短信设置', '1', '1', '', '3', '0', '', '', '1492524122');
INSERT INTO `think_auth_rule` VALUES ('107', 'jzadmin/Site/watermark', '7', '水印设置', '1', '1', '', '3', '0', '', '', '1492524145');
INSERT INTO `think_auth_rule` VALUES ('108', 'jzadmin/Lang/add', '36', '添加语言', '1', '1', '', '3', '0', '', '', '1492524205');
INSERT INTO `think_auth_rule` VALUES ('109', 'jzadmin/Lang/edit', '36', '修改语言', '1', '1', '', '3', '0', '', '', '1492524223');
INSERT INTO `think_auth_rule` VALUES ('110', 'jzadmin/Lang/dellang', '36', '删除语言', '1', '1', '', '3', '0', '', '', '1492524243');
INSERT INTO `think_auth_rule` VALUES ('111', 'jzadmin/Lang/updateStatus', '36', '便携式更改语言状态', '1', '1', '', '3', '0', '', '', '1492524272');
INSERT INTO `think_auth_rule` VALUES ('112', 'jzadmin/Lang/setlang', '36', '修改语言参数', '1', '1', '', '3', '0', '', '', '1492524294');
INSERT INTO `think_auth_rule` VALUES ('113', 'jzadmin/Lang/addparam', '36', '添加语言参数', '1', '1', '', '3', '0', '', '', '1492524312');
INSERT INTO `think_auth_rule` VALUES ('118', 'jzadmin/Admin/add', '15', '添加管理员', '1', '1', '', '3', '0', '', '', '1492524516');
INSERT INTO `think_auth_rule` VALUES ('119', 'jzadmin/Admin/edit', '15', '修改管理员', '1', '1', '', '3', '0', '', '', '1492524537');
INSERT INTO `think_auth_rule` VALUES ('120', 'jzadmin/Admin/delete_admin', '15', '删除管理员', '1', '1', '', '3', '0', '', '', '1492524558');
INSERT INTO `think_auth_rule` VALUES ('121', 'jzadmin/Admin/delete_rule', '17', '删除规则', '1', '1', '', '3', '0', '', '', '1492524610');
INSERT INTO `think_auth_rule` VALUES ('122', 'jzadmin/Database/export', '9', '保存备份', '1', '1', '', '3', '0', '', '', '1492524738');
INSERT INTO `think_auth_rule` VALUES ('123', 'jzadmin/Database/optimize', '10', '优化表', '1', '1', '', '3', '0', '', '', '1492524777');
INSERT INTO `think_auth_rule` VALUES ('124', 'jzadmin/Database/repair', '10', '修复表', '1', '1', '', '3', '0', '', '', '1492524799');
INSERT INTO `think_auth_rule` VALUES ('125', 'jzadmin/Database/del', '10', '删除备份文件', '1', '1', '', '3', '0', '', '', '1492524826');
INSERT INTO `think_auth_rule` VALUES ('126', 'jzadmin/Database/import', '10', '提交还原数据库', '1', '1', '', '3', '0', '', '', '1492524864');
INSERT INTO `think_auth_rule` VALUES ('127', 'jzadmin/User/edit', '51', '修改用户资料', '1', '1', '', '3', '0', '', '', '1492524942');
INSERT INTO `think_auth_rule` VALUES ('128', 'jzadmin/User/ajax_user_levelid_email', '51', 'Ajax搜素用户', '1', '1', '', '3', '0', '', '', '1492524987');
INSERT INTO `think_auth_rule` VALUES ('129', 'jzadmin/user.level/add_level', '52', '添加会员等级', '1', '1', '', '3', '0', '', '', '1492525054');
INSERT INTO `think_auth_rule` VALUES ('130', 'jzadmin/user.level/edit_level', '52', '修改会员等级', '1', '1', '', '3', '0', '', '', '1492525133');
INSERT INTO `think_auth_rule` VALUES ('131', 'jzadmin/user.level/delete_level', '52', '删除会员等级', '1', '1', '', '3', '0', '', '', '1492525193');
INSERT INTO `think_auth_rule` VALUES ('132', 'jzadmin/user.template/add', '100', '添加模板', '1', '1', '', '3', '0', '', '', '1492525234');
INSERT INTO `think_auth_rule` VALUES ('133', 'jzadmin/user.template/edit', '100', '修改模板', '1', '1', '', '3', '0', '', '', '1492525252');
INSERT INTO `think_auth_rule` VALUES ('134', 'jzadmin/user.template/del', '100', '删除模板', '1', '1', '', '3', '0', '', '', '1492525273');
INSERT INTO `think_auth_rule` VALUES ('100', 'jzadmin/user.template/index', '50', '消息模板管理', '1', '1', '', '2', '20', '', '', '1490625155');

-- ----------------------------
-- Table structure for think_devicetoken
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
-- Records of think_devicetoken
-- ----------------------------

-- ----------------------------
-- Table structure for think_email_verify
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of think_email_verify
-- ----------------------------

-- ----------------------------
-- Table structure for think_lang
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
-- Records of think_lang
-- ----------------------------
INSERT INTO `think_lang` VALUES ('1', '中文', 'cn', '', '1', '', '', '50');

-- ----------------------------
-- Table structure for think_lang_param
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
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of think_lang_param
-- ----------------------------
INSERT INTO `think_lang_param` VALUES ('1', '1', 'api', 'cn', 'PLEASE_INPUT_ACCOUNT', '请输入账号', '请输入账号', '1507627984');
INSERT INTO `think_lang_param` VALUES ('2', '1', 'api', 'cn', 'MAILBOX_FORMAT_ERROR', '邮箱格式错误', '邮箱格式错误', '1507629034');
INSERT INTO `think_lang_param` VALUES ('3', '1', 'api', 'cn', 'MAILBOX_HAS_REGISTERED', '邮箱已注册', '邮箱已注册', '1507629103');
INSERT INTO `think_lang_param` VALUES ('4', '1', 'api', 'cn', 'PASSWORD_FORMAT_ERROR', '密码格式错误', '密码格式错误', '1507629154');
INSERT INTO `think_lang_param` VALUES ('5', '1', 'api', 'cn', 'NICKNAME_EMPTY', '昵称为空', '昵称为空', '1507629192');
INSERT INTO `think_lang_param` VALUES ('6', '1', 'api', 'cn', 'NICKNAME_ALREADY_EXISTS', '昵称已注册', '昵称已注册', '1507629241');
INSERT INTO `think_lang_param` VALUES ('7', '1', 'api', 'cn', 'REG_HAS_FAILED', '注册失败', '注册失败', '1507629396');
INSERT INTO `think_lang_param` VALUES ('8', '1', 'api', 'cn', 'MOBILE_FORMAT_ERROR', '手机号码格式错误', '手机号码格式错误', '1507705768');
INSERT INTO `think_lang_param` VALUES ('9', '1', 'api', 'cn', 'SMS_FORMAT_ERROR', '发送类型错误', '发送类型错误', '1507705876');
INSERT INTO `think_lang_param` VALUES ('10', '1', 'api', 'cn', 'SMS_SUCCESS', '发送成功', '发送成功', '1507706067');
INSERT INTO `think_lang_param` VALUES ('11', '1', 'api', 'cn', 'MOBILE_HAS_REGISTERED', '手机号码已经注册', '手机号码已经注册', '1507706360');
INSERT INTO `think_lang_param` VALUES ('12', '1', 'api', 'cn', 'VERIFY_ERROR', '验证码错误', '验证码错误', '1507706604');
INSERT INTO `think_lang_param` VALUES ('13', '1', 'api', 'cn', 'LOGIN_WAS_SUCCESSFUL', '注册成功', '注册成功', '1507706764');
INSERT INTO `think_lang_param` VALUES ('14', '1', 'api', 'cn', 'MOBILE_ALREADY_USERED', '手机号码可以使用', '手机号码可以使用', '1507707111');
INSERT INTO `think_lang_param` VALUES ('15', '1', 'api', 'cn', 'MAILBOX_ALREADY_USERED', '邮箱可以注册', '邮箱可以注册', '1507707250');
INSERT INTO `think_lang_param` VALUES ('16', '1', 'api', 'cn', 'PLEASE_ENTER_LOGIN', '请登录', '请登录', '1507707536');
INSERT INTO `think_lang_param` VALUES ('17', '1', 'api', 'cn', 'MAILBOX_NOT_REGISTERED', '邮箱未注册', '邮箱未注册', '1507708827');
INSERT INTO `think_lang_param` VALUES ('18', '1', 'api', 'cn', 'ACCOUNT_IS_DISABLED', '账号已禁用', '账号已禁用', '1507708883');
INSERT INTO `think_lang_param` VALUES ('19', '1', 'api', 'cn', 'VERIFY_CODE_CORRECT', '验证码正确', '验证码正确', '1507886902');

-- ----------------------------
-- Table structure for think_note
-- ----------------------------
DROP TABLE IF EXISTS `think_note`;
CREATE TABLE `think_note` (
  `id` int(11) NOT NULL,
  `notebook_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `label_num` int(11) DEFAULT '0',
  `url` varchar(255) DEFAULT '',
  `file_size` float(10,2) DEFAULT '0.00',
  `file_ext` varchar(64) DEFAULT '',
  `ctime` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `notebook_id` (`notebook_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of think_note
-- ----------------------------

-- ----------------------------
-- Table structure for think_notebook
-- ----------------------------
DROP TABLE IF EXISTS `think_notebook`;
CREATE TABLE `think_notebook` (
  `id` int(11) NOT NULL,
  `uid` int(11) DEFAULT '0',
  `name` varchar(255) DEFAULT '',
  `quantity` int(10) DEFAULT '0',
  `ctime` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of think_notebook
-- ----------------------------

-- ----------------------------
-- Table structure for think_note_label
-- ----------------------------
DROP TABLE IF EXISTS `think_note_label`;
CREATE TABLE `think_note_label` (
  `id` int(11) NOT NULL,
  `note_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT '',
  `ctime` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `note_id` (`note_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of think_note_label
-- ----------------------------

-- ----------------------------
-- Table structure for think_site
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
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of think_site
-- ----------------------------
INSERT INTO `think_site` VALUES ('1', '3', 'EMAIL_VERIFY_TIME', '邮箱验证间隔', '60', 'cn', 'text', '单位秒(s)', '', '1507715744');
INSERT INTO `think_site` VALUES ('2', '3', 'Mobile_verify_time', '短信发送间隔', '60', 'cn', 'text', '单位(秒)', '', '1507716654');
INSERT INTO `think_site` VALUES ('3', '3', 'registration_gift_space', '注册赠送空间', '500', 'cn', 'text', '单位(MB)', '', '1507887413');

-- ----------------------------
-- Table structure for think_sms_verify
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of think_sms_verify
-- ----------------------------

-- ----------------------------
-- Table structure for think_user
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
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `mobile` (`mobile`),
  UNIQUE KEY `nickname` (`nickname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of think_user
-- ----------------------------

-- ----------------------------
-- Table structure for think_user_disk
-- ----------------------------
DROP TABLE IF EXISTS `think_user_disk`;
CREATE TABLE `think_user_disk` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `total_disk_space` float(10,2) DEFAULT '0.00',
  `used_disk_space` float(10,2) DEFAULT '0.00',
  `default_disk_space` float(10,2) DEFAULT '0.00',
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of think_user_disk
-- ----------------------------

-- ----------------------------
-- Table structure for think_user_info
-- ----------------------------
DROP TABLE IF EXISTS `think_user_info`;
CREATE TABLE `think_user_info` (
  `uid` int(11) NOT NULL,
  `userip` varchar(32) NOT NULL DEFAULT '',
  `qq` varchar(32) NOT NULL DEFAULT '',
  `sex` tinyint(1) NOT NULL DEFAULT '0',
  `is_email` tinyint(1) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  `country_id` smallint(5) NOT NULL,
  `province` smallint(5) NOT NULL DEFAULT '0',
  `city` smallint(5) NOT NULL DEFAULT '0',
  `city_name` varchar(255) NOT NULL DEFAULT '',
  `district` smallint(5) NOT NULL DEFAULT '0',
  `twon` smallint(5) NOT NULL DEFAULT '0',
  `address` varchar(255) NOT NULL DEFAULT '',
  `birth` int(11) NOT NULL DEFAULT '0',
  `device_name` varchar(128) NOT NULL DEFAULT '',
  UNIQUE KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of think_user_info
-- ----------------------------

-- ----------------------------
-- Table structure for think_user_level
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
-- Records of think_user_level
-- ----------------------------

-- ----------------------------
-- Table structure for think_user_template
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
-- Records of think_user_template
-- ----------------------------
INSERT INTO `think_user_template` VALUES ('1', '用户注册邮件', 'SEND_EMAIL_REG', '<p><strong>尊敬的:{name},您好</strong><br/>感谢您使用服务，邮箱验证邮件已经发送,您只需在app输入验证码：<br/><strong>{verify}</strong></p><p>即可验证邮箱。<br/>如果在操作过程中有什么问题可以联系我们,联系我们,谢谢！<br/><br/></p>', '邮箱验证邮件已发送 ', '1', '0', '验证码{verify},示用户名{name} ', '2', 'cn', '1507709641');
INSERT INTO `think_user_template` VALUES ('2', '找回密码邮件', 'SEND_EMAIL_FIND_PASSWORD', '<p><strong>尊敬的:{name},您好</strong><br/>您的密码找回要求已经得到验证,您只需在APP客户端输入验证码：<br/><strong>{verify}</strong></p><p>输入新的密码后提交，之后您即可使用新的密码登录了。<br/>如果在操作过程中有什么问题可以联系我们的\r\n,谢谢！<br/><br/></p>', '找回密码邮件已发送', '1', '0', '验证码{verify},示用户名{name} ', '2', 'cn', '1507709738');
