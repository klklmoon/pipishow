USE `lt_activity_db`;
--
-- 表的结构 `web_long_firstcharge_gifts`
--
CREATE TABLE `web_long_firstcharge_gifts` (
  `rid` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID',
  `uid` int(11) NOT NULL,
  `type` smallint(1) unsigned NOT NULL DEFAULT '1' COMMENT '礼包类型 1：礼包1 2：礼包二',
  `ctime` int(11) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`rid`),
  UNIQUE KEY `idx_uid_type` (`uid`,`type`),
  KEY `idx_type` (`type`)
) ENGINE=Innodb DEFAULT CHARSET=utf8 COMMENT='首次充值礼包赠送';

--
-- Source for table "web_long_giftstar_rule"
--

CREATE TABLE `web_long_giftstar_rule` (
  `rule_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '规则记录id',
  `week_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '周编号',
  `monday_date` varchar(10) NOT NULL DEFAULT '' COMMENT '周一日期',
  `gift_week_order` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '周礼物序号',
  `gift_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '本周礼物id',
  `contention_rule` varchar(255) DEFAULT '' COMMENT '礼物之星争夺规则（主播等级限制）',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`rule_id`),
  UNIQUE KEY `idx_week_gift` (`week_id`,`gift_id`) COMMENT '每周一种礼物只能有一条规早',
  UNIQUE KEY `idx_week_gift_order` (`week_id`,`gift_week_order`) COMMENT '每周一个礼物序号只能出现一次'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='礼物之星礼物规则设定';

--
-- Source for table "web_long_giftstar_set"
--

CREATE TABLE `web_long_giftstar_set` (
  `set_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '设置信息记录id',
  `week_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '周编号',
  `monday_date` varchar(10) NOT NULL DEFAULT '' COMMENT '周一日期',
  `illustration` text COMMENT '礼物之星特别说明',
  `set_type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '设定类型（1、常规；2通用）',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`set_id`),
  UNIQUE KEY `idx_week_id` (`week_id`),
  UNIQUE KEY `idx_week` (`week_id`) COMMENT '每周只有一条记录'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='礼物之星相关信息设定';

--
-- Source for table "web_long_giftstar_dotey"
--

CREATE TABLE `web_long_giftstar_dotey` (
  `record_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录id',
  `week_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '周编号',
  `dotey_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '有参与资格主播',
  `grade` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '主播参与时的等级',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`record_id`),
  UNIQUE KEY `idx_week_odtey` (`week_id`,`dotey_id`) COMMENT '每周一个主播最多只有一条记录'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='礼物之星参与争夺主播';

--
-- Source for table "web_long_giftstar_rank"
--

CREATE TABLE `web_long_giftstar_rank` (
  `rank_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '榜单id',
  `week_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '周编号',
  `dotey_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '主播id',
  `rank` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '名次',
  `gift_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '礼物id',
  `gift_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '礼物数',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '生成时间',
  PRIMARY KEY (`rank_id`),
  UNIQUE KEY `idx_week_gift_rank` (`week_id`,`gift_id`,`rank`) COMMENT '每周一种礼一个名次最多只有一条记录'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='礼物之星排行榜';

--
-- Source for table "web_long_giftstar_img"
--

CREATE TABLE `web_long_giftstar_img` (
  `img_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录id',
  `gift_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '礼物id',
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT '图片文件名',
  `order_number` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '图片序号',
  `summary` varchar(255) DEFAULT NULL COMMENT '描述',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`img_id`),
  UNIQUE KEY `idx_gift_order` (`gift_id`,`order_number`) COMMENT '一种礼物一种效果图片序号只有一条记录'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='礼物之星礼物物效图片';
