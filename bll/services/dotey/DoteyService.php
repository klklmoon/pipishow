<?php
/**
 * 主播服务层
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: DoteyService.php 17735 2014-01-21 03:26:30Z hexin $ 
 * @package service
 */
define('SIGN_TYPE_SHOW',1);
define('SIGN_TYPE_FAMILY',2);
define('SIGN_TYPE_CSITE',4);
define('DOTEY_MANAGER_TUTOR', 1);
define('DOTEY_MANAGER_PROXY', 2);
define('DOTEY_MANAGER_STAR', 3);
define('APPLY_STATUS_WAITING', 0);	//新申请等待审核
define('APPLY_STATUS_SUCCESS', 1);	//成功签约
define('APPLY_STATUS_REFUES', 2);	//审核拒绝
define('APPLY_STATUS_FACE', 3);		//已授权，未签约，即面试审核

define('DOTEY_TYPE_DIRECT',1); 	//直营主播
define('DOTEY_TYPE_PROXY',2);	//代理主播
define('DOTEY_TYPE_FULLTIME',3);//全职主播

class DoteyService extends PipiService {
	/**
	 * 
	 * @var PipiFlashUpload
	 */
	private static $flashUpload = null;
	
	public function __construct(PipiController $pipiController = null){
		parent::__construct($pipiController);
		if(self::$flashUpload == null){
			self::$flashUpload = new PipiFlashUpload();
			self::$flashUpload->realFolder = 'dotey';
			self::$flashUpload->filePrefix = 'dotey_'; 
		}
	}
	/**
	 * 存储主播基本信息
	 * 
	 * @param array $doteyBase
	 * @return boolean
	 */
	public function saveUserDoteyBase(array $doteyBase){
		if(($uid = $doteyBase['uid']) <= 0){
			return $this->setError(Yii::t('common','Parameter is empty').'1',false);
		}
		$flag = false;
		$doteyBaseModel = new DoteyBaseModel();
		$orgDoteyBaseModel = $doteyBaseModel->findByPk($uid);
		if($orgDoteyBaseModel){
			$this->appendDoteyData($orgDoteyBaseModel,$doteyBase);
			$this->attachAttribute($orgDoteyBaseModel,$doteyBase);
			if(!$orgDoteyBaseModel->validate()){
				return $this->setNotices($orgDoteyBaseModel->getErrors(),false);
			}
			$flag = $orgDoteyBaseModel->save();
			$doteys = $orgDoteyBaseModel->attributes;
		}else{
			if(isset($doteyBase['update_desc']) && is_array($doteyBase['update_desc'])){
				$doteyBase['update_desc'] = json_encode($doteyBase['update_desc']);
			}
			$this->attachAttribute($doteyBaseModel,$doteyBase);
			if(!$doteyBaseModel->validate()){
				return $this->setNotices($doteyBaseModel->getErrors(),false);
			}
			$flag = $doteyBaseModel->save();
			$doteys = $doteyBaseModel->attributes;
		}
		
		$redisCacheModel = new OtherRedisModel();
		$redisCacheModel->setDoteyInfoToRedisByUid($uid,$doteys);
			
		if ($this->isAdminAccessCtl() && $doteys && $flag){
			$op_desc = '编辑 主播基本信息(UID='.$uid.')';
			$this->saveAdminOpLog($op_desc,$uid);
		}
		return $flag;
	}
	
	
	/**
	 * 获取主播信息
	 * 
	 * @param int $uid
	 * @return array
	 */
	public function getDoteyInfoByUid($uid){
		if($uid <= 0){
			return $this->setError(Yii::t('common','Parameter is empty').'2',false);
		}
		$redisCacheModel = new OtherRedisModel();
		$data = $redisCacheModel->getDoteyInfoFromRedisByUid($uid);
		if(empty($data)){
			$doteyBaseModel =  DoteyBaseModel::model();
			if($model = $doteyBaseModel->findByPk($uid)){
				$data = $model->attributes;
				$redisCacheModel->setDoteyInfoToRedisByUid($uid,$data);
			}
		}
		return $data;
	}
	
