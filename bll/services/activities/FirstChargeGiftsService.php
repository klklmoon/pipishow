<?php
/**
 * 首充送礼活动业务逻辑服务层
 * 
 * @author supeng <supeng@pipi.cn> 2010-11-2
 * @link http://show.pipi.cn
 * @copyright Copyright &copy; 2003-2010 show.pipi.cn
 * @license
 */
class FirstChargeGiftsService extends PipiService{
	
	const ACTIVITY_NAME='首充送礼';#活动名称
	const ACTIVITY_START_TIME = '2013-07-19 00:00:00';#活动开始时间 2013-07-19 00:00:00
	const ACTIVITY_TYPE_ONE = 1;#礼包一
	const ACTIVITY_TYPE_TWO = 2;#礼包二
	
	/**
	 * 检查用户是否已经领用该活动礼包
	 * @param int $uid
	 * @param int $type
	 * @return mix|number
	 */
	public function checkCollected($uid,$type){
		if (!$uid || !$type){
			return $this->setError(Yii::t('common', 'Parameter is not empty'),'参数不能为空');
		}
		if(strtotime(self::ACTIVITY_START_TIME)>time()){
			return '活动开始时间为（'.self::ACTIVITY_START_TIME.'），请耐心等待吧';
		}
		$model = new FirstChargeGiftsModel();
		return $model->checkCollected($uid, $type)?'你已经领取过该礼包了，不能重复领取':1;
	}
	
	/**
	 * 检查用户是否符合领取规则
	 * @param int $uid
	 * @param int $type
	 * @return mix|number
	 */
	public function checkCollectedRules($uid,$type){
		if (!$uid || !$type){
			return $this->setError(Yii::t('common', 'Parameter is not empty'),'参数不能为空');
		}
		#检查用户是否已经领取过礼包
		$flag = $this->checkCollected($uid, $type);
		if ($flag != 1){
			return $flag;
		}else{
			$userRechargeRecordsModel = new UserRechargeRecordsModel();
			//在活动生效日前是否已经充值过
			$historyRecharge = $userRechargeRecordsModel->getUserPipiEggsByTime($uid,1,strtotime(self::ACTIVITY_START_TIME));
			if($historyRecharge > 0){
				return '你不是首充用户不能享受此活动特权';
			}elseif ($historyRecharge <= 0){
				//获取首充时间
				$firstTimeData = $userRechargeRecordsModel->getFirstCharge($uid, strtotime(self::ACTIVITY_START_TIME));
				if (!$firstTimeData){
					return '你还没有进行首次充值不能享受该活动特权';
				}else{
					$firstTime = $firstTimeData['rtime'];
					if ($type == self::ACTIVITY_TYPE_ONE){
						if(ceil((time()-$firstTime)/86400) > 5){
							return '你已经超过了活动有效领取时间了（首充后5天内领取有效）';
						}
					}elseif($type == self::ACTIVITY_TYPE_TWO){
						if(ceil((time()-$firstTime)/86400) > 7){
							return '你已经超过了活动有效领取时间了（首充后7天内领取有效）';
						}else{
							#充值总金额是否满足
							$endTime = ($firstTime+(86400*5));
							$baseRecharge = $userRechargeRecordsModel->getUserPipiEggsByTime($uid,$firstTime,$endTime);
							if($baseRecharge < 5000){
								return '你累计充值金额不足，小于5000元无法领取该礼包';
							}
						}
					}
				}
			}else{
				return '系统有误，请联系管理员';
			}
		}
		return 1;
	}
	
