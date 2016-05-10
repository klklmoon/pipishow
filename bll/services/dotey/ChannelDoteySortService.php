<?php
define('CHANNEL_DOTEY_SORT_LIVETIME_SUPER',1);	//按直播总时长排序
define('CHANNEL_DOTEY_SORT_RANK',2);			//按主播等级排序
define('CHANNEL_DOTEY_SORT_STARTTIME',3);		//按最新开播时间排序
define('CHANNEL_DOTEY_SORT_SONGS_YESTERDAY',4);	//按昨日点唱榜排序
define('CHANNEL_DOTEY_SORT_CHARMS_YESTERDAY',5);//按昨日魅力榜排序
define('CHANNEL_DOTEY_SORT_CHARMS_SUPER',6);	//按主播超级魅力值排序
define('CHANNEL_DOTEY_SORT_SORT',7);			//用户自定义排序
define('CHANNEL_DOTEY_SORT_USER_TOTAL',8);		//按直播在线人数排序
define('CHANNEL_DOTEY_SORT_WAIT_STARTTIME',9);	//待直播按直播预告的开始时间排序

/**
 * 主播频道排序
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: ChannelDoteySortService.php 17803 2014-01-23 08:22:25Z hexin $ 
 * @package
 */
class ChannelDoteySortService extends PipiService {
	/**
	 * 
	 * @var string 正在排序的字段
	 */
	private $sortingField = '';
	
	/**
	 * 暂存数据
	 * @var array
	 */
	private $localCache = array();
	/**
	 * 取得主播时段统计
	 * 
	 * @param array $uids
	 * @param string $field
	 * @return array
	 */
	public function getDoteyPeriodCountByUids(array $uids,$field='*'){
		if(empty($uids)){
			return $this->setError(Yii::t('common','Parameter is empty'),array());
		}
		
		$doteyPeriodModel = new DoteyPeriodCountModel();
		$periods = $doteyPeriodModel->getDoteyPeriodCountByUids($uids,$field);
		$periods = $this->arToArray($periods);
		return $this->buildDataByIndex($periods,'uid');
	}
	
