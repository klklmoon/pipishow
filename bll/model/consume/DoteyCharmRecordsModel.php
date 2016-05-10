<?php
/**
 * 主播魅力点变化记录数据访问层
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: DoteyCharmRecordsModel.php 16624 2013-11-20 05:51:03Z hexin $ 
 * @package model
 * @subpackage consume 
 */
class DoteyCharmRecordsModel extends PipiActiveRecord {

	public function tableName(){
		return '{{dotey_charm_records}}';
	}
	
	/**
	 * @param string $className
	 * @return DoteyCharmPointsRecordsModel
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
	public function getCharmAwardByCondition(Array $condition = array(),$offset = 0, $pageSize = 10,$isLimit = true){
		$result = array();
		$result['count'] = 0;
		$result['list'] = array();
	
		$criteria = $this->getDbCriteria();
		$criteria->compare('source', SOURCE_SENDS);
		$criteria->compare('sub_source', SUBSOURCE_SENDS_ADMIN);
		$criteria->compare('client', CLIENT_ADMIN);
	
		//是否取得明细
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
	public function getCharmByCondition(Array $condition = array(),$offset = 0, $pageSize = 10,$isLimit = true){
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
		
		if(isset($condition['source_arr']) && is_array($condition['source_arr'])){
			$criteria->addInCondition('source', $condition['source_arr']);
		}
		
		if (!empty($condition['client'])){
			$criteria->compare('client', $condition['client']);
		}
		
		//是否取得明细
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
	 * 获取主播fans的魅力值贡献统计
	 * @author guoshaobo 
	 * @param int $dotey_id
	 * @param int $offset
	 * @param int $limit
	 * @return array
	 */
	public function countDoteyCharmBuSendUid($dotey_id, $offset, $limit)
	{
		$criteria = $this->getDbCriteria();
		$criteria->select = ' sender_uid, sum(`charm`) as points';
		$criteria->condition = ' uid=:uid';
		$criteria->params = array(':uid'=>$dotey_id);
		$criteria->order = 'points desc';
		$criteria->group = 'sender_uid';
	
		$count = $this->count($criteria);
	
		$criteria->offset = $offset;
		$criteria->limit = $limit;
	
		$list = $this->getCommandBuilder()->createFindCommand($this->tableName(),$criteria)->queryAll();
	
		return array('count'=>$count, 'list'=>$list);
	}
	
	/**
	 * @author supeng
	 * @param unknown_type $doteyIds
	 * @param unknown_type $condition
	 * @return mixed
	 */
	public function getDoteyCharmRecords($doteyIds,$condition)
	{
		$criteria = $this->getDbCriteria();
		$criteria->select = ' uid, sum(charm) charm';
	
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
}

?>