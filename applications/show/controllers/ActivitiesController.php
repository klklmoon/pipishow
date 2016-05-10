<?php
/**
 * @author zhangzhifan <zhangzhifan@pipi.cn> 2010-11-2
 * @link http://show.pipi.cn
 * @copyright Copyright &copy; 2003-2010 show.pipi.cn
 * @license
 */
class ActivitiesController extends PipiController {
	
	/**
	 * @var array 允许的操作
	 */
	public $allowOp = array(
			//快乐星期六
			'happystaturday'=>array(),
			//首次充值送礼
			'firstchargegifts'=>array('collectGiftsOne','collectGiftsTwo'),
			//啤酒节活动
			'beer'	=> array(),
			//守护天使
			'guardangel'=>array('startGuard','changeLuckDotey','lookGuardList','lookDoteyRank','lookUserGuardInfo'),
			//七夕节
			'qixi'	=> array(),
			'happybirthday'=>array('DoteyCharmPoints','DoteyMedal','UserDedication','UserMedal'),
			//每日幸运礼物
			'luckstar'=>array(),
			//万圣节
			'halloween'=>array('userExchange','doteyExchange'),
			//2周年庆
			'2years' => array('receive'),
		);
	
	/**
	 * @var string 当前操作
	 */
	public $op;
	
	/**
	 * @var boolean 是否是Ajax请求
	 */
	public $isAjax;
	
	public $pageSize = 20;
	
	public $offset;
	
	/**
	 * @var int page lable
	 */
	public $p;
	
	public function beforeAction($action){
		if (parent::beforeAction($action)){
			$this->op = Yii::app()->request->getParam('op');
			$this->isAjax = Yii::app()->request->isAjaxRequest;
			if(!($this->p = Yii::app()->request->getParam('page'))){
				$this->p = 1;
			}
			$this->offset = ($this->p -1)*$this->pageSize;
			
			$actionName = strtolower($action->getId());
			if ($this->op && (!isset($this->allowOp[$actionName]) || !in_array($this->op, $this->allowOp[$actionName]))){
				throw new CHttpException(405);
			}
		}
		return true;
	}
	
	/**
	 * 活动-快乐星期六
	 * @author zhang zhi fang
	 */
	public function actionHappySaturday(){
		$this->setPageTitle('皮皮乐天-活动-快乐星期六');
		$this->cs->registerCssFile($this->pipiFrontPath.'/css/activities/happysaturday.css?token='.$this->hash);
		$this->render('happysaturday');
	}
	
	/**
	 * 活动-首次充值送礼
	 * @author su peng
	 */
	public function actionFirstChargeGifts(){
		$type = 0;
		if ($this->op == 'collectGiftsOne')
			$type = FirstChargeGiftsService::ACTIVITY_TYPE_ONE;#获取礼包一
		if ($this->op == 'collectGiftsTwo')
			$type = FirstChargeGiftsService::ACTIVITY_TYPE_TWO;#获取礼包二
		if($type > 0){
			$result=array();
			$service = new FirstChargeGiftsService();
			if(!$this->isLogin){
				$result['message']='请先登录才能领取礼包';
			}else{
				$uid = (int)Yii::app()->user->id;
				$result['message'] = $service->collectGifts($uid, $type);
			}
			exit(json_encode($result));
		}else{
			$this->setPageTitle('皮皮乐天-活动-'.FirstChargeGiftsService::ACTIVITY_NAME);
			$this->cs->registerCssFile($this->pipiFrontPath.'/css/activities/firstchargegifts.css?token='.$this->hash);
			$this->render('firstchargegifts');
		}
	}
	
	/**
	 * 啤酒节活动
	 * @author hexin
	 */
	public function actionBeer(){
		$beerService = new BeerService();
		$living = $beerService->getLiving($this->isLogin);
		$top = $beerService->getTop();
		
		$this->setPageTitle('皮皮乐天-活动-啤酒节活动');
		$this->cs->registerCssFile($this->pipiFrontPath.'/css/activities/beer.css?token='.$this->hash);
		$this->render('beer', array(
			'living'	=> array_merge($living, array('wait' => null)),
			'wait'		=> array_merge($living, array('living' => null)),
			'dotey_rank'=> $top['dotey_rank'],
			'user_rank'	=> $top['user_rank']
		));
	}
	
	/**
	 * 活动-守护天使
	 * @author supeng
	 */
	public function actionGuardAngel(){
		$service = new GuardAngelService($this);
		//开始守护
		if ($this->op == 'startGuard'){
			$dotey_uid = (int)Yii::app()->request->getParam('dotey_uid',null);
			$uid = (int)Yii::app()->user->id;
			exit(json_encode($service->startGuard($dotey_uid,$uid)));
		}
		
		//获取幸运主播
		if ($this->op == 'changeLuckDotey'){
			exit(json_encode($service->getLuckDoteyList()));
		}
		
		//查看已经守护的主播
		if ($this->op == 'lookGuardList'){
			$uid = (int)Yii::app()->user->id;
			exit(json_encode($service->lookGuardList($uid)));
		}
		$this->setPageTitle('皮皮乐天-活动-'.GuardAngelService::ACTIVITY_NAME);
		$this->cs->registerCssFile($this->pipiFrontPath.'/css/activities/guardangel.css?token='.$this->hash);
		$render = array(
				'luckList' => $service->getLuckDoteyList(),#幸运主播
				'allDoteyRank' => $service->getAllDoteyGuardRank('ar'),
				'newDoteyRank' => $service->getAllDoteyGuardRank('nr'),
				'allUserRank' => $service->getAllUserGuardRank('ar'),
				'newUserRank' => $service->getAllUserGuardRank('nr'),
			);
		$this->render('guardangel',$render);
	}
	
