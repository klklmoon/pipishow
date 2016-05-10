<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: LiveRecordsModel.php 16722 2013-11-26 09:26:59Z hexin $ 
 * @package model
 * @subpackage archives
 */
class LiveRecordsModel extends PipiActiveRecord {
	/**
	 * @param unknown_type $className
	 * @return LiveRecordsModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{live_records}}';
	}
	
	
	public function getDbConnection(){
		return Yii::app()->db_archives;
	}
	
	public function rules(){
		return array(
			array('archives_id,','required'),
		);
	}
	
	public function attributeLabels(){
		return array(
			'archives_id'=>'档期ID',
			'start_time'=>'开播时间',
			'end_time' =>'结束时间',
			'duration'=>'直播时长',
		);
	}
	
	/**
	 * 根据直播记录Id获取记录信息
	 * @param array $recordIds 直播记录ID
	 * @return array
	 */
	public function getLiveRecordByRecordIds(array $recordIds){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->addInCondition('record_id',$recordIds);
		return $this->findAll($criteria);
	}
	
	/**
	 * 根据档期ID获取最近的直播记录
	 * @param int $archiveId
	 * @return array
	 */
	public function getLiveRecordByarchiveId($archivesId){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->addColumnCondition(array('archives_id'=>$archivesId));
		$criteria->select='max(record_id) as record_id,archives_id';
		$criteria->group='archives_id';
		return $this->find($criteria);
	}
	
	
	
	/**
	 * 获取正在直播的档期
	 * 
	 * @return array
	 */
	public function getLivingArchives(){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->condition = 'status = 1';
		$criteria->order='start_time DESC';
		return $this->findAll($criteria);
	}
	
	/**
	 * 获取待直播的档期
	 * 
	 * @return array
	 */
	public function getWillLiveArchives(){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->condition = 'status = 0 ';
		$criteria->order='live_time DESC';
		$datas = $this->findAll($criteria);
		echo $this->getDbCommand()->getText();
		return $datas;
	}
	
	/**
	 * 获取档期最后播出的记录信息
	 * 
	 * @param array $archiveIds 档期ID
	 * @return array
	 */
	public function getLatestLiveInfoByArchiveIds(array $archivesIds){
		if(empty($archivesIds)){
			return array();
		}
		$archives = $this->getLatestLiveIdByArchiveIds($archivesIds);
		if(empty($archives)){
			return array();
		}
		$recordIds = array();
		foreach($archives as $archive){
			$recordIds[] = $archive['record_id'];
		}
		/* @var $criteria CDbCriteria */
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->addInCondition('record_id',$recordIds);
		return $this->findAll($criteria);
		
	}
	/**
	 * 获取档期最后播出的记录
	 * 
	 * @param array $archiveIds 档期ID
	 * @return array
	 */
	public function getLatestLiveIdByArchiveIds(array $archiveIds){
		if(empty($archiveIds)){
			return array();
		}
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->select = 'archives_id,max(record_id) record_id';
		$criteria->group = 'archives_id';
		$criteria->addInCondition('archives_id',$archiveIds);
		return $this->findAll($criteria);
	}
	
	/**
	 * 获取档期最后播出的记录
	 * @return array
	 */
	public function getLatestLiveRecord(){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->select = 'archives_id,max(record_id) record_id';
		$criteria->group = 'archives_id';
		return $this->findAll($criteria);
	}
	
	
	public function getLiveRecordsByFilter(Array $conditions){
		$criteria = $this->getDbCriteria();
		$criteria->select = 'b.uid';
		$criteria->distinct = 'b.uid';
		$criteria->alias = 'a';
		$criteria->join = 'LEFT JOIN web_archives b ON b.archives_id=a.archives_id';
		
		$criteria->condition = 'a.status = :status';
		$criteria->params[':status'] = 2;
		
		if (isset($conditions['start_time'])){
			$criteria->condition .= ($criteria->condition ? ' AND ' : '').'a.start_time >= :start_time';
			$criteria->params[':start_time'] = $conditions['start_time'];
		}
		
		if (isset($conditions['end_time'])){
			$criteria->condition .= ($criteria->condition ? ' AND ' : '').'a.start_time <= :end_time';
			$criteria->params[':end_time'] = $conditions['end_time'];
		}
		
		
		if (isset($conditions['archives_id'])){
			$criteria->condition .= ($criteria->condition ? ' AND ' : '').'a.archives_id = :archives_id';
			$criteria->params[':archives_id'] = $conditions['archives_id'];
		}
		
		return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();;
	}
	
