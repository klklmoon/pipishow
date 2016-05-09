<?php
/**
 * 通用配置，所以应用的基础配置
 * 
 * @author Su qian <aoxue.1988.su.qian@163.com> $date$
 * @link http://show.pipi.com
 * @copyright Copyright &copy; 2003-2012 http://show.pipi.com
 * @license
 */
$appConfigFile =  CONFIG_PATH.DEV_ENVIRONMENT.DIR_SEP.'web_config.php';

if(is_file($appConfigFile))
	$appConfig = require_once $appConfigFile;
else 
	$appConfig = array();
unset($appConfigFile);

$commonConfig = array(

	'basePath' => APPLICATION_PATH,
	'name'=> '',
	'defaultController' => 'index',
	'runtimePath'=>DATA_PATH.'runtimes'.DIR_SEP,
	'language'=>'zh_cn',

	'preload'=>array(
		'log',
	),
	//只放相关服务层相model/rmodel/service/form这几项
	'import'=>array(
		'lib.core.*',
		'lib.core.base.*',
		'lib.core.redis.*',
		'lib.core.oauth.*',
		'lib.components.*',
		'bll.model.user.*',
		'bll.model.message.*',
		'bll.model.props.*',
		'bll.model.purview.*',
		'bll.model.archives.*',
		'bll.model.consume.*',
		'bll.model.gift.*',
		'bll.model.dotey.*',
		'bll.model.app.*',
		'bll.model.weibo.*',
		'bll.model.bbs.*',
		'bll.model.song.*',
		'bll.model.common.*',
		'bll.model.partner.*',
		'bll.model.activity.*',
		'bll.model.family.*',
		'bll.model.number.*',
 		'bll.model.agents.*',
		'bll.model.tags.*',
		'bll.rmodel.user.*',
		'bll.rmodel.event.*',
		'bll.rmodel.other.*',
		'bll.rmodel.token.*',
		'bll.rmodel.session.*',
		'bll.services.user.*',
		'bll.services.message.*',
		'bll.services.props.*',
		'bll.services.purview.*',
		'bll.services.archives.*',
		'bll.services.common.*',
		'bll.services.gift.*',
		'bll.services.dotey.*',
		'bll.services.app.*',
		'bll.services.weibo.*',
		'bll.services.bbs.*',
		'bll.services.song.*',
		'bll.services.partner.*',
		'bll.services.activities.*',
		'bll.services.family.*',
 		'bll.services.agents.*',
		'application.form.*',
	),
	
	'modules'=>array(
		'gii'=>array(
			 'class'=>'system.gii.GiiModule',
			 'password'=>'suqian0512h',
		 ),
		 'comment'=>array(
			 'class'=>'application.modules.comment.CommentModule',
		 ),
	),
	
);

foreach($commonConfig as $key=>$value){
	if(isset($appConfig[$key])){
		if(is_array($value))
			$appConfig[$key] = array_merge($appConfig[$key],$value);	
	}else
		$appConfig[$key] = $value;
	
}
unset($commonConfig);

require CONFIG_PATH.'config.php';
return $appConfig;