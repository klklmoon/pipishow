<?php
/**
 * 家族管理
 * 
 * @author supeng
 */
class FamilyController extends PipiAdminController {
	
	/**
	 * @var FamilyService
	 */
	protected $famService;
	
	/**
	 * @var UserService
	 */
	protected $userService;
	
	/**
	 * @var array
	 */
	protected $allowOp = array('addSetup','updateList','updateListDo','disbandDo','editApply','checkUserInfo','transFamily','changeSign','dlLive','dlIncome','familyUpgrade', 'toNormal');
	
	/**
	 * @var string 当前操作
	 */
	protected $op;
	
	/**
	 * @var boolean 是否是Ajax请求
	 */
	protected $isAjax;
	
	protected $pageSize = 20;
	
	protected $offset;
	
	/**
	 * @var int page lable
	 */
	protected $p;
	
	public function init(){
		parent::init();
		$this->famService = new FamilyService();
		$this->userService = new UserService();
		$this->op = Yii::app()->request->getParam('op');
		$this->isAjax = Yii::app()->request->isAjaxRequest;
		if(!($this->p = Yii::app()->request->getParam('page'))){
			$this->p = 1;
		}
		$this->offset = ($this->p -1)*$this->pageSize;
	}
	
	/**
	 * 家族申请
	 */
	public function actionApply(){
		if($this->op == 'updateList' && in_array($this->op, $this->allowOp)){
			$this->_updateList();
		}
		
		if($this->op == 'updateListDo' && in_array($this->op, $this->allowOp)){
			$this->_updateListDo();
		}
		
		if($this->op == 'disbandDo' && in_array($this->op, $this->allowOp)){
			$this->_disbandDo();
		}
		
		if($this->op == 'editApply' && in_array($this->op, $this->allowOp)){
			$this->_editApply();
		}
		
		if($this->op == 'checkUserInfo' && in_array($this->op, $this->allowOp)){
			$this->_checkUserInfo();
		}
		
		if($this->op == 'transFamily' && in_array($this->op, $this->allowOp)){
			$this->_transFamily();
		}
		
		if($this->op == 'familyUpgrade' && in_array($this->op, $this->allowOp)){
			$this->_familyUpgrade();
		}
		
		$this->assetsMy97Date();
		$condition = $this->_getSearchCondition();
		$list = $this->famService->searchFamily($condition,$this->offset,$this->pageSize);
		
		$uinfos = array();
		if ($list['list']){
			$udis = array();
			$familyIds = array();
			foreach ($list['list'] as $v){
				$uids[$v['uid']] = $v['uid'];
				$familyIds[$v['id']] = $v['id'];
			}
			if ($uids){
				$uinfos = $this->userService->getUserBasicByUids($uids);
			}
		}
		
		$pager = new CPagination($list['count']);
		$pager->pageSize = $this->pageSize;
		$pager->params = $condition;
		$this->render('family_apply',array('condition'=>$condition,'pager'=>$pager,'uinfos'=>$uinfos,'list'=>$list));
	}
	
	/**
	 * 家族设置
	 */
	public function actionSetting(){
		if($this->op == 'addSetup'){
			if (in_array($this->op, $this->allowOp)){
				$global_enable = Yii::app()->request->getParam('global_enable',true);
				$apply_enable = Yii::app()->request->getParam('apply_enable',true);
				$urank = Yii::app()->request->getParam('urank',5);
				$drank = Yii::app()->request->getParam('drank',10);
				$price = Yii::app()->request->getParam('create_price',10);
				$medal_price = Yii::app()->request->getParam('medal_price',2);
				$update_medal_price = Yii::app()->request->getParam('update_medal_price',200);
				$focus_quit = Yii::app()->request->getParam('focus_quit', 60);
				
				if (!is_numeric($update_medal_price) || !is_numeric($medal_price) || !is_numeric($price)){
					exit('更新失败，参数有误');
				}
				
				$c_value = array();
				$c_value['global_enable'] = $global_enable;
				$c_value['apply_enable'] = $apply_enable;
				$c_value['urank'] = $urank;
				$c_value['drank'] = $drank;
				$c_value['create_price'] = $price;
				$c_value['medal_price'] = $medal_price;
				$c_value['update_medal_price'] = $update_medal_price;
				$c_value['focus_quit'] = $focus_quit;
				if(FamilyService::saveSetting($c_value)){
					exit('更新成功');
				}else{
					exit('更新失败');
				}
			}else{
				exit('更新失败');
			}
		}
		
		$service = new ConsumeService();
		$setInfo = FamilyService::getSetting();
		$rank = $service->getUserRankFromRedis();
		$drank = $service->getDoteyRankFromRedis();
		$_rank = array();
		foreach($rank as $k=>$v){
			$_rank[$k] = $v['name'];
		}
		ksort($_rank);
		
		$_drank = array();
		foreach($drank as $k=>$v){
			$_drank[$k] = $v['name'];
		}
		ksort($_drank);
		
		$this->render('family_set',array('rank'=>$_rank,'drank'=>$_drank,'setInfo'=>$setInfo));
	}
	