	/**
	 * 取得主播信息
	 * 
	 * @param array $uids
	 * @return array
	 */
	public function getDoteyInfoByUids(array $uids){
		if(empty($uids)){
			return $this->setError(Yii::t('common','Parameter is empty').'3',false);
		}
		$redisCacheModel = new OtherRedisModel();
		$doteys = $redisCacheModel->getDoteyInfoFromRedisByUids($uids);
		$unCacheUids = array();
		if(!$doteys){
			$unCacheUids = $uids;
		}else{
			$doteys = $this->buildDataByIndex($doteys,'uid');
			foreach ($uids as $uid){
				if(!in_array($uid,array_keys($doteys))){
					$unCacheUids[] = $uid;
				}
			}
		}
		if($unCacheUids){
			$doteyBaseModel =  DoteyBaseModel::model();
			$models = $doteyBaseModel->getDoteyBaseByUids($unCacheUids);
			foreach($models as $model){
				$doteys[$model->uid] = $model->attributes;
				unset($doteys['']);
				$redisCacheModel->setDoteyInfoToRedisByUid($model->uid,$model->attributes);
			}
		}
		return $doteys;
	}
	/**
	 * 按条件获取主播
	 * 
	 * @param array $condition
	 * @return array
	 */
	public function getDoteysByCondition(array $condition){
		$doteyBaseModel = new DoteyBaseModel();
		$doteyBase = $doteyBaseModel->getDoteysByCondition($condition);
		return $this->buildDataByIndex($this->arToArray($doteyBase),'uid');
	}
	/**
	 * @param array $rangeType
	 */
	public function getDoteyListByDoteyRangeType($rangeType){
		
	}
	
	/**
	 * 取得主播相关图片存储路径
	 * 
	 * @param int $uid
	 * @return string
	 */
	public function getDoteySavePath($uid){
		return self::$flashUpload->getSaveDirPath($uid);
	}
	
	/**
	 * 获取主播图片存储路径
	 * @param int $uid
	 * @param string $size
	 * @param string $type 目前为display
	 * @return string
	 */
	public function getDoteySaveFile($uid,$size,$type = 'display'){
		return self::$flashUpload->getSaveFile($uid,$size,$type);
	}
	/**
	 * 主播大图
	 * @param int $uid
	 * @param string $size
	 * @param string $type 目前为display
	 * @param array $updateDesc 图片更新时间，CDN更新时间
	 * @return string
	 */
	public function getDoteyUpload($uid,$size,$type = 'display',$updateDesc = array()){
		if(!in_array($size,array('small','middle','big'))){
			return '';
		}
		if(Yii::app()->params['images_server']['cdn_open']){
			if(empty($updateDesc)){
				$dotey = $this->getDoteyInfoByUid($uid);
				$updateDesc = $dotey['update_desc'];
			}
			$key = $type.'_'.$size;
			$timestamp = 0;
			if($updateDesc && isset($updateDesc[$key])){
				$timestamp = $updateDesc[$key];
			}
			if( $timestamp > 0  && (time() - $timestamp > Yii::app()->params['images_server']['cdn_time'])){
				return $this->getCdnUrl().self::$flashUpload->realFolder.'/'.self::$flashUpload->getScriptUrl($uid,$size,$type);
			}elseif($timestamp == 0){
				return $this->getCdnUrl().'default'.DIR_SEP.'dotey'.DIR_SEP.'dotey_'.$type.'_default_'.$size.'.png';
			}
			return self::$flashUpload->getFileUrl($uid,$size,$type);
		}
		$file = self::$flashUpload->getFileUrl($uid,$size,$type);
		/*
		$saveFile=self::$flashUpload->getSaveFile($uid,$size,$type);
		if(!is_file($saveFile)){
			$file = $this->getUploadUrl().'default'.DIR_SEP.'dotey'.DIR_SEP.'dotey_'.$type.'_default_'.$size.'.png';
		}*/
		return $file;
	}
	
