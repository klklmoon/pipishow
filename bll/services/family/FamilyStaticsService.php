<?php

/**
 * 家族统计服务层。
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z suqian $ 
 * @package 
 */
class FamilyStaticsService extends PipiService{
	
	/**
	 * @var FamilyService 家族服务层
	 */
	private static $familyService = null;
	
	public function __construct(PipiController $pipiController = NULL){
		parent::__construct($pipiController);
		if(self::$familyService == null){
			self::$familyService = new FamilyService();
		}
	}
	
	/**
	 * 计算家族某月所有主播的开播时长统计
	 * @param unknown_type $familyId
	 * @param unknown_type $month
	 * @param unknown_type $roleId
	 * @param unknown_type $page
	 * @param unknown_type $pageSize
	 * @param array $data
	 * @return multitype:|Ambigous <number, multitype:, multitype:unknown Ambigous <multitype:unknown , unknown> , unknown>
	 */
	public function staticsFamiliyMemeberLiveById($familyId,$month,$roleId=-1, $page = 1, $pageSize = 20,array &$data = array()){
		$return = self::$familyService->getMembersByPage($familyId,$roleId,$page,$pageSize, array('family_dotey' => 1));
		if(!$return['list']){
			return array();
		}
		$archivesService = new ArchivesService();
		$dateCal = new PipiDateCal();
		list($condtion['startTime'],$condtion['endTime']) = $dateCal->getCurPointMonthTime($month);
		$doteys = $this->buildDataByIndex($return['list'],'uid');
		$uids  = array_keys($doteys);
		$effectDays = $archivesService->getLiveEffectDaysUnit($uids);
		$quitRecords = self::$familyService->getUserQuitRecordsByUids($familyId,$uids);
		$archives = $archivesService->getArchivesByUids($uids);
		$archiveIds = array_keys($archives);
		$uarchives = $this->buildDataByIndex($archives,'uid');
		$liveRecords = $archivesService->getLiveRecordsByCondition($archiveIds,$condtion);
		$uLiveRecords = array();
		foreach($liveRecords as $archive_id =>$liveRecord){
			if(isset($archives[$archive_id])){
				$uLiveRecords[$archives[$archive_id]['uid']] = $liveRecords[$archive_id];
			}
		}
		
		foreach($doteys as $uid=>$dotey){
			$liveTime = 0;
			if(isset($quitRecords[$uid])){
				if(isset($uLiveRecords[$uid])){
					foreach($quitRecords[$uid] as $quitRecord){
						//当退出时间小于计算月的开始时间，不加入计算月进行计算
						if($quitRecord['quit_time'] < $condtion['starTime']){
							continue;
						}
						//当加入时间大于计算月的结束时间，不加入计算月进行计算
						if($quitRecord['join_time'] > $condtion['endTime']){
							continue;
						}
						foreach($uLiveRecords[$uid] as $uLiveRecord){
							if($quitRecord['join_time'] <= $uLiveRecord['live_time'] && $quitRecord['quit_time'] > $uLiveRecord['live_time']){
								$liveTime += $uLiveRecord['duration'];
							}
						}
					}
					
				}
			}
			
			//计算退出过，最后一次加入，因为最后一次加并没有在退出记录里
			if(isset($uLiveRecords[$uid])){
				foreach($uLiveRecords[$uid] as $uLiveRecord){
					if($dotey['create_time'] <= $uLiveRecord['live_time']){
						$liveTime += $uLiveRecord['duration'];
					}
				}
			}
			$doteys[$uid]['live_time'] = $liveTime;
		}
		
		foreach($doteys as $uid=>$dotey){
			if($dotey['live_time'] > 0){
				$effectDay = isset($effectDays[$uid]) ? $effectDays[$uid] : 2;
				$hour = floor($dotey['live_time'] / 3600);
				$min = floor(($dotey['live_time'] / 3600 - $hour)*60);
				$doteys[$uid]['live_day'] = floor(round($dotey['live_time'] / 3600,2)/$effectDay,1);
				if($hour){
					$doteys[$uid]['live_hour'] =  $hour.'小时'.$min.'分钟';
				}else{
					$doteys[$uid]['live_hour'] =  $min.'分钟';
				}
			}else{
				$doteys[$uid]['live_hour'] = 0;
				$doteys[$uid]['live_day'] = 0;
			}
		}
		$return['list'] = $doteys;
		$data[0] = $archiveIds;
		$data[1] = $liveRecords;
		return $return;
	}
	
