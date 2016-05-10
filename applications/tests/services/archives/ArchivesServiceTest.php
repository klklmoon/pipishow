<?php
/**
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: ArchivesServiceTest.php 9525 2013-05-03 08:32:57Z hexin $ 
 * @package
 */
class ArchivesServiceTest extends BaseTest {

	protected $archives;
	protected static $archives_id;
	protected static $cat_id;
	protected static $attr_id;
	protected static $live_id;
	protected static $chat_id;
	protected static $live_record_id;
	
	public function __construct(){
		$this->getNewDotey();
		$this->archives = new ArchivesService();
	}
	
	/**
	 * @medium
	 */
	public function testSaveArchivesCat()
	{
		$name = 'c_' .uniqid();
		$category = array();
		$category['name'] = $name;
		$category['en_name'] = 'en_' .$name;
		$res = $this->archives->saveArchivesCat($category);
		$this->assertTrue($res >= 0,'创建档期分类信息失败');
	
		$category['cat_id'] = $res;
		$category['en_name'] .= '_t';
		$res = $this->archives->saveArchivesCat($category);
		$cat = ArchivesCategoryModel::model()->findByPk($res);
		$this->assertTrue($cat['name'] == $category['name'], '修改档期分类信息失败');
		self::$cat_id = $res;
	}
	
	public function testSaveArchivesAttribute()
	{
		$name = 'a_'.uniqid();
		$attributes = array();
		$attributes['cat_id'] = self::$cat_id;
		$attributes['name'] = $name;
		$attributes['value'] = 'v_' . $name;
		$res = $this->archives->saveArchivesAttribute($attributes);
		$this->assertTrue($res>=0,'创建档期属性信息失败');
	
		$attributes['attribute_id'] = $res;
		$attributes['value'] = 'e_v_' . $name;
		$res = $this->archives->saveArchivesAttribute($attributes);
		$attr = $this->archives->getAttributeByCatIds(self::$cat_id);
		$attr = array_pop($attr);
		$this->assertTrue($attributes['name'] == $attr['name'], '更新档期属性信息失败');
		self::$attr_id = $res;
	}
	
	public function testSaveLiveServer(){
		$live = array(
			'import_host'	=> '127.0.0.1',
			'export_host'	=> '127.0.0.1',
		);
		$r = $this->archives->saveLiveServer($live);
		$this->assertTrue($r > 0, '添加视频服务器地址信息测试不通过');
	
		$live['server_id'] = $r;
		$live['use_num'] = 1;
		$r = $this->archives->saveLiveServer($live);
		$tmp = $this->archives->getLiveServerByServerIds($r);
		$tmp = array_pop($tmp);
		$this->assertTrue($live['use_num'] == $tmp['use_num'], '修改视频服务器地址信息测试不通过');
		self::$live_id = $r;
	}
	
	public function testSaveGlobalServer(){
		$chat = array(
			'domain' => '127.0.0.1',
		);
		$r = $this->archives->saveGlobalServer($chat);
		$this->assertTrue($r > 0, '添加聊天服务器地址信息测试不通过');
		
		$chat['global_server_id'] = $r;
		$chat['use_num'] = 1;
		$r = $this->archives->saveGlobalServer($chat);
		$tmp = $this->archives->getGlobalServerByServerIds($r);
		$tmp = array_pop($tmp);
		$this->assertTrue($chat['use_num'] == $tmp['use_num'], '修改聊天服务器地址信息测试不通过');
		self::$chat_id = $r;
	}
	
