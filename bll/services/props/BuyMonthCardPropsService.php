<?php
/**
 * 购买月卡
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: BuyMonthCardPropsService.php 11730 2013-06-06 08:35:11Z suqian $ 
 * @package　service
 * @subpackage props
 */
class BuyMonthCardPropsService extends UserBuyPropsService{
	/**
	 * 月卡不加贡献值
	 */
	public function getPropsDedication(){
		return 0;
	}
	
	/* 
	 * @see lib/components/PipiProps#getPropsValidTime()
	 */
	public function getPropsValidTime(){
		return $this->timeStamp+30*24*3600;
	}
	
	
	/* 
	 * @see lib/components/PipiProps#getPropsInfo()
	 */
	public function getPropsInfo(){
		return $this->props['category']['name'].'('.$this->props['name'].')×1个月';
	}
	
	/* 
	 * @see bll/services/props/UserBuyPropsService#getNum()
	 */
	public function getNum(){
		return $this->num*30;
	} 
	/* 
	 * 重载isPurchased
	 * 
	 * @see lib/components/PipiProps#isPurchased()
	 */
	public function isPurchased(){
		if(parent::isPurchased()){
			if(empty($this->exist)){
				return true;
			}
			if($this->exist['valid_time'] > $this->timeStamp ){
				$days = intval(($this->exist['valid_time']-$this->timeStamp) / (3600*24));
				$this->errorCode = Yii::t('props','You are aleady buy monthcard,{days} day renewal',array('{days}'=>$days));
			 	return false;
			}
			return true;
		}
		return false;
	}
	
	public function afterBuy(){
		parent::afterBuy();
		$userJson['mc'] = array('num'=>99,'img'=>$this->props['image'],'vt'=>$this->getPropsValidTime());
		$userJsonInfoService = new UserJsonInfoService();
		$userJsonInfoService->setUserInfo($this->users['uid'],$userJson);
		$zmq = $this->getZmq();
		$zmq->sendZmqMsg(609,array('type'=>'update_json','uid'=>$this->users['uid'],'json_info'=>$userJson));
	}
	
}