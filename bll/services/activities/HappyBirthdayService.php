<?php
/**
 * @author zhangzhifan <zhangzhifan@pipi.cn> 2010-11-2
 * @link http://show.pipi.cn
 * @copyright Copyright &copy; 2003-2010 show.pipi.cn
 * @license
 */
class HappyBirthdayService extends PipiService {

	const ACTIVITY_NAME = '生日快乐';

	const ACTIVITY_START_DATE = '2013-08-08'; // 活动发布起始日期
	const REWARD_MEDAL_DATE = '01'; // 勋章领取日期
	const REWARD_CHARM_POINTS = 10000; // 主播魅力点奖励
	const REWARD_DEDICATION = 20000; // 用户贡献值奖励
	const PRINCE_MEDAL_ID = 22; // 王子勋章id
	const MEDAL_PERIOD_OF_VALIDITY = 1296000; // 有效期秒数,15天
	public $birthdayGiftList = array(178, 179, 50, 180, 181); // 活动套礼id
	                                                    
	// 获取套礼单价
	public function getBatchPrice() {
		$giftService = new GiftService();
		$giftList = $giftService->getGiftByIds($this->birthdayGiftList);
		$batchPrice = 0;
		foreach ($giftList as $gift) {
			$batchPrice = $batchPrice + $gift['pipiegg'];
		}
		return $batchPrice;
	}
	
	// 初始化主播生日快乐活动记录，每天执行一次
	public function initBirthdayDotey() {
		$birthdayDoteyModel = new BirthdayDoteyModel();
		if (date("Y-m-d") > self::ACTIVITY_START_DATE) {
			$queryDate = date("m-d");
			$doteyList = $birthdayDoteyModel->getDoteyBirthdayByDate($queryDate);
		} else {
			$doteyList = $birthdayDoteyModel->getDoteyBirthdayByDate();
		}
		
		$year = intval(date("Y"));
		$counts = 0;
		foreach ($doteyList as $doteyRow) {
			$birthdayDotey = $birthdayDoteyModel->find(array('condition' => 'year=:year AND dotey_id=:dotey_id', 
				'params' => array(':year' => $year, ':dotey_id' => $doteyRow['uid'])));
			if (!isset($birthdayDotey->record_id) || empty($birthdayDotey->record_id)) {
				$birthdayDotey = new BirthdayDoteyModel();
				$birthdayDotey->year = $year;
				$birthdayDotey->dotey_id = $doteyRow['uid'];
				$birthdayDotey->birthday = date("Y-m-d", $doteyRow['birthday']);
				$birthdayDotey->month = intval(date("m", $doteyRow['birthday']));
				$birthdayDotey->sday = intval(date("d", $doteyRow['birthday']));
				$birthdayDotey->create_time = time();
				$flag = $birthdayDotey->save();
				if ($flag) $counts++;
			}
		}
		return $counts;
	}
	
	// 计算主播生日当天收到套礼数
	public function getGiftSetMealNumByDotey($sdate, $dotey_id) {
		$stime = strtotime($sdate . " 00:00:00");
		$etime = strtotime($sdate . " 23:59:59");
		$birthdayDoteyModel = new BirthdayDoteyModel();
		$doteyGiftList = $birthdayDoteyModel->getSendGiftRecordsByDotey($stime, $etime, $dotey_id, $this->birthdayGiftList);
		if (isset($doteyGiftList) && count($doteyGiftList) == count($this->birthdayGiftList)) {
			$giftSetMealNum = $doteyGiftList[0]['gift_num'];
		} else {
			$giftSetMealNum = 0;
		}
		return $giftSetMealNum;
	}
	
	// 获取指定日期、id的主播生日信息
	public function getBirthdayDoteyInfoById($sdate, $dotey_id) {
		$year = intval(date("Y", strtotime($sdate)));
		$birthdayDoteyModel = new BirthdayDoteyModel();
		$birthdayDoteyData = $birthdayDoteyModel->find(array('condition' => 'year=:year AND dotey_id=:dotey_id', 
			'params' => array(':year' => $year, ':dotey_id' => $dotey_id)));
		$dotey_info = $birthdayDoteyData->attributes;
		return $dotey_info;
	}
	
