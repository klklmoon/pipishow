<?php
define('USER_REG_SOURCE_PIPI',0);
define('USER_REG_SOURCE_QQ',1);
define('USER_REG_SOURCE_RENREN',2);
define('USER_REG_SOURCE_360',3);
define('USER_REG_SOURCE_PPTV',4);
define('USER_REG_SOURCE_BAIDU',5);
define('USER_REG_SOURCE_SINA',6);
define('USER_REG_SOURCE_TULI',7);
define('USER_REG_SOURCE_SOUSHI_GAME',8);

define('USER_LOGIN_USERNAME',0);
define('USER_LOGIN_EMAIL',1);

define('USER_TYPE_COMMON',1);
define('USER_TYPE_DOTEY',2);
define('USER_TYPE_ADMIN',4);
define('USER_TYPE_FAMILY',8);

define('USER_STATUS_ON',0);
define('USER_STATUS_OFF',1);

define('USER_OPERATED_TYPE_USERSTATUS','user_status');#用户被操作类型（用户状态）
/**
 * 用户信息服务
 *
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author Su Qian <aoxue.1988.su.qian@163.com>
 * @version $Id: UserService.php 17577 2014-01-16 06:52:09Z hexin $
 * @package service
 */
class UserService extends PipiService{
	
	/**
	 * @var PipiFlashUpload 用户头像上传组件
	 */
	protected static $flashUpload = null;
	/**
	 * 存储用户基本信息
	 * 
	 * @param array $userBasic
	 * @return int
	 */
	public function saveUserBasic(array $userBasic){
		if(isset($userBasic['uid']) && ($uid = $userBasic['uid']) <= 0){
			return $this->setError(Yii::t('common','Parameter is empty').'1',array());
		}
		
		$userBasicModel = new UserBasicModel();
		if(isset($userBasic['uid']) && ($orgUserBasicModel = $userBasicModel->findByPk($uid))){
			if(empty($orgUserBasicModel)){
				return $this->setNotice('user',Yii::t('user','The user does not exist'),array());
			}
			if(isset($userBasic['reg_salt'])){
				unset($userBasic['reg_salt']);
			}
			if(isset($userBasic['password']) && $userBasic['password'] != '' ){
				if(isset($userBasic['apiPassword']) && $userBasic['apiPassword']){
					//如果是API调用，则传过来的password是加密过了的,避免明文传递
					$userBasic['password'] = md5($userBasic['password'].$orgUserBasicModel->reg_salt);
					unset($userBasic['apiPassword']);
				}else{
					$userBasic['password'] = $this->encryPassword($userBasic['password'],$orgUserBasicModel->reg_salt);
				}
			}
			$counter = array();
			if(isset($userBasic['recharge'])){
				$counter['recharge'] = $userBasic['recharge'];
				unset($userBasic['recharge']);
			}
			
			if(isset($userBasic['recharge_usd'])){
				$counter['recharge_usd'] = $userBasic['recharge_usd'];
				unset($userBasic['recharge_usd']);
			}
			
			if($counter){
				$userBasicModel->updateCounters($counter,'uid = '.$uid);
			}
			
			if(isset($userBasic['update_desc']) && is_array($userBasic['update_desc'])){
				$orgUserBasicModel->update_desc = json_decode($orgUserBasicModel->update_desc,true);
				if(is_array($orgUserBasicModel->update_desc)){
					$userBasic['update_desc'] = array_merge($orgUserBasicModel->update_desc,$userBasic['update_desc']);
				}
				$userBasic['update_desc'] = json_encode($userBasic['update_desc']);
			}
			
			$this->attachAttribute($orgUserBasicModel,$userBasic);
			if(!$orgUserBasicModel->validate()){
				return $this->setNotices($orgUserBasicModel->getErrors(),array());
			}
			$orgUserBasicModel->save();
			$users = $orgUserBasicModel->attributes;
		}else{
			if(isset($userBasic['update_desc']) && is_array($userBasic['update_desc'])){
				$userBasic['update_desc'] = json_encode($userBasic['update_desc']);
			}
			if(!isset($userBasic['reg_salt']) || !$userBasic['reg_salt']){
				$userBasic['reg_salt'] = substr(uniqid(rand()), -6);
			}
			if(isset($userBasic['password'])){
				if(isset($userBasic['apiPassword']) && $userBasic['apiPassword']){
					//如果是API调用，则传过来的password是加密过了的,避免明文传递
					$userBasic['password'] = md5($userBasic['password'].$userBasic['reg_salt']);
					unset($userBasic['apiPassword']);
				}else{
					$userBasic['password'] = $this->encryPassword($userBasic['password'],$userBasic['reg_salt']);
				}
			}
			
			$this->attachAttribute($userBasicModel,$userBasic);
			$userBasicModel->create_time = time();
			if(!isset($userBasic['reg_ip'])){
				$userBasicModel->reg_ip = Yii::app()->request->userHostAddress;
			}
			if(!$userBasicModel->validate()){
				return $this->setNotices($userBasicModel->getErrors(),array());
			}
			if(!isset($userBasic['uid'])){
				$userBasicModel->setPrimaryKey($this->getNextUid());
			}
			$userBasicModel->save();
			$users = $userBasicModel->attributes;
		}
		
		$UserLoginRedisModel = new UserLoginRedisModel();
		$UserLoginRedisModel->saveUserBasicToRedis($users['username'],$users);
			
		if ($this->isAdminAccessCtl() && $users){
			$op_desc = '编辑 用户基本信息(UID='.$users['uid'].')';
			$this->saveAdminOpLog($op_desc,$users['uid']);
		}
		return $users;
	}
	
