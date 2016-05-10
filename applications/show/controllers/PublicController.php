<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <aoxue.1988.su.qian@163.com>
 * @version $Id: PublicController.php 8317 2013-03-29 01:19:47Z suqian $ 
 * @package 
 */
class PublicController extends PipiController {
	
	public function actionError(){
		
		if(Yii::app()->errorHandler->error){
	    	$error=Yii::app()->errorHandler->error;
	    	if(Yii::app()->request->isAjaxRequest){
	    		echo $error['message'];
	    		Yii::app()->end();
	    	}else{
	    		if(isset($error['code'])){
					$this->render ("error_".$error['code'],array('errorMsg'=>YII_DEBUG ? $error['message'] : '操作出错了'));    			
	    		}else{
		        	$this->render('error',array('errorMsg'=>YII_DEBUG ? $error['message'] : '操作出错了'));
	    		}
	    	}
	    }else{
	    	$this->render('error',array('errorMsg'=>'联系网站管理员'));
	    }
		
	}
	
	public function actionHelp(){
		$this->cs->registerCssFile($this->pipiFrontPath.'/css/common/help.css?token='.$this->hash,'all');
		$this->cs->registerScriptFile($this->pipiFrontPath.'/js/common/jquery.lazyload.min.js?token='.$this->hash,CClientScript::POS_END);
		$this->setPageTitle(Yii::t('seo','seo_aboutus_title',array('{category}'=>'问题帮助')));
		$this->setPageKeyWords(Yii::t('seo','seo_aboutus_keywords'));
		$this->setPageDescription(Yii::t('seo','seo_aboutus_description'));
		$this->render('help');
	}
	
	/**
	 * 主播帮助
	 * @author guoshaobo 
	 */
	public function actionDoteyHelp()
	{
		$this->cs->registerCssFile($this->pipiFrontPath.'/css/common/help.css?token='.$this->hash,'all');
// 		$this->cs->registerScriptFile($this->pipiFrontPath.'/js/common/jquery.lazyload.min.js?token='.$this->hash,CClientScript::POS_END);
		$this->setPageTitle(Yii::t('seo','seo_aboutus_title',array('{category}'=>'问题帮助')));
		$this->setPageKeyWords(Yii::t('seo','seo_aboutus_keywords'));
		$this->setPageDescription(Yii::t('seo','seo_aboutus_description'));
		$this->render('dotey_help');
	}
	
	public function actionAboutUs(){
		$type = Yii::app()->request->getParam('type','introduce');
		if(!in_array($type,array('introduce','cooperation','join','contact'))){
			$type = 'introduce';
		}
		$this->viewer['s'] = array('introduce'=>'','cooperation'=>'','join'=>'','contact'=>'');
		$this->viewer['s'][$type] = 'current';
		$this->cs->registerCssFile($this->pipiFrontPath.'/css/common/help.css?token='.$this->hash,'all');
		
		$description = array(
			'introduce'=>'公司介绍',
			'cooperation'=>'市场合作',
			'join'=>'加入我们',
			'contact'=>'联系方式',
		);
		$this->setPageTitle(Yii::t('seo','seo_aboutus_title',array('{category}'=>$description[$type])));
		$this->setPageKeyWords(Yii::t('seo','seo_aboutus_keywords'));
		$this->setPageDescription(Yii::t('seo','seo_aboutus_description'));
		
		$this->render('aboutus_'.$type);
	}
	
	public function actionAnnouce(){
		$threaId = Yii::app()->request->getParam('thread_id');
		if($threaId <= 0){
			throw new CHttpException(500,'公告ID不存在');
		}
		$bbsService = new BbsbaseService();
		$thread = $bbsService->getThreadInfo($threaId);
		$post = $bbsService->getPostList($threaId);
		$this->cs->registerCssFile($this->pipiFrontPath.'/css/common/announcement.css?token='.$this->hash,'all');
		$this->setPageTitle(Yii::t('seo','seo_aboutus_title',array('{category}'=>'公告')));
		$this->setPageKeyWords(Yii::t('seo','seo_aboutus_keywords'));
		$this->setPageDescription(Yii::t('seo','seo_aboutus_description'));
		$this->render('annouce',array('thread'=>$thread,'post'=>$post[0]));
	}

	public function actionSuggest(){
		$operateService = new OperateService();
		$types = $operateService->getSuggestType();
		$type = Yii::app()->request->getParam('type',0);
		$this->cs->registerCssFile($this->pipiFrontPath.'/css/common/help.css?token='.$this->hash,'all');
		$this->cs->registerScriptFile($this->pipiFrontPath.'/css/common/jquery.form.js?token='.$this->hash,'all');
		$this->render('suggest',array('types'=>$types,'type'=>$type));
	}
	
