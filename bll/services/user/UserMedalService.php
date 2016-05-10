<?php
define('MEDAL_TYPE_USER',0);#用户勋章
define('MEDAL_TYPE_DOTEY',1);#主播勋章
define('MEDAL_TYPE_ACTIVITY',2);#活动勋章

define('MEDALAWARD_TYPE_SYS',0);#系统颁发
define('MEDALAWARD_TYPE_GENERAL',1);#正常获取，普通获得
define('MEDALAWARD_TYPE_HAPPYSATURDAY',2);#正常获取，普通获得

define('MEDAL_ICON_PATH',ROOT_PATH."images".DIR_SEP.'medal'.DIR_SEP);
/**
 * 用户勋章服务层
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z suqian $ 
 * @package service
 * @subpackage user
 */
class UserMedalService extends PipiService {
	
	/**
	 * 用得用户拥有的所有有效的勋章
	 * 
	 * @param int|array $uids 用户ID
	 * @param int $vtime  有效期 默认为当前时间
	 * @return array
	 */
	public function getAllMedalsByUids($uids,$vtime = null,$isFormat = true){
		if(!$uids){
			return $this->setError(Yii::t('common','Parameter is empty'),array());
		}
		$userMedalDal = UserMedalModel::model();
		$medals = $userMedalDal->getAllMedalsByUids($uids,$vtime);
		if($isFormat){
			return $this->buildData($medals);
		}
		return $medals;
	}
	
	/**
	 * 用得用户拥有的所有有效的勋章
	 * 
	 * @param int|array $uids 用户ID
	 * @param string $type 勋章类型
	 * @param int $mid 勋章ID
	 * @return array
	 */
 	public function getUserMedalByUid($uid,$type = null,$mid= null){
		if(!$uid){
			return $this->setError(Yii::t('common','Parameter is empty'),array());
		}
		$userMedalModel = UserMedalModel::model();
		$ar = $userMedalModel->getUserMedalByUid($uid,$type,$mid);
		return $this->arToArray($ar);
 	}
 	
	public function buildData(array &$medalData){
		foreach($medalData as $key=>$data){
			if(isset($data['ctime'])){
				$medalData[$key]['ctime'] = date('Y-m-d H:i',$data['vtime']);
			}
			
			if(isset($data['vtime'])){
				if($data['vtime'] == 0 || $data['vtime'] == '0'){
					$medalData[$key]['vtime'] = '永远有效';
				}else{
					$medalData[$key]['vtime'] = date('Y-m-d H:i',$data['vtime']);
				}
				
			}
			if(isset($data['type'])){
				$medalData[$key]['type_desc'] = $this->getGrantTypeList($data['type']);
			}
		}
		return $medalData;
	}
	
	/**
	 * 获取勋章列表
	 *
	 * @author supeng
	 */
	public function getMedalList($condition = array()){
		$medalModel = new MedalListModel();
		return $this->arToArray($medalModel->getMedalList($condition));
	}
	
	/**
	 * 获取勋章类型
	 *
	 * @author supeng
	 */
	public function getMedalType(){
		return array(
			MEDAL_TYPE_USER => '用户勋章',
			MEDAL_TYPE_DOTEY => '主播勋章',
			MEDAL_TYPE_ACTIVITY => '活动勋章'
		);
	}
	
	/**
	 * 获取勋章授权类型
	 *
	 * @author supeng
	 * @return multitype:string
	 */
	public function getGrantTypeList($type = null){
		$typeStatus = array(
			MEDALAWARD_TYPE_SYS => '系统颁发',
			MEDALAWARD_TYPE_GENERAL => '正常获取',
			MEDALAWARD_TYPE_HAPPYSATURDAY => '快乐星期六'
		);
		return is_null($type) ? $typeStatus : $typeStatus[$type];
	}
	
	/**
	 * 获取勋章图片
	 *
	 * @author supeng
	 * @param unknown_type $iconName
	 * @return string
	 */
	public function getMedalIcon($iconName){
		return $this->getUploadUrl().'medal/'.$iconName;
	}
	