	/**
	 * 签约家族
	 */
	public function actionContracted(){
		if($this->op == 'changeSign' && in_array($this->op, $this->allowOp)){
			$this->_changeSign();
		}
		if($this->op == 'toNormal' && in_array($this->op, $this->allowOp)){
			$this->_toNormal();
		}
		
		$this->assetsMy97Date();
		$condition = $this->_getSearchCondition();
		$data = $this->famService->searchSignList($condition,$this->offset,$this->pageSize);
		
		$pager = new CPagination($data['count']);
		$pager->pageSize = $this->pageSize;
		$pager->params = $condition;
		
		$famInfos= array();
		$uinfos = array();
		if ($data['list']){
			foreach($data['list'] as $v){
				$famInfos[$v['family_id']] = $this->famService->getFamily($v['family_id']);
			}
			if($famInfos){
				$uids = array();
				foreach($famInfos as $v){
					$uids[$v['uid']] = $v['uid'];
				}
				if($uids){
					$uinfos = $this->userService->getUserBasicByUids($uids);
				}
			}
		}
		
		$this->render('family_contracted',array('condition'=>$condition,'pager'=>$pager,'list'=>$data,'famInfos'=>$famInfos,'uinfos'=>$uinfos));
	}
	
	/**
	 * 操作理由 
	 */
	public function actionOprecords(){
		$this->assetsMy97Date();
		$condition = $this->_getSearchCondition();
		$data = $this->famService->searchOPRecords($condition,$this->offset,$this->pageSize);
		
		$pager = new CPagination($data['count']);
		$pager->pageSize = $this->pageSize;
		$pager->params = $condition;
		
		$uinfos = array();
		if ($data['list']){
			$uids = array();
			foreach($data['list'] as $v){
				$uids[$v['uid']] = $v['uid'];
				$uids[$v['op_uid']] = $v['op_uid'];
			}
			if($uids){
				$uinfos = $this->userService->getUserBasicByUids($uids);
			}
		}
		
		$this->render('family_oprecords',array('condition'=>$condition,'pager'=>$pager,'list'=>$data,'uinfos'=>$uinfos));
	}
	
