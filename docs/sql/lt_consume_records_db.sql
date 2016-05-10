USE `lt_consume_records_db`;
--
-- 数据库: `lt_consume_records_db`
--

-- --------------------------------------------------------

--
-- 表的结构 `web_dotey_charmpoints_records`
--

DROP TABLE IF EXISTS `web_dotey_charmpoints_records`;
CREATE TABLE IF NOT EXISTS `web_dotey_charmpoints_records` (
  `record_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '记录ID',
  `uid` int(11) NOT NULL COMMENT '主播ID',
  `sender_uid` int(11) NOT NULL COMMENT '事件发起者ID',
  `target_id` int(11) NOT NULL COMMENT '目标ID',
  `record_sid` int(11) NOT NULL COMMENT '流水线记录ＩＤ',
  `charm_points` bigint(20) NOT NULL COMMENT '魅力点变化',
  `num` smallint(5) NOT NULL COMMENT '对像个数',
  `source` varchar(25) NOT NULL DEFAULT '' COMMENT '来源 gifts表示礼物，songs表示歌曲,props表示道具,sends表示后台赠送',
  `sub_source` varchar(25) NOT NULL COMMENT '子来源，buyGifts表示正常购买,bagGifts表示背包礼物，道具为道具的英文名称',
  `client` tinyint(2) NOT NULL COMMENT '数据变化来源，0表示档期，1表示活动 2表示后台赠送',
  `info` varchar(255) NOT NULL COMMENT '概述',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`record_id`),
  KEY `idx_uid_ssource_ctime` (`uid`,`source`,`sub_source`,`create_time`) USING BTREE,
  KEY `idx_uid_source_ctime` (`uid`,`source`,`create_time`) USING BTREE,
  KEY `idx_client_uid_ctime` (`uid`,`client`,`create_time`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='主播魅力点变化表'  ;

-- --------------------------------------------------------

--
-- 表的结构 `web_dotey_charm_records`
--

DROP TABLE IF EXISTS `web_dotey_charm_records`;
CREATE TABLE IF NOT EXISTS `web_dotey_charm_records` (
  `record_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '记录ID',
  `uid` int(11) NOT NULL COMMENT '主播ID',
  `sender_uid` int(11) NOT NULL COMMENT '事件发起者ID',
  `target_id` int(11) NOT NULL COMMENT '目标ID',
  `record_sid` int(11) NOT NULL COMMENT '流水线记录ＩＤ',
  `charm` int(11) NOT NULL COMMENT '魅力值',
  `num` smallint(5) NOT NULL COMMENT '对像个数',
  `source` varchar(25) NOT NULL DEFAULT '' COMMENT '来源 gifts表示礼物，songs表示歌曲,props表示道具,sends表示后台赠送',
  `sub_source` varchar(25) NOT NULL COMMENT '子来源，buyGifts表示正常购买,bagGifts表示背包礼物，道具为道具的英文名称',
  `client` tinyint(2) NOT NULL COMMENT '数据变化来源，0表示档期，1表示活动 2表示后台赠送',
  `info` varchar(255) NOT NULL COMMENT '概述',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`record_id`),
  KEY `idx_uid_ssource_ctime` (`uid`,`source`,`sub_source`,`create_time`) USING BTREE,
  KEY `idx_uid_source_ctime` (`uid`,`source`,`create_time`) USING BTREE,
  KEY `idx_client_uid_ctime` (`uid`,`client`,`create_time`) USING BTREE,
  KEY `idx_uid_ctime` (`uid`,`create_time`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='主播魅力值变化表';

-- --------------------------------------------------------

--
-- 表的结构 `web_stars_record`
--

DROP TABLE IF EXISTS `web_stars_record`;
CREATE TABLE IF NOT EXISTS `web_stars_record` (
  `record_id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户uid',
  `period` int(8) NOT NULL COMMENT '周期',
  `stars_id` tinyint(6) NOT NULL COMMENT '星级',
  `start_time` int(11) NOT NULL COMMENT '周期开始时间',
  `end_time` int(11) NOT NULL COMMENT '周期结束时间',
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`record_id`),
  KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `web_user_bag_records`
--

DROP TABLE IF EXISTS `web_user_bag_records`;
CREATE TABLE IF NOT EXISTS `web_user_bag_records` (
  `record_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '记录ＩＤ',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '购买用户ＩＤ',
  `gift_id` int(11) NOT NULL DEFAULT '0' COMMENT '购买礼物ＩＤ',
  `num` int(11) NOT NULL DEFAULT '0' COMMENT '购买数量',
  `info` text NOT NULL COMMENT '额外信息',
  `source` varchar(25) NOT NULL DEFAULT '0' COMMENT '购买来源　０表示商城购买　１表是直播间购买　２表示游戏　 ３表示后台赠送',
  `sub_source` varchar(25) NOT NULL COMMENT '购买子来源',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '购买时间',
  PRIMARY KEY (`record_id`),
  KEY `idx_uid_presentid_createtime` (`uid`,`gift_id`,`create_time`) USING BTREE,
  KEY `idx_uid_source_createtime` (`uid`,`source`,`create_time`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='用户购买礼物记录'  ;

-- --------------------------------------------------------

--
-- 表的结构 `web_user_dedication_records`
--

DROP TABLE IF EXISTS `web_user_dedication_records`;
CREATE TABLE IF NOT EXISTS `web_user_dedication_records` (
  `record_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '记录ID',
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `from_target_id` int(11) NOT NULL COMMENT '目标ID 如礼物ＩＤ',
  `to_target_id` int(11) NOT NULL COMMENT '目的ＩＤ，如client为档期时的　的档期ＩＤ',
  `record_sid` int(11) NOT NULL COMMENT '流水线记录ＩＤ',
  `dedication` bigint(15) NOT NULL COMMENT '贡献值',
  `num` smallint(5) NOT NULL COMMENT '个数',
  `source` varchar(25) NOT NULL COMMENT '来源 gifts表示礼物，songs表示歌曲,props表示道具,sends表示后台赠送',
  `sub_source` varchar(25) NOT NULL COMMENT '子来源，buyGifts表示正常购买,bagGifts表示背包礼物，道具为道具分类的英文名称',
  `client` tinyint(3) NOT NULL COMMENT '数据变化来源，0表示档期，1表示活动 2表示后台赠送',
  `info` varchar(255) NOT NULL COMMENT '概述',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`record_id`),
  KEY `idx_uid_ssource_ctime` (`uid`,`source`,`sub_source`,`create_time`) USING BTREE,
  KEY `idx_uid_source_ctime` (`uid`,`from_target_id`,`source`,`create_time`) USING BTREE,
  KEY `idx_client_uid_ctime` (`client`,`uid`,`create_time`) USING BTREE,
  KEY `idx_client_totarget_ctime` (`client`,`to_target_id`,`create_time`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='用户贡献值记录表' ;

-- --------------------------------------------------------

--
-- 表的结构 `web_user_eggpoints_records`
--

DROP TABLE IF EXISTS `web_user_eggpoints_records`;
CREATE TABLE IF NOT EXISTS `web_user_eggpoints_records` (
  `record_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '记录ID',
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `target_id` int(11) NOT NULL COMMENT '目标ID',
  `record_sid` int(11) NOT NULL COMMENT '流水线记录',
  `sender_uid` int(11) NOT NULL COMMENT '事件发起者ID',
  `egg_points` bigint(15) NOT NULL COMMENT '皮点',
  `num` smallint(5) NOT NULL COMMENT '个数',
  `source` varchar(25) NOT NULL COMMENT '来源 gifts表示礼物，songs表示歌曲,props表示道具,sends表示后台赠送',
  `sub_source` varchar(25) NOT NULL COMMENT '子来源，buyGifts表示正常购买,bagGifts表示背包礼物，道具为道具的英文名称',
  `client` tinyint(3) NOT NULL COMMENT '数据变化来源，0表示档期，1表示活动 2表示后台赠送',
  `info` varchar(255) NOT NULL COMMENT '概述',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`record_id`),
  KEY `idx_uid_ssource_ctime` (`uid`,`source`,`sub_source`,`create_time`) USING BTREE,
  KEY `idx_uid_source_ctime` (`uid`,`target_id`,`source`,`create_time`) USING BTREE,
  KEY `idx_client_uid_ctime` (`client`,`uid`,`create_time`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='用户皮点变化记录表' ;

-- --------------------------------------------------------

--
-- 表的结构 `web_user_exchange_records`
--

DROP TABLE IF EXISTS `web_user_exchange_records`;
CREATE TABLE IF NOT EXISTS `web_user_exchange_records` (
  `record_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '记录ＩＤ',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户ＩＤ',
  `op_uid` int(11) NOT NULL,
  `ex_type` tinyint(3) NOT NULL DEFAULT '0' COMMENT '兑换类型　０表示皮点兑换皮蛋　１表示魅力点对换皮蛋　２表示魅力点对人民币 3表示平台奖励人民币',
  `handle_type` tinyint(3) NOT NULL DEFAULT '0' COMMENT '0表示未处理 1表示已处理 2表示回撤',
  `info` varchar(255) NOT NULL,
  `org_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '兑换前的币种',
  `dst_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '兑换后的币种',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '兑换时间',
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`record_id`),
  KEY `idx_uid_extype_createtime` (`uid`,`ex_type`,`create_time`) USING BTREE,
  KEY `idx_opuid_exttype_createtime` (`op_uid`,`ex_type`,`create_time`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- 表的结构 `web_user_giftsend_records`
--

DROP TABLE IF EXISTS `web_user_giftsend_records`;
CREATE TABLE IF NOT EXISTS `web_user_giftsend_records` (
  `record_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '标识ID',
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `to_uid` varchar(255) NOT NULL COMMENT '接收者用户ＩＤ 可以是一个　也可以是多少',
  `gift_id` int(11) NOT NULL COMMENT '礼物ID',
  `target_id` int(11) NOT NULL COMMENT '目标ＩＤ，如操作来源是档期时，就存在档期ＩＤ',
  `record_sid` int(11) NOT NULL COMMENT '例如背包ID',
  `num` smallint(5) NOT NULL COMMENT '送礼个数',
  `pipiegg` decimal(11,2) NOT NULL COMMENT '消耗的皮蛋数',
  `charm` int(11) NOT NULL COMMENT '获取的皮蛋',
  `egg_points` int(11) NOT NULL COMMENT '获取的皮点',
  `charm_points` int(11) NOT NULL COMMENT '获取的魅力点',
  `dedication` int(11) NOT NULL COMMENT '获取的贡献值',
  `gift_type` tinyint(4) NOT NULL COMMENT '送礼类型 0表示正常购买 1表示背包送礼',
  `recevier_type` tinyint(4) NOT NULL COMMENT '接收对象 0表示主播 1表示普通用户',
  `source` tinyint(4) NOT NULL COMMENT '操作来源 0表示档期,1表示游戏',
  `info` text NOT NULL COMMENT '概要',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`record_id`),
  KEY `idx_gifts_ctime` (`gift_id`,`create_time`) USING BTREE,
  KEY `idx_source_targetid_ctime` (`source`,`target_id`,`create_time`) USING BTREE,
  KEY `idx_uid_receivetype_ctime` (`uid`,`recevier_type`,`create_time`) USING BTREE,
  KEY `idx_uid_ctime` (`uid`,`create_time`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='礼物记录表'  ;

-- --------------------------------------------------------

--
-- 表的结构 `web_user_giftsend_relation_records`
--

DROP TABLE IF EXISTS `web_user_giftsend_relation_records`;
CREATE TABLE IF NOT EXISTS `web_user_giftsend_relation_records` (
  `relation_id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '收礼ID',
  `record_id` int(11) NOT NULL COMMENT '用户ID',
  `is_onwer` tinyint(4) NOT NULL COMMENT '是否是自己 1表示是送出的 0表示是收到的',
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`relation_id`),
  KEY `idx_owner_ctime` (`uid`,`is_onwer`,`create_time`) USING BTREE,
  KEY `idx_record` (`record_id`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='用户送礼物关系表'  ;

-- --------------------------------------------------------

--
-- 表的结构 `web_user_pipiegg_records`
--

DROP TABLE IF EXISTS `web_user_pipiegg_records`;
CREATE TABLE IF NOT EXISTS `web_user_pipiegg_records` (
  `record_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '皮蛋消费标识',
  `uid` int(11) NOT NULL COMMENT '用户',
  `from_target_id` int(11) NOT NULL COMMENT '来源ＩＤ　如道具ＩＤ',
  `to_target_id` int(11) NOT NULL COMMENT '目标ＩＤ，如购买守护时守护的主播',
  `record_sid` int(11) NOT NULL COMMENT '流水线记录ＩＤ',
  `pipiegg` decimal(11,2) NOT NULL COMMENT '消费皮蛋数',
  `source` varchar(25) NOT NULL COMMENT '来源 gifts表示礼物props表示道具 songs表示点歌 games表示游戏  activity表示活动 task表示任务 recharge表示充值  other代表其它 ',
  `sub_source` varchar(25) NOT NULL COMMENT '子来源项',
  `client` tinyint(4) NOT NULL COMMENT '操作位置 0表示直播间　１表示商城　２表示游戏　３表示活动',
  `num` smallint(6) NOT NULL COMMENT '购买数量',
  `ip_address` varchar(60) NOT NULL COMMENT 'ＩＰ',
  `extra` text NOT NULL COMMENT '扩展信息',
  `consume_time` int(11) NOT NULL COMMENT '消费时间',
  `cbalance` decimal(11,2) NOT NULL COMMENT '余额',
  PRIMARY KEY (`record_id`),
  KEY `idx_user_source_time` (`uid`,`source`,`sub_source`,`consume_time`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='皮蛋消费记录'  ;

-- --------------------------------------------------------

--
-- 表的结构 `web_user_props_records`
--

DROP TABLE IF EXISTS `web_user_props_records`;
CREATE TABLE IF NOT EXISTS `web_user_props_records` (
  `record_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '道具消费ID',
  `uid` int(11) NOT NULL COMMENT '购买用户',
  `prop_id` int(11) NOT NULL COMMENT '道具ID',
  `cat_id` smallint(5) NOT NULL COMMENT '道具分类ID',
  `pipiegg` decimal(11,2) NOT NULL COMMENT '皮蛋变化',
  `dedication` int(11) NOT NULL COMMENT '贡献值变化',
  `egg_points` int(11) NOT NULL COMMENT '皮点',
  `charm_points` int(11) NOT NULL COMMENT '魅力点',
  `charm` int(11) NOT NULL COMMENT '魅力值变经',
  `source` tinyint(1) NOT NULL COMMENT '道具来源 0表示正常购买 1表示后台赠送 2表示活动领取 3道具抵扣',
  `info` varchar(255) NOT NULL COMMENT '购买信息',
  `amount` int(11) NOT NULL COMMENT '购买数量',
  `ctime` int(11) NOT NULL COMMENT '购买时间',
  `vtime` int(11) NOT NULL COMMENT '失效时间',
  PRIMARY KEY (`record_id`),
  KEY `idx_uid_actid_ctime` (`uid`,`cat_id`,`ctime`) USING BTREE,
  KEY `idx_uid_actid_vtime` (`uid`,`cat_id`,`vtime`) USING BTREE,
  KEY `idx_uid_propid_vtime` (`uid`,`prop_id`,`vtime`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='用户购买道具信息表'  ;


-- --------------------------------------------------------

--
-- 表的结构 `web_user_award_records`
--
DROP TABLE IF EXISTS `web_user_award_records`;
CREATE TABLE `web_user_award_records` (
  `record_id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '赠送uid',
  `record_sid` int(11) NOT NULL COMMENT '礼物记录Id',
  `target_id` int(11) NOT NULL COMMENT '礼物或道具Id',
  `to_target_id` int(11) NOT NULL COMMENT '奖励礼物或道具Id',
  `type` tinyint(1) NOT NULL COMMENT '奖励类型：1-礼物；2-道具；3-皮蛋',
  `num` int(11) NOT NULL COMMENT '奖励数量或奖励倍数',
  `pipiegg` float(11,2) NOT NULL COMMENT '奖励的皮蛋数',
  `source` varchar(30) NOT NULL COMMENT '来源 gifts表示主播送物,userGifts表示用户间送礼，songs表示歌曲,props表示道具,sends表示后台赠送',
  `sub_source` varchar(30) NOT NULL COMMENT '子来源，buyGifts表示正常购买普通礼物,bagGifts表示背包普通礼物,buyLuckGifts 表示正常购买幸运礼物，bagLuckGifts 表示背包幸运礼物,道具为道具分类的英文名称',
  `info` varchar(255) NOT NULL COMMENT '描述',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`record_id`),
  KEY `index_uid_target_ctime` (`uid`,`target_id`,`source`,`sub_source`,`create_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户奖励记录表';