	/**
	 * @medium
	 */
	public function testCreateArchives()
	{
		$liveServer = $this->archives->getLiveServerByServerIds(self::$live_id);
		$liveServer = array_pop($liveServer);
		$tmp = $this->archives->getMinUserLiveServer();
		$this->assertSame($liveServer, $tmp, '使用最少的liveServer测试数据不正确');
		
		$chatServer = $this->archives->getGlobalServerByServerIds(self::$chat_id);
		$chatServer = array_pop($chatServer);
		$tmp = $this->archives->getMinUserGlobalServer();
		$this->assertSame($chatServer, $tmp, '使用最少的chatServer测试数据不正确');
		
		$archives = array();
		$archives['uid'] = self::$dotey_uid;
		$archives['title'] = 'title_' . uniqid();
		$archives['cat_id'] = self::$cat_id;
		$archives['notice'] = 'this is a test_notice';
		$archive_id = $this->archives->createArchives($archives);
		if($this->archives->getError()){
			$this->fail(var_export($this->archives->getError(), true));
		}
		if($this->archives->getNotice()){
			$this->fail(var_export($this->archives->getNotice(), true));
		}
		$archive = $this->archives->getArchivesByArchivesId($archive_id);
		$this->assertTrue($archive_id >= 0 && $archives['title'] == $archive['title'],'创建直播间测试不通过');
		
		$user = ArchivesUserModel::model()->findAllByAttributes(array('archives_id' => $archive_id));
		$this->assertTrue(count($user) == 1 && $user[0]->uid == self::$dotey_uid, '存储档期用户信息测试不通过');
		
		$archive_live = ArchivesLiveServerModel::model()->findByAttributes(array('archives_id' => $archive_id));
		$this->assertTrue($archive_live && $archive_live->server_id == self::$live_id, '存储档期跟视频服务器的关系测试不通过');
		
		$tmp = $this->archives->getLiveServerByServerIds(self::$live_id);
		$tmp = array_pop($tmp);
		$this->assertTrue($tmp['use_num'] == $liveServer['use_num'] + 1, 'liveServer使用量加1测试不通过');
		
		$archive_chat = $this->archives->getChatServerByArchivesId($archive_id);
		$this->assertTrue(!empty($archive_chat) && $archive_chat['domain'] == $chatServer['domain'], '存储聊天进程信息测试不通过');
		
		$tmp = $this->archives->getGlobalServerByServerIds(self::$chat_id);
		$tmp = array_pop($tmp);
		$this->assertTrue($tmp['use_num'] == $chatServer['use_num'] + 1, 'chatServer使用量加1测试不通过');
		
		self::$archives_id = $archive_id;
	}
	
	public function testSaveArchivesLiveServer(){
		//修改测试
		$archive_live = ArchivesLiveServerModel::model()->findByAttributes(array('archives_id' => self::$archives_id))->getAttributes();
		$live_id = $archive_live['server_id'];
		$archive_live['server_id'] = 0;
		$id = $this->archives->saveArchivesLiveServer($archive_live);
		$tmp = ArchivesLiveServerModel::model()->findByPk($id);
		$this->assertTrue($tmp->server_id == $archive_live['server_id'], '修改档期跟视频服务器的关系测试不通过');
	
		$archive_live['server_id'] = $live_id;
		$this->archives->saveArchivesLiveServer($archive_live);
	}
	
	public function testSaveChatServer(){
		//修改测试
		$chat = $this->archives->getChatServerByArchivesId(self::$archives_id);
		$domain = $chat['domain'];
		$chat['domain'] = 'test';
		$this->archives->saveChatServer($chat);
		$tmp = $this->archives->getChatServerByArchivesId(self::$archives_id);
		$this->assertTrue($tmp['domain'] == $chat['domain'], '修改聊天进程信息测试不通过');
	
		$chat['domain'] = $domain;
		$this->archives->saveChatServer($chat);
	}
	
	public function testSaveArchivesUser(){
		$this->getNewUser();
		$archiveUser = array(
			'uid'	=> self::$uid,
			'archives_id' => self::$archives_id
		);
		$r = $this->archives->saveArchivesUser($archiveUser);
		$this->assertTrue($r > 0, '添加单个档期用户信息测试不通过');
	
		$archiveUser['id'] = $r;
		$archiveUser['uid'] = 0;
		$r = $this->archives->saveArchivesUser($archiveUser);
		$user = ArchivesUserModel::model()->findByPk($r);
		$this->assertTrue($archiveUser['uid'] == $user->uid, '修改单个档期用户信息测试不通过');
	}
	
	public function testModifyArchivesNotice(){
		$notice = array(
			'archives_id'	=> self::$archives_id,
			'private_notice'=> 'test',
		);
		$this->archives->modifyArchivesNotice(self::$archives_id, $notice);
		if($this->archives->getError()){
			$this->fail(var_export($this->archives->getError(), true));
		}
		if($this->archives->getNotice()){
			$this->fail(var_export($this->archives->getNotice(), true));
		}
		$tmp1 = $this->archives->getArchivesByArchivesId(self::$archives_id); //从redis里读取
		$tmp2 = ArchivesModel::model()->findByPk(self::$archives_id)->getAttributes(); //从数据库里读取
		//$this->assertTrue($tmp1['private_notice'] == $tmp2['private_notice'], '修改直播公告数据库与redis数据不一致');
		//$this->assertTrue($notice['private_notice'] == $tmp2['private_notice'], '修改直播公告测试不通过');
	}
	
