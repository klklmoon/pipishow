<?php
/**
 * 守护天使活动业务逻辑服务层
 * 
 * @author supeng <supeng@pipi.cn> 2010-11-2
 * @link http://show.pipi.cn
 * @copyright Copyright &copy; 2003-2010 show.pipi.cn
 * @license
 */
class GuardAngelService extends PipiService {

	const ACTIVITY_NAME = '守护天使';

	const ACTIVITY_GUARDSTSR_UNIT = 100;  #护星可获取勋章的最小单位 默认100
	const ACTIVITY_LUCKDOTEY_LIMIT = 10;  	#幸运主播分页显示数 默认为10
	const ACTIVITY_RANKING_EXPIRED = 180; 	#排行榜过期时间3分钟
	const ACTIVITY_RANKING_DOTEY_RANK = 11;	#新主播与全部主播榜分界点 默认11
	const ACTIVITY_RANKING_USER_RANK = 7; 	#新用户与全部用户榜分界点 默认7
	const ACTIVITY_MID = 21;				#天使守护勋章ID 线上：21 测试：21
	
	public $isLogin = false;
	
	public function __construct(PipiController $pipiController = null){
		parent::__construct($pipiController);
		$this->isLogin = !Yii::app()->user->isGuest;
	}
	
	/**
	 * 获取活动周期
	 * @return Ambigous <number, Ambigous, string, unknown, mixed>
	 */
	public static function getCurrentCycle(){
		$rsModel = new GuardAngelRecordsModel();
		$cycle = $rsModel->getCurrentCycle();
		return $cycle?$cycle:1;
	}
	
	/**
	 * 获取幸运主播列表
	 */
	public function getLuckDoteyList() {
		$page = Yii::app()->request->getParam('p',1);
		$redisCache = new OtherRedisModel();
		$luckData = $redisCache->getLuckDoteyList();
		if (!$luckData) {
			$liveRecordsModel = new LiveRecordsModel();
			$data = $liveRecordsModel->getLuckDoteyList(array('live_time_on' => self::_getLuckDoteyOnLiveTime()));
			if ($data) {
				$data = $this->buildDataByIndex($data, 'uid');
				$uids = array_keys($data);
	
				$consumeSer = new ConsumeService();
				$userSer = new UserService();
				$consumeInfos = $consumeSer->getConsumesByUids($uids);
				$userInfos = $userSer->getUserBasicByUids($uids);
				$sortInfos = array();
				foreach($consumeInfos as $v){
					if($userInfos[$v['uid']]['user_status'] == 0){
						$sortInfos[$v['dotey_rank']][$v['uid']] = $data[$v['uid']];
					}
				}
				ksort($sortInfos);
	
				$luckData = array();
				foreach ($sortInfos as $drank=>$infos){
					foreach($infos as  $uid=>$info){
						$info['drank'] = $drank;
						$luckData[$info['uid']] = $info;
						//$v = $userInfos[$info['uid']];
						//$luckData[$info['uid']]['nickname'] = $v['nickname'];
						//$luckData[$info['uid']]['avatar'] = $userSer->getUserAvatar($v['uid'],'middle');
						//$luckData[$info['uid']]['href'] = $this->controller->getTargetHref('/'.$v['uid'],true,true);
					}
				}
				$redisCache->setLuckDoteyList($luckData, self::_getLuckDoteyExpired());
			}else{
				return array();
			}
		}
	
		//数据初始化
		$count = ceil(count($luckData)/self::ACTIVITY_LUCKDOTEY_LIMIT);
		if($page > $count)
			$page = 1;
		$offset = ($page-1)*self::ACTIVITY_LUCKDOTEY_LIMIT;
		$data = array_slice($luckData, $offset,self::ACTIVITY_LUCKDOTEY_LIMIT);
	
		$uids = array();
		$newData = array();
		foreach($data as $v){
			$uids[] = $v['uid'];
			$newData[$v['uid']] = $v;
		}
	
		$userSer = new UserService();
		$userInfos = $userSer->getUserBasicByUids($uids);
		foreach($userInfos as $v){
			if($v['user_status'] == 0){
				$newData[$v['uid']]['nickname'] = $v['nickname'];
				$newData[$v['uid']]['avatar'] = $userSer->getUserAvatar($v['uid'],'middle');
				$newData[$v['uid']]['href'] = $this->controller->getTargetHref('/'.$v['uid'],true,true);
			}else{
				unset($newData[$v['uid']]);
			}
		}
		$result['data'] = $newData;
		$result['currPage'] = $page;
		$result['countPage'] = $count;
		return $result;
	}
	
