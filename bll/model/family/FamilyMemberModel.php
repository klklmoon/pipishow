<?php
/**
 * 家族成员
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: 2013-8-6 下午3:28:20 hexin $ 
 * @package
 */
class FamilyMemberModel extends PipiActiveRecord {
	/**
	 * 
	 * @param string $className
	 * @return FamilyMemberModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{family_member}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_family;
	}
	
	/**
	 * 
	 * @param int $family_id
	 * @param int $uid
	 * @return FamilyMemberModel
	 */
	public function findByUid($family_id, $uid){
		return $this->find('family_id = '.$family_id.' and uid='.$uid);
	}
	
	/**
	 * 获取我创建的和我加入的家族id
	 * @param int $uid
	 * @param boolean $hidden 是否包含已隐藏的家族
	 * @return array(create=(int),join=(array))
	 */
	public function getMyFamily($uid, $hidden = true){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->alias = 'm';
		$criteria->join = 'LEFT JOIN web_family as f ON m.family_id = f.id';
		$criteria->select = 'm.family_id, m.uid, f.uid as owner';
		$criteria->condition = 'm.uid = '.$uid.($hidden ? '': ' AND f.status >= 0 AND f.hidden = 0 AND f.forbidden = 0');
		return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
	}
	
	/**
	 * 获取成员人数
	 * @param int $family_id
	 * @param int $role_id
	 * @return number
	 */
	public function getMemberCount($family_id, $role_id = -1){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->select = 'count(*)';
		$criteria->condition = 'family_id='.$family_id.($role_id != -1 ? ' and role_id = ' . $role_id : '');
		return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryScalar();
	}
	
	/**
	 * 获取成员信息，可分页
	 * @param int $family_id
	 * @param int|array $role_id
	 * @param array $conditions
	 * @param boolean $pageEnable
	 * @param int $offset
	 * @param int $limit
	 * @return number
	 */
	public function getMembers($family_id, $role_id = -1, array $conditions = array(), $pageEnable = true, $offset = 0, $limit = 10, $order = ''){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->select = '*';
		$criteria->condition = 'family_id='.$family_id.(!is_array($role_id) && $role_id != -1 ? ' and role_id = ' . $role_id : '');
		if(isset($conditions['medal_enable'])){
			$criteria->condition .= ' and medal_enable = '.intval($conditions['medal_enable']);
		}
		if(isset($conditions['is_dotey'])){
			$criteria->condition .= ' and is_dotey = '.intval($conditions['is_dotey']);
		}
		if(isset($conditions['family_dotey'])){
			$criteria->condition .= ' and family_dotey = '.intval($conditions['family_dotey']);
		}
		if(is_array($role_id)){
			$criteria->condition .= ' and role_id in('.implode(',', $role_id).')';
		}elseif($role_id != -1){
			$criteria->condition .= ' and role_id ='.$role_id;
		}
		if($order){
			$criteria->order = $order;
		}
		if($pageEnable){
			$return['count'] = $this->count($criteria);
// 			$criteria->offset = $offset;
// 			$criteria->limit = $limit;
			$pages=new CPagination($return['count']);
			$pages->pageSize = $limit;
			$pages->applyLimit($criteria);
			$return['pages'] = $pages;
		}
		$return['list'] = $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
		return $return;
	}
	
	/**
	 * 根据uids查询某family里的成员信息
	 * @param int $family_id
	 * @param array $uids
	 * @return array
	 */
	public function getMembersByUids($family_id, array $uids){
		if(empty($uids)) return array();
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->condition = 'family_id='.$family_id.' and uid in ('.implode(',', $uids).')';
		return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
	}
	
	/**
	 * 根据uids查询他们所属的家族
	 * 
	 * @author supeng
	 * @param int $family_id
	 * @param array $uids
	 * @return array
	 */
	public function getMembersGroupByUids(array $uids){
		if(empty($uids)) return array();
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->condition = 'uid in ('.implode(',', $uids).')';
		return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
	}
	
	/**
	 * 根据uid查询所有家族的成员信息
	 * @param array $uid
	 * @return array
	 */
	public function getMembersByUid($uid){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->condition = 'uid = '.$uid;
		return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
	}
	
	/**
	 * 获取族徽成员的成员信息
	 * @param array $uids
	 * @return array
	 */
	public function getMedalMembers(array $uids){
		if(empty($uids)) return array();
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->condition = 'uid in ('.implode(',', $uids).') and medal_enable = 1';
		$criteria->group = 'uid';
		return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
	}
	
	/**
	 * 获取某用户所拥有的族徽
	 * @param int $uid
	 * @return array
	 */
	public function getMyMedals($uid){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->alias = 'm';
		$criteria->select = 'm.*, f.sign, f.level';
		$criteria->join = 'LEFT JOIN web_family AS f ON m.family_id = f.id';
		$criteria->condition = 'm.uid ='.$uid.' and m.have_medal = 1';
		$criteria->order='m.medal_enable desc, m.id asc';
		return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
	}
	
	/**
	 * 获取家族主播的成员信息
	 * @param array $uids
	 * @return array
	 */
	public function getDoteyMembers(array $uids){
		if(empty($uids)) return array();
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->condition = 'uid in ('.implode(',', $uids).') and family_dotey = 1';
		$criteria->group = 'uid';
		return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
	}
	
	/**
	 * 删除家族中的成员
	 * @param int $family_id
	 * @param array $uids
	 * @return boolean
	 */
	public function deleteMembers($family_id, array $uids){
		if(empty($uids)) return false;
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->condition = 'family_id='.$family_id.' and uid in ('.implode(',', $uids).')';
		if($this->getCommandBuilder()->createDeleteCommand($this->tableName(), $criteria)->execute())
			return true;
		else return false;
	}
	
	/**
	 * 删除家族中的所有成员，家族解散用
	 * @param int $family_id
	 * @return boolean
	 */
	public function deleteAllMembers($family_id){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->condition = 'family_id='.$family_id;
		if($this->getCommandBuilder()->createDeleteCommand($this->tableName(), $criteria)->execute())
			return true;
		else return false;
	}
	
	/**
	 * 设置家族成员的角色
	 * @param int $family_id
	 * @param array $uids
	 * @param int $role_id
	 * @return boolean
	 */
	public function updateRole($family_id, array $uids, $role_id){
		if(empty($uids)) return false;
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->condition = 'family_id='.$family_id.' and uid in ('.implode(',', $uids).')';
		if($this->getCommandBuilder()->createUpdateCommand($this->tableName(), array('role_id' => $role_id), $criteria)->execute())
			return true;
		else return false;
	}
	
	/**
	 * 解除家族成员的角色
	 * @param int $family_id
	 * @param array $uids
	 * @param int $role_id
	 * @return boolean
	 */
	public function deleteRole($family_id, array $uids){
		if(empty($uids)) return false;
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->condition = 'family_id='.$family_id.' and uid in ('.implode(',', $uids).')';
		if($this->getCommandBuilder()->createUpdateCommand($this->tableName(), array('role_id' => 0), $criteria)->execute())
			return true;
		else return false;
	}
	
	/**
	 * 设置家族主播身份
	 * @param int $family_id
	 * @param array $uids
	 * @param int $status 1为家族主播，0为非家族主播
	 * @return boolean
	 */
	public function updateFamilyDotey($family_id, array $uids, $status = 1){
		if(empty($uids)) return false;
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->condition = 'family_id='.$family_id.' and uid in ('.implode(',', $uids).')';
		if($this->getCommandBuilder()->createUpdateCommand($this->tableName(), array('family_dotey' => $status), $criteria)->execute())
			return true;
		else return false;
	}
}
