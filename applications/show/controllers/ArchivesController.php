<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: lei wei $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z lei wei  $
 * @package
 */
define('YELLOW_VIP_FREE_USER_LABEL',15);
define('PURPLE_VIP_FREE_USER_LABEL',25);
class ArchivesController extends PipiController {
	const USER_DEFAULT_NUM=50; //用户列表默认数量
	
	public function actionIndex(){
		$contrller = Yii::app()->getController();
		$clientScript = Yii::app()->getClientScript();
		$staticPath = $contrller->pipiFrontPath;
		$clientScript->registerCssFile($staticPath.'/css/archives/living.css?token='.$contrller->hash);
		$clientScript->registerCssFile($staticPath.'/css/archives/anchorinfo.css?token='.$contrller->hash);
		$clientScript->registerCssFile($staticPath.'/css/archives/fanchart.css?token='.$contrller->hash);
		$clientScript->registerScriptFile($staticPath.'/js/archives/common.js?token='.$contrller->hash);
		$doteyId=intval(Yii::app()->request->getParam('uid'));
		$userNumberService=new UserNumberService();
		$userNumber=$userNumberService->isUseNumber($doteyId);
		$doteyId=empty($userNumber)?$doteyId:$userNumber['uid'];
		if($doteyId<=0){
			$this->render('/public/error',array('errorMsg'=>'直播间错误'));
			exit;
		}
		
		$archivesService=new ArchivesService();
		$archivesList=$archivesService->getArchivesByUids($doteyId);
		
		if(empty($archivesList)){
			$this->render('/public/error',array('errorMsg'=>'直播间错误'));
			exit;
		} 
		foreach($archivesList as $_archives){
			if($_archives['cat_id']==1){
				$archives=$_archives;
			}
		}
		
		if($archives==null){
			$this->render('/public/error',array('errorMsg'=>'直播间错误'));
			exit;
		}
		
		$userService=new UserService();
		$userBase=$userService->getUserFrontsAttributeByCondition($doteyId,true,true);
		if($userBase){
			if(!$userService->hasBit(intval($userBase['ut']),USER_TYPE_DOTEY)||$userBase['us']==USER_STATUS_OFF){
				$this->render('/public/error',array('errorMsg'=>'不是主播或账号被停封'));
				exit;
			}
		}else{
			$userBase=$userService->getUserBasicByUids(array($doteyId));
			if(!$userService->hasBit(intval($userBase['user_type']),USER_TYPE_DOTEY)||$userBase['user_status']==USER_STATUS_OFF){
				$this->render('/public/error',array('errorMsg'=>'不是主播或账号被停封'));
				exit;
			}
		}
		$uid=Yii::app()->user->id;
		if($uid>0&&$uid!=$doteyId){
			if(isset($archives['live_record']['record_id'])){
				$archivesService->saveLatestSeeArchives(array('uid'=>$uid,'archives_id'=>$archives['archives_id'],'archives_record_id'=>$archives['live_record']['record_id']));
			}
		}
		//保存一周内登陆或未登陆用户访问过的直播间
		$cookies = Yii::app()->request->getCookies();
		$viewArchiveIds = empty($cookies['view_archives']) ? array() : explode(',', $cookies['view_archives']);
		$viewArchiveIds = array_diff($viewArchiveIds, array($archives['archives_id']));
		$viewArchiveIds = array_merge(array($archives['archives_id']), $viewArchiveIds);
		$cookie = new CHttpCookie('view_archives', implode(',', $viewArchiveIds));
		$cookie->expire = time()+86400*7;
		Yii::app()->request->cookies['view_archives'] = $cookie;
		
		if($archives['uid']==$uid){
			$clientScript->registerScriptFile($staticPath.'/js/archives/show.js?token='.$contrller->hash,CClientScript::POS_END);
			$clientScript->registerScriptFile($staticPath.'/js/common/date.js?token='.$contrller->hash,CClientScript::POS_END);
		}
		$archivesData=$archivesService->getArchivesDataFromRedis($archives['uid'],$archives['archives_id']);
		
		$chatServer=$archivesService->getChatServerByArchivesId($archives['archives_id']);
		
		$archives['chat_server']['domain']=$chatServer['domain'];
		$archives['chat_server']['policy_port']=$chatServer['policy_port'];
		$archives['chat_server']['data_port']=$chatServer['data_port'];
		
		$doteyList=$archivesService->getArchivesUserByArchivesIds($archives['archives_id']);
		$dotey=array();
		foreach($doteyList[$archives['archives_id']] as $row){
			$dotey[]=$row['uid'];
		}
		$userJson=new UserJsonInfoService();
		$doteyBase=$userJson->getUserInfos($dotey,false);
		$doteyUser=array();
		foreach ($doteyBase as $key=>$row){
			$doteyUser[$key]['uid']=$row['uid'];
			$doteyUser[$key]['nickname']=$row['nk'];
		}
		$archives['dotey_list']=$doteyUser;
		$userExetend=$archivesData['dotey_info_'.$archives['uid']];
		$doteyInfo['uid']=$doteyId;
		$doteyInfo['famliy_medal']=FamilyService::getInstance()->getMyMedals($doteyId);
		$doteyInfo['middle_avatar']=$userService->getUserAvatar($doteyId,'middle');
		$doteyInfo['small_avatar']=$userService->getUserAvatar($doteyId,'small');
		$doteyInfo['nickname']=$userBase['nk'];
		$doteyInfo['rank']=$userBase['dk'];
		$doteyInfo['cuch']=empty($userBase['cuch'])?0:$userBase['cuch'];
		$doteyInfo['charm']=empty($userBase['ch'])?0:$userBase['ch'];
		$doteyInfo['nxch']=empty($userBase['nxch'])?99999999:$userBase['nxch'];
		$doteyInfo['birthday']=$userExetend['birthday'];
		$doteyInfo['province']=$userExetend['province'];
		$doteyInfo['city']=$userExetend['city'];
		$doteyInfo['profession']=$userExetend['profession'];
		$doteyInfo['description']=$userExetend['description'];
		if(empty($doteyInfo['birthday'])||empty($doteyInfo['province'])||empty($doteyInfo['city'])||empty($doteyInfo['profession'])||empty($doteyInfo['description'])){
			$doteyInfo['doteyProfile']=true;
		}else{
			$doteyInfo['doteyProfile']=false;
		}
		
		//皇冠粉丝
		$crown=$archivesData['crown_'.$archives['archives_id']];
		
		//用户列表
		$userList=$archivesData['test_userlist_archives_'.$archives['archives_id']];
		
		//直播间发言设置
		$chat_set=$archivesData['chat_set_'.$archives['archives_id']];
		$allowSong=$archivesData['allow_song_'.$archives['archives_id']];
		
		$topGiftList=$archivesData['most_dedication'];
		$topGiftList=array_splice($topGiftList,0,3);
		
		//排行版广告
		$operateServ = new OperateService();
		$listAds=$operateServ->getLivePageAdv($archives['archives_id'], $doteyId,true);
		//动态、关注和粉丝数
		$weiboService=new WeiboService();
		$weibo=$weiboService->getWeiboStatisticsByUid($doteyId);
		
		//直播间礼物
		$giftList=$archivesData['archives_gift_'.$archives['archives_id']];
		
		//本场粉丝榜
		$archives_dedication=$archivesData['archives_dedication_'.$archives['archives_id']];

		//本周粉丝榜
		$week_dedication=$archivesData['week_dedication_'.$archives['archives_id']];
		
		
		//本场情谊榜
		$archives_friendly=$archivesData['archives_friendly_'.$archives['archives_id']];
		
		//本场情谊榜
		$week_archives_friendly=$archivesData['week_archives_friendly_'.$archives['archives_id']];
		//礼物消息
		$archives_dy_msg=$archivesData['archives_dy_msg_'.$archives['archives_id']];

		//主播魅力值今日和本周排行
		$allDoteyCharmTodayRank=$archivesData['all_dotey_charm_today_rank_uid_'];
		$allDoteyCharmWeekRank=$archivesData['all_dotey_charm_week_rank_uid_'];
		
		//礼物之星
		$giftStarService=new GiftStarService();
		$weekId=$giftStarService->getThisWeekId();
		$giftStarInfo=$giftStarService->getLivingboxGiftStar($doteyId,$weekId);
		
		//生日快乐
		$happyBirthdayService=new HappyBirthdayService();
		$doteyBirthdayInfo=$happyBirthdayService->getBirthdayDoteyInfoById(date("Y-m-d"),$doteyId);
		
		//万圣节
		$halloweenService=new HalloweenService();
		$doteyHalloweenInfo=$halloweenService->getFlashByDoteyId($doteyId);

		//表情
		$faceService=new FaceService();
		$faceList=$faceService->getFaceFromCache();
		$face=array();
		foreach($faceList as $row){
			$face[]=array('code'=>$row['code'],'image'=>$row['image'],'type'=>$row['type'],'name'=>$row['name']);
		}
		
		//全站广播
		$broadcastService=new BroadcastService();
		$broadcastList=$broadcastService->getBroadcastFromCache();
		if(count($broadcastList)>2){
			$broadcastList=array_slice($broadcastList, (count($broadcastList)-2),2);
		}
		
		//随机获取正在直播
		$userInfo=$userService->getUserFrontsAttributeByCondition($uid,true,true);
		$reg_sign=Yii::app()->request->cookies['reg_sign'];
		$reg_sign=$reg_sign?$reg_sign:Yii::app()->request->getParam('sign');
		if($reg_sign){
			if($uid){
				if(!$userService->hasBit(intval($userInfo['ut']),USER_TYPE_DOTEY)){
					$livingArchives=self::getRandLivingArchives($doteyId);
				}
			}else{
				$livingArchives=self::getRandLivingArchives($doteyId);
			}
		}
		
		
		$this->setPageTitle(Yii::t('seo','seo_archives_title',array('{nickname}'=>$userBase['nk'])));
		$this->setPageKeyWords(Yii::t('seo','seo_archives_keywords',array('{nickname}'=>$userBase['nk'])));
		$this->setPageDescription(Yii::t('seo','seo_archives_description',array('{nickname}'=>$userBase['nk'],'{weibos}'=>$weibo['weibos'],'{attentions}'=>$weibo['attentions'],'{fans}'=>$weibo['fans'])));
		$this->render('index',array('archives'=>$archives,
								'dotey'=>$doteyInfo,
								'topGiftList'=>$topGiftList,
								'giftList'=>$giftList,
								'archives_dy_msg'=>$archives_dy_msg,
								'archives_dedication'=>$archives_dedication,
								'week_dedication'=>$week_dedication,
								'archives_friendly'=>$archives_friendly,
						        'weeek_archives_friendly'=>$weeek_archives_friendly,
								'listAds'=>$listAds,
								'crown'=>$crown,
								'userList'=>$userList,
								'chat_set'=>$chat_set,
								'allowSong'=>$allowSong,
								'allow_song'=>$archivesData['allow_song_'.$archives['archives_id']],
								'weibo'=>$weibo,
								'staticPath'=>$staticPath,
								'charmRank'=>array('today'=>$allDoteyCharmTodayRank,'week'=>$allDoteyCharmWeekRank),
								'giftStarInfo'=>$giftStarInfo,
								'doteyBirthdayInfo'=>$doteyBirthdayInfo,
								'doteyHalloweenInfo'=>$doteyHalloweenInfo,
								'faceList'=>$face,
								'broadcastList'=>$broadcastList,
								'livingArchives'=>$livingArchives
							));
	}
	
	
	public function actionSensWord(){
		$wordService=new WordService();
		$words=$wordService->getChatWord(true);
		$_words = array();
		foreach($words as $key=>$row){
			$_tmp['type'] =  $row['type'];
			$_tmp['name'] =  str_replace('*','.+?',$row['name']);
			$_tmp['replace'] =  $row['replace'];
			$_words[] = $_tmp;
		}
		echo "var word=".json_encode($_words).";";
	}