	public function saveUserJson($uid,array $user){
		if($uid <= 0 || empty($user)){
			return $this->setError(Yii::t('common','Parameter is empty').'2',false);
		}
		$json = array();
		if(isset($user['nickname'])){
			$json['nk'] = $user['nickname'];
		}
		
		if(isset($user['user_type'])){
			$json['ut'] = $user['user_type'];
		}
		
		if(isset($user['user_status'])){
			$json['us'] = $user['user_status'];
		}
		
		if(isset($user['update_desc'])){
			$json['atr'] = $user['update_desc']['atr'];
		}
		
		$user['uid'] = $uid;
		$res = $this->saveUserBasic($user);
		if(!isset($res['uid'])){
			return false;
		}
		
		$userJsonService = new UserJsonInfoService();
		$userJsonService->setUserInfo($uid, $json);
		
		$zmq = $this->getZmq();
		$zmq->sendZmqMsg(609,array('type'=>'update_json','uid'=>$uid,'json_info'=>$json));
		return true;
		
	}


	/**
	 * 存储用户扩展数据
	 * 
	 * @author guoshaobo 添加查询是否为主播, 添加redis
	 * 
	 * @param array $userExtend 用户扩展数据
	 * @return boolean
	 */
	public function saveUserExtend(array $userExtend){
		if(($uid = $userExtend['uid']) <= 0){
			return $this->setError(Yii::t('common','Parameter is empty').'3',false);
		}
		$flag = false;
		$userExtendModel = new UserExtendModel();
		$orgUserExtendModel = $userExtendModel->findByPk($uid);
		if($orgUserExtendModel){
			$this->attachAttribute($orgUserExtendModel,$userExtend);
			if(!$orgUserExtendModel->validate()){
				return $this->setNotices($orgUserExtendModel->getErrors(),false);
			}
			$flag = $orgUserExtendModel->save();
		}else{
			$this->attachAttribute($userExtendModel,$userExtend);
			if(!$userExtendModel->validate()){
				return $this->setNotices($userExtendModel->getErrors(),false);
			}
			$flag = $userExtendModel->save();
		}
		if($flag) {
			$redisCacheModel = new OtherRedisModel();
			$keys[] = $key ='dotey_info_'.$uid;
			$doteyExtend = $redisCacheModel->getArchivesDataFromRedis($keys);
			if($doteyExtend[$key]){
				$doteys['update_desc'] = array('atr'=>time());
				$redisCacheModel->setDoteyInfoToRedisByUid($uid,$doteys);
			}
			if($this->isAdminAccessCtl()){
				if(isset($userExtend['uid'])){
					$op_desc = '编辑 用户扩展信息(UID='.$userExtend['uid'].')';
					$this->saveAdminOpLog($op_desc,$userExtend['uid']);
				}
			}
		}
		return $flag;
	}
	
	/**
	 * 存储用户开放平台注册
	 * 
	 * @param array $userOauth
	 * @return boolean
	 */
	public function saveUserOauth(array $userOauth){
		if(!isset($userOauth) || $userOauth['uid'] <= 0){
			return $this->setError(Yii::t('common','Parameter is empty').'4',false);
		}
		
		if(!isset($userOauth['openid'])){
			return $this->setError(Yii::t('common','Parameter is empty').'5',false);
		}
		$userOauthModel = new UserOAuthModel();
		$userOauthModel->create_time = time();
		$this->attachAttribute($userOauthModel,$userOauth);
		return $userOauthModel->save();
	}
	
	public function saveUserLoginRecords(array $records){
		if(($uid = $records['uid']) <= 0){
			return $this->setError(Yii::t('common','Parameter is empty').'6',0);
		}
		$records['login_ip'] = Yii::app()->request->userHostAddress;
		$records['login_time'] = time();
		$userLoginRecordsModel = new UserLoginRecordsModel();
		$this->attachAttribute($userLoginRecordsModel,$records);
		$userLoginRecordsModel->save();
		return $userLoginRecordsModel->getPrimaryKey();
	}
	
