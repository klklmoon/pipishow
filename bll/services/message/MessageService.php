<?php
/**
 * @var int 系统消息
 */
define('MESSAGE_CATEGORY_SYSTEM',0);
/**
 * @var 系统消息之升级
 */
define('MESSAGE_CATEGORY_SYSTEM_UPGRADE',0);
/**
 * @var 系统消息之后台推送
 */
define('MESSAGE_CATEGORY_SYSTEM_PUSH',1);
/**
 * @var 系统消息之全站消息
 */
define('MESSAGE_CATEGORY_SYSTEM_SITE',2);
/**
 * @var 系统消息之主播开播
 */
define('MESSAGE_CATEGORY_SYSTEM_KAIBO',3);
/**
 * @var 家族消息
 */
define('MESSAGE_CATEGORY_FAMILY',1);
/**
 * @var 家族消息之加入与退出
 */
define('MESSAGE_CATEGORY_FAMILY_JOIN',0);
/**
 * @var 家族消息之管理
 */
define('MESSAGE_CATEGORY_FAMILY_MANAGE',1);	
/**
 * @var 家族消息之升级
 */
define('MESSAGE_CATEGORY_FAMILY_UPGRADE',2);

define('MESSAGE_PUSH_TYPE_GLOBAL',0);#全站所有人
define('MESSAGE_PUSH_TYPE_USER',1);#指定用户
define('MESSAGE_PUSH_TYPE_LIVE',2);#指定直播间
define('MESSAGE_PUSH_TYPE_DOTEY', 3);#指定主播

/**
 * 消息服务层
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: MessageService.php 16912 2013-12-11 03:10:35Z hexin $ 
 * @package service
 * 
 */
class MessageService extends PipiService {
	/**
	 * 存储用户发送消息
	 * 
	 * @param array $message
	 * @return int
	 */
	public function sendMessage(array $message){
		if(!isset($message['uid']) || $message['uid'] < 0){
			return $this->setNotice('message.param',Yii::t('common','Parameter is empty'),0);
		}
		
		if(!isset($message['category']) || !isset($message['sub_category'])){
			return $this->setNotice('message.param',Yii::t('common','Parameter is empty'),0);
		}
		
		if(isset($message['extra'])){
			if(is_array($message['extra'])){
				$message['extra'] = json_encode($message['extra']);
			}
		}
		
		$counterField = $this->mapMessageTypeCounter($message['category'],$message['sub_category']);
		if(empty($counterField)){
			 return $this->setNotice('message.counter',Yii::t('message','counter is empty'),0);
		}
		if(isset($message['to_uid'])){
			$message['receive_uid'] = $message['to_uid'];
			if(is_array($message['receive_uid']))
				$message['receive_uid'] = implode(',', $message['receive_uid']);
		}
		$senderUid = $message['uid'];
		$isRead = $this->array_get($message,'is_read');
		$toUids = $this->checkBlackList($senderUid,$this->array_get($message,'to_uid'));
		if(empty($toUids)){
			//不是全站消息，接收者UID必须存在
			if((!isset($message['is_site']) || !$message['is_site'])){
				 return $this->setNotice('message.recevive',Yii::t('message','Recipient is empty'),0);
			}
		}
		
		$message['create_time'] = time();
		$message['update_time'] = time();
		$messageContentModel = new MessageContentModel();
		$this->attachAttribute($messageContentModel,$message);
		if(!$messageContentModel->validate()){
				return $this->setNotices($messageContentModel->getErrors(),0);
		}
		if($messageContentModel->insert()){
			if(isset($message['is_site']) && $message['is_site']){
				return $messageContentModel->getPrimaryKey();
			}
			$toUids = array_unique($toUids);
			//更新接收消息人未读消息数
			if(!$isRead){
				$this->updateMessageStatisticsCounters($toUids,array($counterField=>1));
			}
			$relation = array();
			if($senderUid > 0){
				$toUids[$senderUid] = $senderUid;
			}
			$rows = 0;
			foreach($toUids as $uid){
				if($uid == $senderUid){
					$relation[$rows]['is_own'] = 1;
					$relation[$rows]['is_read'] = 0;
				}else{
					$relation[$rows]['is_own'] = 0;
					$relation[$rows]['is_read'] = (int)$isRead;
				}
				$relation[$rows]['uid'] = $uid;
				$relation[$rows]['message_id'] = $messageContentModel->getPrimaryKey();
				$relation[$rows]['create_time'] = time();
				$rows++;
			}
			$this->saveMultiMessageRelation($relation);
		}
		return $messageContentModel->getPrimaryKey();
		
	}
	
