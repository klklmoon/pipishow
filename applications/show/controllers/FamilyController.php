<?php
/**
 * 家族
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: 2013-8-13 上午8:34:15 hexin $ 
 * @package
 */
class FamilyController extends PipiController {
	/* var FamilyService $service */
	private $service;
	
	public function beforeAction($action){
		parent::beforeAction($action);
		$this->service = FamilyService::getInstance();
		if(!(Yii::app()->request->isAjaxRequest || Yii::app()->request->isFlashRequest)){
			$clientScript = Yii::app()->getClientScript();
			$clientScript->registerCssFile($this->pipiFrontPath.'/css/family/family.css?token='.$this->hash);
		}
		if(!$this->isLogin){
			if(!in_array($action->getId(), array('index', 'home', 'top','honor', 'help', 'getMyFamily', 'thread'))){
				$this->showError('请先登陆');
			}
		}
		if(!FamilyService::familyEnable()){
			$this->showError('家族功能尚未开放，请稍后再来');
		}
		return true;
	}
	
	public function actionTest(){
		$uid = Yii::app()->user->id;	
		$familys = FamilyModel::model()->findAll();
		foreach($familys as $family){
			if($family['sign'] == 1){
				//重新生成族徽
				$members = $this->service->getMedalMemberByFamily($family['id']);
				if(!empty($members)){
					foreach($members as $m){
						$this->service->saveMyMedal($m['uid'], $family, $m['role_id']);
					}
				}
				
				//生成族徽
				$src = "fontimg".DIR_SEP."family".DIR_SEP.'0';
				$dst = "family".DIR_SEP.$family['id'].DIR_SEP."medal_0";
				$this->service->makeMedal($family['medal'], STATIC_PATH.$src."1.png", IMAGES_PATH.$dst."1.jpg");
				$this->service->makeMedal($family['medal'], STATIC_PATH.$src."2.png", IMAGES_PATH.$dst."2.jpg");
				$this->service->makeMedal($family['medal'], STATIC_PATH.$src."3.png", IMAGES_PATH.$dst."3.jpg");
			}
		}
		
// 		$members = FamilyMemberModel::model()->findAll();
// 		$members = $this->service->arToArray($members);
// 		$uids = array_keys($this->service->buildDataByIndex($members, 'uid'));
// 		foreach($uids as $uid){
// 			echo $uid;
// 			var_dump($this->service->saveMyFamily($uid));
// 		}

		var_dump(UserJsonInfoService::getInstance()->getUserInfo($uid, false));
		
		Yii::app()->end();
	}
	
	/**
	 * 家族首页
	 */
	public function actionIndex(){
		$type = Yii::app()->request->getParam('type', 'member');
		$sort = Yii::app()->request->getParam('sort', 'desc');
		$page = intval(Yii::app()->request->getParam('page', 1));
		$page = $page < 1 ? 1 : $page;
		$pageSize = 16;
		
		$list = $this->service->getFamilyList($type, $sort, $page, $pageSize);
		$data = array(
			'top_dedication' => $this->service->getFamilyTop('dedication'),
			'top_recharge' => $this->service->getFamilyTop('recharge'),
			'top_medal' => $this->service->getFamilyTop('medal'),
			'type'	=> $type,
			'sort'	=> $sort,
			'list'	=> $list,
		);
		$this->render('index', $data);
	}
	
	/**
	 * 我的家族列表页
	 */
	public function actionMyFamily(){
		$uid = Yii::app()->user->id;
		$myFamily = $this->service->getMyFamily($uid);
		$familys = $my = array();
		if(!empty($myFamily['create'])) $familys[] = $myFamily['create'];
		$familys = array_merge($familys, $myFamily['join']);
		$uids = array_keys($this->service->buildDataByIndex($familys, 'uid'));
		$owners = UserJsonInfoService::getInstance()->getUserInfos($uids, false);
		$level = $this->service->getAllLevel();
		$members = $this->service->getMembersByUid($uid);
		foreach($familys as &$f){
			$f['owner'] = $owners[$f['uid']];
			$f['level_info'] = $level[$f['level']];
			$f['member'] = $members[$f['id']];
		}
		$familys = $this->service->buildDataByIndex($familys, 'id');
		$my['create'] = isset($familys[$myFamily['create']['id']]) ? $familys[$myFamily['create']['id']] : array();
		$my['join'] = array();
		foreach($myFamily['join'] as $join){
			if(isset($familys[$join['id']])){
				$my['join'][] = $familys[$join['id']];
			}
		}
		$config = FamilyService::getSetting();
		
		$user = UserJsonInfoService::getInstance()->getUserInfo($uid, false);
		$manager = $this->service->hasBit(intval($user['ut']), USER_TYPE_FAMILY);
		$this->render('myFamily', array(
			'myFamily' => $my,
			'roles'		=> $this->service->getRole(),
			'medal_price' => $config['medal_price'],
			'manager'	=> $manager
		));
	}
	
	/**
	 * 家族主页
	 */
	public function actionHome(){
		$uid = Yii::app()->user->id;
		$family_id = intval(Yii::app()->request->getParam('family_id'));
		$page = intval(Yii::app()->request->getParam('page', 1));
		$page = $page < 1 ? 1 : $page;
		$pageSize = 10;
		$family = $this->checkFamily();
		$data = $this->right($family);
		
		$bbsService = new BbsbaseService();
		$forum = $bbsService->getForumSub(FORUM_FROM_TYPE_FAMILY, $family_id);
		$forum = array_shift($forum);
		$threads = $bbsService->getThreadList($forum['forum_sid'], $page, $pageSize);
// 		$thread_ids = array_keys($bbsService->buildDataByIndex($threads['list'], 'thread_id'));
// 		$posts = $bbsService->getThreadPost($thread_ids);
		if(!empty($threads['list'])){
			$uids = array_keys($bbsService->buildDataByIndex($threads['list'], 'uid'));
			$users = UserJsonInfoService::getInstance()->getUserInfos($uids, false);
			$medal = $this->service->getMedalMembers($uids);
			$userService = new UserService();
			foreach($threads['list'] as &$t){
				$t['nickname']	= $users[$t['uid']]['nk'];
				$t['rank']		= $users[$t['uid']]['rk'];
				$t['pic']		= $userService->getUserAvatar($t['uid'], 'middle', $users[$t['uid']]['atr']);
				$t['medal']		= isset($medal[$t['uid']]) ? $medal[$t['uid']] : '';
// 				$t['content']	= $posts[$t['thread_id']]['content'];
			}
		}
		
		$this->render('home', array_merge($data, array(
			'family'	=> $family,
			'forum'		=> $forum,
			'threads'	=> $threads,
		)));
	}
	
	/**
	 * 第一次进入家族首页有个成功提示流程，目的是要做家族引导或介绍或开通什么功能之类的，并顺便初始化家族规则
	 * @param array $family
	 * @param boolean $focus 是否强制出现申请成功页 
	 * @return array
	 */
	private function familyInit($family, $focus = false){
		$extend = $this->service->getFamilyExtend($family['id']);
		if(empty($extend)){
			$extend = array(
				'family_id'	=> $family['id'],
				'name'		=> $family['name'],
			);
			$this->service->saveFamilyExtend($extend);
			$focus = true;
		}
		
		if($focus){
			//申请成功页
			$user = UserJsonInfoService::getInstance()->getUserInfo($family['uid'], false);
			$this->showError('', array('type' => 'success', 'user' => $user, 'family' => $family));
		}
		return $extend;
	}
	
