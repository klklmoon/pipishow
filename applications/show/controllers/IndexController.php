<?php
/**
 * @author zhangzhifan <zhangzhifan@pipi.cn> 2010-11-2
 * @link http://show.pipi.cn
 * @copyright Copyright &copy; 2003-2010 show.pipi.cn
 * @license
 */
class IndexController extends PipiController {

	/**
	 *
	 * @var ArchivesService
	 */
	protected $archiveService = null;

	/**
	 *
	 * @var WeiboService
	 */
	protected $weiboService = null;

	/**
	 *
	 * @var DoteyService
	 */
	protected $doteyService = null;

	/**
	 *
	 * @var GiftService
	 */
	protected $giftService = null;

	/**
	 *
	 * @var ChannelService
	 */
	protected $channelService = null;

	/**
	 *
	 * @var ChannelDoteySortService
	 */
	protected $channelDoteySort = null;
	
	/**
	 * 
	 * @var IndexPageService
	 */
	protected $indexPageService = null;

	public function init(){
		parent::init();
		
		$this->weiboService = new WeiboService();
		$this->archiveService = new ArchivesService();
		$this->doteyService = new DoteyService();
		$this->giftService = new GiftService();
		$this->channelService = new ChannelService();
		$this->channelDoteySort = new ChannelDoteySortService();
		$this->indexPageService = new IndexPageService();

		return true;
	}
	
