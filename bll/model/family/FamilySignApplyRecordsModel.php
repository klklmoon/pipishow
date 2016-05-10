<?php
/**
 * 签约家族申请记录
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: 2013-8-6 下午3:28:20 hexin $ 
 * @package
 */
class FamilySignApplyRecordsModel extends PipiActiveRecord {
	/**
	 * 
	 * @param string $className
	 * @return FamilyMemberApplyRecordsModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{family_sign_apply_records}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_family;
	}
	
	/**
	 * 获取未审核的申请记录
	 * @param array $conditions
	 * @param boolean $pageEnable
	 * @param int $pageSize
	 * @param int $page
	 * @return array(list=>array, count=>int)
	 */
	public function getApplyList(array $conditions = array(), $pageEnable = true, $offset = 0, $limit = 10){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->condition = 'status = 0';
		$return = array('list' => array(), 'count' => 0);
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
	 * 查询某family的申请记录
	 * @param int $family_id
	 * @param array $uids
	 * @param int $status 申请状态
	 * @return array
	 */
	public function getApplyByFamily($family_id, $status = ''){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->condition = 'family_id='.$family_id.($status === '' ? '' : 'and status = '.$status);
		$criteria->order = 'id desc';
		$criteria->limit = 1;
		return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryRow();
	}
	
	/**
	 * 批量更新申请记录状态
	 * @param array $ids 申请记录id集合
	 * @param int $status
	 * @return boolean
	 */
	public function updateApplyStatus(array $ids, $status){
		if(empty($ids)) return false;
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->condition = 'id in ('.implode(',', $ids).')';
		if($this->getCommandBuilder()->createUpdateCommand($this->tableName(), array('status' => $status), $criteria)->execute())
			return true;
		else return false;
	}
	
	/**
	 * 删除签约家族申请记录
	 * @param int $family_id
	 * @return boolean
	 */
	public function deleteApplyStatus($family_id){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->condition = 'family_id='.$family_id;
		if($this->getCommandBuilder()->createDeleteCommand($this->tableName(), $criteria)->execute()){
			return true;
		}else return false;
	}
	
	public function searchSignList(Array $condition = array(), $offset = 0, $limit = 20, $isLimit = true){
		$result = array();
		$criteria = $this->getDbCriteria();
		$criteria->order = 'create_time DESC';
	
		if (isset($condition['id']) && is_numeric($condition['id'])){
			$criteria->compare('id', $condition['id']);
		}
		
		if (isset($condition['family_id']) && is_numeric($condition['family_id'])){
			$criteria->compare('family_id', $condition['family_id']);
		}
		
		if (isset($condition['status']) && is_numeric($condition['status'])){
			$criteria->compare('status', $condition['status']);
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
