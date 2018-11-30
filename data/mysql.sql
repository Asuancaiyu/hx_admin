# Host: 127.0.0.1  (Version 8.0.11)
# Date: 2018-11-29 19:31:07
# Generator: MySQL-Front 6.0  (Build 2.20)


#
# Structure for table "hx_admin"
#

DROP TABLE IF EXISTS `hx_admin`;
CREATE TABLE `hx_admin` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `username` char(50) NOT NULL DEFAULT '' COMMENT '用户名',
  `password` varchar(255) NOT NULL DEFAULT '' COMMENT '登陆密码',
  `phone` char(11) NOT NULL DEFAULT '' COMMENT '手机号',
  `email` varchar(50) NOT NULL DEFAULT '' COMMENT '邮箱',
  `nickname` char(30) DEFAULT '' COMMENT '昵称',
  `avatar_src` varchar(255) DEFAULT '' COMMENT '头像',
  `realname` char(30) DEFAULT '' COMMENT '实名',
  `fex` enum('man','woman','other','secret') DEFAULT 'secret' COMMENT '性别（男，女，其他，保密）',
  `address` varchar(255) DEFAULT '' COMMENT '地址',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '账号状态（1启用，0禁用）',
  `register_time` varchar(15) DEFAULT '' COMMENT '注册时间',
  `modify_time` varchar(15) DEFAULT '' COMMENT '修改时间',
  `register_ip` char(20) DEFAULT '' COMMENT '注册ip',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除（0未删除，1删除）',
  PRIMARY KEY (`id`,`phone`,`email`,`username`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=DYNAMIC COMMENT='用户信息';

#
# Data for table "hx_admin"
#

INSERT INTO `hx_admin` VALUES (4,'admin','5365a82a366175a6eebfc653d53ef20b','17683232018','1012083552@qq.com','吃酸菜的鱼','','coding fish','secret','',1,'1538995763','1538995763','127.0.0.1',0),(26,'hello','5365a82a366175a6eebfc653d53ef20b','19999999999','','','','管理','secret','',1,'1542768873','1542768873','127.0.0.1',0);

#
# Structure for table "hx_admin_bind_group"
#

DROP TABLE IF EXISTS `hx_admin_bind_group`;
CREATE TABLE `hx_admin_bind_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` varchar(10) DEFAULT '' COMMENT '管理员id',
  `group_id` int(6) unsigned DEFAULT '0' COMMENT '组id',
  `create_time` varchar(15) NOT NULL DEFAULT '' COMMENT '绑定时间',
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`,`group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='管理员绑定组';

#
# Data for table "hx_admin_bind_group"
#

INSERT INTO `hx_admin_bind_group` VALUES (27,'4',1000,'1542947279'),(30,'26',1013,'1543468387');

#
# Structure for table "hx_admin_bind_role"
#

DROP TABLE IF EXISTS `hx_admin_bind_role`;
CREATE TABLE `hx_admin_bind_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `admin_id` int(4) unsigned DEFAULT '0' COMMENT '管理员id',
  `role_id` int(6) unsigned NOT NULL DEFAULT '0' COMMENT '角色id',
  `create_time` varchar(15) DEFAULT '' COMMENT '绑定时间',
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`,`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='管理员绑定角色';

#
# Data for table "hx_admin_bind_role"
#

INSERT INTO `hx_admin_bind_role` VALUES (37,4,10,'1542947279'),(40,26,17,'1543468387');

#
# Structure for table "hx_admin_group"
#

DROP TABLE IF EXISTS `hx_admin_group`;
CREATE TABLE `hx_admin_group` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `group_name` varchar(50) NOT NULL DEFAULT '' COMMENT '组名',
  `pid` int(6) unsigned DEFAULT '0' COMMENT '父组id',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '组描述',
  `create_time` varchar(15) DEFAULT '' COMMENT '创建时间',
  `modify_time` varchar(15) DEFAULT '' COMMENT '修改时间',
  `path` varchar(255) NOT NULL DEFAULT ',',
  `is_parent` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否为父组',
  `open` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否打开',
  `ban_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '禁止删除',
  PRIMARY KEY (`id`),
  KEY `path` (`path`)
) ENGINE=InnoDB AUTO_INCREMENT=1014 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='管理员组';

#
# Data for table "hx_admin_group"
#

INSERT INTO `hx_admin_group` VALUES (1000,'顶级组',0,'最顶级的组','','',',1000,',1,1,1),(1010,'组2',1000,'父','1542367686','',',1000,1010,',0,0,0),(1013,'信息部',1000,'信息部','1542880288','',',1000,1013,',0,0,0);

#
# Structure for table "hx_admin_group_bind_role"
#