	/**
	 * 秀场后台获取主播图片
	 * 
	 * @author supeng
	 * @param unknown_type $uid
	 * @param unknown_type $size
	 * @param unknown_type $type
	 * @return string
	 */
	public function getShowAdminDoteyUpload($uid,$size,$type = 'display'){
		return $this->getShowAdminUrl().self::$flashUpload->realFolder.'/'.self::$flashUpload->getFile($uid,$size,$type);
	}
	
	/**
	 * 在主播的原始数据上做加减
	 * 
	 * @param DoteyBaseModel $orgActiveRecord 数据库已存在的记录
	 * @param array $newArray 新更新的记录
	 * @param int $plus 0表示增 1表示减
	 * @return null
	 */
	protected function appendDoteyData(DoteyBaseModel   $orgActiveRecord,array &$newArray,$plus = 0){
		if(!$newArray){
			return $this->setError(Yii::t('common','Parameter is empty').'4',null);
		}

		if(isset($newArray['update_desc']) && is_array($newArray['update_desc'])){
			$orgActiveRecord->update_desc = json_decode($orgActiveRecord->update_desc,true);
			if(is_array($orgActiveRecord->update_desc)){
				$newArray['update_desc'] = array_merge($orgActiveRecord->update_desc,$newArray['update_desc']);
			}
			$newArray['update_desc'] = json_encode($newArray['update_desc']);
		}
	}
	
	/**
	 * 主播搜索
	 *
	 * @author supeng
	 * @param array $condition
	 * @param unknown_type $offset
	 * @param unknown_type $pageSize
	 * @return Ambigous <multitype:, multitype:NULL , Ambigous, multitype:multitype: number Ambigous <NULL, unknown, multitype:unknown Ambigous <unknown, NULL> , mixed, multitype:, multitype:unknown > Ambigous <string, unknown, mixed> >
	 */
	public function searchDoteyBase(Array $condition = array(),$offset=0,$pageSize=10,$islimit = true){
		$doteyBaseModel = DoteyBaseModel::model();
		if (isset($condition['sign_type'])){
			$condition['sign_type'] = $this->getBitCondition(intval($condition['sign_type']), SIGN_TYPE_SHOW,SIGN_TYPE_CSITE);
		}
		$result = $doteyBaseModel->searchDoteyBase($condition,$offset,$pageSize,$islimit);
		if (!empty($result['list'])){
			$result['list'] = $this->buildDataByIndex($result['list'], 'uid');
		}
		return $result;
	}
	
	/**
	 * @author supeng
	 * @param unknown_type $offset
	 * @param unknown_type $pageSize
	 * @param array $condition
	 * @param unknown_type $islimit
	 * @return Ambigous <multitype:, multitype:unknown Ambigous <multitype:unknown , unknown> , mixed>
	 */
	public function searchDoteyList($offset=0,$pageSize=10,Array $condition = array(),$islimit = true){
		$doteyBaseModel = DoteyBaseModel::model();
		if (isset($condition['sign_type'])){
			$condition['sign_type'] = $this->getBitCondition(intval($condition['sign_type']), SIGN_TYPE_SHOW,SIGN_TYPE_CSITE);
		}
		$result = $doteyBaseModel->getDoteyList($offset,$pageSize,$condition,$islimit);
		if (!empty($result['list'])){
			$result['list'] = $this->buildDataByIndex($result['list'], 'uid');
		}
		return $result;
	}
	
	/**
	 * 获取主播状态
	 * @return multitype:string 
	 */
	public function getDoteyBaseStatus($isFilter = false){
		if ($isFilter){
			return array(
				APPLY_STATUS_SUCCESS=>'已签约',
				APPLY_STATUS_FACE=>'已授权/未签约',
			);
		}else{
			return array(
				APPLY_STATUS_WAITING=>'待处理',
				APPLY_STATUS_SUCCESS=>'已签约',
				APPLY_STATUS_REFUES=>'已拒绝',
				APPLY_STATUS_FACE=>'已授权/未签约',
			);
		}
	}
	
