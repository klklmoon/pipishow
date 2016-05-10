<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: supeng $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: UserGiftSendRecordsModel.php 14349 2013-08-28 10:12:19Z supeng $ 
 * @package model
 * @subpackage gift
 */
class UserGiftSendRecordsModel extends PipiActiveRecord{
	
	/**
	 * @param unknown_type $className
	 * @return UserGiftSendRecordsModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{user_giftsend_records}}';
	}
	

	public function getDbConnection(){
		return Yii::app()->db_consume_records;
	}
	
	public function rules(){
		return array(
			array('uid,to_uid','required'),
		);
	}
	
	public function attributeLabels(){
		return array(
			'uid'=>'送礼用户uid',
			'to_uid'=>'收礼用户uid',
		);
	}
	
	/**
	 * 获取用户送礼记录
	 * 
	 * @author guosaobo 添加倒序查询 
	 * 
	 * @param unknown_type $uid
	 * @param unknown_type $offset
	 * @param unknown_type $pageSize
	 * @param array $condition
	 * @param unknown_type $isLimit
	 * @return multitype:multitype: number Ambigous <string, mixed, unknown>
	 */
	public function getUserGiftSendRecordsByUid($uid=null,$offset=0,$pageSize=10,array $condition=array(),$isLimit=true){
		$result = array();
		$result['count'] = 0;
		$result['remDuplicateCount'] = 0;
		$result['pipieggSum'] = 0;
		$result['list'] = array();
		
		$criteria = $this->getDbCriteria();
		if($uid){
			$condition['uid']=$uid;
		}
		
		if (!empty($condition['start_time'])){
			$criteria->addCondition('create_time>='.strtotime($condition['start_time']));
			unset($condition['start_time']);
		}
		
		if (!empty($condition['end_time'])){
			$criteria->addCondition('create_time<='.strtotime($condition['end_time']));
			unset($condition['end_time']);
		}
		
		if (isset($condition['uid'])){
			if ($condition['uid']){
				$criteria->compare('uid', $condition['uid']);
			}
			unset($condition['uid']);
		}
		
		if (isset($condition['uids'])){
			if ($condition['uids']){
				$criteria->compare('uid', $condition['uids']);
			}
			unset($condition['uids']);
		}
				
		$result['count'] = $this->count($criteria);

		//去重后的用户总数
		$criteria->group = 'uid';
		$result['remDuplicateCount'] = $this->count($criteria);
		$criteria->group = '';
		
		//总皮蛋
		$criteria->select = 'sum(pipiegg)';
		$result['pipieggSum'] = $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryScalar();
		$criteria->select = '*';
		
		if ($isLimit){
			$criteria->limit=$pageSize;
			$criteria->offset = $offset;
		}
		$criteria->order = ' create_time desc';
		
		if($data = $this->findAll($criteria)){
			foreach($data as $d){
				$result['list'][$d->attributes['record_id']] = $d->attributes;
			}
		}
		
		return  $result;
	}
	
	/**
	 * 获取用户送礼统计
	 * 
	 * @author supeng
	 * @param unknown_type $uid
	 * @param unknown_type $offset
	 * @param unknown_type $pageSize
	 * @param array $condition
	 * @param unknown_type $isLimit
	 * @return multitype:multitype: number NULL mixed 
	 */
	public function getUserGiftStatByUid($uid=null,$offset=0,$pageSize=10,array $condition=array(),$isLimit=true){
		$result = array();
		$result['count'] = 0;
		$result['list'] = array();
	
		$criteria = $this->getDbCriteria();
		$criteria->select = 'sum(pipiegg) as sum_pipiegg,uid,to_uid,info,sum(charm_points) as sum_charm_points';
		$criteria->group = 'uid,to_uid';
		if($uid){
			$condition['uid']=$uid;
		}
	
		if (!empty($condition['start_time'])){
			$criteria->addCondition('create_time>='.strtotime($condition['start_time']));
		}
	
		if (!empty($condition['end_time'])){
			$criteria->addCondition('create_time<'.strtotime($condition['end_time']));
		}
	
		if (!empty($condition['uid'])){
			$criteria->compare('uid', $condition['uid']);
		}
		
		if (!empty($condition['uids'])){
			$criteria->compare('uid', $condition['uids']);
		}
		
		if (!empty($condition['to_uid'])){
			$criteria->compare('to_uid', $condition['to_uid']);
		}
	
		$result['count'] = $this->getCommandBuilder()->createCountCommand($this->tableName(), $criteria)->queryScalar();
		if ($isLimit){
			$criteria->limit=$pageSize;
			$criteria->offset = $offset;
		}
		$criteria->order = ' uid desc';
		$result['list'] = $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
	
		return  $result;
	}
	
	public function getGiftRecordsSumByTargetIds(Array $targetIds){
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('target_id', $targetIds);
		$criteria->select = 'sum(`pipiegg`) as consume_many,count(distinct uid) as send_total,target_id';
		$criteria->group = 'target_id';
	
		return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
	}
	
