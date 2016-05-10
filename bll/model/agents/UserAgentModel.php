<?php
/**
 * @author zhangzhifan <zhangzhifan@pipi.cn> 2010-11-2
 * @link http://show.pipi.cn
 * @copyright Copyright &copy; 2003-2010 show.pipi.cn
 * @license
 */
class UserAgentModel extends PipiActiveRecord{
	/**
	 * @param string $className
	 * @return UserAgentModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}

	public function tableName(){
		return '{{user_agent}}';
	}

	public function getDbConnection(){
		return Yii::app()->db_user;
	}
	
	public function getAgentByUids(array $uids){
		if(empty($uids)){
			return array();
		}
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('uid',$uids);
		return $this->findAll($criteria);
	}
	
	public function getAgentList($conditions = array(), $offset = 0, $limit = 10){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->select = '*';
		$criteria->condition = '';
		if(isset($conditions['agent_type'])){
			$criteria->condition .= 'agent_type = '.$conditions['agent_type'];
		}
		if(isset($conditions['agent_status'])){
			$criteria->condition .= 'agent_status = '.$conditions['agent_status'];
		}
		$return['count'] = $this->count($criteria);
		$criteria->offset = $offset;
		$criteria->limit = $limit;
		$criteria->order = 'create_time desc';
		$return['list'] = $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
		return $return;
	}
}