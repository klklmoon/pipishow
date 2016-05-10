<?php
/**
 * 购买VIP
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: zfzhang $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: BuyVipPropsService.php 17174 2014-01-02 02:51:31Z zfzhang $ 
 * @package　service
 * @subpackage props
 */
class BuyVipPropsService extends UserBuyPropsService{
	
	/**
	 * @var int VIP价格选项ＩＤ
	 */
	public $priceAttrId = 0;
	/**
	 * 
	 * @var string 购买道具组装信息
	 */
	private $buyInfo = '';
	/**
	 * @var array 用户是否拥有紫色VIP
	 */
	private $purpleVips;
	/* 
	 * @see lib/components/PipiProps#getPropsPrice()
	 */
	public function getPropsPrice(){
		if($this->priceAttrId <= 0){
			return 0;
		}
		
		$attribute = $this->getPropsAttriubte();
		if(!isset($attribute[$this->priceAttrId])){
			return 0;
		}
		$priceAttr = $attribute[$this->priceAttrId];//选择具体的价格类型
		return $priceAttr['value']*$this->num;
		
	}
	
	
	/* 
	 * @see lib/components/PipiProps#getPropsDedication()
	 */
	public function getPropsDedication(){
		return $this->getPropsPrice() * Yii::app()->params['change_relation']['pipiegg_to_dedication'];
	}
	
	/* 
	 * @see lib/components/PipiProps#getPropsValidTime()
	 */
	public function getPropsValidTime(){
		$attribute = $this->getPropsAttriubte();
		$priceAttr = $attribute[$this->priceAttrId];
		$todayTime=strtotime(date("Y-m-d 00:00:00",$this->timeStamp))-1;
		if($priceAttr['attr_enname'] == 'vip_price_january'){
			$this->buyInfo = $this->props['category']['name'].'('.$this->props['name'].')×1个月';
			return $todayTime+30*24*3600;
		}elseif($priceAttr['attr_enname'] == 'vip_price_march'){
			$buyData['vtime'] = $todayTime+ 90*24*3600;
			$this->buyInfo = $this->props['category']['name'].'('.$this->props['name'].')×3个月';
			return  $todayTime+ 90*24*3600;
		}elseif($priceAttr['attr_enname'] == 'vip_price_june'){
			$buyData['vtime'] = $todayTime+180*24*3600;
			$this->buyInfo  = $this->props['category']['name'].'('.$this->props['name'].')×6个月';
			return $todayTime+180*24*3600;
		}elseif($priceAttr['attr_enname'] == 'vip_price_december'){
			$this->buyInfo  = $this->props['category']['name'].'('.$this->props['name'].')×12个月';
			return $todayTime+ 365*24*3600;
		}
		$this->buyInfo  = $this->props['category']['name'].'('.$this->props['name'].')×永久';
		return 0;
	}
	
	
	/* 
	 * @see lib/components/PipiProps#getPropsInfo()
	 */
	public function getPropsInfo(){
		return $this->buyInfo;
	}
	
	//获Vip取购买的天数
	public function getVipPurchasedDays()
	{
		$attribute = $this->getPropsAttriubte();
		$priceAttr = $attribute[$this->priceAttrId];
		if($priceAttr['attr_enname'] == 'vip_price_january'){
			$this->buyInfo = $this->props['category']['name'].'('.$this->props['name'].')×1个月';
			return 30;
		}elseif($priceAttr['attr_enname'] == 'vip_price_march'){
			$this->buyInfo = $this->props['category']['name'].'('.$this->props['name'].')×3个月';
			return   90;
		}elseif($priceAttr['attr_enname'] == 'vip_price_june'){
			$this->buyInfo  = $this->props['category']['name'].'('.$this->props['name'].')×6个月';
			return 180;
		}elseif($priceAttr['attr_enname'] == 'vip_price_december'){
			$this->buyInfo  = $this->props['category']['name'].'('.$this->props['name'].')×12个月';
			return 365;
		}
		$this->buyInfo  = $this->props['category']['name'].'('.$this->props['name'].')×永久';
		return 0;
	}
	
