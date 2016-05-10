<?php
class IndexController extends PipiAdminController {

	/**
	 * @var OperateService 道具服务层
	 */
	public $operateSer;
	
	/**
	 * @var array 允许的操作
	 */
	public $allowOp = array('checkDoteyInfo', 'addHomeWindow', 'delOperate', 'addActivityRmd', 'addSideRmd', 
		'addSiteStar', 'addTodayRmd', 'addLivePush', 'addNewsNoticeRmd','addHomeCarousel','addSongCarousel','addSongNoticeRmd','addNewDotey');
	
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
	
	public function init(){
		parent::init();
		$this->operateSer = new OperateService();
		$this->op = Yii::app()->request->getParam('op');
		$this->isAjax = Yii::app()->request->isAjaxRequest;
		if(!($this->p = Yii::app()->request->getParam('page'))){
			$this->p = 1;
		}
		$this->offset = ($this->p -1)*$this->pageSize;
	}
	
	/**
	 * 默认欢迎页
	 */
	public function actionIndex(){
		//列出已经拥有的操作项
		$this->render('index');
	}

	/**
	 * 侧边栏推荐一，新秀主播版
	 */
	public function actionSideRmd(){
		//检查主播信息的合法性
		if($this->op == 'checkDoteyInfo' && in_array($this->op,$this->allowOp)){
			$this->checkDoteyInfo();
		}
		
		//是否是添加修改活动推荐
		if($this->op == 'addSideRmd' && in_array($this->op,$this->allowOp)){
			$this->addSideRmdDo();
		}
		
		//是否是删除侧栏推荐
		if($this->op == 'delOperate' && in_array($this->op,$this->allowOp)){
			$this->delOperateDo();
		}
		
		$sideData = $this->operateSer->getOperateByCategory(CATEGORY_INDEX,CATEGORY_INDEX_COLUMNSRECOMMAND);
		$result = $this->operateSer->getIndexRightData(INDEX_RIGHT_DATA_TYPE_ROOKIEDOTEY);
		$sideRightData = array();
		if($result){
			$uids = array_keys($result);
			$userSer = new UserService();
			$sideRightData = $userSer->getUserBasicByUids($uids);
		}
		$forum_desc = $this->operateSer->getIndexRightDataForPookieDotey();
		$this->render('index_side_rmd',array('sideData'=>$sideData,'sideRightData'=>$sideRightData,'forum_desc'=>$forum_desc));
	}
	
	/**
	 * 侧边栏推荐二  最新加入版块
	 */
	public function actionNewDotey(){
		//检查主播信息的合法性
		if($this->op == 'checkDoteyInfo' && in_array($this->op,$this->allowOp)){
			$this->checkDoteyInfo();
		}
		
		//是否是添加修改活动推荐
		if($this->op == 'addNewDotey' && in_array($this->op,$this->allowOp)){
			$this->addNewDoteyDo();
		}
		
		//是否是删除侧栏推荐
		if($this->op == 'delOperate' && in_array($this->op,$this->allowOp)){
			$this->delOperateDo();
		}
		
		$sideData = $this->operateSer->getOperateByCategory(CATEGORY_INDEX,CATEGORY_INDEX_NEWDOTEY);
		$result = $this->operateSer->getIndexRightData(INDEX_RIGHT_DATA_TYPE_NEWDOTEY);
		$sideRightData = array();
		if($result){
			$uids = array_keys($result);
			$userSer = new UserService();
			$sideRightData = $userSer->getUserBasicByUids($uids);
		}
		$forum_desc = $this->operateSer->getIndexRightDataForNewDotey();
		$this->render('index_new_dotey',array('sideData'=>$sideData,'sideRightData'=>$sideRightData,'forum_desc'=>$forum_desc));
	}
	