	/**
	 * 根据条件查询直播记录
	 */
	public function getLiveRecordsByCondition($archivesIds, $condition, $offset = 0, $limit = 10)
	{
		$criteria = $this->getDbCriteria();
		
// 		$criteria->addCondition('status =2 ');
		if (!empty($condition['start_time'])){
			$criteria->addCondition('live_time>='.$condition['start_time']);
		}
		
		if (!empty($condition['end_time'])){
			$criteria->addCondition('live_time<='.$condition['end_time']);
		}
		$criteria->addInCondition('archives_id',$archivesIds);
		$count = $this->count($criteria);
		
		$criteria->order = 'live_time desc';
		$criteria->offset = $offset;
		$criteria->limit = $limit;
// 		$criteria->order = 'live_time desc';
		$list = $this->findAll($criteria);
		return array('count'=>$count,'list'=>$list);
	}
	
	public function getLiveRecords($archivesIds, array $condition = array()){
		$criteria = $this->getDbCriteria();
		if (isset($condition['startTime'])){
			$criteria->addCondition('live_time>='.$condition['startTime']);
		}
		
		if (isset($condition['endTime'])){
			$criteria->addCondition('live_time<='.$condition['endTime']);
		}
		$criteria->addInCondition('archives_id',$archivesIds);
		$criteria->addInCondition('status',array(1,2));
		$criteria->order = 'live_time desc';
		return $this->findAll($criteria);
	}
	/**
	 * 根据月份统计直播记录
	 */
	public function getLiveRecordsByMonth($archivesIds, $condition)
	{
		$criteria = $this->getDbCriteria();
		$criteria->select = "*";
		$criteria->addInCondition('archives_id', $archivesIds);
		$criteria->compare('status', '2');
		$criteria->addBetweenCondition('live_time', $condition['start_time'], $condition['end_time']);
		$criteria->order = 'live_time asc';
// 		$criteria->limit = 100;
		$data = $this->findAll($criteria);
		return $data;
	}
	
	/**
	 * 查询直播记录
	 *
	 * @author supeng
	 * @param array $condition
	 * @param unknown_type $offset
	 * @param unknown_type $pageSize
	 * @param unknown_type $isLimit
	 */
	public function searchLiveRecordsByCondition(Array $condition,$offset=0,$pageSize=10,$isLimit=true){
		$result = array();
		$result['count'] = 0;
		$result['list'] = array();
	
		$criteria = $this->getDbCriteria();
		$criteria->alias = 'a';
		$criteria->join = ' LEFT JOIN web_archives b ON b.archives_id = a.archives_id ';
		
		if (!empty($condition['group'])){
			$criteria->select = 'b.*';
			$criteria->group = $condition['group'];
		}else{
			$criteria->select = 'a.*,b.*,a.create_time as live_ctime,a.archives_id as archives_id';
		}
		
		if (!empty($condition['record_id'])){
			$criteria->compare('a.record_id', $condition['record_id']);
		}
		
		if (!empty($condition['uid'])){
			$criteria->compare('b.uid', $condition['uid']);
		}
		
		if (!empty($condition['uids'])){
			$criteria->compare('b.uid', $condition['uids']);
		}
	
		if (isset($condition['status']) && is_numeric($condition['status'])){
			$criteria->addCondition(' a.`status`='.intval($condition['status']));
		}
		
		if (!empty($condition['no_status']) && is_array($condition['no_status'])){
			$criteria->addNotInCondition('a.`status`', $condition['no_status']);
		}
	
		if (!empty($condition['cat_id'])){
			$criteria->addCondition(' b.`cat_id`='.intval($condition['cat_id']));
		}
	
		if (!empty($condition['live_time_on'])){
			$criteria->addCondition(' a.`live_time`>='.strtotime($condition['live_time_on']));
		}
	
		if (!empty($condition['live_time_end'])){
			$criteria->addCondition(' a.`live_time`<='.strtotime($condition['live_time_end']));
		}
		
		if (isset($condition['is_hide']) && is_numeric($condition['is_hide'])){
			$criteria->addCondition(' b.`is_hide`='.$condition['is_hide']);
		}
	
		$result['count'] = array_shift($this->getCommandBuilder()->createCountCommand($this->tableName(), $criteria)->queryRow());
		if($isLimit){
			$criteria->offset = $offset;
			$criteria->limit = $pageSize;
			$criteria->order= ' a.live_time DESC';
		}
		$result['list'] = $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
		return $result;
	
	}
	
