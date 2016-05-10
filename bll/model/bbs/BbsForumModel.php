<?php

class BbsForumModel extends PipiActiveRecord{
	/**
	 * @param unknown_type $className
	 * @return BbsForumModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function primaryKey(){
		return 'forum_id';
	}
	
	public function tableName(){
		return '{{bbs_forum}}';
	}

	public function getDbConnection(){
		return Yii::app()->db_bbs;
	}
	
	public function rules(){
		return array();
	}
	
	public function getForumInfo($from,$fromId){
		$db = new CDbCriteria();
		$db->select = '*';
		$db->condition = '`from`  = :from and `from_id` = :fromId';
		$db->params = array(':from'=>$from,':fromId'=>$fromId);
		return self::model()->find($db);
	}
	
	public function getForumByConditions(Array $conditions = array()){
		$criteria = $this->getDbCriteria();
		$criteria->alias = 'a';
		$criteria->select = 'a.forum_id,a.ower_uid,a.name as forum_name,a.from_id,a.status,b.forum_sid,b.name as forum_sname,b.create_time,b.flag';
		$criteria->join = 'LEFT JOIN web_bbs_forum_sub b ON b.forum_id=a.forum_id ';

		if(isset($conditions['status'])){
			$criteria->condition .= ($criteria->condition  ? ' AND ' : '').' a.status = :status';
			$criteria->params += array(':status'=>$conditions['status']);
		}
		
		if(isset($conditions['flag'])){
			$criteria->condition .= ($criteria->condition  ? ' AND ' : '').' b.status = :flag';
			$criteria->params += array(':flag'=>$conditions['flag']);
		}
		
		if(isset($conditions['forum_id'])){
			$criteria->condition .= ($criteria->condition  ? ' AND ' : '').' a.forum_id = :forum_id';
			$criteria->params += array(':forum_id'=>$conditions['forum_id']);
		}
		
		if(isset($conditions['forum_sid'])){
			$criteria->condition .= ($criteria->condition  ? ' AND ' : '').' a.forum_sid = :forum_sid';
			$criteria->params += array(':forum_sid'=>$conditions['forum_sid']);
		}
		
		if(isset($conditions['forum_name'])){
			$criteria->condition .= ($criteria->condition  ? ' AND ' : '').' a.name = :forum_name';
			$criteria->params += array(':forum_name'=>$conditions['forum_name']);
		}
		
		if(isset($conditions['forum_sname'])){
			$criteria->condition .= ($criteria->condition  ? ' AND ' : '').' b.name = :forum_sname';
			$criteria->params += array(':forum_sname'=>$conditions['forum_sname']);
		}
		
		if(isset($conditions['ower_uid'])){
			$criteria->condition .= ($criteria->condition  ? ' AND ' : '').' a.ower_uid = :ower_uid';
			$criteria->params += array(':ower_uid'=>$conditions['ower_uid']);
		}
		
		if(isset($conditions['form'])){
			$criteria->condition .= ($criteria->condition  ? ' AND ' : '').' a.from = :from';
			$criteria->params += array(':from'=>$conditions['from']);
		}
		
		if(isset($conditions['from_id'])){
			$criteria->condition .= ($criteria->condition  ? ' AND ' : '').' a.from_id = :from_id';
			$criteria->params += array(':from_id'=>$conditions['from_id']);
		}
		
		return $this->getCommandBuilder()->createFindCommand($this->tableName(),$criteria)->queryAll();
	}
	
	
}