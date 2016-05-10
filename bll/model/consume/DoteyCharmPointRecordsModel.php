<?php
/**
 * 主播魅力值变化记录数据访问层
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: DoteyCharmPointRecordsModel.php 16722 2013-11-26 09:26:59Z hexin $ 
 * @package model
 * @subpackage consume 
 */
class DoteyCharmPointRecordsModel extends PipiActiveRecord {

	public function tableName(){
		return '{{dotey_charmpoints_records}}';
	}
	
	/**
	 * @param string $className
	 * @return DoteyCharmPointRecordsModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function getDbConnection(){
		return Yii::app()->db_consume_records;
	}
	

	/**
	 * @author supeng
	 * @param array $condition
	 * @param unknown_type $offset
	 * @param unknown_type $pageSize
	 * @param unknown_type $isLimit
	 */
	public function getCharmPointsAwardByCondition(Array $condition = array(),$offset = 0, $pageSize = 10,$isLimit = true){
		$result = array();
		$result['count'] = 0;
		$result['list'] = array();
		
		$criteria = $this->getDbCriteria();
		$criteria->compare('source', SOURCE_SENDS);
		$criteria->compare('sub_source', SUBSOURCE_SENDS_ADMIN);
		$criteria->compare('client', CLIENT_ADMIN);
		
		//获取明细记录
		if (!empty($condition['record_id'])){
			if (is_array($condition['record_id'])){
				$criteria->addInCondition('record_id',$condition['record_id']);
			}else{
				$criteria->addCondition('record_id='.intval($condition['record_id']));
			}
			$result['list'] = $this->findAll($criteria);
			return $result;
		}
		
		//是否已经被撤销
		if (!empty($condition['target_id'])){
			if (is_array($condition['target_id'])){
				$criteria->addInCondition('target_id',$condition['target_id']);
			}else{
				$criteria->addCondition('target_id='.intval($condition['target_id']));
			}
			$result['list'] = $this->findAll($criteria);
			return $result;
		}
		
		$criteria->addCondition('target_id=0');
		
		if (!empty($condition['create_time_on'])){
			$criteria->addCondition('create_time>='.strtotime($condition['create_time_on']));
		}
		
		if (!empty($condition['create_time_end'])){
			$criteria->addCondition('create_time<'.strtotime($condition['create_time_end']));
		}
		
		if (!empty($condition['uid'])){
			if (is_array($condition['uid'])){
				$criteria->addInCondition('uid', $condition['uid']);
			}else{
				$criteria->addCondition('uid='.intval($condition['uid']));
			}
		}
		
		$result['count'] = $this->count($criteria);
		if ($isLimit){
			$criteria->limit = $pageSize;
			$criteria->offset = $offset;
		}
		$criteria->order = 'create_time DESC';
		$result['list'] = $this->findAll($criteria);
		return $result;
	}

