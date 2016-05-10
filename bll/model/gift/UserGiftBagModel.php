<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: leiwei $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: UserGiftBagModel.php 9767 2013-05-07 13:25:17Z leiwei $ 
 * @package model
 * @subpackage gift
 */
class UserGiftBagModel extends PipiActiveRecord {
	
	/**
	 * @param unknown_type $className
	 * @return UserGiftBagModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{user_gift_bag}}';
	}
	
	
	public function getDbConnection(){
		return Yii::app()->db_consume;
	}
	
	public function rules(){
		return array(
			array('uid,gift_id,num','numerical'),
		);
	}
	
	public function attributeLabels(){
		return array(
			'uid'=>'用户uid',
			'gift_id' =>'礼物Id',
			'num' =>'数量',
		);
	}
	
	public function getUserGiftBagByUids($uids){
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('uid',$uids);
		return $this->findAll($criteria);
	}
	
	public function getUserBagByGiftIds($uid,$giftIds){
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('gift_id', $giftIds);
		$criteria->addCondition(array('uid=:uid','num>:num'));
		$criteria->params[':uid']=$uid;
		$criteria->params[':num']=0;
		return $this->find($criteria);
	}
		
	public function getUserGiftBagByUidGiftId($uid,$gift_id){
		$criteria = $this->getDbCriteria();
		$criteria->addCondition(array('uid=:uid','gift_id=:gift_id'));
		$criteria->params[':uid']=$uid;
		$criteria->params[':gift_id']=$gift_id;
		return $this->find($criteria);
	}

	/**
	 * @param int $uid 用户uid
	 * @param int $giftId 礼物ID
	 * @param int $sendNum 扣除数量
	 * @return int 执行结果，1为成功，0为失败
	 */
	public function reduceGiftBag($uid, $giftId,$sendNum) {
		$uid = intval($uid);
		$giftId = intval($giftId);
		$sendNum = intval($sendNum);
		$dbCommand = $this->getDbCommand();
		$dbCommand->setText("call proc_reduceGiftBag(:uid, :giftId,:send_num)");
		$dbCommand->prepare();
		$dbCommand->bindValues(array(':uid'=>$uid,':giftId'=>$giftId,':send_num'=>$sendNum));
		$data = $dbCommand->queryScalar();
		return $data;
	}
	
}

?>