<?php
define('SHOWSTAT', '/webservice/showstat/');
/**
 *　皮皮乐天后台管理基础控制器层，所有应用控制器基类
 *
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su Peng <594524924@qq.com>
 * @version $Id: PipiAdminController.php 8317 2013-03-29 01:19:47Z suqian $
 * @package 
 */
class PipiAdminController extends CController{
	const DOTEY_MANAGER_FLAG = '主播经理';
	
	/**
	 * @var string 页面布局文件
	 */
	public $layout = 'main';
	
	/**
	 * @var array 视图层的变量容器
	 */
	public $viewer = array();
	
	/**
	 * 导航面包屑
	 * @var array
	 */
	public $breadcrumbs = array();
	
	/**
	 * @var PurviewService 权限服务层
	 */
	public $purSer ;
	
	/**
	 * @var array 权限菜单树
	 */
	public $menuTree = array(); 
	
	/**
	 * @var string yii预定义核心JS、CSS库
	 */
	public  $coreScriptPath;
	
	/**
	 * @var string 皮皮乐天后台自定义核心JS、CSS库
	 */
	public  $pipiScriptPath;
	
	/**
	 * @var string 皮皮乐天后台引用CSS资源路径
	 */
	public  $cssAssetsPath;
	
	/**
	 * @var string 皮皮乐天后台引用JS资源路径
	 */
	public  $jsAssetsPath;
	
	/**
	 * @var string 默认的主题样式文件
	 */
	public  $themeCssFile;
	
	/**
	 * @var CClientScript 
	 */
	public 	$cs;
	
	/**
	 * @var string 图像域名路径
	 */
	public  $imgDomain;
	
	/**
	 * @var array 操作者角色ID
	 */
	public $op_role_id;
	
	public $op_uid;
	
	public function __construct($id,$module){
		parent::__construct($id,$module);
	}
	
	public function init(){
		parent::init();
		$params  = Yii::app()->params;
		$this->imgDomain = $params['images_server']['url'];
		
		/* @var $clientScript CClientScript */
		$this->cs = Yii::app()->getClientScript();
// 		$this->cs->reset();
		$this->assetsYiiCore();
		$this->assetsAdminCoreCss();
		$this->assetsAdminCoreJs();
	}
	
	/**
	 * action 前缀动作验证用户是否登录
	 * 
	 * @see CController::beforeAction()
	 */
	public function beforeAction($action){
		//认证登录
		$this->authLogin();
		
		//验证是否有权限可以继续访问
		$this->authAccess();
		//认证菜单
		$this->menuTree = $this->authMenu();
		header("cache-control:no-cache,must-revalidate");
		return true;
	}
	
	public function afterAction($action){
		// @todo
	}
	
	/**
	 * 验证登录
	 */
	public function authLogin(){
		if (strtolower($this->id) != 'user' && !in_array(strtolower($this->action->id), $this->authlessActions())){
			if(Yii::app()->user->getIsGuest()){
				Yii::app()->user->loginRequired();
				Yii::app()->end();
			}
		}
		return true;
	}
	
	/**
	 * 访问认证
	 */
	public function authAccess(){
		$this->purSer = new PurviewService();
		$module = $this->module?$this->module:'';
		$controller = $this->id;
		$action = $this->action->id;
		$uid = Yii::app()->user->getId();
		$this->op_uid = $uid;
		
		if (!in_array(strtolower($this->id).'/'.$this->action->id, $this->authlessActions())){
			if(!($op_role_id = $this->purSer->checkPurview($uid, PURVIEW_ROLETYPE_ADMIN, 0, $action,$controller,$module))){
				if(Yii::app()->request->isAjaxRequest){
					exit("您无权限操作");
				}else{
// 					throw new CHttpException(405);
					Yii::app()->user->loginRequired();
					Yii::app()->end();
				}
			}
			$this->op_role_id = $op_role_id['role_id'];
			define('ADMIN_OP_ROLE_ID',$op_role_id['role_id']);
			define('ADMIN_OP_UID',$uid);
			define('ADMIN_OP_SUB_ID',$op_role_id['sub_id']);
			define('ADMIN_PURVIEW_ID',$op_role_id['purview_id']);
		}
	}
	
	/**
	 * 权限树认证显示
	 * 
	 * @return array
	 */
	public function authMenu(){
		if(Yii::app()->user->getId()){
			$this->purSer = new PurviewService();
			if($allRoles = $this->purSer->getUserRolesBySub(Yii::app()->user->getId(),PURVIEW_ROLETYPE_ADMIN)){
				$roleIds = array();
				foreach ($allRoles as $role){
					$roleIds[] = $role['role_id'];
				}
					
				if($items = $this->purSer->getRolesItems($roleIds)){
					$_menu = array();
					foreach($items as $k=>$item){
						if($item['is_tree_display'] == 1){
							$route = '';
							if (is_null($item['module'])){
								$route .=  $item['module'].'/';
							}
							$route .= $item['controller'].'/'.$item['action'];
							$_menu[$item['group']][$item['purview_name']] = $route;
						}
					}
					return $_menu;
				}else{
					//@todo 还没有分配任何权限操作;
				}
					
			}else{
				//@todo 还没有任何权限操作;
			}
		}
	}
	
