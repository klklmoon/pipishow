USE `lt_account_db`;
--
-- 数据库: `lt_account_db`
--

-- --------------------------------------------------------

--
-- 表的结构 `uc_account_admins`
--

DROP TABLE IF EXISTS `uc_account_admins`;
CREATE TABLE IF NOT EXISTS `uc_account_admins` (
  `uid` int(11) NOT NULL DEFAULT '0',
  `level` tinyint(3) DEFAULT NULL,
  `upurview` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `uc_amourechstat`
--

DROP TABLE IF EXISTS `uc_amourechstat`;
CREATE TABLE IF NOT EXISTS `uc_amourechstat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sdate` varchar(10) DEFAULT NULL COMMENT '统计数据日期',
  `currencycode` char(10) DEFAULT NULL COMMENT '币种',
  `acpnum` int(11) DEFAULT NULL COMMENT '充值人数',
  `avaorenum` int(11) DEFAULT NULL COMMENT '有效订单数',
  `ordersnum` int(11) DEFAULT NULL COMMENT '订单总数',
  `amchamount` decimal(12,4) DEFAULT NULL COMMENT '充值金额',
  `srate` varchar(10) DEFAULT NULL,
  `stime` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '统计时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='充值数据统计';

-- --------------------------------------------------------

--
-- 表的结构 `uc_amourechstat2`
--

DROP TABLE IF EXISTS `uc_amourechstat2`;
CREATE TABLE IF NOT EXISTS `uc_amourechstat2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sdate` varchar(10) DEFAULT NULL,
  `pipiegg` int(11) DEFAULT NULL,
  `rsource` varchar(20) DEFAULT NULL,
  `totalcount` int(11) DEFAULT NULL,
  `succedcount` int(11) DEFAULT NULL,
  `srate` varchar(10) DEFAULT NULL,
  `stime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `uc_proxyrechargelog`
--

DROP TABLE IF EXISTS `uc_proxyrechargelog`;
CREATE TABLE IF NOT EXISTS `uc_proxyrechargelog` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `patype` tinyint(3) unsigned DEFAULT NULL COMMENT '代充类型',
  `uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '充值uid',
  `pipiegg` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '充值皮蛋数',
  `cpipiegg` decimal(12,4) DEFAULT NULL COMMENT '皮蛋余额',
  `ouid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '操作人uid',
  `oip` varchar(15) DEFAULT NULL COMMENT '操作ip',
  `otime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '操作时间',
  `reason` varchar(600) DEFAULT NULL COMMENT '代充原因',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='代充记录';
-- --------------------------------------------------------


--
-- 表的结构 `uc_visitlog`
--

DROP TABLE IF EXISTS `uc_visitlog`;
CREATE TABLE IF NOT EXISTS `uc_visitlog` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '访问记录id',
  `vuid` int(11) unsigned DEFAULT '0' COMMENT '访问用户id',
  `vip` int(11) DEFAULT NULL COMMENT '访问ip',
  `visittime` int(11) unsigned DEFAULT NULL COMMENT '访问时间',
  `action` int(11) DEFAULT NULL COMMENT '操作类型',
  `summary` varchar(100) DEFAULT NULL COMMENT '操作摘要',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='用户访问日志';
