<?php
/**
 * @author zhangzhifan <zhangzhifan@pipi.cn> 2010-11-2
 * @link http://show.pipi.cn
 * @copyright Copyright &copy; 2003-2010 show.pipi.cn
 * @license
 */

class GiftStarService extends PipiService{
	const ACTIVITY_NAME='礼物之星';
	const FIRST_WEEK_MONDAY='2013-07-22';	//礼物之星功能基准时间，测试时可以更改，上线后请不要修改
	const WEEK_TIME_SPAN=604800;		//一周时间的秒数，不可更改
	const REWARD_CHARM_POINTS=10000;			//礼物之星魅力点奖励10000
	public $giftWeekOrderList=array(1,2,3,4,5,6);	//周礼物序号列表
	
	//返回指定周编号周一日期
	public function getMondayDate($weekId)
	{
		return date("Y-m-d",strtotime(self::FIRST_WEEK_MONDAY." 00:10:00")+($weekId-1)*self::WEEK_TIME_SPAN);
	}
	
	//初始化礼物之星指定的周id的礼物之星其他设定
	public function initGiftStarSet($weekId)
	{
		$giftStarSetModel=new GiftStarSetModel();
		$giftStarSetAr=$giftStarSetModel->getGiftStarSetByWeekId($weekId);
		if($giftStarSetAr)
		{
			return false;
		}
		else
		{
			$lastWeekId=$weekId-1;
			$lastGiftStarSetAr=$giftStarSetModel->getGiftStarSetByWeekId($lastWeekId);
			$giftStarSetModel->week_id=$weekId;
			$giftStarSetModel->monday_date=$this->getMondayDate($weekId);
			$giftStarSetModel->illustration=$lastGiftStarSetAr->illustration;
			$giftStarSetModel->set_type=$lastGiftStarSetAr->set_type;
			$giftStarSetModel->create_time=time();
			if($giftStarSetModel->save())
				return true;
			else
				return false;
		}
	}
	
	//初始化礼物之星礼物规则设定
	public function initGiftStarRule($weekId)
	{
		$i=0;
		$giftStarRuleModel=new GiftStarRuleModel();
		foreach ($this->giftWeekOrderList as $orderId)
		{
			$giftStarRuleAr=$giftStarRuleModel->getGiftStarRuleByGiftWeekOrder($weekId,$orderId);
			if($giftStarRuleAr)
			{
				continue;
			}
			else
			{
				$lastWeekId=$weekId-1;
				$lastGiftStarRuleAr=$giftStarRuleModel->getGiftStarRuleByGiftWeekOrder($lastWeekId,$orderId);
				$giftStarRule=new GiftStarRuleModel();
				$giftStarRule->week_id=$weekId;
				$giftStarRule->gift_week_order=$orderId;
				$giftStarRule->monday_date=$this->getMondayDate($weekId);
				$giftStarRule->gift_id=$lastGiftStarRuleAr->gift_id;
				$giftStarRule->contention_rule=$lastGiftStarRuleAr->contention_rule;
				$giftStarRule->create_time=time();
				$flag=$giftStarRule->save();
				if($flag)
					$i++;
			}
		}
		return $i;
	}
	
	//初始化参与争夺榜单主播id和参与时的级别
	public function initGiftStarDotey($weekId)
	{
		//计算周id对应的结束时间
		$weekEndTime=$this->getWeekEndTimestamp($weekId);
		//获取指定周编号对应主播等级限制列表
		$giftStarRuleModel=new GiftStarRuleModel();
		$weekGradeList=$giftStarRuleModel->getWeekDoteyGradeListByWeekId($weekId);

		$consumeService=new ConsumeService();
		$doteyService = new DoteyService();
		//获取所有已签约主播
		$doteys = $doteyService->getDoteysByCondition(array('status'=>1));
		
		$i=0;
		if(count($doteys)<1)
			return $i;

		$giftStarDoteyModel=new GiftStarDoteyModel();
		foreach ($doteys as $doteyRow)
		{
			
			//统计主播魅力值
			$sumCharm=$giftStarDoteyModel->getDoteyCharmByTime($doteyRow['uid'],$weekEndTime);
			//获取魅力值对应的等级
			$doteyRank=$consumeService->getDoteyRankByCharm($sumCharm);
			
			//如果主播的等级符合对应周规则，则存储
			if(isset($doteyRank['rank']) && in_array($doteyRank['rank'],$weekGradeList))
			{
				//如果已经有，则只是更新
				$giftStarDotey=$giftStarDoteyModel->find(array('condition'=>'week_id=:week_id AND dotey_id=:dotey_id',
					'params'=>array(':week_id'=>$weekId,':dotey_id'=>$doteyRow['uid'])));
				
				if(!isset($giftStarDotey->record_id) || empty($giftStarDotey->record_id))
					$giftStarDotey=new GiftStarDoteyModel();
				$giftStarDotey->week_id=$weekId;
				$giftStarDotey->dotey_id=$doteyRow['uid'];
				$giftStarDotey->grade=$doteyRank['rank'];
				$giftStarDotey->create_time=time();
				$flag=$giftStarDotey->save();
				if($flag)
					$i++;
			}
		}
		return $i;
	}
	