	/**
	 * 获取主播榜
	 * 
	 * @param string $type 包括： 
	 * 	ar=全部主播榜
	 * 	nr=新人主播榜
	 * 	pr=全站周期榜
	 * @param int|null $dotey_uid
	 * @return multitype:|Ambigous <multitype:, unknown>
	 */
	public function getAllDoteyGuardRank($type = 'ar',$dotey_uid = null){
		$redisCache = new OtherRedisModel();
		$doteyRank = $redisCache->getGuardAngelDoteyRanking();
		if(!$doteyRank){
			$doteyRank = $this->setAllDoteyGuardRank();
			$this->setAllUserGuardRank();
		}
		$doteyRank = $doteyRank[$type];
		return $doteyRank?($dotey_uid?$doteyRank[$dotey_uid]:$doteyRank):array();
	}
	
	/**
	 * 获取用户榜
	 *
	 * @param string $type 包括：
	 * 	ar=全部用户榜
	 * 	nr=新人用户榜
	 * @return multitype:|Ambigous <multitype:, unknown>
	 */
	public function getAllUserGuardRank($type = 'ar'){
		$redisCache = new OtherRedisModel();
		$userRank = $redisCache->getGuardAngelUserRanking();
		if(!$userRank){
			$userRank = $this->setAllUserGuardRank();
			$this->setAllDoteyGuardRank();
		}
		
		return isset($userRank[$type])?$userRank[$type]:array();
	}
	
	/**
	 * 获取所有守护用户的所有列表
	 * 	去重查询
	 * @return mixed
	 */
	public function getAllGuardUserList(){
		$relModel = new GuardAngelRelationModel();
		return $relModel->getAllGuardUserList();
	}
	
