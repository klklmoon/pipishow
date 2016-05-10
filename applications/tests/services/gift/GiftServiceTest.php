<?php
/**
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: GiftServiceTest.php 9418 2013-05-01 07:04:13Z hexin $ 
 * @package
 */
class GiftServiceTest extends BaseTest {
	protected $giftService;
	private static $cat_id;
	protected static $gift_id;
	private static $effect_id;
	
	public function __construct(){
		$this->giftService = new GiftService();
	}
	
	/**
	 * @medium
	 */
	public function testSaveGiftCategory(){
		$tmp_name = 'c_'.uniqid();
		$cat = array(
			'cat_name'	=> $tmp_name."测试礼物分类",
			'cat_enname'=> $tmp_name,
		);
		$a_id = $this->giftService->saveGiftCategory($cat);
		if($this->giftService->getNotice()){
			$this->fail(var_export($this->giftService->getNotice(), true));
		}
		$this->assertTrue($a_id > 1, '新增礼物分类测试不通过');
		
		$category = $this->giftService->getGiftCategoryByCatIds($a_id);
		$category = $tmp_cat = array_pop($category);
		$this->assertTrue($tmp_cat['cat_name'] == $cat['cat_name'], '获取礼物分类测试不通过');
		
		$category['cat_enname'] = $tmp_name.'_test';
		$e_id = $this->giftService->saveGiftCategory($category);
		if($this->giftService->getNotice()){
			$this->fail(var_export($this->giftService->getNotice(), true));
		}
		$tmp_cat = GiftCategoryModel::model()->findByPk($e_id);
		$this->assertTrue($e_id == $a_id && $tmp_cat->cat_enname == $category['cat_enname'], '修改礼物分类测试不通过');
		
		return $e_id;
	}
	
	/**
	 * @depends testSaveGiftCategory
	 * @medium
	 */
	public function testDelGiftCategoryByCatIds($id){
		$r = $this->giftService->delGiftCategoryByCatIds($id);
		$category = $this->giftService->getGiftCategoryByCatIds($id);
		$this->assertTrue($r == true && empty($category), '删除分类测试不通过');
		
		$tmp_name = 'c_'.uniqid();
		$cat = array(
			'cat_name'	=> $tmp_name."测试礼物分类",
			'cat_enname'=> $tmp_name,
		);
		$a_id = $this->giftService->saveGiftCategory($cat);
		self::$cat_id = $a_id;
	}
	
	/**
	 * @depends testDelGiftCategoryByCatIds
	 * @medium
	 */
	public function testGetGiftCategory(){
		$cats = $this->giftService->getGiftCategory();
		$cats = $this->giftService->buildDataByIndex($cats, 'category_id');
		$cat = array_pop($cats);
		$this->assertTrue($cat['category_id'] == self::$cat_id, '获取礼物分类测试不通过');
	}
	
	/**
	 * @medium
	 */
	public function testSaveGift(){
		$tmp_name = 'g_'.uniqid();
		$gift_add = array(
			'cat_id'	=> self::$cat_id,
			'zh_name'	=> $tmp_name."测试礼物",
			'en_name'	=> $tmp_name,
			'shop_type'	=> array('1','2'),
			'image'		=> 'xxx.jpg',
			'pipiegg'	=> '1.00',
			'charm'		=> '1',
			'charm_points'	=> '1',
			'dedication'=> '1',
			'egg_points'=> '1',
			'gift_type'	=> array('1','2'),
		);
		$a_id = $this->giftService->saveGift($gift_add);
		if($this->giftService->getNotice()){
			$this->fail(var_export($this->giftService->getNotice(), true));
		}
		$this->assertTrue($a_id > 1, '添加礼物测试不通过');
		
		$gifts = $this->giftService->getGiftByIds($a_id);
		$gift = array_pop($gifts);
		$tmp_gift = array();
		foreach($gift_add as $k=>$v){
			$tmp_gift[$k] = $gift[$k];
		}
		$gift_add['shop_type'] = '3';
		$gift_add['gift_type'] = '3';
		$this->assertSame($tmp_gift, $gift_add, '获取礼物测试不通过');
		
		$gift['en_name'] .= '_test';
		$e_id = $this->giftService->saveGift($gift);
		if($this->giftService->getNotice()){
			$this->fail(var_export($this->giftService->getNotice(), true));
		}
		$gifts = $this->giftService->getGiftByIds($e_id);
		$tmp_gift = array_pop($gifts);
		$this->assertTrue($a_id == $e_id && $gift['en_name'] == $tmp_gift['en_name'], '修改礼物测试不通过');
		
		return $e_id;
	}
	
	/**
	 * @depends testSaveGift
	 * @medium
	 */
	public function testDelGiftByGiftId($id){
		$r = $this->giftService->delGiftByGiftId($id);
		$gifts = $this->giftService->getGiftByIds($id);
		$gift = array_pop($gifts);
		$this->assertTrue($r == true && $gift['is_display'] == 2, '删除礼物测试不通过');
		self::$gift_id = $id;
	}
	