	//计算周id对应的开始时间
	public function getWeekStartTimestamp($weekId)
	{
		$weekStartTime=strtotime(self::FIRST_WEEK_MONDAY." 00:00:00")+($weekId-1)*self::WEEK_TIME_SPAN;
		return $weekStartTime;
	}
	
	//计算周id对应的结束时间
	public function getWeekEndTimestamp($weekId)
	{
		$weekEndTime=strtotime(self::FIRST_WEEK_MONDAY." 00:00:00")+($weekId*self::WEEK_TIME_SPAN-1);
		return $weekEndTime;
	}
	
	//初始化指定周编号礼物之星各种设定
	public function createThisWeekDotey($weekId)
	{
		$this->initGiftStarSet($weekId);
		$this->initGiftStarRule($weekId);
		$this->initGiftStarDotey($weekId);
	}
	
	//礼物之星奖励单个主播
	public function rewardSingleDotey($uid,$charmPoints,$weekId,$gift_id)
	{
		$flag=false;
		$consumeService = new ConsumeService();
		$consumeAttibute = array();
		$consumeAttibute['uid'] = $uid;
		$consumeAttibute['charm'] = $charmPoints;
		$consumeAttibute['charm_points'] = $charmPoints;
		if($consumeService->saveUserConsumeAttribute($consumeAttibute)){
			$addRecords = array();
			$addRecords['uid'] = $uid;
			$addRecords['charm'] =  $charmPoints;
			$addRecords['sender_uid'] =  0;
			$addRecords['num'] = 1;
			$addRecords['source'] = SOURCE_ACTIVITY;
			$addRecords['sub_source'] = SUBSOURCE_ACTIVITY_GIFTSTAR;
			$addRecords['client'] = 1;					//1表示活动
			$addRecords['info'] = '礼物之星奖励:'.$this->getMondayDate($weekId).",gift_id:{$gift_id}";	//魅力值说明（礼物之星奖励:2013-07-08）
			$flag1=$consumeService->saveDoteyCharmRecords($addRecords);
			
			$addCharmPointsRecords = array();
			$addCharmPointsRecords['uid'] = $uid;
			$addCharmPointsRecords['charm_points'] =  $charmPoints;
			$addCharmPointsRecords['sender_uid'] =  0;
			$addCharmPointsRecords['num'] = 1;
			$addCharmPointsRecords['source'] = SOURCE_ACTIVITY;
			$addCharmPointsRecords['sub_source'] = SUBSOURCE_ACTIVITY_GIFTSTAR;
			$addCharmPointsRecords['client'] = 1;					//1表示活动
			$addCharmPointsRecords['info'] = '礼物之星奖励:'.$this->getMondayDate($weekId).",gift_id:{$gift_id}";	//魅力值说明（礼物之星奖励:2013-07-08）
			$flag2=$consumeService->saveDoteyCharmPointsRecords($addCharmPointsRecords);
		}
		if($flag1 && $flag2)
			return true;
		else
			return false;
	}
	
	//礼物之星奖励
	public function rewardDotey($weekId)
	{
		$consumeService = new ConsumeService();
		$doteyCharmPointsRecordsModel = new DoteyCharmPointRecordsModel();
		$giftStarRankModel=new GiftStarRankModel();
		$doteyList=$giftStarRankModel->getFirstDoteysByWeekId($weekId);
		$i=0;
		foreach ($doteyList as $doteyRow)
		{
			//检测是否已经奖励过了
			$condition=array(
				'uid'=>$doteyRow['dotey_id'],
				'source'=>SOURCE_ACTIVITY,
				'sub_source'=>SUBSOURCE_ACTIVITY_GIFTSTAR,
				'client'=>1,
				'info'=>'礼物之星奖励:'.$this->getMondayDate($weekId).",gift_id:{$doteyRow['gift_id']}"
			);
			$counts=$doteyCharmPointsRecordsModel->getCharmPointsRecordsCountByCondition($condition);
			if($counts>0)
			{
				$flag=false;
			}
			else
			{
				$flag=$this->rewardSingleDotey($doteyRow['dotey_id'],self::REWARD_CHARM_POINTS,$weekId,$doteyRow['gift_id']);
			}
			if($flag)
				$i++;
		}
		return $i;
	}
	