	// 获取当天过生日的主播生日记录
	public function getBirthdayDoteyRecordsByDate($sdate) {
		$year = intval(date("Y", strtotime($sdate)));
		$month = intval(date("m", strtotime($sdate)));
		$sday = intval(date("d", strtotime($sdate)));
		$birthdayDoteyModel = new BirthdayDoteyModel();
		$birthdayDoteyData = $birthdayDoteyModel->findAll(array(
			'condition' => 'year=:year AND month=:month AND sday=:sday', 
			'params' => array(':year' => $year, ':month' => $month, ':sday' => $sday)));
		$dotey_list = $this->buildDataByIndex($this->arToArray($birthdayDoteyData), 'dotey_id');
		
		return $this->filterDotey($dotey_list);
	}
	
	// 过滤无效主播
	protected function filterDotey(array $dotey_list) {
		if(empty($dotey_list)) return array();
			
		// 取得主播基本信息
		$userService = new UserService();
		$userBascList = $userService->getUserBasicByUids(array_keys($dotey_list));
		
		// 有直播过的主播
		// 过滤相邻两个月的有效主播
		$yearMonth = date("Y-m");
		$start_time = strtotime(date("Y-m-d", strtotime("$yearMonth-01 -1 month")) . " 00:00:00");
		$end_time = strtotime(date("Y-m-d", strtotime("$yearMonth-01 +1 month -1 day")) . " 23:59:59");
		
		$birthdayDoteyModel = new BirthdayDoteyModel();
		$livedDoteyData = $birthdayDoteyModel->getRangThirtyLivedDoteysId($start_time, $end_time);
		$livedDoteyIds = array_keys($this->buildDataByIndex($livedDoteyData, 'uid'));
		
		$validDoteyList = array();
		foreach ($dotey_list as $dotey_id => $dotey_row) {
			if (in_array($dotey_id, $livedDoteyIds) && $userBascList[$dotey_id]['user_status'] == 0) $validDoteyList[$dotey_id] = $dotey_row;
		}
		
		return $validDoteyList;
	}
	
	// 计算主播指定用户送出生日套礼数
	public function getGiftSetMealNumByUser($sdate, $uid, $doteys) {
		$stime = strtotime($sdate . " 00:00:00");
		$etime = strtotime($sdate . " 23:59:59");
		$birthdayPrinceModel = new BirthdayPrinceModel();
		$userGiftList = $birthdayPrinceModel->getSendGiftRecordsByUser($stime, $etime, $uid, $doteys, $this->birthdayGiftList);
		if (count($userGiftList) < 5) {
			$giftSetMealNum = 0;
		} else {
			$giftSetMealNum = $userGiftList[0]['gift_num'];
		}
		return $giftSetMealNum;
	}
	
	// 获取本月过生日的主播生日记录
	public function getMonthBirthdayDoteyRecords($year, $month) {
		$birthdayDoteyModel = new BirthdayDoteyModel();
		$criteria = $birthdayDoteyModel->getDbCriteria();
		$criteria->condition = 'year=:year AND month=:month';
		$criteria->params = array(':year' => intval($year), ':month' => intval($month));
		$criteria->order = 'sday asc';
		$birthdayDoteyData = $birthdayDoteyModel->findAll($criteria);
		$dotey_list = $this->buildDataByIndex($this->arToArray($birthdayDoteyData), 'dotey_id');
		
		return $this->filterDotey($dotey_list);
	}
	
	// 统计月份生日公主榜
	public function getRankByMonth($yearMonth) {
		$year = intval(date("Y", strtotime($yearMonth . "-01")));
		$month = intval(date("m", strtotime($yearMonth . "-01")));
		$birthdayDoteyModel = new BirthdayDoteyModel();
		// 获取生日主播
		$doteys = array_keys($this->getMonthBirthdayDoteyRecords($year, $month));
		
		// 公主排行
		$doteyRank = $birthdayDoteyModel->getMonthPrincessRank($yearMonth, $doteys, $this->birthdayGiftList, 10);
		for ($i = 0; $i < count($doteyRank); $i++) {
			$doteyRank[$i]['month_rank'] = $i + 1;
		}
		
		// 王子排行
		$birthdayPrinceModel = new BirthdayPrinceModel();
		$userRank = $birthdayPrinceModel->getMonthPrinceRank($yearMonth, $doteys, $this->birthdayGiftList, 10);
		for ($i = 0; $i < count($userRank); $i++) {
			$userRank[$i]['month_rank'] = $i + 1;
		}
		return array('doteyRank' => $doteyRank, 'userRank' => $userRank);
	}
	