	/**
	 * 保存用户被操作记录
	 * 
	 * @author supeng
	 * @param array $records
	 */
	public function saveUserOperated(array $records){
		if(!isset($records['uid']) || $records['uid'] <=0){
			return $this->setError(Yii::t('common','Parameter is empty').'7',false);
		}
		
		if(!isset($records['op_uid']) || $records['op_uid'] <=0){
			return $this->setError(Yii::t('common','Parameter is empty').'8',false);
		}
		
		$records['op_time'] = time();
		$userOperatedModel = new UserOperatedModel();
		$this->attachAttribute($userOperatedModel,$records);
		$userOperatedModel->save();
		return $userOperatedModel->getPrimaryKey();
	}
	
	/**
	 * 验证用户密码
	 * 
	 * @param string $condition 登录条件 username,email
	 * @param string $password 用户输入密码
	 * @param string $loginType 登录类型 0表示用户名登录　1表示邮箱登录
	 * @paran array  $user  外界捕获的用户信息
	 * @return boolean
	 */
	public function vadidatorPassword($condition,$password,$loginType,array &$user){
		$isApiPassword = isset($user['apiPassword']) && $user['apiPassword'];
		$user = $this->getVadidatorUser($condition,$loginType);
		if($user){
			if($isApiPassword){
				return  md5($password.$user['reg_salt']) == $user['password'];
			}else{
				return $this->encryPassword($password,$user['reg_salt']) === $user['password'];
			}
		}
		return false;
	}
	
	 /**
	 * 取得有效的用户
	 * 按登录方式(用户名/邮件)取得有效的用户信息.先从redis从，redis没有从数据库取
	 * 
	 * @param string $condition 获取条件 username,email
	 * @param string $loginType 查询类型 0表示用户名　1表示邮箱
	 * @return array
	 */
	public function getVadidatorUser($condition,$loginType){
		$userLoginRedisModel = new UserLoginRedisModel();
		$user = array();
		if($user = $userLoginRedisModel->getVadidatorUser($condition,$loginType)){
			return $user;
		}
		if(	$user = UserBasicModel::model()->getVadidatorUser($condition,$loginType)){
			$userLoginRedisModel->saveUserBasicToRedis($user['username'],$user);
		}
		return $user;
	}
	
	/**
	 * 下一个自增UID,为今后分表预留，用户注册时，必须要判断这个ＵＩＤ存储到那个数据库
	 * 
	 * @return number
	 */
	public function getNextUid(){
		$key = Yii::getKeyConfig('sequence','USER_ID');
		if(empty($key)){
			return trigger_error(Yii::t('common','{config} config is empty',array('{config}'=>'(user key)')),E_USER_ERROR);
		}
		return SequenceModel::model()->nextId($key);
	}
	/**
	 * 设置用户登录密码
	 * 
	 * @param string $password 明文密码
	 * @param string $salt 注册时分配的干扰码
	 * @return string
	 */
	public function encryPassword($password,$salt){
		if(empty($password) || empty($salt)){
			return $this->setError(Yii::t('common','Parameter is empty').'9','');
		}
		return md5($password.$salt);
	}
	/**
	 * 存储用户配置
	 * 
	 * @param array $config
	 * @return int
	 */
	public function saveUserConfig(array $config){
		if(($uid = $config['uid']) <= 0){
			return $this->setError(Yii::t('common','Parameter is empty').'10',0);
		}
		
		if(isset($config['sheildmessage'])){
			if(!is_array($config['sheildmessage']))
			   return $this->setError(Yii::t('message','sheildmessage must is array'),0);
			else
			   $config['sheildmessage'] = serialize($config['sheildmessage']);
		}
		
		if(isset($config['blacklist'])){
			if(!is_array($config['blacklist']))
			   return $this->setError(Yii::t('message','blacklist must is array'),0);
			else
			   $config['blacklist'] = serialize($config['blacklist']);
		}
		
		if(isset($config['sheilddynamic'])){
			if(!is_array($config['sheilddynamic']))
			   return $this->setError(Yii::t('message','sheilddynamic must is array'),0);
			else
			   $config['sheilddynamic'] = serialize($config['sheilddynamic']);
		}
		
		$userConfigModel =  new UserConfigModel();
		$orgUserConfigModel = $userConfigModel->findByPk($uid);
		if($orgUserConfigModel){
			 $this->attachAttribute($orgUserConfigModel,$config);
			 return $orgUserConfigModel->save();
		}else{
			$this->attachAttribute($userConfigModel,$config);
			return $userConfigModel->save();
		}
		return null;
	}
	
	
	/**
	 * 获取用户基本信息
	 * 
	 * @param array $uids 用户ＩＤ
	 * @return array
	 */
	public function getUserBasicByUids(array $uids){
		if(empty($uids)){
			return $this->setError(Yii::t('common','Parameter is empty').'11',array());
		}
		
		$userBasicModel =   UserBasicModel::model();
		$userBasicModels = $userBasicModel->getUserBasicByUids($uids);
		$attriubes = array();
		foreach($userBasicModels as $key=>$user){
			//todo
			$attriubes[$user->uid] = $user->attributes;
		}
		return $attriubes;
	}
	