	/**
	 * 设置主播排行榜
	 * @return Ambigous <multitype:, number, unknown>
	 */
	public function setAllDoteyGuardRank(){
		$redisCache = new OtherRedisModel();
		$userSer = new UserService();
		$consumSer = new ConsumeService();
		$relModel = new GuardAngelRelationModel();
		$data = $relModel->getAllDoteyGuardRank();
		if($data){
			$doteyRank = array();
			$nrDUids = array();
			$arDUids = array();
		
			$new_key = 1;
			$all_key = 1;
			foreach ($data as $k=>$v){
				$doteyRank['pr'][$v['dotey_uid']]['star'] = $v['star'];
				$doteyRank['pr'][$v['dotey_uid']]['rank'] = $k+1;
					
				if($v['star'] > 0){
					if($v['drank'] <= self::ACTIVITY_RANKING_DOTEY_RANK){
						if($new_key > 5){
							continue;
						}
						$nrDUids[] = $v['dotey_uid'];
						$doteyRank['nr'][$new_key]['duid'] = $v['dotey_uid'];
						$doteyRank['nr'][$new_key]['star'] = $v['star'];
						$doteyRank['nr'][$new_key]['drank'] = $v['drank'];
						++$new_key;
					}
		
					if($all_key > 10){
						continue;
					}
					$arDUids[] = $v['dotey_uid'];
					$doteyRank['ar'][$all_key]['duid'] = $v['dotey_uid'];
					$doteyRank['ar'][$all_key]['star'] = $v['star'];
					$doteyRank['ar'][$all_key]['drank'] = $v['drank'];
					++$all_key;
				}
			}
		
			if ($nrDUids){
				if ($doteyRank['nr']){
					$nrUInfos = $userSer->getUserBasicByUids($nrDUids);
					$rs = $relModel->getDoteyMaxStar($nrDUids);
					if ($rs){
						foreach($rs as $v){
							if (!isset($maxRank[$v['dotey_uid']])){
								$maxRank[$v['dotey_uid']] = $v;
							}
						}
					}
					//$maxRank = $this->buildDataByIndex($maxRank, 'dotey_uid');
					$uids = array_keys($this->buildDataByIndex($maxRank, 'uid'));
					$userInfos = $userSer->getUserBasicByUids($uids);
					$consDInfos = $consumSer->getConsumesByUids($nrDUids);
					$consUInfos = $consumSer->getConsumesByUids($uids);
					foreach($doteyRank['nr'] as $r=>&$v2){
						if(isset($maxRank[$v2['duid']])){
							$uid = $maxRank[$v2['duid']]['uid'];
							$doteyRank['nr'][$r]['uid'] = $uid;
							$doteyRank['nr'][$r]['urank'] = isset($consUInfos[$uid])?$consUInfos[$uid]['rank']:$maxRank[$v2['duid']]['urank'];
							$doteyRank['nr'][$r]['nickname'] = $userInfos[$uid]['nickname'];
						}else{
							$doteyRank['nr'][$r]['uid'] = '';
							$doteyRank['nr'][$r]['urank'] = '';
							$doteyRank['nr'][$r]['nickname'] = '';
						}
						$doteyRank['nr'][$r]['dnickname'] = isset($nrUInfos[$v2['duid']])?$nrUInfos[$v2['duid']]['nickname']:'';
						$doteyRank['nr'][$r]['drank'] = isset($consDInfos[$v2['duid']])?$consDInfos[$v2['duid']]['dotey_rank']:$v2['drank'];
					}
				}
			}
			if ($arDUids){
				if ($doteyRank['ar']){
					$arUInfos = $userSer->getUserBasicByUids($arDUids);
					$rs = $relModel->getDoteyMaxStar($arDUids);
					if ($rs){
						foreach($rs as $v){
							if (!isset($maxRank[$v['dotey_uid']])){
								$maxRank[$v['dotey_uid']] = $v;
							}
						}
					}
					$uids = array_keys($this->buildDataByIndex($maxRank, 'uid'));
					$userInfos = $userSer->getUserBasicByUids($uids);
					$consDInfos = $consumSer->getConsumesByUids($arDUids);
					$consUInfos = $consumSer->getConsumesByUids($uids);
					foreach($doteyRank['ar'] as $r=>&$v){
						if(isset($maxRank[$v['duid']])){
							$uid = $maxRank[$v['duid']]['uid'];
							$v['uid'] = $uid;
							$v['urank'] = isset($consUInfos[$uid])?$consUInfos[$uid]['rank']:$maxRank[$v['duid']]['urank'];
							$v['nickname'] = $userInfos[$uid]['nickname'];
						}else{
							$v['uid'] = '';
							$v['urank'] = '';
							$v['nickname'] = '';
						}
						$v['dnickname'] = isset($arUInfos[$v['duid']])?$arUInfos[$v['duid']]['nickname']:'';
						$v['drank'] = isset($consDInfos[$v['duid']])?$consDInfos[$v['duid']]['dotey_rank']:$v['drank'];
					}
				}
			}
			unset($new_key,$all_key,$nrDUids,$arDUids,$maxRank,$userInfos,$arUInfos,$nrUInfos);
			$redisCache->setGuardAngelDoteyRanking($doteyRank, self::ACTIVITY_RANKING_EXPIRED);
		}
		return isset($doteyRank)?$doteyRank:array();
	}
	