	// 统计本月份生日公主榜
	public function getThisMonthRank() {
		$yearMonth = date("Y-m");
		$birthdayDoteyModel = new BirthdayDoteyModel();
		// 获取生日主播
		$doteys = array_keys($this->getMonthBirthdayDoteyRecords(date("Y"), date("m")));
		
		// 公主排行
		$doteyRank = $birthdayDoteyModel->getMonthPrincessRank($yearMonth, $doteys, $this->birthdayGiftList, 10);
		for ($i = 0; $i < count($doteyRank); $i++) {
			$doteyRank[$i]['month_rank'] = $i + 1;
		}
		
		// 王子排行
		$birthdayPrinceModel = new BirthdayPrinceModel();
		$userRank = $birthdayPrinceModel->getMonthPrinceRank($yearMonth, $doteys, $this->birthdayGiftList, 10);
		for ($i = 0; $i < count($userRank); $i++) {
			$userRank[$i]['month_rank'] = $i + 1;
		}
		return array('doteyRank' => $doteyRank, 'userRank' => $userRank);
	}
	
	// 存储指定月份的生日快乐活动数据，一般为存储上月数据且每月只执行一次
	public function saveMonthData($yearMonth) {
		$yearMonthTemp = explode("-", $yearMonth);
		$year = intval($yearMonthTemp[0]);
		$month = intval($yearMonthTemp[1]);
		$birthdayDoteyModel = new BirthdayDoteyModel();
		// 获取生日主播
		$doteys = array_keys($this->getMonthBirthdayDoteyRecords($year, $month));
		// 存储公主排行
		$doteyRank = $birthdayDoteyModel->getMonthPrincessRank($yearMonth, $doteys, $this->birthdayGiftList, 10);
		$doteyRankCounts = 0;
		for ($i = 0; $i < count($doteyRank); $i++) {
			$dotey_id = $doteyRank[$i]['uid'];
			$birthdayDotey = $birthdayDoteyModel->find(array('condition' => 'year=:year AND dotey_id=:dotey_id', 
				'params' => array(':year' => $year, ':dotey_id' => $dotey_id)));
			
			if (isset($birthdayDotey->dotey_id) && $birthdayDotey->dotey_id > 0) {
				$birthdayDotey->receive_gift_num = $doteyRank[$i]['gift_num'];
				$birthdayDotey->birthday_charm = $doteyRank[$i]['sum_charm'];
				$birthdayDotey->month_rank = $i + 1;
				$birthdayDotey->update_time = time();
				$flag = $birthdayDotey->save();
				if ($flag) $doteyRankCounts++;
			}
		}
		
		// 存储王子排行
		$birthdayPrinceModel = new BirthdayPrinceModel();
		$userRank = $birthdayPrinceModel->getMonthPrinceRank($yearMonth, $doteys, $this->birthdayGiftList, 10);
		$userRankCounts = 0;
		for ($i = 0; $i < count($userRank); $i++) {
			$uid = $userRank[$i]['uid'];
			$birthdayPrince = $birthdayPrinceModel->find(array('condition' => 'year=:year AND month=:month AND uid=:uid', 
				'params' => array(':year' => $year, ':month' => $month, ':uid' => $uid)));
			
			if (empty($birthdayPrince->rank_id)) {
				$birthdayPrince = new BirthdayPrinceModel();
			}
			$birthdayPrince->year = $year;
			$birthdayPrince->month = $month;
			$birthdayPrince->uid = $uid;
			$birthdayPrince->send_gift_num = $userRank[$i]['gift_num'];
			$birthdayPrince->birthday_dedication = $userRank[$i]['sum_dedication'];
			$birthdayPrince->month_rank = $i + 1;
			$birthdayPrince->create_time = time();
			$flag = $birthdayPrince->save();
			if ($flag) $userRankCounts++;
		}
		return array('doteyRankCounts' => $doteyRankCounts, 'userRankCounts' => $userRankCounts);
	}
	
