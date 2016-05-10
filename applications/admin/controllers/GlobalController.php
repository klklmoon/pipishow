<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su Peng <594524924@qq.com>
 * @version $Id: UserController.php 8317 2013-03-29 01:19:47Z suqian $ 
 * @package 
 */
class GlobalController extends PipiAdminController {

	/**
	 * @var UserService 道具服务层
	 */
	public $userSer;
	
	/**
	 * @var array 允许的操作
	 */
	public $allowOp = array('showUinfo','updateUinfo','dlSendGiftExcel');
	
	/**
	 * @var string 当前操作
	*/
	public $op;
	
	/**
	 * @var boolean 是否是Ajax请求
	 */
	public $isAjax;
	
	public $pageSize = 20;
	
	public $offset;
	
	/**
	 * @var int page lable
	 */
	public $p;
	
	protected static $wordService;
	
	public function init(){
		parent::init();
		$this->userSer = new UserService();
		$this->op = Yii::app()->request->getParam('op');
		$this->isAjax = Yii::app()->request->isAjaxRequest;
		if(!($this->p = Yii::app()->request->getParam('page'))){
			$this->p = 1;
		}
		$this->offset = ($this->p -1)*$this->pageSize;
	}
	
	/**
	 * 直播服务器列表
	 */
	public function actionServicelist()
	{
		$archivesService = new ArchivesService();
		$list = $archivesService->getLiveServer();
		$this->render('servList',array('list'=>$list));
	}

	/**
	 * 添加直播服务器
	 */
	public function actionAddService(){
		$ajaxReturn = array('result'=>false, 'msg'=>'操作失败, 请稍后再试','data'=>array());
		if(Yii::app()->request->isAjaxRequest){
			$doteyServ = Yii::app()->request->getParam('doteyServ');
			$userServ = Yii::app()->request->getParam('userServ');
			$server = array('import_host'=>$doteyServ, 'export_host'=>$userServ);
			
			$archivesService = new ArchivesService();
			$serverId = $archivesService->saveLiveServer($server);
			if($serverId >0){
				$server = $archivesService->getLiveServerByServerIds(array($serverId));
				if($server[$serverId] && $doteyServ = $server[$serverId]['import_host'] && $doteyServ = $server[$serverId]['export_host']){
					$ajaxReturn['result'] = true;
					$ajaxReturn['msg'] = '操作成功';
					$ajaxReturn['data'] = $server[$serverId];
					exit(json_encode($ajaxReturn));
				}
			}
		}
		exit(json_encode($ajaxReturn));
	}
	
	/**
	 * 编辑直播服务器
	 */
	public function actionEditServerList()
	{
		$ajaxReturn = array('result'=>false, 'msg'=>'操作失败, 请稍后再试','data'=>array());
		if(Yii::app()->request->isAjaxRequest){
			$id = Yii::app()->request->getParam('id');
			$doteyServ = Yii::app()->request->getParam('doteyServ');
			$userServ = Yii::app()->request->getParam('userServ');
			if($id){
				$server = array('server_id'=>$id, 'import_host'=>$doteyServ, 'export_host'=>$userServ);
			}else{
				$server = array('import_host'=>$doteyServ, 'export_host'=>$userServ);
			}
			$archivesService = new ArchivesService();
			$serverId = $archivesService->saveLiveServer($server);
			if($serverId >0){
				$server = $archivesService->getLiveServerByServerIds(array($serverId));
				if($server[$serverId] && $doteyServ = $server[$serverId]['import_host'] && $doteyServ = $server[$serverId]['export_host']){
					$ajaxReturn['result'] = true;
					$ajaxReturn['msg'] = '操作成功';
					$ajaxReturn['data'] = $server[$serverId];
					exit(json_encode($ajaxReturn));
				}
				
			}
		}
		exit(json_encode($ajaxReturn));
	}
	
	/**
	 * 发主敏感词
	 */
	public function actionChatword()
	{
		$condition = array();
		$wordService = $this->getWordService();
		
		$wordList = $wordService->getChatWord(true);
		$count = count($wordList);
		$offset = $this->offset;
		$limit = $this->pageSize;
		$list = array_slice($wordList, $offset, $limit, true);
		
		$pager = new CPagination($count);
		$pager->pageSize = $this->pageSize;
		$pager->params = $condition;
		
		$this->render('chatWord', array('pager'=>$pager,'list'=>$list,'condition'=>$condition));
		
	}
	
