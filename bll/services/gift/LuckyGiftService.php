<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: lei wei $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: LuckyGiftService 894 2010-12-28 07:55:25Z lei wei  $ 
 * @package Pipiservice
 * @subpackage LuckyGiftService
 */
define('DEFAULT_POOL_VALUE',0);     //储金初始值
define('NO_AWARD_TYPE',0);          //无奖励
define('GIFT_AWARD_TYPE',1);        //礼物奖励
define('PROP_AWARD_TYPE',2);        //道具奖励
define('PIPIEGG_AWARD_TYPE',3);     //皮蛋奖励

class LuckyGiftService extends PipiService {
	
	/**
	 * 存储奖池金额
	 * @param array $pool 奖池金额数据
	 * @return int
	 */
	public function saveGiftPool(array $pool){
		if (isset($pool['id']) && $pool['id'] <= 0) {
			return $this->setError(Yii::t('common', 'Parameter is empty'), 0);
		}
		$giftPoolModel=new GiftPoolModel();
		if (isset($pool['id'])) {
			$orggiftPoolModel = $giftPoolModel->findByPk($pool['id']);
			if (empty($orggiftPoolModel)) {
				return $this->setNotice('luckGift', Yii::t('gift', 'The gift pool does not exist'), 0);
			}
			$this->attachAttribute($orggiftPoolModel, $pool);
			if (!$orggiftPoolModel->validate()) {
				return $this->setNotices($orggiftPoolModel->getErrors(), 0);
			}
			$orggiftPoolModel->save();
			$insertId = $pool['id'];
			return $insertId;
		} else {
			$this->attachAttribute($giftPoolModel, $pool);
			if (!$giftPoolModel->validate()) {
				return $this->setNotices($giftPoolModel->getErrors(), 0);
			}
			$giftPoolModel->save();
			return $giftPoolModel->getPrimaryKey();
		}
	}
	
	/**
	 * 存储存储幸运礼物奖品
	 * @param array $award
	 * @return int
	 */
	public function saveGiftAward(array $award){
		if (isset($award['id']) && $award['id'] <= 0) {
			return $this->setError(Yii::t('common', 'Parameter is empty'), 0);
		}
		$giftAwardModel=new GiftAwardModel();
		if (isset($award['id'])) {
			$orggiftAwardModel = $giftAwardModel->findByPk($award['id']);
			if (empty($orggiftAwardModel)) {
				return $this->setNotice('luckGift', Yii::t('gift', 'The gift award does not exist'), 0);
			}
			$this->attachAttribute($orggiftAwardModel, $award);
			if (!$orggiftAwardModel->validate()) {
				return $this->setNotices($orggiftAwardModel->getErrors(), 0);
			}
			$orggiftAwardModel->save();
			$insertId = $award['id'];
			return $insertId;
		} else {
			$this->attachAttribute($giftAwardModel, $award);
			if (!$giftAwardModel->validate()) {
				return $this->setNotices($giftAwardModel->getErrors(), 0);
			}
			$giftAwardModel->save();
			return $giftAwardModel->getPrimaryKey();
		}
	}
	
	/**
	 * 奖池变化记录
	 * @param $pipiegg  奖池变化的皮蛋数
	 * @param $plus     0->奖池减少,1->奖池增加
	 * @return boolean 0->失败，1->成功 
	 */
	public function saveGiftPoolRecord($pipiegg,$plus=true){
		if($pipiegg<=0){
			return 0;
		}
		$giftPoolRecord=new GiftPoolRecordModel();
		return $giftPoolRecord->saveGiftPoolRecord($pipiegg,$plus);
	}
	
	public function editGiftPoolRecord($id,$value,$chance=null){
		if($value<=0 || $id<=0){
			return false;
		}
		$giftPoolRecord=new GiftPoolRecordModel();
		$orgModel = $giftPoolRecord->findByPk($id);
		if($orgModel){
			$data['value'] = $value;
			if(!is_null($chance)){
				$data['chance'] = $chance;
			}
			$this->attachAttribute($orgModel, $data);
			if(!$orgModel->validate()){
				return false;
			}else{
				$orgModel->save();
				return $id;
			}
		}else{
			return false;
		}
		return false;
	}
	
