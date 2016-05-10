USE `lt_family_db`;

DROP TABLE IF EXISTS `web_family`;
CREATE TABLE `web_family` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '族长uid',
  `name` varchar(30) NOT NULL COMMENT '家族名称',
  `cover` varchar(150) DEFAULT NULL COMMENT '家族封面',
  `level` int(11) NOT NULL DEFAULT '0' COMMENT '家族等级',
  `medal` varchar(10) NOT NULL DEFAULT '' COMMENT '徽章名称',
  `status` smallint(6) NOT NULL DEFAULT '0' COMMENT '家族状态，-2未筹备成功，-1审核不通过，0申请，1审核通过，2筹备成功',
  `hidden` tinyint(1) NOT NULL DEFAULT '0' COMMENT '隐藏标志位，0显示，1隐藏',
  `forbidden` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否封停，0启用，1封停',
  `sign` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否签约家族，0否，1是',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '审核时间',
  PRIMARY KEY (`id`),
  KEY `index_uid` (`uid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='家族表';

DROP TABLE IF EXISTS `web_family_consume_records`;
CREATE TABLE `web_family_consume_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `family_id` int(11) NOT NULL,
  `charm` int(11) NOT NULL DEFAULT '0' COMMENT '每小时累计的家族魅力值',
  `dedication` int(11) NOT NULL DEFAULT '0' COMMENT '每小时累计的家族贡献值',
  `medal` int(11) NOT NULL DEFAULT '0' COMMENT '每小时累计的族徽售出量',
  `recharge` int(11) NOT NULL DEFAULT '0' COMMENT '每小时族徽成员充值数',
  `create_time` int(11) NOT NULL COMMENT '记录时间',
  PRIMARY KEY (`id`),
  KEY `family_id` (`family_id`) USING BTREE,
  KEY `time` (`create_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='家族消费统计每小时记录';

DROP TABLE IF EXISTS `web_family_extend`;
CREATE TABLE `web_family_extend` (
  `family_id` int(11) NOT NULL COMMENT '家族id',
  `announcement` text COMMENT '家族公告',
  `config` text COMMENT '家族各种规则配置项',
  `charm_total` int(11) NOT NULL DEFAULT '0' COMMENT '家族魅力累计总和',
  `dedication_total` int(11) NOT NULL DEFAULT '0' COMMENT '家族贡献累计总和',
  `medal_total` int(11) NOT NULL DEFAULT '0' COMMENT '售出的族徽总数',
  `recharge_total` int(11) NOT NULL DEFAULT '0' COMMENT '族徽成员的充值总数',
  PRIMARY KEY (`family_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='家族扩展信息';

DROP TABLE IF EXISTS `web_family_honor`;
CREATE TABLE `web_family_honor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `family_id` int(11) NOT NULL DEFAULT '0' COMMENT '家族id',
  `type` varchar(20) DEFAULT NULL COMMENT '内容类型，create家族诞生，top榜单变动，activity活动记录',
  `honor` text NOT NULL COMMENT '荣誉内容',
  `create_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `family_id` (`family_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='家族荣誉';

DROP TABLE IF EXISTS `web_family_level`;
CREATE TABLE `web_family_level` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `level` int(11) NOT NULL COMMENT '家族等级',
  `elder` int(11) NOT NULL COMMENT '长老数',
  `admin` int(11) NOT NULL COMMENT '家族管理数',
  `dotey` int(11) NOT NULL COMMENT '主播数',
  `members` int(11) NOT NULL COMMENT '成员数',
  `upgrade` int(11) NOT NULL COMMENT '升级需要的皮蛋数',
  `keep` int(11) NOT NULL COMMENT '保级需要的皮蛋数',
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='家族等级';

INSERT INTO `web_family_level` VALUES ('1', '1', '1', '3', '2', '20', '0', '0', '0');
INSERT INTO `web_family_level` VALUES ('2', '2', '2', '8', '10', '100', '1000000', '200000', '0');
INSERT INTO `web_family_level` VALUES ('3', '3', '5', '15', '25', '500', '8000000', '800000', '0');
INSERT INTO `web_family_level` VALUES ('4', '4', '8', '25', '50', '1500', '20000000', '2000000', '0');
INSERT INTO `web_family_level` VALUES ('5', '5', '12', '30', '100', '3000', '50000000', '4500000', '0');
INSERT INTO `web_family_level` VALUES ('6', '6', '15', '50', '0', '0', '100000000', '8000000', '0');

DROP TABLE IF EXISTS `web_family_level_records`;
CREATE TABLE `web_family_level_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `family_id` int(11) NOT NULL DEFAULT '0' COMMENT '家族id',
  `type` varchar(20) NOT NULL DEFAULT 'upgrade' COMMENT '家族级别变更类型，upgrade升级，keep保级，degrade降级',
  `create_time` int(10) unsigned zerofill DEFAULT '0000000000' COMMENT '发生时间',
  PRIMARY KEY (`id`),
  KEY `family_id` (`family_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='家族级别变动记录';

DROP TABLE IF EXISTS `web_family_member`;
CREATE TABLE `web_family_member` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `family_id` int(11) NOT NULL COMMENT '家族id',
  `uid` int(11) NOT NULL COMMENT '成员id',
  `role_id` int(11) NOT NULL COMMENT '角色id',
  `is_dotey` tinyint(1) NOT NULL COMMENT '是否主播，0否，1是',
  `family_dotey` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否家族主播，0否，1是',
  `medal_enable` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否佩戴家族徽章',
  `equip_time` int(11) NOT NULL DEFAULT '0' COMMENT '徽章佩戴时间',
  `have_medal` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否拥有家族徽章',
  `buy_type` smallint(3) NOT NULL DEFAULT '0' COMMENT '购买方式，0特殊角色自动拥有，1花费皮蛋购买',
  `buy_time` int(11) NOT NULL DEFAULT '0' COMMENT '购买时间',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '加入时间',
  PRIMARY KEY (`id`),
  KEY `index_family_id` (`family_id`) USING BTREE,
  KEY `index_uid` (`uid`,`medal_enable`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='家族成员';

DROP TABLE IF EXISTS `web_family_apply_records`;
CREATE TABLE `web_family_member_apply_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '申请用户id',
  `family_id` int(11) NOT NULL DEFAULT '0' COMMENT '家族id',
  `apply_type` smallint(6) NOT NULL DEFAULT '0' COMMENT '申请的身份，0为家族成员，1为家族主播',
  `status` smallint(6) NOT NULL DEFAULT '0' COMMENT '申请状态，0申请，1成功，-1拒绝',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '申请时间',
  `confirm_time` int(11) NOT NULL DEFAULT '0' COMMENT '确认时间',
  PRIMARY KEY (`id`),
  KEY `Index_uid` (`uid`) USING BTREE,
  KEY `Index_family_id` (`family_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='家族成员申请记录表';

DROP TABLE IF EXISTS `web_family_operate_records`;
CREATE TABLE `web_family_operate_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `family_id` int(11) NOT NULL DEFAULT '0' COMMENT '家族id',
  `type` smallint(6) NOT NULL DEFAULT '0' COMMENT '操作类型，0审核通过，1拒绝审核，2隐藏，3显示，4封停，5启用，6解散，7签约家族审核成功，8签约家族审核拒绝',
  `reason` varchar(200) DEFAULT NULL,
  `op_uid` int(11) NOT NULL DEFAULT '0' COMMENT '操作人uid',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '操作时间',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '家族创建人uid',
  `name` varchar(30) DEFAULT NULL COMMENT '家族名称',
  `level` int(11) NOT NULL DEFAULT '0' COMMENT '家族等级',
  PRIMARY KEY (`id`),
  KEY `family_id` (`family_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='家族后台操作原因记录';

DROP TABLE IF EXISTS `web_family_quit_records`;
CREATE TABLE `web_family_quit_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `family_id` int(11) NOT NULL COMMENT '家族id',
  `uid` int(11) NOT NULL COMMENT '成员id',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '退出方式，0主动退出，1被踢出',
  `op_uid` int(11) NOT NULL DEFAULT '0' COMMENT '踢出成员时操作人uid',
  `is_dotey` int(1) NOT NULL DEFAULT '0' COMMENT '退出时是否家族主播',
  `medal_enable` int(1) NOT NULL DEFAULT '0' COMMENT '退出时是否佩戴族徽',
  `join_time` int(11) NOT NULL DEFAULT '0' COMMENT '加入时间',
  `last_time` int(11) NOT NULL DEFAULT '0' COMMENT '上次统计的时间',
  `quit_time` int(11) NOT NULL DEFAULT '0' COMMENT '退出时间',
  `charm` int(11) NOT NULL DEFAULT '0' COMMENT '上次统计完后在家族期内得到的魅力值',
  `dedication` int(11) NOT NULL DEFAULT '0' COMMENT '上次统计完后在家族期内产生的贡献值',
  PRIMARY KEY (`id`),
  KEY `family_id` (`family_id`) USING BTREE,
  KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='家族成员退出记录表';

DROP TABLE IF EXISTS `web_family_sign_apply_records`;
CREATE TABLE `web_family_sign_apply_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `family_id` int(11) NOT NULL COMMENT '家族id',
  `status` smallint(6) NOT NULL DEFAULT '0' COMMENT '申请状态，0待处理，1成功，-1拒绝',
  `create_time` int(11) NOT NULL COMMENT '申请时间',
  `confirm_time` int(11) NOT NULL COMMENT '确认时间',
  PRIMARY KEY (`id`),
  KEY `Index_family_id` (`family_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='签约家族申请记录表';

DROP TABLE IF EXISTS `web_family_unload_records`;
CREATE TABLE `web_family_unload_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `family_id` int(11) NOT NULL DEFAULT '0' COMMENT '家族id',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '成员id',
  `equip_time` int(11) NOT NULL DEFAULT '0' COMMENT '佩戴时间',
  `last_time` int(11) NOT NULL DEFAULT '0' COMMENT '上次统计的时间',
  `unload_time` int(11) NOT NULL DEFAULT '0' COMMENT '卸下时间',
  `recharge` int(11) NOT NULL DEFAULT '0' COMMENT '上次统计完后佩戴族徽期间的充值数',
  PRIMARY KEY (`id`),
  KEY `family_id` (`family_id`) USING BTREE,
  KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='家族成员卸下族徽记录表';
