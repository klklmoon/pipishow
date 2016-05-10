<?php
/**
 * 骰子操作服务层
 * the last known user to change this file in the repository  <$LastChangedBy: lei wei $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z lei wei  $ 
 * @package 
 */
define('DICE_VALID_TIME', 300);  //骰子对局有效时间
define('DICE_SEND_TYPE',1);      //骰子对局发起对局
define('DICE_RECEVICE_TYPE',2);  //骰子对局接受对局
define('DICE_RESULT_TYPE',3);    //骰子对局对局结果
define('COMMON_DICE_SOURCE','common_dice');  //普通骰子对局
define('RED_DICE_SOURCE','red_dice');        //红色骰子对局
define('GOLD_DICE_SOURCE','gold_dice');       //金色骰子对局
define('ALLOW_USE_RANK',2);                 //绅士1用户可以使用骰子游戏
define('WIN_GAME',1);                 //胜局
define('LOST_GAME',2);                //败局
define('DRAW_GAME',3);                //和局
class DiceService extends PipiService{
	
	/**
	 * 发送骰子对局
	 * @param int $archives_id 档期Id
	 * @param int $uid  发送者uid
	 * @param int $to_uid 接受者uid，为0时是对所有人
	 * @param string $type 发送骰子的骰子类型
	 * @return boolean 1->成功,0->失败
	 */
	public function sendDice($archives_id,$uid,$to_uid,$type='common_dice'){
		if($uid<=0||$archives_id<=0)
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		if($uid==$to_uid){
			return $this->setError(Yii::t('dice','Don not throw the dice to yourself'));
		}
		$userJsonInfoService = new UserJsonInfoService();
		if($to_uid>0){
			$userBasic = $userJsonInfoService->getUserInfos(array($uid, $to_uid),false);
		}else{
			$userBasic = $userJsonInfoService->getUserInfos(array($uid),false);
		}
		if($userBasic[$uid]['rk']<ALLOW_USE_RANK&&!$this->hasBit($userBasic[$uid]['ut'], USER_TYPE_DOTEY)){
			return $this->setError(Yii::t('dice','The gentleman over 1 game player can use dice'));
		}
		$propsSevice=new PropsService();
		$props=$propsSevice->getPropsByEnName($type);
		
		if(empty($props))
			return $this->setError(Yii::t('props', 'The props does not exist'), 0);
		$propInfo = $propsSevice->getPropsByIds($props['prop_id'], true, true);
		$attribute = $propsSevice->buildDataByIndex($propInfo[$props['prop_id']]['attribute'], 'attr_enname');
		if($attribute['dice_all_user']['value']!=1&&$to_uid<=0){
			return $this->setError(Yii::t('dice','Not to all the people to throw the dice'));
		}
		if($props['pipiegg']>0){
			$consumeService=new ConsumeService();
			$result=$consumeService->freezeEggs($uid,$props['pipiegg']);
			if($result<=0){
				return $this->setError(Yii::t('common', 'Pipiegg not enough'), 0);
			}
		}
		
		
		
		$diceRecord['uid']=$uid;
		$diceRecord['target_id']=$archives_id;
		$diceRecord['prop_id']=$props['prop_id'];
		$diceRecord['to_uid']=$to_uid;
		$diceRecord['pipiegg']=$props['pipiegg'];
		$diceRecord['type']=DICE_SEND_TYPE;
		$diceRecord['source']=$type;
		$info['uid']=$uid;
		$info['nickname']=str_replace('|', '',$userBasic[$uid]['nk']);
		if($to_uid>0){
			$info['to_uid']=$uid;
			$info['to_nickname']=str_replace('|', '',$userBasic[$to_uid]['nk']);
		}
		$points=array();
		for($i=1;$i<=$attribute['dice_num']['value'];$i++){
			$points[]=rand(1,6);
		}
		$info['points']=$points;
		$diceRecord['info']=serialize($info);
		$diceRecord['valid_time']=time()+DICE_VALID_TIME;
		$recordId=$this->saveUserDiceRecords($diceRecord);
		if($recordId<=0){
			return $this->setError(Yii::t('dice', 'User dice Records save failed'), 0);
		}
		$zmq=$this->getZmq();
		$zmqData['archives_id']=$archives_id;
		$zmqData['domain']=DOMAIN;
		$zmqData['type']='localroom';
		$json_content['type']='dice';
		$json_content['send_type']=DICE_SEND_TYPE;
		$json_content['dice_type']=$type;
		$json_content['uid']=$uid;
		$json_content['nk']=str_replace('|', '',$userBasic[$uid]['nk']);
		$json_content['rk']=$userBasic[$uid]['rk'];
		$userListService=new UserListService();
		$json_content['pk']=($userListService->getPurviewRank($userBasic[$uid]['pk'])==true)?1:0;
		$json_content['lb']=($userListService->getValidProps($userBasic[$uid]['lb'])==true)?1:0;
		$json_content['mc']=($userListService->getValidProps($userBasic[$uid]['mc'])==true)?1:0;
		$json_content['md']=$userListService->getUserMedals($userBasic[$uid]['md']);
		if($to_uid>0){
			$json_content['to_uid']=$to_uid;
			$json_content['to_nk']=str_replace('|', '',$userBasic[$to_uid]['nk']);
			$json_content['to_rk']=$userBasic[$to_uid]['rk'];
			$json_content['to_pk']=($userListService->getPurviewRank($userBasic[$to_uid]['pk'])==true)?1:0;
			$json_content['to_lb']=($userListService->getValidProps($userBasic[$to_uid]['lb'])==true)?1:0;
			$json_content['to_mc']=($userListService->getValidProps($userBasic[$to_uid]['mc'])==true)?1:0;
			$json_content['to_md']=$userListService->getUserMedals($userBasic[$to_uid]['md']);
		}
		$json_content['record_id']=$recordId;
		$json_content['valid_time']=$diceRecord['valid_time'];
		$json_content['points']=$points;
		$json_content['num']=count($points);
		$zmqData['json_content']=$json_content;
		$zmq->sendBrodcastMsg($zmqData);
		if($props['pipiegg']>0){
			$fRecords['uid']=$uid;
			$fRecords['from_target_id']=$props['prop_id'];
			$fRecords['to_target_id']=$archives_id;
			$fRecords['record_sid']=$recordId;
			$fRecords['pipiegg']=$props['pipiegg'];
			$fRecords['source']=SOURCE_PROPS;
			$fRecords['sub_source']=SUBSOURCE_PROPS_DICE;
			$fRecords['num']=1;
			$fRecords['extra']='冻结'.$props['pipiegg'].'个皮蛋(骰子游戏)';
			$consumeService->saveUserFreezeePipiEggRecords($fRecords,true);
		}
		self::saveLastSendDiceTime($uid);
		return true;
	}
	
	
	/**
	 * 接受发送的骰子对局
	 * @param int $recordId 发送对局的记录Id
	 * @param int $uid  接受人的uid
	 * @param int $archives_id 档期Id
	 * @return boolean 1->成功,0->失败
	 */
	public function receiveDice($recordId,$uid,$archives_id){
		if($recordId<=0||$uid<=0||$archives_id<=0)
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		
		$record=UserDiceRecordsModel::model()->findByPk($recordId);
		$record=$record->attributes;
		
		if(!$record)
			return $this->setError(Yii::t('dice', 'User dice record not exits'), 0);
		
		
		if($record['valid_time']-time()<0)
			return $this->setError(Yii::t('dice', 'User dice record is invalid'), 0);
		$propsService=new PropsService();
		$propInfo = $propsService->getPropsByIds($record['prop_id'], true, true);
		$attribute = $propsService->buildDataByIndex($propInfo[$record['prop_id']]['attribute'], 'attr_enname');
		$consumeService=new ConsumeService();
		if($propInfo[$record['prop_id']]['pipiegg']>0){
			if($consumeService->freezeEggs($uid,$propInfo[$record['prop_id']]['pipiegg'])<=0){
				return $this->setError(Yii::t('common', 'Pipiegg not enough'), 0);
			}
		}
		if($this->updateDiceRecord($recordId)<=0){
			return $this->setError(Yii::t('dice', 'Please do not accept to repeat again'), 0);
		}
		$userJsonInfoService = new UserJsonInfoService();
		$userBasic = $userJsonInfoService->getUserInfos(array($uid,$record['uid']),false);
		$diceRecord['uid']=$uid;
		$diceRecord['target_id']=$archives_id;
		$diceRecord['prop_id']=$record['prop_id'];
		$diceRecord['to_uid']=$record['uid'];
		$diceRecord['record_sid']=$recordId;
		$diceRecord['pipiegg']=$record['pipiegg'];
		$diceRecord['type']=DICE_RECEVICE_TYPE;
		$diceRecord['source']=$record['source'];
		$info['uid']=$uid;
		$info['nickname']=str_replace('|', '',$userBasic[$uid]['nk']);
		$info['to_uid']=$record['uid'];
		$info['to_nickname']=str_replace('|', '',$userBasic[$record['uid']]['nk']);
		$points=array();
		for($i=1;$i<=$attribute['dice_num']['value'];$i++){
			$points[]=rand(1,6);
		}
		$info['points']=$points;
		$diceRecord['info']=serialize($info);
		$recordId=$this->saveUserDiceRecords($diceRecord);
		$zmq=$this->getZmq();
		$zmqData['archives_id']=$archives_id;
		$zmqData['domain']=DOMAIN;
		$zmqData['type']='localroom';
		$json_content['type']='dice';
		$json_content['send_type']=DICE_RECEVICE_TYPE;
		$json_content['dice_type']=$record['source'];
		$json_content['uid']=$uid;
		$json_content['nk']=str_replace('|', '',$userBasic[$uid]['nk']);
		$json_content['rk']=$userBasic[$uid]['rk'];
		$userListService=new UserListService();
		$json_content['pk']=($userListService->getPurviewRank($userBasic[$uid]['pk'])==true)?1:0;
		$json_content['lb']=($userListService->getValidProps($userBasic[$uid]['lb'])==true)?1:0;
		$json_content['mc']=($userListService->getValidProps($userBasic[$uid]['mc'])==true)?1:0;
		$json_content['md']=$userListService->getUserMedals($userBasic[$uid]['md']);
		if($record['to_uid']>0){
			$json_content['to_uid']=$record['uid'];
			$json_content['to_nk']=str_replace('|', '',$userBasic[$record['uid']]['nk']);
			$json_content['to_rk']=$userBasic[$record['uid']]['rk'];
			$json_content['to_pk']=($userListService->getPurviewRank($userBasic[$record['uid']]['pk'])==true)?1:0;
			$json_content['to_lb']=($userListService->getValidProps($userBasic[$record['uid']]['lb'])==true)?1:0;
			$json_content['to_mc']=($userListService->getValidProps($userBasic[$record['uid']]['mc'])==true)?1:0;
			$json_content['to_md']=$userListService->getUserMedals($userBasic[$record['uid']]['md']);
		}
		$json_content['record_id']=$record['record_id'];
		$json_content['points']=$points;
		$json_content['num']=count($points);
		$zmqData['json_content']=$json_content;
		$zmq->sendBrodcastMsg($zmqData);
		if($recordId<=0){
			return $this->setError(Yii::t('dice', 'User dice Records save failed'), 0);
		}
		if($propInfo[$record['prop_id']]['pipiegg']>0){
			$tofRecords['uid']=$uid;
			$tofRecords['from_target_id']=$record['prop_id'];
			$tofRecords['to_target_id']=$archives_id;
			$tofRecords['record_sid']=$recordId;
			$tofRecords['pipiegg']=$propInfo[$record['prop_id']]['pipiegg'];
			$tofRecords['source']=SOURCE_PROPS;
			$tofRecords['sub_source']=SUBSOURCE_PROPS_DICE;
			$tofRecords['num']=1;
			$tofRecords['extra']='冻结'.$propInfo[$record['prop_id']]['pipiegg'].'个皮蛋(骰子游戏)';
			$consumeService->saveUserFreezeePipiEggRecords($tofRecords,true);
		}
		$orgInfo=unserialize($record['info']);
		$diceResult=$this->checkDicePoints($orgInfo['points'],$points);
		if($diceResult==DRAW_GAME){
			//骰子对局平局,返还各自的皮蛋
			if($record['pipiegg']>0){
				$consumeService->unFreezeEggs($uid,$record['pipiegg']);
				$consumeService->unFreezeEggs($record['uid'],$record['pipiegg']);
			}
		}else{
			
			if($attribute['dice_reward_num']['value']>0){
				$giftBagService=new GiftBagService();
				//对局获胜者，获得骰子奖励
				$gift['gift_id']=$attribute['dice_reward']['value'];
				$gift['num']=$attribute['dice_reward_num']['value'];
				if($diceResult==WIN_GAME){
					$gift['uid']=$record['uid'];
					$bagRecord['uid']=$record['uid'];
					$bagInfo['uid']=$record['uid'];
					$bagInfo['nickname']=$userBasic[$record['uid']]['nk'];
				}else{
					$gift['uid']=$uid;
					$bagRecord['uid']=$uid;
					$bagInfo['nickname']=$userBasic[$uid]['nk'];
				}
				$bagRecord['gift_id']=$attribute['dice_reward']['value'];
				$bagRecord['num']=$attribute['dice_reward_num']['value'];
				$bagInfo['gift_id']=$attribute['dice_reward']['value'];
				$bagInfo['num']=$attribute['dice_reward_num']['value'];
				$bagInfo['remark']='游戏奖励';
				$bagRecord['info']=serialize($bagInfo);
				$bagRecord['source']=BAGSOURCE_TYPE_ADMIN;
				$bagRecord['sub_source']=SUBSOURCE_PROPS_DICE;
				$giftBagService->saveUserGiftBagByUid($gift,$bagRecord);
			}
		
		}
		$resultData['archives_id']=$archives_id;
		$resultData['domain']=DOMAIN;
		$resultData['type']='localroom';
		$result_content['type']='dice_result';
		$result_content['send_type']=DICE_RESULT_TYPE;
		$result_content['dice_type']=$record['source'];
		$result_content['uid']=$record['uid'];
		$result_content['result']=$diceResult;
		$result_content['nk']=str_replace('|', '',$userBasic[$record['uid']]['nk']);
		$result_content['to_uid']=$uid;
		$result_content['to_nk']=str_replace('|', '',$userBasic[$uid]['nk']);
		$dicePoints=serialize($record['info']);
		$result_content['num']=$attribute['dice_num']['value'];
		$result_content['points']=$orgInfo['points'];
		$result_content['to_points']=$points;
		if(($diceResult==WIN_GAME||$diceResult==LOST_GAME)&&$attribute['dice_reward_num']['value']>0){
			$result_content['gift_id']=$attribute['dice_reward']['value'];
			$result_content['gift_num']=$attribute['dice_reward_num']['value'];
			$giftService=new GiftService();
			$gift=$giftService->getGiftByIds($attribute['dice_reward']['value']);
			$result_content['zh_name']=$gift[$attribute['dice_reward']['value']]['zh_name'];
		}
		$result_content['record_id']=$recordId;
		$resultData['json_content']=$result_content;
		$zmq->sendBrodcastMsg($resultData);
		//存储骰子对局结果记录
		$results['uid']=$record['uid'];
		$results['target_id']=$archives_id;
		$results['prop_id']=$record['prop_id'];
		$results['to_uid']=$uid;
		$results['record_sid']=$recordId;
		$results['pipiegg']=$record['pipiegg'];
		$results['type']=DICE_RESULT_TYPE;
		$results['result']=$diceResult;
		$results['source']=$record['source'];
		$resultInfo['uid']=$record['uid'];
		$resultInfo['nickname']=str_replace('|', '',$userBasic[$record['uid']]['nk']);
		$resultInfo['to_uid']=$uid;
		$resultInfo['to_nickname']=str_replace('|', '',$userBasic[$uid]['nk']);
		
		if(($diceResult==WIN_GAME||$diceResult==LOST_GAME)&&$attribute['dice_reward_num']['value']>0){
			$resultInfo['gift_id']=$attribute['dice_reward']['value'];
			$resultInfo['gift_num']=$attribute['dice_reward_num']['value'];
			$giftService=new GiftService();
			$gift=$giftService->getGiftByIds($attribute['dice_reward']['value']);
			$resultInfo['zh_name']=$gift[$attribute['dice_reward']['value']]['zh_name'];
		}
		$resultInfo['points']=$orgInfo['points'];
		$resultInfo['to_points']=$points;
		$results['info']=serialize($resultInfo);
		$resultRecordId=$this->saveUserDiceRecords($results);
		if($propInfo[$record['prop_id']]['pipiegg']>0&&($diceResult==WIN_GAME||$diceResult==LOST_GAME)){
			if($consumeService->unAddFreezeEggs($uid,$record['pipiegg'])<=0){
				return $this->setError(Yii::t('common', 'Pipiegg not enough'), 0);
			}
			$pipieggRecords['uid'] = $uid;
			$pipieggRecords['pipiegg'] = $record['pipiegg'];
			$pipieggRecords['from_target_id'] = $record['prop_id'];
			$pipieggRecords['num'] = 1;
			$pipieggRecords['record_sid']=$resultRecordId;
			$pipieggRecords['to_target_id'] = $archives_id;
			$pipieggRecords['source']=SOURCE_PROPS;
			$pipieggRecords['sub_source']=SUBSOURCE_PROPS_DICE;
			$pipieggRecords['extra']='骰子游戏';
			$consumeService->saveUserPipiEggRecords($pipieggRecords, false);
			$consumeService->saveUserConsumeAttribute(array('uid'=>$uid,'pipiegg'=>$record['pipiegg']));
			if($consumeService->unAddFreezeEggs($record['uid'],$record['pipiegg'])<=0){
				return $this->setError(Yii::t('common', 'Pipiegg not enough'), 0);
			}
			$to_pipieggRecords['uid'] = $record['uid'];
			$to_pipieggRecords['pipiegg'] = $record['pipiegg'];
			$to_pipieggRecords['from_target_id'] = $record['prop_id'];
			$to_pipieggRecords['num'] = 1;
			$to_pipieggRecords['record_sid']=$resultRecordId;
			$to_pipieggRecords['to_target_id'] = $archives_id;
			$to_pipieggRecords['source']=SOURCE_PROPS;
			$to_pipieggRecords['sub_source']=SUBSOURCE_PROPS_DICE;
			$to_pipieggRecords['extra']='骰子游戏';
			$consumeService->saveUserPipiEggRecords($to_pipieggRecords, false);
			$consumeService->saveUserConsumeAttribute(array('uid'=>$record['uid'],'pipiegg'=>$record['pipiegg']));
		}
		$fRecords=$uid;
		$fRecords['from_target_id']=$record['prop_id'];
		$fRecords['to_target_id']=$archives_id;
		$fRecords['record_sid']=$resultRecordId;
		$fRecords['pipiegg']=$record['pipiegg'];
		$fRecords['source']=SOURCE_PROPS;
		$fRecords['sub_source']=SUBSOURCE_PROPS_DICE;
		$fRecords['num']=1;
		$fRecords['extra']='解冻'.$record['pipiegg'].'个皮蛋(骰子游戏)';
		$consumeService->saveUserFreezeePipiEggRecords($fRecords,false);
		$toRecords['uid']=$record['uid'];
		$toRecords['from_target_id']=$record['prop_id'];
		$toRecords['to_target_id']=$archives_id;
		$toRecords['record_sid']=$resultRecordId;
		$toRecords['pipiegg']=$record['pipiegg'];
		$toRecords['source']=SOURCE_PROPS;
		$toRecords['sub_source']=SUBSOURCE_PROPS_DICE;
		$toRecords['num']=1;
		$toRecords['extra']='解冻'.$record['pipiegg'].'个皮蛋(骰子游戏)';
		$consumeService->saveUserFreezeePipiEggRecords($toRecords,false);
		return true;
	}
	
