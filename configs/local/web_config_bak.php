<?php
/**
 * 开发者本地环境运行配置
 * 
 * @author Su qian <aoxue.1988.su.qian@163.com> $date$
 * @link show.pipi.cn
 * @copyright Copyright &copy; 2003-2010 show.pipi.cn
 * @license
 */

define('YII_DEBUG',true);
define('DOMAIN','.pipi.com');
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
		 	'connectionString' =>'mysql:host=202.91.246.80;dbname=pipi_user_db',
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
		 	'connectionString' =>'mysql:host=202.91.246.80;dbname=pipi_user_records_db',
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
		 	'connectionString' =>'mysql:host=202.91.246.80;dbname=lt_consume_db',
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
		 	'connectionString' =>'mysql:host=202.91.246.80;dbname=lt_archives_db',
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
		 	'connectionString' =>'mysql:host=202.91.246.80;dbname=lt_consume_records_db',
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
		'db_family'=>array(
			'connectionString' =>'mysql:host=192.168.184.129;dbname=family',
			'username' => 'root',
			'password' => '',
			'charset' => 'utf8',
			'emulatePrepare' => true,
			'tablePrefix' => 'web_',
			'class'=>'CDbConnection',
			'enableParamLogging' => YII_DEBUG,//供debug使用
			'schemaCachingDuration'=>3600*24,
			'enableProfiling' => YII_DEBUG,//供debug使用
		),
		'db_purview'=>array(
			'connectionString' =>'mysql:host=202.91.246.80;dbname=lt_purview_db',
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
		'db_weibo'=>array(
			'connectionString' =>'mysql:host=202.91.246.80;dbname=lt_weibo_db',
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
				'connectionString' =>'mysql:host=202.91.246.80;dbname=lt_bbs_db',
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
		
		'redis_session'=>array(
		 		'class'=>'PipiRedisConnection',
		 		'hostname'=>'202.91.246.88',
		 		'port'=> '37777',
		 		'database'=>0
		),
		'redis_user'=>array(
		  	 'class'=>'PipiRedisConnection',
		     'hostname'=>'202.91.246.88',
		     'port'=> '31111',
		),
        'redis_user_cache'=>array(
          	  'class'=>'PipiRedisCache',
          	  'connection' => 'redis_user',
        ),
        'redis_userinfo'=>array(
        	'class'=>'PipiRedisConnection',
        	'hostname'=>'202.91.246.88',
		 	'port'=> '38888',
        	'database' => 0,
        ),
        'redis_event'=>array(
        	'class'=>'PipiRedisConnection',
        	'hostname'=>'202.91.246.88',
		 	'port'=> '39999',
        	'database' => 0,
        ),

        'redis_cache'=>array(
        	'class'=>'PipiRedisConnection',
        	'hostname'=>'202.91.246.88',
		 	'port'=> '32222',
		 	'database' => 0,
        ),

        'redis_token'=>array(
        	'class'=>'PipiRedisConnection',
        	'hostname'=>'202.91.246.88',
        	'port'=> '36660',
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
        		array('host'=>'202.91.246.88',
        		'port'=>5555),
        		array('host'=>'202.91.246.85',
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
	

		'db_bbs_local'=>array(
				'connectionString' =>'mysql:host=10.10.1.176;dbname=lt_bbs_db',
				'username' => 'root',
				'password' => '000000',
				'charset' => 'utf8',
				'emulatePrepare' => true,
				'tablePrefix' => 'web_',
				'class'=>'CDbConnection',
				'enableParamLogging' => YII_DEBUG,
				'schemaCachingDuration'=>3600*24,
				'enableProfiling' => YII_DEBUG,
		),
	),
	'params'=>array(
		'images_server'=>array(
			'url'=>'http://showimg.pipi.com',
		)
	),
);