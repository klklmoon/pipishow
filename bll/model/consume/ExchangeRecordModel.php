<?php
/**
 * 兑换记录表
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: guoshaobo $>
 * @author 郭少波 <guoshaobo@pipi.cn>
 * @version $Id: ExchangeRecordModel.php 8510 2013-04-09 05:02:37Z suqian $ 
 * @package model
 * @subpackage consume 
 */
class ExchangeRecordModel extends PipiActiveRecord {

	public function tableName(){
		return '{{user_exchange_records}}';
	}
	
	/**
	 * @param string $className
	 * @return DoteyCharmRecordsModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function getDbConnection(){
		return Yii::app()->db_consume_records;
	}
	
	/**
	 * 获取现金平台奖励 才艺补贴
	 * @param array $condition
	 * @param unknown_type $offset
	 * @param unknown_type $pageSize
	 * @param unknown_type $isLimit
	 */
	public function getCashAwardByCondition(Array $condition = array(),$offset=0,$pageSize=10,$isLimit=true){
		$result = array();
		$result['count'] = 0;
		$result['list'] = array();
		
		$criteria = $this->getDbCriteria();
		if (isset($condition['ex_type']) && $condition['ex_type'] == 4){
			//才艺补贴
			$criteria->addCondition('ex_type=4');
		}elseif(isset($condition['ex_type']) && $condition['ex_type'] == 3){
			//平台现金奖励
			$criteria->addCondition('ex_type=3');
		}elseif(isset($condition['ex_type'])){
			$criteria->addCondition('ex_type='.$condition['ex_type']);
		}
		
		if (!empty($condition['handle_type'])){
			$criteria->addCondition('handle_type='.intval($condition['handle_type']));
		}
		
		if (!empty($condition['create_time_on'])){
			$criteria->addCondition('create_time>='.strtotime($condition['create_time_on']));
		}
		
		if (!empty($condition['create_time_end'])){
			$criteria->addCondition('create_time<'.strtotime($condition['create_time_end']));
		}
		
		if (!empty($condition['uid'])){
			$criteria->compare('uid', $condition['uid']);		
		}
		//print_r($criteria->condition);exit;
		$result['count'] = $this->count($criteria);
		if ($isLimit){
			$criteria->limit = $pageSize;
			$criteria->offset = $offset;
		}
		$criteria->order = 'create_time DESC';
		$result['list'] = $this->findAll($criteria);
		return $result;
	}
	
	public function getExchangeRecord($uids, $condition,$limit=5)
	{
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('uid', $uids);
		$criteria->addColumnCondition($condition);
		$criteria->limit = $limit;
		$criteria->order = 'create_time desc';
		$res = $this->findAll($criteria);
		return $res;
	}
	
	public function getExchangeEggRecord($uid, $limit = 5)
	{
		$criteria = $this->getDbCriteria();
		$criteria->select = ' uid, sum(org_amount) as oamount, sum(dst_amount) as damount, create_time';
		$criteria->condition = 'uid=:uid and handle_type=1 and ex_type in (0,1) ';
		$criteria->group = 'create_time';
		$criteria->limit = $limit;
		$criteria->order = 'record_id desc';
		$criteria->params = array(':uid'=>$uid);
		return $this->getCommandBuilder()->createFindCommand($this->tableName(),$criteria)->queryAll();
	}
	
	public function countExchangeRecord($uid, $stime, $etime, $exType)
	{
		$criteria = $this->getDbCriteria();
		$criteria->select = ' uid, sum(org_amount) as amounts,info,count(*) as nums,sum(dst_amount) as money';
		$criteria->condition = 'uid=:uid and handle_type=1';
		if(is_array($exType))
		{
			$criteria->condition .= ' and ex_type in (' . implode(',', $exType) .')';
		}
		elseif($exType >= 0){
			$criteria->condition .= ' and ex_type = ' . $exType;
		}
		$criteria->condition .= ' and create_time>=:stime';
		$criteria->condition .= ' and create_time<=:etime';
		$criteria->params = array(':uid'=>$uid,':stime'=>$stime,':etime'=>$etime);
		return $this->getCommandBuilder()->createFindCommand($this->tableName(),$criteria)->queryRow();
	}

	
	public function getExchageRecordsList(array $uids, array $condition = array()){
		$criteria = $this->getDbCriteria();
		if (isset($condition['startTime'])){
			$criteria->addCondition('create_time>='.$condition['startTime']);
		}
		
		if (isset($condition['endTime'])){
			$criteria->addCondition('create_time<='.$condition['endTime']);
		}
		$criteria->condition = ' handle_type = 1 AND  ex_type = 2 ';
		$criteria->addInCondition('uid',$uids);
		$criteria->order = 'create_time desc';
		return $this->findAll($criteria);
	}

	
	public function countExchangeRecordByUids($uids, $stime, $etime, $exType = 1)
	{
		$criteria = $this->getDbCriteria();
		$criteria->select = ' uid, sum(org_amount) as amounts,info,count(*) as nums,sum(dst_amount) as money';
		$criteria->group = 'uid';
		$criteria->compare('uid', $uids);
		$criteria->compare('handle_type', 1);
		if($exType >= 0){
			$criteria->compare('ex_type', $exType);
		}
		
		$criteria->addCondition('create_time>='.strtotime($stime));
		$criteria->addCondition('create_time<='.strtotime($etime));
		return $this->getCommandBuilder()->createFindCommand($this->tableName(),$criteria)->queryAll();
	}
	

}

?>