	/**
	 * 验证是否是主播经理
	 */
	public function authDoteyManager(){
		//可能新增的经理人
		$roleInfo = $this->purSer->getRoleByName(self::DOTEY_MANAGER_FLAG,PURVIEW_ROLETYPE_ADMIN);
		if($roleInfo){
			return $this->purSer->getRoleUserByRoleId($roleInfo['role_id'],$this->op_uid);
		}
		return false;
	}
	
	/**
	 * 验证主播代理
	 */
	public function authDoteyProxy(){
		$dotyeSer = new DoteyService();
		return $dotyeSer->getProxy($this->op_uid);
	}
	
	/**
	 * 获取所有权限分组
	 * 
	 * @return array 
	 */
	public function getAllGroups($keyType = 1) {
		$allGroups = $this->purSer->getPurviewGroups();
		$groups = array();
		foreach($allGroups as $key=>&$group) {
			$key = ($keyType == 1)?$group:($keyType ==2)?($key+1):'';
			$groups[$key] = $group; 
		}
		return $groups;
	}
	
	/**
	 * 获取数据库所有分组信息明细
	 * 
	 * @param array $groups 
	 * @param bollean $isPurviewNameKey  是否以权限名做为key
	 * @return array
	 */
	public function getAllGroupsDetail($groups = array(),$isPurviewNameKey = false,$isFormat = true){
		if (!isset($groups)){
			return $this->getAllGroups();
		}
		$allGroups = $this->purSer->getItemsByGroups($groups);
		if ($isFormat){
			return $this->formatGroupsItems($allGroups,$isPurviewNameKey);
		}else{
			return $allGroups;
		}
	}
	
	/**
	 * 获取所有权限配置信息
	 * 
	 * @return array 
	 */
	public function getAllGroupsConfig(){
		$allGroups = Yii::getKeyConfig('purview','purview_items');
	
		$groupsDetail = array();
		foreach($allGroups as $key=>$group) {
			if(isset($group['items'])){
				foreach($group['items'] as $item => $label){
					$groupsDetail[$key][$key.':'.$item] = $item;
				}
			}
				
		}
		return $groupsDetail;
	}
	
	/**
	 * 格式化分组权限
	 *
	 * @param array $items
	 * @param bollean $isPurviewNameKey 是否用权限名做为KEY
	 * @return array
	 */
	public function formatGroupsItems(Array $items,$isPurviewNameKey = false){
		$groupsDetail = array();
		foreach($items as $item) {
			$key = $isPurviewNameKey?$item['purview_name']:$item['purview_id'];
			$groupsDetail[$item['group']][$key] = $item['purview_name'];
		}
		return $groupsDetail;
	}
	
	/**
	 * 非认证的控制器
	 * 
	 * @return multitype:string 
	 */
	public function authlessActions() {
		return array('user/login','user/logout','public/error');
	}
	
	/**
	 * 恢复账号
	 */
	public function restoreAccount(){
		if(!$this->isAjax){
			exit('不合法请求');
		}
		$uid = Yii::app()->request->getParam('uid');
		if (!$uid){
			exit('缺少参数');
		}
		$userBase = array();
		$userBase['user_status'] = 0;
		
		$userSer = new UserService();
		if($userSer->saveUserJson($uid, $userBase)){
			$userSer->saveAdminOpLog('恢复 账号(UID='.$uid.')',$uid);
			exit('1');
		}else{
			exit('恢复失败，请联系管理员');
		}
	}
	
	/**
	 * 获取皮蛋消费
	 */
	public function getPipiEggsConsumeSum(Array $uids,$type = 'halfMonth',$_condition=array()){
		$condition = array();
		switch ($type){
			case 'halfMonth':
				$condition['create_time_on'] = strtotime('-15 days');
				break;
			case 'month':
				$condition['create_time_on'] = strtotime('-1 months');
				break;
			case 'week':
				$condition['create_time_on'] = strtotime('-7 days');
			case 'year':
				$condition['create_time_on'] = strtotime('-1 years');
			case 'all':
				$condition['create_time_on'] = 0;
				$condition['create_time_end'] = time();
				break;
			case 'custom':
				$condition['create_time_on'] = !empty($_condition['start_time'])?strtotime($_condition['start_time']):0;
				$condition['create_time_end'] = !empty($_condition['end_time'])?strtotime($_condition['end_time']):time();
				break;
		}
	
		if (isset($_condition['isPlus']) && $_condition['isPlus'] == true){
			$condition['isPlus'] = true;
		}else{
			$condition['isPlus'] = false;
		}
		
		$condition['uid'] = $uids;
		$consumeSer = new ConsumeService();
		return $consumeSer->getPipieggsSumByCondition($condition,null,null,false);
	}
	
