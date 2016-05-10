<?php
/**
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: UserPropsServiceTest.php 9334 2013-04-30 06:38:56Z hexin $ 
 * @package
 */
class UserPropsServiceTest extends BaseTest {
	protected $userPropsService;
	
	public function __construct(){
		$this->userPropsService = new UserPropsService();
	}
	
	/**
	 * @medium
	 */
	public function testSaveUserPropsAttribute(){
		$this->getNewUser();
		$data = array(
			'uid'	=> self::$uid,
			'stars'	=> 1,
		);
		$r = $this->userPropsService->saveUserPropsAttribute($data);
		$tmp = $this->userPropsService->getUserPropsAttributeByUid(self::$uid);
		$this->assertTrue($r == true && $tmp['stars'] == 1, '存储用户道具属性测试不通过');
	}
	
	public function testSaveUserPropsBag(){
		$this->getProp();
		$bag = array(
			'uid'		=> self::$uid,
			'prop_id'	=> self::$prop_id,
			'num'		=> 1,
		);
		$a_id = $this->userPropsService->saveUserPropsBag($bag);
		$props = $this->userPropsService->getUserValidPropsOfBagByPropId(self::$uid, self::$prop_id);
		$prop = array_pop($props);
		$this->assertTrue($a_id > 0 && $prop['num'] == 1, '添加用户背包数据并获取用户购买所有有效的道具测试不通过');
		
		$prop['num'] = 2;
		foreach($prop as $key=>$val){
			if(!in_array($key, array('bag_id', 'uid', 'prop_id', 'cat_id', 'record_sid', 'target_id', 'num', 'valid_time'))){
				unset($prop[$key]);
			}
		}
		$e_id = $this->userPropsService->saveUserPropsBag($prop);
		$props = $this->userPropsService->getUserValidPropsOfBagByPropId(self::$uid, self::$prop_id);
		$prop1 = array_pop($props);
		$this->assertTrue($e_id == $a_id && $prop1['num'] == 2, '修改用户背包数据测试不通过');
	}
	
	public function testSaveUserPropsUse(){
		$record = array(
			'uid'	=> self::$uid,
			'prop_id'=> self::$prop_id,
		);
		$id = $this->userPropsService->saveUserPropsUse($record);
		if($this->userPropsService->getNotice()){
			$this->fail(var_export($this->userPropsService->getNotice(), true));
		}
		$this->assertTrue($id > 0, '存储用户使用的道具记录信息测试不通过');
	}
	
	public function testSaveUserPropsRecords(){
		$record = array(
			'uid'	=> self::$uid,
			'prop_id'=> self::$prop_id,
		);
		$id = $this->userPropsService->saveUserPropsRecords($record);
		if($this->userPropsService->getNotice()){
			$this->fail(var_export($this->userPropsService->getNotice(), true));
		}
		$r = $this->userPropsService->getUserPropsRecords(self::$uid, 1, 0);
		$tmp = array_pop($r['list']);
		$this->assertTrue($id > 0 && $tmp['prop_id'] == self::$prop_id, '存储用户购卖道具记录测试不通过');
	}
	
	public function testGetUserValidPropsOfBagByCatId(){
		$props = $this->userPropsService->getUserValidPropsOfBagByCatId(self::$uid, 0);
		$bags = $this->userPropsService->buildDataByIndex($props, 'bag_id');
		$bag = array_pop($bags);
		$this->assertTrue($bag['prop_id'] == self::$prop_id && $bag['num'] == 2, '获取用户购买某分类下所有有效的道具测试不通过');
	}
}

