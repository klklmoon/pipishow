<?php
/**
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: UserBuyPropsServiceTest.php 9229 2013-04-26 06:16:24Z hexin $ 
 * @package
 */
class UserBuyPropsServiceTest extends BaseTest {
	protected $userBuyPropsService;
	protected $consumeService;
	private static $consume_pipiegg = 0;
	private static $dedication = 0;
	private static $egg_points = 0;
	
	public function __construct(){
		$this->getNewUser();
		$this->getProp();
		Yii::app()->user->id = self::$uid;
	}
	
	private function initUserBuy(){
		$this->consumeService = new ConsumeService();
		$users = $this->consumeService->getConsumesByUids(self::$uid);
		$user = array_pop($users);
		$pipiegg = 10 - $user['pipiegg'];
		$this->consumeService->addEggs(self::$uid, $pipiegg);
		self::$consume_pipiegg = $user['consume_pipiegg'];
		self::$dedication = $user['dedication'];
		self::$egg_points = $user['egg_points'];
		$this->userBuyPropsService = new UserBuyPropsService(self::$uid, self::$prop_id);
		if($this->userBuyPropsService->errorCode){
			$this->fail(var_export($this->userBuyPropsService->errorCode, true));
		}
	}
	
	/**
	 * @test
	 * @medium
	 */
	public function buyProps(){
		$this->initUserBuy();
		$r = $this->userBuyPropsService->isPurchased();
		$this->assertTrue($r == true, '是否可购买检查测试不通过');
		
		$price = $this->userBuyPropsService->getPropsPrice();
		$this->assertTrue($price > 0, '取得道具所花费的价格不大于0');
		
		$this->userBuyPropsService->buyProps();
		if($this->userBuyPropsService->getNotice()){
			$this->fail(var_export($this->userBuyPropsService->getNotice(), true));
		}
		
		$users = $this->consumeService->getConsumesByUids(self::$uid);
		$user = array_pop($users);
		$this->assertTrue($user['pipiegg'] == 10-$price, '皮蛋消费不正确');
		
		$model = UserPropsRecordsModel::model();
		$criteria = $model->getCommandBuilder()->createCriteria();
		$criteria->order = 'record_id DESC';
		$criteria->limit = 1;
		$record = $model->find($criteria);
		$this->assertTrue($record->uid == self::$uid, '存储用户道具购买记录不成功');
		
		$model = UserPipiEggRecordsModel::model();
		$criteria = $model->getCommandBuilder()->createCriteria();
		$criteria->order = 'record_id DESC';
		$criteria->limit = 1;
		$record = $model->find($criteria);
		$this->assertTrue($record->uid == self::$uid, '存储用户皮蛋变化不成功');
		
		$model = UserDedicationRecordsModel::model();
		$criteria = $model->getCommandBuilder()->createCriteria();
		$criteria->order = 'record_id DESC';
		$criteria->limit = 1;
		$record = $model->find($criteria);
		$this->assertTrue($record->uid == self::$uid, '存储用户贡献值不成功');
		
		$model = UserPropsBagModel::model();
		$criteria = $model->getCommandBuilder()->createCriteria();
		$criteria->order = 'bag_id DESC';
		$criteria->limit = 1;
		$record = $model->find($criteria);
		$this->assertTrue($record->uid == self::$uid, '将用户购买的道具放入背包不成功');
		self::$consume_pipiegg += $this->userBuyPropsService->getPropsPrice();
		self::$dedication += $this->userBuyPropsService->getPropsDedication();
		self::$egg_points += $this->userBuyPropsService->getPropsEggPoints();
		$this->assertTrue($user['dedication'] == self::$dedication &&
			$user['egg_points'] == self::$egg_points &&
			$user['consume_pipiegg'] == self::$consume_pipiegg, '存储用户消费属性不成功');
	}
	
	private function initBuyCar(){
		$this->consumeService = new ConsumeService();
		$users = $this->consumeService->getConsumesByUids(self::$uid);
		$user = array_pop($users);
		$pipiegg = 10 - $user['pipiegg'];
		$this->consumeService->addEggs(self::$uid, $pipiegg);
		self::$consume_pipiegg = $user['consume_pipiegg'];
		self::$dedication = $user['dedication'];
		self::$egg_points = $user['egg_points'];
		$this->userBuyPropsService = new BuyCarPropsService(self::$uid, self::$prop_id);
		if($this->userBuyPropsService->errorCode){
			$this->fail(var_export($this->userBuyPropsService->errorCode, true));
		}
	}
	
	/**
	 * @test
	 * @medium
	 */
	public function bugCar(){
		$this->initBuyCar();
		$this->buyProps();
	}
	
	private function initBuyGuardian(){
		$this->consumeService = new ConsumeService();
		$users = $this->consumeService->getConsumesByUids(self::$uid);
		$user = array_pop($users);
		$pipiegg = 10 - $user['pipiegg'];
		$this->consumeService->addEggs(self::$uid, $pipiegg);
		self::$consume_pipiegg = $user['consume_pipiegg'];
		self::$dedication = $user['dedication'];
		self::$egg_points = $user['egg_points'];
		$this->userBuyPropsService = new BuyGuardianPropsService(self::$uid, self::$prop_id);
		if($this->userBuyPropsService->errorCode){
			$this->fail(var_export($this->userBuyPropsService->errorCode, true));
		}
	}
	
	/**
	 * @test
	 * @medium
	 */
	public function bugGuardian(){
		$this->initBuyGuardian();
		$this->buyProps();
	}
}