   /**
	 * 生成直播档期的时间
	 * 
	 * @param array $liveArchives
	 * @param int $return 0表示返回所有 1表示返回正在直播的 2表示返回待直播的
	 * @author suqian
	 * @return array
	 */
	public function buildLiveArchives(array &$archives,$loginUid = 0,$return = 0,$ifHasDotey = false,$ifHasAttention = false){
		if(empty($archives)){
			return array();
		}
		$timeStamp  = time();
		$uids = array_keys($this->buildDataByIndex($archives,'uid'));
		$lives = $doteys = $attentions = array();
		if($ifHasDotey){
			$doteyService = new DoteyService();
			if(is_array($ifHasDotey)){
				$doteys = $ifHasDotey;
			}else{
				$doteys = $doteyService->getDoteyInfoByUids($uids);
			}
		}
		if($ifHasAttention && $loginUid){
			$weiboService = new WeiboService();
			$attentions = $weiboService->getDoteyAttentionsByUid($loginUid);
			$attentions = $this->buildDataByIndex($attentions,'uid');
		}
		$lives = array();
		foreach($archives as $key=>$archive){
			$lives[$key]['live_time'] = $archive['live_record']['live_time'];
			$lives[$key]['start_time'] = $archive['live_record']['start_time'];
			$lives[$key]['status'] = $archive['live_record']['status'];
			$lives[$key]['sub_title'] = $archive['live_record']['sub_title'];
			$lives[$key]['title'] = $archive['title'];
			$lives[$key]['uid'] = $archive['uid'];
			$lives[$key]['archives_id'] = $archive['archives_id'];
			if($ifHasDotey && isset($doteys[$archive['uid']])){
				$dotey = $doteys[$archive['uid']];
				$updateDesc = isset($dotey['update_desc']) && is_array($dotey['update_desc']) ? $dotey['update_desc'] : array(); 
				$lives[$key]['display_small'] = $doteyService->getDoteyUpload($archive['uid'],'small','display',$updateDesc);
				$lives[$key]['display_big'] = $doteyService->getDoteyUpload($archive['uid'],'big','display',$updateDesc);
				//是否唱区主播
				$lives[$key]['sing_area'] = $this->hasMoreBit((int)$dotey['sub_channel'],1);
				if(isset($archive['today_recommand']))
				{
					$lives[$key]['today_recommand']=$archive['today_recommand'];
				}
			}
			
			if($ifHasAttention){
				if(isset($attentions[$archive['uid']])){
					$lives[$key]['is_attention'] = 1;
				}else{
					$lives[$key]['is_attention'] = 0;
				}
			}
			
			$lives[$key]['live_desc'] = PipiDate::getLastDate((int)$archive['live_record']['live_time'],$timeStamp,'Y-m-d H:i');
			$lives[$key]['start_desc'] = PipiDate::getFurtureDate((int)$archive['live_record']['start_time'],$timeStamp,'Y-m-d H:i');
		}
	
		$archives = $lives;
		return $lives;
	}
	
	
	/**
	 * 排序值播的档期 包括正在直播的或者待直播的
	 * 
	 * @param array $liveArchives 直播记录
	 * @param int $sortMethod 主播排序方法
	 * @return int $return 0表示返回所有 1表示返回正在直播的 2表示返回待直播的
	 * @return boolean $isSort 是否排序 true表示排序  false表示不排序
	 * @author suqian
	 * @return array
	 */
	public function sortLiveArchives(array $liveArchives,$sortMethod = CHANNEL_DOTEY_SORT_STARTTIME,$return = 0,$isSort = true){
		if(empty($liveArchives)){
			return array();
		}
		$newData = array('living' => array(), 'wait' => array());
		if(isset($liveArchives['living']) && isset($liveArchives['wait'])){
			$newData['living'] = $liveArchives['living'];
			$newData['wait'] = $liveArchives['wait'];
		}else{
			foreach($liveArchives as $key=>$archive){
				if($archive['status'] == 1){
					$newData['living'][$key] = $archive;
				}elseif($archive['status'] == 0){
					$newData['wait'][$key] = $archive;
				}
			}
		}
		
		if($isSort === false){
			return $newData;
		}
		
		$this->localCache = array();
		$newData['living'] = $this->sortArchives($newData['living'], $sortMethod);
		if($sortMethod == CHANNEL_DOTEY_SORT_STARTTIME) $sortMethod = CHANNEL_DOTEY_SORT_WAIT_STARTTIME;
		$newData['wait'] = $this->sortArchives($newData['wait'], $sortMethod);
		return $newData;
	}
	
