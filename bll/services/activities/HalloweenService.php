<?php
/**
 * @author zhangzhifan <zhangzhifan@pipi.cn> 2010-11-2
 * @link http://show.pipi.cn
 * @copyright Copyright &copy; 2003-2010 show.pipi.cn
 * @license
 */
class HalloweenService extends PipiService{

	const ACTIVITY_NAME='万圣节';
	const START_TIME="2013-10-25 12:00:00";		//活动开始时间
	const END_TIME="2013-10-31 23:59:59";			//活动结束时间
	const PUMPKIN_ID=106;									//万圣南瓜礼物id
	const AWARD_PIPIEGGS=10000;						//玩家套餐奖励皮蛋数
	private  static $setmeal_list=array(					//套餐对应的万圣南瓜数列表
		1=>array('setmeal_id'=>1,'PumpkinNum'=>3000),
		2=>array('setmeal_id'=>2,'PumpkinNum'=>5000),
		3=>array('setmeal_id'=>3,'PumpkinNum'=>10000),
		4=>array('setmeal_id'=>4,'PumpkinNum'=>12000),
		5=>array('setmeal_id'=>5,'PumpkinNum'=>13000),
		6=>array('setmeal_id'=>6,'PumpkinNum'=>15000),
		11=>array('setmeal_id'=>11,'PumpkinNum'=>10000,'swf'=>'/swf/activities/halloween/tangguojingling.swf'),
		12=>array('setmeal_id'=>12,'PumpkinNum'=>30000,'swf'=>'/swf/activities/halloween/nanguanvlang.swf'),
		13=>array('setmeal_id'=>13,'PumpkinNum'=>50000,'swf'=>'/swf/activities/halloween/halloweenqueen.swf')
	);
	//万圣节兑换记录model
	protected   static $halloweenRecordsModel;
	
	public function __construct(PipiController $pipiController = null)
	{
		parent::__construct($pipiController);
		if(empty(self::$halloweenRecordsModel))
		{
			self::$halloweenRecordsModel=new HalloweenRecordsModel();
		}

	}
	
	//检测活动时间
	public function checkActivityTime()
	{
		return (time()>=strtotime(self::START_TIME)) && (time()<=strtotime(self::END_TIME))?1:2;
	}
	
	//已兑换消耗掉的南瓜数
	protected  function getExchangedPumpkinNum($uid,$startTime,$endTime,$user_type)
	{
		$exchangeSetmealRecords=$this->arToArray(self::$halloweenRecordsModel->getExchangeSetmealByUid($uid,$startTime,$endTime,$user_type));
		$exchangedPumpkinNum=0;
		if(empty($exchangeSetmealRecords))
			return $exchangedPumpkinNum;
		foreach ($exchangeSetmealRecords as $row)
		{
			$exchangedPumpkinNum+=$row['need_giftnum'];
		}
		return $exchangedPumpkinNum;
	}
	