	/**
	 * 获取某主播在某家族的某月开播时长记录
	 * @param unknown_type $uid
	 * @param unknown_type $familyId
	 * @param unknown_type $month
	 */
	public function getDoteyMonthLiveRecords($uid,$familyId,$month){
		if($uid <= 0 || $familyId <= 0 || empty($month)){
			return array();
		}
		$archivesService = new ArchivesService();
		$dateCal = new PipiDateCal();
		list($condtion['startTime'],$condtion['endTime']) = $dateCal->getCurPointMonthTime($month);
		$archives = $archivesService->getArchivesByUids(array($uid));
		if(empty($archives)){
			return array();
		}
		$family = self::$familyService->getMembersByUid($uid);
		if(empty($family)){
			return array();
		}
	
		$quitRecords = self::$familyService->getUserQuitRecordsByUids($familyId,array($uid));
		$quitRecords = isset($quitRecords[$uid]) ? $quitRecords[$uid] : array();
		$doteyCharmPointsModel = DoteyCharmPointRecordsModel::model();
		$archiveIds = array_keys($archives);
		$liveRecords = $archivesService->getLiveRecordsByCondition($archiveIds,$condtion);
		$liveRecords = $liveRecords ? array_pop($liveRecords) : array();
		$days = ceil(($condtion['endTime'] - $condtion['startTime'])/(3600*24));
		$archives = array_pop($archives);
		$list = array();
		for($i = $days-1;$i >= 0;$i--){
			$startTime = $condtion['startTime']+$i*3600*24;
			$endTime = $startTime+3600*24-1;
			$points = 0;
			$liveTime = 0;
			$program = '';
			if($quitRecords){
				foreach($quitRecords as $quitRecord){
					if($quitRecord['join_time'] >= $startTime && $quitRecord['quit_time'] <= $endTime){
						//如 5月3号10：00 加入 5月3号20点退出 统计5月3号
						$points += $doteyCharmPointsModel->sumDoteyTimeCharmPointsByUid($uid,$quitRecord['join_time'],$quitRecord['quit_time']);
					}elseif($quitRecord['join_time'] >= $startTime && $quitRecord['quit_time'] > $endTime){
						//如 5月3号10：00 加入 5月4号20点退出  统计5月3号
						$points += $doteyCharmPointsModel->sumDoteyTimeCharmPointsByUid($uid,$quitRecord['join_time'],$endTime);
					}elseif($quitRecord['join_time'] < $startTime && $quitRecord['quit_time'] <= $endTime){
						//如 5月3号10：00 加入 5月4号20点退出  统计5月4号
						$points += $doteyCharmPointsModel->sumDoteyTimeCharmPointsByUid($uid,$startTime,$quitRecord['quit_time']);
					}elseif($quitRecord['join_time'] < $startTime && $quitRecord['quit_time'] > $endTime){
						$points += $doteyCharmPointsModel->sumDoteyTimeCharmPointsByUid($uid,$startTime,$endTime);
					}
					
					foreach($liveRecords as $liveRecord){
						//直播时间必须是在当天内
						if($liveRecord['live_time'] >= $startTime && $liveRecord['live_time'] <= $endTime){
							if($liveRecord['live_time'] >= $quitRecord['join_time'] && $liveRecord['live_time'] <= $quitRecord['quit_time']){
								$liveTime += $liveRecord['duration'];
								$program .= $program ? ('<br/>'.date('H:i',$liveRecord['live_time']) .'：' .$liveRecord['sub_title']) : date('H:i',$liveRecord['live_time']) .'：' .$liveRecord['sub_title'];
							}
						}
						
					}
				}
				
			}
			//加入未退出或者退出再次加入
			if($family['create_time'] < $startTime){
				$points += $doteyCharmPointsModel->sumDoteyTimeCharmPointsByUid($uid,$startTime,$endTime);
			}elseif($family['create_time'] >= $startTime && $family['create_time']<=$endTime){
				$points += $doteyCharmPointsModel->sumDoteyTimeCharmPointsByUid($uid,$family['create_time'],$endTime);
			}
			
			foreach($liveRecords as $liveRecord){
				if($liveRecord['live_time'] >= $startTime && $liveRecord['live_time'] <= $endTime){
					if($family['create_time'] < $liveRecord['live_time']){
						$liveTime += $liveRecord['duration'];
						$program .= $program ?('<br/>'.date('H:i',$liveRecord['live_time']) .'：' .$liveRecord['sub_title']) : date('H:i',$liveRecord['live_time']) .'：' .$liveRecord['sub_title'];
					}
				}
			}
			$list[$i]['points'] = $points;
			$list[$i]['date'] = date('m-d',$startTime);
			$list[$i]['program'] = $program ? $program : $archives['title'];
			$list[$i]['live_time'] = $liveTime;
			
		}
		
		foreach($list as $key=>$_list){
			if($_list['live_time'] > 0){
				$hour = floor($_list['live_time'] / 3600);
				$min = floor(($_list['live_time'] / 3600 - $hour)*60);
				if($hour){
					$list[$key]['live_hour'] =  $hour.'小时'.$min.'分钟';
				}else{
					$list[$key]['live_hour'] =  $min.'分钟';
				}
			}else{
				$list[$key]['live_hour'] = 0;
			}
		}
		return $list;
	}
	