	/**
	 * 获取YII核心资源路径
	 */
	public function getCoreScriptPath(){
		if (!$this->coreScriptPath){
			$coreScriptUrl = $this->cs->getCoreScriptUrl();
			$this->coreScriptPath = $coreScriptUrl;
		}
		return $this->coreScriptPath;
	}
	
	/**
	 * @return string
	 */
	public function getPipiScriptPath() {
		if (!isset($this->pipiScriptPath)){
// 			$this->pipiScriptPath = Yii::getPathOfAlias('root.statics');
			$this->pipiScriptPath = '/statics';
		}
		return $this->pipiScriptPath;
	}
	
	/**
	 * @param int $uid
	 * @param int $upData
	 */
	public function updateUserInfo($uid,Array $upData,$redirectUrl=''){
		$userSer = new UserService();
		$doteySer = new DoteyService();
		$archivesSer = new ArchivesService();
		
		$uinfo = $userSer->getUserBasicByUids(array($uid));
		if($uinfo){
			$uinfo = $uinfo[$uid];
			$oldNickName = $uinfo['nickname'];
			$oldUserType = intval($uinfo['user_type']);
			$oldUserStatus = intval($uinfo['user_status']);
			$userType = isset($upData['user_type'])?intval($upData['user_type']):null;
			$userJson = array();
			
			//修改用户昵称发送事件包
			if(isset($upData['nickname'])){
				if($oldNickName != $upData['nickname']) $userJson['nickname'] = $upData['nickname'];
				unset($upData['nickname']);
			}
				
			//修改叠加身份发送事件包
			if(isset($upData['user_type'])){
				$bit = array();
				foreach ($upData['user_type'] as $v) $bit[] = intval($v);
				$newUserType = $userSer->grantMoreBit(0,$bit);
				unset($upData['user_type']);
				if ($newUserType != $oldUserType) $userJson['user_type'] = $newUserType;
			}
			
			//修改用户状态发送事件包
			if(isset($upData['user_status']) && is_numeric($upData['user_status'])){
				if ($oldUserStatus != $upData['user_status']){
					$userJson['user_status'] = $upData['user_status'];
			
					$oped = Yii::app()->request->getParam('oped');
					if(empty($oped['op_desc'])){
						return array('info' => array('操作理由不能为空'));
					}else{
						//存储被操作记录
						$records = array();
						$records['op_desc'] = $oped['op_desc'];
						$records['uid'] = $uid;
						$records['op_uid'] = $this->op_uid;
						$records['op_type'] = USER_OPERATED_TYPE_USERSTATUS;
						$records['op_value'] = $upData['user_status'];
						$userSer->saveUserOperated($records);
					}
				}
				unset($upData['user_status']);
			}
				
			//是否更改用户等级
			/* $consume = Yii::app()->request->getParam('consume',array());
			if($consume){
				$consume['uid'] = $uid;
				$consumeService = new ConsumeService();
				$consumeService->saveUserConsumeAttribute($consume);
			} */
			
			if (isset($upData['username'])) unset($upData['username']);
			
			if ($userJson) $userSer->saveUserJson($uid, $userJson);
			
			//更新用户的其它基本信息
			$upData['uid'] = $uid;
			if(count($upData) > 1){
				if(!$userSer->saveUserBasic($upData)) return $userSer->getNotice();
			}
			
			//是否默认创建直播间
			$_userType = isset($newUserType)?$newUserType:$oldUserType;
			if (isset($userType) && $userSer->hasBit($_userType, USER_TYPE_DOTEY)){
				$archives = Yii::app()->request->getParam('archives');
				if(empty($archives['cat_id'])) return array('info' => array('请选择档期类型'));
			
				//添加信息到主播表
				$dotey = $doteySer->getDoteyInfoByUid($uid);
				$doteyInfo = array();
				$doteyInfo['status'] = 1;
				$doteyInfo['uid'] = $uid;
				if(!$dotey){
					$doteyInfo['sign_type'] = SIGN_TYPE_SHOW;
					$doteyInfo['dotey_type'] = DOTEY_TYPE_DIRECT;
					$doteyInfo['create_time'] = time();
				}
				if(!$doteySer->saveUserDoteyBase($doteyInfo)) return $doteySer->getNotice();
			
				//批量添加档期
				if (!is_array($archives['cat_id'])) $archives['cat_id'] = array($archives['cat_id']);
				
				foreach ($archives['cat_id'] as $cat_id){
					$archivesInfo = array();
					$archivesInfo['uid'] = $uid;
					$archivesInfo['cat_id'] = $cat_id;
					if(isset($archives['title'])){
						$archivesInfo['title'] = $archives['title'];
					}
					if (isset($archives['is_hide']) && is_numeric($archives['is_hide'])) $archivesInfo['is_hide'] = $archives['is_hide'];
					
					$_info = $archivesSer->getArchivesBycondition(array('uid'=>$uid,'cat_id'=>$cat_id));
					if(!$_info){
						$archivesInfo['title'] = $uinfo['nickname'].'的直播间';
						$archivesInfo['create_time'] = time();
						$archivesSer->createArchives($archivesInfo);
					}else{
						$_info = array_shift($_info);
						$archivesInfo = array_merge($_info,$archivesInfo);
						$archivesSer->saveArchives($archivesInfo);
					}
				}
			}
			
			//是否删除或新增聊天进程
			if($newUserType && $oldUserType){
				$_out = $userSer->hasBit($oldUserType, USER_TYPE_DOTEY);
				$_nut = $userSer->hasBit($newUserType, USER_TYPE_DOTEY);
				if ($_out != $_nut){
					$plus = $_nut?true:false;#true：新增聊天进程，false:删除聊天进程
					$catInfo = $archivesSer->getAllArchiveCatByEnName('common');
					$_infos = $archivesSer->getArchivesBycondition(array('uid'=>$uid));
					if ($_infos){
						foreach($_infos as $info){
							$archives_id = $info['archives_id'];
							$archivesSer->changeChatServer($archives_id,$uid,$plus);
						}
					}
				}
			}
			
			//操作等级改变
			if($newUserType){
				$_out_admin = $userSer->hasBit(intval($oldUserType), USER_TYPE_ADMIN);
				$_nut_admin = $userSer->hasBit(intval($newUserType), USER_TYPE_ADMIN);
				if ($_nut_admin != $_out_admin){
					$plus = $_nut_admin?true:false;
					$archivesSer->saveGeneralManageJsonInfo($uid,$plus);#撤销或新增总管操作等级
				}
				$_out_dotey = $this->userSer->hasBit(intval($oldUserType), USER_TYPE_DOTEY);
				$_nut_dotey = $this->userSer->hasBit(intval($newUserType), USER_TYPE_DOTEY);
				if ($_nut_dotey != $_out_dotey){
					$plus = $_nut_dotey?true:false;
					$archivesInfo = $archivesSer->getArchivesBycondition(array('uid'=>$uid));
					if($archivesInfo){
						foreach ($archivesInfo as $archives){
							$archivesSer->saveDoteyPurviewRank($uid,$archives['archives_id'],$plus);#撤销或新增主播操作等级
						}
					}
					if(!$plus) $doteySer->delDoteyInfoFormRedis($uid);#删除dotey_info_uid信息
				}
			}
			
			//用户被禁用后，停播正在开播的直播间 并将现在的档期置隐藏
			if (isset($userJson['user_status'])){
				if($doteySer->getDoteyInfoByUid($uid)){
					if($userJson['user_status'] == USER_STATUS_OFF){
						$archivesInfo = $archivesSer->getArchivesBycondition(array('uid'=>$uid));
						if ($archivesInfo){
							$archivesIds =array_keys($archivesInfo);
							foreach ($archivesIds as $archivesId){
								$archivesSer->stopArchivesLive($uid, $archivesId);#停播处理
							}
							
							//将现有档期设置为隐藏
							foreach ($archivesInfo as $archives){
								$archives['is_hide'] = 1;
								$archivesSer->saveArchives($archives);
							}
							
							//撤消主播操作等级权限
							$plus = $userSer->hasBit(intval($oldUserType), USER_TYPE_DOTEY);
							if(!$plus){
								if($archivesInfo){
									foreach ($archivesInfo as $archives) $archivesSer->saveDoteyPurviewRank($uid,$archives['archives_id'],false);
								}
							}
							//删除dotey_info_uid信息
							$doteySer->delDoteyInfoFormRedis($uid);
						}
					}
				}
			}
			
			//广播状态
			$broadcast_status = Yii::app()->request->getParam('broadcast_status',-1);
			if($broadcast_status >=0){
				$broadcastService = new BroadcastService();
				if($broadcast_status){
					$broadcastService->saveBroadcastDisable($uid);
				}else{
					$broadcastService->deleteDisable($uid);
				}
			}
			
			if($redirectUrl) $this->redirect($redirectUrl);
		}
		return true;
	}
	
	
	/**
	 * 清除用户图像
	 * @param unknown_type $uid
	 */
	public function removeUserAvatar($uid){
		$userSer = new UserService();
		$avatar = $userSer->getAvatarUplaod()->getFileUrl($uid,'small');
		$avatar_use = $userSer->getUserAvatar($uid,'small');
		if ($avatar == $avatar_use){
			@unlink($userSer->getUserAvatar($uid,'small'));
			@unlink($userSer->getUserAvatar($uid,'middle'));
			@unlink($userSer->getUserAvatar($uid,'big'));
			$userSer->saveAdminOpLog('删除 用户图像(UID='.$uid.')',$uid);
			return '1';
		}else{
			return '2';
		}
		return '3';
	}
	
