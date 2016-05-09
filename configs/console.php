<?php
/**
 * 控制台命令，所以控制台应用的基础配置
 * 
 * @author Su qian <aoxue.1988.su.qian@163.com> $date$
 * @link http://show.pipi.com
 * @copyright Copyright &copy; 2003-2012 http://show.pipi.com
 * @license
 */


$appConfigFile =  CONFIG_PATH.DEV_ENVIRONMENT.DIR_SEP.'console_config.php';

if(is_file($appConfigFile))
	$appConfig = require_once $appConfigFile;
else 
	$appConfig = array();
unset($appConfigFile);

$commonConfig = array(

	'basePath' => APPLICATION_PATH,
	'name'=> 'pipi console application',
	'runtimePath'=>DATA_PATH.'runtimes'.DIR_SEP,
	'language'=>'zh_cn',

	'preload'=>array(
		'log',
	),
	'import'=>array(
		'lib.core.*',
		'lib.core.base.*',
		'lib.core.redis.*',
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
		'bll.rmodel.user.*',
		'bll.rmodel.event.*',
		'bll.rmodel.other.*',
		'bll.rmodel.token.*',
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
	),
	
	'commandMap'=>array(
		'test'=>array('class'=>'application.test.TestCommand'),
		'userTransfer'=>array('class'=>'application.transfer.UserTransferCommand'),
		'propsTransfer'=>array('class'=>'application.transfer.PropsTransferCommand'),
		'songTransfer'=>array('class'=>'application.transfer.doteySongCommand'),
		'giftTransfer'=>array('class'=>'application.transfer.giftCommand'),
		'archivesTransfer'=>array('class'=>'application.transfer.ArchivesCommand'),
		'consumeTransfer'=>array('class'=>'application.transfer.consumeRecordCommand'),
		'otherTransfer'=>array('class'=>'application.transfer.OtherTransferCommand'),
		'statTransfer'=>array('class'=>'application.transfer.StatisticsCommand'),
		'vipTimingUpdate'=>array('class'=>'application.transfer.VipTimingUpdateCommand'),

		'adminSessStatCrontab'=>array('class'=>'application.crontab.admin.ArchivesCommand'),
		'adminOperateCrontab'=>array('class'=>'application.crontab.admin.OperateCommand'),
		'partnerCrontab' => array('class' => 'application.crontab.partner.PartnerCronCommand'),
		'doteyPeriodCount'=>array('class' => 'application.crontab.dotey.DoteyPeriodCountCommand'),
		'doteyTop'=>array('class' => 'application.crontab.dotey.DoteyTopCommand'),
		'userTop'=>array('class' => 'application.crontab.user.UserTopCommand'),
		'user'=>array('class' => 'application.crontab.user.UserCommand'),
		'userMessage'=>array('class' => 'application.crontab.user.UserMessageCommand'),
		'DoteyCategoryIndex'=>array('class' => 'application.crontab.dotey.DoteyCategoryIndexCommand'),
		'DoteyIndexRight'=>array('class' => 'application.crontab.dotey.DoteyIndexRightCommand'),
		'PropsSend'=>array('class' => 'application.crontab.props.PropsSendCommand'),
		'Dice'=>array('class'=>'application.crontab.props.DiceCommand'),
		'DatabaseInit'=> array('class'=>'application.refactorOnline.DatabaseInitCommand'),
		'RedisInit'=> array('class'=>'application.refactorOnline.RedisInitCommand'),
		'AllDoteyCharmCrontab'=>array('class' => 'application.crontab.dotey.AllDoteyCharmRankCommand'),
		'Archive' => array('class' => 'application.crontab.archives.ArchiveCommand'),
		'Income' => array('class' => 'application.crontab.stat.IncomeAndExpensesCommand'),
		'Family' => array('class' => 'application.crontab.family.FamilyCommand'),
	
		'RedisStore' => array('class' => 'application.restore.RedisStoreCommand'),
		'createPlayer' => array('class' => 'application.crontab.supply.CreatePlayerCommand'),
		'userStat' => array('class' => 'application.crontab.stat.UserStatCommand'),
		'giftStar' => array('class' => 'application.crontab.activities.GiftStarCommand'),
		'happyBirthday' => array('class' => 'application.crontab.activities.HappyBirthdayCommand'),
		'happyTuesday' => array('class' => 'application.crontab.activities.HappyTuesdayCommand'),
		'luckStar' => array('class' => 'application.crontab.activities.LuckStarCommand'),
		'createTuliXml' => array('class' => 'application.crontab.supply.TuliCreateXmlCommand'),
		'agents' => array('class' => 'application.crontab.agents.AgentsCommand'),
		'battle' => array('class' => 'application.crontab.activities.BattleCommand'),
	)

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