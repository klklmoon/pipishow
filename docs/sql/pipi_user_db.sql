USE `pipi_user_db` ;


--
-- 数据库: `pipi_user_db`
--

-- --------------------------------------------------------

--
-- 表的结构 `web_dotey_apply`
--

DROP TABLE IF EXISTS `web_dotey_apply`;
CREATE TABLE IF NOT EXISTS `web_dotey_apply` (
  `uid` int(11) NOT NULL COMMENT '主播申请表',
  `birth_province` varchar(55) NOT NULL COMMENT '出生地身份',
  `birth_city` varchar(55) NOT NULL COMMENT '出生地城市',
  `internet_condition` varchar(55) NOT NULL COMMENT '上网环境',
  `id_card_front` varchar(200) NOT NULL COMMENT '身份证正面',
  `id_card_back` varchar(200) NOT NULL COMMENT '身份证背面',
  `personal_image` varchar(200) NOT NULL COMMENT '个人形象照',
  `has_experience` tinyint(1) NOT NULL COMMENT '主播经验，0无，1有',
  `live_address` varchar(255) DEFAULT NULL COMMENT '有主播经验时的直播间链接地址',
  `op_uid` int(11) NOT NULL COMMENT '操作的主播经理人uid',
  `reason` varchar(255) DEFAULT NULL COMMENT '操作原因',
  `create_time` int(11) NOT NULL COMMENT '申请时间',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8  COMMENT='主播申请表';

-- --------------------------------------------------------

--
-- 表的结构 `web_dotey_area_range`
--

DROP TABLE IF EXISTS `web_dotey_area_range`;
CREATE TABLE IF NOT EXISTS `web_dotey_area_range` (
  `rang_id` smallint(5) NOT NULL AUTO_INCREMENT COMMENT '区域标识',
  `rang_type` tinyint(3) NOT NULL COMMENT '区域类型　０南方　１表示北方　２表示江浙沪　',
  `rang_area` smallint(5) NOT NULL COMMENT '省份',
  PRIMARY KEY (`rang_id`),
  KEY `idx_type_area` (`rang_type`,`rang_area`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8  COMMENT='主播大区域表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `web_dotey_base`
--

DROP TABLE IF EXISTS `web_dotey_base`;
CREATE TABLE IF NOT EXISTS `web_dotey_base` (
  `uid` int(11) NOT NULL COMMENT '用户ＩＤ',
  `sub_channel` smallint(5) NOT NULL COMMENT '主播子频道',
  `sign_type` tinyint(3) NOT NULL COMMENT '签约类型　１表示秀场　2表示家族　4表示Ｃ站',
  `status` tinyint(1) NOT NULL COMMENT '状态　０待处理　１已签约　２已拒绝 3已授权/未签约',
  `dotey_type` smallint(3) NOT NULL COMMENT '主播类型　1表示直营 2表示代理 3表示全职',
  `proxy_uid` int(11) NOT NULL COMMENT '代理人uid',
  `tutor_uid` int(11) NOT NULL COMMENT '导师uid',
  `check_time` int(11) NOT NULL COMMENT '审核时间',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '修改时间',
  `update_desc` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='主播基本信息表';
-- --------------------------------------------------------

--
-- 表的结构 `web_dotey_fans`
--

DROP TABLE IF EXISTS `web_dotey_fans`;
CREATE TABLE IF NOT EXISTS `web_dotey_fans` (
  `uid` int(11) NOT NULL COMMENT '用户ＩＤ',
  `fans_uid` int(11) NOT NULL COMMENT '粉丝ＩＤ',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`fans_uid`,`uid`),
  KEY `idx_uid_fansuid_createtime` (`uid`,`fans_uid`,`create_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8  COMMENT='主播粉丝表,如果关注主播时，在用户粉丝的数据上冗余存储';

-- --------------------------------------------------------

--
-- 表的结构 `web_dotey_proxy`
--

DROP TABLE IF EXISTS `web_dotey_proxy`;
CREATE TABLE IF NOT EXISTS `web_dotey_proxy` (
  `uid` int(11) NOT NULL COMMENT '主播代理人',
  `type` tinyint(4) NOT NULL COMMENT '导师、代理类别，1导师，2代理',
  `agency` varchar(50) NOT NULL COMMENT '代理机构名',
  `company` varchar(100) NOT NULL COMMENT '代理公司名称',
  `id_card_pic` int(11) NOT NULL COMMENT '身份证复印件',
  `business_license` int(11) NOT NULL COMMENT '公司企业执照复印件',
  `note` varchar(255) NOT NULL COMMENT '备注记录',
  `query_allow` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否允许代理查询',
  `is_display` tinyint(1) NOT NULL DEFAULT '1' COMMENT '前台显示，1显示，0隐藏',
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8  COMMENT='导师（主播经纪人）、代理人表';

-- --------------------------------------------------------

--
-- 表的结构 `web_message_content`
--

DROP TABLE IF EXISTS `web_message_content`;
CREATE TABLE IF NOT EXISTS `web_message_content` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '消息ＩＤ',
  `uid` int(11) NOT NULL COMMENT '消息创建者',
  `receive_uid` varchar(255) NOT NULL COMMENT '接收用户.可以是一个人　也可以是多个人',
  `title` varchar(255) NOT NULL COMMENT '消息标题',
  `content` text NOT NULL COMMENT '消息内容',
  `is_read` tinyint(2) NOT NULL COMMENT '是否已读　０表示未读　１表示已读',
  `extra` text NOT NULL COMMENT '扩展字段',
  `category` tinyint(3) unsigned NOT NULL COMMENT '消息类型　　0表示请求　 1表示广播　 2表示系统通知',
  `sub_category` tinyint(3) NOT NULL COMMENT '子分类　',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`message_id`),
  KEY `idx_uid_mtype_time` (`uid`,`category`,`sub_category`,`create_time`) USING BTREE,
  KEY `idx_category_isread_time` (`category`,`sub_category`,`is_read`,`create_time`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8  COMMENT='消息内容表'  ;

-- --------------------------------------------------------

--
-- 表的结构 `web_message_relation`
--

DROP TABLE IF EXISTS `web_message_relation`;
CREATE TABLE IF NOT EXISTS `web_message_relation` (
  `relation_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '关系ＩＤ',
  `uid` int(11) NOT NULL COMMENT '消息接收者',
  `is_own` tinyint(2) NOT NULL COMMENT '是否是自己的，０表示不是，１表示是',
  `message_id` int(11) NOT NULL COMMENT '消息ＩＤ',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`relation_id`),
  KEY `idx_uid_isown_ctime` (`uid`,`is_own`,`create_time`) USING BTREE,
  KEY `idx_messageid` (`message_id`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8  COMMENT='消息关系表' ;

-- --------------------------------------------------------

--
-- 表的结构 `web_message_statistics`
--

DROP TABLE IF EXISTS `web_message_statistics`;
CREATE TABLE IF NOT EXISTS `web_message_statistics` (
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `request_friends` int(11) NOT NULL COMMENT '好友请求总数',
  `unread_request_friends` int(11) NOT NULL COMMENT '未读好友请求数',
  `request_joinfamilys` int(11) NOT NULL COMMENT '加入家族请求数',
  `unread_request_joinfamilys` int(11) NOT NULL COMMENT '未读加入家族请求数',
  `system_upgrades` int(11) NOT NULL COMMENT '系统升级总数',
  `unread_system_upgrades` int(11) NOT NULL COMMENT '未读系统升级总数',
  `system_fans` int(11) NOT NULL COMMENT '粉丝关注总数',
  `unread_system_fans` int(11) NOT NULL COMMENT '未读粉丝关注数',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- 表的结构 `web_sequence`
--

DROP TABLE IF EXISTS `web_sequence`;
CREATE TABLE IF NOT EXISTS `web_sequence` (
  `name` varchar(50) NOT NULL,
  `current_value` int(11) NOT NULL,
  `increment` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`name`),
  UNIQUE KEY `idx_name` (`name`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- 表的结构 `web_tags`
--

DROP TABLE IF EXISTS `web_tags`;
CREATE TABLE IF NOT EXISTS `web_tags` (
  `tag_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '标签ＩＤ',
  `tag_name` varchar(50) NOT NULL COMMENT '标答名称',
  `tag_type` tinyint(1) NOT NULL COMMENT '标答类型　０表示主播标签',
  `is_display` tinyint(1) NOT NULL COMMENT '是否隐藏',
  `use_nums` int(6) NOT NULL COMMENT '使用次数',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`tag_id`),
  KEY `idx_tagtype_isdisplay_ctime` (`tag_type`,`is_display`,`create_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8  COMMENT='主播标签' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `web_user_base`
--

DROP TABLE IF EXISTS `web_user_base`;
CREATE TABLE IF NOT EXISTS  `web_user_base` (
  `uid` int(11) unsigned NOT NULL COMMENT '与ucenter 同一id',
  `username` char(64) NOT NULL DEFAULT '' COMMENT '注册账号',
  `nickname` varchar(20) NOT NULL DEFAULT '' COMMENT '用户昵称',
  `realname` varchar(25) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `password` char(32) NOT NULL DEFAULT '' COMMENT '登陆密码',
  `user_status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否被封停',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '注册时间',
  `reg_ip` varchar(15) NOT NULL DEFAULT '' COMMENT '注册ip',
  `reg_source` tinyint(3) NOT NULL DEFAULT '0' COMMENT '注册来源 0表示本站，１表示QQ ,2表示人人,３表示360',
  `reg_email` varchar(255) NOT NULL DEFAULT '' COMMENT '注册邮箱',
  `reg_salt` varchar(32) NOT NULL COMMENT '注册干扰码',
  `user_type` smallint(6) NOT NULL DEFAULT '1' COMMENT '用户类型 按位叠加用户身份　１表示普通用户　２表示主播　４表是直播间总管　',
  `recharge` int(11) NOT NULL COMMENT '充值总数',
  `recharge_usd` int(11) NOT NULL COMMENT '累计充值美元',
  `update_desc` varchar(1000) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `uidx_username` (`username`),
  KEY `create_time` (`create_time`,`reg_ip`),
  KEY `idx_uid_lasttime` (`uid`) USING BTREE,
  KEY `uidx_email` (`reg_email`),
  KEY `uidx_nickname` (`nickname`) USING BTREE,
  KEY `idx_regip_createtime` (`reg_ip`,`create_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='账号基本信息';


-- --------------------------------------------------------

--
-- 表的结构 `web_user_config`
--

DROP TABLE IF EXISTS `web_user_config`;
CREATE TABLE IF NOT EXISTS `web_user_config` (
  `uid` int(11) NOT NULL COMMENT '用户ＩＤ',
  `blacklist` text NOT NULL COMMENT '黑名单',
  `sheildmessage` text NOT NULL COMMENT '屏蔽消息类型',
  `sheilddynamic` text NOT NULL COMMENT '屏蔽动态类型',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8  COMMENT='消息配置表';

-- --------------------------------------------------------

--
-- 表的结构 `web_user_extend`
--

DROP TABLE IF EXISTS `web_user_extend`;
CREATE TABLE IF NOT EXISTS `web_user_extend` (
  `uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `gender` tinyint(1) NOT NULL DEFAULT '0' COMMENT '性别：1男性、2女性、0保密',
  `birthday` int(11) NOT NULL COMMENT '出生日期',
  `blood` varchar(2) NOT NULL COMMENT '血型',
  `mobile` varchar(15) NOT NULL COMMENT '手机号码',
  `tel` varchar(30) NOT NULL COMMENT '电话',
  `qq` varchar(20) NOT NULL COMMENT 'qq号码',
  `id_card` varchar(20) NOT NULL COMMENT '身份证',
  `province` varchar(55) NOT NULL COMMENT '所在省份',
  `city` varchar(55) NOT NULL COMMENT '所在城市',
  `district` varchar(50) NOT NULL COMMENT '乡或者区',
  `street` varchar(255) NOT NULL COMMENT '街道',
  `zip` varchar(10) NOT NULL COMMENT '邮编',
  `profession` varchar(55) NOT NULL COMMENT '职业',
  `skill` varchar(255) NOT NULL COMMENT '技能',
  `edu_backgroud` varchar(30) NOT NULL COMMENT '教育背景',
  `description` text NOT NULL COMMENT '描述',
  `bank` varchar(55) NOT NULL COMMENT '开户银行',
  `bank_account` varchar(30) NOT NULL COMMENT '银行账号',
  `height` varchar(10) NOT NULL COMMENT '身高',
  `weight` varchar(10) NOT NULL COMMENT '体重',
  `bwh` varchar(25) NOT NULL COMMENT '三围',
  `size` varchar(10) NOT NULL COMMENT '鞋码',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8  COMMENT='用户扩展信息';

-- --------------------------------------------------------

--
-- 表的结构 `web_user_fans`
--

DROP TABLE IF EXISTS `web_user_fans`;
CREATE TABLE IF NOT EXISTS `web_user_fans` (
  `uid` int(11) NOT NULL COMMENT '用户ＩＤ',
  `fans_uid` int(11) NOT NULL COMMENT '粉丝ＩＤ',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`fans_uid`,`uid`),
  KEY `idx_uid_fansuid_createtime` (`uid`,`fans_uid`,`create_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8  COMMENT='微博粉丝表';

-- --------------------------------------------------------

--
-- 表的结构 `web_user_oauth`
--

DROP TABLE IF EXISTS `web_user_oauth`;
CREATE TABLE IF NOT EXISTS `web_user_oauth` (
  `uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `openid` varchar(32) NOT NULL DEFAULT '0' COMMENT 'openid',
  `open_platform` varchar(20) NOT NULL DEFAULT '0' COMMENT '所属开放平台名称',
  `onickname` varchar(30) NOT NULL DEFAULT '0' COMMENT '平台原昵称',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`uid`),
  KEY `openid` (`openid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8  COMMENT='开放平台用户';

-- --------------------------------------------------------

--
-- 表的结构 `web_user_tags_relation`
--

DROP TABLE IF EXISTS `web_user_tags_relation`;
CREATE TABLE IF NOT EXISTS `web_user_tags_relation` (
  `relation_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '关系ＩＤ',
  `uid` int(11) NOT NULL COMMENT '用户ＩＤ',
  `tag_id` int(11) NOT NULL COMMENT '标签ＩＤ',
  `user_type` tinyint(3) NOT NULL COMMENT '用户类型',
  `tag_time` int(11) NOT NULL COMMENT '标记时间',
  PRIMARY KEY (`relation_id`),
  UNIQUE KEY `idx_uid_tagid_type` (`uid`,`tag_id`,`user_type`) USING BTREE,
  KEY `idx_uid_type_time` (`uid`,`user_type`,`tag_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8  COMMENT='用户与标签关系表' AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `web_user_operated`;
CREATE TABLE IF NOT EXISTS `web_user_operated` (
`rid`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
`uid`  int(11) NOT NULL COMMENT '目标用户UID' ,
`op_type`  char(30) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'common' COMMENT '操作类型' ,
`op_desc`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '操作描述' ,
`op_time`  int(11) NOT NULL COMMENT '操作时间' ,
`op_uid`  int(11) NOT NULL COMMENT '执行者' ,
`op_value`  varchar(255) NULL COMMENT '操作值' ,
PRIMARY KEY (`rid`),
INDEX `idx_uid_type` (`uid`, `op_type`) ,
INDEX `id_op_type` (`op_type`) 
)ENGINE=INNODB DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci COMMENT = '用户被操作记录表';


CREATE FUNCTION `func_addseq`(seq_name VARCHAR(50)) RETURNS bigint(20)
BEGIN 
         insert into web_sequence  (name, current_value)
         values( upper(seq_name), 0);
         RETURN func_currval(seq_name);  
END

CREATE FUNCTION `func_currval`(seq_name VARCHAR(50)) RETURNS bigint(20)
BEGIN
         DECLARE value BIGINT;
         SELECT current_value INTO value
         FROM web_sequence
         WHERE upper(name) = upper(seq_name);
         RETURN value;
END

CREATE  FUNCTION `func_nextval`(seq_name varchar(50)) RETURNS int(11)
begin 
  update web_sequence
  set current_value = current_value + increment
  where name = seq_name;
 return func_currval(seq_name);
end

CREATE  FUNCTION `func_setval`(seq_name VARCHAR(50), value BIGINT) RETURNS bigint(20)
BEGIN 
         UPDATE web_sequence  
         SET current_value = value  
         WHERE upper(name) = upper(seq_name); 
         RETURN func_currval(seq_name);  
END
