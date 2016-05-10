<?php
/**
 * 基本权限项数据
 * the last known user to change this file in the repository  <$LastChangedBy: supeng $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: PurviewItemModel.php 8878 2013-04-19 10:20:42Z supeng $ 
 * @package
 */
class PurviewItemModel extends PipiActiveRecord {
	
	public $purview_id;
	public $purview_name;
	public $range;
	public $group;
	public $module;
	public $controller;
	public $action;
	public $is_use;
	public $is_tree_display;
	
	/**
	 * @param string $className
	 * @return PurviewItemModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}

	public function tableName(){
		return '{{purview_items}}';
	}

	public function getDbConnection(){
		return Yii::app()->db_purview;
	}
	
	public function primaryKey(){
		return 'purview_id';
	}
	
	public function rules(){
		return array(
			array('purview_name,group', 'filter', 'filter'=>array(new CHtmlPurifier(),'purify')),
			array('purview_name,group,action,controller,is_tree_display', 'required'),
			array('purview_name,group', 'length', 'min'=>1, 'max'=>90),
			array('is_use', 'in', 'range'=>array(0,1)),
			array('purview_name','unique'),
		);
	}
	
	public function attributeLabels(){
		return array(
			'purview_id'	=> '权限ID',
			'purview_name'	=> '权限名称',
			'range'			=> '权限范围',
			'group'			=> '权限分组',
			'module'		=> '模块',
			'controller'	=> '控制器',
			'action'		=> '动作',
			'is_use'		=> '是否可用',
			'is_tree_display' => '菜单显示'
		);
	}
	
	/**
	 * 获取有效权限项
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
	 * 批量获取有效权限项
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
	 * 删除权限项
	 * @param int $item_id
	 * @return int
	 */
	public function deleteItem($item_id){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->addColumnCondition(array('purview_id' => intval($item_id)));
		return $this->updateAll(array('is_use' => 0), $criteria);
	}
	
	/**
	 * 获取某权限分组的可选权限项
	 * @param string $groups
	 * @return array
	 */
	public function getAllBySub(array $groups){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->addColumnCondition(array('is_use' => 1));
		$criteria->addInCondition('`group`', $groups);
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
	 * 权限项的相关查询
	 *
	 * @author supeng
	 * @return CActiveDataProvider
	 */
	public function search()
	{
		$criteria=$this->getDbCriteria();
		$criteria->compare('purview_id',$this->purview_id);
		$criteria->compare('purview_name',$this->purview_name,true);
		$criteria->compare('`group`',$this->group,true);
		$criteria->compare('module',$this->module,true);
		$criteria->compare('controller',$this->controller,true);
		$criteria->compare('action',$this->action,true);
		$criteria->compare('is_use',$this->is_use);
		$criteria->compare('is_tree_display',$this->is_tree_display);
	
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	/**
	 * 根据路由查找对应的权限项
	 * @param string $action
	 * @param string $controller
	 * @param string $module
	 * @return array
	 */
	public function getItemsByCondition($action, $controller, $module = ''){
		$return = $this->findAllByAttributes(array('is_use' => 1, 'module' => $module, 'controller' => $controller, 'action' => $action));
		$array = array();
		if($return){
			foreach($return as $r){
				$array[] = $r->getAttributes();
			}
		}
		return $array;
	}
	
	/**
	 * 返回所有权限组
	 * @return array
	 */
	public function getAllGroups(){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->addColumnCondition(array('is_use' => 1));
		$criteria->select = '`group`';
		$criteria->distinct = true;
		$return = $this->findAll($criteria);
		$array = array();
		if($return){
			foreach($return as $r){
				$array[] = $r->getAttribute('group');
			}
		}
		return $array;
	}
	
	/**
	 * 返回所有权限项
	 * @return array
	 */
	public function getAllItems(){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->addColumnCondition(array('is_use' => 1));
		$return = $this->findAll($criteria);
		$array = array();
		if($return){
			foreach($return as $r){
				$array[] = $r->getAttributes();
			}
		}
		return $array;
	}
}