	/**
	 * 家族开播情况 
	 */
	public function actionLive(){
		$familyId = Yii::app()->request->getParam('familyId',false);
		$count = 0;
		$list = array();
		$condition = array();
		$condition['live_time_on'] = date('Y-m',time());
		$condition['familyId'] = $familyId;
		if($familyId){
			$famInfos = $this->famService->getDoteyMembersByFamily($familyId);
			if($famInfos){
				$isLimit = true;
				if($this->op == 'dlLive' && in_array($this->op, $this->allowOp)){
					$isLimit = false;
				}
				
				$uids = array_keys($famInfos);
				$archives = new ArchivesService();
				
				$condition['group'] = 'a.archives_id';
				$condition['uids'] = $uids;
				$result = $archives->searchLiveRecordByCondition($condition,$this->offset,$this->pageSize,$isLimit);
				$count = $result['count'];
				$list = array();
				if($count){
					//主播信息和档期结果集
					$archivesIds = array();
					$uids = array();
					$doteyInfo = $this->_getDoteyInfo($result['list'],$uids,$archivesIds);
					$effectDayUnit = $archives->getLiveEffectDaysUnit($uids);
					//组装数据
					if ($result['list']){
						foreach ($result['list'] as $v){
							$list[$v['archives_id']]['uid'] = $v['uid'];
							$list[$v['archives_id']]['title'] = $v['title'];
							$list[$v['archives_id']]['archives_id'] = $v['archives_id'];
							$list[$v['archives_id']]['nickname'] = isset($doteyInfo[$v['uid']])?$doteyInfo[$v['uid']]['nickname']:'';
							$list[$v['archives_id']]['has_days_unit'] = isset($effectDayUnit[$v['uid']])?$effectDayUnit[$v['uid']]:2;
							$list[$v['archives_id']]['has_days'] = array();
							$list[$v['archives_id']]['has_hours'] = 0;
							$list[$v['archives_id']]['detail'] = array();
						}
					}
					$detailResult = $archives->searchLiveRecordByArchivesIds($archivesIds,$condition);
					if ($detailResult){
						foreach($detailResult as $v){
							$k =ltrim(substr($v['end_time'], -2),'0');
							if (!isset($list[$v['archives_id']]['detail'][$k])){
								$list[$v['archives_id']]['detail'][$k] = $v['duration'];
							}else{
								$list[$v['archives_id']]['detail'][$k] += $v['duration'];
							}
				
							$list[$v['archives_id']]['has_hours'] += $v['duration'];
							if ($list[$v['archives_id']]['has_days_unit']*3600 <= $v['duration']){
								if (!isset($list[$v['archives_id']]['has_days'][$v['end_time']])){
									$list[$v['archives_id']]['has_days'][$v['end_time']] = $v['duration'];
								}else{
									$list[$v['archives_id']]['has_days'][$v['end_time']] += $v['duration'];
								}
							}
						}
					}
				}
				
				//下载Excel
				if($this->op == 'dlLive' && in_array($this->op, $this->allowOp)){
					$this->_dlLive($familyId,$list,$condition['live_time_on']);
				}
				
				if (isset($condition['uids'])){
					unset($condition['uids']);
				}
			}
			
		}
		
		$pager = new CPagination($count);
		$pager->pageSize = $this->pageSize;
		$pager->params = $condition;
		
		$this->render('family_onlive',array('condition'=>$condition,'list'=>$list,'pager'=>$pager));
	}
	
	/**
	 * 家族主播收入
	 */
	public function actionIncome(){
		$familyId = Yii::app()->request->getParam('familyId',false);
		$month = Yii::app()->request->getParam('month', date('Y-m'));
		
		$list = array();
		$condition = array();
		$condition['monthTime'] = $month;
		$filter_uids = $this->getFilterUids();
		$dateCal = new PipiDateCal();
		$monthList = $dateCal->getCurrentYearPrevMonth(true);
		list($startTime,$endTime) = $dateCal->getCurPointMonthTime($month);
		
		$family = $data = $tmp = array();
		$message = '';
		if ($familyId){
			$family = $this->famService->getFamily($familyId);
			if(empty($family) || $family['sign'] == 0){
				$message = '家族不存在或不是签约家族';
			}else{
				$message = '家族名称：'.$family['name'];
				$service = new FamilyStaticsService();
				$data['list1'] = $service->staticsFamiliyIncomeById($familyId, $month, FAMILY_ROLE_DOTEY, $this->p, 100000, $tmp, $filter_uids);
				$data['list2'] = $service->staticsFamiliyForceIncome($familyId, $this->p, 100000, $tmp, $month, $filter_uids);
				foreach($data['list2'] as &$d){
					$d['nickname'] = '[强退]'.$d['nickname'];
				}
				$data['list'] = array_merge($data['list1'], $data['list2']);
				$forceQuitUids = array_keys($data['list2']);
// 				$pages=new CPagination($data['count']);
// 				$pages->pageSize = $this->pageSize;
// 				$data['pages'] = $pages;
				
				$consumeSer = new ConsumeService();
				$doteyRanks = $consumeSer->getDoteyRankFromRedis();
				$uids = array_keys($data['list']);
				$exchangeInfos = $consumeSer->countExchangeRecordByUids($uids, strtotime($startTime), strtotime($endTime), EXCHANGE_MONEY);#当月兑换皮蛋统计
				foreach($data['list'] as &$l){
					$l['rank'] = isset($doteyRanks[$l['dk']])?$doteyRanks[$l['dk']]['name']:'';
					$l['exchange'] = isset($exchangeInfos[$l['uid']])?$exchangeInfos[$l['uid']]['money']:0;
				}
			}
			
			//下载Excel
			if($this->op == 'dlIncome' && in_array($this->op, $this->allowOp)){
				$this->_dlIncome($familyId,$data['list'],$month);
			}
			$condition['familyId'] = $familyId;
		}
		$this->render('family_income',array('condition'=>$condition,'data'=>$data, 'family'=>$family, 'monthList'=>$monthList,'month'=>$month, 'message'=>$message));
	}
	