	/**
	 * 去重 后的直播记录及统计
	 * 
	 * @author supeng
	 * @param array $condition
	 * @param unknown_type $offset
	 * @param unknown_type $pageSize
	 * @param unknown_type $isLimit
	 * @return multitype:multitype: number mixed 
	 */
	public function searchDuplicateLiveRecordsByCondition(Array $condition,$offset=0,$pageSize=10,$isLimit=true){
		$result = array();
		$result['count'] = 0;
		$result['list'] = array();
	
		$command = $this->getDbCommand();
		$where = '';
		if (!empty($condition['uid'])){
			$where .= ' b.uid='.$condition['uid'].' AND ';
		}
		
		if (!empty($condition['uids'])){
			$where .= ' b.uid IN('.implode(',', $condition['uids']).') AND ';
		}
		
		if (isset($condition['status']) && is_numeric($condition['status'])){
			$where .= ' a.`status`='.intval($condition['status']).' AND ';
		}
		
		if (isset($condition['no_status']) && is_numeric($condition['no_status'])){
			$where .= ' a.`status NOT IN('.implode(',', $condition['uid']).') AND ';
		}
		
		if (!empty($condition['live_time_on'])){
			$where .= ' a.`live_time`>='.strtotime($condition['live_time_on']).' AND ';
		}
		
		if (!empty($condition['live_time_end'])){
			$where .= ' a.`live_time`<='.strtotime($condition['live_time_end']).' AND ';
		}
		
		if (isset($condition['is_hide']) && is_numeric($condition['is_hide'])){
			$where .= ' b.`is_hide`='.$condition['is_hide'].' AND ';
		}
		
		if($where){
			$where = ' WHERE '.trim(trim($where,' '),'AND');
		}

		$limit = " LIMIT {$offset},{$pageSize}";
		
		$listSql = ' 
			SELECT c.*,d.*,c.duration as duration
			FROM (
					SELECT
						count(a.record_id) as recordCount, 
						max(a.record_id) as max_record_id,
						sum(a.duration) as duration,
						b.*
					FROM web_live_records a 
					LEFT JOIN web_archives b ON b.archives_id = a.archives_id
					%s
					GROUP BY a.archives_id
					%s
				) c,web_live_records d 
			WHERE c.max_record_id=d.record_id';
		
		$countSql = '
			SELECT
				count(distinct(a.archives_id)) as count
			FROM web_live_records a 
			LEFT JOIN web_archives b ON b.archives_id = a.archives_id
			%s';
		$countSql = sprintf($countSql,$where);
		$result['count'] =  $command->setText($countSql)->queryScalar();
		
		$listSql = sprintf($listSql,$where,$limit);
		$result['list'] = $command->setText($listSql)->queryAll();
		return $result;
	
	}
	
	public function searchLiveRecordByArchivesIds(Array $archivesIds ,Array $condition){
		$criteria = $this->getDbCriteria();
		$criteria->select = "archives_id,FROM_UNIXTIME(live_time,'%Y-%m-%d') as end_time,sum(duration) as duration";
		$criteria->group = "archives_id,FROM_UNIXTIME(live_time,'%Y-%m-%d')";
		$criteria->addInCondition('archives_id', $archivesIds);

		if (!empty($condition['live_time_on'])){
			$ontime = strtotime($condition['live_time_on']);
			$criteria->addCondition('live_time>='.$ontime);
			$endtime = strtotime('+1 months',$ontime);
			$criteria->addCondition('live_time<'.$endtime);
		}elseif(!empty($condition['live_time_start']) && !empty($condition['live_time_end'])){
			$criteria->addCondition('live_time>='.strtotime($condition['live_time_start']));
			$criteria->addCondition('live_time<='.strtotime($condition['live_time_end']));
		}else{
			$time = date('Y-m',time());
			$ontime = strtotime($time);
			$criteria->addCondition('live_time>='.$ontime);
			$endtime = strtotime('+1 months',$ontime);
			$criteria->addCondition('live_time<'.$endtime);
		}
		return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
	}
	
	/**
	 * @author supeng
	 * @param unknown_type $uids
	 * @param unknown_type $cat_ids
	 * @return mixed
	 */
	public function statiticsLiveRecords($uids,$cat_ids){
		$criteria = $this->getDbCriteria();
		$criteria->alias = 'a';
		$criteria->group = 'b.uid';
		$criteria->join = 'LEFT JOIN web_archives b ON b.archives_id=a.archives_id';
		$criteria->select = 'min(a.live_time) as first_live_time,max(a.live_time) as last_live_time,count(a.live_time) as count_lives,sum(a.duration) as sum_duration,b.uid,b.archives_id';
		
		$criteria->compare('a.status', 2);
		$criteria->compare('b.cat_id',$cat_ids);
		$criteria->compare('b.uid', $uids);
	
		return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();;
	}
	
	/**
	 * 活动（守护天使）获取幸运主播
	 * @author supeng
	 */
	public function getLuckDoteyList($condition)
	{
		$criteria = $this->getDbCriteria();
		$criteria->alias = 'a';
		$criteria->join = ' LEFT JOIN web_archives b ON b.archives_id = a.archives_id ';
		$criteria->select = 'DISTINCT(b.uid) as uid,b.title';
		$criteria->compare('a.status', 2);
		$criteria->compare('b.is_hide', 0);
		
		if (!empty($condition['live_time_on'])){
			$criteria->addCondition(' a.`live_time`>='.strtotime($condition['live_time_on']));
		}
		
		if (!empty($condition['live_time_end'])){
			$criteria->addCondition(' a.`live_time`<='.strtotime($condition['live_time_end']));
		}
		return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
	}
}

?>