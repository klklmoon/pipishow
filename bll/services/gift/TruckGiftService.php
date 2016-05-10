<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: lei wei $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z lei wei  $ 
 * @package 
 */

class TruckGiftService extends PipiService {
	
	
	/**
	 * 存储能压过的礼物记录到redis
	 * @param array $record
	 * @return boolean
	 */
	public function saveTruckGiftRecord(array $record){
		if(empty($record)||$record['pipiegg']<=0)
			return $this->setError(Yii::t('common', 'Parameter is error'), 0);
		$otherRedisModel=new OtherRedisModel();
		$otherRedisModel->saveTruckGiftRecord($record);
		return $otherRedisModel->getTruckGiftRecord();
	}
	
	/**
	 * 获取直播间跑道礼物记录
	 * @return array
	 */
	public function getTruckGiftRecord(){
		$otherRedisModel=new OtherRedisModel();
		return $otherRedisModel->getTruckGiftRecord();
	}
	
}

?>