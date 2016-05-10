<?php
/**
 * 家族成员退出记录表
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: 2013-8-6 下午3:28:20 hexin $ 
 * @package
 */
class FamilyQuitRecordsModel extends PipiActiveRecord {
	/**
	 * 
	 * @param string $className
	 * @return FamilyQuitRecordsModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{family_quit_records}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_family;
	}

	/**
	 * 获取家族内指定用户的加入记录退出
	 * 
	 * @param array $uids
	 * @return array
	 */
	public function getUserQuitRecordsByUids($family_id,array $uids){
		if(empty($uids) || $family_id <= 0){
			 return array();
		}
		
		$dbCreteria = $this->getDbCriteria();
		$dbCreteria->compare('family_id',$family_id);
		$dbCreteria->addInCondition('uid',$uids);
		return $this->findAll($dbCreteria);
	}
	
	/**
	 * 是否有强退记录,返回强退的剩余天数
	 * @param int $uid 主播uid
	 * @param int $time 当前时间减去强退的期限，即从当前时间往前的强退开始时间寻找强退记录
	 * @param int $family_id 申请加入时需要判断再次加入的是否是已强退的家族，如果是则忽略强退的加入限制
	 * @return int
	 */
	public function isFocue($uid, $time, $family_id = 0){
		if(empty($uid) || $time < 0) return 0;
		//已经是其他家族家族主播的用户不需要检查强退规则
		$familys = FamilyMemberModel::model()->getDoteyMembers(array($uid));
		if(!empty($familys)) return 0;
		
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->alias = 'q';
		$criteria->join = 'LEFT JOIN web_family AS f ON q.family_id = f.id';
		$criteria->select = 'q.family_id, q.type, q.quit_time';
		$criteria->condition = 'q.uid = '.$uid.' and q.is_dotey = 1 and f.sign = 1 and q.quit_time >'.$time;
		$criteria->order = 'q.id desc';
		$criteria->limit = '1';
		$row = $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryRow();
		if(empty($row)) return 0;
		if($row['type'] == 1) return 0;
		if($row['family_id'] == $family_id) return 0;
		return ceil(($row['quit_time'] - $time)/86400);
	}
}
