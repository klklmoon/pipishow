<?php
define('YELLOW_VIP_FREE_USER_LABEL',15);  //黄色vip每天免费使用贴条数为15次
define('PURPLE_VIP_FREE_USER_LABEL',25);  //紫色vip每天免费使用贴条数为25次
/**
 * 商城
 *
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z suqian $
 * @package
 */
class ShopController extends PipiController {

	/**
	 * @var PropsService
	 */
	protected $propService = null;

	/**
	 *
	 * @var UserPropsService
	 */
	protected $userPropService = null;
	/**
	 *
	 * @var ConsumeService
	 */
	protected $consuemService = null;
	
	/**
	 *
	 * @var AgentsService
	 */
	protected $agentsService = null;	
	
	protected $agent_id=0;
	
	protected $agent_nickname='';

	public function init(){
		parent::init();
	}
	
	public function beforeAction($action){
		parent::beforeAction($action);
		$this->propService = new PropsService();
		$this->userPropService = new UserPropsService();
		$this->consuemService = new ConsumeService();
		$this->agentsService = new AgentsService();
		
		//代理设置
		$this->agent_id = Yii::app()->request->getParam('agent_id')?Yii::app()->request->getParam('agent_id'):0;
		if(empty($this->agent_id))
		{
			$cookies = Yii::app()->request->getCookies();
			if(isset($cookies['agent_id']) && !empty($cookies['agent_id']->value)){
				$this->agent_id=$cookies['agent_id']->value;
			}
		}
		
		if($this->agent_id>0)
		{
			if(!isset($cookies['agent_id']))
			{
				$cookie =new CHttpCookie('agent_id',$this->agent_id);
				Yii::app()->request->cookies['agent_id']=$cookie;
			}
			$userAgents=$this->agentsService->getAgentByUids(array($this->agent_id));
			$this->agent_nickname=$userAgents[$this->agent_id]['agent_nickname'];
		}
		
		if(!empty($this->agent_id) && !$this->agentsService->checkAgentByUid($this->agent_id))
		{
			$this->agent_id=0;
			$cookies = Yii::app()->request->getCookies();
			if(isset($cookies['agent_id']))
				$cookies->remove('agent_id',array('expire'=>-3600,'value'=>null,'domain'=>DOMAIN,'path'=>'/'));
		}
		
		$this->setPageTitle(Yii::t('seo','seo_shop_title'));
		$this->setPageKeyWords(Yii::t('seo','seo_shop_keywords'));
		$this->setPageDescription(Yii::t('seo','seo_shop_description'));
		return true;
	}

	public function index(){

	}