	/**
	 * 侧栏推荐三 明星主播版
	 */
	public function actionSiteStar(){
		//检查主播信息的合法性
		if($this->op == 'checkDoteyInfo' && in_array($this->op,$this->allowOp)){
			$this->checkDoteyInfo();
		}
	
		//是否是添加修改首页厨窗
		if($this->op == 'addSiteStar' && in_array($this->op,$this->allowOp)){
			$this->addSiteStarDo();
		}
	
		//是否是删除首页厨窗
		if($this->op == 'delOperate' && in_array($this->op,$this->allowOp)){
			$this->delOperateDo();
		}
	
		$starData = $this->operateSer->getOperateByCategory(CATEGORY_INDEX,CATEGORY_INDEX_STARCOLLEGE);
		$result = $this->operateSer->getIndexRightData(INDEX_RIGHT_DATA_TYPE_STARDOTEY);
		$sideRightData = array();
		if($result){
			$uids = array_keys($result);
			$userSer = new UserService();
			$sideRightData = $userSer->getUserBasicByUids($uids);
		}
		$forum_desc = $this->operateSer->getIndexRightDataForStarDotey();
		//$doteyInfo = $this->getHomeWindowDoteyInfo($starData);
		$this->render('index_site_star',array('starData'=>$starData,'sideRightData'=>$sideRightData,'forum_desc'=>$forum_desc));
	}
	
	/**
	 * 活动推荐 专栏推荐
	 */
	public function actionActivityRmd(){
		//是否是添加修改活动推荐
		if($this->op == 'addActivityRmd' && in_array($this->op,$this->allowOp)){
			$this->addActivityRmdDo();
		}
		
		//是否是删除首页厨窗
		if($this->op == 'delOperate' && in_array($this->op,$this->allowOp)){
			$this->delOperateDo();
		}
		
		$activeData = $this->operateSer->getOperateByCategory(CATEGORY_INDEX,CATEGORY_INDEX_ACTIVITYRECOMMAND);
		$this->render('index_active_rmd',array('activeData'=>$activeData));
	}
	
	/**
	 * 首页厨窗
	 */
	public function actionHomeWindow(){
		//检查主播信息的合法性
		if($this->op == 'checkDoteyInfo' && in_array($this->op,$this->allowOp)){
			$this->checkDoteyInfo();
		}
		
		//是否是添加修改首页厨窗
		if($this->op == 'addHomeWindow' && in_array($this->op,$this->allowOp)){
			$this->addHomeWindowDo();	
		}
		
		//是否是删除首页厨窗
		if($this->op == 'delOperate' && in_array($this->op,$this->allowOp)){
			$this->delOperateDo();
		}
		
		$homeWindow = $this->operateSer->getOperateByCategory(CATEGORY_INDEX,CATEGORY_INDEX_SHOWCASE);
		$doteyInfo = $this->getHomeWindowDoteyInfo($homeWindow);
		$this->render('index_home_window',array('homeData'=>$homeWindow,'doteyInfo'=>$doteyInfo));
	}
	
	/**
	 * 今日推荐
	 */
	public function actionTodayRmd(){
		//检查主播信息的合法性
		if($this->op == 'checkDoteyInfo' && in_array($this->op,$this->allowOp)){
			$this->checkDoteyInfo();
		}
		
		//是否是添加修改活动推荐
		if($this->op == 'addTodayRmd' && in_array($this->op,$this->allowOp)){
			$this->addTodayRmdDo();
		}
		
		//是否是删除侧栏推荐
		if($this->op == 'delOperate' && in_array($this->op,$this->allowOp)){
			$this->delOperateDo();
		}
		
		$todayData = $this->operateSer->getOperateByCategory(CATEGORY_INDEX,CATEGORY_INDEX_TODAYRECOMMAND);
		//$doteyInfo = $this->getHomeWindowDoteyInfo($todayData);
		$this->render('index_today_rmd',array('todayData'=>$todayData));
	}
	
	/**
	 * 首页强推（直播强推）
	 */
	public function actionLivePush(){
		//检查主播信息的合法性
		if($this->op == 'checkDoteyInfo' && in_array($this->op,$this->allowOp)){
			$this->checkDoteyInfo();
		}
		
		//是否是添加修改活动推荐
		if($this->op == 'addLivePush' && in_array($this->op,$this->allowOp)){
			$this->addLivePushDo();
		}
		
		//是否是删除侧栏推荐
		if($this->op == 'delOperate' && in_array($this->op,$this->allowOp)){
			$this->delOperateDo();
		}
		
		
		$livePushData = $this->operateSer->getOperateByCategory(CATEGORY_INDEX,CATEGORY_INDEX_LIVESRECOMMAND);
		$doteyInfo = $this->getHomeWindowDoteyInfo($livePushData);
		
		$webConfigSer = new WebConfigService();
		$cvalue = $webConfigSer->getLivePush();
		$this->render('index_live_push',array('livePushData'=>$livePushData,'doteyInfo'=>$doteyInfo,'cvalue'=>$cvalue));
	}
	