	/* 
	 * 重载isPurchased
	 * 
	 * @see lib/components/PipiProps#isPurchased()
	 */
	public function isPurchased(){
		/*
		if(parent::isPurchased()){
			$purpleProps = self::$propsService->getPropsByEnName('vip_purple');
			$purpleVips = self::$userPropsService->getUserValidPropsOfBagByPropId($this->users['uid'],$purpleProps['prop_id']);
			if(empty($purpleVips)){
				return true;
			}
			$purpleVips = array_pop($purpleVips);
			if($purpleVips['valid_time'] > $this->timeStamp){
				$this->purpleVips = $purpleVips;
				$this->purpleVips['purple_info'] = $purpleProps;
				$this->errorCode = Yii::t('props','You are aleady buy purple vip');
			 	return false;
			}
			return true;
		}
		*/
		return parent::isPurchased();
	}
	/**
	 * 存储用户道具属性
	 */
	public function saveUserPropsAttribute(){
		$userProps = array();
		$userProps['uid'] = $this->users['uid'];
		if($this->purpleVips || $this->props['en_name'] == 'vip_purple'){
			$userProps['vip_type'] = 2;
			$userProps['vip'] = $this->purpleVips ? $this->purpleVips['prop_id'] : $this->props['prop_id'];
		}else{
			$userProps['vip_type'] = 1;
			$userProps['vip'] = $this->props['prop_id'];
		}
		self::$userPropsService->saveUserPropsAttribute($userProps);
	}
	
	public function afterBuy(){
		parent::afterBuy();
		/*
		$userJson['vip'] = array('t'=>1,'h'=>0,'img'=>'','vt'=>0,'us'=>0);
		if($this->purpleVips || $this->props['en_name'] == 'vip_purple'){
			$userJson['vip']['t'] = 2;
			//如果已购买紫色VIP，紫色VIP生效
			if($this->purpleVips){
				$userJson['vip']['img'] = '/props/'.$this->purpleVips['purple_info']['image'];
				$userJson['vip']['vt'] = $this->purpleVips['valid_time'];
			}else{
				$userJson['vip']['img'] =  $this->props['image'];
				$userJson['vip']['vt'] = $this->getExistValidTime();
			}
		}else{
			$userJson['vip']['t'] = 1;
			$userJson['vip']['img'] =  $this->props['image'];
			$userJson['vip']['vt'] = $this->getExistValidTime();
		}
		$userJsonInfoService = new UserJsonInfoService();
		$userJsonInfoService->setUserInfo($this->users['uid'],$userJson);
		$zmq = $this->getZmq();
		$zmq->sendZmqMsg(609,array('type'=>'update_json','uid'=>$this->users['uid'],'json_info'=>$userJson));
		*/
		//更新userJson中的vip信息
		//self::$propsService->updateUserJsonOfVip($this->users['uid'], $this->props['prop_id']);
	}
	
	/**
	 * 将用户购买的 vip道具放入背包
	 *
	 * @return number 返回背包ＩＤ
	 */
	protected function savePropsToBag(){
		
		$bag=array();
		$bag['uid'] = $this->users['uid'];
		$bag['target_id'] = $this->getToTargetId();
		$bag['prop_id'] = $this->props['prop_id'];
		$bag['cat_id'] = $this->props['cat_id'];
		$bag['record_sid'] = $this->recordSid;
		
		$bag['use_status']=0;	//默认为启用状态
		$bag['update_time']=$this->timeStamp;		//启用时间
		//检测背包中是否有已处于启用状态的vip
		//self::$propsService->checkAndStopVip($this->users['uid'], $this->props['prop_id']);
		//存储背包
		$flag=self::$propsService->saveVipToBag($bag, $this->getVipPurchasedDays());
		
		return $flag;
	}
}