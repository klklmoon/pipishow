<?php

/**
 * 购买守护
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: BuyGuardianPropsService.php 8476 2013-04-07 13:56:31Z suqian $ 
 * @package　service
 * @subpackage props
 */
class BuyGuardianPropsService extends UserBuyPropsService {
	public $doteyId;
	
	protected function saveUserConsumeAttribute(){
		parent::saveUserConsumeAttribute();
		//存储守护主播的魅力值
		$attribute['uid'] = $this->getToTargetId();
		$attribute['charm'] = $this->getPropsCharm();
		$attribute['charm_points'] = $this->getPropsCharmPoints();
		self::$consumeService->saveUserConsumeAttribute($attribute);
	}
	
	public function getToTargetId(){
		return $this->doteyId;
	}
}
