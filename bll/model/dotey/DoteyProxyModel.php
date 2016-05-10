<?php
/**
 * 主播代理或导师
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z suqian $ 
 * @package
 */
class DoteyProxyModel extends PipiActiveRecord {
	/**
	 * @param unknown_type $className
	 * @return DoteyProxyModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{dotey_proxy}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_user;
	}
	
	public function rules(){
		return array(
			array('agency,company','filter','filter'=>array(new CHtmlPurifier(),'purify')),
			array('type','required'),
			array('agency,company', 'requiredByProxy')
		);
	}
	
	public function requiredByProxy(){
		if($this->type == DOTEY_MANAGER_PROXY && (empty($this->agency) || empty($this->company))){
			$this->addError('agency,company', '代理机构名和代理公司名称不能为空');
		}
		else return true;
	}

	/**
	 * 获取导师信息
	 * @param int $uid
	 * @return array
	 */
	public function getTutor($uid){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->alias = 'p';
		$criteria->select = 'p.uid, p.is_display, u.username, u.nickname, u.realname, e.qq, e.mobile';
		$criteria->join = 'LEFT JOIN web_user_base u ON p.uid = u.uid LEFT JOIN web_user_extend e ON p.uid = e.uid';
		$criteria->addColumnCondition(array('type' => DOTEY_MANAGER_TUTOR,'p.uid' => $uid));
		return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryRow();
	}
	
	/**
	 * 获取代理信息
	 * @param int $uid
	 * @return array
	 */
	public function getProxy($uid,$type = DOTEY_MANAGER_PROXY){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->alias = 'p';
		$criteria->select = 'p.uid, p.agency, p.business_license, p.note, p.query_allow,p.company,p.id_card_pic,p.is_display, u.username, u.nickname, u.realname, e.qq, e.mobile,e.bank,e.id_card,e.bank,e.bank_account';
		$criteria->join = 'LEFT JOIN web_user_base u ON p.uid = u.uid LEFT JOIN web_user_extend e ON p.uid = e.uid';
		$criteria->addColumnCondition(array('type' => $type,'p.uid' => $uid));
		return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryRow();
	}
	
	/**
	 * 根据条件获取所有导师或代理
	 * @param int $type
	 * @param boolean $hidden
	 * @return array
	 */
	public function getAll($type = 0, $hidden = false){
		$criteria = $this->getCommandBuilder()->createCriteria();
		if(!empty($type)){
			$criteria->addColumnCondition(array('type' => intval($type)));
		}
		if($hidden){
			$criteria->addColumnCondition(array('is_display' => 1, 'query_allow' => 1));
		}
		return $this->findAll($criteria);
	}
	
	/**
	 * 删除
	 * @param int $uid
	 * @return int
	 */
	public function deleteProxy($uid){
		return $this->deleteByPk($uid);
	}
	
	public function findByUnique($uid,$type){
		$criteria = $this->getDbCriteria();
		$criteria->compare('uid', $uid);
		$criteria->compare('type', $type);
		return $this->find($criteria);
	}
}

