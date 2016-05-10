USE  `lt_bbs_db`;
--
-- 数据库: `lt_bbs_db`
--

-- --------------------------------------------------------

--
-- 表的结构 `web_bbs_forum`
--

DROP TABLE IF EXISTS `web_bbs_forum`;
CREATE TABLE IF NOT EXISTS `web_bbs_forum` (
  `forum_id` int(11) NOT NULL AUTO_INCREMENT,
  `ower_uid` int(11) NOT NULL COMMENT '创建人id',
  `name` varchar(50) DEFAULT NULL COMMENT '贴吧名',
  `from` tinyint(2) NOT NULL DEFAULT '1' COMMENT '来源类型, 1为主播, 2为家族, 3为C站',
  `from_id` int(11) NOT NULL DEFAULT '0' COMMENT '来源id, 主播id, 家族id, C站id等',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态, 1为正常, 0为关闭, 2为锁定等',
  PRIMARY KEY (`forum_id`),
  KEY `idx_ower_id` (`ower_uid`) USING BTREE,
  KEY `idx_from_from_id` (`from`,`from_id`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='bbs总版';

-- --------------------------------------------------------

--
-- 表的结构 `web_bbs_forum_purview`
--

DROP TABLE IF EXISTS `web_bbs_forum_purview`;
CREATE TABLE IF NOT EXISTS `web_bbs_forum_purview` (
  `purview_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '子版块id',
  `forum_sid` int(11) NOT NULL COMMENT '子版块id',
  `uid` int(11) NOT NULL COMMENT '拥有权限的用户id',
  `purview` tinyint(2) NOT NULL DEFAULT '1' COMMENT '权限, 1为所有权限, 2为编辑权限, 4为删除权限',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '权限创建时间',
  PRIMARY KEY (`purview_id`),
  KEY `idx_uid_forumsid_createtime` (`uid`,`forum_sid`,`create_time`) USING BTREE,
  KEY `idx_forumsid_createtime` (`forum_sid`,`create_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='子版块权限设置表, 默认为全部权限(删除, 修改)';

-- --------------------------------------------------------

--
-- 表的结构 `web_bbs_forum_sub`
--

DROP TABLE IF EXISTS `web_bbs_forum_sub`;
CREATE TABLE IF NOT EXISTS `web_bbs_forum_sub` (
  `forum_sid` int(11) NOT NULL AUTO_INCREMENT COMMENT 'bbsid',
  `forum_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL COMMENT '子版块名称, 粉丝吧, 家族吧, 议事厅, 讨论组等',
  `visit_rank` int(11) NOT NULL DEFAULT '0' COMMENT '最低访问等级',
  `post_rank` int(11) NOT NULL DEFAULT '0' COMMENT '最低发帖,回复等级',
  `is_check` tinyint(1) NOT NULL DEFAULT '0' COMMENT '发帖是否需要审核, 1为需要审核, 0为不审核',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `flag` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态0正常, 1为隐藏, 2为锁定, 3为关闭',
  PRIMARY KEY (`forum_sid`),
  KEY `idx_forum_id` (`forum_id`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='bbs子版块';

-- --------------------------------------------------------

--
-- 表的结构 `web_bbs_post`
--

DROP TABLE IF EXISTS `web_bbs_post`;
CREATE TABLE IF NOT EXISTS `web_bbs_post` (
  `post_id` int(11) NOT NULL AUTO_INCREMENT,
  `thread_id` int(11) NOT NULL COMMENT '主题id',
  `uid` int(11) NOT NULL COMMENT '回复人id',
  `content` text COMMENT '回复的内容',
  `reports` int(11) NOT NULL COMMENT '举报数',
  `praises` int(11) NOT NULL COMMENT '赞数目',
  `reply_post_id` int(11) NOT NULL COMMENT '被引用的回复id',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0为正常, 1为删除',
  `create_time` int(11) NOT NULL COMMENT '发布时间',
  PRIMARY KEY (`post_id`),
  KEY `idx_isdel_uid_createtime` (`is_del`,`uid`,`create_time`) USING BTREE,
  KEY `idx_isdel_threadid_createtime` (`is_del`,`thread_id`,`create_time`) USING BTREE,
  KEY `idx_uid_threadid_createtime` (`uid`,`thread_id`,`create_time`) USING BTREE,
  KEY `idx_threadid_createtime` (`thread_id`,`create_time`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='主题回复表';

-- --------------------------------------------------------

--
-- 表的结构 `web_bbs_post_action`
--

DROP TABLE IF EXISTS `web_bbs_post_action`;
CREATE TABLE IF NOT EXISTS `web_bbs_post_action` (
  `action_id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL COMMENT '回复id',
  `uid` int(11) NOT NULL COMMENT '动作人',
  `action_type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '动作类型, 1为赞, 0为举报',
  `create_time` int(11) NOT NULL COMMENT '动作时间',
  PRIMARY KEY (`action_id`),
  KEY `idx_uid_type_createtime` (`uid`,`action_type`,`create_time`) USING BTREE,
  KEY `idx_type_createtime` (`action_type`,`create_time`) USING BTREE,
  KEY `idx_postid_type` (`post_id`,`action_type`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='回复动作表, 赞,举报等动作';

-- --------------------------------------------------------

--
-- 表的结构 `web_bbs_thread`
--

DROP TABLE IF EXISTS `web_bbs_thread`;
CREATE TABLE IF NOT EXISTS `web_bbs_thread` (
  `thread_id` int(11) NOT NULL AUTO_INCREMENT,
  `forum_sid` int(11) NOT NULL COMMENT '板块id',
  `title` varchar(255) NOT NULL COMMENT '主题',
  `uid` int(11) NOT NULL COMMENT '主题发布人',
  `content` varchar(150) DEFAULT NULL COMMENT '主题简要内容',
  `posts` int(11) NOT NULL DEFAULT '0' COMMENT '回复数',
  `flag` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否热帖, 0为普通, 1为热帖, 2为置顶, ',
  `is_del` tinyint(2) NOT NULL DEFAULT '0' COMMENT '0为正常, 1为删除',
  `create_time` int(11) NOT NULL COMMENT '发帖时间',
  `last_reply_uid` int(11) NOT NULL DEFAULT '0' COMMENT '最后回复人',
  `last_reply_time` int(11) NOT NULL COMMENT '最后回帖时间',
  PRIMARY KEY (`thread_id`),
  KEY `idx_isdel_uid_createtime` (`is_del`,`uid`,`create_time`) USING BTREE,
  KEY `idx_isdel_forumsid_createtime` (`is_del`,`forum_sid`,`create_time`) USING BTREE,
  KEY `idx_uid_createtime` (`uid`,`create_time`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='贴吧主题表';


ALTER TABLE `web_bbs_thread`
MODIFY COLUMN `flag`  int(11) NOT NULL DEFAULT 0 COMMENT '帖子属性，按位累加, 0为普通, 1为热帖, 2为主题帖有图, ' AFTER `posts`,
MODIFY COLUMN `is_del`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '0为正常, 1为删除' AFTER `flag`,
ADD COLUMN `top`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否置顶，0否，1是' AFTER `flag`,
DROP INDEX `idx_isdel_uid_createtime`,
DROP INDEX `idx_isdel_forumsid_createtime` ,
ADD INDEX `idx_forumsid` (`forum_sid`, `is_del`, `create_time`) USING BTREE ,
DROP INDEX `idx_uid_createtime` ,
ADD INDEX `idx_uid_createtime` (`uid`, `is_del`, `create_time`) USING BTREE ;

ALTER TABLE `web_bbs_post`
DROP INDEX `idx_isdel_uid_createtime`,
DROP INDEX `idx_isdel_threadid_createtime`,
DROP INDEX `idx_threadid_createtime` ,
ADD INDEX `idx_threadid_createtime` (`thread_id`, `is_del`, `create_time`) USING BTREE ,
DROP INDEX `idx_uid_threadid_createtime` ,
ADD INDEX `idx_uid_createtime` (`uid`, `is_del`, `create_time`) USING BTREE ;

ALTER TABLE `web_bbs_post_action`
MODIFY COLUMN `post_id`  int(11) NOT NULL COMMENT '贴子id' AFTER `action_id`,
CHANGE COLUMN `action_type` `praise`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否赞，0否，1是' AFTER `uid`,
ADD COLUMN `report`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否举报，0否，1是' AFTER `praise`,
CHANGE COLUMN `create_time` `praise_time`  int(11) NOT NULL COMMENT '赞动作时间' AFTER `report`,
ADD COLUMN `report_time`  int(11) NOT NULL COMMENT '举报动作时间' AFTER `praise_time`,
DROP INDEX `idx_type_createtime`,
DROP INDEX `idx_uid_type_createtime` ,
DROP INDEX `idx_postid_type` ,
ADD UNIQUE INDEX `idx_postid_uid` (`post_id`, `uid`) USING BTREE ,
ADD INDEX `idx_uid` (`uid`) USING BTREE ;

ALTER TABLE `web_bbs_post`
ADD COLUMN `floor`  int(11) NOT NULL DEFAULT 0 COMMENT '楼层' AFTER `uid`;