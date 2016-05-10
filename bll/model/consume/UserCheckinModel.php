<?php
/**
 *
 * the last known user to change this file in the repository  <$LastChangedBy: guoshaobo $>
 * @author guoshaobo <guoshaobo@pipi.cn>
 * @version $Id: UserCheckinModel.php 9657 2013-05-06 12:59:31Z guoshaobo $
 * @package model
 * @subpackage consume
 */
class UserCheckinModel extends PipiActiveRecord{
	
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{user_checkin}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_consume;
	}
	
	public function getCheckinByUid($uid, $type, $time)
	{
		$criteria = $this->getDbCriteria();
		$criteria->condition = ' uid=:uid and `type`=:type and create_time>=:time ';
		$criteria->params = array(':uid'=>$uid,':type'=>$type,':time'=>$time);
		return $this->find($criteria);
	}
	
	public function countMonthGift($uid, $type, $stime, $etime)
	{
		$criteria = $this->getDbCriteria();
		$criteria->select = 'uid, sum(`num`) as nums';
		$criteria->condition = ' uid=:uid and `type`=:type and create_time>=:stime and create_time<=:etime ';
		$criteria->params = array(':uid'=>$uid,':type'=>$type,':stime'=>$stime,':etime'=>$etime);
		return $this->getCommandBuilder()->createFindCommand($this->tableName(),$criteria)->queryRow();
	}
	
	/**
	 * 查询用户是否有签到记录
	 * @author hexin 2013-08-01
	 * @param int $uid
	 * @return int
	 */
	public function hasCheckin($uid){
		return $this->count('uid = '.$uid);
	}
	
	/**
	 * 检查当天是否签过到
	 * @param int $uid
	 * @return boolean
	 */
	public function isCheckin($uid){
		$today = date('Y-m-d');
		$count = $this->count('uid = '.$uid.' and create_time >= '.strtotime($today.' 00:00:00').' and create_time <= '.strtotime($today.' 23:59:59'));
		return $count > 0 ? true : false;
	}
	
	/**
	 * 本月累计签到多少天
	 * @param int $uid
	 * @return int
	 */
	public function checkinDays($uid){
		$monthStart = strtotime(date('Y-m').'-01 00:00:00');
		$monthEnd = strtotime(date('Y-m-').date('t').' 23:59:59');
		$criteria = $this->getDbCriteria();
		$criteria->select = 'FROM_UNIXTIME(create_time,"%Y-%m-%d") as day';
		$criteria->condition = ' uid='.$uid.' and create_time >= '.$monthStart.' and create_time<= '.$monthEnd;
		$criteria->group = 'day';
		$list = $this->getCommandBuilder()->createFindCommand($this->tableName(),$criteria)->queryAll();
		return count($list);
	}
	
	/**
	 * 获取某时间段内的签到记录
	 * @param int $uid
	 * @param int $stime
	 * @param int $etime
	 * @return array
	 */
	public function chekinRecords($uid, $stime, $etime)
	{
		$criteria = $this->getDbCriteria();
		$criteria->condition = ' uid=:uid and create_time>=:stime and create_time<=:etime ';
		$criteria->params = array(':uid'=>$uid,':stime'=>$stime,':etime'=>$etime);
		return $this->getCommandBuilder()->createFindCommand($this->tableName(),$criteria)->queryAll();
	}
	
	/**
	 * 检查具体的签到礼物是否已领取
	 * @param int $uid 领取人
	 * @param int $type 签到分类，有normal类型，月卡类型，每日广播类型
	 * @param int $item_type 礼物类型，有礼物，道具，贡献值，魅力值
	 * @param int $item_id 礼物id
	 * @param int $start_time 
	 * @param int $end_time
	 * @return array
	 */
	public function isCheckinItem($uid, $type, $item_type, $item_id, $start_time, $end_time){
		$criteria = $this->getDbCriteria();
		$criteria->condition = ' uid=:uid and `type`=:type and reward_type=:item_type and target_id=:item_id and create_time>=:start_time and create_time<=:end_time';
		$criteria->params = array(':uid'=>$uid,':type'=>$type,':item_type'=>$item_type,':item_id'=>$item_id,':start_time'=>$start_time,':end_time'=>$end_time);
		return $this->find($criteria);
	}
}
?>