	/**
	 * 获取礼包
	 * 
	 * @param int $uid
	 * @param int $type
	 * @return mix|number
	 */
	public function collectGifts($uid,$type){
		if (!$uid || !$type){
			return $this->setError(Yii::t('common', 'Parameter is not empt'),'参数不能为空');
		}
		//验证领取规则
		$flag = $this->checkCollectedRules($uid, $type);
		if ($flag != 1){
			return $flag;
		}else{
			$flgMsg = '';
			if ($type == self::ACTIVITY_TYPE_ONE){
				//小礼品100个，最佳新人勋章5天，20免费贴条万人迷
				$flag = $this->_collectGiftsForXiaolipin($uid);
				$flgMsg .= ($flag != 1)?$flag:'';
				$flag = $this->_collectMedalForNewuser($uid);
				$flgMsg .= ($flag != 1)?$flag:'';
				$flag = $this->_collectPropsLabesForWanrenmi($uid);
				$flgMsg .= ($flag != 1)?$flag:'';
				$flgMsg = '礼包一领取成功，请去个人中心查看';
			}elseif ($type == self::ACTIVITY_TYPE_TWO){
				//黄色vip5天，二手奥拓5天，免费飞屏10次
				$flag = $this->_collectPropsVipForYellowVip($uid);
				$flgMsg .= ($flag != 1)?$flag:'';
				$flag = $this->_collectPropsCarForAotuo($uid);
				$flgMsg .= ($flag != 1)?$flag:'';
				$flag = $this->_collectPropsFlyscreenForCommon($uid);
				$flgMsg .= ($flag != 1)?$flag:'';
				$flgMsg = '礼包二领取成功，请去个人中心查看';
			}
			$array['uid'] = $uid;
			$array['type'] = $type;
			$array['ctime'] = time();
			$model = new FirstChargeGiftsModel();
			$this->attachAttribute($model, $array);
			$model->save();
			return $flgMsg;
		}
		return '系统错误领取礼包失败，请联系管理员';
	}
	
	/**
	 * 获取道具（黄色vip） 属于礼包二
	 * 
	 * @param int $uid
	 * @return boolean
	 */
	private function _collectPropsVipForYellowVip($uid){
		if(empty($uid)){
			return $this->setError(Yii::t('common', 'Parameter is not empt'),'参数不能为空');
		}
		$prop_id = 28;
		$days = 5;
		$propsSer = new PropsService();
		$userPropsSer = new UserPropsService();
			
		$propInfo = $propsSer->getPropsByIds(array($prop_id),true,true);
		$propInfo = $propInfo[$prop_id];
			
		if ($propInfo){
			//流水线记录
			$isUpdatePropsAttr = false;	#是否更改用户道具属性
			$cat_id = $propInfo['cat_id'];
			$pipiegg = $propInfo['pipiegg'];
			$timeout = $days>0?time()+($days*3600*24):time()+7200;
			$bagNum = $num = 1;
			$vipColor = $propInfo['en_name'];
			
			$records['uid'] = $uid;
			$records['cat_id'] = $cat_id;
			$records['prop_id'] = $prop_id;
			$records['pipiegg'] = $num*$propInfo['pipiegg'];
			$records['dedication'] = $num*$propInfo['dedication'];
			$records['egg_points'] = $num*$propInfo['egg_points'];
			$records['charm'] = $num*$propInfo['charm'];
			$records['charm_points'] = $num*$propInfo['charm_points'];
			$records['vtime'] = $timeout;
			$records['info'] = $propInfo['category']['name'].'('.$propInfo['name']*$num.')';;
			$records['amount'] = $num;
			$records['source'] = PROPSRECORDS_SOURCE_ADMIN;
			if(!($recordSid = $userPropsSer->saveUserPropsRecords($records))){
				return '道具(黄色VIP)领取失败';
			}else{
				//是否更新正在使用中的VIP类型
				$orgColorType = 0;	#VIP类型　0表示无，1表示黄，2表示紫
				$colorType = ($vipColor=='vip_yellow')?1:($vipColor=='vip_purple'?2:0);
				
				$uProAttr = $userPropsSer->getUserPropsAttributeByUid($uid);
				if($uProAttr){
					$orgColorType = $uProAttr['vip_type'];
				}
				
				if ($colorType > $orgColorType){
					$isUpdatePropsAttr = true;
				}
				
				//写入道具背包
				$bags = array();
				$bags['uid'] = $uid;
				$bags['prop_id'] = $prop_id;
				$bags['cat_id'] = $cat_id;
				$bags['record_sid'] = $recordSid;
				$bags['target_id'] = 0;
				$bags['num'] = $bagNum;
				$bags['valid_time'] = $timeout;
				if(!($bag_id = $userPropsSer->saveUserPropsBag($bags,$propInfo))){
					return '道具（黄色VIP）写入背包失败';
				}else{
					//更新用户道具属性
					if($isUpdatePropsAttr){
						$propsAttriute['uid'] = $uid;
						$propsAttriute['vip'] = $prop_id;
						$propsAttriute['vip_type'] = $colorType;
						$propsAttriute['is_hidden'] = 0;
						$userPropsSer->saveUserPropsAttribute($propsAttriute);
					}
					//发消息
					$zmqInfo = array();
					$zmqInfo['num'] = $bagNum;
					$zmqInfo['valid_time'] = $timeout;
					$zmqInfo['image'] = $propInfo['image'];
					$zmqInfo['name'] = $propInfo['name'];
					$zmqInfo['flash'] = null;
					$zmqInfo['type'] = isset($colorType)?$colorType:0;
					$zmqInfo['hide'] = 0;
					$zmqInfo['timeout'] = null;
					$userPropsSer->sendPropsZmq($uid,'vip',$zmqInfo);
				}
				return 1;
			}
		}else{
			return '道具(黄色VIP)不存在';
		}
		return '道具(黄色VIP)赠送失败';
	}
	