	/**
	 * 主播签约类型
	 * @return multitype:string 
	 */
	public function getDoteySignType(){
		return array(
				SIGN_TYPE_SHOW=>'秀场',
				SIGN_TYPE_FAMILY=>'家族',
				SIGN_TYPE_CSITE=>'C站'
			);
	}
	
	/**
	 * 获取签约平台类型叠加身份
	 * 
	 * @author supeng
	 * @param unknown_type $range
	 * @param unknown_type $isName
	 * @return multitype:|multitype:Ambigous <string, unknown> 
	 */
	public function checkSignType($range,$isName = false){
		$range = intval($range);
		$signTypes = $this->getDoteySignType();
		$array = array();
		if($range < 1) return $array;
		if($range & SIGN_TYPE_SHOW) $array[] = $isName?$signTypes[SIGN_TYPE_SHOW]:SIGN_TYPE_SHOW;
		if($range & SIGN_TYPE_FAMILY) $array[] = $isName?$signTypes[SIGN_TYPE_FAMILY]:SIGN_TYPE_FAMILY;
		if($range & SIGN_TYPE_CSITE) $array[] = $isName?$signTypes[SIGN_TYPE_CSITE]:SIGN_TYPE_CSITE;
		return $array;
	}
	
	/**
	 * 保存代理或导师信息
	 * @author hexin
	 * @param array $data
	 * @return int
	 */
	public function saveDoteyProxy(array $data){
		if(empty($data['uid']) || empty($data['type'])){
			return $this->setError(Yii::t('common','Parameter is empty').'5', 0);
		}
		$proxyModel = new DoteyProxyModel();
		if(isset($data['uid']) && $e_proxyModel = $proxyModel->findByUnique($data['uid'],$data['type'])){
			$proxyModel = $e_proxyModel;
		}else{
			if(empty($data['type'])){
				return $this->setError(Yii::t('common','Parameter is empty').'6', -2);
			}
			$data['query_allow'] = isset($data['query_allow'])?$data['query_allow']:1;
			$data['is_display'] = isset($data['is_display'])?$data['is_display']:1;
			$data['create_time'] = time();
		}
		$this->attachAttribute($proxyModel, $data);
		if(!$proxyModel->validate()){
			return $this->setNotice($proxyModel->getErrors(), -2);
		}
		$proxyModel->save();
		$flag = $proxyModel->getPrimaryKey();
		if ($flag && $this->isAdminAccessCtl()){
			$this->saveAdminOpLog('编辑 主播代理或导师信息(UID='.$flag.')');
		}
		return $flag;
	}
	
	/**
	 * 保存主播申请信息
	 * @author hexin
	 * @param array $data
	 * @return int
	 */
	public function saveDoteyApply(array $data){
		if(empty($data['uid']) || empty($data['type'])){
			return $this->setError(Yii::t('common','Parameter is empty').'7', 0);
		}
		$applyModel = new DoteyApplyModel();
		if(isset($data['uid']) && $e_applyModel = $applyModel->findByUnique($data['uid'],$data['type'])){
			$applyModel = $e_applyModel;
		}else{
			$data['create_time'] = time();
		}
		$this->attachAttribute($applyModel, $data);
		if(!$applyModel->validate()){
			return $this->setNotice($applyModel->getErrors(), -2);
		}
		$applyModel->save();
		$flag = $applyModel->getPrimaryKey();
		if ($flag && $this->isAdminAccessCtl()){
			if(isset($data['uid'])){
				$op_desc = '编辑主播申请信息(UID='.$flag.')';
			}else{
				$op_desc = '新增主播申请信息(UID='.$flag.')';
			}
			$this->saveAdminOpLog($op_desc,$flag);
		}
		return $flag;
	}
	