	/**
	 * 计算某家族某月所有主播的收益统计
	 * @param unknown_type $familyId
	 * @param unknown_type $month
	 * @param unknown_type $roleId
	 * @param unknown_type $page
	 * @param unknown_type $pageSize
	 * @param unknown_type $data
	 */
	public function staticsFamiliyIncomeById($familyId,$month,$roleId=-1, $page = 1, $pageSize = 20,array &$data = array(), $filter_uids = array()){
		$dateCal = new PipiDateCal();
		list($condtion['startTime'],$condtion['endTime']) = $dateCal->getCurPointMonthTime($month);
		$return = $this->getFamilyExitRecords($familyId,$condtion['startTime'],$condtion['endTime'],$pageSize,($page - 1) * $pageSize);
		if(!$return){
			return array();
		}
		$consumeService = new ConsumeService();
		$doteyService = new DoteyService();
		
		$doteyCharmPointsModel = DoteyCharmPointRecordsModel::model();
		$doteys = $this->buildDataByIndex($return,'uid');
		$uids  = array_keys($doteys);
		$doteyScales = $doteyService->getDoteyCashConfig($uids);
		$scale = self::$familyService->getFamilyScale($familyId);
		$quitRecords = $this->getUserExitRecordsByUids($uids,$familyId,$condtion['startTime'],$condtion['endTime']);
		$exchangeRecords = $consumeService->getExchangeRecordList($uids,$condtion);
		$userInfo = UserJsonInfoService::getInstance()->getUserInfos($uids, false);
		foreach($doteys as $uid=>$dotey){
			$rmb = 0;
			$points = $invalid = 0;
			if(isset($quitRecords[$uid])){
				foreach($quitRecords[$uid] as $quitRecord){
					//当退出时间小于计算月的开始时间，不加入计算月进行计算
					if($quitRecord['quit_time'] < $condtion['starTime']){
						continue;
					}
					//当加入时间大于计算月的结束时间，不加入计算月进行计算
					if($quitRecord['join_time'] > $condtion['endTime']){
						continue;
					}
					if(isset($exchangeRecords[$uid])){
						foreach($exchangeRecords[$uid] as $exchangeRecord){
							if( $quitRecord['quit_time'] > 0){
								if($quitRecord['join_time'] <= $exchangeRecord['create_time'] && $quitRecord['quit_time'] > $exchangeRecord['create_time']){
									$rmb += $exchangeRecord['dst_amount'];
								}
							}else{
								if($exchangeRecord['create_time'] > $quitRecord['join_time']){
									$rmb += $exchangeRecord['dst_amount'];
								}
							}
						}
					}
					$start_time = $end_time = 0;
					if($quitRecord['quit_time']){
						if($condtion['startTime'] >= $quitRecord['join_time'] && $condtion['endTime'] <=  $quitRecord['quit_time']){
							//本月内无退出情况
							$start_time = $condtion['startTime'];
							$end_time = $condtion['endTime'];
						}elseif($condtion['startTime'] >= $quitRecord['join_time'] && $condtion['endTime'] >  $quitRecord['quit_time']){
							//月中退出
							$start_time = $condtion['startTime'];
							$end_time = $quitRecord['quit_time'];
						}elseif($condtion['startTime'] < $quitRecord['join_time'] && $condtion['endTime'] <=  $quitRecord['quit_time']){
							//月中加入
							$start_time = $condtion['startTime'];
							$end_time = $quitRecord['quit_time'];
						}elseif($condtion['startTime'] < $quitRecord['join_time'] && $quitRecord['quit_time'] < $condtion['endTime']){
							//退出时间在本月范围内
							$start_time = $quitRecord['join_time'];
							$end_time = $quitRecord['quit_time'];
						}
					}else{
						if($condtion['startTime'] >= $quitRecord['join_time']){
							$start_time = $condtion['startTime'];
							$end_time = $condtion['endTime'];
						}else{
							$start_time = $quitRecord['join_time'];
							$end_time = $condtion['endTime'];
						}
					}
					if($start_time > 0 && $end_time > 0){
						$points += $doteyCharmPointsModel->sumDoteyTimeCharmPointsByUid($uid,$start_time, $end_time);
						if(!empty($filter_uids)){
							$invalid += $doteyCharmPointsModel->sumDoteyTimeCharmPointsByUid($uid,$start_time, $end_time, $filter_uids);
						}
					}
				}
					
			}
			
			$doteyScale = isset($doteyScales[$uid]) ? $doteyScales[$uid] : $doteyScales;
			$doteys[$uid]['rmb'] = $rmb;
			$doteys[$uid]['points'] = $points;
			$doteys[$uid]['points_valid'] = $points - $invalid;
			$doteys[$uid]['points_invalid'] = $invalid;
			$doteys[$uid]['family_rmb'] = ($points - $invalid)*$scale*$doteyScale;
			$doteys[$uid]['nickname'] = isset($userInfo[$uid]['rk']) ? $userInfo[$uid]['nk'] : '';
			$doteys[$uid]['rk'] = isset($userInfo[$uid]['rk']) ? $userInfo[$uid]['rk'] : 0;
			$doteys[$uid]['dk'] = isset($userInfo[$uid]['dk']) ? $userInfo[$uid]['dk'] : 0;
			
		}
		return $doteys;
	}
	