	/**
	 * 获取道具（座驾二手奥拓）属于礼包二 
	 * @param int $uid
	 * @return boolean
	 */
	private function _collectPropsCarForAotuo($uid){
		if(empty($uid)){
			return $this->setError(Yii::t('common', 'Parameter is not empt'),'参数不能为空');
		}
		$prop_id = 29;
		$days = 5;
		
		$propsSer = new PropsService();
		$userPropsSer = new UserPropsService();
			
		$propInfo = $propsSer->getPropsByIds(array($prop_id),true,true);
		$propInfo = $propInfo[$prop_id];
			
		if ($propInfo){
			//流水线记录
			$cat_id = $propInfo['cat_id'];
			$pipiegg = $propInfo['pipiegg'];
			$timeout = $days>0?time()+($days*3600*24):time()+7200;
			$bagNum = $num = 1;
				
			$records['uid'] = $uid;
			$records['cat_id'] = $cat_id;
			$records['prop_id'] = $prop_id;
			$records['pipiegg'] = $num*$propInfo['pipiegg'];
			$records['dedication'] = $num*$propInfo['dedication'];
			$records['egg_points'] = $num*$propInfo['egg_points'];
			$records['charm'] = $num*$propInfo['charm'];
			$records['charm_points'] = $num*$propInfo['charm_points'];
			$records['vtime'] = $timeout;
			$records['info'] = $propInfo['category']['name'].'('.$propInfo['name']*$num.')';;
			$records['amount'] = $num;
			$records['source'] = PROPSRECORDS_SOURCE_ACTIVITY;
			if(!($recordSid = $userPropsSer->saveUserPropsRecords($records))){
				return '道具(座驾-二和奥拓)领取失败';
			}else{
				//写入道具背包
				$bags = array();
				$bags['uid'] = $uid;
				$bags['prop_id'] = $prop_id;
				$bags['cat_id'] = $cat_id;
				$bags['record_sid'] = $recordSid;
				$bags['target_id'] = 0;
				$bags['num'] = $bagNum;
				$bags['valid_time'] = $timeout;
				if(!($bag_id = $userPropsSer->saveUserPropsBag($bags,$propInfo))){
					return '道具（座驾-二和奥拓）写入背包失败';
				}
				return 1;
			}
		}else{
			return '道具(座驾-二和奥拓)不存在';
		}
		return '道具(座驾-二和奥拓)赠送失败';
	}
	