	/**
	 * 获取代理或导师数据
	 * @author hexin
	 * @param int $type 1为导师，2为代理 3为星探
	 * @param boolean $hidden 导师是否隐藏或代理是否允许查询
	 * @param boolean $extend 是否需要用户扩展信息
	 * @return array
	 */
	public function getProxyOrTutorList($type = 0, $hidden = false, $extend = false){
		$proxy = DoteyProxyModel::model()->getAll($type, $hidden);
		$data = $this->arToArray($proxy);
		$uids = array_keys($this->buildDataByIndex($data, 'uid'));
		
		$userService = new UserService();
		$users = $userService->getUserBasicByUids($uids);
		
		if($extend){
			$extends = $userService->getUserExtendByUids($uids);
		}
		
		$options = array();
		foreach($proxy as $p){
			$option = $p->getAttributes();
			$option['user'] = isset($users[$p->uid]) ? $users[$p->uid] : array();
			if($extend){
				$option['extend'] = isset($extends[$p->uid]) ? $extends[$p->uid] : array();
			}
			$options[] = $option;
		}
		return $options;
	}
	
	/**
	 * 获取经理或代理管辖的主播总数
	 * 
	 * @author supeng
	 * @param int $type
	 * @param int|array $uid
	 */
	public function getProxyOrTutorManagerTotal($type,Array $uids){
		if (empty($type) || !is_array($uids)){
			return $this->setError(Yii::t('common', 'Parameter is empty').'8',false);
		}
		
		$doteyModel = new DoteyBaseModel();
		return $doteyModel->getProxyOrTutorManagerTotal($type,$uids);
	}
		
	/**
	 * 更新代理人信息
	 * @author hexin
	 * @param int $uid
	 * @param array $data
	 * @return boolean
	 */
	public function updateProxy($uid, array $data){
		if(empty($uid) || empty($data['type'])){
			$this->setError(Yii::t('common','Parameter is empty').'9', 0);
		}
		$flag = 0;
		
		$userService = new UserService();
		if(!empty($data['realname']) || !empty($data['nickname'])){
			$user = array();
			$user['uid'] = $uid;
			!empty($data['realname']) && $user['realname'] = $data['realname'];
			!empty($data['nickname']) && $user['nickname'] = $data['nickname'];
			$flag = $userService->saveUserBasic($user);
			if(!$flag){
				return $userService->getNotice();
			}
		}
		
		$extend = array();
		isset($data['qq']) && $extend['qq'] = $data['qq'];
		isset($data['mobile']) && $extend['mobile'] = $data['mobile'];
		isset($data['bank']) && $extend['bank'] = $data['bank'];
		isset($data['bank_account']) && $extend['bank_account'] = $data['bank_account'];
		isset($data['id_card']) && $extend['id_card'] = $data['id_card'];
		if(!empty($extend)){
			$extend['uid'] = $uid;
			$flag = $userService -> saveUserExtend($extend);
			if(!$flag){
				return $userService->getNotice();
			}
		}
		
		$proxy = array();
		$proxy['type'] = $data['type'];
		isset($data['agency']) && $proxy['agency'] = $data['agency'];
		isset($data['company']) && $proxy['company'] = $data['company'];
		isset($data['id_card_pic']) && $proxy['id_card_pic'] = $data['id_card_pic'];
		isset($data['business_license']) && $proxy['business_license'] = $data['business_license'];
		isset($data['note']) && $proxy['note'] = $data['note'];
		isset($data['query_allow']) && $proxy['query_allow'] = $data['query_allow'];
		isset($data['is_display']) && $proxy['is_display'] = $data['is_display'];
		if(!empty($proxy)){
			$proxy['uid'] = $uid;
			$flag = $this->saveDoteyProxy($proxy);
		}
		return $flag == 0 ? false : true;
	}
	