	public function testSaveGiftEffect(){
		$effect_add = array(
			'gift_id'	=> self::$gift_id,
			'position'	=> '0',
			'num'		=> '1',
			'timeout'	=> '1',
			'effect'	=> 'xxx.jpg'
		);
		$a_id = $this->giftService->saveGiftEffect($effect_add);
		if($this->giftService->getNotice()){
			$this->fail(var_export($this->giftService->getNotice(), true));
		}
		$this->assertTrue($a_id > 1, '添加礼物效果测试不通过');
		
		$effects = $this->giftService->getGiftEffectByGiftIds(self::$gift_id);
		$effect = $effects[self::$gift_id][0];
		$tmp_effect = array();
		foreach($effect_add as $k=>$v){
			$tmp_effect[$k] = $effect[$k];
		}
		$this->assertSame($tmp_effect, $effect_add, '根据礼物id获取礼物特效测试不通过');
		
		$effect['num'] = '2';
		$e_id = $this->giftService->saveGiftEffect($effect);
		if($this->giftService->getNotice()){
			$this->fail(var_export($this->giftService->getNotice(), true));
		}
		$effects = $this->giftService->getGiftEffectByGiftIds(self::$gift_id);
		$tmp_effect = $effects[self::$gift_id][0];
		$this->assertTrue($a_id == $e_id && $effect['num'] == $tmp_effect['num'], '修改礼物特效测试不通过');
		
		return $e_id;
	}
	
	/**
	 * @depends testSaveGiftEffect
	 */
	public function testDelGiftEffect($id){
		$r = $this->giftService->delGiftEffectByEffectIds(array($id));
		$effects = $this->giftService->getGiftEffectByGiftIds(self::$gift_id);
		$this->assertTrue($r == true && empty($effects), '删除礼物特效测试不通过');
		
		$effect_add = array(
			'gift_id'	=> self::$gift_id,
			'position'	=> '0',
			'num'		=> '1',
		);
		$this->giftService->saveGiftEffect($effect_add);
		$r = $this->giftService->delGiftEffectByGiftIds(self::$gift_id);
		$effects = $this->giftService->getGiftEffectByGiftIds(self::$gift_id);
		$effects = $this->giftService->buildDataByIndex($effects, 'effect_id');
		$this->assertTrue($r == true && empty($effects), '根据礼物Id删除礼物效果测试不通过');
		
		$effect_add = array(
			'gift_id'	=> self::$gift_id,
			'position'	=> 0,
			'num'		=> 1,
		);
		$a_id = $this->giftService->saveGiftEffect($effect_add);
		self::$effect_id = $a_id;
	}
	
	public function testGetGiftList(){
		$gifts = $this->giftService->getGiftList(array(), true);
		if(empty($gifts)){
			$this->fail('服务层GiftService的getGiftList方法有异常');
		}
		$gift = array_pop($gifts);
		$this->assertTrue($gift['gift_id'] == self::$gift_id && $gift['effects'][0]['effect_id'] == self::$effect_id, '获取所有礼物测试不通过');
	}
	
	public function testGetGiftByCondition(){
		$gifts = $this->giftService->getGiftByCondition(0, 1, array('gift_id' => self::$gift_id));
		$gift = array_pop($gifts['list']);
		$this->assertTrue($gift['gift_id'] == self::$gift_id, '根据条件获取礼物的分页测试不通过');
	}
	
	public function testGetCatGiftList(){
		$gifts = $this->giftService->getCatGiftList();
		if(empty($gifts)){
			$this->fail('服务层GiftService的getCatGiftList方法有异常');
		}
		$gifts = array_pop($gifts);
		$gift = array_pop($gifts['child']);
		$this->assertTrue($gifts['category_id'] == self::$cat_id && $gift['gift_id'] == self::$gift_id, '获取礼物的分类列表测试不通过');
	}
	
	public function testGetGiftByCatIds(){
		$gifts = $this->giftService->getGiftByCatIds(self::$cat_id, true);
		$gifts = $gifts[self::$cat_id];
		if(empty($gifts)){
			$this->fail('服务层GiftService的getGiftList方法有异常');
		}
		$gift = array_pop($gifts);
		$this->assertTrue($gift['gift_id'] == self::$gift_id && $gift['effects'][0]['effect_id'] == self::$effect_id, '根据礼物分类id获取礼物测试不通过');
	}
	
	public function testGetGiftEffectByNum(){
		$effect = $this->giftService->getGiftEffectByNum(self::$gift_id, 1);
		$this->assertTrue($effect['effect_id'] == self::$effect_id, '根据送礼数量获取礼物特效测试不通过');
	}
	
	/**
	 * @medium
	 */
	public function testSaveUserGiftRecords(){
		$this->getNewUser();
		$this->getToUser();
		$records = array(
			'uid'		=> self::$uid,
			'to_uid'	=> self::$to_uid,
			'gift_id'	=> self::$gift_id,
			'gift_type'	=> 0,
			'record_sid'=> 1,
			'num'		=> 1,
			'target_id'	=> 1,
		);
		$record_id = $this->giftService->saveUserGiftRecords($records);
		$this->assertTrue($record_id > 1, '存储用户送礼记录测试不通过');
	}
	
