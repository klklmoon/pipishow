<?php
/**
 * 家族服务层
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: 2013-8-6 下午3:59:59 hexin $ 
 * @package
 */
define('FAMILY_ROLE_OWNER', 51); //家族长
define('FAMILY_ROLE_ELDER', 52); //家族长老
define('FAMILY_ROLE_ADMINISTRATOR', 53); //家族管理员
define('FAMILY_ROLE_DOTEY', 54); //家族主播

define('FAMILY_STATUS_UNPREPARE', -2); //过时未筹备完成，家族集散
define('FAMILY_STATUS_REFUSE', -1); //后台审核拒绝
define('FAMILY_STATUS_WAIT', 0); //待审核
define('FAMILY_STATUS_SUCCESS', 1); //审核成功
define('FAMILY_STATUS_PREPARE', 2); //筹备成功需后台审核

define('FAMILY_MEMBER_STATUS_WAIT', 0); //成员申请待审核
define('FAMILY_MEMBER_STATUS_SUCCESS', 1); //成员申请成功
define('FAMILY_MEMBER_STATUS_REFUSE', -1); //成员申请拒绝

class FamilyService extends PipiService {
	const DEFAULT_CONFIG_KEY = 'FAMILY_GLOBAL_CONF';
	
	private static $instance;
	public static $web;
	public $multiple = 1;
	
