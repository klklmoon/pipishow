<?php
define('PROPSRECORDS_SOURCE_BUY',0);
define('PROPSRECORDS_SOURCE_ADMIN',1);
define('PROPSRECORDS_SOURCE_ACTIVITY',2);
define('PROPSRECORDS_SOURCE_USEPROPS',3);
define('PROPSRECORDS_SOURCE_GAME',4);
define('PROPSRECORDS_SOURCE_AWARD',5);  //中奖赠送

/**
 * 用户与道具相关的服务层
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: UserPropsService.php 17326 2014-01-08 11:37:49Z hexin $ 
 * @package service
 * @subpackage props
 */
class UserPropsService extends PipiService {

	/**
	 * 存储用户道具属性
	 * 
	 * @param array $propsAttriute 道具属性
	 * @return boolean
	 */
	public function  saveUserPropsAttribute(array $propsAttriute){
		if(($uid=$propsAttriute['uid']) <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		
		$userPropsModel = new UserPropsAttributeModel();
		$_userPropsModel = $userPropsModel->findByPk($uid);
		if(empty($_userPropsModel )){
			$this->attachAttribute($userPropsModel,$propsAttriute);
			$flag = $userPropsModel->save();
		}else{
			$this->attachAttribute($_userPropsModel,$propsAttriute);
			$flag=$_userPropsModel->save();
		}
		
		if($flag && $this->isAdminAccessCtl()){
			$this->saveAdminOpLog('修改 用户道具属性(UID='.$uid.')',$uid);
		}
		return $flag;
	}
	
	/**
	 * 修改用户使用的座驾
	 * @edit by guoshaobo
	 * @param unknown_type $condition
	 * @param unknown_type $carInfo
	 * @return mix|boolean
	 */
	public function saveUserCar($condition,$carInfo)
	{
		if(($uid=$condition['uid']) <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		}
		$res = $this->saveUserPropsAttribute($condition);
		if($res){
			$userJson = new UserJsonInfoService();
			$userInfo = $userJson->getUserInfo($uid,false);
			$userInfo['car'] = isset($carInfo['name']) ? array(
								'n'=>$carInfo['name'],
								'vt'=>$carInfo['valid_time'],
								'f'=>$carInfo['flash'],
								'to'=>$carInfo['timeout']
							) : array();
			$userJson->setUserInfo($uid, $userInfo);
			$car['car'] = $userInfo['car'];
			$zmq = $this->getZmq();
			$zmq->sendZmqMsg(609,array('type'=>'update_json','uid'=>$uid,'json_info'=>$car));
			return true;
		}
		return false;
	}
	
	/**
	 * 修改用户使用的vip的隐身状况
	 * @param unknown_type $uid
	 * @param unknown_type $is_hidden
	 */
	public function saveVipHidden($propsAttriute)
	{
		if(($uid=$propsAttriute['uid']) <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		}
		$res = $this->saveUserPropsAttribute($propsAttriute);
		if($res){
			$userJson = new UserJsonInfoService();
			$userInfo = $userJson->getUserInfo($uid,false);
			
			$userInfo['vip']['h'] = $propsAttriute['is_hidden'];
			$vip['vip'] = $userInfo['vip'];

			$userJson->setUserInfo($uid, $userInfo);
			if($vip){
				$zmq = $this->getZmq();
				$zmq->sendZmqMsg(609,array('type'=>'update_json','uid'=>$uid,'json_info'=>$vip));
			}
			return $res;
		}
		return false;
	}
	
	/**
	 * 存储用户背包数据
	 * 
	 * @param array $bag 背包数据
	 * @param array $props 道具信息数据，默认为空，为空时，自己去查询
	 * @return mix
	 */
	public function saveUserPropsBag(array $bag,array $props = array()){
		
		if(($uid=$bag['uid']) <= 0 || ($prop_id=$bag['prop_id']) <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		
		$userPropsBagModel = new UserPropsBagModel();
		if(empty($props)){
			$props = PropsModel::model()->findByPk($prop_id);
			if(empty($props))
				return $this->setNotice('props',Yii::t('props','The props does not exist'),0);
			$props = $props->attributes;
		}
		if(!isset($bag['cat_id']) && $props){
			$bag['cat_id'] = $props['cat_id'];
		}	
		
		$_userPropsBagModel = $userPropsBagModel->findByAttributes(array('uid'=>$uid,'prop_id'=>$prop_id));
		if(empty($_userPropsBagModel )){
			
			if(isset($bag['s_num'])){
				$bag['num'] = $bag['s_num'];
				unset($bag['s_num']);
			}

			$this->attachAttribute($userPropsBagModel,$bag);
			$flag = $userPropsBagModel->save();
			$bagId = $userPropsBagModel->getPrimaryKey();
		}else{
			
			//更新背包数量，正数为加，负数为减
			if(isset($bag['s_num'])){ 
				$counter = array('num'=>$bag['s_num']);
				unset($bag['s_num']);
			}

			$this->appendUserBagProps($_userPropsBagModel,$bag);
			$this->attachAttribute($_userPropsBagModel,$bag);
			$flag=$_userPropsBagModel->save();
			$bagId = $_userPropsBagModel->bag_id;
			if(isset($counter)){
				$userPropsBagModel->updateCounters($counter,"bag_id = :bagId",array(':bagId'=>$_userPropsBagModel->bag_id));
			}
		}
		
		if($bagId && $this->isAdminAccessCtl()){
			$this->saveAdminOpLog('存储用户背包数据(bag_id='.$bagId.')');
		}
		return $bagId;
	}
	
	/**
	 * 存储用户使用的道具信息
	 * 
	 * @param array $record
	 * @return array
	 */
	public function saveUserPropsUse(array $record){
		if(($uid=$record['uid']) <= 0 || ($prop_id=$record['prop_id']) <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		
		$userPropsUseModel = new UserPropsUseModel();
		$userPropsUseModel->create_time = time();
		$this->attachAttribute($userPropsUseModel,$record);
		$flag = $userPropsUseModel->save();
		return $userPropsUseModel->getPrimaryKey();
	}
	
	public function getUserPropsUseCount($uid, $propId, $condition)
	{
		if($uid <= 0 || $propId <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$userPropsUseModel = new UserPropsUseModel();
		$res = $userPropsUseModel->getUserPropsUseCount($uid, $propId, $condition);
		return $res;
	}
	
	/**
	 * 根据道具Id获取道具的使用数量
	 * @author leiwei
	 * @param int $uid  用户uid
	 * @param int $catId 道具分类ID
	 * @param array $condition 检索条件
	 * @return int
	 */
	public function getUserPropsUserCountByCatId($uid, $catId, $condition){
		if($uid <= 0 || $catId <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$userPropsUseModel = new UserPropsUseModel();
		$res = $userPropsUseModel->getUserPropsUserCountByCatId($uid, $catId, $condition);
		return $res;
	}
	
	/**
	 * 存储用户购卖道具记录
	 * 
	 * @param array $record　记录信息
	 * @param array $props 道具信息，默认可为空。为空会自行查询
	 * @return int
	 */
	public function saveUserPropsRecords(array $record,array $props = array()){
		if($record['uid'] <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$prop_id = $record['prop_id'] ? $record['prop_id'] : $props['prop_id'];
		if($prop_id <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		if(!$props){
			$propsService = new PropsService();
			$props = $propsService->getPropsByIds($prop_id,true,false);
			$props = $props[$prop_id];
		}

		if(!$record['prop_id']){
			$record['prop_id'] = $props['prop_id'];
		}
		if(empty($props)){
			return $this->setNotice('props',Yii::t('props','The props does not exist'),0);
		}
		if(!isset($props['category'])){
			return $this->setNotice('props',Yii::t('props','The props category does not exist'),0);
		}

		$record['pipiegg'] = isset($record['pipiegg']) ? $record['pipiegg'] : $props['pipiegg'];
		$record['charm'] = isset($record['charm']) ? $record['charm'] : $props['charm'];
		$record['dedication'] = isset($record['dedication']) ? $record['dedication'] : $props['dedication'];
		$record['amount'] = isset($record['amount']) ? $record['amount'] : 1;
		$record['cat_id'] = isset($record['cat_id']) ? $record['cat_id'] : $props['cat_id'];
		$record['vtime'] = isset($record['vtime']) ? $record['vtime'] : 0;
		$record['source'] = isset($record['source']) ? $record['source'] : 0;
		if(!isset($record['info']) && isset($props['category']['name'])){
			$record['info'] = $props['category']['name'].'('.$props['name'].')';
		}
		if(isset($record['time_desc'])){
			$record['info'] .= $record['time_desc'];
			unset($record['time_desc']);
		}
		$record['ctime'] = time();
		$userPropsRecordsModel = new UserPropsRecordsModel();
		$this->attachAttribute($userPropsRecordsModel,$record);
	    $userPropsRecordsModel->save();
	    $flag = $userPropsRecordsModel->getPrimaryKey();
	    if ($flag && $this->isAdminAccessCtl()){
	    	$this->saveAdminOpLog('存储道具记录(prop_id='.$prop_id.')记录ID('.$flag.') 用户UID('.$record['uid'].')',$record['uid']);
	    }
		return $flag;
	}
	
	/**
	 * 获取用户使用的道具
	 * 
	 * @param int $uid 用户ID
	 * @param int $propId 道具ＩＤ
	 * @param int $validTime 道具有效时间， 传递Unix时间缀
	 * @return array
	 */
	public function getUserValidPropsOfUseByPropId($uid,$propId = 0,$validTime = null){
		if($uid <= 0 || ($propId && $propId <= 0)){
			return $this->setError(Yii::t('common','Parameter is empty'),array());
		}
		$userPropsUseModel = new UserPropsUseModel();
		$models = $userPropsUseModel->getUserValidPropsOfUseByPropId($uid,$propId,$validTime);
		return $this->arToArray($models);
	}
	
	/**
	 * 获取用户使用的道具
	 * 
	 * @param int $uid 用户ID
	 * @param int $catId 道具ＩＤ
	 * @param int $validTime 道具有效时间， 传递Unix时间缀
	 * @return array
	 */
	public function getUserValidPropsOfUseByCatId($uid,$catId = 0,$validTime = null){
		if($uid <= 0 || ($catId && $catId <= 0)){
			return $this->setError(Yii::t('common','Parameter is empty'),array());
		}
		$userPropsUseModel = new UserPropsUseModel();
		$models = $userPropsUseModel->getUserValidPropsOfUseByCatId($uid,$catId,$validTime);
		return $this->arToArray($models);
	}
	
	/** 
	 * 取得某分类道具下最后一次被使用情况
	 * 
	 * @param int $uid
	 * @param int $catId
	 * @return array
	 */
	public function getUserLatestPropsOfUsedByCatId($uid,$catId){
		if($uid <= 0 ||  $catId <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),array());
		}
		$userPropsUseModel = new UserPropsUseModel();
		$model = $userPropsUseModel->getUserLatestPropsOfUsedByCatId($uid,$catId);
		if($model){
			return $model->attributes;
		}
		return array();
		
	}
	/**
	 * 更新已使用道具的过期时间
	 * 
	 * @param int $recordId 记录ID
	 * @param int $validTime 过期时间
	 * @return array
	 */
	public function updatePropsUseValidTime($recordId,$validTime){
		if($recordId <= 0 || $validTime <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$userPropsUseModel = new UserPropsUseModel();
		$userPropsUseModel->updateByPk($recordId,array('valid_time'=>$validTime));
	}
	
	/**
	 * 更新道具背包中的有效时间
	 * @param int $bagId   记录Id
	 * @param int $validTime 过期时间
	 * @return mix
	 */
	public function updateUserPropsBagValidTime($bagId,$validTime){
		if($bagId <= 0 || $validTime <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$userPropsBagModel=new UserPropsBagModel();
		return $userPropsBagModel->updateByPk($bagId,array('valid_time'=>$validTime));
	}
	/**
	 * 获取用户购买所有有效的道具
	 * 
	 * @param int $uid 用户ID
	 * @param int $propId 道具ＩＤ
	 * @param int $validTime 道具有效时间， 传递Unix时间缀
	 * @return array
	 */
	public function getUserValidPropsOfBagByPropId($uid,$propId = 0,$validTime = null){
		if($uid <= 0 || ($propId && $propId <= 0)){
			return $this->setError(Yii::t('common','Parameter is empty'),array());
		}
		$userPropsBagModel = new UserPropsBagModel();
		$models = $userPropsBagModel->getUserValidPropsOfBagByPropId($uid,$propId,$validTime);
		$props = $this->arToArray($models);
		return $this->buildUserProps($props);
	}
	
	
	/**
	 * 获取用户购买某分类下所有有效的道具
	 * 
	 * @param int $uid 用户ID
	 * @param int $cateId 道具分类ＩＤ
	 * @param int $validTime 道具有效时间， 传递Unix时间缀
	 * @return array
	 */
	public function getUserValidPropsOfBagByCatId($uid,$cateId = 0,$validTime = null, $selectNum = false){
		if($uid <= 0 || ($cateId && $cateId <= 0)){
			return $this->setError(Yii::t('common','Parameter is empty'),array());
		}
		
		$userPropsBagModel = new UserPropsBagModel();
		$models = $userPropsBagModel->getUserValidPropsOfBagByCatId($uid,$cateId,$validTime, $selectNum);
		$props = $this->arToArray($models);
		return $this->buildUserProps($props);
	}
	
	/**
	 * 获取用户正在使用的道具
	 * @param unknown_type $uid
	 */
	public function getUserPropsAttributeByUid($uid)
	{
		if(empty($uid)){
			return $this->setError(Yii::t('common','Parameter is empty'),array());
		}
		$attributeModel = new UserPropsAttributeModel();
		$attribute = $attributeModel->findByAttributes(array('uid'=>$uid));
		if($attribute){
			return $attribute->attributes;
		}
		return array();
	}
	
	public function getUserPropsRecords($uid, $limit, $offset, $condition = array())
	{
		$propsRecordModel = new UserPropsRecordsModel();
		$data = $propsRecordModel->searchUserPropsRecord($uid, $limit, $offset, $condition);
		$data['list'] = $this->buildUserPropsRecords($data['list']);
		return $data;
	}
	
	/**
	 * @param unknown_type $condition
	 * @param unknown_type $offset
	 * @param unknown_type $pageSize
	 * @return multitype:number multitype: |Ambigous <multitype:, number, string>
	 */
	public function getUserPropsRecordsByCondition(Array $condition = array(),$offset=0, $pageSize=10){
		if(!empty($condition['username']) || !empty($condition['nickname']) || !empty($condition['realname'])){
			$UserService = new UserService();
			$info = $UserService->searchUserList($offset,$pageSize,$condition,false);
			if($info['uids']){
				$condition['uid'] = $info['uids'];
			}else{
				return array('count'=>0,'list'=>array());
			}
		}

		$propsRecordModel = new UserPropsRecordsModel();
		$data = $propsRecordModel->getUserPropsRecordsByCondition($condition,$offset, $pageSize);
		if (isset($data['list'])){
			$data['list'] = $this->arToArray($data['list']);
		}
		$data['list'] = $this->buildUserPropsRecords($data['list']);
		return $data;
	}
	
	public function buildUserPropsRecords(array &$data){
		if(empty($data)){
			return array();
		}
	
		$propsService  = new PropsService();
		$category = $propsService->getPropsCategoryByEnName('vip');
		$cat_id = $category['cat_id'];
		
		foreach($data as $key=>$_data){
			//处理vip计时方式
			if($_data['cat_id']==$cat_id){
				$data[$key]['time_desc']="赠送{$_data['amount']}天";
				$data[$key]['amount']="1";
			}
			else
			{
				$data[$key]['expired'] = 0;
				if($_data['vtime'] == '0'){
					$data[$key]['time_desc'] = '永久';
					$data[$key]['timediff'] = 0;
				}elseif($_data['vtime']){
					$timediff = $_data['vtime'] - time();
					$data[$key]['timediff'] = $timediff;
					if($timediff <= 0 ){
						$data[$key]['time_desc'] = '已过期';
						$data[$key]['expired'] = 1;
					}else{
						$data[$key]['time_desc'] = '还剩'.ceil($timediff / (3600*24)).'天';
					}
				}
			}
		}
		return $data;
	}
	
	public function buildUserProps(array &$data){
		if(empty($data)){
			return array();
		}
		
		$propsService  = new PropsService();
		$category = $propsService->getPropsCategoryByEnName('vip');
		$cat_id = $category['cat_id'];
		
		foreach($data as $key=>$_data){
			$data[$key]['expired'] = 0;
			if($_data['valid_time'] == '0'){
				$data[$key]['time_desc'] = '永久';
				$data[$key]['timediff'] = 0;
			}elseif($_data['valid_time']){
				//处理vip计时方式
				if($_data['cat_id']!=$cat_id)
				{
					$timediff = $_data['valid_time'] - time();
					$data[$key]['timediff'] = $timediff;
					if($timediff <= 0 ){
						$data[$key]['time_desc'] = '已过期';
						$data[$key]['expired'] = 1;
					}else{
						$data[$key]['time_desc'] = '还剩'.ceil($timediff / (3600*24)).'天';
					}
				}
				else
				{
					$currentTime=time();
					if($_data['use_status']==0)
					{
						$timediff = $_data['valid_time'] - $currentTime;
						$data[$key]['timediff'] = $timediff;
						$remainDays=$propsService->getVipTimingDays($currentTime, $_data['valid_time']);
						if($remainDays <= 0){
							$data[$key]['time_desc'] = '已过期';
							$data[$key]['expired'] = 1;
						}else{
							$data[$key]['time_desc'] = '还剩'.$remainDays.'天';
						}
					}
					else
					{
						$timediff = $_data['valid_time'] - time();
						$data[$key]['timediff'] = $timediff;
						$remainDays1=$_data['num']-$propsService->getVipUsedDays($_data['uid'],$_data['prop_id']);
						if($remainDays1 <= 0){
							$data[$key]['time_desc'] = '已过期';
							$data[$key]['expired'] = 1;
						}else{
							$data[$key]['time_desc'] = '还剩'.$remainDays1.'天';
						}

					}
				}
			}
		}
		return $data;
	}

	/**
	 * @author supeng
	 * @param string $item
	 * @return array
	 */
	public function getSourceTypeList($item = null){
		$list = array(
			PROPSRECORDS_SOURCE_BUY=>'正常购买',
			PROPSRECORDS_SOURCE_ADMIN=>'后台赠送',
			PROPSRECORDS_SOURCE_ACTIVITY=>'活动领取',
			PROPSRECORDS_SOURCE_USEPROPS=>'道具使用',
			PROPSRECORDS_SOURCE_GAME => '游戏中奖',
			PROPSRECORDS_SOURCE_AWARD => '中奖赠品'
		);
		return is_null($item) ? $list : $list[$item];
	}
	
	public function appendUserBagProps(UserPropsBagModel  $model ,array &$newArray){
		if(empty($model) || empty($newArray)){
			return $newArray;
		}
		$timeStamp = time();
		if(isset($newArray['valid_time'])){
			$orgValidateTime = $model->valid_time;
			if($orgValidateTime == 0){
				$newArray['valid_time'] = 0;//永久价道具不做更新
			}else if($newArray['valid_time']>0 && $orgValidateTime > $timeStamp){
				//如果以前购买的道具不是永久价，但还未过期，则在上面叠加
				$newArray['valid_time'] = $orgValidateTime+($newArray['valid_time']-$timeStamp);
			}
		}
		if(isset($newArray['num'])){
			if($newArray['num']>0 && $model->num>=$newArray['num']){
				$newArray['num'] = $model->num - $newArray['num'];
			}
			
		}
	}
	
	
	/**
	 * 获取最后一次发送飞屏的时间戳
	 * @return number
	 * @auther leiwei
	 */
	public function getLastFlyscreenTime(){
		$otherRedisModel=new OtherRedisModel();
		return $otherRedisModel->getLastFlyscreenTime();
	}
	
	/**
	 * 存储最后一次发送飞屏的时间戳
	 * @return int
	 * @auther leiwei
	 */
	public function saveLastFlyscreenTime(){
		$otherRedisModel=new OtherRedisModel();
		return $otherRedisModel->saveLastFlyscreenTime();
	}
	
	/**
	 * @param unknown_type $uid
	 * @param array $user
	 * @return mix|boolean
	 */
	public function sendPropsZmq($uid,$cat_name,array $info){
		$jsonInfo = array();
		
		$userJsonService = new UserJsonInfoService();
		$zmq = $this->getZmq();
		if (strtolower($cat_name) == 'monthcard'){
			new UserGiftService();
			$jsonInfo['mc'] = array(
					'num' => $info['num']*CHECKIN_GIFT_MONTHCARD_NUM,
					'vt' => $info['valid_time'] == 0?0:$info['valid_time'],
					'img' => $info['image']?$info['image']:null,
				);
		}
		
		if (strtolower($cat_name) == 'car'){
			new UserGiftService();
			$jsonInfo['car'] = array(
				'n' => $info['name'],
				'vt' => $info['valid_time'] == 0?0:$info['valid_time'],
				'f' => $info['flash']?$info['flash']:null,
				'to' => $info['timeout']?$info['timeout']:null,
			);
		}
		
		if (strtolower($cat_name) == 'vip'){
			new UserGiftService();
			$jsonInfo['vip'] = array(
				't' => $info['type'],
				'h' => $info['hide'],
				'img' => $info['image'],
				'vt' => $info['valid_time'] == 0?0:$info['valid_time'],
			);
		}
		
		if($jsonInfo){
			$userJsonService->setUserInfo($uid, $jsonInfo);
			$zmq->sendZmqMsg(609,array('type'=>'update_json','uid'=>$uid,'json_info'=>$jsonInfo));
		}
		return true;
	}
}

?>