	/**
	 * @medium
	 */
	public function testModifyArchives(){
		$record = $this->archives->getLiveRecordByArchivesId(self::$archives_id);
		$this->assertTrue(empty($record) == true, '直播记录测试数据不为空');
		
		//直播待开始
		$this->archives->modifyArchives(self::$dotey_uid, self::$archives_id, strtotime('+1 hours'), strtotime('+2 hours'));
		$record = $this->archives->getLiveRecordByArchivesId(self::$archives_id);
		$archive = $this->archives->getArchivesByArchivesId(self::$archives_id);
		$this->assertTrue($record && $record['status'] == 0 && $archive['live_record']['status'] == 0, '修改档期状态为待开始测试不通过');
		
		//开始直播
		$this->archives->modifyArchives(self::$dotey_uid, self::$archives_id);
		$record = $this->archives->getLiveRecordByRecordIds($record['record_id']);
		$record = array_pop($record);
		$archive = $this->archives->getArchivesByArchivesId(self::$archives_id);
		$this->assertTrue($record && $record['status'] == 1 && $archive['live_record']['status'] == 1, '修改档期状态为开始直播测试不通过');
		
		//结束直播间
		$this->archives->modifyArchives(self::$dotey_uid, self::$archives_id);
		$record = $this->archives->getLiveRecordByRecordIds($record['record_id']);
		$record = array_pop($record);
		$archive = $this->archives->getArchivesByArchivesId(self::$archives_id);
		$this->assertTrue($record && $record['status'] == 2 && $archive['live_record']['status'] == 2, '修改档期状态为结束直播测试不通过');
		
		self::$live_record_id == $record['record_id'];
	}
	
	/**
	 * @medium
	 */
	public function testAddManage(){
		$this->getNewUser();
		$this->init_rank();
		$consumeServer = new ConsumeService();
		$consumeServer->saveUserConsumeAttribute(array('uid' => self::$uid, 'dedication' => 200, 'rank' => 1));
		$consumeServer->saveUserConsumeAttribute(array('uid' => self::$dotey_uid, 'charm' => 0, 'dotey_rank' => 0));

		$this->archives->addManage(self::$uid, self::$dotey_uid, self::$archives_id);
		$manage = $this->archives->getPurviewLiveByCondition(array('uid' => self::$uid, 'archives_id' => self::$archives_id));
		$this->assertTrue(!empty($manage), '添加直播间房管测试不通过');
	}
	
	public function testSavePurviewLive(){
		//修改测试
		$manage = $this->archives->getPurviewLiveByCondition(array('uid' => self::$uid, 'archives_id' => self::$archives_id));
		$manage = array_pop($manage);
		$manage['uid'] = 0;
		$this->archives->savePurviewLive($manage);
		$tmp = $this->archives->getPurviewLiveByCondition(array('uid' => $manage['uid'], 'archives_id' => self::$archives_id));
		$this->assertTrue(!empty($tmp), '修改房管信息测试不通过');
		
		$manage['uid'] = self::$uid;
		$this->archives->savePurviewLive($manage);
	}
	
	public function testPurviewLive(){
		$user_manage = $this->archives->getPurviewLiveByUids(self::$uid);
		$user_manage = $user_manage[self::$uid];
		$archives_manage = $this->archives->getPurviewLiveByArchivesIds(self::$archives_id);
		$archives_manage = $archives_manage[self::$archives_id];
		$this->assertTrue(count($user_manage) == count($archives_manage) && $user_manage[0] == self::$archives_id && $archives_manage[0] == self::$uid, '根据用户uid获取所属的房管的档期Id和根据档期Id获取所属房管的用户uid测试不通过');
	}
	