	/**
	 * @author supeng
	 * @param array $condition
	 * @param unknown_type $offset
	 * @param unknown_type $pageSize
	 * @param unknown_type $isLimit
	 */
	public function getCharmPointsByCondition(Array $condition = array(),$offset = 0, $pageSize = 10,$isLimit = true){
		$result = array();
		$result['count'] = 0;
		$result['list'] = array();
	
		$criteria = $this->getDbCriteria();
		
		if (!empty($condition['source'])){
			$criteria->compare('source', $condition['source']);
		}
		
		if (!empty($condition['sub_source'])){
			$criteria->compare('sub_source', $condition['sub_source']);
		}
		
		if (!empty($condition['client'])){
			$criteria->compare('client', $condition['client']);
		}
		
		//获取明细记录
		if (!empty($condition['record_id'])){
			$criteria->compare('record_id', $condition['record_id']);
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
	
		$result['count'] = $this->count($criteria);
		if ($isLimit){
			$criteria->limit = $pageSize;
			$criteria->offset = $offset;
		}
		$criteria->order = 'create_time DESC';
		$result['list'] = $this->findAll($criteria);
		return $result;
	}
	
	/**
	 * @author supeng
	 * @param unknown_type $doteyIds
	 * @param unknown_type $condition
	 * @return mixed
	 */
	public function getDoteyCharmPointsRecords($doteyIds,$condition)
	{
		$criteria = $this->getDbCriteria();
		$criteria->select = ' uid, sum(charm_points) points';
		
		if(isset($condition['stime']) && $condition['stime']>=0){
			$criteria->condition .= ' create_time>=:stime';
			$criteria->params += array(':stime'=>$condition['stime']);
		}
		
		if(isset($condition['etime']) && $condition['etime']>=0){
			$criteria->condition .= ' and create_time<=:etime';
			$criteria->params += array(':etime'=>$condition['etime']);
		}
		
		//按月的范围统计 
		if(isset($condition['monthTime'])){
			$stime = strtotime($condition['monthTime']);
			$criteria->addCondition('create_time >='.$stime);
			$etime = strtotime('+1 months',$stime);
			$criteria->addCondition('create_time <'.$etime);
		}
		
		$criteria->addInCondition('uid', $doteyIds);
		$criteria->group = 'uid';

		return $this->getCommandBuilder()->createFindCommand($this->tableName(),$criteria)->queryAll();
	}
	
	/**
	 * 获取被取消的魅力点记录
	 * 
	 * @author supeng
	 * @param unknown_type $doteyIds
	 * @param unknown_type $condition
	 * @return mixed
	 */
	public function getMonthDoteyCharmPoints($doteyIds,$condition)
	{
		//按月的范围统计
		$stime = strtotime($condition['monthTime']);
		$etime = strtotime('+1 months',$stime);
		if(!empty($condition['start_time']) && !empty($condition['end_time'])){
			$stime = strtotime($condition['start_time']);
			$etime = strtotime($condition['end_time']);
		}
		$dbCommand = $this->getDbCommand();
		
		$sql = " SELECT 
					uid, sum(charm_points) points 
				FROM 
					web_dotey_charmpoints_records 
				WHERE 
					uid IN(".implode(',', $doteyIds).") AND  charm_points>0 AND 
					create_time >= $stime AND 
					create_time < $etime  
				GROUP BY uid ";
		$dbCommand->setText($sql);
		$list = $dbCommand->queryAll();
		$dbCommand->reset();
		
		return $list;
	}
	
	/**
	 * 获取被取消的的魅力点目标ids
	 * 
	 * @author supeng
	 * @param unknown_type $doteyIds
	 * @param unknown_type $condition
	 * @return mixed
	 */
	public function getNotInMonthDoteyTargetIds($doteyIds,$condition)
	{
		//按月的范围统计
		$stime = strtotime($condition['monthTime']);
		$etime = strtotime('+1 months',$stime);
		$dbCommand = $this->getDbCommand();
	
		$sql = " 
			SELECT
				target_id
			FROM web_dotey_charmpoints_records
			WHERE
				uid IN(".implode(',', $doteyIds).") AND
				client='".CLIENT_ADMIN."' AND
				source='".SOURCE_SENDS."' AND
				sub_source='".SUBSOURCE_SENDS_ADMIN."' AND
				create_time >= $stime AND
				create_time < $etime";
		
		$dbCommand->setText($sql);
		$list = $dbCommand->queryAll();
		$dbCommand->reset();
		return $list;
	}
	
	public function countDoteyCharmPointsBuSendUid($dotey_id, $offset, $limit)
	{
		$criteria = $this->getDbCriteria();
		$criteria->select = ' sender_uid, sum(`charm_points`) as points';
		$criteria->condition = ' uid=:uid';
		$criteria->params = array(':uid'=>$dotey_id);
// 		$criteria->addInCondition('sender_uid', $sendUids);
		$criteria->order = 'points desc';
		$criteria->group = 'sender_uid';
		
		$count = $this->count($criteria);
		
		$criteria->offset = $offset;
		$criteria->limit = $limit;
		
		$list = $this->getCommandBuilder()->createFindCommand($this->tableName(),$criteria)->queryAll();
		
		return array('count'=>$count, 'list'=>$list);
	}

	/**
	 * @author zhangzhifan
	 * @param array $condition
	 */
	public function getCharmPointsRecordsCountByCondition(Array $condition = array()){
	
		$criteria = $this->getDbCriteria();
		if (is_array($condition['uid'])){
			$criteria->addInCondition('uid', $condition['uid']);
		}else{
			$criteria->addCondition('uid='.intval($condition['uid']));
		}
		$criteria->compare('source', $condition['source']);
		$criteria->compare('sub_source', $condition['sub_source']);
		$criteria->compare('client', $condition['client']);
		$criteria->compare('info', $condition['info']);
		$counts = $this->count($criteria);
		return $counts;
	}
	
	/**
	 * 获取被取消的魅力点记录
	 * 
	 * @author supeng
	 * @param unknown_type $doteyIds
	 * @param unknown_type $condition
	 * @return mixed
	 */
	public function sumDoteyTimeCharmPointsByUid($uid,$startTime,$endTime,$filter_uids = array()){
		$criteria = $this->getDbCriteria();
		$criteria->select = 'sum(charm_points)';
		$criteria->condition = 'uid = :uid AND create_time >= :startTime AND create_time <= :endTime'.(!empty($filter_uids) && is_array($filter_uids) ? ' AND sender_uid in('.implode(',', $filter_uids).')' : '');
		$criteria->params = array(':uid'=>$uid,':startTime'=>$startTime,':endTime'=>$endTime);
		return $this->getCommandBuilder()->createFindCommand($this->tableName(),$criteria)->queryScalar();
	}
}

?>