	/**
	 * 排序档期，只负责排序，不改变数据的结构
	 *
	 * @param array $archives 直播记录
	 * @param int $sortMethod 主播排序方法
	 * @author hexin
	 * @return array
	 */
	private function sortArchives(array &$archives,$sortMethod = CHANNEL_DOTEY_SORT_STARTTIME){
		if(empty($archives)) return array();
		$periodDefines = array(
			CHANNEL_DOTEY_SORT_CHARMS_YESTERDAY,
			CHANNEL_DOTEY_SORT_LIVETIME_SUPER,
			CHANNEL_DOTEY_SORT_SONGS_YESTERDAY,
		);
		//需要取得主播区段统计数据的排序
		if(in_array($sortMethod,$periodDefines)){
			$this->sortingField = $this->sortMapDbPeiordFiled($sortMethod);
			if(isset($this->localCache['doteyPeriods'])) $doteyPeriods = $this->localCache['doteyPeriods'];
			else{
				$uids = array_keys($this->buildDataByIndex($archives,'uid'));
				$doteyPeriods = $this->getDoteyPeriodCountByUids($uids,$this->sortingField);
				$this->localCache['doteyPeriods'] = $doteyPeriods;
			}
			foreach($archives as $key=>$archive){
				$uid = $archive['uid'];
				if(isset($doteyPeriods[$uid])){
					$archive[$this->sortingField] = $doteyPeriods[$uid][$this->sortingField];
				}else{
					$archive[$this->sortingField] = 0;
				}
			}
		//需要取得主播等级数据的排序
		}elseif($sortMethod == CHANNEL_DOTEY_SORT_RANK){
			$this->sortingField = $this->sortMapDbPeiordFiled($sortMethod);
			if(isset($this->localCache['consumes'])) $consumes = $this->localCache['consumes'];
			else{
				$uids = array_keys($this->buildDataByIndex($archives,'uid'));
				$consumeService = new ConsumeService();
				$consumes = $consumeService->getConsumesByUids($uids);
				$this->localCache['consumes'] = $consumes;
			}
			foreach($archives as $key=>$archive){
				$uid = $archive['uid'];
				if(isset($consumes[$uid])){
					$archive[$this->sortingField] = $consumes[$uid][$this->sortingField];
				}else{
					$archive[$this->sortingField] = 0;
				}
			}
		}
		
		if($sortMethod == CHANNEL_DOTEY_SORT_STARTTIME)
			usort($archives,array($this,'sortLivingArchivesByTimes'));
		elseif($sortMethod == CHANNEL_DOTEY_SORT_WAIT_STARTTIME)
			usort($archives,array($this,'sortWaitArchivesByTimes'));
		elseif(in_array($sortMethod,$periodDefines))
			usort($archives,array($this,'sortLiveArchivesByPeriod'));
		elseif($sortMethod == CHANNEL_DOTEY_SORT_RANK)
			usort($archives,array($this,'sortLiveArchivesByPeriod'));
		elseif($sortMethod == CHANNEL_DOTEY_SORT_SORT)
			usort($archives,array($this,'sortLiveArchivesByPeriod'));
		elseif($sortMethod == CHANNEL_DOTEY_SORT_USER_TOTAL)
			usort($archives,array($this,'sortUserTotal'));
		
		return $archives;
	}
	
	/**
	 * 按主播区段统计排序直播
	 * 
	 * @param $prev
	 * @param $next
	 * @author suqian
	 * @return int
	 */
	public function sortLiveArchivesByPeriod(array $prev,array $next){
		$sortField = $this->sortingField ? $this->sortingField : 'start_time';
		if($prev[$sortField] == $next[$sortField]){
			return 0;
		}
		return $prev[$sortField] > $next[$sortField] ? -1 : 1;
	}
	
	/**
	 * 按真实直播时间排序 正在直播的
	 * 
	 * @param $prev
	 * @param $next
	 * @author suqian
	 * @return int
	 */
	public function sortLivingArchivesByTimes(array $prev,array $next){
		if($prev['live_time'] == $next['live_time']){
			return 0;
		}
		return $prev['live_time'] > $next['live_time'] ? -1 : 1;
	}
	
	/**
	 * 按今日推荐排序 档期信息
	 *
	 * @param $prev
	 * @param $next
	 * @author zzf
	 * @return int
	 */
	public function sortArchivesByTodayRecommand(array $prev,array $next){
		if($prev['today_recommand'] == $next['today_recommand']){
			return 0;
		}
		return $prev['today_recommand'] ==true ? -1 : 1;
	}
	
	/**
	 * 按待开播时间排序 待直播的
	 * 
	 * @param $prev
	 * @param $next
	 * @author suqian
	 * @return int
	 */
	public function sortWaitArchivesByTimes(array $prev,array $next){
		if($prev['start_time'] == $next['start_time']){
			return 0;
		}
		return $prev['start_time'] < $next['start_time'] ? -1 : 1;
	}
	
