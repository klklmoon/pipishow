<?php
/**
 * @author Su qian <suqian@pipi.cn> 2013-4-3
 * @link http://show.pipi.cn
 * @copyright Copyright &copy; 2003-2110 show.pipi.cn
 * @license 
 */

$keyConfig = array(
	'redis' => array(
		'user_session'=>array('mobile_user_session'=>'mobile_uid_'),
		'user_login' => 'uid_',//用户登录Redis信息存储
		'event' => array(
			'eventKey'		=> 'zmqEventKey',	//事件流水线号
			'eventListKey'	=> 'zmqEventList'	//事件失败队列
		), 
		'user_info' => array(
			'key' => 'user_info',
			'property' => array('nk','uid','rk','pk','dk','ut','us','st','pe','fe','de','ep','ch','cp','mc','vip','car','md','lb','gd','atr','to','u_rk','gs','num','fp','agent')
		),
		'other' => array(
			'all_total_online'=>'all_total_online',              //全站在线人数
			'archives'              => 'archives_',              //档期相关信息
			'archives_uid'          => 'archives_uid_',          //uid存储档期相关信息
			'userList'              => 'test_userlist_archives_',//直播间用户列表
			'giftList'				=> 'giftList',				 //礼物列表
			'doteySong'				=> 'doteySong',				 //主播歌单
			'dotey_info'			=> 'dotey_info_', 			 //主播信息
			'archives_parking'	=>'archives_parking_',		//档期停车位
			'all_dotey_charm_today_rank'=>'all_dotey_charm_today_rank_uid_', //所有主播魅力今日排行榜
			'all_dotey_charm_week_rank'=>'all_dotey_charm_week_rank_uid_', //所有主播魅力本周排行榜
			'dotey_charm_today_rank'=>'dotey_charm_today_rank',	 //主播魅力今日排行榜
			'dotey_charm_week_rank'	=>'dotey_charm_week_rank',	 //主播魅力本周排行榜
			'dotey_charm_month_rank'=>'dotey_charm_month_rank',	 //主播魅力本月排行榜
			'dotey_charm_super_rank'=>'dotey_charm_super_rank',	 //主播魅力超级排行榜
			'dotey_songs_today_rank'=>'dotey_songs_today_rank',	 //主播点唱今日排行榜
			'dotey_songs_week_rank'	=>'dotey_songs_week_rank',	 //主播点唱本周排行榜
			'dotey_songs_month_rank'=>'dotey_songs_month_rank',	 //主播点唱本月排行榜
			'dotey_songs_super_rank'=>'dotey_songs_super_rank',	 //主播点唱超级排行榜
			'dotey_fans_super_rank' => 'dotey_fans_super_rank',	 //主播粉丝超级排行榜
			'dotey_fans_new_rank'	=>'dotey_fans_new_rank',	 //主播粉丝新个榜
			'user_songs_today_rank' =>'user_songs_today_rank',	 //用户点唱今日排行榜
			'user_songs_week_rank'	=>'user_songs_week_rank',	 //用户点唱本月排行榜
			'user_songs_month_rank' =>'user_songs_month_rank',	 //用户点唱本周排行榜
			'user_songs_super_rank' =>'user_songs_super_rank',	 //用户点唱超级排行榜
			'user_rich_today_rank'	=>'user_rich_today_rank',	 //用户富豪今日排行榜
			'user_rich_week_rank'	=>'user_rich_week_rank',	 //用户富豪本周排行榜
			'user_rich_month_rank'	=>'user_rich_month_rank',	 //用户富豪本月排行榜
			'user_rich_super_rank'	=>'user_rich_super_rank',	 //用户富豪超级排行榜
			'user_friendly_today_rank' => 'user_friendly_today_rank',//用户情谊今日排行榜
		    'user_friendly_week_rank' => 'user_friendly_week_rank',//用户情谊本周排行榜
		    'user_friendly_month_rank' => 'user_friendly_month_rank',//用户情谊本月排行榜
		    'user_friendly_super_rank' => 'user_friendly_super_rank',//用户情谊超级排行榜
			'dotey_gift_week_rank'	=>'dotey_gift_week_rank',// 本周主播收取礼物排行榜
			'dotey_gift_lastweek_rank'=>'dotey_gift_lastweek_rank',//上周主播收取礼物排行榜
			'dotey_gift_super_rank'=>'dotey_gift_super_rank',//主播送礼超级排行榜
			'dotey_living' => 'dotey_living',//正在直播的档期
			'dotey_will_live' => 'dotey_will_live',//待直播的档期
			'operate'=>'opeate',//运营相关数据
			'channel'=>'channel',//频道相关数据
			'crown'=>'crown_',//本场皇冠粉丝
			'archives_dedication'=>'archives_dedication_',//直播间本场粉丝榜
			'week_dedication'=>'week_dedication_',//直播间本周粉丝榜
			'month_dedication'=>'month_dedication_',//直播间本月粉丝榜
			'super_dedication'=>'super_dedication_',//直播间超级粉丝榜
			'archives_gift'=>'archives_gift_',//直播间礼物
			'most_archives_dedication'=>'most_archives_dedication_',//直播间单次送礼最大值
			'archives_dy_msg'=>'archives_dy_msg_',//直播间动态消息
			'most_dedication'=>'most_dedication',//全局超过80个皮蛋的礼物
			'kefu' => 'kefu',//客服相关
			'chat_set'=>'chat_set_', //直播间发言设置
			'allow_song'=>'allow_song_', //是否允许点歌
			'last_fly_screen_time'=>'last_fly_screen_time', //最后一次发送飞屏的时间
			'user_all_rank'=>'user_all_rank',//用户等级设置
			'dotey_all_rank'=>'dotey_all_rank',//主播等级设置
			'dotey_today_recommand'=>'dotey_today_recommand',//主播今日推荐
			'chat_word'=>'chat_bad_word',// 聊天敏感词
			'web_site_config'=>'web_site_config',	//网站配置
			'labelList'=>'labelList_',              //直播间被贴用户列表
			'manageList'=>'manageList_',             //直播间房管列表
			'archives_forbid'=>'archives_forbid_' ,   //直播间被禁言列表	
			'archives_kickout'=>'archives_kickout_',	  //直播间被踢出列表
			'index_right_data_type_rookiedotey'=>'index_right_data_type_rookiedotey',//首页右侧版块 新秀主播
			'index_right_data_type_newdotey'=>'index_right_data_type_newdotey',	//首页右侧版块 新加入主播
			'index_right_data_type_stardotey'=>'index_right_data_type_stardotey',
			'archives_friendly'=>'archives_friendly_',              //本场情谊榜
			'week_archives_friendly'=>'week_archives_friendly_',    //本周情谊榜
			'week_gift_star_rank_web'=>'week_gift_star_rank_web_',	//周礼物之星排行榜用于活动页面
			'week_gift_star_rank_lingbox'=>'week_gift_star_rank_lingbox_',	//周礼物之星排行榜用于直播间
			'activity_guardangel_luckdotey' => 'activity_guardangel_luckdotey',//天使守护-幸运主播
			'activity_guardangel_dotey_rank' => 'activity_guardangel_dotey_rank',//天使守护-主播榜
			'activity_guardangel_user_rank' => 'activity_guardangel_user_rank',//天使守护-主播榜
			'task_list' => 'task_list', //新手任务列表
			'phone_code_list'=>'phone_code_',                       //手机端验证码
			'dice_game_record'=>'dice_game_record_' ,                 //骰子游戏记录
			'happy_birthday_page'=>'happy_birthday_page',			//生日快乐活动页面数据
			'luck_star'=>'luck_star',                  //每日幸运星
			'last_week_star_singer'=>'last_week_star_singer',		//上周唱将数据
			'family' => 'family_',	//家族基本信息
			'family_top_charm' => 'family_top_charm_', //家族总魅力值榜,分day、week、month、super榜
			'family_top_delication' => 'family_top_delication_', //家族总贡献值榜,分day、week、month、super榜
			'family_top_medal' => 'family_top_medal_', //家族族徽总销量榜,分day、week、month、super榜
			'truck_gift'=>'truck_gift',                             //跑道礼物   
			'last_send_dice_time'=>'last_send_dice_time',           //发送骰子时间间隔  
			'archives_face'=>'archives_face',                       //直播间表情
			'full_site_broadcast'=>'full_site_broadcast',           //全站广播
			'agent_sales_top'=>'agent_sales_top',				//代理销售榜
			'dotey_rank_count' => 'dotey_rank_count',			//主播等级人数
			'tag_dotey_uids' => 'tag_dotey_uids_',				//某个印象标签的主播
			'recommend_red_dotey' => 'recommend_red_dotey',		//首页最新加入
			'recommend_blue_dotey' => 'recommend_blue_dotey',		//首页新秀主播
			'battle_16'	=> 'battle_16', //女神争夺战16强
			'battle_8'	=> 'battle_8', //女神争夺战8强
			'battle_4'	=> 'battle_4', //女神争夺战4强
			'battle_2'	=> 'battle_2', //女神争夺战2强
			'battle_1'	=> 'battle_1', //女神争夺战冠军
		),
		'token'=>'token_'                     //聊天token
	 ),
	'sequence'=>array(
	 	'USER_ID'=>'USER_ID',//用户登录ID的KEY标识
	 ),	
);