	//返回指定周编号的礼物ID、文件名、url
	public function getGiftStarGiftsByWeekId($weekId)
	{
		$giftStarRuleModel=new GiftStarRuleModel();
		$giftStarRuleList=$giftStarRuleModel->getGiftStarRuleListByWeekId($weekId);
		$giftIds=array();
		foreach ($giftStarRuleList as $giftStartRuleRow)
		{
			$giftIds[]=$giftStartRuleRow['gift_id'];
		}
		return 	$giftIds;
	}
	
	//返回礼物ID对应的路径列表
	public function getGiftUrlList($giftIds)
	{
		$giftService=new GiftService();
		$giftNameList=$giftService->getGiftByIds($giftIds);
		$giftList=array();
		if($giftNameList){
			foreach($giftNameList as $giftNameRow)
			{
				$giftNameRow['url']=$giftService->getGiftUrl($giftNameRow['image']);
				$giftList[$giftNameRow['gift_id']]=$giftNameRow;
					
			}
		}
		
		return $giftList;
	}
	
	//返回当前周编号
	public function getThisWeekId()
	{
		$ctime=time();
		$weekIdStarTimestamp=strtotime(self::FIRST_WEEK_MONDAY." 00:00:00");
		$thisWeekId=intval(floor(($ctime-$weekIdStarTimestamp)/self::WEEK_TIME_SPAN))+1;
		return $thisWeekId;
	}
	
	//获取礼物之星第一名主播列表
	public function getFirstDoteysByWeekId($weekId)
	{
		if($weekId < 1) return array();
		//取得周礼物信息
		$giftIds=$this->getGiftStarGiftsByWeekId($weekId);
		$giftList=$this->getGiftUrlList($giftIds);
		
		//取得周主播榜第一名信息
		$userService=new UserService();
		$giftStarRank=new GiftStarRankModel();
		$doteyList=$giftStarRank->getFirstDoteysByWeekId($weekId);
		$giftStarList=array();
		foreach ($doteyList as $doteyRow)
		{
			$doteyInfo=$userService->getUserFrontsAttributeByCondition($doteyRow['dotey_id'],true,true);
/* 			$giftStarList[$doteyRow['dotey_id']]=array(
				'dotey_id'=>$doteyRow['dotey_id'],
				'gift_name'=>$giftList[$doteyRow['gift_id']]['zh_name'],
				'gift_url'=>$giftList[$doteyRow['gift_id']]['url'],
				'gift_num'=>$doteyRow['gift_num'],
				'dotey_nickname'=>empty($doteyInfo['nk'])?"求昵称":$doteyInfo['nk'],
				'dotey_rank'=>$doteyInfo['dk'],
			); */
			
			$giftStarList[]=array(
				'dotey_id'=>$doteyRow['dotey_id'],
				'gift_name'=>$giftList[$doteyRow['gift_id']]['zh_name'],
				'gift_url'=>$giftList[$doteyRow['gift_id']]['url'],
				'gift_num'=>$doteyRow['gift_num'],
				'dotey_nickname'=>empty($doteyInfo['nk'])?"求昵称":$doteyInfo['nk'],
				'dotey_rank'=>$doteyInfo['dk'],
			);
		}
		return $giftStarList;
	}
	
	//获取指定周编号的特别说明
	public function getIllustrationByWeekId($weekId)
	{
		$giftStarSetModel=new GiftStarSetModel();
		$giftStarSetAr=$giftStarSetModel->getGiftStarSetByWeekId($weekId);
		return $giftStarSetAr->attributes;
	}
	