	// 奖励主播生日当天魅力点,最多能领3份
	public function rewardDoteyCharmPoints($dotey_id) {
		$result = -1;
		$sdate = date("Y-m-d");
		$birthdayDotey = $this->getBirthdayDoteyInfoById($sdate, $dotey_id);
		// 判断主播当天是否过生日
		
		if (!isset($birthdayDotey['month']) || $birthdayDotey['month'] != intval(date("m")) || $birthdayDotey['sday'] != intval(date("d"))) return -3;
		
		$stime = strtotime($sdate . " 00:00:00");
		$etime = strtotime($sdate . " 23:59:59");
		$consumeService = new ConsumeService();
		// 已领奖励数
		$doteyCharmPointRecordsModel = new DoteyCharmPointRecordsModel();
		$receivedRewardNum = $doteyCharmPointRecordsModel->count(array(
			'condition' => 'uid=:uid AND source=:source AND sub_source=:sub_source AND 
			create_time>=:start_time AND create_time<=:end_time', 
			'params' => array(':uid' => $dotey_id, ':source' => SOURCE_ACTIVITY, 
				':sub_source' => SUBSOURCE_ACTIVITY_HAPPYBIRTHDAY, ':start_time' => $stime, ':end_time' => $etime)));
		
		if ($receivedRewardNum >= 3) return -4;
		
		// 收到套礼数
		$giftSetMealNum = $this->getGiftSetMealNumByDotey($sdate, $dotey_id);
		
		if ($receivedRewardNum < 3 && $receivedRewardNum < $giftSetMealNum) {
			
			$consumeAttibute = array();
			$consumeAttibute['uid'] = $dotey_id;
			$consumeAttibute['charm_points'] = self::REWARD_CHARM_POINTS;
			if ($consumeService->saveUserConsumeAttribute($consumeAttibute)) {
				$addCharmPointsRecords = array();
				$addCharmPointsRecords['uid'] = $dotey_id;
				$addCharmPointsRecords['charm_points'] = self::REWARD_CHARM_POINTS;
				$addCharmPointsRecords['sender_uid'] = 0;
				$addCharmPointsRecords['num'] = 1;
				$addCharmPointsRecords['source'] = SOURCE_ACTIVITY;
				$addCharmPointsRecords['sub_source'] = SUBSOURCE_ACTIVITY_HAPPYBIRTHDAY;
				$addCharmPointsRecords['client'] = 1; // 1表示活动
				$addCharmPointsRecords['info'] = '生日快乐奖励:' . date("Y-m-d"); // 魅力值说明（生日快乐奖励:2013-07-08）
				$flag = $consumeService->saveDoteyCharmPointsRecords($addCharmPointsRecords);
			}
		} else {
			return -3;
		}
		if (isset($flag) && $flag)
			$result = 1;
		else
			$result = -1;
			// $result=$giftSetMealNum;
		return $result;
	}
	
	// 当月收到生日魅力值前三名的主播，均可在次月1日领取1个生日公主勋章
	public function rewardDoteyMedal($dotey_id) {
		$result = -1;
		$year = intval(date("Y"));
		$month = intval(date("m"));
		$today = date("d");
		if ($today != self::REWARD_MEDAL_DATE) return -3;
		
		// 检测主播名次
		$birthdayDoteyModel = new BirthdayDoteyModel();
		$birthdayDotey = $birthdayDoteyModel->find(array('condition' => 'year=:year AND dotey_id=:dotey_id', 
			'params' => array(':year' => $year, ':dotey_id' => $dotey_id)));
		if (isset($birthdayDotey->record_id) && $birthdayDotey->princess_medal == 1) return -4;
		
		if (isset($birthdayDotey->record_id) && $birthdayDotey->month_rank <= 3 && $birthdayDotey->month == ($month - 1) && $birthdayDotey->princess_medal != 1) {
			$birthdayDotey->princess_medal = 1;
			$birthdayDotey->medal_time = time();
			$flag = $birthdayDotey->save();
		} else {
			return -3;
		}
		
		if (isset($flag) && $flag)
			$result = 1;
		else
			$result = -1;
		return $result;
	}
	