	public function testPurviewLiveCount(){
		$user_count = $this->archives->getPurviewLiveCountByUids(self::$uid);
		$archives_count = $this->archives->getPurviewLiveCountByArchivesId(self::$archives_id);
		$this->assertTrue($user_count == $archives_count, '获取用户拥有的房管数和获取档期拥有的房管数测试不通过');
	}
	
	public function testRemoveManage(){
		$this->archives->removeManage(self::$uid, self::$archives_id);
		$manage = $this->archives->getPurviewLiveByCondition(array('uid' => self::$uid, 'archives_id' => self::$archives_id));
		$this->assertTrue(empty($manage), '解除直播间房管测试不通过');
	}
	
	public function testGetArchivesByUids(){
		$archives = $this->archives->getArchivesByUids(array(0,self::$dotey_uid), true);
		$archive = array_pop($archives);
		//$this->assertTrue($archive['archives_id'] == self::$archives_id && $archive['live_record']['record_id'] == self::$live_record_id, '根据档期创建者获取档期信息和最近的直播记录测试不通过');
	}
	
	public function testGetChatServerByArchivesIds(){
		$chat = $this->archives->getChatServerByArchivesIds(self::$archives_id);
		$chat = array_pop(array_pop($chat));
		$chatServer = $this->archives->getGlobalServerByServerIds(self::$chat_id);
		$chatServer = array_pop($chatServer);
		$this->assertTrue($chat['domain'] == $chatServer['domain'], '根据档期Id获取聊天进程信息测试不通过');
	}
	
	public function testGetLiveServer(){
		$liveServer = $this->archives->getLiveServer();
		$liveServer = array_pop($liveServer);
		$this->assertTrue($liveServer['server_id'] == self::$live_id, '获取所有视频服务器地址测试不通过');
	}
	
	public function testGetRecommondLiveArchives(){
		//@todo 服务层暂时没写完
	}
	
	public function testGetWillLiveArchives(){
		//直播待开始
		$this->archives->modifyArchives(self::$dotey_uid, self::$archives_id, strtotime('+1 hours'), strtotime('+2 hours'));
		$archives = $this->archives->getWillLiveArchives(self::$uid);
		$archives = array_pop($archives);
		$this->assertTrue($archives['archives_id'] == self::$archives_id, '取得待直播的档期测试不通过');
	}
	
	public function testGetLivingArchives(){
		//开始直播
		$this->archives->modifyArchives(self::$dotey_uid, self::$archives_id);
		$archives = $this->archives->getLivingArchives(self::$uid);
		$archives = array_shift($archives);
		$this->assertTrue($archives['archives_id'] == self::$archives_id, '获取正在直播的档期测试不通过');
	}
	
	public function testGetUserManagerArchives(){
		$this->archives->addManage(self::$uid, self::$dotey_uid, self::$archives_id);
		
		$archives = $this->archives->getUserManagerArchives(self::$uid);
		$archives = array_shift($archives['living']);
		$this->assertTrue($archives['archives_id'] == self::$archives_id, '取得用户管理的直播的档期测试不通过');
	}
	
	public function testGetUserAttentionArchives(){
		$weiboServer = new WeiboService();
		$weiboServer->attentionDotey(self::$dotey_uid, self::$uid);
		
		$archives = $this->archives->getUserAttentionArchives(self::$uid);
		$archives = array_shift($archives);
		//$this->assertTrue($archives['archives_id'] == self::$archives_id, '取得用户关注的主播的档期测试不通过');
	}
	
	public function testGetUserLatestSeeArchives(){
		$view = array(
			'uid'			=> self::$uid,
			'archives_id'	=> self::$archives_id,
			'archives_record_id' => self::$live_record_id,
			'view_time'		=> time(),
		);
		$model = new UserArchivesViewModel();
		$this->archives->attachAttribute($model, $view);
		$model->save();
		
		$archives = $this->archives->getUserLatestSeeArchives(self::$uid);
		$archives = array_shift($archives['living']);
		$this->assertTrue($archives['archives_id'] == self::$archives_id, '取得用户最近观看的直播的档期测试不通过');
	}
	
	public function testGetLatestRegisterDoteyArchives(){
		$archives = $this->archives->getLatestRegisterDoteyArchives(self::$uid, 1);
		$archives = array_shift($archives);
		//$this->assertTrue($archives['archives_id'] == self::$archives_id, '取得最近注册主播的直播的档期测试不通过');
	}
}