	/**
	 * 按用户名称取用户信息
	 * 
	 * @param array $userNames
	 * @return array
	 */
	public function getUserBasicByUserNames(array $userNames){
		if(empty($userNames)){
			return $this->setError(Yii::t('common','Parameter is empty').'12',array());
		}
		$userRedisModel = new UserLoginRedisModel();
		$userBasics = $userRedisModel->getUserBasicByUserNames($userNames);
		$unCacheUserNames = array();
		if(empty($userBasics)){
			$unCacheUserNames = $userNames;
		}else{
			$_userBasics = $this->buildDataByIndex($userBasics,'username');
			foreach ($userNames as $username){
				if(!in_array($username,array_keys($_userBasics))){
					$unCacheUserNames[] = $username;
				}
			}
		}
		
		if($unCacheUserNames){
			$userBasicModel =  UserBasicModel::model();
			$models = $userBasicModel->getUserBasicByUsernames($unCacheUserNames);
			foreach($models as $model){
				$userBasics[] = $model->attributes;
				$userRedisModel->saveUserBasicToRedis($model->username,$model->attributes);
			}
		}
		return $this->buildDataByIndex($userBasics,'uid');
	}
	
	/**
	 * 按用户昵称取用户信息
	 *
	 * @param array $nickNames
	 * @return array
	 */
	public function getUserBasicByNickNames(array $userNames){
		$userBasicModel =  UserBasicModel::model();
		$models = $userBasicModel->getUserBasicByNicknames($userNames);
		$userBasics = array();
		foreach($models as $model){
			$userBasics[] = $model->attributes;
		}
		return $this->buildDataByIndex($userBasics,'nickname');
	}

	/**
	 * 获取用户扩展信息
	 *
	 * @param array $uids 用户ＩＤ
	 * @return array
	 */
	public function getUserExtendByUids(array $uids){
		if(empty($uids)){
			return $this->setError(Yii::t('common','Parameter is empty').'13',0);
		}
	
		$userExtendModel =   UserExtendModel::model();
		$userExtendModels = $userExtendModel->getUserExtendByUids($uids);
		$attriubes = array();
		foreach($userExtendModels as $key=>$user){
			//todo
			$attriubes[$user->uid] = $user->attributes;
		}
		return $attriubes;
	}
	/**
	 * 获取用户配置
	 * 
	 * @param array $uids 用户ＩＤ
	 * @return array
	 */
	public function getUserConfigByUids(array $uids){
		if(empty($uids)){
			return $this->setError(Yii::t('common','Parameter is empty').'14',0);
		}
		
		$userConfigModel =   UserConfigModel::model();
		$userConfigs = $userConfigModel->getUserConfigByUids($uids);
		$attriubes = array();
		foreach($userConfigs as $key=>$config){
			$config->blacklist = unserialize($config->blacklist);
			$config->sheildmessage = unserialize($config->sheildmessage);
			$config->sheilddynamic = unserialize($config->sheilddynamic);
			$attriubes[$config->uid] = $config->attributes;
		}
		return $attriubes;
	}
	