	/**
	 * 加载后台的通用Css
	 */
	public function assetsAdminCoreCss(){
		$cookie = Yii::app()->request->getCookies();
		$theme = isset($cookie['current_theme']) ?$cookie['current_theme']->value:'cerulean';
		/* @var $assetManager CAssetManager */
		$assetManager = Yii::app()->getAssetManager();
		$assetManager->excludeFiles = array('.svn','.gitignore');
		
		$static = $this->getPipiScriptPath();
// 		$staticPath = $assetManager->publish($static.'/css/admin');
		$staticPath = $this->getHost().$static.'/css/admin';
		$cssHash = sprintf('%x',crc32($staticPath.SOFT_VERSION));
		//第一个css文件必须是bootstrap-{theme_name}.css类型的文件
		$this->themeCssFile = $staticPath.'/bootstrap-'.$theme.'.css?token='.$cssHash;
		$this->cs->registerCssFile($this->themeCssFile);
		$this->cs->registerCssFile($staticPath.'/bootstrap-responsive.css?token='.$cssHash);
		$this->cs->registerCssFile($staticPath.'/charisma-app.css?token='.$cssHash);
		$this->cs->registerCssFile($staticPath.'/jquery-ui-1.9.2.custom.min.css?token='.$cssHash);
		//$this->cs->registerCssFile($staticPath.'/jquery-ui-1.8.21.custom.css?token='.$cssHash,'all');
		$this->cs->registerCssFile($staticPath.'/fullcalendar.css?token='.$cssHash);
		$this->cs->registerCssFile($staticPath.'/fullcalendar.print.css?token='.$cssHash);
		$this->cs->registerCssFile($staticPath.'/chosen.css?token='.$cssHash);
		$this->cs->registerCssFile($staticPath.'/uniform.default.css?token='.$cssHash);
		$this->cs->registerCssFile($staticPath.'/colorbox.css?token='.$cssHash);
		//$this->cs->registerCssFile($staticPath.'/jquery.cleditor.css?token='.$cssHash,'all');
		//$this->cs->registerCssFile($staticPath.'/jquery.noty.css?token='.$cssHash,'all');
		//$this->cs->registerCssFile($staticPath.'/noty_theme_default.css?token='.$cssHash,'all');
		$this->cs->registerCssFile($staticPath.'/elfinder.min.css?token='.$cssHash);
		$this->cs->registerCssFile($staticPath.'/elfinder.theme.css?token='.$cssHash);
		//$this->cs->registerCssFile($staticPath.'/jquery.iphone.toggle.css?token='.$cssHash,'all');
		$this->cs->registerCssFile($staticPath.'/opa-icons.css?token='.$cssHash);
		$this->cs->registerCssFile($staticPath.'/uploadify.css?token='.$cssHash);
		$this->cs->registerCssFile($staticPath.'/common.css?token='.$cssHash);
		$this->cssAssetsPath = $staticPath;
	}
	