	// 主播生日当天，用户每送出一套生日套礼，即可领取1份奖励（不限领取次数）
	public function rewardUserDedication($uid) {
		$result = -1;
		$sdate = date("Y-m-d");
		$stime = strtotime($sdate . " 00:00:00");
		$etime = strtotime($sdate . " 23:59:59");
		$consumeService = new ConsumeService();
		// 已领奖励数
		$userDedicationRecordsModel = new UserDedicationRecordsModel();
		$receivedRewardNum = $userDedicationRecordsModel->count(array(
			'condition' => 'uid=:uid AND source=:source AND sub_source=:sub_source AND 
			create_time>=:start_time AND create_time<=:end_time', 
			'params' => array(':uid' => $uid, ':source' => SOURCE_ACTIVITY, 
				':sub_source' => SUBSOURCE_ACTIVITY_HAPPYBIRTHDAY, ':start_time' => $stime, ':end_time' => $etime)));
		
		// 送出套礼数
		$doteys = array_keys($this->getBirthdayDoteyRecordsByDate($sdate));
		$giftSetMealNum = $this->getGiftSetMealNumByUser($sdate, $uid, $doteys);
		
		if ($receivedRewardNum < $giftSetMealNum) {
			$consumeAttibute = array();
			$consumeAttibute['uid'] = $uid;
			$consumeAttibute['dedication'] = self::REWARD_DEDICATION;
			if ($consumeService->saveUserConsumeAttribute($consumeAttibute)) {
				$addDedicationRecords = array();
				$addDedicationRecords['uid'] = $uid;
				$addDedicationRecords['dedication'] = self::REWARD_DEDICATION;
				$addDedicationRecords['num'] = 1;
				$addDedicationRecords['source'] = SOURCE_ACTIVITY;
				$addDedicationRecords['sub_source'] = SUBSOURCE_ACTIVITY_HAPPYBIRTHDAY;
				$addDedicationRecords['client'] = 1; // 1表示活动
				$addDedicationRecords['info'] = '生日快乐奖励:' . date("Y-m-d"); // 贡献值说明（生日快乐奖励:2013-07-08）
				$flag = $consumeService->saveUserDedicationRecords($addDedicationRecords);
			}
		} else {
			return -3;
			// return "$receivedRewardNum,$giftSetMealNum";
		}
		
		if (isset($flag) && $flag)
			$result = 1;
		else
			$result = -1;
			// $result="$receivedRewardNum,$giftSetMealNum";
		return $result;
	}
	
	// 当月生日贡献值前三名的用户，均可在次月1日领取1个生日王子勋章
	public function rewardUserMedal($uid) {
		$result = -1;
		$year = intval(date("Y"));
		$month = intval(date("m")) - 1;
		$today = date("d");
		if ($today != self::REWARD_MEDAL_DATE) return -3;
		
		// 检测用户名次
		$birthdayPrinceModel = new BirthdayPrinceModel();
		$birthdayPrince = $birthdayPrinceModel->find(array('condition' => 'year=:year AND month=:month AND uid=:uid', 
			'params' => array(':year' => $year, ':month' => $month, ':uid' => $uid)));
		
		// var_dump($birthdayPrince->attributes);exit;
		if (isset($birthdayPrince->rank_id) && $birthdayPrince->uid > 0 && $birthdayPrince->month_rank <= 3) {
			// 构造勋章数据
			$userMedalService = new UserMedalService();
			$ctime = time();
			
			$userMeda = $userMedalService->getUserMedalByUid($uid, 2, self::PRINCE_MEDAL_ID);
			if (isset($userMeda[0]['rid'])) 			// 已有
			{
				if ($userMeda[0]['vtime'] > time()) return -4;
				$userMedalData = array('mid' => self::PRINCE_MEDAL_ID, 'uid' => $uid, 'type' => 2, 
					'vtime' => $ctime + self::MEDAL_PERIOD_OF_VALIDITY);
				$userMedalData['rid'] = $userMeda[0]['rid'];
			} else {
				$userMedalData = array('mid' => self::PRINCE_MEDAL_ID, 'uid' => $uid, 'type' => 2, 
					'vtime' => $ctime + self::MEDAL_PERIOD_OF_VALIDITY, 'ctime' => $ctime);
			}
			$flag = $userMedalService->saveUserMedal($userMedalData);
		} else {
			return -3;
		}
		if (isset($flag) && $flag)
			$result = 1;
		else
			$result = -1;
		return $result;
	}

	/**
	 * ****以下为页面显示部分*****
	 */
	
