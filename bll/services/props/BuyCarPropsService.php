<?php

/**
 * 购买座驾
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: leiwei $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: BuyCarPropsService.php 16727 2013-11-27 06:08:52Z leiwei $ 
 * @package　service
 * @subpackage props
 */
class BuyCarPropsService extends UserBuyPropsService{
	
	/**
	 * @var int 座价的价格选项ＩＤ
	 */
	public $priceAttrId = 0;
	
	private $buyInfo = '';
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
		$attribute = $this->getPropsAttriubte();
		$nameAttriubte = $this->getPropsEnAttriubte();
		$priceAttr = $attribute[$this->priceAttrId];
		$priceAttrEnName = $priceAttr['attr_enname'];
		$tmp = explode('_',$priceAttrEnName);
		$tmp[1] = 'dedication';
		$dedicationAttrEnName= implode('_',$tmp);
		$dedicationAttr = $nameAttriubte[$dedicationAttrEnName];
		if($dedicationAttr){
			return $dedicationAttr['value'];
		}
		return $this->getPropsPrice() * Yii::app()->params['change_relation']['pipiegg_to_dedication'];
	}
	
	/* 
	 * @see lib/components/PipiProps#getPropsValidTime()
	 */
	public function getPropsValidTime(){
		$attribute = $this->getPropsAttriubte();
		$priceAttr = $attribute[$this->priceAttrId];
		if($priceAttr['attr_enname'] == 'car_price_sevenday'){
			$this->buyInfo = $this->props['category']['name'].'('.$this->props['name'].')×7天';
			return  $this->timeStamp+7*24*3600;
		}elseif($priceAttr['attr_enname'] == 'car_price_ninetyday'){
			$this->buyInfo = $this->props['category']['name'].'('.$this->props['name'].')×9个月';
			return $this->timeStamp+90*24*3600;
		}elseif($priceAttr['attr_enname'] == 'car_price_year'){
			$this->buyInfo = $this->props['category']['name'].'('.$this->props['name'].')×1年';
			return $this->timeStamp+365*24*3600;
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
	/* 
	 * 重载isPurchased
	 * 
	 * @see lib/components/PipiProps#isPurchased()
	 */
	public function isPurchased(){
		if(parent::isPurchased()){
			$nameAttriubte = $this->getPropsEnAttriubte();
			if($nameAttriubte['car_is_limit']['value'] > 0){
				$orgNum = $nameAttriubte['car_limit']['value'];
				if($orgNum <= 0){
					$this->errorCode = Yii::t('props','Car is sold out');
					return false;
				}
				$uAttribute['value'] = $orgNum - $this->num;
				if($uAttribute['value'] < 0){
					$this->errorCode = Yii::t('props','Car is sold out');
					return false;
				}
				$uAttribute['pattr_id'] = $nameAttriubte['car_limit']['pattr_id'];
				//更新剩余数量
				self::$propsService->saveSinglePropsAttribute($uAttribute);
			
			}
			return true;
		}
		return false;
	}
	
	
}