	//普通用户套餐兑换
	public function userExchange($uid,$setmeal_id)
	{
		if(1!=$this->checkActivityTime())
			return -9;
		$startTime=strtotime(self::START_TIME);
		$endTime=strtotime(self::END_TIME);
		$user_type=0;
		$setmeal_type=0;
		//已兑换消耗掉的南瓜数
		$exchangedPumpkinNum=$this->getExchangedPumpkinNum($uid,$startTime,$endTime,$user_type);

		//兑换所需南瓜数
		$needPumpkinNum=self::$setmeal_list[$setmeal_id]['PumpkinNum'];
		//用户送出的总南瓜数
		$userTotalPumpkinNum=self::$halloweenRecordsModel->getSumPumpkinByUser($startTime, $endTime, $uid, self::PUMPKIN_ID);
		
		//兑换用户套餐
		$remainPumpkinNum=$userTotalPumpkinNum-$exchangedPumpkinNum;
		if($remainPumpkinNum>=$needPumpkinNum)
		{
			$halloweenRecords=new HalloweenRecordsModel();
			$halloweenRecords->uid=$uid;
			$halloweenRecords->user_type=$user_type;
			$halloweenRecords->setmeal_type=$setmeal_type;
			$halloweenRecords->exchange_setmeal=$setmeal_id;
			$halloweenRecords->need_giftnum=$needPumpkinNum;
			$halloweenRecords->create_time=time();
			if($halloweenRecords->save())
			{
				switch ($setmeal_id)
				{
					case 1:{		//黄色vip
						$flag=$this->addPropsBag("vip_yellow", $uid, 1, 0, 86400*15);
						if($flag)
							return 1;
						else
							return -3;
					}
					break;
					case 2:{		//紫色vip
						$flag=$this->addPropsBag("vip_purple", $uid, 1, 0, 86400*15);
						if($flag)
							return 1;
						else
							return -3;
					}
					break;
					case 3:{		//南瓜马车
						$flag=$this->addPropsBag("nanguamache", $uid, 1, 0, 86400*30);
						if($flag)
							return 1;
						else
							return -3;
					}
					break;
					case 4:{		//黄色vip+南瓜马车
						$flag1=$this->addPropsBag("vip_yellow", $uid, 1, 0, 86400*15);
						$flag2=$this->addPropsBag("nanguamache", $uid, 1, 0, 86400*30);
						if($flag1 && $flag2)
							return 1;
						else
							return -3;
					}
					break;
					case 5:{		//紫色vip+南瓜马车
						$flag1=$this->addPropsBag("vip_purple", $uid, 1, 0, 86400*15);
						$flag2=$this->addPropsBag("nanguamache", $uid, 1, 0, 86400*30);
						if($flag1 && $flag2)
							return 1;
						else
							return -3;
					}
					break;
					case 6:{		//紫色vip+南瓜马车+皮蛋
						$flag1=$this->addPropsBag("vip_purple", $uid, 1, 0, 86400*15);
						$flag2=$this->addPropsBag("nanguamache", $uid, 1, 0, 86400*30);
						//赠皮蛋10000
						$consume = new ConsumeService();
						$addEggsResult=$consume->addEggs($uid, self::AWARD_PIPIEGGS);
						if($addEggsResult){
							$consume->updateUserJsonInfo($uid, array('pipiegg' => true));
							$record = array(
								'uid'			=> $uid,
								'from_target_id'=> 0,
								'to_target_id'	=> 0,
								'pipiegg'		=> self::AWARD_PIPIEGGS,
								'record_sid'	=> $halloweenRecords->getPrimaryKey(),
								'num'			=> 0,
								'source'		=> SOURCE_ACTIVITY,
								'sub_source'	=> SUBSOURCE_ACTIVITY_HALLOWEEN,
								'extra'			=> '万圣节 - '.date("Y"),
								'client'		=> CLIENT_ACTIVITES
							);
							$flag3=$consume->saveUserPipiEggRecords($record);
						}
						if($flag1 && $flag2 && $addEggsResult)
							return 1;
						else
						{
							return -3;
						}
					}
					break;																			
				}
			}
			return 1;
		}
		else
			return -2;		
	}
	