	public function actionHappSatuReceOrdiGiftBag()
	{
		$result=array();
		
		if($this->isLogin)
		{
			$happySaturdayService=new HappySaturdayService();
			$uid = (int)Yii::app()->user->id;
			//当前用户领取快乐星期六普通礼包
			$result['flag']=$happySaturdayService->receiveOrdinaryGiftBag($uid);
			if($result['flag']==1)
			{
				$result['message']="普通礼包领取成功";
			}
			elseif($result['flag']==-2)
			{
				$result['message']="今天不是星期六，不能领取普通礼包";
			}
			elseif($result['flag']==-3)
			{
				$result['message']="充值不满50个皮蛋，不能领取普通礼包";
			}
			elseif($result['flag']==-4)
			{
				$result['message']="普通礼包已经领取过了，不能再次领取普通礼包";
			}
			else
			{
				$result['message']="领取失败";
			}							
		}
		else
		{
			$result['flag']=-1;
			$result['message']="您还没有登录皮皮乐天";
		}
		echo(json_encode($result));
	}
	
	public function actionHappSatuReceAdvaGiftBag()
	{
		$result=array();
		if($this->isLogin)
		{
			$happySaturdayService=new HappySaturdayService();
			$uid = (int)Yii::app()->user->id;
			//当前用户领取快乐星期六高级礼包
			$result['flag']=$happySaturdayService->receiveAdvancedGiftBag($uid);
			if($result['flag']==1)
			{
				$result['message']="升级版礼包领取成功";
			}
			elseif($result['flag']==-2)
			{
				$result['message']="今天不是星期六，不能领取升级版礼包";
			}
			elseif($result['flag']==-3)
			{
				$result['message']="充值不满50个皮蛋，不能领取升级版礼包";
			}
			elseif($result['flag']==-4)
			{
				$result['message']="升级版礼包已经领取过了，不能再次领取升级版礼包";
			}
			elseif($result['flag']==-6)
			{
				$result['message']="周一至周五充值不满500个皮蛋，不能领取升级版礼包";
			}			
			else
			{
				$result['message']="领取失败";
			}
		}
		else
		{
			$result['flag']=-1;
			$result['message']="您还没有登录皮皮乐天";
		}
		echo(json_encode($result));
	}
	
	//礼物之星
	public function actionGiftStar()
	{
		$clientScript = Yii::app()->getClientScript();
		$clientScript->registerCssFile($this->pipiFrontPath.'/css/activities/giftstar.css?token='.$this->hash);
		//取得本周指定礼物
		$giftStarService=new GiftStarService();
		$weekId=$giftStarService->getThisWeekId();
		$giftIds=$giftStarService->getGiftStarGiftsByWeekId($weekId);
		$giftList=$giftStarService->getGiftUrlList($giftIds);
		$this->viewer['giftList'] = $giftList;
		//取得本周特别说明
		$thisWeekSet=$giftStarService->getIllustrationByWeekId($weekId);
		$this->viewer['thisIllustration']=$thisWeekSet['illustration'];
		if($weekId == 1){
			$this->viewer['thisIllustration']= "
1、“爱心”和“板砖”仅限等级5钻（含）以下的主播争夺<br/>
2、“可乐”和“加油”仅限等级6钻（含）至皇冠（不含）之间的主播争夺<br/>
3、“我爱你”和“抱一抱”仅限等级皇冠以上的主播争夺";
		}
		
		//上周礼物之星
		$lastWeekId=$weekId-1;
		$lastGiftStarList=$giftStarService->getFirstDoteysByWeekId($lastWeekId);
		$this->viewer['lastGiftStarList'] = $lastGiftStarList;
		
		//本周单项礼物排行榜
		$giftStarRankService=new GiftStarRankService();
		$thisWeekRankList=$giftStarRankService->getWeekRankWeb($weekId);
		//$thisWeekRankList=$giftStarRankService->getRankByWeekId($weekId);
		$this->viewer['thisWeekRankList'] = $thisWeekRankList;
		
		$this->render('giftstar');
	}
	
	public function actionQixi()
	{
		$qixiService = new QixiService();
		$living = $qixiService->getLiving($this->isLogin);
		$top = $qixiService->getTop();
		
		$this->setPageTitle('皮皮乐天-活动-七夕节活动');
		$this->cs->registerCssFile($this->pipiFrontPath.'/css/activities/qixi.css?token='.$this->hash);
		$this->render('qixi', array(
				'living'	=> array_merge($living, array('wait' => null)),
				'wait'		=> array_merge($living, array('living' => null)),
				'dotey_rank'=> $top['dotey_rank'],
				'user_rank'	=> $top['user_rank']
		));
	}
	
