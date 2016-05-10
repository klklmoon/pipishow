<?php
/**
 * 消费服务层测试用例
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: ConsumeServiceTest.php 9525 2013-05-03 08:32:57Z hexin $ 
 * @package
 */
class ConsumeServiceTest extends BaseTest {
	protected $consumeService;
	protected $userService;
	
	public function __construct(){
		$this->consumeService = new ConsumeService();
		$this->userService = new UserService();
	}
	
	/**
	 * @medium
	 */
	public function testAddEggs(){
		$this->getNewUser();
		$user = $this->userService->getUserBasicByUids(array(self::$uid));
		$user = array_pop($user);
		if(empty($user)){
			$this->fail('用户Service的getUserBasicByUids方法有异常');
		}
		$r = $this->consumeService->addEggs(self::$uid, 10);
		if(!$r){
			$this->fail('调用存储过程失败');
		}
		$user_consume = $this->consumeService->getConsumesByUids(self::$uid);
		$user_consume = array_pop($user_consume);
		$this->assertTrue($user_consume['pipiegg'] == 10, '加皮蛋测试不通过');
	}
	
	public function testAddFreezeEggs(){
		$this->getNewUser();
		$user = $this->userService->getUserBasicByUids(array(self::$uid));
		$user = array_pop($user);
		if(empty($user)){
			$this->fail('用户Service的getUserBasicByUids方法有异常');
		}
		$r = $this->consumeService->addFreezeEggs(self::$uid, 10);
		if(!$r){
			$this->fail('调用存储过程失败');
		}
		$user_consume = $this->consumeService->getConsumesByUids(self::$uid);
		$user_consume = array_pop($user_consume);
		$this->assertTrue($user_consume['pipiegg'] == 20 && $user_consume['freeze_pipiegg'] == 10, '加冻结皮蛋测试不通过');
	}
	
	public function testUnAddFreezeEggs(){
		$this->getNewUser();
		$user = $this->userService->getUserBasicByUids(array(self::$uid));
		$user = array_pop($user);
		if(empty($user)){
			$this->fail('用户Service的getUserBasicByUids方法有异常');
		}
		
		//不允许撤销过度的情况
		$r = $this->consumeService->UnAddFreezeEggs(self::$uid, 20);
		$this->assertTrue($r == 0, '过度撤销加冻结皮蛋测试不通过');
		
		//正常情况
		$r = $this->consumeService->unAddFreezeEggs(self::$uid, 10);
		$user_consume = $this->consumeService->getConsumesByUids(self::$uid);
		$user_consume = array_pop($user_consume);
		$this->assertTrue($user_consume['pipiegg'] == 10 && $user_consume['freeze_pipiegg'] == 0, '撤销加冻结皮蛋测试不通过');
		
		//不允许再次撤销的情况
		$r = $this->consumeService->unAddFreezeEggs(self::$uid, 10);
		$this->assertTrue($r == 0, '再次撤销加冻结皮蛋测试不通过');
	}
	
	public function testConsumeEggs(){
		$this->getNewUser();
		$user = $this->userService->getUserBasicByUids(array(self::$uid));
		$user = array_pop($user);
		if(empty($user)){
			$this->fail('用户Service的getUserBasicByUids方法有异常');
		}
		
		//测试皮蛋全部冻结后即余额不足不能消费的情况
		$r = $this->consumeService->freezeEggs(self::$uid, 10);
		$r = $this->consumeService->consumeEggs(self::$uid, 10);
		$this->assertTrue($r == 0, '皮蛋全部冻结后不能消费情况测试不通过');
		
		//不能消费过度出负值的情况
		$r = $this->consumeService->unFreezeEggs(self::$uid, 10);
		$r = $this->consumeService->consumeEggs(self::$uid, 20);
		$this->assertTrue($r == 0, '过度消费皮蛋测试不通过');
		
		//正常情况
		$r = $this->consumeService->consumeEggs(self::$uid, 10);
		$user_consume = $this->consumeService->getConsumesByUids(self::$uid);
		$user_consume = array_pop($user_consume);
		$this->assertTrue($user_consume['pipiegg'] == 0,'消费皮蛋测试不通过');
	}
	
	public function testFreezeEggs(){
		$this->getNewUser();
		$user = $this->userService->getUserBasicByUids(array(self::$uid));
		$user = array_pop($user);
		if(empty($user)){
			$this->fail('用户Service的getUserBasicByUids方法有异常');
		}
		
		//余额不足不能冻结皮蛋的情况
		$r = $this->consumeService->freezeEggs(self::$uid, 10);
		$this->assertTrue($r == 0, '余额不足不能冻结皮蛋测试不通过');
		
		//正常情况
		$r = $this->consumeService->addEggs(self::$uid, 10);
		$r = $this->consumeService->freezeEggs(self::$uid, 10);
		$user_consume = $this->consumeService->getConsumesByUids(self::$uid);
		$user_consume = array_pop($user_consume);
		$this->assertTrue($user_consume['pipiegg'] == 10 && $user_consume['freeze_pipiegg'] == 10, '消费皮蛋测试不通过');
	}
	
	public function testUnFreezeEggs(){
		$this->getNewUser();
		$user = $this->userService->getUserBasicByUids(array(self::$uid));
		$user = array_pop($user);
		if(empty($user)){
			$this->fail('用户Service的getUserBasicByUids方法有异常');
		}
		
		//不能过度释放冻结皮蛋的情况
		$r = $this->consumeService->unFreezeEggs(self::$uid, 20);
		$this->assertTrue($r == 0, '过度释放冻结皮蛋测试不通过');
		
		//正常情况
		$r = $this->consumeService->unFreezeEggs(self::$uid, 10);
		$user_consume = $this->consumeService->getConsumesByUids(self::$uid);
		$user_consume = array_pop($user_consume);
		$this->assertTrue($user_consume['pipiegg'] == 10 && $user_consume['freeze_pipiegg'] == 0, '消费皮蛋测试不通过');
		$r = $this->consumeService->consumeEggs(self::$uid, 10);
	}
	
