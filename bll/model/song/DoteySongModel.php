<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: lei wei $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z lei wei  $ 
 * @package 
 */
class DoteySongModel extends PipiActiveRecord {
	/**
	 * @param unknown_type $className
	 * @return DoteySongModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{dotey_song}}';
	}
	
	
	public function getDbConnection(){
		return Yii::app()->db_consume;
	}
	
	public function rules(){
		return array(
			array('dotey_id','numerical'),
		);
	}
	
	public function attributeLabels(){
		return array(
			'dotey_id'=>'主播uid',
		);
	}
	
	/**
	 * 根据主播Id获取主播歌单
	 * @param array $doteyIds 主播Id
	 * @return array
	 */
	public function getDoteySongByDoteyIds(array $doteyIds){
		$criteria =$this->getDbCriteria();
		$criteria->addInCondition('dotey_id',$doteyIds);
		return $this->findAll($criteria);
	}
	
	/**
	 * 根据歌曲记录Id获取歌曲信息
	 * @param int $songId 歌曲记录Id
	 * @return array
	 */
	public function getDoteySongBySongId($songId){
		if(empty($songId)) return array();
		$criteria =$this->getDbCriteria();
		$criteria->addColumnCondition(array('song_id'=>$songId));
		return $this->find($criteria);
	}
	
	/**
	 * 根据条件获取歌曲信息
	 * @param array $condition 查询条件
	 * @return array
	 */
	public function getDoteySongByCondition(array $condition){
		if(empty($condition)) return array();
		$criteria =$this->getDbCriteria();
		$criteria->addColumnCondition($condition);
		return $this->findAll($criteria);
	}
	
	/**
	 * 根据歌曲记录Id删除歌曲
	 * @param int $songId
	 * @return boolean
	 */
	public function delDoteySongBySongId($songId){
		if(empty($songId)) return array();
		return $this->deleteByPk($songId);
	}
	
	/**
	 * 主播歌单
	 * 
	 * @author supeng
	 * @param unknown_type $offset
	 * @param unknown_type $limit
	 * @param unknown_type $condition
	 * @param unknown_type $isLimit
	 * @return multitype:multitype: number Ambigous <NULL, unknown, multitype:unknown Ambigous <unknown, NULL> , mixed, multitype:, multitype:unknown > Ambigous <string, unknown, mixed> 
	 */
	public function searchDoteySongByCondition($offset=0,$limit=20,$condition = array(),$isLimit= true){
		$result = array();
		$result['count'] = 0;
		$result['list'] = array();
		
		$criteria = $this->getDbCriteria();
		
		if (!empty($condition['uid'])){
			$criteria->compare('dotey_id', $condition['uid']);
		}
		
		$result['count'] = $this->count($criteria);
		if ($isLimit){
			$criteria->offset = $offset;
			$criteria->limit = $limit;
		}
		$result['list'] = $this->findAll($criteria);
		return $result;
	}
	
	
}

?>