<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: leiwei $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: ArchivesModel.php 16030 2013-10-17 06:33:35Z leiwei $ 
 * @package model
 * @subpackage archives
 */
class ArchivesModel extends PipiActiveRecord {
	
	/**
	 * @param unknown_type $className
	 * @return ArchivesModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{archives}}';
	}
	
	
	public function getDbConnection(){
		return Yii::app()->db_archives;
	}
	
	public function rules(){
		return array(
			array('title','filter','filter'=>array(new CHtmlPurifier(),'purify')),
			array('cat_id,uid,','required'),
		);
	}
	
	public function attributeLabels(){
		return array(
			'title'=>'档期名称',
			'cat_id'=>'档期分类ID',
			'display' =>'分类是否显示',
			'notice'=>'公聊公告',
			'private_notice'=>'私聊公告',
		);
	}
	
	/**
	 * 根据档期ID获取档期信息
	 * @param int $archivesId  档期ID
	 * @return array
	 */
	public function getArchivesByArchivesId($archivesId){
		$criteria = $this->getDbCriteria();
		$criteria->addColumnCondition(array('archives_id'=>$archivesId));
		return $this->find($criteria);
	}
	
	/**
	 * 根据档期ID获取档期信息
	 * @param array $archivesIds  档期ID
	 * @return array
	 */
	public function getArchivesByArchivesIds(array $archivesIds){
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('archives_id',$archivesIds);
		return $this->findAll($criteria);
	}
	
	
	/**
	 * 根据创建者uid获取档期
	 * @param int $uid  档期创建者uid
	 * @param int $sub_id 分站Id,默认为主站
	 * @return array
	 */
	public function getArchivesByUid($uid,$sub_id=0){
		$criteria = $this->getDbCriteria();
		$criteria->addColumnCondition(array('uid'=>$uid,'sub_id'=>$sub_id));
		return $this->findAll($criteria);
	}
	
	/**
	 * 根据创建者uid获取档期
	 * @param array $uids  档期创建者uid
	 * @param int $sub_id 分站Id,默认为主站
	 * @return array
	 */
	public function getArchivesByUids(array $uids,$sub_id=0){
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('uid',$uids);
		$criteria->addColumnCondition(array('sub_id'=>$sub_id));
		return $this->findAll($criteria);
	}
	
	
	/**
	 * 根据档期类型获取档期
	 * @param array $catIds 分类ID
	 * @return array
	 */
	public function getArchivesByCatIds(array $catIds){
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('cat_id',$catIds);
		return $this->findAll($criteria);
	}
	
	
	/**
	 * 根据条件获取档期信息
	 * @param array $condition  查询条件
	 * @return array
	 */
	public function getArchivesBycondition(array $condition=array()){
		$criteria = $this->getDbCriteria();
		if(isset($condition['uids'])){
			$criteria->compare('uid', $condition['uids']);
			unset($condition['uids']);
		}
		
		if(isset($condition['uid'])){
			$criteria->compare('uid', $condition['uid']);
			unset($condition['uid']);
		}
		
		if(isset($condition['title'])){
			$criteria->compare('title', $condition['title'],true);
			unset($condition['title']);
		}
		
		if ($condition){
			$criteria->addColumnCondition($condition);
		}
		
		return $this->findAll($criteria);
	}
	
	public function getArchives(){
		$criteria = $this->getDbCriteria();
		return $this->findAll($criteria);
	}
	
	
	public function getTodayLiveArchives($num){
		$criteria = $this->getDbCriteria();
		$start_time=time();
		$end_time=strtotime(date('Y-m-d 23:59:59',time()));
		$criteria->alias = 'a';
		$criteria->select ='a.archives_id,a.uid,a.title,a.recommond,b.live_time';
		$criteria->join ="JOIN web_live_records as b ON a.archives_id=b.archives_id WHERE b.start_time>={$start_time} AND b.start_time<={$end_time}";
		$criteria->order='a.recommond desc';
		$criteria->limit=$num;
		return $this->findAll($criteria);
	}
	
	/**
	 * 查询直播直播间
	 *
	 * @author supeng
	 * @param array $condition
	 * @param unknown_type $offset
	 * @param unknown_type $pageSize
	 * @param unknown_type $isLimit
	 */
	public function searchArchivesByCondition(Array $condition,$offset=0,$pageSize=10,$isLimit=true){
		$result = array();
		$result['count'] = 0;
		$result['list'] = array();
	
		$criteria = $this->getDbCriteria();
		$criteria->alias = 'a';
		$criteria->join = ' LEFT JOIN web_archives_category b ON b.cat_id = a.cat_id ';
		$criteria->select = 'a.*,b.name,b.en_name';
	
		if (!empty($condition['archives_id'])){
			$criteria->compare('a.archives_id', $condition['archives_id']);
		}
		
		if (!empty($condition['uid'])){
			$criteria->compare('a.uid', $condition['uid']);
		}
	
		if (!empty($condition['title'])){
			$criteria->compare(' a.`title`',$condition['title']);
		}
	
		if (!empty($condition['cat_id'])){
			$criteria->addCondition(' a.`cat_id`='.intval($condition['cat_id']));
		}
		
		if (!empty($condition['sub_id'])){
			$criteria->addCondition(' a.`sub_id`='.intval($condition['sub_id']));
		}
		
		if (!empty($condition['recommond'])){
			$criteria->addCondition(' a.`recommond`='.intval($condition['recommond']));
		}
		
		if (!empty($condition['is_hide'])){
			$criteria->addCondition(' a.`is_hide`='.intval($condition['is_hide']));
		}
	
		if (!empty($condition['create_time_start'])){
			$criteria->addCondition(' a.`create_time`>='.strtotime($condition['create_time_start']));
		}
	
		if (!empty($condition['create_time_end'])){
			$criteria->addCondition(' a.`create_time`<='.strtotime($condition['create_time_end']));
		}
	
		$result['count'] = array_shift($this->getCommandBuilder()->createCountCommand($this->tableName(), $criteria)->queryRow());
		if($isLimit){
			$criteria->offset = $offset;
			$criteria->limit = $pageSize;
			$criteria->order= ' a.create_time DESC';
		}
		$result['list'] = $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
		return $result;
	
	}
}

?>