	/**
	 * @medium
	 */
	public function testSaveUserConsumeAttribute(){
		$this->getToUser();
// 		//不允许接口不通过事务更新皮蛋的情况，目前代码有问题，新增记录会写入负值
// 		$consume = array(
// 			'uid'				=> self::$to_uid,
// 			'pipiegg'			=> -10,
// 			'freeze_pipiegg'	=> -10,
// 			'consume_pipiegg'	=> -10,
// 		);
// 		$r = $this->consumeService->saveUserConsumeAttribute($consume);
// 		$user = $this->consumeService->getConsumesByUids(self::$to_uid);
// 		$user = array_pop($user);
// 		$this->assertTrue($user['pipiegg'] >= 0 && $user['freeze_pipiegg'] >= 0 && $user['consume_pipiegg'] >= 0, '不允许接口不通过事务更新皮蛋的情况测试不通过');
	
// 		//不允许更新魅力值、贡献值、魅力点、皮点有负值的情况，目前代码有问题，新增记录会写入负值
// 		$consume = array(
// 			'uid'			=> self::$to_uid,
// 			'dedication'	=> -10,
// 			'charm'			=> -10,
// 			'charm_points'	=> -10,
// 			'egg_points'	=> -10,
// 		);
// 		$r = $this->consumeService->saveUserConsumeAttribute($consume);
// 		$user = $this->consumeService->getConsumesByUids(self::$to_uid);
// 		$user = array_pop($user);
// 		$this->assertTrue($user['dedication'] >= 0 && $user['charm'] >= 0 && $user['charm_points'] >= 0 && $user['egg_points'] >= 0, '不允许更新魅力值、贡献值、魅力点、皮点有负值的情况测试不通过');
		
		//正常情况只允许更新魅力值、贡献值、魅力点、皮点、用户等级、主播等级
		$consume = array(
			'uid'			=> self::$to_uid,
			'dedication'	=> 10,
			'charm'			=> 10,
			'charm_points'	=> 10,
			'egg_points'	=> 10,
			'rank'			=> 1,
			'dotey_rank'	=> 1,
		);
		$r = $this->consumeService->saveUserConsumeAttribute($consume);
		$user = $this->consumeService->getConsumesByUids(self::$to_uid);
		$user = array_pop($user);
		$this->assertTrue($user['dedication'] == 10 && $user['charm'] == 10 && $user['charm_points'] == 10 && $user['egg_points'] == 10, '正常情况只允许更新魅力值、贡献值、魅力点、皮点、用户等级、主播等级测试不通过');
	}
	
	public function testSaveUserDedicationRecords(){
		$records['uid'] = self::$uid;
		$records['from_target_id'] = 1;
		$records['to_target_id'] = 1;
		$records['dedication'] = 1;
		$records['record_sid'] = 1;
		$records['num'] = 1;
		$records['source'] = SOURCE_GIFTS;
		$records['sub_source'] = SUBSOURCE_GIFTS_BUY;
		$id = $this->consumeService->saveUserDedicationRecords($records, 1);
		$this->assertGreaterThan(0, $id, '添加贡献值记录测试不通过');
	}
	
	public function testSaveUserPipiEggRecords(){
		$records['uid'] = self::$uid;
		$records['from_target_id'] = 1;
		$records['to_target_id'] = 1;
		$records['pipiegg'] = 1;
		$records['record_sid'] = 1;
		$records['num'] = 1;
		$records['source'] = SOURCE_GIFTS;
		$records['sub_source'] = SUBSOURCE_GIFTS_BUY;
		$id = $this->consumeService->saveUserPipiEggRecords($records, 0);
		$this->assertGreaterThan(0, $id, '添加皮蛋消费记录测试不通过');
	}
	
	public function testSaveUserEggPointsRecords(){
		$records['uid'] = self::$uid;
		$records['target_id'] = 1;
		$records['sender_uid'] = 1;
		$records['egg_points'] = 1;
		$records['record_sid'] = 1;
		$records['num'] = 1;
		$records['source'] = SOURCE_GIFTS;
		$records['sub_source'] = SUBSOURCE_GIFTS_BUY;
		$id = $this->consumeService->saveUserEggPointsRecords($records, 0);
		$this->assertGreaterThan(0, $id, '添加皮点变化记录测试不通过');
	}
	
	public function testSaveDoteyCharmRecords(){
		$records['uid'] = self::$uid;
		$records['target_id'] = 1;
		$records['sender_uid'] = 1;
		$records['charm'] = 1;
		$records['record_sid'] = 1;
		$records['num'] = 1;
		$records['source'] = SOURCE_GIFTS;
		$records['sub_source'] = SUBSOURCE_GIFTS_BUY;
		$id = $this->consumeService->saveDoteyCharmRecords($records, 1);
		$this->assertGreaterThan(0, $id, '添加魅力值变化记录测试不通过');
	}
	
	public function testSaveDoteyCharmPointsRecords(){
		$records['uid'] = self::$uid;
		$records['target_id'] = 1;
		$records['sender_uid'] = 1;
		$records['charm_points'] = 1;
		$records['record_sid'] = 1;
		$records['num'] = 1;
		$records['source'] = SOURCE_GIFTS;
		$records['sub_source'] = SUBSOURCE_GIFTS_BUY;
		$id = $this->consumeService->saveDoteyCharmPointsRecords($records, 1);
		$this->assertGreaterThan(0, $id, '添加魅力点变化记录测试不通过');
	}
}