	public function actionIndex() {
		$loginUid = isset(Yii::app()->user->id) ? Yii::app()->user->id : 0;
		$indexRightModel = new IndexRightDataModel();
		$indexRightData = $indexRightModel->findAll(array('order'=>'type ASC,charms DESC'));
		$rookieDotey = $newJoinDotey = $starDotey = array();
		$finalRookieDotey = $finalNewJoinDotey = $finalStarDotey = array();
		if($indexRightData){
			$indexRightData = $this->archiveService->arToArray($indexRightData);
			$indexRightUids = array_keys($this->archiveService->buildDataByIndex($indexRightData,'uid'));
			$indexRightAvatars = $this->userService->getUserAvatarsByUids($indexRightUids,'small');
			foreach($indexRightData as $indexRight){
				if($indexRight['type'] == 0){
					$rookieDotey[$indexRight['uid']]['uid'] = $indexRight['uid'];
					$rookieDotey[$indexRight['uid']]['charms'] = $indexRight['charms'];
					$rookieDotey[$indexRight['uid']]['auto'] = 1;
					$rookieDotey[$indexRight['uid']]['subject'] = '';
					$rookieDotey[$indexRight['uid']]['username'] = $indexRight['username'];
					$rookieDotey[$indexRight['uid']]['nickname'] = $indexRight['nickname'];
					$rookieDotey[$indexRight['uid']]['small_avatar'] = $indexRightAvatars[$indexRight['uid']];
				}elseif($indexRight['type'] == 1){
					$newJoinDotey[$indexRight['uid']]['uid'] = $indexRight['uid'];
					$newJoinDotey[$indexRight['uid']]['charms'] = $indexRight['charms'];
					$newJoinDotey[$indexRight['uid']]['auto'] = 1;
					$newJoinDotey[$indexRight['uid']]['subject'] = '';
					$newJoinDotey[$indexRight['uid']]['username'] = $indexRight['username'];
					$newJoinDotey[$indexRight['uid']]['nickname'] = $indexRight['nickname'];
					$newJoinDotey[$indexRight['uid']]['small_avatar'] = $indexRightAvatars[$indexRight['uid']];
				}elseif($indexRight['type'] == 2){
					$starDotey[$indexRight['uid']]['uid'] = $indexRight['uid'];
					$starDotey[$indexRight['uid']]['charms'] = $indexRight['charms'];
					$starDotey[$indexRight['uid']]['auto'] = 1;
					$starDotey[$indexRight['uid']]['subject'] = '';
					$starDotey[$indexRight['uid']]['username'] = $indexRight['username'];
					$starDotey[$indexRight['uid']]['nickname'] = $indexRight['nickname'];
					$starDotey[$indexRight['uid']]['small_avatar'] = $indexRightAvatars[$indexRight['uid']];
				}
			}
		}
		
		
		$operate = $this->operateService->getOperateByCategoryFromCache(CATEGORY_INDEX);
		$this->viewer['operate'] = $operate;
		$this->viewer['operateUrl'] = $this->operateService->getOperateUrl();
		$doteysInfo = array();
		
		
		if(isset($this->viewer['operate'][CATEGORY_INDEX_STARCOLLEGE])){
			foreach($this->viewer['operate'][CATEGORY_INDEX_STARCOLLEGE] as $value){
				$starDotey[$value['target_id']]['uid'] = $value['target_id'];
				$starDotey[$value['target_id']]['auto'] = 0;
				$starDotey[$value['target_id']]['charms'] = 0;
				$starDotey[$value['target_id']]['subject'] = $value['subject'];
				$starDotey[$value['target_id']]['username'] = $value['content']['username'];
				$starDotey[$value['target_id']]['nickname'] = $value['content']['nickname'];
			}
		}
		
		if($starDotey){
			$starDoteyArchives = $this->archiveService->getArchivesByUids(array_keys($starDotey),true,0);
			$starDoteyArchives = $this->channelDoteySort->filterArchives($starDoteyArchives,0);
			$starDoteyArchives = $this->channelDoteySort->buildLiveArchives($starDoteyArchives,$loginUid,0,false,$this->isLogin);
			$starDoteyArchives = $this->channelDoteySort->sortLiveArchives($starDoteyArchives,CHANNEL_DOTEY_SORT_STARTTIME,0,false);
			$livingStarDoteyArchives = $starDoteyArchives['living'];
			$waitDoteyArchives = $starDoteyArchives['wait'];
		
			$countLivingStarDoteyArchives = count($livingStarDoteyArchives);
			if($countLivingStarDoteyArchives > 4){
				$randStarDoteyKey = array_rand($livingStarDoteyArchives,4);
				foreach($randStarDoteyKey as $starDoteyKey){
					$starDoteyArchive = $livingStarDoteyArchives[$starDoteyKey];
					$_starDotey = $starDotey[$starDoteyArchive['uid']];
					$finalStarDotey[$starDoteyArchive['uid']] = $starDotey[$starDoteyArchive['uid']];
					$finalStarDotey[$starDoteyArchive['uid']]['is_attention'] = $starDoteyArchive['is_attention'];
					if(isset($starDotey[$starDoteyArchive['uid']]['subject']) && !$starDotey[$starDoteyArchive['uid']]['subject']){
						$finalStarDotey[$starDoteyArchive['uid']]['subject'] = $starDoteyArchive['sub_title'] ? $starDoteyArchive['sub_title'] : $starDoteyArchive['title'];
					}
					if(!isset($_starDotey['small_avatar']) || !$_starDotey['small_avatar']){
						$findalDoteyAvatar = $this->userService->getUserAvatarsByUids(array($starDoteyArchive['uid']),'small');
						$finalStarDotey[$starDoteyArchive['uid']]['small_avatar'] =  $findalDoteyAvatar[$starDoteyArchive['uid']];
					}
				}
		
			}else{
				$countWaitDoteyArchives = count($waitDoteyArchives);
				$i = 0;
				foreach($livingStarDoteyArchives as $starDoteyArchive){
					$i++;
					$_starDotey = $starDotey[$starDoteyArchive['uid']];
					$finalStarDotey[$starDoteyArchive['uid']] = $starDotey[$starDoteyArchive['uid']];
					$finalStarDotey[$starDoteyArchive['uid']]['is_attention'] = $starDoteyArchive['is_attention'];
					if(isset($starDotey[$starDoteyArchive['uid']]['subject']) && !$starDotey[$starDoteyArchive['uid']]['subject']){
						$finalStarDotey[$starDoteyArchive['uid']]['subject'] = $starDoteyArchive['sub_title'];
						if(!isset($_starDotey['small_avatar']) || !$_starDotey['small_avatar']){
							$findalDoteyAvatar = $this->userService->getUserAvatarsByUids(array($starDoteyArchive['uid']),'small');
							$finalStarDotey[$starDoteyArchive['uid']]['small_avatar'] =  $findalDoteyAvatar[$starDoteyArchive['uid']];
						}
					}
				}
				if($waitDoteyArchives){
					shuffle($waitDoteyArchives);
					foreach($waitDoteyArchives as $starDoteyArchive){
						if($i >= 4){
							break;
						}
						$_starDotey = $starDotey[$starDoteyArchive['uid']];
						$finalStarDotey[$starDoteyArchive['uid']] = $starDotey[$starDoteyArchive['uid']];
						$finalStarDotey[$starDoteyArchive['uid']]['is_attention'] = $starDoteyArchive['is_attention'];
						if(isset($starDotey[$starDoteyArchive['uid']]['subject']) && !$starDotey[$starDoteyArchive['uid']]['subject']){
							$finalStarDotey[$starDoteyArchive['uid']]['subject'] = $starDoteyArchive['sub_title'] ? $starDoteyArchive['sub_title'] : $starDoteyArchive['title'];
						}
						if(!isset($_starDotey['small_avatar']) || !$_starDotey['small_avatar']){
							$findalDoteyAvatar = $this->userService->getUserAvatarsByUids(array($starDoteyArchive['uid']),'small');
							$finalStarDotey[$starDoteyArchive['uid']]['small_avatar'] =  $findalDoteyAvatar[$starDoteyArchive['uid']];
						}
						$i++;
					}
				}
			}
				
		}
		
		
		if(isset($this->viewer['operate'][CATEGORY_INDEX_COLUMNSRECOMMAND])){
			foreach($this->viewer['operate'][CATEGORY_INDEX_COLUMNSRECOMMAND] as $value){
				$rookieDotey[$value['target_id']]['uid'] = $value['target_id'];
				$rookieDotey[$value['target_id']]['auto'] = 0;
				$rookieDotey[$value['target_id']]['charms'] = 0;
				$rookieDotey[$value['target_id']]['subject'] = $value['subject'];
				$rookieDotey[$value['target_id']]['username'] = $value['content']['username'];
				$rookieDotey[$value['target_id']]['nickname'] = $value['content']['nickname'];
			}
		}
		
		if($rookieDotey){
			$rookieDoteyArchives = $this->archiveService->getArchivesByUids(array_keys($rookieDotey),true,0);
			$rookieDoteyArchives = $this->channelDoteySort->filterArchives($rookieDoteyArchives,1);
			$rookieDoteyArchives = $this->channelDoteySort->buildLiveArchives($rookieDoteyArchives,$loginUid,1,false,$this->isLogin);
			$rookieDoteyArchives = $this->channelDoteySort->sortLiveArchives($rookieDoteyArchives,CHANNEL_DOTEY_SORT_STARTTIME,1,false);
			$rookieDoteyArchives = $rookieDoteyArchives['living'];
				
			$countRookieDoteyArchives = count($rookieDoteyArchives);
			if($countRookieDoteyArchives > 3){
				$randRookieDoteyKey = array_rand($rookieDoteyArchives,3);
				foreach($randRookieDoteyKey as $starRookieKey){
					$starRookieArchive = $rookieDoteyArchives[$starRookieKey];
					$_rookieDotey = $rookieDotey[$starRookieArchive['uid']];
					$finalRookieDotey[$starRookieArchive['uid']] = $rookieDotey[$starRookieArchive['uid']];
					$finalRookieDotey[$starRookieArchive['uid']]['is_attention'] = $starRookieArchive['is_attention'];
					if(isset($rookieDotey[$starRookieArchive['uid']]['subject']) && !$rookieDotey[$starRookieArchive['uid']]['subject']){
						$finalRookieDotey[$starRookieArchive['uid']]['subject'] = $starRookieArchive['sub_title'] ? $starRookieArchive['sub_title'] : $starRookieArchive['title'];
					}
					if(!isset($_rookieDotey['small_avatar']) || !$_rookieDotey['small_avatar']){
						$finalRookieDoteyAvatar = $this->userService->getUserAvatarsByUids(array($starRookieArchive['uid']),'small');
						$finalRookieDotey[$starRookieArchive['uid']]['small_avatar'] = $finalRookieDoteyAvatar[$starRookieArchive['uid']];
					}
				}
		
			}else{
				if($rookieDoteyArchives){
					shuffle($rookieDoteyArchives);
					$i = 0;
					foreach($rookieDoteyArchives as $starRookieArchive){
						if($i >= 4){
							break;
						}
						$_rookieDotey = $rookieDotey[$starRookieArchive['uid']];
						$finalRookieDotey[$starRookieArchive['uid']] = $rookieDotey[$starRookieArchive['uid']];
						$finalRookieDotey[$starRookieArchive['uid']]['is_attention'] = $starRookieArchive['is_attention'];
						if(isset($rookieDotey[$starRookieArchive['uid']]['subject']) && !$rookieDotey[$starRookieArchive['uid']]['subject']){
							$finalRookieDotey[$starRookieArchive['uid']]['subject'] = $starRookieArchive['sub_title'] ? $starRookieArchive['sub_title'] : $starRookieArchive['title'];
						}
						if(!isset($_rookieDotey['small_avatar']) || !$_rookieDotey['small_avatar']){
							$finalRookieDoteyAvatar = $this->userService->getUserAvatarsByUids(array($starRookieArchive['uid']),'small');
							$finalRookieDotey[$starRookieArchive['uid']]['small_avatar'] = $finalRookieDoteyAvatar[$starRookieArchive['uid']];
						}
						$i++;
					}
				}
			}
		}
		
		if(isset($this->viewer['operate'][CATEGORY_INDEX_NEWDOTEY])){
			foreach($this->viewer['operate'][CATEGORY_INDEX_NEWDOTEY] as $value){
				if(isset($finalRookieDotey[$value['target_id']])){
					continue;
				}
				$newJoinDotey[$value['target_id']]['uid'] = $value['target_id'];
				$newJoinDotey[$value['target_id']]['auto'] = 0;
				$newJoinDotey[$value['target_id']]['charms'] = 0;
				$newJoinDotey[$value['target_id']]['subject'] = $value['subject'];
				$newJoinDotey[$value['target_id']]['username'] = $value['content']['username'];
				$newJoinDotey[$value['target_id']]['nickname'] = $value['content']['nickname'];
			}
				
		}
		if($newJoinDotey){
			$newJoinDoteyArchives = $this->archiveService->getArchivesByUids(array_keys($newJoinDotey),true,0);
			$newJoinDoteyArchives = $this->channelDoteySort->filterArchives($newJoinDoteyArchives,1);
			$newJoinDoteyArchives = $this->channelDoteySort->buildLiveArchives($newJoinDoteyArchives,$loginUid,1,false,$this->isLogin);
			$newJoinDoteyArchives = $this->channelDoteySort->sortLiveArchives($newJoinDoteyArchives,CHANNEL_DOTEY_SORT_STARTTIME,1,false);
			$newJoinDoteyArchives = $newJoinDoteyArchives['living'];
				
			$countJoinDoteyArchives = count($newJoinDoteyArchives);
			if($countJoinDoteyArchives > 3){
				$randJoinDoteyKey = array_rand($newJoinDoteyArchives,3);
				foreach($randJoinDoteyKey as $starJoinKey){
					$newJoinArchive = $newJoinDoteyArchives[$starJoinKey];
					$_newJoinDotey = $newJoinDotey[$newJoinArchive['uid']];
					$finalNewJoinDotey[$newJoinArchive['uid']] = $newJoinDotey[$newJoinArchive['uid']];
					$finalNewJoinDotey[$newJoinArchive['uid']]['is_attention'] = $newJoinArchive['is_attention'];
					if(isset($newJoinDotey[$newJoinArchive['uid']]['subject']) && !$newJoinDotey[$newJoinArchive['uid']]['subject']){
						$finalNewJoinDotey[$newJoinArchive['uid']]['subject'] = $newJoinArchive['sub_title'] ? $newJoinArchive['sub_title'] : $newJoinArchive['title'];
					}
					if(!isset($_newJoinDotey['small_avatar']) || !$_newJoinDotey['small_avatar']){
						$finalNewJoinDoteyAvatar = $this->userService->getUserAvatarsByUids(array($newJoinArchive['uid']),'small');
						$finalNewJoinDotey[$newJoinArchive['uid']]['small_avatar'] = $finalNewJoinDoteyAvatar[$newJoinArchive['uid']];
					}
				}
		
			}else{
				if($newJoinDoteyArchives){
					$i = 0;
					shuffle($newJoinDoteyArchives);
					foreach($newJoinDoteyArchives as $newJoinArchive){
						if($i >= 4){
							break;
						}
						$_newJoinDotey = $newJoinDotey[$newJoinArchive['uid']];
						$finalNewJoinDotey[$newJoinArchive['uid']] = $newJoinDotey[$newJoinArchive['uid']];
						$finalNewJoinDotey[$newJoinArchive['uid']]['is_attention'] = $newJoinArchive['is_attention'];
						if(isset($newJoinDotey[$newJoinArchive['uid']]['subject']) && !$newJoinDotey[$newJoinArchive['uid']]['subject']){
							$finalNewJoinDotey[$newJoinArchive['uid']]['subject'] = $newJoinArchive['sub_title'] ? $newJoinArchive['sub_title'] : $newJoinArchive['title'];
						}
						if(!isset($_newJoinDotey['small_avatar']) || !$_newJoinDotey['small_avatar']){
							$finalNewJoinDoteyAvatar = $this->userService->getUserAvatarsByUids(array($newJoinArchive['uid']),'small');
							$finalNewJoinDotey[$newJoinArchive['uid']]['small_avatar'] = $finalNewJoinDoteyAvatar[$newJoinArchive['uid']];
						}
						$i++;
					}
				}
			}
		}
		
		//首页轮播
		$siteStars = $this->getSiteStars();
		
		$topSendGift = $this->giftService->getGlobalGiftList();
		if($topSendGift){
			$topSendDoteyIds =  array_keys($this->giftService->buildDataByIndex($topSendGift,'d_uid'));
			$archives = $this->archiveService->getArchivesByUids($topSendDoteyIds,true,0);
			$archives = $this->archiveService->buildDataByIndex($archives,'uid');
				
			foreach($topSendGift as $key => $topSend){
				if(isset($archives[$topSend['d_uid']])){
					$archive = $archives[$topSend['d_uid']];
					$topSendGift[$key]['title'] = $archive['title'];
					$topSendGift[$key]['sub_title'] = $archive['title'];
				}else{
					$topSendGift[$key]['title'] = '';
					$topSendGift[$key]['sub_title'] = '';
				}
					
			}
		}
		$this->scr = 'index';
		$channels = $this->channelService->reverseAllChannelByNames();
		$this->viewer['siteStars'] = $siteStars;
		$this->viewer['channels'] = $channels;
		$this->viewer['topSendGift'] = $topSendGift;
		$this->viewer['finalStarDotey'] = $finalStarDotey;
		$this->viewer['finalRookieDotey'] = $finalRookieDotey;
		$this->viewer['newJoinDotey'] = $finalNewJoinDotey;
		
		$this->cs->registerCssFile($this->pipiFrontPath.'/css/index/index.css?token='.$this->hash,'all');
		
		
		$this->viewer['charmRank'] = $this->userService->getUserCharmRank('today',$this->isLogin ? Yii::app()->user->id : 0);
		$this->viewer['richRank'] = $this->userService->getUserRichRank('today');
		$this->viewer['friendlyRank'] = $this->userService->getUserFriendlyRank('today');		

		
		$this->render('application.views.index.index2');
	}
	
