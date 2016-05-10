<?php
/**
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: UserJsonInfoServiceTest.php 10226 2013-05-15 06:16:51Z hexin $ 
 * @package
 */
class UserJsonInfoServiceTest extends BaseTest {
	protected $jsonInfoService;
	
	public function __construct(){
		$this->jsonInfoService = new UserJsonInfoService();
	}
	
	/**
	 * @test
	 * @medium
	 */
	public function userInfo(){
		$this->getNewUser();
		$data = array(
			'nk'	=> 'abc',
			'vip'	=> '1',
			'lb'	=> array(
				'img'	=> 'xxx.jpg',
				't'		=> 30,
			),
		);
		$r = $this->jsonInfoService->setUserInfo(self::$uid, $data);
		$this->assertTrue($r == true, 'UserInfoJson保存测试不通过');
		
		$userInfo = $this->jsonInfoService->getUserInfo(self::$uid);
		$this->assertSame(json_decode($userInfo, true), $data, 'UserInfoJson获取数据测试不通过');
	}
	
	/**
	 * @test
	 */
	public function getUserInfo(){
		$userInfo = $this->jsonInfoService->getUserInfo(-1);
		$this->assertSame($userInfo, '{}', 'UserInfoJson获取不到数据时的返回值测试不通过');
	}
	
	/**
	 * @test
	 */
	public function getUserInfos(){
		$this->getToUser();
		$data = array(
			'nk'	=> 'bbbbb',
			'vip'	=> '2',
			'lb'	=> array(
				'img'	=> 'xxx.jpg',
				't'		=> 30,
			),
		);
		$this->jsonInfoService->setUserInfo(self::$to_uid, $data);
		
		$userInfos = $this->jsonInfoService->getUserInfos(array(self::$uid, self::$to_uid), false);
		$this->assertTrue($userInfos[self::$uid]['nk'] == 'abc' && $userInfos[self::$to_uid]['nk'] == 'bbbbb', '批量获取userInfo信息测试不通过');
	}
	
	/**
	 * @test
	 */
	public function deleteUserInfo(){
		$r = $this->jsonInfoService->deleteUserInfo(self::$uid, array('vip'));
		$this->assertTrue($r == true, 'UserInfoJson删除测试不通过');
		
		$userInfo = $this->jsonInfoService->getUserInfo(self::$uid, false);
		$this->assertArrayNotHasKey('vip', $userInfo, 'UserInfoJson删除测试不通过');
	}
}