	/**
	 * 获取首具（普通飞屏） 属于礼包二
	 * @param int $uid
	 * @return boolean
	 */
	private function _collectPropsFlyscreenForCommon($uid){
		if(empty($uid)){
			return $this->setError(Yii::t('common', 'Parameter is not empt'),'参数不能为空');
		}
		$prop_id = 25;
		$bagNum = $num = 10;
		$propsSer = new PropsService();
		$userPropsSer = new UserPropsService();
		
		$propInfo = $propsSer->getPropsByIds(array($prop_id),true);
		$propInfo = $propInfo[$prop_id];
		$propAttrInfo = $propsSer->getPropsAttributeByPropIds(array($prop_id));
		$propAttrInfo = $propAttrInfo[$prop_id];
		if ($propInfo && $propAttrInfo){
			$changeRealation = Yii::app()->params->change_relation;
			//写流水线记录
			$timeout = 0;
			foreach ($propAttrInfo as $propAttr){
				if($propAttr['attr_enname'] == 'flyscreen_timeout'){
					//$timeout = $propAttr['value'];
				}
			}
			$pipiegg = $propInfo['pipiegg'];
			$egg_points = $propInfo['egg_points'];
			$charm = $propInfo['charm'];
			$charm_points = $propInfo['charm_points'];
			$dedication = $propInfo['dedication'];
			$info = $propInfo['category']['name'].'('.$propInfo['name'].'*'.$num.')';
			$cat_id = $propInfo['cat_id'];
				
			$records['uid'] = $uid;
			$records['cat_id'] = $cat_id;
			$records['prop_id'] = $prop_id;
			$records['pipiegg'] = $num*$pipiegg;
			$records['dedication'] = $num*$dedication;
			$records['egg_points'] = $num*$egg_points;
			$records['charm'] = $num*$charm;
			$records['charm_points'] = $num*$charm_points;
			$records['vtime'] = $timeout;
			$records['info'] = $info;
			$records['amount'] = $num;
			$records['source'] = PROPSRECORDS_SOURCE_ACTIVITY;
			if(!($recordSid = $userPropsSer->saveUserPropsRecords($records))){
				return '道具(普通飞屏)领取失败';
			}else{
				$_onum = 0 ;
				$_info = $userPropsSer->getUserValidPropsOfBagByPropId($uid,$prop_id); #是否已经存在于背包中
				if($_info){
					$_onum = $_info[0]['num'];
				}
				//存入背包
				$bag['uid'] = $uid;
				$bag['target_id'] = 0;
				$bag['prop_id'] = $prop_id;
				$bag['cat_id'] = $cat_id;
				$bag['record_sid'] = $recordSid;
				$bag['num'] = $bagNum+$_onum;
				$bag['valid_time'] = $timeout;
				if(!($bagId = $userPropsSer->saveUserPropsBag($bag))){
					return '道具(普通飞屏)存入背包失败';
				}
				return 1;
			}
		}else{
			return '道具(普通飞屏)不存在';
		}
		return '道具(普通飞屏)领取失败';
	}
	
	/**
	 * 获取道具（贴条万人迷） 属于礼包一
	 * @param int $uid
	 * @return boolean
	 */
	private function _collectPropsLabesForWanrenmi($uid){
		if(empty($uid)){
			return $this->setError(Yii::t('common', 'Parameter is not empt'),'参数不能为空');
		}
		$prop_id = 26;
		$bagNum = $num = 20;
		$propsSer = new PropsService();
		$userPropsSer = new UserPropsService();
		
		$propInfo = $propsSer->getPropsByIds(array($prop_id),true);
		$propInfo = $propInfo[$prop_id];
		$propAttrInfo = $propsSer->getPropsAttributeByPropIds(array($prop_id));
		$propAttrInfo = $propAttrInfo[$prop_id];
		if ($propInfo && $propAttrInfo){
			//写流水线记录
			$timeout = 0;
			$lbimg = '';
			foreach ($propAttrInfo as $propAttr){
				if($propAttr['attr_enname'] == 'label_timeout'){
					//$timeout = $propAttr['value']*60;
				}
				if($propAttr['attr_enname'] == 'label_picture'){
					$lbimg = $propAttr['value'];
				}
			}
			$pipiegg = $propInfo['pipiegg'];
			$egg_points = $propInfo['egg_points'];
			$charm = $propInfo['charm'];
			$charm_points = $propInfo['charm_points'];
			$dedication = $propInfo['dedication'];
			$info = $propInfo['category']['name'].'('.$propInfo['name'].'*'.$num.')';
			$cat_id = $propInfo['cat_id'];
			
			$records['uid'] = $uid;
			$records['cat_id'] = $cat_id;
			$records['prop_id'] = $prop_id;
			$records['pipiegg'] = $num*$pipiegg;
			$records['dedication'] = $num*$dedication;
			$records['egg_points'] = $num*$egg_points;
			$records['charm'] = $num*$charm;
			$records['charm_points'] = $num*$charm_points;
			$records['vtime'] = $timeout;
			$records['info'] = $info;
			$records['amount'] = $num;
			$records['source'] = PROPSRECORDS_SOURCE_ADMIN;
			if(!($recordSid = $userPropsSer->saveUserPropsRecords($records))){
				return '道具(普通贴条)领取失败';
			}else{
				$_onum = 0 ;
				$_info = $userPropsSer->getUserValidPropsOfBagByPropId($uid,$prop_id); #是否已经存在于背包中
				if($_info){
					$_onum = $_info[0]['num'];
				}
				//存入背包
				$bag['uid'] = $uid;
				$bag['target_id'] = 0;
				$bag['prop_id'] = $prop_id;
				$bag['cat_id'] = $cat_id;
				$bag['record_sid'] = $recordSid;
				$bag['num'] = $bagNum+$_onum;
				$bag['valid_time'] = $timeout;
				if(!($bagId = $userPropsSer->saveUserPropsBag($bag))){
					return '道具(普通贴条)存入背包失败';
				}else{
					//zmq以及userjsoninfo信息更新
					$userJson['lb'] = array('img'=>$lbimg,'vt'=>$timeout);
					$userJsonInfoService = new UserJsonInfoService();
					$userJsonInfoService->setUserInfo($uid,$userJson);
					
					$zmq = $this->getZmq();
					$zmq->sendZmqMsg(609,array('type'=>'update_json','uid'=>$uid,'json_info'=>$userJson));
					
					$eventData['archives_id']='*';
					$eventData['domain']=DOMAIN;
					$eventData['type']='localroom';
					$json_content['type']='stickLabel';
					$json_content['uid']=$uid;
					$json_content['img']=$lbimg;
					$eventData['json_content']=$json_content;
					$zmq->sendZmqMsg(606,$eventData);
				}
				return 1;
			}
		}else{
			return '道具(普通贴条)不存在';
		}
		return '道具(普通贴条)领取失败';
	}
	
