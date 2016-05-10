<?php
/**
 * 购买贴条
 *
 * the last known user to change this file in the repository  <$LastChangedBy: leiwei $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: BuyLabelPropsService.php 15921 2013-10-15 08:53:59Z leiwei $
 * @package　service
 * @subpackage props
 */
class BuyLabelPropsService extends UserBuyPropsService{

	public $isSavePropsUse = true;

	public $isSavePropsBag = false;
	/**
	 * @var int 被贴条用户ID
	 */
	public $toUid = 0;
	/**
	 * @var int 档期ID
	 */
	public $archivesId = 0;
	/**
	 * @var int 是否为移除贴条操作
	 */
	public $isRemoveLable = false;
	/*
	 * @see lib/components/PipiProps#getPropsPrice()
	 */
	public function getPropsPrice(){
		if($this->isRemoveLable){
			$propsAttribute = $this->getPropsEnAttriubte();
			return $propsAttribute['label_remove_price']['value']*$this->num;
		}
		return $this->props['pipiegg']*$this->num;
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
		$propsAttribute = $this->getPropsEnAttriubte();
		if($this->isRemoveLable){
			return $this->timeStamp - $propsAttribute['label_timeout']['value']*60;
		}
		return $this->timeStamp+$propsAttribute['label_timeout']['value']*60;
	}

	public function  isPurchased(){
		if(parent::isPurchased()){
			$toUid = $this->getToUid();
			if($toUid <= 0){
				$this->errorCode = Yii::t('props','Stickers user does not exist');
				return false;
			}

			$userLatestLabel = self::$userPropsService->getUserLatestPropsOfUsedByCatId($toUid,$this->props['cat_id']);
			//移除贴条
			if($this->isRemoveLable){
				$this->isSavePropsUse = false;
				//没有使用过贴条
				if(empty($userLatestLabel)){
					$this->errorCode = Yii::t('props','Can only remove the stickers');
					return true;
				}

				if($this->getToUid() != $this->users['uid']){
					$this->errorCode = Yii::t('props','Can only remove the stickers');
					return false;
				}
				self::$userPropsService->updatePropsUseValidTime($userLatestLabel['record_id'],$this->getPropsValidTime());
				return true;
			}else{
				//没有使用过贴条
				if(empty($userLatestLabel)){
					return true;
				}
				//最后使用的贴条和本次使用的贴条一样
				if($this->props['prop_id'] == $userLatestLabel['prop_id']){
					return true;
				}
				//已经使用过贴条，贴条还未过期
				if($userLatestLabel['valid_time'] > $this->timeStamp){
					$userLatestLabelInfo = self::$propsService->getPropsByIds($userLatestLabel['prop_id'],false,true);
					if(empty($userLatestLabelInfo)){
						$this->errorCode = Yii::t('props','Stickers user does not exist');
						return false;
					}
					$userLatestLabelInfo = $userLatestLabelInfo[$userLatestLabel['prop_id']];
					$latestAttribute = self::$propsService->buildDataByIndex($userLatestLabelInfo['attribute'],'attr_enname');
					$localAttribute = $this->getPropsEnAttriubte();
					if($localAttribute['label_category']['value'] < $latestAttribute['label_category']['value']){
						$this->errorCode =  Yii::t('props','The ordinary stickers can not be covered senior stickers');
						return false;
					}
					return true;
				}
			}
			return true;

		}
		return false;
	}
	/*
	 * @see lib/components/PipiProps#getPropsInfo()
	 */
	public function getPropsInfo(){
		if($this->isRemoveLable)
			return Yii::t('props','remove_label').$this->props['category']['name'].'('.$this->props['name'].'*'.$this->num.')';
		return $this->props['category']['name'].'('.$this->props['name'].'*'.$this->num.')';
	}


	/*
	 * @see bll/services/props/UserBuyPropsService#getOperatePage()
	 */
	public function getOperatePage(){
		return 0;
	}

	public function afterBuy(){
		parent::afterBuy();
		$attriubte = $this->getPropsEnAttriubte();
		$userJson['lb'] = array('img'=>$attriubte['label_picture']['value'],'vt'=>$this->getPropsValidTime());
		$userJsonInfoService = new UserJsonInfoService();
		$userJsonInfoService->setUserInfo($this->getToUid(),$userJson);
		$userListService=new UserListService();
		$userListService->saveArchivesLabel($this->archivesId,$this->getToUid());
		$zmq = $this->getZmq();
		$zmq->sendZmqMsg(609,array('type'=>'update_json','uid'=>$this->getToUid(),'json_info'=>$userJson));
		$eventData['archives_id']=$this->archivesId;
		$eventData['domain']=DOMAIN;
		$eventData['type']='localroom';
		$userService = new UserService();
		$userBasic = $userService->getUserBasicByUids(array($this->users['uid'], $this->getToUid()));
		$json_content['type']='stickLabel';
		$json_content['uid']=$this->users['uid'];
		$json_content['nickname']=$userBasic[$this->users['uid']]['nickname'];
		$json_content['to_uid']=$this->getToUid();
		$json_content['to_nickname']=$userBasic[$this->getToUid()]['nickname'];
		$json_content['name']=$this->props['name'];
		$eventData['json_content']=$json_content;
		$zmq->sendZmqMsg(606,$eventData);
	}

	/*
	 * @see bll/services/props/UserBuyPropsService#getToTargetId()
	 */
	public function getToTargetId(){
		return $this->archivesId;
	}

	/*
	 * @see bll/services/props/UserBuyPropsService#getToUid()
	 */
	public function getToUid(){
		return $this->toUid;
	}

}