	/**
	 * 加载皮皮后台通用JS资源
	 */
	public function assetsAdminCoreJs(){
		/* @var $assetManager CAssetManager */
		$assetManager = Yii::app()->getAssetManager();
		$assetManager->excludeFiles = array('.svn','.gitignore','images');
		
		$static = $this->getPipiScriptPath();
// 		$staticPath = $assetManager->publish($static.'/js/admin');
		$staticPath = $this->getHost().$static.'/js/admin';
		$jsHash = sprintf('%x',crc32($staticPath.SOFT_VERSION));
		
		//$this->cs->registerScriptFile($staticPath.'/jquery-ui-1.8.21.custom.min.js?token='.$jsHash,CClientScript::POS_HEAD );
		$this->cs->registerScriptFile($staticPath.'/jquery-ui-1.9.2.custom.min.js?token='.$jsHash,CClientScript::POS_HEAD );
		$this->cs->registerScriptFile($staticPath.'/bootstrap-transition.js?token='.$jsHash,CClientScript::POS_HEAD );
		$this->cs->registerScriptFile($staticPath.'/bootstrap-alert.js?token='.$jsHash,CClientScript::POS_HEAD );
		$this->cs->registerScriptFile($staticPath.'/bootstrap-modal.js?token='.$jsHash,CClientScript::POS_HEAD );
		$this->cs->registerScriptFile($staticPath.'/bootstrap-dropdown.js?token='.$jsHash,CClientScript::POS_HEAD );
		$this->cs->registerScriptFile($staticPath.'/bootstrap-scrollspy.js?token='.$jsHash,CClientScript::POS_HEAD );
		$this->cs->registerScriptFile($staticPath.'/bootstrap-tab.js?token='.$jsHash,CClientScript::POS_HEAD );
		$this->cs->registerScriptFile($staticPath.'/bootstrap-tooltip.js?token='.$jsHash,CClientScript::POS_HEAD );
		$this->cs->registerScriptFile($staticPath.'/bootstrap-popover.js?token='.$jsHash,CClientScript::POS_HEAD );
		$this->cs->registerScriptFile($staticPath.'/bootstrap-button.js?token='.$jsHash,CClientScript::POS_HEAD );
		$this->cs->registerScriptFile($staticPath.'/bootstrap-collapse.js?token='.$jsHash,CClientScript::POS_HEAD );
		$this->cs->registerScriptFile($staticPath.'/bootstrap-carousel.js?token='.$jsHash,CClientScript::POS_HEAD );
		$this->cs->registerScriptFile($staticPath.'/bootstrap-typeahead.js?token='.$jsHash,CClientScript::POS_HEAD );
		$this->cs->registerScriptFile($staticPath.'/bootstrap-tour.js?token='.$jsHash,CClientScript::POS_HEAD );
		//$this->cs->registerScriptFile($staticPath.'/jquery.cookie.js?token='.$jsHash,CClientScript::POS_HEAD );
		$this->cs->registerScriptFile($staticPath.'/fullcalendar.min.js?token='.$jsHash,CClientScript::POS_HEAD );
		$this->cs->registerScriptFile($staticPath.'/jquery.dataTables.min.js?token='.$jsHash,CClientScript::POS_HEAD );
		//$this->cs->registerScriptFile($staticPath.'/excanvas.js?token='.$jsHash,CClientScript::POS_HEAD );
		//$this->cs->registerScriptFile($staticPath.'/jquery.flot.min.js?token='.$jsHash,CClientScript::POS_HEAD );
		//$this->cs->registerScriptFile($staticPath.'/jquery.flot.pie.min.js?token='.$jsHash,CClientScript::POS_HEAD );
		//$this->cs->registerScriptFile($staticPath.'/jquery.flot.stack.js?token='.$jsHash,CClientScript::POS_HEAD );
		//$this->cs->registerScriptFile($staticPath.'/jquery.flot.resize.min.js?token='.$jsHash,CClientScript::POS_HEAD );
		$this->cs->registerScriptFile($staticPath.'/jquery.chosen.min.js?token='.$jsHash,CClientScript::POS_HEAD );
		$this->cs->registerScriptFile($staticPath.'/jquery.uniform.min.js?token='.$jsHash,CClientScript::POS_HEAD );
		$this->cs->registerScriptFile($staticPath.'/jquery.colorbox.min.js?token='.$jsHash,CClientScript::POS_HEAD );
		//$this->cs->registerScriptFile($staticPath.'/jquery.cleditor.min.js?token='.$jsHash,CClientScript::POS_HEAD );
		//$this->cs->registerScriptFile($staticPath.'/jquery.noty.js?token='.$jsHash,CClientScript::POS_HEAD );
		$this->cs->registerScriptFile($staticPath.'/jquery.elfinder.min.js?token='.$jsHash,CClientScript::POS_HEAD );
		$this->cs->registerScriptFile($staticPath.'/jquery.raty.min.js?token='.$jsHash,CClientScript::POS_HEAD );
		$this->cs->registerScriptFile($staticPath.'/jquery.iphone.toggle.js?token='.$jsHash,CClientScript::POS_HEAD );
		$this->cs->registerScriptFile($staticPath.'/jquery.autogrow-textarea.js?token='.$jsHash,CClientScript::POS_HEAD );
		$this->cs->registerScriptFile($staticPath.'/jquery.uploadify-3.1.min.js?token='.$jsHash,CClientScript::POS_HEAD );
		$this->cs->registerScriptFile($staticPath.'/jquery.history.js?token='.$jsHash,CClientScript::POS_HEAD );
		$this->cs->registerScriptFile($staticPath.'/charisma.js?token='.$jsHash,CClientScript::POS_HEAD );
		$this->jsAssetsPath = $staticPath;
	}
	
