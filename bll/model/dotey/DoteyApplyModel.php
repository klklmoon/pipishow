<?php
/**
 * 主播申请
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z hexin $ 
 * @package
 */
class DoteyApplyModel extends PipiActiveRecord {
	/**
	 * @param unknown_type $className
	 * @return DoteyApplyModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{dotey_apply}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_user;
	}
	
	public function rules(){
		return array(
			array('birth_province,birth_city,internet_condition','filter','filter'=>array(new CHtmlPurifier(),'purify')),
// 			array('personal_image','required'),
		);
	}
	
	/**
	 * 获取申请信息
	 * @param array $uids
	 * @return array
	 */
	public function getApplyInfos(array $uids,$type=4){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->alias = 'a';
		$criteria->select = 'a.*, u.nickname, u.realname, e.gender, e.mobile, e.qq, e.id_card, e.province, e.city, e.profession, e.skill,e.bank,e.bank_account';
		$criteria->join = 'LEFT JOIN web_user_base u ON u.uid = a.uid LEFT JOIN web_user_extend e ON e.uid = a.uid';
		$criteria->addInCondition('a.uid', $uids);
		$criteria->compare('a.type', $type);
		return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
	}
	
	public function getApplyDoteyInfos(array $uids){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->alias = 'a';
		$criteria->select = 'a.*, d.status, d.proxy_uid, d.tutor_uid, d.finder_uid, u.nickname, u.realname, e.gender, e.mobile, e.qq, e.id_card, e.province, e.city, e.profession, e.skill, e.bank_user, e.bank, e.bank_account';
		$criteria->join = 'LEFT JOIN web_dotey_base d ON d.uid = a.uid LEFT JOIN web_user_base u ON u.uid = a.uid LEFT JOIN web_user_extend e ON e.uid = a.uid';
		$criteria->addInCondition('a.uid', $uids);
		$criteria->compare('a.type', 4);
		return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
	}
	
	public function getApplyList($page = 1, $pageSize = 20, array $search = array(),$isLimit = true){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->alias = 'a';
		$criteria->select = 'a.*, u.username, u.nickname, u.realname, e.gender, e.mobile, e.qq, e.id_card, e.province, e.city, e.profession, e.skill,e.bank_user,e.bank,e.bank_account';
		$criteria->join = 'LEFT JOIN web_user_base u ON u.uid = a.uid LEFT JOIN web_user_extend e ON e.uid = a.uid';
		$criteria->order = 'a.create_time DESC';
		
		if(!empty($search['type'])){
			$criteria->compare('a.type', $search['type']);
		}
		
		if(!empty($search['status'])){
			$criteria->compare('a.status', $search['status']);
		}
		
		//用户名
		if(!empty($search['username'])){
			$criteria->compare('u.username', $search['username'],true);
		}

		//姓名
		if(!empty($search['realname'])){
			$criteria->compare('u.realname', $search['realname'],true);
		}
		
		//主播经验
		if(isset($search['has_experience']) && is_numeric($search['has_experience'])){
			$criteria->addColumnCondition(array('a.has_experience' => $search['has_experience']));
		}
		
		//住在杭州
		if(!empty($search['city'])){
			$criteria->addColumnCondition(array('e.city' => $search['city']));
		}
		
		//性别
		if(!empty($search['gender'])){
			$criteria->addColumnCondition(array('e.gender' => $search['gender']));
		}
		
		//申请时间
		if(!empty($search['create_time_start'])){
			$criteria->addCondition('a.create_time>='.strtotime($search['create_time_start']));
		}
		
		if(!empty($search['create_time_end'])){
			$criteria->addCondition('a.create_time<'.strtotime($search['create_time_end']));
		}
		
		if(isset($search['user_status']) && is_numeric($search['user_status'])){
			$criteria->addCondition('u.user_status='.$search['user_status']);
		}
		
		$return['count'] = $this->getCommandBuilder()->createCountCommand($this->tableName(), $criteria)->queryScalar();
		if($page && $isLimit){
			$criteria->offset = ($page - 1) * $page;
			$criteria->limit = $pageSize;
		}
		$return['list'] = $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
		return $return;
	}
	
