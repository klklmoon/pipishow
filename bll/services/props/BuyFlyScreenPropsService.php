<?php
/**
 * 购买飞屏
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: BuyFlyScreenPropsService.php 9671 2013-05-06 13:51:21Z suqian $ 
 * @package　service
 * @subpackage props
 */
class BuyFlyScreenPropsService extends UserBuyPropsService{
	
	public $isSavePropsBag = false;
	/* 
	 * @see lib/components/PipiProps#getPropsPrice()
	 */
	public function getPropsPrice(){
		$price = $this->props['pipiegg']*$this->num;
		if($price <= 0){
			return 0;
		}
		$disCount = $this->getDisCountFlyScreen();
		if($disCount > 1){
			return 0;
		}
		return $price*$disCount;
	}
	/* 
	 * @see lib/components/PipiProps#getPropsValidTime()
	 */
	public function getPropsValidTime(){
		$propsAttribute = $this->getPropsEnAttriubte();
		return $this->timeStamp+$propsAttribute['flyscreen_timeout']['value'];
	}
	
	
	/* 
	 * @see lib/components/PipiProps#getPropsInfo()
	 */
	public function getPropsInfo(){
		return $this->props['category']['name'].'('.$this->props['name'].'*'.$this->num.')';
	}
	
	/**
	 * 取得飞屏折扣
	 * 
	 * @return int
	 */
	public function getDisCountFlyScreen(){
		$userPropsAttribute = self::$userPropsService->getUserPropsAttributeByUid($this->users['uid']);
		if(empty($userPropsAttribute)){
			return 1;
		}
		if($userPropsAttribute['vip_type'] == 1){
			return 0.9;
		}elseif($userPropsAttribute['vip_type'] == 2){
			return 0.8;
		}
		return 1;
	}
	
	public function getOperatePage(){
		return 0;
	}
	
	public function afterBuy(){
		parent::afterBuy();
	
	}
	
}