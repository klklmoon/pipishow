USE `lt_archives_db`;
--
-- 数据库: `lt_archives_db`
--

-- --------------------------------------------------------

--
-- 表的结构 `web_archives`
--

DROP TABLE IF EXISTS `web_archives`;
CREATE TABLE IF NOT EXISTS `web_archives` (
  `archives_id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户uid',
  `title` varchar(80) NOT NULL COMMENT '档期标题',
  `cat_id` int(11) NOT NULL COMMENT '档期分类',
  `sub_id` int(11) NOT NULL DEFAULT '0' COMMENT '分站ID',
  `recommond` tinyint(1) NOT NULL COMMENT '是否推荐：0-不推荐;1-推荐',
  `notice` varchar(600) NOT NULL COMMENT '公聊公告',
  `private_notice` varchar(600) NOT NULL COMMENT '私聊公告',
  `video` varchar(100) NOT NULL DEFAULT '' COMMENT '离线视频',
  `background` varchar(150) NOT NULL COMMENT '直播间背景',
  `is_hide` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否隐藏:0-不隐藏;1-隐藏',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`archives_id`),
  KEY `index_uid_cat_status` (`uid`,`cat_id`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='档期基本信息表';
-- --------------------------------------------------------

--
-- 表的结构 `web_archives_attribute`
--

DROP TABLE IF EXISTS `web_archives_attribute`;
CREATE TABLE IF NOT EXISTS `web_archives_attribute` (
  `attribute_id` int(11) NOT NULL AUTO_INCREMENT,
  `cat_id` int(11) NOT NULL COMMENT '档期分类ID',
  `name` varchar(30) NOT NULL COMMENT '属性名称',
  `value` varchar(255) NOT NULL COMMENT '属性值',
  PRIMARY KEY (`attribute_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='档期属性表';

-- --------------------------------------------------------

--
-- 表的结构 `web_archives_category`
--

DROP TABLE IF EXISTS `web_archives_category`;
CREATE TABLE IF NOT EXISTS `web_archives_category` (
  `cat_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL COMMENT '档期类型中文名称',
  `en_name` varchar(30) NOT NULL COMMENT '档期类型英文标识',
  PRIMARY KEY (`cat_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='档期分类';

-- --------------------------------------------------------

--
-- 表的结构 `web_archives_live_server`
--

DROP TABLE IF EXISTS `web_archives_live_server`;
CREATE TABLE IF NOT EXISTS `web_archives_live_server` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '视频ID',
  `archives_id` int(11) NOT NULL COMMENT '档期ID',
  `server_id` int(11) NOT NULL COMMENT '视频ID',
  PRIMARY KEY (`id`),
  KEY `index_archives_server` (`archives_id`,`server_id`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='档期视频服务器记录表';

-- --------------------------------------------------------

--
-- 表的结构 `web_archives_purview`
--

DROP TABLE IF EXISTS `web_archives_purview`;
CREATE TABLE IF NOT EXISTS `web_archives_purview` (
  `purview_live_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '直播间权限标识',
  `uid` int(11) NOT NULL COMMENT '用户ＩＤ',
  `archives_id` int(11) NOT NULL COMMENT '档期ＩＤ',
  PRIMARY KEY (`purview_live_id`),
  KEY `idx_uid_archivesid` (`uid`,`archives_id`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='直播间权限';

-- --------------------------------------------------------

--
-- 表的结构 `web_archives_time`
--

DROP TABLE IF EXISTS `web_archives_time`;
CREATE TABLE IF NOT EXISTS `web_archives_time` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `archives_id` int(11) NOT NULL COMMENT '档期ID',
  `live_time` varchar(255) NOT NULL COMMENT '直播起止时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='档期开播预告';

-- --------------------------------------------------------

--
-- 表的结构 `web_archives_user`
--

DROP TABLE IF EXISTS `web_archives_user`;
CREATE TABLE IF NOT EXISTS `web_archives_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户uid',
  `archives_id` int(11) NOT NULL COMMENT '档期ID',
  PRIMARY KEY (`id`),
  KEY `index_uid_archivesId` (`uid`,`archives_id`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='档期拥有者';

-- --------------------------------------------------------

--
-- 表的结构 `web_chat_server`
--

CREATE TABLE `web_chat_server` (
  `chat_id` int(11) NOT NULL AUTO_INCREMENT,
  `archives_id` int(11) NOT NULL COMMENT '档期ID',
  `policy_port` smallint(6) NOT NULL COMMENT '协议端口号',
  `data_port` smallint(6) NOT NULL COMMENT '数据端口号',
  `domain` varchar(100) NOT NULL COMMENT '聊天地址',
  PRIMARY KEY (`chat_id`),
  UNIQUE KEY `archives_index` (`archives_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=991 DEFAULT CHARSET=utf8 COMMENT='聊天进程记录表';


-- --------------------------------------------------------

--
-- 表的结构 `web_global_server`
--

DROP TABLE IF EXISTS `web_global_server`;
CREATE TABLE IF NOT EXISTS `web_global_server` (
  `global_server_id` int(11) NOT NULL AUTO_INCREMENT,
  `domain` varchar(100) NOT NULL COMMENT 'global Server地址',
  `use_num` int(11) NOT NULL COMMENT '使用数量',
  PRIMARY KEY (`global_server_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='globalServer服务器表';

-- --------------------------------------------------------

--
-- 表的结构 `web_live_records`
--

DROP TABLE IF EXISTS `web_live_records`;
CREATE TABLE IF NOT EXISTS `web_live_records` (
  `record_id` int(11) NOT NULL AUTO_INCREMENT,
  `archives_id` int(11) NOT NULL COMMENT '档期ID',
  `sub_title` varchar(80) NOT NULL COMMENT '档期副标题',
  `status` smallint(1) NOT NULL DEFAULT '0' COMMENT '直播状态：0-待开始；1-正在直播；2-直播结束',
  `start_time` int(11) NOT NULL COMMENT '开始时间',
  `end_time` int(11) NOT NULL COMMENT '结束时间',
  `live_time` int(11) NOT NULL COMMENT '开始直播时间',
  `duration` int(11) NOT NULL COMMENT '直播时长',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`record_id`),
  KEY `idx_archives_stime_etime` (`archives_id`,`start_time`,`end_time`) USING BTREE,
  KEY `idx_archives_duration` (`archives_id`,`duration`) USING BTREE,
  KEY `idx_status_startime` (`status`,`start_time`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='档期开播记录表';

-- --------------------------------------------------------

--
-- 表的结构 `web_live_server`
--

DROP TABLE IF EXISTS `web_live_server`;
CREATE TABLE IF NOT EXISTS `web_live_server` (
  `server_id` int(11) NOT NULL AUTO_INCREMENT,
  `import_host` varchar(100) NOT NULL COMMENT '视频输入地址',
  `export_host` varchar(100) NOT NULL COMMENT '视频输出地址',
  `use_num` int(11) NOT NULL COMMENT '使用数量',
  PRIMARY KEY (`server_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='视频服务器列表';

-- --------------------------------------------------------

--
-- 表的结构 `web_sess_stat`
--

DROP TABLE IF EXISTS `web_sess_stat`;
CREATE TABLE IF NOT EXISTS `web_sess_stat` (
  `sess_stat_id` int(11) NOT NULL AUTO_INCREMENT,
  `archives_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '档期id',
  `total` mediumint(8) NOT NULL DEFAULT '0' COMMENT '总人数',
  `online_total` mediumint(8) NOT NULL DEFAULT '0' COMMENT '登入用户总人数',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '产生记录的时间',
  PRIMARY KEY (`sess_stat_id`),
  KEY `archives_id` (`archives_id`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `web_sess_total`
--

DROP TABLE IF EXISTS `web_sess_total`;
CREATE TABLE IF NOT EXISTS `web_sess_total` (
  `archives_id` int(11) unsigned NOT NULL,
  `domain` varchar(100) NOT NULL DEFAULT '1' COMMENT '子系统域名',
  `total` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '总人数',
  `online_total` smallint(6) NOT NULL DEFAULT '0' COMMENT '登入用户人数',
  PRIMARY KEY (`archives_id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `web_user_archives_view`
--

DROP TABLE IF EXISTS `web_user_archives_view`;
CREATE TABLE IF NOT EXISTS `web_user_archives_view` (
  `uid` int(11) NOT NULL COMMENT '用户',
  `archives_id` int(11) NOT NULL COMMENT '档期ID',
  `archives_record_id` int(11) NOT NULL COMMENT '档期直播记录ID',
  `view_time` int(11) NOT NULL COMMENT '浏览时间',
  PRIMARY KEY (`uid`,`archives_id`),
  KEY `idx_uid_viewtime` (`uid`,`view_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户最近观看主播的及其直播记录';






