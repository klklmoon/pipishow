<?php
/**
 * 用户勋章管理
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: guoshaobo $>
 * @author guoshaobo <guoshaobo@pipi.cn>
 * @version $Id: UserCheckinModel.php 9657 2013-05-06 12:59:31Z guoshaobo $
 * @package model
 * @subpackage consume
 */
class UserMedalModel extends PipiActiveRecord{
	/**
	 * @param unknown_type $className
	 * @return UserMedalModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{user_medal}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_consume;
	}
	
 
	/**
	 * 用得用户拥有的所有有效的勋章
	 * 
	 * @param int|array $uids 用户ID
	 * @param string $type 勋章类型
	 * @param int $mid 勋章ID
	 * @return array
	 */
 	public function getUserMedalByUid($uid,$type = null,$mid = null){
		if(empty($uid)){
			return array();
		}
		$uid = is_array($uid) ? $uid : array($uid);
		$criteria = $this->getDbCriteria();
		if($type){
			$criteria->condition .= ' type = :type ';
			$criteria->params[':type'] = $type;
		}
		
 		if($mid){
			$criteria->condition .= ($criteria->condition  ? ' AND ' : ' ').' mid = :mid ';
			$criteria->params[':mid'] = $mid;
		}
		$criteria->addInCondition('uid',$uid);
		return  $this->findAll($criteria);
	}
	
	
	/**
	 * 用得用户拥有的所有有效的勋章
	 * 
	 * @param int|array $uids 用户ID
	 * @param int $vtime  有效期 默认为当前时间
	 * @return array
	 */
	public function getAllMedalsByUids($uids,$vtime = null){
		if(!$uids){
			return array();
		}
		$uids = is_array($uids) ? $uids : array($uids);
		$vtime = is_null($vtime) ? time() : (int)$vtime;
		$criteria = $this->getDbCriteria();
		$criteria->select = 'b.name,b.icon,b.type mtype,a.*';
		$criteria->alias = 'a';
		$criteria->condition = 'b.name IS NOT NULL AND (a.type = 0 OR (a.type=2 AND a.vtime > :vtime))';
		$criteria->join = ' LEFT JOIN web_medal_list b ON a.mid = b.mid ';
		$criteria->params = array(':vtime'=>$vtime);
		$criteria->addInCondition('uid',$uids);
		return $this->getCommandBuilder()->createFindCommand($this->tableName(),$criteria)->queryAll();
	}
	
	/**
	 * 删除用户勋章
	 * 
	 * @author supeng
	 * @param unknown_type $condition
	 * @return boolean
	 */
	public function delUserMedal($condition = array()){
		if (empty($condition)){
			return false;
		}
		
		$criteria = $this->getDbCriteria();
		
		if (isset($condition['mid'])){
			$criteria->compare('mid', $condition['mid']);	
		}
		
		if (empty($criteria->condition)) {
			return false;
		}
		return $this->deleteAll($criteria);
	}
	
	/**
	 * @author supeng
	 * @param array $condition
	 * @param unknown_type $offset
	 * @param unknown_type $pageSize
	 * @return multitype:multitype: number mixed 
	 */
	public function getUserMedalByCondition(Array $condition,$offset = 0,$pageSize = 10,$isLimit = true){
		$result = array();
		$result['count'] = 0;
		$result['list'] = array();
		
		$criteria = $this->getDbCriteria();
		
		if(isset($condition['group'])){
			$criteria->group = $condition['group'];
		}
		
		if (!empty($condition['uid'])){
			$criteria->compare('uid', $condition['uid']);
		}
		
		if (!empty($condition['uids'])){
			$criteria->compare('uid', $condition['uids']);
		}
		
		if (!empty($condition['mid'])){
			$criteria->compare('mid', $condition['mid']);
		}
		
		if (isset($condition['type']) && $condition['type'] >= 0){
			$criteria->compare('type', $condition['type']);
		}
		
		if (isset($condition['vtime']) && $condition['vtime'] > 0){
			$criteria->addCondition('vtime>='.$condition['vtime']);
		}
		
		$result['count'] = array_shift($this->getCommandBuilder()->createCountCommand($this->tableName(),$criteria)->queryRow());
		
		if($isLimit){
			$criteria->offset = $offset;
			$criteria->limit = $pageSize;
		}
		
		$result['list'] = $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
		return $result;
	}
}
?>