	public function getGiftRecordsSumByTargetId(Array $targetId,$condition=array()){
		$criteria = $this->getDbCriteria();
		$criteria->compare('target_id', $targetId);
		$criteria->select = 'sum(`pipiegg`) as consume_many,count(distinct uid) as send_total,target_id';
		$criteria->group = 'target_id';
	
		if(!empty($condition['live_time'])){
			$criteria->addCondition('create_time>='.$condition['live_time']);
		}
		
		if(!empty($condition['end_time'])){
			$criteria->addCondition('create_time<='.$condition['end_time']);
		}
		
		return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
	}
	
	public function  sumUserConsumePipieggsByTime($uid,$startTime,$endTime){
		$criteria = $this->getDbCriteria();
		$criteria->condition = 'uid = :uid and create_time >= :startTime and create_time <= :endTime';
		$criteria->select = 'sum(`pipiegg`) as consume_many';
		$criteria->params = array(':uid'=>$uid,':startTime'=>$startTime,':endTime'=>$endTime);
		return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryScalar();
	}
	/**
	 * 获取送礼记录
	 * 
	 * @author guoshaobo  添加查询参数, 添加倒序查询
	 * 
	 * @param unknown_type $uid
	 * @param unknown_type $offset
	 * @param unknown_type $pageSize
	 * @param array $condition
	 * @param unknown_type $isLimit
	 * @return multitype:multitype: number mixed Ambigous <multitype:, mixed>
	 */
	public function getUserGiftReceiveRecordsByUid($uid,$offset=0,$pageSize=10,array $condition=array(),$isLimit){
		$result = array();
		$result['count'] = 0;
		$result['list']  = array();
		
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->alias = 'a';
		$criteria->select = 'a.*,b.uid as send_uid';
		$criteria->join = 'JOIN web_user_giftsend_relation_records b ON a.record_id=b.record_id';
		$criteria->condition=' b.uid=:uid AND b.is_onwer=:is_onwer ';
		$criteria->params[':uid']=$uid;
		$criteria->params[':is_onwer']=0;
		if(!empty($condition['start_time'])){
			$criteria->condition.=' AND a.create_time >= :start_time ';
			$criteria->params[':start_time']=strtotime($condition['start_time']);
		}
		
		if(!empty($condition['end_time'])){
			$criteria->condition.=' AND a.create_time <= :end_time ';
			$criteria->params[':end_time']=strtotime($condition['end_time']);
		}
		
		if(isset($condition['recevier_type'])){
			$criteria->condition.=' AND a.recevier_type = :recevier_type ';
			$criteria->params[':recevier_type'] = $condition['recevier_type'];
		}
		
		$result['count'] = array_shift($this->getCommandBuilder()->createCountCommand($this->tableName(), $criteria)->queryRow());
		if($isLimit){
			$criteria->limit=$pageSize;
			$criteria->offset=$offset;
		}
		$criteria->order='b.create_time DESC';
		$result['list'] = $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
		return $result;
	}
	
	public function countUserGiftReceiveRecordsByUid($uid, $condition=array())
	{
		$dbCommand = $this->getDbCommand();
		$where = "";
		if(isset($condition['start_time']) && $condition['start_time']>0) {
			$where .= " and b.create_time>= " . strtotime($condition['start_time']);
		}
		if (!empty($condition['end_time']) && $condition['end_time']>0){
			$where .= " and b.create_time <= " . strtotime($condition['end_time']);
		}
		$dbCommand->text = ' SELECT count(*) as `count`,
						sum(num) as num, 
						sum(pipiegg) as pipiegg,
						sum(egg_points) as egg_points,
						sum(charm_points) as charm_points,
						sum(dedication) as dedication
					 FROM '.$this->tableName().' a 
					 left join web_user_giftsend_relation_records b 
					 		on a.record_id = b.record_id 
					 where b.uid= ' . $uid . ' and b.is_onwer=0 ' .$where .'   LIMIT 1';
		$result = $dbCommand->queryRow();// queryScalar();
		return $result;
	}
	
	/**
	 * @param int $archivesId
	 * @param int $offset
	 * @param int $pageSize
	 * @param array $condition
	 * @return array
	 */
	public function getArchivesGiftByArchivesId($archivesId,$offset=1,$pageSize=10,array $condition=array()){
		$criteria = $this->getDbCriteria();
		$condition['target_id']=$archivesId;
		$condition&&$criteria->addColumnCondition($condition);
		$criteria->limit=$pageSize;
		$criteria->offset = $offset;
		return $this->findAll($criteria);
	}
	