	public function saveUserDiceRecords(array $records){
		if($records['uid']<=0||$records['target_id']<=0)
			return $this->setError(Yii::t('common', 'Parameter is empty'), 0);
		$records['create_time'] = time();
		$userDiceRecordsModel =new UserDiceRecordsModel();
		$this->attachAttribute($userDiceRecordsModel, $records);
		if (!$userDiceRecordsModel->validate()) {
			return $this->setNotices($userDiceRecordsModel->getErrors(), 0);
		}
		$userDiceRecordsModel->save();
		return $userDiceRecordsModel->getPrimaryKey();
	}
	
	/**
	 * 存储用户发送骰子限制时间
	 * @param int $uid       用户uid
	 * @param int $expirTime 失效时间
	 * @return boolean       0->失败，1->成功
	 */
	public function saveLastSendDiceTime($uid,$expirTime=20){
		if($uid<=0)
			return $this->setError(Yii::t('common', 'Parameter is empty'),0);
		$otherRedisModel=new OtherRedisModel();
		return $otherRedisModel->saveLastSendDiceTime($uid,$expirTime);
	}
	
	public function getDiceGameRecords($archives_id){
		if($archives_id<=0){
			return $this->setError(Yii::t('common', 'Parameter is empty'), array());
		}
		$otherRedisModel=new OtherRedisModel();
		return $otherRedisModel->getDiceRecord($archives_id);
	}
	