	public function testGetUserGiftSendRecordsByUid(){
		$records = $this->giftService->getUserGiftSendRecordsByUid(self::$uid);
		$record = array_pop($records['list']);
		$this->assertTrue($record['uid'] == self::$uid && $record['to_uid'] == self::$to_uid && $record['gift_id'] == self::$gift_id, '获取用户送出的礼物记录测试不通过');
	}
	
	/**
	 * @medium
	 */
	public function testSendGift(){
		//用户余额不足的情况
		$r = $this->giftService->sendGift(self::$uid, self::$to_uid, 1, self::$gift_id, 1);
		$this->assertTrue($r == 0, '用户余额不足的情况送礼不成功测试不通过');
		
		//用户余额充足的情况
		$consumeService = new ConsumeService();
		$consumeService->addEggs(self::$uid, 1);
		$this->giftService->sendGift(self::$uid, self::$to_uid, 1, self::$gift_id, 1);
		$consume = $consumeService->getConsumesByUids(self::$uid);
		$consume = array_pop($consume);
		
		$records = $this->giftService->getUserGiftSendRecordsByUid(self::$uid);
		$record = array_pop($records['list']);
		
		$relation_count = UserGiftSendRelationRecordsModel::model()->countByAttributes(array('record_id' => $record['record_id']));
		
		$gift = $this->giftService->getGiftByIds($record['gift_id']);
		$gift = array_pop($gift);
		
		$pipiEggModel = UserPipiEggRecordsModel::model();
		$criteria = $pipiEggModel->getCommandBuilder()->createCriteria();
		$criteria->condition = 'uid = '.self::$uid;
		$criteria->order = 'record_id DESC';
		$criteria->limit = '1';
		$pipiEggRecord = $pipiEggModel->find($criteria);
		
		$delicationModel = UserDedicationRecordsModel::model();
		$criteria = $delicationModel->getCommandBuilder()->createCriteria();
		$criteria->condition = 'uid = '.self::$uid;
		$criteria->order = 'record_id DESC';
		$criteria->limit = '1';
		$delicationRecord = $delicationModel->find($criteria);
		
		$charmModel = DoteyCharmRecordsModel::model();
		$criteria = $charmModel->getCommandBuilder()->createCriteria();
		$criteria->condition = 'uid = '.self::$to_uid;
		$criteria->order = 'record_id DESC';
		$criteria->limit = '1';
		$charmRecord = $charmModel->find($criteria);
		
		$charmPointModel = DoteyCharmPointRecordsModel::model();
		$criteria = $charmPointModel->getCommandBuilder()->createCriteria();
		$criteria->condition = 'uid = '.self::$to_uid;
		$criteria->order = 'record_id DESC';
		$criteria->limit = '1';
		$charmPointRecord = $charmPointModel->find($criteria);
		
		$this->assertTrue($consume['pipiegg'] == 0 &&
			$record['gift_id'] == self::$gift_id &&
			$relation_count == 2 &&
			$pipiEggRecord->record_sid == $record['record_id'] &&
			$delicationRecord->dedication == $gift['dedication'] * $record['num'] &&
			$charmRecord->charm == $gift['charm'] * $record['num'] &&
			$charmPointRecord->charm_points == $gift['charm_points'] * $record['num'],
			'主播送礼测试不通过');
	}
	
	public function testGetUserGiftReceiveRecordsByUid(){
		$records = $this->giftService->getUserGiftReceiveRecordsByUid(self::$to_uid);
		$record = array_pop($records);
		$this->assertTrue($record['uid'] == self::$uid && $record['send_uid'] == self::$to_uid && $record['gift_id'] == self::$gift_id, '获取用户收到的礼物记录测试不通过');
	}
	
	public function testCountUserGiftReceiveRecordsByUid(){
		$row = $this->giftService->countUserGiftReceiveRecordsByUid(self::$to_uid);
		$this->assertTrue($row['count'] == 2, '统计用户收到的礼物数据测试不通过');
	}
	
	public function testGetGiftType(){
		$type = $this->giftService->getGiftType(1);
		$this->assertTrue(array_pop($type) == '主站', '获取礼物类型测试不通过');
	}
	
	public function testBatchSaveGiftEffect(){
		$effect_adds = array(
			array(
				'gift_id'	=> self::$gift_id,
				'position'	=> '0',
				'num'		=> '2',
				'timeout'	=> '1',
				'effect'	=> 'xxx.jpg'
			),
			array(
				'gift_id'	=> self::$gift_id,
				'position'	=> '0',
				'num'		=> '3',
				'timeout'	=> '1',
				'effect'	=> 'xxx.jpg'
			),
		);
		$this->giftService->batchSaveGiftEffect(self::$gift_id, $effect_adds);
		$effects = $this->giftService->getGiftEffectByGiftIds(self::$gift_id);
		$count = count($effect = $effects[self::$gift_id]);
		$this->assertTrue($count == 3, '批量添加礼物效果测试不通过');
	}
}

