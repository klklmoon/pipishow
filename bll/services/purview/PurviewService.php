<?php
/**
 * 权限操作服务层
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: PurviewService.php 16160 2013-10-24 03:12:58Z hexin $ 
 * @package
 */

define('PURVIEW_ROLETYPE_ADMIN', 1); 	//秀场后台
define('PURVIEW_POLETYPE_SHOW', 2);		//主站
define('PURVIEW_POLETYPE_ARCHIVE', 4);  //秀场档期直播间
define('PURVIEW_POLETYPE_CWEB', 8);		//C站
define('PURVIEW_POLETYPE_FAMILY', 16);	//家族

class PurviewService extends PipiService{
	private static $instance;
	
	/**
	 * 返回PurviewService对象的单例
	 * @return PurviewService
	 */
	public static function getInstance(){
		if(self::$instance == null){
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	/**
	 * 网站后台保存权限项, 含有主键是修改，不含有主键是添加
	 * @param array $data = array([purview_id,] purview_name, sub_system, module, controller, action);
	 * @return boolean
	 */
	public function saveItem(array $data){
		if(empty($data)){
			return $this->setError(Yii::t('purview','The purview item can not empty'), false);
		}
		$itemModel = new PurviewItemModel();
		$data['is_use'] = 1;
		if(isset($data['range']) && is_array($data['range'])){
			$range = 0;
			foreach($data['range'] as $r){
				$range = $this->grantBit($range, (int)$r);
			}
			$data['range'] = $range;
		}
		if(isset($data['purview_id'])){
			if(intval($data['purview_id']) > 0) {
				$itemModel = $itemModel->findByPk(intval($data['purview_id']));
			}
			if(empty($itemModel)){
				return $this->setError(Yii::t('purview','The purview item does not exist'), false);
			}
			$op_desc = Yii::t('purview','edit purivew item');
		}else{
			$op_desc = Yii::t('purview','add purivew item');
		}
		$this->attachAttribute($itemModel, $data);
		if(!$itemModel->validate()){
			return $this->setNotices($itemModel->getErrors(), false);
		}
		$r = $itemModel->save();
		
		$record['op_role_id']	= 1; //超级管理员默认角色id为1
		$record['op_sub_id']	= 0;
		$record['op_desc']		= $op_desc;
		$record['purview_id']	= $itemModel->getPrimaryKey();
		$this->saveRecord($record);
		return $itemModel->getPrimaryKey();
	}
	
	/**
	 * 网站后台删除权限项
	 * @param int $id 权限项id
	 * @return int
	 */
	public function deleteItem($id){
		if(intval($id) < 1){
			return $this->setError(Yii::t('common','Parameters are wrong').'1', 0);
		}
		$itemModel = PurviewItemModel::model()->findByPk(intval($id));
		if(empty($itemModel)){
			return $this->setError(Yii::t('purview','The purview item does not exist'), 0);
		}
		$record['op_role_id'] = 1; //超级管理员默认角色id为1
		$record['op_sub_id'] = 0;
		$record['op_desc'] = Yii::t('purview','delete purivew item');
		$record['purview_id'] = $id;
		$this->saveRecord($record);
		$return = PurviewItemModel::model()->deleteItem($id);
		if($return){
			PurviewRoleItemModel::model()->deleteRelationByItem($id);
		}
		return $return;
	}
	
	/**
	 * 创建角色，后台创建的系统默认角色sub_id必须为-1，其他必须传入子系统或子模块的id作为权限角色的关联
	 * @param array $data = array([role_id,] role_name, role_type, sub_id, description);
	 * @param int $op_uid 操作人uid
	 * @param int $op_role_id 操作人角色id
	 * @param int $sub_id 子系统或模块id
	 * @return boolean
	 */
	public function saveRole(array $data, $op_uid, $op_role_id, $sub_id = 0){
		if(empty($data)){
			return $this->setError(Yii::t('purview','The purview role can not empty'), false);
		}
		$roleModel = new PurviewRoleModel();
		$data['is_use'] = 1;
		if(isset($data['role_id'])){
			if(intval($data['role_id']) > 0) {
				$roleModel = PurviewRoleModel::model()->findByPk(intval($data['role_id']));
			}
			if(empty($roleModel)){
				return $this->setError(Yii::t('purview','The purview role does not exist'), false);
			}
			$op_desc = Yii::t('purview','edit role');
		}else{
			$op_desc = Yii::t('purview','add role');
		}
		$this->attachAttribute($roleModel, $data);
		if(!$roleModel->validate()){
			
			return $this->setNotices($roleModel->getErrors(), false);
		}
		$r = $roleModel->save();
		
		$record['op_uid']		= $op_uid;
		$record['op_role_id']	= $op_role_id;
		$record['op_sub_id']	= $sub_id;
		$record['op_desc']		= $op_desc;
		$record['role_id']		= $roleModel->getPrimaryKey();
		$this->saveRecord($record);
		return $roleModel->getPrimaryKey();
	}
	
	/**
	 * 删除角色
	 * @param int $id 被操作角色id
	 * @param int $op_uid 操作人uid
	 * @param int $op_role_id 操作人角色id
	 * @param int $sub_id 子系统或模块id
	 * @return int
	 */
	public function deleteRole($id, $op_uid, $op_role_id, $sub_id = 0){
		if(intval($id) < 1){
			return $this->setError(Yii::t('common','Parameters are wrong').'2', 0);
		}
		$roleModel = PurviewRoleModel::model()->findByPk(intval($id));
		if(empty($roleModel)){
			return $this->setError(Yii::t('purview','The purview role does not exist'), 0);
		}
		$record['op_uid']		= $op_uid;
		$record['op_role_id']	= $op_role_id;
		$record['op_sub_id']	= $sub_id;
		$record['op_desc']		= Yii::t('purview','delete role');
		$record['role_id']		= $id;
		$this->saveRecord($record);
		$return = PurviewRoleModel::model()->deleteRole(intval($id));
		if($return){
			UserRoleModel::model()->deleteRelationByRole($id);
		}
		return $return;
	}
	
	/**
	 * 保存某角色选择的权限值
	 * @param int $role_id 被操作角色id
	 * @param array $item_ids 权限项id数组
	 * @param int $op_uid 操作人uid
	 * @param int $op_role_id 操作人角色id
	 * @param int $sub_id 子系统或模块id
	 * @return boolean
	 */
	public function saveRoleItem($role_id, array $item_ids, $op_uid, $op_role_id, $sub_id = 0){
		if(intval($role_id) < 1 || empty($item_ids)){
			return $this->setError(Yii::t('common','Parameters are wrong').'3', false);
		}
		$o_relations = PurviewRoleItemModel::model()->getRoleItemIds($role_id);
		$del_ids = $edit_ids = $add_ids = $ids = array();
		foreach($o_relations as $or){
			$ids[] = $or['purview_id'];
			if(!in_array($or['purview_id'], $item_ids)){
				$del_ids[] = intval($or['purview_id']);
			}else{
				$edit_ids[] = intval($or['purview_id']);
			}
		}
		$add_ids = array_diff($item_ids, $ids);
		$relations = $records = array();
		foreach($add_ids as $item_id){
			if(intval($item_id) > 0){
				$relations[] = array(
					'role_id'	=> $role_id,
					'purview_id'=> $item_id,
					'is_use'	=> 1
				);
			}
		}
		if(!empty($del_ids)) $this->deleteRoleItem($role_id, $del_ids, $op_uid, $op_role_id, $sub_id);
		if(!empty($relations)){
			foreach($relations as $rel){
				$relationModel = PurviewRoleItemModel::model()->findByRoleItem($rel['role_id'], $rel['purview_id']);
				if(empty($relationModel)) $relationModel = new PurviewRoleItemModel();
				$this->attachAttribute($relationModel, $rel);
				$relationModel->save();
				$records[] = array(
					'op_uid'		=> $op_uid,
					'op_role_id'	=> $op_role_id,
					'op_sub_id'		=> $sub_id,
					'op_desc'		=> Yii::t('purview','add role item relation'),
					'role_id'		=> $rel['role_id'],
					'purview_id'	=> $rel['purview_id'],
					'relation_id'	=> $relationModel->getPrimaryKey(),
				);
			}
			$this->saveRecords($records);
		}
		return true;
	}
	
	/**
	 * 删除某角色的权限，或某角色的某些权限
	 * @param int $role_id 被操作角色id
	 * @param array $item_ids 权限项id数组
	 * @param int $op_uid 操作人uid
	 * @param int $op_role_id 操作人角色id
	 * @param int $sub_id 子系统或模块id
	 * @return int
	 */
	public function deleteRoleItem($role_id, array $item_ids, $op_uid, $op_role_id, $sub_id = 0){
		if(intval($role_id) < 1){
			return $this->setError(Yii::t('common','Parameters are wrong').'4', 0);
		}
		$records = array();
		foreach($item_ids as $id){
			$relationModel = PurviewRoleItemModel::model()->findByRoleItem($role_id, $id);
			$records[] = array(
				'op_uid'		=> $op_uid,
				'op_role_id'	=> $op_role_id,
				'op_sub_id'		=> $sub_id,
				'op_desc'		=> Yii::t('purview','delete role item relation'),
				'role_id'		=> $role_id,
				'purview_id'	=> $id,
				'relation_id'	=> $relationModel->relation_id,
			);
		}
		$this->saveRecords($records);
		return PurviewRoleItemModel::model()->deleteRoleItems($role_id, $item_ids);
	}
	
	/**
	 * 给用户分配角色
	 * @param int $uid 被操作人uid
	 * @param array $role_ids 角色id数组
	 * @param int $op_uid 操作人uid
	 * @param int $op_role_id 操作人角色id
	 * @param int $sub_id 子系统或模块id
	 * @param int $role_type 
	 * @return boolean
	 */
	public function saveUserRoles($uid, array $role_ids, $op_uid, $op_role_id, $sub_id = 0, $role_type = 0){
		if(intval($uid) < 1 || empty($role_ids)){
			return $this->setError(Yii::t('common','Parameters are wrong').'5');
		}
		$o_roles = UserRoleModel::model()->getUserRoles($uid, $role_type, $sub_id);
		$del_ids = $edit_ids = $add_ids = $ids = array();
		foreach($o_roles as $or){
			$ids[] = $or['role_id'];
			if(!in_array($or['role_id'], $role_ids)){
				$del_ids[] = $or['role_id'];
			}else{
				$edit_ids[] = $or['role_id'];
			}
		}
		$add_ids = array_diff($role_ids, $ids);
		$relations = $records = array();
		foreach($add_ids as $ur_id){
			if(intval($ur_id) > 0){
				$relations[] = array(
					'uid'		=> $uid,
					'role_id'	=> $ur_id,
					'sub_id'	=> $sub_id
				);
				$records[] = array(
					'op_uid'	=> $op_uid,
					'op_role_id'=> $op_role_id,
					'op_sub_id' => $sub_id,
					'op_desc'	=> Yii::t('purview','add user roles'),
					'uid'		=> $uid,
					'role_id'	=> $ur_id,
				);
			}
		}
		if(!empty($del_ids)) $this->deleteUserRoles($uid, $del_ids, $op_uid, $op_role_id, $sub_id);
		if(!empty($add_ids)){
			$this->saveRecords($records);
			$return = UserRoleModel::model()->batchInsert($relations);
			return $return;
		}
		return true;
	}
	
	/**
	 * 删除用户的角色
	 * @param int $uid
	 * @param array $role_ids
	 * @param int $op_uid
	 * @param int $op_role_id
	 * @param int $sub_id
	 * @return int
	 */
	public function deleteUserRoles($uid, array $role_ids, $op_uid, $op_role_id, $sub_id = 0){
		if(intval($uid) < 1){
			return $this->setError(Yii::t('common','Parameters are wrong').'6', 0);
		}
		$records = array();
		foreach($role_ids as $id){
			$records[] = array(
				'op_uid'	=> $op_uid,
				'op_role_id'=> $op_role_id,
				'op_sub_id'	=> $sub_id,
				'op_desc'	=> Yii::t('purview','delete user roles'),
				'uid'		=> $uid,
				'role_id'	=> $id
			);
		}
		$this->saveRecords($records);
		$return = UserRoleModel::model()->deleteUserRoles($uid, $role_ids);
		return $return;
	}
	
	/**
	 * 存储权限操作记录
	 * @param array $data = array(op_uid, op_role_id, op_sub_id, op_desc, uid, sub_id, role_id, purview_id, relation_id); 带op前缀的是操作人，操作人角色，操作人所在子系统，操作说明，后面是被操作人uid, 被操作所在子系统id，被操作角色id，被操作权限项id，被操作权限关联id
	 * @return boolean
	 */
	public function saveRecord(array $data){
		$data['op_uid'] = isset($data['op_uid']) ? $data['op_uid'] : intval(Yii::app()->user->id);
		if(intval($data['op_uid']) < 1 || intval($data['op_role_id']) < 1){
			return $this->setError(Yii::t('common','Parameters are wrong').'7', false);
		}
		$data['params'] = isset($data['params'])? $data['params'] : (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '');
		$data['op_ip'] = Yii::app()->request->userHostAddress;
		$data['op_time'] = time();
		$recordModel = new PurviewRecordModel();
		$this->attachAttribute($recordModel, $data);
		if(!$recordModel->validate()){
			return $this->setNotices($recordModel->getErrors(), false);
		}
		return $recordModel->save();
	}
	
	/**
	 * 批量存储权限操作记录
	 * @param array $record = array(op_uid, op_role_id, op_sub_id, op_desc, uid, sub_id, role_id, purview_id, relation_id); 带op前缀的是操作人，操作人角色，操作人所在子系统，操作说明，后面是被操作人uid, 被操作所在子系统id，被操作角色id，被操作权限项id，被操作权限关联id
	 * @return boolean
	 */
	public function saveRecords(array $datas){
		$records = array();
		foreach($datas as $data){
			if(intval($data['op_uid']) > 0 || intval($data['op_role_id']) > 0){
				$data['params'] = isset($data['params'])? $data['params'] : (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '');
				$data['op_ip'] = Yii::app()->request->userHostAddress;
				$data['op_time'] = time();
				$records[] = $data;
			}
		}
		if(empty($records)){
			return $this->setError(Yii::t('common','Parameters are wrong').'8', false);
		}else{
			return PurviewRecordModel::model()->batchInsert($records);
		}
	}
	
	
	
	
	/**
	 * 获取某权限分组的可选权限项
	 * @param max $group
	 * @return array
	 */
	public function getItemsByGroups($groups){
		if(empty($groups)){
			return $this->setError(Yii::t('common','Parameters are wrong').'9', array());
		}
		if(!is_array($groups)) $groups = array($groups);
		return PurviewItemModel::model()->getAllBySub($groups);
	}
	
	/**
	 * 获取某套系统的可选权限项
	 * @param max $range
	 * @return array
	 */
	public function getItemsByRange($range){
		if(empty($range)){
			return $this->setError(Yii::t('common','Parameters are wrong').'10', array());
		}
		$items = $this->getAllItems();
		foreach($items as $k => $item){
			if(!$this->hasBit((int)$item['range'], (int)$range)){
				unset($items[$k]);
			}
		}
		return $items;
	}
	
	/**
	 * 获取某模块或子系统的所有角色
	 * @param string $sub_type 家族或分站的类型
	 * @param int $sub_id 家族或分站的ID
	 * @return array
	 */
	public function getRolesBySub($sub_type, $sub_id = 0){
		if(empty($sub_type)){
			return $this->setError(Yii::t('common','Parameters are wrong').'11', array());
		}
		return PurviewRoleModel::model()->getRoles($sub_type, $sub_id);
	}
	
	/**
	 * 获取某角色所有权限值
	 * @param int $role_id 角色id
	 * @return array
	 */
	public function getRoleItems($role_id){
		if(intval($role_id) < 1){
			return $this->setError(Yii::t('common','Parameters are wrong').'12', array());
		}
		return PurviewRoleItemModel::model()->getRoleItems($role_id);
	}
	
	/**
	 * 获取某些角色的所有权限值
	 * @param array $role_ids 角色id数组
	 * @return array
	 */
	public function getRolesItems(array $role_ids){
		if(empty($role_ids)){
			return $this->setError(Yii::t('common','Parameters are wrong').'13', array());
		}
		return PurviewRoleItemModel::model()->getRolesItems($role_ids);
	}
	
	/**
	 * 查询某用户在某子系统或模块内的所有角色
	 * @param int $uid 用户uid
	 * @param int $sub_type 子系统或模块类型
	 * @param int $sub_id 子系统或模块id
	 * @return array
	 */
	public function getUserRolesBySub($uid, $sub_type, $sub_id = 0){
		if(intval($uid) < 1){
			return $this->setError(Yii::t('common','Parameters are wrong').'14', array());
		}
		return UserRoleModel::model()->getUserRoles($uid, $sub_type, $sub_id);
	}
	
	/**
	 * 查询某些用户在某子系统或模块内的所有角色
	 * @param array $uid 用户uid集合
	 * @param int $sub_type 子系统或模块类型
	 * @param int $sub_id 子系统或模块id
	 * @return array
	 */
	public function getUsersRolesBySub(array $uids, $sub_type, $sub_id = 0){
		if(empty($uids)){
			return $this->setError(Yii::t('common','Parameters are wrong').'15', array());
		}
		return UserRoleModel::model()->getUsersRoles($uids, $sub_type, $sub_id);
	}
	
	/**
	 * 查询某用户的所有角色
	 * @param int $uid 用户uid
	 * @return array
	 */
	public function getUserRolesByUid($uid){
		if(intval($uid) < 1){
			return $this->setError(Yii::t('common','Parameters are wrong').'16', array());
		}
		return UserRoleModel::model()->getUserRoles($uid);
	}
	
	/**
	 * 根据角色ID获取相关用户
	 * 
	 * @author supeng
	 * @param unknown_type $role_id
	 * @return mix|Ambigous <multitype:, multitype:multitype:, multitype:, multitype:multitype: >
	 */
	public function getRoleUserByRoleId($role_id,$uid = null){
		if(empty($role_id)){
			return $this->setError(Yii::t('common','Parameters are wrong').'17', false);
		}
		return $this->arToArray(UserRoleModel::model()->getRoleUserByRoleId($role_id,$uid));
	}
	
	/**
	 * 获取权限操作记录
	 * @param int $pageSize
	 * @param int $page
	 * @param array $search = array(op_uid, uid, op_time); 操作人po_uid, 被操作人uid, 操作时间op_time = array($start, $end)
	 * @return array
	 */
	public function getRecords($pageSize = 20, $page = 1, array $search = array()){
		$pageSize = intval($pageSize) < 1 ? 20 : intval($pageSize);
		$page = intval($page) < 1 ? 1 : intval($page);
		return PurviewRecordModel::model()->getAll($pageSize, $page, $search);
	}
	
	/**
	 * 根据role_id获得角色信息
	 * @param int $id
	 * @return array
	 */
	public function getRole($id){
		return PurviewRoleModel::model()->getByPk($id);
	}
	
	/**
	 * 根据role_id数组获得角色信息
	 * @param array $ids
	 * @return array
	 */
	public function getRoles(array $ids){
		if(empty($ids)) return array();
		return PurviewRoleModel::model()->getByPks($ids);
	}
	
	/**
	 * 通过角色名来获取角色信息
	 * @param string $role_name
	 */
	public function getRoleByName($role_name){
		if(empty($role_name)) return array();
		$roleModel = new PurviewRoleModel();
		return $roleModel->getRoleByName($role_name);
	}
	
	/**
	 * 根据item_id获得权限项
	 * @param int $id
	 * @return array
	 */
	public function getItem($id){
		return PurviewItemModel::model()->getByPk($id);
	}
	
	/**
	 * 根据item_id数组获得权限项
	 * @param array $ids
	 * @return array
	 */
	public function getItems(array $ids){
		if(empty($ids)) return array();
		return PurviewItemModel::model()->getByPks($ids);
	}
	
	/**
	 * 检查某用户是否具有操作权限
	 * @param int $uid
	 * @param int $role_type
	 * @param int $sub_id
	 * @param string $action
	 * @param string $controller
	 * @param string $module
	 * @return boolean
	 */
	public function checkPurview($uid, $role_type, $sub_id, $action, $controller, $module = null){
		if(intval($uid) <= 0) return false;
		$module = isset($module) ? $module : '';
		$items = PurviewItemModel::model()->getItemsByCondition($action, $controller, $module);
		if(empty($items)){
			if($role_type == PURVIEW_ROLETYPE_ADMIN) return false;
			else return true;
		}
		return UserRoleModel::model()->checkPurview($uid, $role_type, $sub_id, array_keys($this->buildDataByIndex($items, 'purview_id')));
	}
	
	/**
	 * 返回权限的所有应用子系统范围
	 * @return array
	 */
	public function getPurviewRange(){
		return array(
			PURVIEW_ROLETYPE_ADMIN	=> '秀场后台',
			PURVIEW_POLETYPE_SHOW	=> '主站',
			PURVIEW_POLETYPE_ARCHIVE=> '档期直播间',
			PURVIEW_POLETYPE_CWEB	=> 'C站',
			PURVIEW_POLETYPE_FAMILY => '家族',
		);
	}
	
	/**
	 * 返回所有权限项的分组
	 * @return array
	 */
	public function getPurviewGroups(){
		return PurviewItemModel::model()->getAllGroups();
	}
	
	/**
	 * 返回所有的权限项
	 * @return array
	 */
	public function getAllItems(){
		return PurviewItemModel::model()->getAllItems();
	}
	
	/**
	 * 返回该权限项所属的子系统
	 * @param int $range
	 * @return array
	 */
	public function checkRange($range){
		$range = intval($range);
		$array = array();
		if($range < 1) return $array;
		if($range & PURVIEW_ROLETYPE_ADMIN) $array[] = PURVIEW_ROLETYPE_ADMIN;
		if($range & PURVIEW_POLETYPE_SHOW) $array[] = PURVIEW_POLETYPE_SHOW;
		if($range & PURVIEW_POLETYPE_ARCHIVE) $array[] = PURVIEW_POLETYPE_ARCHIVE;
		if($range & PURVIEW_POLETYPE_CWEB) $array[] = PURVIEW_POLETYPE_CWEB;
		if($range & PURVIEW_POLETYPE_FAMILY) $array[] = PURVIEW_POLETYPE_FAMILY;
		return $array;
	}
	
	
	/**
	 * 角色搜索
	 * 
	 * @author supeng
	 * @param array $data
	 * @return CActiveDataProvider
	 */
	public function roleSearch(Array $data = array()){
		$roleModel = new PurviewRoleModel();
		$this->attachAttribute($roleModel, $data);
		$dataProvider = $roleModel->search();
		return $dataProvider;
	}
	
	/**
	 * 权限项搜索
	 *
	 * @author supeng
	 * @param array $data
	 * @return CActiveDataProvider
	 */
	public function purviewItemSearch(Array $data = array()){
		$purviewItemModel = new PurviewItemModel();
		$this->attachAttribute($purviewItemModel, $data);
		$dataProvider = $purviewItemModel->search();
		return $dataProvider;
	}
	
	/**
	 * 角色权限项搜索
	 *
	 * @author supeng
	 * @param array $data
	 * @return CActiveDataProvider
	 */
	public function rolesPurviewSearch(Array $data = array()){
		$purviewRoleItemModel = new PurviewRoleItemModel();
		$this->attachAttribute($purviewRoleItemModel, $data);
		$dataProvider = $purviewRoleItemModel->search();
		return $dataProvider;
	}
	
	/**
	 * 用户角色关联项搜索
	 * 
	 * @author supeng
	 * @param array $data
	 * @return CActiveDataProvider
	 */
	public function userRoleSearch(Array $data = array()){
		$userRoleModel = new UserRoleModel();
		$dataProvider = $userRoleModel->search($data);
		return $dataProvider;
	}
}