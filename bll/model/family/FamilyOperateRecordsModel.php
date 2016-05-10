<?php
/**
 * 家族操作记录
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: 2013-8-6 下午3:28:20 hexin $ 
 * @package
 */
class FamilyOperateRecordsModel extends PipiActiveRecord {
	/**
	 * 
	 * @param string $className
	 * @return FamilyOperateRecordsModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{family_operate_records}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_family;
	}
	
	/**
	 * 查询操作原因
	 * @param int $family_id
	 * @param int $type
	 * @return array
	 */
	public function getReason($family_id, $type = 4){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->condition = 'family_id='.$family_id.' and type = '.intval($type);
		$criteria->order = 'id desc';
		$criteria->limit = 1;
		return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryRow();
	}
	
	public function searchOPRecords(Array $condition = array(), $offset = 0, $limit = 20, $isLimit = true){
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
	
		if (isset($condition['opType']) && is_numeric($condition['opType'])){
			$criteria->compare('type', $condition['opType']);
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
