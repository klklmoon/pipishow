<?php
/**
 * 用户角色及权限变更记录
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: PurviewRecordModel.php 8659 2013-04-15 09:47:05Z hexin $ 
 * @package
 */
class PurviewRecordModel extends PipiActiveRecord {

	/**
	 * @param string $className
	 * @return PurviewRecordModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}

	public function tableName(){
		return '{{purview_userop_records}}';
	}

	public function getDbConnection(){
		return Yii::app()->db_purview;
	}
	
	/**
	 * 获取权限操作记录
	 * @param int $pageSize
	 * @param int $page
	 * @param array $search = array(op_uid, uid, op_time); 操作人po_uid, 被操作人uid, 操作时间op_time = array($start, $end)
	 * @return array
	 */
	public function getAll($pageSize = 20, $page = 1, array $search = array()){
		$criteria = $this->getCommandBuilder()->createCriteria();
		if(isset($search['op_uid']) && intval($search['op_uid']) > 0){
			$criteria->addColumnCondition(array('op_uid' => intval($search['op_uid'])));
		}
		if(isset($search['uid']) && intval($search['uid']) > 0){
			$criteria->addColumnCondition(array('uid' => intval($search['uid'])));
		}
		if(isset($search['op_time']) && !(intval($search['op_time'][0]) < 1 && intval($search['op_time'][1]) < 1)){
			$start = intval($search['optime'][0]) > 0 ? intval($search['optime'][0]) : time();
			$end = intval($search['optime'][1]) > 0 ? intval($search['optime'][1]) : time();
			$criteria->addBetweenCondition('op_time', $start, $end);
		}
		$criteria->order = 'record_id desc';
		$criteria->limit = $pageSize;
		$criteria->offset = ($page-1) * $pageSize;
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