	//生日快乐
	public function actionHappyBirthday()
	{
		$happyBirthdayService=new HappyBirthdayService();
		if ($this->op == 'DoteyCharmPoints')						//奖励主播生日当天魅力点,最多能领3份
		{
			$uid = (int)Yii::app()->user->id;
			if(!empty($uid) && $uid>0)
			{
				$result=$happyBirthdayService->rewardDoteyCharmPoints($uid);
			}
			else
			{
				$result=-2;
			}
			exit(json_encode($result));
		}
		elseif($this->op == 'DoteyMedal')						//当月收到生日魅力值前三名的主播，均可在次月1日领取1个生日公主勋章
		{
			$uid = (int)Yii::app()->user->id;
			if(!empty($uid) && $uid>0)
			{
				$result=$happyBirthdayService->rewardDoteyMedal($uid);
			}
			else
			{
				$result=-2;
			}
			exit(json_encode($result));
		}
		elseif($this->op == 'UserDedication')						//主播生日当天，用户每送出一套生日套礼，即可领取1份奖励（不限领取次数）
		{
			$uid = (int)Yii::app()->user->id;
			if(!empty($uid) && $uid>0)
			{
				$result=$happyBirthdayService->rewardUserDedication($uid);
			}
			else
			{
				$result=-2;
			}
			exit(json_encode($result));
		}
		elseif($this->op == 'UserMedal')						//当月生日贡献值前三名的用户，均可在次月1日领取1个生日王子勋章
		{
			$uid = (int)Yii::app()->user->id;
			if(!empty($uid) && $uid>0)
			{
				$result=$happyBirthdayService->rewardUserMedal($uid);
			}
			else
			{
				$result=-2;
			}
			exit(json_encode($result));
		}
		else
		{	
			$otherRedisModel=new OtherRedisModel();
			$happy_birthday_page=$otherRedisModel->getHappyBirthdayPageData();
			$todayBirthdayDoteys=$happy_birthday_page['todayBirthdayDoteys'];
			$honorRank=$happy_birthday_page['honorRank'];
			$thisMonthRank=$happy_birthday_page['thisMonthRank'];
			$activityGiftList=$happy_birthday_page['activityGiftList'];
			$batchPrice=$happy_birthday_page['batchPrice'];

			if(!isset($todayBirthdayDoteys) || empty($todayBirthdayDoteys))
				$todayBirthdayDoteys=$happyBirthdayService->getTodayBirthdayDoteys();
			
			if(!isset($honorRank) || empty($honorRank))
				$honorRank=$happyBirthdayService->getHonorRank();
			
			if(!isset($thisMonthRank) || empty($thisMonthRank))
				$thisMonthRank=$happyBirthdayService->getThisMonthRank();
			
			if(!isset($activityGiftList) || empty($activityGiftList))
				$activityGiftList=$happyBirthdayService->getActivityGiftList();
			
			if(!isset($batchPrice) || empty($batchPrice))
				$batchPrice=$happyBirthdayService->getBatchPrice();
			
			$this->setPageTitle('皮皮乐天-活动-生日快乐');
			$this->cs->registerCssFile($this->pipiFrontPath.'/css/common/propbox.css?token='.$this->hash,'all');
			$this->cs->registerCssFile($this->pipiFrontPath.'/css/activities/happybirthday.css?token='.$this->hash);
			$this->render('happybirthday',array(
				'todayBirthdayDoteys'=>$todayBirthdayDoteys,
				'honorRank'=>$honorRank,
				'thisMonthRank'=>$thisMonthRank,
				'activityGiftList'=>$activityGiftList,
				'batchPrice'=>$batchPrice
			));
		}
	}
	
	//本月生日主播分页
	public function actionPageMonthBirthdayDoteys()
	{
		$happyBirthdayService=new HappyBirthdayService();
		$otherRedisModel=new OtherRedisModel();
		$happy_birthday_page=$otherRedisModel->getHappyBirthdayPageData();
		$activityGiftList=$happy_birthday_page['activityGiftList'];
		$monthDoteyList=$happy_birthday_page['monthDoteyList'];
		
		if(!isset($activityGiftList) || empty($activityGiftList))
			$activityGiftList=$happyBirthdayService->getActivityGiftList();
		//获取本月生日主播分页
		if(!isset($monthDoteyList) || empty($monthDoteyList))
			$monthDoteyList=$happyBirthdayService->getThisMonthBirthdayDoteys();
		$counts=count($monthDoteyList);
		
		$pager = new CPagination($counts);
		$pager->pageSize= 5;
		$starRow=($this->p-1)*$pager->pageSize;
		$endRow=($starRow+$pager->pageSize)<$counts?($starRow+$pager->pageSize):$counts;
		
		$thisMonthBirthdayDoteys=array();
		for ($i=$starRow;$i<$endRow;$i++)
		{
			$thisMonthBirthdayDoteys[]=$monthDoteyList[$i];
		}
		$this->renderPartial('pagemonthbirthdaydoteys',
			array('thisMonthBirthdayDoteys'=>$thisMonthBirthdayDoteys,
			'activityGiftList'=>$activityGiftList,
			'pager'=>$pager	
		));
	}
	
