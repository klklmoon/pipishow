<?php
/**
 * 七夕节活动服务层
 * @author Guo ShaoBo <guoshaobo@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z guoshaobo $ 
 * @package
 */
class QixiService extends PipiService{
	//小啤酒礼物ID
	CONST GIFT_ID = 177;
	//活动时间
	CONST START_TIME = "2013-08-09 09:00:00";
	CONST END_TIME = "2013-08-13 23:30:00";
	//排行榜个数限制
	CONST TOP_LIMIT = 20;
	//参数主播列表
	static $dotey_list = array(
		10954551,11701272,11038648,10761939,11855674,12698048,12738546,12684650,12632672,12476487,12682334,
		12609990,12287225,12331696,11762600,10675778,10899138,11306842,10864558,12642318,12774985,12784656,
		11826477,12804936,11328472,12756733,11442604,12810180,12721982,12809048,10909818,10942936,10965959,
		11784160,12193758,10895528,12777733,11014405,12831229,11064611,12291126,12795019,12314952,12709477,
		12040950,11552389,11813052,11813052,12305423,11538120,11074155,10312053,11039263
	);
	
	public function getLiving($isLogin = false){
		$archives = new ArchivesService();
		$living = $archives->getLivingArchives(Yii::app()->user->id,true,$isLogin);
		foreach($living['living'] as $k=>$_live){
			if(!in_array($_live['uid'], self::$dotey_list)) unset($living['living'][$k]);
		}
		$willLive = $archives->getWillLiveArchives(Yii::app()->user->id,true,$isLogin);
		foreach($willLive['wait'] as $k=>$_live){
			if(!in_array($_live['uid'], self::$dotey_list)) unset($willLive['wait'][$k]);
		}
		$living['wait'] = $willLive['wait'];
		return $living;
	}
	
	public function getTop(){
		$start_time = strtotime(self::START_TIME);
		$end_time = strtotime(self::END_TIME);
		if(time() < $start_time && Yii::app()->request->getParam('start')){
			$start_time = strtotime(Yii::app()->request->getParam('start')." 00:00:00");
		}
		$giftServer = new GiftService();
		$dotey_rank = $giftServer->getGiftSumToDoteys(self::GIFT_ID, self::$dotey_list, $start_time, $end_time);
		$user_rank = $giftServer->getGiftSumFromUsers(self::GIFT_ID, self::$dotey_list, $start_time, $end_time);
		usort($dotey_rank, array($this, "sortByDesc"));
		usort($user_rank, array($this, "sortByDesc"));
		$dotey_uids = $uids = array();
		foreach($dotey_rank as $r){
			$dotey_uids[] = $r['uid'];
		}
		foreach($user_rank as $r){
			$uids[] = $r['uid'];
		}
		$userInfo = new UserJsonInfoService();
		$users = $userInfo->getUserInfos(array_merge($dotey_uids, $uids), false);
		foreach($dotey_rank as &$r){
			$r['nickname'] = $users[$r['uid']]['nk'];
			$r['rank'] = $users[$r['uid']]['dk'];
		}
		foreach($user_rank as &$r){
			$r['nickname'] = $users[$r['uid']]['nk'];
			$r['rank'] = $users[$r['uid']]['rk'];
		}
		return array('dotey_rank' => array_slice($dotey_rank, 0, self::TOP_LIMIT), 'user_rank' => array_slice($user_rank, 0, self::TOP_LIMIT));
	}
	
	/**
	 * group by后的数据不命中索引不通过mysql排序，通过程序来排序
	 * @author hexin
	 * @param unknown_type $a
	 * @param unknown_type $b
	 * @return number
	 */
	private function sortByDesc($a, $b){
		if($a['num'] > $b['num']) return -1;
		elseif($a['num'] < $b['num']) return 1;
		else return 0;
	}
}