	/**
	 * 过滤陪玩账户
	 * @return multitype:
	 */
	private function getFilterUids(){
		$upFile = CUploadedFile::getInstanceByName('filter_uids');
		if($upFile){
			if(strtolower($upFile->getExtensionName()) == 'txt'){
				$tmpName = $upFile->getTempName();
				if($tmpName){
					return file($tmpName);
				}
			}
		}
		return array();
	}
	
	/**
	 * 获取主播信息
	 *
	 * @param array $list
	 * @param unknown_type $uids
	 * @param unknown_type $archivesIds
	 * @return Ambigous <multitype:, mix, multitype:NULL >
	 */
	private function _getDoteyInfo(Array $list,&$uids = array(),&$archivesIds = array(),$isUserInfo = true){
		if ($list) {
			foreach ($list as $v){
				if (isset($v['uid'])){
					if(!in_array($v['uid'], $uids)){
						$uids[] = $v['uid'];
					}
					if (isset($v['archives_id'])){
						if(!in_array($v['archives_id'], $archivesIds)){
							$archivesIds[$v['uid']] = $v['archives_id'];
						}
					}
				}
			}
				
			if($uids && $isUserInfo){
				return $this->userService->getUserBasicByUids($uids);
			}
		}
		return array();
	}
	
	private function _updateList(){
		if (!$this->isAjax){
			exit('不是合法请求');
		}
		
		$type = Yii::app()->request->getParam('type');
		$familyId = Yii::app()->request->getParam('familyId');
		if(is_numeric($familyId) && in_array($type, array('opHidden','opForbidden','opStatus','opDisband','opTrans','opLevel'))){
			$famInfo = $this->famService->getFamily($familyId);
			if($famInfo){
				exit($this->renderPartial('_apply_'.$type,array('info'=>$famInfo)));
			}else{
				exit('不存在该家族信息');
			}
		}else{
			exit('参数有误');
		}
		exit;
	}
	
	private function _updateListDo(){
		if (!$this->isAjax){
			exit('不是合法请求');
		}
	
		$type = Yii::app()->request->getParam('type');
		$familyId = Yii::app()->request->getParam('familyId');
		$reason = Yii::app()->request->getParam('reason');
		$value = Yii::app()->request->getParam('value');
		if(is_numeric($familyId) && in_array($type, array(0,1,2,3,4,5,6)) && $reason){
			$famInfo = $this->famService->getFamily($familyId);
			if($famInfo){
				$func = '';
				if (in_array($type, array(2,3))){
					$func = 'hiddenFamily';
				}
				
				if (in_array($type, array(0,1))){
					$func = 'changeFamilyStatus';
				}
				
				if (in_array($type, array(4,5))){
					$func = 'forbiddenFamily';
				}
				
				if ($func){
					if($this->famService->$func($famInfo['id'],$value,$reason)){
						exit('1');
					}
				}
				
			}else{
				exit('不存在该家族信息');
			}
		}else{
			exit('参数有误');
		}
		exit('编辑失败');
	}
	