	/**
	 * 获取某主播在某家族的收益记录
	 * @param unknown_type $familyId
	 * @param unknown_type $uid
	 * @param unknown_type $days
	 * @param unknown_type $plus
	 */
	public function getFamilyToDayDayCharmPoints($familyId,$uid,$days = 0,$plus = false){
		if($familyId <= 0){
			return 0;
		}
		$dateCal = new PipiDateCal();
		list($condtion['startTime'],$condtion['endTime']) = $dateCal->pushDownDaysTime($days,false);
		$family = self::$familyService->getMembersByUid($uid);
		if(empty($family)){
			return 0;
		}
	
		$quitRecords = self::$familyService->getUserQuitRecordsByUids($familyId,array($uid));
		$quitRecords = isset($quitRecords[$uid]) ? $quitRecords[$uid] : array();
		$doteyCharmPointsModel = DoteyCharmPointRecordsModel::model();
		$startTime = $condtion['startTime'];
		$endTime = $condtion['endTime'];
		$points = 0;
		if($quitRecords){
			foreach($quitRecords as $quitRecord){
				if($quitRecord['join_time'] >= $startTime && $quitRecord['quit_time'] <= $endTime){
					//如 5月3号10：00 加入 5月3号20点退出 统计5月3号
					$points += $doteyCharmPointsModel->sumDoteyTimeCharmPointsByUid($uid,$quitRecord['join_time'],$quitRecord['quit_time']);
				}elseif($quitRecord['join_time'] >= $startTime && $quitRecord['quit_time'] > $endTime){
					//如 5月3号10：00 加入 5月4号20点退出  统计5月3号
					$points += $doteyCharmPointsModel->sumDoteyTimeCharmPointsByUid($uid,$quitRecord['join_time'],$endTime);
				}elseif($quitRecord['join_time'] < $startTime && $quitRecord['quit_time'] <= $endTime){
					//如 5月3号10：00 加入 5月4号20点退出  统计5月4号
					$points += $doteyCharmPointsModel->sumDoteyTimeCharmPointsByUid($uid,$startTime,$quitRecord['quit_time']);
				}elseif($quitRecord['join_time'] < $startTime && $quitRecord['quit_time'] > $endTime){
					$points += $doteyCharmPointsModel->sumDoteyTimeCharmPointsByUid($uid,$startTime,$endTime);
				}
			}
		}
		//加入未退出或者退出再次加入
		if($family['create_time'] < $startTime){
			$points += $doteyCharmPointsModel->sumDoteyTimeCharmPointsByUid($uid,$startTime,$endTime);
		}elseif($family['create_time'] >= $startTime && $family['create_time']<=$endTime){
			$points += $doteyCharmPointsModel->sumDoteyTimeCharmPointsByUid($uid,$family['create_time'],$endTime);
		}
		return $points;
	}
	
