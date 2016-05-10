<?php
define('OPERATORS_CMS_FROM_TYPE', 4);
define('OPERATORS_CMS_NEWSNOTICE_FORUMNAME','新闻公告');
define('OPERATORS_CMS_NEWSNOTICE_OWERUID',1);
define('OPERATORS_CMS_DOTEYPOLICY_FORUMNAME','主播政策');
define('OPERATORS_CMS_DOTEYPOLICY_OWERUID',2);
define('OPERATORS_CMS_USERHELP_FORUMNAME','用户帮助');
define('OPERATORS_CMS_USERHELP_OWERUID',3);
define('OPERATORS_CMS_DOTEYHELP_FORUMNAME','主播帮助');
define('OPERATORS_CMS_DOTEYHELP_OWERUID',4);
define('OPERATORS_CMS_ABOUTUS_FORUMNAME','关于我们');
define('OPERATORS_CMS_ABOUTUS_OWERUID',5);
define('OPERATORS_CMS_AGENTPOLICY_FORUMNAME','代理政策');
define('OPERATORS_CMS_AGENTPOLICY_OWERUID',6);


define('FORUM_FROM_TYPE_PERSONAL', 1); //个人主页用
define('FORUM_FROM_TYPE_FAMILY', 2); //家族用
define('FORUM_FROM_TYPE_CSITE', 3); //C站用
define('FORUM_FROM_TYPE_ADMIN', 4); //后台用
/**
 * BBS服务层
 *
 * the last known user to change this file in the repository  <$LastChangedBy: guoshaobo $>
 * @author Guo Shaobo <guoshaobo@pipi.cn>
 * @version $Id: BbsbaseService.php 2013-04-16 15:29:37  guoshaobo
 * @package service
 */