	private function _disbandDo(){
		if (!$this->isAjax){
			exit('不是合法请求');
		}
	
		$familyId = Yii::app()->request->getParam('familyId');
		$reason = Yii::app()->request->getParam('reason');
		if(is_numeric($familyId) && $reason){
			$famInfo = $this->famService->getFamily($familyId);
			if($famInfo){
				if($this->famService->dissolution($famInfo['id'], $this->op_uid, $reason)){
					exit('1');
				}
			}else{
				exit('不存在该家族信息,无法解散');
			}
		}else{
			exit('参数有误');
		}
		exit('解散家族失败');
	}
	
	private function _editApply(){
		$familyId = Yii::app()->request->getParam('familyId');
		$isEdit = Yii::app()->request->getParam('isEdit',false);
		$famInfo = $this->famService->getFamily($familyId);
		$famEInfo = $this->famService->getFamilyExtend($familyId);
		if($famEInfo){
			$famEInfo['config'] = json_decode($famEInfo['config'],true);
		}
		
		if(!$famInfo && $famEInfo){
			exit('参数有误');
		}
		$uid = $famInfo['uid'];
		if($familyId && !$isEdit){
			$uInfo = $this->userService->getUserBasicByUids(array($uid));
			$uInfo = $uInfo[$uid];
			$UEInfo = $this->userService->getUserExtendByUids(array($uid));
			$UEInfo = $UEInfo?$UEInfo[$uid]:array();
			//$famEInfo = $this->famService->getFamilyExtend($familyId);
			exit($this->renderPartial('_apply_edit',array('info'=>$famInfo,'einfo'=>$famEInfo,'uinfo'=>$uInfo,'UEInfo'=>$UEInfo)));
		}else{
			$apply = Yii::app()->request->getParam('apply');
			$eapply = Yii::app()->request->getParam('eapply');
			$user = Yii::app()->request->getParam('user');
			$euser = Yii::app()->request->getParam('euser');
			
			//家族基本信息
			$cover = $this->famService->uploadFamilyCover('cover');#上传封面图
			$apply['id'] = $familyId;
			if($cover){
				$apply['cover'] = $cover;
			}
			$this->famService->saveFamily($apply);
			
			$famEInfo['config']['scale'] = $eapply['scale'];
			$famEInfo['config'] = $famEInfo['config'];
			//家族扩展信息
			$extend = array(
				'family_id'	=> $familyId,
				'announcement' => $eapply['announcement'],
				'config' => json_encode($famEInfo['config']),
			);
			$this->famService->saveFamilyExtend($extend);
			
			//用户基本信息
			$userBasic['uid'] = $uid;
			$userBasic['nickname'] = $user['nickname'];
			$this->userService->saveUserBasic($userBasic);
			
			//用户扩展信息
			$userExtend['uid'] = $uid;
			$userExtend['qq'] = $euser['qq'];
			$userExtend['mobile'] = $euser['mobile'];
			$this->userService->saveUserExtend($userExtend);
			$this->redirect($this->createUrl('family/apply',array('uid'=>$uid)));
			exit;
		}
		exit('操作有误');
	}
	
