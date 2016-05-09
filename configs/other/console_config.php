<?php
/**
 * 开发者本地环境运行配置
 * 
 * @author Su qian <aoxue.1988.su.qian@163.com> $date$
 * @link show.pipi.cn
 * @copyright Copyright &copy; 2003-2010 show.pipi.cn
 * @license
 */
define('YII_DEBUG',false);

return array(
	'components'=>array(
 
		'db_read_pipishow'=>array(
			'connectionString' =>'mysql:host=202.91.246.74;dbname=pipishow',
		    'username' => 'spipiuc',
		 	'password' => 'spipi%74%*(@)db',
		 	'charset' => 'utf8',
		 	'emulatePrepare' => true,
		 	'tablePrefix' => 'web_',
		 	'class'=>'CDbConnection',
		 	'schemaCachingDuration'=>3600*24,
		),
		'db_read_ucenter'=>array(
			'connectionString' =>'mysql:host=202.91.246.76;dbname=ucenter',
		    'username' => 'spipiuc',
		 	'password' => 'spipi%76%*(@)db',
		 	'charset' => 'utf8',
		 	'emulatePrepare' => true,
		 	'tablePrefix' => 'web_',
		 	'class'=>'CDbConnection',
		 	'schemaCachingDuration'=>3600*24,
		),
		'db_user'=>array(
		 	'connectionString' =>'mysql:host=202.91.246.73;dbname=pipi_user_db',
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
		 	'connectionString' =>'mysql:host=202.91.246.73;dbname=pipi_user_records_db',
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
		 	'connectionString' =>'mysql:host=202.91.246.74;dbname=pipi_user_db',
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
		 	'connectionString' =>'mysql:host=202.91.246.74;dbname=pipi_user_records_db',
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
		 	'connectionString' =>'mysql:host=202.91.246.75;dbname=lt_consume_db',
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
		 	'connectionString' =>'mysql:host=202.91.246.75;dbname=lt_consume_records_db',
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
		 	'connectionString' =>'mysql:host=202.91.246.76;dbname=lt_consume_db',
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
		 	'connectionString' =>'mysql:host=202.91.246.76;dbname=lt_consume_records_db',
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
		 	'connectionString' =>'mysql:host=202.91.246.79;dbname=lt_archives_db',
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
			'connectionString' =>'mysql:host=202.91.246.79;dbname=lt_purview_db',
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
			'connectionString' =>'mysql:host=202.91.246.79;dbname=lt_purview_db',
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
		
		'db_weibo'=>array(
			'connectionString' =>'mysql:host=202.91.246.79;dbname=lt_weibo_db',
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
		 	'connectionString' =>'mysql:host=202.91.246.78;dbname=lt_archives_db',
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
			'connectionString' =>'mysql:host=202.91.246.78;dbname=lt_purview_db',
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
			'connectionString' =>'mysql:host=202.91.246.78;dbname=lt_purview_db',
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
		
		'db_weibo_slave'=>array(
			'connectionString' =>'mysql:host=202.91.246.78;dbname=lt_weibo_db',
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
			'connectionString' =>'mysql:host=202.91.246.77;dbname=lt_partner_db',
			'username' => 'pipiuc',
		 	'password' => 'pipi%77%*(@)db',
			'charset' => 'utf8',
			'emulatePrepare' => true,
			'tablePrefix' => 'web_',
			'class'=>'CDbConnection',
			'enableParamLogging' => YII_DEBUG,//供debug使用
			'schemaCachingDuration'=>3600*24,
			'enableProfiling' => YII_DEBUG,//供debug使用
		),
		
		'db_bbs'=>array(
				'connectionString' =>'mysql:host=202.91.246.77;dbname=lt_bbs_db',
				'username' => 'pipiuc',
				'password' => 'pipi%77%*(@)db',
				'charset' => 'utf8',
				'emulatePrepare' => true,
				'tablePrefix' => 'web_',
				'class'=>'CDbConnection',
				'enableParamLogging' => YII_DEBUG,
				'schemaCachingDuration'=>3600*24,
				'enableProfiling' => YII_DEBUG,
		),
		
		'db_common'=>array(
				'connectionString' =>'mysql:host=202.91.246.77;dbname=lt_common_db',
				'username' => 'pipiuc',
				'password' => 'pipi%77%*(@)db',
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
		 		'hostname'=>'202.91.246.94',
		 		'port'=> '36004',
		 		'database'=>0
		),
		'redis_user'=>array(
		  	 'class'=>'PipiRedisConnection',
		     'hostname'=>'202.91.246.93',
		     'port'=> '36001',
			 'database' => 0,
		),
        'redis_user_cache'=>array(
          	  'class'=>'PipiRedisCache',
          	  'connection' => 'redis_user',
        ),
        'redis_userinfo'=>array(
        	'class'=>'PipiRedisConnection',
        	'hostname'=>'202.91.246.93',
		 	'port'=> '36000',
        	'database' => 0,
        ),
        'redis_event'=>array(
        	'class'=>'PipiRedisConnection',
        	'hostname'=>'202.91.246.93',
		 	'port'=> '36002',
        	'database' => 0,
        ),

        'redis_cache'=>array(
        	'class'=>'PipiRedisConnection',
        	'hostname'=>'202.91.246.94',
		 	'port'=> '36003',
        	'database' => 0,
        ),

        'redis_token'=>array(
        	'class'=>'PipiRedisConnection',
        	'hostname'=>'202.91.246.94',
        	'port'=> '36005',
        	'database' => 0,
        ),

        'zmq'=>array(
        	'class'=>'PipiZmq',
        	'hosts'=>array(
        		array('host'=>'202.91.246.88',
        		'port'=>5557),
        		array('host'=>'202.91.246.88',
        			'port'=>5556)
        	)
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

);