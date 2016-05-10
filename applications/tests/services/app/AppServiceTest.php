<?php
/**
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: AppServiceTest.php 9525 2013-05-03 08:32:57Z hexin $ 
 * @package
 */
class AppServiceTest extends BaseTest {
	protected $app;
	protected static $app_id;
	protected static $app_name;
	protected static $app_enname;
	protected static $token;
	
	public function __construct(){
		$this->getNewUser();
		$this->app = new AppService();
	}
	/**
	 * @test
	 * @medium
	 */
	public function saveApp()
	{
		$app = array();
		self::$app_name = $app['app_name'] = $app['app_enname'] = uniqid();
		
		$app_secret = $app['app_secret'] = $this->app->buildAppSecret();
		$app_id = $this->app->saveApp($app);
		$this->assertTrue($app_id > 0,'保存新APP测试不通过');
		
		unset($app['app_secret']);
		$app['app_id'] = $app_id;
		$app_enname = $app['app_enname'] = 'test_' . uniqid();
		$res = $this->app->saveApp($app);
		$this->assertTrue($res >= 0,'更新APP测试不通过');
		
		self::$app_id = $app_id;
		self::$app_enname = $app_enname;
		self::$token = $app_secret;
		
		$getapp = $this->app->getAppInfoById($app_id);
		$this->assertTrue($getapp['app_name'] == $app['app_name'] && $getapp['app_enname'] == $app['app_enname'] && $getapp['app_secret'] == $app_secret,'APP保存数据不一致');
	}
	/**
	 * @test
	 */
	public function createAppToken()
	{
		$token = array();
		$token['uid'] = self::$uid;
		$token['app_id'] = self::$app_id;
		$token['token'] = self::$token;
		$res = $this->app->createAppToken($token);
		$this->assertTrue(is_array($res) && isset($res['uid']) && isset($res['app_id']) && ($res['uid'] == $token['uid']) && ($res['app_id'] == $token['app_id']) && $res['token'] == self::$token, 
				'创建AppToken测试不通过');
	}
	
	/**
	 * @test
	 */
	public function getAppInfoById()
	{
		$appId = self::$app_id;
		$res = $this->app->getAppInfoById($appId);
		$this->assertTrue($res['app_name'] == self::$app_name && $res['app_enname'] == self::$app_enname && $res['app_secret']==self::$token, 
				'按APP标识取得APP信息失败');
	}
	
	/**
	 * @test
	 */
	public function getAppInfoByEname()
	{
		$enname = self::$app_enname;
		$res = $this->app->getAppInfoByEname($enname);
		$this->assertTrue(is_array($res) && $res['app_name'] == self::$app_name && $res['app_enname'] == self::$app_enname && $res['app_secret']==self::$token, '按APP英文名取得APP信息失败');
	}
	
	/**
	 * @test
	 */
	public function getAppTokenByUid()
	{
		$uid = self::$uid;
		$appId = self::$app_id;
		$token = self::$token;
		$res = $this->app->getAppTokenByUid($uid,$appId,$token);
		$this->assertTrue(is_array($res) && $res['uid']==$uid && $res['app_id']==$appId && $res['token']==$token, '用uid,appid,token取得有效的token信息失败');
	}
	
	/**
	 * @test
	 */
	public function getAppTokenByAppId()
	{
		$uid = self::$uid;
		$appId = self::$app_id; 
		$token = self::$token;
		$res = $this->app->getAppTokenByAppId($appId,$token);
		$this->assertTrue(is_array($res) && $res['uid']==$uid && $res['app_id']==$appId && $res['token']==$token, '用token_id取得有效的token信息失败');
	}
	
	/**
	 * @test
	 */
	public function buildAppSecret()
	{
		$res = $this->app->buildAppSecret();
		$this->assertTrue(is_string($res), '生成app_secret失败');
	}
}