	public function updateDiceRecord($recordId){
		if($recordId<=0)
			return $this->setError(Yii::t('common', 'Parameter is empty'), 0);
		$userDiceRecordsModel=new UserDiceRecordsModel();
		return $userDiceRecordsModel->updateDiceRecord($recordId);
	}
	
	
	/**
	 * 获取用户玩骰子的记录
	 * @author guoshaobo
	 * @param int $uid
	 * @param int $offset
	 * @param int $limit
	 * @param array $attribute
	 * @return array
	 */
	public function getUserDiceRecord($uid, $offset = 0, $limit = 10, $attribute = array())
	{
		if($uid <= 0){
			return $this->setError(Yii::t('common', 'Parameter is empty'), array('count'=>0,'list'=>array()));
		}
		$userDiceRecordsModel =new UserDiceRecordsModel();
		$res = $userDiceRecordsModel->getUserDiceRecord($uid, $offset, $limit, $attribute);
		if($res){
			$res['list'] = $this->arToArray($res['list']);
			return $res;
		}
		return array('count'=>0,'list'=>array());
	}
	
	
	/**
	 * 获取用户是否被限制发送骰子
	 * @param int $uid   用户uid
	 * @return boolean 0->不限制，1->受限
	 */
	public function getLastSendDiceTime($uid){
		if($uid<=0)
			return $this->setError(Yii::t('common', 'Parameter is empty'), 0);
		$otherRedisModel=new OtherRedisModel();
		return $otherRedisModel->getLastSendDiceTime($uid);
	}

