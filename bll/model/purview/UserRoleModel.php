<?php
/**
 * 用户所具有的与基本角色关联的数据，即用户拥有的角色数据
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: UserRoleModel.php 16719 2013-11-26 06:51:29Z hexin $ 
 * @package
 */
class UserRoleModel extends PipiActiveRecord {
	
	public $uid;
	public $role_id;
	public $sub_id;
	public $role_name;
	public $role_type;
	public $is_use;
	
	public function rules() {
		return array(
			//array('is_use','in','range'=>array(0,1)),
			array('sub_id,role_id,uid','required'),	
		);
	}
	
	public function attributeLabels() {
		return array(
			'uid' => '用户ID',
			'role_id' => '角色ID',
			'is_use' => '是否可用',
			'role_name' => '角色名称',
			'role_type' => '角色类型'
		);
	}
	
	/**
	 * @param string $className
	 * @return UserRoleModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}

	public function tableName(){
		return '{{purview_userroles}}';
	}

	public function getDbConnection(){
		return Yii::app()->db_purview;
	}
	
	public function relations(){
		return array(
			'role' => array(self::BELONGS_TO, 'PurviewRoleModel', 'role_id'),
		);
	}
	
	public function primaryKey(){
		return array('uid', 'role_id', 'sub_id');
	}
	
	/**
	 * 获取用户所有角色的关联表数据
	 * @param int $uid 用户uid
	 * @return array
	 */
	public function getUserRoleIds($uid){
		if(intval($uid) < 1) return array();
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->addColumnCondition(array('uid' => $uid));
		$return = $this->findAll($criteria);
		$array = array();
		if($return){
			foreach($return as $r){
				$array[] = $r->getAttributes();
			}
		}
		return $array;
	}
	
	/**
	 * 获取用户某子系统或全部的所有角色
	 * @param int $uid 用户uid
	 * @param int $role_type 子系统或模块类型
	 * @param int $sub_id 子系统或模块id
	 * @return array
	 */
	public function getUserRoles($uid, $role_type = 0, $sub_id = 0){
		if(intval($uid) < 1) return array();
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->alias = 'ur';
		$criteria->addColumnCondition(array('ur.uid' => $uid));
		$criteria->addColumnCondition(array('role.is_use' => 1));
		if($role_type > 0) $criteria->addColumnCondition(array('role.role_type' => $role_type));
		if($sub_id > 0) $criteria->addColumnCondition(array('ur.sub_id'=>$sub_id));
		$return = $this->with('role')->findAll($criteria);
		$array = array();
		if($return){
			foreach($return as $r){
				$array[] = array_merge($r->role->getAttributes(), $r->getAttributes());
			}
		}
		return $array;
	}
	
	/**
	 * 批量获取某些用户某子系统或全部的所有角色
	 * @param array $uids 用户uid集合
	 * @param int $role_type 子系统或模块类型
	 * @param int $sub_id 子系统或模块id
	 * @return array
	 */
	public function getUsersRoles(array $uids, $role_type = 0, $sub_id = 0){
		if(empty($uids)) return array();
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->alias = 'ur';
		$criteria->join = 'LEFT JOIN web_purview_roles AS r ON ur.role_id = r.role_id';
		$criteria->select = "ur.*,r.role_name,r.role_type,r.is_use,r.description";
		$criteria->addInCondition('ur.uid', $uids);
		$criteria->addColumnCondition(array('r.is_use' => 1));
		if($role_type > 0) $criteria->addColumnCondition(array('r.role_type' => $role_type));
		if($sub_id > 0) $criteria->addColumnCondition(array('ur.sub_id' => $sub_id));
		$return = $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
		$array = array();
		if($return){
			foreach($return as $r){
				$array[$r['uid']][$r['role_id']] = $r;
			}
		}
		return $array;
	}
	
	/**
	 * 通过角色ID获取相关用户
	 * 
	 * @author supeng
	 * @param unknown_type $role_id
	 * @return multitype:|multitype:multitype: 
	 */
	public function getRoleUserByRoleId($role_id,$uid = null){
		if (!is_array($role_id)){
			$role_id = array($role_id);
		}
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('role_id', $role_id);
		if ($uid){
			$criteria->compare('uid', $uid);
		}
		return $this->findAll($criteria);
	}
	
	/**
	 * 删除用户角色
	 * @param int $uid
	 * @param array $role_ids
	 * @return int
	 */
	public function deleteUserRoles($uid, array $role_ids = array()){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->addColumnCondition(array('uid' => intval($uid)));
		if(!empty($role_ids)){
			$criteria->addInCondition('role_id', $role_ids);
		}
		return $this->deleteAll($criteria);
	}
	
	/**
	 * 删除角色的关联数据
	 * @param int $role_id
	 * @return int
	 */
	public function deleteRelationByRole($role_id){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->addColumnCondition(array('role_id' => $role_id));
		return $this->deleteAll($criteria);
	}
	
	/**
	 * 用户角色
	 *
	 * @author supeng
	 * @param Array $data
	 * @return CActiveDataProvider
	 */
	public function search(Array $data) {
		$criteria = $this->getCommandBuilder()->createCriteria();
		
		$uid = '';
		if(isset($data['user_info'])){
			$user_info = $data['user_info'];
			if (!is_numeric($user_info)){
				$username = $user_info;
				$userService = new UserService();
				if($uinfos = $userService->getVadidatorUser($username, USER_LOGIN_USERNAME)){
					$uid = $uinfos['uid'];
				}
			}else{
				$uid = intval($user_info);
			}
			
			if($uid){
				$criteria->addColumnCondition(array('uid' => $uid));
			}else{
				return array();
			}
		}
		
		$return = $this->with('role')->findAll($criteria);
		$array = array();
		if($return){
			foreach($return as $r){
				$array[] = array_merge($r->role->getAttributes(), $r->getAttributes());
			}
		}
		return $array;
	}
	
	/**
	 * 检查某用户是否具有该权限
	 * @param int $uid
	 * @param int $role_type
	 * @param int $sub_id
	 * @param array $purview_ids
	 * @return boolean
	 */
	public function checkPurview($uid, $role_type, $sub_id, array $purview_ids){
		$criteria = $this->getDbCriteria();
		$criteria->alias = 'ur';
		$criteria->select = 'ur.uid,r.role_id,r.sub_id,pr.purview_id';
		$criteria->join = ' LEFT JOIN {{purview_roles}} AS r ON r.role_id = ur.role_id LEFT JOIN {{purview_roleitem}} AS pr ON pr.role_id = ur.role_id';
		$criteria->condition = ' ur.uid = '.$uid.' AND ur.sub_id = '.$sub_id.' AND r.is_use = 1 AND r.role_type = '.$role_type.' AND pr.purview_id IN ('.implode(',', $purview_ids).')';
		$return = $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryRow();
		if(empty($return)) return false;
		else return $return;
	}
	
}