	/**
	 * 返回FamilyService对象的单例
	 * @return FamilyService
	 */
	public static function getInstance(){
		if(self::$instance == null){
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	/**
	 * 返回全局家族功能是否隐藏
	 * @return boolean
	 */
	public static function familyEnable(){
		if(!is_array(self::$web)){
			self::$web = self::getSetting();
		}
		return isset(self::$web['global_enable']) ? (boolean) self::$web['global_enable'] : false;
	}

	/**
	 * 返回家族的url地址
	 * @param int $family_id
	 * @param string $route
	 * @param array $params
	 * @return string
	 */
	public function getFamilyUrl($family_id, $route = '', array $params = array()){

 		//if(empty($route)) $route = 'family/home&family_id=';
 		//else $route .= '&family_id=';
 		//return '/index.php?r='.$route.$family_id.(!empty($params) ? '&'.http_build_query($params) : '');

		$return = '/f'.$family_id;
		if(!empty($route) || !empty($params)){
			$return .= '?'.(!empty($route) ? "r=".$route : '');
			$return .= (substr($return, -1, 1) != '?' && !empty($params) ? '&' : '');
			$return .= (!empty($params) ? http_build_query($params) : '');
		}
		return $return;
	}
	
	/**
	 * 查询家族分页列表
	 * @param string $order
	 * @param string $desc
	 * @param int $page
	 * @param int $pageSize
	 * @return array
	 */
	public function getFamilyList($order = 'member', $desc = 'desc', $page = 1, $pageSize = 8){
		$page = intval($page) < 1 ? 1 : intval($page);
		//根据家族贡献排行
		if($order == 'dedication'){
			$list = FamilyModel::model()->getFamilyList(array(), ($page-1)*$pageSize, $pageSize, 'e.dedication_total '.$desc);
		//根据家族族徽成员人数排行
		}elseif($order == 'medal'){
			$list = FamilyModel::model()->getFamilyList(array(), ($page-1)*$pageSize, $pageSize, 'm.medal '.$desc);
		//根据家族成员人数排行
		}elseif($order == 'member'){
			$list = FamilyModel::model()->getFamilyList(array(), ($page-1)*$pageSize, $pageSize, 'm.member '.$desc);
		//根据家族主播人数排行
		}elseif($order == 'dotey'){
			$list = FamilyModel::model()->getFamilyList(array(), ($page-1)*$pageSize, $pageSize, 'm.dotey '.$desc);
		//根据家族成立时间排行
		}elseif($order == 'time'){
			$list = FamilyModel::model()->getFamilyList(array(), ($page-1)*$pageSize, $pageSize, 'f.id '.$desc);
		}
		if(empty($list)) return array();
		
		$uids = array_keys($this->buildDataByIndex($list['list'], 'uid'));
		$users = UserJsonInfoService::getInstance()->getUserInfos($uids, false);
		$fids = array_keys($this->buildDataByIndex($list['list'], 'id'));
		$familys = $this->getFamilyIds($fids);
		foreach($list['list'] as &$l){
			$l['nickname']	= $users[$l['uid']]['nk'];
			$l['members']	= $familys[$l['id']]['member_total'];
			$l['doteys']	= $familys[$l['id']]['dotey_total'];
		}
		return $list;
	}
	
	/**
	 * 获取我的家族信息
	 * @param int $uid
	 * @return array
	 */
	public function getMyFamily($uid){
		$user = UserJsonInfoService::getInstance()->getUserInfo($uid, false);
		$my = isset($user['fp']) ? $user['fp'] : null;
		if(!isset($my)){
			$my = $this->saveMyFamily($uid);
		}
		$fids = array();
		if(!empty($my['create'])) $fids[] = $my['create'];
		if(empty($my['join'])) $my['join'] = array();
		$fids = array_merge($fids, $my['join']);
		$familys = $this->getFamilyIds($fids);
		$return = array('create' => array(), 'join' => array());
		$return['create'] = isset($familys[$my['create']]) ? $familys[$my['create']] : array();
		foreach($my['join'] as $join){
			if(isset($familys[$join])){
				$return['join'][] = $familys[$join];
			}
		}
		return $return;
	}
	
	/**
	 * 保存我的家族信息，在uid与family_id关联关系变化的情况下需要保存
	 * @param int $uid
	 * @return array
	 */
	public function saveMyFamily($uid){
		$family = FamilyMemberModel::model()->getMyFamily($uid);
		$user = UserJsonInfoService::getInstance()->getUserInfo($uid, false);
		$return = array();
		if(isset($user['fp'])) $return = $user['fp'];
		$return['create'] = 0;
		$return['join'] = array();
		foreach($family as $a){
			if($a['owner'] == $a['uid']){
				$return['create'] = $a['family_id'];
			}else{
				$return['join'][] = $a['family_id'];
			}
		}
		UserJsonInfoService::getInstance()->setUserInfo($uid, array('fp' => $return));
		$zmq = $this->getZmq();
		$zmq->sendZmqMsg(609,array('type'=>'update_json','uid'=>$uid,'json_info'=>array('fp' => $return)));
		return $return;
	}
	
	/**
	 * 保存我的族徽
	 * @param int $uid
	 * @param array $family
	 * @param int $role_id
	 */
	public function saveMyMedal($uid, $family, $role_id = 0, $unload = false){
		if(intval($uid) < 0) return false;
		if(!isset($family['id']) || !isset($family['level'])) return false;
		$user = UserJsonInfoService::getInstance()->getUserInfo($uid, false);
		$return = isset($user['fp']) ? $user['fp'] : array();
		if(isset($family['uid']) && $uid == $family['uid']) $role_type = 3;
		elseif($role_id == FAMILY_ROLE_OWNER) $role_type == 3;
		elseif(in_array($role_id, array(FAMILY_ROLE_ELDER, FAMILY_ROLE_ADMINISTRATOR))) $role_type = 2;
		else $role_type = 1;
		if(isset($family['sign']) && $family['sign'] == 1) $family['level'] = 0;
		if($unload){
			$return['medal'] = '';
		}else{
			$return['medal'] = '/images/family/'.$family['id'].'/medal_'.$family['level'].$role_type.'.jpg';
		}
		UserJsonInfoService::getInstance()->setUserInfo($uid, array('fp' => $return));
		$zmq = $this->getZmq();
		$zmq->sendZmqMsg(609,array('type'=>'update_json','uid'=>$uid,'json_info'=>array('fp' => $return)));
		return true;
	}
	
	/**
	 * 查询家族信息
	 * @param int $family_id
	 * @return array
	 */
	public function getFamily($family_id){
 		$family = OtherRedisModel::getInstance()->getFamily($family_id);
		if(!$family){
			$family = FamilyModel::model()->findByPk($family_id);
			if(!empty($family)){
				$family = $family->getAttributes();
				$family = $this->saveFamilyCache($family_id, $family);
			}
		}
		return $family;
	}
	
	/**
	 * 批量获取家族信息
	 * @param array $family_ids
	 * @return array
	 */
	public function getFamilyIds(array $family_ids){
		$family = OtherRedisModel::getInstance()->getFamilyIds($family_ids);
		$unCache = array_diff($family_ids, array_keys($family));
		if(!empty($unCache)){
			foreach($unCache as $id){
				$family[$id] = FamilyModel::model()->findByPk($id);
				if(!empty($family[$id])){
					$family[$id] = $family[$id]->getAttributes();
					$family[$id] = $this->saveFamilyCache($id, $family[$id]);
				}
			}
		}
		return $family;
	}
	
	/**
	 * 保存family信息
	 * @param array $data
	 * @return number
	 */
	public function saveFamily(array $data){
		if(isset($data['id']) && intval($data['id']) < 1 || (!isset($data['id']) && (empty($data['uid']) || empty($data['name'])))){
			return $this->setError(Yii::t('common','Parameter is empty'), 0);
		}
		$model = new FamilyModel();
		if(isset($data['id'])){
			$model = $model->findByPk($data['id']);
			if(empty($model)) return $this->setError(Yii::t('common','Data not exists'), 0);
			$data['update_time'] = time();
			if(isset($data['cover']) && $model->cover && $model->cover != $data['cover']){
				@unlink(IMAGES_PATH.'family'.DIR_SEP.$model->id.DIR_SEP.$model->cover);
			}
		}else{
			$data['status'] = FAMILY_STATUS_WAIT;
			$data['hidden'] = 0;
			$data['level'] = 1;
			$data['forbidden'] = 0;
			if(!isset($data['sign'])){
				$data['sign'] = 0;
			}
			$data['create_time'] = time();
		}
		$this->attachAttribute($model, $data);
		if(!$model->validate()){
			return $this->setNotices($model->getErrors(), 0);
		}
		$model->save();
		
		$this->saveFamilyCache($model->getPrimaryKey(), $model->getAttributes());
		
		return $model->getPrimaryKey();
	}
	
	/**
	 * 保存家族信息到缓存
	 * @param int $family_id
	 * @param array $family
	 * @return array
	 */
	private function saveFamilyCache($family_id, array $family){
		if(!empty($family)){
			$members = $this->getMembers($family_id);
			$family['member_total'] = count($members);
			$family['elder_total'] = 0;
			$family['admin_total'] = 0;
			$family['dotey_total'] = 0;
			$family['medal_total'] = 0;
			if($family['member_total'] >= 1){
				foreach($members as $m){
					if($m['role_id'] == FAMILY_ROLE_ELDER) $family['elder_total']++;
					elseif($m['role_id'] == FAMILY_ROLE_ADMINISTRATOR) $family['admin_total']++;
					if($m['medal_enable'] == 1) $family['medal_total']++;
					if($m['family_dotey']) $family['dotey_total']++;
				}
			}
			OtherRedisModel::getInstance()->setFamily($family_id, $family);
		}
		return $family;
	}
	
	/**
	 * 保存家族扩展信息
	 * @param array $data
	 * @return int
	 */
	public function saveFamilyExtend(array $data){
		if(isset($data['family_id']) && intval($data['family_id']) < 1){
			return $this->setError(Yii::t('common','Parameter is empty'), 0);
		}
		$model = new FamilyExtendModel();
		if(isset($data['family_id'])){
			$model_0 = $model->findByPk($data['family_id']);
			if(!empty($model_0)) $model = $model_0;
			else{
				$config = array(
					'join_rank'	=> 0, //任何人都可以申请加入
					'post_rank' => 0, //所有家族成员可以发帖
					'reply_rank'=> 0, //所有家族成员可以回应他人发帖
					'scale' => 0.25, //家族长提成比例
					'activity_room'	=> 0,
				);
				if(!isset($data['config'])) $data['config'] = json_encode($config);
				if(!isset($data['announcement']) && isset($data['name'])){
					$data['announcement'] = '祝贺，'.$data['name'].' 家族正式成立！';
					unset($data['name']);
				}
				$top = array(
					'dedication'=> '--',
					'members'	=> '--',
					'medal'		=> '--',
					'rank1'		=> '--',
					'rank2'		=> '--',
					'rank3'		=> '--',
				);
				$data['top'] = json_encode($top);
			}
		}
		$this->attachAttribute($model, $data);
		if(!$model->validate()){
			return $this->setNotices($model->getErrors(), 0);
		}
		$model->save();
		
		return $model->getPrimaryKey();
	}
	
	/**
	 * 获取家族扩展信息
	 * @param int $family_id
	 * @return array
	 */
	public function getFamilyExtend($family_id){
		$return = FamilyExtendModel::model()->findByPk($family_id);
		if(empty($return)) return array();
		else return $return->getAttributes();
	}
	
	public function getFamilyScale($familyId){
		$family = $this->getFamilyExtend($familyId);
		if($family){
			$config = json_decode($family['config'],true);
			return isset($config['scale']) ? floatval($config['scale']) : 0.25;
		}
		return 0.25;
	}
	
	/**
	 * 获取所有家族成员
	 * @param int $family_id
	 * @param int|array $role_id
	 * @return array
	 */
	public function getMembers($family_id, $role_id = -1){
		$return = FamilyMemberModel::model()->getMembers($family_id, $role_id, array(), false);
		return $this->buildDataByIndex($return['list'], 'uid');
	}
	
	/**
	 * 分页获取家族成员相信信息
	 * @param int $family_id
	 * @param int|array $role_id
	 * @param int $page
	 * @param int $pageSize
	 * @return array
	 */
	public function getMembersByPage($family_id, $role_id = -1, $page = 1, $pageSize = 20, array $conditions = array(), $order = ''){
		$page = intval($page) < 1 ? 1 : $page;
		$return = FamilyMemberModel::model()->getMembers($family_id, $role_id, $conditions, true, ($page - 1) * $pageSize, $pageSize, $order);
		$uids = array_keys($this->buildDataByIndex($return['list'], 'uid'));
		$userInfo = UserJsonInfoService::getInstance()->getUserInfos($uids, false);
		foreach($return['list'] as &$r){
			$r['nickname'] = isset($userInfo[$r['uid']]['nk']) ? $userInfo[$r['uid']]['nk'] : '';
			$r['nk'] = isset($userInfo[$r['uid']]['nk']) ? $userInfo[$r['uid']]['nk'] : '';
			$r['ut'] = isset($userInfo[$r['uid']]['ut']) ? $userInfo[$r['uid']]['ut'] : 0;
			$r['rk'] = isset($userInfo[$r['uid']]['rk']) ? $userInfo[$r['uid']]['rk'] : 0;
			$r['dk'] = isset($userInfo[$r['uid']]['dk']) ? $userInfo[$r['uid']]['dk'] : 0;
			$r['medal'] = isset($userInfo[$r['uid']]['fp']['medal']) ? $userInfo[$r['uid']]['fp']['medal'] : '';
			$r['atr'] = isset($userInfo[$r['uid']]['atr']) ? $userInfo[$r['uid']]['atr'] : array();
			$r['de'] = isset($userInfo[$r['uid']]['de']) ? $userInfo[$r['uid']]['de'] : 0;
		}
		return $return;
	}
	
	/**
	 * 根据uids查询某family里的成员信息
	 * @param int $family_id
	 * @param int|array $uids
	 * @return array
	 */
	public function getMembersByUids($family_id, $uids){
		if(!is_array($uids)) $uids = array(intval($uids));
		if(empty($uids)) return array();
		$return = FamilyMemberModel::model()->getMembersByUids($family_id, $uids);
		return $this->buildDataByIndex($return, 'uid');
	}
	
	/**
	 * 根据uids查询所有家族信息
	 * 
	 * @author supeng
	 * @param int|array $uids
	 * @return array
	 */
	public function getMembersGroupByUids($uids){
		if(!is_array($uids)) $uids = array(intval($uids));
		if(empty($uids)) return array();
		$return = FamilyMemberModel::model()->getMembersGroupByUids($uids);
		return $return;
	}
	
	/**
	 * 根据uid查询所有家族的成员信息
	 * @param int $uid
	 * @return array
	 */
	public function getMembersByUid($uid){
		$return = FamilyMemberModel::model()->getMembersByUid($uid);
		return $this->buildDataByIndex($return, 'family_id');
	}
	
	/**
	 * 获取家族的所有族徽成员信息
	 * @param int $family_id
	 * @return array
	 */
	public function getMedalMemberByFamily($family_id){
		$return = FamilyMemberModel::model()->getMembers($family_id, -1, array('medal_enable' => 1), false);
		return $return['list'];
	}
	
	/**
	 * 获取成员的族徽地址
	 * @param array $uids
	 * @return array
	 */
	public function getMedalMembers($uids){
		if(!is_array($uids)) $uids = array(intval($uids));
		$members = FamilyMemberModel::model()->getMedalMembers($uids);
		if(empty($members)) return array();
		$family_ids = array_keys($this->buildDataByIndex($members, 'family_id'));
		$familys = $this->getFamilyIds($family_ids);
		$medal = array();
		foreach($members as $u){
			$f = $familys[$u['family_id']];
			$medal[$u['uid']] = '/images/family/'.$f['id'].'/medal_'.($f['sign'] == 1 ? '0' : $f['level']).($u['role_id'] == FAMILY_ROLE_OWNER ? 3 : ($u['role_id'] == FAMILY_ROLE_ELDER || $u['role_id'] == FAMILY_ROLE_ADMINISTRATOR ? 2 : 1)).'.jpg';
		}
		return $medal;
	}
	
	/**
	 * 获取某些uid的家族主播信息
	 * @param array $uids
	 * @return array
	 */
	public function getDoteyMembers($uids){
		if(!is_array($uids)) $uids = array(intval($uids));
		$members = FamilyMemberModel::model()->getDoteyMembers($uids);
		if(empty($members)) return array();
		return $this->buildDataByIndex($members, 'uid');
	}
	
	/**
	 * 获取家族中的所有家族主播
	 * @param int $family_id
	 * @return array
	 */
	public function getDoteyMembersByFamily($family_id){
		$return = FamilyMemberModel::model()->getMembers($family_id, -1, array('family_dotey' => 1), false);
		return $this->buildDataByIndex($return['list'], 'uid');
	}
	
	/**
	 * 保存家族成员
	 * @param array $data
	 * @return boolean
	 */
	public function saveMember(array $data){
		if(empty($data['family_id']) || empty($data['uid'])){
			return $this->setError(Yii::t('common','Parameter is empty'), false);
		}
		$model = new FamilyMemberModel();
		if(isset($data['id'])){
			$model = $model->findByPk($data['id']);
			if(empty($model)) return $this->setError(Yii::t('common','Data not exists'), false);
		}elseif($model0 = $model->findByUid($data['family_id'], $data['uid'])){
			$model = $model0;
		}else{
			$data['create_time'] = time();
		}
		$this->attachAttribute($model, $data);
		if(!$model->validate()){
			return $this->setNotices($model->getErrors(), false);
		}
		$model->save();
		return true;
	}
	
	/**
	 * 获取家族等级信息
	 * @param int $level
	 * @return array
	 */
	public function getAllLevel(){
		$level = FamilyLevelModel::model()->findAll();
		$level = $this->arToArray($level);
		return $this->buildDataByIndex($level, 'level');
	}
	
	/**
	 * 保存家族等级信息
	 * @param array $data
	 * @return boolean
	 */
	public function saveLevel(array $data){
		if(empty($data['level'])){
			return $this->setError(Yii::t('common','Parameter is empty'), false);
		}
		$model = new FamilyLevelModel();
		if(isset($data['id'])){
			$model = $model->findByPk($data['id']);
			if(empty($model)) return $this->setError(Yii::t('common','Data not exists'), false);
		}else{
			$data['create_time'] = time();
		}
		$this->attachAttribute($model, $data);
		if(!$model->validate()){
			return $this->setNotices($model->getErrors(), false);
		}
		$model->save();
		return true;
	}
	
	/**
	 * 获取需要审核的家族成员申请列表
	 * @param int $family_id
	 * @param int $apply_type 申请的身份，默认为全部身份, 0普通族员, 1家族主播
	 * @param int $page
	 * @param int $pageSize
	 * @param boolean $pageEnable
	 * @return array(list=>array, count=>int)
	 */
	public function getApplyList($family_id, $apply_type = 0, $pageEnable = true, $page = 1, $pageSize = 10){
		$page = intval($page) < 1 ? 1 : $page;
		return FamilyMemberApplyRecordsModel::model()->getApplyList($family_id, $apply_type, $pageEnable, ($page - 1)*$pageSize, $pageSize);
	}
	
	/**
	 * 查询某些uid在某family的申请记录
	 * @param int $family_id
	 * @param int|array $uids
	 * @param int $status 申请状态
	 * @return array
	 */
	public function getApplyRecordByUids($family_id, $uids, $status = null){
		if(!is_array($uids)) $uids = array(intval($uids));
		if(empty($uids)) return array();
		return FamilyMemberApplyRecordsModel::model()->getApplyByUids($family_id, $uids, $status);
	}
	
	/**
	 * 保存成员申请记录
	 * @param array $data
	 * @return boolean
	 */
	public function saveMemberApplyRecord(array $data){
		if(empty($data['family_id']) || empty($data['uid'])){
			return $this->setError(Yii::t('common','Parameter is empty'), false);
		}
		$model = new FamilyMemberApplyRecordsModel();
		if(isset($data['id'])){
			$model = $model->findByPk($data['id']);
			if(empty($model)) return $this->setError(Yii::t('common','Data not exists'), false);
			$data['confirm_time'] = time();
		}else{
			$model_0 = $model->find('family_id = '.$data['family_id'].' and uid = '.$data['uid']);
			if(!empty($model_0)){
				$model = $model_0;
			}
			$data['create_time'] = time();
		}
		$this->attachAttribute($model, $data);
		if(!$model->validate()){
			return $this->setNotices($model->getErrors(), false);
		}
		$model->save();
		return true;
	}
	
	/**
	 * 家族创建申请
	 * @param FamilyApplyForm $form
	 * @return number
	 */
	public function apply(FamilyApplyForm $form){
		if(!$this->applyCheck($form->uid)){
			return 0;
		}
		//家族名称唯一性检查，家族转让无需此检查
		$family = FamilyModel::model()->getFamilyList(array('name' => $form->name, 'status' => 'all'), 0, 10, 'f.id desc', false);
		if(!empty($family['list'])){
			return $this->setNotice(7, Yii::t('family','family name is exist'), 0);
		}
		//族徽名称唯一性检查，家族转让无需此检查
		$family = FamilyModel::model()->getFamilyList(array('medal' => strtoupper($form->medal), 'status' => 'all'), 0, 10, 'f.id desc', false);
		if(!empty($family['list'])){
			return $this->setNotice(8, Yii::t('family','family medal is exist'), 0);
		}
		
		$consumeService = new ConsumeService();
		$consume = $consumeService->getConsumesByUids(array($form->uid));
		$config = self::getSetting();
		if($consume[$form->uid]['pipiegg'] < $config['create_price'])
			return $this->setNotice(9, Yii::t('family','pipiegg not enough'), 0);
		
		$doteyService = new DoteyService();
		$doteys = $doteyService->getDoteysInUids(array($form->uid));
		$is_dotey = isset($doteys[$form->uid]) ? 1 : 0;
		
		//强退的家族主播不能创建家族
		if($is_dotey && $day = FamilyQuitRecordsModel::model()->isFocue($form->uid, strtotime('-'.$config['focus_quit'].' days'))){
			return $this->setNotice(10, str_replace(':day', $day, Yii::t('family','you have focus quit record')), 0);
		}
		
		$form->cover = $this->uploadFamilyCover('cover');
		if(empty($form->cover)) return 0;
		
		$user['uid'] = $form->uid;
		$user['realname'] = $form->realname;
		$userService = new UserService();
		$userService->saveUserBasic($user);
		
		$extend['uid'] = $form->uid;
		$extend['mobile'] = $form->mobile;
		$extend['qq'] = $form->qq;
		$userService->saveUserExtend($extend);
		
		$config = self::getSetting();
		if($config['create_price'] == 0 || $consumeService->consumeEggs($form->uid, $config['create_price'])){
			if($family_id = $this->saveFamily($form->getFamilyAttributes())){
				$tmp = IMAGES_PATH.'tmp'.DIR_SEP.'family'.DIR_SEP.$form->cover;
				$src = IMAGES_PATH.'family'.DIR_SEP.$family_id.DIR_SEP.$form->cover;
				$this->makeCover($tmp, $src);
				unlink($tmp);
				
				$member = array(
					'family_id'	=> $family_id,
					'uid'		=> $form->uid,
					'role_id'	=> FAMILY_ROLE_OWNER,
					'is_dotey'	=> $is_dotey,
					'have_medal'=> 1,
					'buy_type'	=> 0,
					'buy_time'	=> time()
				);
				if($is_dotey){
					$attr = $form->getFamilyAttributes();
					if(isset($attr['sign']) && $attr['sign'] == 1){
						$member['family_dotey'] = 1;
					}
				}
				$this->saveMember($member);
				
				$consumeService->updateUserJsonInfo($form->uid, array('pipiegg' => true));
				
				$this->saveMyFamily($form->uid);
				
				$roles = PurviewService::getInstance()->getUserRolesBySub($form->uid, PURVIEW_POLETYPE_FAMILY, $family_id);
				$role = array_keys($this->buildDataByIndex($roles, 'role_id'));
				//创建族长权限
				$role[] = FAMILY_ROLE_OWNER;
				PurviewService::getInstance()->saveUserRoles($form->uid, $role, $form->uid, 0, $family_id, PURVIEW_POLETYPE_FAMILY);
				
				//创建家族论坛
				BbsbaseService::getInstance()->createForum($form->uid, '家族话题', FORUM_FROM_TYPE_FAMILY, $family_id);
			}
			
			$pipieggRecords['uid'] = $form->uid;
			$pipieggRecords['pipiegg'] = $config['create_price'];
			$pipieggRecords['to_target_id'] = $family_id;
			$pipieggRecords['source'] = SOURCE_FAMILY;
			$pipieggRecords['sub_source'] = SUBSOURCE_FAMILY_CREATE;
			$pipieggRecords['extra'] = '申请创建家族';
			$consumeService->saveUserPipiEggRecords($pipieggRecords, 0);
			
			//生成族徽
			$src = "fontimg".DIR_SEP."family".DIR_SEP;
			$dst = "family".DIR_SEP.$family_id.DIR_SEP."medal_";
			$this->makeMedal($form->medal, STATIC_PATH.$src."11.png", IMAGES_PATH.$dst."11.jpg");
			$this->makeMedal($form->medal, STATIC_PATH.$src."12.png", IMAGES_PATH.$dst."12.jpg");
			$this->makeMedal($form->medal, STATIC_PATH.$src."13.png", IMAGES_PATH.$dst."13.jpg");
			
			if(!$family_id){
				$filename = DATA_PATH.'runtimes/create_family_error.log';
				error_log(date("Y-m-d H:i:s")."创建家族失败：".json_encode($form->getFamilyAttributes())."\n\r",3,$filename);
				return $this->setNotice(3, Yii::t('family','system error'), 0);
			}
		}else{
			return $this->setNotice(9, Yii::t('family','pipiegg not enough'), 0);
		}
		
		return $family_id;
	}
	
	/**
	 * 家族创建检查
	 * @param int $uid
	 * @return boolean
	 */
	public function applyCheck($uid, $is_transfer = false, $family_id = 0){
		$return = true;
		
		//全局是否允许家族创建申请检查
		$config = self::getSetting();
		if(!$is_transfer){
			if(empty($config) || $config['apply_enable'] == false)
				return $this->setNotice(0, Yii::t('family','apply forbidden'), false);
		}
		$user = UserJsonInfoService::getInstance()->getUserInfo($uid, false);
		if(!(isset($user['dk']) && $user['dk'] >= $config['drank'] || isset($user['rk']) && $user['rk'] >= $config['urank'])){
			$return = $this->setNotice(1, Yii::t('family','dotey or user rank not enough'), false);
		}elseif(empty($user['ut'])){
			$return = $this->setNotice(3, Yii::t('family','system error'), false);
		}
		
		$familys = $this->getMyFamily($uid);
		
		if($is_transfer && !empty($familys['create'])){
			return $this->setNotice(5, Yii::t('family','everyone only create one family'), false);
		}
		$doteyMembers = $this->getDoteyMembers($uid);
		if(!empty($doteyMembers)){
			$flag = true;
			if($is_transfer && intval($family_id) > 0 && !empty($familys['join'])){
				foreach($familys['join'] as $join){
					if($join['id'] == $family_id) $flag = false;
				}
			}
			if($flag) return $this->setNotice(6, Yii::t('family','family dotey only join one family'), false);
		}
		
		if(!empty($familys['create'])){
			$family = $familys['create'];
			//申请被拒绝3天后，自动消失，恢复到重新可提交申请的状态
			if($family['status'] < 0 && $family['update_time'] < time() - 86400 * 3){
				$this->dissolution($family['id'], $uid, '审核拒绝三天后重新申请');
			}elseif($family['status'] < 0 && $family['update_time'] >= time() - 86400 * 3){
				if($family['status'] == FAMILY_STATUS_UNPREPARE) $error = Yii::t('family','auto forbidden');
				else{
					$error = $this->getReason($family['id'], 1);
				}
				$return = $this->setNotice(4, $error['reason'], false);
			}elseif($family['status'] >= 0){
				$return = $this->setNotice(5, Yii::t('family','everyone only create one family'), false);
			}
		}
		
		return $return;
	}
	
	/**
	 * 生成族徽
	 * @param unknown_type $medal 族徽简字
	 * @param unknown_type $src 族徽背景原图
	 * @param unknown_type $dst 生成族徽地址
	 */
	public function makeMedal($medal, $src, $dst){
		if(!is_dir(dirname($dst)))
			mkdir(dirname($dst), 0755, true);
		$medal = strtoupper($medal);
		$srcInfo = getimagesize($src);
		$srcWidth = $srcInfo[0];
		$srcHeight = $srcInfo[1];
		unset($srcInfo);
		$font = STATIC_PATH."font/simsun.ttc";
		$box = imageftbbox(9, 0, $font, $medal);
		$w = $box[2] - $box[6];
		$h = $box[3] - $box[7];
		unset($box);
		$posX = ceil(($srcWidth - $w) / 2);
		$posY = ceil(($srcHeight - $h) / 2);
		
		$srcImg = imagecreatefrompng($src);
		$white = imagecolorallocate($srcImg, 255, 255, 255);
		$grey = imagecolorallocate($srcImg, 128, 128, 128);
		imagettftext($srcImg, 9, 0, $posX+1, 13, $grey, $font, $medal);
		imagettftext($srcImg, 9, 0, $posX, 12, $white, $font, $medal);
		imagejpeg($srcImg, $dst);
		imagedestroy($srcImg);
	}
	
	/**
	 * 生成家族封面
	 * @param string $src 源图
	 * @param string $dst 目标图
	 * @param int $width 目标图宽
	 * @param int $height 目标图高
	 */
	public function makeCover($src, $dst, $width = 205, $height = 121){
		if(!is_dir(dirname($dst))){
			mkdir(dirname($dst), 0755, true);
		}
		list($swidth, $sheight) = getimagesize($src);
		$scaleW = $width/$swidth;
		$scaleH = $height/$sheight;
		if($scaleW < $scaleH){
			$dwidth = $width;
			$dheight = ceil($scaleW * $sheight);
			$w = 0;
			$h = ceil(($height - $dheight) / 2);
		}else{
			$dheight = $height;
			$dwidth = ceil($scaleH * $swidth);
			$w = ceil(($width - $dwidth) / 2);
			$h = 0;
		}
		if (function_exists('imagecreatetruecolor'))
			$image = imagecreatetruecolor($width, $height);
		else $image = imagecreate($width, $height);
		$white = imagecolorallocate($image, 255, 255, 255);
		imagefill($image, 0, 0, $white);
		$srcImg = imagecreatefromjpeg($src);
		if (function_exists("imagecopyresampled"))
			imagecopyresampled($image, $srcImg, $w, $h, 0, 0, $dwidth, $dheight, $swidth, $sheight);
		else imagecopyresized($image, $srcImg, $w, $h, 0, 0, $dwidth, $dheight, $swidth, $sheight);
		imagejpeg($image, $dst);
		imagedestroy($srcImg);
		imagedestroy($image);
	}
	
	/**
	 * 申请加入家族
	 * @param int $family_id
	 * @param int $uid
	 * @param boolean $is_check
	 * @return boolean
	 */
	public function join($family_id, $uid, $is_check = true){
		if(empty($family_id) || empty($uid)){
			return $this->setNotice(0, Yii::t('common','Parameter is empty'), false);
		}
		$my = $this->getMyFamily($uid);
		if(count($my['join']) >= 3){
			return $this->setNotice(1, Yii::t('family','everyone only join three family'), false);
		}
		
		$member = $this->getMembersByUids($family_id, $uid, true);
		if(!empty($member[$uid])){
			return $this->setNotice(2, Yii::t('family','you are in family'), false);
		}
		
		$family = $this->getFamily($family_id);
		if(empty($family)) return $this->setNotice(9, Yii::t('family','family is not checked'), false);
		
		//筹备阶段申请的必须满足绅士03级别
		if($family['status'] == FAMILY_STATUS_WAIT){
			$userInfo = UserJsonInfoService::getInstance()->getUserInfo($uid, false);
			if($userInfo['rk'] < 3){
				$conditions = $this->getJoinCondition();
				return $this->setNotice(3, $conditions[3], false);
			}
		}
		
		if($family['sign'] == 0){
			$level = $this->getAllLevel();
			if(isset($level[$family['level']]) && $level[$family['level']]['members'] > 0 && $family['member_total'] >= $level[$family['level']]['members']){
				return $this->setNotice(4, Yii::t('family','member limit'), false);
			}
		}
		
		$doteyService = new DoteyService();
		$doteys = $doteyService->getDoteysInUids(array($uid));
		$is_dotey = false;
		if(!empty($doteys[$uid])){
			$is_dotey = true;
			if($family['sign']){
				$doteyMembers = $this->getDoteyMembers($uid);
				if(!empty($doteyMembers)){
					return $this->setNotice(5, Yii::t('family','family dotey only join one family'), false);
				}
			}
			//强退的家族主播不能加入其他签约家族
			$web = self::getSetting();
			if($family['sign'] == 1 && $day = FamilyQuitRecordsModel::model()->isFocue($uid, strtotime('-'.intval($web['focus_quit']).' days'), $family['id'])){
				return $this->setNotice(8, str_replace(':day', $day, Yii::t('family','you have focus quit record')), 0);
			}
// 			if($family['dotey_total'] >= $level[$family['level']]['dotey']){
// 				return $this->setNotice(4, Yii::t('family','dotey limit'), false);
// 			}
		}
		
		//判断加入申请条件
		if($is_check){
			$extend = $this->getFamilyExtend($family_id);
			$config = json_decode($extend['config'], true);
			$conditions = $this->getJoinCondition();
			if($config['join_rank'] == -3){
				return $this->setNotice(6, $conditions[$config['join_rank']], false);
			}elseif($config['join_rank'] == -2){
				if(!$is_dotey){
					return $this->setNotice(6, $conditions[$config['join_rank']], false);
				}
			}elseif($config['join_rank'] == -1){
				$is_check = false;
			}elseif($config['join_rank'] > 0){
				$userInfo = UserJsonInfoService::getInstance()->getUserInfo($uid, false);
				$rk = 0;
				if(empty($userInfo) || !isset($userInfo['rk'])){
					$consumeService = new ConsumeService();
					$consume = $consumeService->getConsumesByUids($uid);
					$consume = $consume[$uid];
					$rk = $consume['rank'];
				}else{
					$rk = $userInfo['rk'];
				}
				if($rk < $config['join_rank']){
					return $this->setNotice(6, $conditions[$config['join_rank']], false);
				}
			}
		}
		
		//签约家族主播申请必须审核
		if($family['sign']){
			if($is_dotey && $family['status'] == FAMILY_STATUS_SUCCESS) $is_check = true;
		}
		
		if($is_check){
			$record = $this->getApplyRecordByUids($family_id, $uid, 0);
			if(!empty($record)){
				return $this->setNotice(7, Yii::t('family', 'you are applied'), false);
			}
			
			$apply = array(
				'uid'		=> $uid,
				'family_id'	=> $family_id,
				'apply_type'=> $is_dotey ? 1 : 0,
				'status'	=> FAMILY_MEMBER_STATUS_WAIT,
			);
			$this->saveMemberApplyRecord($apply);
			
			//发消息给族长有新的成员加入家族
			$title = '家族新成员申请审批提醒';
			$content = '有新加入 '.$family['name'].' 的申请，请及时审批！';
			$url = $this->getFamilyUrl($family_id, 'family/adminCheck');
			$messageService = new MessageService();
			$message['uid'] = $uid;
			$message['to_uid'] = $family['uid'];
			$message['title'] = $title;
			$message['category'] = MESSAGE_CATEGORY_FAMILY;
			$message['sub_category'] = MESSAGE_CATEGORY_FAMILY_JOIN;
			$message['target_id'] =  $family['id'];
			$message['content'] = $content;
			$message['extra']= array('from'=>$family['name'],'href'=>$url);
			$messageService->sendMessage($message);
		}else{
			$member = array(
				'family_id'	=> $family_id,
				'uid'		=> $uid,
				'role_id'	=> 0,
				'is_dotey'	=> $is_dotey ? 1 : 0,
				'have_medal'=> 0,
				'buy_type'	=> 0,
				'buy_time'	=> 0
			);
			if($is_dotey && $family['sign']){
				$member['have_medal'] = 1;
				$member['buy_time']	= time();
				$member['family_dotey'] = 1;
			}
			$this->saveMember($member);
			$this->saveFamilyCache($family_id, $family);
			$this->saveMyFamily($uid);
			
			//创建家族主播权限，家族活动房用
			if($is_dotey && $family['sign']){
				$roles = PurviewService::getInstance()->getUserRolesBySub($uid, PURVIEW_POLETYPE_FAMILY, $family_id);
				$role = $this->buildDataByIndex($roles, 'role_id');
				$role[] = FAMILY_ROLE_DOTEY;
				PurviewService::getInstance()->saveUserRoles($uid, $role, $uid, 0, $family_id, PURVIEW_POLETYPE_FAMILY);
			}
			
			//发消息给族长、长老、管理等有新的成员申请需处理
			$members = $this->getMembers($family_id, array(FAMILY_ROLE_OWNER, FAMILY_ROLE_ELDER, FAMILY_ROLE_ADMINISTRATOR));
			$uids = array_keys($this->buildDataByIndex($members, 'uid'));
			$title = '家族新成员加入提醒';
			$content = '您的 '.$family['name'].' 有新的成员加入';
			$url = $this->getFamilyUrl($family['id']);
			$messageService = new MessageService();
			$message['uid'] = $uid;
			$message['to_uid'] = $uids;
			$message['title'] = $title;
			$message['category'] = MESSAGE_CATEGORY_FAMILY;
			$message['sub_category'] = MESSAGE_CATEGORY_FAMILY_JOIN;
			$message['target_id'] =  $family['id'];
			$message['content'] = $content;
			$message['extra']= array('from'=>$family['name'],'href'=>$url);
			$messageService->sendMessage($message);
		}
		
		return true;
	}
	
	/**
	 * 家族成员申请审核
	 * @param int $family_id
	 * @param int|array $uids
	 * @param int $status
	 * @return boolean
	 */
	public function memberCheck($family_id, $uids, $status){
		if(!is_array($uids)) $uids = array(intval($uids));
		if(empty($family_id) || empty($uids)){
			return $this->setNotice(0, Yii::t('common','Parameter is empty'), false);
		}
		
		if($status == FAMILY_MEMBER_STATUS_SUCCESS){
			$family = $this->getFamily($family_id);
			if($family['sign'] == 0){
				$level = $this->getAllLevel();
				if(isset($level[$family['level']]) && $level[$family['level']]['members'] > 0 &&  $family['member_total'] + count($uids) > $level[$family['level']]['members']){
					return $this->setNotice(1, Yii::t('family','member limit'), false);
				}
			}
			
			foreach($uids as $k => $u){
				$my = $this->getMyFamily($u);
				if(count($my['join']) >= 3){
					unset($uids[$k]);
				}
			}
			if(empty($uids)){
				return $this->setNotice(2, Yii::t('family','everyone only join three family'), false);
			}
			
			$doteyService = new DoteyService();
			$doteys = $doteyService->getDoteysInUids($uids);
			if(!empty($doteys)){
				if($family['sign']){
					$doteyMembers = $this->getDoteyMembers($uids);
					if(!empty($doteyMembers)){
						return $this->setNotice(3, Yii::t('family','family dotey only join one family'), false);
					}
				}
// 				if($family['dotey_total'] + count($doteys) >= $level[$family['level']]['dotey']){
// 					return $this->setNotice(2, Yii::t('family','dotey limit'), false);
// 				}
			}
			
			if(FamilyMemberApplyRecordsModel::model()->updateApplyStatus($family_id, $uids, $status)){
				foreach($uids as $u){
					$member = array(
						'family_id'	=> $family_id,
						'uid'		=> $u,
						'role_id'	=> 0,
						'is_dotey'	=> isset($doteys[$u]) ? 1 : 0,
						'have_medal'=> 0,
						'buy_type'	=> 0,
						'buy_time'	=> 0
					);
					if(isset($doteys[$u]) && $family['sign']){
						$member['have_medal'] = 1;
						$member['buy_time']	= time();
						$member['family_dotey'] = 1;
					}
					$this->saveMember($member);
					$this->saveMyFamily($u);
					
					//创建家族主播权限，家族活动房用
					if(isset($doteys[$u]) && $family['sign']){
						$roles = PurviewService::getInstance()->getUserRolesBySub($uid, PURVIEW_POLETYPE_FAMILY, $family_id);
						$role = $this->buildDataByIndex($roles, 'role_id');
						$role[] = FAMILY_ROLE_DOTEY;
						PurviewService::getInstance()->saveUserRoles($uid, $role, $uid, 0, $family_id, PURVIEW_POLETYPE_FAMILY);
					}
				}
				$this->saveFamilyCache($family_id, $family);
				
				//发消息给申请加入家族的用户
				$title = '家族成员申请通过';
				$content = '您成功加入 '.$family['name'].'！';
				$url = $this->getFamilyUrl($family['id']);
				$messageService = new MessageService();
				$message['uid'] = Yii::app()->user->id;
				$message['to_uid'] = $uids;
				$message['title'] = $title;
				$message['category'] = MESSAGE_CATEGORY_FAMILY;
				$message['sub_category'] = MESSAGE_CATEGORY_FAMILY_JOIN;
				$message['target_id'] =  $family['id'];
				$message['content'] = $content;
				$message['extra']= array('from'=>$family['name'],'href'=>$url);
				$messageService->sendMessage($message);
			}
		}elseif($status == FAMILY_MEMBER_STATUS_REFUSE){
			FamilyMemberApplyRecordsModel::model()->updateApplyStatus($family_id, $uids, $status);
			//发消息给申请加入家族的用户
			$title = '家族成员申请拒绝';
			$content = '您的加入申请被 '.$family['name'].' 拒绝！';
			$url = '';
			$messageService = new MessageService();
			$message['uid'] = Yii::app()->user->id;
			$message['to_uid'] = $uids;
			$message['title'] = $title;
			$message['category'] = MESSAGE_CATEGORY_FAMILY;
			$message['sub_category'] = MESSAGE_CATEGORY_FAMILY_JOIN;
			$message['target_id'] =  $family['id'];
			$message['content'] = $content;
			$message['extra']= array('from'=>$family['name'],'href'=>$url);
			$messageService->sendMessage($message);
		}
		return true;
	}
	
	/**
	 * 退出家族
	 * @param int $family_id
	 * @param int $uid
	 * @return boolean
	 */
	public function quit($family_id, $uid){
		if(empty($family_id) || empty($uid)){
			return $this->setNotice(0, Yii::t('common','Parameter is empty'), false);
		}
		
		$members = $this->getMembersByUids($family_id, $uid);
		if(empty($members)){
			return $this->setNotice(1, Yii::t('family','you are not in family'), false);
		}
		
		$family = $this->getFamily($family_id);
		if($family['status'] == FAMILY_STATUS_PREPARE){
			return $this->setNotice(2, Yii::t('family','family is prepared'), false);
		}
		
		//留下退出或踢出的时间记录，并计算退出用户对家族的消费累计
		if($family['status'] > 0) $this->quitRecord($family_id, $uid);
		
		if(FamilyMemberModel::model()->deleteMembers($family_id, array(intval($uid)))){
			$this->saveFamilyCache($family_id, $family);
			$this->saveMyFamily($uid);
			if($members[$uid]['medal_enable']) $this->saveMyMedal($uid, $family, 0, true);
			
			//删除用户在家族内所有权限
			$roles = PurviewService::getInstance()->getUserRolesBySub($uid, PURVIEW_POLETYPE_FAMILY, $family_id);
			if(!empty($roles)){
				$role_ids = array_keys($this->buildDataByIndex($roles, 'role_id'));
				PurviewService::getInstance()->deleteUserRoles($uid, $role_ids, $uid, 0, $family_id);
			}
		}else{
			return $this->setNotice(1, Yii::t('family','you are not in family'), false);
		}
		return true;
	}
	
	/**
	 * 踢出家族
	 * @param int $family_id
	 * @param int|max $uids
	 * @param int $op_uid 操作人uid
	 * @param int $op_role 操作人角色id
	 * @return boolean
	 */
	public function kick($family_id, $uids, $op_uid, $op_role = 0){
		if(!is_array($uids)) $uids = array(intval($uids));
		if(empty($family_id) || empty($uids)){
			return $this->setNotice(0, Yii::t('common','Parameter is empty'), false);
		}
		
		//过滤家族管理、长老等，只允许踢普通成员和主播，要踢管理、长老先卸任成普通成员在踢
		$members = $this->getMembersByUids($family_id, $uids);
		$diff = array();
		foreach($members as $m){
			if(in_array($m['role_id'], array(FAMILY_ROLE_OWNER, FAMILY_ROLE_ELDER, FAMILY_ROLE_ADMINISTRATOR))){
				$diff[] = $m['uid'];
			}
		}
		$uids = array_diff($uids, $diff);
		if(empty($uids)) return true;
		
		$family = $this->getFamily($family_id);
		//留下退出或踢出的时间记录，并计算退出用户对家族的消费累计
		if($family['status'] > 0) {
			foreach($uids as $uid){
				$this->quitRecord($family_id, $uid, 1);
			}
		}
		
		if(FamilyMemberModel::model()->deleteMembers($family_id, $uids)){
			$this->saveFamilyCache($family_id, $family);
			foreach($uids as $uid){
				$this->saveMyFamily($uid);
				if($members[$uid]['medal_enable']){
					$this->saveMyMedal($uid, $family, 0, true);
				}
			}
			
			//删除用户在家族内所有权限
			$roles = PurviewService::getInstance()->getUsersRolesBySub($uids, PURVIEW_POLETYPE_FAMILY, $family_id);
			foreach($roles as $uid => $r){
				PurviewService::getInstance()->deleteUserRoles($uid, array_keys($r), $op_uid, $op_role, $family_id);
			}
			
			//发消息给用户已被踢出
			$messageService = new MessageService();
			$message['uid'] = $op_uid;
			$message['to_uid'] = $uids;
			$message['title'] = '踢出家族提醒';
			$message['category'] = MESSAGE_CATEGORY_FAMILY;
			$message['sub_category'] = MESSAGE_CATEGORY_FAMILY_MANAGE;
			$message['target_id'] =  $family['id'];
			$message['content'] = '您被踢出了 '.$family['name'].'！';
			$message['extra']= array('from'=>$family['name'],'href'=>'');
			$messageService->sendMessage($message);
			return true;
		}else{
			return $this->setNotice(0, Yii::t('family','you are not in family'), false);
		}
		return true;
	}
	/**
	 * 获取用户加入与退出记录
	 * 
	 * @param int $familyId 家族ID
	 * @param $uids array 用户ID
	 * @return array
	 */
	public function getUserQuitRecordsByUids($familyId,array $uids){
		if(empty($uids)){
			return array();
		}
		$quitModel = FamilyQuitRecordsModel::model();
		$quitRecords = $quitModel->getUserQuitRecordsByUids($familyId,$uids);
		$quitRecords = $this->arToArray($quitRecords);
		return $this->buildDataByKey($quitRecords,'uid');
	}
	/**
	 * 留下退出或踢出的时间记录，并计算退出用户对家族的消费累计
	 * @param int $family_id
	 * @param int $uid
	 * @param int $type 是否踢人操作，0否，1是
	 * @return boolean
	 */
	private function quitRecord($family_id, $uid, $type = 0){
		$type = intval($type);
		$end_time = time();
		$member = $this->getMembersByUids($family_id, $uid);
		$member = $member[$uid];
		$member['last_time'] = FamilyConsumeRecordsModel::model()->getLastRecordTime($family_id);
		
		//留下卸下族徽记录，并计算卸下时对家族的充值累计
		if($member['medal_enable'] == 1){
			$this->unloadRecord($family_id, $uid, $member);
		}
		
		//自从上次统计过后，用户退出的这段时间产生的消费记录
		$start_time = $member['last_time'] > $member['create_time'] ? $member['last_time'] : $member['create_time'];
		
		$dedication = $charm = 0;
		$consumeService = new ConsumeService();
		$conditions = array(
			'uid'	=> $uid,
			'create_time_on' => date('Y-m-d H:i:s', $start_time),
			'create_time_end' => date('Y-m-d H:i:s', $end_time)
		);
		$records = $consumeService->getDedicationByCondition($conditions, 0, 100000, false);
		foreach($records as $r){
			$dedication = $dedication + $r['dedication'];
		}
		if($member['family_dotey'] == 1){
			$records = $consumeService->getCharmByCondition($conditions, 0, 100000, false);
			foreach($records as $r){
				$charm = $charm + $r['charm'];
			}
		}
		
		$record = array(
			'family_id'	=> $family_id,
			'uid'		=> $uid,
			'type'		=> $type,
			'op_uid'	=> $type ? Yii::app()->user->id : 0,
			'is_dotey'	=> $member['family_dotey'],
			'medal_enable' => $member['medal_enable'],
			'join_time'	=> $member['create_time'],
			'last_time'	=> $member['last_time'],
			'quit_time'	=> $end_time,
			'charm'		=> $charm,
			'dedication'=> $dedication
		);
		$this->saveQuitRecord($record);
		return true;
	}
	
	/**
	 * 留下卸下族徽记录，并计算卸下时对家族的充值累计
	 * @param int $family_id
	 * @param int $uid
	 * @param array $member
	 * @return boolean
	 */
	private function unloadRecord($family_id, $uid, $member = null){
		$end_time = time();
		if($member === null){
			$member = $this->getMembersByUids($family_id, $uid);
			$member = $member[$uid];
			$member['last_time'] = FamilyConsumeRecordsModel::model()->getLastRecordTime($family_id);
		}
		//自从上次统计过后，用户退出的这段时间产生的充值记录
		$start_time = $member['last_time'] > $member['equip_time'] ? $member['last_time'] : $member['create_time'];
		
		$consumeService = new ConsumeService();
		$recharge = UserRechargeRecordsModel::model()->getUserPipiEggsByTime($uid, $start_time, $end_time);
		
		$record = array(
			'family_id'	=> $family_id,
			'uid'		=> $uid,
			'equip_time'=> $member['equip_time'],
			'last_time'	=> $member['last_time'],
			'unload_time'=> $end_time,
			'recharge'	=> $recharge
		);
		$this->saveUnloadRecord($record);
		return true;
	}
	
	/**
	 * 保存退出记录
	 * @param array $data
	 * @return number
	 */
	private function saveQuitRecord(array $data){
		if(!isset($data['id']) && (intval($data['family_id']) < 1 || intval($data['uid']) < 1)){
			return $this->setError(Yii::t('common','Parameter is empty'), 0);
		}
		$model = new FamilyQuitRecordsModel();
		if(isset($data['id'])){
			$model = $model->findByPk($data['id']);
			if(empty($model)) return $this->setError(Yii::t('common','Data not exists'), 0);
		}
		$this->attachAttribute($model, $data);
		if(!$model->validate()){
			return $this->setNotices($model->getErrors(), 0);
		}
		$model->save();
		
		return $model->getPrimaryKey();
	}
	
	/**
	 * 保存卸下记录
	 * @param array $data
	 * @return number
	 */
	private function saveUnloadRecord(array $data){
		if(!isset($data['id']) && (intval($data['family_id']) < 1 || intval($data['uid']) < 1)){
			return $this->setError(Yii::t('common','Parameter is empty'), 0);
		}
		$model = new FamilyUnloadRecordsModel();
		if(isset($data['id'])){
			$model = $model->findByPk($data['id']);
			if(empty($model)) return $this->setError(Yii::t('common','Data not exists'), 0);
		}
		$this->attachAttribute($model, $data);
		if(!$model->validate()){
			return $this->setNotices($model->getErrors(), 0);
		}
		$model->save();
		
		return $model->getPrimaryKey();
	}
	
	/**
	 * 获取家族荣誉内容
	 * @param int $family_id
	 * @param int $limit
	 * @param int $last_id
	 * @return array
	 */
	public function getHonor($family_id, $limit = 1, $last_id = 0){
		return FamilyHonorModel::model()->getHonor($family_id, $limit, $last_id);
	}
	
	/**
	 * 保存家族荣誉
	 * @param array $data
	 * @return boolean
	 */
	public function saveHonor(array $data){
		if(isset($data['id']) && intval($data['id']) < 1){
			return $this->setError(Yii::t('common','Parameter is empty'), false);
		}
		$model = new FamilyHonorModel();
		if(isset($data['id'])){
			$model = $model->findByPk($data['id']);
			if(empty($model)) return $this->setError(Yii::t('common','Data not exists'), false);
		}else{
			$data['create_time'] = time();
		}
		$this->attachAttribute($model, $data);
		if(!$model->validate()){
			return $this->setNotices($model->getErrors(), false);
		}
		$model->save();
		return true;
	}
	
	/**
	 * 改变家族状态
	 * @param int $family_id
	 * @param int $status
	 * @return number
	 */
	public function changeFamilyStatus($family_id, $status, $reason = ''){
		$family['id'] = $family_id;
		$family['status'] = $status;
		
		if($status == FAMILY_STATUS_SUCCESS){
			//家族创建荣誉
			$f = $this->getFamily($family_id);
			if($f['status'] == 2 || $f['status'] == 0 || $f['status'] == -2){
				$member = $this->getMembers($family_id);
				$m = $member[$f['uid']];
				unset($member[$f['uid']]);
				$uids = array_keys($member);
				$users = UserJsonInfoService::getInstance()->getUserInfos($uids, false);
				$array = array();
				foreach($users as $u){
					$array[] = array('uid' => $u['uid'], 'nk' => $u['nk']);
				}
				$data = array(
					'family_id'	=> $family_id,
					'type'		=> 'create',
					'honor'		=> json_encode($array),
				);
				$this->saveHonor($data);
				
				//族长是否已有族徽，如果有族徽强制佩戴自己家族的族徽
				$medalMember = FamilyMemberModel::model()->getMedalMembers(array($f['uid']));
				if(!empty($medalMember)){
					$medalMember = array_pop($medalMember);
					if($medalMember['family_id'] != $family_id){
						$medalMember['medal_enable'] = 0;
						$this->unloadMedal($medalMember['family_id'], $f['uid']);
					}
				}
				$m['medal_enable']	= 1;
				$m['equip_time'] = time();
				$this->saveMember($m);
				$this->saveMyMedal($f['uid'], $f, FAMILY_ROLE_OWNER);
			}
			//人工审核成功记录
			$record = array(
				'family_id'	=> $family_id,
				'type'		=> 0,
				'reason'	=> '审核通过',
				'op_uid'	=> Yii::app()->user->id,
			);
			$this->saveOperateRcord($record);

			$content = '您的 '.$f['name'].' 已被管理员审核通过！';
			$url = $this->getFamilyUrl($family_id);
			$title = '家族状态提醒';
				
			$messageService = new MessageService();
			$message['uid'] = Yii::app()->user->id;
			$message['to_uid'] = $f['uid'];
			$message['title'] = $title;
			$message['category'] = MESSAGE_CATEGORY_FAMILY;
			$message['sub_category'] = MESSAGE_CATEGORY_FAMILY_MANAGE;
			$message['target_id'] =  $f['id'];
			$message['content'] = $content;
			$message['extra']= array('from'=>$f['name'],'href'=>$url);
			$messageService->sendMessage($message);
		}elseif($status == FAMILY_STATUS_REFUSE){
			//人工审核拒绝记录
			if(empty($reason)) return $this->setNotice(0, Yii::t('family','reason can not be empty'), false);
			$record = array(
				'family_id'	=> $family_id,
				'type'		=> 1,
				'reason'	=> $reason,
				'op_uid'	=> Yii::app()->user->id,
			);
			$this->saveOperateRcord($record);
			
			//家族状态改变提醒
			$f = $this->getFamily($family_id);
			$content = '您的 '.$f['name'].' 已被管理员审核拒绝！';
			$url = '';
			$title = '家族状态提醒';
			
			$messageService = new MessageService();
			$message['uid'] = Yii::app()->user->id;
			$message['to_uid'] = $f['uid'];
			$message['title'] = $title;
			$message['category'] = MESSAGE_CATEGORY_FAMILY;
			$message['sub_category'] = MESSAGE_CATEGORY_FAMILY_MANAGE;
			$message['target_id'] =  $f['id'];
			$message['content'] = $content;
			$message['extra']= array('from'=>$f['name'],'href'=>$url);
			$messageService->sendMessage($message);
		}
		return $this->saveFamily($family);
	}
	
	/**
	 * 家族隐藏操作
	 * @param int $family_id
	 * @param boolean $hidden
	 * @param string $reason
	 * @return boolean
	 */
	public function hiddenFamily($family_id, $hidden = true, $reason = ''){
		if($hidden && empty($reason))
			return $this->setNotice(0, Yii::t('family','reason can not be empty'), false);
		$family['family_id'] = $family_id;
		$family['hidden'] = intval($hidden);
		//人工审核隐藏显示记录
		if($hidden){
			$type = 2;
		}else{
			$type = 3;
			$reason = empty($reason) ? '显示家族' : $reason;
		}
		$record = array(
			'family_id'	=> $family_id,
			'type'		=> $type,
			'reason'	=> $reason,
			'op_uid'	=> Yii::app()->user->id,
		);
		$this->saveOperateRcord($record);
		if($this->saveFamily($family)){
			$family = $this->getFamily($family_id);
			
			//家族状态改变提醒
			$title = '家族隐藏';
			$content = '您的 '.$family['name'].' 已被管理员隐藏！';
			$url = $this->getFamilyUrl($family_id);
				
			$messageService = new MessageService();
			$message['uid'] = Yii::app()->user->id;
			$message['to_uid'] = $family['uid'];
			$message['title'] = $title;
			$message['category'] = MESSAGE_CATEGORY_FAMILY;
			$message['sub_category'] = MESSAGE_CATEGORY_FAMILY_MANAGE;
			$message['target_id'] =  $family['id'];
			$message['content'] = $content;
			$message['extra']= array('from'=>$family['name'],'href'=>$url);
			$messageService->sendMessage($message);
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * 家族封禁操作
	 * @param int $family_id
	 * @param boolean $forbidden
	 * @param string $reason
	 * @return boolean
	 */
	public function forbiddenFamily($family_id, $forbidden = true, $reason = ''){
		if($forbidden && empty($reason))
			return $this->setNotice(0, Yii::t('family','reason can not be empty'), false);
		$family['id'] = $family_id;
		$family['forbidden'] = intval($forbidden);
		//人工审核封停恢复记录
		if($forbidden){
			$type = 4;
		}else{
			$type = 5;
			$reason = empty($reason) ? '恢复家族' : $reason;
		}
		$record = array(
			'family_id'	=> $family_id,
			'type'		=> $type,
			'reason'	=> $reason,
			'op_uid'	=> Yii::app()->user->id,
		);
		$this->saveOperateRcord($record);
		if($this->saveFamily($family)){
			$family = $this->getFamily($family_id);
			
			//家族状态改变提醒
			$title = '家族封禁';
			$content = '您的 '.$family['name'].' 已被管理员封禁！';
			$url = '';
			
			$messageService = new MessageService();
			$message['uid'] = Yii::app()->user->id;
			$message['to_uid'] = $family['uid'];
			$message['title'] = $title;
			$message['category'] = MESSAGE_CATEGORY_FAMILY;
			$message['sub_category'] = MESSAGE_CATEGORY_FAMILY_MANAGE;
			$message['target_id'] =  $family['id'];
			$message['content'] = $content;
			$message['extra']= array('from'=>$family['name'],'href'=>$url);
			$messageService->sendMessage($message);
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * 转让家族
	 * @param int $family_id
	 * @param int $from_uid
	 * @param int $to_uid
	 * @param string $password
	 * @param int $op_role 操作人角色
	 * @return boolean
	 */
	public function transferFamily($family_id, $from_uid, $to_uid, $op_role_id = 0, $password = ''){
		if(!$this->applyCheck($to_uid, true, $family_id)){
			return false;
		}
		
		if($password){
			$userService = new UserService();
			$user = $userService->getUserBasicByUids(array($from_uid));
			if(!$userService->vadidatorPassword($user[$from_uid]['username'], $password, 0)){
				return $this->setNotice(3, Yii::t('user','You enter the user name or password incorrect'), false);
			}
		}
		
		$member = array(
			'family_id'	=> $family_id,
			'uid'		=> $from_uid,
			'role_id'	=> 0
		);
		$this->saveMember($member);
		
		$member = $this->getMembersByUids($family_id, $to_uid);
		//族长是否已有族徽，如果有族徽则不能再次佩戴
		$medalMember = FamilyMemberModel::model()->getMedalMembers(array($to_uid));
		if(!empty($medalMember)){
			//族长强制佩戴族徽，此需求有问题需求方还未最终确定，暂时隐藏
// 			$medalMember = array_pop($medalMember);
// 			$medalMember['medal_enable'] = 0;
// 			$this->saveMember($medalMember);
		}
		if(isset($member[$to_uid])){
			$member = $member[$to_uid];
			$member['role_id'] = FAMILY_ROLE_OWNER;
			if($member['have_medal'] == 0){
				$member['have_medal'] = 1;
				$member['buy_type'] = 0;
				$member['buy_time'] = time();
			}
		}else{
			$member = array(
				'family_id' => $family_id,
				'uid'		=> $to_uid,
				'role_id'	=> FAMILY_ROLE_OWNER,
				'have_medal'=> 1,
				'buy_type'	=> 0,
				'buy_time'	=> time(),
			);
		}
		if(empty($medalMember)){
			$member['medal_enable'] = 1;
			$member['equip_time'] = time();
		}
		$doteyService = new DoteyService();
		$doteys = $doteyService->getDoteysInUids(array($to_uid));
		if(isset($doteys[$to_uid])) $member['is_dotey'] = 1;
		$family = $this->getFamily($family_id);
		if($family['sign']) $member['family_dotey'] = 1;
		$this->saveMember($member);
		
		$family['uid'] = $to_uid;
		$this->saveFamily($family);
		$this->saveMyFamily($from_uid);
		$this->saveMyFamily($to_uid);
		
		$this->saveMyMedal($from_uid, $family, 0);
		$this->saveMyMedal($to_uid, $family, FAMILY_ROLE_OWNER);
		
		//删除用户在家族内的族长权限
		PurviewService::getInstance()->deleteUserRoles($from_uid, array(FAMILY_ROLE_OWNER), $from_uid, $op_role_id, $family_id);
		
		//创建族长权限
		$role = array(FAMILY_ROLE_OWNER);
		//创建家族主播权限，家族活动房用
		if($family['sign'] && isset($doteys[$to_uid])){
			$role[] = FAMILY_ROLE_DOTEY;
		}
		PurviewService::getInstance()->saveUserRoles($to_uid, $role, $from_uid, $op_role_id, $family_id, PURVIEW_POLETYPE_FAMILY);
		
		//发消息给转让方用户
		$user = UserJsonInfoService::getInstance()->getUserInfo($uid, false);
		$title = '家族转让';
		$content = $user['nk'].'把 '.$family['name'].' 转让给您！';
		$url = $this->getFamilyUrl($family_id);
			
		$messageService = new MessageService();
		$message['uid'] = $uid;
		$message['to_uid'] = $to_uid;
		$message['title'] = $title;
		$message['category'] = MESSAGE_CATEGORY_FAMILY;
		$message['sub_category'] = MESSAGE_CATEGORY_FAMILY_MANAGE;
		$message['target_id'] =  $family['id'];
		$message['content'] = $content;
		$message['extra']= array('from'=>$family['name'],'href'=>$url);
		$messageService->sendMessage($message);
		return true;
	}
	
	/**
	 * 设置家族成员的角色
	 * @param int $family_id
	 * @param int|array $uids
	 * @param int $role_id
	 * @param $op_uid 操作人uid
	 * @param $op_role 操作人角色
	 * @return boolean
	 */
	public function addMemberRole($family_id, $uids, $role_id, $op_uid=0, $op_role=0){
		if(!is_array($uids)) $uids = array(intval($uids));
		if(empty($family_id) || empty($uids)){
			return $this->setNotice(0, Yii::t('common','Parameter is empty'), false);
		}
		
		$family = $this->getFamily($family_id);
		$level = $this->getAllLevel();
		
		//设置长老
		if($role_id == FAMILY_ROLE_ELDER){
			if($family['sign'] == 0) $max = $level[$family['level']]['elder'];
			else $max = 15;
			if($family['elder_total'] + count($uids) > $max){
				return $this->setNotice(1, Yii::t('family','elder limit'), false);
			}
		}
		
		//设置家族管理
		if($role_id == FAMILY_ROLE_ADMINISTRATOR){
			if($family['sign'] == 0) $max = $level[$family['level']]['admin'];
			else $max = 50;
			if($family['admin_total'] + count($uids) > $max){
				return $this->setNotice(2, Yii::t('family','admin limit'), false);
			}
		}
		
		$return = FamilyMemberModel::model()->updateRole($family_id, $uids, $role_id);
		$this->saveFamilyCache($family_id, $family);
		
		$members = $this->getMedalMembers($uids);
		foreach($members as $m){
			$this->saveMyMedal($m['uid'], $family, $role_id);
		}
		
		if($return){
			//设置权限，每一个用户在家族中只能有一种管理身份，重新设置的话之前的身份会被删除
			foreach($uids as $uid){
				PurviewService::getInstance()->saveUserRoles($uid, array($role_id), $op_uid, $op_role, $family_id, PURVIEW_POLETYPE_FAMILY);
			}
			
			//发消息给用户设置或解除长老
			if($role_id == FAMILY_ROLE_ELDER){
				$title = '家族委任';
				$content = '您被 '.$family['name'].' 委任为家族长老！';
				$url = $this->getFamilyUrl($family['id']);
				$messageService = new MessageService();
				$message['uid'] = $op_uid;
				$message['to_uid'] = $uids;
				$message['title'] = $title;
				$message['category'] = MESSAGE_CATEGORY_FAMILY;
				$message['sub_category'] = MESSAGE_CATEGORY_FAMILY_MANAGE;
				$message['target_id'] =  $family['id'];
				$message['content'] = $content;
				$message['extra']= array('from'=>$family['name'],'href'=>$url);
				$messageService->sendMessage($message);
			//发消息给用户设置或解除管理
			}elseif($role_id == FAMILY_ROLE_ADMINISTRATOR){
				$title = '家族委任';
				$content = '您被 '.$family['name'].' 委任为家族管理！';
				$url = $this->getFamilyUrl($family['id']);
				$messageService = new MessageService();
				$message['uid'] = $data['uid'];
				$message['to_uid'] = $uids;
				$message['title'] = $title;
				$message['category'] = MESSAGE_CATEGORY_FAMILY;
				$message['sub_category'] = MESSAGE_CATEGORY_FAMILY_MANAGE;
				$message['target_id'] =  $family['id'];
				$message['content'] = $content;
				$message['extra']= array('from'=>$family['name'],'href'=>$url);
				$messageService->sendMessage($message);
			}
		}
		return $return;
	}
	
	/**
	 * 解除家族成员的角色
	 * @param int $family_id
	 * @param int|array $uids
	 * @param int $role_id
	 * @param $op_uid 操作人uid
	 * @param $op_role 操作人角色
	 * @return boolean
	 */
	public function removeMemberRole($family_id, $uids, $role_id, $op_uid=0, $op_role=0){
		if(!is_array($uids)) $uids = array(intval($uids));
		if(empty($family_id) || empty($uids)){
			return $this->setNotice(0, Yii::t('common','Parameter is empty'), false);
		}
	
		$family = $this->getFamily($family_id);
	
		$return = FamilyMemberModel::model()->deleteRole($family_id, $uids);
		$this->saveFamilyCache($family_id, $family);
		
		$members = $this->getMedalMembers($uids);
		foreach($members as $m){
			$this->saveMyMedal($m['uid'], $family, $role_id);
		}
	
		if($return){
			//删除权限
			foreach($uids as $uid){
				PurviewService::getInstance()->deleteUserRoles($uid, array($role_id), $op_uid, $op_role, $family_id, PURVIEW_POLETYPE_FAMILY);
			}
			
			//发消息给用户设置或解除长老
			if($role_id == FAMILY_ROLE_ELDER){
				$title = '家族卸任';
				$content = '您被 '.$family['name'].' 解除了长老权利！';
				$url = '';
				$messageService = new MessageService();
				$message['uid'] = $op_uid;
				$message['to_uid'] = $uids;
				$message['title'] = $title;
				$message['category'] = MESSAGE_CATEGORY_FAMILY;
				$message['sub_category'] = MESSAGE_CATEGORY_FAMILY_MANAGE;
				$message['target_id'] =  $family['id'];
				$message['content'] = $content;
				$message['extra']= array('from'=>$family['name'],'href'=>$url);
				$messageService->sendMessage($message);
			//发消息给用户设置或解除管理
			}elseif($role_id == FAMILY_ROLE_ADMINISTRATOR){
				$title = '家族卸任';
				$content = '您被 '.$family['name'].' 解除了管理权利！';
				$url = '';
				$messageService = new MessageService();
				$message['uid'] = $data['uid'];
				$message['to_uid'] = $uids;
				$message['title'] = $title;
				$message['category'] = MESSAGE_CATEGORY_FAMILY;
				$message['sub_category'] = MESSAGE_CATEGORY_FAMILY_MANAGE;
				$message['target_id'] =  $family['id'];
				$message['content'] = $content;
				$message['extra']= array('from'=>$family['name'],'href'=>$url);
				$messageService->sendMessage($message);
			}
		}
		return $return;
	}
	
	/**
	 * 获得某用户所拥有的所有家族徽章
	 * @param int $uid
	 * @return array('enable' => array, 'have' => array) enable 是当前佩戴的唯一族徽信息，have是拥有的未佩戴的族徽列表
	 */
	public function getMyMedals($uid){
		$medals = FamilyMemberModel::model()->getMyMedals($uid);
		$return = array('enable' => array(), 'have' => array());
		if(empty($medals)) $return;
		foreach($medals as &$m){
			$m['medal'] = '/images/family/'.$m['family_id'].'/medal_'.($m['sign'] ? '0' : $m['level']).($m['role_id'] == FAMILY_ROLE_OWNER ? 3 : ($m['role_id'] == FAMILY_ROLE_ELDER ? 2 : 1)).'.jpg';
			$m['url'] = $this->getFamilyUrl($m['family_id']);
			if($m['medal_enable'] == 1) $return['enable'] = $m;
			else $return['have'][] = $m;
		}
		return $return;
	}
	
	/**
	 * 购买家族徽章
	 * @param int $family_id
	 * @param int $uid
	 * @return boolean
	 */
	public function buyMedal($family_id, $uid){
		$member = $this->getMembersByUids($family_id, $uid);
		if(!isset($member[$uid])){
			return $this->setNotice(0, Yii::t('family','you are not in family'), false);
		}
		$consumeService = new ConsumeService();
		$consume = $consumeService->getConsumesByUids($uid);
		$config = self::getSetting();
		if($consume[$uid]['pipiegg'] < $config['medal_price']){
			return $this->setNotice(1, Yii::t('common','Pipiegg not enough'), false);
		}
		if($consumeService->consumeEggs($uid, $config['medal_price'])){
			$member = array(
				'family_id'	=> $family_id,
				'uid'		=> $uid,
				'have_medal'=> 1,
				'buy_type'	=> 1,
				'buy_time'	=> time()	
			);
			$this->saveMember($member);
			$family = $this->getFamily($family_id);
			
			$consumeService->updateUserJsonInfo($uid, array('pipiegg' => true));
			$consumeService->saveUserConsumeAttribute(array('uid' => $uid, 'dedication' => $config['medal_price'] * $this->multiple));
			$consumeService->saveUserConsumeAttribute(array('uid' => $family['uid'], 'egg_points' => $config['medal_price'] * $this->multiple * 0.5));
			
			$pipieggRecords['uid'] = $uid;
			$pipieggRecords['pipiegg'] = $config['medal_price'];
			$pipieggRecords['to_target_id'] = $family_id;
			$pipieggRecords['source'] = SOURCE_FAMILY;
			$pipieggRecords['sub_source'] = SUBSOURCE_FAMILY_MEDAL;
			$pipieggRecords['extra'] = '购买家族徽章';
			$consumeService->saveUserPipiEggRecords($pipieggRecords, 0);
			
			$dedicationRecords['uid'] = $uid;
			$dedicationRecords['dedication'] = $config['medal_price'] * $this->multiple;
			$dedicationRecords['to_target_id'] = $family_id;
			$dedicationRecords['source'] = SOURCE_FAMILY;
			$dedicationRecords['sub_source'] = SUBSOURCE_FAMILY_MEDAL;
			$dedicationRecords['client'] = CLIENT_FAMILY;
			$dedicationRecords['info'] = '购买家族徽章';
			$consumeService->saveUserDedicationRecords($dedicationRecords, 1);
			
			$eggPointsRecords['uid'] = $family['uid'];
			$eggPointsRecords['sender_uid'] = $uid;
			$eggPointsRecords['target_id'] = $family_id;
			$eggPointsRecords['egg_points'] = $config['medal_price'] * $this->multiple * 0.5;
			$eggPointsRecords['source'] = SOURCE_FAMILY;
			$eggPointsRecords['sub_source'] = SUBSOURCE_FAMILY_MEDAL;
			$eggPointsRecords['client'] = CLIENT_FAMILY;
			$eggPointsRecords['info'] = '购买家族徽章';
			$consumeService->saveUserEggPointsRecords($eggPointsRecords, 1);
			return true;
		}else{
			return $this->setNotice(2, Yii::t('common','Pipiegg reduce failed'), false);
		}
	}
	
	/**
	 * 变更家族徽章名称
	 * @param int $family_id
	 * @param int $uid
	 * @param string $medal
	 * @return boolean
	 */
	public function updateMedal($family_id, $uid, $medal){
		if(intval($family_id) < 1 || intval($uid) < 1 || empty($medal)){
			return $this->setNotice(0, Yii::t('common','Parameter is empty'), false);
		}
		$family = $this->getFamily($family_id);
		if(empty($family) || $family['status'] != FAMILY_MEMBER_STATUS_SUCCESS){
			return $this->setNotice(0, Yii::t('family','family is not checked'), false);
		}
		if($family['medal'] == $medal){
			return $this->setNotice(1, Yii::t('family','medal is not changed'), false);
		}
		
		$member = $this->getMembersByUids($family_id, $uid);
		if(!isset($member[$uid])){
			return $this->setNotice(2, Yii::t('family','you are not in family'), false);
		}
		$consumeService = new ConsumeService();
		$consume = $consumeService->getConsumesByUids($uid);
		$config = self::getSetting();
		if($consume[$uid]['pipiegg'] < $config['update_medal_price']){
			return $this->setNotice(3, Yii::t('common','Pipiegg not enough'), false);
		}
		if($consumeService->consumeEggs($uid, $config['update_medal_price'])){
			$level = $family['level'];
			$family = array(
				'id'	=> $family_id,
				'medal'	=> $medal,
			);
			$this->saveFamily($family);
			
			$consumeService->updateUserJsonInfo($uid, array('pipiegg' => true));
				
			$pipieggRecords['uid'] = $uid;
			$pipieggRecords['pipiegg'] = $config['update_medal_price'];
			$pipieggRecords['to_target_id'] = $family_id;
			$pipieggRecords['source'] = SOURCE_FAMILY;
			$pipieggRecords['sub_source'] = SUBSOURCE_FAMILY_MEDAL_UPDATE;
			$pipieggRecords['extra'] = '变更家族徽章';
			$consumeService->saveUserPipiEggRecords($pipieggRecords, 0);
			
			//生成族徽
			for($i = $level; $i > 0; $i--){
				$src = "fontimg".DIR_SEP."family".DIR_SEP.$i;
				$dst = "family".DIR_SEP.$family_id.DIR_SEP."medal_".$i;
				$this->makeMedal($family['medal'], STATIC_PATH.$src."1.png", IMAGES_PATH.$dst."1.jpg");
				$this->makeMedal($family['medal'], STATIC_PATH.$src."2.png", IMAGES_PATH.$dst."2.jpg");
				$this->makeMedal($family['medal'], STATIC_PATH.$src."3.png", IMAGES_PATH.$dst."3.jpg");
			}
			return true;
		}else{
			return $this->setNotice(4, Yii::t('common','Pipiegg reduce failed'), false);
		}
	}
	
	/**
	 * 佩戴家族徽章
	 * @param int $family_id
	 * @param int $uid
	 * @return boolean
	 */
	public function equipMedal($family_id, $uid){
		$member = $this->getMembersByUids($family_id, $uid);
		if(!isset($member[$uid])){
			return $this->setNotice(0, Yii::t('family','you are not in family'), false);
		}
		$member = $member[$uid];
		if($member['have_medal'] == 0){
			return $this->setNotice(1, Yii::t('family','you do not have medal'), false);
		}
		if($member['medal_enable'] == 1){
// 			return $this->setNotice(2, Yii::t('family','your medal have equiped'), false);
			return true;
		}
		
		$members = $this->getMembersByUid($uid);
		$medal_enable = $equip_time = 0;
// 		foreach($members as $m){
// 			if($m['medal_enable'] == 1) $medal_enable = 1;
// 			if($m['equip_time'] > $equip_time) $equip_time = $m['equip_time'];
// 		}
// 		if($medal_enable){
// 			return $this->setNotice(2, Yii::t('family','your medal have equiped'), false);
// 		}
		foreach($members as $m){
			if($m['medal_enable'] == 1){
				$m['medal_enable'] = 0;
				$this->unloadMedal($m['family_id'], $m['uid']);
				break;
			}
		}
		
		//佩戴30天不能更换限制
// 		if($equip_time > time() - 30 * 86400){
// 			return $this->setNotice(3, Yii::t('family','medal has changed'), false);
// 		}
		
		$member['medal_enable'] = 1;
		$member['equip_time'] = time();
		
		$this->saveMember($member);
		$family = $this->getFamily($family_id);
		$this->saveFamily($family);
		$this->saveMyMedal($uid, $family, $member['role_id']);
		return true;
	}
	
	/**
	 * 卸下家族徽章
	 * @param int $family_id
	 * @param int $uid
	 * @return boolean
	 */
	public function unloadMedal($family_id, $uid){
		$member = $this->getMembersByUids($family_id, $uid);
		if(!isset($member[$uid])){
			return $this->setNotice(0, Yii::t('family','you are not in family'), false);
		}
		$member = $member[$uid];
		if($member['have_medal'] == 0){
			return $this->setNotice(1, Yii::t('family','you do not have medal'), false);
		}
		if($member['medal_enable'] == 0){
			return $this->setNotice(2, Yii::t('family','your medal have unloaded'), false);
		}
		
		$member['last_time'] = FamilyConsumeRecordsModel::model()->getLastRecordTime($family_id);
		//留下卸下族徽记录，并计算卸下时对家族的充值累计
		$this->unloadRecord($family_id, $uid, $member);
		
		$m = array(
			'family_id'	=> $family_id,
			'uid'		=> $uid,
			'medal_enable'=> 0
		);
		$this->saveMember($m);
		$family = $this->getFamily($family_id);
		$this->saveFamily($family);
		$this->saveMyMedal($uid, $family, $member['role_id'], true);
		return true;
	}
	
	/**
	 * 获取家族榜单
	 * @param string $type 榜单种类
	 * @param string $date 时间类别
	 * @return array
	 */
	public function getFamilyTop($type, $date = 'day'){
		if(!in_array($type, array('charm', 'dedication', 'medal', 'recharge'))) return array();
		if(!in_array($date, array('day', 'week', 'month', 'super'))) return array();
		return OtherRedisModel::getInstance()->getFamilyTop($type, $date);
	}
	
	/**
	 * 保存家族级别变动记录
	 * @param array $data
	 * @return boolean
	 */
	public function saveLevelRecord(array $data){
		if(isset($data['id']) && intval($data['id']) < 1){
			return $this->setError(Yii::t('common','Parameter is empty'), false);
		}
		$model = new FamilyLevelRecordsModel();
		if(isset($data['id'])){
			$model = $model->findByPk($data['id']);
			if(empty($model)) return $this->setError(Yii::t('common','Data not exists'), false);
		}else{
			$data['create_time'] = time();
		}
		$this->attachAttribute($model, $data);
		if(!$model->validate()){
			return $this->setNotices($model->getErrors(), false);
		}
		$model->save();
		return true;
	}
	
	/**
	 * 保存操作家族记录
	 * @param array $data
	 * @return boolean
	 */
	public function saveOperateRcord(array $data){
		if(isset($data['id']) && intval($data['id']) < 1){
			return $this->setError(Yii::t('common','Parameter is empty'), false);
		}
		$model = new FamilyOperateRecordsModel();
		if(isset($data['id'])){
			$model = $model->findByPk($data['id']);
			if(empty($model)) return $this->setError(Yii::t('common','Data not exists'), false);
		}else{
			$data['create_time'] = time();
		}
		$this->attachAttribute($model, $data);
		if(!$model->validate()){
			return $this->setNotices($model->getErrors(), false);
		}
		$model->save();
		return true;
	}
	
	/**
	 * 查询操作原因
	 * @param int $family_id
	 * @param int $type
	 * @return array
	 */
	public function getReason($family_id, $type = 4){
		return FamilyOperateRecordsModel::model()->getReason($family_id, $type);
	}
	
	/**
	 * 解散家族，后台操作
	 * @param int $family_id
	 * @param int $op_uid 操作人
	 * @param string $reason 操作原因
	 * @return boolean
	 */
	public function dissolution($family_id, $op_uid, $reason){
		if(intval($family_id) < 1 || intval($op_uid) < 1 || empty($reason)){
			return $this->setError(Yii::t('common','Parameter is empty'), false);
		}
		$family = $this->getFamily($family_id);
		$members = $this->getMembers($family_id);
		$uids = array_keys($this->buildDataByIndex($members, 'uid'));
		if(FamilyModel::model()->deleteFamily($family_id)){
			//@todo 此处返回皮蛋要用事务的，时间来不及的情况目前只能暂时处理
			if($family['status'] < 0){
				$config = self::getSetting();
				$consumeService = new ConsumeService();
				if($consumeService->addEggs($family['uid'], $config['create_price'])){
					$pipieggRecords['uid'] = $family['uid'];
					$pipieggRecords['pipiegg'] = $config['create_price'];
					$pipieggRecords['to_target_id'] = $family_id;
					$pipieggRecords['source'] = SOURCE_FAMILY;
					$pipieggRecords['sub_source'] = SUBSOURCE_FAMILY_CREATE_RETURN;
					$pipieggRecords['extra'] = '申请创建家族审核不通过返回皮蛋';
					$consumeService->saveUserPipiEggRecords($pipieggRecords, 1);
					
					$consumeService->updateUserJsonInfo($family['uid'], array('pipiegg' => true));
				}else{
					$filename = DATA_PATH.'runtimes/create_family_error.log';
					error_log(date("Y-m-d H:i:s")."创建家族审核不通过返回皮蛋失败：".json_encode($family)."\n\r",3,$filename);
				}
			}
			
			OtherRedisModel::getInstance()->deleteFamily($family_id);
			FamilyExtendModel::model()->deleteExtend($family_id);
			FamilyMemberModel::model()->deleteAllMembers($family_id);
			$record = array(
				'family_id'	=> $family_id,
				'type'		=> 6,
				'reason'	=> $reason,
				'op_uid'	=> $op_uid,
				'uid'		=> $family['uid'],
				'name'		=> $family['name'],
				'level'		=> $family['level'],
			);
			$this->saveOperateRcord($record);
			
			foreach($members as $m){
				$this->saveMyFamily($m['uid']);
				if($m['medal_enable']){
					$this->saveMyMedal($m['uid'], $family, $m['role_id'], true);
				}
				$role = array();
				if($m['role_id'] > 0){
					$role[] = $m['role_id'];
				}
				if($m['family_dotey']){
					$role[] = FAMILY_ROLE_DOTEY;	
				}
				if(!empty($role)){
					//删除家族所有权限
					PurviewService::getInstance()->deleteUserRoles($m['uid'], $role, $op_uid, 0, $family_id, PURVIEW_POLETYPE_FAMILY);
				}
			}
			
			$title = '家族解散';
			$content = '您的 '.$family['name'].' 已解散！';
			$url = '';
			
			$messageService = new MessageService();
			$message['uid'] = $op_uid;
			$message['to_uid'] = $uids;
			$message['title'] = $title;
			$message['category'] = MESSAGE_CATEGORY_FAMILY;
			$message['sub_category'] = MESSAGE_CATEGORY_FAMILY_MANAGE;
			$message['target_id'] =  $family['id'];
			$message['content'] = $content;
			$message['extra']= array('from'=>$family['name'],'href'=>$url);
			$messageService->sendMessage($message);
			
			$dir = IMAGES_PATH.'family'.DIR_SEP.$family_id;
			if(is_dir($dir)){
				$d = dir($dir);
				while($file = $d->read()){
					if(is_file($dir.DIR_SEP.$file)){
						@unlink($dir.DIR_SEP.$file);
					}
				}
				$d->close();
				@rmdir($dir);
			}
			return true;
		}else return false;
	}
	
	/**
	 * 保存签约家族申请记录
	 * @param array $data
	 * @return number
	 */
	private function saveSignApplyRecord(array $data){
		if(isset($data['id']) && intval($data['id']) < 1){
			return $this->setError(Yii::t('common','Parameter is empty'), 0);
		}
		$model = new FamilySignApplyRecordsModel();
		if(isset($data['id'])){
			$model = $model->findByPk($data['id']);
			if(empty($model)) return $this->setError(Yii::t('common','Data not exists'), 0);
			$data['confirm_time'] = time();
		}else{
			$data['create_time'] = time();
		}
		$this->attachAttribute($model, $data);
		if(!$model->validate()){
			return $this->setNotices($model->getErrors(), 0);
		}
		$model->save();
		return $model->getPrimaryKey();
	}
	
	/**
	 * 申请成为签约家族
	 * @param int $uid
	 * @return boolean
	 */
	public function signApply($uid){
		$my = $this->getMyFamily($uid);
		if(empty($my['create'])){
			return $this->setNotice(0, Yii::t('family','you must be owner'), false);
		}
		if($my['create']['sign'] == 1){
			return $this->setNotice(1, Yii::t('family','your family is signed'), false);
		}
		//只能有唯一一个签约家族
		$members = $this->getDoteyMembers(array($uid));
		if(!empty($members)){
			return $this->setNotice(2, Yii::t('family','family dotey only join one family'), false);
		}
		//强退的家族主播不能转签约家族
		$config = self::getSetting();
		if($day = FamilyQuitRecordsModel::model()->isFocue($uid, strtotime('-'.$config['focus_quit'].' days'))){
			return $this->setNotice(3, str_replace(':day', $day, Yii::t('family','you have focus quit record')), 0);
		}
		$record = FamilySignApplyRecordsModel::model()->getApplyByFamily($my['create']['id']);
		if(isset($record) && $record['status'] == -1){
			$reason = $this->getReason($my['create']['id'], 8);
			return $this->setNotice(4, $reason['reason'], false);
		}
		
		$record['family_id']	= $my['create']['id'];
		$record['status']		= 0;
		$record['create_time']	= time();
		return $this->saveSignApplyRecord($record);
	}
	
	
	/**
	 * 获取申请列表
	 * 
	 * @author supeng
	 * @param array $condition
	 * @param unknown_type $offset
	 * @param unknown_type $limit
	 * @param unknown_type $isLimit
	 * @return multitype:number multitype: |Ambigous <multitype:, multitype:NULL , multitype:Ambigous <NULL, unknown, multitype:unknown Ambigous <unknown, NULL> , mixed, multitype:, multitype:unknown > Ambigous <string, unknown, mixed> >
	 */
	public function searchSignList(Array $condition = array(), $offset = 0, $limit = 20, $isLimit = true){
		$model = new FamilySignApplyRecordsModel();
		$data = $model->searchSignList($condition, $offset, $limit, $isLimit);
		if ($data['list']){
			$data['list'] = $this->arToArray($data['list']);
		}
		return $data;
	}
	
	/**
	 * 改变签约家族状态
	 * @param int $id 申请记录的id
	 * @param int $status
	 * @return boolean
	 */
	public function changeSignFamilyStatus($id, $status, $reason = ''){
		$apply = FamilySignApplyRecordsModel::model()->findByPk($id);
		if(empty($apply)){
			return $this->setNotice(0, Yii::t('common','Data not exists'), false);
		}else{
			$apply = $apply->getAttributes();
		}
		$apply['status'] = $status;
		
		if($status == FAMILY_STATUS_SUCCESS){
			$family = $this->getFamily($apply['family_id']);
			//强退的家族主播不能转签约家族
			$config = self::getSetting();
			if($day = FamilyQuitRecordsModel::model()->isFocue($family['uid'], strtotime('-'.$config['focus_quit'].' days'))){
				return $this->setNotice(1, str_replace(':day', $day, Yii::t('family','you have focus quit record')), 0);
			}
			//人工审核成功记录
			$record = array(
				'family_id'	=> $apply['family_id'],
				'type'		=> 7,
				'reason'	=> '签约家族审核通过',
				'op_uid'	=> Yii::app()->user->id,
			);
			$this->saveOperateRcord($record);
			
			$doteys = FamilyMemberModel::model()->getMembers($apply['family_id'], -1, array('is_dotey' => 1), false);
			$doteys = $doteys['list'];
			if(!empty($doteys)){
				$dotey_uids = array_keys($this->buildDataByIndex($doteys, 'uid'));
				$members = $this->getDoteyMembers($dotey_uids);
				$family_dotey = array_keys($members);
				$change = array_diff($dotey_uids, $family_dotey);
				if(!empty($change)){
					FamilyMemberModel::model()->updateFamilyDotey($apply['family_id'], $change);
					foreach($doteys as $d){
						if(in_array($d['uid'], $change)){
							if($d['have_medal'] == 0){
								$m = array(
									'family_id' => $apply['family_id'],
									'uid' => $d['uid'],
									'have_medal' => 1,
									'buy_time' => time()
								);
								$this->saveMember($m);
							}
						}
					}
				}
			}
			$family['sign']	= 1;
			$this->saveFamily($family);

			$content = '您的 '.$family['name'].' 已被管理员审核通过！';
			$url = $this->getFamilyUrl($family['id']);
			$title = '签约家族状态提醒';
	
			$messageService = new MessageService();
			$message['uid'] = Yii::app()->user->id;
			$message['to_uid'] = $family['uid'];
			$message['title'] = $title;
			$message['category'] = MESSAGE_CATEGORY_FAMILY;
			$message['sub_category'] = MESSAGE_CATEGORY_FAMILY_MANAGE;
			$message['target_id'] =  $family['id'];
			$message['content'] = $content;
			$message['extra']= array('from'=>$family['name'],'href'=>$url);
			$messageService->sendMessage($message);
			
			//重新生成族徽
			$members = $this->getMedalMemberByFamily($family['id']);
			if(!empty($members)){
				foreach($members as $m){
					$this->saveMyMedal($m['uid'], $family, $m['role_id']);
				}
			}
			
			//生成族徽
			$src = "fontimg".DIR_SEP."family".DIR_SEP.'0';
			$dst = "family".DIR_SEP.$family['id'].DIR_SEP."medal_0";
			$this->makeMedal($family['medal'], STATIC_PATH.$src."1.png", IMAGES_PATH.$dst."1.jpg");
			$this->makeMedal($family['medal'], STATIC_PATH.$src."2.png", IMAGES_PATH.$dst."2.jpg");
			$this->makeMedal($family['medal'], STATIC_PATH.$src."3.png", IMAGES_PATH.$dst."3.jpg");
		}elseif($status == FAMILY_STATUS_REFUSE){
			//人工审核拒绝记录
			if(empty($reason)) return $this->setNotice(1, Yii::t('family','reason can not be empty'), false);
			$record = array(
				'family_id'	=> $apply['family_id'],
				'type'		=> 8,
				'reason'	=> $reason,
				'op_uid'	=> Yii::app()->user->id,
			);
			$this->saveOperateRcord($record);
	
			$family = $this->getFamily($apply['family_id']);
			if($family['sign']){
				$family['sign']	= 0;
				$this->saveFamily($family);
				
				$members = $this->getDoteyMembersByFamily($apply['family_id']);
				if(!empty($members)){
					$dotey_uids = array_keys($members);
					FamilyMemberModel::model()->updateFamilyDotey($apply['family_id'], $dotey_uids, 0);
				}
			}
			
			//家族状态改变提醒
			$content = '您的 '.$family['name'].' 已被管理员审核拒绝！';
			$url = '';
			$title = '签约家族状态提醒';
	
			$messageService = new MessageService();
			$message['uid'] = Yii::app()->user->id;
			$message['to_uid'] = $family['uid'];
			$message['title'] = $title;
			$message['category'] = MESSAGE_CATEGORY_FAMILY;
			$message['sub_category'] = MESSAGE_CATEGORY_FAMILY_MANAGE;
			$message['target_id'] =  $family['id'];
			$message['content'] = $content;
			$message['extra']= array('from'=>$family['name'],'href'=>$url);
			$messageService->sendMessage($message);
		}
		return $this->saveSignApplyRecord($apply);
	}
	
	/**
	 * 签约家族转普通家族
	 * @param int $famly_id
	 * @return boolean
	 */
	public function changeSignFamily($family_id, $reason){
		$family = $this->getFamily($family_id);
		if($family['sign'] == 0){
			return $this->setNotice(0, Yii::t('family','this family is normal family'), false);
		}
		
		//人工操作记录
		if(empty($reason)) return $this->setNotice(1, Yii::t('family','reason can not be empty'), false);
		$record = array(
			'family_id'	=> $family_id,
			'type'		=> 9,
			'reason'	=> $reason,
			'op_uid'	=> Yii::app()->user->id,
		);
		$this->saveOperateRcord($record);
		
		$level = $this->getAllLevel();
		$recharge = FamilyConsumeRecordsModel::model()->getSum($family_id);
		foreach($level as $k => $l){
			if($recharge >= $l['upgrade']){
				$family['level'] = $k;
			}else{
				break;
			}
		}
		$family['sign'] = 0;
		$this->saveFamily($family);
		
		$doteys = FamilyMemberModel::model()->getMembers($family_id, -1, array('family_dotey' => 1), false);
		$doteys = $doteys['list'];
		if(!empty($doteys)){
			$dotey_uids = array_keys($this->buildDataByIndex($doteys, 'uid'));
			if(!empty($dotey_uids)){
				FamilyMemberModel::model()->updateFamilyDotey($family_id, $dotey_uids, 0);
			}
		}
		
		FamilySignApplyRecordsModel::model()->deleteApplyStatus($family_id);
		
		$members = $this->getMedalMemberByFamily($family_id);
		if(!empty($members)){
			foreach($members as $m){
				$this->saveMyMedal($m['uid'], $family, $m['role_id']);
			}
		}
		
		//生成族徽
		$src = "fontimg".DIR_SEP."family".DIR_SEP.$family['level'];
		$dst = "family".DIR_SEP.$family['id'].DIR_SEP."medal_".$family['level'];
		$this->makeMedal($family['medal'], STATIC_PATH.$src."1.png", IMAGES_PATH.$dst."1.jpg");
		$this->makeMedal($family['medal'], STATIC_PATH.$src."2.png", IMAGES_PATH.$dst."2.jpg");
		$this->makeMedal($family['medal'], STATIC_PATH.$src."3.png", IMAGES_PATH.$dst."3.jpg");
		
		//家族状态改变提醒
		$content = '您的 '.$family['name'].' 已被管理员转为普通家族！';
		$url = '';
		$title = '家族状态提醒';
		
		$messageService = new MessageService();
		$message['uid'] = Yii::app()->user->id;
		$message['to_uid'] = $family['uid'];
		$message['title'] = $title;
		$message['category'] = MESSAGE_CATEGORY_FAMILY;
		$message['sub_category'] = MESSAGE_CATEGORY_FAMILY_MANAGE;
		$message['target_id'] =  $family['id'];
		$message['content'] = $content;
		$message['extra']= array('from'=>$family['name'],'href'=>$url);
		$messageService->sendMessage($message);
		return true;
	}
	
	/**
	 * 变更家族等级
	 * @param int $family_id
	 * @param int $level
	 * @return boolean
	 */
	public function familyUpgrade($family_id, $level){
		if(intval($family_id) < 1) return false;
		$level = intval($level);
		if($level < 1) $level = 1;
		if($level > 6) $level = 6;
		$family = array(
			'id'	=> $family_id,
			'level'	=> $level,
		);
		$this->saveFamily($family);
		
		$family = $this->getFamily($family['id']);
		if($family['sign'] == 1) $family['level'] = 0;
		//生成族徽
		$src = "fontimg".DIR_SEP."family".DIR_SEP.$family['level'];
		$dst = "family".DIR_SEP.$family['id'].DIR_SEP."medal_".$family['level'];
		$this->makeMedal($family['medal'], STATIC_PATH.$src."1.png", IMAGES_PATH.$dst."1.jpg");
		$this->makeMedal($family['medal'], STATIC_PATH.$src."2.png", IMAGES_PATH.$dst."2.jpg");
		$this->makeMedal($family['medal'], STATIC_PATH.$src."3.png", IMAGES_PATH.$dst."3.jpg");
		
		$members = $this->getMedalMemberByFamily($family['id']);
		if(!empty($members)){
			foreach($members as $m){
				$this->saveMyMedal($m['uid'], $family, $m['role_id']);
			}
		}
		return true;
	}
	
	/**
	 * 查询家族中的长老数
	 * @param int $family_id
	 * @return number
	 */
	public function famileyElderNumber($family_id){
		return FamilyMemberModel::model()->getMemberCount($family_id, FAMILY_ROLE_ELDER);
	}
	
	/**
	 * 查询家族中的管理员数
	 * @param int $family_id
	 * @return number
	 */
	public function familyAdministratorNumber($family_id){
		return FamilyMemberModel::model()->getMemberCount($family_id, FAMILY_ROLE_ADMINISTRATOR);
	}
	
	/**
	 * 查询家族中的成员数
	 * @param int $family_id
	 * @return number
	 */
	public function familyMemberNumber($family_id){
		return FamilyMemberModel::model()->getMemberCount($family_id);
	}

	/**
	 * 获取家族所有角色名称或某个家族角色名称
	 * @param int $role_id
	 * @return string | array
	 */
	public function getRole($role_id = -1){
		$roles = array(
			0 => '普通族员',
			FAMILY_ROLE_OWNER => '家族长',
			FAMILY_ROLE_ELDER => '家族长老',
			FAMILY_ROLE_ADMINISTRATOR => '家族管理',
		);
		if($role_id == -1) return $roles;
		if(!in_array(intval($role_id), array_keys($roles))) return $roles[0];
		return $roles[$role_id];
	}
	
	/**
	 * 获取加入条件
	 * @return multitype:string
	 */
	public function getJoinCondition(){
		return array(
			'-3'	=> '禁止任何人加入家族',
			'-2'	=> '只允许主播申请加入',
			'-1'	=> '允许开放加入，无需审批',
			'0'		=> '任何人都可以申请加入',
			'3'		=> '等级绅士03以上可以申请加入',
			'4'		=> '等级绅士04以上可以申请加入',
			'5'		=> '等级绅士05以上可以申请加入',
			'6'		=> '等级绅士06以上可以申请加入',
			'7'		=> '等级绅士07以上可以申请加入',
			'8'		=> '等级富豪08以上可以申请加入',
			'9'		=> '等级富豪09以上可以申请加入',
			'10'	=> '等级富豪10以上可以申请加入',
			'11'	=> '等级富豪11以上可以申请加入',
			'12'	=> '等级富豪12以上可以申请加入',
			'13'	=> '等级富豪13以上可以申请加入',
			'14'	=> '等级富豪14以上可以申请加入',
			'15'	=> '等级富豪15以上可以申请加入',
			'16'	=> '等级男爵16以上可以申请加入',
		);
	}
	
	/**
	 * 获取发帖条件
	 * @return multitype:string
	 */
	public function getPostCondition(){
		return array(
			'-1'	=> '禁止所有家族成员发贴',
			'0'		=> '所有家族成员可以发帖',	
			'3'		=> '只允许绅士03以上的家族成员发帖',
			'4'		=> '只允许绅士04以上的家族成员发帖',
			'5'		=> '只允许绅士05以上的家族成员发帖',
			'6'		=> '只允许绅士06以上的家族成员发帖',
			'7'		=> '只允许绅士07以上的家族成员发帖',
			'8'		=> '只允许富豪08以上的家族成员发帖',
			'9'		=> '只允许富豪09以上的家族成员发帖',
		);
	}
	
	/**
	 * 获取回帖条件
	 * @return multitype:string
	 */
	public function getReplyCondition(){
		return array(
			'-1'	=> '禁止所有家族成员回应他人发帖',
			'0'		=> '所有家族成员可以回应他人发帖',
			'3'		=> '只允许绅士03以上的家族成员回应他人发帖',
		);
	}
	
	/**
	 * 发帖
	 * @param int $family_id
	 * @param int $uid
	 * @param string $title
	 * @param string $content
	 * @return int
	 */
	public function sendThread($family_id, $uid, $title, $content){
		$member = $this->getMembersByUids($family_id, $uid);
		if(empty($member)){
			return $this->setNotice(0, Yii::t('family','you are not in family'), 0);
		}
		
		if(!(in_array($member[$uid]['role_id'], array(FAMILY_ROLE_OWNER, FAMILY_ROLE_ELDER, FAMILY_ROLE_ADMINISTRATOR)) || $member[$uid]['is_dotey'])){
			$extend = $this->getFamilyExtend($family_id);
			$config = json_decode($extend['config'], true);
			$condition = $this->getPostCondition();
			if($config['post_rank'] == -1){
				return $this->setNotice(1, $condition[$config['post_rank']], 0);
			}elseif($config['post_rank'] > 0){
				$userInfo = UserJsonInfoService::getInstance()->getUserInfo($uid, false);
				if($userInfo['rk'] < $config['post_rank']){
					return $this->setNotice(2, $condition[$config['post_rank']], 0);
				}
			}
		}
		
		$forum = BbsbaseService::getInstance()->getForumSub(FORUM_FROM_TYPE_FAMILY, $family_id);
		$forum = array_shift($forum);
		if($thread_id = BbsbaseService::getInstance()->releaseThread($forum['forum_sid'], $title, $uid, $content)){
			return $thread_id;
		}else{
			return $this->setNotice(3, Yii::t('common','System error'), 0);
		}
	}
	
	/**
	 * 回帖
	 * @param int $family_id
	 * @param int $thread_id
	 * @param int $uid
	 * @param string $content
	 * @param int $reply_id
	 * @return int
	 */
	public function sendPost($family_id, $thread_id, $uid, $content, $reply_id = 0){
		$member = $this->getMembersByUids($family_id, $uid);
		if(empty($member)){
			return $this->setNotice(0, Yii::t('family','you are not in family'), 0);
		}
		
		if(!(in_array($member[$uid]['role_id'], array(FAMILY_ROLE_OWNER, FAMILY_ROLE_ELDER, FAMILY_ROLE_ADMINISTRATOR)) || $member[$uid]['is_dotey'])){
			$extend = $this->getFamilyExtend($family_id);
			$config = json_decode($extend['config'], true);
			$condition = $this->getPostCondition();
			if($config['reply_rank'] == -1){
				return $this->setNotice(1, $condition[$config['reply_rank']], 0);
			}elseif($config['reply_rank'] > 0){
				
				if($userInfo['rk'] < $config['reply_rank']){
					return $this->setNotice(2, $condition[$config['reply_rank']], 0);
				}
			}
		}
		
		if($post_id = BbsbaseService::getInstance()->releasePost($uid, $thread_id, $content, $reply_id)){
			return $post_id;
		}else{
			return $this->setNotice(3, Yii::t('common','System error'), 0);
		}
	}
	
	/**
	 * 获得某家族的家族主播在直播列表
	 * @param int $uid
	 * @param int $family_id
	 * @param int $page
	 * @param int $pageSize
	 * @param array $dotey_uids
	 * @return array('count' => 0, 'list' => array())
	 */
	public function getLivingDotey($uid, $family_id, $dotey_uids = array()){
		if(empty($dotey_uids)){
			$doteys = $this->getDoteyMembersByFamily($family_id);
			$dotey_uids = array_keys($doteys);
		}
		if(empty($dotey_uids)) return array('count' => 0, 'list' => array());
		
		$archives = new ArchivesService();
		$all = $archives->getLivingArchives($uid);
		$living_dotey = array_keys($archives->buildDataByIndex($all['living'], 'uid'));
		$dotey_uids = array_intersect($living_dotey, $dotey_uids);
		if(empty($dotey_uids)) return array('count' => 0, 'list' => array());
		$count = count($dotey_uids);
// 		$dotey_uids = array_slice($dotey_uids, ($page-1)*$pageSize, $pageSize);
// 		if(empty($dotey_uids)) return array('count' => $count, 'list' => array());
		
		$users = UserJsonInfoService::getInstance()->getUserInfos($dotey_uids, false);
		$userService = new UserService();
		$living = array();
		foreach($all['living'] as $k=>$_live){
			if(in_array($_live['uid'], $dotey_uids)){
				$array = $all['living'][$k];
				$array['ut'] = $users[$_live['uid']]['ut'];
				$array['nk'] = $users[$_live['uid']]['nk'];
				$array['rk'] = intval($users[$_live['uid']]['rk']);
				$array['dk'] = intval($users[$_live['uid']]['dk']);
				$array['pic'] = $userService->getUserAvatar($_live['uid'], 'small', isset($users[$_live['uid']]['atr']) ? $users[$_live['uid']]['atr'] : array());
				$living[] = $array;
			}
		}
		return array('count' => $count, 'list' => $living);
	}
	
	/**
	 * 家族查询
	 * 
	 * @author supeng
	 * @param array $condition
	 * @param int $offset
	 * @param int $limit
	 * @param boolean $isLimit
	 * @return array $data
	 */
	public function searchFamily(Array $condition = array(), $offset = 0, $limit = 20, $isLimit = true){
		if(!empty($condition['username']) || !empty($condition['nickname']) || !empty($condition['realname'])){
			$UserService = new UserService();
			$info = $UserService->searchUserList($offset,$limit,$condition,false);
			if($info['uids']){
				$condition['uids'] = $info['uids'];
			}else{
				return array('count'=>0,'list'=>array());
			}
		}
		
		$model = new FamilyModel();
		$data = $model->searchFamily($condition, $offset, $limit, $isLimit);
		if ($data['list']){
			$data['list'] = $this->arToArray($data['list']);
		}
		return $data;
	}
	
	/**
	 * 查询操作记录
	 * @author supeng
	 * @param array $condition
	 * @param unknown_type $offset
	 * @param unknown_type $limit
	 * @param unknown_type $isLimit
	 * @return multitype:number multitype: |Ambigous <multitype:, multitype:NULL , multitype:Ambigous <NULL, unknown, multitype:unknown Ambigous <unknown, NULL> , mixed, multitype:, multitype:unknown > Ambigous <string, unknown, mixed> >
	 */
	public function searchOPRecords(Array $condition = array(), $offset = 0, $limit = 20, $isLimit = true){
		if(!empty($condition['username']) || !empty($condition['nickname']) || !empty($condition['realname'])){
			$UserService = new UserService();
			$info = $UserService->searchUserList($offset,$limit,$condition,false);
			if($info['uids']){
				$condition['uids'] = $info['uids'];
			}else{
				return array('count'=>0,'list'=>array());
			}
		}
	
		$model = new FamilyOperateRecordsModel();
		$data = $model->searchOPRecords($condition, $offset, $limit, $isLimit);
		if ($data['list']){
			$data['list'] = $this->arToArray($data['list']);
		}
		return $data;
	}
	/**
	 * 保存家族全局配置信息
	 * 
	 * @author supeng
	 * @param array $c_value
	 * @return boolean
	 */
	public static function saveSetting(Array $c_value = array()){
		if (!$c_value){
			$c_value = FamilyService::getDefaultSetting();
		}
		$service = new WebConfigService();
		$conf['c_key'] = FamilyService::DEFAULT_CONFIG_KEY;
		$conf['c_type'] = 'array';
		$conf['c_value'] = $c_value;
		if($service->saveWebConfig($conf)){
			return true;
		}
		return false;
	}
	
	/**
	 * 获取家族全局配置
	 * 
	 * @author supeng
	 * @return array
	 */
	public static function getSetting(){
		$service = new WebConfigService();
		$setInfo = $service->getWebConfig(FamilyService::DEFAULT_CONFIG_KEY);
		if(!$setInfo){
			$setInfo = self::getDefaultSetting();
		}else{
			$setInfo = $setInfo['c_value'];
		}
		return $setInfo;
	}
	
	/**
	 * 获取家族全局配置的默认值
	 * 
	 * @author supeng
	 * @return array
	 */
	public static function getDefaultSetting(){
		$info = array();
		$info['global_enable'] = true;
		$info['apply_enable'] = true;
		$info['urank'] = 10;
		$info['drank'] = 10;
		$info['create_price'] = 1000;
		$info['medal_price'] = 200;
		$info['update_medal_price'] = 20000;
		$info['focus_quit'] = 60;
		return $info;
	}
	
	/**
	 * 家族申请状态
	 * 
	 * @author supeng
	 * @return array $status 
	 */
	public static function getFamilyStatus(){
		$status = array();
		$status[FAMILY_STATUS_PREPARE] = '筹备邀请成功';
		$status[FAMILY_STATUS_SUCCESS] = '审核通过';
		$status[FAMILY_STATUS_WAIT] = '待审核';
		$status[FAMILY_STATUS_REFUSE] = '拒绝审核';
		$status[FAMILY_STATUS_UNPREPARE] = '筹备邀请失败';
		return $status;
	}
	
	/**
	 * 获取家族显示状态
	 *
	 * @author supeng
	 * @return array ; 
	 */
	public static function getFamilyHidden(){
		return array(0=>'显示',1=>'隐藏');
	}
	
	/**
	 * 获取家族的启用状态
	 * @author supeng
	 * @return array 
	 */
	public static function getFamilyForbidden(){
		return array(0=>'启用',1=>'停封');
	}
	
	/**
	 * 获取家族的启用状态
	 * @author supeng
	 * @return array
	 */
	public static function getFamilySign(){
		return array(0=>'未签约',1=>'已签约');
	}
	
	/**
	 * 获取家族签约状态
	 * @author supeng
	 * @return array
	 */
	public static function getFamilySignStatus(){
		return array(0=>'待处理',1=>'成功',-1=>'拒绝');
	}
	
	/**
	 * 获取操作记录类型
	 * @author supeng
	 * @return array
	 */
	public static function getOPTypes(){
		return array(
				0 => '审核通过',
				1 => '拒绝审核',
				2 => '隐藏',
				3 => '显示',
				4 => '封停',
				5 => '启用',
				6 => '解散家族',
				7 => '家族签约成功',
				8 => '拒绝家族签约',
			);
	}
	
	public function getFamilyUploadPath(){
		return 'tmp'.DIR_SEP.'family';
	}
	
	/**
	 * 上传家族封面图
	 * 
	 * @author supeng
	 * @param string $name
	 * @return mixed|Ambigous <string, unknown>
	 */
	public function uploadFamilyCover($name = 'cover'){
		$imgFile = CUploadedFile::getInstanceByName($name);
		if(!empty($imgFile)){
			if(!($imgFile->getType() == 'image/jpeg' || $imgFile->getType() == 'image/pjpeg')){
				return $this->setNotice(7, '家族封面必须上传jpg格式', '');
			}
			if($imgFile->getSize() > 2*1024*1024){
				return $this->setNotice(8, '家族封面上传大小不能超过2MB', '');
			}
		}
		return $this->uploadSingleImages($name, $this->getFamilyUploadPath());
	}
}