	/**
	 * 计算某家族所有强退主播的收益统计
	 * @param unknown_type $familyId
	 * @param unknown_type $page
	 * @param unknown_type $pageSize
	 * @param unknown_type $data
	 */
	public function staticsFamiliyForceIncome($familyId, $page = 1, $pageSize = 20,array &$data = array(), $month='', $filter_uids = array()){
		$start = $end = 0;
		$dateCal = new PipiDateCal();
		if(!empty($month)) list($start,$end) = $dateCal->getCurPointMonthTime($month);
		$return = $this->getUserForceExitRecords($familyId,$pageSize,($page - 1) * $pageSize, $start, $end);
		if(!$return){
			return array();
		}
		$consumeService = new ConsumeService();
		$doteyService = new DoteyService();
		
		$doteyCharmPointsModel = DoteyCharmPointRecordsModel::model();
		$doteys = $this->buildDataByIndex($return,'uid');
		$uids  = array_keys($doteys);
		$doteyScales = $doteyService->getDoteyCashConfig($uids);
		$scale = self::$familyService->getFamilyScale($familyId);
		$quitRecords = $this->getUserForceExitRecordsByUid($familyId,$uids, $start, $end);
		$userInfo = UserJsonInfoService::getInstance()->getUserInfos($uids, false);
		foreach($doteys as $uid=>$dotey){
			$rmb = $points = $invalid = 0;
			if(isset($quitRecords[$uid])){
				$useQuitRecords = $quitRecords[$uid];
				foreach($useQuitRecords as $quitRecord){
					$latestQuitRecord = $this->getUserLatestJoinFamily($uid,$familyId,$quitRecord['join_time']);
					$endTime = time();
					if($latestQuitRecord){
						$endTime = $latestQuitRecord['join_time'];
					}
					$startTime = $quitRecord['quit_time'];
					//当加入时间大于计算月的结束时间，不加入计算月进行计算
					$points += $doteyCharmPointsModel->sumDoteyTimeCharmPointsByUid($uid,$startTime, $endTime);
					if(!empty($filter_uids)){
						$invalid += $doteyCharmPointsModel->sumDoteyTimeCharmPointsByUid($uid,$startTime, $endTime, $filter_uids);
					}
				}
					
			}
			
			$doteyScale = isset($doteyScales[$uid]) ? $doteyScales[$uid] : $doteyScales;
			$doteys[$uid]['points'] = $points;
			$doteys[$uid]['points_valid'] = $points - $invalid;
			$doteys[$uid]['points_invalid'] = $invalid;
			$doteys[$uid]['family_rmb'] = ($points - $invalid)*$scale*$doteyScale;
			$doteys[$uid]['nickname'] = isset($userInfo[$uid]['rk']) ? $userInfo[$uid]['nk'] : '';
			$doteys[$uid]['rk'] = isset($userInfo[$uid]['rk']) ? $userInfo[$uid]['rk'] : 0;
			$doteys[$uid]['dk'] = isset($userInfo[$uid]['dk']) ? $userInfo[$uid]['dk'] : 0;
			
		}
		return $doteys;
	}
	public function getFamilyExitRecords($familyId,$startTime,$endTime,$limit=20,$offset=0){
		$sql = "SELECT DISTINCT uid FROM `web_family_exit_records` WHERE is_dotey = 1 AND family_id = {$familyId} AND (quit_time =0  OR (quit_time > {$startTime} AND quit_time <= {$endTime} AND live_type = 1)) AND join_time <= {$endTime} LIMIT {$offset},{$limit}";
		$familyExitRecordsModel = new FamilyExitRecordsModel();
		$familyDbCommand = $familyExitRecordsModel->getDbCommand();
		return $familyDbCommand->setText($sql)->queryAll();
		
	}
	public function countFamilyExitRecords($familyId,$startTime,$endTime){
		$sql = "SELECT count(DISTINCT uid) FROM `web_family_exit_records` WHERE is_dotey = 1 AND family_id = {$familyId} AND (quit_time =0  OR (quit_time > {$startTime} AND quit_time <= {$endTime} AND live_type = 1)) AND join_time <= {$endTime}";
		$familyExitRecordsModel = new FamilyExitRecordsModel();
		$familyDbCommand = $familyExitRecordsModel->getDbCommand();
		return $familyDbCommand->setText($sql)->queryScalar();
	}
	
