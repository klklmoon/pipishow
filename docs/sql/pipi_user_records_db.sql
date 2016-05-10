USE `pipi_user_records_db` ;

--
-- 数据库: `pipi_user_records_db`
--

-- --------------------------------------------------------

--
-- 表的结构 `web_user_login_records`
--

DROP TABLE IF EXISTS `web_user_login_records`;
CREATE TABLE IF NOT EXISTS `web_user_login_records` (
  `record_id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `login_ip` varchar(32) NOT NULL COMMENT '登录ＩＰ',
  `login_page` varchar(255) NOT NULL COMMENT '登录前置页面',
  `login_time` int(11) NOT NULL COMMENT '登录时间',
  `login_type` varchar(15) NOT NULL COMMENT '登录类型　第三方　还是官方',
  PRIMARY KEY (`record_id`),
  KEY `idx_uid_logintime` (`uid`,`login_time`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8  ;

-- --------------------------------------------------------

--
-- 表的结构 `web_user_recharge_records`
--

DROP TABLE IF EXISTS `web_user_recharge_records`;
CREATE TABLE IF NOT EXISTS `web_user_recharge_records` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '充值记录id',
  `ruid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '充值用户id',
  `uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '充值目标用户id',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '充值金额',
  `currencycode` char(10) DEFAULT NULL COMMENT '币种',
  `pipiegg` decimal(10,2) DEFAULT NULL COMMENT '用户充皮蛋数',
  `rorderid` varchar(64) NOT NULL DEFAULT '' COMMENT '充值订单号',
  `rsource` varchar(20) NOT NULL DEFAULT '' COMMENT '充值订单来源',
  `rtime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '充值时间',
  `rip` char(15) NOT NULL DEFAULT '' COMMENT '充值ip',
  `issuccess` tinyint(3) NOT NULL DEFAULT '0' COMMENT '充值是否成功',
  `cbalance` decimal(12,4) NOT NULL DEFAULT '0.0000' COMMENT '用户当前余额',
  `cpipiegg` decimal(12,4) DEFAULT NULL COMMENT '用户当前皮蛋',
  `sign` varchar(256) NOT NULL DEFAULT '' COMMENT '订单签名',
  `summary` varchar(20) DEFAULT NULL COMMENT '充值摘要',
  `ctime` int(11) unsigned DEFAULT NULL COMMENT '成交时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `rorderid` (`rorderid`) USING BTREE,
  KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='账户充值记录' ;
