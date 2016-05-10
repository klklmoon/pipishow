<?php
/**
 * 用户扩展信息存储
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <aoxue.1988.su.qian@163.com>
 * @version $Id: UserBasicModel.php 8366 2013-04-01 14:56:32Z suqian $ 
 * @package model
 * @subpackage user
 */
class SequenceModel extends PipiActiveRecord {
	
	/**
	 * @param string $className
	 * @return SequenceModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{sequence}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_user;
	}
	
	/**
	 * 求取下一个自增类型
	 * 
	 * @param string $name 自增类型名
	 * @return int
	 */
	public function nextId($name){
		$dbCommand = $this->getDbCommand();
		$dbCommand->text = 'SELECT func_nextval(:name) as '.$name;
		$dbCommand->prepare();
		$dbCommand->bindParam(':name',$name);
		$data = $dbCommand->queryAll();

		if($data[0][$name] == ''){
			$dbCommand->text = 'SELECT func_addseq(:name) as '.$name;
			$dbCommand->prepare();
			$dbCommand->bindParam(':name',$name);
			$dbCommand->execute();
			$dbCommand->text = 'SELECT func_nextval(:name) as '.$name;
			$dbCommand->prepare();
			$dbCommand->bindParam(':name',$name);
			$data = $dbCommand->queryAll();
		}
		return $data[0][$name];
		
	}
}

?>