	/**
	 * 取得用户登录后的客户端初始化的属性
	 * 
	 * @param mixed $condition 用户ID/用户名
	 * @param boolean $isUid 是否是用户ID
	 * @param boolean $isDotey 是否是主播
	 * @return string 返回json字符串
	 */
	public function getUserFrontsAttributeByCondition($condition,$isUid = false,$isDotey = false){
		if(empty($condition)){
			return $this->setNotice(Yii::t('common','Parameter is empty').'15',0);
		}
		$user = array();
		if($isUid){
			$uid = $condition;
		}else{
			$user = $this->getVadidatorUser($condition,USER_LOGIN_USERNAME);
			$uid = $user['uid'];
		}
		$userJsonInfoModel = new UserJsonInfoRedisModel();
		$upload = $this->getAvatarUplaod();
		$userJson = $userJsonInfoModel->getUserInfo($uid);
		$returnUser = array();
		if(empty($userJson) || $userJson == '{}' || trim($userJson) == '' || strlen(trim($userJson)) == 0){
			if(empty($user)){
				$user = $this->getUserBasicByUids(array($uid));
				$user = $user[$uid];
			}
			$updateDesc = $user['update_desc'] ? json_decode($user['update_desc'],true) : array();
			$returnUser['uid'] = $uid;
			$returnUser['nk'] = $user['nickname'];
			$updateDesc['atr'] = isset($updateDesc['atr']) ? $updateDesc['atr'] : 0;
			$returnUser['is_redis'] = false;
			$zmq = $this->getZmq();
			$zmq->sendZmqMsg(609,array('type'=>'login_json','uid'=>$uid,'json_info'=>array('uid'=>$uid)));
		}else{
			$returnUser = json_decode($userJson,true);
			if(!isset($returnUser['uid']) || $returnUser['uid'] <= 0){
				$url =  Yii::app()->request->getHostInfo().Yii::app()->request->getRequestUri();
				$userJsonInfoModel->deleteUserInfos(array($uid));
				header('Location: '.$url);
				exit();
			}
			$consumeService = new ConsumeService();
			$allUserRank = $consumeService->getUserRankFromRedis();
			$nextRank = $allUserRank[$returnUser['rk']+1];
			$returnUser['nxde'] = $nextRank['dedication'];
			$curRank = $allUserRank[$returnUser['rk']];
			$returnUser['cude'] = $curRank['dedication'];
			if($isDotey){
				if(!isset($returnUser['dk'])){
					$returnUser['dk'] = 0;
				}
				$allDoteyRank = $consumeService->getDoteyRankFromRedis();
				$nextDRank = $allDoteyRank[$returnUser['dk']+1];
				$returnUser['nxch'] = $nextDRank['charm'];
				$curDRank = $allDoteyRank[$returnUser['dk']];
				$returnUser['cuch'] = $curDRank['charm'];
				
			}
			$returnUser['is_redis'] = true;
		}
		$returnUser['avatar'] = $this->getUserAvatar($uid,'small',$returnUser);
		return $returnUser;
	}
	
	/**
	 * 返回用户头像上传组件
	 * 
	 * @return PipiFlashUpload
	 */
	public function getAvatarUplaod(){
		if(self::$flashUpload == null){
			self::$flashUpload = new PipiFlashUpload();
		}
		return self::$flashUpload;
	}
	
	/**
	 * 取得用户头像
	 * 
	 * @param int $uid 用户ID，头像尺寸
	 * @param string $size
	 * @param array $updateDesc
	 * @return string
	 */
	public function getUserAvatar($uid,$size = 'small',$updateDesc = array()){
		if(!in_array($size,array('small','middle','big'))){
			return '';
		}
		$upload = $this->getAvatarUplaod();
		if(Yii::app()->params['images_server']['cdn_open']){
			if(empty($updateDesc)){
				$updateDesc = $this->getUserFrontsAttributeByCondition($uid,true,false);
			}
			$timestamp = 0;
			if($updateDesc && isset($updateDesc['atr'])){
				$timestamp = $updateDesc['atr'];
			}
			if( $timestamp > 0 && (time() - $timestamp > Yii::app()->params['images_server']['cdn_time'])){
				return $this->getCdnUrl().$upload->realFolder.'/'.$upload->getScriptUrl($uid,$size);
// 			//userinfo出错，没有atr属性的话，会导致头像不正确，此时不能读取cdn的默认头像
// 			}elseif($timestamp == 0){
// 				return $this->getCdnUrl().'default'.DIR_SEP.'avatar'.DIR_SEP.'avatar_default_'.$size.'.png';
			}
			return $upload->getFileUrl($uid,$size);
		}
		$file = $upload->getFileUrl($uid,$size);
		/**
		$saveFile=$upload->getSaveFile($uid,$size);
		if(!is_file($saveFile)){
			$file = $this->getUploadUrl().'default'.DIR_SEP.'avatar'.DIR_SEP.'avatar_default_'.$size.'.png';
		}
		*/
		return $file;
	}
	/**
	 * 批量获取用户头像 减少userJsonInfo Redis取的数量
	 * @param array $uids
	 * @param unknown_type $size
	 */
	public function getUserAvatarsByUids(array $uids,$size='small'){
		if(empty($uids)){
			return array();
		}
		$keys = '';
		$avatars = array();
		if(Yii::app()->params['images_server']['cdn_open']){
			$userJsonService = new UserJsonInfoService();
			$userJsons = $userJsonService->getUserInfos($uids,false);
			foreach($uids as $uid){
				if(isset($userJsons[$uid])){
					$avatars[$uid] = $this->getUserAvatar($uid,$size,$userJsons[$uid]);
				}else{
					$avatars[$uid] = $this->getUserAvatar($uid,$size);
				}
			}
		}else{
			foreach($uids as $uid){
				$avatars[$uid] = $this->getUserAvatar($uid,$size);
			}
		}
		return $avatars;
	}
	/**
	 * 取得富豪排行榜
	 * 
	 * @param string $type 富豪排行榜类型 今日 本周 本月 超级
	 * @return array
	 */
	public function getUserRichRank($type){
		$keyConfig = Yii::getKeyConfig('redis','other');
		$list = array(
			'today'=>$keyConfig['user_rich_today_rank'],
			'week'=>$keyConfig['user_rich_week_rank'],
			'month'=>$keyConfig['user_rich_month_rank'],
			'super'=>$keyConfig['user_rich_super_rank'],
		);
		$type = !$type || in_array($type,array_keys($list)) ? $type : 'today';
		$redisModel = new OtherRedisModel();
		$rank = $redisModel->getUserRichRank($list[$type]);
		
		$uids = array();
		foreach($rank as $_rank){
			$uids[] = $_rank['uid'];
		}
		$userService = new UserService();
		$avatars = $userService->getUserAvatarsByUids($uids,'small');
		foreach($rank as $key=>$_rank){
			$rank[$key]['avatar'] = $avatars[$_rank['uid']];
			$rank[$key]['number'] = $_rank['num']['n'];
		}
		return $rank;
	}
	
