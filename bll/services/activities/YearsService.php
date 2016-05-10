<?php
/**
 * 2周年庆服务层
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: 2013-11-18 下午2:30:18 hexin $ 
 * @package
 */
class YearsService extends PipiService{
	//活动时间
	CONST START_TIME = "2013-11-25 00:00:01";
	CONST END_TIME = "2013-11-30 23:59:59";
	//领取礼包限制条件
	public static $packs = array(
		1 => array('need' => 500, 'reward' => '50000', 'desc' => '黄色VIP30天+座驾（大众甲壳虫）15天+50000贡献值'),
		2 => array('need' => 1000, 'reward' => '100000', 'desc' => '紫色VIP7天 +座驾（ 地狱战车 ）7天+100000贡献值'),
		3 => array('need' => 2000, 'reward' => '200000', 'desc' => '紫色VIP7天 +座驾（ 地狱战车 ）15天+6位靓号（888ABC形式）+200000贡献值'),
		4 => array('need' => 3000, 'reward' => '300000', 'desc' => '紫色VIP15天+座驾（ 地狱战车 ）30天+6位靓号（888AAB）+300000贡献值'),
		5 => array('need' => 5000, 'reward' => '600000', 'desc' => '紫色VIP30天+新品恶搞座驾（公交车）30天+6位靓号（8888AA）+600000贡献值'),
		6 => array('need' => 10000, 'reward' => '1250000', 'desc' => '紫色VIP60天+新品恶搞座驾（公交车）30天+5位靓号（888AA） +1250000贡献值'),
		7 => array('need' => 20000, 'reward' => '3000000', 'desc' => '紫色VIP一年+新品恶搞座驾（公交车）30天 +5位靓号（8888A） + 3000000贡献值'),
		8 => array('need' => 100000, 'reward' => '100000', 'desc' => '100000魅力值'),
		9 => array('need' => 300000, 'reward' => '300000', 'desc' => '300000魅力值'),
		10 => array('need' => 500000, 'reward' => '500000', 'desc' => '500000魅力值'),
		11 => array('need' => 1000000, 'reward' => '1200000', 'desc' => '1200000魅力值'),
		12 => array('need' => 1500000, 'reward' => '1800000', 'desc' => '1800000魅力值'),
		13 => array('need' => 2000000, 'reward' => '2500000', 'desc' => '2500000魅力值'),
	);
	
	/**
	 * 检查活动时间
	 * @return boolean
	 */
	public function checkTime(){
		if(time() < strtotime(self::START_TIME)){
			return $this->setNotice(0, '活动还未开始!', false);
		}elseif(time() > strtotime(self::END_TIME)){
			return $this->setNotice(1, '该活动已结束!', false);
		}else return true;
	}
	