	public function saveUserAwardRecords(array $record){
		if ($record['uid'] <= 0) {
			return $this->setError(Yii::t('common', 'Parameter is empty'), 0);
		}
		$record['create_time']=time();
		$userAwardRecordsModel=new UserAwardRecordsModel();
		$this->attachAttribute($userAwardRecordsModel, $record);
		if (!$userAwardRecordsModel->validate()) {
			return $this->setNotices($userAwardRecordsModel->getErrors(), 0);
		}
		$userAwardRecordsModel->save();
		return $userAwardRecordsModel->getPrimaryKey();
	}
	
	/**
	 * 存储每日幸运星数据
	 * @param array $record
	 * @return boolean
	 */
	public function saveLuckStar(array $record){
		$otherRedisModel=new OtherRedisModel();
		return $otherRedisModel->saveLuckStar($record);
	}
	
	/**
	 * 根据奖池金额id删除奖池金额
	 * @param int|array $ids 奖池金额id
	 * @return boolean 0->失败 1->成功
	 */
	public function delGiftPoolByIds($Ids) {
		if (empty($Ids)) 
			return $this->setError(Yii::t('common', 'Parameter is error'), 0);
		if (!is_array($Ids) && !is_numeric($Ids)) 
			return $this->setError(Yii::t('common', 'Parameter is error'), 0);
		$Ids = is_array($Ids) ? $Ids : array($Ids);
		$giftPoolModel=new GiftPoolModel();
		return $giftPoolModel->delGiftPoolByIds($Ids);
	}
	
	/**
	 * 根据幸运礼物奖品id删除奖品
	 * @param int|array $ids 奖池金额id
	 * @return boolean 0->失败 1->成功
	 */
	public function delGiftAwardByIds($Ids) {
		if (empty($Ids)) 
			return $this->setError(Yii::t('common', 'Parameter is error'), 0);
		if (!is_array($Ids) && !is_numeric($Ids))
			return $this->setError(Yii::t('common', 'Parameter is error'), 0);
		$Ids = is_array($Ids) ? $Ids : array($Ids);
		$giftAwardModel=new GiftAwardModel();
		return $giftAwardModel->delGiftAwardByIds($Ids);
	}
	
	/**
	 * 根据幸运礼物礼物id删除奖品
	 * @param int $giftId 幸运礼物礼物id
	 * @return boolean 0->失败 1->成功
	 */
	public function delGiftAwardByGiftId($giftId){
		if (empty($giftId))
			return $this->setError(Yii::t('common', 'Parameter is error'), 0);
		$giftAwardModel=new GiftAwardModel();
		return $giftAwardModel->delGiftAwardByGiftId($giftId);
	}
	
	/**
	 * 获取档期奖池奖金值
	 * @return array
	 */
	public function getLastGiftPoolRecord(){
		$giftPoolRecordModel=new GiftPoolRecordModel();
		$data=$giftPoolRecordModel->getLastGiftPoolRecord();
		return $data?$data->attributes:array();
	}
	
	
	/**
	 * 根据奖池金额获取A值
	 * @param int $value  当前奖池金额
	 * @return float  
	 */
	public function getGiftPoolByValue($value){
		$giftPoolModel=new GiftPoolModel();
		$data=$giftPoolModel->getGiftPoolByValue($value);
		return isset($data->attributes)?$data->attributes:array();
	}
	
	/**
	 * 根据礼物Id获取幸运礼物的中奖奖项
	 * @param int $giftId 礼物Id
	 * @return array
	 */
	public function getGiftAwardByGiftId($giftId){
		if($giftId<=0)
			return $this->setError(Yii::t('common', 'Parameter is error'), 0);
		$giftAwardModel=new GiftAwardModel();
		$data=$giftAwardModel->getGiftAwardByGiftId(array($giftId));
		return $this->arToArray($data);
	}
	
