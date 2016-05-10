<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author lei wei <leiwei@pipi.cn>
 * @version $Id: templates.xml 894 2013-07-25 07:55:25Z leiwei $ 
 * @package
 */
class PhoneService extends PipiService {
	
	/**
	 * 获取手机端验证码
	 * @param string $id  随机键值
	 * @return string
	 */
	public function getPhoneCode($id){
		if(empty($id)){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$otherRedisModel=new OtherRedisModel();
		return $otherRedisModel->getPhoneCode($id);
	}
	
	/**
	 * 存储手机端验证码
	 * @param string $id  随机键值
	 * @param string $value  验证码值
	 * @param int $expirTime 失效时间
	 * @return boolean
	 */
	public function savePhoneCode($id,$value,$expirTime=60){
		if(empty($id)){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$otherRedisModel=new OtherRedisModel();
		return $otherRedisModel->savePhoneCode($id,$value,$expirTime);
	}
	
}

?>