	public function actionDoSuggest(){
		$content = Yii::app()->request->getPost('content');
		$type = Yii::app()->request->getPost('type');
		$contact = Yii::app()->request->getPost('contact');
		
		if(!$this->isLogin){
			exit ( json_encode ( array ('flag' => 0, 'message' => '您还没登录哦' ) ) );
		}
		
		if(!$content || !$contact){
			exit ( json_encode ( array ('flag' => 0, 'message' => '参数错误' ) ) );
		}
		
		$operateService = new OperateService();
		
		$ret['contact'] = $contact;
		$ret['content'] = $content;
		$ret['type'] = $type;
		$ret['uid'] = Yii::app()->user->id ?  Yii::app()->user->id : 0;
		$ret['create_time'] = time();
		$ret['attach'] = $operateService->uploadSingleImages('attach','suggest',true);
		
		if($operateService->saveSuggest($ret)){
			exit ( json_encode ( array ('flag' => 1, 'message' => '提交成功' ) ) );
		}else{
			exit ( json_encode ( array ('flag' => 0, 'message' => $operateService->getError() ) ) );
		}
	}
	/**
	 * 主播推荐
	 */
	public function actionRecommend()
	{
		$this->cs->registerCssFile($this->pipiFrontPath.'/css/recommend/recommender.css?token='.$this->hash,'all');
		$this->render('xiaoyi');
	}
	
	/**
	 * 皮蛋比例调整说明页
	 */
	public function actionUpgrade(){
		$this->cs->registerCssFile($this->pipiFrontPath.'/css/other/update.css?token='.$this->hash,'all');
		$this->render('upgrade');
	}
	
	/**
	 * 星探页
	 */
	public function actionStar(){
		$this->cs->registerCssFile($this->pipiFrontPath.'/css/other/talent.css?token='.$this->hash,'all');
		$this->render('star');
	}
	
