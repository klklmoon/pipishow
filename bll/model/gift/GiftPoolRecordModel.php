<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: lei wei $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: GiftPoolRecordModel.php 8971 2013-04-22 14:30:27Z lei wei $ 
 * @package model
 * @subpackage gift
 */
class GiftPoolRecordModel extends PipiActiveRecord{
	
	/**
	 * @param unknown_type $className
	 * @return GiftPoolRecordModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{gift_pool_record}}';
	}
	

	public function getDbConnection(){
		return Yii::app()->db_consume;
	}
	
	public function rules(){
		return array(
			array('value,chance','required'),
		);
	}
	
	public function attributeLabels(){
		return array(
			'value'=>'奖池储金值',
			'chance' =>'A值',
			'create_time'=>'时间'
		);
	}
	
	/**
	 * 获取当前奖池奖金值
	 * @return array
	 */
	public function getLastGiftPoolRecord(){
		$criteria = $this->getDbCriteria();
		$criteria->order='id desc';
		$criteria->limit=1;
		return $this->find($criteria);
	}
	

	/**
	 * 奖池变化记录
	 * @param $pipiegg  奖池变化的皮蛋数
	 * @param $plus     0->奖池减少,1->奖池增加
	 * @return boolean 0->失败，1->成功 
	 */
	public function saveGiftPoolRecord($pipiegg, $plus=true){
		$pipiegg = floatval($pipiegg);
		$dbCommand = $this->getDbCommand();
		$dbCommand->setText("call proc_giftPoolRecord(:pipiegg, :plus)");
		$dbCommand->prepare();
		$dbCommand->bindValues(array(':pipiegg'=>$pipiegg,':plus'=>$plus));
		return$dbCommand->queryScalar();
	}
	

	public function searchGiftPoolRecord(Array $condition = array(),$offset=0,$pageSize=20,$isLimit=true){
		$criteria = $this->getDbCriteria();
		$result['count'] = $this->count($criteria);
		$criteria->order = 'id DESC';
		if ($isLimit){
			$criteria->offset = $offset;
			$criteria->limit = $pageSize;
		}
		$result['list'] = $this->findAll($criteria);
		return $result;
	}

	
}

?>