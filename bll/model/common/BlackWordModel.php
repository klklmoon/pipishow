<?php
/**
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: guoshaobo $>
 * @author guoshaobo <guoshaobo@pipi.cn>
 * @version $Id: BlackWordModel.php 9671 2013-05-06 13:51:21Z guoshaobo $ 
 * @package model
 * @subpackage consume
 */
class BlackWordModel extends PipiActiveRecord
{
	/**
	 * @param string $className
	 * @return OperateModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{black_word}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_common;
	}
	
	public function getChatWordList($word_type = 0, $offset, $limit, $getAll = false)
	{
		$criteria = $this->getDbCriteria();
		$criteria->condition = ' `status` = 1 and word_type=:word_type';
		$criteria->params = array(':word_type'=>$word_type);
		$count = $this->count($criteria);
		if(!$getAll){
			$criteria->offset = $offset;
			$criteria->limit = $limit;
		}
		$list = $this->findAll($criteria);
		$result = array('count'=>$count, 'list'=>$list);
		return $result;
	}
}

?>