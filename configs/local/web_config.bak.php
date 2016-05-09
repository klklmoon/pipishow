<?php
/**
 * 开发者本地环境运行配置
 * 
 * @author Su qian <aoxue.1988.su.qian@163.com> $date$
 * @link show.pipi.cn
 * @copyright Copyright &copy; 2003-2010 show.pipi.cn
 * @license
 */

define('YII_DEBUG', true);
define('DOMAIN', '.yii.dev');

$appConfigFile =  CONFIG_PATH.'test'.DIR_SEP.'web_config.php';
$appConfig = require_once $appConfigFile;
unset($appConfigFile);

$config = array(
	'components'=>array(
		'db_consume'=>array(
			'connectionString' =>'mysql:host=10.10.1.175;dbname=lt_consume_db',
			'username' => 'root',
			'password' => '',
		 	'charset' => 'utf8',
		 	'emulatePrepare' => true,
		 	'tablePrefix' => 'web_',
		 	'class'=>'CDbConnection',
		 	'enableParamLogging' => YII_DEBUG,
		 	'schemaCachingDuration'=>3600*24,
			'enableProfiling' => YII_DEBUG,
		 ),
		'db_purview'=>array(
			'connectionString' =>'mysql:host=10.10.1.175;dbname=lt_purview_db',
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
        'redis_other'=>array(
        	'class'=>'PipiRedisConnection',
        	'hostname'=>'192.168.184.130',
        	'port'=>6379,
        	'database' => 0,
        ),
		'urlManager'=>array(
		 	'urlFormat' => 'path',
		 	//'rules' => array(
		 	//	'<controller:\w+>/<id:\d+>'=>'<controller>/view',
			//	'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
			//	'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
		 	//)
		),
	),
	'params'=>array(
		'images_server'=>array(
			'url'=>'http://image.yii.dev',
		)
	),
);

foreach($config as $key=>$value){
	if(isset($appConfig[$key])){
		if(is_array($value)) $appConfig[$key] = array_merge($appConfig[$key],$value);	
	}else{
		$appConfig[$key] = $value;
	}
}
unset($config);
return $appConfig;