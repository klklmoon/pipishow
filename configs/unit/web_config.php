<?php
/**
 * 单元测试环境运行时配置
 * @author Su qian <aoxue.1988.su.qian@163.com> $date$
 * @link http://www.yiijob.com
 * @copyright Copyright &copy; 2003-2010 topchoice.com.cn
 * @license
 */
define('YII_DEBUG', false);
define('DOMAIN', '.yii.dev');
define('YII_ENABLE_ERROR_HANDLER', false);
return array(
	'runtimePath'=>DATA_PATH.'runtimes'.DIR_SEP,
	'language'=>'zh_cn',

	'import'=>array(
		'application.*',
		'application.base.*',
		'application.services.*',
		'application.services.activity.*',
		'application.services.app.*',
		'application.services.archives.*',
		'application.services.bbs.*',
		'application.services.categories.*',
		'application.services.common.*',
		'application.services.dotey.*',
		'application.services.family.*',
		'application.services.gift.*',
		'application.services.message.*',
		'application.services.open.*',
		'application.services.props.*',
		'application.services.purview.*',
		'application.services.user.*',
		'application.services.weibo.*',
		'application.api.*',
		'application.api.internal.*',
		'application.api.mobile.*',
		'application.api.open.*',
	 ),
	 
	'components'=>array(
		'user'=>array(
			'class'=>'PipiWebUser',
			'allowAutoLogin'=>true,
			'identityCookie' => array('domain'=>DOMAIN,'path'=>'/'),
			'stateKeyPrefix' =>'user_login',
			'loginUrl' => array('user/login'),  
		),
		
		'db_user'=>array(
		 	'connectionString' =>'mysql:host=202.91.246.88;dbname=pipi_user_db',
		    'username' => 'pipiuc',
		 	'password' => 'pipiuc%%*()@db',
		 	'charset' => 'utf8',
		 	'emulatePrepare' => true,
		 	'tablePrefix' => 'web_',
		 	'class'=>'CDbConnection',
		 	'enableParamLogging' => YII_DEBUG,
		 	'schemaCachingDuration'=>3600*24,
			'enableProfiling' => YII_DEBUG,
		),
		'db_user_records'=>array(
		 	'connectionString' =>'mysql:host=202.91.246.88;dbname=pipi_user_records_db',
		    'username' => 'pipiuc',
		 	'password' => 'pipiuc%%*()@db',
		 	'charset' => 'utf8',
		 	'emulatePrepare' => true,
		 	'tablePrefix' => 'web_',
		 	'class'=>'CDbConnection',
		 	'enableParamLogging' => YII_DEBUG,
		 	'schemaCachingDuration'=>3600*24,
			'enableProfiling' => YII_DEBUG,
		),
		'db_consume'=>array(
		 	'connectionString' =>'mysql:host=202.91.246.88;dbname=lt_consume_db',
		    'username' => 'pipiuc',
		 	'password' => 'pipiuc%%*()@db',
		 	'charset' => 'utf8',
		 	'emulatePrepare' => true,
		 	'tablePrefix' => 'web_',
		 	'class'=>'CDbConnection',
		 	'enableParamLogging' => YII_DEBUG,
		 	'schemaCachingDuration'=>3600*24,
			'enableProfiling' => YII_DEBUG,
		),
		'db_consume_records'=>array(
		 	'connectionString' =>'mysql:host=202.91.246.88;dbname=lt_consume_records_db',
		    'username' => 'pipiuc',
		 	'password' => 'pipiuc%%*()@db',
		 	'charset' => 'utf8',
		 	'emulatePrepare' => true,
		 	'tablePrefix' => 'web_',
		 	'class'=>'CDbConnection',
		 	'enableParamLogging' => YII_DEBUG,
		 	'schemaCachingDuration'=>3600*24,
			'enableProfiling' => YII_DEBUG,
		),
		'db_purview'=>array(
			'connectionString' =>'mysql:host=202.91.246.88;dbname=lt_purview_db',
			'username' => 'pipiuc',
			'password' => 'pipiuc%%*()@db',
			'charset' => 'utf8',
			'emulatePrepare' => true,
			'tablePrefix' => 'web_',
			'class'=>'CDbConnection',
			'enableParamLogging' => YII_DEBUG,//供debug使用
			'schemaCachingDuration'=>3600*24,
			'enableProfiling' => YII_DEBUG,//供debug使用
		),
		'db_bbs'=>array(
				'connectionString' =>'mysql:host=202.91.246.88;dbname=lt_bbs_db',
				'username' => 'pipiuc',
				'password' => 'pipiuc%%*()@db',
				'charset' => 'utf8',
				'emulatePrepare' => true,
				'tablePrefix' => 'web_',
				'class'=>'CDbConnection',
				'enableParamLogging' => YII_DEBUG,
				'schemaCachingDuration'=>3600*24,
				'enableProfiling' => YII_DEBUG,
		),
		'db_archives'=>array(
				'connectionString' =>'mysql:host=202.91.246.88;dbname=lt_archives_db',
				'username' => 'pipiuc',
				'password' => 'pipiuc%%*()@db',
				'charset' => 'utf8',
				'emulatePrepare' => true,
				'tablePrefix' => 'web_',
				'class'=>'CDbConnection',
				'enableParamLogging' => YII_DEBUG,
				'schemaCachingDuration'=>3600*24,
				'enableProfiling' => YII_DEBUG,
		),
		'db_common'=>array(
			'connectionString' =>'mysql:host=202.91.246.88;dbname=lt_common_db',
			'username' => 'pipiuc',
			'password' => 'pipiuc%%*()@db',
			'charset' => 'utf8',
			'emulatePrepare' => true,
			'tablePrefix' => 'web_',
			'class'=>'CDbConnection',
			'enableParamLogging' => YII_DEBUG,
			'schemaCachingDuration'=>3600*24,
			'enableProfiling' => YII_DEBUG,
		),
		'redis_userinfo'=>array(
			'class'=>'PipiRedisConnection',
			'hostname'=>'202.91.246.86',
			'port' => '36000',
			'database' => 0,
		),
		'redis_session'=>array(
		 	'class'=>'PipiRedisConnection',
		 	'hostname'=>'202.91.246.86',
		 	'port'=> '36000',
		 	//'database'=>0
		),
		'redis_user'=>array(
		  	 'class'=>'PipiRedisConnection',
		     'hostname'=>'202.91.246.86',
		     'port'=> '36000',
		),
        'redis_user_cache'=>array(
          	  'class'=>'PipiRedisCache',
          	  'connection' => 'redis_user',
        ),
        'redis_event'=>array(
        	'class'=>'PipiRedisConnection',
        	'hostname'=>'202.91.246.86',
		 	'port'=> '36000',
		 	'database' => 0,
        ),
        'redis_cache'=>array(
        		'class'=>'PipiRedisConnection',
        		'hostname'=>'202.91.246.86',
        		'port'=> '36000',
        ),
        'redis_event_list'=>array(
        	'class'=>'PipiRedisList',
        	'connection' => 'redis_event',
        ),
        'redis_other'=>array(
        	'class'=>'PipiRedisConnection',
        	'hostname'=>'202.91.246.86',
        	'port'=>36000,
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
        		array('host'=>'202.91.246.88',
        		'port'=>9555),
        		array('host'=>'202.91.246.85',
        			'port'=>9556)
        	)
        ),
		'urlManager'=>array(
		 	'urlFormat' => 'path',
		 	//'rules' => array(
		 	//	'<controller:\w+>/<id:\d+>'=>'<controller>/view',
			//	'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
			//	'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
		 	//)
		),
		 
// 		'errorHandler'=>array(
// 		 	'errorAction' => 'public/error',
// 		),
		 
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

);