	//获取指定主播id、周编号的直播间礼之星信息
	public function getLivingboxGiftStar($doteyId,$weekId)
	{
		$giftStarRank=new GiftStarRankModel();
		$giftStarRankService=new GiftStarRankService();
		//从redis取数据
		$thisWeekList=$giftStarRankService->getWeekRankLingbox($weekId);
		//$thisWeekList=$giftStarRankService->createRankByWeekId($weekId);
		$giftStarImgModel=new GiftStarImgModel();
		$giftService=new GiftService();
		$thisWeekInfo=array();
		foreach ($thisWeekList as $thisWeekRow)
		{
			foreach ($thisWeekRow['data'] as $dataRow)
			{
				if($dataRow['uid']==$doteyId && $dataRow['rank'] <=5)
				{
					$giftImgInfo=$giftStarImgModel->getGiftImgByCondition($dataRow['gift_id'],$dataRow['rank']);
					$thisWeekInfo[]=array(
						'week_id'=>$dataRow['week_id'],
						'dotey_id'=>$dataRow['uid'],
						'rank'=>$dataRow['rank'],
						'gift_id'=>$dataRow['gift_id'],
						'gift_img_url'=>$giftService->getGiftUrl($giftImgInfo['image'])
					);
				}
			}
		}
		$lastWeekList=$giftStarRank->getGiftStarRank($doteyId,$weekId-1);
		$lastWeekInfo=array();
		foreach ($lastWeekList as $lastWeekRow)
		{
			if($lastWeekRow['rank']==1)
			{
				$giftImgInfo=$giftStarImgModel->getGiftImgByCondition($lastWeekRow['gift_id'],$lastWeekRow['rank']);
				$lastWeekInfo[]=array(
					'week_id'=>$lastWeekRow['week_id'],
					'dotey_id'=>$lastWeekRow['dotey_id'],
					'rank'=>$lastWeekRow['rank'],
					'gift_id'=>$lastWeekRow['gift_id'],
					'gift_img_url'=>$giftService->getShowAdminGiftUrl($giftImgInfo['image'])
				);
			}		
		}
				
		$livingboxGiftStar=array(
			'flag'=>isset($lastWeekInfo) && count($lastWeekInfo)>0?1:0,
			'thisWeekInfo'=>$thisWeekInfo,
			'lastWeekInfo'=>$lastWeekInfo,
			);
		return $livingboxGiftStar;
	}

	/******以下为与后台有关部分******/
	
	/**
	 * 根据条件获取规早的分页
	 * @param int $offset 获取页数
	 * @param int $pageSize 页数
	 * @param array $condition
	 * @return array
	 */
	
	public function getRuleByCondition($offset = 0, $pageSize = 10, array $condition = array()) {
		$ruleModel = new GiftStarRuleModel();
		$data = $ruleModel->getRuleByCondition($offset, $pageSize, $condition);
		$list = $this->arToArray($data);
		$count=$ruleModel->getRuleCountByCondition($condition);
		$ruleList=array();
		$ruleList['list']=$this->buildDataByIndex($list, 'rule_id');
		$ruleList['count']=$count;
		return $ruleList;
	}
	
	/**
	 * 根据规则Id获取规则
	 * @param array|int $ruleIds  礼物Id
	 * @return array
	 */
	public function getRuleByIds($ruleIds) {
		if (empty($ruleIds)) return $this->setError(Yii::t('common', 'Parameter is empty'), 0);
		$ruleIds = is_array($ruleIds) ? $ruleIds : array($ruleIds);
		$ruleModel = new GiftStarRuleModel();
		$data = $ruleModel->getRuleByIds($ruleIds);
		$rule_list=$this->buildDataByIndex($this->arToArray($data), 'rule_id');
		return $rule_list;
	}
	
	/**
	 * 存储礼物规则
	 * @param array $rule 礼物规则
	 * @return int
	 */
	public function saveRule(array $rule) {
		if (isset($rule['rule_id']) && $rule['rule_id'] <= 0) {
			return $this->setError(Yii::t('common', 'Parameter is empty'), 0);
		}
		if (isset($rule['gift_id']) && $rule['gift_id'] <= 0) {
			return $this->setError(Yii::t('common', 'Parameter is empty'), 0);
		}
		
		
		if(is_array($rule['contention_rule'])){
			$contention_rule=implode(",",$rule['contention_rule']);
		}else{
			$contention_rule=$rule['contention_rule'];
		}
		$rule['contention_rule']= $contention_rule;

		$ruleModel=new GiftStarRuleModel();
		$newRule=$ruleModel->findByPk((int)$rule['rule_id']);
		$newRule->gift_id=$rule['gift_id'];
		$newRule->contention_rule=$rule['contention_rule'];
		$result=$newRule->save();
		
		if($result && $this->isAdminAccessCtl()){
			$op_desc = '更新礼物之星规则设定('.$result.')';
			$this->saveAdminOpLog($op_desc);
		}
		return $result;
	}
	
	/**
	 * 根据条件获取礼物之星礼物图片的分页
	 * @param int $offset 获取页数
	 * @param int $pageSize 页数
	 * @param array $condition
	 * @return array
	 */
	public function getImgByCondition($offset = 0, $pageSize = 10, array $condition = array()) {
		$imgModel = new GiftStarImgModel();
		$data = $imgModel->getImgByCondition($offset, $pageSize, $condition);
		$list = $this->arToArray($data);
		$count=$imgModel->getImgCountByCondition($condition);
		$giftImgList=array();
		$giftImgList['list']=$this->buildDataByIndex($list, 'img_id');
		$giftImgList['count']=$count;
		return $giftImgList;
	}
	
