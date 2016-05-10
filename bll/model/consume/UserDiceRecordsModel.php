<?php
/**
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: leiwei $>
 * @author 雷伟 <leiwei@pipi.cn>
 * @version $Id: UserDiceRecordsModel.php 13232 2013-07-22 08:28:29Z leiwei $ 
 * @package model
 * @subpackage UserDiceRecords
 */
class UserDiceRecordsModel extends PipiActiveRecord{
	/**
	 * @param string $className
	 * @return UserDiceRecordsModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{user_dice_records}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_consume;
	}
	
	public function getUserDiceRecord($uid, $offset = 0, $limit = 10, $attribute = array())
	{
		$result = array();
		$result['count'] = 0;
		$result['list'] = array();
		if($uid <= 0){
			return $result;
		}
		$criteria = $this->getDbCriteria();
		$criteria->compare('uid', $uid);
		if(!empty($attribute) && isset($attribute['type'])){
			$criteria->compare('type',$attribute['type']);
		}
		

		$result['count'] = $this->count($criteria);
		
		$criteria->limit = $limit;
		$criteria->offset = $offset;
		
		$criteria->order = 'create_time DESC';
		$result['list'] = $this->findAll($criteria);
		return $result;
	}
	
	/**
	 * 更新发送骰子对局状态
	 * @param int $recordId
	 * @return boolean 0->失败 1->成功
	 */
	public function updateDiceRecord($recordId){
		$recordId=intval($recordId);
		$dbCommand = $this->getDbCommand();
		$dbCommand->setText("call proc_updateDiceRecord(:recordId)");
		$dbCommand->prepare();
		$dbCommand->bindValues(array(':recordId'=>$recordId));
		$data = $dbCommand->queryScalar();
		return $data;
	}
}