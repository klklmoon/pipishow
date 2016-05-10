<?php
/**
 * 家族成员申请记录
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: 2013-8-6 下午3:28:20 hexin $ 
 * @package
 */
class FamilyMemberApplyRecordsModel extends PipiActiveRecord {
	/**
	 * 
	 * @param string $className
	 * @return FamilyMemberApplyRecordsModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{family_member_apply_records}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_family;
	}
	
	/**
	 * 获取未审核的申请记录
	 * @param int $family_id
	 * @param int $apply_type 申请的身份，默认为全部身份
	 * @param int $pageSize
	 * @param int $page
	 * @param boolean $pageEnable
	 * @return array(list=>array, count=>int)
	 */
	public function getApplyList($family_id, $apply_type = -1, $pageEnable = true, $offset = 0, $limit = 10){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->condition = 'family_id='.$family_id.' and status = 0'.($apply_type != -1 ? ' and apply_type = '.$apply_type : '');
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
	 * 查询某些uid在某family的申请记录
	 * @param int $family_id
	 * @param array $uids
	 * @param int $status 申请状态
	 * @return array
	 */
	public function getApplyByUids($family_id, array $uids , $status = null){
		if(empty($uids)) return array();
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->condition = 'family_id='.$family_id.' and uid in('.implode(',', $uids).')'.($status !== null ? ' and status = '.$status : '');
		return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
	}
	
	/**
	 * 批量更新申请记录状态
	 * @param int $family_id
	 * @param array $uids
	 * @param int $status
	 * @return boolean
	 */
	public function updateApplyStatus($family_id, array $uids, $status){
		if(empty($uids)) return false;
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->condition = 'family_id='.$family_id.' and uid in ('.implode(',', $uids).')';
		if($this->getCommandBuilder()->createUpdateCommand($this->tableName(), array('status' => $status), $criteria)->execute())
			return true;
		else return false;
	}
}
