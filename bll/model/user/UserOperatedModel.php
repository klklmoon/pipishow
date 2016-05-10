<?php

class UserOperatedModel extends PipiActiveRecord {
	
	/**
	 * @param string $className
	 * @return UserOperatedModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function primaryKey(){
		return 'rid';
	}
	
	public function tableName(){
		return '{{user_operated}}';
	}
	
	public function rules(){
		return array(
			array('uid,op_uid,op_time,op_type','required'),
		);
	}
	
	public function attributeLabels(){
		return array(
			'uid'=>'ID',
			'op_uid'=>'操作ID',
			'op_time'=>'操作时间',
			'op_type'=>'操作类型',
		);
	}
	
	public function getDbConnection(){
		return Yii::app()->db_user;
	}
	
	/**
	 * @param int $offset
	 * @param int $pageSize
	 * @param array $condition
	 * @return CActiveDataProvider
	 */
	public function search(Array $condition,$offset = 0, $limit = 10,$isLimit = true){
		$return = array();
		$return['count'] = 0;
		$return['list'] = array();
		
		$criteria = $this->getDbCriteria();
		$criteria->alias = 'a';
		$criteria->select = 'a.rid,a.uid,a.op_uid,a.op_time,a.op_desc,b.username,b.nickname,b.realname,b.user_status,b.create_time,b.user_type';
		$criteria->join = 'LEFT JOIN web_user_base b ON b.uid=a.uid ';
		$criteria->order = 'a.rid DESC';
		
		if(!empty($condition['uid'])){
			$criteria->compare('a.uid',$condition['uid']);
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
		
		if(isset($condition['user_status'])){
			if($condition['user_status'] >= 0){
				$criteria->compare('b.user_status', $condition['user_status']);
			}
		}
		
		//注册时间
		if(!empty($condition['create_time_start'])){
			$criteria->addCondition('b.create_time>='.strtotime($condition['create_time_start']));
		}
		if(!empty($condition['create_time_end'])){
			$criteria->addCondition('b.create_time<'.strtotime($condition['create_time_end']));
		}
		//操作时间
		if(!empty($condition['op_time_start'])){
			$criteria->addCondition('a.op_time>='.strtotime($condition['op_time_start']));
		}
		if(!empty($condition['op_time_end'])){
			$criteria->addCondition('a.op_time<'.strtotime($condition['op_time_end']));
		}
		
		if(!empty($condition['op_type'])){
			$criteria->addCondition('a.op_type="'.$condition['op_type'].'"');
		}
		
		if(isset($condition['op_value'])){
			$criteria->addCondition('a.op_value="'.$condition['op_value'].'"');
		}
		
		if($isLimit){
			$return['count'] = array_shift($this->getCommandBuilder()->createCountCommand($this->tableName(), $criteria)->queryRow());
			$criteria->offset = $offset;
			$criteria->limit = $limit;
			$return['list'] = $this->getCommandBuilder()->createFindCommand($this->tableName(),$criteria)->queryAll();
		}else{
			$return['list'] =  $this->getCommandBuilder()->createFindCommand($this->tableName(),$criteria)->queryAll();
		}
		return $return;
	}
	
	public function getUserOperatedByUids($uids,$op_type,$op_value){
		$dbCommand = $this->getDbCommand();
		$dbCommand->setText(
			"SELECT * FROM {{user_operated}} a 
				WHERE a.rid IN(SELECT MAX(rid) FROM {{user_operated}}  
					where uid in(".implode(',', $uids).") AND op_type = '".$op_type."' AND op_value='".$op_value."' GROUP BY uid)"
			);
		return $dbCommand->queryAll();
	}
}