	/**
	 * 取得情谊排行榜
	 * 
	 * @param string $type 富豪排行榜类型 今日 本周 本月 超级
	 * @return array
	 */
	public function getUserFriendlyRank($type){
		$keyConfig = Yii::getKeyConfig('redis','other');
		$list = array(
			'today'=>$keyConfig['user_friendly_today_rank'],
			'week'=>$keyConfig['user_friendly_week_rank'],
			'month'=>$keyConfig['user_friendly_month_rank'],
			'super'=>$keyConfig['user_friendly_super_rank'],
		);
		$type = !$type || in_array($type,array_keys($list)) ? $type : 'today';
		$redisModel = new OtherRedisModel();
		$rank = $redisModel->getUserFriendlyRank($list[$type]);
		
		$uids = array();
		foreach($rank as $_rank){
			$uids[] = $_rank['uid'];
		}
		$userService = new UserService();
		$avatars = $userService->getUserAvatarsByUids($uids,'small');
		foreach($rank as $key=>$_rank){
			$rank[$key]['avatar'] = $avatars[$_rank['uid']];
		}
		return $rank;
	}
	/**
	 * 取得魅力排行榜
	 * 
	 * @param string $type 魅力排行榜类型 今日 本周 本月 超级
	 * @param int $loginUid 登录UID
	 * @return array
	 */
	public function getUserCharmRank($type,$loginUid){
		$keyConfig = Yii::getKeyConfig('redis','other');
		$list = array(
			'today'=>$keyConfig['dotey_charm_today_rank'],
			'week'=>$keyConfig['dotey_charm_week_rank'],
			'month'=>$keyConfig['dotey_charm_month_rank'],
			'super'=>$keyConfig['dotey_charm_super_rank'],
		);
	
		$type = !$type || in_array($type,array_keys($list)) ? $type : 'today';
		$redisModel = new OtherRedisModel();
		$rank = $redisModel->getDoteyCharmRank($list[$type]);
		if($rank){
			$weiboService = new WeiboService();
			$attentions = array();
			if($loginUid){
				$attentions = $weiboService->getDoteyAttentionsByUid($loginUid);
				$attentions = $this->buildDataByIndex($attentions,'uid');
				foreach($rank as &$r){
					$r['is_attention'] = isset($attentions[$r['d_uid']]) ? 1 : 0;
				}
			}else{
				foreach($rank as &$r){
					$r['is_attention'] = 0;
				}
			}
		}
		$uids = array();
		foreach($rank as $_rank){
			$uids[] = $_rank['d_uid'];
		}
		$userService = new UserService();
		$avatars = $userService->getUserAvatarsByUids($uids,'small');
		foreach($rank as $key=>$_rank){
			$rank[$key]['d_avatar'] = $avatars[$_rank['d_uid']];
		}
		return $rank;
	}

	/**
	 * 取得用户类型
	 * 
	 * @author supeng
	 * @return array
	 */
	public function getUserBaseType(){
		return array(
				USER_TYPE_COMMON=>'普通用户',
				USER_TYPE_DOTEY=>'主播',
				USER_TYPE_ADMIN=>'房间总管',
				USER_TYPE_FAMILY=>'家族总管',
			);	
	}
	
	/**
	 * 返回用户状态
	 * @author supeng
	 * @return array
	 */
	public function getUserStatus(){
		return array(
				USER_STATUS_ON=>'正常',
				USER_STATUS_OFF=>'已禁用',
			);
	}
	
	/**
	 * 用户注册来源
	 * 
	 * @author supeng
	 * @return array
	 */
	public function getUserRegSource(){
		return array(
			USER_REG_SOURCE_PIPI=>'本站',
			USER_REG_SOURCE_QQ=>'QQ',
			USER_REG_SOURCE_RENREN=>'人人',
			USER_REG_SOURCE_360=>'360',
			USER_REG_SOURCE_PPTV=>'pptv',
			USER_REG_SOURCE_BAIDU=>'百度',
			USER_REG_SOURCE_SINA=>'新浪',
			USER_REG_SOURCE_SOUSHI_GAME =>'搜视－游戏平台'
		);
	}
	
