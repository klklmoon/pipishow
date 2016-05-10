<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: lei wei $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z lei wei  $ 
 * @package 
 */
class UserSongModel extends PipiActiveRecord {
	/**
	 * @param unknown_type $className
	 * @return UserSongModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{user_song}}';
	}
	
	
	public function getDbConnection(){
		return Yii::app()->db_consume;
	}
	
	public function rules(){
		return array(
			array('uid,to_uid','numerical'),
		);
	}
	
	public function attributeLabels(){
		return array(
			'uid'=>'用户uid',
			'to_uid'=>'主播uid',
		);
	}
	
	/**
	 * 根据记录Id获取点歌记录
	 * @param array $recordIds 记录Id
	 * @return array
	 */
	public function getUserSongRecordsByRecordIds(array $recordIds){
		if(empty($recordIds)) return array();
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('record_id', $recordIds);
		return $this->findAll($criteria);
	}
	
	/**
	 * 获取主播未处理的点个记录
	 * @param int $doteyId  主播uid
	 * @return array
	 */
	public function getUnhandleUserSongRecordsBydoteyId($doteyId){
		if($doteyId<=0) return array();
		$criteria = $this->getDbCriteria();
		$criteria->condition='to_uid=:to_uid AND is_handle=:is_handle';
		$criteria->params[':to_uid']=$doteyId;
		$criteria->params[':is_handle']=0;
		return $this->findAll($criteria);
	}
	
	
	/**
	 * 根据记录id获取在主播未处理的歌曲记录的位置
	 * @param int $record_id 点歌记录Id
	 * @param int $dotey_id 主播uid
	 * @return int
	 */
	public function getCountUserSongRecordsByRecordId($record_id,$dotey_id){
		if($record_id<=0) return array();
		$criteria = $this->getDbCriteria();
		$criteria->condition = 'record_id <= :record_id AND to_uid=:to_uid AND is_handle=:is_handle';
	 	$criteria->order='record_id ASC ';
	 	$criteria->params[':record_id'] = $record_id;
	 	$criteria->params[':to_uid'] = $dotey_id;
	 	$criteria->params[':is_handle'] = 0;
	 	return $this->count($criteria);
	}
	
	public function searchSongByTargetIds(Array $targetIds,Array $condition = array()){
		$criteria = $this->getDbCriteria();
		$criteria->select = "target_id,FROM_UNIXTIME(update_time,'%Y-%m-%d') as update_time,sum(charm_points) as charm_points";
		$criteria->group = 'target_id,update_time';
	
		$criteria->addCondition('is_handle=1');
		$criteria->addInCondition('target_id', $targetIds);
	
		if (!empty($condition['live_time_on'])){
			$ontime = strtotime($condition['live_time_on']);
			$criteria->addCondition('update_time>='.$ontime);
			$endtime = strtotime('+1 months',$ontime);
			$criteria->addCondition('update_time<'.$endtime);
		}else{
			$time = date('Y-m',strtotime('-1 months',time()));
			$ontime = strtotime($time);
			$criteria->addCondition('update_time>='.$ontime);
			$endtime = strtotime('+1 months',$ontime);
			$criteria->addCondition('update_time<'.$endtime);
		}
		return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
	}
	
	public function searchSongByToUids(Array $uids,Array $condition = array()){
		$criteria = $this->getDbCriteria();
		$criteria->select = "to_uid,sum(charm_points) as charm_points, count(*) as nums";
		$criteria->group = 'to_uid';
	
		$criteria->addCondition('is_handle=1');
		$criteria->addInCondition('to_uid', $uids);
	
		if (!empty($condition['live_time_on'])){
			$ontime = strtotime($condition['live_time_on']);
			$criteria->addCondition('update_time>='.$ontime);
		}
		
		if (!empty($condition['live_time_end'])){
			$endtime = strtotime($condition['live_time_end']);
			$criteria->addCondition('update_time<'.$endtime);
		}
		
		return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
	}
	