	public function actionIndex2() {
		$this->layout = 'main1';
		$loginUid = isset(Yii::app()->user->id) ? Yii::app()->user->id : 0;
		
		//首页运营数据及首页定制数据
		$indexRightData = $this->indexPageService->getIndexRightData();
		$operate = $this->indexPageService->getOperateData();
		
		//右侧明星、新秀、最新加入主播
		$finalRookieDotey = $finalNewJoinDotey = $finalStarDotey = array();
		$finalRookieDotey = $this->indexPageService->getDoteyData($indexRightData[0], $operate, 3,  CATEGORY_INDEX_COLUMNSRECOMMAND);
		$finalNewJoinDotey = $this->indexPageService->getDoteyData($indexRightData[1], $operate, 3, CATEGORY_INDEX_NEWDOTEY, $finalRookieDotey);
		$finalStarDotey = $this->indexPageService->getDoteyData($indexRightData[2], $operate, 4, CATEGORY_INDEX_STARCOLLEGE);
		
		//首页轮播
		$siteStars = $this->getSiteStars();
		//首页动态图
		$dynamic = $this->indexPageService->getDynamicDotey($operate[CATEGORY_INDEX_DOTEY_RECOMMAND], 6);
		
		//默认出现我看过的数据
		$attentions = $this->indexPageService->getDoteyLayer($loginUid, 'latestSee');
		$attentions = array_slice($attentions, 0, 9);
		
		//全局礼物
		$topSendGift = $this->indexPageService->getGlobalGift();
		
		//今日推荐
		$todayRecommand = $this->indexPageService->getAllTodayRecommand();
		
		//正在直播
		$living = $this->indexPageService->getLivingArchives();
		$this->indexPageService->addTodayRecommandForArchives($living, $todayRecommand);
		
		//最新开播
		$newLiving = array_slice($living, 0, 3);
		
		//热门主播，需要取出今日推荐数据，并根据在线人数排序
		$this->indexPageService->deleteTodayRecommandForArchives($living, $todayRecommand);
		$hotArchives = $this->channelDoteySort->sortLiveArchives($living['living'], CHANNEL_DOTEY_SORT_USER_TOTAL);
		
		//待直播
		$willLive = $this->indexPageService->getWillLiveArchives();
		
		//生日专栏
		$birthday = $this->indexPageService->getBirthdayDotey();
		
		//专栏推荐，或活动推荐，暂时没用到
		$recommand = isset($operate[CATEGORY_INDEX_ACTIVITYRECOMMAND]) ? $operate[CATEGORY_INDEX_ACTIVITYRECOMMAND] : array();
		
		//主播等级人数
		$doteyRank = $this->indexPageService->getDoteyRankCount();
		
		//主播印象
		$tags = $this->indexPageService->getAllTags();
		
		$this->scr = 'index';
		$viewer['operate'] = $operate;
		$viewer['siteStars'] = $siteStars;
		$viewer['topSendGift'] = $topSendGift;
		$viewer['finalStarDotey'] = $finalStarDotey;
		$viewer['finalRookieDotey'] = $finalRookieDotey;
		$viewer['newJoinDotey'] = $finalNewJoinDotey;
		$viewer['finalStarDoteyDesc'] = $this->operateService->getIndexRightDataForStarDotey(); //明星主播描述
		$viewer['finalRookieDoteyDesc'] = $this->operateService->getIndexRightDataForPookieDotey(); //新秀主播描述
		$viewer['newJoinDoteyDesc'] = $this->operateService->getIndexRightDataForNewDotey(); //最新加入描述
		$viewer['living'] = $living;
		$viewer['willLive'] = $willLive;
		$viewer['todayBirthdayArchives'] = $birthday['today'];
		$viewer['willBirthdayArchives'] = $birthday['will'];
		$viewer['todayRecommand'] = $todayRecommand;
		//橱窗广告
		$viewer['showcase'] = isset($operate[CATEGORY_INDEX_SHOWCASE]) ? $operate[CATEGORY_INDEX_SHOWCASE] : array();
		//公告
		$viewer['notice'] = isset($operate[CATEGORY_INDEX_NEWSNOTICE]) ? $operate[CATEGORY_INDEX_NEWSNOTICE] : array();
		$viewer['recommand'] = $recommand;
		//明星榜
		$viewer['charmRank'] = $this->userService->getUserCharmRank('today',$this->isLogin ? Yii::app()->user->id : 0);
		//富豪榜
		$viewer['richRank'] = $this->userService->getUserRichRank('today');
		//情谊榜
		$viewer['friendlyRank'] = $this->userService->getUserFriendlyRank('today');
		$viewer['doteyRank'] = $doteyRank;
		$viewer['tags'] = $tags;
	
		$this->cs->registerCssFile($this->pipiFrontPath.'/css/index/index.css?token='.$this->hash,'all');
		$this->render('application.views.index.index3', $viewer);
	}
	