	/**
	 * 按在线人数排序
	 *
	 * @param $prev
	 * @param $next
	 * @author suqian
	 * @return int
	 */
	public function sortUserTotal(array $prev,array $next){
		if(!isset($prev['user_total']) || !isset($next['user_total']))
			return 0;
		if($prev['user_total'] == $next['user_total']){
			return 0;
		}
		return $prev['user_total'] > $next['user_total'] ? -1 : 1;
	}
	
	/**
	 * 主播排序类型
	 * 
	 * @param string $sort
	 * @return mixed
	 */
	public function getDoteySortList($sort = NULL){
		$list = array(
			CHANNEL_DOTEY_SORT_LIVETIME_SUPER => '按直播总时长排序',
			CHANNEL_DOTEY_SORT_RANK	=>'按主播等级排序',
			CHANNEL_DOTEY_SORT_STARTTIME => '按最新开播时间排序',
			CHANNEL_DOTEY_SORT_SONGS_YESTERDAY => '按昨日点唱榜排序',
			CHANNEL_DOTEY_SORT_CHARMS_YESTERDAY => '按昨日魅力榜排序',
			CHANNEL_DOTEY_SORT_CHARMS_SUPER=>'按主播超级魅力值排序',
			CHANNEL_DOTEY_SORT_SORT=>'用户自定义排序',
		);
		return isset($list[$sort]) ? $list[$sort] : $list;
	}
	
	/**
	 * 排序类型与数据库对应的字段
	 * 
	 * @param $sort
	 * @return string
	 */
	public function sortMapDbPeiordFiled($sort){
		$map = array(
			CHANNEL_DOTEY_SORT_CHARMS_YESTERDAY=>'yesterday_charms',
			CHANNEL_DOTEY_SORT_SONGS_YESTERDAY=>'yesterday_songs',
			CHANNEL_DOTEY_SORT_LIVETIME_SUPER => 'super_livetime',
			CHANNEL_DOTEY_SORT_RANK => 'dotey_rank',
			CHANNEL_DOTEY_SORT_CHARMS_SUPER=>'charm',
			CHANNEL_DOTEY_SORT_SORT =>'sort',
		);
		return isset($map[$sort]) ? $map[$sort] : 'yesterday_charms';
	}
	
	/**
	 * 过滤档期的通用方法
	 * 
	 * @param array $archives 
	 * @return int $return 0表示返回所有 1表示返回正在直播的 2表示返回待直播的
 	 * @return int $removeDays 过滤掉超出指定天数范围的记录,参数值大于0生效
	 * @return array
	 */
	public function filterArchives(array &$archives ,$return = 0,$removeDays=0){
		foreach($archives as $key=>$archive){
			//过滤没有直播记录的
			if(!isset($archive['live_record']) || empty($archive['live_record'])){
				unset($archives[$key]);
				continue;
			}
			//过滤掉已经结束直播的
			if($archive['live_record']['status'] == 2 || $archive['live_record']['status'] == -1){
				unset($archives[$key]);
				continue;
			}
			//过滤掉后台隐藏的
			if(isset($archive['is_hide']) && $archive['is_hide']){
				unset($archives[$key]);
				continue;
			}
			//取得正在直揪或者待直播的
			if($return == 1 && $archive['live_record']['status'] == 0){
				unset($archives[$key]);
				continue;
			}elseif($return == 2 &&  $archive['live_record']['status'] == 1){
				unset($archives[$key]);
				continue;
			}
			
			if($archive['live_record']['status'] == 0){
				$startTime = $archive['live_record']['start_time'];
				if(time() - $startTime > 3600*3){
					unset($archives[$key]);
					continue;
				}
			}
			
			//过滤掉超出指定天数范围的记录
			if($archive['live_record']['status'] == 0 && $removeDays>0){
				$startTime = $archive['live_record']['start_time'];
				$testResult=PipiDate::checkTimeRange(time(),$startTime,true,$removeDays,true);
				if(!$testResult)
				{
					unset($archives[$key]);
					continue;
				}
			}
		}
		return $archives;
	}
}

?>