	/**
	 * 获取幸运礼物的奖励
	 * @param int $giftId 幸运礼物Id
	 * @param int $num  赠送数量
	 * @return array  奖励
	 */
	public function getLuckyGiftAward($giftId,$num){
		$poolRecord=$this->getLastGiftPoolRecord();
		if($poolRecord){
			$giftPool=$this->getGiftPoolByValue($poolRecord['value']);
			$poolChance=isset($giftPool['chance'])?$giftPool['chance']:0;
		}else{
			$giftPool=$this->getGiftPoolByValue(DEFAULT_POOL_VALUE);
			$poolChance=$giftPool['chance'];
		}
		
		$giftAward=$this->getGiftAwardByGiftId($giftId);
		$giftChance=array();
		if($giftAward){
			foreach($giftAward as $key=>$row){
				if($row['type']>0){
					if(((float)$row['chance']+(float)$poolChance)>0){
						$giftChance[$key]=($row['chance']+$poolChance)*10000;
					}
				}else{
					$giftChance[$key]=$row['chance']*10000;
				}
			}
		}
		
		$giftSevice=new GiftService();
		$propsService=new PropsService();
		$award=array();
		for($i=0;$i<$num;$i++){
			$result=$this->getRandGiftAward($giftChance);
			if($giftAward[$result]['type']>0){
				$giftAward[$result]['id']=$i;
				$giftAward[$result]['zh_name']='';
				if($giftAward[$result]['type']==GIFT_AWARD_TYPE){
					$giftInfo=$giftSevice->getGiftByIds($giftAward[$result]['target_id']);
					$giftAward[$result]['zh_name']=$giftInfo[$giftAward[$result]['target_id']]['zh_name'];
				}elseif($giftAward[$result]['type']==PROP_AWARD_TYPE){
					$propsInfo=$propsService->getPropsByIds($giftAward[$result]['target_id']);
					$giftAward[$result]['zh_name']=$propsInfo[$giftAward[$result]['target_id']]['name'];
				}elseif($giftAward[$result]['type']==PIPIEGG_AWARD_TYPE){
					$giftInfo=$giftSevice->getGiftByIds($giftAward[$result]['gift_id']);
					$giftAward[$result]['zh_name']=$giftInfo[$giftAward[$result]['gift_id']]['pipiegg']*$giftAward[$result]['award'];
				}
				$award[]=$giftAward[$result];
			}
		}
		return $award;
	}
	
	/**
	 * 获取礼物奖池列表
	 * @author supeng
	 * @return Ambigous <multitype:, multitype:NULL , NULL, unknown, multitype:unknown Ambigous <unknown, NULL> , mixed, multitype:unknown >
	 */
	public function getGiftPoolList(){
		$giftPoolModel=new GiftPoolModel();
		$data=$giftPoolModel->getGiftPoolList();
		if($data){
			$data = $this->arToArray($data);
		}
		return $data;
	}
	
	/**
	 * 获取每日幸运星数据
	 * @return array
	 */
	public function getLuckStar(){
		$otherRedisModel=new OtherRedisModel();
		return $otherRedisModel->getLuckStar();
	}
	
	/**
	 * 根据条件查询用户获奖记录
	 * @param array $condition 获奖记录查询条件
	 * @return array
	 */
	public function getUserAwardRecords(Array $condition = array()){
		$userAwardRecords=new UserAwardRecordsModel();
		$data=$userAwardRecords->getUserAwardRecords($condition);
		return isset($data->attributes)?$data->attributes:array();
	}

