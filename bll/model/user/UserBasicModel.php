<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author Su qian <aoxue.1988.su.qian@163.com>
 * @version $Id: UserBasicModel.php 17797 2014-01-22 10:01:38Z hexin $ 
 * @package model
 * @subpackage user
 */
class UserBasicModel extends PipiActiveRecord {
	
	/**
	 * @param unknown_type $className
	 * @return UserBasicModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function primaryKey(){
		return 'uid';
	}
	
	public function tableName(){
		return '{{user_base}}';
	}
	
	public function rules(){
		return array(
			array('username,nickname,realname','filter','filter'=>array(new CHtmlPurifier(),'purify')),
			array('username,nickname,password,reg_salt','required'),
		//	array('reg_email','email','message'=>Yii::t('user','Sorry, your registered mailbox format is incorrect')),
// 			array('username','length','min'=>4,'max'=>20), //注册用户已有from做验证，数据存储上不需要再次做验证，否则历史的用户名过长的数据，只要调用这个接口全出错
			array('nickname','length','min'=>2,'max'=>20),
			array('password','length','min'=>4,'max'=>40),
			array('username,nickname','unique'),
		);
	}
	
	public function attributeLabels(){
		return array(
			'username'=>'用户名称',
			'nickname'=>'用户昵称',
			'password'=>'用户密码',
			'reg_email'=>'注册邮箱',
			'reg_salt'=>'注册干扰码码'
		);
	}
	
	public function getDbConnection(){
		return Yii::app()->db_user;
	}
	
    /**
	 * 取得有效的用户
	 * 
	 * @param string $condition 获取条件 username,email
	 * @param string $loginType 查询类型 0表示用户名　1表示邮箱
	 * @return array
	 */
	public function getVadidatorUser($condition,$loginType){
		$user =  array();
		if($loginType == USER_LOGIN_USERNAME){
			$user = $this->find('username = :username',array(':username'=>$condition));
		}else if($loginType == USER_LOGIN_EMAIL){
			$user = $this->find('reg_email = :email',array(':email'=>$condition));
		}else{
			return array();
		}
		if(!$user){
			return array();
		}
		return $user->attributes;
	}
	
	/**
	 * 取得用户基本信息列表
	 * 
	 * @param mixed $uids
	 * @return UserBasicModel
	 */
	public function getUserBasicByUids(array $uids){
		if(empty($uids))
			return array();
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('uid',$uids);
		return $this->findAll($criteria);
	}
	
	/**
	 * 取得用户基本信息列表
	 * 
	 * @param mixed $userNames
	 * @return UserBasicModel
	 */
	public function getUserBasicByUsernames(array $userNames){
		if(empty($userNames))
			return array();
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('username',$userNames);
		return $this->findAll($criteria);
	}
	
	/**
	 * 取得用户基本信息列表
	 *
	 * @param mixed $userNames
	 * @return UserBasicModel
	 */
	public function getUserBasicByNicknames(array $nickNames){
		if(empty($nickNames))
			return array();
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('nickname',$nickNames);
		return $this->findAll($criteria);
	}
	