	/**
	 * 设置用户排行榜
	 * @return string|multitype:
	 */
	public function setAllUserGuardRank(){
		$redisCache = new OtherRedisModel();
		$userSer = new UserService();
		$consumeSer = new ConsumeService();
		$relModel = new GuardAngelRelationModel();
		$newRank = $relModel->getAllUserGuardRank(10,false,self::ACTIVITY_RANKING_USER_RANK);
		$allRank = $relModel->getAllUserGuardRank(20,true,0);
			
		$userRank = array();
		if($newRank){
			$uids = array();
			$dotey_uids = array();
			foreach($newRank as $k=>$v){
				$uids[$v['uid']] = $v['uid'];
				$dotey_uids[$v['dotey_uid']] = $v['dotey_uid'];
			}
			$uInfos = $userSer->getUserBasicByUids($uids);
			$dInfos = $userSer->getUserBasicByUids($dotey_uids);
			$consUInfos = $consumeSer->getConsumesByUids($uids);
			foreach($newRank as $k=>$v){
				if($v['star'] > 0){
					$_r = $k+1;
					$userRank['nr'][$_r]['uid'] =  $v['uid'];
					$userRank['nr'][$_r]['star'] = $v['star'];
					$userRank['nr'][$_r]['dotey_uid'] = $v['dotey_uid'];
					$userRank['nr'][$_r]['urank'] = isset($consUInfos[$v['uid']])?$consUInfos[$v['uid']]['rank']:$v['urank'];
					$userRank['nr'][$_r]['nickname'] = isset($uInfos[$v['uid']])?$uInfos[$v['uid']]['nickname']:'';
					$userRank['nr'][$_r]['dnickname'] = isset($dInfos[$v['dotey_uid']])?$dInfos[$v['dotey_uid']]['nickname']:'';
				}
			}
			unset($uids,$dotey_uids);
		}
			
		if($allRank){
			$uids = array();
			$dotey_uids = array();
			foreach($allRank as $k=>$v){
				$uids[$v['uid']] = $v['uid'];
				$dotey_uids[$v['dotey_uid']] = $v['dotey_uid'];
			}
			$uInfos = $userSer->getUserBasicByUids($uids);
			$dInfos = $userSer->getUserBasicByUids($dotey_uids);
			$consUInfos = $consumeSer->getConsumesByUids($uids);
			foreach($allRank as $k=>$v){
				if($v['star'] > 0){
					$_r = $k+1;
					$userRank['ar'][$_r]['uid'] =  $v['uid'];
					$userRank['ar'][$_r]['star'] = $v['star'];
					$userRank['ar'][$_r]['dotey_uid'] = $v['dotey_uid'];
					$userRank['ar'][$_r]['urank'] = isset($consUInfos[$v['uid']])?$consUInfos[$v['uid']]['rank']:$v['urank'];
					$userRank['ar'][$_r]['nickname'] = isset($uInfos[$v['uid']])?$uInfos[$v['uid']]['nickname']:'';
					$userRank['ar'][$_r]['dnickname'] = isset($dInfos[$v['dotey_uid']])?$dInfos[$v['dotey_uid']]['nickname']:'';
				}
			}
			unset($uids,$dotey_uids);
		}
		if($userRank){
			$redisCache->setGuardAngelUserRanking($userRank, self::ACTIVITY_RANKING_EXPIRED);
			return $userRank;
		}else{
			return array();
		}
	}
	
	/**
	 * 开始守护
	 * @return number
	 */
	public function startGuard($dotey_uid,$uid){
		if(!$dotey_uid || !$uid){
			$result['flag'] = 0;
			$result['message']['info'] = '<br/>参数不能为空';
		//}elseif (!$this->isLogin){
			//$result['flag'] = 1;
			//$result['message']['info'] = '<br/>请先登录才能参与守护天使活动';
		}else{
			//$uid = (int)Yii::app()->user->id;
			if (!$uid || !$dotey_uid){
				$result['flag'] = 2;
				$result['message']['info'] = '请求不合法';
			}else{
				$relModel = new GuardAngelRelationModel();
				if($relModel->checkGuard($uid, $dotey_uid)){
					$sumStar = $relModel->getUserToDoteyCountStar($uid, $dotey_uid);
					$sumStar = $sumStar?$sumStar:0;
					$prank = $this->getAllDoteyGuardRank('pr',$dotey_uid);
					$rank = $prank['rank']?$prank['rank']:0;
					$result['flag'] = 3;
					$result['message']['rank'] = $rank;
					$result['message']['sumStar'] = $sumStar;
					$result['message']['info'] = '你已经守护了该主播</br>主播当前排在第<span class="col-red">'.$rank.'</span>名<br/>你已为TA累计<span class="col-red">'.$sumStar.'</span>颗守护星';
				}else{
					$consumeSer = new ConsumeService();
					$consumeInfo = $consumeSer->getConsumesByUids(array($uid,$dotey_uid));
					if(count($consumeInfo) == 2){
						$urank = $consumeInfo[$uid]['rank'];
						$drank = $consumeInfo[$dotey_uid]['dotey_rank'];
						$countGuard = $relModel->lookGuardList($uid,true);
						$allowGuardNum = self::_getGuardNum($uid);
						if ($countGuard == $allowGuardNum){
							$result['flag'] = 4;
							$result['message']['allowGuardNum'] = $allowGuardNum;
							$result['message']['info'] = '对不起 <br/>你目前只能守护位<span class="col-red">'.$allowGuardNum.'</span>主播';
						}else{
							$archivesSer = new ArchivesService();
							$archivesInfo = $archivesSer->getArchivesByUids(array($dotey_uid),false);
							$archivesInfo = array_shift($archivesInfo);
							if(!isset($archivesInfo['archives_id'])){
								$result['flag'] = 5;
								$result['message']['info'] = '守护失败，主播信息有误';
							}else{
								if(!$this->insertGuardAngel($uid, $dotey_uid, $urank, $drank)){
									$result['flag'] = 5;
									$result['message']['info'] = '守护失败，操作异常';
								}else{
									$aid = $archivesInfo['archives_id'];
									$this->sendZMQForGs($uid, $aid,0,true);
									$result['flag'] = 6;
									$result['message']['num'] = self::ACTIVITY_GUARDSTSR_UNIT;
									$result['message']['info'] = '守护成功<br/>为TA累计满<span class="col-red">'.self::ACTIVITY_GUARDSTSR_UNIT.'</span>颗<br/>守护星就可获得守护天使勋章哟';
								}
							}
						}
					}else{
						$result['flag'] = 4;
						$result['message']['info'] = '<br/>守护失败，异常操作';
					}
				}
			}
		}
		return $result;
	}
	