	/**
	 *新版首页
	 */
	public function actionIndexv5()
	{
		$loginUid = isset(Yii::app()->user->id) ? Yii::app()->user->id : 0;
		//首页运营数据及首页定制数据
		$indexRightData = $this->indexPageService->getIndexRightData();
		$operate = $this->indexPageService->getOperateData();
		
		$viewer=array();
		/*
		 * 首页中间部分
		 */
		//首页磁贴
		$dynamic = $this->indexPageService->getDynamicDotey($operate[CATEGORY_INDEX_DOTEY_RECOMMAND], 6);
		$viewer['dynamic'] = $dynamic;
		
		//正在直播
		$living = $this->indexPageService->getLivingArchives();
		//最新开播
		$newLiving = array_slice($living['living'], 0, 3);
		$viewer['newLiving']['living'] = $newLiving;
		$viewer['newLiving']['isLazyLoad']=true;
		
		//热门主播，需要取出今日推荐数据，并根据在线人数排序
		$hotArchives = $this->channelDoteySort->sortLiveArchives($living['living'], CHANNEL_DOTEY_SORT_USER_TOTAL);
		$viewer['hotArchives']['living'] = $hotArchives['living'];
		$viewer['hotArchives']['isLazyLoad']=true;
		
		//今日推荐，不足三个的补充热门主播的前几个
		$todayRecommand = $this->indexPageService->getAllTodayRecommand();
		$num = count($todayRecommand['living']);
		if($num < 3){
			$uids = array_keys($this->archiveService->buildDataByIndex($todayRecommand['living'], 'uid'));
			foreach($hotArchives['living'] as $_live){
				if($num >=3) break;
				if(!in_array($_live['uid'], $uids)){
					$_live['today_recommand']=true;
					$todayRecommand['living'][] = $_live;
					$num++;
				}
			}
		}
		$viewer['todayRecommand']['living'] = $todayRecommand['living'];
		$viewer['todayRecommand']['isLazyLoad']=true;
		
		//即将开播
		$willLive = $this->indexPageService->getWillLiveArchives();
		$viewer['willLive']['wait'] = $willLive['wait'];
		$viewer['willLive']['isLazyLoad']=true;
		
 		/*
		 * 首页右侧部分
		 */
		//橱窗广告
		$viewer['showcase'] = isset($operate[CATEGORY_INDEX_SHOWCASE]) ? $operate[CATEGORY_INDEX_SHOWCASE] : array();
		//公告
		$viewer['notice'] = isset($operate[CATEGORY_INDEX_NEWSNOTICE]) ? $operate[CATEGORY_INDEX_NEWSNOTICE] : array();
		
		//生日专栏
		$birthday = $this->indexPageService->getBirthdayDotey();
		$viewer['todayBirthdayArchives'] = $birthday['today'];
		$viewer['willBirthdayArchives'] = $birthday['will'];
		
		//右侧明星、新秀、最新加入主播
		$finalRookieDotey = $finalNewJoinDotey = $finalStarDotey = array();
		//明星主播
		$finalStarDotey = $this->indexPageService->getDoteyData($indexRightData[2], $operate, 3, CATEGORY_INDEX_STARCOLLEGE);
		$viewer['finalStarDotey'] = $finalStarDotey;
		$viewer['finalStarDoteyDesc'] = $this->operateService->getIndexRightDataForStarDotey(); //明星主播描述
		//新秀主播
		$finalRookieDotey = $this->indexPageService->getDoteyData($indexRightData[0], $operate, 3,  CATEGORY_INDEX_COLUMNSRECOMMAND);
		$viewer['finalRookieDotey'] = $finalRookieDotey;
		$viewer['finalRookieDoteyDesc'] = $this->operateService->getIndexRightDataForPookieDotey(); //新秀主播描述		
		//最新加入主播
		$finalNewJoinDotey = $this->indexPageService->getDoteyData($indexRightData[1], $operate, 3, CATEGORY_INDEX_NEWDOTEY, $finalRookieDotey);
		$viewer['newJoinDotey'] = $finalNewJoinDotey;
		$viewer['newJoinDoteyDesc'] = $this->operateService->getIndexRightDataForNewDotey(); //最新加入描述
		
		//明星魅力榜
		$viewer['todayCharmRank'] = $this->userService->getUserCharmRank('today',$loginUid);
		$viewer['weekCharmRank'] = $this->userService->getUserCharmRank('week',$loginUid);
		$viewer['monthCharmRank'] = $this->userService->getUserCharmRank('month',$loginUid);
		$viewer['superCharmRank'] = $this->userService->getUserCharmRank('super',$loginUid);
		//富豪榜
		$viewer['todayRichRank'] = $this->userService->getUserRichRank('today');
		$viewer['weekRichRank'] = $this->userService->getUserRichRank('week');
		$viewer['monthRichRank'] = $this->userService->getUserRichRank('month');
		$viewer['superRichRank'] = $this->userService->getUserRichRank('super');

		$this->cs->registerScriptFile($this->pipiFrontPath.'/js/common/indexmodule.js?token='.$this->hash,CClientScript::POS_END);
		$this->cs->registerScriptFile($this->pipiFrontPath.'/js/common/fixed.js?token='.$this->hash);
		$this->cs->registerCssFile($this->pipiFrontPath.'/css/index/pubmodule.css?token='.$this->hash,'all');
		$this->cs->registerCssFile($this->pipiFrontPath.'/css/index/index2.css?token='.$this->hash,'all');
		$this->render('application.views.index.indexv5', $viewer);
	}
	