	/**
	 * 加载Yii核心资源
	 */
	public function assetsYiiCore(){
		$this->cs->registerCoreScript('jquery');
		$this->cs->registerCoreScript('cookie');
	}
	
	/**
	 * 加载google 2D绘图控件 基于HTML5 Y
	 */
	public function assetsGooleExcanvas(){
		$jsHash = sprintf('%x',crc32($this->jsAssetsPath.SOFT_VERSION));
		$this->cs->registerScriptFile($this->jsAssetsPath.'/excanvas.js?token='.$jsHash,CClientScript::POS_HEAD);
	}
	
	/**
	 * 加载JQuery 手机控件资源 
	 * 	CSS Y
	 * 	JSS N
	 */
	public function assetsJqueryIphone() {
		$cssHash = sprintf('%x',crc32($this->cssAssetsPath.SOFT_VERSION));
		$this->cs->registerCssFile($this->cssAssetsPath.'/jquery.iphone.toggle.css?token='.$cssHash,'all');
	
		$jsHash = sprintf('%x',crc32($this->jsAssetsPath.SOFT_VERSION));
		$this->cs->registerScriptFile($this->jsAssetsPath.'/jquery.iphone.toggle.js?token='.$jsHash,CClientScript::POS_HEAD);
	}
	
	/**
	 * 加载Jquery 评分控件资源 N
	 */
	public function assetsJqueryRaty() {
		$jsHash = sprintf('%x',crc32($this->jsAssetsPath.SOFT_VERSION));
		$this->cs->registerScriptFile($this->jsAssetsPath.'/jquery.raty.min.js?token='.$jsHash,CClientScript::POS_HEAD);
	}
	