	public function actionGift(){
		$giftService=new GiftService();
		$condition['gift_type']=4;
		$gift=$giftService->getGiftByCondition(0,20,$condition);
		$consumeService=new ConsumeService();
		$rank=$consumeService->getAllUserRanks();
		$rankList=array();
		foreach($rank as $row){
			$rankList[$row['rank']]=$row['name'];
		}
		$giftList=array();
		foreach($gift['list'] as $key=>$row){
			$giftList[$key]['gift_id']=$row['gift_id'];
			$giftList[$key]['zh_name']=$row['zh_name'];
			$giftList[$key]['pipiegg']=$row['pipiegg'];
			$giftList[$key]['image']=Yii::app()->params['images_server']['url'].'/gift/'.$row['image'];
			$giftList[$key]['sell_nums']=$row['sell_nums'];
			$giftList[$key]['buy_limit']=$row['buy_limit'];
			$giftList[$key]['grade']=$row['sell_grade'];
			$giftList[$key]['sell_grade']=$rankList[$row['sell_grade']];
			$giftList[$key]['shop_type']=$giftService->getShopType($row['shop_type']);
		}
		$this->render('gift',array('giftList'=>$giftList,'rankList'=>$rankList));
	}


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
		if($userInfo['rk']<$gift[$gift_id]['sell_grade']){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('giftBag','Restrictions on the purchase'))));
		}
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

	/**
	 *
	 */
	public function actionCar(){
		$category = $this->propService->getPropsCategoryByEnName('car');
		$id = $category['cat_id'];
		$props = $this->propService->getPropsByCatId($id,false,true);
		$userRank = $this->propService->buildDataByIndex($this->consuemService->getAllUserRanks(),'rank');

		foreach($props as $key=>$prop){
			$attribute = $this->propService->buildDataByIndex($prop['attribute'],'attr_enname');
			if($attribute['car_is_limit']['value']){
				$props[$key]['limit_type'] = $attribute['car_is_limit']['value'];
				$props[$key]['limit_num'] = $attribute['car_limit']['value'];
			}else{
				$props[$key]['limit_type'] = 0;
				$props[$key]['limit_num'] = 0;
			}
			$props[$key]['image'] = $this->propService->getUploadUrl().$props[$key]['image'];
			//是否有座架logo
			$props[$key]['car_logo']= isset($attribute['car_logo']['value'])?$this->propService->getUploadUrl().$attribute['car_logo']['value']:'';
			$seventPrice = $attribute['car_price_sevenday'];
			$ninePrice = $attribute['car_price_ninetyday'];
			$permentPrice = $attribute['car_price_permanent'];
			$yearPrice = isset($attribute['car_price_year'])?$attribute['car_price_year']:array();

			$seventDedication = $attribute['car_dedication_sevenday'];
			$nineDedication = $attribute['car_dedication_ninetyday'];
			$permentDedication = $attribute['car_dedication_permanent'];
			$yearDedication = isset($attribute['car_dedication_year'])?$attribute['car_dedication_year']:array();

			$props[$key]['priceList'] = array();
			if($seventPrice['value']){
				$props[$key]['priceList'][$seventPrice['attr_id']] = array('id'=>$seventPrice['attr_id'],'value'=>$seventPrice['value'].'皮蛋/7天/'.$seventDedication['value'].'贡献值');
			}
			if($ninePrice['value']){
				$props[$key]['priceList'][$ninePrice['attr_id']] = array('id'=>$ninePrice['attr_id'],'value'=>$ninePrice['value'].'皮蛋/90天/'.$nineDedication['value'].'贡献值');
			}
			if(isset($yearPrice['value'])){
				$props[$key]['priceList'][$permentPrice['attr_id']] = array('id'=>$yearPrice['attr_id'],'value'=>$yearPrice['value'].'皮蛋/1年/'.$yearDedication['value'].'贡献值');
			}
			if($permentPrice['value']){
				$props[$key]['priceList'][$permentPrice['attr_id']] = array('id'=>$permentPrice['attr_id'],'value'=>$permentPrice['value'].'皮蛋/永久/'.$permentDedication['value'].'贡献值');
			}
			

			if($prop['rank']){
				$props[$key]['rank_desc'] = $userRank[$prop['rank']]['name'].'以上';
			}else{
				$props[$key]['rank_desc'] = '不限';
			}
			if($prop['rank']){
				$props[$key]['rank_desc'] = $userRank[$prop['rank']]['name'].'以上';
			}else{
				$props[$key]['rank_desc'] = '不限';
			}
			unset($props[$key]['attribute']);

		}
		//座驾排序
		usort($props,array($this,'sortCarBySort'));
		$this->render('car',array('props'=>$props));
	}
	
	//座驾排序
	private  function sortCarBySort(array $prev,array $next){
		if($prev['sort'] == $next['sort']){
			return 0;
		}
		return $prev['sort'] < $next['sort'] ? -1 : 1;
	}

	public function actionVip(){

		$category = $this->propService->getPropsCategoryByEnName('vip');
		$id = $category['cat_id'];
		$props =  $this->propService->getPropsByCatId($id,false,true);
		$userRank =  $this->propService->buildDataByIndex( $this->consuemService->getAllUserRanks(),'rank');

		foreach($props as $key=>$prop){
			$attribute =  $this->propService->buildDataByIndex($prop['attribute'],'attr_enname');
			$januaryPrice = $attribute['vip_price_january'];
			$marchPrice = $attribute['vip_price_march'];
			$junePrice = $attribute['vip_price_june'];
			$decemberPrice = $attribute['vip_price_december'];
			$foreverPrice = $attribute['vip_price_forever'];
			$props[$key]['image'] = $this->propService->getUploadUrl().$props[$key]['image'];
			$props[$key]['priceList'] = array(
				$januaryPrice['attr_id'] => array('id'=>$januaryPrice['attr_id'],'value'=>'1个月'.$januaryPrice['value'].'皮蛋','data'=>'1/'.$januaryPrice['value']),
				$marchPrice['attr_id'] => array('id'=>$marchPrice['attr_id'],'value'=>'3个月'.$marchPrice['value'].'皮蛋','data'=>'3/'.$marchPrice['value']),
				$junePrice['attr_id'] => array('id'=>$junePrice['attr_id'],'value'=>'6个月'.$junePrice['value'].'皮蛋','data'=>'6/'.$junePrice['value']),
				$decemberPrice['attr_id'] => array('id'=>$decemberPrice['attr_id'],'value'=>'12个月'.$decemberPrice['value'].'皮蛋','data'=>'12/'.$decemberPrice['value']),
				$foreverPrice['attr_id'] => array('id'=>$foreverPrice['attr_id'],'value'=>'永久'.$foreverPrice['value'].'皮蛋','data'=>'永久/'.$foreverPrice['value']),
			);
			$props[$key]['rank_desc'] = '不限';
/* 			if($prop['rank']){
				$props[$key]['rank_desc'] = $userRank[$prop['rank']]['name'].'以上';
			}else{
				$props[$key]['rank_desc'] = '不限';
			} */
			$props[$key]['right'] = strtr($attribute['vip_right']['value'],array("\n\r"=>'<br/>',"\n"=>'<br/>','\r'=>'<br/>'));
			unset($props[$key]['attribute']);

		}


		$this->render('vip',array('props'=>$props));
	}

	public function actionMonthCard(){
		$category = $this->propService->getPropsCategoryByEnName('monthcard');
		$id = $category['cat_id'];
		$props = $this->propService->getPropsByCatId($id,false,true);
		$userRank=$this->consuemService->getAllUserRanks();
		foreach($userRank as $row){
			if($row['rank']==0){
				$row['name']='无限制';
			}
			$rankList[$row['rank']]=$row['name'];
		}
		unset($userRank);
		$propsList = array();
		foreach($props as $key=>$row){
			$attribute=$this->propService->buildDataByIndex($row['attribute'],'attr_enname');
			$row['attribute']=$attribute;
			$row['rank']=$rankList[$row['rank']];
			$propsList[$key]=$row;
		}
		$this->render('monthcard',array('props'=>$propsList));
	}

	public function actionGuardian(){

	}

	public function actionLabel(){
		echo '<pre>';
		$data = array();
		$return = $this->bitCondition(4,8,$data);
		asort($return,SORT_NUMERIC);
		print_r(array_values($return));



	}

	public function actionFlyScreen(){

	}
	
	public function actionNumber(){
		$numberService = new UserNumberService();
		$fourNumberList = $numberService->getNumberList(NUMBER_TYPE_FOUR,20);
		$fiveNumberList = $numberService->getNumberList(NUMBER_TYPE_FIVE,10);
		if(!$fiveNumberList){
			$fiveNumberList = $numberService->getRandNumber(5,10);
		}
		$sixNumberList = $numberService->getNumberList(NUMBER_TYPE_SIX,10);
		if(!$sixNumberList){
			$sixNumberList = $numberService->getRandNumber(6,10);
		}
		$sevenNumberList = $numberService->getNumberList(NUMBER_TYPE_SEVEN,15);
		if(!$sevenNumberList){
			$sevenNumberList = $numberService->getRandNumber(7,15);
		}
		$this->viewer['four'] = $fourNumberList;
		$this->viewer['five'] = $fiveNumberList;
		$this->viewer['six'] =  $sixNumberList;
		$this->viewer['seven'] =  $sevenNumberList;
		$this->render('number');
	}

	public function actionNumberPage(){
		$numberService = new UserNumberService();
		$p = Yii::app()->request->getParam('p');
		$type = Yii::app()->request->getParam('type');
		$p = $p < 0 ? 1 : $p;
		$reGroup = 0;
		if($type == NUMBER_TYPE_FOUR){
			$limit = 20;
			$count = $numberService->countNumberList(NUMBER_TYPE_FOUR);
			$pages = ceil($count / $limit);
			$offset = ($p-1)*$limit;
			if($p >= $pages){
				$reGroup = 1;
			}
			$numberList = $numberService->getNumberList(NUMBER_TYPE_FOUR,$limit,$offset);
			if($numberList){
				echo '<span style="display:none;" id="four_renumber">'.$reGroup.'</span>';
			}else{
				echo 'no_page';
				Yii::app()->end();
			}
		}elseif($type == NUMBER_TYPE_FIVE){
			$limit = 10;
			$count = $numberService->countNumberList(NUMBER_TYPE_FIVE);
			$pages = ceil($count / $limit);
			$offset = ($p-1)*$limit;
			if($p >= $pages){
				$reGroup = 1;
			}
			$numberList = $numberService->getNumberList(NUMBER_TYPE_FIVE,$limit,$offset);
			if(!$numberList  && $count == 0){
				$numberList = $numberService->getRandNumber(5,$limit);
			}else{
				if($numberList){
					echo '<span style="display:none;" id="five_renumber">'.$reGroup.'</span>';
				}else{
					echo 'no_page';
					Yii::app()->end();
				}
			}
		}elseif($type == NUMBER_TYPE_SIX){
			$limit = 10;
			$count = $numberService->countNumberList(NUMBER_TYPE_SIX);
			$pages = ceil($count / $limit);
			$offset = ($p-1)*$limit;
			if($p >= $pages){
				$reGroup = 1;
			}
			$numberList = $numberService->getNumberList(NUMBER_TYPE_SIX,$limit,$offset);
			if(!$numberList  && $count == 0){
				$numberList = $numberService->getRandNumber(6,$limit);
			}else{
				if($numberList){
				 	echo '<span style="display:none;" id="six_renumber">'.$reGroup.'</span>';
				}else{
					echo 'no_page';
					Yii::app()->end();
				}
				
			}
		}elseif($type == NUMBER_TYPE_SEVEN){
			$limit = 15;
			$count = $numberService->countNumberList(NUMBER_TYPE_SEVEN);
			$pages = ceil($count / $limit);
			$offset = ($p-1)*$limit;
			if($p >= $pages){
				$reGroup = 1;
			}
			$numberList = $numberService->getNumberList(NUMBER_TYPE_SEVEN,$limit,$offset);
			if(!$numberList && $count == 0){
				$numberList = $numberService->getRandNumber(7,$limit);
			}else{
				if($numberList){
				 	echo '<span style="display:none;" id="seven_renumber">'.$reGroup.'</span>';
				}else{
					echo 'no_page';
					Yii::app()->end();
				}
			}
		}
		$this->viewer['numberList'] = $numberList;
		$this->renderPartial('numberTemplate',array('list'=>$numberList,'type'=>$type));
		
		
		
	}
	public function actionBuyCar(){
		$propId = Yii::app()->request->getParam('prop_id');
		$priceId = Yii::app()->request->getParam('price_attr_id');
		
		
		if($propId <= 0 || $priceId <= 0){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('common','Parameters are wrong'))));
		}
		if(!$this->isLogin){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('user','You are not logged'))));
		}
		$buyCarPropsService = new BuyCarPropsService(Yii::app()->user->id,$propId,1);
		$buyCarPropsService->isCheckExpired = false;
		$buyCarPropsService->isCheckBuy = false;
		$buyCarPropsService->priceAttrId = $priceId;
		$flag = $buyCarPropsService->buyProps();
		
		//通过代理销售
		if($flag && !empty($this->agent_id) && $this->agent_id>0)
		{
			$agentRate=$this->agentsService->getRateByUid($this->agent_id);
			$_pipieggs=$buyCarPropsService->getPropsPrice();
			$saleRecords=array(
				'agent_id'=>$this->agent_id,
				'uid'=>Yii::app()->user->id,
				'goods_type'=>0,	//道具
				'goods_id'=>$propId,
				'goods_num'=>1,
				'pipieggs'=>$_pipieggs,
				'agent_income'=>$_pipieggs*$agentRate
			);
			$this->agentsService->saveSaleRecords($saleRecords);
		}		
		
		$message = $buyCarPropsService->getErrorCode();
		if($flag && empty($message)){
			exit(json_encode(array('flag'=>1,'message'=>Yii::t('props','Car is sold success'))));
		}
		exit(json_encode(array('flag'=>0,'message'=>$message)));
	}

	public function actionBuyVip(){
		$propId = Yii::app()->request->getParam('prop_id');
		$priceId = Yii::app()->request->getParam('price_attr_id');
		
		
		if($propId <= 0 || $priceId <= 0){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('common','Parameters are wrong'))));
		}
		if(!$this->isLogin){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('user','You are not logged'))));
		}
		$buyVipPropsService = new BuyVipPropsService(Yii::app()->user->id,$propId,1);
		$buyVipPropsService->isCheckExpired = false;
		$buyVipPropsService->isCheckBuy = false;
		$buyVipPropsService->priceAttrId = $priceId;
		$flag = $buyVipPropsService->buyProps();

		//通过代理销售
		if($flag && !empty($this->agent_id) && $this->agent_id>0)
		{
			$agentRate=$this->agentsService->getRateByUid($this->agent_id);
			$_pipieggs=$buyVipPropsService->getPropsPrice();
			$saleRecords=array(
				'agent_id'=>$this->agent_id,
				'uid'=>Yii::app()->user->id,
				'goods_type'=>0,	//道具
				'goods_id'=>$propId,
				'goods_num'=>1,
				'pipieggs'=>$_pipieggs,
				'agent_income'=>$_pipieggs*$agentRate
			);
			$this->agentsService->saveSaleRecords($saleRecords);
		}
		
		$message = $buyVipPropsService->getErrorCode();
		if($flag && empty($message)){
			exit(json_encode(array('flag'=>1,'message'=>Yii::t('props','Vip is sold success'))));
		}
		exit(json_encode(array('flag'=>0,'message'=>$message)));
	}

	public function actionBuyMonthCard(){
		$propId = Yii::app()->request->getParam('prop_id');
		
		if($propId <= 0){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('common','Parameters are wrong'))));
		}

		if(!$this->isLogin){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('user','You are not logged'))));
		}
		$buyMonthCardPropsService = new BuyMonthCardPropsService(Yii::app()->user->id,$propId,1);
		$buyMonthCardPropsService->isCheckExpired = false;
		$buyMonthCardPropsService->isCheckBuy = false;
		$flag = $buyMonthCardPropsService->buyProps();

		//通过代理销售
		if($flag && !empty($this->agent_id) && $this->agent_id>0)
		{
			$agentRate=$this->agentsService->getRateByUid($this->agent_id);
			$_pipieggs=$buyMonthCardPropsService->getPropsPrice();
			$saleRecords=array(
				'agent_id'=>$this->agent_id,
				'uid'=>Yii::app()->user->id,
				'goods_type'=>0,	//道具
				'goods_id'=>$propId,
				'goods_num'=>1,
				'pipieggs'=>$_pipieggs,
				'agent_income'=>$_pipieggs*$agentRate
			);
			$this->agentsService->saveSaleRecords($saleRecords);
		}		
		
		$message = $buyMonthCardPropsService->getErrorCode();
		if($flag && empty($message)){
			exit(json_encode(array('flag'=>1,'message'=>Yii::t('props','MonthCard is sold success'))));
		}
		exit(json_encode(array('flag'=>0,'message'=>$message)));
	}

	public function actionBuyFlyscreen(){
		$to_uid = Yii::app()->request->getParam('to_uid');
		$content = Yii::app()->request->getParam('content');
		$archives_id = Yii::app()->request->getParam('archives_id');
		if(!$this->isLogin){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('user','You are not logged'))));
		}

		if($archives_id<=0){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('common','Parameters are wrong'))));
		}
		$forbidenService=new ForbidenService();
		if($forbidenService->getArchivesKickout($archivesId,$to_uid)){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('archives','You have been kicked out of archives'))));
		}
		
		$forbidenService=new ForbidenService();
		$forbid=$forbidenService->getArchivesForbid($archives_id,$uid);
		if($forbid){
			if($forbid['vt']>time()){
				if($forbid['t']==1){
					$msg='You have been banned IP';
				}else if($forbid['t']==5){
					$msg='You have been banned';
				}else if($forbid['t']==11){
					$msg='You have been the global gag';
				}
				exit(json_encode(array('flag'=>0,'message'=>Yii::t('props',$msg))));
			}
		}
		
		$userPropsService=new UserPropsService();
		$last_time=$userPropsService->getLastFlyscreenTime();
		if(time()-$last_time<=10){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('props','Fly screen operation is being processed'))));
		}

		if(strlen($content)<=0){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('props','Fly screen sending failed</p><p class="otline">Please enter a fly screen content'))));
		}
		$len=mb_strlen($content,'UTF8');
		if(strlen($len)>20){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('props','Fly screen sending failed</p><p class="otline">The text you entered exceeds 20 characters'))));
		}

		//敏感词过滤
		$wordService=new WordService();
		$content=$wordService->wordFilter($content);
		
		$propsService=new PropsService();
		$props = $propsService->getPropsByEnName('flyscreen');
		$propId=$props['prop_id'];
		$uid=Yii::app()->user->id;
		
		$propsInfo = $propsService->getPropsByIds($propId, true, true);
		$attribute = $propsService->buildDataByIndex($propsInfo[$propId]['attribute'], 'attr_enname');

		$bag_fly=$userPropsService->getUserValidPropsOfBagByPropId($uid,$props['prop_id']);
		$bag_fly=array_pop($bag_fly);
		$userService=new UserService();
		//优先送出道具背包中的飞屏
		if($bag_fly['num']>=1){
			$userPropsService=new UserPropsService();
			//减去背包中的飞屏数量
			if($userPropsService->saveUserPropsBag(array('uid'=>$uid,'prop_id'=>$props['prop_id'],'s_num'=>-1))){
				$propsUse['target_id']=$archives_id;
				$propsUse['uid']=$uid;
				$propsUse['to_uid']=$to_uid;
				$propsUse['prop_id']=$propId;
				$propsUse['cat_id']=$props['cat_id'];
				$propsUse['num']=1;
				$userPropsService->saveUserPropsUse($propsUse);
				$zmq=new PipiZmq();
				$eventData['archives_id']=$archives_id;
				$eventData['domain']=DOMAIN;
				$eventData['type']='localroom';
				$json_content['type']='flyscreen';
				if($to_uid>0){
					$userBase=$userService->getUserBasicByUids(array($uid,$to_uid));
					$json_content['from_nickname']=$userBase[$uid]['nickname'];
					$json_content['to_nickname']=$userBase[$to_uid]['nickname'];
				}else{
					$userBase=$userService->getUserBasicByUids(array($uid));
					$json_content['from_nickname']=$userBase[$uid]['nickname'];
					$json_content['to_nickname']='';
				}
				$json_content['content']=$content;
				$json_content['time_out']=$attribute['flyscreen_timeout']['value'];
				$eventData['json_content']=$json_content;
				$zmq->sendZmqMsg(606,$eventData);
				$userPropsService->saveLastFlyscreenTime();
				exit(json_encode(array('flag'=>0,'message'=>Yii::t('props','Fly screen successfully sent,The use of is your backpack'))));
			}else{
				exit(json_encode(array('flag'=>0,'message'=>Yii::t('props','Fly screen sending failed'))));
			}
		}
		$buyFlyScreenCardPropsService = new BuyFlyScreenPropsService(Yii::app()->user->id,$propId,1);
		$buyFlyScreenCardPropsService->isCheckExpired = false;
		$buyFlyScreenCardPropsService->isCheckBuy = false;
		$buyFlyScreenCardPropsService->isCheckForeverPrice = false;
		$flag = $buyFlyScreenCardPropsService->buyProps();
		$message = $buyFlyScreenCardPropsService->getErrorCode();
		if($flag && empty($message)){
			$zmq=new PipiZmq();
			$eventData['archives_id']=$archives_id;
			$eventData['domain']=DOMAIN;
			$eventData['type']='localroom';
			$json_content['type']='flyscreen';
			if($to_uid>0){
				$userBase=$userService->getUserBasicByUids(array($uid,$to_uid));
				$json_content['from_nickname']=$userBase[$uid]['nickname'];
				$json_content['to_nickname']=$userBase[$to_uid]['nickname'];
			}else{
				$userBase=$userService->getUserBasicByUids(array($uid));
				$json_content['from_nickname']=$userBase[$uid]['nickname'];
				$json_content['to_nickname']='';
			}
			$json_content['content']=$content;
			$json_content['time_out']=$attribute['flyscreen_timeout']['value'];
			$eventData['json_content']=$json_content;
			$zmq->sendZmqMsg(606,$eventData);
			$userPropsService->saveLastFlyscreenTime();
			exit(json_encode(array('flag'=>1,'message'=>Yii::t('props','Flyscreen is sold success'))));
		}
		exit(json_encode(array('flag'=>0,'message'=>$message)));
	}

	public function actionBuyLabel(){
		$propId = Yii::app()->request->getParam('prop_id');
		$toUid = Yii::app()->request->getParam('to_uid');
		$archivesId = Yii::app()->request->getParam('archive_id');
		$isRemoveLabel = Yii::app()->request->getParam('is_remove');
		if($propId <= 0 || $toUid<= 0){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('common','Parameters are wrong'))));
		}
		if(!$this->isLogin){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('user','You are not logged'))));
		}
		
		$forbidenService=new ForbidenService();
		if($forbidenService->getArchivesKickout($archivesId,$to_uid)){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('archives','You have been kicked out of archives'))));
		}
		
		$propsService=new PropsService();
		$labelInfo = $propsService->getPropsByIds($propId, true, true);
		$attribute = $propsService->buildDataByIndex($labelInfo[$propId]['attribute'], 'attr_enname');
	
		$buyLabelPropsService = new BuyLabelPropsService(Yii::app()->user->id,$propId,1);
		$buyLabelPropsService->isCheckExpired = false;
		$buyLabelPropsService->isCheckBuy = false;
		$buyLabelPropsService->isRemoveLable = $isRemoveLabel;
		$buyLabelPropsService->toUid = $toUid;
		$buyLabelPropsService->archivesId = $archivesId;

		$flag = $buyLabelPropsService->buyProps();
		$message = $buyLabelPropsService->getErrorCode();
		if($flag && empty($message)){
			exit(json_encode(array('flag'=>1,'message'=>Yii::t('props','Label is sold success'))));
		}
		exit(json_encode(array('flag'=>0,'message'=>$message)));
	}
	
	public function actionCheckUid(){
		$uid = intval(Yii::app()->request->getParam('uid'));
		$status = 0;
		$message = '用户不存在';
		$data = array();
		if($uid > 0){
			$user = UserJsonInfoService::getInstance()->getUserInfo($uid, false);
			if(!empty($user) && isset($user['uid'])){
				$status = 1;
				$message = $user['nk'];
			}
		}
		exit(json_encode(array('flag'=>$status,'message'=> $message)));
	}
	
	public function actionBuyNumber(){
		if(!$this->isLogin){
			exit(json_encode(array('flag'=>0,'message'=> Yii::t('user','You are not logged'))));
		}
		$uid = Yii::app()->user->id;
		$number = Yii::app()->request->getParam('number');
		$type = Yii::app()->request->getParam('type');
		$sender_uid = Yii::app()->request->getParam('to_uid');
		$proxy_uid = Yii::app()->request->getParam('proxy_uid');
		if($number <= 0 || ($sender_uid && $sender_uid <= 0) || ($proxy_uid && $proxy_uid <=0)){
			exit(json_encode(array('flag'=>0,'message'=> Yii::t('common','Parameters are wrong'))));
		}
		
		$len = strlen($number);
		if($number <= 0 || $len < 4 || $len > 7){
			exit(json_encode(array('flag'=>0,'message'=> Yii::t('number','This pretty number must be greater than three and less than  eight figures'))));
		}
		
		if(!$type && len == 4){
			exit(json_encode(array('flag'=>0,'message'=> Yii::t('number','This pretty number temporarily open to buy1'))));
		}
		if($sender_uid){
			$userServerice = new UserService();
			$user = $userServerice->getUserBasicByUids(array($sender_uid));
			if(empty($user)){
				exit(json_encode(array('flag'=>0,'message'=> Yii::t('user','The user does not exist'))));
			}
			$user = $user[$sender_uid];
		}
		$userNumberService = new UserNumberService();
		if($userNumberService->buyNumber($uid,$number,$sender_uid,$this->agent_id)){
			//通过代理销售
			if(!empty($this->agent_id) && $this->agent_id>0)
			{
				$agentRate=$this->agentsService->getRateByUid($this->agent_id);
				$numberInfo = $userNumberService->getNumberById($number);
				$price = 0;
				if(empty($numberInfo)){
					$price = $userNumberService->calNumberPrice($number);
				}else{
					$price = $numberInfo['confirm_price'] ? $numberInfo['confirm_price'] : $numberInfo['buffer_price'];
					if($price <= 0){
						$price = $userNumberService->calNumberPrice($number);
					}
				}
				$saleRecords=array(
					'agent_id'=>$this->agent_id,
					'uid'=>Yii::app()->user->id,
					'goods_type'=>1,	//靓号
					'goods_id'=>$number,
					'goods_num'=>1,
					'pipieggs'=>$price,
					'agent_income'=>$price*$agentRate
				);
				$this->agentsService->saveSaleRecords($saleRecords);
			}
			
			if($sender_uid && $user){
				$nickname = $this->getUserJsonAttribute('nk',true,false);
				$messageService = new MessageService();
				$message['uid'] = $uid;
				$message['to_uid'] = $sender_uid;
				$message['category'] = MESSAGE_CATEGORY_SYSTEM;
				$message['sub_category'] = MESSAGE_CATEGORY_SYSTEM_PUSH;
				$message['title'] = '收到赠送靓号';
				$message['content'] = "您收到了昵称{$nickname}赠送的靓号{$number}，快点击看一下吧";
				$message['extra'] = array('from'=>$nickname,'href'=>'index.php?r=account/number');
				$message['is_read'] = 0;
				$messageService->sendMessage($message);
				exit(json_encode(array('flag'=>1,'message'=> "赠送成功！昵称{$user['nickname']}获得靓号<em class='pink'>{$number}</em>。")));
			}
			exit(json_encode(array('flag'=>1,'message'=> '赠送成功')));
		}else{
			$notice = $userNumberService->getNotice();
			if($notice){
				$notice = array_pop($notice);
			}
			exit(json_encode(array('flag'=>0,'message'=> $notice)));
		}
	}
	
	public function actionQueryNumber(){
		$uid = Yii::app()->user->id;
		$number = Yii::app()->request->getParam('number');
		if(!$this->isLogin){
			exit(json_encode(array('flag'=>0,'message'=> Yii::t('user','You are not logged'))));
		}
		$len = strlen($number);
		if($number <= 0 || $len < 4 || $len > 7){
			exit(json_encode(array('flag'=>0,'message'=> Yii::t('number','This pretty number must be greater than three and less than  eight figures'))));
		}
		
		if($len == 4){
			exit(json_encode(array('flag'=>0,'message'=> Yii::t('number','This pretty number temporarily open to buy1'))));
		}
		
		$userNumberService = new UserNumberService();
		$useNumber = $userNumberService->isUseNumber($number);
		if($useNumber){
			$userServerice = new UserService();
			$user = $userServerice->getUserBasicByUids(array($useNumber['uid']));
			if($user){
				exit(json_encode(array('flag'=>0,'message'=> "靓号{$number}已属于昵称 {$user[$useNumber['uid']]['nickname']},请挑选其它靓号")));
			}
		}
		$numberModel =  new NumberModel();
		$_number = $numberModel->findByPk($number);
		$data = array();
		if($_number){
			$data['number'] = $number;
			$data['short_desc'] = $_number['short_desc'];
			$data['price'] = $_number['confirm_price'] ? $_number['confirm_price'] : $_number['buffer_price'];
		}else{
			$data['number'] = $number;
			$data['short_desc'] = '';
			$data['price'] = $userNumberService->calNumberPrice($number);
		}
		exit(json_encode(array('flag'=>1,'message'=> $data)));
	}

	//检测测agent_id是否授权
	public function actionCheckAgentId()
	{
		$agent_id = Yii::app()->request->getParam('agent_id');
		if(empty($agent_id))
			exit(json_encode(array('flag'=>0,'message'=> 'fasle')));
		if($this->agentsService->checkAgentByUid($agent_id))
			exit(json_encode(array('flag'=>1,'message'=> 'true')));
		exit(json_encode(array('flag'=>0,'message'=> 'fasle')));
	}
}