	/**
	 * 保存或修改勋章 
	 * 	如果修改了勋章图片则发zmq消息
	 * 
	 * @author supeng
	 * @param unknown_type $data
	 * @return mix|mixed|multitype:
	 */
	public function saveMedal($data){
		if (empty($data)){
			return $this->setError(Yii::t('common', 'Parameter is not empty'));
		}
		
		$medalModel = new MedalListModel();
		if(isset($data['mid'])){
			$orgMedalModel = $medalModel->findByPk($data['mid']);
			if(empty($orgMedalModel)){
				return $this->setNotice('mid',Yii::t('medal','The mid does not exist'),false);
			}
			$orgData = $orgMedalModel->attributes;
			$data = array_merge($orgData,$data);
			$this->attachAttribute($orgMedalModel,$data);
			if(!$orgMedalModel->validate()){
				return $this->setNotices($orgMedalModel->getErrors(),array());
			}
			$orgMedalModel->save();
			$medal = $orgMedalModel->attributes;
		}else{
			$this->attachAttribute($medalModel,$data);
			$medalModel->ctime = time();
			if(!$medalModel->validate()){
				return $this->setNotices($medalModel->getErrors(),array());
			}
			$medalModel->save();
			$medal = $medalModel->attributes;
		}
		
		//是否修改了勋章图片
		if ($data['icon'] && $medal){
			$mid = $medal['mid'];
			$vtime = time();
			$info = $this->getUserMedalByCondition(array('mid'=>$mid,'vtime'=>$vtime));
			if($info['list']){
				foreach ($info['list'] as $v){
					$this->sendZmqForUserMedal($v['uid']);
				}
			}
			if($this->isAdminAccessCtl()){
				if(isset($data['mid'])){
					$op_desc = "编辑 勋章(MID=".$medal['mid'].")";
				}else{
					$op_desc = "新增 勋章(MID=".$medal['mid'].")";
				}
				$this->saveAdminOpLog($op_desc);
			}
			return $medal;
		}
		return false;
	}
	/**
	 * 更新用户勋章
	 * 
	 * @param array $medal
	 * @return int
	 */
	public function updateUserMedal(array $medal){
		if(!isset($medal['uid']) || !isset($medal['mid']) || !isset($medal['type'])){
			return $this->setError(Yii::t('common', 'Parameter is not empty'),0);
		}
		$userMedalModel = new UserMedalModel();
		$orgUserMedalModel = $userMedalModel->findByAttributes(array('uid'=>$medal['uid'],'mid'=>$medal['mid'],'type'=>$medal['type']));
		if($orgUserMedalModel){
			$this->attachAttribute($orgUserMedalModel,$medal);
			if(!$orgUserMedalModel->validate()){
				return $this->setNotices($orgUserMedalModel->getErrors(),0);
			}
			$orgUserMedalModel->save();
			$pk = $orgUserMedalModel->getPrimaryKey();
		}else{
			$this->attachAttribute($userMedalModel,$medal);
			if(!$userMedalModel->validate()){
				return $this->setNotices($userMedalModel->getErrors(),0);
			}
			$userMedalModel->save();
			$pk = $userMedalModel->getPrimaryKey();
		}
		
		if($pk){
			$this->sendZmqForUserMedal($medal['uid']);
		}
		return $pk;
	}
	/**
	 * 保存或修改用户勋章 发ZMQ消息
	 *
	 * @author supeng
	 * @param array $data
	 * @param array $archivesIds 用于限定勋章在特有直播间显示
	 * @return mix|mixed|multitype:
	 */
	public function saveUserMedal($data,$archivesIds=array()){
		if (empty($data) || !isset($data['mid'])){
			return $this->setError(Yii::t('common', 'Parameter is not empty'));
		}
	
		$medalModel = new UserMedalModel();
		if(isset($data['rid'])){
			$orgMedalModel = $medalModel->findByPk($data['rid']);
			if(empty($orgMedalModel)){
				return $this->setNotice('rid',Yii::t('medal','The rid does not exist'),false);
			}
				
			$this->attachAttribute($orgMedalModel,$data);
			if(!$orgMedalModel->validate()){
				return $this->setNotices($orgMedalModel->getErrors(),array());
			}
			$orgMedalModel->save();
			$medal = $orgMedalModel->attributes;
		}else{
			$info = $medalModel->getUserMedalByCondition($data);
			if(count($info['list']) == 0){
				$this->attachAttribute($medalModel,$data);
				$medalModel->ctime = isset($data['ctime'])?$data['ctime']:time();
				if(!$medalModel->validate()){
					return $this->setNotices($medalModel->getErrors(),array());
				}
				$medalModel->save();
				$medal = $medalModel->attributes;
			}else{
				return $this->setNotices(array('primary key'=>Yii::t('medal', 'alerady exists records')),false);
			}
		}
		
		if ($medal){
			$this->sendZmqForUserMedal($medal['uid'],$medal['mid'],$archivesIds);
			if($this->isAdminAccessCtl()){
				if(isset($data['rid'])){
					$op_desc = '编辑 用户[UID='.$medal['uid'].']勋章 记录(rid='.$medal['rid'].')';
				}else{
					$op_desc = '添加 用户[UID='.$medal['uid'].']勋章 记录(rid='.$medal['rid'].')';
				}
				$this->saveAdminOpLog($op_desc,$medal['uid']);
			}
			return $medal;
		}
		return false;
	}
	
