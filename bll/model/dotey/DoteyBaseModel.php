<?php

/**
 * 主播基本信息数据访问层
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: DoteyBaseModel.php 17132 2013-12-31 07:55:07Z hexin $ 
 * @package model
 * @subpackage dotey
 */
class DoteyBaseModel extends PipiActiveRecord {
	/**
	 * @param unknown_type $className
	 * @return DoteyBaseModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{dotey_base}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_user;
	}
	
	/**
	 * 取得主揪基本信息列表
	 * 
	 * @param mixed $uids
	 * @return UserBasicModel
	 */
	public function getDoteyBaseByUids(array $uids){
		if(empty($uids))
			return array();
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('uid',$uids);
		return $this->findAll($criteria);
	}
	
	/**
	 * 获取主播信息
	 * 
	 * @param array $condition
	 * @return array
	 */
	public function getDoteysByCondition(array $condition){
		/* @var $criteria CDbCriteria */
		$criteria = $this->getCommandBuilder()->createCriteria();
		$this->buildCriteria($criteria, $condition);
		return $this->findAll($criteria);
	}
	
	/**
	 * 
	 * @param array $condition
	 * @return Ambigous <string, unknown, mixed>
	 */
	public function getCountByCondition(array $condition){
		/* @var $criteria CDbCriteria */
		$criteria = $this->getCommandBuilder()->createCriteria();
		$this->buildCriteria($criteria, $condition);
		return $this->count($criteria);
	}
	
	public function getProxyOrTutorManagerTotal($type,Array $uids){
		$criteria = $this->getDbCriteria();
		
		
		if ($type == DOTEY_MANAGER_TUTOR) {
			$criteria->select = ' tutor_uid,count(tutor_uid) as total_dotey';
			$criteria->group = 'tutor_uid';
			$criteria->addInCondition('tutor_uid', $uids);
		}
		
		if ($type == DOTEY_MANAGER_PROXY) {
			$criteria->select = ' proxy_uid,count(proxy_uid) as total_dotey';
			$criteria->group = 'proxy_uid';
			$criteria->addInCondition('proxy_uid', $uids);
		}
		return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
	}
	
	/**
	 * 查询主播基本信息
	 * 
	 * @author supeng
	 * @param array $condition
	 * @param unknown_type $offset
	 * @param unknown_type $pageSize
	 * @param unknown_type $isLimit
	 * @return multitype:multitype: number NULL mixed 
	 */
	public function searchDoteyBase(Array $condition,$offset=0,$pageSize=10,$isLimit=true){
		$return = array();
		$return['count'] = 0;
		$return['list'] = array();
		
		$criteria = $this->getDbCriteria();
		$criteria->alias = 'a';
		$criteria->select = 'a.uid,a.sub_channel,a.sign_type,a.status,a.dotey_type,a.update_time,a.create_time as apply_time,a.proxy_uid,a.tutor_uid,
				b.username,b.nickname,b.realname,b.user_status,b.create_time,b.reg_source,b.user_type,
				c.gender,c.mobile,c.qq,c.bank,c.bank_account,c.id_card,c.province,c.city,c.bank_account,c.bank_account,c.birthday';
		$criteria->join = 'LEFT JOIN web_user_base b ON b.uid=a.uid LEFT JOIN web_user_extend c on c.uid=a.uid';
		
		if(!empty($condition['uid'])){
			$criteria->compare('a.uid',$condition['uid']);
		}
		
		if(!empty($condition['uids'])){
			$criteria->compare('a.uid',$condition['uids']);
		}
		
		if(!empty($condition['username'])){
			$keyword=strtr($condition['username'],array('%'=>'\%', '_'=>'\_', '\\'=>'\\\\')).'%';
			$criteria->addCondition("b.username LIKE '{$keyword}'");
		}
		
		if(!empty($condition['realname'])){
			$keyword=strtr($condition['realname'],array('%'=>'\%', '_'=>'\_', '\\'=>'\\\\')).'%';
			$criteria->addCondition("b.realname LIKE '{$keyword}'");
		}
		
		if(!empty($condition['nickname'])){
			$keyword=strtr($condition['nickname'],array('%'=>'\%', '_'=>'\_', '\\'=>'\\\\')).'%';
			$criteria->addCondition("b.nickname LIKE '{$keyword}'");
		}
		
		if(isset($condition['status'])){
			if($condition['status'] >= 0){
				$criteria->compare('a.status', $condition['status']);
			}
		}
		
		if(!empty($condition['_status']) || isset($condition['_status'])){
			if($condition['_status'] >= 0){
				$criteria->compare('a.status', $condition['_status']);
			}
		}
		
		if(isset($condition['user_status']) && is_numeric($condition['user_status'])){
			if($condition['user_status'] >= 0){
				$criteria->compare('b.user_status', $condition['user_status']);
			}
		}
		
		if(!empty($condition['create_time_start'])){
			$criteria->addCondition('b.create_time>='.strtotime($condition['create_time_start']));
		}
		
		if(!empty($condition['create_time_end'])){
			$criteria->addCondition('b.create_time<'.strtotime($condition['create_time_end']));
		}
		
		if(!empty($condition['live_time_on'])){
			$criteria->addCondition('a.update_time>='.strtotime($condition['live_time_on']));
		}
		
		if(!empty($condition['live_time_end'])){
			$criteria->addCondition('a.update_time<'.strtotime($condition['live_time_end']));
		}
		
		if(!empty($condition['dotey_type'])){
			$criteria->compare('a.dotey_type', $condition['dotey_type']);
		}
		
		if(!empty($condition['sign_type'])){
			$criteria->compare('a.sign_type', $condition['sign_type']);
		}
		
		if(isset($condition['sources']) && is_numeric($condition['sources'])){
			$criteria->compare('b.reg_source', $condition['sources']);
		}
		
		$return['count'] = $this->getCommandBuilder()->createCountCommand($this->tableName(), $criteria)->queryRow();
		if($isLimit){
			$criteria->offset = $offset;
			$criteria->limit = $pageSize;
		}
		$return['list'] = $this->getCommandBuilder()->createFindCommand($this->tableName(),$criteria)->queryAll();
		return $return;
	}
	