	private function _getSearchCondition(){
		$condition = Yii::app()->request->getParam('form');
		
		$uid = Yii::app()->request->getParam('uid');
		if (is_numeric($uid)){
			$condition['uid'] = $uid;
		}
		
		$id = Yii::app()->request->getParam('id');
		if (is_numeric($id)){
			$condition['id'] = $id;
		}
		
		$username = Yii::app()->request->getParam('username');
		if($username){
			$condition['username'] = $username;
		}
		
		$nickname = Yii::app()->request->getParam('nickname');
		if($nickname){
			$condition['nickname'] = $nickname;
		}
		
		$realname = Yii::app()->request->getParam('realname');
		if($realname){
			 $condition['realname'] = $realname;
		}
		
		$name = Yii::app()->request->getParam('name');
		if($name){
			$condition['name'] = $name;
		}
		
		$family_id = Yii::app()->request->getParam('family_id');
		if($family_id){
			$condition['family_id'] = $family_id;
		}
		
		$hidden = Yii::app()->request->getParam('hidden');
		if (array_key_exists($hidden, FamilyService::getFamilyHidden())){
			$condition['hidden'] = $hidden;
		}
		
		$status = Yii::app()->request->getParam('status');
		if (array_key_exists($status, FamilyService::getFamilyStatus()) || array_key_exists($status, FamilyService::getFamilySignStatus())){
			$condition['status'] = $status;
		}
		
		$opType = Yii::app()->request->getParam('opType');
		if (array_key_exists($opType, FamilyService::getOPTypes())){
			$condition['opType'] = $opType;
		}
		
		$sign = Yii::app()->request->getParam('sign');
		if (array_key_exists($sign, FamilyService::getFamilySign())){
			$condition['sign'] = $sign;
		}
		
		$forbidden = Yii::app()->request->getParam('forbidden');
		if (array_key_exists($forbidden, FamilyService::getFamilyForbidden())){
			$condition['forbidden'] = $forbidden;
		}
		
		$create_time_start = Yii::app()->request->getParam('create_time_start');
		if($create_time_start){
			 $condition['create_time_start'] = $create_time_start;
		}
		
		$create_time_end = Yii::app()->request->getParam('create_time_end');
		if($create_time_end){
			$condition['create_time_end'] = $create_time_end;
		}
		
		if($condition){
			foreach ($condition as $k=>&$v){
				if ($v == ''){
					unset($condition[$k]);
				}
			}
		}
		return $condition?$condition:array();
	}
	
	/**
	 * AJAX 检验主播信息的合法性
	 */
	private function _checkUserInfo(){
		if (!$this->isAjax){
			exit('不合法请求');
		}
	
		$doteyName = Yii::app()->request->getParam('doteyName');
		if(empty($doteyName)){
			exit('请输入用户信息后进行校验 ');
		}
	
		$userSer = new UserService();
		if(!is_numeric($doteyName)){
			if(!($userInfo = $userSer->getVadidatorUser($doteyName,0))){
				exit('不合法用户，请重新输入');
			}
			$uid = $userInfo['uid'];
		}else{
			$uid = (int)$doteyName;
		}
	
		if ($uid){
			if(!isset($userInfo)){
				if(!($userInfo = $userSer->getUserBasicByUids(array($uid)))){
					exit('不合法用户，请重新输入');
				}else{
					$userInfo = $userInfo[$uid];
				}
			}
			exit('1'.'#xx#'.$userInfo['uid'].'#xx#'.$userInfo['username'].'#xx#'.$userInfo['nickname'].'#xx#'.$userInfo['realname']);
		}else{
			exit('不合法用户，请重新输入');
		}
	}
	
	/**
	 * 转移家族
	 */
	private function _transFamily(){
		$family_id = Yii::app()->request->getParam('familyId',false);
		$to_uid = Yii::app()->request->getParam('to_uid',false);
		if ($family_id && $to_uid) {
			$famInfo = $this->famService->getFamily($family_id);
			if ($famInfo){
				$from_uid = $famInfo['uid'];
				if($this->famService->transferFamily($family_id, $from_uid, $to_uid)){
					exit('1');
				}
				$notice = $this->famService->getNotice();
				if($notice){
					exit(array_pop($notice));
				}
				exit($notice);
			}
			exit('转让家族信息有误');
		}
		exit('参数有误');
	}
	
	private function _familyUpgrade(){
		$family_id = Yii::app()->request->getParam('familyId',false);
		$level = Yii::app()->request->getParam('level',false);
		if ($family_id && $level) {
			if($this->famService->familyUpgrade($family_id, $level)){
				exit('1');
			}
		}
		exit('家族等级升级失败');
	}
	
	private function _changeSign(){
		$signId = Yii::app()->request->getParam('signId',false);
		$id = Yii::app()->request->getParam('id',false);
		$reason = Yii::app()->request->getParam('reason',false);
		$status = Yii::app()->request->getParam('status',false);
		if ($signId && is_numeric($signId)) {
			exit($this->renderPartial('_contracted_status',array('id'=>$signId)));
		}elseif ($id && $reason && $status){
			if($this->famService->changeSignFamilyStatus($id, $status,$reason)){
				exit('1');
			}else{
				$notice = $this->famService->getNotice();
				if($notice){
					exit(array_pop($notice));
				}
				exit($notice);
			}
		}
		exit('参数有误');
	}
	