	/**
	 * 检查是否已经守护
	 * @param int $uid
	 * @param int $dotey_uid
	 * @return Ambigous <Ambigous, NULL, unknown, multitype:unknown Ambigous <unknown, NULL> , mixed, multitype:, multitype:unknown >
	 */
	public function checkIsGuard($uid, $dotey_uid){
		$relModel = new GuardAngelRelationModel();
		return $relModel->checkGuard($uid, $dotey_uid);
	}
	
	/**
	 * 查看用户的守护列表
	 */
	public function lookGuardList($uid){
		if (!$uid){
			$result['flag'] = 1;
			$result['message'] = '未登录用户无法查看你的守护信息';
		}else{
			$relModel = new GuardAngelRelationModel();
			$list = $relModel->lookGuardList($uid);
			if($list){
				$userSer = new UserService();
				$userInfo = $userSer->getUserBasicByUids(array_keys($this->buildDataByIndex($list, 'dotey_uid')));
				$nameStr = '';
				if($userInfo){
					foreach ($userInfo as $info){
						$nameStr .= $info['nickname'].',';
					}
				}
				$result['flag'] = 2;
				$result['message'] = '<br/>当前守护的主播 <br/><span class="col-red">'.trim($nameStr,',').'</span>';
			}else{
				$result['flag'] = 3;
				$result['message'] = '您当前还没有守护的主播哦<br/>快去守护心仪的主播吧！';
			}
		}
		return $result;
	}
	
	/**
	 * 获取主播守护星排行
	 */
	public function lookDoteyRank($dotey_uid){
		if($dotey_uid && is_numeric($dotey_uid)){
			$prank = $this->getAllDoteyGuardRank('pr',$dotey_uid);
			$rank = $prank['rank'];
			$star = $prank['star']?$prank['star']:0;
			$result['flag'] = 1;
			$result['message'] = array('rank'=>$rank,'star'=>$star,'info'=>'查看主播守护排行成功');
		}else{
			$result['flag'] = 2;
			$result['message'] = array('info'=>'参数有误，获取守护排行失败');
		}
		return $result;
	}
	
	/**
	 * 查看用户守护星数量
	 */
	public function lookUserGuardStar($uid,$dotey_uid){
		if($uid && $dotey_uid && is_numeric($dotey_uid) && is_numeric($uid)){
			$relModel = new GuardAngelRelationModel();
			$prank = $this->getAllDoteyGuardRank('pr',$dotey_uid);
			$sumStar = $relModel->getUserToDoteyCountStar($uid, $dotey_uid);
			$sumStar = $sumStar?$sumStar:0;
			$drank = $prank['rank'];
			$dstar = $prank['star']?$prank['star']:0;
			$result['flag'] = 1;
			$result['message'] = array('drank'=>$drank,'dstar'=>$dstar,'ustar'=>$sumStar,'info'=>'查看用户守护星成功');
		}else{
			$result['flag'] = 2;
			$result['message'] = array('info'=>'参数有误，查看用户守护星失败');
		}
		return $result;
	}
	
