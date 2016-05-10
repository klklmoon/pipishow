<?php
/**
 * 基本角色与基本权限项关联的数据
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: PurviewRoleItemModel.php 9401 2013-05-01 05:07:53Z hexin $ 
 * @package
 */
class PurviewRoleItemModel extends PipiActiveRecord {
	
	public $relation_id;
	public $role_id;
	public $purview_id;
	public $is_use;
	
	public $purview_name;
	public $group;
	public $is_tree_display;
	
	public $role_name;
	public $role_type;
	public $sub_id;
	
	/**
	 * @param string $className
	 * @return PurviewRoleItemModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}

	public function tableName(){
		return '{{purview_roleitem}}';
	}

	public function getDbConnection(){
		return Yii::app()->db_purview;
	}
	
	public function relations(){
		return array(
			'item' => array(self::BELONGS_TO, 'PurviewItemModel', 'purview_id'),
		);
	}
	
	public function rules(){
		return array(
			array('role_id,purview_id,is_use', 'required'),
			array('is_use', 'in', 'range'=>array(0,1)),
		);
	}
	
	public function attributeLabels(){
		return array(
			'relation_id'	=> '关系ID',
			'purview_id'	=> '权限ID',
			'is_use'		=> '是否可用',
			'role_id'		=> '角色ID',
			'purview_name'	=> '权限名称',
			'purview_id'	=> '权限ID',
			'is_tree_display' => '菜单显示',
			'group'			=> '权限分组',
			'role_name'		=> '角色名称',
			'sub_id'		=> '子系统ID',
		);
	}
	
	/**
	 * 获取某角色权限值关系数据
	 * @param int $role_id
	 * @return array
	 */
	public function getRoleItemIds($role_id){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->addColumnCondition(array('is_use' => 1));
		$criteria->addColumnCondition(array('role_id' => intval($role_id)));
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
	 * 获取某角色权限值
	 * @param int $role_id
	 * @return array
	 */
	public function getRoleItems($role_id){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->addColumnCondition(array($this->getTableAlias().'.is_use' => 1));
		$criteria->addColumnCondition(array('role_id' => intval($role_id)));
		$criteria->addColumnCondition(array('item.is_use' => 1));
		$return = $this->with('item')->findAll($criteria);
		$array = array();
		if($return){
			foreach($return as $r){
				$array[] = array_merge($r->item->getAttributes(), $r->getAttributes());
			}
		}
		return $array;
	}
	
	/**
	 * 获取某些角色的权限值
	 * @param array $role_ids
	 * @return array
	 */
	public function getRolesItems(array $role_ids){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->addColumnCondition(array($this->getTableAlias().'.is_use' => 1));
		$criteria->addInCondition('role_id', $role_ids);
		$criteria->addColumnCondition(array('item.is_use' => 1));
		$return = $this->with('item')->findAll($criteria);
		$array = array();
		if($return){
			foreach($return as $r){
				$array[] = array_merge($r->item->getAttributes(), $r->getAttributes());
			}
		}
		return $array;
	}
	
	/**
	 * 删除权限角色关联
	 * @param int $role_id
	 * @param array $item_ids
	 * @return int
	 */
	public function deleteRoleItems($role_id, array $item_ids = array()){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->addColumnCondition(array('role_id' => intval($role_id)));
		if(!empty($item_ids)){
			$criteria->addInCondition('purview_id', $item_ids);
		}
		return $this->updateAll(array('is_use' => 0), $criteria);
	}
	
	/**
	 * 删除某权限项的所有关联数据
	 * @param int $item_id
	 * @return int
	 */
	public function deleteRelationByItem($item_id){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->addColumnCondition(array('purview_id' => intval($item_id)));
		return $this->updateAll(array('is_use' => 0), $criteria);
	}
	
	/**
	 * 查询关联表的id
	 * @param int $role_id
	 * @param int $item_id
	 * @return PurviewRoleItemModel
	 */
	public function findByRoleItem($role_id, $item_id){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->addColumnCondition(array('role_id' => intval($role_id)));
		$criteria->addColumnCondition(array('purview_id' => intval($item_id)));
		return $this->find($criteria);
	}
	
	/**
	 * 角色权限搜索
	 * 
	 * @author supeng
	 * @return CActiveDataProvider
	 */
	public function search() {
		$criteria = $this->getDbCriteria();
		$criteria->select = 'r.role_id,r.role_name,r.role_type,r.sub_id,ri.relation_id,ri.is_use,pi.purview_name,ri.purview_id,pi.group,pi.is_tree_display';
		$criteria->alias = 'ri';
		$criteria->order = 'ri.relation_id DESC ';
		$criteria->join = ' LEFT JOIN  {{purview_roles}} r on r.role_id = ri.role_id LEFT JOIN {{purview_items}} pi on pi.purview_id=ri.purview_id';
		
		if(isset($this->role_id) && !empty($this->role_id)){
			$criteria->condition .= ' ri.role_id = :role_id ';
			$criteria->params += array(':role_id'=>$this->role_id);
		}
		
		if(isset($this->relation_id) && !empty($this->relation_id)){
			$criteria->condition .= ($criteria->condition  ? 'AND' : '') . ' ri.relation_id= :relation_id ';
			$criteria->params += array(':relation_id'=>$this->relation_id);
		}
		
		if(isset($this->is_use) && !empty($this->is_use)){
			$criteria->condition .= ($criteria->condition  ? 'AND' : '') . ' ri.is_use = :is_use ';
			$criteria->params += array(':is_use'=>$this->is_use);
		}
		
		if(isset($this->purview_id) && !empty($this->purview_id)){
			$criteria->condition .= ($criteria->condition  ? 'AND' : '') . ' ri.purview_id = :purview_id ';
			$criteria->params += array(':purview_id'=>$this->purview_id);
		}
		
		if(isset($this->purview_name) && !empty($this->purview_name)){
			$criteria->condition .= ($criteria->condition  ? 'AND' : '') . ' pi.purview_name= :purview_name ';
			$criteria->params += array(':purview_name'=>$this->purview_name);
		}
		
		if(isset($this->group) && !empty($this->group)){
			$criteria->condition .= ($criteria->condition  ? 'AND' : '') . ' pi.group= :group ';
			$criteria->params += array(':group'=>$this->group);
		}
		
		return new CActiveDataProvider('PurviewRoleItemModel', array(
			'criteria'=>$criteria,
		));
	}
}