	/**
	 * 根据礼物图片Id获取礼物图片记录
	 * @param array|int $ruleIds  礼物Id
	 * @return array
	 */
	public function getGiftImgByIds($giftImgIds) {
		if (empty($giftImgIds)) return $this->setError(Yii::t('common', 'Parameter is empty'), 0);
		$giftImgIds = is_array($giftImgIds) ? $giftImgIds : array($giftImgIds);
		$imgModel = new GiftStarImgModel();
		$data = $imgModel->getImgByIds($giftImgIds);
		$giftImg_list=$this->buildDataByIndex($this->arToArray($data), 'img_id');
		return $giftImg_list;
	}
	
	/**
	 * 存储礼物特效图片
	 * @param array $giftImg 礼物特效图片
	 * @return int
	 */
	public function saveGiftImg(array $giftImg) {
		if (isset($giftImg['img_id']) && $giftImg['img_id'] <= 0) {
			return $this->setError(Yii::t('common', 'Parameter is empty'), 0);
		}
		if (isset($giftImg['gift_id']) && $giftImg['gift_id'] <= 0) {
			return $this->setError(Yii::t('common', 'Parameter is empty'), 0);
		}
		if (isset($giftImg['order_number']) && $giftImg['order_number'] <= 0) {
			return $this->setError(Yii::t('common', 'Parameter is empty'), 0);
		}	
	
		$imgModel=new GiftStarImgModel();
		if(isset($giftImg['img_id']))
		{
			$newImg=$imgModel->findByPk((int)$giftImg['img_id']);
			if(!empty($giftImg['image']))
			{
				$newImg->image=$giftImg['image'];
			}
			$newImg->order_number=$giftImg['order_number'];
			$newImg->summary=$giftImg['summary'];
			$result=$newImg->save();
		}
		else
		{
			$imgModel->gift_id=$giftImg['gift_id'];
			$imgModel->image=$giftImg['image'];
			$imgModel->order_number=$giftImg['order_number'];
			$imgModel->summary=$giftImg['summary'];
			$result=$imgModel->save();
		}

		if($result && $this->isAdminAccessCtl()){
			$op_desc = '更新礼物之星礼物图片('.$result.')';
			$this->saveAdminOpLog($op_desc);
		}
	
		return $result;
	}
	
	/**
	 * 根据条件获取礼物之星规则说明的分页
	 * @param int $offset 获取页数
	 * @param int $pageSize 页数
	 * @param array $condition
	 * @return array
	 */
	public function getSetByCondition($offset = 0, $pageSize = 10, array $condition = array()) {
		$setModel = new GiftStarSetModel();
		$data = $setModel->getSetByCondition($offset, $pageSize, $condition);
		$list = $this->arToArray($data);
		$count=$setModel->getSetCountByCondition($condition);
		$setList=array();
		$setList['list']=$this->buildDataByIndex($list, 'set_id');
		$setList['count']=$count;
		return $setList;
	}
	
	/**
	 * 存储礼物之星特别说明
	 * @param array $giftStarSet 礼物之星特别说明
	 * @return int
	 */
	public function saveGiftStarSet(array $giftStarSet) {
		if (isset($giftStarSet['set_id']) && $giftStarSet['set_id'] <= 0) {
			return $this->setError(Yii::t('common', 'Parameter is empty'), 0);
		}
	
		$setModel=new GiftStarSetModel();
		$newSet=$setModel->findByPk((int)$giftStarSet['set_id']);
		$newSet->illustration=$giftStarSet['illustration'];
		$result=$newSet->save();

		if($result && $this->isAdminAccessCtl()){
			$op_desc = '更新礼物之星特别说明('.$result.')';
			$this->saveAdminOpLog($op_desc);
		}
	
		return $result;
	}
	
	/**
	 * 根据特别说明Id获取特别说明记录
	 * @param array|int $setIds  特别说明Id
	 * @return array
	 */
	public function getSetByIds($setIds) {
		if (empty($setIds)) return $this->setError(Yii::t('common', 'Parameter is empty'), 0);
		$setIds = is_array($setIds) ? $setIds : array($setIds);
		$setModel = new GiftStarSetModel();
		$data = $setModel->getSetByIds($setIds);
		$set_list=$this->buildDataByIndex($this->arToArray($data), 'set_id');
		return $set_list;
	}
}