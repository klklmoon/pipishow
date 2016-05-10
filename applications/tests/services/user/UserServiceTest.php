<?php
/**
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: UserServiceTest.php 9330 2013-04-30 05:58:41Z hexin $ 
 * @package
 */
class UserServiceTest extends BaseTest {
	protected $userService;
	
	public function __construct(){
		$this->userService = new UserService();
	}
	
	/**
	 * @medium
	 */
	public function testSaveUserBasic(){
		$username = 't_'.uniqid();
		$user['nickname'] = $username;
		$user['username'] = $username;
		$user['reg_email'] = $username.'@pipi.cn';
		$user['password'] = $username;
		$userData = $this->userService->saveUserBasic($user);
		if($this->userService->getNotice()){
			$this->fail(var_export($this->userService->getNotice(), true));
		}
		$this->assertTrue($user['username'] == $userData['username'], '添加用户基础信息测试不通过');
		
		$userTmp = $this->userService->getUserBasicByUids(array($userData['uid']));
		$userTmp = array_pop($userTmp);
		$this->assertTrue($userData['username'] == $userTmp['username'], '获取用户基本信息测试不通过');
		
		$userData['nickname'] .= '_1';
		unset($userData['password']);
		$userDataTmp = $this->userService->saveUserBasic($userData);
		if($this->userService->getNotice()){
			$this->fail(var_export($this->userService->getNotice(), true));
		}
		$this->assertTrue($userData['username'] == $userDataTmp['username'], '修改用户基本信息测试不通过');
		self::$uid = $userData['uid'];
		
		//异常测试
		$userData['uid'] = -1;
		$userDataTmp = $this->userService->saveUserBasic($userData);
		$this->assertTrue($this->userService->getError() == Yii::t('common','Parameter is empty'), 'uid异常测试不通过');
		
		//不存在的uid
		$userData['uid'] = 100000000;
		$userDataTmp = $this->userService->saveUserBasic($userData);
		$this->assertTrue(array_pop($this->userService->getNotice()) == Yii::t('user','The user does not exist'), 'uid不存在测试不通过');
	
		//username唯一
		unset($userData['uid']);
		$userDataTmp = $this->userService->saveUserBasic($userData);
		$this->assertTrue(array_pop(array_pop($this->userService->getNotice())) == '用户名称 "'.$userData['username'].'" 已被取用.', '用户名唯一测试不通过');
		
		//修改用户名不合格
		$userData['uid'] = self::$uid;
		$userData['username'] = 'a';
		$userDataTmp = $this->userService->saveUserBasic($userData);
		$this->assertTrue(array_pop(array_pop($this->userService->getNotice())) == '用户名称 太短 (最小值为 4 字符串).', '修改用户名不合格测试不通过');
	}
	
	public function testSaveUserExtend(){
		$this->getNewUser();
		$user['uid'] = self::$uid;
		$r = $this->userService->saveUserExtend($user);
		$this->assertTrue($r == true, '添加用户扩展信息测试不通过');
		
		$userData = $this->userService->getUserExtendByUids(array(self::$uid));
		$userData = array_pop($userData);
		$this->assertTrue($userData['uid'] == self::$uid, '获取用户扩展信息测试不通过');
		
		$userData['gender'] = 1;
		$r = $this->userService->saveUserExtend($user);
		$userTmp = $this->userService->getUserExtendByUids(array(self::$uid));
		$userTmp = array_pop($userTmp);
		$this->assertTrue($r == true && $userData['gender'] == $userData['gender'], '修改用户扩展信息测试不通过');
		
		//异常测试
		$userData['uid'] = -1;
		$userDataTmp = $this->userService->saveUserExtend($userData);
		$this->assertTrue($this->userService->getError() == Yii::t('common','Parameter is empty'), 'uid异常测试不通过');
	}
	
	public function testVadidatorPassword(){
		$this->getNewUser();
		$user = $this->userService->getUserBasicByUids(array(self::$uid));
		$user = array_pop($user);
		if(empty($user)){
			$this->fail('服务层UserService的getUserBasicByUids方法有异常');
		}
		$userData = array();
		$r = $this->userService->vadidatorPassword($user['username'], $user['username'], 0, $userData);
		$this->assertTrue($r == true, '用户登陆测试不通过');
	}
	
	public function testSaveUserConfig(){
		$this->getNewUser();
		$config['uid'] 			= self::$uid;
		$config['sheildmessage']= array('abc');
		$config['blacklist']	= array('abc');
		$config['sheilddynamic']= array('abc');
		$this->userService->saveUserConfig($config);
		$configData = $this->userService->getUserConfigByUids(array(self::$uid));
		$this->assertTrue($config['uid'] == self::$uid && $config['sheildmessage'] == array('abc') && $config['blacklist'] == array('abc') && $config['sheilddynamic'] == array('abc'), '保存用户配置信息测试不通过');
	}
	
	public function testSaveUserLoginRecords(){
		$record = array(
			'uid'	=> self::$uid,
		);
		$this->userService->saveUserLoginRecords($record);
		$data = UserLoginRecordsModel::model()->findByAttributes(array('uid' => self::$uid));
		$this->assertTrue($data->uid == self::$uid, '保存用户登陆记录测试不通过');
	}
	
	public function testGetUserFrontsAttributeByCondition(){
		$userTmp = $this->userService->getUserBasicByUids(array(self::$uid));
		$userTmp = array_pop($userTmp);
		$user = $this->userService->getUserFrontsAttributeByCondition($userTmp['username']);
		$this->assertTrue($user['uid'] == self::$uid && $user['is_redis'] == false, '取得用户登录后的客户端初始化的属性测试不通过');
	}
}