	/**
	 * 添加, 编辑, 删除敏感词
	 */
	public function actionEditChatWord()
	{
		$uid = Yii::app()->user->getId();
		$ajaxReturn = array('result'=>false, 'msg'=>'操作失败, 请稍后再试','data'=>array());
		if(Yii::app()->request->isAjaxRequest){
			$id = Yii::app()->request->getParam('id');
			$name = Yii::app()->request->getParam('name');
			$type = isset($_POST['replace_type']) ? Yii::app()->request->getParam('replace_type') : '1';
			$replace = isset($_POST['replace']) ? Yii::app()->request->getParam('replace') : '*';
			$word_type = isset($_POST['word_type']) ? Yii::app()->request->getParam('word_type') : 0;
			$isAdd = Yii::app()->request->getParam('isAdd');
			$isDel = Yii::app()->request->getParam('isDel');

			$wordService = $this->getWordService();
			if($isAdd && $isAdd=='1'){
				$res = $wordService->getChatWordByAttribute(array('name'=>$name, 'word_type'=>$word_type));
				if($res){
					$ajaxReturn['result'] = false;
					$ajaxReturn['msg'] = '该敏感词已存在';
					$ajaxReturn['data'] = $res;
					$res['status'] = '1';
					$wordService->saveCharWord($res,$uid,$word_type);
					exit(json_encode($ajaxReturn));
				}
				$word['name'] = $name;
				$word['type'] = $type;
				$word['replace'] = $replace;
				$word['id'] = 0;
				$word['word_type'] = $word_type;
				$res = $wordService->saveCharWord($word, $uid, $word_type);
				if($res){
					$ajaxReturn['result'] = true;
					$ajaxReturn['msg'] = '操作成功';
					$ajaxReturn['data'] = $word;
					exit(json_encode($ajaxReturn));
				}
			}else{
				if($id >= 0){
					if($word_type==0){
						$wordList = $wordService->getChatWord(true);
						if(isset($wordList[$id])){
							$word = $wordList[$id];
							$word['id'] = $id;
							if($isDel){
								$word['status'] = '0';
							}else{
								$word['name'] = $name;
								$word['type'] = $type;
								$word['replace'] = $replace;
							}
							$res = $wordService->saveCharWord($word, $uid);
							if($res){
								$ajaxReturn['result'] = true;
								$ajaxReturn['msg'] = '操作成功';
								$ajaxReturn['data'] = $word;
								exit(json_encode($ajaxReturn));
							}
						}
					}else{
						$word['id'] = $id;
						if($isDel){
							$word['status'] = '0';
						}else{
							$word['name'] = $name;
						}
						$res = $wordService->saveCharWord($word, $uid, $word_type);
						if($res){
							$ajaxReturn['result'] = true;
							$ajaxReturn['msg'] = '操作成功';
							$ajaxReturn['data'] = $word;
							exit(json_encode($ajaxReturn));
						}
					}
				}
				$ajaxReturn['msg'] = '错误ID, 该敏感词不存在';
			}
		}
		exit(json_encode($ajaxReturn));
	}
	
	/**
	 * 主播与用户等级
	 */
	public function actionLevelSetup()
	{
		$consumeService = new ConsumeService();
		$userList = $consumeService->getAllUserRanks();
		$doteyList = $consumeService->getDoteyAllRank();
		
		$data = array('user_list'=>$userList, 'dotey_list'=>$doteyList);
		$this->render('levelSetup',$data);
	}
	
	/**
	 * 编辑主播或用户等级
	 */
	public function actionEditLevel()
	{
		$ajaxReturn = array('result'=>false, 'msg'=>'操作失败, 请稍后再试','data'=>array());
		if(Yii::app()->request->isAjaxRequest){
			$type = Yii::app()->request->getParam('type');
			$consumeService = new ConsumeService();
			if($type=='user'){
				$userLevel = array();
				if(Yii::app()->request->getParam('ac')=='del'){
					// 删除
					$rankId = Yii::app()->request->getParam('id');
					$res = $consumeService->deleteUserRank($rankId);
				}else{
					if(Yii::app()->request->getParam('id') > 0){
						$userLevel['rank_id'] = Yii::app()->request->getParam('id');
					}
					$userLevel['rank'] = Yii::app()->request->getParam('user_level');
					$userLevel['name'] = Yii::app()->request->getParam('user_name');
					$userLevel['dedication'] = Yii::app()->request->getParam('user_dedication');
					$userLevel['house_m_num'] = Yii::app()->request->getParam('user_house_m_num');
					$res = $consumeService->saveUserRank($userLevel);
				}
				if($res){
					$ajaxReturn['result'] = true;
					$ajaxReturn['msg'] = '操作成功';
					$ajaxReturn['data'] = $userLevel;
				}
			}elseif($type=='dotey'){
				$doteyLevel = array();
				if(Yii::app()->request->getParam('ac')=='del'){
					// 删除
					$rankId = Yii::app()->request->getParam('id');
					$res = $consumeService->deleteDoteyRank($rankId);
				}else{
					if(Yii::app()->request->getParam('id') > 0){
						$doteyLevel['rank_id'] = Yii::app()->request->getParam('id');
					}
					$doteyLevel['rank'] = Yii::app()->request->getParam('dotey_level');
					$doteyLevel['name'] = Yii::app()->request->getParam('dotey_name');
					$doteyLevel['charm'] = Yii::app()->request->getParam('dotey_dedication');
					$doteyLevel['house_m_num'] = Yii::app()->request->getParam('dotey_house_m_num');
					$doteyLevel['divieded_scale'] = Yii::app()->request->getParam('dotey_divieded_scale');
					$doteyLevel['divieded_rate'] = Yii::app()->request->getParam('dotey_divieded_rate');
					
					$ajaxReturn['data'] = $doteyLevel;
					$res = $consumeService->saveDoteyRank($doteyLevel);
				}
				if($res){
					$ajaxReturn['result'] = true;
					$ajaxReturn['msg'] = '操作成功';
					$ajaxReturn['data'] = $doteyLevel;
				}
			}else{
				$ajaxReturn['msg'] = '非法操作!!!';
			}
		}
		exit(json_encode($ajaxReturn));
	}
	
