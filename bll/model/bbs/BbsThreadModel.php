<?php 

class BbsThreadModel extends PipiActiveRecord{
	/**
	 * 
	 * @param unknown_type $className
	 * @return BbsThreadModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{bbs_thread}}';
	}
	
	public function primaryKey(){
		return 'thread_id';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::getDbConnection()
	 */
	public function getDbConnection(){
		return Yii::app()->db_bbs;
	}
	
	public function rules(){
		return array(
			array('title,content', 'filter','filter'=>array(new CHtmlPurifier(),'purify')),
		);
	}
	
	public function getThreadList($fromSid = null, $offset = 1, $limit = 10){
		if(empty($fromSid)) {
			return array();
		}
		$result = array();
		$result['count'] = 0;
		$result['list'] = array();
		
		$db = new CDbCriteria();
		$db->select = '*';
		if(is_array($fromSid)){
			$db->condition = '`forum_sid` in('.implode(',', $fromSid).') and is_del=0';
		}else{
			$db->condition = '`forum_sid` = :forumSid and is_del=0';
			$db->params = array(':forumSid'=>$fromSid);
		}
		$result['count'] = $this->count($db);
		
		$db->order = 'top desc, last_reply_time desc';
// 		$db->limit = $limit;
// 		$db->offset = $offset;
		$pages=new CPagination($result['count']);
		$pages->pageSize = $limit;
		$pages->applyLimit($db);
		
		$result['list'] = $this->findAll($db);
		$result['pages'] = $pages;
		
		return $result;
	}
	
	public function deleteThreads($threadIds){
		$threadIds = is_array($threadIds) ? $threadIds : array(intval($threadIds));
		if(empty($threadIds)) return false;
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->addInCondition('thread_id', $threadIds);
		return (bool) $this->updateAll(array('is_del' => 1), $criteria);
	}
	
	/**
	 * 贴子的回复数加1
	 * @param int $thread_id
	 * @return int
	 */
	public function addPost($thread_id){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->addColumnCondition(array('thread_id' => $thread_id));
		return $this->updateCounters(array('posts' => 1), $criteria);
	}
	
	/**
	 * 贴子的回复数减操作
	 * @param int $thread_id
	 * @return int
	 */
	public function subPost($thread_id, $count){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->addColumnCondition(array('thread_id' => $thread_id));
		return $this->updateCounters(array('posts' => -1 * $count), $criteria);
	}
	
	/**
	 * 置顶贴
	 * @param array $thread_ids
	 * @return boolean
	 */
	public function topThreads(array $threadIds){
		if(empty($threadIds)) return false;
		$sql = "UPDATE ".$this->tableName().' SET top = top ^ 1 WHERE thread_id in ('.implode(',', $threadIds).')';
		return $command = $this->getDbConnection()->createCommand()->setText($sql)->execute();
	}
}