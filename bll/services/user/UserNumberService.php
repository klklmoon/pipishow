<?php
define('NUMBER_TYPE_FOUR',0);
define('NUMBER_TYPE_FIVE',1);
define('NUMBER_TYPE_SIX',2);
define('NUMBER_TYPE_SEVEN',3);

define('NUMBER_BUY_SHOP',0);
define('NUMBER_BUY_SEND',1);
define('NUMBER_BUY_ADMIN',2);
/**
 * 用户靓号服务层
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z suqian $ 
 * @package service
 * @subpackage user
 */
class UserNumberService extends PipiService {
	
	/**
	 * 购买靓号
	 * 
	 * @param int $uid 购买者UID
	 * @param int $number 靓号
	 * @param int $senderUid 被赠送人UID
	 * @param int $proxyUid 销售代理人UID
	 * @return boolean
	 */
	public function buyNumber($uid,$number,$senderUid = NULL,$proxyUid = NULL){
		if($uid <= 0 || $number<= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$len = strlen($number);
		if(!in_array($len,array(4,5,6,7))){
			return $this->setNotice('number',Yii::t('number','This pretty number must be greater than three and less than  eight figures'),0);
		}
// 		if($len == 5){
// 			return $this->setNotice('number',Yii::t('number','This pretty number temporarily open to buy'),0);
// 		}
		if($this->isUseNumber($number)){
			return $this->setNotice('number',Yii::t('number','This pretty number has been purchased from other Member'),0);
		}
		$numberModel = NumberModel::model();
		$orgNumberModel = $numberModel->findByPk($number);
		$price = 0;
		if(!$orgNumberModel){
			$price = $this->calNumberPrice($number);
		}else{
			$price = $orgNumberModel->confirm_price ? $orgNumberModel->confirm_price : $orgNumberModel->buffer_price;
			if($price <= 0){
				$price = $this->calNumberPrice($number);
			}
		}
		if($price <= 0){
			return $this->setNotice('number',Yii::t('number','Price error'),0);
		}
		$recordsId = 0;
		$consumeService = new ConsumeService();
		if($consumeService->consumeEggs($uid,$price)){
			$records['uid'] = $uid;
			$records['number'] = $number;
			$records['proxy_uid'] = $proxyUid ? $proxyUid : 0;
			$records['sender_uid'] = $senderUid ? $senderUid : 0;
			$records['buffer_price'] = $orgNumberModel ? $orgNumberModel->buffer_price : $this->calNumberPrice($number);
			$records['confirm_price'] = $price;
			$records['source'] = NUMBER_BUY_SHOP;
			$records['desc'] = '商城购买*'.$number;
			$recordsId = $this->saveUserBuyRecords($records);
			$userRechargeModel = new UserRechargeRecordsModel();
			if(!$senderUid){
				$charge = $userRechargeModel->getLastCharge($uid);
				$useingNumber['uid'] = $uid;
				$userNumber['uid'] = $uid;
				$userNumber['number'] = $number;
				$userNumber['short_desc'] = $orgNumberModel ? $orgNumberModel->short_desc : '';
				$userNumber['record_id'] = $recordsId;
				$userNumber['reward_type'] = NUMBER_BUY_SHOP;
				$userNumber['last_recharge_time'] = $charge ? $charge['ctime'] : 0;
				$this->saveUserNumber($userNumber);
			}else{
				$charge = $userRechargeModel->getLastCharge($senderUid);
				$useingNumber['uid'] = $senderUid;
				$records['uid'] = $senderUid;
				$records['sender_uid'] = $uid;
				$records['source'] = NUMBER_BUY_SEND;
				$records['source_record_id'] = $recordsId;
				$records['desc'] = $records['desc'] = '用户赠送*'.$number;
				$recordsId = $this->saveUserBuyRecords($records);
				$userNumber['uid'] = $senderUid;
				$userNumber['number'] = $number;
				$userNumber['short_desc'] = $orgNumberModel ? $orgNumberModel->short_desc : '';
				$userNumber['record_id'] = $recordsId;
				$userNumber['reward_type'] = NUMBER_BUY_SEND;
				$userNumber['last_recharge_time'] = $charge ? $charge['ctime'] : 0;
				$this->saveUserNumber($userNumber);
			}
			
			$pipieggRecords['uid'] = $uid ;
			$pipieggRecords['source'] = SOURCE_PROPS ;
			$pipieggRecords['sub_source'] = SUBSOURCE_PROPS_NUMBER ;
			$pipieggRecords['client'] = CLIENT_SHOP;
			$pipieggRecords['pipiegg'] = $price;
			$pipieggRecords['record_sid'] = $recordsId;
			$pipieggRecords['from_target_id'] = $number;
			$pipieggRecords['to_target_id'] = $senderUid ? $senderUid : 0;
			$pipieggRecords['extra'] = '购买靓号' ;
			$consumeService->saveUserPipiEggRecords($pipieggRecords,0);
			
			$userJson['num']['n'] = (string)$number;
			$userJson['num']['s'] = $orgNumberModel ? $orgNumberModel->short_desc : '';
			$useingNumber['number'] = $number;
			$useingNumber['number_short_desc'] = $orgNumberModel ? $orgNumberModel->short_desc : '';
			$userPropsServie = new UserPropsService();
			$userJsonInfoService = new UserJsonInfoService();
			$userPropsServie->saveUserPropsAttribute($useingNumber);
			$consumeService->saveUserConsumeAttribute(array('uid'=>$uid,'dedication'=>$price * Yii::app()->params['change_relation']['pipiegg_to_dedication'],'pipiegg'=>$price));
			$userJsonInfoService->setUserInfo($senderUid ? $senderUid : $uid,$userJson);
			$zmq = $this->getZmq();
			$zmq->sendZmqMsg(609,array('type'=>'update_json','uid'=>$senderUid ? $senderUid : $uid,'json_info'=>$userJson));
			if($orgNumberModel){
				$orgNumberModel->delete();
			}
		}else{
			return $this->setNotice('number', Yii::t('props','You do not have a sufficient share of balance, recharge'),0);
		}
		return $recordsId;
	}
	
	/**
	 * 存储用户靓号
	 * 
	 * @param array $userNumber
	 * @return array
	 */
	public function saveUserNumber(array $userNumber){
		if (!isset($userNumber['uid']) || $userNumber['uid'] <= 0){
			return $this->setError(Yii::t('common', 'Parameter is not empty'),array());
		}
		
		if (!isset($userNumber['number']) || empty($userNumber['number'])){
			return $this->setError(Yii::t('common', 'Parameter is not empty'),array());
		}
		$userNumberModel = new UserNumberModel();
		$_userNumberModel = $userNumberModel->findByPk(array('uid'=>$userNumber['uid'],'number'=>$userNumber['number']));
		if($_userNumberModel){
			$this->attachAttribute($_userNumberModel,$userNumber);
			if(!$_userNumberModel->validate()){
				return $this->setNotices($_userNumberModel->getErrors(),array());
			}
			return $_userNumberModel->save();
		}else{
			$userNumberModel->create_time = time();
			$userNumberModel->status = 0;
			$this->attachAttribute($userNumberModel,$userNumber);
			if(!$userNumberModel->validate()){
				return $this->setNotices($userNumberModel->getErrors(),array());
			}
			return $userNumberModel->save();
		}
		return true;
	}
	
	/**
	 * 存储用户购买靓号记录
	 * 
	 * @param array $records
	 * @return int
	 */
	public function saveUserBuyRecords(array $records){
		if (!isset($records['uid']) || $records['uid'] <= 0){
			return $this->setError(Yii::t('common', 'Parameter is not empty'));
		}
		
		if (!isset($records['number'])){
			return $this->setError(Yii::t('common', 'Parameter is not empty'));
		}
		$userBuyRecordsModel = new UserNumberRecordsModel();
		$userBuyRecordsModel->create_time = time();
		$this->attachAttribute($userBuyRecordsModel,$records);
		$userBuyRecordsModel->save();
		return $userBuyRecordsModel->getPrimaryKey();
	}
	
	public function isUseNumber($number,$uid = null){
		$condition = 'number = :number AND status = 0';
		$params[':number'] = $number;
		if($uid && is_numeric($uid)){
			$condition .= ' AND uid = :uid ';
			$params[':uid'] = $uid;
		}
		$UseInfo = UserNumberModel::model()->find($condition,$params);
		if($UseInfo)
			$UseInfo = $UseInfo->getAttributes();
		else 
			$UseInfo = array();
		return $UseInfo;
	}
	
	public function getUserNumberList($uid){
		if($uid <= 0){
			return $this->setError(Yii::t('common', 'Parameter is not empty'));
		}
		$userNumberModel = new UserNumberModel();
		$userNumbers = $userNumberModel->getUserNumber($uid);
		$userNumbers = $this->arToArray($userNumbers);
		return $this->buildUserNumbers($userNumbers);
	}
	/**
	 * 取得靓号列表
	 * 
	 * @param int $bit
	 * @param int $limit
	 * @return array
	 */
	public function getNumberList($bit = null,$limit = NULL,$offSet = NULL){
		$numberModel = NumberModel::model();
		$return = $numberModel->getNumberList($bit,$limit,$offSet);
		if(!empty($return)){
			foreach($return as &$r){
				$r['buffer_price'] = intval($r['buffer_price']);
				$r['confirm_price'] = intval($r['confirm_price']);
			}
		}
		return $return;
	}
	
	/**
	 * @param int $bit
	 * @return int
	 */
	public function countNumberList($bit){
		return  NumberModel::model()->countNumberList($bit);
	}
	/**
	 * 获取随机生成的靓号
	 * 
	 * @param int $bit
	 * @param int $num
	 * @return array
	 */
	public function getRandNumber($bit,$num = 20){
		$userNumberModel  =  UserNumberModel::model();
		$number = $this->randNumber($bit,$num);
		$useNumber = $userNumberModel->getUseNumber($number);
		if($useNumber){
			$useNumber = $this->arToArray($useNumber);
			foreach($useNumber as $_use){
				$key = array_search($_use['number'],$number);
				if($key != false){
					unset($number[$key]);
				}
			}
		}
		$validNumber = array();
		foreach($number as $_key=>$_number){
			$validNumber[$_key]['number'] = $_number;
			if($bit == 5){
				$validNumber[$_key]['buffer_price'] = $this->calFiveNumberPrice($_number);
			}elseif($bit == 6){
				$validNumber[$_key]['buffer_price'] = $this->calSixNumberPrice($_number);
			}elseif($bit == 7){
				$validNumber[$_key]['buffer_price'] = $this->calSevenNumberPrice($_number);
			}else{
				$validNumber[$_key]['buffer_price'] = 0;
			}
		}
		return $validNumber;
	}
	/**
	 * 生成随机的靓号，不能有前导0
	 * 
	 * @param int $bit
	 * @param int $num
	 * @return array
	 */
	public function randNumber($bit,$num = 20){
		$_number = array();
		for($i=0;$i<$num;$i++){
			$tmp = '';
			for($j=0;$j<$bit;$j++){
				if($j == 0) $tmp = substr(mt_rand(1,9),0,1);
				else $tmp .= substr(mt_rand(0,9),0,1);
			}
			$_number[$i] = $tmp;
		}
		return array_unique($_number);
	}
	
	public function calNumberPrice($number){
		$strLen = strlen($number);
		if($strLen == 4){
			return $this->calFourNumberPrice($number);
		}elseif($strLen == 5){
			return $this->calFiveNumberPrice($number);
		}elseif($strLen == 6){
			return $this->calSixNumberPrice($number);
		}elseif($strLen == 7){
			return $this->calSevenNumberPrice($number);
		}
		return -1;
	}
	/** 
	 * 计算四位靓号自助价格
	 * 
	 * @param int $number
	 * @return int
	 */
	public function calFourNumberPrice($number){
		$strLen = strlen($number);
		if($number <= 0 || $strLen != 4){
			return -1;
		}
		$price = 500000;
		$conData = $this->getMaxConNumber($number);
		if($conData){
			if($conData['count'] >= 3){
				$price += 1000000;
			}
		}
		return $price;
	}
	
	/**
	 * 计算五位靓号自助价格
	 *
	 * @param int $number
	 * @return int
	 */
	public function calFiveNumberPrice($number){
		$strLen = strlen($number);
		if($number <= 0 || $strLen != 5){
			return -1;
		}
		$price = 35000;
		//判断是否有连号
		$conData = $this->getMaxConNumber($number);
		if($conData){
			if($conData['count'] == 2){
				$price += 10000;
			}elseif($conData['count'] == 3){
				$price += 20000;
			}elseif($conData['count'] == 4){
				$price += 30000;
			}elseif($conData['count'] == 5){
				$price += 50000;
			}
		}
		//判断是否有倒号
		$conData = $this->getMaxUnConNumber($number);
		if($conData){
			if($conData['count'] == 3){
				$price += 10000;
			}elseif($conData['count'] == 4){
				$price += 20000;
			}elseif($conData['count'] == 5){
				$price += 40000;
			}
		}
		//判断复数连号
		$conData = $this->getMaxPluralConNumber($number);
		if($conData){
			if($conData['count'] == 2){
				$price += 10000;
			}elseif($conData['count'] == 3){
				$price += 20000;
			}elseif($conData['count'] == 4){
				$price += 40000;
			}elseif($conData['count'] == 5){
				$price += 60000;
			}
		}
		//判断是否是对数
		if($this->isFiveSymmetryNumber($number)){
			$price += 35000;
		}
		//判断是否含有数字8
		if($count = $this->hasNumber($number,8)){
			$price += 5000 * $count;
		}
		return $price;
	}
	
	/** 
	 * 计算六位靓号自助价格
	 * 
	 * @param int $number
	 * @return int
	 */
	public function calSixNumberPrice($number){
		$strLen = strlen($number);
		if($number <= 0 || $strLen != 6){
			return -1;
		}
		$price = 20000;
		//判断是否有连号
		$conData = $this->getMaxConNumber($number);
		if($conData){
			if($conData['count'] == 3){
				$price += 5000;
			}elseif($conData['count'] == 4){
				$price += 15000;
			}elseif($conData['count'] == 5){
				$price += 25000;
			}elseif($conData['count'] == 6){
				$price += 50000;
			}
		}
		//判断是否有倒号
		$conData = $this->getMaxUnConNumber($number);
		if($conData){
			if($conData['count'] == 3){
				$price += 5000;
			}elseif($conData['count'] == 4){
				$price += 10000;
			}elseif($conData['count'] == 5){
				$price += 20000;
			}elseif($conData['count'] == 6){
				$price += 35000;
			}
		}
		//判断复数连号
		$conData = $this->getMaxPluralConNumber($number);
		if($conData){
			if($conData['count'] == 3){
				$price += 5000;
			}elseif($conData['count'] == 4){
				$price += 20000;
			}elseif($conData['count'] == 5){
				$price += 30000;
			}elseif($conData['count'] == 6){
				$price += 60000;
			}
		}
		//判断是否是对数
		if($this->isSixSymmetryNumber($number)){
			$price += 30000;
		}
		//判断是否含有数字8
		if($count = $this->hasNumber($number,8)){
			$price += 2000 * $count;
		}
		return $price;
	}
	
	/** 
	 * 计算七位靓号自助价格
	 * 
	 * @param int $number
	 * @return int
	 */
	public function calSevenNumberPrice($number){
		$strLen = strlen($number);
		if($number <= 0 || $strLen != 7){
			return -1;
		}
		$price = 10000;
		//判断是否有连号
		$conData = $this->getMaxConNumber($number);
		if($conData){
			if($conData['count'] == 3){
				$price += 5000;
			}elseif($conData['count'] == 4){
				$price += 10000;
			}elseif($conData['count'] == 5){
				$price += 20000;
			}elseif($conData['count'] == 6){
				$price += 30000;
			}elseif($conData['count'] == 7){
				$price += 40000;
			}
		}
		//判断是否有倒号
		$conData = $this->getMaxUnConNumber($number);
		if($conData){
			if($conData['count'] == 4){
				$price += 5000;
			}elseif($conData['count'] == 5){
				$price += 10000;
			}elseif($conData['count'] == 6){
				$price += 20000;
			}elseif($conData['count'] == 7){
				$price += 40000;
			}
		}
		//判断复数连号
		$conData = $this->getMaxPluralConNumber($number);
		if($conData){
			if($conData['count'] == 3){
				$price += 5000;
			}elseif($conData['count'] == 4){
				$price += 10000;
			}elseif($conData['count'] == 5){
				$price += 20000;
			}elseif($conData['count'] == 6){
				$price += 30000;
			}elseif($conData['count'] == 7){
				$price += 50000;
			}
		}
		//判断是否是对数
		if($this->isSevenSymmetryNumber($number)){
			$price += 15000;
		}
		//判断是否含有数字8
		if($count = $this->hasNumber($number,8)){
			$price += 1000 * $count;
		}
		return $price;
	}
	/**
	 * 取得最大的连对
	 * @param int $number
	 * @return array
	 */
	public function getMaxConNumber($number){
		$data = $this->formatConNumber($number);
		if($data){
			$max = 0;
			$key = 0;
			foreach($data as $_key=>$_data){
				if($_data['count'] > $max){
					$max = $_data['count'];
					$key = $_key;
				}
			}
			return $data[$key];
		}
		return array();
	}
	
	/**
	 * 取得最大的倒数连对
	 * 
	 * @param int $number
	 * @return array
	 */
	public function getMaxUnConNumber($number){
		$data = $this->formatUnConNumber($number);
		if($data){
			$max = 0;
			$key = 0;
			foreach($data as $_key=>$_data){
				if($_data['count'] > $max){
					$max = $_data['count'];
					$key = $_key;
				}
			}
			return $data[$key];
		}
		return array();
	}
	
	/**
	 * 取得最大的复数连对
	 * 
	 * @param int $number
	 * @return array
	 */
	public function getMaxPluralConNumber($number){
		$data = $this->formatPluralConNumber($number);
		if($data){
			$max = 0;
			$key = 0;
			foreach($data as $_key=>$_data){
				if($_data['count'] > $max){
					$max = $_data['count'];
					$key = $_key;
				}
			}
			return $data[$key];
		}
		return array();
	}
	
	/**
	 * 取得一串数字中是连号的
	 * 
	 * @param int $number
	 * @return array
	 */
	public function formatConNumber($number){
		if($number <= 0){
			return array();
		}
		$number = (string) $number;
		$strLen = strlen($number);
		$array = array();
		$j = 0;
		$a = 0;
		$count = 0;
		for ($i = 0; $i < $strLen; $i++) {
			if(isset($number[$i+1])){
				if($number[$i] != $number[$i+1]-1){
					if($j>0){
						$start = 0;
						foreach($array as $item){
							$start +=  $item['count'];
							
						}
						if($a>0){
							if($number[0] == $number[1] -1 ){
								$start = $start + $a - 1;
							}else{
								if(empty($array)){
									$start = $start + $a;
								}else{
									$start = $start + $a-count($array);
								}
							}
						}
						$array[$count++] = array(
							'count'=>$j+1,
							'value'=>substr($number,$start,$j+1)
						);
					}
					$j = 0;
				}
			 	if($number[$i] == $number[$i+1]-1){
			 		$j++;
			 	}else{
			 		$a++;
			 	}
			}
		}
		if($j>0){
			$array[$count++] = array(
							'count'=>$j+1,
							'value'=>substr($number,$strLen-($j+1),$j+1)
			);
		}
		return $array;
	}
	/**
	 * 求倒号
	 * 
	 * @param int $number
	 * @return int
	 */
	public function formatUnConNumber($number){
		if($number <= 0){
			return array();
		}
		$number = (string) $number;
		$strLen = strlen($number);
		$array = array();
		$j = 0;
		$a = 0;
		$count = 0;
		for ($i = 0; $i < $strLen; $i++) {
			if(isset($number[$i+1])){
				if($number[$i]-1 != $number[$i+1]){
					if($j>0){
						$start = 0;
						foreach($array as $item){
							$start +=  $item['count'];
							
						}
						if($a>0){
							if($number[0]-1 == $number[1] ){
								$start = $start + $a - 1;
							}else{
								if(empty($array)){
									$start = $start + $a;
								}else{
									$start = $start + $a-count($array);
								}
							}
						}
						$array[$count++] = array(
							'count'=>$j+1,
							'value'=>substr($number,$start,$j+1)
						);
					}
					$j = 0;
				}
			 	if($number[$i]-1 == $number[$i+1]){
			 		$j++;
			 	}else{
			 		$a++;
			 	}
			}
		}
		if($j>0){
			$array[$count++] = array(
							'count'=>$j+1,
							'value'=>substr($number,$strLen-($j+1),$j+1)
			);
		}
		return $array;
	}
	
	/**
	 * 求复数连号倒号
	 * 
	 * @param int $number
	 * @return int
	 */
	public function formatPluralConNumber($number){
		if($number <= 0){
			return array();
		}
		$number = (string) $number;
		$strLen = strlen($number);
		$array = array();
		$j = 0;
		$a = 0;
		$count = 0;
		for ($i = 0; $i < $strLen; $i++) {
			if(isset($number[$i+1])){
				if($number[$i] != $number[$i+1]){
					if($j>0){
						$start = 0;
						foreach($array as $item){
							$start +=  $item['count'];
							
						}
						if($a>0){
							if($number[0] == $number[1] ){
								$start = $start + $a - count($array);
							}else{
								if(empty($array)){
									$start = $start + $a;
								}else{
									$start = $start + $a-count($array);
								}
							}
						}
						$array[$count++] = array(
							'count'=>$j+1,
							'value'=>substr($number,$start,$j+1)
						);
					}
					$j = 0;
				}
			 	if($number[$i] == $number[$i+1]){
			 		$j++;
			 	}else{
			 		$a++;
			 	}
			}
		}
		if($j>0){
			$array[$count++] = array(
							'count'=>$j+1,
							'value'=>substr($number,$strLen-($j+1),$j+1)
			);
		}
		return $array;
	}
	
	/**
	 * 是否有几个指定的特殊数字
	 * 
	 * @param int $number
	 * @param int $point
	 * @return int
	 */
	public function hasNumber($number ,$point){
		if($number <= 0){
			return 0;
		}
		$number = (string) $number;
		$strLen = strlen($number);
		$r = 0;
		for ($i = 0; $i < $strLen; $i++) {
			if($number[$i] == $point){
				$r++;
			}
		}
		return $r;
	}
	
	/**
	 * 5位靓号中是否有对数靓号
	 *
	 * @param int $number
	 * @return int
	 */
	public function isFiveSymmetryNumber($number){
		if($number <= 0){
			return false;
		}
		$number = (string) $number;
		$strLen = strlen($number);
		if($strLen != 5){
			return false;
		}
		for ($i = 0; $i < 2; $i++) {
			if($number[$i] != $number[$strLen-$i-1]){
				return false;
			}
		}
		return true;
	}
	
	/**
	 * 6位靓号中是否有对数靓号
	 * 
	 * @param int $number
	 * @return int
	 */
	public function isSixSymmetryNumber($number){
		if($number <= 0){
			return false;
		}
		$number = (string) $number;
		$strLen = strlen($number);
		if($strLen != 6){
			return false;
		}
		for ($i = 0; $i <= 2; $i++) {
			if($number[$i] != $number[$strLen-$i-1]){
				return false;
			}
		}
		return true;
	}
	
	/**
	 * 7位靓号中是否有对数靓号
	 * 
	 * @param int $number
	 * @return int
	 */
	public function isSevenSymmetryNumber($number){
		if($number <= 0){
			return false;
		}
		$number = (string) $number;
		$strLen = strlen($number);
		if($strLen != 7){
			return false;
		}
		$middle = ceil(7 / 2); 
		for ($i = 0; $i < $middle; $i++) {
			if($number[$i] != $number[$strLen-$i-1]){
				return false;
			}
		}
		return true;
	}
	
	public function getUserNumber($status = null){
		$list = array(
			NUMBER_BUY_SHOP=>'商城购买',
			NUMBER_BUY_SEND=>'用户赠送',
			NUMBER_BUY_ADMIN=>'系统赠送'
		);
		return is_null($status) ? $list : $list[$status];
	}
	
	protected function buildUserNumbers(array $userNumbers){
		foreach($userNumbers as $key => $userNumber){
			$userNumbers[$key]['type_desc'] = $this->getUserNumber($userNumber['reward_type']);
			$userNumbers[$key]['time_desc'] = date('Y-m-d H:i',$userNumber['create_time']);
			if($userNumber['status']){
				$userNumbers[$key]['status_desc'] = '已回收';
			}else{
				$userNumbers[$key]['status_desc'] = '正常';
			}
		}
		return $userNumbers;
	}
	
	/**
	 * @author supeng
	 * @return array
	 */
	public function getNumberType(){
		return array(
			NUMBER_TYPE_FOUR => '四位靓号',
			NUMBER_TYPE_FIVE => '五位靓号',
			NUMBER_TYPE_SIX => '六位靓号',
			NUMBER_TYPE_SEVEN => '七位靓号'
		);
	}
	
	/**
	 * 获取用户靓号状态
	 * 
	 * @author supeng
	 * @return Array 
	 */
	public function getUNumberStatus(){
		return array(
				0 => '正常',
				1 => '回收',
			);
	}
	
	/**
	 * 获取用户靓号回收类型
	 *
	 * @author supeng
	 * @return Array
	 */
	public function getRecoverType(){
		return array(
			0 => '自动回收',
			1 => '手工回收',
		);
	}
	
	/**
	 * 查询靓号列表
	 *
	 * @author supeng
	 * @param array $condition
	 * @param unknown_type $offset
	 * @param unknown_type $limit
	 * @param unknown_type $isLimit
	 * @return Ambigous <multitype:, multitype:NULL , NULL, unknown, multitype:unknown Ambigous <unknown, NULL> , mixed, multitype:unknown >
	 */
	public function searchNumberList(Array $condition = array(),$offset = 0, $limit = 20, $isLimit = true){
		$model = new NumberModel();
		$data = $model->searchNumberList($condition,$offset,$limit,$isLimit);
		if ($data['list']){
			$data['list'] = $this->arToArray($data['list']);
		}
		return $data;
	}
	
	/**
	 * 获取用户靓号列表
	 * 
	 * @author supeng
	 * @param array $condition
	 * @param unknown_type $offset
	 * @param unknown_type $limit
	 * @param unknown_type $isLimit
	 * @return multitype:number multitype: |Ambigous <multitype:, multitype:NULL , NULL, unknown, multitype:unknown Ambigous <unknown, NULL> , mixed, multitype:unknown , number, string>
	 */
	public function searchUserNumberList(Array $condition = array(),$offset = 0, $limit = 20, $isLimit = true){
		if(!empty($condition['username']) || !empty($condition['nickname']) || !empty($condition['realname'])){
			$UserService = new UserService();
			$info = $UserService->searchUserList($offset,$limit,$condition,false);
			if($info['uids']){
				$count = $info['count'];
				$condition['uids'] = $info['uids'];
			}else{
				return array('count'=>0,'list'=>array());
			}
		}
		$model = new UserNumberModel();
		$data = $model->searchUserNumberList($condition,$offset,$limit,$isLimit);
		if ($data['list']){
			$data['list'] = $this->arToArray($data['list']);
		}
		return $data;
	}
	
	/**
	 * 获取用户靓号回收记录列表
	 *
	 * @author supeng
	 * @param array $condition
	 * @param unknown_type $offset
	 * @param unknown_type $limit
	 * @param unknown_type $isLimit
	 * @return multitype:number multitype: |Ambigous <multitype:, multitype:NULL , NULL, unknown, multitype:unknown Ambigous <unknown, NULL> , mixed, multitype:unknown , number, string>
	 */
	public function searchUserNumberRecoverList(Array $condition = array(),$offset = 0, $limit = 20, $isLimit = true){
		if(!empty($condition['username']) || !empty($condition['nickname']) || !empty($condition['realname'])){
			$UserService = new UserService();
			$info = $UserService->searchUserList($offset,$limit,$condition,false);
			if($info['uids']){
				$count = $info['count'];
				$condition['uids'] = $info['uids'];
			}else{
				return array('count'=>0,'list'=>array());
			}
		}
		$model = new UserNumberRecoverModel();
		$data = $model->searchUserNumberRecoverList($condition,$offset,$limit,$isLimit);
		if ($data['list']){
			$data['list'] = $this->arToArray($data['list']);
		}
		return $data;
	}
	
	/**
	 * 获取用户靓号回收记录列表
	 *
	 * @author supeng
	 * @param array $condition
	 * @param unknown_type $offset
	 * @param unknown_type $limit
	 * @param unknown_type $isLimit
	 * @return multitype:number multitype: |Ambigous <multitype:, multitype:NULL , NULL, unknown, multitype:unknown Ambigous <unknown, NULL> , mixed, multitype:unknown , number, string>
	 */
	public function searchBuyNumberRecordsList(Array $condition = array(),$offset = 0, $limit = 20, $isLimit = true){
		if(!empty($condition['username']) || !empty($condition['nickname']) || !empty($condition['realname'])){
			$UserService = new UserService();
			$info = $UserService->searchUserList($offset,$limit,$condition,false);
			if($info['uids']){
				$count = $info['count'];
				$condition['uids'] = $info['uids'];
			}else{
				return array('count'=>0,'list'=>array());
			}
		}
		$model = new UserNumberRecordsModel();
		$data = $model->searchBuyNumberRecordsList($condition,$offset,$limit,$isLimit);
		if ($data['list']){
			$data['list'] = $this->arToArray($data['list']);
		}
		return $data;
	}
	
	/**
	 * 获取靓号基本信息
	 *
	 * @author supeng
	 * @param int $number
	 * @return multitype:
	 */
	public function getNumberById($number){
		$number = (string) $number;
		if (!$number)
			return array();
		$model = new NumberModel();
		$info = $model->findByPk($number);
		if ($info)
			$info = $info->getAttributes();
		else 
			$info = array();
		return $info;
	}
	
	/**
	 * 存储用户靓号
	 *
	 * @author supeng
	 * @param array $Number
	 * @return array
	 */
	public function saveNumber(array $Number){
		if (!isset($Number['number'])){
			return $this->setError(Yii::t('common', 'Parameter is not empty'),array());
		}
	
		$Number['number'] = (string)$Number['number'];
		if (!isset($Number['confirm_price']) || $Number['confirm_price'] < 0){
			return $this->setError(Yii::t('common', 'Parameter is not empty'),array());
		}
		
		if (!isset($Number['short_desc']) || empty($Number['short_desc'])){
			return $this->setError(Yii::t('common', 'Parameter is not empty'),array());
		}
		
		if (isset($Number['number_type']) && !is_numeric($Number['number_type'])){
			return $this->setError(Yii::t('common', 'Parameter is not empty'),array());
		}
	
		$model = new NumberModel();
		$_numberModel = $model->findByPk($Number['number']);
		if($_numberModel){
			$this->attachAttribute($_numberModel,$Number);
			if(!$_numberModel->validate()){
				return $this->setNotices($_numberModel->getErrors(),array());
			}
			return $_numberModel->save();
		}else{
			$Number['buffer_price'] = $this->calNumberPrice($Number['number']);
			$Number['create_time'] = time();
			$this->attachAttribute($model,$Number);
			if(!$model->validate()){
				return $this->setNotices($model->getErrors(),array());
			}
			return $model->save();
		}
		return false;
	}
	
	/**
	 * 存储回收记录
	 *
	 * @author supeng
	 * @param array $recover
	 * @return boolean
	 */
	public function saveUserNumberRecover(array $recover){
		if (!isset($recover['number']) || empty($recover['number'])){
			return $this->setError(Yii::t('common', 'Parameter is not empty'),array());
		}
	
		if (!isset($recover['uid']) || $recover['uid'] < 0){
			return $this->setError(Yii::t('common', 'Parameter is not empty'),array());
		}
		
		if (!isset($recover['opertor_uid']) || $recover['opertor_uid'] < 0){
			return $this->setError(Yii::t('common', 'Parameter is not empty'),array());
		}
	
		if (!isset($recover['reason']) || empty($recover['reason'])){
			return $this->setError(Yii::t('common', 'Parameter is not empty'),array());
		}
	
		if (isset($recover['recover_type']) && !is_numeric($recover['recover_type'])){
			return $this->setError(Yii::t('common', 'Parameter is not empty'),array());
		}
	
		$recover['create_time'] = time();
		$model = new UserNumberRecoverModel();
		$this->attachAttribute($model,$recover);
		if(!$model->validate()){
			return $this->setNotices($model->getErrors(),array());
		}
		return $model->save();
	}
	
	/**
	 * 回收靓号属性并发消息
	 * 
	 * @author supeng
	 * @param int $uid
	 * @param int $number
	 * @return boolean
	 */
	public function recoverUPropsAttr($uid,$number){
		if($uid && is_numeric($uid) && $number && is_numeric($number)){
			$service = new UserPropsService();
			$attrInfo = $service->getUserPropsAttributeByUid($uid);
			if($attrInfo){
				$isNum = true;
				if(isset($attrInfo['number']) && $attrInfo['number'] == $number){
					$isNum = false;
					$useingNumber['uid'] = $uid;
					$useingNumber['number'] = '';
					$useingNumber['number_short_desc'] = '';
					$service->saveUserPropsAttribute($useingNumber);
				}
				
				if($isNum && $attrInfo['number']){
					$userJson['num']['n'] = $attrInfo['number'];
					$userJson['num']['s'] = $attrInfo['number_short_desc'];
				}else{
					$userJson['num'] = '{}';
				}
				
				$userJsonInfoService = new UserJsonInfoService();
				$userJsonInfoService->setUserInfo($uid,$userJson);
				$zmq = $this->getZmq();
				$zmq->sendZmqMsg(609,array('type'=>'update_json','uid'=>$uid,'json_info'=>$userJson));
			}
		}
		return false;
	}
	
	/**
	 * 删除靓号
	 * 
	 * @author supeng
	 * @param int $number
	 * @return mix
	 */
	public function delNumber($number){
		if (!$number){
			return $this->setError(Yii::t('common', 'Parameter is not empty'),false);
		}
		
		$model = new NumberModel();
		return $model->deleteByPk($number);
	}
	
	/**
	 * 后台赠送靓号
	 *
	 * @author supeng
	 * @param int $uid 购买者UID
	 * @param int $number 靓号
	 * @return int
	 */
	public function adminSendNumber($uid,$number,$last_recharge_time = 0){
		$number = (string) $number;
		if($uid <= 0 || !$number){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$len = strlen($number);
		if(!in_array($len,array(4,5,6,7))){
			return $this->setNotice('number',Yii::t('number','This pretty number must be greater than three and less than  eight figures'),0);
		}
		if($len == 5){
			//return $this->setNotice('number',Yii::t('number','This pretty number temporarily open to buy'),0);
		}
		if($this->isUseNumber($number)){
			return $this->setNotice('number',Yii::t('number','This pretty number has been purchased from other Member'),0);
		}
		$numberModel = NumberModel::model();
		$orgNumberModel = $numberModel->findByPk($number);
		$price = 0;
		if(!$orgNumberModel){
			$price = $this->calNumberPrice($number);
		}else{
			$price = $orgNumberModel->confirm_price ? $orgNumberModel->confirm_price : $orgNumberModel->buffer_price;
			if($price <= 0){
				$price = $this->calNumberPrice($number);
			}
		}
		if($price <= 0){
			return $this->setNotice('number',Yii::t('number','Price error'),0);
		}
		$recordsId = 0;
	
		$records['uid'] = $uid;
		$records['number'] = $number;
		$records['proxy_uid'] = 0;
		$records['sender_uid'] = 0;
		$records['buffer_price'] = $orgNumberModel ? $orgNumberModel->buffer_price : $this->calNumberPrice($number);
		$records['confirm_price'] = $price;
		$records['source'] = NUMBER_BUY_ADMIN;
		$records['desc'] = '后台赠送*'.$number;
		$recordsId = $this->saveUserBuyRecords($records);
	
		if($recordsId){
			$userNumber['uid'] = $uid;
			$userNumber['number'] = $number;
			$userNumber['short_desc'] = $orgNumberModel ? $orgNumberModel->short_desc : '';
			$userNumber['record_id'] = $recordsId;
			$userNumber['reward_type'] = NUMBER_BUY_ADMIN;
			$userNumber['last_recharge_time'] = $last_recharge_time?$last_recharge_time:0;
			$userNumber['status'] = 0;
			$this->saveUserNumber($userNumber);
				
			$userJson['num']['n'] = (string)$number;
			$userJson['num']['s'] = $orgNumberModel ? $orgNumberModel->short_desc : '';
				
			$useingNumber['uid'] = $uid;
			$useingNumber['number'] = $number;
			$useingNumber['number_short_desc'] = $orgNumberModel ? $orgNumberModel->short_desc : '';
			$userPropsServie = new UserPropsService();
			$userJsonInfoService = new UserJsonInfoService();
			$userPropsServie->saveUserPropsAttribute($useingNumber);
			$userJsonInfoService->setUserInfo($uid,$userJson);
			$zmq = $this->getZmq();
			$zmq->sendZmqMsg(609,array('type'=>'update_json','uid'=>$uid,'json_info'=>$userJson));
		}
		return $recordsId;
	}
}

?>