	/**
	 * 删除用户的基本信息
	 * @param array $uids
	 * @return int
	 */
	public function delUserBasicByUids(array $uids){
		if(empty($uids))
			return 0;
			
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('uid', $uids);
		return $this->deleteAll($criteria);
	}
	
	
	/**
	 * @param int $offset
	 * @param int $pageSize
	 * @param array $condition
	 * @return CActiveDataProvider
	 */
	public function search($offset = 0, $pageSize = 20,$condition,$isLimit = true,$countLimt=null){
		$result = array();
		$criteria = $this->getDbCriteria();
		
		if (!empty($condition['username'])){
			$keyword=strtr($condition['username'],array('%'=>'\%', '_'=>'\_', '\\'=>'\\\\')).'%';
			$criteria->addCondition("username LIKE '{$keyword}'");
		}
		
		if (!empty($condition['nickname'])){
			$keyword=strtr($condition['nickname'],array('%'=>'\%', '_'=>'\_', '\\'=>'\\\\')).'%';
			$criteria->addCondition("nickname LIKE '{$keyword}'");
		}
		
		if (!empty($condition['realname'])){
			$keyword=strtr($condition['realname'],array('%'=>'\%', '_'=>'\_', '\\'=>'\\\\')).'%';
			$criteria->addCondition("realname LIKE '{$keyword}'");
		}
		
		if (!empty($condition['uid'])){
			$criteria->compare('uid', $condition['uid']);
		}
		
		if (!empty($condition['uids'])){
			$criteria->compare('uid', $condition['uids']);
		}
		
		if (!empty($condition['start_time'])){
			$criteria->addCondition('create_time>='.strtotime($condition['start_time']));
		}
		
		if (!empty($condition['end_time'])){
			$criteria->addCondition('create_time<='.strtotime($condition['end_time']));
		}
		
		if (!empty($condition['reg_ip'])){
			$criteria->compare('reg_ip',$condition['reg_ip'],true);
		}
		
		if (!empty($condition['user_type'])){
			$criteria->compare('user_type',$condition['user_type']);
		}
		
		if (!empty($condition['user_status'])){
			$criteria->compare('user_status',$condition['user_status']);
		}
		
		if (!empty($condition['bind_tel'])){
			$criteria->compare('reg_mobile',$condition['bind_tel'],true);
		}
		
		//count
		$result['count'] = $this->count($criteria);
		//ip重复的次数
		if (!empty($condition['reg_ip_count']) && !empty($condition['reg_ip'])){
			if($result['count'] < intval($condition['reg_ip_count'])){
				$result['list'] = array();
				$result['uids'] = array();
				$result['count'] = 0;
				return $result;
			}
		}
		
		if ($isLimit){
			//list
			$criteria->limit=$pageSize;
			$criteria->offset = $offset;
		}else{
			if((int)$countLimt >0){
				$criteria->limit=(int)$countLimt;
			}
		}
		
		$data = $this->findAll($criteria);
		
		$uids = array();
		$list = array();
		foreach ($data as $d){
			$uids[] = $d->attributes['uid'];
			$list[] = $d->attributes;
		}
		$result['list'] = $list;
		$result['uids'] = $uids;
		return $result;
		
	}
	
	public function countIpReister($ip,$time){
		$criteria = $this->getDbCriteria();
		$criteria->condition = 'reg_ip = :ip AND create_time >= :time';
		$criteria->params = array(':ip'=>$ip,':time'=>$time);
		$criteria->limit = 1;
		return $this->count($criteria);
	}
	
	/**
	 * 注册总数
	 */
	public function getLatelyRegisters(Array $condition,$offset = 0,$limit =10,$isLimit = true){
		$result = array();
		$result['count'] = 0;
		$result['regcount'] = 0;
		$result['list'] = array();
		
		$criteria = $this->getDbCriteria();
		$criteria->order = 'create_time DESC';
		if (!empty($condition['start_time'])){
			$criteria->addCondition('create_time >='.strtotime($condition['start_time']));
		}
		if (!empty($condition['end_time'])){
			$criteria->addCondition('create_time <'.strtotime($condition['end_time']));
		}
		$result['regcount'] = $this->count($criteria);#总注册数
		
		if (isset($condition['isList'])){
			if($condition['isList']){
				//列表查询
				if (!empty($condition['uid'])){
					$criteria->compare('uid', $condition['uid']);
				}
				if (!empty($condition['uids'])){
					$criteria->compare('uid', $condition['uids']);
				}
					
				if($isLimit){
					$criteria->offset = $offset;
					$criteria->limit = $limit;
				}
				$result['count'] = $this->count($criteria);#分页总数
				$result['list'] = $this->findAll($criteria);
			}
		}
		return $result;
	}
}