	//首页磁贴
	public function actionBatchlist()
	{
		//首页磁贴
		$operate = $this->indexPageService->getOperateData();
		$dynamic = $this->indexPageService->getDynamicDotey($operate[CATEGORY_INDEX_DOTEY_RECOMMAND], 6);
		$this->renderPartial('application.views.index.showbatch',array('dynamic'=>$dynamic));
	}
	
	//获取标签
	public function getTagClass()
	{
		return array(1=>"pecz redbg",2=>"pecz greenbg",3=>"pecz purplebg",4=>"pecz pinkbg",5=>"pecz bluebg");
	}
	
	
	/**
	 *新版首页左侧导航
	 */
	public function getIndexLeft()
	{
		$indexLeft=array();
		$onlineCount=$this->indexPageService->getOnlineCount();
		$onlineCount=$onlineCount?$onlineCount:0;
		$indexLeft['onlineCount'] = sprintf("%s", $onlineCount);
		//主播等级人数
		$doteyRank = $this->indexPageService->getDoteyRankCount();
		$indexLeft['doteyRank']=$doteyRank;
		//主播印象
		$tags = $this->indexPageService->getAllTags();
		$indexLeft['tags'] = $tags;
		return $indexLeft;
	}
	
	/**
	 *新版首页主播魅力榜
	 */
	public function actionCharmrank()
	{
		$loginUid = isset(Yii::app()->user->id) ? Yii::app()->user->id : 0;
		$type = Yii::app()->request->getParam('type', 'today');	//默认是今日榜
		//明星魅力榜
		$charmRank = $this->userService->getUserCharmRank($type,$loginUid);
		$this->renderPartial('application.views.index.charmrank',array('rank'=>$charmRank));
	}
	
	/**
	 *新版首页富豪榜
	 */
	public function actionRichrank()
	{
		$type = Yii::app()->request->getParam('type', 'today');	//默认是今日榜
		//富豪榜
		$richRank = $this->userService->getUserRichRank($type);
		$this->renderPartial('application.views.index.richrank',array('rank'=>$richRank));
	}
	