	//主播兑换
	public function doteyExchange($dotey_id,$setmeal_id)
	{
		if(1!=$this->checkActivityTime())
			return -9;
		
		$startTime=strtotime(self::START_TIME);
		$endTime=strtotime(self::END_TIME);
		$user_type=1;
		$setmeal_type=1;
		//已兑换消耗掉的南瓜数
		$exchangeSetmealRecords=$this->arToArray(self::$halloweenRecordsModel->getExchangeSetmealByUid($dotey_id,$startTime,$endTime,$user_type));
		$recordsCount=count($exchangeSetmealRecords);
		if($recordsCount>0)
			return -3;				//主播只能兑换一次
		
		$exchangedPumpkinNum=0;
		foreach ($exchangeSetmealRecords as $row)
		{
			$exchangedPumpkinNum+=$row['need_giftnum'];
		}
		
		//兑换所需南瓜数
		$needPumpkinNum=self::$setmeal_list[$setmeal_id]['PumpkinNum'];
		//主播收到的总南瓜数
		$doteyTotalPumpkinNum=self::$halloweenRecordsModel->getSumPumpkinByDotey($startTime, $endTime, $dotey_id, self::PUMPKIN_ID);

		//兑换主播套餐
		$remainPumpkinNum=$doteyTotalPumpkinNum-$exchangedPumpkinNum;
		if($remainPumpkinNum>=$needPumpkinNum)
		{
			$halloweenRecords=new HalloweenRecordsModel();
			$halloweenRecords->uid=$dotey_id;
			$halloweenRecords->user_type=$user_type;
			$halloweenRecords->setmeal_type=$setmeal_type;
			$halloweenRecords->exchange_setmeal=$setmeal_id;
			$halloweenRecords->need_giftnum=$needPumpkinNum;
			$halloweenRecords->time_limit=time()+30*86400;
			$halloweenRecords->create_time=time();
			return $halloweenRecords->save()?1:-5;
		}	
		else
			return -4;
	}
	