	/**
	 * 家族首页或管理页面右侧公共数据
	 * @param $family
	 * @return array
	 */
	private function right($family){
		$uid = Yii::app()->user->id;
		$extend = $this->familyInit($family);
		$config = json_decode($extend['config'], true);
		$top = json_decode($extend['top'], true);
		$member = $role = null;
		$manager = $isFamilyDotey = 0;
		$userService = new UserService();
		$living = $family_dotey = array();
		
		if($uid > 0){
			$member = $this->service->getMembersByUids($family['id'], $uid);
			$member = array_pop($member);
			$role = $this->getAdminRole($family_id);
			$user = UserJsonInfoService::getInstance()->getUserInfo($uid, false);
			//是否家族总管
			$manager = $this->service->hasBit(intval($user['ut']), USER_TYPE_FAMILY);
			//是否已是家族主播
			$doteyMembers = $this->service->getDoteyMembers($uid);
			if(!empty($doteyMembers)) $isFamilyDotey = 1;
		}
		
		$elders = $this->service->getMembers($family['id'], FAMILY_ROLE_ELDER);
		$admins = $this->service->getMembers($family['id'], FAMILY_ROLE_ADMINISTRATOR);
		$elder_uids = array_keys($elders);
		$admin_uids = array_keys($admins);
		$uids = array_merge(array($family['uid']), $elder_uids, $admin_uids);
		
		if($family['sign'] == 1){
			$doteys = $this->service->getDoteyMembersByFamily($family['id']);
			$dotey_uids = array_keys($doteys);
			$uids = array_merge($uids, $dotey_uids);
		}
		
		$uids = array_unique($uids);
		$users = UserJsonInfoService::getInstance()->getUserInfos($uids, false);
		foreach($users as &$u){
			$u['pic'] = $userService->getUserAvatar($u['uid'], 'small', isset($u['atr']) ? $u['atr'] : array());
		}
		
		$family_owner = $users[$family['uid']];
		$family_elder = $family_admin = $family_dotey = array();
		foreach($elder_uids as $uid){
			$family_elder[] = $users[$uid];
		}
		foreach($admin_uids as $uid){
			$family_admin[] = $users[$uid];
		}
		
		if($family['sign'] == 1){
			$living = $this->service->getLivingDotey($uid, $family['id'], $dotey_uids);
			foreach($doteys as $d){
				if(!isset($users[$d['uid']]['dk'])) $users[$d['uid']]['dk'] = 0;
				$users[$d['uid']]['rank'] = $users[$d['uid']]['dk'];
				$users[$d['uid']]['id'] = $d['id'];
				$family_dotey[] = $users[$d['uid']];
			}
			usort($family_dotey, array($this, 'memberSort'));
			$condition = array('family_dotey' => 0);
		}else{
			$condition = array();
		}
		$family_members = $this->service->getMembersByPage($family['id'], -1, 1, 10000, $condition, 'id desc');
		foreach($family_members['list'] as &$m){
			$m['pic'] = $userService->getUserAvatar($m['uid'], 'small', $m['atr']);
		}
		usort($family_members['list'], array($this, 'memberSort'));
		
		$honor = $this->service->getHonor($family['id'], 2);
		$web = FamilyService::getSetting();
		$levels = $this->service->getAllLevel();
		$level = array('start' => 0, 'end' => 0);
		foreach($levels as $k => $lv){
			if($family['level'] >= $lv['level']){
				$level['start'] = intval($lv['upgrade']);
				$level['end'] = $family['level'] >= 6 ? $lv['upgrade'] : $levels[$k+1]['upgrade'];
			}
		}
		$level['process'] = $level['end'] <= $extend['recharge_total'] ? $level['end'] : ($extend['recharge_total'] - $level['start']);
		$level['percent'] = ($level['end'] <= $extend['recharge_total'] ? 100 : round($level['process']*100 / ($level['end'] - $level['start']), 2)).'%';
		$level['need'] = $level['end'] <= $extend['recharge_total'] ? 0 : ($level['end'] - $extend['recharge_total']);
		return array(
			'extend'		=> $extend,
			'config'		=> $config,
			'member'		=> $member,
			'family_owner'	=> $family_owner,
			'family_elder'	=> $family_elder,
			'family_admin'	=> $family_admin,
			'all_dotey_uids'=> $all_dotey_uids,
			'honor'			=> $honor,
			'admin'			=> empty($role) ? false : true,
			'medal_price'	=> $web['medal_price'],
			'manager'		=> $manager,
			'isFamilyDotey' => $isFamilyDotey,
			'user'			=> $user,
			'top'			=> $top,
			'living'		=> $living,
			'family_dotey'	=> $family_dotey,
			'family_members'=> $family_members,
			'level'			=> $level,
		);
	}
	
	/**
	 * 家族成员排序
	 */
	private function memberSort($a, $b){
		if($a['de'] > $b['de']) return -1;
		elseif($a['de'] == $b['de'] && $a['id'] < $b['id']) return -1;
		else return 1;
	}
	
	/**
	 * 家族id检查
	 * @param int $family_id
	 */
	private function checkFamily($family_id = 0, $prepare = false){
		$family_id = $family_id ? $family_id : intval(Yii::app()->request->getParam('family_id'));
		if(!$family_id){
			$this->showError('不存在的家族');
		}
		$family = $this->service->getFamily($family_id);
		
		if(empty($family)){
			$this->showError('不存在的家族');
		}
		
		if($family['status'] < 0 && $family['update_time'] < time() - 86400 * 3){
			$this->service->dissolution($family_id, $family['uid'], '审核拒绝三天后重新申请');
		}
		
		if($family['status'] == -2){
			$this->showError('申请审核超时间自动拒绝', array('type' => 'refuse'));
		}elseif($family['status'] == -1){
			$reason = $this->service->getReason($family_id, 1);
			$userService = new UserService();
			$extend = $userService->getUserExtendByUids(array($reason['op_uid']));
			$this->showError($reason['reason'], array('type' => 'refuse', 'qq' => $extend[$reason['op_uid']]['qq']));
		}elseif(!$prepare && ($family['status'] == 0 || $family['status'] == 2)){
			$this->showPrepared($family);
		}

		if($family['hidden'] == 1 && $family['uid'] != Yii::app()->user->id){
			$reason = $this->service->getReason($family_id, 2);
			$userService = new UserService();
			$extend = $userService->getUserExtendByUids(array($reason['op_uid']));
			$this->showError($reason['reason'], array('type' => 'hidden', 'qq' => $extend[$reason['op_uid']]['qq']));
		}
		if($family['forbidden'] == 1 && !in_array($this->getAction()->getId(), array('home', 'honor'))){
			$reason = $this->service->getReason($family_id, 4);
			$userService = new UserService();
			$extend = $userService->getUserExtendByUids(array($reason['op_uid']));
			$this->showError($reason['reason'], array('type' => 'forbidden', 'qq' => $extend[$reason['op_uid']]['qq']));
		}
		return $family;
	}
	
	/**
	 * 出错提示或输出json
	 * @param string|array $error
	 * @param string $type
	 * @param array $data status和type的key有其他用途不要覆盖
	 */
	private function showError($error, array $data = array()){
		if(!is_array($error)) $error = array($error);
		//出错状态码
		$status = 0;
		if(isset($data['status'])){
			$status = intval($data['status']);
			unset($data['status']);
		}
		//错误页模板
		if(!isset($data['type'])){
			$data['type'] = 'error';
		}
		if(Yii::app()->request->isAjaxRequest){
			$this->renderToJson($status, $error, $data);
		}else{
			$data['error'] = $error;
			$this->render('prompt', $data);
		}
		Yii::app()->end();
	}
	
	/**
	 * 筹备提示页
	 * @param array $family
	 * @param array $members
	 */
	private function showPrepared($family, $members = array()){
		$uid = Yii::app()->user->id;
		if(empty($members)){
			$members = $this->service->getMembers($family['id']);
			unset($members[$family['uid']]);
		}
		$users = UserJsonInfoService::getInstance()->getUserInfos(array_merge(array_keys($members), array($family['uid'])), false);
		$in = false;
		foreach($members as $k => &$m){
			$m['nickname'] = $users[$m['uid']]['nk'];
			$m['rank'] = $users[$m['uid']]['rk'];
			if($m['uid'] == $uid) $in = true;
		}
		$this->render('prepare', array('user' => $users[$family['uid']], 'family' => $family, 'members' => $members, 'in' => $in));
		Yii::app()->end();
	}
	
	/**
	 * 跳转或输出json
	 * @param string $url
	 * @param string $message
	 */
	public function redirect($url, $message='',$statusCode=302){
		if(Yii::app()->request->isAjaxRequest){
			$this->renderToJson(1, $message, array('url' => $url));
		}else{
			parent::redirect($url);
		}
		Yii::app()->end();
	}
	
	/**
	 * 统一返回家族主页地址
	 * @param int $family_id
	 * @return string
	 */
	public function createHomeUrl($family_id){
		return $this->createUrl('family/home', array('family_id' => $family_id));
	}
	
	/**
	 * 统一返回家族地址
	 * @see CController::createUrl()
	 */
	public function createUrl($route,$params=array(),$ampersand='&'){
		if(isset($params['family_id'])){
			$family_id = $params['family_id'];
			unset($params['family_id']);
			if($route == 'family/home'){
				$route = '';
			}
			return $this->service->getFamilyUrl($family_id, $route, $params);
		}else{
			return parent::createUrl($route, $params, $ampersand);
		}
	}
	
	/**
	 * 家族榜单
	 */
	public function actionTop(){
		$type = Yii::app()->request->getParam('type');
		$date = Yii::app()->request->getParam('date');
		$this->renderPartial('top', array('top' => $this->service->getFamilyTop($type, $date)));
	}
	
