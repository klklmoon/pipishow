<?php

class BbsForumSubModel extends PipiActiveRecord {
	/**
	 * 
	 * @param unknown_type $className
	 * @return BbsForumSubModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{bbs_forum_sub}}';
	}
	
	public function primaryKey(){
		return 'forum_sid';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_bbs;
	}
	

	public function getForumSub($forumId = NULL, $getHide = false){
		if(empty($forumId)) {
			return array();
		}
		$db = new CDbCriteria();
		$db->select = '*';
		$db->condition = '`forum_id` = :fromId';
		if(!$getHide){
			$db->condition .= ' and flag = :flag';
		}
		$db->params = array(':fromId'=>$forumId,':flag'=>0);
		$res = $this->model()->findAll($db);
		if($res){
			return $res;
		}
		return array();
	}
	
}