	public function getUserRegEnSource($source = null){
		$list = array(
			'qq'=> USER_REG_SOURCE_QQ,
			'renren'=>USER_REG_SOURCE_RENREN,
			'safe360'=>USER_REG_SOURCE_360,
			'pptv'=>USER_REG_SOURCE_PPTV,
			'baidu'=>USER_REG_SOURCE_BAIDU,
			'sina'=>USER_REG_SOURCE_SINA,
			'tuli'=>USER_REG_SOURCE_TULI,
			'soushi_game'=>USER_REG_SOURCE_SOUSHI_GAME
		);
		if($source == null){
			return $list;
		}
		return isset($list[$source]) ? $list[$source] : 0;
	}
	/**
	 * 检查用户的叠加身份类型
	 * 
	 * @author supeng
	 * @param unknown_type $range
	 * @return multitype:|multitype:string 
	 */
	public function checkUserType($range,$isName = false){
		$range = intval($range);
		$uTypeNames = $this->getUserBaseType();
		$array = array();
		if($range < 1) return $array;
		if($range & USER_TYPE_COMMON) $array[] = $isName?$uTypeNames[USER_TYPE_COMMON]:USER_TYPE_COMMON;
		if($range & USER_TYPE_DOTEY) $array[] = $isName?$uTypeNames[USER_TYPE_DOTEY]:USER_TYPE_DOTEY;
		if($range & USER_TYPE_ADMIN) $array[] = $isName?$uTypeNames[USER_TYPE_ADMIN]:USER_TYPE_ADMIN;
		if($range & USER_TYPE_FAMILY) $array[] = $isName?$uTypeNames[USER_TYPE_FAMILY]:USER_TYPE_FAMILY;
		return $array;
	}
	/**
	 * 获取用户开放平台注册信息
	 * 
	 * @param string $flatform 开放平台标识
	 * @param string $open_id 开放平台用户标识
	 * @return array
	 */
	public function getUserOauthByOpenFlatform($flatform,$open_id){
		$list = $this->getUserRegEnSource();
		if(!in_array($flatform,array_keys($list))|| empty($open_id)){
			return $this->setError(Yii::t('common','Parameter is empty').'16',0);
		}
		$userOauthModel = UserOAuthModel::model();
		$model = $userOauthModel->getUserOauthByOpenFlatform($flatform,$open_id);
		if($model){
			return $model->attributes;
		}
		return array();
	}
	/**
	 * 用户列表信息搜索
	 * 
	 * @param int $offset
	 * @param int $pageSize
	 * @param array $condition
	 * @return array 
	 */
	public function searchUserList($offset = 0, $pageSize = 20,$condition = array(),$isLimit = true,$countLimt=null){
		$userModel = new UserBasicModel();
		if (!empty($condition['user_type'])){
			$condition['user_type'] = $this->getBitCondition(intval($condition['user_type']),USER_TYPE_COMMON,USER_TYPE_ADMIN);
		}
		
		$data = $userModel->search($offset, $pageSize,$condition,$isLimit,$countLimt);
		if ($data['list']){
			$data['list'] = $this->buildDataByIndex($data['list'], 'uid');
		}
		return $data;
	}
	
	/**
	 * 获取用户被操作记录
	 * 
	 * @author supeng
	 * @param array $condition
	 * @param unknown_type $offset
	 * @param unknown_type $limit
	 * @param unknown_type $isLimit
	 */
	public function getUserOperatedByConditions(Array $condition,$offset=0,$limit=10,$isLimit=true){
		$userOperatedModel = new UserOperatedModel();
		if (!empty($condition['user_type'])){
			$condition['user_type'] = $this->getBitCondition(intval($condition['user_type']),USER_TYPE_COMMON,USER_TYPE_ADMIN);
		}
		
		$data = $userOperatedModel->search($condition ,$offset,$limit,$isLimit);
		if (isset($data['list'])){
			$data['list'] = $this->buildDataByIndex($data['list'], 'uid');
		}
		return $data;
	}
	
	/**
	 * 获取用户状态改变的操作时间及操作理由
	 * 
	 * @author supeng
	 * @param array $uids
	 * @return mix
	 */
	public function getUserOperatedByUids(array $uids,$op_type,$op_value){
		if(!is_array($uids) || !$op_type || !$op_value){
			return $this->setError(Yii::t('common','Parameter is empty').'17',false);
		}
		$condition = array();
		$condition['op_type'] = USER_OPERATED_TYPE_USERSTATUS;
		$condition['uid'] = $uids;
		
		$userOperatedModel = new UserOperatedModel();
		$data = $userOperatedModel->getUserOperatedByUids($uids,$op_type,$op_value);
		return $this->buildDataByIndex($data, 'uid');
	}
	