	/**
	 * 判断骰子的胜负
	 * @param array $orgPoints  原始骰子点数数据
	 * @param array $points     对比骰子点数数据
	 * @return int  1->胜局 2->败局 3->平局
	 */
	protected function checkDicePoints(array $orgPoints,array $points){
		if(count($orgPoints)>1){
			if(count(array_unique($orgPoints))>1&&count(array_unique($points))>1){
				if(array_sum($orgPoints)>array_sum($points)){
					$result=WIN_GAME;
				}elseif(array_sum($orgPoints)==array_sum($points)){
					$result=DRAW_GAME;
				}else{
					$result=LOST_GAME;
				}
			}else if(count(array_unique($orgPoints))>1&&count(array_unique($points))==1){
				$result=LOST_GAME;
			}elseif(count(array_unique($orgPoints))==1&&count(array_unique($points))==1){
				if($orgPoints[0]>$points[0]){
					$result=WIN_GAME;
				}elseif($orgPoints[0]==$points[0]){
					$result=DRAW_GAME;
				}else{
					$result=LOST_GAME;
				}
			}
		}else{
			if($orgPoints[0]>$points[0]){
				$result=WIN_GAME;
			}elseif($orgPoints[0]==$points[0]){
				$result=DRAW_GAME;
			}else{
				$result=LOST_GAME;
			}
		}
		return $result;
	}
	
}

?>