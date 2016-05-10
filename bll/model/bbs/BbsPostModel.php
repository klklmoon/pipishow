<?php

class BbsPostModel extends PipiActiveRecord{
	/**
	 * 
	 * @param string $className
	 * @return BbsPostModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}

	public function tableName(){
		return '{{bbs_post}}';
	}

	public function primaryKey(){
		return 'post_id';
	}

	public function getDbConnection(){
		return Yii::app()->db_bbs;
	}
	
	/**
	 * 
	 * @param $threadId
	 * @param $offset
	 * @param $limit
	 */
	public function getPostList($threadId = null, $offset = 0, $limit = 10, $pageEnable = false, $order = 'post_id asc'){
		if(empty($threadId)){
			return array();
		}
		$db = new CDbCriteria();
		$db->select = '*';
		$db->condition = '`thread_id` = :threadId and is_del = 0';
		$db->params = array(':threadId'=>$threadId);
		$db->order = $order;
		if(!$pageEnable){
			$db->limit = $limit;
			$db->offset = $offset;
			return self::model()->findAll($db);
		}else{
			$result['count'] = $this->count($db);
			$pages=new CPagination($result['count']);
			$pages->pageSize = $limit;
			$pages->applyLimit($db);
			$result['list'] = self::model()->findAll($db);
			$result['pages'] = $pages;
			return $result;
		}
	}
	
	/**
	 * 返回指定id集合的贴子
	 * @param array $postIds
	 * @return array
	 */
	public function getPostIds(array $postIds){
		if(empty($postIds)) return array();
		return $this->findAllByPk($postIds, 'is_del = 0');
	}
	
	public function deleteThreadPosts($threadIds){
		$threadIds = is_array($threadIds) ? $threadIds : array(intval($threadIds));
		if(empty($threadIds)) return false;
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->addInCondition('thread_id', $threadIds);
		return $this->updateAll(array('is_del' => 1), $criteria);
	}
	
	public function deletePosts($postIds, $uid){
		$postIds = is_array($postIds) ? $postIds : array(intval($postIds));
		if(empty($postIds)) return false;
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->addInCondition('post_id', $postIds);
		return $this->updateAll(array('is_del' => 1, 'op_uid' => $uid, 'update_time' => time()), $criteria);
	}
	
	/**
	 * 获取贴子的楼层
	 * @param int $threadId
	 * @return number
	 */
	public function getFloor($threadId){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->addColumnCondition(array('thread_id' => $threadId));
		return $this->count($criteria) + 1;
	}
	
	/**
	 * 获取主题帖
	 * @param array $threadIds
	 * @return array
	 */
	public function getThreadPost(array $threadIds){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->addInCondition('thread_id', $threadIds);
		$criteria->group = 'thread_id';
		return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
	}
}