	/**
	 * 家族帮助页面，即家族等级权限说明页
	 */
	public function actionHelp(){
		$operateService = new OperateService();
		$kefuList = $operateService->getAllKefuFromCache();
		$data = $kefu = array();
		foreach($kefuList as $kf){
			if($kf['contact_type'] == KEFU_QQ){
				$data[$kf['kefu_type']][] = $kf;
			}
		}
		$kefu[$this->operateService->getKefuType(KEFU_QQ_WORK)] = isset($data[KEFU_QQ_WORK]) ? $data[KEFU_QQ_WORK] : array();
		$kefu[$this->operateService->getKefuType(KEFU_QQ_FAMILY)] = isset($data[KEFU_QQ_FAMILY]) ? $data[KEFU_QQ_FAMILY] : array();
		$kefu[$this->operateService->getKefuType(KEFU_QQ_DOTEY)] = isset($data[KEFU_QQ_DOTEY]) ? $data[KEFU_QQ_DOTEY] : array();
		$this->render('help', array('kefu' => $kefu));
	}
	
	/**
	 * 更多家族荣誉
	 */
	public function actionHonor(){
		$family_id = Yii::app()->request->getParam('family_id');
		$last_id = intval(Yii::app()->request->getParam('last_id'));
		$family = $this->checkFamily($family_id);
		$user = UserJsonInfoService::getInstance()->getUserInfo($family['uid'], false);
		$honor = $this->service->getHonor($family_id, 1, $last_id);
		$this->renderPartial('honor', array('family'=> $family, 'honor' => $honor, 'family_owner' => $user));
	}

	public function actionGetMyFamily(){
		$uid = Yii::app()->request->getParam('uid');
		if($uid < 1) $this->showError('用户ID不能为空');
		$my = $this->service->getMyFamily($uid);
		$familys = array();
		if(!empty($my['create'])) $familys[] = $my['create'];
		$familys = array_merge($familys, $my['join']);
		$user = UserJsonInfoService::getInstance()->getUserInfo($uid, false);
		$mf = 0;
		if(isset($user['fp']['medal']) && !empty($user['fp']['medal'])){
			$tmp = explode('/', $user['fp']['medal']);
			$mf = $tmp[3];
		}
		$json = array();
		foreach($familys as $f){
			$array = array(
				'id'	=> $f['id'],
				'name'	=> $f['name'],
				'url'	=> $this->createHomeUrl($f['id']),
				'medal' => ''
			);
			if($f['id'] == $mf){
				$array['medal'] = $user['fp']['medal'];
			}
			$json[] = $array;
		}
		$this->renderToJson(1, '', $json);
	}
	
	/**
	 * 家族申请
	 */
	public function actionApply(){
		$uid = Yii::app()->user->id;
		if(Yii::app()->request->getIsPostRequest()){
			$form = new FamilyApplyForm();
			$form->uid = $uid;
			foreach($_POST as $k => $v){
				$form->$k = $v;
			}
			if($form->validate()){
				if($family_id = $this->service->apply($form)){
					$this->redirect($this->createUrl('family/prepare', array('family_id' => $family_id)));
					Yii::app()->end();
				}else{
					$error = $this->service->getNotice();
				}
			}else{
				$temp = $form->getErrors();
				$error = array();
				foreach($temp as $err){
					foreach($err as $e){
						if(!in_array($e, $error)) $error[] = $e;
					}
				}
			}
			$this->render('prompt', array('type' => 'apply', 'error' => $error));
			Yii::app()->end();
		}
		if(!$this->service->applyCheck($uid)){
			$error = $this->service->getNotice();
			foreach($error as $e){
				if($e == Yii::t('family','everyone only create one family')){
					$myFamily = $this->service->getMyFamily($uid);
					$this->showError($e, array('url' => $this->createHomeUrl($myFamily['create']['id'])));
				}
			}
			$this->showError($this->service->getNotice());
		}
		
		Yii::app()->getClientScript()->registerScriptFile($this->pipiFrontPath.'/js/common/jquery.validate.js?token='.$this->hash);
		$user = UserJsonInfoService::getInstance()->getUserInfo($uid, false);
		$userService = new UserService();
		$users = $userService->getUserBasicByUids(array($uid));
		$extends = $userService->getUserExtendByUids(array($uid));
		$user['realname'] = $users[$uid]['realname'];
		$user['qq']	= $extends[$uid]['qq'];
		$user['mobile'] = $extends[$uid]['mobile'];
		$web = FamilyService::getSetting();
		$this->render('apply', array('user' => $user, 'create_price' => $web['create_price']));
	}
	
	/**
	 * 生成族徽图
	 */
	public function actionMakeMedal(){
		$medal = Yii::app()->request->getParam('medal');
		$uid = Yii::app()->user->id;
		$src = "fontimg".DIR_SEP."family".DIR_SEP."63.png";
		$tmp = "tmp".DIR_SEP."family".DIR_SEP."medal_preview_".$uid.".jpg";
		if(!is_dir(IMAGES_PATH."tmp".DIR_SEP."family")) mkdir(IMAGES_PATH."tmp".DIR_SEP."family", 755, true);
		if(!empty($medal)){
			$this->service->makeMedal($medal, STATIC_PATH.$src, IMAGES_PATH.$tmp);
		}
		echo $tmp;
		Yii::app()->end();
	}
	
	/**
	 * 筹备页
	 */
	public function actionPrepare(){
		$uid = Yii::app()->user->id;
		$family_id = intval(Yii::app()->request->getParam('family_id'));
		$family = $this->checkFamily($family_id, true);
		
		if($family['status'] == 1){
			$this->familyInit($family, true);
		}
		
		$members = array();
		//家族长的完成筹备动作
		if(Yii::app()->request->isPostRequest){
			//权限检查
			if(!$this->checkPurview($family_id)){
				$this->showError('您不具有该操作的权限');
			}
			$members = $this->service->getMembers($family_id);
			unset($members[$family['uid']]);
			if(count($members) < 8){
				$this->showError(Yii::t('family','prepare failed'), array('title' => $family['name'].' 家族筹备中'));
			}
			if(!$this->service->changeFamilyStatus($family_id, FAMILY_STATUS_PREPARE)){
				$this->showError('系统出错，请与客服联系！');
			}
		}
		
		$this->showPrepared($family , $members);
	}
	
	/**
	 * 家族成员申请
	 */
	public function actionJoin(){
		$uid = Yii::app()->user->id;
		$family_id = intval(Yii::app()->request->getParam('family_id'));
		$family = $this->checkFamily($family_id, true);
		
		if($family['status'] == 0){
			if(!$this->service->join($family_id, $uid, false)){
				$this->showError($this->service->getNotice());
			}else{
				$this->redirect($this->createUrl('family/prepare', array('family_id' => $family_id)));
			}
		}else{
			if(!$this->service->join($family_id, $uid)){
				$this->showError($this->service->getNotice());
			}else{
				$extend = $this->service->getFamilyExtend($family_id);
				$config = json_decode($extend['config'], true);
				$doteyService = new DoteyService();
				$doteys = $doteyService->getDoteysInUids(array($uid));
				if($config['join_rank'] == -1 && !($family['sign'] == 1 && !empty($doteys))){
					$this->redirect($this->createHomeUrl($family_id));
				}else{
					if($family['sign'] == 1 && !empty($doteys)){
						$this->showError('家族成员申请已提交，等待家族审核！成功加入本签约家族后，您将正式成为该家族的家族主播。同时，不能再加入其他签约家族，若要退出家族，必须获得家族长的同意！');
					}else{
						$this->showError('家族成员申请已提交，等待家族审核！');
					}
				}
			}
		}
	}
	
	/**
	 * 家族成员退出
	 */
	public function actionQuit(){
		$uid = Yii::app()->user->id;
		$family_id = intval(Yii::app()->request->getParam('family_id'));
		$family = $this->checkFamily($family_id, true);
		if($uid == $family['uid']){
			$this->showError('家族长不能退出自己创建的家族！');
		}
		
		if(!$this->service->quit($family_id, $uid)){
			$this->showError($this->service->getNotice());
		}else{
			if($family['status'] == 0){
				$url = $this->createUrl('family/prepare', array('family_id' => $family_id));
			}else{
				$url = $this->createHomeUrl($family_id);
			}
			$this->redirect($url);
		}
	}
	