	/**
	 * 主播申请
	 * @author hexin
	 * @param int $uid
	 * @param array $data = array(realname, gender, mobile, qq, id_card, bank_user, bank, bank_account,
	 * 						has_experience[, live_address][, proxy_uid], tutor_uid)
	 * 						基本都是必填项，方括号里的是可选项
	 * @return int
	 */
	public function doteyApply($uid, array $data){
		if(empty($uid)){
			$this->setError(Yii::t('common','Parameter is empty').'10', 0);
		}
		
		$user['uid'] = $uid;
		$user['realname'] = $data['realname'];
		$userService = new UserService();
		$userService->saveUserBasic($user);
		
		$extend['uid'] = $uid;
		$extend['gender'] = $data['gender'];
		$extend['mobile'] = $data['mobile'];
		$extend['qq'] = $data['qq'];
		$extend['id_card'] = $data['id_card'];
		if(isset($data['bank_user'])){
			$extend['bank_user'] = $data['bank_user'];
		}
		if(isset($data['bank'])){
			$extend['bank'] = $data['bank'];
		}
		if(isset($data['bank_account'])){
			$extend['bank_account'] = $data['bank_account'];
		}
		$userService->saveUserExtend($extend);
		
		$apply['uid'] = $uid;
		if(isset($data['has_experience'])){
			$apply['has_experience'] = $data['has_experience'];
		}
		if(isset($data['status'])){
			$apply['status'] = $data['status'];
		}
		$apply['type'] = isset($data['type'])?$data['type']:4;
		isset($data['live_address']) && $apply['live_address'] = $data['live_address'];
		$flag = $this->saveDoteyApply($apply);
		if($flag && $apply['type'] == 4){
			$dotey['uid'] = $uid;
			$dotey['sign_type'] = SIGN_TYPE_SHOW;
			$dotey['dotey_type'] = !empty($data['proxy_uid']) ? DOTEY_TYPE_PROXY : DOTEY_TYPE_DIRECT;
			$dotey['status'] = 0;
			isset($data['proxy_uid']) && $dotey['proxy_uid'] = $data['proxy_uid'];
			isset($data['finder_uid']) && $dotey['finder_uid'] = $data['finder_uid'];
			$dotey['tutor_uid'] = $data['tutor_uid'];
			$dotey['create_time'] = time();
			$this->saveUserDoteyBase($dotey);
		}
		return $flag;
	}
	
	/**
	 * 获取申请列表(非主播)
	 * @author hexin
	 * @param int $page
	 * @param int $pagesize
	 * @param array $search
	 * @return array = array(count, list = array)
	 */
	public function applyList($page = 1, $pagesize = 20, array $search = array(),$isLimit=true){
		$page = intval($page) < 1 ? 1 : intval($page);
		$list = DoteyApplyModel::model()->getApplyList($page, $pagesize, $search,$isLimit);
		if(isset($list['list'])){
			$list['list'] = $this->buildDataByIndex($list['list'], 'uid');
		}
		return $list;
	}
	
	/**
	 * 获取主播申请列表
	 * @author hexin
	 * @param int $page
	 * @param int $pagesize
	 * @param array $search
	 * @return array = array(count, list = array)
	 */
	public function getDoteyApplyList($page = 1, $pagesize = 20, array $search = array(),$isLimit=true){
		$page = intval($page) < 1 ? 1 : intval($page);
		$list = DoteyApplyModel::model()->getDoteyApplyList($page, $pagesize, $search,$isLimit);
		if(isset($list['list'])){
			$list['list'] = $this->buildDataByIndex($list['list'], 'uid');
		}
		return $list;
	}
	
	/**
	 * 获取导师信息
	 * @author hexin
	 * @param int $uid
	 * @return array
	 */
	public function getTutor($uid){
		return DoteyProxyModel::model()->getTutor($uid);
	}
	
	/**
	 * 获取代理信息
	 * @author hexin
	 * @param int $uid
	 * @return array
	 */
	public function getProxy($uid,$type = DOTEY_MANAGER_PROXY){
		return DoteyProxyModel::model()->getProxy($uid,$type);
	}
	
