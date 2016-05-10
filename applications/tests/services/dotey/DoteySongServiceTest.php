<?php
/**
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: guoshaobo $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: DoteySongServiceTest.php 9045 2013-04-23 14:17:14Z guoshaobo $ 
 * @package
 */
class DoteySongServiceTest extends BaseTest {
	
	protected $song;
	protected static $dotey_id;
	protected static $song_id;
	
	public function __construct(){
		$this->getNewUser();
		$this->song = new DoteySongService();
	}
	

	/**
	 * @test
	 */
	public function getDoteySongByDoteyId()
	{
		$doteyId = self::$uid;
		$res = $this->song->getDoteySongByDoteyId($doteyId);
		$this->assertTrue(is_array($res),'获取主播的歌单失败');
	}
	
	/**
	 * @test
	 */
	public function getDoteySongByDoteyIdLimit()
	{
		$doteyId = self::$uid;
		$res = $this->song->getDoteySongByDoteyIdLimit($doteyId);
		$this->assertTrue(is_array($res),'获取主播的歌单');
	}
	
	/**
	 * @test
	 */
	public function saveDoteySong()
	{
		$song = array();
		$song['dotey_id'] = self::$uid;
		$res = $this->song->saveDoteySong($song);
		$this->assertTrue($res >= 0,'存储主播歌曲信息');
		
		self::$song_id = $song['song_id'] = $res;
		$res = $this->song->saveDoteySong($song);
		$this->assertTrue($res >= 0,'更新主播歌曲信息');
	}
	
	/**
	 * @test
	 */
	public function delDoteySongBySongId()
	{
		$songIds = array(self::$song_id);
		$res = $this->song->delDoteySongBySongId($songIds);
		$this->assertTrue($res >= 0,'删除主播歌单信息');
	}
	
	/**
	 * @test
	 */
	public function getUserSongRecordsByDoteyId()
	{
		$doteyId = self::$uid;
		$res = $this->song->getUserSongRecordsByDoteyId($doteyId);
		$this->assertTrue(is_array($res),'根据条件获取主播的点歌记录');
	}
	
	/**
	 * @test
	 */
	public function getUserRecordsByUid()
	{
		$uid = self::$uid;
		$res = $this->song->getUserRecordsByUid($uid);
		$this->assertTrue(is_array($res),'根据条件获取用户的点歌记录');
	}
	
	/**
	 * @test
	 */
	public function saveUserSongRecords()
	{
		$records = array();
		$records['to_uid'] = self::$uid;
		$records['uid'] = self::$uid;
		$records['target_id'] = 3;
		$res = $this->song->saveUserSongRecords($records);
		$this->assertTrue($res >= 0,'存储用户点歌记录');
		
		$records['record_id'] = $res;
		$records['target_id'] = 2;
		$records['song_id'] = $res;
		$res = $this->song->saveUserSongRecords($records);
		$this->assertTrue($res >= 0,'更新用户点歌记录');
	}
	

}

