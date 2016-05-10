<?php
/**
 * 基本角色数据
 * the last known user to change this file in the repository  <$LastChangedBy: supeng $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: PurviewRoleModel.php 9974 2013-05-10 05:43:13Z supeng $ 
 * @package
 */
class PurviewRoleModel extends PipiActiveRecord {
	
	public $role_id;
	public $role_name;
	public $role_type;
	public $sub_id;
	public $is_use;
	
	/**
	 * @param string $className
	 * @return PurviewRoleModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}

	public function tableName(){
		return '{{purview_roles}}';
	}

	public function getDbConnection(){
		return Yii::app()->db_purview;
	}
	
	public function primaryKey(){
		return 'role_id';
	}
	
	public function rules(){
		return array(
			array('role_name,description', 'filter', 'filter'=>array(new CHtmlPurifier(),'purify')),
			array('role_name,role_type,sub_id,description', 'required'),
			array('role_name', 'length', 'min'=>1, 'max'=>90),
			array('is_use', 'in', 'range'=>array(0,1)),
			array('description', 'length', 'min'=>0, 'max'=>255),
		);
	}
	
	public function attributeLabels(){
		return array(
			'role_name'		=> '角色名称',
			'role_type'		=> '角色类型',
			'description'	=> '角色描述',
			'is_use'		=> '是否可用',
			'role_id'		=> '角色标识',
			'sub_id'		=> '作用ID',
		);
	}

	/**
	 * 获取有效角色
	 * @param int $id
	 * @return array
	 */
	public function getByPk($id){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->addColumnCondition(array('is_use' => 1));
		$return = $this->findByPk($id, $criteria);
		if(!$return) return array();
		return $return->getAttributes();
	}
	
	/**
	 * 批量获取有效角色
	 * @param array $ids
	 * @return array
	 */
	public function getByPks(array $ids){
		if(empty($ids)) return array();
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->addColumnCondition(array('is_use' => 1));
		$return = $this->findAllByPk($ids, $criteria);
		$array = array();
		if($return){
			foreach($return as $r){
				$array[] = $r->getAttributes();
			}
		}
		return $array;
	}
	
	/**
	 * 删除角色
	 * @param int $role_id
	 * @return int
	 */
	public function deleteRole($role_id){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->addColumnCondition(array('role_id' => intval($role_id)));
		return $this->updateAll(array('is_use' => 0), $criteria);
	}
	
	/**
	 * 获取某模块或子系统的所有角色
	 * @param string $sub_type 家族或分站的类型
	 * @param int $sub_id 家族或分站的ID
	 * @return array
	 */
	public function getRoles($role_type, $sub_id = 0){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->addColumnCondition(array('is_use' => 1));
		$criteria->addColumnCondition(array('role_type' => $role_type));
		$sub_ids = array(-1);
		if($sub_id > 0){
			$sub_ids[] = $sub_id;
		}
		$criteria->addInCondition('sub_id', $sub_ids);
		$return = $this->findAll($criteria);
		$array = array();
		if($return){
			foreach($return as $r){
				$array[] = $r->getAttributes();
			}
		}
		return $array;
	}
	
	public function getRoleByName($roleName,$role_type = 0){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->addColumnCondition(array('is_use' => 1));
		$criteria->addColumnCondition(array('role_name' => $roleName));
		if($role_type > 0) $criteria->addColumnCondition(array('role_type'=>$role_type));
		return $this->find($criteria)->getAttributes();
	}
	
	/**
	 * 角色查询相关
	 * 
	 * @author supeng
	 * @return CActiveDataProvider
	 */
	public function search()
	{
		$criteria= $this->getDbCriteria();
		$criteria->compare('role_id',$this->role_id);
		$criteria->compare('role_name',$this->role_name,true);
		$criteria->compare('role_type',$this->role_type);
		$criteria->compare('is_use',$this->is_use);
		$criteria->compare('sub_id',$this->sub_id,true);
	
		return new CActiveDataProvider('PurviewRoleModel', array(
			'criteria'=>$criteria,
		));
	}
}