	/**
	 * 加载Jquery 图表 控件资源 Y
	 */
	public function assetsJqueryFlot() {
		$jsHash = sprintf('%x',crc32($this->jsAssetsPath.SOFT_VERSION));
		$this->cs->registerScriptFile($this->jsAssetsPath.'/jquery.flot.min.js?token='.$jsHash,CClientScript::POS_HEAD);
		$this->cs->registerScriptFile($this->jsAssetsPath.'/jquery.flot.pie.min.js?token='.$jsHash,CClientScript::POS_HEAD);
		$this->cs->registerScriptFile($this->jsAssetsPath.'/jquery.flot.stack.js?token='.$jsHash,CClientScript::POS_HEAD);
		$this->cs->registerScriptFile($this->jsAssetsPath.'/jquery.flot.resize.min.js?token='.$jsHash,CClientScript::POS_HEAD);
	}
	
	/**
	 * 加载JQUERY 表单控件资源 N
	 */
	public function assetsJqueryForm() {
		$cssHash = sprintf('%x',crc32($this->cssAssetsPath.SOFT_VERSION));
		$this->cs->registerCssFile($this->cssAssetsPath.'/uniform.default.css?token='.$cssHash,'all');
		$this->cs->registerCssFile($this->cssAssetsPath.'/chosen.css?token='.$cssHash,'all');
	
		$jsHash = sprintf('%x',crc32($this->jsAssetsPath.SOFT_VERSION));
		$this->cs->registerScriptFile($this->jsAssetsPath.'/jquery.chosen.min.js?token='.$jsHash,CClientScript::POS_HEAD);
		$this->cs->registerScriptFile($this->jsAssetsPath.'/jquery.uniform.min.js?token='.$jsHash,CClientScript::POS_HEAD);
		$this->cs->registerScriptFile($this->jsAssetsPath.'/jquery.autogrow-textarea.js?token='.$jsHash,CClientScript::POS_HEAD);
	}
	
	/**
	 * 加载JQUERY 上传控件 资源 N
	 */
	public function assetsJqueryUpload() {
		$cssHash = sprintf('%x',crc32($this->cssAssetsPath.SOFT_VERSION));
		$this->cs->registerCssFile($this->cssAssetsPath.'/uploadify.css?token='.$cssHash,'all');
	
		$jsHash = sprintf('%x',crc32($this->jsAssetsPath.SOFT_VERSION));
		$this->cs->registerScriptFile($this->jsAssetsPath.'/jquery.uploadify-3.1.min.js?token='.$jsHash,CClientScript::POS_HEAD);
	}
	
	/**
	 * 加载JQUERY WEB文件管理器 资源 N
	 */
	public function assetsJqueryElfinder(){
		$cssHash = sprintf('%x',crc32($this->cssAssetsPath.SOFT_VERSION));
		$this->cs->registerCssFile($this->cssAssetsPath.'/elfinder.min.css?token='.$cssHash,'all');
		$this->cs->registerCssFile($this->cssAssetsPath.'/elfinder.theme.css?token='.$cssHash,'all');
	
		$jsHash = sprintf('%x',crc32($this->jsAssetsPath.SOFT_VERSION));
		$this->cs->registerScriptFile($this->jsAssetsPath.'/jquery.elfinder.min.js?token='.$jsHash,CClientScript::POS_HEAD);
	}
	
	/**
	 * 加载JQuery消息器 资源 Y
	 */
	public function assetsJqueryNoty(){
		$cssHash = sprintf('%x',crc32($this->cssAssetsPath.SOFT_VERSION));
		$this->cs->registerCssFile($this->cssAssetsPath.'/jquery.noty.css?token='.$cssHash,'all');
		$this->cs->registerCssFile($this->cssAssetsPath.'/noty_theme_default.css?token='.$cssHash,'all');
	
		$jsHash = sprintf('%x',crc32($this->jsAssetsPath.SOFT_VERSION));
		$this->cs->registerScriptFile($this->jsAssetsPath.'/jquery.noty.js?token='.$jsHash,CClientScript::POS_HEAD);
	}
	
	/**
	 * 加载JQuery编辑器 资源 N
	 */
	public function assetsJqueryEditor(){
		$cssHash = sprintf('%x',crc32($this->cssAssetsPath.SOFT_VERSION));
		$this->cs->registerCssFile($this->cssAssetsPath.'/jquery.cleditor.css?token='.$cssHash,'all');
	
		$jsHash = sprintf('%x',crc32($this->jsAssetsPath.SOFT_VERSION));
		$this->cs->registerScriptFile($this->jsAssetsPath.'/jquery.cleditor.min.js?token='.$jsHash,CClientScript::POS_HEAD);
	}
	
	/**
	 * 加载颜色选择器 资源 N
	 */
	public function assetsColorBox(){
		$cssHash = sprintf('%x',crc32($this->cssAssetsPath.SOFT_VERSION));
		$this->cs->registerCssFile($this->cssAssetsPath.'/colorbox.css?token='.$cssHash,'all');
	
		$jsHash = sprintf('%x',crc32($this->jsAssetsPath.SOFT_VERSION));
		$this->cs->registerScriptFile($this->jsAssetsPath.'/jquery.colorbox.min.js?token='.$jsHash,CClientScript::POS_HEAD );
	}
	