	/**
	 *新版首页右侧用户登录信息
	 */	
	public function actionUserInfo()
	{
		$this->renderPartial('application.views.index.userinfo');
	}
	
	
	/**
	 * 新分类页
	 */
	public function actionCategoryv5()	
	{
		$type = Yii::app()->request->getParam('type', 'normal');	//默认是点唱专区
		$sort = Yii::app()->request->getParam('sort', 'online');	//
		$style = Yii::app()->request->getParam('style', 'big'); 	//默认显示大图
		$id = Yii::app()->request->getParam('id');					//主播等级、印象标签需要的id值
		$by = Yii::app()->request->getParam('by', 'desc');
		
		$queryCondtion = array();
		$searchUrl = 'index.php?'.$this->searchCondition($queryCondtion);
		
		//主播等级人数
		$doteyRank = $this->indexPageService->getDoteyRankCount();
		//主播印象
		$tags = DoteyTagsService::getInstance()->getAllTags();
		//今日推荐
		$todayRecommand = $this->archiveService->getAllTodayRecommand(Yii::app()->user->id, true, $this->isLogin);
		//取在直播和待开播数据
		switch($type){
			//点唱专区
			case 'song':
				$title = '点唱专区';
				$sort = empty($sort) ? 'online' : $sort;
				$archives = $this->indexPageService->getDoteyArchivesOfSong();
				break;
				//主播等级
			case 'rank':
				$title = '主播等级';
				$sort = empty($sort) ? 'rank' : $sort;
				$archives = $this->indexPageService->getDoteyByRank($id);
				break;
				//印象标签
			case 'tag':
				$title = '印象标签';
				$sort = empty($sort) ? 'online' : $sort;
				$archives = $this->indexPageService->getDoteyByTag($id);
				break;
				//今日推荐的更多
			case 'recommond':
				$title = '所有主播';
				$sort = empty($sort) ? 'online' : $sort;
				$archives = $todayRecommand;
				break;
				//最新开播的更多
			case 'new':
				$title = '所有主播';
				$sort = empty($sort) ? 'time' : $sort;
				$living = $this->indexPageService->getLivingArchives();
				$willLive = $this->indexPageService->getWillLiveArchives();
				$archives['living'] = empty($living['living']) ? array() : $living['living'];
				$archives['wait'] = empty($willLive['wait']) ? array() : $willLive['wait'];
				break;
				//热门主播和即将开播的更多
			case 'normal':
				$title = '所有主播';
				$sort = empty($sort) ? 'online' : $sort;
				$living = $this->indexPageService->getLivingArchives();
				$willLive = $this->indexPageService->getWillLiveArchives();
				$archives['living'] = empty($living['living']) ? array() : $living['living'];
				$archives['wait'] = empty($willLive['wait']) ? array() : $willLive['wait'];
				break;
		}
		
		//给数据排序，如果需要分页的话，也是在这里做，是先给所有数据排序后再分页
		switch($sort){
			//按观众人数
			case 'online':
				$archives = $this->channelDoteySort->sortLiveArchives($archives, CHANNEL_DOTEY_SORT_USER_TOTAL);
				break;
				//按主播等级
			case 'rank':
				$archives = $this->channelDoteySort->sortLiveArchives($archives, CHANNEL_DOTEY_SORT_RANK);
				break;
				//按开播时间
			case 'time':
				$archives = $this->channelDoteySort->sortLiveArchives($archives, CHANNEL_DOTEY_SORT_STARTTIME);
				break;
				//按联播天数
			case 'days':
				$archives = $this->channelDoteySort->sortLiveArchives($archives, CHANNEL_DOTEY_SORT_LIVETIME_SUPER);
				break;
		}
		
		//升降序
		if($by == 'asc'){
			$archives['living'] = array_reverse($archives['living']);
			$archives['wait'] = array_reverse($archives['wait']);
		}

		//点唱专区才有的排行榜
		$top = array();
		if($type == 'song'){
			$top['dotey_songs_rank'] = $this->indexPageService->getDoteySongsRank();
			$top['user_songs_rank'] = $this->indexPageService->getUserSongsRank();
			$seo = 'songs';
			$seoTitle = Yii::t('seo','seo_'.$seo.'_title');
		}else{
			$seo = 'category';
			$seoTitle = $title.' - '.Yii::t('seo','seo_'.$seo.'_title');
		}
		$viewer['title'] = $title;
		$viewer['url'] = 'index.php?'.$this->searchCondition();
		$viewer['archives'] = $archives;
		$viewer['livingArchives']['living']=$archives['living'] ;
		$viewer['livingArchives']['isLazyLoad']=true;
		$viewer['waitArchives']['wait']=$archives['wait'];
		$viewer['waitArchives']['isLazyLoad']=true;
		$viewer['top'] = $top;
		$viewer['searchUrl']=urldecode($searchUrl);
		$viewer['lastsort']=$sort;
		$viewer['type']=$type;
		
		$this->setPageTitle($seoTitle);
		$this->setPageKeyWords(Yii::t('seo','seo_'.$seo.'_keywords'));
		$this->setPageDescription(Yii::t('seo','seo_'.$seo.'_description'));
		$this->cs->registerScriptFile($this->pipiFrontPath.'/js/common/indexmodule.js?token='.$this->hash,CClientScript::POS_END);
		$this->cs->registerScriptFile($this->pipiFrontPath.'/js/common/fixed.js?token='.$this->hash);
		$this->cs->registerCssFile($this->pipiFrontPath.'/css/index/pubmodule.css?token='.$this->hash,'all');
		$this->cs->registerCssFile($this->pipiFrontPath.'/css/channel/category.css?token='.$this->hash,'all');
		$this->render('categoryv5', $viewer);
	}
	