	/**
	 * 获取勋章（最佳新人） 属于礼包一
	 * @param int $uid
	 * @return boolean
	 */
	private function _collectMedalForNewuser($uid){
		$userMedalSer = new UserMedalService();
		
		$mid = 20;
		$type = MEDALAWARD_TYPE_SYS;
		
		$array = array();
		$array['uid'] = $uid;
		$array['mid'] = $mid;
		$array['type'] = MEDALAWARD_TYPE_SYS;
		$array['vtime'] = strtotime("+5days",time());
		if(!$userMedalSer->getUserMedalByUid($uid,MEDALAWARD_TYPE_SYS,$mid)){
			$rs = $userMedalSer->saveUserMedal($array);
			return $rs?1:'获取“最佳新人勋章”失败';
		}else{
			return 1;
		}
		return '获取“最佳新人勋章”失败';
	}
	
	/**
	 * 获取礼物（小礼品） 属于礼包一
	 * @param int $uid
	 */
	private function _collectGiftsForXiaolipin($uid){
		$gift_id = 171;
		$num = 100;
		
		$giftBagSer = new GiftBagService();
		$giftSer = new GiftService();
		$userSer = new UserService();
		//礼物信息
		$giftInfo = $giftSer->getGiftByIds(array($gift_id));
		$giftInfo = $giftInfo[$gift_id];
		//用户信息
		$userInfo = $userSer->getUserBasicByUids(array($uid));
		$userInfo = $userInfo[$uid];
		
		if($giftInfo && $userInfo){
			$_gift = array();
			$_gift['uid'] = $uid;
			$_gift['gift_id'] = $gift_id;
			$_gift['num'] = $num;
					
			$info = array();
			$info['uid'] = $uid;
			$info['nickname'] = $userInfo['nickname'];
			$info['gift_id'] = $gift_id;
			$info['gift_name'] = $giftInfo['zh_name'];
			$info['num'] = (int)$num;
			$info['remark'] = '首充送礼(小礼品*'.$num.')';
		
			$addRecords = array();
			$addRecords['uid'] = $uid;
			$addRecords['gift_id'] =  $gift_id;
			$addRecords['num'] = $num;
			$addRecords['source'] = BAGSOURCE_TYPE_ADMIN;
			$addRecords['sub_source'] = 'FirstChargeGifts';
			$addRecords['info'] = serialize($info);
			$rs = $giftBagSer->saveUserGiftBagByUid($_gift, $addRecords);
			return $rs?1:'获取礼包一中的“小礼品”失败';
		}
		return '不存在该礼物';
	}
	
}