	/**
	 * 首页公告推荐
	 */
	public function actionNewsNoticeRmd(){
		//是否是添加修改活动推荐
		if($this->op == 'addNewsNoticeRmd' && in_array($this->op,$this->allowOp)){
			$this->addNewsNoticeRmdDo();
		}
		
		//是否有新增
		$info = array();
		if($threadId = Yii::app()->request->getParam('threadId')){
			$bbsSer = new BbsbaseService();
			if(!($info = $bbsSer->getThreadInfo($threadId))){
				exit("获取信息失败");
			}
		}
		
		//是否是删除首页厨窗
		if($this->op == 'delOperate' && in_array($this->op,$this->allowOp)){
			$this->delOperateDo();
		}
	
		$newsNoticeData = $this->operateSer->getOperateByCategory(CATEGORY_INDEX,CATEGORY_INDEX_NEWSNOTICE);
		
		if($this->isAjax){
			exit($this->renderPartial('index_news_notice_rmd',array('newsNoticeData'=>$newsNoticeData,'info'=>$info)));
		}
		$this->render('index_news_notice_rmd',array('newsNoticeData'=>$newsNoticeData,'info'=>$info));
	}
	
	/**
	 * 唱区公告推荐
	 */
	public function actionSongNoticeRmd(){
		//是否是添加修改活动推荐
		if($this->op == 'addSongNoticeRmd' && in_array($this->op,$this->allowOp)){
			$this->addSongNoticeRmdDo();
		}
	
		//是否有新增
		$info = array();
		if($threadId = Yii::app()->request->getParam('threadId')){
			$bbsSer = new BbsbaseService();
			if(!($info = $bbsSer->getThreadInfo($threadId))){
				exit("获取信息失败");
			}
		}
	
		//是否是删除首页厨窗
		if($this->op == 'delOperate' && in_array($this->op,$this->allowOp)){
			$this->delOperateDo();
		}
	 
		$newsNoticeData = $this->operateSer->getOperateByCategory(CATEGORY_CHANNEL,CATEGORY_CHANNEL_SONG_NOTICE);
		
		if($this->isAjax){
			exit($this->renderPartial('index_song_notice_rmd',array('newsNoticeData'=>$newsNoticeData,'info'=>$info)));
		}
		$this->render('index_song_notice_rmd',array('newsNoticeData'=>$newsNoticeData,'info'=>$info));
	}
	
	/**
	 * 首页轮播  本站明星
	 */
	public function actionHomeCarousel(){
		//检查主播信息的合法性
		if($this->op == 'checkDoteyInfo' && in_array($this->op,$this->allowOp)){
			$this->checkDoteyInfo();
		}
		
		//是否是添加修改活动推荐
		if($this->op == 'addHomeCarousel' && in_array($this->op,$this->allowOp)){
			if(isset($_FILES['doteyinfo'])){
				//105,211,230 三种尺寸
				$_file = $_FILES['doteyinfo']['name']['display_big'];
				$imageUpload = new PipiImageUpload();
				$uids = array();
				foreach($_file as $uid=>$file){
					if($file){
						$src = $_FILES['doteyinfo']['tmp_name']['display_big'][$uid];
						$imageUpload->setDir($uid,'dotey', 'dynamic_big');
						$filePath = $imageUpload->getFile();
						$imageUpload->makeThumb($src, $filePath, 230, 230, false);
						$imageUpload->setDir($uid,'dotey', 'dynamic_middle');
						$filePath = $imageUpload->getFile();
						$imageUpload->makeThumb($src, $filePath, 211, 211, false);
						$imageUpload->setDir($uid,'dotey', 'dynamic_small');
						$filePath = $imageUpload->getFile();
						$imageUpload->makeThumb($src, $filePath, 105, 105, false);
						$uids[$uid] = $uid;
					}
				}
				
				if($uids){
					$form = 'doteyinfo';
					//上传主播大图
					//$this->operateSer->uploadDoteyDisplayBig($form, $uids);
				}
			}
			$this->addHomeCarouselDo();
		}
		
		//是否是删除侧栏推荐
		if($this->op == 'delOperate' && in_array($this->op,$this->allowOp)){
			$this->delOperateDo();
		}
		
		$sideData = $this->operateSer->getOperateByCategory(CATEGORY_INDEX,CATEGORY_INDEX_DOTEY_RECOMMAND);
		
		$uids = array();
		$doteyInfos = array();
		if ($sideData){
			$imageUpload = new PipiImageUpload();
			foreach ($sideData as &$v){
				$uids[$v['target_id']] = $v['target_id'];
				$imageUpload->setDir($v['target_id'],'dotey', 'dynamic_big');
				if(is_file($imageUpload->getFile())){
					$v['dynamic_big'] = $imageUpload->getAdminFileUrl();
					$imageUpload->setDir($v['target_id'],'dotey', 'dynamic_middle');
					$v['dynamic_middle'] = $imageUpload->getAdminFileUrl();
					$imageUpload->setDir($v['target_id'],'dotey', 'dynamic_small');
					$v['dynamic_small'] = $imageUpload->getAdminFileUrl();
				}else{
					$v['dynamic_big'] = $v['dynamic_middle'] = $v['dynamic_small'] = '';
				}
			}
			$doteyInfos = $this->getDoteyInfo($uids);
		}
		$this->render('index_home_carousel',array('sideData'=>$sideData,'doteyInfos'=>$doteyInfos));
	}
	
