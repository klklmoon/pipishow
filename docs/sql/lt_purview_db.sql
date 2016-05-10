USE `lt_purview_db` ;

--
-- 表的结构 `web_app`
--

DROP TABLE IF EXISTS `web_app`;
CREATE TABLE IF NOT EXISTS `web_app` (
  `app_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'app标识',
  `app_name` varchar(255) NOT NULL COMMENT 'app中文名',
  `app_enname` varchar(255) NOT NULL COMMENT 'app英文名',
  `app_secret` varchar(100) NOT NULL COMMENT 'app密钥',
  `app_domain` varchar(25) NOT NULL COMMENT 'app的独立域名',
  `app_type` tinyint(3) NOT NULL COMMENT '应用　类型　０表示外部应用　１表示内部应用',
  `app_status` tinyint(3) NOT NULL DEFAULT '1' COMMENT '状态　0表示停用　１表示启用',
  `description` text NOT NULL COMMENT '描述',
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`app_id`),
  KEY `idx_enname` (`app_enname`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='app应用注册表，管理比较小的APP用的'  ;

-- --------------------------------------------------------

--
-- 表的结构 `web_app_token`
--

DROP TABLE IF EXISTS `web_app_token`;
CREATE TABLE IF NOT EXISTS `web_app_token` (
  `token_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '标记ＩＤ',
  `uid` int(11) NOT NULL COMMENT '用户ＩＤ',
  `app_id` int(11) NOT NULL COMMENT '应用ＩＤ',
  `token` varchar(50) NOT NULL COMMENT '生成的token串',
  `valid_time` int(11) NOT NULL COMMENT '第三方生成token时间',
  PRIMARY KEY (`token_id`),
  KEY `idx_uid_appid_token` (`uid`,`app_id`,`token`) USING BTREE,
  KEY `idx_appid_token` (`app_id`,`token`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='应用的token表'  ;

-- --------------------------------------------------------

--
-- 表的结构 `web_purview_items`
--

DROP TABLE IF EXISTS `web_purview_items`;
CREATE TABLE IF NOT EXISTS `web_purview_items` (
  `purview_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '权限ＩＤ',
  `purview_name` varchar(90) NOT NULL COMMENT '权限名称',
  `range` smallint(5) NOT NULL COMMENT '权限范围 ',
  `group` varchar(90) NOT NULL COMMENT '权限分组',
  `module` varchar(35) NOT NULL COMMENT '模块',
  `controller` varchar(35) NOT NULL COMMENT '控制器',
  `action` varchar(35) NOT NULL COMMENT '动作',
  `is_use` tinyint(2) NOT NULL COMMENT '是否启用　０表示关闭，１表示启用',
  `is_tree_display` tinyint(2) NOT NULL COMMENT '是否在树形菜单显示',
  PRIMARY KEY (`purview_id`),
  KEY `idx_isuse_subsystem` (`is_use`,`group`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='权限项' ;

-- --------------------------------------------------------

--
-- 表的结构 `web_purview_roleitem`
--

DROP TABLE IF EXISTS `web_purview_roleitem`;
CREATE TABLE IF NOT EXISTS `web_purview_roleitem` (
  `relation_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '关系ＩＤ',
  `role_id` int(11) NOT NULL COMMENT '角色',
  `purview_id` int(11) NOT NULL COMMENT '权限项',
  `is_use` tinyint(11) NOT NULL,
  PRIMARY KEY (`relation_id`),
  KEY `idx_all_column` (`is_use`,`role_id`,`purview_id`,`relation_id`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='角色与权项对应关系'  ;

-- --------------------------------------------------------

--
-- 表的结构 `web_purview_roles`
--

DROP TABLE IF EXISTS `web_purview_roles`;
CREATE TABLE IF NOT EXISTS `web_purview_roles` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '角色ＩＤ',
  `role_name` varchar(90) NOT NULL COMMENT '角色名称',
  `role_type` smallint(5) NOT NULL COMMENT '角色类型',
  `sub_id` int(11) NOT NULL COMMENT '角色作用的目标ＩＤ',
  `is_use` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否使用',
  `description` varchar(255) NOT NULL COMMENT '角色描述',
  PRIMARY KEY (`role_id`),
  KEY `idx_isuse_type_id` (`is_use`,`role_type`,`sub_id`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='角色表'  ;

-- --------------------------------------------------------

--
-- 表的结构 `web_purview_userop_records`
--

DROP TABLE IF EXISTS `web_purview_userop_records`;
CREATE TABLE IF NOT EXISTS `web_purview_userop_records` (
  `record_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '记录标识',
  `uid` int(11) NOT NULL COMMENT '被操作用户ＩＤ',
  `sub_id` int(11) NOT NULL COMMENT '被操作用户的子系统，如Ｃ站ＩＤ，家族ＩＤ',
  `role_id` int(11) NOT NULL COMMENT '角色',
  `purview_id` int(11) NOT NULL COMMENT '权限项',
  `relation_id` int(11) NOT NULL COMMENT '角色与权限关联ＩＤ',
  `params` varchar(255) NOT NULL COMMENT '调用参数',
  `op_role_id` int(11) NOT NULL COMMENT '操作者角色ＩＤ',
  `op_sub_id` int(11) NOT NULL COMMENT '操作者在什么系统',
  `op_uid` int(11) NOT NULL COMMENT '操作者ＵＩＤ',
  `op_desc` text NOT NULL COMMENT '操作描述',
  `op_ip` varchar(32) NOT NULL COMMENT '操作ＩＰ',
  `op_time` int(11) NOT NULL COMMENT '操作时间',
  PRIMARY KEY (`record_id`),
  KEY `idx_opuid_optime` (`op_uid`,`op_time`) USING BTREE,
  KEY `idx_uid_sub_pur` (`uid`,`op_time`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='用户权限操作记录表' ;

-- --------------------------------------------------------

--
-- 表的结构 `web_purview_userroles`
--

DROP TABLE IF EXISTS `web_purview_userroles`;
CREATE TABLE IF NOT EXISTS `web_purview_userroles` (
  `uid` int(11) NOT NULL COMMENT '用户ＩＤ',
  `role_id` int(11) NOT NULL COMMENT '角色ＩＤ',
  `sub_id` int(11) NOT NULL COMMENT '子系统对象ＩＤ',
  KEY `idx_uid_sub_role` (`uid`,`sub_id`,`role_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户对应的角色表';