	/**
	 * 删除勋章 并删除所有关联记录 且发ZMQ消息通知
	 * 
	 * @author supeng
	 * @param unknown_type $mid
	 * @return mix|boolean
	 */
	public function delMedal($mid){
		if (empty($mid) || !intval($mid)){
			return $this->setError(Yii::t('common', 'Parameter is not empty'));
		}
		
		$medalModel = new MedalListModel();
		if($minfo = array_shift($this->getMedalList(array('mid'=>$mid)))){
			$icon = $minfo['icon'];	#勋章图标
			$info = $this->getUserMedalByCondition(array('mid'=>$mid,'group'=>'uid'),null,null,false);
			if($info['list']){
				foreach($info['list'] as $md){
					if($this->delUserMedal($md['rid'])){
						$this->sendZmqForUserMedal($md['uid']);
					}
				}
			}
			
			if($medalModel->deleteByPk($mid)){
				if($this->isAdminAccessCtl()){
					$this->saveAdminOpLog('删除 勋章(MID='.$mid.')');
				}
				@unlink(MEDAL_ICON_PATH.$icon);
				return true;
			}
		}
		return false;
	}
	
	/**
	 * 删除用户勋章 发ZMQ消息通知
	 *
	 * @author supeng
	 * @param int $rid
	 * @return mix|boolean
	 */
	public function delUserMedal($rid){
		if (empty($rid) || !intval($rid)){
			return $this->setError(Yii::t('common', 'Parameter is not empty'));
		}
		$zmq = $this->getZmq(); #ZMQ实例
		$userJsonInfoService = new UserJsonInfoService();	#用户信息接口服务层
		$medalModel = new UserMedalModel();
		
		$info = $medalModel->findByPk($rid)->attributes;
		if($info){
			$uid = $info['uid'];
			if($medalModel->deleteByPk($rid)){
				$this->sendZmqForUserMedal($uid);
				if($this->isAdminAccessCtl()){
					$this->saveAdminOpLog('删除 用户[UID='.$uid.']勋章记录(rid='.$rid.')',$uid);
				}
				return true;
			}
		}
		return false;
	}
	
	/**
	 * 上传勋章图片
	 *
	 * @author supeng
	 * @param unknown_type $formName
	 * @return unknown|boolean
	 */
	public function uploadMedalIcon($formName){
		$imgFiles = CUploadedFile::getInstancesByName($formName);
		if($imgFiles){
			foreach ($imgFiles as $imgFile){
				$filename = $imgFile->getName();
				if($filename){
					$extName = $imgFile->getExtensionName();
					$uploadDir = ROOT_PATH."images".DIR_SEP.'medal'.DIR_SEP;
					if (!file_exists($uploadDir)){
						mkdir($uploadDir,0777,true);
					}
					$uploadfile = MEDAL_ICON_PATH.$filename;
					if($imgFile->saveAs($uploadfile,true)){
						return $filename;
					}
				}else{
					return false;
				}
			}
		}
		return false;
	}
	
	/**
	 * 用户勋章列表
	 * 
	 * @author supeng
	 * @param array $condition
	 * @param unknown_type $offset
	 * @param unknown_type $pageSize
	 * @return multitype:number multitype: |Ambigous <multitype:multitype:, multitype:multitype: number mixed >
	 */
	public function getUserMedalByCondition(Array $condition,$offset = 0,$pageSize = 10,$isLimit = true){
		$userMedal = new UserMedalModel();
		
		if(!empty($condition['username']) || !empty($condition['nickname']) || !empty($condition['realname'])){
			$userSer = new UserService();
			$info = $userSer->searchUserList(null,null,$condition,false);
			if($info['uids']){
				$condition['uid'] = $info['uids'];
			}else{
				return array('count'=>0,'list'=>array());
			}
		}
		$records = $userMedal->getUserMedalByCondition($condition,$offset,$pageSize,$isLimit);
		return $records;
		
	}
	
	/**
	 * @author supeng
	 * @param int $uid
	 * @param int $mid 当mid不为空时配置某一勋章在某个档期内专有显示
	 * @param array $archivesIds 当档期ID不为空时这个勋章只在该档期的直播间显示
	 * @return boolean
	 */
	public function sendZmqForUserMedal($uid,$mid=0,$archivesIds=array()){
		$allMedal = $this->getAllMedalsByUids(array($uid),null,false);
		if ($allMedal){
			$zmq = $this->getZmq();
			$userJsonInfoService = new UserJsonInfoService();
			$userJson['md'] = array();
			foreach($allMedal as $v){
				if ($v['vtime'] >= time()){
					$_tmp = array();
					$_tmp['img'] = '/medal/'.$v['icon'];
					$_tmp['vt'] = $v['vtime'];
					if ($mid > 0 && count($archivesIds) > 0  && $v['mid'] == $mid){
						$_tmp['aid'] = $archivesIds;
					}
					$userJson['md'][] = $_tmp;
				}
			}
			
			if (!$userJson['md']){
				$userJson['md'] = array();
			}
			if($userJsonInfoService->setUserInfo($uid,$userJson)){
				$zmq->sendZmqMsg(609,array('type'=>'update_json','uid'=>$uid,'json_info'=>$userJson));
			}
			return true;
		}
		return false;
	}
}

?>