	// 今日寿星
	public function getTodayBirthdayDoteys() {
		$today = date("Y-m-d");
		$yearMonth = date("Y-m");
		$mstime = strtotime($yearMonth . "-01 00:00:00");
		$metime = strtotime(date("Y-m-d", strtotime("$yearMonth-01 +1 month -1 day")) . " 23:59:59");
		$birthdayDoteyModel = new BirthdayDoteyModel();
		$doteyService = new DoteyService();
		
		$doteyList = $this->getBirthdayDoteyRecordsByDate($today);
		$archivesService = new ArchivesService();
		
		foreach ($doteyList as $dotey_id => $doteyRow) {
			// 本月礼物详情
			$doteyGiftList = $birthdayDoteyModel->getSendGiftRecordsByDotey($mstime, $metime, $dotey_id, $this->birthdayGiftList);
			$giftTotalNum = 0;
			foreach ($doteyGiftList as $doteyGiftRow) {
				$giftTotalNum += $doteyGiftRow['gift_num'];
			}
			$doteyList[$dotey_id]['pic'] = $doteyService->getDoteyUpload($dotey_id, 'small');
			$doteyList[$dotey_id]['giftTotalNum'] = $giftTotalNum;
			$doteyList[$dotey_id]['giftDetail'] = $this->buildDataByIndex($doteyGiftList, 'gift_id');
			$doteyArchives = $archivesService->getArchivesByUids($dotey_id, true, 0);
			$doteyArchives = array_pop($doteyArchives);
			
			$doteyList[$dotey_id]['title'] = $doteyArchives['title'];
			if (isset($doteyArchives['live_record'])) {
				$doteyList[$dotey_id]['sub_title'] = $doteyArchives['live_record']['sub_title'];
				$doteyList[$dotey_id]['status'] = $doteyArchives['live_record']['status'];
				$doteyList[$dotey_id]['live_record'] = $doteyArchives['live_record'];
			}
			$birthdayTimeStampStart = strtotime($doteyList[$dotey_id]['year'] . "-" . $doteyList[$dotey_id]['month'] . "-" . $doteyList[$dotey_id]['sday'] . " 00:00:00") - 86400 * 3;
			$birthdayTimeStampEnd = strtotime($doteyList[$dotey_id]['year'] . "-" . $doteyList[$dotey_id]['month'] . "-" . $doteyList[$dotey_id]['sday'] . " 23:59:59");
			$doteyList[$dotey_id]['show_cake'] = time() >= $birthdayTimeStampStart && time() <= $birthdayTimeStampEnd;
		}
		
		return $doteyList;
	}
	
	// 本月寿星
	public function getThisMonthBirthdayDoteys() {
		$year = intval(date("Y"));
		$month = intval(date("m"));
		$yearMonth = date("Y-m");
		$doteyList = $this->getMonthBirthdayDoteyRecords($year, $month);
		
		$mstime = strtotime($yearMonth . "-01 00:00:00");
		$metime = strtotime(date("Y-m-d", strtotime("$yearMonth-01 +1 month -1 day")) . " 23:59:59");
		$birthdayDoteyModel = new BirthdayDoteyModel();
		$doteyService = new DoteyService();
		$archivesService = new ArchivesService();
		foreach ($doteyList as $dotey_id => $doteyRow) {
			// 本月礼物详情
			$doteyGiftList = $birthdayDoteyModel->getSendGiftRecordsByDotey($mstime, $metime, $dotey_id, $this->birthdayGiftList);
			$giftTotalNum = 0;
			foreach ($doteyGiftList as $doteyGiftRow) {
				$giftTotalNum += $doteyGiftRow['gift_num'];
			}
			$doteyList[$dotey_id]['pic'] = $doteyService->getDoteyUpload($dotey_id, 'small');
			$doteyList[$dotey_id]['giftTotalNum'] = $giftTotalNum;
			$doteyList[$dotey_id]['giftDetail'] = $this->buildDataByIndex($doteyGiftList, 'gift_id');
			$doteyArchives = $archivesService->getArchivesByUids($dotey_id, true, 0);
			$doteyArchives = array_pop($doteyArchives);
			
			$doteyList[$dotey_id]['title'] = $doteyArchives['title'];
			if (isset($doteyArchives['live_record'])) {
				$doteyList[$dotey_id]['sub_title'] = $doteyArchives['live_record']['sub_title'];
				$doteyList[$dotey_id]['status'] = $doteyArchives['live_record']['status'];
				$doteyList[$dotey_id]['live_record'] = $doteyArchives['live_record'];
			}
			
			$birthdayTimeStampStart = strtotime($doteyList[$dotey_id]['year'] . "-" . $doteyList[$dotey_id]['month'] . "-" . $doteyList[$dotey_id]['sday'] . " 00:00:00") - 86400 * 3;
			$birthdayTimeStampEnd = strtotime($doteyList[$dotey_id]['year'] . "-" . $doteyList[$dotey_id]['month'] . "-" . $doteyList[$dotey_id]['sday'] . " 23:59:59");
			$doteyList[$dotey_id]['show_cake'] = time() >= $birthdayTimeStampStart && time() <= $birthdayTimeStampEnd;
		}
		
		$resultList = array();
		foreach ($doteyList as $doteyRow) {
			$resultList[] = $doteyRow;
		}
		return $resultList;
	}
	
