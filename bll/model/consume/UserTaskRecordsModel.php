<?php

/**
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z suqian $ 
 * @package 
 */
class UserTaskRecordsModel extends PipiActiveRecord {
	
	/**
	 * @param unknown_type $className
	 * @return UserTaskRecordsModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{user_task_records}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_consume;
	}
	
	/**
	 * 获取用户已完成的新手任务列表
	 * @author hexin
	 * @param int $uid
	 * @param array $tids
	 * @return array
	 */
	public function getTaskRecords($uid, array $tids = array()){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->addCondition('uid = '.$uid);
		$criteria->addCondition("task_type = 'task'");
		if(!empty($tids)) $criteria->addInCondition('target_id', $tids);
		return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
	}
	
	/**
	 * 事务保证只有未领取过奖励才可以领取，并记录状态
	 * @author hexin
	 * @param int $tid
	 * @param int $uid
	 * @param int $eggs
	 * @return int
	 */
	public function reward($tid, $uid, $eggs){
		$tid = intval($tid);
		$uid = intval($uid);
		$eggs = floatval($eggs);
		$dbCommand = $this->getDbCommand();
		$dbCommand->setText("call proc_reward(:tid, :uid, :eggs)");
		$dbCommand->prepare();
		$dbCommand->bindValues(array('tid'=>$tid,':uid'=>$uid,':eggs'=>$eggs));
		$data = $dbCommand->queryScalar();
		return $data;
	}

}

?>