	/**
	 * 领取礼包
	 * @return boolean
	 */
	public function receive($uid, $id){
		if(!$this->checkTime()){
			return false;
		}
		
		$record = YearsModel::model()->findAllByAttributes(array('uid' => $uid));
		if(!empty($record) && count($record) > 1){
			return $this->setNotice(3, '抱歉，您还未达到要求或者已领取过！');
		}elseif(!empty($record) && $id < 8 && $record[0]->pack_id < 8){
			return $this->setNotice(3, '抱歉，您还未达到要求或者已领取过！');
		}elseif(!empty($record) && $id > 7 && $record[0]->pack_id > 7){
			return $this->setNotice(3, '抱歉，您还未达到要求或者已领取过！');
		}
		
		$flag = false;
		$dotey = 0;
		if($id < 8){
			$recharge = UserRechargeRecordsModel::model()->getUserRechargeByDay($uid, strtotime(self::START_TIME), time());
			foreach($recharge as $r){
				if($r['money'] >= self::$packs[$id]['need']){
					$flag = true;
					break;
				}
			}
		}else{
			$doteyService = new DoteyService();
			$dotey = $doteyService->getDoteysInUids(array($uid));
			if(!isset($dotey[$uid])) return $this->setNotice(4, '抱歉，您不符合领取条件！');
			else $dotey = 1;
			
			$consumeService = new ConsumeService();
			$consume = $consumeService->getCharmPointsByCondition(array('uid' => $uid, 'create_time_on' => self::START_TIME, 'create_time_end' => date('Y-m-d H:i:s')), 0, 10, false);
			if(!empty($consume['list'])){
				$point = 0;
				foreach($consume['list'] as $r){
					$point += $r['charm_points'];
				}
				if($point >= self::$packs[$id]['need']) $flag = true;
			}else $flag = false;
		}
		if(!$flag) return $this->setNotice(4, '抱歉，您不符合领取条件！');
		
		$record = new YearsModel();
		$record->uid = $uid;
		$record->user_type = $dotey;
		$record->pack_id = $id;
		$record->need = self::$packs[$id]['need'];
		$record->reward = self::$packs[$id]['reward'];
		$record->pack_desc = self::$packs[$id]['desc'];
		$record->create_time = time();
		if($record->save()){
			switch($id){
				case 1:
					$flag1 = $this->addPropsBag("vip_yellow", $uid, 1, 0, 86400*30);
					$flag2 = $this->addPropsBag("dzjkc", $uid, 1, 0, 86400*15);
					$flag3 = $this->addDelication($uid, self::$packs[$id]['reward']);
					return $flag1 && $flag2 && $flag3;
					break;
				case 2:
					$flag1 = $this->addPropsBag("vip_purple", $uid, 1, 0, 86400*7);
					$flag2 = $this->addPropsBag("motuoche", $uid, 1, 0, 86400*7);
					$flag3 = $this->addDelication($uid, self::$packs[$id]['reward']);
					return $flag1 && $flag2 && $flag3;
					break;
				case 3:
					$flag1 = $this->addPropsBag("vip_purple", $uid, 1, 0, 86400*7);
					$flag2 = $this->addPropsBag("motuoche", $uid, 1, 0, 86400*15);
					$flag3 = $this->addDelication($uid, self::$packs[$id]['reward']);
					return $flag1 && $flag2 && $flag3;
					break;
				case 4:
					$flag1 = $this->addPropsBag("vip_purple", $uid, 1, 0, 86400*15);
					$flag2 = $this->addPropsBag("motuoche", $uid, 1, 0, 86400*30);
					$flag3 = $this->addDelication($uid, self::$packs[$id]['reward']);
					return $flag1 && $flag2 && $flag3;
					break;
				case 5:
					$flag1 = $this->addPropsBag("vip_purple", $uid, 1, 0, 86400*30);
					$flag2 = $this->addPropsBag("gongjiaoche", $uid, 1, 0, 86400*30);
					$flag3 = $this->addDelication($uid, self::$packs[$id]['reward']);
					return $flag1 && $flag2 && $flag3;
					break;
				case 6:
					$flag1 = $this->addPropsBag("vip_purple", $uid, 1, 0, 86400*60);
					$flag2 = $this->addPropsBag("gongjiaoche", $uid, 1, 0, 86400*30);
					$flag3 = $this->addDelication($uid, self::$packs[$id]['reward']);
					return $flag1 && $flag2 && $flag3;
					break;
				case 7:
					$flag1 = $this->addPropsBag("vip_purple", $uid, 1, 0, 86400*365);
					$flag2 = $this->addPropsBag("gongjiaoche", $uid, 1, 0, 86400*30);
					$flag3 = $this->addDelication($uid, self::$packs[$id]['reward']);
					return $flag1 && $flag2 && $flag3;
					break;
				case 8:
					return $this->addCharm($uid, self::$packs[$id]['reward']);
					break;
				case 9:
					return $this->addCharm($uid, self::$packs[$id]['reward']);
					break;
				case 10:
					return $this->addCharm($uid, self::$packs[$id]['reward']);
					break;
				case 11:
					return $this->addCharm($uid, self::$packs[$id]['reward']);
					break;
				case 12:
					return $this->addCharm($uid, self::$packs[$id]['reward']);
					break;
				case 13:
					return $this->addCharm($uid, self::$packs[$id]['reward']);
					break;
			}
		}else return false;
	}
	
	//加贡献值
	private function addDelication($uid, $delication){
		$consumeService = new ConsumeService();
		$consumeService->saveUserConsumeAttribute(array('uid' => $uid, 'dedication' => $delication));
		
		$records = array();
		$records['uid'] = $uid;
		$records['dedication'] = $delication;
		$records['source'] = SOURCE_ACTIVITY;
		$records['sub_source'] = SUBSOURCE_ACTIVITY_2YEARS;
		$records['client'] = CLIENT_ACTIVITES;
		$records['info'] = '2周年庆奖励';
		$consumeService->saveUserDedicationRecords($records, 1);
		return true;
	}
	
	//加魅力值
	private function addCharm($uid, $charm){
		$consumeService = new ConsumeService();
		$consumeService->saveUserConsumeAttribute(array('uid' => $uid, 'charm' => $charm));
		
		$records = array();
		$records['uid'] = $uid;
		$records['render_uid'] = $uid;
		$records['charm'] = $charm;
		$records['source'] = SOURCE_ACTIVITY;
		$records['sub_source'] = SUBSOURCE_ACTIVITY_2YEARS;
		$records['client'] = CLIENT_ACTIVITES;
		$records['info'] = '2周年庆奖励';
		$consumeService->saveDoteyCharmRecords($records, 1);
		return true;
	}
	
	//发放道具到背包
	private function addPropsBag($prop_name,$uid,$num = 1,$numUpdate = 0,$vtime = 0){
	
		if(empty($prop_name) || $uid <= 0 || ($numUpdate && $num <= 0)){
			return $this->setNotice(5,Yii::t('common','Parameter is empty'),false);
		}
		//获取道具id
		$props = PropsModel::model()->findByAttributes(array('en_name'=>$prop_name));
		if(empty($props)){
			return $this->setNotice(6, Yii::t('props','The props does not exist'),false);
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
		$records['vtime'] = $vtime ? $timeStamp+$vtime : 0;
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
			return $this->setNotice(6, $error,false);
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
							$userJson['vip']['vt'] = $vtime ? $timeStamp+$vtime :0;
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
				return $this->setNotice(7, Yii::t('props','You insert props bag failed'),false);
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
						$userJson['vip']['vt'] = $vtime ? $timeStamp+$vtime :0;
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
}