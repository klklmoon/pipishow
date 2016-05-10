<?php

/**
 * 子频道数据访问层
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: supeng $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: ChannelSubModel.php 11308 2013-05-31 01:09:32Z supeng $ 
 * @package model
 * @subpackage dotey
 */
class ChannelSubModel extends PipiActiveRecord {
	/**
	 * @param unknown_type $className
	 * @return ChannelSubModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{channel_sub}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_common;
	}
	
	public function rules(){
		return array(
			array('sub_name','filter','filter'=>array(new CHtmlPurifier(),'purify')),
			array('sub_name,channel_id','required'),
			array('sub_name','unique'),
		);
	}
	
	public function attributeLabels(){
		return array(
			'sub_name'=>'子频道名称',
			'channel_id'=>'频道ID',
		);
	}
	
	public function getNextSubChannelPrimaryId(){
		$dbCommand = $this->getDbCommand();
		$dbCommand->text = ' SELECT max(sub_channel_id) primaryId  FROM '.$this->tableName().' LIMIT 1';
		$primaryId = $dbCommand->queryScalar();
		
		if($primaryId){
			return $primaryId * 2;
		}
		return 1;
	}
	
	/**
	 * 删除子频道信息
	 * 
	 * @param array $ids
	 * @return int
	 */
	public function delSubChannelByIds(array $ids){
		if(empty($ids))
			return 0;
			
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('sub_channel_id', $ids);
		return $this->deleteAll($criteria);
	}
	
	/**
	 * 删除子频道信息
	 * 
	 * @param array $channelIds
	 * @return int
	 */
	public function delSubChannelByChannelIds(array $channelIds){
		if(empty($channelIds))
			return 0;
			
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('channel_id', $channelIds);
		return $this->deleteAll($criteria);
	}
	
	public function getChannelsAllInfoByCondition(array $condition ){
		$criteria = $this->getDbCriteria();
		$criteria->select = ' channel_name,sub_name,a.channel_id,sub_channel_id,index_sort,index_ssort,is_show_index,is_show_sindex,dotey_sort,dotey_num,b.desc ';
		$this->buildCriteria($criteria,$condition);
		return $this->getCommandBuilder()->createFindCommand($this->tableName(),$criteria)->queryAll();
	}
	
	protected function buildCriteria(CDbCriteria  $criteria,array $condition){
		$criteria->alias = 'b';
		$criteria->order = ' index_sort DESC,index_ssort DESC ';
		$criteria->join = ' JOIN  web_channel a  ON a.channel_id = b.channel_id ';
		
		
		if(isset($condition['channel_id'])){
			$criteria->condition .= ' b.channel_id = :channel_id ';
			$criteria->params += array(':channel_id'=>$condition['channel_id']);
		}
		
		if(isset($condition['sub_channel_id'])){
			$criteria->condition .= ($criteria->condition  ? 'AND' : '') . ' b.sub_channel_id = :sub_channel_id ';
			$criteria->params += array(':sub_channel_id'=>$condition['sub_channel_id']);
		}
		
		if(isset($condition['channel_name'])){
			$criteria->condition .= ' a.channel_name = :channel_name ';
			$criteria->params += array(':channel_name'=>$condition['channel_name']);
		}
		
		if(isset($condition['sub_name'])){
			$criteria->condition .= ($criteria->condition  ? 'AND' : '') . ' b.sub_name = :sub_name ';
			$criteria->params += array(':sub_name'=>$condition['sub_name']);
		}

	}
	
	
}