	/**
	 * 唱区轮播  
	 */
	public function actionSongCarousel(){
		//是否是添加修改活动推荐
		if($this->op == 'addSongCarousel' && in_array($this->op,$this->allowOp)){
			$this->addSongCarouselDo();
		}
		
		//是否是删除首页厨窗
		if($this->op == 'delOperate' && in_array($this->op,$this->allowOp)){
			$this->delOperateDo();
		}
		
		$activeData = $this->operateSer->getOperateByCategory(CATEGORY_CHANNEL,CATEGORY_CHANNEL_SONG_CAROUSEL);
		$this->render('index_song_carousel',array('activeData'=>$activeData));
	}
	
	/**
	 * 获取首页厨窗主播信息
	 * 
	 * @param array $homeData
	 */
	public function getHomeWindowDoteyInfo(Array $homeData){
		$result = array();
		$doteyId = array();
		if ($homeData){
			foreach ($homeData as $dotey){
				if(isset($dotey['target_id'])){
					$doteyId[] = $dotey['target_id'];
				}
			}
			
			if($doteyId){
				$userSer = new UserService();
				if($doteyInfo = $userSer->getUserBasicByUids($doteyId)){
					foreach ($doteyInfo as $dinfo){
						$result[$dinfo['uid']] = $dinfo['nickname'];
					}
				}
			}
		}
		return $result;
	}
	
	/**
	 * 
	 * @param array $homeData
	 * @return multitype:unknown 
	 */
	public function getDoteyInfo(Array $uids){
		if($uids){
			$doteySer = new DoteyService();
			if($doteyInfo = $doteySer->getDoteyInfoByUids($uids)){
				return $doteyInfo;
			}
		}
		return array();
	}
	