	/**
	 * 家族管理踢出成员
	 */
	public function actionKick(){
		$uid = Yii::app()->user->id;
		$family_id = intval(Yii::app()->request->getParam('family_id'));
		$uids = Yii::app()->request->getParam('uids');
		if(empty($uids)){
			$this->showError('请先选择成员');
		}
		$family = $this->checkFamily($family_id, true);
		
		$user = UserJsonInfoService::getInstance()->getUserInfo($uid, false);
		$manager = $this->service->hasBit(intval($user['ut']), USER_TYPE_FAMILY);
		$role_type = FAMILY_ROLE_OWNER;
		if(!$manager){
			$role = $this->getAdminRole($family_id);
			$role_type = $role['role_id'];
			if(!$this->checkPurview($family_id, $role)){
				$this->showError('您不具有该操作的权限');
			}
		}
		
		if(!$this->service->kick($family_id, $uids, $uid, $role_type)){
			$this->showError($this->service->getNotice());
		}else{
			if($family['status'] == 0){
				$route = 'family/prepare';
			}else{
				$route = 'family/adminMember';
			}
			$this->redirect($this->createUrl($route, array('family_id' => $family_id)));
		}
	}
	
	/**
	 * 购买家族徽章
	 */
	public function actionBuyMedal(){
		$uid = Yii::app()->user->id;
		$family_id = intval(Yii::app()->request->getParam('family_id'));
		$family = $this->checkFamily();
		
		if(!$this->service->buyMedal($family_id, $uid)){
			$this->showError($this->service->getNotice());
		}else{
			$this->redirect($this->createHomeUrl($family_id));
		}
	}
	
	/**
	 * 佩戴家族徽章
	 */
	public function actionEquipMedal(){
		$uid = Yii::app()->user->id;
		$family_id = intval(Yii::app()->request->getParam('family_id'));
		$family = $this->checkFamily();
		
		if(!$this->service->equipMedal($family_id, $uid)){
			$this->showError($this->service->getNotice());
		}else{
			$this->redirect($this->createHomeUrl($family_id));
		}
	}
	
	/**
	 * 卸下家族徽章
	 */
	public function actionUnloadMedal(){
		$uid = Yii::app()->user->id;
		$family_id = intval(Yii::app()->request->getParam('family_id'));
		$family = $this->checkFamily();
	
		if(!$this->service->unloadMedal($family_id, $uid)){
			$this->showError($this->service->getNotice());
		}else{
			$this->redirect($this->createHomeUrl($family_id));
		}
	}
	
	/**
	 * 家族转让，转让需求有问题，前台禁止转让
	 * 问题原因是如果族长必须拥有族徽的话，那可以通过族内成员互相转让，从而全部成员都不需要购买即可拥有族徽
	 */
	public function actionTransferFamily(){
		$this->showError('转让功能已关闭');
		$uid = Yii::app()->user->id;
		$family_id = intval(Yii::app()->request->getParam('family_id'));
		$family = $this->checkFamily();
		$to_uid = Yii::app()->request->getParam('to_uid');
		$password = Yii::app()->request->getParam('password');
		if(empty($to_uid) || empty($password)){
			$this->showError('受让方未填选或密码错误');
		}
		
		//权限检查
		$role = $this->getAdminRole($family_id);
		if(!$this->checkPurview($family_id, $role)){
			$this->showError('您不具有该操作的权限');
		}
		
		if(!$this->service->transferFamily($family_id, $uid, $to_uid, $role['role_id'], $password)){
			$user = UserJsonInfoService::getInstance()->getUserInfo($to_uid, false);
			$data = array(
				'uid'	=> $user['uid'],
				'nk'	=> $user['nk'],
				'rk'	=> $user['rk']
			);
			$this->showError($this->service->getNotice(), $data);
		}else{
			$this->redirect($this->createHomeUrl($family_id));
		}
	}
	
	/**
	 * 转让时选定uid
	 */
	public function actionCheckUid(){
		$uid = intval(Yii::app()->request->getParam('uid'));
		$status = 0;
		$message = '用户不存在';
		$data = array();
		if($uid > 0){
			$user = UserJsonInfoService::getInstance()->getUserInfo($uid, false);
			if(!empty($user) && isset($user['uid'])){
				$status = 1;
				$message = '';
				$data = array(
					'uid'	=> $user['uid'],
					'nk'	=> $user['nk'],
					'rk'	=> $user['rk']	
				);
			}
		}
		$this->renderToJson($status, $message, $data);
	}
	
	/**
	 * 申请签约家族
	 */
	public function actionSignApply(){
		$uid = Yii::app()->user->id;
		if(!$this->service->signApply($uid)){
			$this->showError($this->service->getNotice());
		}else{
			$this->showError('成功提交申请，请联系签约家族QQ客服完成审核');
		}
	}
	
	/**************************************************
	 * 家族管理
	 **************************************************/
	
	/**
	 * 权限检查
	 * @param int $family_id
	 * @param array $role
	 * @return boolean
	 */
	private function checkPurview($family_id, $role = null){
		if($role === null || !is_array($roles)){
			$role = $this->getAdminRole($family_id);
		}
		if(empty($role)) return false;
		$action = $this->getAction()->getId();
		$controller = str_replace('Controller', '', get_class($this));
		$uid = Yii::app()->user->id;
		return PurviewService::getInstance()->checkPurview($uid, PURVIEW_POLETYPE_FAMILY, $family_id, $action, $controller);
	}
	
	/**
	 * 获取当前用户在家族中不包含身份的唯一角色
	 * @param int $family_id
	 * @return array
	 */
	private function getAdminRole($family_id){
		$uid = Yii::app()->user->id;
		$roles = PurviewService::getInstance()->getUserRolesBySub($uid, PURVIEW_POLETYPE_FAMILY, $family_id);
		$roles = $this->service->buildDataByIndex($roles, 'role_id');
		unset($roles[FAMILY_ROLE_DOTEY]);
		return array_shift($roles);
	}
	
	/**
	 * 家族管理的公共数据
	 * @return array
	 */
	private function familyAdmin(){
		$uid = Yii::app()->user->id;
		$family_id = intval(Yii::app()->request->getParam('family_id'));
		$family = $this->checkFamily();
		
		if($family['forbidden'] == 1){
			$operate = $this->service->getReason($family_id);
			$this->showError('家族封停，封停原因：'.$operate['reason']);
		}
		
		$user = UserJsonInfoService::getInstance()->getUserInfo($uid, false);
		$manager = $this->service->hasBit(intval($user['ut']), USER_TYPE_FAMILY);
		if(!$manager){
			$role = $this->getAdminRole($family_id);
			if(!$this->checkPurview($family_id, $role)){
				$this->showError('您不具有该操作的权限');
			}
		}
		$menu = $role['role_id'] > 0 ? PurviewService::getInstance()->getRoleItems($role['role_id']) : array();
		if($manager) $role['role_id'] = FAMILY_ROLE_OWNER;
		return array(
			'uid' 		=> $uid,
			'family_id' => $family_id,
			'family' 	=> $family,
			'role' 		=> $role,
			'menu' 		=> $menu,
			'manager'	=> $manager
		);
	}
	
	/**
	 * 家族内成员排行
	 */
	public function actionAdminTop(){
		$data = $this->familyAdmin();
		$type = Yii::app()->request->getParam('type', 'normal');
		$page = Yii::app()->request->getParam('page', 1);
		$page = intval($page) < 1 ? 1 : $page;
		$pageSize = 15;
		$right_data = $this->right($data['family']);
		
		$consumeService = new ConsumeService();
		$dotey_rank = $user_rank = array();
		$member = $this->service->getMembers($data['family_id']);
		$uids = array();
		$user_rank = $consumeService->getUserRankFromRedis();
		$dotey_rank = $consumeService->getDoteyRankFromRedis();
		if($type == 'dotey'){
			foreach($member as $m){
				if($m['family_dotey'] == 1) $uids[] = $m['uid'];
			}
			$order = 'charm desc';
		}else{
			foreach($member as $m){
				if($m['family_dotey'] == 0) $uids[] = $m['uid'];
			}
			$order = 'dedication desc';
		}
		$members = $consumeService->getConsumesByConditions(array('uids' => $uids), ($page - 1) * $pageSize, $pageSize, true, $order);
		$pages = $members['pages'];
		
		$uids = array();
		foreach($members['list'] as $m){
			$uids[] = $m['uid'];
		}
		
		$userInfo = UserJsonInfoService::getInstance()->getUserInfos($uids, false);
		$roles = $this->service->getRole();
		$top = array();
		foreach($uids as &$u){
			$m = $member[$u];
			$m['nickname'] = $userInfo[$u]['nk'];
			$m['role'] = $roles[intval($m['role_id'])];
			if($m['family_dotey']) $m['role'] = '家族主播';
			if($m['is_dotey']){
				$m['rank'] = $dotey_rank[$userInfo[$u]['dk']]['rank'];
			}else{
				$m['rank'] = $user_rank[$userInfo[$u]['rk']]['rank'];
			}
			$top[] = $m;
		}
		$this->render('adminTop', array_merge($right_data, $data, array('members' => $top, 'type' => $type, 'pages' => $pages)));
	}
	
