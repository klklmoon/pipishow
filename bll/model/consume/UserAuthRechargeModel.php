<?php 
/**
 * 第三方充值记录表
 *
 * the last known user to change this file in the repository  <$LastChangedBy: guoshaobo $>
 * @author 郭少波 <guoshaobo@pipi.cn>
 * @version $Id: PayModel.php 8510 2013-05-08 20:06:37Z guoshaobo $
 * @package model
 * @subpackage consume
 */
class UserAuthRechargeModel extends PipiActiveRecord {

	public function tableName(){
		return '{{user_auth_recharge}}';
	}
	
	/**
	 * @param string $className
	 * @return UserRechargeRecordsModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function getDbConnection(){
		return Yii::app()->db_consume;
	}
	
	public function authRecharge($uid,$data)
	{
		$auth = $data['auth']; 
		$auth_uid = $data['auth_uid']; 
		$pipiegg = $data['pipiegg']; 
		$order_id = $data['order_id']; 
		$info = stripslashes(json_encode($data)); 
		$create_time = $data['create_time'];
		$uid = intval($uid);
		$dbCommand = $this->getDbCommand();
		$dbCommand->setText("call proc_auth_recharge(:uid, :auth, :auth_uid, :pipiegg, :order_id, :info, :create_time)");
		$dbCommand->prepare();
		$dbCommand->bindValues(array(':uid'=>$uid,':auth'=>$auth,'auth_uid'=>$auth_uid,'pipiegg'=>$pipiegg,'order_id'=>$order_id,'info'=>$info,'create_time'=>$create_time));
		$res = $dbCommand->queryScalar();
		return $res;
	}
}


?>