	// 生日荣誉榜
	public function getHonorRankByMonth($yearMonth) {
		$year = intval(date("Y", strtotime($yearMonth . "-01")));
		$month = intval(date("m", strtotime($yearMonth . "-01")));
		$thisMonthRank = $this->getRankByMonth($yearMonth);
		$doteyRank = $thisMonthRank['doteyRank'];
		$userRank = $thisMonthRank['userRank'];
		$birthdayDoteyModel = new BirthdayDoteyModel();
		$birthdayPrinceModel = new BirthdayPrinceModel();
		
		$doteyCounts = count($doteyRank) >= 3 ? 3 : count($doteyRank);
		$resultDoteyRank = array();
		for ($i = 0; $i < $doteyCounts; $i++) {
			// 获取守护者
			$dotey_id = $doteyRank[$i]['uid'];
			if ($doteyRank[$i]['month_rank'] == 1) {
				$guardian = $birthdayDoteyModel->getMonthUserRankByDotey($yearMonth, $dotey_id, $this->birthdayGiftList, 3);
				$resultDoteyRank[] = array('dotey_id' => $dotey_id, 'rank' => $doteyRank[$i]['month_rank'], 
					'gift_num' => $doteyRank[$i]['gift_num'], 'sum_charm' => $doteyRank[$i]['sum_charm'], 
					'guardian' => count($guardian) > 0 ? $guardian : array());
			} else {
				$guardian = $birthdayDoteyModel->getMonthUserRankByDotey($yearMonth, $dotey_id, $this->birthdayGiftList, 1);
				$resultDoteyRank[] = array('dotey_id' => $dotey_id, 'rank' => $doteyRank[$i]['month_rank'], 
					'gift_num' => $doteyRank[$i]['gift_num'], 'sum_charm' => $doteyRank[$i]['sum_charm'], 
					'guardian' => isset($guardian[0]) ? $guardian[0] : 0);
			}
		}
		
		$userCounts = count($userRank) >= 3 ? 3 : count($userRank);
		// 获取生日主播
		$doteys = array_keys($this->getMonthBirthdayDoteyRecords($year, $month));
		$resultUserRank = array();
		for ($i = 0; $i < $userCounts; $i++) {
			// 获取守护主播
			$user_id = $userRank[$i]['uid'];
			$guardDotey = $birthdayPrinceModel->getMonthDoteyRankByUser($yearMonth, $user_id, $doteys, $this->birthdayGiftList, 1);
			$resultUserRank[] = array('uid' => $user_id, 'rank' => $userRank[$i]['month_rank'], 
				'gift_num' => $userRank[$i]['gift_num'], 'sum_dedication' => $userRank[$i]['sum_dedication'], 
				'guardDotey' => isset($guardDotey[0]) ? $guardDotey[0] : 0);
		}
		
		$result = array('doteyRank' => $this->buildDataByIndex($resultDoteyRank, 'rank'), 
			'userRank' => $this->buildDataByIndex($resultUserRank, 'rank'));
		return $result;
	}
	
	// 生日荣誉榜
	public function getHonorRank() {
		$sdate = date("Y-m-d");
		$lastMonth = date("Y-m", strtotime($sdate . " -1 month"));
		if ($lastMonth >= date("Y-m", strtotime(HappyBirthdayService::ACTIVITY_START_DATE))) {
			$honorRank = $this->getHonorRankByMonth($lastMonth);
		}
		$result = array('yearMonth' => $lastMonth, 'honorRank' => isset($honorRank) ? $honorRank : array());
		return $result;
	}
	