	public function getUserExitRecords($uid,$familyId,$startTime,$endTime){
		$sql = "SELECT DISTINCT uid FROM `web_family_exit_records` WHERE is_dotey = 1 AND family_id = {$familyId} AND (quit_time =0  OR (quit_time > {$startTime} AND quit_time <= {$endTime} AND live_type = 1)) AND join_time <= {$endTime} AND uid = {$uid}";
		$familyExitRecordsModel = new FamilyExitRecordsModel();
		$familyDbCommand = $familyExitRecordsModel->getDbCommand();
		return $familyDbCommand->setText($sql)->queryAll();
	}
	
	public function getUserExitRecordsByUids(array $uids,$familyId,$startTime,$endTime){
		$uids = implode(',',$uids);
		$sql = "SELECT * FROM `web_family_exit_records` WHERE is_dotey = 1 AND family_id = {$familyId} AND (quit_time =0  OR (quit_time > {$startTime} AND quit_time <= {$endTime} AND live_type = 1)) AND join_time <= {$endTime} AND uid IN ({$uids})";
		$familyExitRecordsModel = new FamilyExitRecordsModel();
		$familyDbCommand = $familyExitRecordsModel->getDbCommand();
		$records = $familyDbCommand->setText($sql)->queryAll();
		return $this->buildDataByKey($records,'uid');
	}
	
