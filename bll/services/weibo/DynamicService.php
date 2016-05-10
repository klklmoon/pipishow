<?php
/**
 * 动态的临时服务层，借用bbs服务层，没有底层的数据表
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author hexin
 * @version $Id: DynamicService.php 17138 2014-01-07 08:52:38Z hexin $ 
 * @package service
 */
define('DYNAMIC_SOURCE_DOTEY', 'dotey');
define('DYNAMIC_SOURCE_ALBUM', 'album');
define('DYNAMIC_SOURCE_SUPER', 'super');
define('DYNAMIC_SOURCE_UPGRADE', 'upgrade');
class DynamicService extends PipiService {
	private static $instance;
	/**
	 * @var BbsbaseService $bbs
	 */
	private $bbs;
	
	/**
	 * 返回DynamicService对象的单例
	 * @return DynamicService
	 */
	public static function getInstance(){
		if(self::$instance == null){
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	public function __construct(PipiController $pipiController = null){
		parent::__construct($pipiController);
		$this->bbs = BbsbaseService::getInstance();
	}
	
	/**
	 * 获取动态分类
	 * @param string $type
	 * @return string
	 */
	public function getDynamicType($type = ''){
		$dType = array(
			DYNAMIC_SOURCE_DOTEY	=> '主播动态',
			DYNAMIC_SOURCE_ALBUM => '相册动态',
			DYNAMIC_SOURCE_SUPER	=> '超礼动态',
			DYNAMIC_SOURCE_UPGRADE => '升级动态',
		);
		if(empty($type)) return $dType;
		elseif(isset($dType[$type])) return $dType[$type];
		else return '';
	}
	
	/**
	 * 定位到具体动态分类的bbs板块id
	 * @param int $uid
	 * @param string $type
	 * @return number
	 */
	private function getForumSid($uid, $type){
		$subForums = $this->getDynamicType();
		if(!isset($subForums[$type]))
			return $this->setError('该动态类型未注册', 0);
		$forum = $this->bbs->getForumSub(FORUM_FROM_TYPE_PERSONAL, $uid);
		if(empty($forum)){
			$this->bbs->createForum($uid, $subForums, FORUM_FROM_TYPE_PERSONAL, $uid);
			$forum = $this->bbs->getForumSub(FORUM_FROM_TYPE_PERSONAL, $uid);
		}
		$forumId = $forum[0]['forum_id'];
		$forum = $this->buildDataByIndex($forum, 'name');
		if(!isset($forum[$subForums[$type]])){
			$forumSid = $this->bbs->createSubForum($forumId, $subForums[$type]);
		}else{
			$forumSid = $forum[$subForums[$type]]['forum_sid'];
		}
		return $forumSid;
	}
	
	/**
	 * 所有动态分类对应到bbs里的板块id
	 * @param int $uid
	 * @return array
	 */
	private function getForumSids($uid){
		$forum = $this->getForums($uid);
		return array_keys($this->buildDataByIndex($forum, 'forum_sid'));
	}
	
	/**
	 * 所有动态分类对应到bbs里的板块详情
	 * @param int $uid
	 * @return array
	 */
	private function getForums($uid){
		$forum = $this->bbs->getForumSub(FORUM_FROM_TYPE_PERSONAL, $uid);
		if(empty($forum)){
			$subForums = $this->getDynamicType();
			$this->bbs->createForum($uid, $subForums, FORUM_FROM_TYPE_PERSONAL, $uid);
			$forum = $this->bbs->getForumSub(FORUM_FROM_TYPE_PERSONAL, $uid);
		}
		return $this->buildDataByIndex($forum, 'forum_sid');
	}
	
	/**
	 * 发动态
	 * @param int $uid
	 * @param string $content
	 * @param string $type 分类, DYNAMIC_SOURCE_DOTEY主播动态, DYNAMIC_SOURCE_ALBUM相册动态, DYNAMIC_SOURCE_SUPER超礼动态, DYNAMIC_SOURCE_UPGRADE升级动态
	 * @param string $image
	 * @param string $title
	 * @return number
	 */
	public function dynamic($uid, $content = '', $type = DYNAMIC_SOURCE_DOTEY, $image = '', $title = ''){
		$doteyService = new DoteyService();
		$dotey = $doteyService->getDoteysInUids(array($uid));
		if(empty($dotey)) return $this->setNotice(0, '只有主播才能发动态', 0);
		$forumId = $this->getForumSid($uid, $type);
		if($forumId > 0){
			if($thread_id = $this->bbs->releaseThread($forumId, $title, $uid, $content, true, $image)){
				return $thread_id;
			}
		}
		return $this->setNotice(1, Yii::t('common','System error'), 0);
	}
	
	/**
	 * 删动态
	 * @param int $uid
	 * @param int $ids
	 * @return boolean
	 */
	public function deleteDynamic($uid, $ids){
		$ids = is_array($ids) ? $ids : array(intval($ids));
		$doteyService = new DoteyService();
		$dotey = $doteyService->getDoteysInUids(array($uid));
		if(empty($dotey)) return $this->setNotice(0, '只有主播才能删动态', 0);
		
		//过滤不是该用户的动态内容
		$threads = $this->bbs->getThreads($ids);
		$tids = array();
		foreach($threads as $t){
			if($t['uid'] == $uid)
				$tids[] = $t['thread_id'];
		}
		return $this->bbs->deleteThread($tids);
	}
	
	/**
	 * 获取动态
	 *
	 * @param int $uid 用户ＩＤ
	 * @param string $type 类型 空为取所有类型
	 * @param int $limit 条数
	 * @param int $page 分页数，-1为不分页
	 * @return array = array(count => 1, list => array(...), pages => object) | array() 不分页直接返回list
	 */
	public function getDynamicList($uid, $type = '', $limit = -1, $page = -1){
		$source = $this->getDynamicType();
		if(empty($type)){
			$forums = $this->getForums($uid);
			$forumSid = array_keys($this->buildDataByIndex($forums, 'forum_sid'));
		}else{
			$forumSid = $this->getForumSid($uid, $type);
			$forums[$forumSid] = $source[$type];
		}
		if($page == -1 && $limit = -1){
			$limit = 10000;
		}
		$return = $this->bbs->getThreadList($forumSid, $page, $limit);
		$ids = array_keys($this->buildDataByIndex($return['list'], 'thread_id'));
		$counts = CommentService::getInstance()->getCountByDynamic($ids, $type);
		foreach($return['list'] as &$list){
			$list['source'] = $forums[$list['forum_sid']]['name'];
			$key = array_search($list['source'], $source);
			if($key && isset($counts[$key.'_'.$list['thread_id']])) $list['comments'] = intval($counts[$key.'_'.$list['thread_id']]);
			else $list['comments'] = 0;
			if(!empty($list['image'])){
				$list['thumb'] = AlbumService::getInstance()->getImageUrl($list['uid'], $list['image'], 'thumb');
				$list['image'] = AlbumService::getInstance()->getImageUrl($list['uid'], $list['image']);
			}else{
				$list['thumb'] = '';
				$list['image'] = '';
			}
			unset($list['forum_sid']);
		}
		if($page == -1){
			return $return['list'];
		}
		return $return;
	}
	
	/**
	 * 返回具体的动态详情
	 * @param int|array $ids
	 * @return array
	 */
	public function getDynamic($ids){
		$ids = is_array($ids) ? $ids : array(intval($ids));
		$return = $this->bbs->getThreads($ids);
		if(empty($return)) return array();
		
		$fids = array_keys($this->buildDataByIndex($return, 'forum_sid'));
		$forums = $this->bbs->getForumByIds($fids);
		$ids = array_keys($this->buildDataByIndex($return, 'thread_id'));
		$counts = CommentService::getInstance()->getCountByDynamic($ids, '');
		$source = $this->getDynamicType();
		foreach($return as &$list){
			$list['source'] = $forums[$list['forum_sid']]['name'];
			$key = array_search($list['source'], $source);
			if($key && isset($counts[$key.'_'.$list['thread_id']])) $list['comments'] = intval($counts[$key.'_'.$list['thread_id']]);
			else $list['comments'] = 0;
			unset($list['forum_sid']);
			if(!empty($list['image'])){
				$list['thumb'] = AlbumService::getInstance()->getImageUrl($list['uid'], $list['image'], 'thumb');
				$list['image'] = AlbumService::getInstance()->getImageUrl($list['uid'], $list['image']);
			}else{
				$list['thumb'] = '';
				$list['image'] = '';
			}
		}
		return $return;
	}
	
	/**
	 * 动态设置
	 * @param int $uid
	 * @param array $config array('post_rank' => -1) -1不允许评论，0允许
	 * @return boolean
	 */
	public function setConfig($uid, $type, array $config){
		$doteyService = new DoteyService();
		$dotey = $doteyService->getDoteysInUids(array($uid));
		if(empty($dotey)) return $this->setNotice(0, '只有主播才能删动态', false);
		
		$forumSid = $this->getForumSid($uid, $type);
		if($forumSid > 0){
			return $this->bbs->updateSubForum($forumSid, $config);
		}
		return $this->setNotice(1, Yii::t('common','System error'), 0);
	}
	
	/**
	 * 取得动态配置
	 * @param int $uid
	 * @param string $type
	 * @return array
	 */
	public function getConfig($uid, $type){
		$forums = $this->bbs->getForumSub(FORUM_FROM_TYPE_PERSONAL, $uid);
		$forums = $this->buildDataByIndex($forums, 'name');
		$subForums = $this->getDynamicType();
		$forum = $forums[$subForums[$type]];
		$return = array(
			'visit_rank' =>	$forum['visit_rank'],	//访问限制
			'post_rank'	 => $forum['post_rank'],	//评论限制
			'is_check'	 => $forum['is_check'],		//审核限制
		);
		return $return;
	}
	
	
}