	public function searchGiftRecordsByTargetIds(Array $targetIds,Array $condition = array()){
		$criteria = $this->getDbCriteria();
		$criteria->alias = 'a';
		$criteria->select = "a.target_id,FROM_UNIXTIME(a.create_time,'%Y-%m-%d') as create_time,sum(a.charm_points) as charm_points";
		$criteria->group = 'target_id,create_time';
		$criteria->join = ' LEFT JOIN web_user_giftsend_relation_records b ON b.record_id=a.record_id';
		
		$criteria->addCondition('b.is_onwer=1');
		$criteria->addInCondition('a.target_id', $targetIds);
		
		if (!empty($condition['live_time_on'])){
			$ontime = strtotime($condition['live_time_on']);
			$criteria->addCondition('a.create_time>='.$ontime);
			$endtime = strtotime('+1 months',$ontime);
			$criteria->addCondition('a.create_time<'.$endtime);
		}else{
			$time = date('Y-m',strtotime('-1 months',time()));
			$ontime = strtotime($time);
			$criteria->addCondition('a.create_time>='.$ontime);
			$endtime = strtotime('+1 months',$ontime);
			$criteria->addCondition('a.create_time<'.$endtime);
		}
		return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
	}
	
	public function searchGiftRecordsByUids(Array $uids,Array $condition = array()){
		$criteria = $this->getDbCriteria();
		$criteria->alias = 'a';
		$criteria->select = "a.uid,sum(a.charm_points) as charm_points";
		$criteria->group = 'uid';
		$criteria->join = ' LEFT JOIN web_user_giftsend_relation_records b ON b.record_id=a.record_id';
	
		$criteria->addCondition('b.is_onwer=1');
		$criteria->addInCondition('b.uid', $uids);
	
		if (!empty($condition['live_time_on'])){
			$ontime = strtotime($condition['live_time_on']);
			$criteria->addCondition('a.create_time>='.$ontime);
		}
		
		if (!empty($condition['live_time_end'])){
			$endtime = strtotime($condition['live_time_end']);
			$criteria->addCondition('a.create_time<'.$endtime);
		}
		
		return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
	}
	
	/**
	 * 获取从直播间购买礼物的统计
	 * 
	 * @author supeng
	 * @param array $giftIds
	 * @param unknown_type $startTime
	 * @param unknown_type $endTime
	 * @return mixed
	 */
	public function getSumSendGiftRecords(Array $giftIds,$startTime=null,$endTime=null){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->alias = 'a';
		$criteria->select = 'sum(a.num) as num, a.gift_id';
		$criteria->join = 'JOIN web_user_giftsend_relation_records b ON a.record_id=b.record_id';
		$criteria->group = 'gift_id';
		
		$criteria->compare('b.is_onwer', 1);
		$criteria->compare('a.gift_id', $giftIds);
		$criteria->compare('a.gift_type', 0);
		
		if($startTime){
			$criteria->addCondition('a.create_time>='.$startTime);
		}
		
		if($endTime){
			$criteria->addCondition('a.create_time<'.$endTime);
		}
	
		return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
	}
	
	/**
	 * 统计某礼物在某些主播的主播收礼个数
	 * @author hexin
	 * @param int $gift_id
	 * @param array $uids
	 * @param int is_owner
	 * @param int $start_time
	 * @param int $end_time
	 * @return array
	 */
	public function getGiftSumToDoteys($gift_id, array $uids, $start_time = null, $end_time = null)
	{
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->alias = 'a';
		$criteria->select = "b.uid, sum(a.num) as num";
		$criteria->join = 'JOIN web_user_giftsend_relation_records b ON a.record_id=b.record_id';
		$criteria->group = 'b.uid';
		
		$criteria->addInCondition('b.uid', $uids);
		$criteria->addCondition('b.is_onwer = 0');
		if(!empty($start_time)){
			$criteria->addCondition('b.create_time>' . $start_time);
		}
		if(!empty($end_time)){
			$criteria->addCondition('b.create_time<=' . $end_time);
		}
		
		$criteria->addCondition('a.gift_id = ' . $gift_id);
		
		return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
	}
	
	/**
	 * 统计某礼物在某些主播的用户送礼个数
	 * @author hexin
	 * @param int $gift_id
	 * @param array $uids
	 * @param int $startTime
	 * @param int $endTime
	 * @return array
	 */
	public function getGiftSumFromUsers($gift_id, array $uids, $start_time = null, $end_time = null){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->alias = 'a';
		$criteria->select = "a.uid, sum(a.num) as num";
		$criteria->join = 'JOIN web_user_giftsend_relation_records b ON a.record_id=b.record_id';
		$criteria->group = 'a.uid';
		
		$criteria->addInCondition('b.uid', $uids);
		$criteria->addCondition('b.is_onwer=0');
		if(!empty($start_time)){
			$criteria->addCondition('b.create_time>' . $start_time);
		}
		if(!empty($end_time)){
			$criteria->addCondition('b.create_time<=' . $end_time);
		}
		
		$criteria->addCondition('a.gift_id =' . $gift_id);
		
		return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
	}
}

?>