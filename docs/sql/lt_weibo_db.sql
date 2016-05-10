USE `lt_weibo_db` ;


--
-- 表的结构 `web_user_weibo`
--

DROP TABLE IF EXISTS `web_user_weibo`;
CREATE TABLE IF NOT EXISTS `web_user_weibo` (
  `uw_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ＩＤ',
  `uid` int(11) NOT NULL COMMENT '关注的用户',
  `weibo_id` int(11) NOT NULL COMMENT '收到的微博',
  `type` tinyint(3) NOT NULL COMMENT '类型　同web_weibo中type',
  `create_time` int(11) NOT NULL COMMENT '推送时间',
  PRIMARY KEY (`uw_id`),
  KEY `idex_weibo` (`weibo_id`) USING BTREE,
  KEY `idx_uid_createtime` (`uid`,`create_time`) USING BTREE,
  KEY `idx_type_createtime` (`uid`,`type`,`create_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='关注的人的微博推送表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `web_user_weibo_statistics`
--

DROP TABLE IF EXISTS `web_user_weibo_statistics`;
CREATE TABLE IF NOT EXISTS `web_user_weibo_statistics` (
  `uid` int(11) NOT NULL,
  `weibos` int(11) NOT NULL,
  `fans` int(11) NOT NULL,
  `attentions` int(11) NOT NULL COMMENT '微博统计表',
  `unread_at_weibos` smallint(5) NOT NULL COMMENT '未读的@我的微博',
  `unread_at_comments` smallint(5) NOT NULL COMMENT '未读的@我的评论数',
  `unread_comments` smallint(5) NOT NULL COMMENT '未读的评论数',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `web_weibo`
--

DROP TABLE IF EXISTS `web_weibo`;
CREATE TABLE IF NOT EXISTS `web_weibo` (
  `weibo_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '微博标识',
  `uid` int(11) NOT NULL COMMENT '创建者用户ＩＤ',
  `target_id` int(11) NOT NULL COMMENT '目标ＩＤ',
  `type` tinyint(3) NOT NULL COMMENT '1微博类型  2表示转发 4表示文字，8表示图片，16表示链接，32表示视频',
  `source` smallint(5) NOT NULL COMMENT '微博来源',
  `subsource` smallint(5) NOT NULL COMMENT '微博子来源',
  `transmits` int(11) NOT NULL COMMENT '转发数',
  `comments` int(11) NOT NULL COMMENT '评论数',
  `praises` int(11) NOT NULL COMMENT '赞扬数',
  `content` varchar(255) NOT NULL COMMENT '轻文本',
  `extra` text NOT NULL COMMENT '额外信息　',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`weibo_id`),
  KEY `idx_uid_createtime` (`uid`) USING BTREE,
  KEY `idx_uid_targetid_createtime` (`uid`,`target_id`,`create_time`) USING BTREE,
  KEY `idx_uid_type_createtime` (`uid`,`type`,`create_time`) USING BTREE,
  KEY `idx_type_createtime` (`type`,`create_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微博表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `web_weibo_atme`
--

DROP TABLE IF EXISTS `web_weibo_atme`;
CREATE TABLE IF NOT EXISTS `web_weibo_atme` (
  `at_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '标识',
  `uid` int(11) NOT NULL COMMENT '被@用户',
  `weibo_id` int(11) NOT NULL COMMENT '微博ＩＤ',
  `comment_id` int(11) NOT NULL COMMENT '评论ＩＤ',
  `type` tinyint(4) NOT NULL COMMENT '评论类型，０表示@我的微博，１表示@我的评论',
  `create_time` int(11) NOT NULL COMMENT '@时间',
  PRIMARY KEY (`at_id`),
  KEY `idx_uid_type_createtime` (`uid`,`type`,`create_time`) USING BTREE,
  KEY `idx_weiboid_createtime` (`weibo_id`,`create_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='@我的微博+评论' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `web_weibo_comment`
--

DROP TABLE IF EXISTS `web_weibo_comment`;
CREATE TABLE IF NOT EXISTS `web_weibo_comment` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '评论标识',
  `weibo_id` int(11) NOT NULL COMMENT '微博ＩＤ',
  `uid` int(11) NOT NULL COMMENT '用户ＩＤ',
  `content` varchar(255) NOT NULL COMMENT '评论内容',
  `create_time` int(11) NOT NULL COMMENT '评论时间',
  PRIMARY KEY (`comment_id`),
  KEY `idx_weiboid` (`weibo_id`) USING BTREE,
  KEY `idx_uid_createtime` (`uid`,`create_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微博评论表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `web_weibo_comment_relation`
--

DROP TABLE IF EXISTS `web_weibo_comment_relation`;
CREATE TABLE IF NOT EXISTS `web_weibo_comment_relation` (
  `uid` int(11) NOT NULL COMMENT '被评论人',
  `comment_id` int(11) NOT NULL COMMENT '评论ID',
  `create_time` int(11) NOT NULL COMMENT '评论时间',
  PRIMARY KEY (`comment_id`,`uid`),
  KEY `idx_uid_commentid_createtime` (`uid`,`comment_id`,`create_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='被评论人关系表';
