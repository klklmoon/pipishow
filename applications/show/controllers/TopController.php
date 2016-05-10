<?php
/**
 * 排行榜页面
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z suqian $ 
 * @package
 */
class TopController extends PipiController {
	
	public function actionIndex(){
		$this->cs->registerCssFile($this->pipiFrontPath.'/css/top/list.css?token='.$this->hash,'all');
		$viewer['rank'] = $this->getUserRank();
		$viewer['dotey_rank'] = $this->getDoteyRank();
		$viewer['gift'] = $this->getGiftRank();
		$viewer['songs'] = $this->getSongsRank();
		
		$this->setPageTitle(Yii::t('seo','seo_top_title'));
		$this->setPageKeyWords(Yii::t('seo','seo_top_keywords'));
		$this->setPageDescription(Yii::t('seo','seo_top_description'));
		$this->render('index',$viewer);
	}
	
	
	public function getUserRank(){
		$rank['today'] = $this->userService->getUserRichRank('today');
		$rank['week'] =  $this->userService->getUserRichRank('week');
		$rank['month'] =  $this->userService->getUserRichRank('month');
		$rank['super'] =  $this->userService->getUserRichRank('super');
		return $rank;
	}
	public function getDoteyRank(){
		$rank['today'] =  $this->userService->getUserCharmRank('today',0);
		$rank['week'] =  $this->userService->getUserCharmRank('week',0);
		//$rank['month'] =  $this->userService->getUserCharmRank('month',0);
		$rank['super'] =  $this->userService->getUserCharmRank('super',0);
		
		$giftService = new GiftService();
		//$rank['dotey_gift_super'] = $giftService->getDoteyReceiveGiftRank('super');
		return $rank;
	}
	
	public function getGiftRank(){
		$giftService = new GiftService();
		$gift['week'] = $giftService->getDoteyGiftRank('week');
		$gift['lastweek'] = $giftService->getDoteyGiftRank('lastweek');
		return $gift;
	}
	
	public function getSongsRank(){
		$songsService = new DoteySongService();
		//$rank['today'] =  $songsService->getDoteySongsRank('today',0);
		$rank['week'] =   $songsService->getDoteySongsRank('week',0);
		$rank['month'] =  $songsService->getDoteySongsRank('month',0);
		//$rank['super'] =  $songsService->getDoteySongsRank('super',0);
		return $rank;
	}
	
	public function actionAjax(){
		$viewer['rank'] = $this->getUserRank();
		$viewer['dotey_rank'] = $this->getDoteyRank();
		$viewer['gift'] = $this->getGiftRank();
		$viewer['songs'] = $this->getSongsRank();
		$this->renderPartial('ajax',$viewer);
	}
}