	// 活动礼物信息
	public function getActivityGiftList() {
		$giftService = new GiftService();
		$giftNameList = $giftService->getGiftByIds($this->birthdayGiftList);
		$giftList = array();
		foreach ($giftNameList as $giftNameRow) {
			$giftNameRow['url'] = $giftService->getGiftUrl($giftNameRow['image']);
			$giftList[$giftNameRow['gift_id']] = $giftNameRow;
		}
		return $this->buildDataByIndex($giftList, 'gift_id');
	}
	
	// 本年的所有月份荣誉
	public function getAllMonthHonorRank() {
		$monthList = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');
		$monthHonorRankData = array();
		foreach ($monthList as $month) {
			$yearMonth = date("Y") . "-" . $month;
			if ($yearMonth >= date("Y-m", strtotime(self::ACTIVITY_START_DATE)) && $yearMonth < date("Y-m")) {
				$honorRank = $this->getHonorRankByMonth($yearMonth);
				if (!empty($honorRank['doteyRank']) && count($honorRank['doteyRank']) > 0) $monthHonorRankData[$month] = $honorRank;
			}
		}
		return $monthHonorRankData;
	}
	
	//首页生日专栏主播
	public function getBirthdayArchives()
	{
		//今日生日主播
		$doteyIds=array_keys($this->getBirthdayDoteyRecordsByDate(date("Y-m-d")));
		
		$todayBirthdayArchives = $willBirthdayArchives = array();
		$archivesService = new ArchivesService();
		if(!empty($doteyIds)){
			$todayBirthdayArchives = $archivesService->getArchivesByUids($doteyIds);
		}
		
		$channelDoteySortService=new ChannelDoteySortService();
		//$channelDoteySortService->filterArchives($todayBirthdayArchives);
		//$channelDoteySortService->buildLiveArchives($todayBirthdayArchives,$uid,0,true,true);
		
		//将要生日主播
		$sdate_list=array(
			date("Y-m-d",strtotime("+1 day")),
			date("Y-m-d",strtotime("+2 day")),
			date("Y-m-d",strtotime("+3 day")),
		);

		$willBirthdayDoteys=array();
		foreach ($sdate_list as $sdate)
		{
			$sBirthdayDoteys=$this->getBirthdayDoteyRecordsByDate($sdate);
			$willBirthdayDoteys=array_merge($willBirthdayDoteys,$sBirthdayDoteys);
		}
		$willBirthdayDoteys=$this->buildDataByIndex($willBirthdayDoteys, "dotey_id");
		$doteyIds=array_keys($willBirthdayDoteys);
		
		if(!empty($doteyIds)){
			$willBirthdayArchives=$archivesService->getArchivesByUids($doteyIds);
			//$channelDoteySortService->filterArchives($willBirthdayArchives,0);
			//$channelDoteySortService->buildLiveArchives($willBirthdayArchives,$uid,0,true,true);
			$willBirthdayArchives=$this->buildDataByIndex($willBirthdayArchives, "uid");
		}
		
		$resultWillBirthdayArchives=array();
		if(count($willBirthdayArchives)>3)
		{
			$doteyIds=array_rand($willBirthdayArchives,3);
			foreach ($doteyIds as $doteyid)
			{
				$willBirthdayArchives[$doteyid]['sbirthday']=$willBirthdayDoteys[$doteyid]['month']."月".$willBirthdayDoteys[$doteyid]['sday']."日";
				$resultWillBirthdayArchives[$doteyid]=$willBirthdayArchives[$doteyid];
			}
		}
		else 
		{
			$doteyIds=array_keys($willBirthdayArchives);
			foreach ($doteyIds as $doteyid)
			{
				$willBirthdayArchives[$doteyid]['sbirthday']=$willBirthdayDoteys[$doteyid]['month']."月".$willBirthdayDoteys[$doteyid]['sday']."日";
				$resultWillBirthdayArchives[$doteyid]=$willBirthdayArchives[$doteyid];
			}
		}
		//var_dump($resultWillBirthdayArchives);
		$result=array(
			'todayBirthdayArchives'=>$todayBirthdayArchives,
			'willBirthdayArchives'=>$resultWillBirthdayArchives
		);
		
		return $result;
	}
}