	private function _toNormal(){
		$familyId = Yii::app()->request->getParam('familyId',false);
		$id = Yii::app()->request->getParam('id',false);
		$reason = Yii::app()->request->getParam('reason',false);
		if ($familyId && is_numeric($familyId)) {
			exit($this->renderPartial('_contracted_toNormal',array('id'=>$familyId)));
		}elseif ($id && $reason){
			if($this->famService->changeSignFamily($id, $reason)){
				exit('1');
			}else{
				$notice = $this->famService->getNotice();
				if($notice){
					exit(array_pop($notice));
				}
				exit($notice);
			}
		}
		exit('参数有误');
	}
	
	private function _dlLive($familyId,$list,$live_time){
		if ($familyId && $live_time){
			$days = date('t',strtotime($live_time));
				
			if($list){
				$fileName = "家族[".$familyId."]开播查询_".$live_time.'.csv';
				$output = fopen('php://output','w') or die("Can't open php://output");
				header("Content-Type: application/force-download");
				header('Content-Disposition: attachment;filename="'.$fileName.'"');
				header('Cache-Control: max-age=0');
				header("Content-Transfer-Encoding: binary");
				header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				header("Pragma: no-cache");
					
				$temp_arr = array('昵称','UID','有效天数','小时数');
				for ($i=1;$i<=$days;$i++){
					$temp_arr[] = $live_time.'-'.$i;
				}
		
				foreach($temp_arr as $k=>$v){
					$temp_arr[$k] = mb_convert_encoding($v, "GBK", "UTF-8");
				}
				fputcsv($output,$temp_arr);
					
				foreach ($list as $uinfo) {
					$c1 = $uinfo['uid'];
					$c2 = $uinfo['nickname'];
					$c3 = count($uinfo['has_days']);
					$c4 = number_format($uinfo['has_hours']/3600,2);
						
					$temp_arr = array($c2,$c1,$c3,$c4);
					for ($i=1;$i<=$days;$i++){
						$temp_arr[] = isset($uinfo['detail'][$i])?number_format($uinfo['detail'][$i]/3600,2):0;
					}
						
					foreach($temp_arr as $k=>$v){
						$temp_arr[$k] = mb_convert_encoding($v, "GBK", "UTF-8");
					}
					fputcsv($output,$temp_arr);
				}
				fclose($output) or die("Can't close php://output");
			}
		}
		exit;
	}
	
	private function _dlIncome($familyId,$list,$live_time){
		if ($familyId && $live_time){
			if($list){
				$fileName = "家族[".$familyId."]主播收入_".$live_time.'.csv';
				$output = fopen('php://output','w') or die("Can't open php://output");
				header("Content-Type: application/force-download");
				header('Content-Disposition: attachment;filename="'.$fileName.'"');
				header('Cache-Control: max-age=0');
				header("Content-Transfer-Encoding: binary");
				header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				header("Pragma: no-cache");
					
				$temp_arr = array('昵称','UID','等级','当月获得魅力点(总)','当月获得魅力点(有效)','当月获得魅力点(无效)','当月已兑换金额','族长收入');
				foreach($temp_arr as $k=>$v){
					$temp_arr[$k] = mb_convert_encoding($v, "GBK", "UTF-8");
				}
				fputcsv($output,$temp_arr);
					
				foreach ($list as $uinfo) {
					$c1 = $uinfo['uid'];
					$c2 = $uinfo['nickname'];
					$c3 = $uinfo['rank'];
					$c4 = $uinfo['points'];
					$c5 = $uinfo['points_valid'];
					$c6 = $uinfo['points_invalid'];
					$c7 = $uinfo['recharge'];
					$c8 = $uinfo['family_rmb'];
	
					$temp_arr = array($c2,$c1,$c3,$c4,$c5,$c6,$c7,$c8);
					foreach($temp_arr as $k=>$v){
						$temp_arr[$k] = mb_convert_encoding($v, "GBK", "UTF-8");
					}
					fputcsv($output,$temp_arr);
				}
				fclose($output) or die("Can't close php://output");
			}
		}
		exit;
	}
}

?>