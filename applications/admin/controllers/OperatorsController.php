<?php
class OperatorsController extends PipiAdminController {

	/**
	 * @var BbsbaseService 道具服务层
	 */
	public $bbsSer;

	/**
	 * @var array 允许的操作
	 */
	public $allowOp = array('addNewsNotice', 'getThreadInfo', 'delNewsNotice', 'addDoteyPolicy', 'delDoteyPolicy', 
		'addUserHelp', 'delUserHelp', 'delDoteyHelp', 'addDoteyHelp', 'addAboutUs', 'delAboutUs', 'addKefu', 'getKefu', 
		'delKefu', 'addMedal', 'delMedal', 'delUserMedal', 'checkDoteyInfo', 'addUserMedal', 'delSuggest', 'lookSuggest', 
		'flagSuggest', 'addGiveaway','getGiveawayView','checkPropList','task','addTask','getTaskInfo');

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
		$this->bbsSer = new BbsbaseService();
		$this->op = Yii::app()->request->getParam('op');
		$this->isAjax = Yii::app()->request->isAjaxRequest;
		if(!($this->p = Yii::app()->request->getParam('page'))){
			$this->p = 1;
		}
		$this->offset = ($this->p -1)*$this->pageSize;
	}
	
	/**
	 * 新闻公告管理
	 */
	public function actionNewsNotice(){
		$this->assetsCKEditor();
		$threadList = array();
		$threadList['count'] = 0;
		$threadList['list'] = array();
		$forum_sid = '';
		if($forum_sid = $this->getNewsNoticeForumSubId()){
			$threadList = $this->bbsSer->getThreadList($forum_sid,$this->p,$this->pageSize);
		}
		
		//分页实例化
		$pager = new CPagination($threadList['count']);
		$pager->pageSize= $this->pageSize;
		$this->render('operators_news_notice',array('threadList'=>$threadList['list'],'pager'=>$pager,'forum_sid'=>$forum_sid));
	}
	
	/**
	 * 添加新闻公告 
	 */
	public function actionAddNewsNotice(){
		$this->assetsCKEditor();
		
		//是否删除
		if($this->op == 'delNewsNotice' && in_array($this->op,$this->allowOp)){
			$notices = $this->delNewsNoticeDo();
		}
		
		$notices = array();
		//是否是添加动作
		if($this->op == 'addNewsNotice' && in_array($this->op,$this->allowOp)){
			$notices = $this->addNewsNoticeDo();
		}
		
		//是否是修改
		$info = array();
		$postInfo = array();
		if($this->op == 'getThreadInfo' && in_array($this->op,$this->allowOp)){
			$info = $this->getThreadInfo();
			$postInfo = $this->getPostInfo();
		}
		
		if($this->isAjax){
			$this->renderPartial('operators_add_news_notice',array('notices'=>$notices,'info'=>$info,'postInfo'=>$postInfo));
		}else{
			$this->render('operators_add_news_notice',array('notices'=>$notices,'info'=>$info,'postInfo'=>$postInfo));
		}
	}
	
	/**
	 * 主播政策管理
	 */
	public function actionDoteyPolicy(){
		$this->assetsCKEditor();
		$threadList = array();
		$threadList['count'] = 0;
		$forum_sid = '';
		if($forum_sid = $this->getDoteyPolicyForumSubId()){
			$threadList = $this->bbsSer->getThreadList($forum_sid,$this->p,$this->pageSize);
		}
		
		//分页实例化
		$pager = new CPagination($threadList['count']);
		$pager->pageSize= $this->pageSize;
		$this->render('operators_dotey_policy',array('threadList'=>$threadList['list'],'pager'=>$pager,'forum_sid'=>$forum_sid));
	}
	
	/**
	 * 添加主播政策 
	 */
	public function actionAddDoteyPolicy(){
		$this->assetsCKEditor();
		//是否删除
		if($this->op == 'delDoteyPolicy' && in_array($this->op,$this->allowOp)){
			$notices = $this->delDoteyPolicyDo();
		}
		
		$notices = array();
		//是否是添加动作
		if($this->op == 'addDoteyPolicy' && in_array($this->op,$this->allowOp)){
			$notices = $this->addDoteyPolicyDo();
		}
		
		//是否是修改
		$info = array();
		$postInfo = array();
		if($this->op == 'getThreadInfo' && in_array($this->op,$this->allowOp)){
			$info = $this->getThreadInfo();
			$postInfo = $this->getPostInfo();
		}
		
		
		if($this->isAjax){
			$this->renderPartial('operators_add_dotey_policy',array('notices'=>$notices,'info'=>$info,'postInfo'=>$postInfo));
		}else{
			$this->render('operators_add_dotey_policy',array('notices'=>$notices,'info'=>$info,'postInfo'=>$postInfo));
		}
	}
	
	/**
	 * 用户帮助管理
	 */
	public function actionUserHelp(){
		$this->assetsCKEditor();
		$threadList = array();
		$threadList['count'] = 0;
		$threadList['list'] = array();
		$forum_sid = '';
		
		$allSubForum = $this->bbsSer->getAllCmsSubForum(OPERATORS_CMS_USERHELP_FORUMNAME);
		$subForum = Yii::app()->request->getParam('sub_forum');
		$subForum = !empty($subForum)?$subForum:reset($allSubForum);
		
		if($forum_sid = $this->getUserHelpForumSubId($subForum)){
			$threadList = $this->bbsSer->getThreadList($forum_sid,$this->p,$this->pageSize);
		}
		
		//分页实例化
		$pager = new CPagination($threadList['count']);
		$pager->pageSize= $this->pageSize;
		$this->render('operators_user_help',array('threadList'=>$threadList['list'],'pager'=>$pager,'forum_sid'=>$forum_sid,'allSubForum'=>$allSubForum,'subForum'=>$subForum));
	}
	
	/**
	 * 添加用户帮助 
	 */
	public function actionAddUserHelp(){
		$this->assetsCKEditor();
		//是否删除
		if($this->op == 'delUserHelp' && in_array($this->op,$this->allowOp)){
			$notices = $this->delUserHelpDo();
		}
		
		$notices = array();
		//是否是添加动作
		if($this->op == 'addUserHelp' && in_array($this->op,$this->allowOp)){
			$notices = $this->addUserHelpDo();
		}
		
		//是否是修改
		$info = array();
		//是否是修改
		$info = array();
		$postInfo = array();
		if($this->op == 'getThreadInfo' && in_array($this->op,$this->allowOp)){
			$info = $this->getThreadInfo();
			$postInfo = $this->getPostInfo();
		}
		
		$subForum = Yii::app()->request->getParam('sub_forum');
		$allSubForum = $this->bbsSer->getAllCmsSubForum(OPERATORS_CMS_USERHELP_FORUMNAME);
		
		if($this->isAjax){
			$this->renderPartial('operators_add_user_help',array('notices'=>$notices,'info'=>$info,'allSubForum'=>$allSubForum,'subForum'=>$subForum,'postInfo'=>$postInfo));
		}else{
			$this->render('operators_add_user_help',array('notices'=>$notices,'info'=>$info,'allSubForum'=>$allSubForum,'subForum'=>$subForum,'postInfo'=>$postInfo));
		}
	}
	
	/**
	 * 主播帮助
	 */
	public function actionDoteyHelp(){
		$this->assetsCKEditor();
		$threadList = array();
		$threadList['count'] = 0;
		$threadList['list'] = array();
		$forum_sid = '';
		
		$allSubForum = $this->bbsSer->getAllCmsSubForum(OPERATORS_CMS_DOTEYHELP_FORUMNAME);
		$subForum = Yii::app()->request->getParam('sub_forum');
		$subForum = !empty($subForum)?$subForum:reset($allSubForum);
		
		if($forum_sid = $this->getDoteyHelpForumSubId($subForum)){
			$threadList = $this->bbsSer->getThreadList($forum_sid,$this->p,$this->pageSize);
		}
		
		//分页实例化
		$pager = new CPagination($threadList['count']);
		$pager->pageSize= $this->pageSize;
		$this->render('operators_dotey_help',array('threadList'=>$threadList['list'],'pager'=>$pager,'forum_sid'=>$forum_sid,'allSubForum'=>$allSubForum,'subForum'=>$subForum));
	}
	
	/**
	 * 添加主播帮助 
	 */
	public function actionAddDoteyHelp(){
		$this->assetsCKEditor();
		//是否删除
		if($this->op == 'delDoteyHelp' && in_array($this->op,$this->allowOp)){
			$notices = $this->delDoteyHelpDo();
		}
		
		$notices = array();
		//是否是添加动作
		if($this->op == 'addDoteyHelp' && in_array($this->op,$this->allowOp)){
			$notices = $this->addDoteyHelpDo();
		}
		
		//是否是修改
		$info = array();
		$postInfo = array();
		if($this->op == 'getThreadInfo' && in_array($this->op,$this->allowOp)){
			$info = $this->getThreadInfo();
			$postInfo = $this->getPostInfo();
		}
		
		$subForum = Yii::app()->request->getParam('sub_forum');
		$allSubForum = $this->bbsSer->getAllCmsSubForum(OPERATORS_CMS_DOTEYHELP_FORUMNAME);
		
		if($this->isAjax){
			$this->renderPartial('operators_add_dotey_help',array('notices'=>$notices,'info'=>$info,'allSubForum'=>$allSubForum,'subForum'=>$subForum,'postInfo'=>$postInfo));
		}else{
			$this->render('operators_add_dotey_help',array('notices'=>$notices,'info'=>$info,'allSubForum'=>$allSubForum,'subForum'=>$subForum,'postInfo'=>$postInfo));
		}
	}
	
	/**
	 * 关于我们 
	 */
	public function actionAboutUs(){
		$this->assetsCKEditor();
		$threadList = array();
		$threadList['count'] = 0;
		$threadList['list'] = array();
		$forum_sid = '';
		
		$allSubForum = $this->bbsSer->getAllCmsSubForum(OPERATORS_CMS_ABOUTUS_FORUMNAME);
		$subForum = Yii::app()->request->getParam('sub_forum');
		$subForum = !empty($subForum)?$subForum:reset($allSubForum);
		
		if($forum_sid = $this->getAboutUsForumSubId($subForum)){
			$threadList = $this->bbsSer->getThreadList($forum_sid,$this->p,$this->pageSize);
		}
		
		//分页实例化
		$pager = new CPagination($threadList['count']);
		$pager->pageSize= $this->pageSize;
		$this->render('operators_about_us',array('threadList'=>$threadList['list'],'pager'=>$pager,'forum_sid'=>$forum_sid,'allSubForum'=>$allSubForum,'subForum'=>$subForum));
	}
	
	/**
	 * 添加关于我们
	 */
	public function actionAddAboutUs(){
		$this->assetsCKEditor();
		//是否删除
		if($this->op == 'delAboutUs' && in_array($this->op,$this->allowOp)){
			$notices = $this->delAboutUsDo();
		}
		
		$notices = array();
		//是否是添加动作
		if($this->op == 'addAboutUs' && in_array($this->op,$this->allowOp)){
			$notices = $this->addAboutUsDo();
		}
		
		//是否是修改
		$info = array();
		$postInfo = array();
		if($this->op == 'getThreadInfo' && in_array($this->op,$this->allowOp)){
			$info = $this->getThreadInfo();
			$postInfo = $this->getPostInfo();
		}
		
		$subForum = Yii::app()->request->getParam('sub_forum');
		$allSubForum = $this->bbsSer->getAllCmsSubForum(OPERATORS_CMS_ABOUTUS_FORUMNAME);
		
		if($this->isAjax){
			$this->renderPartial('operators_add_about_us',array('notices'=>$notices,'info'=>$info,'allSubForum'=>$allSubForum,'subForum'=>$subForum,'postInfo'=>$postInfo));
		}else{
			$this->render('operators_add_about_us',array('notices'=>$notices,'info'=>$info,'allSubForum'=>$allSubForum,'subForum'=>$subForum,'postInfo'=>$postInfo));
		}
	}
	
	/**
	 * 客服列表管理
	 */
	public function actionKefu(){
		$operateSer = new OperateService();
		$condition = array();
		if (Yii::app()->request->getParam('kefu')){
			$condition = Yii::app()->request->getParam('kefu');
		}
		
		$kefuList = $operateSer->getKefuList($condition,$this->offset,$this->pageSize);
		$count = $kefuList['count'];
		$list = $kefuList['list'];
		$pager = new CPagination($count);
		$pager->pageSize = $this->pageSize;
		
		$this->render('operators_kefu_list',array('operateSer'=>$operateSer,'list'=>$list,'pager'=>$pager,'condition'=>$condition));
	}
	
	/**
	 * 添加客服
	 */
	public function actionAddKefu(){
		$operateSer = new OperateService();
		
		//是否删除
		if($this->op == 'delKefu' && in_array($this->op,$this->allowOp)){
			$notices = $this->delKefuDo($operateSer);
		}
		
		$notices = array();
		//是否是添加动作
		if($this->op == 'addKefu' && in_array($this->op,$this->allowOp)){
			$notices = $this->addKefuDo($operateSer);
		}
		
		//是否是修改
		$info = array();
		if($this->op == 'getKefu' && in_array($this->op,$this->allowOp)){
			$info = $this->getKefuDo($operateSer);
		}
		
		if($this->isAjax){
			exit($this->renderPartial('operators_add_kefu',array('operateSer'=>$operateSer,'info'=>$info,'notices'=>$notices)));
		}
		$this->render('operators_add_kefu',array('operateSer'=>$operateSer,'info'=>$info,'notices'=>$notices));
	}
	
	/**
	 * 勋章列表管理
	 */
	public function actionMedal(){
		$userMedal = new UserMedalService();
		$list = $userMedal->getMedalList();
		$medalType = $userMedal->getMedalType();
		$this->render('operators_medal_list',array('list'=>$list,'medalType'=>$medalType,'userMedal'=>$userMedal));
	}
	
	/**
	 * 添加勋章 
	 */
	public function actionAddMedal(){
		$userMedal = new UserMedalService();
		
		$cinfo = array();
		$mid = Yii::app()->request->getParam('mid');
		if($mid){
			$cinfo = $userMedal->getMedalList(array('mid'=>$mid));
			if(!($cinfo = array_shift($cinfo))){
				exit('没有获取到数据，无法进行编辑操作');
			}
		}
		
		//添加操作
		$notices = array();
		if($this->op == 'addMedal' && in_array($this->op, $this->allowOp)){
			$notices = $this->addMedalDo($userMedal);
		}
		
		//是否是删除操作 
		if($this->op == 'delMedal' && in_array($this->op, $this->allowOp)){
			$notices = $this->delMedalDo($userMedal);
		}
		
		if ($this->isAjax){
			exit($this->renderPartial('operators_add_medal',array('userMedal'=>$userMedal,'cinfo'=>$cinfo)));
		}else{
			$this->render('operators_add_medal',array('userMedal'=>$userMedal,'cinfo'=>$cinfo,'notices'=>$notices));
		}
	}
	
	/**
	 * 用户勋章管理
	 */
	public function actionUserMedal(){
		$userMedal = new UserMedalService();
		$mids = $this->formatMedalList($userMedal->getMedalList());
		
		$condition = $this->getSearchCondition();
		$result = $userMedal->getUserMedalByCondition($condition, $this->offset, $this->pageSize);
		$count = $result['count'];
		$list = $result['list'];
		$uids = array();
		
		foreach ($list as $v){
			$uids[$v['uid']] = $v['uid'];
		}
		
		$uinfo = array();
		if($uids){
			$userSer = new UserService();
			$uinfo = $userSer->getUserBasicByUids($uids);
		}
		
		$pager = new CPagination($count);
		$pager->pageSize = $this->pageSize;
		$pager->params = $condition;
		
		$this->render('operators_user_medal_list', array('mids' => $mids, 'userMedal' => $userMedal, 
			'condition' => $condition, 'list' => $list, 'pager' => $pager,'uinfo'=>$uinfo));
	}
	
	/**
	 * 授予用户勋章 
	 */
	public function actionAddUserMedal(){
		$userMedal = new UserMedalService();
		//是否是删除操作
		if($this->op == 'delUserMedal' && in_array($this->op, $this->allowOp)){
			$this->delUserMedalDo($userMedal);
		}
		
		if($this->op == 'checkDoteyInfo' && in_array($this->op, $this->allowOp)){
			$this->checkDoteyInfo(false);
		}
		
		//是否是添加操作
		$notices = array();
		if($this->op == 'addUserMedal' && in_array($this->op, $this->allowOp)){
			$notices = $this->addUserMedalDo($userMedal);
		}
		
		$mids = $this->formatMedalList($userMedal->getMedalList());
		
		if ($this->isAjax){
			exit($this->renderPartial('operators_add_user_medal',array('mids'=>$mids,'userMedal'=>$userMedal)));
		}else{
			$this->render('operators_add_user_medal',array('mids'=>$mids,'userMedal'=>$userMedal,'notices'=>$notices));
		}
	}
	
	/**
	 * 意见反馈
	 */
	public function actionUserSuggest(){
		$operateSer = new OperateService();
		//是否是删除
		if($this->op == 'delSuggest' && in_array($this->op, $this->allowOp)){
			$this->delSuggestDo($operateSer);
		}
		//标记为处理
		if($this->op == 'flagSuggest' && in_array($this->op, $this->allowOp)){
			$this->flagSuggestDo($operateSer);
		}
		
		$userSer = new UserService();
		
		$condition = $this->getSearchCondition();
		$result = $operateSer->getSuggestByCondition($condition,$this->offset,$this->pageSize);
		$count = $result['count'];
		$list = $result['list'];
		$uids = array();
		$uinfo = array();
		if ($list){
			foreach($list as $v){
				$uids[$v['uid']] = $v['uid'];
			}
			if($uids){
				$uinfo = $userSer->getUserBasicByUids($uids);
			}
		}
		
		if($this->op == 'lookSuggest' && in_array($this->op, $this->allowOp)){
			exit($this->renderPartial('operators_look_user_suggest',array('list'=>$list,'uinfo'=>$uinfo,'operateSer'=>$operateSer)));
		}
		
		$pager = new CPagination($count);
		$pager->pageSize = $this->pageSize;
		$pager->params = $condition;
		
		$this->render('operators_user_suggest_list', array('operateSer' => $operateSer, 'pager' => $pager, 
			'list' => $list, 'uinfo' => $uinfo, 'condition' => $condition));
	}
	
	/**
	 * 赠品发放
	 */
	public function actionGiveaway(){
		$_commonSer = null;
		$consumeSer = new ConsumeService();
		$condition = $this->getSearchCondition();
		if (!isset($condition['type']) || $condition['type'] == ''){
			$condition['type'] = GIVEAWAY_TYPE_GIFT;
		}
		
		$result = array();
		$result['count'] = 0;
		$result['list'] = array();
		if ($condition['type'] == GIVEAWAY_TYPE_GIFT){
			$_commonSer = new GiftBagService();
			$result = $_commonSer->getUserBagRecordsByCondition($condition,$this->offset,$this->pageSize);
		}elseif ($condition['type'] == GIVEAWAY_TYPE_PROPS){
			$_commonSer = new UserPropsService();
			$result = $_commonSer->getUserPropsRecordsByCondition($condition,$this->offset,$this->pageSize);
		}elseif ($condition['type'] == GIVEAWAY_TYPE_CHARM){
			$result = $consumeSer->getCharmByCondition($condition,$this->offset,$this->pageSize);
		}elseif ($condition['type'] == GIVEAWAY_TYPE_CHARMPOINTS){
			$result = $consumeSer->getCharmPointsByCondition($condition,$this->offset,$this->pageSize);
		}elseif ($condition['type'] == GIVEAWAY_TYPE_DEDICATION){
			$result = $consumeSer->getDedicationByCondition($condition,$this->offset,$this->pageSize);
		}elseif ($condition['type'] == GIVEAWAY_TYPE_PIPIEGGS){
			$result = $consumeSer->getPipieggsByCondition($condition,$this->offset,$this->pageSize);
		}
		
		$list = $result['list'];
		$count = $result['count'];
		$this->formatGiveawayList($list,$condition['type'],$_commonSer);
		
		$userInfo = $this->getUserInfo($list);
		
		$pager = new CPagination($count);
		$pager->pageSize = $this->pageSize;
		$pager->params = $condition;

		$this->render('operators_giveaway_list_' . $condition['type'], array('pager' => $pager, 
			'condition' => $condition, 'list' => $list, 'doteyInfo' => $userInfo, 'consumeSer' => $consumeSer,'_commonSer'=>$_commonSer));
	}
	
	/**
	 * 新增赠品赠送
	 */
	public function actionAddGiveaway(){
		$consumeSer = new ConsumeService();
		//检查用户信息的合法性
		if($this->op == 'checkDoteyInfo' && in_array($this->op,$this->allowOp)){
			$isDotey = Yii::app()->request->getParam('isDotey');
			$this->checkDoteyInfo($isDotey);
		}
		
		//渲染赠品类型相关的视图
		if($this->op == 'getGiveawayView' && in_array($this->op,$this->allowOp)){
			$type = Yii::app()->request->getParam('type');
			$this->getGiveawayView($type);
		}
		
		//获取道具 
		if($this->op == 'checkPropList' && in_array($this->op,$this->allowOp)){
			$this->checkPropList();
		}
		
		//是否是添加动作
		$notices = array();
		if ($this->op == 'addGiveaway' && in_array($this->op, $this->allowOp)){
			$notices = $this->addGiveawayDo();
		}
		
		if ($this->isAjax) {
			exit($this->renderPartial('operators_add_giveaway',array('consumeSer'=>$consumeSer)));
		}else{
			$this->render('operators_add_giveaway',array('consumeSer'=>$consumeSer,'notices'=>$notices));
		}
	}
	
	/**
	 * 查看直播在线人数,历史曲线图及所有详细数据
	 * 搜索直播在线人数,根据时间区间搜索
	 */
	public function actionGetShowOnline(){
		$this->assetsMy97Date();
		$this->assetsGChart();
		
		$operateSer = new OperateService();
		
		$isLimit = Yii::app()->request->getParam('page_flag',false);
		
		$condition = array();
		$condition['page_flag'] = $isLimit;
		if(Yii::app()->request->getParam('start_date',false)){
			$condition['start_date'] = date('Ymd',strtotime(Yii::app()->request->getParam('start_date'))).'0000';
			$start_date = Yii::app()->request->getParam('start_date');
		}
		
		if(Yii::app()->request->getParam('end_date',false)){
			$condition['end_date'] = date('Ymd',strtotime(Yii::app()->request->getParam('end_date'))).'2359';
			$end_date = Yii::app()->request->getParam('end_date');
		}
		
		if (!isset($condition['start_date'])){
			$condition['start_date'] = date('Ymd').'0000';
			$start_date = date('Y-m-d');
		}
		
		if (!isset($condition['end_date'])){
			$condition['end_date'] = date('Ymd').'2359';
			$end_date = date('Y-m-d');
		}
		
		$count = $operateSer->getShowStatListCount($condition);
		$pager = new CPagination($count);
		$pager->pageSize = $this->pageSize;
		$pager->params = $condition;
		
		$condition['_select'] = 'time, total_num';
		$condition['_order'] = 'time asc';
		$list_asc = $operateSer->getShowStatList($condition,$this->offset,$this->pageSize,false);
		$condition['_select'] = 'time, total_num, tel_num, cnc_num, yd_num, edu_num';
		$condition['_order'] = 'time desc';
		$list_desc = $operateSer->getShowStatList($condition,$this->offset,$this->pageSize,$isLimit);
		$time_str = "[";
		$total_num = "[";
		$max_num = 0;
		
		$x_dot_total = 24;// x 轴总共取几个点
		$x_dot_total_arr = array(0,1,2,3,4,6,8,12,24);//能被24整除的数
		$date_span = (strtotime($end_date) - strtotime($start_date) ) / 86400;// 日期跨度,1天,两天...
		if (in_array($date_span, $x_dot_total_arr)) {
			$hour_span = $date_span == 0 ? 1 : $date_span;
		} else {
			$hour_span = 6 * (24 / $x_dot_total);
		}
		
		// 数据列表
		foreach ($list_desc as $key => $arr) {
			$list_desc[$key]['time'] = preg_replace('/^(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})$/', '\1-\2-\3 \4:\5', $list_desc[$key]['time']);
		}
		// 数据曲线图
		foreach ($list_asc as $key => $arr) {
			$list_asc[$key]['time'] = preg_replace('/^(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})$/', '\1-\2-\3 \4:\5', $list_asc[$key]['time']);
			$new_date = substr($list_asc[$key]['time'], 0, 10);
			$hour = (int) substr($list_asc[$key]['time'], 11, 2);
			$minute = (int) substr($list_asc[$key]['time'], 14, 2);
			if ($key == 0) {
				$time_str .= "'$new_date'";
				$total_num .= $arr['total_num'];
			}
			elseif ($minute != 0) {
				continue;
			}
			elseif ( $hour == 0 ) { // 每天的0点,把日期打印出来(每个整点有两条数据)
				$time_str .= ", '$new_date'";
				$total_num .= (", " . $arr['total_num']);
			}
			elseif ($hour % $hour_span == 0) {
				$time_str .= ", '$hour'";
				$total_num .= (", " . $arr['total_num']);
			}
			if ($arr['total_num'] > $max_num)
				$max_num=$arr['total_num'];
		}
		$time_str .= "]";
		$total_num .= "]";
		$max_num = ceil( $max_num / 100 ) * 100;
		
		$assign = array(
			'condition'=>$condition,
			'time_str'=>$time_str,
			'total_num'=>$total_num,
			'max_num'=>$max_num,
			'list' => $list_desc,	
			'page_flag' => $isLimit,	
			'start_date' =>$start_date,
			'end_date' =>$end_date,
			'pager' => $pager,
		);
		$this->render('show_online_list',$assign);
	}
	
	/**
	 * 新手任务
	 */
	public function actionTask(){
		$taskService = new TaskService();
		$taskList = $taskService->getAllTask();
		$this->render('operators_task',array('list'=>$taskList));
	}
	
	/**
	 * 新手任务添加修改页面
	 */
	public function actionAddTask(){
	
		$notices = array();
		//是否是添加动作
		if($this->op == 'addTask' && in_array($this->op,$this->allowOp)){
			$notices = $this->addTaskDo();
		}
	
		//是否是修改
		$info = array();
		if($this->op == 'getTaskInfo' && in_array($this->op,$this->allowOp)){
			$tid = intval(Yii::app()->request->getParam('dataId'));
			$taskService = new TaskService();
			$info = $taskService->getTask($tid);
			if($info['pic']) $info['pic'] = $taskService->getAdminTaskImage($info['pic']);
		}
	
		if($this->isAjax){
			$this->renderPartial('operators_add_task',array('info'=>$info,'notices'=>$notices));
		}else{
			$this->render('operators_add_task',array('info'=>$info,'notices'=>$notices));
		}
	}
	
	/**
	 * 添加文章的总接口
	 */
	public function addArticle($data,$forum_sid,$redirect,$post_id){
		if ($data && $forum_sid && $redirect){
			$data['uid'] = Yii::app()->user->getId();
			$data['forum_sid'] = $forum_sid;
			$data['create_time'] = time();
			
			if (isset($data['thread_id'])){
				if($post_id){
					$post = array();
					$post['post_id'] = $post_id;
					$post['content'] = $data['content'];
					$post['create_time'] = time();
					if($this->bbsSer->editThread($data) && $this->bbsSer->editPost($post, false)){
						$this->redirect($redirect);
					}else{
						return $this->bbsSer->getNotice();
					}
				}
			}else{
				$title = $data['title'];
				$uid = Yii::app()->user->getId();
				$content = $data['content'];
				if($forum_sid){
					if($this->bbsSer->releaseThread($forum_sid,$title,$uid,$content,false)){
						$this->redirect($redirect);
					}else{
						return $this->bbsSer->getNotice();
					}
				}
			}
		}
		return array('info'=>array('输入的数据有误'));
	}
	
	/**
	 * 添加或修改新闻公告
	 * 
	 * @return Ambigous <获取用户界面友好提提示, 用户界面友好提提示>|multitype:multitype:string  
	 */
	public function addNewsNoticeDo(){
		$data = Yii::app()->request->getParam('newsnotice');
		$forum_sid = $this->getNewsNoticeForumSubId();
		$redirect = $this->createUrl('operators/newsnotice');
		$post_id = Yii::app()->request->getParam('post_id');
		return $this->addArticle($data, $forum_sid, $redirect, $post_id);
	}
	
	/**
	 * 添加或修改主播政策动作
	 * 
	 * @return Ambigous <获取用户界面友好提提示, 用户界面友好提提示>|multitype:multitype:string  
	 */
	public function addDoteyPolicyDo(){
		$data = Yii::app()->request->getParam('doteypolicy');
		$forum_sid = $this->getDoteyPolicyForumSubId();
		$redirect = $this->createUrl('operators/doteypolicy');
		$post_id = Yii::app()->request->getParam('post_id');
		return $this->addArticle($data, $forum_sid, $redirect, $post_id);
	}
	
	/**
	 * 添加或修改用户帮助
	 * 
	 * @return Ambigous <获取用户界面友好提提示, 用户界面友好提提示>|multitype:multitype:string  
	 */
	public function addUserHelpDo(){
		$data = Yii::app()->request->getParam('userhelp');
		if (isset($data['name'])){
			$subForum = $data['name'];
			unset($data['name']);
			$forum_sid = $this->getUserHelpForumSubId($subForum);
			$redirect = $this->createUrl('operators/userhelp',array('sub_forum'=>$subForum));
			$post_id = Yii::app()->request->getParam('post_id');
			return $this->addArticle($data, $forum_sid, $redirect, $post_id);
		}
		return array('info'=>array('输入的数据有误'));
	}
	
	/**
	 * 添加或修改主播帮助
	 * 
	 * @return Ambigous <获取用户界面友好提提示, 用户界面友好提提示>|multitype:multitype:string  
	 */
	public function addDoteyHelpDo(){
		$data = Yii::app()->request->getParam('doteyhelp');
		if (isset($data['name'])){
			$subForum = $data['name'];
			unset($data['name']);
			$forum_sid = $this->getDoteyHelpForumSubId($subForum);
			$redirect = $this->createUrl('operators/doteyhelp',array('sub_forum'=>$subForum));
			$post_id = Yii::app()->request->getParam('post_id');
			return $this->addArticle($data, $forum_sid, $redirect, $post_id);
		}
		return array('info'=>array('输入的数据有误'));
	}
	
	/**
	 * 添加或修改关于我们
	 * 
	 * @return Ambigous <获取用户界面友好提提示, 用户界面友好提提示>|multitype:multitype:string  
	 */
	public function addAboutUsDo(){
		$data = Yii::app()->request->getParam('aboutus');
		if (isset($data['name'])){
			$subForum = $data['name'];
			unset($data['name']);
			$forum_sid = $this->getAboutUsForumSubId($subForum);
			$redirect = $this->createUrl('operators/aboutus',array('sub_forum'=>$subForum));
			$post_id = Yii::app()->request->getParam('post_id');
			return $this->addArticle($data, $forum_sid, $redirect, $post_id);
		}
		return array('info'=>array('输入的数据有误'));
	}
	
	/**
	 * 执行添加修改客服动作
	 * 
	 * @param OperateService $operateSer
	 */
	public function addKefuDo(OperateService $operateSer){
		$kefu = Yii::app()->request->getParam('kefu');
		if($kefu){
			if($operateSer->saveKefu($kefu)){
				$this->redirect($this->createUrl('operators/kefu'));
			}else{
				return $operateSer->getNotice();
			}
		}
		return array('info'=>array('提交信息不完整，请确认'));
	}
	
	/**
	 * 执行勋章添加动作
	 * 
	 * @param UserMedalService $userMedalSer
	 * @return multitype:multitype:string  |Ambigous <获取用户界面友好提提示, 用户界面友好提提示>
	 */
	public function addMedalDo(UserMedalService $userMedalSer){
		$medal = Yii::app()->request->getParam('medal');
		if (!$medal){
			return array('info'=>array('提交的数据不能为空'));
		}
		
		//上传勋章图片
		$icon = $userMedalSer->uploadMedalIcon('medal');
		if($icon){
			$medal['icon'] = $icon;
		}else{
			unset($medal['icon']);
		}
		if($userMedalSer->saveMedal($medal)){
			$this->redirect($this->createUrl('operators/medal'));
		}else{
			return $userMedalSer->getNotice();
		}
	}
	
	/**
	 * 添加用户勋章操作
	 * 
	 * @param UserMedalService $userMedalSer
	 * @return multitype:multitype:string  |Ambigous <获取用户界面友好提提示, 用户界面友好提提示>
	 */
	public function addUserMedalDo(UserMedalService $userMedalSer){
		$medal = Yii::app()->request->getParam('medal');
		if (!$medal){
			return array('info'=>array('提交的数据不能为空'));
		}
		
		if (!isset($medal['uid'])){
			return array('info'=>array('缺少参数无法添加'));
		}
		
		if (!isset($medal['mid'])){
			return array('info'=>array('缺少参数无法添加'));
		}
		
		if (!isset($medal['type'])){
			return array('info'=>array('缺少参数无法添加'));
		}
		
		if (!isset($medal['vtime'])){
			return array('info'=>array('缺少参数无法添加'));
		}
		
		foreach ($medal['uid'] as $uid){
			$array = array();
			$array['uid'] = $uid;
			$array['mid'] = $medal['mid'];
			$array['type'] = $medal['type'];
			$array['vtime'] = strtotime($medal['vtime']);
			if(!$userMedalSer->getUserMedalByUid($uid,$medal['type'],$medal['mid'])){
				if(!$userMedalSer->saveUserMedal($array)){
					return $userMedalSer->getNotice();
				}
			}else{
				return array('info'=>array('该勋章该用户已经存在，可删除后再新增！'));
			}
		}
		$this->redirect($this->createUrl('operators/usermedal',array('type'=>$medal['type'],'uids'=>json_encode($medal['uid']))));
	}
	
	/**
	 * 添加赠品动作
	 */
	public function addGiveawayDo(){
		$_form = Yii::app()->request->getParam('_form');
		if (empty($_form) || !isset($_form['type']) || !isset($_form['uid'])){
			return array('info'=>array('缺少参数，新增失败'));
		}
		
		$type = $_form['type'];
		$uids = $_form['uid'];
		
		$consumeSer = new ConsumeService();
		if (!key_exists($type, $consumeSer->getGiveawayType())){
			return array('info'=>array('赠送类型不正确，新增失败'));
		}
		
		//赠送魅力值
		if ($type == GIVEAWAY_TYPE_CHARM){
			$num = intval($_form['num']);
			$info = $_form['info'];
			if (empty($num) || empty($info)){
				return array('info'=>array('赠送魅力值失败，赠送数量和描述不能为空'));
			}
			foreach ($uids as $uid){
				$consumeAttibute = array();
				$consumeAttibute['uid'] = $uid;
				$consumeAttibute['charm'] = abs($num);
				if($consumeSer->saveUserConsumeAttribute($consumeAttibute)){
					$addRecords = array();
					$addRecords['uid'] = $uid;
					$addRecords['charm'] =  abs($num);
					$addRecords['sender_uid'] =  Yii::app()->user->getId();
					$addRecords['num'] = 1;
					$addRecords['source'] = SOURCE_SENDS;
					$addRecords['sub_source'] = SUBSOURCE_SENDS_ADMIN;
					$addRecords['client'] = CLIENT_ADMIN;
					$addRecords['info'] = $info;
					$consumeSer->saveDoteyCharmRecords($addRecords);
				}
			}
			$consumeSer->saveAdminOpLog('新增 赠品：魅力值('.abs($num).') 到UIDS('.implode(',', $uids).')');
			$source = SOURCE_SENDS.'*'.SUBSOURCE_SENDS_ADMIN;
			$client = CLIENT_ADMIN;
		}
		//赠送魅力点
		if ($type == GIVEAWAY_TYPE_CHARMPOINTS){
			$num = intval($_form['num']);
			$info = $_form['info'];
			if (empty($num) || empty($info)){
				return array('info'=>array('赠送魅力点失败，赠送数量和描述不能为空'));
			}
			foreach ($uids as $uid){
				$consumeAttibute = array();
				$consumeAttibute['uid'] = $uid;
				$consumeAttibute['charm_points'] = abs($num);
				if($consumeSer->saveUserConsumeAttribute($consumeAttibute)){
					$addRecords = array();
					$addRecords['uid'] = $uid;
					$addRecords['charm_points'] =  abs($num);
					$addRecords['sender_uid'] =  Yii::app()->user->getId();
					$addRecords['num'] = 1;
					$addRecords['source'] = SOURCE_SENDS;
					$addRecords['sub_source'] = SUBSOURCE_SENDS_ADMIN;
					$addRecords['client'] = CLIENT_ADMIN;
					$addRecords['info'] = $info;
					$consumeSer->saveDoteyCharmPointsRecords($addRecords);
				}
			}
			$consumeSer->saveAdminOpLog('新增 赠品：魅力点('.abs($num).') 到UIDS('.implode(',', $uids).')');
			$source = SOURCE_SENDS.'*'.SUBSOURCE_SENDS_ADMIN;
			$client = CLIENT_ADMIN;
		}
		
		//赠送贡献值
		if ($type == GIVEAWAY_TYPE_DEDICATION){
			$num = intval($_form['num']);
			$info = $_form['info'];
			if (empty($num) || empty($info)){
				return array('info'=>array('赠送贡献值失败，赠送数量和描述不能为空'));
			}
			foreach ($uids as $uid){
				$consumeAttibute = array();
				$consumeAttibute['uid'] = $uid;
				$consumeAttibute['dedication'] = abs($num);
				if($consumeSer->saveUserConsumeAttribute($consumeAttibute)){
					$addRecords = array();
					$addRecords['uid'] = $uid;
					$addRecords['dedication'] =  abs($num);
					$addRecords['from_target_id'] =  Yii::app()->user->getId();
					$addRecords['num'] = 1;
					$addRecords['source'] = SOURCE_SENDS;
					$addRecords['sub_source'] = SUBSOURCE_SENDS_ADMIN;
					$addRecords['client'] = CLIENT_ADMIN;
					$addRecords['info'] = $info;
					$consumeSer->saveUserDedicationRecords($addRecords);
				}
			}
			$consumeSer->saveAdminOpLog('新增 赠品：贡献值('.abs($num).') 到UIDS('.implode(',', $uids).')');
			$source = SOURCE_SENDS.'*'.SUBSOURCE_SENDS_ADMIN;
			$client = CLIENT_ADMIN;
		}
		
		//赠送礼物
		if ($type == GIVEAWAY_TYPE_GIFT){
			$adminUid = Yii::app()->user->getId();
			$num = intval($_form['num']);
			$_info = $_form['info'];
			$gift_id = $_form['gift_id'];
			if (empty($num) || empty($_info) || empty($gift_id)){
				return array('info'=>array('赠送礼物失败，赠送数量和描述和礼物对象不能为空'));
			}
			
			$giftBagSer = new GiftBagService();
			$giftSer = new GiftService();
			$userSer = new UserService();
			//礼物信息
			$giftInfo = $giftSer->getGiftByIds(array($gift_id));
			$giftInfo = $giftInfo[$gift_id];
			//用户信息
			$userInfo = $userSer->getUserBasicByUids($uids);
			//管理员信息
			$adminInfo = $userSer->getUserBasicByUids(array($adminUid));
			$adminInfo = $adminInfo[$adminUid];
			
			if($giftInfo && $adminInfo && $userInfo){
				foreach ($uids as $uid){
					$_gift = array();
					$_gift['uid'] = $uid;
					$_gift['gift_id'] = $gift_id;
					$_gift['num'] = $num;
					
					$info = array();
					$info['uid'] = $uid;
					$info['nickname'] = $userInfo[$uid]['nickname'];
					$info['from_uid'] = $adminInfo['uid'];
					$info['from_nickname'] = $adminInfo['nickname'];
					$info['gift_id'] = $gift_id;
					$info['gift_name'] = $giftInfo['zh_name'];
					$info['num'] = $num;
					$info['remark'] = $_info;
						
					$addRecords = array();
					$addRecords['uid'] = $uid;
					$addRecords['gift_id'] =  $gift_id;
					$addRecords['num'] = $num;
					$addRecords['source'] = BAGSOURCE_TYPE_ADMIN;
					$addRecords['info'] = serialize($info);
					
					$giftBagSer->saveUserGiftBagByUid($_gift, $addRecords);
				}
				$consumeSer->saveAdminOpLog('新增 赠品：礼物('.$gift_id.') 数量('.abs($num).') 到UIDS('.implode(',', $uids).')');
			}
			
			$source = BAGSOURCE_TYPE_ADMIN;
			$client = null;
		}
		
		//送道具
		if($type == GIVEAWAY_TYPE_PROPS){
			$adminUid = Yii::app()->user->getId();
			$days = intval($_form['days']);
			$info = $_form['info'];
			$cat_id_str = $_form['cat_id'];
			$prop_id_str = $_form['prop_id'];
			
			$num =isset($_form['num']) && intval($_form['num'])>0?intval($_form['num']):1;
			if(!is_int($num))
				return array('info'=>array('数量必须是整数'));

			$_onum = 0;
			if ($days<0 || empty($info) || empty($cat_id_str) || empty($prop_id_str)){
				return array('info'=>array('赠送道具失败，有效天，描述，道具分类和道具名称不能为空'));
			}
			
			$propsSer = new PropsService();
			$userPropsSer = new UserPropsService();
			
			$cat_id_str = explode('*',$cat_id_str);
			$cat_id = $cat_id_str[0];
			$cat_en_name = $cat_id_str[1];
			
			$prop_id_str = explode('*',$prop_id_str);
			$prop_id = $prop_id_str[0];
			$prop_en_name = $prop_id_str[1];
			$propInfo = $propsSer->getPropsByIds(array($prop_id));
			$propInfo = $propInfo[$prop_id];
			
			$propAttrInfo = $propsSer->getPropsAttributeByPropIds(array($prop_id));
			$propAttrInfo = $propAttrInfo[$prop_id];
			if ($propInfo && $propAttrInfo){
				//判断月卡的有效天
				if (strtolower($cat_en_name) == 'monthcard'){
					if ($days <= 0 || $days > 30){
						return array('info'=>array('赠送月卡的有效天必须是1-30天以内'));
					}
				}
				
				//座驾入场动画
				$isSendZmq = true;
				if (strtolower($cat_en_name) == 'car'){
					$isSendZmq = false;
					foreach ($propAttrInfo as $v){
						if($v['attr_enname'] == 'car_animation'){
							$flash = $v['value'];
						}
						if($v['attr_enname'] == 'car_animation_time'){
							$flash_time = $v['value'];
						}
					}
					
					if(!isset($flash)){
						return array('info'=>array('赠送座驾失败，入场动画不能为空'));
					}
					
					if(!isset($flash_time)){
						return array('info'=>array('赠送座驾失败，入场动画时间不能为空'));
					}
				}
				
				if (strtolower($cat_en_name) == 'prop' || strtolower($cat_en_name) == 'label'){
					$isSendZmq = false;
				}
				
				//批量操作
				foreach ($uids as $uid){
					$isExec = true; 			#是否可执行
					$isUpdatePropsAttr = false;	#是否更改用户道具属性
					
					$_info = $userPropsSer->getUserValidPropsOfBagByPropId($uid,$prop_id); #是否已经存在于背包中
					if ($_info){
						$_onum = $_info[0]['num'];
					}
					$bags = array();
					$bags['uid'] = $uid;
					$bags['prop_id'] = $prop_id;
					$bags['cat_id'] = $cat_id;
					$bags['record_sid'] = $adminUid;
					$bags['target_id'] = 0;
					$bags['s_num'] = $num;
					$bags['valid_time'] = $days>0?time()+($days*3600*24):0;
					//月卡有效天
					if (strtolower($cat_en_name) == 'monthcard'){
						if ($_info){
							if($_info[0]['valid_time'] >= time()){
								$isExec = false; #未过期的月卡
								return array('info'=>array('当前有未过期月卡，不能赠送'));
							}
						}
						if ($isExec){
							$bags['s_num'] = $days;
						}
					}
					
/* 					if (strtolower($cat_en_name) == 'prop'  || strtolower($cat_en_name) == 'flyscreen'){
						if ($_info){
							$_onum = $_info[0]['num'];
							$bags['num'] += $_onum;
						}
					} */
					//vip 是否更改用户道具属性
					if (strtolower($cat_en_name) == 'vip'){
						$color = $propInfo['en_name'];
						$orgColorType = 0;
						$colorType = ($color=='vip_yellow')?1:($color=='vip_purple'?2:0);
						
						$uProAttr = $userPropsSer->getUserPropsAttributeByUid($uid);
						if($uProAttr){
							$orgColorType = $uProAttr['vip_type'];
						}
						
						if ($colorType >= $orgColorType){
							$isUpdatePropsAttr = true;
							$isSendZmq = true;
						}else{
							$isSendZmq = false;
						}
						
					}
					
					if($isExec){
						//更新用户道具属性
						if($isUpdatePropsAttr){
							$propsAttriute = array();
							if (strtolower($cat_en_name) == 'vip'){
								$propsAttriute['uid'] = $uid;
								$propsAttriute['vip'] = $prop_id;
								$propsAttriute['vip_type'] = $colorType;
								$propsAttriute['is_hidden'] = 0;
							}
							
							if($propsAttriute){
								$userPropsSer->saveUserPropsAttribute($propsAttriute);
							}
						}

						//存背包,vip特别处理
						if (strtolower($cat_en_name) == 'vip'){
							$bag_id = $propsSer->saveVipToBag($bags, $days,1); #写入道具背包
						}
						else
						{
							$bag_id = $userPropsSer->saveUserPropsBag($bags,$propInfo); #写入道具背包
						}
						
						$_info = $userPropsSer->getUserValidPropsOfBagByPropId($uid,$prop_id); #获取已经存在的背包记录
						if($bag_id){
							//存记录
							$records = array();
							$records['uid'] = $uid;
							$records['prop_id'] = $prop_id;
							$records['cat_id'] = $cat_id;
							$records['pipiegg'] = $propInfo['pipiegg'];
							$records['dedication'] = $propInfo['dedication'];
							$records['egg_points'] = $propInfo['egg_points'];
							$records['charm_points'] = $propInfo['charm_points'];
							$records['charm'] = $propInfo['charm'];
							$records['source'] = PROPSRECORDS_SOURCE_ADMIN;
							$records['info'] = $propInfo['name'].'('.$info.')';
							//$records['amount'] = $bags['num']-$_onum;
							if (strtolower($cat_en_name) == 'vip'){
								$records['amount'] = $days;
							}
							else
							{
								$records['amount'] = $bags['s_num'];
							}
							$records['vtime'] = $_info[0]['valid_time'];
							if($userPropsSer->saveUserPropsRecords($records) && $isSendZmq){
								if (strtolower($cat_en_name) == 'vip'){
									$propsSer->updateUserJsonOfVip($uid, $prop_id);
								}
								else {
									//发消息
									$zmqInfo = array();
									$zmqInfo['num'] = $bags['s_num'];
									$zmqInfo['valid_time'] = $_info[0]['valid_time'];
									$zmqInfo['image'] = $propInfo['image'];
									$zmqInfo['name'] = $propInfo['name'];
									$zmqInfo['flash'] = isset($flash)?$flash:null;
									$zmqInfo['type'] = isset($colorType)?$colorType:0;
									$zmqInfo['hide'] = 0;
									$zmqInfo['timeout'] = isset($flash_time)?$flash_time:null;
									$userPropsSer->sendPropsZmq($uid,$cat_en_name,$zmqInfo);
								}
							}
						}
					}
				}
				$consumeSer->saveAdminOpLog('新增 赠品：道具('.$prop_id.') 到UIDS('.implode(',', $uids).')');
			}
			
			$source = PROPSRECORDS_SOURCE_ADMIN;
			$client = ''; 
		}
		
		$this->redirect($this->createUrl('operators/giveaway',array('source'=>$source,'client'=>$client,'type'=>$type)));
	}
	
	/**
	 * 新手任务添加修改
	 *
	 * @return array
	 */
	public function addTaskDo(){
		$data = Yii::app()->request->getParam('data');
		if(!empty($data)){
			$taskService = new TaskService();
			$pic = $taskService->uploadSingleImages('pic', 'task');
			if($pic) $data['pic'] = $pic;
				
			if($taskService->adminSaveTask($data)){
				$this->redirect($this->createUrl('operators/task'));
			}else{
				return $taskService->getNotice();
			}
		}
		return array('info'=>array('输入的数据有误'));
	}
	
	/**
	 * 标记意见为已处理
	 * 
	 * @param OperateService $operateSer
	 */
	public function flagSuggestDo(OperateService $operateSer){
		if(!$this->isAjax){
			exit('不合法请求');
		}
		
		if(!($suggestId = Yii::app()->request->getParam('suggestId'))){
			exit('缺少参数');
		}
		$data = array();
		$data['suggest_id'] = $suggestId;
		$data['is_handle'] = SUGGEST_HANDLER_YES;
		if($operateSer->saveSuggest($data)){
			exit('1');
		}else{
			exit('标识失败');
		}
	}
	
	/**
	 * 获取新闻公告的子板块ID
	 * 
	 * @return boolean
	 */
	public function getNewsNoticeForumSubId(){
		$forum_sid = false;
		$conditions = array(
			'forum_name'=>OPERATORS_CMS_NEWSNOTICE_FORUMNAME,
			'forum_sname'=>OPERATORS_CMS_NEWSNOTICE_FORUMNAME,
			'ower_uid'=>OPERATORS_CMS_NEWSNOTICE_OWERUID,
			'from'=>OPERATORS_CMS_FROM_TYPE
		);
		if($forum = $this->bbsSer->getFormByConditions($conditions)){
			$forum_sid = $forum[0]['forum_sid'];
		}
		return $forum_sid;
	}
	
	/**
	 * 获取主播政策的子板块ID
	 *
	 * @return boolean
	 */
	public function getDoteyPolicyForumSubId(){
		$forum_sid = false;
		$conditions = array(
			'forum_name'=>OPERATORS_CMS_DOTEYPOLICY_FORUMNAME,
			'forum_sname'=>OPERATORS_CMS_DOTEYPOLICY_FORUMNAME,
			'ower_uid'=>OPERATORS_CMS_DOTEYPOLICY_OWERUID,
			'from'=>OPERATORS_CMS_FROM_TYPE
		);
		if($forum = $this->bbsSer->getFormByConditions($conditions)){
			$forum_sid = $forum[0]['forum_sid'];
		}
		return $forum_sid;
	}
	
	/**
	 * 获取用户帮助的所有子模块ID
	 * @return boolean
	 */
	public function getUserHelpForumSubId($subForumName){
		$forum_sid = false;
		if($subForumName){
			$conditions = array(
				'forum_name'=>OPERATORS_CMS_USERHELP_FORUMNAME,
				'forum_sname'=>$subForumName,
				'ower_uid'=>OPERATORS_CMS_USERHELP_OWERUID,
				'from'=>OPERATORS_CMS_FROM_TYPE
			);
			if($forum = $this->bbsSer->getFormByConditions($conditions)){
				$forum_sid = $forum[0]['forum_sid'];
			}
		}
		return $forum_sid;
	}
	
	/**
	 * 获取用户帮助的所有子模块ID
	 * @return boolean
	 */
	public function getDoteyHelpForumSubId($subForumName){
		$forum_sid = false;
		if($subForumName){
			$conditions = array(
				'forum_name'=>OPERATORS_CMS_DOTEYHELP_FORUMNAME,
				'forum_sname'=>$subForumName,
				'ower_uid'=>OPERATORS_CMS_DOTEYHELP_OWERUID,
				'from'=>OPERATORS_CMS_FROM_TYPE
			);
			if($forum = $this->bbsSer->getFormByConditions($conditions)){
				$forum_sid = $forum[0]['forum_sid'];
			}
		}
		return $forum_sid;
	}
	
	/**
	 * 获取用户帮助的所有子模块ID
	 * @return boolean
	 */
	public function getAboutUsForumSubId($subForumName){
		$forum_sid = false;
		if($subForumName){
			$conditions = array(
				'forum_name'=>OPERATORS_CMS_ABOUTUS_FORUMNAME,
				'forum_sname'=>$subForumName,
				'ower_uid'=>OPERATORS_CMS_ABOUTUS_OWERUID,
				'from'=>OPERATORS_CMS_FROM_TYPE
			);
			if($forum = $this->bbsSer->getFormByConditions($conditions)){
				$forum_sid = $forum[0]['forum_sid'];
			}
		}
		return $forum_sid;
	}
	
	/**
	 * 获取主题信息
	 */
	public function getThreadInfo(){
		$info = array();
		if(!($threadId = Yii::app()->request->getParam('threadId'))){
			exit('缺少参数');
		}
		if(!($info = $this->bbsSer->getThreadInfo($threadId))){
			exit('要修改的数据不存在');
		}
		return $info;
	}
	
	/**
	 * 获取主题内容
	 * @return Ambigous <NULL>
	 */
	public function getPostInfo(){
		$info = array();
		if(!($threadId = Yii::app()->request->getParam('threadId'))){
			exit('缺少参数');
		}
		
		if(!($info = $this->bbsSer->getPostList($threadId))){
			exit('要修改的数据不存在');
		}
		return $info[0];
	}
	/**
	 * 获取客服信息
	 * 
	 * @param OperateService $operateSer
	 * @return Ambigous <mix, Ambigous, NULL, unknown, multitype:unknown Ambigous <unknown, NULL> , mixed, multitype:, multitype:unknown >
	 */
	public function getKefuDo(OperateService $operateSer){
		$kefuId = Yii::app()->request->getParam('kefuId');
		if($kefuId){
			if($info = $operateSer->getKefuInfoById($kefuId)){
				return $info;
			}else{
				exit('要修改的数据不存在');
			}
		}
		exit('缺少参数');
	}
	
	public function getSearchCondition(){
		$condition = array();
		
		if (Yii::app()->request->getParam('form')){
			return Yii::app()->request->getParam('form');
		}
		
		if (Yii::app()->request->getParam('username')){
			$condition['username'] = Yii::app()->request->getParam('username');
		}
		
		if (Yii::app()->request->getParam('nickname')){
			$condition['nickname'] = Yii::app()->request->getParam('nickname');
		}
		
		if (Yii::app()->request->getParam('realname')){
			$condition['realname'] = Yii::app()->request->getParam('realname');
		}
		
		if (Yii::app()->request->getParam('mid')){
			$condition['mid'] = Yii::app()->request->getParam('mid');
		}
		
		if (Yii::app()->request->getParam('type')){
			$condition['type'] = Yii::app()->request->getParam('type');
		}
		
		if (Yii::app()->request->getParam('uid')){
			$condition['uid'] = Yii::app()->request->getParam('uid');
		}
		
		if (Yii::app()->request->getParam('uids')){
			$uids = Yii::app()->request->getParam('uids');
			$condition['uids'] = json_decode($uids,true);
		}
		
		if (Yii::app()->request->getParam('is_handle')){
			$condition['is_handle'] = Yii::app()->request->getParam('is_handle');
		}
		
		if (Yii::app()->request->getParam('create_time_on')){
			$condition['create_time_on'] = Yii::app()->request->getParam('create_time_on');
		}
		
		if (Yii::app()->request->getParam('create_time_end')){
			$condition['create_time_end'] = Yii::app()->request->getParam('create_time_end');
		}
		
		if (Yii::app()->request->getParam('suggestId')){
			$condition['suggestId'] = Yii::app()->request->getParam('suggestId');
		}
		
		if (Yii::app()->request->getParam('source')){
			$condition['source'] = Yii::app()->request->getParam('source');
		}
		
		if (Yii::app()->request->getParam('client')){
			$condition['client'] = Yii::app()->request->getParam('client');
		}
		
		return $condition;
	}
	
	/**
	 * 获取主播信息
	 *
	 * @param array $list
	 * @param unknown_type $uids
	 * @param unknown_type $archivesIds
	 * @return Ambigous <multitype:, mix, multitype:NULL >
	 */
	public function getUserInfo(Array $list,&$uids = array(),&$archivesIds = array(),$isUserInfo = true){
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
				$userSer = new UserService();
				return $userSer->getUserBasicByUids($uids);
			}
		}
		return array();
	}
	
	/**
	 * 获取来源
	 */
	public function getConsumeSourceList(){
		$result = array();
		$consumeSer = new ConsumeService();
		$list = $consumeSer->getSourceList();
		if($list){
			foreach($list as $k=>$v){
				if(isset($v['subsource'])){
					$result[$k] = '【'.$v['name'].'】';
					foreach($v['subsource'] as $k2=>$v2){
						$result[$k.'*'.$k2] = '--'.$v2;
					}
				}
			}
		}
		return $result;
	}
	
	public function getGiveawayView($type){
		if (!$this->isAjax){
			exit('不合法请求');
		}
		
		$consumeSer = new ConsumeService();
		if (!key_exists($type, $consumeSer->getGiveawayType())) {
			exit('不合法的赠品类型，请确认');
		}
		
		exit($this->renderPartial('_add_giveaway_'.$type));
	}
	
	/**
	 * 获取礼物列表 
	 */
	public function getGiftListOption(){
		$giftList = array();
		$giftSer = new GiftService();
		$list = $giftSer->getGiftList();
		if($list){
			foreach($list as $v){
				$giftList[$v['gift_id']] = $v['zh_name'];
			}
		}
		return $giftList;
	}
	
	/**
	 * 获取道具分类
	 * 
	 * @return array
	 */
	public function getAllowSendPropsCat(){
		$newCat = array();
		$propSer = new PropsService();
		$allCat = $propSer->getPropsCatList();
		if($allCat){
			foreach ($allCat as $cat_id => $catInfo){
				if (in_array($catInfo['en_name'], array('car','monthcard','vip','prop','flyscreen','broadcast'))){
					$newCat[$catInfo['cat_id'].'*'.$catInfo['en_name']] = $catInfo['name'];
				}
			}
		}
		return $newCat;
	}
	
	/**
	 * 删除新闻公告操作
	 */
	public function delNewsNoticeDo(){
		if(!$this->isAjax){
			exit('不合法请求');
		}
		if(!($threadId = Yii::app()->request->getParam('threadId'))){
			exit('缺少参数');
		}
		if(!($info = $this->bbsSer->getThreadInfo($threadId))){
			exit('要删除的数据不存在');
		}
		if($info['forum_sid'] == $this->getNewsNoticeForumSubId()){
			if($this->bbsSer->deleteThread($threadId)){
				exit('1');
			}else{
				exit("删除失败");
			}
		}else{
			exit("你没有权限删除此公告");
		}
	}
	
	/**
	 * 删除主播政策操作
	 */
	public function delDoteyPolicyDo(){
		if(!$this->isAjax){
			exit('不合法请求');
		}
		if(!($threadId = Yii::app()->request->getParam('threadId'))){
			exit('缺少参数');
		}
		if(!($info = $this->bbsSer->getThreadInfo($threadId))){
			exit('要删除的数据不存在');
		}
		if($info['forum_sid'] == $this->getDoteyPolicyForumSubId()){
			if($this->bbsSer->deleteThread($threadId)){
				exit('1');
			}else{
				exit("删除失败");
			}
		}else{
			exit("你没有权限删除该主播政策内容");
		}
	}
	
	/**
	 * 删除用户帮助
	 */
	public function delUserHelpDo(){
		if(!$this->isAjax){
			exit('不合法请求');
		}
		if(!($threadId = Yii::app()->request->getParam('threadId'))){
			exit('缺少参数');
		}
		if(!($subForum = Yii::app()->request->getParam('sub_forum'))){
			exit('缺少参数');
		}
		
		if(!($info = $this->bbsSer->getThreadInfo($threadId))){
			exit('要删除的数据不存在');
		}
		if($info['forum_sid'] == $this->getUserHelpForumSubId($subForum)){
			if($this->bbsSer->deleteThread($threadId)){
				exit('1');
			}else{
				exit("删除失败");
			}
		}else{
			exit("你没有权限删除该用户帮助相关的内容");
		}
	}

	/**
	 * 删除主播帮助
	 */
	public function delDoteyHelpDo(){
		if(!$this->isAjax){
			exit('不合法请求');
		}
		if(!($threadId = Yii::app()->request->getParam('threadId'))){
			exit('缺少参数');
		}
		if(!($subForum = Yii::app()->request->getParam('sub_forum'))){
			exit('缺少参数');
		}
	
		if(!($info = $this->bbsSer->getThreadInfo($threadId))){
			exit('要删除的数据不存在');
		}
		if($info['forum_sid'] == $this->getDoteyHelpForumSubId($subForum)){
			if($this->bbsSer->deleteThread($threadId)){
				exit('1');
			}else{
				exit("删除失败");
			}
		}else{
			exit("你没有权限删除该主播帮助相关的内容");
		}
	}
	
	/**
	 * 删除关于我们
	 */
	public function delAboutUsDo(){
		if(!$this->isAjax){
			exit('不合法请求');
		}
		if(!($threadId = Yii::app()->request->getParam('threadId'))){
			exit('缺少参数');
		}
		if(!($subForum = Yii::app()->request->getParam('sub_forum'))){
			exit('缺少参数');
		}
	
		if(!($info = $this->bbsSer->getThreadInfo($threadId))){
			exit('要删除的数据不存在');
		}
		if($info['forum_sid'] == $this->getAboutUsForumSubId($subForum)){
			if($this->bbsSer->deleteThread($threadId)){
				exit('1');
			}else{
				exit("删除失败");
			}
		}else{
			exit("你没有权限删除该主播帮助相关的内容");
		}
	}
	
	/**
	 * 删除客服
	 * 
	 * @param OperateService $operateSer
	 */
	public function delKefuDo(OperateService $operateSer){
		if(!$this->isAjax){
			exit('不合法请求');
		}
		
		if(!($kefuId = Yii::app()->request->getParam('kefuId'))){
			exit('缺少参数');
		}
		
		if($operateSer->delKefuById($kefuId)){
			exit('1');
		}else{
			exit('删除失败');
		}
	}
	
	/**
	 * 删除勋章
	 * 
	 * @param UserMedalService $userMedalSer
	 */
	public function delMedalDo(UserMedalService $userMedalSer){
		if(!$this->isAjax){
			exit('不合法请求');
		}
		
		if(!($mid = Yii::app()->request->getParam('mid'))){
			exit('缺少参数');
		}
		
		if($userMedalSer->delMedal($mid)){
			exit('1');
		}else{
			exit('删除失败');
		}
	}
	
	/**
	 * 删除用户勋章操作
	 * 
	 * @param UserMedalService $userMedalSer
	 */
	public function delUserMedalDo(UserMedalService $userMedalSer){
		if(!$this->isAjax){
			exit('不合法请求');
		}
		
		if(!($rid = Yii::app()->request->getParam('rid'))){
			exit('缺少参数');
		}
		
		if($userMedalSer->delUserMedal($rid)){
			exit('1');
		}else{
			exit('删除失败');
		}
	}
	
	/**
	 * 执行删除意见的操作
	 * 
	 * @param OperateService $operateSer
	 */
	public function delSuggestDo(OperateService $operateSer){
		if(!$this->isAjax){
			exit('不合法请求');
		}
		
		if(!($suggestId = Yii::app()->request->getParam('suggestId'))){
			exit('缺少参数');
		}
		
		if($operateSer->delUserSuggest($suggestId)){
			exit('1');
		}else{
			exit('删除失败');
		}
	}
	
	/**
	 * 格式化勋章列表
	 * 
	 * @param array $list
	 */
	public function formatMedalList(Array $list){
		$result = array();
		foreach ($list as $v){
			$result[$v['mid']] = $v['name'];
		}
		return $result;
	}
	
	public function formatGiveawayList(Array &$list,$type,$_commonSer){
		if ($list){
			if ($type == GIVEAWAY_TYPE_GIFT){
				$giftSer = new GiftService();
				$giftIds = array();
				foreach($list as &$r){
					$giftIds[$r['gift_id']] = $r['gift_id'];
					$r['info'] = unserialize($r['info']);
				}
				
				$giftInfos = $giftSer->getGiftByIds($giftIds);
				foreach($list as &$r){
					if(isset($giftInfos[$r['gift_id']])){
						$r['gift_id'] = $giftInfos[$r['gift_id']];
					}
				}
			}
			
			if ($type == GIVEAWAY_TYPE_PROPS){
				$propsSer = new PropsService();
				$propsIds = array();
				foreach($list as $r){
					$propsIds[$r['prop_id']] = $r['prop_id'];
				}
				$propsInfo = $propsSer->getPropsByIds($propsIds);
				foreach($list as &$r){
					$r['prop_id'] = $propsInfo[$r['prop_id']];
				}
			}
		}
	}
	
	/**
	 * AJAX 检验主播信息的合法性
	 */
	public function checkDoteyInfo($isDotey = true){
		if (!$this->isAjax){
			exit('不合法请求');
		}
	
		$doteyName = Yii::app()->request->getParam('doteyName');
		if(empty($doteyName)){
			exit('请输入主播信息后进行校验 ');
		}
	
		$userSer = new UserService();
		$doteySer = new DoteyService();
		if(!is_numeric($doteyName)){
			if(!($userInfo = $userSer->getVadidatorUser($doteyName,0))){
				exit('不合法用户，请重新输入');
			}
			$uid = $userInfo['uid'];
		}else{
			$uid = (int)$doteyName;
		}
	
		if ($uid){
			if($isDotey){
				if(!($doteyInfo = $doteySer->getDoteyInfoByUid($uid))){
					exit('该用户不是主播，请确认');
				}
			}
				
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
	 * 检查道具列表
	 */
	public function checkPropList(){
		if (!$this->isAjax){
			exit('不合法请求');
		}
		
		$cat_id = Yii::app()->request->getParam('cat_id');
		if(empty($cat_id)){
			exit('1');
		}
		$catInfo = explode('*', $cat_id);
		$cat_id = $cat_id[0];
		
		$propsSer = new PropsService();
		$propsModel = PropsModel::model();
		$propModels = $propsModel->findAll('(status = 0 OR status = 2) AND cat_id = '.$cat_id);
		$info = $propsSer->arToArray($propModels);
		$info = $propsSer->buildProps($info);
		if($info){
			$html = '';
			foreach ($info as $v){
				$html .= "<option value='".$v['prop_id'].'*'.$v['en_name']."'>{$v['name']}</option>";
			}
			if($html){
				exit($html);
			}
		}
		exit('2');
	}
	
}