	//发放道具到背包
	public function addPropsBag($prop_name,$uid,$num,$numUpdate,$vtime)
	{
		
		if(empty($prop_name) || $uid <= 0 || ($numUpdate && $num <= 0) || $vtime <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		}
		//获取道具id
		$props = PropsModel::model()->findByAttributes(array('en_name'=>$prop_name));
		if(empty($props)){
			return $this->setError(Yii::t('props','The props does not exist'),false);
		}
		$props = $props->attributes;
		$prop_id = $props['prop_id'];
			
		$propsService = new PropsService();
		$userPropsService = new UserPropsService();
		
		//获取道具属性和分类属性
		$props = $propsService->getPropsByIds($prop_id,true,true);
		$props = $props[$prop_id];
		//构造道具流水记录
		$timeStamp = time();
		$records['uid'] = $uid;
		$records['prop_id'] = $prop_id;
		$records['amount'] = $num;
		$records['vtime'] = $timeStamp+$vtime;
		$records['source'] = PROPSRECORDS_SOURCE_ACTIVITY;
		$records['cat_id'] = $props['cat_id'];
		$record_sid =  $userPropsService->saveUserPropsRecords($records,$props);
		if($record_sid <= 0){
			$error = '';
			if($userPropsService->getError()){
				$error = $userPropsService->getError();
			}elseif($userPropsService->getNotice()){
				$notice = $userPropsService->getNotice();
				$error = array_pop($notice);
			}
			return $this->setError($error,false);
		}
		//存储用户道具背包	
		$userPropsBagModel = new UserPropsBagModel();
		$userProps = $userPropsBagModel->findByAttributes(array('uid'=>$uid,'prop_id'=>$prop_id));
		if(empty($userProps)){								//向背包中新增道具
			$userPropsBagModel->uid = $uid;
			$userPropsBagModel->prop_id = $prop_id;
			$userPropsBagModel->num = $num;
			$userPropsBagModel->valid_time = $vtime > 1 ? $timeStamp+$vtime : 0;
			$userPropsBagModel->cat_id = $props['cat_id'];
			$userPropsBagModel->record_sid = $record_sid;
			if($userPropsBagModel->save()){
				if($props['category']['en_name'] == 'vip'){				//处理道具分类为vip
					$purpleProps = $propsService->getPropsByEnName('vip_purple');
					$purpleVips =  $userPropsService->getUserValidPropsOfBagByPropId($uid,$purpleProps['prop_id'],time());
					if($purpleVips){
						$purpleVips = array_pop($purpleVips);
					}
					$userPropsAttriubte = array();
					$userJson['vip'] = array('t'=>1,'h'=>0,'img'=>'','vt'=>0);
					$userPropsAttriubte['uid'] = $uid;
					if($purpleVips || $props['en_name'] == 'vip_purple'){			 //处理紫色vip
						$userJson['vip']['t'] = 2;
						$userPropsAttriubte['vip_type'] = 2;
						$userPropsAttriubte['vip'] = $purpleProps['prop_id'];
						//如果已购买紫色VIP，紫色VIP生效
						if($purpleVips){
							if($props['en_name'] == 'vip_purple'){
								$userJson['vip']['img'] = $props['image'];
								if($purpleVips['valid_time'] == 0){
									$userJson['vip']['vt'] = 0;
								}else{
									$userJson['vip']['vt'] =  $purpleVips['valid_time'] > $timeStamp ? $purpleVips['valid_time']+$vtime : $timeStamp+$vtime;
								}
							}else{
								$userJson['vip']['img'] = '/props/'.$purpleProps['image'];
								$userJson['vip']['vt'] =  $purpleVips['valid_time'];
							}
						}else{
							$userJson['vip']['img'] =  $props['image'];
							$userJson['vip']['vt'] = $timeStamp+$vtime;
						}
					}else{
						$userPropsAttriubte['vip_type'] = 1;
						$userPropsAttriubte['vip'] = $prop_id;
						$userJson['vip']['t'] = 1;
						$userJson['vip']['img'] =  $props['image'];
						$userJson['vip']['vt'] =  $vtime ? $timeStamp+$vtime :0;
		
					}
					$userPropsService->saveUserPropsAttribute($userPropsAttriubte);					//存储用户道具属性
					$userJsonInfoService = new UserJsonInfoService();
					$userJsonInfoService->setUserInfo($uid,$userJson);										//更新用户信息
					$zmq = $userPropsService->getZmq();
					$zmq->sendZmqMsg(609,array('type'=>'update_json','uid'=>$uid,'json_info'=>$userJson));
						
				}
				return true;
			}else{
				return $this->setError(Yii::t('props','You insert props bag failed'),false);
			}
		}else{														//更新背包中的道具
			$counters = array();
			if($userProps->valid_time == 0){
				$userProps->valid_time = 0;
			} else if($userProps->valid_time > $timeStamp && $vtime > 1){
				$counters['valid_time'] = $vtime;
			}else{
				if($vtime > 1){
					$userProps->valid_time = $timeStamp+$vtime;
				}
			}
			$userProps->save();
			if($numUpdate){
				$counters['num'] = $num;
			}
			if($counters){
				$userPropsBagModel->updateCounters($counters,' uid = '.$uid .' AND  prop_id = '.$prop_id);
			}
		
			if($props['category']['en_name'] == 'vip'){											//处理道具分类为vip
				$purpleProps = $propsService->getPropsByEnName('vip_purple');
				$purpleVips =  $userPropsService->getUserValidPropsOfBagByPropId($uid,$purpleProps['prop_id'],time());
				if($purpleVips){
					$purpleVips = array_pop($purpleVips);
				}
				$userPropsAttriubte = array();
				$userJson['vip'] = array('t'=>1,'h'=>0,'img'=>'','vt'=>0);
				$userPropsAttriubte['uid'] = $uid;
				if($purpleVips || $props['en_name'] == 'vip_purple'){						//处理紫色vip
					$userJson['vip']['t'] = 2;
					$userPropsAttriubte['vip_type'] = 2;
					$userPropsAttriubte['vip'] = $purpleProps['prop_id'];
					//如果已购买紫色VIP，紫色VIP生效
					if($purpleVips){
						if($props['en_name'] == 'vip_purple'){
							$userJson['vip']['img'] = $props['image'];
							if($purpleVips['valid_time'] == 0){
								$userJson['vip']['vt'] = 0;
							}else{
								$userJson['vip']['vt'] =  $purpleVips['valid_time'] > $timeStamp ? $purpleVips['valid_time']+$vtime : $timeStamp+$vtime;
							}
						}else{
							$userJson['vip']['img'] = '/props/'.$purpleVips['image'];
							$userJson['vip']['vt'] =  $purpleVips['valid_time'];
						}
					}else{
						$userJson['vip']['img'] =  $props['image'];
						$userJson['vip']['vt'] = $timeStamp+$vtime;
					}
				}else{
					$userPropsAttriubte['vip_type'] = 1;
					$userPropsAttriubte['vip'] = $prop_id;
					$userJson['vip']['t'] = 1;
					$userJson['vip']['img'] =  $props['image'];
					if($userProps->valid_time == 0){
						$userJson['vip']['vt'] = 0;
					}else{
						$userJson['vip']['vt'] =  $userProps->valid_time > $timeStamp ? $userProps->valid_time+$vtime : $timeStamp+$vtime;
					}
				}
				$userPropsService->saveUserPropsAttribute($userPropsAttriubte);
				$userJsonInfoService = new UserJsonInfoService();
				$userJsonInfoService->setUserInfo($uid,$userJson);
				$zmq = $userPropsService->getZmq();
				$zmq->sendZmqMsg(609,array('type'=>'update_json','uid'=>$uid,'json_info'=>$userJson));
			}
			return true;
		}
	}
	