	public function sendGiftAward(array $record,array $award){
		if(empty($record)||empty($award)||$record['uid']<=0||$record['record_sid']<=0)
			return $this->setError(Yii::t('common', 'Parameter is error'), 0);
		$totalPipiegg=$awardPipiegg=0;
		$consumeService=new ConsumeService();
		$giftService=new GiftService();
		$propService=new PropsService();
		$userPropsService=new UserPropsService();
		$userService=new UserService();
		$giftBagService=new GiftBagService();
		$userPropsBagModel = new UserPropsBagModel();
		foreach($award as $row){
			if($row['type']==GIFT_AWARD_TYPE){
				//礼物奖励
				$giftInfo=$giftService->getGiftByIds($row['target_id']);
				$totalPipiegg+=$giftInfo[$row['target_id']]['pipiegg']*$row['award'];
				$gift['gift_id']=$row['target_id'];
				$gift['num']=$row['award'];
				$gift['uid']=$record['uid'];
				$bagRecord['uid']=$record['uid'];
				$userBasic=$userService->getUserBasicByUids(array($record['uid']));
				$bagRecord['gift_id']=$row['target_id'];
				$bagRecord['num']=$row['award'];
				$bagInfo['uid']=$record['uid'];
				$bagInfo['nickname']=$userBasic[$record['uid']]['nickname'];
				$bagInfo['gift_id']=$row['target_id'];
				$bagInfo['gift_name']=$giftInfo[$row['target_id']]['zh_name'];
				$bagInfo['num']=$row['award'];
				$bagInfo['remark']='幸运礼物奖励';
				$bagRecord['info']=serialize($bagInfo);
				$bagRecord['source']=BAGSOURCE_TYPE_AWARD;
				$bagRecord['sub_source']='luckGifts';
				$record_sid=$giftBagService->saveUserGiftBagByUid($gift,$bagRecord);
				$awardRecord['target_id']=$row['gift_id'];
				$awardRecord['to_target_id']=$row['target_id'];
				$awardRecord['num']=$row['award'];
				$awardRecord['pipiegg']=$giftInfo[$row['target_id']]['pipiegg']*$row['award'];
				$awardRecord['info']=$giftInfo[$row['target_id']]['zh_name'].'X'.$row['award'];
			}elseif($row['type']==PROP_AWARD_TYPE){
				//道具奖励
				$props=$propService->getPropsByIds($row['target_id'],true);
				$totalPipiegg+=$props[$row['target_id']]['pipiegg']*$row['award'];
				$records['uid'] = $record['uid'];
				$records['cat_id'] = $props[$row['target_id']]['cat_id'];
				$records['prop_id'] = $row['target_id'];
				$records['info'] = $props[$row['target_id']]['category']['name'].'('.$props[$row['target_id']]['name'].')';;
				$records['source'] = PROPSRECORDS_SOURCE_AWARD;
				$records['amount'] = $row['award'];
				$record_sid=$userPropsService->saveUserPropsRecords($records);
				if($row['award']>0){
					$userPropsBagModel->updateCounters(array('num'=>$row['award']),'uid='.$record['uid'].' AND prop_id='.$row['target_id'].' AND cat_id='.$props[$row['target_id']]['cat_id']);
				}
				
				$awardRecord['target_id']=$row['gift_id'];
				$awardRecord['to_target_id']=$row['target_id'];
				$awardRecord['num']=$row['award'];
				$awardRecord['pipiegg']=$props[$row['target_id']]['pipiegg']*$row['award'];
				$awardRecord['info']=$props[$row['target_id']]['name'].'X'.$row['award'];
			}elseif($row['type']==PIPIEGG_AWARD_TYPE){
				//皮蛋奖励
				$giftInfo=$giftService->getGiftByIds($row['gift_id']);
				$totalPipiegg+=$row['award']*$giftInfo[$row['gift_id']]['pipiegg'];
				$awardPipiegg+=$row['award']*$giftInfo[$row['gift_id']]['pipiegg'];
				$awardRecord['target_id']=$row['gift_id'];
				$awardRecord['num']=$row['award'];
				$awardRecord['pipiegg']= $row['award']*$giftInfo[$row['gift_id']]['pipiegg'];
				$awardRecord['info']='奖励'.$row['award']*$giftInfo[$row['gift_id']]['pipiegg'].'个皮蛋';
			}
			$awardRecord['record_sid']=$record['record_sid'];
			$awardRecord['uid']=$record['uid'];
			$awardRecord['type']=$row['type'];
			$awardRecord['source']=$record['source'];
			$awardRecord['sub_source']=$record['sub_source'];
			$this->saveUserAwardRecords($awardRecord);
		}
		if($awardPipiegg>0){
			if($consumeService->addEggs($record['uid'],$awardPipiegg)>0){
				$consumeService->saveUserConsumeAttribute(array('uid'=>$record['uid'],'pipiegg'=>$awardPipiegg));
				$pipieggRecords['uid'] = $record['uid'];
				$pipieggRecords['pipiegg'] = $awardPipiegg;
				$pipieggRecords['num'] = 1;
				$pipieggRecords['from_target_id'] = $row['gift_id'];
				$pipieggRecords['to_target_id'] = $record['archives_id'];
				$pipieggRecords['record_sid']=$record['record_sid'];
				$pipieggRecords['source']=$record['source'];
				$pipieggRecords['sub_source']=SUBSOURCE_LUCK_GIFT_AWARD;
				$pipieggRecords['extra']='中奖赠品';
				$record_sid=$consumeService->saveUserPipiEggRecords($pipieggRecords, true);
			}else{
				$filename = DATA_PATH.'runtimes/luckGift.txt';
				error_log(date("Y-m-d H:i:s")."幸运礼物中奖皮蛋，用户增加皮蛋失败uid：".$record['uid'].",皮蛋数:".$awardPipiegg."\n\r",3,$filename);
			}
		}
		$this->saveGiftPoolRecord($totalPipiegg,false);
		return true;
	}
		/**
	 * 礼物中奖概率设置查询
	 * @author supeng
	 * @param array $condition
	 * @param unknown_type $offset
	 * @param unknown_type $pageSize
	 * @param unknown_type $isLimit
	 * @return Ambigous <multitype:, multitype:NULL , NULL, unknown, multitype:unknown Ambigous <unknown, NULL> , mixed, multitype:unknown >
	 */
	public function searchGiftAwardList(Array $condition = array(),$offset=0,$pageSize=20,$isLimit=true){
		$model = new GiftAwardModel();
		$data = $model->searchGiftAwardList($condition,$offset,$pageSize,$isLimit);
		if($data['list']){
			$data['list'] = $this->arToArray($data['list']);
		}
		return $data;
	}
	
	
	