	/**
	 * 每周期首次插入守护时插入守护记录及关系
	 * @param int $uid
	 * @param int $dotey_uid
	 * @param int $urank
	 * @param int $drank
	 * @return multitype:
	 */
	public function insertGuardAngel($uid,$dotey_uid,$urank,$drank){
		$data['uid'] = $uid;
		$data['dotey_uid'] = $dotey_uid;
		$data['star'] = 0;
		$data['urank'] = $urank;
		$data['drank'] = $drank;
		$relModel = new GuardAngelRelationModel();
		$this->attachAttribute($relModel,$data);
		if(!$relModel->validate()){
			return $this->setNotices($relModel->getErrors(),array());
		}
		$relModel->save();
		$records = $data = $relModel->attributes;
		if($records){
			$records['cycle'] = self::getCurrentCycle();
			$records['guard_star'] = $records['star'];
			$records['ctime'] = time();
			$records['stime'] = time();
			$records['etime'] = time();
			unset($records['star']);
			$rsModel = new GuardAngelRecordsModel();
			$this->attachAttribute($rsModel, $records);
			$rsModel->save();
		}
		return $data;
	}
	
	/**
	 * 插入下一条周期记录标识
	 * 	在结束当前周期记录的时候执行此操作
	 * @return boolean
	 */
	public function insertNextCycle(){
		$cycle = self::getCurrentCycle();
		$records['cycle'] = $cycle+1;
		$records['guard_star'] = 0;
		$records['ctime'] = time();
		$records['stime'] = time();
		$records['etime'] = time();
		$rsModel = new GuardAngelRecordsModel();
		$this->attachAttribute($rsModel, $records);
		$rsModel->save();
		return $rsModel->attributes;
	}
	
	/**
	 * 更新守护星，守护关系及守护记录
	 * 
	 * @param int $uid			守护者ID
	 * @param int $dotey_uid 	被守护的主播id
	 * @param int $cycle 		当前的守护周期
	 * @param int $star  		当前的守护星
	 * @param int $stime 		守护开始时间
	 * @param int $etime 		守护结束时间
	 * @return multitype:number string 
	 */
	public function updateGuardAngel($uid,$dotey_uid,$cycle,$star,$stime,$etime){
		$result = array();
		$relModel = new GuardAngelRelationModel();
		$rsModel = new GuardAngelRecordsModel();
		if($relModel->checkGuard($uid, $dotey_uid) && $rsModel->checkGuard($uid, $dotey_uid, $cycle)){
			//更新关系数据
			if($relModel->updateGuardAngelRelation($uid, $dotey_uid,$star)){
				$relData = $relModel->checkGuard($uid, $dotey_uid)->attributes;
				$archivesSer = new ArchivesService();
				$archivesInfo = $archivesSer->getArchivesByUids(array($dotey_uid),false);
				if($archivesInfo){
					$archivesInfo = array_shift($archivesInfo);
					$aid = $archivesInfo['archives_id'];
					//是否分颁发天使守护勋章
					if($star == self::ACTIVITY_GUARDSTSR_UNIT){
						self::sendMedalToUser($uid,$etime);
					}
					self::sendZMQForGs($uid,$aid,$star,true,false);
				}
				#更新记录数据
				$data['uid'] = $uid;
				$data['dotey_uid'] = $dotey_uid;
				$data['cycle'] = $cycle;
				$data['guard_star'] = $star;
				$data['urank'] =$relData['urank'];
				$data['drank'] =$relData['drank'];
				$data['stime'] =$stime;
				$data['etime'] =$etime;
				$rsModel->updateGuardAngelRecords($data);
				$result['flag'] = 3;
				$result['message'] = '更新守护星成功';
			}else{
				$result['flag'] = 2;
				$result['message'] = '更新守护星失败';
			}
		}else{
			$result['flag'] = 1;
			$result['message'] = '该守护信息不存在'; 
		}
		return $result;
	}
	
	/**
	 * 删除守护记录
	 * @param int $record_id
	 */
	public function delRecords($record_id){
		$relModel = new GuardAngelRecordsModel();
		return $relModel->deleteByPk($record_id);
	}
	
	/**
	 * 删除所有守护关系
	 */
	public function delAllRelation(){
		$relModel = new GuardAngelRelationModel();
		return $relModel->deleteAll();
	}
	
	/**
	 * 删除主播排行
	 */
	public function delGuardAngelDoteyRanking(){
		$redisCache = new OtherRedisModel();
		return $doteyRank = $redisCache->delGuardAngelDoteyRanking();
	}
	
	/**
	 * 删除用户排行
	 */
	public function delGuardAngelUserRanking(){
		$redisCache = new OtherRedisModel();
		return $doteyRank = $redisCache->delGuardAngelUserRanking();
	}
	