	/**
	 * 新分类页
	 */
	public function actionCategoryModule()
	{
		$type = Yii::app()->request->getParam('type', 'normal');	//默认是点唱专区
		$sort = Yii::app()->request->getParam('sort', 'online');		//
		$style = Yii::app()->request->getParam('style', 'big'); //默认显示大图
		$id = Yii::app()->request->getParam('id');				//主播等级、印象标签需要的id值
		$by = Yii::app()->request->getParam('by', 'desc');
	
		$queryCondtion = array();
		$searchUrl = 'index.php?'.$this->searchCondition($queryCondtion);
	
		//主播等级人数
		$doteyRank = $this->indexPageService->getDoteyRankCount();
		//主播印象
		$tags = DoteyTagsService::getInstance()->getAllTags();
		//今日推荐
		$todayRecommand = $this->archiveService->getAllTodayRecommand(Yii::app()->user->id, true, $this->isLogin);
		//取在直播和待开播数据
		switch($type){
			//点唱专区
			case 'song':
				$title = '点唱专区';
				$sort = empty($sort) ? 'online' : $sort;
				$archives = $this->indexPageService->getDoteyArchivesOfSong();
				break;
				//主播等级
			case 'rank':
				$title = '主播等级';
				$sort = empty($sort) ? 'rank' : $sort;
				$archives = $this->indexPageService->getDoteyByRank($id);
				break;
				//印象标签
			case 'tag':
				$title = '印象标签';
				$sort = empty($sort) ? 'online' : $sort;
				$archives = $this->indexPageService->getDoteyByTag($id);
				break;
				//今日推荐的更多
			case 'recommond':
				$title = '所有主播';
				$sort = empty($sort) ? 'online' : $sort;
				$archives = $todayRecommand;
				break;
				//最新开播的更多
			case 'new':
				$title = '所有主播';
				$sort = empty($sort) ? 'time' : $sort;
				$living = $this->indexPageService->getLivingArchives();
				$willLive = $this->indexPageService->getWillLiveArchives();
				$archives['living'] = empty($living['living']) ? array() : $living['living'];
				$archives['wait'] = empty($willLive['wait']) ? array() : $willLive['wait'];
				break;
				//热门主播和即将开播的更多
			case 'normal':
				$title = '所有主播';
				$sort = empty($sort) ? 'online' : $sort;
				$living = $this->indexPageService->getLivingArchives();
				$willLive = $this->indexPageService->getWillLiveArchives();
				$archives['living'] = empty($living['living']) ? array() : $living['living'];
				$archives['wait'] = empty($willLive['wait']) ? array() : $willLive['wait'];
				break;
		}
	
		//给数据排序，如果需要分页的话，也是在这里做，是先给所有数据排序后再分页
		switch($sort){
			//按观众人数
			case 'online':
				$archives = $this->channelDoteySort->sortLiveArchives($archives, CHANNEL_DOTEY_SORT_USER_TOTAL);
				break;
				//按主播等级
			case 'rank':
				$archives = $this->channelDoteySort->sortLiveArchives($archives, CHANNEL_DOTEY_SORT_RANK);
				break;
				//按开播时间
			case 'time':
				$archives = $this->channelDoteySort->sortLiveArchives($archives, CHANNEL_DOTEY_SORT_STARTTIME);
				break;
				//按联播天数
			case 'days':
				$archives = $this->channelDoteySort->sortLiveArchives($archives, CHANNEL_DOTEY_SORT_LIVETIME_SUPER);
				break;
		}
	
		if(isset($archives['wait']))
			unset($archives['wait']);
		//升降序
		if($by == 'asc'){
			$archives['living'] = array_reverse($archives['living']);
		}

		$archives['isLazyLoad']=false;
		$viewer['html']=$this->renderPartial('doteylist', $archives,true);
		$viewer['title'] = $title;
		$viewer['searchUrl']=urldecode($searchUrl);
		$viewer['lastsort']=$sort;
		$viewer['type']=$type;

		exit(json_encode($viewer));
		
	}
	
	
	public function searchCondition(array &$queryCondition = array()){
		$queryString = Yii::app()->request->getQueryString();
		parse_str($queryString,$queryCondition);
		//return $queryCondition;
		return http_build_query($queryCondition);
	}
	
	/**
	 * 我关注的档期
	 * @author suqian
	 */
	public function actionAttentionArchives(){
		if(!$this->isLogin){
			return array();
		}
		$uid = Yii::app()->user->id;
		$archivesService = new ArchivesService();
		$attentionArchives = $archivesService->getUserAttentionArchives($uid,true);
		$attentionArchives['attentionType'] = 'attention';
		$attentionArchives['type'] = '关注';
		$archivesService->addStarSingerForArchives($attentionArchives);
		$this->renderPartial('application.views.index2.liveArchivesTemplate2',$attentionArchives);
	}
	
	/**
	 * 我管理的档期
	 * @author 苏骞
	 */
	public function actionManagerArchives(){
		if(!$this->isLogin){
			return array();
		}
		$uid = Yii::app()->user->id;
		$archivesService = new ArchivesService();
		$managerArchives = $archivesService->getUserManagerArchives($uid,true,true);
		if(!isset($managerArchives['living']))
			$managerArchives['living']=array();
		if(!isset($managerArchives['wait']))
			$managerArchives['wait']=array();
		$managerArchives['manager'] = 'manager';
		$managerArchives['type'] = '管理';
		$archivesService->addStarSingerForArchives($managerArchives);
		$this->renderPartial('application.views.index2.liveArchivesTemplate2',$managerArchives);
	}
	
	/**
	 * 我最近观看的档期
	 * @author suqian
	 */
	public function actionLatestSeeArchives(){
		if(!$this->isLogin){
			return array();
		}
		$uid = Yii::app()->user->id;
		$archivesService = new ArchivesService();
		$latestSeeArchives = $archivesService->getUserLatestSeeArchives($uid,true,true);
		$latestSeeArchives['attentionType'] = 'latestSee';
		$latestSeeArchives['type']="看过";
		$archivesService->addStarSingerForArchives($latestSeeArchives);
		$this->renderPartial('application.views.index2.liveArchivesTemplate2',$latestSeeArchives);
	}
	
	//正在直播
	public function actionOnliveArchives()
	{
		$archivesService=$this->archiveService;
		$uid=Yii::app()->user->id;
		$living = $archivesService->getLivingArchives($uid,true,$this->isLogin);
		$archivesService->addTodayRecommandForArchives($living,$uid,true,$this->isLogin);
		$archivesService->addStarSingerForArchives($living);
		$this->renderPartial('application.views.index2.liveArchivesTemplate2',$living);
	}
	