	/**
	 * 获取登录天数统计
	 * 
	 * @author supeng
	 * @param int $logins
	 * @param array $condition
	 */
	public function getLatelyLogins(Array $condition,$offset = 0,$limit=10,$isLimit=true){
		if(!isset($condition['logins'])){
			return $this->setError(Yii::t('common','Parameter is empty').'18',false);
		}
		
		if(!empty($condition['username']) || !empty($condition['nickname']) || !empty($condition['realname'])){
			$info = $this->searchUserList($offset,$limit,$condition,false);
			if($info['uids']){
				$condition['uids'] = $info['uids'];
			}else{
				return array('count'=>0,'list'=>array());
			}
		}
		
		$userLoginRecordsModel = new UserLoginRecordsModel();
		$data = $userLoginRecordsModel->getLatelyLogins($condition,$offset,$limit,$isLimit);
		if ($data['list']){
			$data['list'] = $this->buildDataByIndex($data['list'], 'uid');
		}
		return $data;
	}
	
	/**
	 * 获取登录明细记录
	 * 
	 * @author supeng
	 * @param array $condition
	 * @param unknown_type $offset
	 * @param unknown_type $limit
	 * @return Ambigous <multitype:, multitype:NULL , multitype:multitype:, multitype:multitype: number mixed Ambigous <NULL, unknown, multitype:unknown Ambigous <unknown, NULL> , mixed, multitype:, multitype:unknown > >
	 */
	public function getLoginDetails(Array $condition = array(),$offset = 0,$limit=10){
		if(!empty($condition['username']) || !empty($condition['nickname']) || !empty($condition['realname'])){
			$info = $this->searchUserList($offset,$limit,$condition,false);
			if($info['uids']){
				$condition['uids'] = $info['uids'];
			}else{
				return array('count'=>0,'list'=>array());
			}
		}
		
		$userLoginRecordsModel = new UserLoginRecordsModel();
		$data = $userLoginRecordsModel->getLoginDetails($condition,$offset,$limit);
		if ($data['list']){
			$data['list'] = $this->arToArray($data['list']);
		}
		return $data;
	}
	
	/**
	 * 获取登录明细记录 去重统计
	 *
	 * @author supeng
	 * @param array $condition
	 * @param unknown_type $offset
	 * @param unknown_type $limit
	 * @return Ambigous <multitype:, multitype:NULL , multitype:multitype:, multitype:multitype: number mixed Ambigous <NULL, unknown, multitype:unknown Ambigous <unknown, NULL> , mixed, multitype:, multitype:unknown > >
	 */
	public function getDuplicateLogins(Array $condition = array(),$offset = 0,$limit=10){
		if(!empty($condition['username']) || !empty($condition['nickname']) || !empty($condition['realname'])){
			$info = $this->searchUserList($offset,$limit,$condition,false);
			if($info['uids']){
				$condition['uids'] = $info['uids'];
			}else{
				return array('count'=>0,'list'=>array());
			}
		}
		
		$userLoginRecordsModel = new UserLoginRecordsModel();
		$data = $userLoginRecordsModel->getDuplicateLogins($condition,$offset,$limit);
		return $data;
	}
	
	/**
	 * 获取最近注册统计
	 * 
	 * @author supeng
	 * @param array $condition
	 * @param unknown_type $offset
	 * @param unknown_type $limit
	 * @param unknown_type $isLimit
	 */
	public function getLatelyRegisters(Array $condition,$offset=0,$limit=10,$isLimit = true){
		$userBasicModel = new UserBasicModel();
		$data = $userBasicModel->getLatelyRegisters($condition,$offset,$limit,$isLimit);
		if($data['list']){
			$data['list'] = $this->buildDataByIndex($this->arToArray($data['list']), 'uid');
		}
		return $data;
	}
	
	/**
	 * @author supeng
	 * @param unknown_type $username
	 */
	public function delUserBaseRedisForUsername($username){
		if($username){
			$userLoginRedisModel = new UserLoginRedisModel();
			return $userLoginRedisModel->delLoginRedisForUsername($username);
		}
		return false;
	}
	
	/**
	 * 根据uid获取用户的第三方平台的数据信息
	 * $author guoshaobo
	 * @param array $uids
	 * @return array
	 */
	public function getUserOuthInfo(array $uids)
	{
		if(!is_array($uids) || empty($uids)){
			return $this->setError(Yii::t('common','Parameter is empty').'19',array());
		}
		$OAuthModel = new UserOAuthModel();
		$res = $OAuthModel->getUserOAuthyUids($uids);
		if($res){
			$_res = array();
			foreach($res as $k=>$v){
				$_tmp = $v->attributes;
				$_res[$_tmp['uid']] = $_tmp; 
			}
			return $_res;
		}
		return array();
	}
}