	/**
	 * 奖池记录
	 * @author supeng
	 * @param array $condition
	 * @param unknown_type $offset
	 * @param unknown_type $pageSize
	 * @param unknown_type $isLimit
	 * @return Ambigous <multitype:, multitype:NULL , NULL, unknown, multitype:unknown Ambigous <unknown, NULL> , mixed, multitype:unknown >
	 */
	public function searchGiftPoolRecord(Array $condition = array(),$offset=0,$pageSize=20,$isLimit=true){
		$model = new GiftPoolRecordModel();
		$data = $model->searchGiftPoolRecord($condition,$offset,$pageSize,$isLimit);
		if($data['list']){
			$data['list'] = $this->arToArray($data['list']);
		}
		return $data;
	}
	
	public function searchUserAwardRecords(Array $condition = array(),$offset=0,$pageSize=20,$isLimit=true){
		$model = new UserAwardRecordsModel();
		$data = $model->searchUserAwardRecords($condition,$offset,$pageSize,$isLimit);
		if($data['list']){
			$data['list'] = $this->arToArray($data['list']);
		}
		return $data;
	}
	
	protected function getRandGiftAward(array $giftAward) { 

	    $result = array();
	    //概率数组的总概率精度 
	    $giftChance = array_sum($giftAward); 
	    //概率数组循环 
	   foreach ($giftAward as $key=>$row) {
    		$randNum = mt_rand(0, $giftChance);
    		if ($randNum <= $row) {
    			$result= $key;
    			break;
    		} else {
    			$giftChance -= $row;
    		}
    		 
    	}
	   unset ($giftAward);
	   return $result; 
	} 
}

?>