	//获取万圣节主播套餐flash
	public function getFlashByDoteyId($dotey_id)
	{
		$startTime=strtotime(self::START_TIME);
		$endTime=strtotime(self::END_TIME);
		$user_type=1;
		$exchangeSetmealRecords=$this->arToArray(self::$halloweenRecordsModel->getExchangeSetmealByUid($dotey_id,$startTime,$endTime,$user_type));
		if(count($exchangeSetmealRecords)>0)
		{
			$exchangeSetmealRecords=$this->buildDataByIndex($exchangeSetmealRecords, 'uid');
			$setmealInfo=$exchangeSetmealRecords[$dotey_id];
			$result=array(
				'dotey_id'=>$dotey_id,
				'is_display'=>(time()<=$setmealInfo['time_limit'] && $setmealInfo['is_display']==0)?true:false,
				'swf'=>self::$setmeal_list[$setmealInfo['exchange_setmeal']]['swf']
			);
		}
		else
		{
			$result=array(
				'dotey_id'=>$dotey_id,
				'is_display'=>false,
				'swf'=>''
			);
		}
		return $result;
	}
	
	//返回普通用户或主播的万圣节礼物信息
	public function getDoteyAndUserGiftInfo($uid,$user_type)
	{
		$startTime=strtotime(self::START_TIME);
		$endTime=strtotime(self::END_TIME);
		if($user_type==1)
		{
			$doteyInfo=array('dotey_id'=>$uid,
				'user_type'=>$user_type,
				'totalPumpkinNum'=>self::$halloweenRecordsModel->getSumPumpkinByDotey($startTime, $endTime, $uid, self::PUMPKIN_ID),
				'exchangedPumpkinNum'=>$this->getExchangedPumpkinNum($uid,$startTime,$endTime,$user_type),
			);
			$userInfo=array('uid'=>$uid,
				'user_type'=>$user_type,
				'totalPumpkinNum'=>self::$halloweenRecordsModel->getSumPumpkinByUser($startTime, $endTime, $uid, self::PUMPKIN_ID),
				'exchangedPumpkinNum'=>$this->getExchangedPumpkinNum($uid,$startTime,$endTime,0),
			);
		}
		else
		{
			$doteyInfo=array();
			$userInfo=array('uid'=>$uid,
				'user_type'=>$user_type,
				'totalPumpkinNum'=>self::$halloweenRecordsModel->getSumPumpkinByUser($startTime, $endTime, $uid, self::PUMPKIN_ID),
				'exchangedPumpkinNum'=>$this->getExchangedPumpkinNum($uid,$startTime,$endTime,$user_type),
			);
		}

		return array('userInfo'=>$userInfo,'doteyInfo'=>$doteyInfo);
	}
}