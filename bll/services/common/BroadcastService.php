<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: supeng $>
 * @author Su Peng <supeng@pipi.cn>
 * @version $Id: BroadcastService.php 13289 2013-07-24 00:47:42Z supeng $ 
 * @package
 */
class BroadcastService extends PipiService {
	const DEFAULT_PRICE = 100;
	const CONTENT_LENGTH = 50;
	const TIMEOUT=600;
	
	
	
	/**
	 * 获取禁播用户通过uid
	 * @param int $uid
	 * @return Ambigous <Ambigous, multitype:>
	 */
	public function getBroadcastDisableByUid($uid){
		$model = new BroadcastDisableModel();
		return $model->getBroadcastDisableByUid($uid);
	}
	
	/**
	 * 获取禁播用户通过uid集合
	 * @param array $uids
	 * @return Ambigous <multitype:, multitype:unknown Ambigous <multitype:unknown , unknown> , Ambigous, NULL, unknown, multitype:unknown Ambigous <unknown, NULL> , mixed, multitype:unknown >
	 */
	public function getBroadcastDisableByUids(array $uids){
		$model = new BroadcastDisableModel();
		$data = $model->getBroadcastDisableByUids($uids);
		if($data){
			$data = $this->buildDataByIndex($this->arToArray($data), 'uid');
		}
		return $data;
	}
	
	/**
	 * 获取禁播列表
	 * @param array $condition
	 * @param int $offset
	 * @param int $pagesize
	 * @return Ambigous <multitype:, multitype:unknown Ambigous <multitype:unknown , unknown> , NULL, unknown, multitype:unknown Ambigous <unknown, NULL> , mixed, multitype:unknown >
	 */
	public function getBroadcastDisableList(Array $condition = array(),$offset=0,$pagesize=20){
		$model = new BroadcastDisableModel();
		$data = $model->getBroadcastDisableList($condition,$offset,$pagesize);
		if($data['list']){
			$data['list'] = $this->buildDataByIndex($this->arToArray($data['list']), 'uid');
		}
		return $data;
	}
	
	/**
	 * 获取广播内容
	 * @param array $condition
	 * @param int $offset
	 * @param unknown_type $pagesize
	 * @return Ambigous <multitype:, multitype:NULL , NULL, unknown, multitype:unknown Ambigous <unknown, NULL> , mixed, multitype:unknown >
	 */
	public function getBroadcastContentList(Array $condition = array(),$offset=0,$pagesize=20){
		$model = new BroadcastContentModel();
		$data = $model->getBroadcastContentList($condition,$offset,$pagesize);
		if($data['list']){
			$data['list'] = $this->arToArray($data['list']);
		}
		return $data;
	}
	
	/**
	 * 获取广播配置信息
	 * @return Ambigous <string, mix, boolean, unknown>
	 */
	public function getBroadcastSetup(){
		$webConfigService = new WebConfigService();
		$setInfo = $webConfigService->getWebConfig($webConfigService->getBroadcastSetupKey());
		if(!$setInfo){
			$setInfo['power'] = 1;
			$setInfo['urank'] = 0;
			$setInfo['price'] = self::DEFAULT_PRICE;
		}else{
			$setInfo = $setInfo['c_value'];
		}
		return $setInfo;
	}
	
	/**
	 * 获取有效全站广播
	 * @return array
	 */
	public function getBroadcastFromCache(){
		$otherRedisModel=new OtherRedisModel();
		$data=$otherRedisModel->getFullSiteBroadcast();
		$list=array();
		if($data){
			foreach($data as $row){
				if($row['timeout']>time()){
					$list[]=$row;
				}
			}
		}
		return $list;
	}
	
	/**
	 * 删除禁播数据 恢复成非禁播状态
	 * @param int $uid
	 */
	public function deleteDisable($uid){
		$model = new BroadcastDisableModel();
		return $model->deleteByPk($uid);
	}
	