	/**
	 * 获取点歌记录
	 * 
	 * @author supeng
	 * @param unknown_type $offset
	 * @param unknown_type $limit
	 * @param unknown_type $condition
	 * @param unknown_type $isLimit
	 */
	public function searchVODRecordsByCondition($offset=0,$limit=20,$condition=array(),$isLimit=true){
		$result = array();
		$result['count'] = 0;
		$result['list'] = array();
		//print_r($condition);exit;
		$criteria = $this->getDbCriteria();
		$this->getVODCondition($criteria,$condition);
		
		
		$result['count'] = $this->count($criteria);
		if ($isLimit){
			$criteria->offset = $offset;
			$criteria->limit = $limit;
		}
		$criteria->order = 'create_time DESC';
		$result['list'] = $this->findAll($criteria);
		return $result;
	}
	
	/**
	 * 点歌统计
	 * 
	 * @author supeng
	 * @param unknown_type $offset
	 * @param unknown_type $limit
	 * @param unknown_type $condition
	 * @param unknown_type $isLimit
	 * @return multitype:multitype: number NULL Ambigous <string, unknown, mixed> 
	 */
	public function searchVODStatByCondition($offset=0,$limit=20,$condition=array(),$isLimit=true,$isDotey=true){
		$result = array();
		$result['count'] = 0;
		$result['list'] = array();
		
		$criteria = $this->getDbCriteria();
		$this->getVODCondition($criteria,$condition);
		$criteria->compare('is_handle', 1);
		
		if($isDotey){
			$criteria->select = 'count(1) as count,sum(pipiegg) as sum_pipiegg, sum(charm) as sum_charm,sum(charm_points) as sum_charm_points, sum(dedication) as sum_dedication,sum(egg_points) as sum_egg_points,to_uid';
			$criteria->group = 'to_uid';
		}else{
			$criteria->select = 'count(1) as count,sum(pipiegg) as sum_pipiegg, sum(charm) as sum_charm,sum(charm_points) as sum_charm_points, sum(dedication) as sum_dedication,sum(egg_points) as sum_egg_points,uid';
			$criteria->group = 'uid';
		}
		$criteria->order = 'count DESC';
		
		$result['count'] = $this->getCommandBuilder()->createCountCommand($this->tableName(), $criteria)->queryScalar();
		if ($isLimit){
			$criteria->offset = $offset;
			$criteria->limit = $limit;
		}
		$result['list'] = $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
		return $result;
	}
	
	/**
	 * @author supeng
	 * @param CDbCriteria $criteria
	 * @param array $condition
	 */
	public function getVODCondition(CDbCriteria &$criteria,$condition=array()){
		if (!empty($condition['uid'])){
			$criteria->compare('uid', $condition['uid']);
		}
		
		if (!empty($condition['uids'])){
			$criteria->compare('uid', $condition['uids']);
		}
		
		if (!empty($condition['to_uid'])){
			$criteria->compare('to_uid', $condition['to_uid']);
		}
		
		if (!empty($condition['to_uids'])){
			$criteria->compare('to_uid', $condition['to_uids']);
		}
		
		if (!empty($condition['name'])){
			$criteria->compare('name', $condition['name'],true);
		}
		
		if (!empty($condition['singer'])){
			$criteria->compare('singer', $condition['singer'],true);
		}
		
		if (isset($condition['is_handle']) && $condition['is_handle']>=0){
			$criteria->compare('is_handle', $condition['is_handle']);
		}
		
		if (!empty($condition['start_time'])){
			$criteria->addCondition('create_time>='.strtotime($condition['start_time']));
		}
		
		if (!empty($condition['end_time'])){
			$criteria->addCondition('create_time<'.strtotime($condition['end_time']));
		}
		
		if (!empty($condition['s_update_time'])){
			$criteria->addCondition('update_time>='.strtotime($condition['s_update_time']));
		}
		
		if (!empty($condition['e_update_time'])){
			$criteria->addCondition('update_time<'.strtotime($condition['e_update_time']));
		}
	}
	
}

?>