class BbsbaseService extends PipiService {
	private static $instance;
	private $hot_post_count = 100; //热帖数量
	/**
	 * 返回FamilyService对象的单例
	 * @return BbsbaseService
	 */
	public static function getInstance(){
		if(self::$instance == null){
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	/**
	 * 创建新的bbs
	 * @param $uid		创建者uid
	 * @param $data		创建需要的数据array('from' => null, 'fromId' => null, 'name' => null)from 来源类型, fromId来源id, name 名称
	 * @return integer	成功返回int, 失败返回0;
	 * n 方法未被使用过标识
	 */
	public function createForum($uid, $name, $from = OPERATORS_CMS_FROM_TYPE, $fromId = 0){
		if(intval($uid) <= 0 || empty($name)) {
			return $this->setError(Yii::t('common','Parameter is empty'), 0);
		}
		$forum = array(
			'ower_uid'	=> $uid,
			'from'		=> $from,
			'from_id'	=> $fromId,
			'status'	=> 1,
		);
		if($forumId = $this->saveForum($forum)){
			$name = is_array($name) ? $name : array($name);
			foreach($name as $n){
				$sub = array(
					'forum_id'	=> $forumId,
					'name'		=> $n,
				);
				$this->saveForumSub($sub);
			}
			return $forumId;
		}else{
			return $this->setError(Yii::t('common','System error'), 0);
		}
	}
	
	/**
	 * 创建子板块
	 * @param int $forumId
	 * @param string $name
	 * @return number
	 */
	public function createSubForum($forumId, $name){
		$sub = array(
			'forum_id'	=> $forumId,
			'name'		=> $name,
		);
		return $this->saveForumSub($sub);
	}
	
	/**
	 * 修改子板块
	 * @param int $forumId
	 * @param array $forum
	 * @return number
	 */
	public function updateSubForum($forumId, array $forum){
		$forum['forum_sid'] = $forumId;
		return $this->saveForumSub($forum);
	}
	
	/**
	 * 保存父版块
	 * @param array $data
	 * @return number
	 */
	private function saveForum(array $data){
		if(isset($data['forum_id']) && intval($data['forum_id']) < 1){
			return $this->setError(Yii::t('common','Parameter is empty'), 0);
		}
		$model = new BbsForumModel();
		if(isset($data['forum_id'])){
			$model = $model->findByPk($data['forum_id']);
			if(empty($model)) return $this->setError(Yii::t('common','Data not exists'), 0);
		}else{
			switch ($data['from']) {
				case FORUM_FROM_TYPE_PERSONAL:
					$data['name'] = '个人主页';
					break;
				case FORUM_FROM_TYPE_FAMILY:
					$data['name'] = '家族';
					break;
				case FORUM_FROM_TYPE_CSITE:
					$data['name'] = 'C站';
					break;
				case FORUM_FROM_TYPE_ADMIN:
					$data['name'] = '运营 CMS';
					break;
				default:
					$data['name'] = '其他';
					break;
			}
			$data['status'] = 1;
		}	
		$this->attachAttribute($model, $data);
		if(!$model->validate()){
			return $this->setNotices($model->getErrors(), 0);
		}
		$model->save();
		return $model->getPrimaryKey();
	}
	
	/**
	 * 保存子版块
	 * @param $fromid	bbs的id
	 * @param $name		子版块的名称
	 * @return integer 成功返回子版块的id, 失败返回0;
	 * n
	 */
	private function saveForumSub(array $data){
		if(isset($data['forum_sid']) && intval($data['forum_sid']) < 1 || !isset($data['forum_sid']) && intval($data['forum_id']) < 1){
			return $this->setError(Yii::t('common','Parameter is empty'), 0);
		}
		$model = new BbsForumSubModel();
		if(isset($data['forum_sid'])){
			$model = $model->findByPk($data['forum_sid']);
			if(empty($model)) return $this->setError(Yii::t('common','Data not exists'), 0);
		}else{
			if(!isset($data['flag'])) $data['flag'] = 0;
			$data['create_time'] = time();
		}
		$this->attachAttribute($model, $data);
		if(!$model->validate()){
			return $this->setNotices($model->getErrors(), 0);
		}
		$model->save();
		return $model->getPrimaryKey();
	}
	
	/**
	 * 获取某来源的所有子版块列表
	 * @param $from		来源类型, 1为主播, 2为家族, 3为C站, 4为运营CMS
	 * @param $fromId	来源id
	 * @param $getHide	是否获取非正常状态的子版块
	 * @return array
	 * n
	 */
	public function getForumSub($from = null, $fromId = null, $getHide = false){
		if($from <= 0 || $fromId <= 0) {
			return $this->setError(Yii::t('common','Parameter is empty'),array());
		}
		$baseModel = new BbsForumModel();
		$forumSubModel = new BbsForumSubModel();
		$forumInfo = $baseModel->getForumInfo($from,$fromId); //一个来源主版块是唯一
		if($forumInfo['status'] != 1) return array();
		if($forumInfo) {
			$forumId = $forumInfo['forum_id'];
			$forumSub =  $forumSubModel->getForumSub($forumId, $getHide);
			if($forumSub){
				return $this->arToArray($forumSub);
			}
		}
		return array();
	}
	
	/**
	 * 通过条件查询父子版块信息
	 * 
	 * @author supeng
	 * @param array $conditions
	 * @return mix|mixed
	 */
	public function getFormByConditions(Array $conditions = array()){
		if(!$conditions){
			return $this->setError(Yii::t('common','Parameter is empty'),array());
		}
		$bbsModel = new BbsForumModel();
		return $bbsModel->getForumByConditions($conditions);
	}
	
	/**
	 * 根据板块id获取板块详情
	 * @param int|array $sids
	 * @return array
	 */
	public function getForumByIds($sids){
		$sids = is_array($sids) ? $sids : array(intval($sids));
		$forums = BbsForumSubModel::model()->findAll('forum_sid in ('.implode(',', $sids).')');
		return $this->buildDataByIndex($this->arToArray($forums), 'forum_sid');
	}
	
	/**
	 * 保存主题
	 * @param array $data
	 * @return number
	 */
	private function saveThread(array $data){
		if(!isset($data['thread_id']) && intval($data['forum_sid']) <= 0 || isset($data['thread_id']) && intval($data['thread_id']) <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		}
		$model = new BbsThreadModel();
		if(isset($data['thread_id'])){
			$model = $model->findByPk($data['thread_id']);
			if(empty($model)) return $this->setError(Yii::t('common','Data not exists'), 0);
			if($model->posts >= $this->hot_post_count){
				$data['flag'] = $this->grantBit(intval($model->flag), 1);
			}
		}else{
			$data['is_del'] = 0;
			$data['create_time'] = $data['last_reply_time'] = time();
		}
		$this->attachAttribute($model, $data);
		if(!$model->validate()){
			return $this->setNotices($model->getErrors(), 0);
		}
		$model->save();
		return $model->getPrimaryKey();
	}
	
	/**
	 * 修改主题
	 * @author supeng
	 * @param array $thread
	 * @return boolean
	 */
	public function editThread(array $thread){
		if(!isset($thread['thread_id']) || $thread['thread_id'] <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		}
		if($this->saveThread($thread)){
			if ($this->isAdminAccessCtl()){
				$this->saveAdminOpLog('编辑 主题(thread_id='.$thread['thread_id'].')');
			}
			return true;
		}
		return false;
	}
	
	/**
	 * 发表主题
	 * @param $forumSid	子版块id
	 * @param $uid		发表人
	 * @param $title	主题
	 * @param $content	发表内容
	 * @return int		发表成功之后的threadId
	 */
	public function releaseThread($forumSid = 0, $title = '', $uid = 0, $content = '', $filter = true, $image=''){
		if($forumSid <=0 || $uid <= 0) {
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		if($filter){
			$wordService = new WordService();
			$title = $wordService->wordFilter($title);
			$content = $wordService->wordFilter(htmlspecialchars_decode($content), true);
		}else{
			$content = htmlspecialchars_decode($content);
		}
		$thread = array(
			'forum_sid'	=> $forumSid,
			'title'		=> $title,
			'uid'		=> $uid,
			'content'	=> strip_tags($content),
			'image'		=> $image
		);
		$threadId = $this->saveThread($thread);
		
		if($threadId){
			$post = array(
				'thread_id'	=> $threadId,
				'uid'		=> $uid,
				'content'	=> $content,
				'floor' 	=> 1,
			);
			$this->savePost($post);
			
			if ($this->isAdminAccessCtl()){
				$this->saveAdminOpLog('发表 主题(thread_id='.$threadId.')',$uid);
			}
			return $threadId;
		} 
		return 0;
	}
	
	/**
	 * 获取主题信息
	 * @param $threadId
	 * @return array
	 */
	public function getThreadInfo($threadId){
		if($threadId <= 0) {
			return $this->setError(Yii::t('common','Parameter is empty'),array());
		}
		$model = BbsThreadModel::model()->findByPk($threadId);
		if($model) {
			$thread = $model->attributes;
			$thread['flag_hot'] = 0;
			$thread['flag_image'] = 0;
			if($this->hasBit(intval($thread['flag']), 1)){
				$thread['flag_hot'] = 1;
			}
			if($this->hasBit(intval($thread['flag']), 2)){
				$thread['flag_image'] = 1;
			}
			return $thread;
		}
		return array();
	}
	
	/**
	 * 获取多个主题信息
	 * @param int|array $threadIds
	 * @return array
	 */
	public function getThreads($threadIds){
		$threadIds = is_array($threadIds) ? $threadIds : array(intval($threadIds));
		if(empty($threadIds)) return array();
		$threads = BbsThreadModel::model()->findAll('thread_id in('.implode(',', $threadIds).')');
		$threads = $this->arToArray($threads);
		foreach($threads as &$t){
			$t['flag_hot'] = 0;
			$t['flag_image'] = 0;
			if($this->hasBit(intval($t['flag']), 1)){
				$t['flag_hot'] = 1;
			}
			if($this->hasBit(intval($t['flag']), 2)){
				$t['flag_image'] = 1;
			}
		}
		return $this->buildDataByIndex($threads, 'thread_id');
	}
	
	/**
	 * 获取主题列表
	 * @param $fromSid	子版块id
	 * @param $page		当前页码
	 * @param $limit	每页显示数
	 * @return array	array()
	 */
	public function getThreadList($fromSid = null, $page = 1, $limit = 10){
		if($fromSid<=0) {
			return $this->setError(Yii::t('common','Parameter is empty'),array());
		}
		
		$page = intval($page) < 1 ? 1 : intval($page);
		$offset = ($page - 1 >= 0 ? $page -1 : $page) * $limit;
		$res = BbsThreadModel::model()->getThreadList($fromSid, $offset, $limit);
		if($res['list']) {
			foreach($res['list'] as $k=>$v){
				$res['list'][$k] = $v->attributes;
				$res['list'][$k]['flag_hot'] = 0;
				$res['list'][$k]['flag_image'] = 0;
				if($this->hasBit(intval($res['list'][$k]['flag']), 1)){
					$res['list'][$k]['flag_hot'] = 1;
				}
				if($this->hasBit(intval($res['list'][$k]['flag']), 2)){
					$res['list'][$k]['flag_image'] = 1;
				}
			}
		}
		return $res;
	}
	
	/**
	 * 删除主题
	 * @param int|array $threadIds		主题id
	 * @return boolean
	 */
	public function deleteThread($threadIds){
		$threadIds = is_array($threadIds) ? $threadIds : array(intval($threadIds));
		$res = BbsThreadModel::model()->deleteThreads($threadIds);
		if($res) {
			if ($this->isAdminAccessCtl()){
				$this->saveAdminOpLog('删除 主题(thread_id='.implode(',', $threadIds).')');
			}
			$this->deleteThreadPosts($threadIds);
			return true;
		}
		return false;
	}
	
	/**
	 * 置顶贴
	 * @param int|array $threadIds
	 * @return boolean
	 */
	public function topThreads($threadIds){
		if(!is_array($threadIds)) $threadIds = array(intval($threadIds));
		if(empty($threadIds)) return false;
		return BbsThreadModel::model()->topThreads($threadIds);
	}
	
	/**
	 * 保存贴子
	 * @param array $data
	 * @return number
	 */
	private function savePost(array $data){
		if(!isset($data['post_id']) && intval($data['thread_id']) <= 0 || isset($data['post_id']) && intval($data['post_id']) <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		}
		$model = new BbsPostModel();
		if(isset($data['post_id'])){
			$model = $model->findByPk($data['post_id']);
			if(empty($model)) return $this->setError(Yii::t('common','Data not exists'), 0);
		}else{
			$data['is_del'] = 0;
			$data['create_time'] = time();
		}
		if(isset($data['content'])){
			$data['content'] = $data['content'];
		}
		$this->attachAttribute($model, $data);
		if(!$model->validate()){
			return $this->setNotices($model->getErrors(), 0);
		}
		$model->save();
		return $model->getPrimaryKey();
	}
	
	/**
	 * 修改贴子
	 * 
	 * @author supeng
	 * @param array $post
	 */
	public function editPost(array $post, $filter = true){
		if(!isset($post['post_id']) || $post['post_id'] <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),fasle);
		}
		
		if($filter){
			$wordService = new WordService();
			$post['content'] = $wordService->wordFilter(htmlspecialchars_decode($post['content']), true);
		}else{
			$post['content'] = htmlspecialchars_decode($post['content']);
		}
		if(isset($post['floor']) && intval($post['floor']) == 1){
			$thread = array(
				'thread_id'	=> $post['thread_id'],
				'content'	=> strip_tags($post['content']),
			);
			$this->saveThread($thread);
		}
		
		if($this->savePost($post)){
			if ($this->isAdminAccessCtl()){
				$this->saveAdminOpLog('编辑 主题回复(post_id='.$post['post_id'].')');
			}
			return true;
		}
		return false;
	}
	
	/**
	 * 回复主题
	 * @param $uid			用户uid
	 * @param $threadId		主题id
	 * @param $content		回复的内容
	 * @param $replyPostId	引用的postId
	 * @return integer		操作成功,返回id, 失败返回0;
	 * n
	 */
	public function releasePost($uid, $threadId, $content, $replyPostId = 0){
		if($uid  <=0  || $threadId <=0 ){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$wordService = new WordService();
		$content = $wordService->wordFilter(htmlspecialchars_decode($content), true);
		$post = array(
			'thread_id'	=> $threadId,
			'uid'		=> $uid,
			'content'	=> $content,
			'reply_post_id'	=> $replyPostId,
			'floor'		=> BbsPostModel::model()->getFloor($threadId),
		);
		if($postId = $this->savePost($post)) {
			BbsThreadModel::model()->addPost($threadId);
			$thread = array(
				'thread_id'	=> $threadId,
				'last_reply_uid' =>	$uid,
				'last_reply_time'=> time()
			);
			$this->saveThread($thread);
			
			if ($this->isAdminAccessCtl()){
				$this->saveAdminOpLog('发布 主题回复(post_id='.$postId.')',$uid);
			}
			return $postId;
		}
		return 0;
	}
	
	/**
	 * 获取回复列表
	 * @param $hreadId
	 * @return mixed 成功返回数据数组, 失败返回boolean
	 */
	public function getPostList($threadId = null, $page = 1, $limit = 10)
	{
		if($threadId <= 0) {
			return $this->setError(Yii::t('common','Parameter is empty'), array());
		}
		$offset = ($page - 1 >= 0 ? $page -1 : $page) * $limit;
		$res = BbsPostModel::model()->getPostList($threadId, $offset, $limit);
		if($res) {
			return $this->arToArray($res);
		}
		return array();
	}
	
	/**
	 * 获取带分页回复列表
	 * @param $hreadId
	 * @return mixed 成功返回数据数组, 失败返回boolean
	 * n
	 */
	public function getPostListByPage($threadId = null, $page = 1, $limit = 10, $order = 'post_id asc')
	{
		if($threadId <= 0) {
			return $this->setError(Yii::t('common','Parameter is empty'), array());
		}
		$page = intval($page) < 1 ? 1 : intval($page);
		$offset = ($page - 1 >= 0 ? $page -1 : $page) * $limit;
		$res = BbsPostModel::model()->getPostList($threadId, $offset, $limit, true, $order);
		if(!empty($res['list'])) {
			$res['list'] = $this->arToArray($res['list']);
			return $res;
		}
		return array('count' => 0, 'list' => array(), 'pages' => null);
	}
	
	public function getPostByIds($postIds){
		$postIds = is_array($postIds) ? $postIds : array(intval($postIds));
		$posts = BbsPostModel::model()->getPostIds($postIds);
		if(!empty($posts)){
			$posts = $this->arToArray($posts);
			$uids = $this->buildDataByIndex($posts, 'uid');
			$users = UserJsonInfoService::getInstance()->getUserInfos($uids, false);
			foreach($posts as &$p){
				$p['reply_post_nickname'] = $users[$p['uid']]['nk'];
			}
			return $this->buildDataByIndex($posts, 'post_id');
		}else{
			return array();
		}
	}
	
	/**
	 * 获取主题帖
	 * @param int|array $threadIds
	 * @return array
	 */
	public function getThreadPost($threadIds){
		$threadIds = is_array($threadIds) ? $threadIds : array(intval($threadIds));
		if(empty($threadIds)) return array();
		$posts = BbsPostModel::model()->getThreadPost($threadIds);
		return $this->buildDataByIndex($posts, 'thread_id');
	}
	
	/**
	 * 删除回复
	 * @param int|array $postId	回复id
	 * @return boolean
	 * n
	 */
	public function deletePost($postIds){
		$uid = Yii::app()->user->id;
		$postIds = is_array($postIds) ? $postIds : array(intval($postIds));
		$res = BbsPostModel::model()->deletePosts($postIds, $uid);
		if($res) {
			$count = count($postIds);
			$pid = array_pop($postIds);
			$post = BbsPostModel::model()->findByPk($pid);
			if($post){
				BbsThreadModel::model()->subPost($post->thread_id, $count);
			}
			if ($this->isAdminAccessCtl()){
				$this->saveAdminOpLog('删除 主题回复(post_id='.$postId.')');
			}
			return true;
		}
		return false;
	}
	
	/**
	 * 删除贴子
	 * @param int|array $threadIds
	 * @return mix|Ambigous <boolean, number, unknown>
	 * n
	 */
	public function deleteThreadPosts($threadIds){
		$threadIds = is_array($threadIds) ? $threadIds : array(intval($threadIds));
		return BbsPostModel::model()->deleteThreadPosts($threadIds);
	}
	
	/**
	 * 保存赞、举报动作
	 * @param array $data
	 * @return mix|Ambigous <mixed, NULL, multitype:NULL >
	 */
	private function savePostAction(array $data){
		if(!isset($data['action_id']) && (intval($data['post_id']) <= 0 || intval($data['uid']) <= 0) || isset($data['action_id']) && intval($data['action_id']) <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		}
		$model = new BbsPostActionModel();
		if(isset($data['action_id'])){
			$model = $model->findByPk($data['action_id']);
			if(empty($model)) return $this->setError(Yii::t('common','Data not exists'), 0);
		}else{
			if(isset($data['praise']) && $data['praise'] == 1) $data['praise_time'] = time();
			if(isset($data['report']) && $data['report'] == 1) $data['report_time'] = time();
		}
		$this->attachAttribute($model, $data);
		if(!$model->validate()){
			return $this->setNotices($model->getErrors(), 0);
		}
		$model->save();
		return $model->getPrimaryKey();
	}

	/**
	 * 获取用户对该回复的动作
	 * @param $uid
	 * @param $postId
	 * @return array	返回一个包含赞动作和举报动作的数组array('praise'=>1,'report'=>1);1 表示有过动作, 0表示无动作
	 * n
	 */
	public function getPostAction($uid, $postId)
	{
		if($postId <= 0 || $uid <= 0) {
			return $this->setError(Yii::t('common','Parameter is empty'), array('praise'=>0,'report'=>0));
		}
		$return = BbsPostActionModel::model()->getPostAction($uid, $postId);
		return array('praise' => $return['praise'] ? 1 : 0, 'report' => $return['report'] ? 1 : 0);
	}
	
	/**
	 * 赞 / 举报
	 * @param $postId	操作对象
	 * @param $uid		操作人uid
	 * @param $action	动作类型 1为赞, 0为举报
	 * @return int		成功返回插入的id,失败返回0;
	 * n
	 */
	public function doPostAction($postId, $uid, $action = 1)
	{
		if($postId <= 0 ) {
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$data = BbsPostActionModel::model()->getPostAction($uid, $postId);
		if(empty($data)){
			$data = array(
				'post_id'=>$postId,
				'uid'=>$uid
			);
		}
		
		if($action == 1){
			if($data['praise'] == 1)
				return $this->setNotice(0, Yii::t('bbs','you have praised it'), 0);
			$data['praise'] = 1;
		}else{
			if($data['report'] == 1)
				return $this->setNotice(0, Yii::t('bbs','you have reported it'), 0);
			$data['report'] = 1;
		}
		return $this->savePostAction($data);
	}
	
	/**
	 * 取消赞 / 举报
	 * @param $postId	操作对象
	 * @param $uid		操作人uid
	 * @param $action	动作类型 1为赞, 0为举报
	 * @return boolean	
	 * n
	 */
	public function deletePostAction($postId, $uid, $action = 1)
	{
		if($actionId <= 0) {
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		}
		$data = BbsPostActionModel::model()->getPostAction($uid, $postId);
		if($action == 1){
			$data['praise'] = 0;
		}else{
			$data['report'] = 0;
		}
		if($data['praise'] == 0 && $data['report'] == 0){
			return BbsPostActionModel::model()->deletePostAction($data['action_id']);
		}else{
			$this->savePostAction($data);
			return true;
		}
	}
	
	public function getAllCmsSubForum($forumName = ''){
		$allCms = array(
			OPERATORS_CMS_NEWSNOTICE_FORUMNAME => '新闻公告',
			OPERATORS_CMS_DOTEYPOLICY_FORUMNAME => '主播政策',
			OPERATORS_CMS_USERHELP_FORUMNAME => array(
					'新手上路' => '新手上路',
					'充值帮助' => '充值帮助',
					'常见问题' => '常见问题',
					'入驻平台' => '入驻平台',
				),
			OPERATORS_CMS_DOTEYHELP_FORUMNAME => array(
					'功能介绍' => '功能介绍',
					'点歌系统介绍' => '点歌系统介绍',
				),
			OPERATORS_CMS_ABOUTUS_FORUMNAME => array(
					'公司介绍' => '公司介绍',
					'市场合作' => '市场合作',
					'加入我们' => '加入我们',
					'联系我们' => '联系我们'
				)
			);
		
		return isset($allCms[$forumName])?$allCms[$forumName]:$allCms;
	}
}
?>