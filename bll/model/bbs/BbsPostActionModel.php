<?php

class BbsPostActionModel extends PipiActiveRecord{
	/**
	 * 
	 * @param unknown_type $className
	 * @return BbsPostActionModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}

	public function tableName(){
		return '{{bbs_post_action}}';
	}

	public function primaryKey(){
		return 'post_id';
	}

	public function getDbConnection(){
		return Yii::app()->db_bbs;
	}
	
	public function getPostAction($uid, $postId){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->addColumnCondition(array('post_id'=>$postId,'uid'=>$uid));
		$return = $this->find($criteria);
		if($return) return $return->getAttributes();
		else return array();
	}
	
	public function deletePostAction($actionId){
		return $this->deleteByPk($actionId);
	}
	
}