DROP TABLE IF EXISTS `hx_admin_group_bind_role`;
CREATE TABLE `hx_admin_group_bind_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) unsigned DEFAULT '0' COMMENT '组id',
  `role_id` int(11) unsigned DEFAULT '0' COMMENT '角色id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='管理员组绑定角色';

#
# Data for table "hx_admin_group_bind_role"
#

INSERT INTO `hx_admin_group_bind_role` VALUES (1,1004,10),(27,1005,10),(30,1006,10),(42,1010,10),(59,1000,10);

#
# Structure for table "hx_admin_p_menu"
#

DROP TABLE IF EXISTS `hx_admin_p_menu`;
CREATE TABLE `hx_admin_p_menu` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='菜单权限';

#
# Data for table "hx_admin_p_menu"
#


#
# Structure for table "hx_admin_p_operate"
#

DROP TABLE IF EXISTS `hx_admin_p_operate`;
CREATE TABLE `hx_admin_p_operate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) unsigned DEFAULT '0' COMMENT '父id',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '权限名称',
  `url` varchar(255) DEFAULT '' COMMENT 'url 地址',
  `sort` int(11) unsigned DEFAULT '6000' COMMENT '排序',
  `create_time` varchar(15) DEFAULT '' COMMENT '创建时间',
  `path` varchar(255) NOT NULL DEFAULT ',' COMMENT '路径',
  `delete_disable` tinyint(3) unsigned DEFAULT '0' COMMENT '禁用删除 0为不禁止',
  `open` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否展开 0为不展开',
  `is_parent` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否为父权限 0 为不是父权限',
  PRIMARY KEY (`id`),
  KEY `path` (`path`,`pid`,`name`,`id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='操作权限';

#
# Data for table "hx_admin_p_operate"
#

INSERT INTO `hx_admin_p_operate` VALUES (1,0,'所有权限','/admin',0,'',',1,',1,1,1),(42,1,'管理中心','/admin/center',0,'1542882454',',1,42,',0,0,0),(43,1,'管理员管理','null',0,'1542882933',',1,43,',0,0,0),(44,43,'管理员列表','/admin/list',0,'1542882970',',1,43,44,',0,0,0);

#
# Structure for table "hx_admin_role"
#

DROP TABLE IF EXISTS `hx_admin_role`;
CREATE TABLE `hx_admin_role` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) NOT NULL DEFAULT '' COMMENT '角色名称',
  `description` varchar(255) DEFAULT '' COMMENT '描述',
  `create_time` varchar(15) DEFAULT '' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='管理员角色';

#
# Data for table "hx_admin_role"
#

INSERT INTO `hx_admin_role` VALUES (10,'超级管理员','超级管理员，拥有至高无上的权限',''),(17,'普通管理员','普通管理员','');

#
# Structure for table "hx_member"
#

DROP TABLE IF EXISTS `hx_member`;
CREATE TABLE `hx_member` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `sid` char(30) NOT NULL DEFAULT '' COMMENT 'id 壳（防id遍历）',
  `username` char(50) NOT NULL DEFAULT '' COMMENT '用户名',
  `password` varchar(255) NOT NULL DEFAULT '' COMMENT '登陆密码',
  `phone` char(11) NOT NULL DEFAULT '' COMMENT '手机号',
  `email` varchar(50) NOT NULL DEFAULT '' COMMENT '邮箱',
  `avatar_src` varchar(255) DEFAULT '' COMMENT '头像',
  `realname` char(30) DEFAULT '' COMMENT '实名',
  `fex` enum('man','woman','other','secret') DEFAULT 'secret' COMMENT '性别（男，女，其他，保密）',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '账号状态（1启用，0禁用）',
  `register_time` varchar(15) DEFAULT '' COMMENT '注册时间',
  `modify_time` varchar(15) DEFAULT '' COMMENT '修改时间',
  `register_ip` char(20) DEFAULT '' COMMENT '注册ip',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除（0未删除，1删除）',
  `addres` varchar(255) DEFAULT '' COMMENT '住址',
  PRIMARY KEY (`id`,`phone`,`email`,`username`,`sid`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=DYNAMIC COMMENT='用户信息';

#
# Data for table "hx_member"
#

INSERT INTO `hx_member` VALUES (3,'','','518458d05a055a2b0841980d92c78de4','16666666666','1012083522@qq.cc','','','secret',1,'1543123775','','127.0.0.1',0,''),(4,'','','5365a82a366175a6eebfc653d53ef20b','17683232018','101208@qq.cc','','','secret',1,'1543479477','','127.0.0.1',0,''),(12,'','','5365a82a366175a6eebfc653d53ef20b','17777777777','13215456@qq.cc','','','secret',1,'1543482992','','127.0.0.1',0,'');

#
# Structure for table "hx_member_bind_group"
#

DROP TABLE IF EXISTS `hx_member_bind_group`;
CREATE TABLE `hx_member_bind_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` varchar(10) DEFAULT '' COMMENT '用户id',
  `group_id` int(6) unsigned DEFAULT '0' COMMENT '组id',
  `create_time` varchar(15) NOT NULL DEFAULT '' COMMENT '绑定时间',
  PRIMARY KEY (`id`),
  KEY `admin_id` (`member_id`,`group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=DYNAMIC COMMENT='用户绑定组';

#
# Data for table "hx_member_bind_group"
#

INSERT INTO `hx_member_bind_group` VALUES (3,'12',8,'');

#
# Structure for table "hx_member_bind_tag"
#

DROP TABLE IF EXISTS `hx_member_bind_tag`;
CREATE TABLE `hx_member_bind_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` varchar(10) DEFAULT '' COMMENT '用户id',
  `tag_id` int(6) unsigned DEFAULT '0' COMMENT '标签id',
  `create_time` varchar(15) NOT NULL DEFAULT '' COMMENT '绑定时间',
  PRIMARY KEY (`id`),
  KEY `admin_id` (`member_id`,`tag_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=DYNAMIC COMMENT='用户绑定标签';

#
# Data for table "hx_member_bind_tag"
#

INSERT INTO `hx_member_bind_tag` VALUES (5,'12',3,''),(6,'12',5,'');

#
# Structure for table "hx_member_group"
#

DROP TABLE IF EXISTS `hx_member_group`;
CREATE TABLE `hx_member_group` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `group_name` varchar(50) NOT NULL DEFAULT '' COMMENT '组名',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '组描述',
  `create_time` varchar(15) DEFAULT '' COMMENT '创建时间',
  `modify_time` varchar(15) DEFAULT '' COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=DYNAMIC COMMENT='用户组';

#
# Data for table "hx_member_group"
#

INSERT INTO `hx_member_group` VALUES (1,'asdf','asdf','1543466074',''),(8,'阿斯蒂芬','阿斯蒂芬','1543468790',''),(9,'许','辅导费','1543468794','');

#
# Structure for table "hx_member_tag"
#

DROP TABLE IF EXISTS `hx_member_tag`;
CREATE TABLE `hx_member_tag` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `tag_name` varchar(50) NOT NULL DEFAULT '' COMMENT '标签名',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  `create_time` varchar(15) DEFAULT '' COMMENT '创建时间',
  `modify_time` varchar(15) DEFAULT '' COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=DYNAMIC COMMENT='用户标签';

#
# Data for table "hx_member_tag"
#

INSERT INTO `hx_member_tag` VALUES (3,'圣达菲','新政策v','1543470322',''),(5,'父风格','','1543482992','');

#
# Structure for table "hx_role_bind_p_operate"
#

DROP TABLE IF EXISTS `hx_role_bind_p_operate`;
CREATE TABLE `hx_role_bind_p_operate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `auth_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '权限id',
  `role_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '角色id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=79 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='角色绑定权限';

#
# Data for table "hx_role_bind_p_operate"
#

INSERT INTO `hx_role_bind_p_operate` VALUES (65,1,10),(77,42,17),(78,44,17);

#
# View "hx_view_admin_group_bind_role"
#

DROP VIEW IF EXISTS `hx_view_admin_group_bind_role`;
CREATE
  ALGORITHM = UNDEFINED
  VIEW `hx_view_admin_group_bind_role`
  AS
SELECT
  `g`.`id` AS 'group_id',
  `g`.`group_name`,
  `g`.`pid` AS 'group_pid',
  `g`.`description` AS 'group_desc',
  `g`.`path` AS 'group_path',
  `r`.`id` AS 'role_id',
  `r`.`role_name`,
  `r`.`description` AS 'role_desc'
FROM
  ((`hx_admin_group_bind_role` b
    LEFT JOIN `hx_admin_role` r ON ((`b`.`role_id` = `r`.`id`)))
    LEFT JOIN `hx_admin_group` g ON ((`b`.`group_id` = `g`.`id`)));

#
# View "hx_view_member_bind_group"
#

DROP VIEW IF EXISTS `hx_view_member_bind_group`;
CREATE
  ALGORITHM = MERGE
  VIEW `hx_view_member_bind_group`
  AS
SELECT
  `g`.`id` AS 'group_id', `b`.`member_id`, `g`.`group_name`, `g`.`description`
FROM
  (`hx_member_bind_group` b
    LEFT JOIN `hx_member_group` g ON ((`g`.`id` = `b`.`group_id`)));

#
# View "hx_view_member_bind_tag"
#

DROP VIEW IF EXISTS `hx_view_member_bind_tag`;
CREATE
  ALGORITHM = UNDEFINED
  VIEW `hx_view_member_bind_tag`
  AS
SELECT
  `t`.`id` AS 'tag_id', `b`.`member_id`, `t`.`tag_name`, `t`.`description`
FROM
  (`hx_member_bind_tag` b
    LEFT JOIN `hx_member_tag` t ON ((`t`.`id` = `b`.`tag_id`)));

#
# View "hx_view_role_operate_auth"
#

DROP VIEW IF EXISTS `hx_view_role_operate_auth`;
CREATE
  ALGORITHM = UNDEFINED
  VIEW `hx_view_role_operate_auth`
  AS
SELECT
  `b`.`id` AS 'b_id',
  `b`.`auth_id`,
  `b`.`role_id`,
  `a`.`pid`,
  `a`.`name`,
  `a`.`url`,
  `a`.`sort`,
  `a`.`open`,
  `a`.`path`,
  `a`.`is_parent`
FROM
  (`hx`.`hx_role_bind_p_operate` b
    JOIN `hx`.`hx_admin_p_operate` a ON ((`a`.`id` = `b`.`auth_id`)))
WITH CASCADED CHECK OPTION;