	public function getUserForceExitRecords($familyId,$limit = 20,$offset = 0, $startTime = 0, $endTime = 0){
		$sql = "SELECT DISTINCT uid FROM `web_family_exit_records` a WHERE family_id={$familyId} AND quit_time >0 AND live_type=0 AND is_dotey=1 ".($startTime > 0 ? " AND join_time >= ".$startTime : "").($endTime > 0 ? " AND quit_time <= ".$endTime : "")." AND NOT EXISTS (
				SELECT uid FROM web_family b WHERE   a.family_id=b.id AND a.uid=b.uid
				) ORDER BY join_time DESC LIMIT {$offset},{$limit}";
		$familyExitRecordsModel = new FamilyExitRecordsModel();
		$familyDbCommand = $familyExitRecordsModel->getDbCommand();
		return $familyDbCommand->setText($sql)->queryAll();
	}
	
	public function countUserForceExitRecords($familyId, $startTime = 0, $endTime = 0){
		$sql = "SELECT COUNT(DISTINCT uid) FROM `web_family_exit_records` a WHERE family_id={$familyId} AND quit_time >0 AND live_type=0 AND is_dotey=1 ".($startTime > 0 ? " AND join_time >= ".$startTime : "").($endTime > 0 ? " AND quit_time <= ".$endTime : "")." AND NOT EXISTS (
				SELECT uid FROM web_family b WHERE   a.family_id=b.id AND a.uid=b.uid
				) ORDER BY join_time ";
		$familyExitRecordsModel = new FamilyExitRecordsModel();
		$familyDbCommand = $familyExitRecordsModel->getDbCommand();
		return $familyDbCommand->setText($sql)->queryScalar();
	}
	
	public function getUserForceExitRecordsByUid($familyId,array $uids, $startTime = 0, $endTime = 0){
		$uids = implode(',',$uids);
		$sql = "SELECT * FROM `web_family_exit_records` a WHERE family_id={$familyId}  AND  uid IN ({$uids}) AND quit_time >0 AND live_type=0 AND is_dotey=1 ".($startTime > 0 ? " AND join_time >= ".$startTime : "").($endTime > 0 ? " AND quit_time <= ".$endTime : "")." AND NOT EXISTS (
				SELECT uid FROM web_family b WHERE   a.family_id=b.id AND a.uid=b.uid
				)	ORDER BY join_time ";
		$familyExitRecordsModel = new FamilyExitRecordsModel();
		$familyDbCommand = $familyExitRecordsModel->getDbCommand();
		$records = $familyDbCommand->setText($sql)->queryAll();
		return $this->buildDataByKey($records,'uid');
	}
	
	public function getUserLatestJoinFamily($uid,$familyId,$joinTime){
		/* @var $familyDb CDbConnection */
		$familyDb = Yii::app()->db_family;
		/* @var $familyCommand CDbCommand */
		$familyCommand = $familyDb->createCommand();
		return $familyCommand->setText("SELECT uid,family_id,join_time,quit_time FROM `web_family_exit_records` a WHERE uid = {$uid} AND is_dotey = 1 AND join_time > {$joinTime} ORDER BY join_time ASC LIMIT 1")->queryRow();
	}
	
	/**
	 * @param $prev
	 * @param $next
	 * @author suqian
	 * @return int
	 */
	public function sortJoinTime(array $prev,array $next){
		if($prev['join_time'] == $next['join_time']){
			return 0;
		}
		return $prev['join_time'] > $next['join_time'] ? -1 : 1;
	}
	//public function getUserExitRecord
	public function getFamilyService(){
		return self::$familyService;
	}
	
}