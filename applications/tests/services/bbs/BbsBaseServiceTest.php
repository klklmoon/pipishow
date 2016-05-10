<?php
/**
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: guoshaobo $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: BbsBaseServiceTest.php 9036 2013-04-23 13:02:04Z guoshaobo $ 
 * @package
 */
class BbsBaseServiceTest extends BaseTest {
	
	protected $bbsService;
	protected static $bbs_id;
	protected static $fromId;
	protected static $from;
	protected static $forumSid;
	protected static $threadId;
	protected static $thread_title;
	protected static $post_id;
	protected static $action_id;
	
	public function __construct(){
		$this->getToUser();
		$this->getNewUser();
		$this->bbsService = new BbsbaseService();
	}
	
	public function testCreateForum()
	{
		$uid = self::$uid;
		$data = array();
		$fromId = $uid;
		$from = rand(1,3);
		$data['from'] = $from;
		$data['fromId'] = $fromId;
		$data['name'] = 'bbs_' . uniqid();
		$bbs_id = $this->bbsService->createForum($uid, $data);
		$this->assertTrue($bbs_id > 0,'创建bbs失败');
		
		self::$bbs_id = $bbs_id;
		self::$fromId = $fromId;
		self::$from = $from;
		$forum = $this->bbsService->getforum($bbs_id);
		$this->assertTrue(!empty($forum) && $forum['forum_id'] == $bbs_id && $forum['from'] == $from && $forum['from_id'] == $fromId && $forum['ower_uid'] == $uid,'创建bbs后获取信息比对失败');
	}
	
	public function testEditForum()
	{
		$forum = array();
		$forum['forum_id'] = self::$bbs_id;
		$forum['name'] = 'bbs_name_' . $forum['forum_id'];
		$res = $this->bbsService->editForum($forum);
		$this->assertTrue($res && $res['name'] == $forum['name'],'修改bbs失败');
	}
	
	public function testGetForumSub()
	{
		$from = self::$from;
		$fromId = self::$fromId;
		$res = $this->bbsService->getForumSub($from, $fromId);
		$this->assertTrue(is_array($res),'获取子版块失败');
		self::$forumSid = $res[0]['forum_sid'];
	}
	
	public function testCreateForumSub()
	{
		$forumId = self::$bbs_id;
		$name = '子版块_'.$forumId;
		$res = $this->bbsService->createForumSub($forumId, $name);
		$this->assertTrue($res > 0,'创建子版块失败');
	}
	
	public function testReleaseThread()
	{
		$forumSid = self::$forumSid;
		self::$thread_title = $title = 'test_title_' . uniqid();
		$uid = self::$uid;
		$content = 'test_content_' . uniqid();
		$res = $this->bbsService->releaseThread($forumSid, $title, $uid, $content);
		$this->assertTrue($res >= 0,'发表新主题失败');
		self::$threadId = $res;
	}
	
	public function testGetThreadList()
	{
		$fromSid = self::$forumSid;
		$res = $this->bbsService->getThreadList($fromSid);
		$this->assertTrue(is_array($res),'获取主题列表失败');
	}
	
	public function testEditThread()
	{
		$data = array();
		$data['thread_id'] = self::$threadId;
		$data['title'] = 'edit_title_'.self::$threadId;
		$res = $this->bbsService->editThread($data);
		$this->assertTrue($res,'编辑主题失败');
	}
	
	public function testDeleteThread()
	{
		$res = $this->bbsService->deleteThread(38);
		$this->assertTrue(is_bool($res),'删除主题失败');
	}
	
	public function testReleasePost()
	{
		$uid = self::$uid;
		$threadId = self::$threadId;
		$content = '我是一个评论';
		$res = $this->bbsService->releasePost($uid, $threadId, $content);
		$this->assertTrue($res >= 0,'发表回复失败');
		self::$post_id = $res;
	}
	
	public function testGetPostList()
	{
		$threadId = self::$threadId;
		$page = 1;
		$limit = 10;
		$res = $this->bbsService->getPostList($threadId, $page, $limit);
		$this->assertTrue(is_array($res),'获取回复列表失败');
	}
	
	public function testDeletePost()
	{
		$res = $this->bbsService->deletePost(26);
		$this->assertTrue(is_bool($res),'删除回复失败');
	}
	
	public function testGetPostAction()
	{
		$uid = self::$uid;
		$postId = self::$post_id;
		$res = $this->bbsService->getPostAction($uid,$postId);
		$this->assertTrue(is_array($res) && isset($res['praise']) && isset($res['report']),'获取回复内容失败');
	}
	
	public function testDoPostAction()
	{
		$postId = self::$post_id;
		$uid = self::$uid;
		$action = rand(0,1);
		$res = $this->bbsService->doPostAction($postId,$uid,$action);
		$this->assertTrue($res > 0, '回复互动失败');
		self::$action_id = $res;
	}
	
	public function testDeletePostAction()
	{
		$action_id = self::$action_id;
		$res = $this->bbsService->deletePostAction($action_id);
		$this->assertTrue($res,'删除互动失败');
	}
}

