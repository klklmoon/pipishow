<?php
/**
 * 测试环境运行配置
 * 
 * @author Su qian <aoxue.1988.su.qian@163.com> $date$
 * @link show.pipi.cn
 * @copyright Copyright &copy; 2003-2010 show.pipi.cn
 * @license
 */

if(!defined('YII_DEBUG')) define('YII_DEBUG', false);
if(!defined('DOMAIN')) define('DOMAIN', '.pipi.cn');

return array(
	'components'=>array(
		'user'=>array(
			'class'=>'PipiWebUser',
			'allowAutoLogin'=>true,
			'identityCookie' => array('domain'=>DOMAIN,'path'=>'/'),
			'stateKeyPrefix' =>'user_login',
			'loginUrl' => array('user/login'),  
		),
		 
		'db_user'=>array(
		 	'connectionString' =>'mysql:host=10.0.2.73;dbname=pipi_user_db',
		    'username' => 'pipiuc',
		 	'password' => 'pipi%73%*(@)db',
		 	'charset' => 'utf8',
		 	'emulatePrepare' => true,
		 	'tablePrefix' => 'web_',
		 	'class'=>'CDbConnection',
		 	'enableParamLogging' => YII_DEBUG,
		 	'schemaCachingDuration'=>3600*24,
			'enableProfiling' => YII_DEBUG,
		 ),
		 'db_user_records'=>array(
		 	'connectionString' =>'mysql:host=10.0.2.73;dbname=pipi_user_records_db',
		    'username' => 'pipiuc',
		 	'password' => 'pipi%73%*(@)db',
		 	'charset' => 'utf8',
		 	'emulatePrepare' => true,
		 	'tablePrefix' => 'web_',
		 	'class'=>'CDbConnection',
		 	'enableParamLogging' => YII_DEBUG,
		 	'schemaCachingDuration'=>3600*24,
			'enableProfiling' => YII_DEBUG,
		 ),
		 
		 'db_user_slave'=>array(
		 	'connectionString' =>'mysql:host=10.0.2.74;dbname=pipi_user_db',
		    'username' => 'spipiuc',
		 	'password' => 'spipi%74%*(@)db',
		 	'charset' => 'utf8',
		 	'emulatePrepare' => true,
		 	'tablePrefix' => 'web_',
		 	'class'=>'CDbConnection',
		 	'enableParamLogging' => YII_DEBUG,
		 	'schemaCachingDuration'=>3600*24,
			'enableProfiling' => YII_DEBUG,
		 ),
		 'db_user_records_slave'=>array(
		 	'connectionString' =>'mysql:host=10.0.2.74;dbname=pipi_user_records_db',
		    'username' => 'spipiuc',
		 	'password' => 'spipi%74%*(@)db',
		 	'charset' => 'utf8',
		 	'emulatePrepare' => true,
		 	'tablePrefix' => 'web_',
		 	'class'=>'CDbConnection',
		 	'enableParamLogging' => YII_DEBUG,
		 	'schemaCachingDuration'=>3600*24,
			'enableProfiling' => YII_DEBUG,
		 ),
		 
		 'db_consume'=>array(
		 	'connectionString' =>'mysql:host=10.0.2.75;dbname=lt_consume_db',
		    'username' => 'pipiuc',
		 	'password' => 'pipi%75%*(@)db',
		 	'charset' => 'utf8',
		 	'emulatePrepare' => true,
		 	'tablePrefix' => 'web_',
		 	'class'=>'CDbConnection',
		 	'enableParamLogging' => YII_DEBUG,
		 	'schemaCachingDuration'=>3600*24,
			'enableProfiling' => YII_DEBUG,
		 ),
		 'db_consume_records'=>array(
		 	'connectionString' =>'mysql:host=10.0.2.75;dbname=lt_consume_records_db',
		    'username' => 'pipiuc',
		 	'password' => 'pipi%75%*(@)db',
		 	'charset' => 'utf8',
		 	'emulatePrepare' => true,
		 	'tablePrefix' => 'web_',
		 	'class'=>'CDbConnection',
		 	'enableParamLogging' => YII_DEBUG,
		 	'schemaCachingDuration'=>3600*24,
			'enableProfiling' => YII_DEBUG,
		 ),
		 
		 'db_consume_slave'=>array(
		 	'connectionString' =>'mysql:host=10.0.2.76;dbname=lt_consume_db',
		    'username' => 'spipiuc',
		 	'password' => 'spipi%76%*(@)db',
		 	'charset' => 'utf8',
		 	'emulatePrepare' => true,
		 	'tablePrefix' => 'web_',
		 	'class'=>'CDbConnection',
		 	'enableParamLogging' => YII_DEBUG,
		 	'schemaCachingDuration'=>3600*24,
			'enableProfiling' => YII_DEBUG,
		 ),
		 'db_consume_records_slave'=>array(
		 	'connectionString' =>'mysql:host=10.0.2.76;dbname=lt_consume_records_db',
		    'username' => 'spipiuc',
		 	'password' => 'spipi%76%*(@)db',
		 	'charset' => 'utf8',
		 	'emulatePrepare' => true,
		 	'tablePrefix' => 'web_',
		 	'class'=>'CDbConnection',
		 	'enableParamLogging' => YII_DEBUG,
		 	'schemaCachingDuration'=>3600*24,
			'enableProfiling' => YII_DEBUG,
		 ),
		 
		 'db_archives'=>array(
		 	'connectionString' =>'mysql:host=10.0.2.79;dbname=lt_archives_db',
		 	'username' => 'pipiuc',
		 	'password' => 'pipi%79%*(@)db',
		 	'charset' => 'utf8',
		 	'emulatePrepare' => true,
		 	'tablePrefix' => 'web_',
		 	'class'=>'CDbConnection',
		 	'enableParamLogging' => YII_DEBUG,
		 	'schemaCachingDuration'=>3600*24,
		 	'enableProfiling' => YII_DEBUG,
		 ),
		
		 'db_purview'=>array(
			'connectionString' =>'mysql:host=10.0.2.79;dbname=lt_purview_db',
			'username' => 'pipiuc',
			'password' => 'pipi%79%*(@)db',
			'charset' => 'utf8',
			'emulatePrepare' => true,
			'tablePrefix' => 'web_',
			'class'=>'CDbConnection',
			'enableParamLogging' => YII_DEBUG,//供debug使用
			'schemaCachingDuration'=>3600*24,
			'enableProfiling' => YII_DEBUG,//供debug使用
		),
		
		'db_account'=>array(
			'connectionString' =>'mysql:host=10.0.2.79;dbname=lt_account_db',
			'username' => 'pipiuc',
			'password' => 'pipi%79%*(@)db',
			'charset' => 'utf8',
			'emulatePrepare' => true,
			'tablePrefix' => 'uc_',
			'class'=>'CDbConnection',
			'enableParamLogging' => YII_DEBUG,//供debug使用
			'schemaCachingDuration'=>3600*24,
			'enableProfiling' => YII_DEBUG,//供debug使用
		),
		
		'db_weibo'=>array(
			'connectionString' =>'mysql:host=10.0.2.79;dbname=lt_weibo_db',
			'username' => 'pipiuc',
			'password' => 'pipi%79%*(@)db',
			'charset' => 'utf8',
			'emulatePrepare' => true,
			'tablePrefix' => 'web_',
			'class'=>'CDbConnection',
			'enableParamLogging' => YII_DEBUG,//供debug使用
			'schemaCachingDuration'=>3600*24,
			'enableProfiling' => YII_DEBUG,//供debug使用
		),
		
		'db_archives_slave'=>array(
		 	'connectionString' =>'mysql:host=10.0.2.78;dbname=lt_archives_db',
		 	'username' => 'spipiuc',
		 	'password' => 'spipi%78%*(@)db',
		 	'charset' => 'utf8',
		 	'emulatePrepare' => true,
		 	'tablePrefix' => 'web_',
		 	'class'=>'CDbConnection',
		 	'enableParamLogging' => YII_DEBUG,
		 	'schemaCachingDuration'=>3600*24,
		 	'enableProfiling' => YII_DEBUG,
		 ),
		
		 'db_purview_slave'=>array(
			'connectionString' =>'mysql:host=10.0.2.78;dbname=lt_purview_db',
			'username' => 'spipiuc',
			'password' => 'spipi%78%*(@)db',
			'charset' => 'utf8',
			'emulatePrepare' => true,
			'tablePrefix' => 'web_',
			'class'=>'CDbConnection',
			'enableParamLogging' => YII_DEBUG,//供debug使用
			'schemaCachingDuration'=>3600*24,
			'enableProfiling' => YII_DEBUG,//供debug使用
		),
		
		'db_account_slave'=>array(
			'connectionString' =>'mysql:host=10.0.2.78;dbname=lt_account_db',
			'username' => 'spipiuc',
			'password' => 'spipi%78%*(@)db',
			'charset' => 'utf8',
			'emulatePrepare' => true,
			'tablePrefix' => 'uc_',
			'class'=>'CDbConnection',
			'enableParamLogging' => YII_DEBUG,//供debug使用
			'schemaCachingDuration'=>3600*24,
			'enableProfiling' => YII_DEBUG,//供debug使用
		),
		
		'db_weibo_slave'=>array(
			'connectionString' =>'mysql:host=10.0.2.78;dbname=lt_weibo_db',
			'username' => 'spipiuc',
			'password' => 'spipi%78%*(@)db',
			'charset' => 'utf8',
			'emulatePrepare' => true,
			'tablePrefix' => 'web_',
			'class'=>'CDbConnection',
			'enableParamLogging' => YII_DEBUG,//供debug使用
			'schemaCachingDuration'=>3600*24,
			'enableProfiling' => YII_DEBUG,//供debug使用
		),
		
		
		'db_partner'=>array(
			'connectionString' =>'mysql:host=10.0.2.81;dbname=lt_partner_db',
			'username' => 'pipiuc',
		 	'password' => 'pipi%81%*(@)db',
			'charset' => 'utf8',
			'emulatePrepare' => true,
			'tablePrefix' => 'web_',
			'class'=>'CDbConnection',
			'enableParamLogging' => YII_DEBUG,//供debug使用
			'schemaCachingDuration'=>3600*24,
			'enableProfiling' => YII_DEBUG,//供debug使用
		),
		
		'db_bbs'=>array(
				'connectionString' =>'mysql:host=10.0.2.81;dbname=lt_bbs_db',
				'username' => 'pipiuc',
				'password' => 'pipi%81%*(@)db',
				'charset' => 'utf8',
				'emulatePrepare' => true,
				'tablePrefix' => 'web_',
				'class'=>'CDbConnection',
				'enableParamLogging' => YII_DEBUG,
				'schemaCachingDuration'=>3600*24,
				'enableProfiling' => YII_DEBUG,
		),
		
		'db_common'=>array(
				'connectionString' =>'mysql:host=10.0.2.81;dbname=lt_common_db',
				'username' => 'pipiuc',
				'password' => 'pipi%81%*(@)db',
				'charset' => 'utf8',
				'emulatePrepare' => true,
				'tablePrefix' => 'web_',
				'class'=>'CDbConnection',
				'enableParamLogging' => YII_DEBUG,
				'schemaCachingDuration'=>3600*24,
				'enableProfiling' => YII_DEBUG,
		),
		'db_activity'=>array(
			'connectionString' =>'mysql:host=10.0.2.81;dbname=lt_activity_db',
			'username' => 'pipiuc',
			'password' => 'pipi%81%*(@)db',
			'charset' => 'utf8',
			'emulatePrepare' => true,
			'tablePrefix' => 'web_',
			'class'=>'CDbConnection',
			'enableParamLogging' => YII_DEBUG,
			'schemaCachingDuration'=>3600*24,
			'enableProfiling' => YII_DEBUG,
		),
		'db_family'=>array(
			'connectionString' =>'mysql:host=10.0.2.81;dbname=lt_family_db',
			'username' => 'pipiuc',
			'password' => 'pipi%81%*(@)db',
			'charset' => 'utf8',
			'emulatePrepare' => true,
			'tablePrefix' => 'web_',
			'class'=>'CDbConnection',
			'enableParamLogging' => YII_DEBUG,
			'schemaCachingDuration'=>3600*24,
			'enableProfiling' => YII_DEBUG,
		),
		
		'redis_session'=>array(
		 		'class'=>'PipiRedisConnection',
		 		'hostname'=>'10.0.2.94',
		 		'port'=> '36004',
		 		'database'=>0
		),
		'redis_user'=>array(
		  	 'class'=>'PipiRedisConnection',
		     'hostname'=>'10.0.2.93',
		     'port'=> '36001',
			 'database' => 0,
		),
        'redis_user_cache'=>array(
          	  'class'=>'PipiRedisCache',
          	  'connection' => 'redis_user',
        ),
        'redis_userinfo'=>array(
        	'class'=>'PipiRedisConnection',
        	'hostname'=>'10.0.2.93',
		 	'port'=> '36000',
        	'database' => 0,
        ),
        'redis_event'=>array(
        	'class'=>'PipiRedisConnection',
        	'hostname'=>'10.0.2.93',
		 	'port'=> '36002',
        	'database' => 0,
        ),

        'redis_cache'=>array(
        	'class'=>'PipiRedisConnection',
        	'hostname'=>'10.0.2.94',
		 	'port'=> '36003',
        	'database' => 0,
        ),

        'redis_token'=>array(
        	'class'=>'PipiRedisConnection',
        	'hostname'=>'10.0.2.94',
        	'port'=> '36005',
        	'database' => 0,
        ),

		'session' => array (
            'class'=> 'PipiRedisSession',
            'connection' => 'redis_session',
        	'cookieParams' => array('domain' => DOMAIN, 'lifetime' => 0),
        	'timeout' => 3600,
        ),
		 
        'zmq'=>array(
        	'class'=>'PipiZmq',
        	'hosts'=>array(
        		array('host'=>'10.0.2.93',
        		'port'=>5557),
        		array('host'=>'10.0.2.88',
        			'port'=>5556)
        	)
        ),
		'urlManager'=>array(
		 	//'urlFormat' => 'path',
		 	//'rules' => array(
		 	//	'<controller:\w+>/<id:\d+>'=>'<controller>/view',
			//	'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
			//	'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
		 	//)
		),
		
	   'widgetFactory'=>array(
        	
		
        ),
		 
		'errorHandler'=>array(
		 	'errorAction' => 'public/error',
		),
		 
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				array(
					'class'=>'CWebLogRoute',
				    'levels'=>'trace,info',
        			//'categories'=>'system.db.*',
					//'showInFireBug'=>true
				),
				array(
					'class'=>'CProfileLogRoute',
				),

			),
		),
		 
		'messages'=>array(
		 	'class'=>'CPhpMessageSource',
		 	'basePath'=>DATA_PATH.'messages'.DIR_SEP,
		),
 
		'fileCache'=>array(
		 	'class'	=>	'system.caching.CFileCache'
		),
	
	),
	'params'=>array(
		'images_server'=>array(
			'url'=>'http://show.pipi.cn:8100/images',
			'cdn_open'=>true,
			'cdn_time'=>3600*24*2,
			'cdn_url'=>'http://showimg.pipi.cn',
		  ),
		'open'=>array(
			'qq'=>array(
				'client_id'=>100317993,
				'client_secret'=>'955915a9862023673333b6276e9baa93',
			),
			'renren'=>array(
				'client_id'=>216519,
				'client_secret'=>'df78718c2dc54ee3acc15f447de156ef',
				'api_key'=>'ef79034fcfe14884bfa81f7276cc195a',
			),
			'safe360'=>array(
				'client_id'=>'bc5baad9b99d78f9c3cbf2d5e164f6ea',
				'client_secret'=>'9aa2749bb7763b51b2c854641defa2e6',
			),
			'baidu'=>array(
				'client_id'=>'8StgZAZfGsXwoThSFVvMZ7Gn',
				'client_secret'=>'M8wxlRo8oDzcAxFfi94bbeCzdQIvB8tE',
			),
			'sina'=>array(
				'client_id'=>2457794768,
				'client_secret'=>'c97a2427d86e186fc258fb91c89cfebd',
			)
		),
		'pptv'=>array(
			'main_url'=>'http://pptv.pipi.cn',
			'pptv_url'=>'http://show.pptv.com/pipi?url=',
			'logout_url'=>'http://api.show.pptv.com/authentication.php?gid=pipi&action=logout',
			'login_url'=>'http://passport.pptv.com/Login.aspx?APReturnURL=',
			'auth_url'=>'http://api.show.pptv.com/authentication.php',
			// 'login_js'=>'http://static.vas.pptv.com/vas/show/v_20130523152630/130416/js/external.js',
			'login_js'=>'http://static.g.pptv.com/cms/show/js/external.js',
			'recharge_url'=>'http://show.pptv.com/pay?gid=pipi',
			'app_key'=>'78d00a4c79d777456e26c1b82a4fd977',
		),
		'tuli'=>array(
			'main_url' => 'http://co.pipi.cn',
			'tuli_url' => 'http://www.huaseji.com/mm/play/?source=3&url=',
			'load_js'=>'http://www.huaseji.com/js/proxy.js',
			'identity'=>'22bccac1d905bfe2a9acadf10aa72337',
			'key'=>'6d03ce06edea6b1cc99213058e5eab94',
			'user_url' => 'http://www.huaseji.com/api/user/profile/query/',
		),
		'verification_code' => 'pipishow', //替代ucenter与useraccount数据互通的签名认证
		'exchange' => 'http://useraccount.pipi.cn/Index/uclogin',
		'gameList' => 'http://tgametest.pipi.cn/gp/html/GameListInfo.xml',
		'gameLoginKey' => '0dsjccc234566f6pda6adafnnc87112d',
		'letian_game'=> array(
			'host'=>'http://tgame.pipi.cn',
			'list' => 'http://tgame.pipi.cn/gp/html/GameListInfo.xml',
			'key' => '0dsjccc234566f6pda6adafnnc87112d',
		),
// 		'smtp' => array(
// 			'host' => 'smtp.163.com',
// 			'username' => 'tianwensx@163.com',
// 			'password' => 'sxtwxamzzf',
// 		),
		'smtp' => array(
			'host' => 'smtp.exmail.qq.com',
			'username' => 'no-reply@88488.com',
			'password' => 'no1234',
		),
		'fftkj'=>array(
			'url'=>'http://www.fftkj.com/',
			'username'=>'soushi',
			'password'=>'pipi8823',
			'id'=>9805
		),
		//皮蛋，魅力值，魅力点，贡献值，皮点兑换关系
		'change_relation'=>array(
			'rmb_to_pipiegg'=>100,          //人民币转化皮蛋
			'pipiegg_to_charm'=>0.5,         //皮蛋转化魅力值
			'pipiegg_to_charmpoints'=>0.5,   //皮蛋转化魅力点
			'pipiegg_to_dedication'=>1,   //皮蛋转化贡献值
			'pipiegg_to_eggpoints'=>0.5,    //皮蛋转化皮点
			'charmpoints_to_pipiegg'=>1,    //魅力点转化皮蛋
			'eggpoints_to_pipiegg'=>1       //皮点转化皮蛋
		),
		'api_config'=>array(
			'session_timeout'=>86400,    //移动端用户登录sessionid失效时间
			'valid_time'=>180,           //移动接口
		),
	),
);