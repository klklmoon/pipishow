<?php
/**
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: GiftBagServiceTest.php 9225 2013-04-26 05:53:29Z hexin $ 
 * @package
 */
class GiftBagServiceTest extends BaseTest {
	protected $bagService;
	
	public function __construct(){
		$this->bagService = new GiftBagService();
	}
	
	/**
	 * @medium
	 */
	public function testSaveUserGiftBagByUid(){
		$this->getNewUser();
		$this->getGift();
		$bag = array(
			'uid'		=> self::$uid,
			'gift_id'	=> self::$gift_id,
			'num'		=> 1,
		);
		$a_id = $this->bagService->saveUserGiftBagByUid($bag, array('source' => 1));
		$this->assertTrue($a_id == true, '保存礼物背包测试不通过');
		
		$gift_bag = $this->bagService->getUserBagByGiftIds(self::$uid, self::$gift_id);
		$gift_bag = array_pop($gift_bag[self::$uid]);
		$this->assertTrue($gift_bag['num'] == $bag['num'], '根据礼物Id获取用户背包中的礼物测试不通过');
		
		$records = $this->bagService->getUserBagRecords(self::$uid, 0, 10000000);
		$record = array_pop($records[self::$uid]);
		$this->assertTrue($record['uid'] == self::$uid && $record['gift_id'] == self::$gift_id && $record['num'] == $bag['num'], '写入并获取用户背包记录测试不通过');
		
		$e_id = $this->bagService->saveUserGiftBagByUid($bag, array('source' => 1));
		$gift_bag = $this->bagService->getUserBagByGiftIds(self::$uid, self::$gift_id);
		$gift_bag = array_pop($gift_bag[self::$uid]);
		$this->assertTrue($gift_bag['num'] == 2 && $a_id == $e_id, '继续送入用户背包中的礼物测试不通过');
	}
	
	public function testGetUserGiftBagByUids(){
		$bags = $this->bagService->getUserGiftBagByUids(self::$uid);
		$bag = array_pop($bags[self::$uid]);
		$this->assertTrue($bag['gift_id'] == self::$gift_id, '获取用户背包中的礼物测试不通过');
	}
	
	public function testSendFromUserBagByUid(){
		//正常情况
		$r = $this->bagService->sendFromUserBagByUid(self::$uid, self::$gift_id, 1);
		$gift_bag = $this->bagService->getUserBagByGiftIds(self::$uid, self::$gift_id);
		$gift_bag = array_pop($gift_bag[self::$uid]);
		$this->assertTrue($r == 1 && $gift_bag['num'] == 1, '礼物从用户背包送出测试不通过');
		
		//送出背包礼物过度的情况
		$r = $this->bagService->sendFromUserBagByUid(self::$uid, self::$gift_id, 10);
		$this->assertTrue($r == 0, '送出背包礼物过度的情况测试不通过');
	}
}