	/**
	 * 清除主播本周期内的守护星
	 * @param int $uid
	 * @return mix|Ambigous <boolean, unknown>
	 */
	public function clearGuardStarByUid($uid){
		if($uid<=0)
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		$userJson=new UserJsonInfoService();
		$newUserJson=$userJson->getUserInfo($uid,false);
		//$json_info['gs']=array();
		$json_info['gs']= new stdClass();
		$zmq=$this->getZmq();
		$zmqData['type']='update_json';
		$zmqData['uid']=$uid;
		$zmqData['json_info']=$json_info;
		$zmq->sendZmqMsg(609, $zmqData);
		return $userJson->setUserInfo($uid,$json_info);
	}
	
	/**
	 * 发送 update_json事件 更新天天使守护信息
	 * @param int $uid
	 * @param int $archives_id
	 * @param int $star
	 * @param boolean $plus
	 * @return mix|Ambigous <boolean, unknown>
	 */
	public function sendZMQForGs($uid,$archives_id,$star=0,$plus=true,$isZmq=true){
		if($uid<=0||$archives_id<=0)
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		$userJson=new UserJsonInfoService();
		$newUserJson=$userJson->getUserInfo($uid,false);
		if($plus){
			$newUserJson['gs'][$archives_id] = $star;
		}else{
			if(isset($newUserJson['gs'])){
				if(key_exists($archives_id,$newUserJson['gs'])){
					unset($newUserJson['gs'][$archives_id]);
				}
			}
		}
		if(empty($newUserJson['gs'])){
			$newUserJson['gs'] = new stdClass();
		}
		$json_info['gs']=$newUserJson['gs'];
		
		if($isZmq){
			$zmq=$this->getZmq();
			$zmqData['type']='update_json';
			$zmqData['uid']=$uid;
			$zmqData['json_info']=$json_info;
			$zmq->sendZmqMsg(609, $zmqData);
		}
		return $userJson->setUserInfo($uid,$json_info);
	}
	
	/**
	 * 发送天使守护勋章给用户
	 * 
	 * @param int $uid
	 * @param int $etime
	 * @return boolean
	 */
	public function sendMedalToUser($uid,$etime) {
		$userMedalSer = new UserMedalService();
		$mid = self::ACTIVITY_MID;
		$type = MEDALAWARD_TYPE_SYS;
		$array['uid'] = $uid;
		$array['mid'] = $mid;
		$array['type'] = MEDALAWARD_TYPE_SYS;
		$array['vtime'] = $etime;
		
		$relModel = new GuardAngelRelationModel();
		$guardList = $relModel->lookGuardList($uid);
		if($guardList){
			foreach($guardList as $v){
				if($v['star'] >= self::ACTIVITY_GUARDSTSR_UNIT){
					$doteyUids[] = $v['dotey_uid'];
				}
			}
			if($doteyUids){
				$archivesSer = new ArchivesService();
				$archivesInfo = $archivesSer->getArchivesByUids($doteyUids,false);
				$aids = array_keys($archivesInfo);
			}
		}
		
		if($aids){
			$medalInfo = $userMedalSer->getUserMedalByUid($uid,MEDALAWARD_TYPE_SYS,$mid);
			if($medalInfo){
				$array['rid'] = $medalInfo[0]['rid'];
			}
			$userMedalSer->saveUserMedal($array,$aids);
		}
		return true;
	}
	
	/**
	 * 获取幸运主播的存储过期时间
	 * 
	 * @return number
	 */
	private static function _getLuckDoteyExpired() {
		return strtotime(date('Y-m-d 23:59:59', time())) - time();
	}

	/**
	 * 获取幸运主播开播的开始时间
	 * 
	 * @return number
	 */
	private static function _getLuckDoteyOnLiveTime() {
		return date('Y-m-d 00:00:00', strtotime('-1 months'));exit;
	}

	/**
	 * 获取可以守护主播的数量
	 * 
	 * @param int $uid        	
	 * @return number
	 */
	private static function _getGuardNum($uid) {
		$service = new ConsumeService();
		$info = $service->getConsumesByUids($uid);
		if ($info) {
			$rank = $info[$uid]['rank'];
		} else {
			return 0;
		}
		
		if ($rank <= 10) {
			return 1; // 于富豪4
		} elseif ($rank <= 14 && $rank > 10) {
			return 2; // 豪5以上，富豪8以下
		} elseif ($rank >= 15) {
			return 3; // 豪8以上
		}
		return 0;
	}
}