	/**
	 * 添加推送消息
	 * 
	 * @param array $message
	 * @return number
	 */
	public function pushMessage(array $message){
		if(isset($message['title']) && empty($message['title'])){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		
		if(!isset($message['content']) || empty($message['content'])){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		
		if (isset($message['extra'])){
			$message['extra'] = json_encode($message['extra']);
		}
		
		$messagePushModel = new MessagePushModel();
		if(isset($message['push_id'])){
			$_messagePushModel = $messagePushModel->findByPk($message['push_id']);
			if(empty($_messagePushModel)){
				return $this->setNotice('message',Yii::t('user','The push message does not exist'),0);
			}
			$this->attachAttribute($_messagePushModel,$message);
			$_messagePushModel->save();
			return $_messagePushModel->getPrimaryKey();
		}else{
			$this->attachAttribute($messagePushModel,$message);
			$messagePushModel->save();
			return $messagePushModel->getPrimaryKey();
		}
		return 0;
	}
	/**
	 * 获取用户收到的消息
	 * 
	 * @param int $uid 用户ＩＤ
	 * @param int $category　消息分类
	 * @param int $subcategory　消息子分类
	 * @param array $extendCondition　额外扩展条件
	 * @return array
	 */
	public function getUserReceiveMessagesByUid($uid,$category,$subcategory,array $extendCondition = array()){
		if($uid <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$condition = array();
		$condition['uid'] = $uid;
		$condition['is_own'] = 0;
		if($category>=0)
			$condition['category'] = $category;
		if(!is_null($subcategory))
			$condition['sub_category'] = $subcategory;
		$condition = array_merge($condition,$extendCondition);
		$relationModel =  MessageRelationModel::model();
		$userMessages = $relationModel->getUserMessageByUidsCondition($condition);
		return $this->buildUserMessage($userMessages);
	}
	
	/**
	 * 统计获取用户收到的消息数量
	 * @param int $uid 用户ＩＤ
	 * @param int $category　消息分类
	 * @param mixed $subcategory　消息子分类
	 * @param array $extendCondition　额外扩展条件
	 * @return array
	 */
	public function countUserReceiveMessages($uid,$category,$subcategory,array $extendCondition = array())
	{
		if($uid <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$condition = array();
		$condition['uid'] = $uid;
		$condition['is_own'] = 0;
		if($category>=0)
			$condition['category'] = $category;
		if(!is_null($subcategory))
			$condition['sub_category'] = $subcategory;
			
		$condition = array_merge($condition,$extendCondition);
		$relationModel =  MessageRelationModel::model();
		return $relationModel->countUserMessageByUidsCondition($condition);
	}
	
	/**
	 * 获取用户发送的消息
	 * 
	 * @param int $uid 用户ＩＤ
	 * @param int $category　消息分类
	 * @param int $subcategory　消息子分类
	 * @param array $extendCondition　额外扩展条件
	 * @return array
	 */
	public function getUserSendMessagesByUid($uid,$category,$subcategory,array $extendCondition = array()){
		if($uid <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$condition = array();
		$condition['uid'] = $uid;
		$condition['is_own'] = 1;
		if($category >= 0)
			$condition['category'] = $category;
		if(!is_null($subcategory))
			$condition['sub_category'] = $subcategory;
			
		$condition = array_merge($condition,$extendCondition);
		$relationModel =  MessageRelationModel::model();
		$userMessages = $relationModel->getUserMessageByUidsCondition($condition);
		return $this->buildUserMessage($userMessages);
	}
	
	/**
	 * 取得全站信息
	 * 
	 * @param int $uid
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	public function getUserReceiveSiteMessagesByUid($uid,$limit,$offset){
		$messageContentModel = new MessageContentModel();
		$messages = $messageContentModel->getUserReceiveSiteMessagesByUid($limit,$offset);
		if(empty($messages)){
			return array();
		}
		$messages = $this->arToArray($messages);
		$messages = $this->buildUserMessage($messages);
		$messagesIds = array_keys($messages);
		$unReadMessages = $this->getUserUnReadSiteMessagesByUid($uid,$messagesIds);
		$delSiteMessages = $this->getUserDelSiteMessagesByUid($uid,$messagesIds);
		foreach($messages as $id => $_message){
			if(isset($unReadMessages[$id])){
				$messages[$id]['is_read'] = 0;
			}else{
				$messages[$id]['is_read'] = 1;
			}
			if(isset($delSiteMessages[$id])){
				unset($messages[$id]);
			}
		}
		return $messages;
		
	}
	/**
	 * 计算用户读取全的全站消息总数
	 * 
	 * @param $uid
	 * @return int
	 */
	public function countUserReceiveSiteMessagesByUid($uid){
		 return MessageContentModel::model()->countUserReceiveSiteMessagesByUid() - MessageContentModel::model()->countDelSiteMessagesByUid($uid);
	}
	/**
	 * 计算用户未读取全的全站消息的总数
	 * 
	 * @param $uid
	 * @return int
	 */
	public function countUserUnReadSiteMessagesByUid($uid){
		return MessageContentModel::model()->countUserUnReadSiteMessagesByUid($uid);
	}
	
	/**
	 * 获取用户删除的全站消息总数
	 * 
	 * @param int $uid
	 * @return int
	 */
	public function countDelSiteMessagesByUid($uid){
		return MessageContentModel::model()->countDelSiteMessagesByUid($uid);
	}
	
	public function getUserUnReadSiteMessagesByUid($uid,array $messageIds = array()){
		$messageContentModel = new MessageContentModel();
		$messages = $messageContentModel->getUserUnReadSiteMessagesByUid($uid,$messageIds);
		return $this->buildDataByIndex($messages,'message_id');
	}
	
	public function getUserDelSiteMessagesByUid($uid,array $messageIds = array()){
		$messageContentModel = new MessageContentModel();
		$messages = $messageContentModel->getUserDelSiteMessagesByUid($uid,$messageIds);
		return $this->buildDataByIndex($messages,'message_id');
	}
	/**
	 * 获取用户配置
	 * 
	 * @param int $uid 用户ＩＤ
	 * @return array
	 */
	public function getMessageConfigByUid($uid){
		if($uid <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		
		$userConfigModel =   UserConfigModel::model();
		$orgMessageConfigModel = $userConfigModel->findByPk($uid);
		if($orgMessageConfigModel){
			$orgMessageConfigModel->blacklist = unserialize($orgMessageConfigModel->blacklist);
			$orgMessageConfigModel->sheildmessage = unserialize($orgMessageConfigModel->sheildmessage);
			return $orgMessageConfigModel->attributes;
		}
		return array();
	}
	
	/**
	 * 获取用户配置
	 * 
	 * @param array $uids 用户ＩＤ
	 * @return array
	 */
	private function getMessageConfigByUids(array $uids){
		if(empty($uids)){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		
		$userConfigModel =   UserConfigModel::model();
		$messageConfigs = $userConfigModel->getUserConfigByUids($uids);
		$attriubes = array();
		foreach($messageConfigs as $key=>$config){
			$config->blacklist = unserialize($config->blacklist);
			$config->sheildmessage = unserialize($config->sheildmessage);
			$attriubes[$config->uid] = $config->attributes;
		}
		return $attriubes;
	}
	
	/**
	 * 删除消息
	 * 
	 * @param int $ids 消息ＩＤ
	 * @return array
	 */
	public function delMessageByIds(array $ids){
		if(empty($ids)){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$this->delMessageRelationByMessageIds($ids);
		return MessageContentModel::model()->delMessageByIds($ids);
	}
	
	/**
	 * 删除摄推送消息
	 *
	 * @param array $pushIds 消息ＩＤ
	 * @return array
	 */
	public function delPushByIds(array $pushIds){
		if(empty($pushIds)){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		return MessagePushModel::model()->delPushByIds($pushIds);
	}
	

	/**
	 * 标记消息已读
	 * 
	 * @param int $uid
	 * @param int $messageId
	 * @param  int $type
	 * @return boolean
	 */
	public function markReadMessage($uid,$messageId,$type = NULL){
		if($messageId <=0 || $uid <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		}
		$condition = array('uid'=>$uid,'message_id'=>$messageId);
		if($type == 'site'){
			$messagePushModel = new MessagePushReadModel();
			if($messagePushModel->findByAttributes($condition)){
				return false;
			}
			$messagePushModel->uid = $uid;
			$messagePushModel->message_id = $messageId;
			$messagePushModel->is_del = 0;
			$messagePushModel->create_time = time();
			return $messagePushModel->save();
		}else{
			$messageRelationModel = new MessageRelationModel();
			
			$messageContent = MessageContentModel::model()->findByPk($messageId);
			if($messageContent){
				$countField = $this->mapMessageTypeCounter($messageContent->category,$messageContent->sub_category);
				$this->updateMessageStatisticsCounters($uid,array($countField=>-1));
			}
			
			if($messageRelationModel->updateAll(array('is_read'=>1),'uid = :uid AND message_id = :messageId',array(':uid'=>$uid,':messageId'=>$messageId))){
				return true;
			}
		}
		return false;
		
	}
	
	/**
	 * 删除用户收件箱
	 * 
	 * @param int $uid
	 * @param int $messageId
	 * @param string $type
	 * @return number
	 */
	public function delUserMessage($uid,$messageId,$type = NULL){
		if($messageId <=0 || $uid <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$condition = array('uid'=>$uid,'message_id'=>$messageId);
		if($type == 'site'){
			$messagePushModel = new MessagePushReadModel();
			$orgMessagePushModel = $messagePushModel->findByAttributes($condition);
			if(!$orgMessagePushModel){
				$messagePushModel->uid = $uid;
				$messagePushModel->message_id = $messageId;
				$messagePushModel->is_del = 1;
				$messagePushModel->create_time = time();
				return $messagePushModel->save();
			}
			return $messagePushModel->updateAll(array('is_del'=>1),'uid = :uid AND message_id = :messageId',array(':uid'=>$uid,':messageId'=>$messageId));
		}else{
			$messageRelationModel = new MessageRelationModel();
			if($messageRelationModel->findByAttributes(array_merge($condition,array('is_read'=>0)))){
				$messageContent = MessageContentModel::model()->findByPk($messageId);
				if($messageContent){
					$countField = $this->mapMessageTypeCounter($messageContent->category,$messageContent->sub_category);
					$this->updateMessageStatisticsCounters($uid,array($countField=>-1));
				}
			}
			return $messageRelationModel->deleteAllByAttributes($condition);
		}
		return 0;
	}
	/**
	 * 删除用户关系的消息
	 * 
	 * @param int $ids 关系ID
	 * @return array
	 */
	public function delMessageRelationByIds(array $ids){
		if(empty($ids)){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		return  MessageRelationModel::model()->delRelationByIds($ids);
	}
	
	/**
	 * 删除用户关系的消息
	 * 
	 * @param array $messageIds 消息ＩＤ
	 * @return mix|number
	 */
	public function delMessageRelationByMessageIds(array $messageIds){
		if(empty($messageIds)){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		return MessageRelationModel::model()->delRelationByMessageIds($messageIds);
	}
	
	/**
	 * 取得消息分类列表
	 * 
	 * @param string $item 分类项
	 * @return string|array
	 */
	public function getMessageCateGoryList($item = NULL){
		$cateGoryList = array(
			MESSAGE_CATEGORY_FAMILY => '家族消息',
			MESSAGE_CATEGORY_SYSTEM=>'系统消息',
		);
		
		return !is_null($item) && isset($cateGoryList[$item]) ? $cateGoryList[$item] : $cateGoryList;
	}
	
	/**
	 * 获取消息分类
	 * 
	 * @param string $item 消息分类
	 * @param string $subItem　消息分类子项
	 * @return  string | array
	 */
	public function getMessageSubCategoryList($item = null ,$subItem = null){
		$cateGoryList = array(
			MESSAGE_CATEGORY_FAMILY => array(
				'name'=>$this->getMessageCateGoryList(MESSAGE_CATEGORY_FAMILY),
				'child'=>array(
					MESSAGE_CATEGORY_FAMILY_JOIN=>'家族申请',
					MESSAGE_CATEGORY_FAMILY_MANAGE=>'家族管理',
					MESSAGE_CATEGORY_FAMILY_UPGRADE=>'家族升级',
				)
			),
			MESSAGE_CATEGORY_SYSTEM=>array(
				'name'=>$this->getMessageCateGoryList(MESSAGE_CATEGORY_SYSTEM),
				'child'=>array(
					MESSAGE_CATEGORY_SYSTEM_UPGRADE=>'用户升级',
					MESSAGE_CATEGORY_SYSTEM_PUSH=>'后台推送',
					MESSAGE_CATEGORY_SYSTEM_KAIBO=>'主播开播',
					MESSAGE_CATEGORY_SYSTEM_SITE=>'全站消息',
				)
			),
			
		);
		
		if( $item>=0 && isset($cateGoryList[$item])){
			if( $item>=0 && isset($cateGoryList[$item]['child'][$subItem])){
				return $cateGoryList[$item]['child'][$subItem];
			}else{
				return $cateGoryList[$item];
			}
		}
		return $cateGoryList;
	}
	
	/**
	 * 检查发送进是否被接收者加入黑名单
	 * 
	 * @param int $senderUid
	 * @param mixed $receiveUids
	 * @return array
	 */
	private function checkBlackList($senderUid,$receiveUids){
		if(!$receiveUids){
			return array();
		}
		if(!is_array($receiveUids)){
			$receiveUids = explode(',',$receiveUids);
		}
		$_tempUids = array();
		foreach($receiveUids as $uid){
			$_tempUids[$uid] = $uid;
		}
		$userMessageConfigs = $this->getMessageConfigByUids($receiveUids);
		foreach ($userMessageConfigs as $uid=>$config){
			if($config['blacklist'] && in_array($senderUid,$config['blacklist'])){
				unset($_tempUids[$uid]);
				$this->setNotice('message.blacklist',Yii::t('message','{uid}:Shields you send message',array('{uid}'=>$uid)));
			}
		}
		return $_tempUids;
	}
	
	/**
	 * 是否屏蔽此消息接收类型
	 * 
	 * @param int $uid 发送者用户
	 * @param string $category　消息类型
	 * @param string $subcategory 消息子类型
	 * @return array
	 */
	public function isShieldMessage($uid,$category,$subcategory){
		$meMessageConfig = $this->getMessageConfigByUid($uid);
		if($meMessageConfig){
			$sheildMesage = $meMessageConfig['sheildmessage'];
			if(isset($sheildMesage[$category]) && $sheildMesage[$category]['is_close']){
				return true;
			}
			$childCategory = $sheildMesage[$category]['child'];
			if(isset($childCategory[$subcategory]) && $childCategory[$subcategory]){
				return true;
			}
		}
		return false;
	}
	
	/**
	 * 存储用户消息关系
	 * 
	 * @param array $relation
	 * @return int
	 */
	protected function saveMultiMessageRelation(array $relation){
		if(empty($relation)){
			return $this->setError(Yii::t('common','Parameter is empty'),array());
		}
		
		$relationModel = new MessageRelationModel();
		return $relationModel->batchInsert($relation);
	}
	
	
	protected function buildUserMessage(array $message){
		$userMessage = array();
		foreach($message as $_message){
			$_message['extra'] = json_decode($_message['extra'],true);
			$userMessage[$_message['message_id']] = $_message;
		}
		return $userMessage;
	}
	
	
	/**
	 * 更新用户未读消息数
	 * @param mixed $uid
	 * @param array $counters
	 * @return mix|number
	 */
	public function updateMessageStatisticsCounters($uid ,array $counters){
		if(!$counters || !$uid){
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		}
		$uid = is_array($uid) ? $uid : array($uid);
		$messageStatisModel = new MessageStatisticsModel();
		$criteria = $messageStatisModel->getDbCriteria();
		$criteria->addInCondition('uid',$uid);
		$eUsers = $messageStatisModel->findAll($criteria);
		if($eUsers){
			$eUsers = $this->arToArray($eUsers);
			$eUsers = array_keys($this->buildDataByIndex($eUsers,'uid'));
			foreach($uid as $key=>$_uid){
				if(in_array($_uid,$eUsers)){
					unset($uid[$key]);
				}
			}
			$messageStatisModel->updateCounters($counters,'uid IN ('.implode(',',$eUsers).')');
		}
		if($uid){
			$newData = array();
			$i = 0;
			foreach($uid  as $_uid){
				foreach($counters as $key=>$value){
					$newData[$i]['uid'] = $_uid;
					$newData[$i][$key] = $value;
				}
				$i++;
			}
			return $messageStatisModel->batchInsert($newData);
		}
		
	}
	
	public function getUserMessageUnReads($uids){
		if(empty($uids)){
			return array();
		}
		$uids = is_array($uids) ? $uids : array($uids);
		$messageStatisModel = new MessageStatisticsModel();
		$dbCriteria = $messageStatisModel->getDbCriteria();
		$dbCriteria->addInCondition('uid',$uids);
		$unReads = $messageStatisModel->findAll($dbCriteria);
		if($unReads){
			$unReads = $this->arToArray($unReads);
			$unReads = $this->buildDataByIndex($unReads,'uid');
		}
		return $unReads;
	}
	public function mapMessageTypeCounter($item ,$subItem){
		$cateGoryList = array(
			MESSAGE_CATEGORY_FAMILY => array(
				'value'=>'family',
				'child'=>array(
					MESSAGE_CATEGORY_FAMILY_JOIN=>'family_join',
					MESSAGE_CATEGORY_FAMILY_MANAGE=>'family_manage',
					MESSAGE_CATEGORY_FAMILY_UPGRADE=>'family_upgrade',
				)
			),
			MESSAGE_CATEGORY_SYSTEM=>array(
				'value'=>'system',
				'child'=>array(
					MESSAGE_CATEGORY_SYSTEM_UPGRADE=>'system_upgrade',
					MESSAGE_CATEGORY_SYSTEM_PUSH=>'system_push',
					MESSAGE_CATEGORY_SYSTEM_KAIBO=>'system_kaibo',
					MESSAGE_CATEGORY_SYSTEM_SITE=>'system_site',
				)
			),
		);
		
		if( $item>=0 && isset($cateGoryList[$item])){
			if( $item>=0 && isset($cateGoryList[$item]['child'][$subItem])){
				return $cateGoryList[$item]['child'][$subItem];
			}else{
				return '';
			}
		}
		return '';
	}
	
	
	/**
	 * @author supeng
	 * @param array $condition
	 * @param unknown_type $offset
	 * @param unknown_type $pageSize
	 * @param unknown_type $isLimit
	 * @return Ambigous <multitype:, multitype:NULL , multitype:Ambigous <NULL, unknown, multitype:unknown Ambigous <unknown, NULL> , mixed, multitype:, multitype:unknown > Ambigous <string, unknown, mixed> >
	 */
	public function searchMessage(Array $condition = array(),$offset = 0,$pageSize=20,$isLimit=true){
		$model = new MessageContentModel();
		$data = $model->searchMessage($condition,$offset,$pageSize,$isLimit);
		if (!empty($data['list'])) {
			$data['list'] = $this->arToArray($data['list']);
		}
		return $data;
	}
	
	/**
	 * @author supeng
	 * @param array $condition
	 * @param unknown_type $offset
	 * @param unknown_type $pageSize
	 * @param unknown_type $isLimit
	 */
	public function searchPush(Array $condition = array(),$offset = 0,$pageSize=20,$isLimit=true){
		$model = new MessagePushModel();
		$data = $model->searchPush($condition,$offset,$pageSize,$isLimit);
		if (!empty($data['list'])) {
			$data['list'] = $this->arToArray($data['list']);
		}
		return $data;
	}
	
	/**
	 * @author supeng
	 * @param unknown_type $flag
	 */
	public function getExtraFlag($flag = null){
		$flags = array(
				'href'=>array('name'=>'链接','default'=>''),
				'from'=>array('name'=>'来源','default'=>'系统发送'),
			);
		return isset($flags[$flag])?$flags[$flag]:$flags;
	}
	
	/**
	 * @author supeng
	 * @param unknown_type $type
	 */
	public function getPushType($type = null){
		$types = array(
				MESSAGE_PUSH_TYPE_GLOBAL=>'全部所有人',
				MESSAGE_PUSH_TYPE_USER=>'指定用户',
				MESSAGE_PUSH_TYPE_LIVE=>'指定直播间',
				MESSAGE_PUSH_TYPE_DOTEY=>'指定主播'
			);
		return isset($types[$type])?$types[$type]:$types;
	}
}

?>