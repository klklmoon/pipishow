USE `lt_consume_db`;
--
-- 数据库: `lt_consume_db`
--

-- --------------------------------------------------------

--
-- 表的结构 `web_dotey_rank`
--

DROP TABLE IF EXISTS `web_dotey_rank`;
CREATE TABLE IF NOT EXISTS `web_dotey_rank` (
  `rank_id` smallint(4) NOT NULL AUTO_INCREMENT COMMENT '等级标识',
  `rank` smallint(4) NOT NULL COMMENT '等级值',
  `name` varchar(25) NOT NULL COMMENT '中文名称',
  `charm` int(11) NOT NULL COMMENT '魅力值升级上限',
  `house_m_num` smallint(5) NOT NULL COMMENT '可成为房间管理员的数量',
  `divieded_scale` smallint(4) NOT NULL DEFAULT '80' COMMENT '分成比例',
  `divieded_rate` smallint(4) NOT NULL DEFAULT '100' COMMENT '兑换摔',
  PRIMARY KEY (`rank_id`),
  UNIQUE KEY `idx_rank` (`rank`) USING BTREE,
  KEY `idx_charm_num` (`charm`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='主播等级表';

--
-- 转存表中的数据 `web_dotey_rank`
--


-- --------------------------------------------------------

--
-- 表的结构 `web_dotey_song`
--

DROP TABLE IF EXISTS `web_dotey_song`;
CREATE TABLE IF NOT EXISTS `web_dotey_song` (
  `song_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '歌曲ＩＤ',
  `dotey_id` int(11) NOT NULL COMMENT '主播ＩＤ',
  `name` varchar(50) NOT NULL COMMENT '歌曲名',
  `singer` varchar(50) NOT NULL COMMENT '歌唱者',
  `pipiegg` int(11) NOT NULL COMMENT '皮蛋数',
  `charm` int(11) NOT NULL COMMENT '魅力值',
  `charm_points` int(11) NOT NULL COMMENT '魅力点',
  `dedication` int(11) NOT NULL COMMENT '贡献值',
  `egg_points` int(11) NOT NULL COMMENT '皮点',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`song_id`),
  KEY `idx_doteyid` (`dotey_id`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='主播歌曲预定表';

--
-- 转存表中的数据 `web_dotey_song`
--


-- --------------------------------------------------------

--
-- 表的结构 `web_gift_category`
--

DROP TABLE IF EXISTS `web_gift_category`;
CREATE TABLE IF NOT EXISTS `web_gift_category` (
  `category_id` tinyint(3) NOT NULL AUTO_INCREMENT COMMENT '分类ＩＤ',
  `cat_name` varchar(55) NOT NULL COMMENT '分类名称',
  `cat_enname` varchar(55) NOT NULL COMMENT '分类英文名称',
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='礼物分类表';

--
-- 转存表中的数据 `web_gift_category`
--


-- --------------------------------------------------------

--
-- 表的结构 `web_gift_effect`
--

DROP TABLE IF EXISTS `web_gift_effect`;
CREATE TABLE IF NOT EXISTS `web_gift_effect` (
  `effect_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '效果ＩＤ',
  `gift_id` int(11) NOT NULL COMMENT '礼物ＩＤ',
  `effect_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '特效显示类型：0-flash效果，1-图片效果',
  `num` smallint(6) NOT NULL COMMENT '达到一定效果的数量',
  `timeout` smallint(3) NOT NULL COMMENT '时长',
  `position` tinyint(3) NOT NULL COMMENT '显示位置，０表示全屏居中，１表示聊区域　２表示视频区域',
  `effect` varchar(255) NOT NULL COMMENT '效果　图片或进flash',
  PRIMARY KEY (`effect_id`),
  KEY `idx_giftid_quantity` (`gift_id`,`num`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='礼物效果';
--
-- 转存表中的数据 `web_gift_effect`
--


-- --------------------------------------------------------

--
-- 表的结构 `web_gift_info`
--

DROP TABLE IF EXISTS `web_gift_info`;
CREATE TABLE `web_gift_info` (
  `gift_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '礼物ＩＤ',
  `cat_id` tinyint(3) NOT NULL COMMENT '礼物分类ＩＤ',
  `zh_name` varchar(50) NOT NULL COMMENT '礼物中文名称',
  `en_name` varchar(50) NOT NULL COMMENT '礼物英文名称',
  `gift_type` int(11) NOT NULL COMMENT '礼物类型　1表示主站　２表示游戏　４表示商城',
  `shop_type` smallint(6) NOT NULL DEFAULT '1' COMMENT '商城礼物类型，按位可叠加。１表示普通　２表示热买　４表示新品　８表示推荐',
  `is_display` tinyint(2) NOT NULL COMMENT '是否显示',
  `image` varchar(255) NOT NULL COMMENT '礼物图片',
  `pipiegg` decimal(10,2) NOT NULL COMMENT '消耗的皮蛋数',
  `charm` int(11) NOT NULL COMMENT '魅力值',
  `charm_points` int(11) NOT NULL COMMENT '魅力点',
  `dedication` int(11) NOT NULL COMMENT '贡献值',
  `egg_points` int(11) NOT NULL COMMENT '皮点',
  `buy_limit` tinyint(1) DEFAULT '0' COMMENT '购买数量限制：0-不限；1-限制',
  `sell_nums` int(11) NOT NULL COMMENT '出售数量',
  `sell_grade` smallint(5) NOT NULL COMMENT '出售等级',
  `sort` int(11) NOT NULL COMMENT '排序',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  `remark` varchar(255) NOT NULL COMMENT '文字描述',
  PRIMARY KEY (`gift_id`),
  KEY `idx_catid_display_sort` (`cat_id`,`is_display`,`sort`) USING BTREE,
  KEY `idx_typeid_catid` (`gift_type`,`cat_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='礼物基本信息表';

--
-- 转存表中的数据 `web_gift_info`
--


-- --------------------------------------------------------

--
-- 表的结构 `web_medal_list`
--

DROP TABLE IF EXISTS `web_medal_list`;
CREATE TABLE IF NOT EXISTS `web_medal_list` (
  `mid` smallint(6) unsigned NOT NULL AUTO_INCREMENT COMMENT '勋章ID',
  `name` varchar(50) NOT NULL COMMENT '勋章名称',
  `type` tinyint(3) NOT NULL COMMENT '勋章类型 0表示普通用户，1表示主播',
  `desc` varchar(255) NOT NULL COMMENT '描述',
  `icon` varchar(30) NOT NULL COMMENT '勋章图标',
  `ctime` int(11) NOT NULL COMMENT '勋章创建时间',
  PRIMARY KEY (`mid`),
  KEY `idx_type_ctime` (`type`,`ctime`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='勋章表';

--
-- 转存表中的数据 `web_medal_list`
--


-- --------------------------------------------------------

--
-- 表的结构 `web_pay`
--

DROP TABLE IF EXISTS `web_pay`;
CREATE TABLE IF NOT EXISTS `web_pay` (
  `pay_id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `pay_type` tinyint(3) NOT NULL COMMENT '类型',
  `live_times` int(11) NOT NULL DEFAULT '0' COMMENT '达标直播小时数',
  `live_days` int(11) NOT NULL DEFAULT '0' COMMENT '达标直播天数 (有效天)',
  `charm_points` int(11) NOT NULL DEFAULT '0' COMMENT '达标魅力点',
  `basic_salary` int(11) NOT NULL DEFAULT '0' COMMENT '基础奖励 (底薪)',
  `bonus` int(11) NOT NULL DEFAULT '0' COMMENT '奖金',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `is_del` int(11) NOT NULL DEFAULT '0' COMMENT '0表示起效, 1表示删除, 不起效',
  PRIMARY KEY (`pay_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='主播报酬规则表';

--
-- 转存表中的数据 `web_pay`
--


-- --------------------------------------------------------

--
-- 表的结构 `web_props`
--

DROP TABLE IF EXISTS `web_props`;
CREATE TABLE IF NOT EXISTS `web_props` (
  `prop_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '道具ID',
  `cat_id` smallint(5) NOT NULL COMMENT '道具分类ID',
  `name` varchar(150) NOT NULL COMMENT '道具名称',
  `en_name` varchar(25) NOT NULL COMMENT '道具英文名称',
  `pipiegg` decimal(12,4) NOT NULL COMMENT '价格',
  `image` varchar(100) NOT NULL COMMENT '图标',
  `charm` int(11) NOT NULL COMMENT '魅力值',
  `charm_points` int(11) NOT NULL COMMENT '魅力点',
  `status` tinyint(2) NOT NULL COMMENT '0表示正常 1表示停用 2表示赠品',
  `rank` smallint(3) NOT NULL COMMENT '当道具为赠品时用户获取道具的等级',
  `dedication` int(11) NOT NULL COMMENT '贡献值',
  `egg_points` int(11) NOT NULL COMMENT '皮点',
  `sort` smallint(5) NOT NULL COMMENT '排序',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`prop_id`),
  UNIQUE KEY `en_name` (`en_name`) USING BTREE,
  KEY `idx_status_cat_ctime` (`status`,`cat_id`,`create_time`) USING BTREE,
  KEY `idx_status_sort` (`cat_id`,`status`,`sort`) USING BTREE,
  KEY `idx_status_rank_sort` (`status`,`rank`,`sort`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='道具基本信息表';

--
-- 转存表中的数据 `web_props`
--


-- --------------------------------------------------------

--
-- 表的结构 `web_props_attribute`
--

DROP TABLE IF EXISTS `web_props_attribute`;
CREATE TABLE IF NOT EXISTS `web_props_attribute` (
  `pattr_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '道具属性ID',
  `prop_id` int(11) NOT NULL COMMENT '道具ID',
  `attr_id` smallint(5) NOT NULL COMMENT '道具属性ID',
  `value` varchar(255) NOT NULL COMMENT '属性值',
  PRIMARY KEY (`pattr_id`),
  UNIQUE KEY `idx_prop_attr` (`prop_id`,`attr_id`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='道具属性表';

--
-- 转存表中的数据 `web_props_attribute`
--


-- --------------------------------------------------------

--
-- 表的结构 `web_props_category`
--

DROP TABLE IF EXISTS `web_props_category`;
CREATE TABLE IF NOT EXISTS `web_props_category` (
  `cat_id` smallint(5) NOT NULL AUTO_INCREMENT COMMENT '分类ID',
  `name` varchar(60) NOT NULL COMMENT '分类名称',
  `en_name` varchar(50) NOT NULL COMMENT '英文名称',
  `is_display` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否显示,0表示不显示，1表示显示',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`cat_id`),
  UNIQUE KEY `idx_name` (`name`) USING BTREE,
  UNIQUE KEY `en_name` (`en_name`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='道具分类表';

--
-- 转存表中的数据 `web_props_category`
--


-- --------------------------------------------------------

--
-- 表的结构 `web_props_cat_attribute`
--

DROP TABLE IF EXISTS `web_props_cat_attribute`;
CREATE TABLE IF NOT EXISTS `web_props_cat_attribute` (
  `attr_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '属性ID',
  `cat_id` smallint(5) NOT NULL COMMENT '道具分类ID',
  `attr_name` varchar(60) NOT NULL COMMENT '属性名称',
  `attr_enname` varchar(25) NOT NULL COMMENT '英文名称',
  `is_display` tinyint(4) NOT NULL COMMENT '0表示不限示 1表示限示',
  `attr_value` text NOT NULL COMMENT '属性默认值',
  `attr_type` tinyint(4) NOT NULL COMMENT '属性类弄 0表示input 1表示radio 2表示checkbox 3表示select 4表示textarea 5表file',
  `is_multi` tinyint(4) NOT NULL COMMENT '是否是多选',
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`attr_id`),
  KEY `idx_catid` (`cat_id`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='道具属性表';

--
-- 转存表中的数据 `web_props_cat_attribute`
--


-- --------------------------------------------------------

--
-- 表的结构 `web_props_config`
--

DROP TABLE IF EXISTS `web_props_config`;
CREATE TABLE IF NOT EXISTS `web_props_config` (
  `prop_id` int(11) NOT NULL DEFAULT '0' COMMENT '道具ＩＤ',
  `prop_category` varchar(30) NOT NULL COMMENT '道具分类',
  `prop_enname` varchar(30) NOT NULL COMMENT '道具英文名称',
  `config` text COMMENT '道具配置',
  UNIQUE KEY `idx_category_name` (`prop_category`,`prop_enname`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='道具参数表　如守护经验加成';

--
-- 转存表中的数据 `web_props_config`
--


-- --------------------------------------------------------

--
-- 表的结构 `web_stars_rank`
--

DROP TABLE IF EXISTS `web_stars_rank`;
CREATE TABLE IF NOT EXISTS `web_stars_rank` (
  `stars_id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL COMMENT '星级名称',
  `stars` int(6) NOT NULL DEFAULT '0' COMMENT '星级数量',
  `pipiegg` decimal(11,2) NOT NULL COMMENT '所需皮蛋数',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：0-未启用;1-启用',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`stars_id`),
  KEY `name` (`name`) USING BTREE,
  KEY `charm_num` (`pipiegg`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `web_stars_rank`
--


-- --------------------------------------------------------

--
-- 表的结构 `web_user_checkin`
--

DROP TABLE IF EXISTS `web_user_checkin`;
CREATE TABLE IF NOT EXISTS `web_user_checkin` (
  `checkin_id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户uid',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '签到类型：1-普通签到；2-月卡签到',
  `reward_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '签到奖励类型：1-礼物;2-皮蛋;3-道具',
  `target_id` int(11) NOT NULL DEFAULT '0' COMMENT '物品id',
  `num` int(10) NOT NULL COMMENT '奖励数量',
  `pipiegg` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '皮蛋价值',
  `info` varchar(100) NOT NULL COMMENT '签到赠送记录',
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`checkin_id`),
  KEY `idx_uid_type_createtime` (`uid`,`type`,`create_time`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='签到领取礼物记录表';

--
-- 转存表中的数据 `web_user_checkin`
--


-- --------------------------------------------------------

--
-- 表的结构 `web_user_consume_attribute`
--

DROP TABLE IF EXISTS `web_user_consume_attribute`;
CREATE TABLE IF NOT EXISTS `web_user_consume_attribute` (
  `uid` int(11) NOT NULL COMMENT '用户ＩＤ',
  `pipiegg` decimal(11,2) NOT NULL COMMENT '用户皮蛋数量',
  `freeze_pipiegg` decimal(11,2) NOT NULL COMMENT '冻结皮蛋',
  `consume_pipiegg` decimal(11,2) NOT NULL COMMENT '用户消耗的皮蛋总数',
  `dedication` int(11) NOT NULL COMMENT '用户贡献值',
  `charm` int(11) NOT NULL COMMENT '主播魅力值',
  `charm_points` int(11) NOT NULL COMMENT '主播魅力点',
  `egg_points` int(11) NOT NULL COMMENT '用户皮点',
  `rank` smallint(5) NOT NULL COMMENT '用户等级',
  `dotey_rank` smallint(5) NOT NULL COMMENT '主播等级',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `web_user_consume_attribute`
--


-- --------------------------------------------------------

--
-- 表的结构 `web_user_gift_bag`
--

DROP TABLE IF EXISTS `web_user_gift_bag`;
CREATE TABLE IF NOT EXISTS `web_user_gift_bag` (
  `bag_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '背包ＩＤ',
  `uid` int(11) NOT NULL,
  `gift_id` int(11) NOT NULL COMMENT '礼物ＩＤ',
  `num` int(11) NOT NULL COMMENT '数量',
  PRIMARY KEY (`bag_id`),
  KEY `uidx_uid_presentid` (`uid`,`gift_id`,`num`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='礼物背包';

--
-- 转存表中的数据 `web_user_gift_bag`
--


-- --------------------------------------------------------

--
-- 表的结构 `web_user_medal`
--

DROP TABLE IF EXISTS `web_user_medal`;
CREATE TABLE IF NOT EXISTS `web_user_medal` (
  `rid` int(11) NOT NULL AUTO_INCREMENT,
  `mid` smallint(6) NOT NULL COMMENT '勋章ID',
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `type` tinyint(3) NOT NULL COMMENT '发送类型 0表示系统颁发 1表示用户正常获取 2表示快乐星期六领取',
  `ctime` int(11) NOT NULL COMMENT '颁发时间',
  `vtime` int(11) NOT NULL COMMENT '有效时间',
  PRIMARY KEY (`rid`),
  UNIQUE KEY `idx_type_uid_mid` (`type`,`uid`,`mid`) USING BTREE,
  KEY `idx_type_uid_vtime` (`type`,`uid`,`vtime`) USING BTREE,
  KEY `idx_type_uid_ctime` (`type`,`uid`,`ctime`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='勋章发放表';

--
-- 转存表中的数据 `web_user_medal`
--


-- --------------------------------------------------------

--
-- 表的结构 `web_user_props_attribute`
--

DROP TABLE IF EXISTS `web_user_props_attribute`;
CREATE TABLE IF NOT EXISTS `web_user_props_attribute` (
  `uid` int(11) NOT NULL COMMENT '用户ＩＤ',
  `car` smallint(5) NOT NULL COMMENT '正在使用的座驾标识',
  `vip` smallint(11) NOT NULL COMMENT '正在使用的ＶＩＰ标识',
  `stars` smallint(11) NOT NULL COMMENT '星级',
  `is_hidden` smallint(11) NOT NULL COMMENT '是否隐身　１表示隐身，０表示未隐身',
  `vip_type` tinyint(2) NOT NULL COMMENT 'ＶＩＰ类型　０表示无，１表示黄，2表示紫',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `web_user_props_attribute`
--


-- --------------------------------------------------------

--
-- 表的结构 `web_user_props_bag`
--

DROP TABLE IF EXISTS `web_user_props_bag`;
CREATE TABLE IF NOT EXISTS `web_user_props_bag` (
  `bag_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '背包ＩＤ',
  `uid` int(11) NOT NULL COMMENT 'uid,跟userbase中一致',
  `prop_id` int(11) NOT NULL COMMENT '道具id',
  `cat_id` tinyint(3) NOT NULL COMMENT '道具分类ID',
  `record_sid` int(11) NOT NULL COMMENT '流水线ＩＤ',
  `target_id` int(11) NOT NULL COMMENT '道具作用对象ＩＤ.如category是守护时　为主播ＩＤ',
  `num` int(11) NOT NULL DEFAULT '0' COMMENT '数量',
  `valid_time` int(11) NOT NULL DEFAULT '0' COMMENT '过期时间',
  PRIMARY KEY (`bag_id`),
  UNIQUE KEY `uidx_uid_propid` (`uid`,`prop_id`) USING BTREE,
  KEY `idx_uid_category` (`uid`,`cat_id`) USING BTREE,
  KEY `idx_targetid_category` (`target_id`,`cat_id`,`valid_time`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='道具背包表';

--
-- 转存表中的数据 `web_user_props_bag`
--


-- --------------------------------------------------------

--
-- 表的结构 `web_user_props_use`
--

DROP TABLE IF EXISTS `web_user_props_use`;
CREATE TABLE IF NOT EXISTS `web_user_props_use` (
  `use_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `target_id` int(11) NOT NULL COMMENT '目标ＩＤ　如use_type表示直播间时　就是档期ＩＤ',
  `record_sid` int(11) NOT NULL COMMENT '购具购买流水线记录',
  `uid` int(11) NOT NULL COMMENT '使用者id',
  `to_uid` int(11) NOT NULL COMMENT '使用对象id',
  `prop_id` int(11) NOT NULL COMMENT '道具id',
  `cat_id` smallint(5) NOT NULL COMMENT '道具分类ＩＤ',
  `use_type` tinyint(3) NOT NULL DEFAULT '0' COMMENT '使用类型　1-vip使用;2-普通贴条',
  `client` tinyint(3) DEFAULT '0' COMMENT '使用位置:0-档期',
  `num` int(11) NOT NULL DEFAULT '1' COMMENT '使用数量',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `valid_time` int(11) NOT NULL COMMENT '失效时间',
  PRIMARY KEY (`use_id`),
  KEY `idx_uid_catid_createtime` (`uid`,`cat_id`,`create_time`) USING BTREE,
  KEY `idx_touid_catid_validtime` (`to_uid`,`cat_id`,`valid_time`) USING BTREE,
  KEY `idx_recordsid` (`record_sid`) USING BTREE,
  KEY `idx_uid_propid_createtime` (`uid`,`prop_id`,`create_time`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='道具使用记录表';

--
-- 转存表中的数据 `web_user_props_use`
--


-- --------------------------------------------------------

--
-- 表的结构 `web_user_rank`
--

DROP TABLE IF EXISTS `web_user_rank`;
CREATE TABLE IF NOT EXISTS `web_user_rank` (
  `rank_id` smallint(4) NOT NULL AUTO_INCREMENT COMMENT '等级标识',
  `rank` smallint(4) NOT NULL COMMENT '等级值',
  `name` varchar(20) NOT NULL COMMENT '中文名称',
  `dedication` bigint(15) NOT NULL COMMENT '贡献值升级上限',
  `house_m_num` smallint(5) NOT NULL COMMENT '可成为房间管理员的数量',
  PRIMARY KEY (`rank_id`),
  UNIQUE KEY `idx_rank` (`rank`) USING BTREE,
  KEY `idx_dedication` (`dedication`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='用户等级表';

--
-- 转存表中的数据 `web_user_rank`
--


-- --------------------------------------------------------

--
-- 表的结构 `web_user_song`
--

DROP TABLE IF EXISTS `web_user_song`;
CREATE TABLE IF NOT EXISTS `web_user_song` (
  `record_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '记录ＩＤ',
  `song_id` int(11) NOT NULL COMMENT '点歌ＩＤ',
  `target_id` int(11) NOT NULL COMMENT '目的ＩＤ　默认为档期ＩＤ',
  `uid` int(11) NOT NULL COMMENT '点歌用户',
  `to_uid` int(11) NOT NULL COMMENT '对象用户　默认为主播用户',
  `name` varchar(255) NOT NULL COMMENT '歌曲名',
  `singer` varchar(50) NOT NULL COMMENT '歌手',
  `pipiegg` int(11) NOT NULL COMMENT '皮蛋数',
  `charm` int(11) NOT NULL COMMENT '魅力值',
  `charm_points` int(11) NOT NULL COMMENT '魅力点',
  `dedication` int(11) NOT NULL COMMENT '贡献值',
  `egg_points` int(11) NOT NULL COMMENT '皮点',
  `is_handle` tinyint(1) NOT NULL COMMENT '处理状态，０表未处理，１表示已处理，２表示已取消',
  `create_time` int(11) NOT NULL COMMENT '点歌时间',
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`record_id`),
  KEY `idx_uid_doteyid_ctime` (`uid`,`to_uid`,`create_time`) USING BTREE,
  KEY `idx_uid_archives_ctime` (`uid`,`target_id`,`create_time`) USING BTREE,
  KEY `idx_dotey_ctime` (`to_uid`,`create_time`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='用户点歌表';

DROP TABLE IF EXISTS `web_user_task_records`;
CREATE TABLE IF NOT EXISTS  `web_user_task_records` (
  `record_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '记录ID',
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `task_type` varchar(25) NOT NULL COMMENT '任务类型 yiruite,task,how_song,lovebm',
  `target_id` int(11) NOT NULL COMMENT '第三方ID,如档期ID',
  `pipiegg` decimal(11,2) NOT NULL COMMENT '所获皮蛋数',
  `task_serial` varchar(50) NOT NULL COMMENT '任务序列号、获者是交易号',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`record_id`),
  KEY `idx_tastserial` (`task_type`,`task_serial`) USING BTREE,
  KEY `idx_uid_tasktype_createtime` (`uid`,`task_type`,`create_time`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1602125 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `web_user_song`
--


DROP PROCEDURE IF EXISTS `proc_addEggs`;
CREATE PROCEDURE `proc_addEggs`(IN `userid` int(11),IN `pipieggs` decimal(11,2))
    COMMENT '直接加皮蛋\r\n@author 贺新\r\n@date 2013-04-04\r\n@version v0.1'
BEGIN
DECLARE flag INT DEFAULT 0;
DECLARE error_count INT DEFAULT 0;
DECLARE is_exist INT DEFAULT 0;
DECLARE EXIT HANDLER FOR SQLEXCEPTION 
BEGIN
SET flag = 0;
select flag;
END;
SET SESSION TRANSACTION ISOLATION LEVEL SERIALIZABLE;

IF(userid <= 0 OR pipieggs <= 0) THEN
SET flag = 0;
ELSE
START TRANSACTION;
SELECT uid INTO is_exist FROM web_user_consume_attribute WHERE uid = userid;
IF(is_exist != userid) THEN
INSERT web_user_consume_attribute(uid, pipiegg) VALUES(userid, pipieggs);
SET flag = 1;
ELSE
UPDATE web_user_consume_attribute SET pipiegg = pipiegg + pipieggs WHERE uid = userid;
SET flag = 1;
END IF;

SELECT @@error_count INTO error_count;
IF(error_count > 0) THEN
ROLLBACK;
SET flag = 0;
END IF;
COMMIT ;
END IF;
SELECT flag;
END;




DROP PROCEDURE IF EXISTS `proc_addFreezeEggs`;
CREATE PROCEDURE `proc_addFreezeEggs`(IN `userid` int(11),IN `pipieggs` decimal(11,2))
    COMMENT '用来添加担保消费用的冻结皮蛋\r\n@author 贺新\r\n@date 2013-04-04\r\n@version v0.1'
BEGIN
DECLARE flag INT DEFAULT 0;
DECLARE error_count INT DEFAULT 0;
DECLARE is_exist INT DEFAULT 0;
DECLARE EXIT HANDLER FOR SQLEXCEPTION 
BEGIN
SET flag = 0;
select flag;
END;
SET SESSION TRANSACTION ISOLATION LEVEL SERIALIZABLE;

IF(userid <= 0 OR pipieggs <= 0) THEN
SET flag = 0;
ELSE
START TRANSACTION;
SELECT uid INTO is_exist FROM web_user_consume_attribute WHERE uid = userid;
IF(is_exist != userid) THEN
INSERT web_user_consume_attribute(uid, pipiegg, freeze_pipiegg) VALUES(userid, pipieggs, pipieggs);
SET flag = 1;
ELSE
UPDATE web_user_consume_attribute SET pipiegg = pipiegg + pipieggs, freeze_pipiegg = freeze_pipiegg + pipieggs WHERE uid = userid;
SET flag = 1;
END IF;

SELECT @@error_count INTO error_count;
IF(error_count > 0) THEN
ROLLBACK;
SET flag = 0;
END IF;
COMMIT ;
END IF;
SELECT flag;
END;




DROP PROCEDURE IF EXISTS `proc_consumeCharmPoint`;
CREATE PROCEDURE `proc_consumeCharmPoint`(IN `userid` int,IN `charmpoints` int)
    COMMENT '兑换魅力值\r\n@author 郭少波\r\n@date 2013-05-06\r\n@version v0.1'
BEGIN
DECLARE flag INT DEFAULT 0;
DECLARE error_count INT DEFAULT 0;
DECLARE charmpoint_balance DECIMAL DEFAULT 0;
DECLARE EXIT HANDLER FOR SQLEXCEPTION 
BEGIN
SET flag = 0;
select flag;
END;
SET SESSION TRANSACTION ISOLATION LEVEL SERIALIZABLE;

IF(userid <= 0 OR charmpoints <= 0) THEN
SET flag = 0;
ELSE
START TRANSACTION;
SELECT charm_points INTO charmpoint_balance FROM web_user_consume_attribute WHERE uid = userid;

IF(charmpoint_balance >= charmpoints) THEN
UPDATE web_user_consume_attribute SET charm_points = charm_points - charmpoints WHERE uid = userid;
SET flag = 1;
ELSE
SET flag = 0;
END IF;

SELECT @@error_count INTO error_count;
IF(error_count > 0) THEN
ROLLBACK;
SET flag = 0;
END IF;
COMMIT ;
END IF;
SELECT flag;
END;




DROP PROCEDURE IF EXISTS `proc_consumeEggs`;
CREATE PROCEDURE `proc_consumeEggs`(IN `userid` int(11),IN `pipieggs` decimal(11,2))
    COMMENT '消费皮蛋\r\n@author 贺新\r\n@date 2013-04-04\r\n@version v0.1'
BEGIN
DECLARE flag INT DEFAULT 0;
DECLARE error_count INT DEFAULT 0;
DECLARE pipiegg_balance DECIMAL(11,2) DEFAULT 0;
DECLARE EXIT HANDLER FOR SQLEXCEPTION 
BEGIN
SET flag = 0;
select flag;
END;
SET SESSION TRANSACTION ISOLATION LEVEL SERIALIZABLE;

IF(userid <= 0 OR pipieggs <= 0) THEN
SET flag = 0;
ELSE
START TRANSACTION;
SELECT pipiegg - freeze_pipiegg INTO pipiegg_balance FROM web_user_consume_attribute WHERE uid = userid;

IF(pipiegg_balance >= pipieggs) THEN
UPDATE web_user_consume_attribute SET pipiegg = pipiegg - pipieggs, consume_pipiegg = consume_pipiegg + pipieggs WHERE uid = userid;
SET flag = 1;
ELSE
SET flag = 0;
END IF;

SELECT @@error_count INTO error_count;
IF(error_count > 0) THEN
ROLLBACK;
SET flag = 0;
END IF;
COMMIT ;
END IF;
SELECT flag;
END;




DROP PROCEDURE IF EXISTS `proc_freezeEggs`;
CREATE PROCEDURE `proc_freezeEggs`(IN `userid` int(11),IN `pipieggs` decimal(11,2))
    COMMENT '冻结皮蛋\r\n@author 贺新\r\n@date 2013-04-04\r\n@version v0.1'
BEGIN
DECLARE flag INT DEFAULT 0;
DECLARE error_count INT DEFAULT 0;
DECLARE pipiegg_balance DECIMAL(11,2) DEFAULT 0;
DECLARE EXIT HANDLER FOR SQLEXCEPTION 
BEGIN
SET flag = 0;
select flag;
END;
SET SESSION TRANSACTION ISOLATION LEVEL SERIALIZABLE;

IF(userid <= 0 OR pipieggs <= 0) THEN
SET flag = 0;
ELSE
START TRANSACTION;
SELECT pipiegg - freeze_pipiegg INTO pipiegg_balance FROM web_user_consume_attribute WHERE uid = userid;

IF(pipiegg_balance >= pipieggs) THEN
UPDATE web_user_consume_attribute SET freeze_pipiegg = freeze_pipiegg + pipieggs WHERE uid = userid;
SET flag = 1;
ELSE
SET flag = 0;
END IF;

SELECT @@error_count INTO error_count;
IF(error_count > 0) THEN
ROLLBACK;
SET flag = 0;
END IF;
COMMIT ;
END IF;
SELECT flag;
END;




DROP PROCEDURE IF EXISTS `proc_reducecharm`;
CREATE PROCEDURE `proc_reducecharm`(IN `userid` int,IN `charms` int)
    COMMENT '扣减魅力值\r\n@author 苏朋\r\n@date 2013-05-09\r\n@version v0.1'
BEGIN
DECLARE flag INT DEFAULT 0;
DECLARE error_count INT DEFAULT 0;
DECLARE charm_balance DECIMAL DEFAULT 0;
DECLARE EXIT HANDLER FOR SQLEXCEPTION 
BEGIN
SET flag = 0;
select flag;
END;
SET SESSION TRANSACTION ISOLATION LEVEL SERIALIZABLE;

IF(userid <= 0 OR charms <= 0) THEN
SET flag = 0;
ELSE
START TRANSACTION;
SELECT charm INTO charm_balance FROM web_user_consume_attribute WHERE uid = userid;

IF(charm_balance >= charms) THEN
UPDATE web_user_consume_attribute SET charm = charm - charms WHERE uid = userid;
SET flag = 1;
ELSE
SET flag = 0;
END IF;

SELECT @@error_count INTO error_count;
IF(error_count > 0) THEN
ROLLBACK;
SET flag = 0;
END IF;
COMMIT ;
END IF;
SELECT flag;
END;




DROP PROCEDURE IF EXISTS `proc_reduceGiftBag`;
CREATE PROCEDURE `proc_reduceGiftBag`(IN `userId` INT,IN `giftId` INT,IN `send_num` INT)
    COMMENT '背包礼物送出\r\n@author lei wei\r\n@date 2013/4/7'
BEGIN
 DECLARE flag INT DEFAULT 0;
 DECLARE error_count INT DEFAULT 0;
 DECLARE quantity INT DEFAULT 0;
 DECLARE EXIT HANDLER FOR SQLEXCEPTION 
BEGIN
SET flag = 0;
select flag;
END;
 SET SESSION TRANSACTION ISOLATION LEVEL SERIALIZABLE;
 
IF(userId<=0 OR giftId<=0) THEN
SET flag=0;
ELSE
START TRANSACTION;
SELECT num INTO quantity FROM web_user_gift_bag WHERE uid= userId AND gift_id= giftId;
IF(quantity>0 AND (quantity-send_num)>=0) THEN
UPDATE web_user_gift_bag SET num= num - send_num WHERE uid= userId AND gift_id= giftId;
SET flag = 1;
END IF;
SELECT @@error_count INTO error_count;
IF(error_count > 0) THEN
SET flag = 0;
ROLLBACK;
END IF;
COMMIT ;
END IF;
SELECT flag;
END;




DROP PROCEDURE IF EXISTS `proc_transferEggs`;
CREATE PROCEDURE `proc_transferEggs`(IN `from_uid` int, IN `to_uid` int, IN `pipieggs` decimal(11,2))
    COMMENT '皮蛋转账，用于客户处理用户充错人的情况\r\n@author 贺新\r\n@date 2013-05-17\r\n@version v0.1'
BEGIN
DECLARE flag INT DEFAULT 0;
DECLARE error_count INT DEFAULT 0;
DECLARE pipiegg_balance DECIMAL(11,2) DEFAULT 0;
DECLARE is_exist INT DEFAULT 0;
DECLARE EXIT HANDLER FOR SQLEXCEPTION 
BEGIN
SET flag = 0;
select flag;
END;
SET SESSION TRANSACTION ISOLATION LEVEL SERIALIZABLE;

IF(from_uid <= 0 OR to_uid <=0 OR pipieggs <= 0) THEN
SET flag = 0;
ELSE
START TRANSACTION;
SELECT pipiegg - freeze_pipiegg INTO pipiegg_balance FROM web_user_consume_attribute WHERE uid = from_uid;

IF(pipiegg_balance >= pipieggs) THEN
UPDATE web_user_consume_attribute SET pipiegg = pipiegg - pipieggs WHERE uid = from_uid;
SELECT uid INTO is_exist FROM web_user_consume_attribute WHERE uid = to_uid;
IF(is_exist != to_uid) THEN
INSERT web_user_consume_attribute(uid, pipiegg) VALUES(to_uid, pipieggs);
ELSE
UPDATE web_user_consume_attribute SET pipiegg = pipiegg + pipieggs WHERE uid = to_uid;
END IF;
SET flag = 1;
ELSE
SET flag = 0;
END IF;

SELECT @@error_count INTO error_count;
IF(error_count > 0) THEN
ROLLBACK;
SET flag = 0;
END IF;
COMMIT ;
END IF;
SELECT flag;
END;




DROP PROCEDURE IF EXISTS `proc_unAddFreezeEggs`;
CREATE PROCEDURE `proc_unAddFreezeEggs`(IN `userid` int(11),IN `pipieggs` decimal(11,2))
    COMMENT '撤销添加担保消费用的冻结皮蛋\r\n@author 贺新\r\n@date 2013-04-04\r\n@version v0.'
BEGIN
DECLARE flag INT DEFAULT 0;
DECLARE error_count INT DEFAULT 0;
DECLARE is_exist INT DEFAULT 0;
DECLARE pipiegg_blance DECIMAL(11,2) DEFAULT 0;
DECLARE freeze_pipiegg_blance DECIMAL(11,2) DEFAULT 0;
DECLARE EXIT HANDLER FOR SQLEXCEPTION 
BEGIN
SET flag = 0;
select flag;
END;
SET SESSION TRANSACTION ISOLATION LEVEL SERIALIZABLE;

IF(userid <= 0 OR pipieggs <= 0) THEN
SET flag = 0;
ELSE
START TRANSACTION;
SELECT uid INTO is_exist FROM web_user_consume_attribute WHERE uid = userid;
IF(is_exist != userid) THEN
SET flag = 0;
ELSE
SELECT pipiegg INTO pipiegg_blance FROM web_user_consume_attribute WHERE uid = userid;
SELECT freeze_pipiegg INTO freeze_pipiegg_blance FROM web_user_consume_attribute WHERE uid = userid;
IF(pipiegg_blance >= pipieggs AND freeze_pipiegg_blance >= pipieggs) THEN
UPDATE web_user_consume_attribute SET pipiegg = pipiegg - pipieggs, freeze_pipiegg = freeze_pipiegg - pipieggs WHERE uid = userid;
SET flag = 1;
ELSE
SET flag = 0;
END IF;
END IF;

SELECT @@error_count INTO error_count;
IF(error_count > 0) THEN
ROLLBACK;
SET flag = 0;
END IF;
COMMIT ;
END IF;
SELECT flag;
END;




DROP PROCEDURE IF EXISTS `proc_unFreezeEggs`;
CREATE PROCEDURE `proc_unFreezeEggs`(IN `userid` int(11),IN `pipieggs` decimal(11,2))
    COMMENT '释放冻结皮蛋\r\n@author 贺新\r\n@date 2013-04-04\r\n@version v0.1'
BEGIN
DECLARE flag INT DEFAULT 0;
DECLARE error_count INT DEFAULT 0;
DECLARE freeze_pipiegg_balance DECIMAL(11,2) DEFAULT 0;
DECLARE EXIT HANDLER FOR SQLEXCEPTION 
BEGIN
SET flag = 0;
select flag;
END;
SET SESSION TRANSACTION ISOLATION LEVEL SERIALIZABLE;

IF(userid <= 0 OR pipieggs <= 0) THEN
SET flag = 0;
ELSE
START TRANSACTION;
SELECT freeze_pipiegg INTO freeze_pipiegg_balance FROM web_user_consume_attribute WHERE uid = userid;

IF(freeze_pipiegg_balance >= pipieggs) THEN
UPDATE web_user_consume_attribute SET freeze_pipiegg = freeze_pipiegg - pipieggs WHERE uid = userid;
SET flag = 1;
ELSE
SET flag = 0;
END IF;

SELECT @@error_count INTO error_count;
IF(error_count > 0) THEN
ROLLBACK;
SET flag = 0;
END IF;
COMMIT ;
END IF;
SELECT flag;
END;



DROP PROCEDURE IF EXISTS `proc_exchangeEggPointCharmPoint`;
CREATE PROCEDURE `proc_exchangeEggPointCharmPoint`(IN userid INT, IN points int)
    COMMENT '皮点和魅力点兑换皮蛋\r\n@author 郭少波\r\n@date 2013-07-04\r\n@version v1.0\r\n@param points必须为100的倍数'
BEGIN
DECLARE flag INT DEFAULT 0;
DECLARE error_count INT DEFAULT 0;
DECLARE eggpoint_balance INT DEFAULT 0;
DECLARE charmpoint_balance INT DEFAULT 0;
DECLARE pipiegg_balance DECIMAL(11,2) DEFAULT 0;

DECLARE EXIT HANDLER FOR SQLEXCEPTION 
BEGIN
SET flag = 0;
SELECT flag;
END;
SET SESSION TRANSACTION ISOLATION LEVEL SERIALIZABLE;

IF(userid <= 0 OR points <= 0) THEN
SET flag = 0;
ELSE
START TRANSACTION;
SELECT pipiegg,egg_points,charm_points INTO pipiegg_balance,eggpoint_balance,charmpoint_balance FROM web_user_consume_attribute WHERE uid = userid;
IF(points > (eggpoint_balance + charmpoint_balance)) THEN
SET flag = 0;
ELSEIF(points <= eggpoint_balance) THEN
UPDATE web_user_consume_attribute SET egg_points = (eggpoint_balance - points), pipiegg = (pipiegg + (points/100)) WHERE uid = userid;
SET flag = 1;
ELSE
UPDATE web_user_consume_attribute SET egg_points = 0,charm_points = (charm_points + eggpoint_balance - points),pipiegg = (pipiegg + (points/100)) WHERE uid = userid;
SET flag = 1;
END IF;

SELECT @@error_count INTO error_count;
IF(error_count > 0) THEN
ROLLBACK;
SET flag = 0;
END IF;
COMMIT;
END IF;
SELECT flag;
END;




DROP PROCEDURE IF EXISTS `proc_consumeEggPoint`;
CREATE PROCEDURE `proc_consumeEggPoint`(IN userid int, IN eggpoints int)
    COMMENT '兑换皮点\r\n@author 郭少波\r\n@date 2013-07-03\r\n@version v0.1'
BEGIN
DECLARE flag INT DEFAULT 0;
DECLARE error_count INT DEFAULT 0;
DECLARE eggpoint_balance INT DEFAULT 0;
DECLARE EXIT HANDLER FOR SQLEXCEPTION 
BEGIN
SET flag = 0;
select flag;
END;
SET SESSION TRANSACTION ISOLATION LEVEL SERIALIZABLE;

IF(userid <= 0 OR eggpoints <= 0) THEN
SET flag = 0;
ELSE
START TRANSACTION;
SELECT egg_points INTO eggpoint_balance FROM web_user_consume_attribute WHERE uid = userid;

IF(eggpoint_balance >= eggpoints) THEN
UPDATE web_user_consume_attribute SET egg_points = egg_points - eggpoints WHERE uid = userid;
SET flag = 1;
ELSE
SET flag = 0;
END IF;

SELECT @@error_count INTO error_count;
IF(error_count > 0) THEN
ROLLBACK;
SET flag = 0;
END IF;
COMMIT ;
END IF;
SELECT flag;
END;

DROP PROCEDURE IF EXISTS `proc_cancelSongReturnEggs`;
CREATE DEFINER=`root`@`localhost` PROCEDURE `proc_cancelSongReturnEggs`(IN `recordId` int(11),IN `userid` int(11),IN `pipieggs` decimal(11,2))
	COMMENT '取消点歌返还皮蛋\r\n@author 雷伟\r\n@date 2013-07-22\r\n@version v0.1'
BEGIN
DECLARE flag INT DEFAULT 0;
DECLARE error_count INT DEFAULT 0;
DECLARE is_exist INT DEFAULT 0;
DECLARE handle INT DEFAULT 0;
DECLARE EXIT HANDLER FOR SQLEXCEPTION 
BEGIN
	SET flag = 0;
	select flag;
	END;
	SET SESSION TRANSACTION ISOLATION LEVEL SERIALIZABLE;

	IF(recordId<=0 OR userid<=0 OR pipieggs<=0) THEN
		SET flag=0;
	ELSE
		START TRANSACTION;
		SELECT is_handle INTO handle FROM web_user_song WHERE record_id = recordId;
		IF(handle!=0) THEN
			SET flag=0;
		ELSE
			SELECT uid INTO is_exist FROM web_user_consume_attribute WHERE uid = userid;
			IF(is_exist != userid) THEN
				INSERT web_user_consume_attribute(uid, pipiegg) VALUES(userid, pipieggs);
			ELSE
				UPDATE web_user_consume_attribute SET pipiegg = pipiegg + pipieggs WHERE uid = userid;
			END IF;
			UPDATE web_user_song SET is_handle = 2,update_time=unix_timestamp() WHERE record_id = recordId;
			SET flag = 1;
		END IF;
		SELECT @@error_count INTO error_count;
		IF(error_count > 0) THEN
			ROLLBACK;
			SET flag = 0;
		END IF;
		COMMIT ;
	END IF;
SELECT flag;
END;

DROP PROCEDURE IF EXISTS `proc_actSong`;
CREATE DEFINER=`root`@`localhost` PROCEDURE `proc_actSong`(IN `recordId` int(11))
	COMMENT '确认点歌记录\r\n@author 雷伟\r\n@date 2013-07-22\r\n@version v0.1'
BEGIN
DECLARE flag INT DEFAULT 0;
DECLARE error_count INT DEFAULT 0;
DECLARE handle INT DEFAULT 0;
DECLARE EXIT HANDLER FOR SQLEXCEPTION 
BEGIN
	SET flag = 0;
	select flag;
	END;
	SET SESSION TRANSACTION ISOLATION LEVEL SERIALIZABLE;

	IF(recordId<=0) THEN
		SET flag=0;
	ELSE
		START TRANSACTION;
		SELECT is_handle INTO handle FROM web_user_song WHERE record_id = recordId;
		IF(handle!=0) THEN
			SET flag=0;
		ELSE
			UPDATE web_user_song SET is_handle = 1,update_time=unix_timestamp() WHERE record_id = recordId;
			SET flag = 1;
		END IF;
		SELECT @@error_count INTO error_count;
		IF(error_count > 0) THEN
			ROLLBACK;
			SET flag = 0;
		END IF;
		COMMIT ;
	END IF;
SELECT flag;
END;