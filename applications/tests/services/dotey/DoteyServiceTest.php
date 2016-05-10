<?php
/**
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: DoteyServiceTest.php 9222 2013-04-26 04:35:59Z hexin $ 
 * @package
 */
class DoteyServiceTest extends BaseTest {

	protected $dotey;
	
	public function __construct(){
		$this->getNewUser();
		$this->dotey = new DoteyService();
	}

	/**
	 * @test
	 */
	public function saveUserDoteyBase()
	{
		$doteyBase = array();
		$doteyBase['uid'] = self::$uid;
		$doteyBase['sign_type'] = array_rand(array(1,2,4,8));
		$res = $this->dotey->saveUserDoteyBase($doteyBase);
		if($this->dotey->getNotice()){
			$this->fail(var_export($this->dotey->getNotice(), true));
		}
		$dotey = $this->dotey->getDoteyInfoByUid(self::$uid);
		$this->assertTrue($res && $dotey['sign_type'] == $doteyBase['sign_type'], '保存主播基本信息失败');
		
		$doteyBase['sign_type'] = array_rand(array(1,2,4,8));
		$res = $this->dotey->saveUserDoteyBase($doteyBase);
		if($this->dotey->getNotice()){
			$this->fail(var_export($this->dotey->getNotice(), true));
		}
		$dotey = $this->dotey->getDoteyInfoByUid(self::$uid);
		$this->assertTrue($res && $dotey['sign_type'] == $doteyBase['sign_type'], '更新主播基本信息失败');
	}

	/**
	 * @test
	 */
	public function getDoteyInfoByUid()
	{
		$uid = self::$uid;
		$res = $this->dotey->getDoteyInfoByUid($uid);
		$this->assertTrue($res['uid'] == $uid,'获取主播信息失败');
	}

	/**
	 * @test
	 */
	public function getDoteyInfoByUids()
	{
		$uids = array(self::$uid);
		$res = $this->dotey->getDoteyInfoByUids($uids);
		$this->assertTrue($res[self::$uid]['uid'] == self::$uid,'取得主播信息失败');
	}

	
}

