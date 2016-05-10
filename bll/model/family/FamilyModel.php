<?php
/**
 * 家族基本信息
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: 2013-8-6 下午3:28:20 hexin $ 
 * @package
 */
class FamilyModel extends PipiActiveRecord {
	/**
	 * 
	 * @param string $className
	 * @return FamilyModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{family}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_family;
	}
	
	public function rules(){
		return array(
			array('name,cover','required'),
			array('name', 'filter','filter'=>array(new CHtmlPurifier(),'purify')),
			array('name', 'length', 'min' => 2, 'max' => 20, 'message'=>'20字以内'),
			array('medal', 'length', 'min' => 2, 'max' => 3, 'message'=>'2个汉字或2-3个英文字母'),
		);
	}
	
	/**
	 * 查询家族列表，可分页
	 * @param array $conditions
	 * @param int $offset
	 * @param int $limit
	 * @param string $orderby
	 * @param boolean $pageEnable
	 * @return array(list=>array, count=>int)
	 */
	public function getFamilyList(array $conditions = array(), $offset = 0, $limit = 10, $orderby = 'f.id desc', $pageEnable = true){
		$where = '1 = 1';
		if(isset($conditions['status'])){
			if($conditions['status'] != 'all') $where .= ' and f.status = '.intval($conditions['status']);
		}else{
			$where .= ' and f.status = '.FAMILY_STATUS_SUCCESS;
		}
		
		if(isset($conditions['hidden'])){
			if($conditions['hidden'] != 'all') $where .= ' and f.hidden = '.intval($conditions['hidden']);
		}else{
			$where .= ' and f.hidden = 0';
		}
		
		if(isset($conditions['forbidden'])){
			if($conditions['forbidden'] != 'all') $where .= ' and f.forbidden = '.intval($conditions['forbidden']);
		}else{
			$where .= ' and f.forbidden = 0';
		}
		
		if(isset($conditions['name'])){
			$where .= " and f.name = '".mysql_escape_string($conditions['name'])."'";
		}
		
		if(isset($conditions['medal'])){
			$where .= " and f.medal = '".mysql_escape_string($conditions['medal'])."'";
		}
		
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->alias = 'f';
		$criteria->select = 'f.*';
		$criteria->condition = $where;
		$return = array('list' => array(), 'count' => 0);
		if($orderby){
			$criteria->order = $orderby;
			$pre = explode('.', $orderby);
			if($pre[0] == 'e'){
				$criteria->join = 'LEFT JOIN web_family_extend as e ON f.id = e.family_id';
			}elseif($pre[0] == 'm'){
				$criteria->join = 'LEFT JOIN web_family_member as m ON f.id = m.family_id';
				$pre = explode(' ', $pre[1]);
				if($pre[0] == 'medal'){
					$criteria->select = 'f.*, sum(m.medal_enable) as medal';
					$criteria->group = 'm.family_id';
					$criteria->order = substr($orderby, 2);
				}elseif($pre[0] == 'member'){
					$criteria->select = 'f.*, count(*) as member';
					$criteria->group = 'm.family_id';
					$criteria->order = substr($orderby, 2);
				}elseif($pre[0] == 'dotey'){
					$criteria->select = 'f.*, sum(m.family_dotey) as dotey';
					$criteria->group = 'm.family_id';
					$criteria->order = substr($orderby, 2);
				}
			}
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
	 * 删除家族
	 * @param int $family_id
	 * @return boolean
	 */
	public function deleteFamily($family_id){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->condition = 'id='.$family_id;
		if($this->getCommandBuilder()->createDeleteCommand($this->tableName(), $criteria)->execute())
			return true;
		else return false;
	}
	
	public function searchFamily(Array $condition = array(), $offset = 0, $limit = 20, $isLimit = true){
		$result = array();
		$criteria = $this->getDbCriteria();
		$criteria->order = 'create_time DESC';
		if (!empty($condition['uid'])){
			$criteria->compare('uid', $condition['uid']);
		}
		
		if (!empty($condition['uids'])){
			$criteria->compare('uid', $condition['uids']);
		}
		
		if (!empty($condition['name'])){
			$criteria->compare('name', $condition['name'],true);
		}
		
		if (isset($condition['status']) && is_numeric($condition['status'])){
			$criteria->compare('status', $condition['status']);
		}
		
		if (isset($condition['hidden']) && is_numeric($condition['hidden'])){
			$criteria->compare('hidden', $condition['hidden']);
		}
		
		if (isset($condition['forbidden']) && is_numeric($condition['forbidden'])){
			$criteria->compare('forbidden', $condition['forbidden']);
		}
		
		if (!empty($condition['create_time_start'])){
			$criteria->addCondition('create_time>='.strtotime($condition['create_time_start']));
		}
		
		if (!empty($condition['create_time_end'])){
			$criteria->addCondition('create_time<='.strtotime($condition['create_time_end']));
		}
		
		$result['count'] = $this->count($criteria);
		if($isLimit){
			$criteria->offset = $offset;
			$criteria->limit = $limit;
		}
		$result['list'] = $this->findAll($criteria);
		return $result;
		
	}
}
