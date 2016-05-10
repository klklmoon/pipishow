<?php
/**
 * 用户印象标签关系
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z suqian $ 
 * @package
 */
class UserTagsModel extends PipiActiveRecord {
	/**
	 * @param unknown_type $className
	 * @return UserTagsModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{user_tags_relation}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_user;
	}
	
	/**
	 * 给用户添加标签
	 * @param array $uids
	 * @param array $tag_ids
	 * @param array $exists 需要过滤的，避免重复添加的用户标签
	 * @return boolean
	 */
	public function addTags(array $uids, array $tag_ids, array $exists = array()){
		if(empty($uids) || empty($tag_ids)) return false;
		$sql = 'INSERT INTO '.$this->tableName().'(uid,tag_id,user_type,tag_time) VALUES';
		foreach($uids as $uid){
			foreach($tag_ids as $tid){
				if(isset($exists[$tid]) && in_array($uid, $exists[$tid]))
					continue;
				$sql.="($uid, $tid, 0, ".time()."),";
			}
		}
		if(substr($sql, -1) !== ',') return false;
		$sql = rtrim($sql, ',');
		return $this->getCommandBuilder()->createSqlCommand($sql)->execute();
	}
	
	/**
	 * 获取某些用户的所有标签
	 * @param array $uids
	 * @return array
	 */
	public function getTagsByUids(array $uids){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria -> alias = 'a';
		$criteria -> join = 'LEFT JOIN {{tags}} as t ON a.tag_id = t.tag_id';
		$criteria -> select = 'a.uid, a.tag_id, a.tag_time, t.tag_name';
		$criteria -> addInCondition('a.uid', $uids);
		return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
	}
	
	/**
	 * 获取分页的已有标签的主播列表
	 * @param int $limit
	 * @param int $page
	 * @param array $condition
	 * @return array
	 */
	public function getTagsByDotey($limit = 0, $page = 1, array $condition = array()){
		$cr = $this->getCommandBuilder()->createCriteria();
		$cr -> alias = 't';
		$cr -> select = 't.uid, GROUP_CONCAT(tag_id) as tag_ids, u.nickname';
		$cr -> join = 'LEFT JOIN {{user_base}} AS u ON t.uid = u.uid';
		if(isset($condition['tag_id'])){
			$cr -> condition = 't.uid in (SELECT distinct uid FROM {{user_tags_relation}} WHERE tag_id = '.$condition['tag_id'].')';
		}
		if(isset($condition['uid'])){
			$cr -> addColumnCondition(array('t.uid' => $condition['uid']));
		}
		$cr -> group = 'uid';
		
		if($page != -1){
			$r['count'] = $this->count($cr);
			$pages=new CPagination($r['count']);
			$pages->pageSize = $limit;
			$pages->applyLimit($cr);
			$r['pages'] = $pages;
		}
		$r['list'] = $this->getCommandBuilder()->createFindCommand($this->tableName(), $cr)->queryAll();
		return $r;
	}
}