	/**
	 * 获取星探信息
	 * @author hexin
	 * @param int $uid
	 * @return array
	 */
	public function getFinder($uid,$type = DOTEY_MANAGER_STAR){
		return DoteyProxyModel::model()->getProxy($uid,$type);
	}
	
	/**
	 * 是否有主播经验
	 * 
	 * @author supeng
	 * @return multitype:string 
	 */
	public function getWhetherDotey(){
		return array(
				0 => '无',
				1 => '有'
			);
	}
	
	/**
	 * 获取主播性别项
	 * 
	 * @author supeng
	 * @return multitype:string 
	 */
	public function getDoteyGender(){
		return array(
				0 => '保密',
				1 => '男',
				2 => '女'
			);
	}
	
	/**
	 * 获取主播类型
	 * 
	 * @author supeng
	 * @return multitype:string 
	 */
	public function getDoteyType(){
		return array(
				DOTEY_TYPE_DIRECT => '直营主播',
				DOTEY_TYPE_PROXY => '代理主播',
				DOTEY_TYPE_FULLTIME => '全职主播',
			);
	}
	
	/**
	 * 主播特长数据
	 * @author hexin
	 * @return array
	 */
	public function getDoteySkill(){
		return array('聊天','演唱','主持','舞蹈','乐器','其它');
	}
	
	/**
	 * 获取主播的申请信息
	 * @author hexin
	 * @param int $uid
	 * @return array
	 */
	public function getApplyInfo($uid,$type=4){
		$apply = DoteyApplyModel::model()->getApplyInfos(array($uid),$type);
		$apply = $this->buildDataByIndex($apply, 'uid');
		if($apply){
			$apply = $apply[$uid];
			$apply['status'] = $apply['status'] === null ? -1 : $apply['status'];
		}
		return $apply;
	}
	
	public function getApplyDoteyInfo($uid){
		$apply = DoteyApplyModel::model()->getApplyDoteyInfos(array($uid));
		$apply = $this->buildDataByIndex($apply, 'uid');
		if($apply){
			$apply = $apply[$uid];
			$apply['status'] = $apply['status'] === null ? -1 : $apply['status'];
		}
		return $apply;
	}
	
	/**
	 * 批量获取主播的申请信息
	 * @author hexin
	 * @param int $uid
	 * @return array
	 */
	public function getApplyInfos(array $uids,$type=4){
		if(empty($uids)) return array();
		$applys = DoteyApplyModel::model()->getApplyInfos($uids,$type);
		$applys = $this->buildDataByIndex($applys, 'uid');
		foreach($applys as &$a){
			$a['status'] = $a['status'] === null ? -1 : $a['status'];
		}
		return $applys;
	}
	
	public function getApplyDoteyInfos(array $uids){
		if(empty($uids)) return array();
		$applys = DoteyApplyModel::model()->getApplyDoteyInfos($uids);
		$applys = $this->buildDataByIndex($applys, 'uid');
		foreach($applys as &$a){
			$a['status'] = $a['status'] === null ? -1 : $a['status'];
		}
		return $applys;
	}
	
	/**
	 * 删除主播申请信息
	 * @author hexin
	 * @param int $uid
	 * @param int
	 */
	public function deleteApplyInfo($uid){
		$dotey = $this->getDoteyInfoByUid($uid);
		if($dotey['status'] != APPLY_STATUS_REFUES){
			return $this->setError(Yii::t('dotey','The dotey can not be delete'), 0);
		}
		
		//DoteyApplyModel::model()->deleteApply($uid);
		
		$redisCacheModel = new OtherRedisModel();
		$redisCacheModel -> deleteDoteyInfoByUids($uid);
		$flag = DoteyBaseModel::model()->deleteByPk($uid);
		if ($flag && $this->isAdminAccessCtl()){
			$op_desc = '删除 主播申请(UID='.$uid.')';
			$this->saveAdminOpLog($op_desc,$uid);
		}
		return $flag;
	}
	