	/**
	 * AJAX 检验主播信息的合法性
	 */
	public function checkDoteyInfo(){
		if (!$this->isAjax){
			exit('不合法请求');
		}
		
		$doteyName = Yii::app()->request->getParam('doteyName');
		if(empty($doteyName)){
			exit('请输入主播信息后进行校验 ');
		}
		
		$doteySer = new DoteyService();
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
			if(!($doteyInfo = $doteySer->getDoteyInfoByUid($uid))){
				exit('该用户不是主播，请确认');
			}else{
				if(!isset($userInfo)){
					if(!($userInfo = $userSer->getUserBasicByUids(array($uid)))){
						exit('不合法用户，请重新输入');
					}else{
						$userInfo = $userInfo[$uid];
					}
				}
				
				exit('1'.'#xx#'.$userInfo['uid'].'#xx#'.$userInfo['username'].'#xx#'.$userInfo['nickname']);
			}
			
		}else{
			exit('不合法用户，请重新输入');
		}
		
	}
	
	
	/**
	 * 执行添加 ，编辑首页厨窗操作 
	 */
	public function addHomeWindowDo(){
		$post = Yii::app()->request->getParam('indexForm');
		if($post){
			$uploads = array();
			$addInfo = array();
			foreach ($post as $k=>$v){
				if(is_array($v)){
					foreach($v as $k2 => $v2){
						$addInfo[$k2][$k] = $v2;
					}
				}
			}
			$this->uploadIndexPic($uploads);
			
			//添加或修改
			foreach ($addInfo as $k=>&$v){
				$v['category'] = CATEGORY_INDEX;
				$v['sub_category'] = CATEGORY_INDEX_SHOWCASE;
				
				if (!isset($v['piclink']) || empty($v['piclink'])){
					$v['piclink'] = array_shift($uploads);
				}
				if(!isset($v['textlink']) || empty($v['textlink'])){
					//$v['textlink'] = Yii::app()->request->getHostInfo().'/'.$v['target_id'];
				}
				if(!isset($v['sort']) || empty($v['sort'])){
					$v['sort'] = 0;
				}
				unset($addInfo[$k]['target_name']);
				$this->operateSer->saveOperate($addInfo[$k]);
			}
			$this->operateSer->saveAdminOpLog('编辑 首页厨窗');
		}
		
		$this->redirect($this->createUrl('index/homewindow'));
		
	}
	
	/**
	 * 执行首页活动推荐动作 专栏推荐
	 */
	public function addActivityRmdDo(){
		$post = Yii::app()->request->getParam('indexForm');
		if($post){
			$uploads = array();
			$addInfo = array();
			foreach ($post as $k=>$v){
				if(is_array($v)){
					foreach($v as $k2 => $v2){
						$addInfo[$k2][$k] = $v2;
					}
				}
			}
			$this->uploadIndexPic($uploads);
				
			//添加或修改
			foreach ($addInfo as $k=>&$v){
				$v['category'] = CATEGORY_INDEX;
				$v['sub_category'] = CATEGORY_INDEX_ACTIVITYRECOMMAND;
		
				if (!isset($v['piclink']) || empty($v['piclink'])){
					$v['piclink'] = array_shift($uploads);
				}
				if(!isset($v['sort']) || empty($v['sort'])){
					$v['sort'] = 0;
				}
				$this->operateSer->saveOperate($addInfo[$k]);
			}
			$this->operateSer->saveAdminOpLog('编辑 专栏推荐');
		}
		
		$this->redirect($this->createUrl('index/activityrmd'));
	}
	
	/**
	 * 执行首页侧边栏推荐操作 新秀主播版块
	 */
	public function addSideRmdDo(){
		//版块描述
		$forum_desc = Yii::app()->request->getParam('forum_desc',false);
		if ($forum_desc){
			$this->operateSer->setIndexRightDataForPookieDotey($forum_desc);
		}
		$post = Yii::app()->request->getParam('indexForm');
		if($post){
			$addInfo = array();
			foreach ($post as $k=>$v){
				if(is_array($v)){
					foreach($v as $k2 => $v2){
						if(is_array($v2)){
							foreach ($v2 as $k3=>$v3){
								$addInfo[$k3][$k][$k2] = $v3;
							}
						}else{
							$addInfo[$k2][$k] = $v2;
						}
					}
				}
			}
			
			//添加或修改
			$targetIds = array();
			foreach ($addInfo as $k=>&$v){
				$targetIds[] = $v['target_id'];
				$v['category'] = CATEGORY_INDEX;
				$v['sub_category'] = CATEGORY_INDEX_COLUMNSRECOMMAND;
				
				if(!isset($v['textlink']) || empty($v['textlink'])){
					//$v['textlink'] = Yii::app()->request->getHostInfo().'/'.$v['target_id'];
				}
				if(!isset($v['sort']) || empty($v['sort'])){
					$v['sort'] = 0;
				}
				
				if(!isset($v['content']) || empty($v['content'])){
					$v['sort'] = 0;
				}
				$this->operateSer->saveOperate($addInfo[$k]);
			}
			$this->operateSer->saveAdminOpLog('编辑 侧边栏推荐一 新秀主播版块 ('.implode(',',$targetIds).')');
		}
		
		$this->redirect($this->createUrl('index/sidermd'));
	}
	
	/**
	 * 执行首页侧边栏推荐操作 新秀主播版块
	 */
	public function addNewDoteyDo(){
		//版块描述
		$forum_desc = Yii::app()->request->getParam('forum_desc',false);
		if ($forum_desc){
			$this->operateSer->setIndexRightDataForNewDotey($forum_desc);
		}
		$post = Yii::app()->request->getParam('indexForm');
		if($post){
			$addInfo = array();
			foreach ($post as $k=>$v){
				if(is_array($v)){
					foreach($v as $k2 => $v2){
						if(is_array($v2)){
							foreach ($v2 as $k3=>$v3){
								$addInfo[$k3][$k][$k2] = $v3;
							}
						}else{
							$addInfo[$k2][$k] = $v2;
						}
					}
				}
			}
				
			//添加或修改
			$targetIds = array();
			foreach ($addInfo as $k=>&$v){
				$targetIds[] = $v['target_id'];
				$v['category'] = CATEGORY_INDEX;
				$v['sub_category'] = CATEGORY_INDEX_NEWDOTEY;
	
				if(!isset($v['textlink']) || empty($v['textlink'])){
					//$v['textlink'] = Yii::app()->request->getHostInfo().'/'.$v['target_id'];
				}
				if(!isset($v['sort']) || empty($v['sort'])){
					$v['sort'] = 0;
				}
	
				if(!isset($v['content']) || empty($v['content'])){
					$v['sort'] = 0;
				}
				$this->operateSer->saveOperate($addInfo[$k]);
			}
			
			$this->operateSer->saveAdminOpLog('编辑 侧边栏推荐二 最新加入版块 ('.implode(',',$targetIds).')');
		}
	
		$this->redirect($this->createUrl('index/newdotey'));
	}
	
	/**
	 * 执行本站明星添加或修改操作 
	 */
	public function addSiteStarDo(){
		//版块描述
		$forum_desc = Yii::app()->request->getParam('forum_desc',false);
		if ($forum_desc){
			$this->operateSer->setIndexRightDataForStarDotey($forum_desc);
		}
		$post = Yii::app()->request->getParam('indexForm');
		if($post){
			$uploads = array();
			$addInfo = array();
			foreach ($post as $k=>$v){
				if(is_array($v)){
					foreach($v as $k2 => $v2){
						if(is_array($v2)){
							foreach ($v2 as $k3=>$v3){
								$addInfo[$k3][$k][$k2] = $v3;
							}
						}else{
							$addInfo[$k2][$k] = $v2;
						}
					}
				}
			}
			$this->uploadIndexPic($uploads);
				
			//添加或修改
			$targetIds = array();
			foreach ($addInfo as $k=>&$v){
				$targetIds[] = $v['target_id'];
				$v['category'] = CATEGORY_INDEX;
				$v['sub_category'] = CATEGORY_INDEX_STARCOLLEGE;
		
				if (!isset($v['piclink']) || empty($v['piclink'])){
					$v['piclink'] = array_shift($uploads);
				}
				if(!isset($v['textlink']) || empty($v['textlink'])){
					//$v['textlink'] = Yii::app()->request->getHostInfo().'/'.$v['target_id'];
				}
				if(!isset($v['sort']) || empty($v['sort'])){
					$v['sort'] = 0;
				}
				$this->operateSer->saveOperate($addInfo[$k]);
			}
			
			$this->operateSer->saveAdminOpLog('编辑 明星主播版('.implode(',',$targetIds).')');
		}
		
		$this->redirect($this->createUrl('index/sitestar'));
	}
	
	/**
	 * 今日推荐操作
	 */
	public function addTodayRmdDo(){
		$post = Yii::app()->request->getParam('indexForm');
		if($post){
			$addInfo = array();
			foreach ($post as $k=>$v){
				if(is_array($v)){
					foreach($v as $k2 => $v2){
						if(is_array($v2)){
							foreach ($v2 as $k3=>$v3){
								$addInfo[$k3][$k][$k2] = $v3;
							}
						}else{
							$addInfo[$k2][$k] = $v2;
						}
					}
				}
			}
				
			//添加或修改
			$targetIds = array();
			foreach ($addInfo as $k=>&$v){
				$targetIds[] = $v['target_id'];
				$v['category'] = CATEGORY_INDEX;
				$v['sub_category'] = CATEGORY_INDEX_TODAYRECOMMAND;
		
				if(!isset($v['textlink']) || empty($v['textlink'])){
					//$v['textlink'] = Yii::app()->request->getHostInfo().'/'.$v['target_id'];
				}
				if(!isset($v['sort']) || empty($v['sort'])){
					$v['sort'] = 0;
				}
				$this->operateSer->saveOperate($addInfo[$k]);
			}
			$this->operateSer->saveAdminOpLog('编辑 今日推荐('.implode(',',$targetIds).')');
		}
		
		$this->redirect($this->createUrl('index/todayrmd'));
	}
	
	/**
	 * 首页强推推荐操作
	 */
	public function addLivePushDo(){
		$post = Yii::app()->request->getParam('indexForm');
		$rmd = Yii::app()->request->getParam('rmd');
		
		if($post && $rmd){
			//强推配置
			$webConfigSer = new WebConfigService();
			$config = array();
			$config['c_key'] = $webConfigSer->getLivePushKey();
			$config['c_type'] = 'int';
			if (isset($rmd['global'])){
				$config['c_value'] = WEB_LIVE_PUSH_GLOGAL;
			}else if (isset($rmd['custom'])){
				$customValue = 0;
				foreach ($rmd['custom'] as $v){
					$customValue += $v;
				}
				
				if(in_array($customValue, array(WEB_LIVE_PUSH_CUSTOM_ALL,WEB_LIVE_PUSH_CUSTOM_DOTEY,WEB_LIVE_PUSH_CUSTOM_TODYARMD))){
					$config['c_value'] = $customValue;
				}else{
					$config['c_value'] = WEB_LIVE_PUSH_GLOGAL;
				}
			}
			if (isset($config['c_value'])){
				$webConfigSer->saveWebConfig($config);
			}
			
			$addInfo = array();
			foreach ($post as $k=>$v){
				if(is_array($v)){
					foreach($v as $k2 => $v2){
						$addInfo[$k2][$k] = $v2;
					}
				}
			}
	
			//添加或修改
			$targetIds = array();
			foreach ($addInfo as $k=>&$v){
				$targetIds[] = $v['target_id'];
				$v['category'] = CATEGORY_INDEX;
				$v['sub_category'] = CATEGORY_INDEX_LIVESRECOMMAND;
	
				if(!isset($v['textlink']) || empty($v['textlink'])){
					//$v['textlink'] = Yii::app()->request->getHostInfo().'/'.$v['target_id'];
				}
				if(!isset($v['sort']) || empty($v['sort'])){
					$v['sort'] = 0;
				}
				unset($addInfo[$k]['target_name']);
				$this->operateSer->saveOperate($addInfo[$k]);
			}
			$this->operateSer->saveAdminOpLog('编辑 首页强推('.implode(',', $targetIds).')');
		}
	
		$this->redirect($this->createUrl('index/livepush'));
	}
	
	/**
	 * 执行首页新闻公告推荐动作
	 */
	public function addNewsNoticeRmdDo(){
		$post = Yii::app()->request->getParam('indexForm');
		if($post){
			$addInfo = array();
			foreach ($post as $k=>$v){
				if(is_array($v)){
					foreach($v as $k2 => $v2){
						$addInfo[$k2][$k] = $v2;
					}
				}
			}
			
			//添加或修改
			$targetIds = array();
			foreach ($addInfo as $k=>&$v){
				$targetIds[] = $v['target_id'];
				$v['category'] = CATEGORY_INDEX;
				$v['sub_category'] = CATEGORY_INDEX_NEWSNOTICE;
				$v['textlink'] = $this->createUrl('public/annouce',array('thread_id'=>$v['target_id']));
				if(!isset($v['sort']) || empty($v['sort'])){
					$v['sort'] = 0;
				}
				$this->operateSer->saveOperate($addInfo[$k]);
			}
			$this->operateSer->saveAdminOpLog('编辑 首页公告推荐thread_ids=('.implode(',', $targetIds).')');
		}
		$this->redirect($this->createUrl('operators/newsnotice'));
	}
	
	/**
	 * 执行唱区新闻公告推荐动作
	 */
	public function addSongNoticeRmdDo(){
		$post = Yii::app()->request->getParam('indexForm');
		if($post){
			$addInfo = array();
			foreach ($post as $k=>$v){
				if(is_array($v)){
					foreach($v as $k2 => $v2){
						$addInfo[$k2][$k] = $v2;
					}
				}
			}
				
			//添加或修改
			$targetIds = array();
			foreach ($addInfo as $k=>&$v){
				$targetIds[] = $v['target_id'];
				$v['category'] = CATEGORY_CHANNEL;
				$v['sub_category'] = CATEGORY_CHANNEL_SONG_NOTICE;
				$v['textlink'] = $this->createUrl('public/annouce',array('thread_id'=>$v['target_id']));
				
				if(!isset($v['sort']) || empty($v['sort'])){
					$v['sort'] = 0;
				}
				$this->operateSer->saveOperate($addInfo[$k]);
			}
			$this->operateSer->saveAdminOpLog('编辑 唱区公告推荐thread_ids=('.implode(',', $targetIds).')');
		}
		$this->redirect($this->createUrl('operators/newsnotice'));
	}
	
	/**
	 * 执行首页侧边栏推荐操作
	 */
	public function addHomeCarouselDo(){
		$post = Yii::app()->request->getParam('indexForm');
		if($post){
			$addInfo = array();
			foreach ($post as $k=>$v){
				if(is_array($v)){
					foreach($v as $k2 => $v2){
						if(is_array($v2)){
							foreach ($v2 as $k3=>$v3){
								$addInfo[$k3][$k][$k2] = $v3;
							}
						}else{
							$addInfo[$k2][$k] = $v2;
						}
					}
				}
			}
			
			//添加或修改
			$targetIds = array();
			foreach ($addInfo as $k=>&$v){
				$targetIds[] = $v['target_id'];
				$v['category'] = CATEGORY_INDEX;
				$v['sub_category'] = CATEGORY_INDEX_DOTEY_RECOMMAND;
				$v['textlink'] = $this->createUrl('archives/index',array('uid'=>$v['target_id']));
				if(!isset($v['sort']) || empty($v['sort'])){
					$v['sort'] = 0;
				}
	
				if(!isset($v['content']) || empty($v['content'])){
					$v['sort'] = 0;
				}
				$this->operateSer->saveOperate($addInfo[$k]);
			}
			$this->operateSer->saveAdminOpLog('编辑 本站明星UIDS=('.implode(',', $targetIds).')');
		}
	
		$this->redirect($this->createUrl('index/homecarousel'));
	}
	
	/**
	 * 执行唱区轮播图片的推荐操作
	 */
	public function addSongCarouselDo(){
		
		$post = Yii::app()->request->getParam('indexForm');
		if($post){
			$uploads = array();
			$addInfo = array();
			foreach ($post as $k=>$v){
				if(is_array($v)){
					foreach($v as $k2 => $v2){
						$addInfo[$k2][$k] = $v2;
					}
				}
			}
			$this->uploadIndexPic($uploads);
		
			//添加或修改
			foreach ($addInfo as $k=>&$v){
				$v['category'] = CATEGORY_CHANNEL;
				$v['sub_category'] = CATEGORY_CHANNEL_SONG_CAROUSEL;
		
				if (!isset($v['piclink']) || empty($v['piclink'])){
					$v['piclink'] = array_shift($uploads);
				}
				if(!isset($v['sort']) || empty($v['sort'])){
					$v['sort'] = 0;
				}
				$this->operateSer->saveOperate($addInfo[$k]);
			}
			$this->operateSer->saveAdminOpLog('编辑 唱区轮播');
		}
		
		$this->redirect($this->createUrl('index/songcarousel'));
	}
	
	/**
	 * 删除操作
	 */
	public function delOperateDo(){
		if (!$this->isAjax){
			exit('不合法请求');
		}
		
		$operateId = Yii::app()->request->getParam('operateId');
		if(empty($operateId)){
			exit('缺少参数 ');
		}
		
		if($this->operateSer->delOperateByOperateIds(array($operateId))){
			exit('1');
		}else{
			exit('信息有误，删除失败');
		}
		
	}
	
	/**
	 * 上传首页管理图片
	 */
	public function uploadIndexPic(Array &$update){
		if($effectFiles = CUploadedFile::getInstancesByName('indexForm')){
			foreach ($effectFiles as $effectFile){
				if($filename = $effectFile->getName()){
					$extName = $effectFile->getExtensionName();
					$newName = uniqid().'.'.$extName;
					$uploadDir = ROOT_PATH."images".DIR_SEP.'operate'.DIR_SEP;
					if (!file_exists($uploadDir)){
						mkdir($uploadDir,0777,true);
					}
					$uploadfile = $uploadDir.$newName;
					if($effectFile->saveAs($uploadfile,true)){
						$update[] = $newName;
					}else{
						$update[] = '';
					}
				}
			}
		}
		return true;
	}

}
