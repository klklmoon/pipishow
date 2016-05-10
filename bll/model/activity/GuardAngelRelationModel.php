<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: supeng $>
 * @author su peng <suqian@pipi.cn>
 * @version $Id: GuardAngelRelationModel.php 10145 2013-05-14 04:49:25Z supeng $ 
 * @package model
 */
class GuardAngelRelationModel extends PipiActiveRecord{
	/**
	 * @param string $className
	 * @return GuardAngelRelationModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{long_guardangel_relation}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_activity;
	}
	
	/**
	 * 检查是否已经守护
	 * @param int $uid
	 * @param int $dotey_uid
	 * @return Ambigous <NULL, unknown, multitype:unknown Ambigous <unknown, NULL> , mixed, multitype:, multitype:unknown >
	 */
	public function checkGuard($uid,$dotey_uid){
		$criteria = $this->getDbCriteria();
		$criteria->compare('uid', $uid);
		$criteria->compare('dotey_uid', $dotey_uid);
		return $this->find($criteria);
	}
	
	/**
	 * 获取主播所有排行
	 */
	public function getAllDoteyGuardRank(){
		$criteria = $this->getDbCriteria();
		$criteria->group = 'dotey_uid';
		$criteria->select = 'sum(star) as star,dotey_uid,min(drank) as drank';
		$criteria->order = 'star DESC';
		return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
	}
	
	/**
	 * 获取用户所有排行榜
	 * @param int $limit
	 * @param int $rank
	 * @param boolean $flag
	 * @return mixed
	 */
	public function getAllUserGuardRank($limit=10,$flag=true,$rank=7){
		$criteria = $this->getDbCriteria();
		$criteria->select = 'star as star,uid,urank,dotey_uid';
		$criteria->order = 'star DESC,uid ASC';
		if($flag){
			$criteria->addCondition('urank >= '.$rank);
		}else{
			$criteria->addCondition('urank <= '.$rank);
		}
		$criteria->limit = $limit;
		return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
	}
	
	/**
	 * 获取用户对主播单个守护星的统计
	 * @param int $uid
	 * @param int $dotey_uid
	 * @return Ambigous <string, unknown, mixed>
	 */
	public function getUserToDoteyCountStar($uid,$dotey_uid){
		$criteria = $this->getDbCriteria();
		$criteria->select = 'star';
		$criteria->compare('uid', $uid);
		$criteria->compare('dotey_uid', $dotey_uid);
		return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryScalar();
	}

	/**
	 * 获取守护主播产生守护星最高的一位
	 * @param array $dotey_uids
	 * @return mixed
	 */
	public function getDoteyMaxStar($dotey_uids = array()){
		$criteria = $this->getDbCriteria();
		$criteria->select = 'DISTINCT(dotey_uid) as dotey_uid, star,uid,urank,drank';
		$criteria->distinct = true;
		$criteria->order = 'star DESC';
		if($dotey_uids){
			$criteria->compare('dotey_uid', $dotey_uids);
		}
		return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
	}
	
	/**
	 * 查看守护的列表信息
	 * @param int $uid
	 * @return mixed
	 */
	public function lookGuardList($uid,$isCount = false){
		$criteria = $this->getDbCriteria();
		$criteria->compare('uid', $uid);
		if($isCount){
			return $this->count($criteria);	
		}else{
			return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
		}
	}
	
	/**
	 * 更新守护星关系
	 * 
	 * @param int $uid
	 * @param int $dotey_uid
	 * @param int $star
	 * @return Ambigous <unknown, number>
	 */
	public function updateGuardAngelRelation($uid,$dotey_uid,$star){
		$criteria = $this->getDbCriteria();
		$criteria->compare('uid', $uid);
		$criteria->compare('dotey_uid', $dotey_uid);
		$data['star'] = $star;
		return $this->getCommandBuilder()->createUpdateCommand($this->tableName(), $data, $criteria)->execute();
	}
	
	/**
	 * 获取所有守护用户的所有列表
	 * 	去重查询
	 * @return mixed
	 */
	public function getAllGuardUserList(){
		$criteria = $this->getDbCriteria();
		$criteria->select = 'DISTINCT(uid) as uid';
		return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
	}
	
}