	/**
	 * 家族管理，包含公告和家族基础信息修改
	 */
	public function actionAdmin(){
		$data = $this->familyAdmin();
		if(Yii::app()->request->isPostRequest){
			$announcement = Yii::app()->request->getParam('announcement');
			if(empty($announcement)) $this->showError('请填写公告内容！');
			$activity_room = Yii::app()->request->getParam('activity_room', 0);
			$cover = $this->service->uploadFamilyCover();
			if(empty($cover)){
				$error = $this->service->getNotice();
				if(!empty($error)) $this->showError($error);
			}
			$extend = $this->service->getFamilyExtend($data['family_id']);
			$config = json_decode($extend['config'], true);
			$config['activity_room'] = $activity_room;
			$extend = array(
				'family_id'	=> $data['family_id'],
				'announcement' => $announcement,
				'config'	=> json_encode($config),
			);
			if($this->service->saveFamilyExtend($extend)){
				if(!empty($cover)){
					$tmp = IMAGES_PATH.'tmp'.DIR_SEP.'family'.DIR_SEP.$cover;
					$src = IMAGES_PATH.'family'.DIR_SEP.$data['family_id'].DIR_SEP.$cover;
					$this->service->makeCover($tmp, $src);
					unlink($tmp);
					$family = array(
						'id'	=> $data['family_id'],
						'cover'	=> $cover
					);
					$this->service->saveFamily($family);
					$this->showError('家族封面图已更改，1分钟后家族封面生效');
				}else{
					$this->redirect($this->createHomeUrl($data['family_id']));
				}
			}else{
				$this->showError($this->service->getNotice());
			}
		}else{
			$right_data = $this->right($data['family']);
			$this->render('admin', array_merge($right_data, $data));
		}
	}
	
	/**
	 * 加入条件
	 */
	public function actionAdminJoin(){
		$data = $this->familyAdmin();
		if(Yii::app()->request->isPostRequest){
			$extend = $this->service->getFamilyExtend($data['family_id']);
			$config = json_decode($extend['config'], true);
			$join_rank = Yii::app()->request->getParam('join_rank');
			$config['join_rank'] = $join_rank;
			$extend = array(
				'family_id'	=> $data['family_id'],
				'config' => json_encode($config),
			);
			if($this->service->saveFamilyExtend($extend)){
				$this->redirect($this->createHomeUrl($data['family_id']));
			}else{
				$this->showError($this->service->getNotice());
			}
		}else{
			$right_data = $this->right($data['family']);
			$conditions = $this->service->getJoinCondition();
			$this->render('adminJoin', array_merge($right_data, $data, array('conditions' => $conditions)));
		}
	}
	
	/**
	 * 成员审批
	 */
	public function actionAdminCheck(){
		$data = $this->familyAdmin();
		if(Yii::app()->request->isPostRequest){
			$status = Yii::app()->request->getParam('status', 'agree');
			if($status != 'refuse') $status = 'agree';
			$uids = Yii::app()->request->getParam('uids');
			if(!is_array($uids)) $uids = array(intval($uids));
			if(!$this->service->memberCheck($data['family_id'], $uids, $status == 'agree' ? FAMILY_MEMBER_STATUS_SUCCESS : FAMILY_MEMBER_STATUS_REFUSE)){
				$this->showError($this->service->getNotice());
			}
		}
		$type = Yii::app()->request->getParam('type', 'normal');
		$page = Yii::app()->request->getParam('page', 1);
		if($type != 'dotey') $type = 'normal';
		$page = intval($page) > 1 ? intval($page) : 1;
		$pageSize = 10;
		$right_data = $this->right($data['family']);
		
		if($data['family']['sign'] == 1){
			$members = $this->service->getApplyList($data['family_id'], 0, true, $page, $pageSize);
			$doteys = $this->service->getApplyList($data['family_id'], 1, true, $page, $pageSize);
		}else{
			$members = $this->service->getApplyList($data['family_id'], -1, true, $page, $pageSize);
		}
		
		$consumeService = new ConsumeService();
		$dotey_rank = $consumeService->getDoteyRankFromRedis();
		$user_rank = $consumeService->getUserRankFromRedis();
		if($type == 'dotey' && !empty($doteys['list'])){
			$uids = array_keys($this->service->buildDataByIndex($doteys['list'], 'uid'));
			$userInfo = UserJsonInfoService::getInstance()->getUserInfos($uids, false);
			$medal = $this->service->getMedalMembers($uids);
			foreach($doteys['list'] as &$d){
				$d['nickname'] = $userInfo[$d['uid']]['nk'];
				$d['rank'] = $dotey_rank[$userInfo[$d['uid']]['dk']]['rank'];
				$m['medal'] = $medal[$m['uid']];
			}
		}elseif(!empty($members['list'])){
			$uids = array_keys($this->service->buildDataByIndex($members['list'], 'uid'));
			$userInfo = UserJsonInfoService::getInstance()->getUserInfos($uids, false);
			$medal = $this->service->getMedalMembers($uids);
			foreach($members['list'] as &$m){
				$m['nickname'] = $userInfo[$m['uid']]['nk'];
				if($m['apply_type']){
					$m['rank'] = $dotey_rank[$userInfo[$d['uid']]['dk']]['rank'];
				}else{
					$m['rank'] = $user_rank[$userInfo[$m['uid']]['rk']]['rank'];
				}
				$m['medal'] = $medal[$m['uid']];
			}
		}
		$this->render('adminCheck', array_merge($right_data, $data, array('type' => $type, 'members' => $members, 'doteys' => $doteys)));
	}
	
	/**
	 * 成员管理
	 */
	public function actionAdminMember(){
		$data = $this->familyAdmin();
		$type = Yii::app()->request->getParam('type', 'all');
		if(!in_array($type, array('all', 'elder', 'admin', 'dotey_medal', 'dotey', 'member_medal', 'member'))){
			$type = 'member';
		}
		$page = Yii::app()->request->getParam('page', 1);
		$page = intval($page) > 1 ? intval($page) : 1;
		$uid = intval(Yii::app()->request->getParam('uid'));
		$pageSize = 15;
		$right_data = $this->right($data['family']);
		
		$role = -1;
		$conditions = $dotey_rank = $user_rank = array();
		$consumeService = new ConsumeService();
		$user_rank = $consumeService->getUserRankFromRedis();
		$dotey_rank = $consumeService->getDoteyRankFromRedis();
		if($type == 'elder'){
			$role = FAMILY_ROLE_ELDER;
		}elseif($type == 'admin'){
			$role = FAMILY_ROLE_ADMINISTRATOR;
		}elseif($type == 'dotey_medal'){
			$conditions = array('family_dotey' => 1, 'medal_enable' => 1);
		}elseif($type == 'dotey'){
			$conditions = array('family_dotey' => 1);
		}elseif($type == 'member_medal'){
			$conditions = array('family_dotey' => 0, 'medal_enable' => 1);
		}elseif($type == 'member'){
			$conditions = array('family_dotey' => 0);
		}
		$roles = $this->service->getRole();
		
		if(empty($uid)){
			$members = $this->service->getMembersByPage($data['family_id'], $role, $page, $pageSize, $conditions, 'role_id desc, uid asc');
			$uids = array_keys($this->service->buildDataByIndex($members['list'], 'uid'));
			$medal = $this->service->getMedalMembers($uids);
		}else{
			$m = $this->service->getMembersByUids($data['family_id'], $uid);
			$members = array('count' => 0, 'list' => array(), 'page' => null);
			if(!empty($m)){
				$m = $m[$uid];
				$user = UserJsonInfoService::getInstance()->getUserInfo($uid, $false);
				$m['nickname'] = $user['nk'];
				$m['rk'] = $user['rk'];
				$m['dk'] = $user['dk'];
				$members['count'] = 1;
				$members['list'][] = $m;
				$medal = $this->service->getMedalMembers($uid);
			}
		}
		foreach($members['list'] as &$m){
			$m['role'] = $roles[$m['role_id']];
			if($m['role_id'] == 0 && $m['family_dotey']) $m['role'] = '家族主播';
			if($m['is_dotey']){
				$m['rank'] = $dotey_rank[$m['dk']]['rank'];
			}else{
				$m['rank'] = $user_rank[$m['rk']]['rank'];
			}
			$m['medal'] = '';
			if(isset($medal[$m['uid']])){
				$m['medal'] = $medal[$m['uid']];
			}
		}
		$this->render('adminMember', array_merge($right_data, $data, array('type' => $type, 'uid' => $uid, 'members' => $members)));
	}
	