	/**
	 * 统计代码设置
	 */
	public function actionWebCount()
	{
		$webSiteService = new WebConfigService();
		if(Yii::app()->request->isPostRequest){
			$c_value = array(
					'content'=>(Yii::app()->request->getParam('content')),
					'time'=>time(),
					'uid'=>Yii::app()->user->getId(),
				);
			$config = array(
					'c_key'=>WEB_SITE_COUNT,
					'c_type'=>'array',
					'c_value'=>$c_value,	
				);
			$webSiteService->saveWebConfig($config);
		}
		$webSite = $webSiteService->getWebConfig(WEB_SITE_COUNT);
		$web_config = isset($webSite['c_value']) ? $webSite['c_value'] : array();
		$this->render('webCount', array('web_config'=>$web_config));
	}
	
	/**
	 * 昵称敏感词
	 */
	public function actionNickWord()
	{
		
		$wordService = new WordService();
		$res = $wordService->getNickNameBadWord();
		$count = $res['count'];
		$list = $res['list'];
		
		$pager = new CPagination($count);
		$pager->pageSize = $this->pageSize;
		
		$this->render('nickWord', array('pager'=>$pager,'list'=>$list));
	}
	
	/**
	 * 注册防灌设置
	 */
	public function actionRegister()
	{
		$webSiteService = new WebConfigService();
		if(Yii::app()->request->isAjaxRequest){
			$ajaxReturn = array('result'=>false, 'msg'=>'操作失败, 请稍后再试');
			(int) $minute = Yii::app()->request->getParam('minute');
			(int) $rate = Yii::app()->request->getParam('rate');
			$ip = Yii::app()->request->getParam('ip');
			$type = Yii::app()->request->getParam('type');
			$del = Yii::app()->request->getParam('del');
			$key = Yii::app()->request->getParam('key');
			
			if($type=='1'){
				$config = array(
					'c_key'=>WEB_REGISTER_SITE,
					'c_type'=>'array',
					'c_value'=>array('minute'=>($minute > 0 ? $minute : 10),
								'rate'=>($rate > 0 ? $rate : 3)),
				);
			}elseif($type=='2'){
				$badIpInfo = $webSiteService->getWebConfig(WEB_BAD_IP);
				$cvalue = $badIpInfo['c_value'] ? $badIpInfo['c_value'] : array();
				if($key>=0 && isset($cvalue[$key])){
					if($del==1){
						unset($cvalue[$key]);
					}else{
						$cvalue[$key] = $ip;
					}
				}else{
					$cvalue = array_unique(array_merge($cvalue, array($ip)));
				}
				$cvalue = array_unique($cvalue);
				$config = array(
						'c_key'=>WEB_BAD_IP,
						'c_type'=>'array',
						'c_value'=>$cvalue,
				);
			}else{
				exit(json_encode($ajaxReturn));
			}
			$res = $webSiteService->saveWebConfig($config);
			if($res){
				$ajaxReturn['result'] = true;
				$ajaxReturn['msg'] = '操作成功';
			}
			exit(json_encode($ajaxReturn));
		}
		
		$badIpInfo = $webSiteService->getWebConfig(WEB_BAD_IP);
		$badIps = isset($badIpInfo['c_value']) ? $badIpInfo['c_value'] : array();
		
		$registerSite = $webSiteService->getWebConfig(WEB_REGISTER_SITE);
		$reg_config = isset($registerSite['c_value']) ? $registerSite['c_value'] : array();
		$this->render('registerSite', array('reg_config'=>$reg_config, 'bad_ip'=>$badIps));
	}
	
