<?php

/**
 * 主播与频道关系数据访问层
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: supeng $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: DoteyChannelModel.php 13369 2013-07-29 00:44:12Z supeng $ 
 * @package model
 * @subpackage dotey
 */
class DoteyChannelModel extends PipiActiveRecord {
	/**
	 * @param unknown_type $className
	 * @return DoteyChannelModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{dotey_channel}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_common;
	}
	
	public function getDoteysOfSong($channelId,$subChannelId = null){
		if($channelId <= 0){
			return array();
		}
		$criteria = $this->getDbCriteria();
		$criteria->select = 'uid,channel_id,sub_channel_id';
		$criteria->condition = 'channel_id = :channel_id';
		$criteria->params[':channel_id'] = $channelId;
		if($subChannelId){
			$criteria->condition .= ' AND sub_channel_id = :sub_channel_id';
			$criteria->params[':sub_channel_id'] = $subChannelId;
		}
		return $this->findAll($criteria);
	}
	/**
	 * 取得主播所属频道
	 * 
	 * @author guoshaobo  添加参数的时候, 没有做 and 联合查询
	 * @param array $uids 主播ID
	 * @param string $channel 频道
	 * @param string $subChannel 子频道
	 * @return array
	 */
	public function getChannelDoteyByUids(array $uids ,$channel = null ,$subChannel = null){
		if(empty($uids)){
			return array();
		}
		
		$criteria = $this->getDbCriteria();

		$criteria->addInCondition('uid',$uids);
		if($channel && $channel > 0){
			$criteria->condition .= ' and channel_id = :channel_id ';
			$criteria->params[':channel_id'] = $channel;
		}
		if($subChannel && $subChannel > 0){
			$criteria->condition .= ' and sub_channel_id = :sub_channel_id ';
			$criteria->params[':sub_channel_id'] = $subChannel;
		}
	
		return $this->findAll($criteria);
	}
	/**
	 * @author supeng
	 * @param array $conditions
	 * @param unknown_type $offset
	 * @param unknown_type $pageSize
	 * @return multitype:mixed 
	 */
	public function getDoteySongByConditions(Array $conditions,$offset = 0,$pageSize=10,$isLimit = true){
		$result = array();
		$criteria = $this->getDbCriteria();
		$criteria->select = 'a.uid,a.channel_id,a.sub_channel_id,a.target_relation_id,b.sub_name,c.channel_name';
		$criteria->alias = 'a';
		$criteria->join = 'LEFT JOIN web_channel_sub b ON b.sub_channel_id=a.sub_channel_id LEFT JOIN web_channel c ON c.channel_id = b.channel_id';
		
		if (!empty($conditions['channel_id'])){
			$criteria->condition .= ($criteria->condition ? ' AND ' : '').'a.channel_id = :channel_id';
			$criteria->params[':channel_id'] = $conditions['channel_id'];
		}
		
		if (!empty($conditions['sub_channel_id'])){
			$criteria->condition .= ($criteria->condition ? ' AND ' : '').'a.sub_channel_id = :sub_channel_id';
			$criteria->params[':sub_channel_id'] = $conditions['sub_channel_id'];
		}
		
		if (!empty($conditions['uid'])){
			$criteria->compare('a.uid', $conditions['uid']);
		}
		
		if (!empty($conditions['channel_name'])){
			$criteria->condition .= ($criteria->condition ? ' AND ' : '').'c.channel_name = :channel_name';
			$criteria->params[':channel_name'] = $conditions['channel_name'];
		}
		
		if (!empty($conditions['sub_name'])){
			$criteria->condition .= ($criteria->condition ? ' AND ' : '').'b.sub_name = :sub_name';
			$criteria->params[':sub_name'] = $conditions['sub_name'];
		}
		
		$result['count'] = array_shift($this->getCommandBuilder()->createCountCommand($this->tableName(), $criteria,'count')->queryRow());
		if($isLimit){
			$criteria->offset = $offset;
			$criteria->limit = $pageSize;
		}
		$result['list'] = $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();;
		return $result;
	}
	
	public function delDoteyChannelRel($uid,$channel_id,$sub_channel_id,$target_relation_id=0){
		$criteria = $this->getDbCriteria();
		$criteria->compare('uid', $uid);
		$criteria->compare('channel_id', $channel_id);
		$criteria->compare('sub_channel_id', $sub_channel_id);
		$criteria->compare('target_relation_id', $target_relation_id);
		return $this->deleteAll($criteria);
	}
}