	/**
	 * 加载日历资源 N
	 */
	public function assetsCalendar(){
		$cssHash = sprintf('%x',crc32($this->cssAssetsPath.SOFT_VERSION));
		$this->cs->registerCssFile($this->cssAssetsPath.'/fullcalendar.css?token='.$cssHash,'all');
		$this->cs->registerCssFile($this->cssAssetsPath.'/fullcalendar.print.css?token='.$cssHash,'all');
	
		$jsHash = sprintf('%x',crc32($this->jsAssetsPath.SOFT_VERSION));
		$this->cs->registerScriptFile($this->jsAssetsPath.'/fullcalendar.min.js?token='.$jsHash,CClientScript::POS_HEAD );
	}
	
	/**
	 * JqueryUI 资源文件
	 */
	public function assetsJqueryUI(){
		/* @var $assetManager CAssetManager */
		$assetManager = Yii::app()->getAssetManager();
		$assetManager->excludeFiles = array('.svn','.gitignore');
	
		$jqueryUIJS = $assetManager->publish(Yii::getPathOfAlias('root.statics').'/js/jqueryUI');
		$jqueryUICSS = $assetManager->publish(Yii::getPathOfAlias('root.statics').'/css/jqueryUI');
		$jsHash = sprintf('%x',crc32($jqueryUIJS.SOFT_VERSION));
		$cssHash = sprintf('%x',crc32($jqueryUICSS.SOFT_VERSION));
	
		$this->cs->registerCssFile($jqueryUICSS.'/jquery-ui-1.9.2.custom.min.css?token='.$cssHash,'all');
		$this->cs->registerScriptFile($jqueryUIJS.'/jquery-1.8.3.js?token='.$jsHash,CClientScript::POS_HEAD );
		$this->cs->registerScriptFile($jqueryUIJS.'/jquery-ui-1.9.2.custom.min.js?token='.$jsHash,CClientScript::POS_HEAD );
	}
	
	/**
	 * 发布地区组件
	 */
	public function assetsArea(){
		$assetManager = Yii::app()->getAssetManager();
		$assetManager->excludeFiles = array('.svn','.gitignore');
		$staticPath = $this->getPipiScriptPath();
// 		$areaJS = $assetManager->publish($staticPath.'/js/area');
		$areaJS = $staticPath.'/js/area';
		$jsHash = sprintf('%x',crc32($areaJS.SOFT_VERSION));
		$this->cs->registerScriptFile($areaJS.'/city_data.js?token='.$jsHash);
		$this->cs->registerScriptFile($areaJS.'/datajs.js?token='.$jsHash);
	}
	
	/**
	 * 发布My97Date日历组件
	 */
	public function assetsMy97Date(){
		$assetManager = Yii::app()->getAssetManager();
		$assetManager->excludeFiles = array('.svn','.gitignore');
		$staticPath = $this->getPipiScriptPath();
// 		$dateJs = $assetManager->publish($staticPath.'/utils/My97DatePicker');
		$dateJs = $staticPath.'/utils/My97DatePicker';
		$jsHash = sprintf('%x',crc32($dateJs.SOFT_VERSION));
		$this->cs->registerScriptFile($dateJs.'/WdatePicker.js?token='.$jsHash, CClientScript::POS_END);
	}
	
	/**
	 * 发布CKEditor编辑器组件
	 */
	public function assetsCKEditor(){
		$assetManager = Yii::app()->getAssetManager();
		$assetManager->excludeFiles = array('.svn','.gitignore');
		$staticPath = $this->getPipiScriptPath();
// 		$dateJs = $assetManager->publish($staticPath.'/utils/CKEditor');
		$dateJs = $staticPath.'/utils/CKEditor';
		$jsHash = sprintf('%x',crc32($dateJs.SOFT_VERSION));
		$this->cs->registerScriptFile($dateJs.'/ckeditor.js?token='.$jsHash, CClientScript::POS_HEAD);
		$this->cs->registerScriptFile($dateJs.'/config.js?token='.$jsHash, CClientScript::POS_HEAD);
	}
	
	public function assetsGChart(){
		$assetManager = Yii::app()->getAssetManager();
		$assetManager->excludeFiles = array('.svn','.gitignore');
		$staticPath = $this->getPipiScriptPath();
// 		$dateJs = $assetManager->publish($staticPath.'/js/common');
		$dateJs = $staticPath.'/js/common';
		$jsHash = sprintf('%x',crc32($dateJs.SOFT_VERSION));
		$this->cs->registerScriptFile($dateJs.'/jquery.gchart.js?token='.$jsHash, CClientScript::POS_HEAD);
	}
	
	/**
	 * 获取静态文件的域名
	 * @return string
	 */
	private function getHost(){
		$config = Yii::app()->params['images_server'];
		$host = '';
		if($config['cdn_open']){
			if(SOFT_VERSION != 'v1.0.0.1'){
				$time = strtotime('20'.SOFT_VERSION.'00');
				if(DEV_ENVIRONMENT == 'release' && time() - $time > 3 * 3600) $host = $config['cdn_url'];
			}
		}
		return $host;
	}
}
