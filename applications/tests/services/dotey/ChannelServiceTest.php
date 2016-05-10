<?php
/**
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: ChannelServiceTest.php 9395 2013-05-01 02:06:01Z hexin $ 
 * @package
 */
class ChannelServiceTest extends BaseTest {

	protected $channel;
	protected static $channel_id;
	protected static $sub_channel_id;
	protected static $relation_id;

	public function __construct() {
		$this->channel = new ChannelService();
	}
	
	/**
	 * @medium
	 */
	public function testSaveChannel(){
		$channel = array(
			'channel_name' => 'c_'.uniqid(),
		);
		$r = $this->channel->saveChannel($channel);
		$this->assertTrue($r > 0, '添加主频道测试不通过');
		
		$channel['channel_id'] = $r;
		$channel['is_show_index'] = 1;
		$r = $this->channel->saveChannel($channel);
		$tmp = $this->channel->getAllParentChannel($r);
		$tmp = array_pop($tmp);
		$this->assertTrue($tmp['is_show_index'] == $channel['is_show_index'], '修改主频道测试不通过');
		self::$channel_id = $r;
	}
	
	public function testSaveSubChannel(){
		$sub = array(
			'channel_id'=> self::$channel_id,
			'sub_name'	=> 'sub_'.uniqid(),
		);
		$r = $this->channel->saveSubChannel($sub);
		$this->assertTrue($r > 0, '添加子频道测试不通过');
		
		$sub['sub_channel_id'] = $r;
		$sub['sub_name'] .= '_t';
		$sub['dotey_num'] = 1;
		$r = $this->channel->saveSubChannel($sub);
		$tmp = $this->channel->getChannelByIdsFromCache(self::$channel_id, $r);
		$this->assertTrue($tmp['sub_name'] == $sub['sub_name'], '修改子频道测试不通过');
		self::$sub_channel_id = $r;
	}
	
	public function testSaveAreaChannel(){
		$area = array(
			array(
				'安徽省' => array('宣城市', '池州市'),
			),
			array(
				'浙江省' => array('绍兴市', '嘉兴市'),
			)	
		);
		$channel = array(self::$sub_channel_id); //子频道ID
		$r = $this->channel->saveAreaChannel($area, $channel);
		$this->assertTrue($r == true, '存储地区频道与分类关联测试不通过');
		self::$relation_id = $r;
	}
	
	public function testSaveAreaDotey(){
		$this->getNewUser();
		$dotey = array(
			array(
				'uid'	=> self::$uid,
				'area_channel_id' => self::$sub_channel_id,
				'area_relation_id'=> self::$relation_id,
			),
		);
		$r = $this->channel->saveAreaDotey($dotey);
		$this->assertTrue($r == true, '存储主播和地区频道的关系测试不通');
	}
	
	public function testGetAllChannelFromCache(){
		$channel = $this->channel->getAllChannelFromCache();
		ksort($channel);
		$channel = array_pop(array_pop($channel));
		$this->assertTrue($channel['channel_id'] == self::$channel_id && $channel['sub_channel_id'] == self::$sub_channel_id, '取得所有的频道测试不通过');
	}
	
	public function testGetChannelByCateId(){
		$c1 = $this->channel->getChannelByIdsFromCache(self::$channel_id, self::$sub_channel_id);
		$c2 = $this->channel->getChannelByCateId(self::$channel_id, self::$sub_channel_id);
		$this->assertSame($c1, $c2, '获取指定的分类的的频道，从缓存中读取的和从数据库中读取的不一致');
	}
	
	public function testGetAllAreaChannel(){
		$area = $this->channel->getAllAreaChannel();
		$area = array_pop($area);
		$this->assertTrue($area['sub_channel_id'] == self::$sub_channel_id, '获取所有地区频道测试不通过');
	}
	
	public function testDelChannelAreaRel(){
		$r = $this->channel->delChannelAreaRel(array('area_channel_id' => self::$sub_channel_id));
		$this->assertTrue($r == true, '根据条件删除频道地区关联信息测试不通过');
	}
	
	public function testDelSubChannelByIds(){
		$r = $this->channel->delSubChannelByIds(array(self::$sub_channel_id));
		$tmp = $this->channel->getChannelByIdsFromCache(self::$channel_id, self::$sub_channel_id);
		$this->assertTrue(empty($tmp) == true, '删除子频道信息测试不通过');
	}
	
	public function testDelChannelByIds(){
		$r = $this->channel->delChannelByIds(array(self::$channel_id));
		$tmp = $this->channel->getAllParentChannel(self::$channel_id);
		$this->assertTrue(empty($tmp) == true, '删除主频道信息测试不通过');
	}
}