	/**
	 * 获取直播间礼物列表
	 * @author leiwei
	 */
	public function actionGetGiftList(){
		$archives_id=Yii::app()->request->getParam('archives_id');
		if($archives_id<=0) $gift=array();
		$archivesService=new ArchivesService();
		//最大贡献值礼物
		$maxGift=$archivesService->getArchivesRelationData($archives_id,'most_archives_dedication');
		echo json_encode($maxGift);
	}



	/**
	 * 获取用户属性
	 * @author leiwei
	 */
	public function actionGetUserJsonInfo(){
		$from_uid=Yii::app()->request->getParam('from_uid');
		$to_uid=Yii::app()->request->getParam('to_uid');
		$content=Yii::app()->request->getParam('content');
		$userJson=new UserJsonInfoService();
		$from_json=$userJson->getUserInfo($from_uid,false);
		$to_json=$userJson->getUserInfo($to_uid,false);
		$json_info=array('from_uid'=>$from_uid,'from_nickname'=>$from_json['nk'],'to_uid'=>$to_uid,'to_nickname'=>$to_json['nk'],'from_json'=>$from_json,'to_json'=>$to_json,'content'=>$content,'creat_time'=>date('H:i'));
		exit(json_encode($json_info));
	}

	/**
	 * 获取本场或本周粉丝榜
	 * @author leiwei
	 */
	public function actionGetArchivesRankList(){
		$archives_id=Yii::app()->request->getParam('archives_id');
		$type=Yii::app()->request->getParam('type');
		$archives_dedication=array();
		$archivesService = new ArchivesService();
		$archives_dedication=$archivesService->getArchivesRelationData($archives_id,$type);
		echo json_encode($archives_dedication);
	}
	