	//生日套礼购买
	public function actionBuyBatchGift(){
		$buyNum = Yii::app()->request->getParam('buyNum');
		$happyBirthdayService=new HappyBirthdayService();
		$birthdayGiftList=$happyBirthdayService->birthdayGiftList;

		if($buyNum<= 0){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('common','Parameters are wrong'))));
		}
		if(!$this->isLogin){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('user','You are not logged'))));
		}
		$uid=Yii::app()->user->id;
		$consumeService=new ConsumeService();
		$consume = $consumeService->getConsumesByUids($uid);

		$userService=new UserService();
		$userInfo=$userService->getUserFrontsAttributeByCondition($uid,true);
		//获取套礼单价
		$batchPrice=$happyBirthdayService->getBatchPrice();

		if($batchPrice<=0)
		{
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('common','Parameters are wrong'))));
		}
		
		if($consume[$uid]['pipiegg']-$consume[$uid]['freeze_pipiegg']-$batchPrice * $buyNum<0||$consume[$uid]['pipiegg']-$consume[$uid]['freeze_pipiegg']<0){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('common','Pipiegg not enough'))));
		}
		$giftBagService=new GiftBagService();
		$result=true;
		foreach($birthdayGiftList as $gift_id)
		{
			$result=$result && $giftBagService->buyShopGift($uid,$gift_id,$buyNum);
		}
		if(!$result){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('gift','Gift buy failed'))));
		}
		exit(json_encode(array('flag'=>1,'message'=>Yii::t('gift','Gift buy successed'))));
	}
	
	public function actionMonthHonorRank()
	{
		$happyBirthdayService=new HappyBirthdayService();
		$otherRedisModel=new OtherRedisModel();
		$happy_birthday_page=$otherRedisModel->getHappyBirthdayPageData();
		$monthHonorRankData=$happy_birthday_page['monthHonorRankData'];
		
		if(!isset($monthHonorRankData) || empty($monthHonorRankData))
			$monthHonorRankData=$happyBirthdayService->getAllMonthHonorRank();

		$this->setPageTitle('皮皮乐天-活动-生日快乐');
		$this->cs->registerCssFile($this->pipiFrontPath.'/css/activities/happybirthday.css?token='.$this->hash);
		$this->render('monthhonorrank',
			array(
				'monthHonorRankData'=>$monthHonorRankData
		));
	}
	
	//购买套礼中单项
	public function actionBuyGift(){
		$gift_id = Yii::app()->request->getParam('gift_id');
		$buyNum = Yii::app()->request->getParam('buyNum');
		if($gift_id <= 0 || $buyNum<= 0){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('common','Parameters are wrong'))));
		}
		if(!$this->isLogin){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('user','You are not logged'))));
		}
		$uid=Yii::app()->user->id;
		$consumeService=new ConsumeService();
		$consume = $consumeService->getConsumesByUids($uid);
		$giftService=new GiftService();
		$gift=$giftService->getGiftByIds($gift_id);
		if(empty($gift)){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('common','Parameters are wrong'))));
		}
		$userService=new UserService();
		$userInfo=$userService->getUserFrontsAttributeByCondition($uid,true);

		if($gift[$gift_id]['buy_limit']==1){
			if($gift[$gift_id]['sell_nums']-$buyNum<0){
				exit(json_encode(array('flag'=>0,'message'=>Yii::t('giftBag','Restrictions on the purchase'))));
			}
		}
		if($consume[$uid]['pipiegg']-$consume[$uid]['freeze_pipiegg']-$gift[$gift_id]['pipiegg'] * $buyNum<0||$consume[$uid]['pipiegg']-$consume[$uid]['freeze_pipiegg']<0){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('common','Pipiegg not enough'))));
		}
		$giftBagService=new GiftBagService();
		$result=$giftBagService->buyShopGift($uid,$gift_id,$buyNum);
		if(!$result){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('gift','Gift buy failed'))));
		}
		exit(json_encode(array('flag'=>1,'message'=>Yii::t('gift','Gift buy successed'))));
	}
	//生日快乐结尾
	
	public function actionTuesDay(){
		$this->setPageTitle('皮皮乐天-活动-周二送礼享福利');
		$this->cs->registerCssFile($this->pipiFrontPath.'/css/activities/tuesday.css?token='.$this->hash);
		$this->render('tuesday');
	}
	
	public function actionDoTuesDay(){
		$timestamp = time();
		$week = date('w',$timestamp);
		//判断是否是星期二
		if($week != 2){
			exit(json_encode(array('flag'=>'fail','message'=>'此活动只能在每周星期二参加')));
		}
		
		if(!$this->isLogin){
			exit(json_encode(array('flag'=>'fail','message'=>Yii::t('user','You are not logged'))));
		}
		
		$uid=Yii::app()->user->id;
		$starTime =  strtotime(date('Y-m-d',$timestamp).' 00:00:00');
		$endTime = strtotime(date('Y-m-d',$timestamp).' 23:59:59');
		
		$giftService = new GiftService();
		$pipieggs = $giftService->sumUserConsumePipieggsByTime($uid,$starTime,$endTime);
		$changeRelation = Yii::app()->params->change_relation;
		$toEgg = isset($changeRelation['rmb_to_pipiegg'])?$changeRelation['rmb_to_pipiegg']:1;
		$egg = 500*$toEgg;
		if($pipieggs < $egg ){
			exit(json_encode(array('flag'=>'fail','message'=>'您今天消费还没有满'.$egg.'皮蛋')));
		}
		
		$happyTudesdayModel = new HappyTuesdayActivityModel();
		if($happyTudesdayModel->alerayJoin($uid,$starTime,$endTime)){
			exit(json_encode(array('flag'=>'fail','message'=>'您已经参加过本周活动，请下周二再来')));
		}
		
		$propsSer = new PropsService();
		$userPropsSer = new UserPropsService();
		$flayScreen = $propsSer->getPropsByEnName('flyscreen');
		$commonLabel = $propsSer->getPropsByEnName('common_label');
		$highLable = $propsSer->getPropsByEnName('high_label');
		
		$propsIds = array();
		if($flayScreen){
			$propsIds[] = $flayScreen['prop_id'];
		}
		if($commonLabel){
			$propsIds[] = $commonLabel['prop_id'];
		}
		if($highLable){
			$propsIds[] = $highLable['prop_id'];
		}
		$props = $propsSer->getPropsByIds($propsIds,true,true);
		//赠送普通飞屏
		if($flayScreen){
			$flayNum = 20;
			$_flayProps = $props[$flayScreen['prop_id']];
			
			$frecords['uid'] = $uid;
			$frecords['cat_id'] = $_flayProps['cat_id'];
			$frecords['prop_id'] = $_flayProps['prop_id'];
			$frecords['pipiegg'] = $flayNum*$_flayProps['pipiegg'];
			$frecords['dedication'] = $flayNum*$_flayProps['dedication'];
			$frecords['egg_points'] = $flayNum*$_flayProps['egg_points'];
			$frecords['charm'] = $flayNum*$_flayProps['charm'];
			$frecords['charm_points'] = $flayNum*$_flayProps['charm_points'];
			$frecords['vtime'] = $_flayPropsAttr['flyscreen_timeout']['value'];
			$frecords['info'] = '周二赠礼('.$_flayProps['name'].'*'.$flayNum.')';
			$frecords['amount'] = $flayNum;
			$frecords['source'] = PROPSRECORDS_SOURCE_ACTIVITY;
			if($frecordSid = $userPropsSer->saveUserPropsRecords($frecords)){
				$flayBag['uid'] = $uid;
				$flayBag['target_id'] = 0;
				$flayBag['prop_id'] = $_flayProps['prop_id'];
				$flayBag['cat_id'] = $_flayProps['cat_id'];
				$flayBag['record_sid'] = $frecordSid;
				$flayBag['s_num'] = $flayNum;
				$flayBag['valid_time'] = $_flayPropsAttr['flyscreen_timeout']['value'];
				$userPropsSer->saveUserPropsBag($flayBag);
				
			}
		}
		
		//赠送普通贴条
		if($commonLabel){
			$cLabelNum = 20;
			$_cLabelProps = $props[$commonLabel['prop_id']];
			$_flayPropsAttr = $propsSer->buildDataByIndex($_cLabelProps['attribute'],'attr_enname');
			
					
			$crecords['uid'] = $uid;
			$crecords['cat_id'] = $_cLabelProps['cat_id'];
			$crecords['prop_id'] = $_cLabelProps['prop_id'];
			$crecords['pipiegg'] = $cLabelNum*$_cLabelProps['pipiegg'];
			$crecords['dedication'] = $cLabelNum*$_cLabelProps['dedication'];
			$crecords['egg_points'] = $cLabelNum*$_cLabelProps['egg_points'];
			$crecords['charm'] = $cLabelNum*$_cLabelProps['charm'];
			$crecords['charm_points'] = $cLabelNum*$_cLabelProps['charm_points'];
			$crecords['vtime'] = 0;
			$crecords['info'] = '周二赠礼('.$_cLabelProps['name'].'*'.$cLabelNum.')';
			$crecords['amount'] = $cLabelNum;
			$crecords['source'] = PROPSRECORDS_SOURCE_ACTIVITY;
			if($crecordSid = $userPropsSer->saveUserPropsRecords($crecords)){
				$cLableBag['uid'] = $uid;
				$cLableBag['target_id'] = 0;
				$cLableBag['prop_id'] = $_cLabelProps['prop_id'];
				$cLableBag['cat_id'] = $_cLabelProps['cat_id'];
				$cLableBag['record_sid'] = $crecordSid;
				$cLableBag['s_num'] = $cLabelNum;
				$cLableBag['valid_time'] = 0;
				$userPropsSer->saveUserPropsBag($cLableBag);		
			}
		}
		//赠送高级贴条
		if($highLable){
			$hLabelNum = 20;
			$_hLabelProps = $props[$highLable['prop_id']];
			$_flayPropsAttr = $propsSer->buildDataByIndex($_cLabelProps['attribute'],'attr_enname');
			
					
			$hrecords['uid'] = $uid;
			$hrecords['cat_id'] = $_hLabelProps['cat_id'];
			$hrecords['prop_id'] = $_hLabelProps['prop_id'];
			$hrecords['pipiegg'] = $hLabelNum*$_hLabelProps['pipiegg'];
			$hrecords['dedication'] = $hLabelNum*$_hLabelProps['dedication'];
			$hrecords['egg_points'] = $hLabelNum*$_hLabelProps['egg_points'];
			$hrecords['charm'] = $hLabelNum*$_hLabelProps['charm'];
			$hrecords['charm_points'] = $hLabelNum*$_hLabelProps['charm_points'];
			$hrecords['vtime'] = 0;
			$hrecords['info'] = '周二赠礼('.$_hLabelProps['name'].'*'.$hLabelNum.')';
			$hrecords['amount'] = $hLabelNum;
			$hrecords['source'] = PROPSRECORDS_SOURCE_ACTIVITY;
			if($crecordSid = $userPropsSer->saveUserPropsRecords($hrecords)){
				$hLableBag['uid'] = $uid;
				$hLableBag['target_id'] = 0;
				$hLableBag['prop_id'] = $_hLabelProps['prop_id'];
				$hLableBag['cat_id'] = $_hLabelProps['cat_id'];
				$hLableBag['record_sid'] = $crecordSid;
				$hLableBag['s_num'] = $hLabelNum;
				$hLableBag['valid_time'] = 0;
				$userPropsSer->saveUserPropsBag($hLableBag);		
			}
		}
		$happyTudesdayModel->uid = $uid;
		$happyTudesdayModel->create_time = $timestamp;
		$happyTudesdayModel->pipiegg = $pipieggs;
		$happyTudesdayModel->save();
		exit(json_encode(array('flag'=>'success','message'=>'领取成功')));
	}
	
	/**
	 * 活动-每日幸运星
	 * @author leiwei
	 */
	public function actionLuckStar(){
		$this->setPageTitle('皮皮乐天-活动-每日幸运星');
		$contrller = Yii::app()->getController();
		$clientScript = Yii::app()->getClientScript();
		$staticPath = $contrller->pipiFrontPath;
		$clientScript->registerCssFile($staticPath.'/css/activities/luckgift.css?token='.$contrller->hash);
		
		//每日幸运星数据
		$luckGiftService=new LuckyGiftService();
		$luckStar=$luckGiftService->getLuckStar();
		$this->render('luckstar',array('luckStar'=>$luckStar));
	}
	
	public function actionGetAward(){
		//活动配置
		$changeRelation = Yii::app()->params->change_relation;
		$toEgg = isset($changeRelation['rmb_to_pipiegg'])?$changeRelation['rmb_to_pipiegg']:1;
		$egg = 200*$toEgg;
		$luckStarConfig=array('gift_id'=>182,  //幸运星Id
			'type'=>3,		  //奖励类型
			'multiple'=>500,  //倍数
			'award'=>$egg	  //奖励皮蛋数
		);
		if($this->isLogin){
			$uid=Yii::app()->user->id;
			$today=date('Y-m-d');
			$luckGiftService=new LuckyGiftService();
			$todayCondition['target_id']=$luckStarConfig['gift_id'];
			$todayCondition['type']=$luckStarConfig['type'];
			$todayCondition['num']=$luckStarConfig['multiple'];
			if(date('H')<22){
				$todayCondition['stime']=strtotime($today.' 22:00:00')-86400*2;
				$todayCondition['etime']=strtotime($today.' 22:00:00')-86400;
			}else{
				$todayCondition['stime']=strtotime($today.' 22:00:00')-86400;
				$todayCondition['etime']=strtotime($today.' 22:00:00');
			}
			
			$todayStar=$luckGiftService->getUserAwardRecords($todayCondition);
			
			if(!$todayStar){
				exit(json_encode(array('flag'=>0,'message'=>'抱歉，您不能领奖')));
			}
			if($todayStar['uid']!=$uid){
				exit(json_encode(array('flag'=>0,'message'=>'抱歉，您不能领奖')));
			}
			$consumeService=new ConsumeService();
			$condition['uid']=$uid;
			$condition['record_sid']=$todayStar['record_sid'];
			$condition['source']=SOURCE_ACTIVITY;
			$condition['sub_source']=SUBSOURCE_LUCK_STAR;
			$pipiRecord=$consumeService->getLuckStarPipiRecord($condition);
			
			if(!empty($pipiRecord)){
				exit(json_encode(array('flag'=>0,'message'=>'抱歉，您不能领奖')));
			}
			
			if($consumeService->addEggs($uid,$luckStarConfig['award'])<=0){
				exit(json_encode(array('flag'=>0,'message'=>'系统繁忙，稍后领取')));
			}
			$consumeService->saveUserConsumeAttribute(array('uid'=>$uid,'pipiegg'=>$luckStarConfig['award']));
			$pipieggRecords['uid'] = $uid;
			$pipieggRecords['from_target_id']=$todayStar['target_id'];
			$pipieggRecords['record_sid'] = $todayStar['record_sid'];
			$pipieggRecords['pipiegg'] = $luckStarConfig['award'];
			$pipieggRecords['source']=SOURCE_ACTIVITY;
			$pipieggRecords['sub_source']=SUBSOURCE_LUCK_STAR;
			$pipieggRecords['extra']='每日幸运星奖励领取';
			if($consumeService->saveUserPipiEggRecords($pipieggRecords, true)<=0){
				$filename = DATA_PATH.'runtimes/luckStar.txt';
				error_log(date("Y-m-d H:i:s").'每日幸运星皮蛋记录存储异常：'.json_encode($pipieggRecords)."\n\r",3,$filename);
			}
			exit(json_encode(array('flag'=>1,'message'=>'<strong>'.$luckStarConfig['award'].'皮蛋</strong><br/>恭喜，奖励领取成功')));
		}
	}
	
	//2013中秋活动
	public function actionMoonFestival(){
		$this->setPageTitle('皮皮乐天-中秋节');
		$this->cs->registerCssFile($this->pipiFrontPath.'/css/activities/zhongqiu.css?token='.$this->hash);
		$moonFestivalService=new MoonFestivalService();
		$this->render('moonfestival',
			array('moonfestival'=>$moonFestivalService->getActivityPageData(),
				'end_time'=>MoonFestivalService::END_TIME,
				'c_time'=>date("Y-m-d H:i:s"))
		);
	}
	
	//2013国庆活动
	public function actionNationalDay(){
		$this->setPageTitle('皮皮乐天-国庆节');
		$this->cs->registerCssFile($this->pipiFrontPath.'/css/activities/guoqing.css?token='.$this->hash);
		$nationalDayService=new NationalDayService();

		$this->render('nationalday',
			array('nationalday'=>$nationalDayService->getActivityPageData(),
				'time_test'=>(time()<=strtotime(NationalDayService::END_TIME))?1:2
			)
		);
	}
	
	//2013国庆活动充值页
	public function actionNationalDayRecharge(){
		$this->setPageTitle('皮皮乐天-国庆节');
		$this->cs->registerCssFile($this->pipiFrontPath.'/css/activities/guoqingchongzhi.css?token='.$this->hash);
		$this->render('nationaldayrecharge');
	}
	
	//2013万圣节
	public function actionHalloween(){
		$halloweenService=new HalloweenService();
		$setmeal_id = Yii::app()->request->getParam('setmeal_id');
		if ($this->op == 'userExchange')						//普通用户套餐兑换
		{
			$uid = (int)Yii::app()->user->id;
			if(!empty($uid) && $uid>0)
			{
				$result=$halloweenService->userExchange($uid,$setmeal_id);
			}
			else
			{
				$result=-1;
			}
			exit(json_encode($result));
		}
		else if($this->op == 'doteyExchange')
		{
			$dotey_id = (int)Yii::app()->user->id;
			if(!empty($dotey_id) && $dotey_id>0)
			{
				$doteyInfo=$this->userService->getUserFrontsAttributeByCondition($dotey_id,true,true);
				$isDotey = $this->userService->hasBit((int)$doteyInfo['ut'],USER_TYPE_DOTEY);
				if($isDotey)
					$result=$halloweenService->doteyExchange($dotey_id,$setmeal_id);
				else
					$result=-2;
			}
			else
			{
				$result=-1;
			}
			exit(json_encode($result));
		}
		else 
		{	
			$startTime=strtotime(HalloweenService::START_TIME);
			$endTime=strtotime(HalloweenService::END_TIME);
			$uid = (int)Yii::app()->user->id;
			if(!empty($uid) && $uid>0)
			{
				$doteyInfo=$this->userService->getUserFrontsAttributeByCondition($uid,true,true);
				$isDotey = $this->userService->hasBit((int)$doteyInfo['ut'],USER_TYPE_DOTEY);
				$user_type=isset($isDotey) && $isDotey?1:0;
				$userHalloweenInfo=$halloweenService->getDoteyAndUserGiftInfo($uid,$user_type);
			}
			else
				$userHalloweenInfo=array();

			$this->setPageTitle('皮皮乐天-万圣节');
			$this->cs->registerCssFile($this->pipiFrontPath.'/css/activities/halloween.css?token='.$this->hash);
			if(empty($userHalloweenInfo))
				$this->render('halloween',
					array('time_test'=>(time()>=strtotime(HalloweenService::START_TIME)) && (time()<=strtotime(HalloweenService::END_TIME))?1:2)
				);
			else 
				$this->render('halloween',array(
					'time_test'=>$halloweenService->checkActivityTime(),
					'userInfo'=>$userHalloweenInfo['userInfo'],
					'doteyInfo'=>$userHalloweenInfo['doteyInfo'])
				);
		}
	}
	
	public function actionDoubleEleven(){
		$endTime=strtotime(date('2013-11-11 23:59:59'));
		$endFlag=0;
		if(time()>$endTime){
			$endFlag=1;
		}
		$this->setPageTitle('双11限时充值抢购-皮皮乐天');
		$this->cs->registerCssFile($this->pipiFrontPath.'/css/activities/reset.css?token='.$this->hash);
		$this->cs->registerCssFile($this->pipiFrontPath.'/css/activities/double11.css?token='.$this->hash);
		$this->render('eleven',array('endFlag'=>$endFlag));
	}
	
	/**
	 * 2周年庆活动
	 */
	public function action2Years(){
		$service = new YearsService();
		if($this->op == 'receive'){
			$uid = Yii::app()->user->id;
			$id = intval(Yii::app()->request->getPost('id'));
			if($uid > 0){
				if($id < 0 || $id > 13){
					$this->renderToJson(-2, '该礼包不存在');
				}
				if($service->receive($uid, $id)){
					if($id < 3) $message = '恭喜，您已领取该礼包，赶紧把道具用起来吧！';
					elseif($id < 8) $message = '恭喜，您已领取该礼包，请及时联系首页客服领取靓号奖励。';
					else $message = '恭喜，您已成功领取了魅力值！';
					$this->renderToJson(1, $message);
				}
				$error = $service->getNotice();
				if(is_array($error)){
					$temp = $error;
					$error = '';
					foreach($temp as $e){
						$error .="\n$e";
					}
				}
				$this->renderToJson(0, $error);
			}else{
				$this->renderToJson(-1, '请先登陆');
			}
		}else{
			$this->setPageTitle('秀场周年庆-感恩送大礼-皮皮乐天');
			$this->cs->registerCssFile($this->pipiFrontPath.'/css/activities/2years.css?token='.$this->hash);
			$this->render('2years', array('startTime' => YearsService::START_TIME, 'endTime' => YearsService::END_TIME, 'packs' => YearsService::$packs));
		}
	}
	
	//2013国庆活动
	public function actionWarmWinter(){
		$this->setPageTitle('皮皮乐天-温暖一冬');
		$this->cs->registerCssFile($this->pipiFrontPath.'/css/activities/warmwinter.css?token='.$this->hash);
		$warmWinterService=new WarmWinterService();
	
		$this->render('warmwinter',
			array('warmwinter'=>$warmWinterService->getActivityPageData(),
				'time_test'=>(time()<=strtotime(WarmWinterService::END_TIME))?1:2
			)
		);
	}
	
	//圣诞节
	public function actionChristmas(){
		$this->setPageTitle('皮皮乐天-圣诞狂欢节');
		$this->cs->registerCssFile($this->pipiFrontPath.'/css/activities/christmas.css?token='.$this->hash);
		$service=new ChristmasService();
	
		$this->render('christmas',
			array(
				'top'=>$service->getActivityPageData(),
				'time_start'=>(time()<strtotime(ChristmasService::START_TIME))?0:1,
				'time_end'=>(time()<=strtotime(ChristmasService::END_TIME))?1:0,
				'position' => Yii::app()->request->getParam('pos', '')
			)
		);
	}
	
	//马年充值活动
	public function actionRecharge(){
		$this->setPageTitle('皮皮乐天-马上有礼');
		$this->cs->registerCssFile($this->pipiFrontPath.'/css/activities/recharge.css?token='.$this->hash);
		$this->render('recharge');
	}
	
	//安卓下载页
	public function actionAndroidPage()
	{
		$this->setPageTitle('皮皮乐天-安卓版下载');
		$this->cs->registerCssFile($this->pipiFrontPath.'/css/activities/androidDown.css?token='.$this->hash);
		$this->render('androidpage');
	}
	
	//女神争夺战
	public function actionBattleTop(){
		$this->setPageTitle('皮皮乐天-女神争夺战');
		$this->cs->registerCssFile($this->pipiFrontPath.'/css/activities/nvshen.css?token='.$this->hash);
		$service=new BattleService();
	
		$this->render('battletop',
			array(
				'top'=>$service->getActivityPageData(),
				'top_start'=>BattleService::START_TIME,
				'top_end'=>BattleService::END_TIME,
				'battle_start'=>BattleService::BATTLE_16_START_TIME,
				'battle_end'=>BattleService::BATTLE_2_END_TIME,
				'position' => Yii::app()->request->getParam('pos', '')
			)
		);
	}
	
	public function actionBattle(){
		$this->setPageTitle('皮皮乐天-女神争夺战');
		$this->cs->registerCssFile($this->pipiFrontPath.'/css/activities/nvshen.css?token='.$this->hash);
		$this->cs->registerCssFile($this->pipiFrontPath.'/css/activities/nvshen1-2.css?token='.$this->hash);
		$service=new BattleService();
		
		$battle = 16;
		$now = Yii::app()->request->getParam('time', '');
		$time = empty($now) ? time() : strtotime(urldecode($now));
		if($time >= strtotime(BattleService::BATTLE_2_START_TIME)){
			$battle = 2;
		}elseif($time >= strtotime(BattleService::BATTLE_4_START_TIME)){
			$battle = 4;
		}elseif($time >= strtotime(BattleService::BATTLE_8_START_TIME)){
			$battle = 8;
		}
		$res = Yii::app()->request->getParam('res', '');
		if($res) $battle=$res;
		
		$this->render('battle',
			array(
				'top' => $service->getBattle(),
				'battle'=>$battle,
				'time'=>$time,
				'now'=>$now,
				'top_start'=>BattleService::START_TIME,
				'top_end'=>BattleService::END_TIME,
				'battle_16_start'=>BattleService::BATTLE_16_START_TIME,
				'battle_16_end'=>BattleService::BATTLE_16_END_TIME,
				'battle_8_start'=>BattleService::BATTLE_8_START_TIME,
				'battle_8_end'=>BattleService::BATTLE_8_END_TIME,
				'battle_4_start'=>BattleService::BATTLE_4_START_TIME,
				'battle_4_end'=>BattleService::BATTLE_4_END_TIME,
				'battle_2_start'=>BattleService::BATTLE_2_START_TIME,
				'battle_2_end'=>BattleService::BATTLE_2_END_TIME,
				'position' => Yii::app()->request->getParam('pos', '')
			)
		);
	}
}