	//获取首页轮播档期，新首页上线后该方法废弃
	private function getSiteStars()
	{
		$operate = $this->operateService->getOperateByCategoryFromCache(CATEGORY_INDEX);
		$this->viewer['operate'] = $operate;
		$siteStars = array();
		if(isset($this->viewer['operate'][CATEGORY_INDEX_DOTEY_RECOMMAND])){
			$recUids = array();
			$tmpSiteStars = $this->viewer['operate'][CATEGORY_INDEX_DOTEY_RECOMMAND];
			$recUids = array_keys( $this->archiveService->buildDataByIndex($tmpSiteStars,'target_id'));
			//首页伦播图档期是否正在直播
			if($recUids){
				$archives = $this->archiveService->getArchivesByUids($recUids,true,0);
				$archives = $this->archiveService->buildDataByIndex($archives,'uid');
		
				foreach($tmpSiteStars as $value){
					$doteyUid = $value['target_id'];
					if(isset($archives[$doteyUid])){
						$archives[$doteyUid]['sort'] = $value['sort'];
					}
				}
				$archives = $this->channelDoteySort->buildLiveArchives($archives,0,0,false,false);
				$archives = $this->channelDoteySort->sortLiveArchives($archives,CHANNEL_DOTEY_SORT_SORT,0,true);
				//如果存在正在直播的则直接取出
				if(isset($archives['living']) && count($archives['living'])>0){
					$count=count($archives['living']);
					//如果正在直播小于3，直接取出
					if($count <= 3){
						$siteStars = array_slice($archives['living'],0,$count);
					}else{
						//如果正在直播大于3，前两个排序值都大于0，优先展示前2个，只有一个大于0，截取前一个，后面在剩余的数组随机取
						$one = array_slice($archives['living'],0,1);
						$two =  array_slice($archives['living'],1,1);
/* 						if($two[0]['sort']>0){
							$ones = array_shift($archives['living']);
							$twos =  array_shift($archives['living']);
							$randLivingKey = array_rand($archives['living'],1);
							$siteStars[] = $ones;
							$siteStars[] = $twos;
							$siteStars[] = $archives['living'][$randLivingKey];
						}else */

						if($one[0]['sort'] > 0 && $one[0]['sort']!=$two[0]['sort']){
							$ones = array_shift($archives['living']);
							$siteStars[] = $ones;
							$randLivingKey = array_rand($archives['living'],2);
							foreach ($randLivingKey as $rkey=>$rvalue){
								$siteStars[] = $archives['living'][$rvalue];
							}
						}else{
							$randLivingKey = array_rand($archives['living'],3);
							foreach ($randLivingKey as $rkey=>$rvalue){
								$siteStars[] = $archives['living'][$rvalue];
							}
						}
					}
				}
				//正在直播中小于3，随机从待直播中取
				$sCount = count($siteStars);
				if($sCount <3 && isset($archives['wait']) && count($archives['wait'])>0){
					$wcount = count($archives['wait']);
					$ycount = 3-$sCount;
					if($wcount >= $ycount){
						$randLivingKey = array_rand($archives['wait'],$ycount);
					}else{
						$randLivingKey = array_rand($archives['wait'],$wcount);
					}
					$randLivingKey = is_array($randLivingKey) ? $randLivingKey : array($randLivingKey);
					foreach ($randLivingKey as $rkey=>$rvalue){
						$siteStars[] = $archives['wait'][$rvalue];
					}
				}
				foreach($siteStars as $key=>$star){
					foreach ($tmpSiteStars as $tmpStar){
						if($star['uid'] == $tmpStar['target_id']){
							if(is_array($tmpStar['content'])){
								$siteStars[$key]['nickname'] = $tmpStar['content']['nickname'];
							}
							$siteStars[$key]['display_big'] = $this->doteyService->getDoteyUpload($star['uid'],'big','display');
							$siteStars[$key]['small_avatar'] = $this->userService->getUserAvatar($star['uid'],'small');
						}
					}
				}
			}
		}

		$result=$siteStars;
		return $result;
	}
	
	//首页轮播更新
	public function actionUpdateSiteStars()
	{
		$siteStars = $this->getSiteStars();
		$this->renderPartial('application.views.index2.updateSiteStars',
			array(
				'siteStars'=>$siteStars
			));
	}
	
	public function actionGameRedirect(){
		$game = Yii::app()->request->getParam('game');
		$redirect = 'index.php';
		$gameList  = array(
			'zuixiyou'=>array(
				'login'=>'http://game.pipi.cn/letian_zxy9.action',
				'unlogin'=>'http://121.10.139.116/index.php?s=/Admin/Ad/index/ID/709',
			),
			'shengxiandao'=>array(
				'login'=>'http://game.pipi.cn/letian_sxd49.action',
				'unlogin'=>'http://121.10.139.116/index.php?s=/Admin/Ad/index/ID/712'
			),
				
			'xiaobaoshengzhi'=>array(
				'login'=>'http://game.pipi.cn/letian_xbszj6.action',
				'unlogin'=>'http://121.10.139.116/index.php?s=/Admin/Ad/index/ID/710'
			),
				
			'jiangshenglu'=>array(
				'login'=>'http://game.pipi.cn/letian_js18.action',
				'unlogin'=>'http://121.10.139.116/index.php?s=/Admin/Ad/index/ID/708'
			),
				
			'longjiang'=>array(
				'login'=>'http://game.pipi.cn/letian_lj55.action',
				'unlogin'=>'http://121.10.139.116/index.php?s=/Admin/Ad/index/ID/711'
			),
				
				
		);
		$game = ($game  && isset($gameList[$game]))? $game : 'zuixiyou';
		if(!$this->isLogin){
			$redirect = $gameList[$game]['unlogin'];
		}else{
			$uid = Yii::app()->user->id;
			$userService = new UserService();
			$user = $userService->getUserBasicByUids(array($uid));
			$user = $user[$uid];
			if(empty($user)){
				$redirect = $gameList[$game]['unlogin'];
			}else{
				$redirect = "{$gameList[$game]['login']}?userid={$uid}&userName={$user['username']}";
			}
		}
		$this->redirect($redirect);
	}
	
	/**
	 * 收藏到桌面，实际就是下载一个windos下的链接的快捷方式文件
	 * @author hexin
	 */
	public function actionDesktop(){
		$filename=DATA_PATH."letian.url";
		$file = fopen($filename,"r");
		$ua = $_SERVER["HTTP_USER_AGENT"];
		$name = "皮皮乐天";
		if (preg_match("/MSIE/", $ua)) {
			$name = urlencode($name);
		}
		// 输入文件标签
		Header("Content-type: application/octet-stream");
		Header("Accept-Ranges: bytes");
		Header("Accept-Length: ".filesize($filename));
		Header("Content-Disposition: attachment; filename=".$name.".url");
		// 输出文件内容
		echo fread($file,filesize($filename));
		fclose($file);
	}
	
	public function actionGuide(){
		$operateService=new OperateService();
		$url=$operateService->getSpreadPrograme();
		exit(json_encode(array('url'=>$url)));
	}
	
}
