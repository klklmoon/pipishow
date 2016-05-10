<?php
/**
 * 用户账户充值记录服务层
 * the last known user to change this file in the repository  <$LastChangedBy: lei wei $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z lei wei  $ 
 * @package service
 */
define('CURRENCY_RMB','RMB');
define('CURRENCY_USD','USD');
define('RECHAEGE_COUNT_LIMIT_DAY',100);     //每日充值次数限制
define('RECHAEGE_PIPIEGG_LIMIT_DAY',1000);  //每日充值皮蛋总数限制
class UserRechargeService extends PipiService {
	
	/**
	 * 写入用户账户充值记录
	 * @param array $records
	 * @return int
	 */
	public function saveUserRechargeRecords(array $records){
		if($records['ruid']<=0||$records['money']<=0||$records['pipiegg']<=0){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		if(!isset($records['uid'])){
			$records['uid']=$records['ruid'];
		}
		if(!isset($records['rip'])){
			$records['rip'] = Yii::app()->request->userHostAddress;
		}
		
		$records['currencycode'] = isset($records['currencycode']) ? $records['currencycode'] : CURRENCY_RMB;
		$records['issuccess'] = isset($records['issuccess']) ? $records['issuccess'] : 1;
		if(!isset($records['cbalance'])){
			$records['cbalance'] = UserBasicModel::model()->findByPk($records['ruid'])->recharge;
		}
		if(!isset($records['cpipiegg'])){
			$records['cpipiegg'] = ConsumeModel::model()->findByPk($records['ruid'])->pipiegg;
		}
		$records['sign'] = isset($records['sign']) ? $records['sign'] : md5($records['uid'].$records['rsource'].time());
		$records['rtime'] = time();
		$userRechargeReocrds=new UserRechargeRecordsModel();
		$this->attachAttribute($userRechargeReocrds,$records);
		$userRechargeReocrds->save();
		return $userRechargeReocrds->getPrimaryKey();
	}
	
	/**
	 * 检测用户当日充值限制
	 * @param int $uid 用户uid
	 * @return boolean
	 */
	public function checkUserRechargeLimitByDay($uid){
		$userRechargeReocrds=new UserRechargeRecordsModel();
		$stime=strtotime(date('Y-m-d 00:00:00',time()));
		$etime=strtotime(date('Y-m-d 23:59:59',time()));
		$rechargeCount=$userRechargeReocrds->getUserAllRechargeByDay($uid,$stime,$etime);
		$rechargeCount=isset($rechargeCount['pipiegg'])?$rechargeCount['pipiegg']:0;
		$rechargePipiegg=$userRechargeReocrds->getUserAllPipiEggsByTime($uid,$stime,$etime);
		if($rechargeCount>RECHAEGE_COUNT_LIMIT_DAY||$rechargePipiegg>RECHAEGE_PIPIEGG_LIMIT_DAY){
			return false;
		}
		return true;
	}
	
	public function updateUserRechargeRecords($recordId,$orderId){
		if($recordId<=0||$orderId<=0){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$userRechargeReocrds=new UserRechargeRecordsModel();
		return $userRechargeReocrds->updateByPk($recordId,array('rorderid'=>$orderId,'issuccess'=>2,'ctime'=>time()));
	}
}

?>