	/**
	 * 保存广播记录
	 * @param array $record
	 * @return mix|Ambigous <NULL, multitype:NULL >
	 */
	public function saveBroadcastContent(Array $record){
		if($record['uid'] <= 0 || $record['dotey_uid'] <= 0 || $record['aid'] <= 0 || $record['price'] <= 0 || empty($record['content'])){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$record['ctime'] = time();
		$broadcastContentModel = new BroadcastContentModel();
		$this->attachAttribute($broadcastContentModel,$record);
		if(!$broadcastContentModel->validate()){
			return $this->setNotices($broadcastContentModel->getErrors(),0);
		}
		$broadcastContentModel->save();
		$flag = $broadcastContentModel->getPrimaryKey();
		return $flag;
	}
	
	/**
	 * 保存禁用广播用户关系
	 * @param int $uid
	 * @return mix|boolean
	 */
	public function saveBroadcastDisable($uid){
		if($uid <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		}
		$model = new BroadcastDisableModel();
		$orgModel = $model->findByPk($uid);
		if(!$orgModel){
			$record['uid'] = $uid;
			$record['utime'] = time();
			$this->attachAttribute($model, $record);
			if(!$model->validate()){
				return false;
			}
			return $model->save();
		}
		return true;
	}
	
	/**
	 * 存储全站广播到redis
	 * @param array $content
	 * @return boolean 0->失败，1->成功
	 */
	public function saveBroadcastToCache(array $content){
		if(empty($content)) 
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		$otherRedisModel=new OtherRedisModel();
		$content['timeout']=time()+self::TIMEOUT;
		$orgData=self::getBroadcastFromCache();
		$orgData[]=$content;
		return $otherRedisModel->saveFullSiteBroadcast($orgData);
	}
	
	/**
	 * @param int $uid	用户ID
	 * @param int $aid	来源档期
	 * @param string $content 广播内容
	 * @return mix|number
	 */
	public function releaseBroadcast($uid,$aid,$content){
		$content = preg_replace('/\r|\n|\r\n/', '', strip_tags($content));
		if (!is_int($uid) || !is_int($aid) || empty($content) || !$content){
			return $this->setError(Yii::t('common', 'Parameter is Wrong'),0);#参数有误
		}else if($this->getBroadcastDisableByUid($uid)){
			return 1;#属于禁播用户
		}
		
		$uid = (int)$uid;
		$aid = (int)$aid;
		if(mb_strlen($content,'UTF-8') > self::CONTENT_LENGTH){
			return 2;#广播内容超出范围
		}else{
			$setInfo = $this->getBroadcastSetup();
			if(!$setInfo['power']){
				return 3;#全站广播关闭
			}else{
				$consumeService = new ConsumeService();
				$consumeInfo = $consumeService->getConsumesByUids(array($uid));#消费信息
				if(!isset($consumeInfo[$uid])){
					return 4;#无消费属性或信息有误
				}else{
					$consumeInfo = $consumeInfo[$uid];
					$urank = $consumeInfo['rank'];
					if ($setInfo['urank'] > $urank){
						return 5;#用户等级不够，无法发布广播内容
					}else{
						$propsService=new PropsService();
						$userPropsService=new UserPropsService();
						$propsInfo=$propsService->getPropsByEnName('day_broadcast');
						$day_broadcast=$userPropsService->getUserValidPropsOfBagByPropId($uid,$propsInfo['prop_id']);
						$day_broadcast=array_pop($day_broadcast);
						$_propsInfo=$propsService->getPropsByEnName('common_broadcast');
						$common_broadcast=$userPropsService->getUserValidPropsOfBagByPropId($uid,$_propsInfo['prop_id']);
						$common_broadcast=array_pop($common_broadcast);
						$broadcastId=$broadcastNum=0;
						if($day_broadcast['num']>0&&$day_broadcast['valid_time']>time()){
							$broadcastId=$propsInfo['prop_id'];
						}elseif($common_broadcast['num']>0){
							$broadcastId=$_propsInfo['prop_id'];
						}
						if($broadcastId>0){
							$userPropsService->saveUserPropsBag(array('uid'=>$uid,'prop_id'=>$broadcastId,'s_num'=>-1));
							$record['uid']=$uid;
							$record['prop_id']=$propsInfo['prop_id'];
							$records['target_id']=$aid;
							$records['cat_id']=$propsInfo['cat_id'];
							$records['num']=1;
							$userPropsService->saveUserPropsUse($record);
						}else{
							$freeze_pipiegg = $consumeInfo['freeze_pipiegg'];
							$pipiegg = $consumeInfo['pipiegg'];
							$price = (float)$setInfo['price'];
							$dedication = $price*Yii::app()->params['change_relation']['pipiegg_to_dedication'];
							if(($pipiegg-$freeze_pipiegg) < $price){
								return 6;#余额不足
							}
							if(!$consumeService->consumeEggs($uid,$price)){
								return 6;#余额不足
							}
						}
						$archivesService = new ArchivesService();
						$archivesInfo = $archivesService->getArchivesByArchivesId($aid);#档期信息
						$UserJsonService = new UserJsonInfoService();
						$userInfo = $UserJsonService->getUserInfo($uid,false);
						if (!isset($archivesInfo['title']) || !isset($userInfo)){
							return 4;
						}
						try {
							//全站广播
							$this->_sendZmqForBroadcast($uid,$archivesInfo['title'],$content,$aid,$archivesInfo['uid']);
							//写入广播记录表
							$contents['uid'] = $uid;
							$contents['dotey_uid'] = $archivesInfo['uid'];
							$contents['aid'] = $aid;
							$contents['price'] = $price;
							$contents['content'] = $content;
							$record_id = $this->saveBroadcastContent($contents);
							$contents['title']=$archivesInfo['title'];
							$contents['nickname']=$userInfo['nk'];
							if(isset($userInfo['vip'])){
								if(isset($userInfo['vip']['t'])&&$userInfo['vip']['t']>0){
									$contents['isVip']=1;
								}
							}else{
								$contents['isVip']=0;
							}
							
							$contents['time']=time();
							self::saveBroadcastToCache($contents);
							if($broadcastId<=0){
								//写入皮蛋log
								$pipieggRecords['uid'] = $uid;
								$pipieggRecords['pipiegg'] = $price;
								$pipieggRecords['from_target_id'] = $dotey_uid;
								$pipieggRecords['num'] = 1;
								$pipieggRecords['to_target_id'] = $archives_id;
								$pipieggRecords['record_sid'] = $record_id;
								$pipieggRecords['source']=SOURCE_SENDS;
								$pipieggRecords['sub_source']=SUBSOURCE_SENDS_ACTIVITY;
								$pipieggRecords['extra']='全站广播';
								$consumeService->saveUserPipiEggRecords($pipieggRecords, false);
								
								//写入用户贡献值记录
								$dedicationRecords['uid'] = $uid;
								$dedicationRecords['dedication'] = $dedication;
								$dedicationRecords['num'] = 1;
								$dedicationRecords['from_target_id'] = $dotey_uid;
								$dedicationRecords['to_target_id'] = $archives_id;
								$dedicationRecords['record_sid'] = $record_id;
								$dedicationRecords['source']=SOURCE_SENDS;
								$dedicationRecords['sub_source']=SUBSOURCE_SENDS_ACTIVITY;
								$dedicationRecords['info']='全站广播';
								$consumeService->saveUserDedicationRecords($dedicationRecords, true);
								$consumeService->saveUserConsumeAttribute(array('uid'=>$uid,'dedication'=>$dedication,'pipiegg'=>$price));
							}
							return 7;#成功
						}catch (Exception $m){
							return 7;#成功
						}
					}
				}
			}
		}
	}
	
	/**
	 * @param int $uid
	 * @param int $nickname
	 * @param string $title
	 * @param string $content
	 */
	private function _sendZmqForBroadcast($uid,$title,$content,$archives_id,$dotey_uid){
		$UserJsonService = new UserJsonInfoService();
		$userJson = $UserJsonService->getUserInfo($uid,false);
		if($userJson){
			$zmq=new PipiZmq();
			$json_content['type']='FullSite';
			$json_content['user_json']=$userJson;
			$json_content['title']=$title;
			$json_content['content']=$content;
			$json_content['aid']=$archives_id;
			$json_content['dotey_uid']=$dotey_uid;
			$json_content['time']=date('H:i',time());
			$eventData['archives_id']='*';
			$eventData['domain']=DOMAIN;
			$eventData['type']='broadcast';
			$eventData['json_content']=$json_content;
			$zmq->sendBrodcastMsg($eventData);
		}
	}
}

?>