	/**
	 * 获取本场或本周情谊榜
	 * @author leiwei
	 */
	public function actionGetArchivesFriendly(){
		$archives_id=Yii::app()->request->getParam('archives_id');
		$type=Yii::app()->request->getParam('type');
		$archives_friendly=array();
		$archivesService = new ArchivesService();
		$archives_friendly=$archivesService->getArchivesRelationData($archives_id,$type);
		echo json_encode($archives_friendly);
	}


	public function actionLatestRegister(){
		if(!$this->isLogin){
			return array();
		}
		$uid = Yii::app()->user->id;
		$archivesService = new ArchivesService();
		$latestSeeArchives = $archivesService->getLatestRegisterDoteyArchives($uid,30,true);
		$latestSeeArchives['attentionType'] = 'latestRegister';
		$this->renderPartial('liveArchivesTemplate',$latestSeeArchives);
	}

	public function actionGetUserBag(){
		$uid = Yii::app()->user->id;
		$giftBagService=new GiftBagService();
		$giftBagList=$giftBagService->getUserGiftBagByUids($uid);
		$giftService=new GiftService();
		$giftList=$giftService->getGiftList(array(),true);
		$userBagGiftList=array();
		$i=0;
		foreach($giftBagList[$uid] as $key=>$row){
			if($row['num']>0){
				$userBagGiftList[$i]['gift_id']=$row['gift_id'];
				$userBagGiftList[$i]['zh_name']=$giftList[$row['gift_id']]['zh_name'];
				$userBagGiftList[$i]['image']=$giftList[$row['gift_id']]['image'];
				$userBagGiftList[$i]['pipiegg']=$giftList[$row['gift_id']]['pipiegg'];
				$userBagGiftList[$i]['num']=$row['num'];
				$effects=array();
				if(isset($giftList[$row['gift_id']]['effects'])){
					foreach($giftList[$row['gift_id']]['effects'] as $val){
						if($val['num']>1){
							$effects[]=$val['num'];
						}
					}
				}
				$userBagGiftList[$i]['effects']=!empty($effects)?implode('|',$effects):null;
				$i++;
			}
		}
		echo json_encode($userBagGiftList);
	}