	protected function buildCriteria(CDbCriteria $criteria,array $condition){
		if(isset($condition['status'])){
			$criteria->condition .= ' status = :status';
			$criteria->params[':status'] =$condition['status'];
		}
		
		if(isset($condition['startRegisterTime'])){
			$criteria->condition .= ' AND create_time >= :startRegisterTime';
			$criteria->params[':startRegisterTime'] =$condition['startRegisterTime'];
		}
		
		if(isset($condition['limit'])){
			$criteria->limit = $condition['limit'];
		}
		
		return $criteria;
	}
	
	/**
	 * @author supeng
	 * @param unknown_type $provice
	 * @param unknown_type $city
	 * @return mixed
	 */
	public function searchDoteyArea($province,$city){
		$criteria = $this->getDbCriteria();
		$criteria->alias = 'a';
		$criteria->select = 'a.uid';
		$criteria->join = 'LEFT JOIN web_user_base b ON b.uid=a.uid LEFT JOIN web_user_extend c on c.uid=a.uid';
		$criteria->compare('c.province', $province);
		$criteria->compare('c.city', $city);
		return $this->getCommandBuilder()->createFindCommand($this->tableName(),$criteria)->queryAll();
	}
	
	/**
	 * @author supeng
	 * @param unknown_type $page
	 * @param unknown_type $pageSize
	 * @param array $search
	 * @param unknown_type $isLimit
	 * @return mixed
	 */
	public function getDoteyList($page = 1, $pageSize = 20, array $search = array(),$isLimit = true){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->alias = 'a';
		$criteria->select = 'a.*';
		$criteria->join = 'LEFT JOIN web_user_base u ON u.uid = a.uid LEFT JOIN web_user_extend e ON e.uid = a.uid';
		$criteria->order = 'a.create_time DESC';
	
		if(!empty($search['type'])){
			$type = $search['type'];
			$criteria->addColumnCondition(array('a.dotey_type' => $search['type']));
		}
	
		if(!empty($search['sources'])){
			$sources = explode('#XX#', $search['sources']);
			$_type = $sources[0];
			if ($_type == DOTEY_MANAGER_PROXY){
				$criteria->compare('a.proxy_uid', $sources[1]);
			}
			if ($_type == DOTEY_MANAGER_TUTOR){
				$criteria->compare('a.tutor_uid', $sources[1]);
			}
		}
	
		$return['count'] = $this->getCommandBuilder()->createCountCommand($this->tableName(), $criteria)->queryScalar();
		if($page && $isLimit){
			$criteria->offset = ($page - 1) * $page;
			$criteria->limit = $pageSize;
		}
		$return['list'] = $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
		return $return;
	}
	
	/**
	 * 返回一堆uid中真实是主播的信息
	 * @author hexin
	 * @param array $uids
	 * @return array
	 */
	public function getDoteysInUids(array $uids){
		if(empty($uids)) return array();
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->alias = 'a';
		$criteria->select = 'a.*';
		$criteria->join = 'LEFT JOIN web_user_base u ON u.uid = a.uid';
		$criteria->condition = 'a.uid in ('.implode(',', $uids).') and u.user_status = 0 and u.user_type & 2';
		return $this->getCommandBuilder()->createFindCommand($this->tableName(),$criteria)->queryAll();
	}
	
	/**
	 * 返回所有有效的主播uid
	 * @return array
	 */
	public function getAllDoteyUids(){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->alias = 'a';
		$criteria->select = 'a.uid';
		$criteria->join = 'LEFT JOIN web_user_base u ON u.uid = a.uid';
		$criteria->condition = 'u.user_status = 0 and u.user_type & 2';
		return $this->getCommandBuilder()->createFindCommand($this->tableName(),$criteria)->queryColumn();
	}
}

