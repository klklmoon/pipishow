USE `lt_common_db`;
--
-- 数据库: `lt_common_db`
--

-- --------------------------------------------------------

--
-- 表的结构 `web_area_channel`
--

DROP TABLE IF EXISTS `web_area_channel`;
CREATE TABLE IF NOT EXISTS `web_area_channel` (
  `area_relation_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '关系标识ID',
  `area_channel_id` smallint(5) NOT NULL COMMENT '频道ID',
  `province` varchar(255) NOT NULL COMMENT '省份ID',
  `city` varchar(255) NOT NULL COMMENT '城市ID',
  PRIMARY KEY (`area_relation_id`),
  UNIQUE KEY `uidx_all` (`province`,`city`,`area_channel_id`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='分类频道对应的城市与省份';

--
-- 转存表中的数据 `web_area_channel`
--


-- --------------------------------------------------------

--
-- 表的结构 `web_black_word`
--

DROP TABLE IF EXISTS `web_black_word`;
CREATE TABLE IF NOT EXISTS `web_black_word` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL COMMENT '敏感词',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '屏蔽方式：0:部分；1：全部',
  `replace` varchar(30) NOT NULL DEFAULT '*' COMMENT '屏蔽后替换显示',
  `word_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '敏感词类型, 0为聊天敏感词, 1为昵称敏感词',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否启用：0：不启用，1：启用',
  `displayorder` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_name_wordtype` (`name`,`word_type`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `web_black_word`
--


-- --------------------------------------------------------

--
-- 表的结构 `web_channel`
--

DROP TABLE IF EXISTS `web_channel`;
CREATE TABLE IF NOT EXISTS `web_channel` (
  `channel_id` smallint(5) NOT NULL AUTO_INCREMENT COMMENT '频道ID',
  `channel_name` varchar(100) NOT NULL COMMENT '频道名称',
  `is_show_index` tinyint(1) NOT NULL COMMENT '是否在道页展示',
  `index_sort` smallint(5) NOT NULL COMMENT '首页展示排序值',
  PRIMARY KEY (`channel_id`),
  KEY `idx_index` (`is_show_index`,`index_sort`) USING BTREE,
  KEY `uidx_name` (`channel_name`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='频道分类表';

--
-- 转存表中的数据 `web_channel`
--


-- --------------------------------------------------------

--
-- 表的结构 `web_channel_sub`
--

DROP TABLE IF EXISTS `web_channel_sub`;
CREATE TABLE IF NOT EXISTS `web_channel_sub` (
  `sub_channel_id` smallint(5) NOT NULL COMMENT '子频道ID',
  `channel_id` smallint(5) NOT NULL,
  `sub_name` varchar(100) NOT NULL COMMENT '子频道名称',
  `desc` varchar(255) NOT NULL COMMENT '描述',
  `dotey_sort` tinyint(3) NOT NULL COMMENT '主播排序方式',
  `dotey_num` int(11) NOT NULL COMMENT '改频道下主播数量',
  `is_show_sindex` tinyint(1) NOT NULL COMMENT '是否道页展示',
  `index_ssort` smallint(5) NOT NULL COMMENT '展示排序',
  PRIMARY KEY (`sub_channel_id`),
  KEY `idx_channel_index` (`sub_channel_id`,`is_show_sindex`,`index_ssort`) USING BTREE,
  KEY `idx_channel` (`channel_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='子频道';

--
-- 转存表中的数据 `web_channel_sub`
--


-- --------------------------------------------------------

--
-- 表的结构 `web_common_operate`
--

DROP TABLE IF EXISTS `web_common_operate`;
CREATE TABLE IF NOT EXISTS `web_common_operate` (
  `operate_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '运营ID',
  `category` smallint(5) NOT NULL COMMENT '分类',
  `sub_category` smallint(5) NOT NULL COMMENT '子分类',
  `target_id` int(11) NOT NULL COMMENT '分类对应的目标ID',
  `subject` varchar(255) NOT NULL COMMENT '主题',
  `content` text NOT NULL COMMENT '内容',
  `textlink` varchar(255) NOT NULL COMMENT '导航链接',
  `piclink` varchar(255) NOT NULL COMMENT '图片链接',
  `sort` smallint(5) NOT NULL COMMENT '分类排序值',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`operate_id`),
  KEY `idx_category_sort` (`category`,`sub_category`,`sort`) USING BTREE,
  KEY `idx_category_targetid` (`category`,`sub_category`,`target_id`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='运营通且表';

--
-- 转存表中的数据 `web_common_operate`
--


-- --------------------------------------------------------

--
-- 表的结构 `web_dotey_category_index`
--

DROP TABLE IF EXISTS `web_dotey_category_index`;
CREATE TABLE IF NOT EXISTS `web_dotey_category_index` (
  `uid` int(11) NOT NULL COMMENT '主播用户',
  `archives_id` int(11) NOT NULL COMMENT '档期ID',
  `rank` smallint(5) NOT NULL COMMENT '主播等级',
  `channel_area_id` smallint(5) NOT NULL COMMENT '主播子地区频道ID，叠加',
  `status` tinyint(2) NOT NULL COMMENT '直播状态 0表示待直播 1表示正在直播',
  `tag_id` varchar(150) NOT NULL COMMENT '标签ID，逗号切隔',
  `title` varchar(150) NOT NULL,
  `sub_title` varchar(150) NOT NULL,
  `live_time` int(11) NOT NULL,
  `start_time` int(11) NOT NULL,
  PRIMARY KEY (`uid`,`archives_id`),
  KEY `idx_area_type_status` (`channel_area_id`,`rank`,`status`) USING BTREE,
  KEY `idx_area_status` (`channel_area_id`,`status`) USING BTREE,
  KEY `idx_rank_status` (`rank`,`status`) USING BTREE,
  KEY `idx_status` (`status`) USING BTREE,
  KEY `idx_tagid` (`tag_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='主播节目分类页检索';

--
-- 转存表中的数据 `web_dotey_category_index`
--


-- --------------------------------------------------------

--
-- 表的结构 `web_dotey_channel`
--

DROP TABLE IF EXISTS `web_dotey_channel`;
CREATE TABLE IF NOT EXISTS `web_dotey_channel` (
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `channel_id` int(11) NOT NULL COMMENT '频道分类ID',
  `sub_channel_id` int(11) NOT NULL COMMENT '子频道分类ID',
  `target_relation_id` int(11) NOT NULL,
  PRIMARY KEY (`uid`,`channel_id`,`sub_channel_id`),
  KEY `idx_relation_id` (`target_relation_id`) USING BTREE,
  KEY `idx_channel` (`channel_id`,`sub_channel_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='主播与频道关联ID';

--
-- 转存表中的数据 `web_dotey_channel`
--


-- --------------------------------------------------------

--
-- 表的结构 `web_dotey_period_count`
--

DROP TABLE IF EXISTS `web_dotey_period_count`;
CREATE TABLE IF NOT EXISTS `web_dotey_period_count` (
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '主播ID',
  `yesterday_songs` int(11) DEFAULT NULL COMMENT '昨日点唱数',
  `week_songs` int(11) DEFAULT NULL COMMENT '本周点唱数',
  `month_songs` int(11) NOT NULL COMMENT '本月点唱数',
  `super_songs` int(11) NOT NULL COMMENT '超级点唱数',
  `yesterday_charms` int(11) NOT NULL COMMENT '昨日获取魅力值',
  `week_charms` int(11) NOT NULL COMMENT '本周获取魅力值',
  `month_charms` int(11) NOT NULL COMMENT '本月获取魅力值',
  `super_charms` int(11) NOT NULL COMMENT '魅力总数',
  `yesterday_livetime` int(11) NOT NULL COMMENT '昨天直播时长',
  `week_livetime` int(11) NOT NULL COMMENT '本周直播时长',
  `month_livetime` int(11) NOT NULL COMMENT '本月直播时长',
  `super_livetime` int(11) NOT NULL COMMENT '直播总时长',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='主播区段统计表';

--
-- 转存表中的数据 `web_dotey_period_count`
--


-- --------------------------------------------------------

--
-- 表的结构 `web_dotey_today_recommand`
--

DROP TABLE IF EXISTS `web_dotey_today_recommand`;
CREATE TABLE `web_dotey_today_recommand` (
  `uid` int(11) NOT NULL COMMENT '主播用户',
  `archives_id` int(11) NOT NULL COMMENT '档期ID',
  `type` tinyint(1) NOT NULL COMMENT '类型 0表示上周 1表示昨日',
  `title` varchar(150) NOT NULL,
  `sub_title` varchar(150) NOT NULL,
  `live_time` int(11) NOT NULL,
  `start_time` int(11) NOT NULL,
  `charms` int(11) NOT NULL,
  PRIMARY KEY (`uid`,`archives_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='主播今日推荐';


--
-- 转存表中的数据 `web_dotey_today_recommand`
--


-- --------------------------------------------------------

--
-- 表的结构 `web_kefu`
--

DROP TABLE IF EXISTS `web_kefu`;
CREATE TABLE IF NOT EXISTS `web_kefu` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `contact_type` tinyint(2) NOT NULL COMMENT '联系类型 0表示qq',
  `kefu_type` tinyint(2) NOT NULL COMMENT '客服类型',
  `contact_name` varchar(30) NOT NULL COMMENT '联系人',
  `contact_account` varchar(20) NOT NULL COMMENT '联系方式 qq类型为qq号',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_contatct_kefu_time` (`contact_type`,`kefu_type`,`create_time`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='客服表';

--
-- 转存表中的数据 `web_kefu`
--


-- --------------------------------------------------------

--
-- 表的结构 `web_user_suggest`
--

DROP TABLE IF EXISTS `web_user_suggest`;
CREATE TABLE IF NOT EXISTS `web_user_suggest` (
  `suggest_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '标识ID',
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `type` tinyint(3) NOT NULL COMMENT '类型',
  `is_handle` tinyint(3) NOT NULL COMMENT '是否已处理',
  `contact` varchar(25) NOT NULL COMMENT '联系方式',
  `attach` varchar(150) NOT NULL COMMENT '附件',
  `content` text NOT NULL COMMENT '内容',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`suggest_id`),
  KEY `idx_type_create_time` (`type`,`create_time`) USING BTREE,
  KEY `idx_handle_type_ctime` (`is_handle`,`type`,`create_time`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='用户建议表';

--
-- 转存表中的数据 `web_user_suggest`
--


-- --------------------------------------------------------

--
-- 表的结构 `web_website_config`
--

DROP TABLE IF EXISTS `web_website_config`;
CREATE TABLE IF NOT EXISTS `web_website_config` (
  `c_key` varchar(50) NOT NULL,
  `c_type` enum('int','string','array','class','float') NOT NULL,
  `c_value` text NOT NULL,
  PRIMARY KEY (`c_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='站点配置';

--
-- 转存表中的数据 `web_website_config`
--


CREATE TABLE `web_index_rightdata` (
`uid`  int(11) NOT NULL COMMENT '主播' ,
`type`  tinyint(3) NOT NULL COMMENT '0表示新秀主播 1表示最新加入 2表示明星主播' ,
`charms`  int(11) NOT NULL ,
INDEX `idx_type_charms` (`type`, `charms`) USING BTREE,
INDEX `idx_uid`(`uid`) 
)ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COMMENT='首页主播右侧数据';

--
-- 转存表中的数据 `web_task`
--

CREATE TABLE `web_task` (
  `tid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '任务编号',
  `name` varchar(150) NOT NULL DEFAULT '' COMMENT '任务名称',
  `content` varchar(200) NOT NULL COMMENT '任务描述',
  `egg` double(8,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '奖励皮蛋数',
  `pic` varchar(200) NOT NULL COMMENT '图片路径',
  PRIMARY KEY (`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='任务列表';

--
-- 转存表中的数据 `web_task_records`
--

CREATE TABLE `web_task_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户id',
  `tid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '任务编号',
  `create_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uid_tid` (`uid`,`tid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='任务记录表';