USE `lt_partner_db` ;
--
-- 数据库: `lt_partner_db`
--

-- --------------------------------------------------------

--
-- 表的结构 `web_channel_id_pre_max`
--

DROP TABLE IF EXISTS `web_channel_id_pre_max`;
CREATE TABLE IF NOT EXISTS `web_channel_id_pre_max` (
  `channel_id_prefix` char(8) NOT NULL COMMENT '两位前缀',
  `channel_id_max` char(8) DEFAULT NULL COMMENT '两位前缀对应的已使用最大id',
  PRIMARY KEY (`channel_id_prefix`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='渠道前缀与最大已用id对应表';

-- --------------------------------------------------------

--
-- 表的结构 `web_login_stat_online`
--

DROP TABLE IF EXISTS `web_login_stat_online`;
CREATE TABLE IF NOT EXISTS `web_login_stat_online` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `dotey_id` int(11) NOT NULL COMMENT '主播ID',
  `archives_id` int(11) NOT NULL COMMENT '档期ID',
  `time_online` int(11) DEFAULT '0' COMMENT '时长统计值',
  `create_time` int(11) NOT NULL COMMENT '统计时间',
  PRIMARY KEY (`id`),
  KEY `IDX_uid_date` (`uid`,`create_time`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='用户在直播间的在线时长统计' AUTO_INCREMENT=50 ;

-- --------------------------------------------------------

--
-- 表的结构 `web_login_stat_online_day`
--

DROP TABLE IF EXISTS `web_login_stat_online_day`;
CREATE TABLE IF NOT EXISTS `web_login_stat_online_day` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `channel_name` varchar(32) NOT NULL COMMENT '推广渠道名',
  `partner_id` int(11) NOT NULL COMMENT '合作商ID',
  `time_online` int(11) DEFAULT '0' COMMENT '时长统计值',
  `create_time` int(8) NOT NULL COMMENT '统计时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `IDX_uid_date` (`uid`,`create_time`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户在直播间的在线时长按天统计' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `web_login_stat_partner`
--

DROP TABLE IF EXISTS `web_login_stat_partner`;
CREATE TABLE IF NOT EXISTS `web_login_stat_partner` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `channel_name` varchar(32) NOT NULL COMMENT '推广渠道名',
  `partner_id` int(11) NOT NULL COMMENT '合作商ID',
  `times` int(11) DEFAULT '0' COMMENT '登录次数',
  `create_time` int(8) NOT NULL DEFAULT '0' COMMENT '登入时间YYYYMMDD',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UQ_uid_date` (`uid`,`create_time`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='渠道用户登录统计' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `web_partner_admin`
--

DROP TABLE IF EXISTS `web_partner_admin`;
CREATE TABLE IF NOT EXISTS `web_partner_admin` (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '管理员id',
  `admin_user` char(64) NOT NULL COMMENT '登录帐号',
  `admin_passwd` char(32) NOT NULL COMMENT '登录密码',
  `admin_name` varchar(64) NOT NULL COMMENT '管理员备注名称',
  `purview_type` smallint(5) NOT NULL DEFAULT '1' COMMENT '1表示普通管理员 2为超级管理员',
  `fail_num` int(11) NOT NULL DEFAULT '0' COMMENT '登录失败次数',
  `is_ban` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否封禁,0未封禁,1已封禁',
  `create_time` int(11) NOT NULL COMMENT '记录时间',
  PRIMARY KEY (`admin_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='管理员表' AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- 表的结构 `web_partner_admin_log`
--

DROP TABLE IF EXISTS `web_partner_admin_log`;
CREATE TABLE IF NOT EXISTS `web_partner_admin_log` (
  `admin_id` int(11) NOT NULL COMMENT '管理员id',
  `action_type` tinyint(4) DEFAULT NULL COMMENT '行为类别',
  `action_detail` varchar(512) DEFAULT NULL COMMENT '行为明细',
  `create_time` int(11) NOT NULL COMMENT '记录时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='管理员操作记录表';

-- --------------------------------------------------------

--
-- 表的结构 `web_partner_channel`
--

DROP TABLE IF EXISTS `web_partner_channel`;
CREATE TABLE IF NOT EXISTS `web_partner_channel` (
  `channel_id` char(32) NOT NULL COMMENT '渠道id',
  `channel_id_comment` varchar(256) DEFAULT NULL COMMENT '渠道名备注',
  `partner_id` int(11) NOT NULL COMMENT '合作商id',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`channel_id`),
  KEY `par_ind` (`partner_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='渠道信息表';

-- --------------------------------------------------------

--
-- 表的结构 `web_partner_config`
--

DROP TABLE IF EXISTS `web_partner_config`;
CREATE TABLE IF NOT EXISTS `web_partner_config` (
  `name` char(64) NOT NULL COMMENT '配置项名',
  `value` text COMMENT '配置项值',
  `remark` char(128) DEFAULT NULL COMMENT '说明',
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='推广联盟配置表';

-- --------------------------------------------------------

--
-- 表的结构 `web_partner_info`
--

DROP TABLE IF EXISTS `web_partner_info`;
CREATE TABLE IF NOT EXISTS `web_partner_info` (
  `partner_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '合作商id',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `partner_user` char(64) DEFAULT NULL COMMENT '登录帐号',
  `partner_passwd` char(32) DEFAULT NULL COMMENT '登录密码',
  `partner_name` varchar(64) DEFAULT NULL COMMENT '合作商名称',
  `contact_name` varchar(64) DEFAULT NULL COMMENT '联系人姓名',
  `contact_phone` varchar(32) DEFAULT NULL COMMENT '联系手机',
  `contact_qq` varchar(32) DEFAULT NULL COMMENT '联系qq',
  `popularize_domain` text COMMENT '合作域名，多个',
  `popularize_client` text COMMENT '推广客户端，多个',
  `popularize_other` text COMMENT '其他推广描述',
  `estimate_uv` float DEFAULT '0' COMMENT '日独立访客量估计',
  `is_pass` tinyint(4) DEFAULT '0' COMMENT '是否通过申请.0未处理，1第一次通过，2未通过，3已通过',
  `channel_prefix` char(8) DEFAULT NULL COMMENT '特征码',
  `refuse_reason` varchar(512) DEFAULT NULL COMMENT '拒绝申请原因',
  `fail_num` int(11) DEFAULT '0' COMMENT '登录失败次数',
  `is_ban` tinyint(4) DEFAULT '0' COMMENT '是否封禁,0未封禁，1已封禁',
  `ip` char(32) DEFAULT NULL COMMENT 'ip',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`partner_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='合作商信息表'  ;

-- --------------------------------------------------------

--
-- 表的结构 `web_partner_shield_channel`
--

DROP TABLE IF EXISTS `web_partner_shield_channel`;
CREATE TABLE IF NOT EXISTS `web_partner_shield_channel` (
  `channel_id` char(32) NOT NULL COMMENT '屏蔽的渠道id',
  PRIMARY KEY (`channel_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='子查询帐号，渠道id屏蔽表';

-- --------------------------------------------------------

--
-- 表的结构 `web_partner_subuser_channel`
--

DROP TABLE IF EXISTS `web_partner_subuser_channel`;
CREATE TABLE IF NOT EXISTS `web_partner_subuser_channel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `channel_id` char(32) NOT NULL COMMENT '渠道id',
  `partner_id` int(11) NOT NULL COMMENT '子帐号id',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `par_ind` (`partner_id`) USING BTREE,
  KEY `cha_for` (`channel_id`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='子查询帐号，渠道关联表'  ;

-- --------------------------------------------------------

--
-- 表的结构 `web_regpromote_admin`
--

DROP TABLE IF EXISTS `web_regpromote_admin`;
CREATE TABLE IF NOT EXISTS `web_regpromote_admin` (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '管理员id',
  `admin_user` char(64) DEFAULT NULL COMMENT '登录帐号',
  `admin_passwd` char(32) DEFAULT NULL COMMENT '登录密码',
  `admin_name` varchar(64) DEFAULT NULL COMMENT '管理员备注名称',
  `fail_num` int(11) DEFAULT '0' COMMENT '登录失败次数',
  `is_ban` tinyint(4) DEFAULT '0' COMMENT '是否封禁,0未封禁,1已封禁',
  `create_time` int(11) NOT NULL COMMENT '记录时间',
  PRIMARY KEY (`admin_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='管理员表'  ;

-- --------------------------------------------------------

--
-- 表的结构 `web_regpromote_admin_log`
--

DROP TABLE IF EXISTS `web_regpromote_admin_log`;
CREATE TABLE IF NOT EXISTS `web_regpromote_admin_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `admin_id` int(11) NOT NULL COMMENT '管理员id',
  `action_type` tinyint(4) DEFAULT NULL COMMENT '行为类别',
  `action_detail` varchar(512) DEFAULT NULL COMMENT '行为明细',
  `create_time` int(11) NOT NULL COMMENT '记录时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='管理员操作记录表'  ;

-- --------------------------------------------------------

--
-- 表的结构 `web_regpromote_stat`
--

DROP TABLE IF EXISTS `web_regpromote_stat`;
CREATE TABLE IF NOT EXISTS `web_regpromote_stat` (
  `stat_date` int(11) NOT NULL COMMENT '时间，8位数字',
  `channel_id` char(32) NOT NULL COMMENT '渠道id',
  `user_id` int(11) NOT NULL COMMENT '合作商id',
  `new_reg` int(11) DEFAULT '0' COMMENT '新注册人数',
  `accu_new_reg` int(11) DEFAULT '0' COMMENT '累计新注册人数',
  `new_recharge` int(11) DEFAULT '0' COMMENT '新增付费人数',
  `accu_new_recharge` int(11) DEFAULT '0' COMMENT '累计付费人数',
  `recharge_money` float DEFAULT '0' COMMENT '充值金额',
  `accu_recharge_money` float DEFAULT '0' COMMENT '累计充值金额',
  `new_reg_after` int(11) DEFAULT '0' COMMENT '扣量后新注册人数',
  `accu_new_reg_after` int(11) DEFAULT '0' COMMENT '扣量后累计新注册人数',
  `new_recharge_after` int(11) DEFAULT '0' COMMENT '扣量后新增付费人数',
  `accu_new_recharge_after` int(11) DEFAULT '0' COMMENT '扣量后累计付费人数',
  `recharge_money_after` float DEFAULT '0' COMMENT '扣量后充值金额',
  `accu_recharge_money_after` float DEFAULT '0' COMMENT '扣量后累计充值金额',
  PRIMARY KEY (`stat_date`,`channel_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='注册推广统计表';

-- --------------------------------------------------------

--
-- 表的结构 `web_regpromote_user`
--

DROP TABLE IF EXISTS `web_regpromote_user`;
CREATE TABLE IF NOT EXISTS `web_regpromote_user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '合作商id',
  `username` char(64) DEFAULT NULL COMMENT '登录帐号',
  `passwd` char(32) DEFAULT NULL COMMENT '登录密码',
  `nickname` varchar(64) DEFAULT NULL COMMENT '昵称',
  `percent` float DEFAULT NULL COMMENT '扣量比例',
  `contact_name` varchar(64) DEFAULT NULL COMMENT '联系人姓名',
  `contact_phone` varchar(32) DEFAULT NULL COMMENT '联系手机',
  `contact_qq` varchar(32) DEFAULT NULL COMMENT '联系qq',
  `fail_num` int(11) DEFAULT '0' COMMENT '登录失败次数',
  `is_ban` tinyint(4) DEFAULT '0' COMMENT '是否封禁,0未封禁，1已封禁',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='渠道商信息表'  ;

-- --------------------------------------------------------

--
-- 表的结构 `web_regpromote_user_channel`
--

DROP TABLE IF EXISTS `web_regpromote_user_channel`;
CREATE TABLE IF NOT EXISTS `web_regpromote_user_channel` (
  `channel_id` char(32) NOT NULL COMMENT '渠道id',
  `channel_id_comment` varchar(256) DEFAULT NULL COMMENT '渠道名备注',
  `user_id` int(11) NOT NULL COMMENT '合作商id',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`channel_id`),
  KEY `usr_ind` (`user_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='渠道商与渠道ID关联表';

-- --------------------------------------------------------

--
-- 表的结构 `web_reg_channel`
--

DROP TABLE IF EXISTS `web_reg_channel`;
CREATE TABLE IF NOT EXISTS `web_reg_channel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` varchar(12) DEFAULT NULL COMMENT '日期',
  `channel_name` varchar(100) NOT NULL COMMENT '推广渠道',
  `channel_id` varchar(200) NOT NULL COMMENT '渠道ID',
  `person_in_charge` varchar(50) DEFAULT NULL COMMENT '负责人',
  `day_cost` mediumint(6) DEFAULT NULL COMMENT '日推广成本',
  `total_cost` mediumint(6) DEFAULT NULL COMMENT '累计推广成本',
  PRIMARY KEY (`id`),
  UNIQUE KEY `channel_index` (`channel_id`,`date`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='推广渠道表'  ;

-- --------------------------------------------------------

--
-- 表的结构 `web_reg_channel_id_name`
--

DROP TABLE IF EXISTS `web_reg_channel_id_name`;
CREATE TABLE IF NOT EXISTS `web_reg_channel_id_name` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `channel_id` varchar(200) NOT NULL COMMENT '渠道ID',
  `channel_name` varchar(100) NOT NULL COMMENT '推广渠道',
  `person_in_charge` varchar(50) DEFAULT NULL COMMENT '负责人',
  PRIMARY KEY (`id`),
  UNIQUE KEY `channel_index` (`channel_id`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='推广渠道ID-NAME表' ;

-- --------------------------------------------------------

--
-- 表的结构 `web_reg_log`
--

DROP TABLE IF EXISTS `web_reg_log`;
CREATE TABLE IF NOT EXISTS `web_reg_log` (
  `uid` int(11) unsigned NOT NULL,
  `referer` varchar(255) DEFAULT '' COMMENT '来源页',
  `sign` char(20) DEFAULT '' COMMENT '推广确认标志',
  `curl` varchar(255) NOT NULL DEFAULT '' COMMENT '首次访问页面',
  `access_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '首次访问时间',
  `access_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `web_stat_popularize`
--

DROP TABLE IF EXISTS `web_stat_popularize`;
CREATE TABLE IF NOT EXISTS `web_stat_popularize` (
  `stat_date` int(11) NOT NULL COMMENT '时间，8位数字',
  `channel_id` char(32) NOT NULL COMMENT '渠道id',
  `domain` char(64) NOT NULL DEFAULT 'null' COMMENT '来路域名',
  `partner_id` int(11) NOT NULL COMMENT '合作商id',
  `new_reg` int(11) DEFAULT '0' COMMENT '新注册人数',
  `new_recharge` int(11) DEFAULT '0' COMMENT '新增付费人数',
  `recharge_money` float DEFAULT '0' COMMENT '充值金额',
  `percentage_money` float DEFAULT '0' COMMENT '充分成金额',
  PRIMARY KEY (`stat_date`,`channel_id`,`domain`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='推广渠道统计表';



--
-- 表的结构 `uc_show_popularization_stat`
--

DROP TABLE IF EXISTS `web_show_popularization_stat`;
CREATE TABLE IF NOT EXISTS `web_show_popularization_stat` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sdate` varchar(10) DEFAULT NULL COMMENT '统计数据日期',
  `channel_id` varchar(20) DEFAULT NULL COMMENT '渠道id',
  `channel_name` varchar(100) DEFAULT NULL,
  `principal` varchar(20) DEFAULT NULL COMMENT '负责人',
  `total_cost` decimal(12,4) unsigned DEFAULT NULL COMMENT '累计推广成本',
  `day_cost` decimal(12,4) unsigned DEFAULT NULL COMMENT '日推广成本',
  `total_register_num` int(11) unsigned DEFAULT NULL COMMENT '累计注册数',
  `total_amount` decimal(12,4) unsigned DEFAULT NULL COMMENT '累计充值金额',
  `total_acpnum` int(11) unsigned DEFAULT NULL COMMENT '累计付费账号数',
  `total_arpu` decimal(12,4) unsigned DEFAULT NULL COMMENT 'ARPU（累计充值/累计注册数）',
  `day_register_num` int(11) unsigned DEFAULT NULL COMMENT '日注册数',
  `day_amount` decimal(12,4) unsigned DEFAULT NULL COMMENT '日新注册充值金额',
  `day_cpnum` int(11) unsigned DEFAULT NULL COMMENT '当日付费用户数',
  `all_day_amount` decimal(12,4) unsigned DEFAULT NULL COMMENT '渠道全部注册日充值金额',
  `day_contribution_eq0` decimal(12,4) unsigned DEFAULT NULL COMMENT '日贡献值=0比例',
  `day_contribution_gt0` decimal(12,4) unsigned DEFAULT NULL COMMENT '日贡献值>0比例',
  `day_contribution_gt50` decimal(12,4) unsigned DEFAULT NULL COMMENT '日贡献值>50比例',
  `all_day_contribution_eq0` decimal(12,4) unsigned DEFAULT NULL COMMENT '渠道全部注册日贡献值=0比例',
  `all_day_contribution_gt0` decimal(12,4) unsigned DEFAULT NULL COMMENT '渠道全部注册日贡献值>0比例',
  `all_day_contribution_gt50` decimal(12,4) unsigned DEFAULT NULL COMMENT '渠道全部注册日贡献值>50比例',
  `total_contribution_eq0` decimal(12,4) unsigned DEFAULT NULL COMMENT '累计贡献值=0比例',
  `total_contribution_gt0` decimal(12,4) unsigned DEFAULT NULL COMMENT '累计贡献值>0比例',
  `total_contribution_gt50` decimal(12,4) unsigned DEFAULT NULL COMMENT '累计贡献值>50比例',
  `total_register_cost` decimal(12,4) unsigned DEFAULT NULL COMMENT '累计注册成本（累计推广成本/累计注册数）',
  `day_register_cost` decimal(12,4) unsigned DEFAULT NULL COMMENT '日注册成本（日推广成本/日注册数）',
  `total_input_output_ratio` decimal(12,4) unsigned DEFAULT NULL COMMENT '累计投入产出比（累计充值金额*0.6/累计推广成本）',
  `stime` int(1) unsigned DEFAULT NULL COMMENT '统计时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='秀场推广统计';


DROP TABLE IF EXISTS `web_channel_total_uids`;
CREATE TABLE IF NOT EXISTS `web_channel_total_uids` (
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `channel_id` varchar(20) NOT NULL DEFAULT '',
  `sdate` varchar(10) DEFAULT NULL COMMENT '用户注册日期',
  PRIMARY KEY (`uid`),
  KEY `channel_id` (`channel_id`),
  KEY `idx_sdate` (`sdate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
-- --------------------------------------------------------
--
-- 限制导出的表
--

--
-- 限制表 `web_partner_channel`
--
ALTER TABLE `web_partner_channel`
  ADD CONSTRAINT `web_partner_channel_ibfk_1` FOREIGN KEY (`partner_id`) REFERENCES `web_partner_info` (`partner_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 限制表 `web_partner_subuser_channel`
--
ALTER TABLE `web_partner_subuser_channel`
  ADD CONSTRAINT `web_partner_subuser_channel_ibfk_1` FOREIGN KEY (`channel_id`) REFERENCES `web_partner_channel` (`channel_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `web_partner_subuser_channel_ibfk_2` FOREIGN KEY (`partner_id`) REFERENCES `web_partner_info` (`partner_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 限制表 `web_regpromote_user_channel`
--
ALTER TABLE `web_regpromote_user_channel`
  ADD CONSTRAINT `web_regpromote_user_channel_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `web_regpromote_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