	/**
	 * 设置或解除长老
	 */
	public function actionSetElder(){
		$data = $this->familyAdmin();
		$status = Yii::app()->request->getParam('status', 'set');
		if($status != 'unset') $status = 'set';
		$uids = Yii::app()->request->getParam('uids');
		
		$func = $status == 'set' ? 'addMemberRole' : 'removeMemberRole';
		if(!$this->service->$func($data['family_id'], $uids, FAMILY_ROLE_ELDER, $data['uid'], $data['role']['role_id'])){
			$this->showError($this->service->getNotice());
		}else{
			$this->redirect($this->createUrl('family/adminMember', array('family_id' => $data['family_id'], 'type' => 'elder')));
		}
	}
	
	/**
	 * 设置或解除家族管理
	 */
	public function actionSetAdmin(){
		$data = $this->familyAdmin();
		$status = Yii::app()->request->getParam('status', 'set');
		if($status != 'unset') $status = 'set';
		$uids = Yii::app()->request->getParam('uids');
		
		$func = $status == 'set' ? 'addMemberRole' : 'removeMemberRole';
		if(!$this->service->$func($data['family_id'], $uids, FAMILY_ROLE_ADMINISTRATOR, $data['uid'], $data['role']['role_id'])){
			$this->showError($this->service->getNotice());
		}else{
			$this->redirect($this->createUrl('family/adminMember', array('family_id' => $data['family_id'], 'type' => 'admin')));
		}
	}
	
	/**
	 * 族徽管理
	 */
	public function actionAdminMedal(){
		$data = $this->familyAdmin();
		if(Yii::app()->request->isPostRequest){
			$medal = Yii::app()->request->getParam('medal');
			if(!$this->service->updateMedal($data['family_id'], $data['uid'], $medal)){
				$this->showError($this->service->getNotice());
			}
			$this->redirect($this->createUrl('family/adminMedal', array('family_id' => $data['family_id'])));
		}
		$page = Yii::app()->request->getParam('page', 1);
		$page = intval($page) > 1 ? intval($page) : 1;
		$pageSize = 10;
		$right_data = $this->right($data['family']);
		
		$conditions = array(
			'source' => SOURCE_FAMILY . '*' . SUBSOURCE_FAMILY_MEDAL,
			'to_target_id'	=> $data['family_id'],
		);
		$consumeService = new ConsumeService();
		$records = $consumeService->getPipieggsByCondition($conditions, ($page - 1) * $pageSize, $pageSize, true);
		$uids = array_keys($this->service->buildDataByIndex($records['list'], 'uid'));
		
		$userInfo = UserJsonInfoService::getInstance()->getUserInfos($uids, false);
		$user_rank = $consumeService->getUserRankFromRedis();
		$members = $this->service->getMembersByUids($data['family_id'], $uids);
		$roles = $this->service->getRole();
		$medal = $this->service->getMedalMembers($uids);
		
		foreach($records['list'] as &$r){
			$r['nickname'] = $userInfo[$r['uid']]['nk'];
			$r['rank'] = $user_rank[$userInfo[$r['uid']]['rk']]['rank'];
			$r['role'] = $roles[$members[$r['uid']]['role_id']];
			$r['medal'] = $medal[$r['uid']];
		}
		$web = FamilyService::getSetting();
		$this->render('adminMedal', array_merge($right_data, $data, array('records' => $records, 'medal_eggpoint' => $web['medal_price'] * $this->service->multiple * 0.5, 'update_medal_price' => $web['update_medal_price'])));
	}
	
	/**
	 * 发帖管理
	 */
	public function actionAdminBbs(){
		$data = $this->familyAdmin();
		if(Yii::app()->request->isPostRequest){
			$post_rank = Yii::app()->request->getParam('post_rank');
			$reply_rank = Yii::app()->request->getParam('reply_rank');
			$extend = $this->service->getFamilyExtend($data['family_id']);
			$config = json_decode($extend['config'], true);
			$config['post_rank'] = $post_rank;
			$config['reply_rank'] = $reply_rank;
			$extend = array(
				'family_id'	=> $data['family_id'],
				'config' => json_encode($config),
			);
			if($this->service->saveFamilyExtend($extend)){
				$this->redirect($this->createHomeUrl($data['family_id']));
			}else{
				$this->showError($this->service->getNotice());
			}
		}else{
			$right_data = $this->right($data['family']);
			$post_conditions = $this->service->getPostCondition();
			$reply_conditions = $this->service->getReplyCondition();
			$this->render('adminBbs', array_merge($right_data, $data, array('post_conditions' => $post_conditions, 'reply_conditions' => $reply_conditions)));
		}
	}
	
	/**************************************************
	 * 家族论坛操作
	**************************************************/
	
