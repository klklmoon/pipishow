<?php

/**
 * 用户中心之用户登录数据访问层
 * 
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: UserLoginRecordsModel.php 8369 2013-04-02 06:55:05Z suqian $ 
 * @package model
 * @subpackage user
 */
class UserLoginRecordsModel extends PipiActiveRecord {
	
	/**
	 * @param unknown_type $className
	 * @return UserLoginRecordsModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{user_login_records}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_user_records;
	}
	
	/**
	 * 登录天数 
	 * @author supeng
	 */
	public function getLatelyLogins(Array $condition,$offset = 0,$limit =10,$isLimit=true){
		if (!empty($condition['logins'])){
			$dbCommand = $this->getDbCommand();
			$logins = intval($condition['logins']);
			
			$result = array();
			$result['count'] = 0;
			$result['list'] = array();
			
			$where = '';
			if (!empty($condition['uid'])){
				if(is_array($condition['uid'])){
					$where .= ' AND uid IN('.implode(',', $condition['uid']).') ';
				}else{
					$where .= ' AND uid='.$condition['uid'];
				}
			}
			
			if (!empty($condition['uids'])){
				if(is_array($condition['uids'])){
					$where .= ' AND uid IN('.implode(',', $condition['uids']).')';
				}else{
					$where .= ' AND uid='.$condition['uids'];
				}
			}
			
			if (!empty($condition['start_time'])){
				$where .= ' AND login_time >='.strtotime($condition['start_time']);
			}
			if (!empty($condition['end_time'])){
				$where .= ' AND login_time <'.strtotime($condition['end_time']);
			}
			
			if($where){
				$where = ' WHERE '.trim($where,' AND');
			}
			
			$countsql = "SELECT count(logins) AS logins FROM
				( SELECT uid, count( FROM_UNIXTIME(login_time, '%Y-%m-%d') ) logins
				FROM web_user_login_records {$where} GROUP BY uid ) AS b
				WHERE  logins>{$logins} ";
			
			$isLimitPage = false;
			if (!empty($condition['group'])){
				$isLimitPage = true;
				if($condition['group'] == 'uid'){
					$sql = "SELECT b.uid,b.logins,b.max_login_time,a.login_ip,a.record_id FROM
						( SELECT uid, count( FROM_UNIXTIME(login_time, '%Y-%m-%d')) logins,max(login_time) as max_login_time,max(record_id) as record_id
						FROM web_user_login_records {$where} GROUP BY uid ) AS b
						LEFT JOIN web_user_login_records a ON a.record_id=b.record_id 
						WHERE logins>{$logins} GROUP BY uid";
				}
			}else{
				$sql = $countsql;
			}
			
			if (!empty($condition['uid']) || !empty($condition['uids'])){
				$isLimitPage = false;
			}
			
			if($isLimitPage && $isLimit){
				$sql .= " LIMIT {$offset},{$limit}";
			}
			
			if(isset($sql)){
				$dbCommand->setText($sql);
				$result['list'] =$dbCommand->queryAll();
			}
			
			$dbCommand->setText($countsql);
			$result['count'] = array_shift($dbCommand->queryRow());
			return $result;
		}
		return false;
	}
	
	/**
	 * 获取登录明细
	 * 
	 * @author supeng
	 * @param array $condition
	 * @param unknown_type $offset
	 * @param unknown_type $limit
	 * @return multitype:multitype: number mixed Ambigous <NULL, unknown, multitype:unknown Ambigous <unknown, NULL> , mixed, multitype:, multitype:unknown > 
	 */
	public function getLoginDetails(Array $condition = array(),$offset = 0,$limit=10){
		$result = array();
		$result['count'] = 0;
		$result['list'] = array();
		
		$criteria = $this->getDbCriteria();
		$criteria->order = 'login_time DESC';
		
		if (!empty($condition['uid'])){
			$criteria->compare('uid', $condition['uid']);
		}
		
		if (!empty($condition['uids'])){
			$criteria->compare('uid', $condition['uids']);
		}
		
		if (!empty($condition['start_time'])){
			$criteria->addCondition('login_time>='.strtotime($condition['start_time']));
		}
		
		if (!empty($condition['end_time'])){
			$criteria->addCondition('login_time<'.strtotime($condition['end_time']));
		}
		
		if(!empty($condition['login_ip'])){
			$criteria->compare('login_ip', $condition['login_ip']);
		}
		
		$result['count'] = $this->count($criteria);
		$criteria->offset = $offset;
		$criteria->limit = $limit;
		$result['list'] = $this->findAll($criteria);
		return $result;
	}
	
	public function getDuplicateLogins(Array $condition = array(),$offset = 0,$limit=10){
		$result = array();
		$result['count'] = 0;
		$result['list'] = array();
		
		$dbCommand = $this->getDbCommand();
		
		$where = '';
		if (!empty($condition['uid'])){
			$where .= ' uid='.$condition['uid']. ' AND ';
		}
		
		if (!empty($condition['uids'])){
			$where .= ' uid IN('.implode(',', $condition['uids']). ') AND ';
		}
		
		if (!empty($condition['start_time'])){
			$where .= ' login_time>='.strtotime($condition['start_time']).' AND ';
		}
		
		if (!empty($condition['end_time'])){
			$where .= ' login_time<'.strtotime($condition['end_time']).' AND ';
		}
		
		if (!empty($condition['login_ip'])){
			$where .= ' login_ip=\''. $condition['login_ip']. '\' AND ';
		}
		
		if($where){
			$where = ' WHERE '.trim(trim($where,' '),'AND');
		}
		
		$limit = " LIMIT {$offset},{$limit}";
		
		$listSql = "
			SELECT
				a.uid, a.record_id, a.loginCount,
				b.login_ip, b.login_time
			FROM ( SELECT count(1) loginCount, uid, max(record_id) AS record_id
				FROM web_user_login_records
				%s
				GROUP BY uid
				%s ) a,
				web_user_login_records b
			WHERE
				b.record_id = a.record_id; ";
		
		$countSql = "
			SELECT count(DISTINCT(uid)) as count 
			FROM web_user_login_records %s	";
		
		$countSql = sprintf($countSql,$where);
		$listSql = sprintf($listSql,$where,$limit);
		
		$result['count'] = $dbCommand->setText($countSql)->queryScalar();
		$result['list'] = $dbCommand->setText($listSql)->queryAll();
		return $result;
	}
}

?>