	/**
	 * 删除代理人或导师
	 * @author hexin
	 * @param int $uid
	 * @return int
	 */
	public function deleteProxy($uid){
		return DoteyProxyModel::model()->deleteProxy($uid);
	}
	
	/**
	 * 根据条件获取主播IDS
	 * 
	 * @author supeng
	 * @param array $condition
	 */
	public function searchDoteyUidsByCodition(Array $condition = array()){
		if (empty($condition)){
			return true;
		}
		
		$ubaseCondition = array();
		if (!empty($condition['username'])){
			$ubaseCondition['username'] = $condition['username'];
		}
		
		if (!empty($condition['nickname'])){
			$ubaseCondition['nickname'] = $condition['nickname'];
		}
		
		if (!empty($condition['realname'])){
			$ubaseCondition['realname'] = $condition['realname'];
		}
		
		if (!empty($condition['uid'])){
			$ubaseCondition['uid'] = $condition['uid'];
		}
		
		//是否有主播名
		if (!empty($ubaseCondition)){
			$doteyBase = $this->searchDoteyBase($ubaseCondition,null,null,false);
			if($doteyBase['list']){
				return array_keys($doteyBase['list']);
			}else{
				return false;
			}
		}
		return true;
	}
	
	/**
	 * 根据频道地区信息来查询相关主播明细
	 * 
	 * @author supeng
	 * @param unknown_type $provice
	 * @param unknown_type $city
	 * @return mix
	 */
	public function searchDoteyArea($province,$city){
		if (empty($province) || empty($city)){
			return $this->setError(Yii::t('common', 'Parameter is empty').'11',false);
		}
		$doteyBaseModel = new DoteyBaseModel();
		return $this->buildDataByIndex($doteyBaseModel->searchDoteyArea($province,$city), 'uid');
	}
	
	/**
	 * 删除主播的doteyInfo信息
	 * 
	 * @author supeng
	 * @param unknown_type $uid
	 */
	public function delDoteyInfoFormRedis($uid){
		$redisCacheModel = new OtherRedisModel();
		return $redisCacheModel -> deleteDoteyInfoByUids($uid);
	}
	
	/**
	 * 获取魅力提现公式
	 * 
	 * @author supeng
	 * @param unknown_type $uid
	 * @return unknown|number
	 */
	public function getDoteyCashConfig($uid){
		$defaultScale = 0.007;
		$webConfigSer = new WebConfigService();
		$doteyPayKeys = $webConfigSer->getDoteyPayKey();
		$doteyInfo = $this->getDoteyInfoByUid($uid);
		$scaleKey = $doteyPayKeys[$doteyInfo['dotey_type']]['scale'];
		$rs = $webConfigSer->getWebConfig($scaleKey);
		if($rs){
			if (is_array($uid)){
				$scales = array();
				foreach ($uid as $v){
					$rs_tmp = isset($rs['c_value'][$uid])?$rs['c_value'][$uid]:(isset($rs['c_value'][0])?$rs['c_value'][0]:null);
					$scales[$v] = $rs_tmp?$rs_tmp['scale']:$defaultScale;
				}
				return $scales;
			}else{
				$rs_tmp = isset($rs['c_value'][$uid])?$rs['c_value'][$uid]:(isset($rs['c_value'][0])?$rs['c_value'][0]:null);
				return $rs_tmp?$rs_tmp['scale']:$defaultScale;
			}
		}
		return $defaultScale;
	}
	
	/**
	 * 返回一堆uid中真实是主播的信息
	 * @param array $uids
	 * @return array
	 */
	public function getDoteysInUids(array $uids){
		if(empty($uids)) return array();
		$return = DoteyBaseModel::model()->getDoteysInUids($uids);
		return $this->buildDataByIndex($return, 'uid');
	}
}

?>