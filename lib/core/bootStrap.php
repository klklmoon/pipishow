<?php
/**
 * 所有应用基础启动文件
 * 
 * @author Su qian <aoxue.1988.su.qian@163.com> 2011-10-2
 * @link http://show.pipi.cn
 * @copyright Copyright &copy; 2003-2013 show.pipi.cn
 * @license
 */

if(is_file('/etc/HOSTNAME')){
	//用来跟踪线上show.pipi.cn访问的是哪台机器
	header('letian: '.file_get_contents('/etc/HOSTNAME'));
}
date_default_timezone_set('PRC');
/**
 * @var string 跨平台目录分隔符
 */
define('DIR_SEP','/');

/**
 * @var string 网站根目录定义
 */
define('ROOT_PATH',dirname(dirname(dirname(__FILE__))).DIR_SEP);

/**
 * @var string 数据目录定义
 */
define('DATA_PATH',ROOT_PATH.'data'.DIR_SEP);
/**
 * @var string 配置文件目录定义
 */
define('CONFIG_PATH',ROOT_PATH.'configs'.DIR_SEP);

/**
 * @var string 静态资源目录定义
 */
define('STATIC_PATH',ROOT_PATH.'statics'.DIR_SEP);
/**
 * @var string 图片目录定义
 */
define('IMAGES_PATH',ROOT_PATH.'images'.DIR_SEP);

/**
 * @var string Yii框架目录
 */
define('YII_PATH',ROOT_PATH.'framework'.DIR_SEP);
/**
 * @var string 定义库目录
 */
define('LIB_PATH',ROOT_PATH.'lib'.DIR_SEP);

/**
 * @var string 应用的路径
 */
define('APPLICATION_PATH',ROOT_PATH.'applications'.DIR_SEP.SYSTEM_NAME.DIR_SEP);
/**
 * @var string 商业逻辑层
 */
define('BLL_PATH',ROOT_PATH.'bll'.DIR_SEP);

/**
 * @var string 软件版本号，每发布一次，主版本号、次版本号、内部版本号（build）和修订号
 */
define('SOFT_VERSION','v1.0.0.1');

//载入需要的环境标识
if(is_file(CONFIG_PATH.DIR_SEP.'environment.php')){
	require_once CONFIG_PATH.DIR_SEP.'environment.php';
}

if(!defined('DEV_ENVIRONMENT'))
	/**
	 * @var string 网站运行环境
	 */
	define('DEV_ENVIRONMENT','release');

if(!defined('ACCESS_ENTRANCE'))
	/**
	 * @var string 访问入口
	 */
	define('ACCESS_ENTRANCE','web');

$config = require_once CONFIG_PATH.('command' == ACCESS_ENTRANCE ? 'console' : 'main').'.php';
require_once YII_PATH.'yii.php';

Yii::setPathOfAlias('lib', LIB_PATH);
Yii::setPathOfAlias('data', DATA_PATH);
Yii::setPathOfAlias('root', ROOT_PATH);
Yii::setPathOfAlias('bll', BLL_PATH);
if('web' === ACCESS_ENTRANCE){
	try{
		if(isset($_SERVER['HTTP_HOST'])) $root_url = $_SERVER['HTTP_HOST'];
		else $root_url = 'http://show.pipi.cn';
		define('ROOT_URL','http://'.$root_url.DIR_SEP);
		Yii::createWebApplication($config)->run();
	}catch(Exception $e){
		$filename = DATA_PATH.'runtimes/web_error.log';
		error_log(date('Y-m-d H:i:s').' '.$e."\n\r",3,$filename);
		throw $e;
	}
}elseif('phpunit' == ACCESS_ENTRANCE){
	
	Yii::createWebApplication($config);
}elseif('other' == ACCESS_ENTRANCE){
	
	Yii::createWebApplication($config);
	
}elseif('command' == ACCESS_ENTRANCE){
	
	$app=Yii::createConsoleApplication($config);
	$app->commandRunner->addCommands(YII_PATH.'/cli/commands');
	$env=@getenv('YII_CONSOLE_COMMANDS');
	if($env){
		$app->commandRunner->addCommands($env);
	}
	$app->run();
}