	public function actionMyAttentionDotey(){
		if(!$this->isLogin){
			return array();
		}
		$uid = Yii::app()->user->id;
		
		$indexPageService = new IndexPageService();
		
		$archivesService = new ArchivesService();
		$attentions=$manages=array();
/* 		$attentionArchives = $archivesService->getUserAttentionArchives($uid,true);
		$attentions=array_merge($attentionArchives['living'],$attentionArchives['wait']); */
		$attentions=$indexPageService->getDoteyLayer($uid, 'attention');
		
		$attentionsArchivesIds=array();
		foreach($attentions['list'] as $row){
			$attentionsArchivesIds[]=$row['archives_id'];
		}
		$userListService=new UserListService();
		$attentionsNum=$userListService->getArchivesOnlineNumByArchivesIds($attentionsArchivesIds);
		$attentionsText='';
		if($attentions['list']){
			$attentionsText.='<div class="connerbd-list"><div class="connerbdcon">';
			foreach($attentions['list'] as $row){
				if($row['is_attention']==1){
					$attentionsText.='<dd class="attent"><a href="'.$this->getTargetHref('/'.$row['uid'],true,true).'"><img src="'.$row['display_small'].'"><span>'.$row['title'].'</span><em class="viewnum">'.$attentionsNum[$row['archives_id']].'</em></a></dd>';
				}else{
					$attentionsText.='<dd><a href="'.$this->getTargetHref('/'.$row['uid'],true,true).'"><img src="'.$row['display_small'].'"><span>'.$row['title'].'</span><em class="viewnum">'.$attentionsNum[$row['archives_id']].'</em></a></dd>';
				}
			}
			$attentionsText.='</div></div>';
			if(count($attentions['list'])>9){
				$attentionsText.='<div class="changebtn"><p><span class="prevb"></span><span class="nextb"></span></p><em class="pagenums"></em></div>';
			}
		}else{
			$attentionsText.='<p class="nodata">当前被关注的主播都没有在直播，去看看其他主播吧！</p>';
		}
		
		$attentionArchivesNum=$attentions['total'];
		$attentionLivingArchivesNum=count($attentions['list']);
		
/* 		$manageArchives = $archivesService->getUserManagerArchives($uid,true,true);
		$manages=array_merge($manageArchives['living'],$manageArchives['wait']); */
		$manages=$indexPageService->getDoteyLayer($uid, 'manager');
		$managesArchivesIds=array();
		foreach($manages['list'] as $row){
			$managesArchivesIds[]=$row['archives_id'];
		}
		$managesNum=$userListService->getArchivesOnlineNumByArchivesIds($managesArchivesIds);
		$manageText='';
		if($manages['list']){
			$manageText.='<div class="connerbd-list"><div class="connerbdcon">';
			foreach($manages['list'] as $row){
				if($row['is_attention']==1){
					$manageText.='<dd class="attent"><a href="'.$this->getTargetHref('/'.$row['uid'],true,true).'"><img src="'.$row['display_small'].'"><span>'.$row['title'].'</span><em class="viewnum">'.$managesNum[$row['archives_id']].'</em></a></dd>';
				}else{
					$manageText.='<dd><a href="'.$this->getTargetHref('/'.$row['uid'],true,true).'"><img src="'.$row['display_small'].'"><span>'.$row['title'].'</span><em class="viewnum">'.$managesNum[$row['archives_id']].'</em></a></dd>';
				}
					
			}
			$manageText.='</div></div>';
			if(count($manages['list'])>9){
				$manageText.='<div class="changebtn"><p><span class="prevb"></span><span class="nextb"></span></p><em class="pagenums"></em></div>';
			}
		}else{
			$manageText.='<p class="nodata">当前被管理的主播都没有在直播，去看看其他主播吧！</p>';
		}
		
		$manageArchivesNum=$manages['total'];
		$manageLivingArchivesNum=count($manages['list']);
		
/* 		$seeArchives = $archivesService->getUserLatestSeeArchives($uid,true,true);
		$viewArchives=array_merge($seeArchives['living'],$seeArchives['wait']); */
		//默认出现我看过的数据
		
		$viewArchives=$indexPageService->getDoteyLayer($uid, 'latestSee');
		$seeArchivesNum=$viewArchives['total'];
		$viewArchives = array_slice($viewArchives, 0, 9);
		$viewArchivesIds=array();
		foreach($viewArchives['list'] as $row){
			$viewArchivesIds[]=$row['archives_id'];
		}
		$viewNum=$userListService->getArchivesOnlineNumByArchivesIds($viewArchivesIds);
		$seeArchivesText='';
		if($viewArchives['list']){
			$seeArchivesText.='<div class="connerbd-list"><div class="connerbdcon">';
			foreach($viewArchives['list'] as $row){
				if($row['is_attention']==1){
					$seeArchivesText.='<dd class="attent"><a href="'.$this->getTargetHref('/'.$row['uid'],true,true).'"><img src="'.$row['display_small'].'"><span>'.$row['title'].'</span><em class="viewnum">'.$viewNum[$row['archives_id']].'</em></a></dd>';
				}else{
					$seeArchivesText.='<dd><a href="'.$this->getTargetHref('/'.$row['uid'],true,true).'"><img src="'.$row['display_small'].'"><span>'.$row['title'].'</span><em class="viewnum">'.$viewNum[$row['archives_id']].'</em></a></dd>';
				}
			}
			$seeArchivesText.='</div></div>';
			if(count($viewArchives['list'])>9){
				$seeArchivesText.='<div class="changebtn"><p><span class="prevb"></span><span class="nextb"></span></p><em class="pagenums"></em></div>';
			}
		}else{
			$seeArchivesText.='<p class="nodata">还没有看过谁，去<a class="pink" href="'.$this->getTargetHref('channel/category',true,true).'">感受一下美女主播的欢乐吧！</a></p>';
		}
		
		exit(json_encode(array('attentionArchivesNum'=>$attentionArchivesNum,
			'attentionLivingArchivesNum'=>$attentionLivingArchivesNum,
			'attentionArchives'=>$attentionsText,
			'manageArchivesNum'=>$manageArchivesNum,
			'manageLivingArchivesNum'=>$manageLivingArchivesNum,
			'manageArchives'=>$manageText,
			'seeArchivesNum'=>$seeArchivesNum,
			'seeArchives'=>$seeArchivesText)));
	}
	
	public function actionsGetCheckinItems(){
		if(!$this->isLogin){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('user','You are not logged'))));
		}
		$uid = Yii::app()->user->id;
		$indexPageService=new IndexPageService();
		$monthCard=$indexPageService->getMonthCard($uid);
		$list=array();
		$list['list']=$indexPageService->getCheckinItems($uid,true,$monthCard);
		$allCheck=false;
		foreach($list as $row){
			if($row['status']==0){
				$allCheck=true;
			}
		}
		$list['allCheck']=$allCheck;
		$list['count']=$indexPageService->getCheckinDays($uid);
		$list['monthHref']=$monthCard?$this->createUrl('account/moon'):$this->createUrl('shop/monthcard');
		exit(json_encode(array('flag'=>1,'data'=>$list)));
	}
	
	
	
	
}

?>