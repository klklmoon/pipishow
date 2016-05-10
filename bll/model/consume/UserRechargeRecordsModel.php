<?php
/**
 * 用户充值数据访问层
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: UserEggPointRecordsModel.php 8510 2013-04-09 05:02:37Z suqian $ 
 * @package model
 * @subpackage consume 
 */
class UserRechargeRecordsModel extends PipiActiveRecord {

	public function tableName(){
		return '{{user_recharge_records}}';
	}
	
	/**
	 * @param string $className
	 * @return UserRechargeRecordsModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function getDbConnection(){
		return Yii::app()->db_user_records;
	}
	
	public function checkSignBySource($source ,$sign){
		if(empty($source) || empty($sign)){
			return false;
		}
		$criteria = $this->getDbCriteria();
		$criteria->condition = 'rsource= :source and sign=:sign';
		$criteria->params = array(':source'=>$source,':sign'=>$sign);
		$criteria->select = ' sign ';
		$data = $this->find($criteria);
		return $data ? $data->attributes : array();
	}
	
	/**
	 * 根据时间段统计用户充值皮蛋数
	 * 
	 * @param int $uid 用户id
	 * @param int $stime 时间段起始时间戳
	 * @param int $etime 时间段终止时间戳
	 * @return float
	 */
	public function getUserPipiEggsByTime($uid,$stime,$etime)
	{
		if(empty($uid) || empty($stime) || empty($etime)){
			return false;
		}
		//查充值记录
		$command=Yii::app()->db_user_records->createCommand();
		$sum_pipiegg=$command->select("sum(pipiegg) as sum_pipiegg")
		->from("{{user_recharge_records}}")
		->where('uid = :uid and ctime >= :stime and ctime <= :etime and issuccess = 2',
			array(':uid'=>$uid,':stime'=>$stime,':etime'=>$etime))
		->queryScalar();
		//查代充记录只统计现金代充,patype = 2表示现金代充值
		$accountCommand=Yii::app()->db_account->createCommand();
		$proxyRechargePipiegg=$accountCommand->select("sum(pipiegg) as proxy_recharge_pipiegg")
		->from("{{proxyrechargelog}}")
		->where('uid = :uid and otime >= :stime and otime <= :etime and patype = 2',
			array(':uid'=>$uid,':stime'=>$stime,':etime'=>$etime))
		->queryScalar();
		
		//echo "$sum_pipiegg,$proxyRechargePipiegg";exit;
		$sum_pipiegg=isset($sum_pipiegg) && $sum_pipiegg>0?$sum_pipiegg:0;
		$proxyRechargePipiegg=isset($proxyRechargePipiegg) && $proxyRechargePipiegg>0?$proxyRechargePipiegg:0;
		
		$totalPipieggs=$sum_pipiegg+$proxyRechargePipiegg;
		return $totalPipieggs;
	}
	
	
	/**
	 * 根据时间段统计用户充值皮蛋数
	 *
	 * @param int $uid 用户id
	 * @param int $stime 时间段起始时间戳
	 * @param int $etime 时间段终止时间戳
	 * @return float
	 */
	public function getUserAllPipiEggsByTime($uid,$stime,$etime)
	{
		if(empty($uid) || empty($stime) || empty($etime)){
			return false;
		}
		//查充值记录
		$command=Yii::app()->db_user_records->createCommand();
		$sum_pipiegg=$command->select("sum(pipiegg) as sum_pipiegg")
		->from("{{user_recharge_records}}")
		->where('uid = :uid and ctime >= :stime and ctime <= :etime',
			array(':uid'=>$uid,':stime'=>$stime,':etime'=>$etime))
			->queryScalar();
		//查代充记录只统计现金代充,patype = 2表示现金代充值
		$accountCommand=Yii::app()->db_account->createCommand();
		$proxyRechargePipiegg=$accountCommand->select("sum(pipiegg) as proxy_recharge_pipiegg")
		->from("{{proxyrechargelog}}")
		->where('uid = :uid and otime >= :stime and otime <= :etime and patype = 2',
			array(':uid'=>$uid,':stime'=>$stime,':etime'=>$etime))
			->queryScalar();
	
		//echo "$sum_pipiegg,$proxyRechargePipiegg";exit;
		$sum_pipiegg=isset($sum_pipiegg) && $sum_pipiegg>0?$sum_pipiegg:0;
		$proxyRechargePipiegg=isset($proxyRechargePipiegg) && $proxyRechargePipiegg>0?$proxyRechargePipiegg:0;
	
		$totalPipieggs=$sum_pipiegg+$proxyRechargePipiegg;
		return $totalPipieggs;
	}
	
	/**
	 * 获取某用户的单日充值总数
	 * @param int $uid
	 * @param int $stime
	 * @param int $etime
	 */
	public function getUserRechargeByDay($uid, $stime, $etime){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->select = "uid, FROM_UNIXTIME(ctime,'%Y-%m-%d') as day, sum(if(currencycode = 'USD', money * 6, money)) as money, sum(pipiegg) as pipiegg";
		$criteria->condition = "uid = ".$uid." and issuccess = 2 and ctime >= ".$stime." and ctime <= ".$etime;
		$criteria->group = "uid, day";
		return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
	}
	
	/**
	 * 获取某用户的单日充值总数
	 * @param int $uid
	 * @param int $stime
	 * @param int $etime
	 */
	public function getUserAllRechargeByDay($uid, $stime, $etime){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->select = "uid, FROM_UNIXTIME(ctime,'%Y-%m-%d') as day, sum(if(currencycode = 'USD', money * 6, money)) as money, sum(pipiegg) as pipiegg";
		$criteria->condition = "uid = ".$uid." and ctime >= ".$stime." and ctime <= ".$etime;
		$criteria->group = "uid, day";
		return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
	}
	
	/**
	 * 获取某特定时间范围内首充的时间
	 * 
	 * @author supeng
	 * @param int $uid
	 * @param int $stime
	 * @return boolean|Ambigous <NULL, unknown, multitype:unknown Ambigous <unknown, NULL> , mixed, multitype:, multitype:unknown >
	 */
	public function getFirstCharge($uid,$stime){
		if(empty($uid) || empty($stime) ){
			return false;
		}
		$criteria = $this->getDbCriteria();
		$criteria->compare('issuccess', 2);
		$criteria->compare('uid', $uid);
		$criteria->addCondition('ctime>='.$stime);
		$criteria->order = 'rtime ASC';
		$criteria->limit = 1;
		return $this->find($criteria)->attributes;
	}
	
	public function getLastCharge($uid){
		if($uid <= 0){
			return false;
		}
		$criteria = $this->getDbCriteria();
		$criteria->compare('issuccess', 2);
		$criteria->compare('uid', $uid);
		$criteria->order = 'ctime DESC';
		$criteria->limit = 1;
		$data = $this->find($criteria);
		if($data){
			return $data->attributes;
		}
		return array();
	}
}

?>