	public function getDoteyApplyList($page = 1, $pageSize = 20, array $search = array(),$isLimit = true){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->alias = 'a';
		$criteria->select = 'a.*, d.status, d.proxy_uid, d.tutor_uid, u.username, u.nickname, u.realname, e.gender, e.mobile, e.qq, e.id_card, e.province, e.city, e.profession, e.skill';
		$criteria->join = 'LEFT JOIN web_dotey_base d ON d.uid = a.uid LEFT JOIN web_user_base u ON u.uid = a.uid LEFT JOIN web_user_extend e ON e.uid = a.uid';
		$criteria->order = 'a.create_time DESC';
	
		$criteria->compare('a.type', 4);
		if(!empty($search['type'])){
			$type = $search['type'];
			$criteria->addColumnCondition(array('d.dotey_type' => $search['type']));
			if(isset($search['pt_uid'])){
				if($type == DOTEY_MANAGER_TUTOR){
					$criteria->addColumnCondition(array('d.tutor_uid' => $search['pt_uid']));
				}elseif($type == DOTEY_MANAGER_PROXY){
					$criteria->addColumnCondition(array('d.proxy_uid' => $search['pt_uid']));
				}
			}
		}
	
		if(!empty($search['sources'])){
			$sources = explode('#XX#', $search['sources']);
			$_type = $sources[0];
			if ($_type == DOTEY_MANAGER_PROXY){
				$criteria->compare('d.proxy_uid', $sources[1]);
			}
			if ($_type == DOTEY_MANAGER_TUTOR){
				$criteria->compare('d.tutor_uid', $sources[1]);
			}
		}
	
		//用户名
		if(!empty($search['username'])){
			$criteria->compare('u.username', $search['username'],true);
		}
	
		//姓名
		if(!empty($search['realname'])){
			$criteria->compare('u.realname', $search['realname'],true);
		}
	
		//主播经验
		if(isset($search['has_experience']) && is_numeric($search['has_experience'])){
			$criteria->addColumnCondition(array('a.has_experience' => $search['has_experience']));
		}
	
		//住在杭州
		if(!empty($search['city'])){
			$criteria->addColumnCondition(array('e.city' => $search['city']));
		}
	
		//状态
		if(isset($search['status']) && is_numeric($search['status'])){
			$criteria->addColumnCondition(array('d.status' => $search['status']));
		}
	
		//性别
		if(!empty($search['gender'])){
			$criteria->addColumnCondition(array('e.gender' => $search['gender']));
		}
	
		//签约状态
		if(!empty($search['contract'])){
			if($search['contract'] == 1){
				$criteria->addCondition('d.status = 1');
			}else{
				$criteria->addCondition('d.status <> 1');
			}
		}
	
		//申请时间
		if(!empty($search['create_time_start'])){
			$criteria->addCondition('a.create_time>='.strtotime($search['create_time_start']));
		}
	
		if(!empty($search['create_time_end'])){
			$criteria->addCondition('a.create_time<'.strtotime($search['create_time_end']));
		}
	
		if(isset($search['user_status']) && is_numeric($search['user_status'])){
			$criteria->addCondition('u.user_status='.$search['user_status']);
		}
	
		$return['count'] = $this->getCommandBuilder()->createCountCommand($this->tableName(), $criteria)->queryScalar();
		if($page && $isLimit){
			$criteria->offset = ($page - 1) * $page;
			$criteria->limit = $pageSize;
		}
		$return['list'] = $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
		return $return;
	}
	
	public function deleteApply($uid){
		return $this->deleteByPk($uid);
	}
	
	public function findByUnique($uid,$type){
		$criteria = $this->getDbCriteria();
		$criteria->compare('uid', $uid);
		$criteria->compare('type', $type);
		return $this->find($criteria);
	}
}