	/**
	 * 礼物消息推送
	 * 
	 * key=WEB_GIFT_MSG_PUSH
	 */
	public function actionGiftMsgPush(){
		$webConfSer = new WebConfigService();
		$c_key = $webConfSer->getGiftMsgPushKey();
		
		$formMsg = Yii::app()->request->getParam('msg',false);
		$notices = null;
		if($formMsg){
			if (empty($formMsg['private']) || empty($formMsg['global'])){
				$notices = '输入的信息不完整';
			}else if(!is_numeric($formMsg['private']) || !is_numeric($formMsg['global'])){
				$notices = '输入的值必须为数字类型';
			}else{
				$data = array();
				$data['c_key'] = $c_key;
				$data['c_value'] = $formMsg;
				$data['c_type'] = 'array';
				if(!$webConfSer->saveWebConfig($data)){
					$notices = '保存配置信息失败';
				}
			}
		}
		$keyInfo = $webConfSer->getWebConfig($c_key);
		$this->render('gift_msg_push',array('keyInfo'=>$keyInfo['c_value'],'notices'=>$notices));
	}
	
	public function actionFace(){
		$condition = array();
		$offset = $this->offset;
		$limit = $this->pageSize;
		$faceService=new FaceService();
		$list=$faceService->getAllFace();
		$count = count($list);
		$list = array_slice($list, $offset, $limit, true);
		$pager = new CPagination($count);
		$pager->pageSize = $this->pageSize;
		$pager->params = $condition;
		$this->render('face',array('pager'=>$pager,'list'=>$list,'condition'=>$condition));
	}
	
	public function actionAddFace(){
		$ajaxReturn = array('result'=>false, 'msg'=>'操作失败, 请稍后再试','data'=>array());
		if(Yii::app()->request->isAjaxRequest){
			$name=Yii::app()->request->getParam('name');
			$type=Yii::app()->request->getParam('type');
			$image=Yii::app()->request->getParam('image');
			$displayorder=Yii::app()->request->getParam('displayorder');
			$faceService=new FaceService();
			$face['name']=$name;
			$face['type']=$type?$type:'common';
			$face['code']='['.$name.']';
			$face['image']=$image;
			$face['displayorder']=$displayorder?$displayorder:0;
			$result=$faceService->saveFace($face);
			if($result>0){
				$ajaxReturn['result']=true;
				$ajaxReturn['msg']='表情添加成功';
				$faceType=$faceService->getFaceType($type);
				$face['type']=$faceType[$type];
				$ajaxReturn['data']=$face;
			}
		}
		exit(json_encode($ajaxReturn));
	}
	
	public function actioneditFace(){
		$ajaxReturn = array('result'=>false, 'msg'=>'操作失败, 请稍后再试','data'=>array());
		if(Yii::app()->request->isAjaxRequest){
			$id=Yii::app()->request->getParam('id');
			$name=Yii::app()->request->getParam('name');
			$type=Yii::app()->request->getParam('type');
			$image=Yii::app()->request->getParam('image');
			$displayorder=Yii::app()->request->getParam('displayorder');
			$faceService=new FaceService();
			$face['id']=$id;
			$face['name']=$name;
			$face['type']=$type?$type:'common';
			$face['code']='['.$name.']';
			$image&&$face['image']=$image;
			$face['displayorder']=$displayorder?$displayorder:0;
			$result=$faceService->saveFace($face);
			if($result>0){
				$ajaxReturn['result']=true;
				$ajaxReturn['msg']='表情修改成功';
				$faceType=$faceService->getFaceType($type);
				$face['type']=$faceType[$type];
				$image&&$face['image']='/statics/fontimg/express/'.$type.'/'.$image;
				$ajaxReturn['data']=$face;
			}
		}
		exit(json_encode($ajaxReturn));
	}
	
	public function actionDelFace(){
		$ajaxReturn = array('result'=>false, 'msg'=>'操作失败, 请稍后再试','data'=>array());
		if(Yii::app()->request->isAjaxRequest){
			$id=Yii::app()->request->getParam('id');
			$faceService=new FaceService();
			$result=$faceService->delFaceByIds(array($id));
			if($result>0){
				$ajaxReturn['result']=true;
				$ajaxReturn['msg']='表情删除成功';
			}
		}
		exit(json_encode($ajaxReturn));
	}
	
	public function getWordService()
	{
		if(!self::$wordService){
			self::$wordService = new WordService();
		}
		return self::$wordService;
	}
}

?>