	/**
	 * (non-PHPdoc) 验证码
	 * @see CController::actions()
	 */
	public function actions(){
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,  //背景颜色
				'minLength'=>4,  //最短为4位
				'maxLength'=>4,   //是长为4位
				'transparent'=>true,  //显示为透明，当关闭该选项，才显示背景颜色
				'width'=>95,
				'height'=>40,
			),
		);
	}
	
	/**
	 * 贴子详细页
	 */
	public function actionThread(){
		$uid = Yii::app()->user->id;
		$family_id = intval(Yii::app()->request->getParam('family_id'));
		$thread_id = intval(Yii::app()->request->getParam('thread_id'));
		$page = intval(Yii::app()->request->getParam('page', 1));
		$page = $page < 1 ? 1 : $page;
		$pageSize = 20;
		$family = $this->checkFamily();
		$data = $this->right($family);
		
		$thread = BbsbaseService::getInstance()->getThreadInfo($thread_id);
		if(empty($thread)){
			$this->showError('贴子不存在');
		}
		if($thread['is_del']){
			$this->showError('贴子已删除', array('url' => $this->createHomeUrl($family_id)));
		}
		$posts = BbsbaseService::getInstance()->getPostListByPage($thread_id, $page, $pageSize);
		if(!empty($posts['list'])){
			$uids = array_keys($this->service->buildDataByIndex($posts['list'], 'uid'));
			foreach($posts['list'] as $p){
				if($p['op_uid'] > 0 && !in_array($p['op_uid'], $uids))
					$uids[] = $p['op_uid'];
			}
			$users = UserJsonInfoService::getInstance()->getUserInfos($uids, false);
			$medal = $this->service->getMedalMembers($uids);
			$userService = new UserService();
			$replyIds = array();
			foreach($posts['list'] as &$p){
				$p['nickname']	= $users[$p['uid']]['nk'];
				$p['rank']		= $users[$p['uid']]['rk'];
				$p['pic']		= $userService->getUserAvatar($p['uid'], 'small', $users[$p['uid']]['atr']);
				$p['medal']		= isset($medal[$p['uid']]) ? $medal[$p['uid']] : '';
				if(!in_array($p['reply_post_id'], $replyIds)) $replyIds[] = $p['reply_post_id'];
				if($p['op_uid'] > 0) $p['op_user'] = $users[$p['op_uid']]['nk'];
			}
			$reply = BbsbaseService::getInstance()->getPostByIds($replyIds);
			foreach($posts['list'] as &$p){
				if(isset($reply[$p['reply_post_id']])){
					$p['reply'] = $reply[$p['reply_post_id']];
				}
			}
		}
		$role = $this->getAdminRole($family_id);
		$needValidate = true;
		if(!empty($role)) $needValidate = false;
		else{
			$user = UserJsonInfoService::getInstance()->getUserInfo($uid, false);
			$manager = $this->service->hasBit(intval($user['ut']), USER_TYPE_FAMILY);
			if(!$manager){
				$userInfo = UserJsonInfoService::getInstance()->getUserInfo($uid, false);
				if($userInfo['rk'] >= 4) $needValidate = false;
			}
		}
		$this->render('thread', array_merge($data, array(
			'family'	=> $family,
			'admin'		=> empty($role) ? false : true,
			'thread'	=> $thread,
			'posts'		=> $posts,
			'needValidate' => $needValidate
		)));
	}
	
	/**
	 * 发帖
	 */
	public function actionSendThread(){
		$uid = Yii::app()->user->id;
		$family_id = intval(Yii::app()->request->getParam('family_id'));
		$family = $this->checkFamily();
		$member = $this->service->getMembersByUids($family_id, $uid);
		$needValidate = true;
		if(isset($member[$uid]) && (in_array($member[$uid]['role_id'], array(FAMILY_ROLE_OWNER, FAMILY_ROLE_ELDER, FAMILY_ROLE_ADMINISTRATOR)) || $member[$uid]['is_dotey'])){
			$needValidate = false;
		}else{
			$userInfo = UserJsonInfoService::getInstance()->getUserInfo($uid, false);
			if($userInfo['rk'] >= 4) $needValidate = false;
		}
	
		if(Yii::app()->request->isPostRequest){
			$form = new ThreadForm();
			$form->codeEnable = $needValidate;
			foreach($_POST as $k => $v){
				$form->$k = $v;
			}
			if($form->validate()){
				if($thread_id = $this->service->sendThread($family_id, $uid, $form->title, $form->content)){
					$this->redirect($this->createUrl('family/thread', array('family_id' => $family_id, 'thread_id' => $thread_id)));
					Yii::app()->end();
				}else{
					$error = $this->service->getNotice();
				}
			}else{
				$temp = $form->getErrors();
				$error = array();
				foreach($temp as $err){
					foreach($err as $e){
						if(!in_array($e, $error)) $error[] = $e;
					}
				}
			}
			$this->showError($error);
		}else{
			$user = UserJsonInfoService::getInstance()->getUserInfo($uid, false);
			$userService = new UserService();
			$user['pic'] = $userService->getUserAvatar($user['uid'], 'small', $user['atr']);
				
			$data = $this->right($family);
			$this->render('sendThread', array_merge($data, array('family' => $family,'user' => $user,'needValidate' => $needValidate, 'edit' => false)));
		}
	}
	
	/**
	 * 回帖
	 */
	public function actionSendPost(){
		$uid = Yii::app()->user->id;
		$family_id = intval(Yii::app()->request->getParam('family_id'));
		$thread_id = intval(Yii::app()->request->getParam('thread_id'));
		$family = $this->checkFamily();
		$member = $this->service->getMembersByUids($family_id, $uid);
		$needValidate = true;
		if(isset($member[$uid]) && (in_array($member[$uid]['role_id'], array(FAMILY_ROLE_OWNER, FAMILY_ROLE_ELDER, FAMILY_ROLE_ADMINISTRATOR)) || $member[$uid]['is_dotey'])){
			$needValidate = false;
		}else{
			$userInfo = UserJsonInfoService::getInstance()->getUserInfo($uid, false);
			if($userInfo['rk'] >= 4) $needValidate = false;
		}
	
		if(Yii::app()->request->isPostRequest){
			$form = new PostForm();
			$form->codeEnable = $needValidate;
			foreach($_POST as $k => $v){
				$form->$k = $v;
			}
			if($form->validate()){
				if($post_id = $this->service->sendPost($family_id, $thread_id, $uid, $form->content, $form->reply_post_id)){
					$this->redirect($this->createUrl('family/thread', array('family_id' => $family_id, 'thread_id' => $thread_id)));
					Yii::app()->end();
				}else{
					$error = $this->service->getNotice();
				}
			}else{
				$temp = $form->getErrors();
				$error = array();
				foreach($temp as $err){
					foreach($err as $e){
						if(!in_array($e, $error)) $error[] = $e;
					}
				}
			}
			$this->showError($error);
		}
	}
	
	/**
	 * 编辑帖子
	 */
	public function actionEditPost(){
		$uid = Yii::app()->user->id;
		$family_id = intval(Yii::app()->request->getParam('family_id'));
		$tid = intval(Yii::app()->request->getParam('tid'));
		$pid = intval(Yii::app()->request->getParam('pid'));
		if($tid < 1 || $pid < 1) $this->showError('帖子错误');
		$family = $this->checkFamily();
	
		$role = $this->getAdminRole($family_id);
		$user = UserJsonInfoService::getInstance()->getUserInfo($uid, false);
		//是否家族总管
		$manager = $this->service->hasBit(intval($user['ut']), USER_TYPE_FAMILY);
		if(!empty($role) || $manager){
			$thread = BbsbaseService::getInstance()->getThreadInfo($tid);
			$post = BbsbaseService::getInstance()->getPostByIds($pid);
			if(empty($thread) || empty($post)) $this->showError('帖子错误');
			$post = array_pop($post);
			
			if(Yii::app()->request->isPostRequest){
				$content = Yii::app()->request->getParam('content');
				if(empty($content)) $this->showError('内容不完整');
				$post['content'] = $content;
				$post['op_uid'] = $uid;
				$post['update_time'] = time();
				if(BbsbaseService::getInstance()->editPost($post)){
					$this->redirect($this->createUrl('family/thread', array('family_id' => $family_id, 'thread_id' => $tid)));
				}else{
					$this->showError(Yii::t('family','system error'));
				}
			}else{
				$data = $this->right($family);
				$this->render('sendThread', array_merge($data, array('family' => $family,'user' => $user,'edit' => true,'thread' => $thread, 'post' => $post)));
			}
		}else{
			$this->showError('您不具有该操作的权限');
		}
	}
	
	/**
	 * 删除回复贴
	 */
	public function actionDeletePost(){
		$uid = Yii::app()->user->id;
		$family_id = intval(Yii::app()->request->getParam('family_id'));
		$tid = intval(Yii::app()->request->getParam('tid'));
		$pid = intval(Yii::app()->request->getParam('pid'));
		if($tid < 1 || $pid < 1) $this->showError('帖子错误');
		$family = $this->checkFamily();
		
		$role = $this->getAdminRole($family_id);
		$user = UserJsonInfoService::getInstance()->getUserInfo($uid, false);
		//是否家族总管
		$manager = $this->service->hasBit(intval($user['ut']), USER_TYPE_FAMILY);
		if(!empty($role) || $manager){
			if(BbsbaseService::getInstance()->deletePost($pid)){
				$this->redirect($this->createUrl('family/thread', array('family_id' => $family_id, 'thread_id' => $tid)));
			}else{
				$this->showError(Yii::t('family','system error'));
			}
		}else{
			$this->showError('您不具有该操作的权限');
		}
	}
	
	/**
	 * 置顶动作
	 */
	public function actionBbsTop(){
		$data = $this->familyAdmin();
		$thread_ids = Yii::app()->request->getParam('ids');
		if(empty($thread_ids)) $this->showError('请选择贴子');
		
		if(BbsbaseService::getInstance()->topThreads($thread_ids)){
			$this->redirect($this->createHomeUrl($data['family_id']));
		}else{
			$this->showError(Yii::t('family','system error'));
		}
	}
	
	/**
	 * 删帖动作
	 */
	public function actionBbsDelete(){
		$data = $this->familyAdmin();
		$thread_ids = Yii::app()->request->getParam('ids');
		if(empty($thread_ids)) $this->showError('请选择贴子');
		
		if(BbsbaseService::getInstance()->deleteThread($thread_ids)){
			$this->redirect($this->createHomeUrl($data['family_id']));
		}else{
			$this->showError(Yii::t('family','system error'));
		}
	}
	
	/**
	 * 举报动作
	 */
	public function actionBbsReport(){
		$uid = Yii::app()->user->id;
		$family_id = intval(Yii::app()->request->getParam('family_id'));
		$post_id = intval(Yii::app()->request->getParam('post_id'));
		$family = $this->checkFamily();
		if(!BbsbaseService::getInstance()->doPostAction($post_id, $uid, 0)){
			$this->showError(BbsbaseService::getInstance()->getNotice());
		}else{
			$post = BbsbaseService::getInstance()->getPostByIds($post_id);
			$post = array_pop($post);
			$this->redirect($this->createUrl('family/thread', array('family' => family_id, 'thread_id' => $post['thread_id'])));
		}
	}
	
	/**
	 * 赞动作
	 */
	public function actoinBbsPraise(){
		$uid = Yii::app()->user->id;
		$family_id = intval(Yii::app()->request->getParam('family_id'));
		$post_id = intval(Yii::app()->request->getParam('post_id'));
		$family = $this->checkFamily();
		
		if(!BbsbaseService::getInstance()->doPostAction($post_id, $uid, 1)){
			$this->showError(BbsbaseService::getInstance()->getNotice());
		}else{
			$post = BbsbaseService::getInstance()->getPostByIds($post_id);
			$post = array_pop($post);
			$this->redirect($this->createUrl('family/thread', array('family' => family_id, 'thread_id' => $post['thread_id'])));
		}
	}
	
	/**
	 * 家族收益
	 * @author suqian
	 */
	public function actionAdminIncome(){
		$data = $this->familyAdmin();
		$right_data = $this->right($data['family']);
		$type = Yii::app()->request->getParam('type');
		$page = Yii::app()->request->getParam('page', 1);
		$familyId = Yii::app()->request->getParam('family_id');
		$month = Yii::app()->request->getParam('month');
		$month = $month ? $month : date('Y-m');
		$page = intval($page) < 1 ? 1 : $page;
		$pageSize = 20;
		$type = !in_array($type,array('live','live_info','income','forceIncome','income_info','join')) ? 'live' : $type;
		$familyStaticesService = new FamilyStaticsService();
		$dateCal = new PipiDateCal();
		$archivesService = new ArchivesService();
		$monthList = $dateCal->getCurrentYearPrevMonth(true);
		//直播记录
		if($type == 'live'){
			$_data = array();
			$statices = $familyStaticesService->staticsFamiliyMemeberLiveById($familyId,$month,-1,$page,$pageSize,$_data);
			if(!$statices){
				$statices = array('count'=>0,'list'=>array(),'pages'=>null);
			}
			if(date('n',time()) != date('n',strtotime($month))){
				list($condition['startTime'],$condition['endTime']) = $dateCal->pushDownMonthTime(0,false);
				$liveRecords = $archivesService->getLiveRecordsByCondition($_data[0],$condition);
			}else{
				$liveRecords = $_data[1];
			}
			list($tStartTime,$tEndTime) = $dateCal->pushDownDaysTime(0,false);
			list($yStartTime,$yEndTime) = $dateCal->pushDownDaysTime(1,false);
			$tmp = array('today'=>array(),'yesterday'=>array(),'month'=>array());
			$header = array('today_lives'=>0,'yesterday_lives'=>0,'month_lives'=>0);
			foreach($liveRecords as $_liveRecord){
				foreach($_liveRecord as $liveRecord){
					if($liveRecord['live_time'] >= $tStartTime &&  $liveRecord['live_time'] <= $tEndTime && !isset($tmp['today'][$liveRecord['archives_id']])){
						$tmp['today'][$liveRecord['archives_id']] = 1;
						$header['today_lives'] += 1;
					}
					if($liveRecord['live_time'] >= $yStartTime &&  $liveRecord['live_time'] <= $yStartTime && !isset($tmp['yesterday'][$liveRecord['archives_id']])){
						$tmp['today'][$liveRecord['archives_id']] = 1;
						$header['yesterday_lives'] += 1;
					}
					if($liveRecord['live_time'] > 0 && !isset($tmp['month'][$liveRecord['archives_id']])){
						$tmp['month'][$liveRecord['archives_id']] = 1;
						$header['month_lives'] += 1;
					}
				}
			}
			$header['family_id'] = $familyId;
			$this->render('adminIncome', array_merge($right_data, $data, array('type' => $type,'statices'=>$statices,'monthList'=>$monthList,'month'=>$month,'header'=>$header)));
		//直播信息
		}elseif($type == 'live_info'){
			$uid =  (int)Yii::app()->request->getParam('uid');
			$userService = new UserService();
			$userJson = $userService->getUserFrontsAttributeByCondition($uid,true);
			$statices = $familyStaticesService->getDoteyMonthLiveRecords($uid,$familyId,$month);
			$info['uid'] = $uid;
			$info['nk'] = $userJson['nk'];
			$info['dk'] = $userJson['dk'];
			$info['time'] = 0;
			$info['point'] = 0;
			foreach($statices as $statice){
				$info['time'] += $statice['live_time'];
				$info['points'] += $statice['points'];
			}
			$this->render('adminIncomeInfo', array_merge($right_data, $data, array('type' => $type,'statices'=>$statices,'month'=>$month,'info'=>$info)));
		}elseif($type == 'income'){
			//家族收益
			$_data = array();
			list($condtion['startTime'],$condtion['endTime']) = $dateCal->getCurPointMonthTime($month);
			$statices['list'] = $familyStaticesService->staticsFamiliyIncomeById($familyId,$month,-1,$page,$pageSize,$_data);
			$statices['count'] = $familyStaticesService->countFamilyExitRecords($familyId,$condtion['startTime'],$condtion['endTime']);
			$localStatices = $statices;
			if(!$statices){
				$statices = array('count'=>0,'list'=>array(),'pages'=>null);
			}
			if(date('n',time()) != date('n',strtotime($month))){
				$localStatices['list'] = $familyStaticesService->staticsFamiliyIncomeById($familyId,date('Y-m',time()),-1,$page,$pageSize,$_data);
					$statices['count'] = $familyStaticesService->countFamilyExitRecords($familyId,$condtion['startTime'],$condtion['endTime']);
			}else{
				$localStatices = $statices;
			}
			$pages=new CPagination($statices['count']);
			$pages->pageSize = $pageSize;
			$statices['pages'] = $pages;
			
			$header['family_id'] = $familyId;
			$header['today_points'] = 0;
			$header['yesterday_points'] = 0;
			$header['family_points'] = 0;
			$header['family_rmb'] = 0;
			$header['rmb'] = 0;
			foreach($localStatices['list'] as $statice){
				$header['today_points'] += $familyStaticesService->getFamilyToDayDayCharmPoints($familyId,$statice['uid'],0,false);
				$header['yesterday_points'] += $familyStaticesService->getFamilyToDayDayCharmPoints($familyId,$statice['uid'],1,false);
				$header['rmb'] += $statice['rmb'];
				$header['family_rmb'] += $statice['family_rmb'];
				$header['family_points'] += $statice['points'];
			}
			$this->render('adminIncomeLive', array_merge($right_data, $data, array('type' => $type,'statices'=>$statices,'monthList'=>$monthList,'month'=>$month,'header'=>$header)));
		}elseif($type == 'forceIncome'){
			//强退主播收益
			$_data = array();
			$statices['list'] = $familyStaticesService->staticsFamiliyForceIncome($familyId,$page,$pageSize,$_data);
			$statices['count'] = $familyStaticesService->countUserForceExitRecords($familyId);
			$localStatices = $statices;
			if(!$statices){
				$statices = array('count'=>0,'list'=>array(),'pages'=>null);
			}
			$pages=new CPagination($statices['count']);
			$pages->pageSize = $pageSize;
			$statices['pages'] = $pages;
			
			$header['family_id'] = $familyId;
			$header['family_points'] = 0;
			$header['family_rmb'] = 0;
			foreach($localStatices['list'] as $statice){
				$header['family_rmb'] += $statice['family_rmb'];
				$header['family_points'] += $statice['points'];
			}
			$this->render('adminIncomeForce', array_merge($right_data, $data, array('type' => $type,'statices'=>$statices,'monthList'=>$monthList,'month'=>$month,'header'=>$header)));
		}elseif($type == 'income_info'){
			$uid =  (int)Yii::app()->request->getParam('uid');
			$userService = new UserService();
			$userJson = $userService->getUserFrontsAttributeByCondition($uid,true);
			$statices = $familyStaticesService->getDoteyMonthLiveRecords($uid,$familyId,$month);
			$info['uid'] = $uid;
			$info['nk'] = $userJson['nk'];
			$info['dk'] = $userJson['dk'];
			$info['time'] = 0;
			$info['point'] = 0;
			foreach($statices as $statice){
				$info['time'] += $statice['live_time'];
				$info['points'] += $statice['points'];
			}
			$this->render('adminIncomeLiveInfo', array_merge($right_data, $data, array('type' => $type,'statices'=>$statices,'month'=>$month,'info'=>$info)));
		}elseif($type == 'join'){
			//主播退入退出
			$userJoinRecordsModel = new FamilyExitRecordsModel();
			$criteria = $userJoinRecordsModel->getDbCriteria();
			$criteria->condition = 'family_id='.(int)($familyId).' AND is_dotey = 1';
			$criteria->order = 'create_time DESC ';
			$count = $userJoinRecordsModel->count($criteria);
			$pages = new CPagination($count);
			$pages->pageSize = $pageSize;
			$pages->applyLimit($criteria);
			$records = $userJoinRecordsModel->findAll($criteria);
			$records = $archivesService->arToArray($records);
			$uids = array_keys($archivesService->buildDataByIndex($records, 'uid'));
			$uids = array_merge($uids,array_keys($archivesService->buildDataByIndex($records, 'op_uid')));
			$userInfo = UserJsonInfoService::getInstance()->getUserInfos($uids, false);
			foreach($records as &$r){
				$r['nk'] = isset($userInfo[$r['uid']]['nk']) ? $userInfo[$r['uid']]['nk'] : '';
				$r['rk'] = isset($userInfo[$r['uid']]['rk']) ? $userInfo[$r['uid']]['rk'] : 0;
				$r['op_nk'] =  isset($userInfo[$r['op_uid']]['nk']) ? $userInfo[$r['op_uid']]['nk'] : '';
				if($r['quit_time']){
					$r['leave'] = $r['leave_type'] ? '由<em class="pink">'.$r['op_nk'].'</em>踢出' : '强行离开';
					$r['quit_time'] = date('Y-m-d H:i',$r['quit_time']);
				}else{
					$r['leave'] = '成员加入';
					$r['quit_time'] = '';
				}
				$r['join_time'] = date('Y-m-d H:i',$r['join_time']);
				
			}
			$this->render('adminIncomeJoin', array_merge($right_data, $data, array('type' => $type,'records'=>$records,'pages'=>$pages)));
		}
	}
}
