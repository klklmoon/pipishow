<?php
/**
 * 频道控制器
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z suqian $ 
 * @package
 */
class ChannelController extends PipiController{
	/**
	 * @var ChannelService 频道服务层
	 */
	protected $channelService;
	/**
	 * @var DoteyService 主播服务层
	 */
	protected $doteyService;
	/**
	 * @var ArchivesService 档期服务层
	 */
	protected $archivesService;
	/**
	 * @var operateService
	 */
	protected $operateService;
	
	public function beforeAction($action){
		parent::beforeAction($action);
		$this->channelService = new ChannelService();
		$this->doteyService = new DoteyService();
		$this->archivesService = new ArchivesService();
		$this->operateService = new OperateService();
		return true;
	}
	
	public function actionSongs(){
		$operate = $this->operateService->getOperateByCategoryFromCache(CATEGORY_CHANNEL);
		$queryCondtion = array();
		$searchUrl = 'index.php?'.$this->searchCondition($queryCondtion);
		$this->viewer['typeSelect'] = array(0=>'',1=>'',2=>'');
		$this->viewer['orderSelect'] = array(6=>'',4=>'',2=>'',3=>'');
		if(isset($queryCondtion['type'])){
			$this->viewer['typeSelect'][$queryCondtion['type']] = 'on';
		}else{
			$this->viewer['typeSelect'][0] = 'on';
			$queryCondtion['type'] = 0;
		}
	
		if(isset($queryCondtion['order'])){
			$this->viewer['orderSelect'][$queryCondtion['order']] = 'on';
		}else{
			$this->viewer['orderSelect'][6] = 'on';
			$queryCondtion['order'] = 6;
		}
		
		$this->viewer['operate'] = $operate;
		$this->viewer['operateUrl'] = $this->operateService->getOperateUrl();
		
		$songArchives = $this->channelService->getDoteyArchivesOfSong(Yii::app()->user->id,true,$this->isLogin,$queryCondtion['order'],$queryCondtion['type']);
		$this->archivesService->addTodayRecommandForArchives($songArchives,Yii::app()->user->id,true,$this->isLogin);
		$this->archivesService->addStarSingerForArchives($songArchives);
      				
		$rightData['user_songs_rank'] = $this->getUserSongsRank();
		$rightData['dotey_songs_rank'] = $this->getDoteySongsRank();
		$this->cs->registerCssFile($this->pipiFrontPath.'/css/channel/songarea.css?token='.$this->hash,'all');
		$this->setPageTitle(Yii::t('seo','seo_songs_title'));
		$this->setPageKeyWords(Yii::t('seo','seo_songs_keywords'));
		$this->setPageDescription(Yii::t('seo','seo_songs_description'));
		
		$this->render('songs',array('rightData'=>$rightData,'songArchives'=>$songArchives,'searchUrl'=>$searchUrl));
	}

	public function actionAjaxRight(){
		$rightData['user_songs_rank'] = $this->getUserSongsRank();
		$rightData['dotey_songs_rank'] = $this->getDoteySongsRank();
		$this->renderPartial('songsright',array('rightData'=>$rightData));
	}
	
	public function actionCategory(){
		$channels = $this->channelService->reverseAllChannelByNames();
		$areaChannel = $channels[CHANNEL_AREA];
		$doteySearchService = new DoteySearchService();
		$queryCondtion = array();
		$searchUrl = 'index.php?'.$this->searchCondition($queryCondtion);
		$this->viewer['q']['area'][0] = 'on';
		$bit = array();
		
		foreach($areaChannel as $_channel){
			if(isset($queryCondtion['area']) && $_channel['sub_channel_id'] == $queryCondtion['area']){
				$this->viewer['q']['area'][$_channel['sub_channel_id']] = 'on';
				$this->viewer['q']['area'][0] = '';
			}else{
				$this->viewer['q']['area'][$_channel['sub_channel_id']] = '';
			}
			$bit[] = (int)$_channel['sub_channel_id'];
		}
		asort($bit);
		if($bit && isset($queryCondtion['area'])){
			$bit = array_values($bit);
			$first = $bit[0];
			$last = $bit[count($bit) - 1];
			$queryCondtion ['channel'] = $this->channelService->getBitCondition((int)$queryCondtion['area'],$first,$last);
		}else{
			$queryCondtion ['channel'] = 0;
		}
		
		$this->viewer['q']['rank'] = array(0=>'',1=>'',2=>'',3=>'');
		$this->viewer['q']['status'] = array(0=>'',1=>'',2=>'');
		$this->viewer['q']['order'] = array(6=>'',3=>'',2=>'',1=>'');
		
		if(isset($queryCondtion['status'])){
			$this->viewer['q']['status'][$queryCondtion['status']] = 'on';
		}else{
			$this->viewer['q']['status'][0] = 'on';
			$queryCondtion['status'] = 0;
		}
		if(isset($queryCondtion['rank'])){
			$this->viewer['q']['rank'][$queryCondtion['rank']] = 'on';
		}else{
			$this->viewer['q']['rank'][0] = 'on';
			$queryCondtion['rank'] = 0;
		}
	
		if(isset($queryCondtion['order'])){
			$this->viewer['q']['order'][$queryCondtion['order']] = 'on';
		}else{
			$this->viewer['q']['order'][6] = 'on';
			$queryCondtion['order'] = 6;
		}
		$queryCondtion['uid'] = Yii::app()->user->id;
		$songArchives = $doteySearchService->searchCategory($queryCondtion);
		$this->archivesService->addTodayRecommandForArchives($songArchives,$queryCondtion['uid'],true,$this->isLogin);
		$this->archivesService->addStarSingerForArchives($songArchives);
		
		$leftData['dotey_fans_rank'] = $this->getDoteyFansRank();
		$this->viewer['channels'] = $channels;
		$this->cs->registerCssFile($this->pipiFrontPath.'/css/channel/sort.css?token='.$this->hash,'all');
		$this->setPageTitle(Yii::t('seo','seo_category_title'));
		$this->setPageKeyWords(Yii::t('seo','seo_category_keywords'));
		$this->setPageDescription(Yii::t('seo','seo_category_description'));
		$this->render('category',array('searchUrl'=>$searchUrl,'songArchives'=>$songArchives,'leftData'=>$leftData));
	}
	public function getUserSongsRank(){
		$songsService = new DoteySongService();
		$songs['today'] =  $songsService->getUserSongsRank('today',0);
		$songs['week'] =   $songsService->getUserSongsRank('week',0);
		$songs['month'] =  $songsService->getUserSongsRank('month',0);
		$songs['super'] =  $songsService->getUserSongsRank('super',0);
		return $songs;
	}
	
	public function getDoteySongsRank(){
		$songsService = new DoteySongService();
		$songs['today'] =  $songsService->getDoteySongsRank('today',0);
		$songs['week'] =   $songsService->getDoteySongsRank('week',0);
		$songs['month'] =  $songsService->getDoteySongsRank('month',0);
		$songs['super'] =  $songsService->getDoteySongsRank('super',0);
		return $songs;
	}
	
	public function getDoteyFansRank(){
		$weiboService = new WeiboService();
		$fans['super'] = $weiboService->getDoteyFansRank('super');
		$fans['new'] = $weiboService->getDoteyFansRank('new');
		return $fans;
	}
		
}
?>