	public function actionConfirmSendGift(){
		$archivesId=Yii::app()->request->getParam('archivesId');
		$to_uid=Yii::app()->request->getParam('to_uid');
		$giftId=Yii::app()->request->getParam('giftId');
		$giftNum=Yii::app()->request->getParam('giftNum');
		$giftType=Yii::app()->request->getParam('giftType');
		$remark=Yii::app()->request->getParam('remark');
		$uid = Yii::app()->user->id;
		//$uid=Yii::app()->request->getParam('uid');
		if($archivesId<=0||$to_uid<=0||$giftId<=0||$giftNum<=0||empty($giftType)){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('common','Parameters are wrong'))));
		}
		if(!$this->isLogin){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('user','You are not logged'))));
		}
		$forbidenService=new ForbidenService();
		if($forbidenService->getArchivesKickout($archivesId,$uid)){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('archives','You have been kicked out of archives'))));
		}
		if($giftNum>9999){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('gift','Gift numeber not greater than 9999'))));
		}
		$giftTypeList=array('common','bag');
		if(!in_array($giftType, $giftTypeList)){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('gift','The wrong gift way'))));
		}
		$giftService=new GiftService();
		$lastSendGiftTime=$giftService->getLastSendGiftTime($uid);
		if(time()-$lastSendGiftTime<2){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('gift','Gifts are processing operation...'))));
		}
		if($giftType=='common'){
			try{
				$result=$giftService->sendGift($uid,$to_uid,$archivesId,$giftId,$giftNum,$remark);
			}catch (Exception $e){
				$error=$e->getMessage();
				$filename = DATA_PATH.'runtimes/user_attribute_exception.txt';
				error_log('用户送礼异常：'.$error."\n\r",3,$filename);
			}
			if(!$result){
				$msg=$giftService->getError();
				if($msg=='皮蛋不足'){
					exit(json_encode(array('flag'=>-1,'message'=>Yii::t('gift',$msg))));
				}else{
					exit(json_encode(array('flag'=>0,'message'=>Yii::t('gift',$msg))));
				}
				
			}else{
				exit(json_encode(array('flag'=>1,'message'=>Yii::t('gift','Send gifts successed'))));
			}
		}else if($giftType=='bag'){
			$giftBagService=new GiftBagService();
			$userGiftBag=$giftBagService->getUserBagByGiftIds($uid,$giftId);
			if($userGiftBag['num']-$giftNum<0){
				exit(json_encode(array('flag'=>0,'message'=>Yii::t('giftBag','Gift quantity not sufficient'))));
			}
			try{
				$result=$giftBagService->sendBagGift($uid,$to_uid,$archivesId,$giftId,$giftNum,$remark);
			}catch (Exception $e){
				$error=$e->getMessage();
				$filename = DATA_PATH.'runtimes/user_attribute_exception.txt';
				error_log('用户背包送礼异常：'.$error."\n\r",3,$filename);
			}
			if(!$result){
				$msg=$giftBagService->getError();
				exit(json_encode(array('flag'=>0,'message'=>Yii::t('gift',$msg))));
			}else{
				exit(json_encode(array('flag'=>2,'message'=>Yii::t('gift','Send gifts successed'))));
			}
		}
		
	}

	public function actionGetLabelInfo(){
		$prop_id=Yii::app()->request->getParam('prop_id');
		$propsService = new PropsService();
		$category = $propsService->getPropsCategoryByEnName('label');
		$attrList = $propsService->getPropsCatAttrtByIds($category['cat_id'], 1);
		foreach ($attrList as $row) {
			if ($row['attr_enname'] == 'label_category') {
				$labelCatList = $row['list'];
			}
		}
		unset($attrList);
		$attribute=$propsService->getPropsAttributeByPropIds($prop_id);
		$attributs=array();
		foreach($attribute[$prop_id] as $key=>$row){
			$attributs[$row['attr_enname']] =$row['value'];

		}
		print_r($attributs);
	}

	/**
	 * 获取用户列表
	 * @author leiwei
	 */
	public function actionGetUserList(){
		$archives_id=Yii::app()->request->getParam('archives_id');
		$num=Yii::app()->request->getParam('num');
		$num=empty($num)?50:$num;
		$userList=array();
		if($archives_id>0){
			$userListService=new UserListService();
			$userList=$userListService->getUserList($archives_id,$num);
		}
		exit(json_encode($userList));
	}
	
	public function actionAddUserToList(){
		$archives_id=Yii::app()->request->getParam('archives_id');
		$uid=Yii::app()->request->getParam('uid');
		$userListService=new UserListService();
		$userList=$userListService->addUserToUserList($archives_id,$uid);
		exit(json_encode($userList));
	}


	public function actionStickUserLabel(){
		$archives_id=Yii::app()->request->getParam('archives_id');
		$img=Yii::app()->request->getParam('img');
		$uid=Yii::app()->request->getParam('uid');
		$userListService=new UserListService();
		echo $userList=$userListService->labelToUserList($archives_id,$uid,$img);
	}

	public function actionRemoveUserLabel(){
		$archives_id=Yii::app()->request->getParam('archives_id');
		$uid=Yii::app()->request->getParam('uid');
		$userListService=new UserListService();
		echo $userList=$userListService->labelToUserList($archives_id,$uid);
	}

	/**
	 * 获取贴条列表
	 *
	 * @return array 返回标签列表
	 *
	 */
	public function actionGetLabelList() {
		$propsService = new PropsService();
		$category = $propsService->getPropsCategoryByEnName('label');
		// 获取贴条下的分类
		$attrList = $propsService->getPropsCatAttrtByIds($category['cat_id'], 1);
		foreach ($attrList as $row) {
			if ($row['attr_enname'] == 'label_category') {
				$labelCatList = $row['list'];
			}
		}
		$labelHtml='<div class="stick-hd"><ul class="clearfix">';
		foreach($labelCatList as $key=>$row){
			if($key>0){
				$labelHtml.='<li class="end">'.$row.'</li>';
			}else{
				$labelHtml.='<li>'.$row.'</li>';
			}

		}
		$labelHtml.='</ul></div>';
		unset($attrList);
		$propsList = $propsService->getPropsByCatId($category['cat_id'], false, true);
		foreach ($propsList as $key => $row) {
			$attribute = $propsService->buildDataByIndex($row['attribute'], 'attr_enname');
			$props[$key]['prop_id'] = $row['prop_id'];
			$props[$key]['cat_id'] = $row['cat_id'];
			$props[$key]['sort'] = $row['sort'];
			$props[$key]['category_id'] = $attribute['label_category']['value'];
			$props[$key]['category'] = $labelCatList[$attribute['label_category']['value']];
			$props[$key]['picture'] = $attribute['label_picture']['value'];
			$props[$key]['timeout'] = $attribute['label_timeout']['value'];
			$props[$key]['name'] = $row['name'];
			$props[$key]['en_name'] = $row['en_name'];
			$props[$key]['pipiegg'] = $row['pipiegg'];
			$props[$key]['image'] = $row['image'];
		}
		foreach ($props as $k=>$v){
			$keysvalue[$k] = $v['sort'];
		}
		asort($keysvalue);
		foreach ($keysvalue as $k=>$v){
				$new_props[$k] = $props[$k];
		}
		unset($propsList);
		$labelHtml.='<div class="stick-bd">';
		foreach ($new_props as $key => $row) {
			if ($row['category'] == $labelCatList[$row['category_id']]) {
				$labelProps[$row['category_id']]['category'] = $row['category'];
				$labelProps[$row['category_id']]['list'][$key] = $row;
			}
		}
		asort($labelProps);
		foreach($labelProps as $row){
			$labelHtml.='<ul class="clearfix">';
			foreach($row['list'] as $val){
				$labelHtml.='<li><a href="javascript:UserList.stickLabel('.$val['prop_id'].')" title="'.$val['name'].'"><Img src="'.Yii::app()->params->images_server['url'].$val['image'].'"></a></li>';
			}
			$labelHtml.='</ul>';
			$labelHtml.='<script>$(function(){$("#StickBox").slide({titCell:".stick-hd li",mainCell:".stick-bd",titOnClassName:"overed",trigger:"click",delayTime:0});})</script>';
		}
		echo  $labelHtml;
	}

	public function actionStickLabel(){
		$prop_id=Yii::app()->request->getParam('prop_id');
		$to_uid=Yii::app()->request->getParam('to_uid');
		$archives_id=Yii::app()->request->getParam('archives_id');
		$uid=Yii::app()->user->id;
		if($prop_id <= 0){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('common','Parameters are wrong'))));
		}
		if(!$this->isLogin){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('user','You are not logged'))));
		}
		
		$forbidenService=new ForbidenService();
		if($forbidenService->getArchivesKickout($archives_id,$uid)){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('archives','You have been kicked out of archives'))));
		}
		
		$propsService = new PropsService();
		$labelInfo = $propsService->getPropsByIds($prop_id, true, true);
		$attribute = $propsService->buildDataByIndex($labelInfo[$prop_id]['attribute'], 'attr_enname');
		$userPropsService=new UserPropsService();
		$userLabel=$userPropsService->getUserLatestPropsOfUsedByCatId($to_uid,$labelInfo[$prop_id]['cat_id']);
		//判断是否被贴高级贴条
		if($userLabel['valid_time']>time()){
			$orgLabelInfo = $propsService->getPropsByIds($userLabel['prop_id'], true, true);
			$orgAttribute = $propsService->buildDataByIndex($orgLabelInfo[$userLabel['prop_id']]['attribute'], 'attr_enname');
			if($attribute['label_category']['value']<$orgAttribute['label_category']['value']){
				exit(json_encode(array('flag'=>0,'message'=>Yii::t('props','Ordinary stickers can not cover advanced stickers'))));
			}
		}
		
		$userJson = new UserJsonInfoService();
		$userInfo = $userJson->getUserInfo($uid,false);
		$userPropsService=new UserPropsService();
		$userListService=new UserListService();
		
		$userService = new UserService();
		$userBasic = $userService->getUserBasicByUids(array($uid, $to_uid));
		
		$user_label=0;
	
		if(isset($userInfo['vip'])){
			//查看用户vip免费使用贴条数量
			$userPropsService=new UserPropsService();
			$now=date('Y-m-d');
			$vip_use_num=$userPropsService->getUserPropsUserCountByCatId($uid,$labelInfo[$prop_id]['cat_id'],array('use_type'=>1,'start_time'=>$now,'end_time'=>$now.' 23:00:00'));
			if($userInfo['vip']['t']==1){
				$user_label=YELLOW_VIP_FREE_USER_LABEL-$vip_use_num;
			}elseif($userInfo['vip']['t']==2){
				$user_label=PURPLE_VIP_FREE_USER_LABEL-$vip_use_num;
			}
			
			if($user_label>0){
				$records['target_id']=$archives_id;
				$records['record_sid']=0;
				$records['uid']=$uid;
				$records['to_uid']=$to_uid;
				$records['prop_id']=$prop_id;
				$records['cat_id']=$labelInfo[$prop_id]['cat_id'];
				$records['use_type']=1;
				$records['num']=1;
				$records['valid_time']=time()+$attribute['label_timeout']['value']*60;
				$result=$userPropsService->saveUserPropsUse($records);
				if($result){
					$propRecord['uid'] = $uid;
					$propRecord['cat_id'] = $labelInfo[$prop_id]['cat_id'];
					$propRecord['prop_id'] = $prop_id;
					$propRecord['pipiegg'] = 0;
					$propRecord['dedication'] = 0;
					$propRecord['egg_points'] = 0;
					$propRecord['charm'] = 0;
					$propRecord['charm_points'] =0;
					$propRecord['vtime'] = time()+$attribute['label_timeout']['value']*60;
					$propRecord['info'] = '贴条('.$labelInfo[$prop_id]['name'].'*1)';
					$propRecord['source'] = 3;
					$propRecord['amount'] = 1;
					$userPropsService->saveUserPropsRecords($propRecord);
					$time=time()+$attribute['label_timeout']['value']*60;
					$newUserJson['lb']=array('img'=>$attribute['label_picture']['value'],'vt'=>$time);
					$userJson->setUserInfo($to_uid,$newUserJson);
					$vip_free_user_label=$user_label-1;
					$zmq=new PipiZmq();
					$zmq->sendZmqMsg(609,array('type'=>'update_json','uid'=>$to_uid,'json_info'=>$newUserJson));
					$eventData['archives_id']=$archives_id;
					$eventData['domain']=DOMAIN;
					$eventData['type']='localroom';
					$json_content['type']='stickLabel';
					$json_content['uid']=$uid;
					$json_content['nickname']=$userBasic[$uid]['nickname'];
					$json_content['to_uid']=$to_uid;
					$json_content['to_nickname']=$userBasic[$to_uid]['nickname'];
					$json_content['name']=$labelInfo[$prop_id]['name'];
					$eventData['json_content']=$json_content;
					$zmq->sendZmqMsg(606,$eventData);
					$userListService->saveArchivesLabel($archives_id,$to_uid);
					exit(json_encode(array('flag'=>1,'message'=>Yii::t('props','Vip free to use,has {num} times',array('{num}'=>$vip_free_user_label)))));
				}
			}

		}
		
		$common_label_info=array();
		if($user_label<=0){
			if($attribute['label_category']['value']==0){
				$common_label=$propsService->getPropsByEnName('common_label');
			}
			if($attribute['label_category']['value']==1){
				$common_label=$propsService->getPropsByEnName('high_label');
			}
			$common_label_info=$userPropsService->getUserValidPropsOfBagByPropId($uid,$common_label['prop_id']);
			$common_label_info=array_pop($common_label_info);
			if($common_label_info['num']>0){
				$user_label=$common_label_info['num'];
			}
			
		}
		if($user_label>0){
			$bag['prop_id']=$common_label_info['prop_id'];
			$bag['uid']=$uid;
			$bag['s_num']=-1;
			$bagRecord=$userPropsService->saveUserPropsBag($bag);
			$records['target_id']=$archives_id;
			$records['record_sid']=0;
			$records['uid']=$uid;
			$records['to_uid']=$to_uid;
			$records['prop_id']=$prop_id;
			$records['cat_id']=$labelInfo[$prop_id]['cat_id'];
			$records['use_type']=2;
			$records['num']=1;
			$records['valid_time']=time()+$attribute['label_timeout']['value']*60;
			$result=$userPropsService->saveUserPropsUse($records);
			
			$propRecord['uid'] = $uid;
			$propRecord['cat_id'] = $labelInfo[$prop_id]['cat_id'];
			$propRecord['prop_id'] = $prop_id;
			$propRecord['pipiegg'] = 0;
			$propRecord['dedication'] = 0;
			$propRecord['egg_points'] = 0;
			$propRecord['charm'] = 0;
			$propRecord['charm_points'] =0;
			$propRecord['vtime'] = time()+$attribute['label_timeout']['value']*60;
			$propRecord['info'] = '贴条('.$labelInfo[$prop_id]['name'].'*1)';
			$propRecord['source'] = 3;
			$propRecord['amount'] = 1;
			
			$userPropsService->saveUserPropsRecords($propRecord);
			$time=time()+$attribute['label_timeout']['value']*60;
			$zmq=new PipiZmq();
			$newUserJson['lb']=array('img'=>$attribute['label_picture']['value'],'vt'=>$time);
			$userJson->setUserInfo($to_uid, $newUserJson);
			$zmq->sendZmqMsg(609,array('type'=>'update_json','uid'=>$to_uid,'json_info'=>$newUserJson));
			$eventData['archives_id']=$archives_id;
			$eventData['domain']=DOMAIN;
			$eventData['type']='localroom';
			$json_content['type']='stickLabel';
			$json_content['uid']=$uid;
			$json_content['nickname']=$userBasic[$uid]['nickname'];
			$json_content['to_uid']=$to_uid;
			$json_content['to_nickname']=$userBasic[$to_uid]['nickname'];
			$json_content['name']=$labelInfo[$prop_id]['name'];
			$eventData['json_content']=$json_content;
			
			$zmq->sendZmqMsg(606,$eventData);
			$quantity=$common_label_info['num']-1;
			$userListService->saveArchivesLabel($archives_id,$to_uid);
			exit(json_encode(array('flag'=>1,'message'=>Yii::t('props','Common label use,has {num} times',array('{num}'=>$quantity)))));
		}else{
			$attrList = $propsService->getPropsCatAttrtByIds(2, 1);
			foreach ($attrList as $row) {
				if ($row['attr_enname'] == 'label_category') {
					$labelCatList = $row['list'];
				}
			}
			exit(json_encode(array('flag'=>2,'message'=>Yii::t('props','The use of {category} stickers, will cost you <em class="pink">{pipiegg}</em> preserved egg',array('{category}'=>$labelCatList[$attribute['label_category']['value']],'{pipiegg}'=>number_format($labelInfo[$prop_id]['pipiegg'],1))))));
		}

	}
	
	/**
	 * 更新主播等级进度条
	 */
	public function actionGetDoteyCharm(){
		$doteyId=Yii::app()->request->getParam('doteyId');
		$userService=new UserService();
		$userBase=$userService->getUserFrontsAttributeByCondition($doteyId,true,true);
		$doteyInfo['rank']=$userBase['dk'];
		$doteyInfo['cuch']=empty($userBase['cuch'])?0:$userBase['cuch'];
		$doteyInfo['charm']=empty($userBase['ch'])?0:$userBase['ch'];
		$doteyInfo['nxch']=empty($userBase['nxch'])?99999999:$userBase['nxch'];
		$doteyInfo['nowRank']=$doteyInfo['charm']-$doteyInfo['cuch'];
		$doteyInfo['nextRank']=$doteyInfo['nxch']-$doteyInfo['cuch'];
		exit(json_encode($doteyInfo));
	}
	

	/**
	 * 移除贴条
	 */
	public function actionRemoveLabel(){
		$to_uid=Yii::app()->request->getParam('to_uid');
		$archives_id=Yii::app()->request->getParam('archives_id');
		$uid=Yii::app()->user->id;
		if($to_uid <= 0||$archives_id<=0){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('common','Parameters are wrong'))));
		}

		if(!$this->isLogin){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('user','You are not logged'))));
		}
		if($to_uid!=$uid){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('props','only remove my stickers'))));
		}
		
		$forbidenService=new ForbidenService();
		if($forbidenService->getArchivesKickout($archives_id,$uid)){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('archives','You have been kicked out of archives'))));
		}
		
		$propsService=new PropsService();
		$label = $propsService->getPropsCategoryByEnName('label');
		$userPropsService=new UserPropsService();
		$userLabel=$userPropsService->getUserLatestPropsOfUsedByCatId($uid,$label['cat_id']);
		if(isset($userLabel['valid_time'])){
			if($userLabel['valid_time']<time()){
				exit(json_encode(array('flag'=>0,'message'=>Yii::t('props','No stickers or labels of aging has been'))));
			}
		}


		$labelInfo = $propsService->getPropsByIds($userLabel['prop_id'], true, true);
		$attribute = $propsService->buildDataByIndex($labelInfo[$userLabel['prop_id']]['attribute'], 'attr_enname');

		$consumeService=new ConsumeService();
		if(!$consumeService->consumeEggs($to_uid,$attribute['label_remove_price']['value'])){
			exit(json_encode(array('flag'=>-1,'message'=>Yii::t('common','Pipiegg not enough'))));
		}
		$userPropsService->updatePropsUseValidTime($userLabel['use_id'],$userLabel['create_time']);
		$propRecord['uid'] = $uid;
		$propRecord['cat_id'] = $labelInfo[$userLabel['prop_id']]['cat_id'];
		$propRecord['prop_id'] = $userLabel['prop_id'];
		$propRecord['pipiegg'] = '-'.$attribute['label_remove_price']['value'];
		$propRecord['dedication'] = $attribute['label_remove_dedication']['value'];
		$propRecord['vtime'] = time()+$attribute['label_timeout']['value']*60;
		$propRecord['info'] = '揭除('.$labelInfo[$userLabel['prop_id']]['name'].'*1)';
		$propRecord['source'] = 0;
		$propRecord['amount'] = 1;
		$userPropsService->saveUserPropsRecords($propRecord);
		//写入皮蛋log
		$pipieggRecords['uid'] = $uid;
		$pipieggRecords['pipiegg'] = $attribute['label_remove_price']['value'];
		$pipieggRecords['from_target_id'] = $userLabel['prop_id'];
		$pipieggRecords['num'] = 1;
		$pipieggRecords['to_target_id'] = $archives_id;
		$pipieggRecords['record_sid'] = $userLabel['use_id'];
		$pipieggRecords['source']='props';
		$pipieggRecords['sub_source']='remove_label';
		$pipieggRecords['extra']='揭除'.$labelInfo[$userLabel['prop_id']]['name'];
		$consumeService->saveUserPipiEggRecords($pipieggRecords, false);
		//写入用户贡献值记录
		$dedicationRecords['uid'] = $uid;
		$dedicationRecords['dedication'] = $attribute['label_remove_dedication']['value'];
		$dedicationRecords['num'] = 1;
		$dedicationRecords['from_target_id'] = $userLabel['prop_id'];
		$dedicationRecords['to_target_id'] = $archives_id;
		$dedicationRecords['record_sid'] = $labelInfo['use_id'];
		$dedicationRecords['source']='props';
		$dedicationRecords['sub_source']='removeLabel';
		$dedicationRecords['info']='揭除'.$labelInfo[$userLabel['prop_id']]['name'];
		$consumeService->saveUserDedicationRecords($dedicationRecords, true);
		$consumeService->saveUserConsumeAttribute(array('uid'=>$uid,'dedication'=>$attribute['label_remove_dedication']['value'],'pipiegg'=>$attribute['label_remove_price']['value']));
		$userListService=new UserListService();
		$userListService->removeArchivesLabel($archives_id, $uid);
		
		$userJson = new UserJsonInfoService();
		$userInfo = $userJson->setUserInfo($uid, array('lb'=>array()));
		$zmq=new PipiZmq();
		$zmq->sendZmqMsg(609,array('type'=>'update_json','uid'=>$uid,'json_info'=>array('lb'=>array())));
		$eventData['archives_id']=$archives_id;
		$eventData['domain']=DOMAIN;
		$eventData['type']='localroom';
		$json_content['type']='removeLabel';
		$json_content['uid']=$uid;
		$eventData['json_content']=$json_content;
		$zmq->sendZmqMsg(606,$eventData);
		exit(json_encode(array('flag'=>1,'message'=>Yii::t('props','Label remove success'))));
	}


	public function actionSendFlyscreen(){
		$archives_id=Yii::app()->request->getParam('archives_id');
		$uid=Yii::app()->user->id;
		if(!$this->isLogin){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('user','You are not logged'))));
		}
		$forbidenService=new ForbidenService();
		if($forbidenService->getArchivesKickout($archives_id,$uid)){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('archives','You have been kicked out of archives'))));
		}
		$forbid=$forbidenService->getArchivesForbid($archives_id,$uid);
		if($forbid){
			if($forbid['vt']>time()){
				if($forbid['t']==1){
					$msg='You have been banned IP';
				}else if($forbid['t']==5){
					$msg='You have been banned';
				}else if($forbid['t']==11){
					$msg='You have been the global gag';
				}
				exit(json_encode(array('flag'=>0,'message'=>Yii::t('props',$msg))));
			}
		}
		$fly_num = 0;
		$propsService=new PropsService();
		$props = $propsService->getPropsByEnName('flyscreen');

		$userPropsService=new UserPropsService();
		$bag_fly=$userPropsService->getUserValidPropsOfBagByPropId($uid,$props['prop_id']);
		$bag_fly=array_pop($bag_fly);
		if($bag_fly){
			$fly_num=$bag_fly['num'];
		}

		if($fly_num>0){
			exit(json_encode(array('flag'=>1,'data'=>$props['prop_id'],'message'=>Yii::t('props','Your backpack and {fly_num} fly screen, will give priority to the use of props Backpack',array('{fly_num}'=>$fly_num)))));
		}


		$userJson=new UserJsonInfoService();
		$userData=$userJson->getUserInfo($uid,false);
		
		$propsInfo=$propsService->getPropsByIds($props['prop_id'],false,true);
		$attributes=$propsService->buildDataByIndex($propsInfo[$props['prop_id']]['attribute'],'attr_enname');
		$vip_type=isset($userData['vip']['t'])?$userData['vip']['t']:0;
		$price = $base_price = $propsInfo[$props['prop_id']]['pipiegg'];
		//黄色vip的折扣
		if($vip_type==1){
			$price = $price * 0.9;
		}
		//紫色vip的折扣
		if($vip_type==2){
			$price = $price *0.8;
		}
		//限时折扣


		if($vip_type>0){
			$price = number_format($price, 1);
			$vip_name = array(1 => 'VIP', 2 => '高级VIP');
			exit(json_encode(array('flag'=>1,'data'=>$props['prop_id'],'message'=>Yii::t('props','Your identity is <em class="pink">{vip_name}</em>, fly screen will charge <em class="pink">{price} egg</em>',array('{price}'=>$price,'{vip_name}'=>$vip_name[$vip_type])))));
		}else{
			$price=number_format($price,1);
			exit(json_encode(array('flag'=>1,'data'=>$props['prop_id'],'message'=>Yii::t('props','Fly screen will charge <em class="pink">{price} preserved egg</em></p><p class="otline"><a href="#" class="pink">For VIP discount </a>',array('{price}'=>$price)))));
		}
	}
	
	/**
	 * 获取游戏列表
	 * @author hexin
	 */
	public function actionGetGames(){
		$xmlParser = new PipiXmlParser();
		$xmlParser->name = 'appname';
		$data = $xmlParser->parse(Yii::app()->params['letian_game']['list']);
		echo json_encode($data);
	}
	
	
	
	/**
	 * 获取守护星
	 * @author leiwei
	 */
	public function actionGetGuard(){
		$dotey_uid=Yii::app()->request->getParam('dotey_uid');
		$archives_id=Yii::app()->request->getParam('archives_id');
		if(!$this->isLogin){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('user','You are not logged'))));
		}
		if($dotey_uid<=0||$archives_id<=0){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('common','Parameters are wrong'))));
		}
		$archivesService=new ArchivesService();
		$archives=$archivesService->getArchivesByArchivesId($archives_id);
		$onclick='';
		if(isset($archives['live_record'])){
			if($archives['live_record']['status']==START_LIVE){
				$onclick="Chat.starGuard()";
			}
		}
		$guardService=new GuardAngelService();
		$doteyGuard=$guardService->lookDoteyRank($dotey_uid);
		if($doteyGuard['flag']!=1){
			exit(json_encode(array('flag'=>0,'message'=>$doteyGuard['message']['info'])));
		}
		$userGuard=$guardService->lookUserGuardStar(Yii::app()->user->id,$dotey_uid);
		if($guardService->checkIsGuard(Yii::app()->user->id,$dotey_uid)){
			exit(json_encode(array('flag'=>1,'message'=>'<ul class="paysong"><li><p>主播当前排在第<em class="pink">'.$userGuard['message']['drank'].'</em>名,您已为TA累计了<em class="pink">'.$userGuard['message']['ustar'].'</em>颗守护星</p></li><p class="oneline"><input class="shiftbtn" onClick="$.mask.hide(\'SucMove\');" type="button" value="守护中"></p></ul>')));
		}else{
			if($doteyGuard['message']['star']<=0){
				exit(json_encode(array('flag'=>1,'message'=>'<ul class="paysong"><li><p>主播当前尚无名次 </p></li><p class="oneline"><input class="shiftbtn" type="button" onclick="'.$onclick.'" value="我要守护TA"></p></ul>')));
			}
			if($userGuard['message']['ustar']>0){
				exit(json_encode(array('flag'=>1,'message'=>'<ul class="paysong"><li><p>主播当前排在第<em class="pink">'.$doteyGuard['message']['rank'].'</em>名,您已为TA累计了<em class="pink">'.$userGuard['message']['ustar'].'</em>颗守护星</p></li><p class="oneline"><input class="shiftbtn" onclick="'.$onclick.'" type="button" value="我要守护TA"></p></ul>')));
			}else{
				exit(json_encode(array('flag'=>1,'message'=>'<ul class="paysong"><li><p>主播当前排在第<em class="pink">'.$doteyGuard['message']['rank'].'</em>名<p class="oneline"><input class="shiftbtn" onclick="'.$onclick.'" type="button" value="我要守护TA"></p></ul>')));
			}
			
		}
	}
	
	public function actionStartGuard(){
		$dotey_uid=Yii::app()->request->getParam('dotey_uid');
		$guardService=new GuardAngelService();
		$guard=$guardService->startGuard($dotey_uid,Yii::app()->user->id);
		if($guard['flag']==3){
			exit(json_encode(array('flag'=>1,'message'=>'<ul class="paysong"><li><p>你已经守护了该主播,主播当前排在第<em class="pink">'.$guard['message']['rank'].'</em>名,您已为TA累计了<em class="pink">'.$guard['message']['sumStar'].'</em>颗守护星</p></li><p class="oneline"><input class="shiftbtn" onClick="$.mask.hide(\'SucMove\');" type="button" value="确&nbsp;&nbsp;定"></p></ul>')));
		}elseif($guard['flag']==4){
			exit(json_encode(array('flag'=>1,'message'=>'<ul class="paysong"><li><p>对不起,你目前只能守护<em class="pink">'.$guard['message']['allowGuardNum'].'</em>位主播</p></li><p class="oneline"><input class="shiftbtn" onClick="$.mask.hide(\'SucMove\');" type="button" value="确&nbsp;&nbsp;定"></p></ul>')));
		}elseif($guard['flag']==6){
			exit(json_encode(array('flag'=>1,'message'=>'<ul class="paysong"><li><p>守护成功!为TA累计满<em class="pink">'.$guard['message']['num'].'</em>颗守护星就可获得守护天使勋章哟</p></li><p class="oneline"><input class="shiftbtn" onClick="$.mask.hide(\'SucMove\');" type="button" value="确&nbsp;&nbsp;定"></p></ul>')));
		}else{
			exit(json_encode(array('flag'=>0,'message'=>'<ul class="paysong"><li><p>'.$guard['message'].'</p></li><p class="oneline"><input class="shiftbtn" onClick="$.mask.hide(\'SucMove\');" type="button" value="确&nbsp;&nbsp;定"></p></ul>')));
		}
	}

	public function actionSendDice(){
		$type=Yii::app()->request->getParam('type');
		$archives_id=Yii::app()->request->getParam('archives_id');
		$to_uid=Yii::app()->request->getParam('to_uid');
		if(!$this->isLogin){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('user','You are not logged'))));
		}
		if(empty($type)||$archives_id<=0){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('common','Parameters are wrong'))));
		}
		$uid=Yii::app()->user->id;
		$forbidenService=new ForbidenService();
		if($forbidenService->getArchivesKickout($archives_id,$uid)){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('archives','You have been kicked out of archives'))));
		}
		$forbid=$forbidenService->getArchivesForbid($archives_id,$uid);
		if($forbid){
			if($forbid['vt']>time()){
				if($forbid['t']==1){
					$msg='You have been banned IP';
				}else if($forbid['t']==5){
					$msg='You have been banned';
				}else if($forbid['t']==11){
					$msg='You have been the global gag';
				}
				exit(json_encode(array('flag'=>0,'message'=>Yii::t('props',$msg))));
			}
		}
		$diceService=new DiceService();
		if($diceService->getLastSendDiceTime($uid)){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('props','After sending the dice, the interval of 20 seconds before using again'))));
		}
		$result=$diceService->sendDice($archives_id, $uid, $to_uid,$type);
		if($result>0){
			exit(json_encode(array('flag'=>1,'message'=>Yii::t('dice','Dice send successfull'))));
		}else{
			exit(json_encode(array('flag'=>0,'message'=>$diceService->getError())));
		}
	}
	
	public function actionReceiveDice(){
		$record_id=Yii::app()->request->getParam('record_id');
		$archives_id=Yii::app()->request->getParam('archives_id');
		if(!$this->isLogin){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('user','You are not logged'))));
		}
		$uid=Yii::app()->user->id;
		if($record_id<=0||$archives_id<=0||$uid<=0){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('common','Parameters are wrong'))));
		}
		$diceService=new DiceService();
		$result=$diceService->receiveDice($record_id, $uid,$archives_id);
		if($result>0){
			exit(json_encode(array('flag'=>1,'message'=>Yii::t('dice','Dice receive successfull'))));
		}else{
			exit(json_encode(array('flag'=>0,'message'=>$diceService->getError())));
		}
	}
	
	public function actionGetDiceRecord(){
		$archives_id=Yii::app()->request->getParam('archives_id');
		$record=array();
		$diceService=new DiceService();
		$record=$diceService->getDiceGameRecords($archives_id);
		exit(json_encode($record));
	}
	
	public function actionSendBroadcast(){
		$archives_id=Yii::app()->request->getParam('archives_id');
		$content=Yii::app()->request->getParam('content');
		if(!$this->isLogin){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('user','You are not logged'))));
		}
		if($archives_id<=0||empty($content)){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('common','Parameters are wrong'))));
		}
		$uid=Yii::app()->user->id;
		$broadcastService=new BroadcastService();
		$result=$broadcastService->releaseBroadcast((int)$uid,(int)$archives_id,$content);
		if($result==1){
			exit(json_encode(array('flag'=>0,'message'=>'发送失败，您已被禁止发送广播')));
		}elseif($result==2){
			exit(json_encode(array('flag'=>0,'message'=>'广播内容超出范围')));
		}elseif($result==3){
			exit(json_encode(array('flag'=>0,'message'=>'全站广播已关闭')));
		}elseif($result==4){
			exit(json_encode(array('flag'=>0,'message'=>'用户信息异常')));
		}elseif($result==5){
			exit(json_encode(array('flag'=>0,'message'=>'用户等级不够，无法发布广播内容')));
		}elseif($result==6){
			exit(json_encode(array('flag'=>2,'message'=>'您的账户余额不足')));
		}elseif($result==7){
			exit(json_encode(array('flag'=>1,'message'=>'发布成功，广播即将显示！')));
		}else{
			exit(json_encode(array('flag'=>0,'message'=>'广播发送异常')));
		}
	}
	
	public function actionCheckTruckGift(){
		$gift_id=Yii::app()->request->getParam('gift_id');
		$num=Yii::app()->request->getParam('num');
		
		$truckGiftService=new TruckGiftService();
		$truckGiftRecord=$truckGiftService->getTruckGiftRecord();
		if($truckGiftRecord){
			$giftService=new GiftService();
			$gift=$giftService->getGiftByIds($gift_id);
			$giftPrice=$gift[$gift_id]['pipiegg']*$num;
			if($giftPrice>=$truckGiftRecord['pipiegg']){
				exit(json_encode(array('flag'=>1,'message'=>'本次送礼可取代当前跑道礼物')));
			}else{
				exit(json_encode(array('flag'=>1,'message'=>'本次送礼无法取代当前跑道礼物')));
			}
		}else{
			exit(json_encode(array('flag'=>1,'message'=>'本次将登上跑道，持续显示2小时')));
		}
	}
	
	//开播直播间随机推荐
	public function getRandLivingArchives($doteyId){
		$archivesService=new ArchivesService();
		$userListService=new UserListService();
		$userJson=new UserJsonInfoService();
		$livingArchives=$archivesService->getLivingArchives(0,true);
		$archivesList=array();
		if(isset($livingArchives['living'])){
			foreach($livingArchives['living'] as $key=>$row){
				if($row['uid']!=$doteyId){
					$archivesList[$key]['uid']=$row['uid'];
					$archivesList[$key]['title']=$row['title'];
					$archivesList[$key]['display_small']=$row['display_small'];
					$archivesList[$key]['display_big']=$row['display_big'];
					$archivesList[$key]['online']=$userListService->getArchivesOnlineNum($row['archives_id']);
					$userInfo=$userJson->getUserInfo($row['uid'],false);
					$archivesList[$key]['rank']=$userInfo['dk'];
				}
			}
		}
		$newArchivesList=array();
		if(count($archivesList)>3){
			$randKeys=array_rand($archivesList,3);
			foreach($randKeys as $row){
				$newArchivesList[]=$archivesList[$row];
			}
		}else{
			$newArchivesList=$archivesList;
		}
		return $newArchivesList;
	}
	

}

?>
