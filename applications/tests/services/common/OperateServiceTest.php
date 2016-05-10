<?php
/**
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: OperateServiceTest.php 9496 2013-05-02 13:50:44Z hexin $ 
 * @package
 */
class OperateServiceTest extends BaseTest {

	protected $operate;
	
	public function __construct() {
		$this->operate = new OperateService();
	}
	
	public function testSaveOperate(){
		$operate = array(
			'category'		=> CATEGORY_COMMON,
			'sub_category'	=> CATEGORY_INDEX_ACTIVITYRECOMMAND,
			'subject'		=> 'o_'.uniqid(),
			'content'		=> array(1, 2),
		);
		$r = $this->operate->saveOperate($operate);
		$this->assertTrue($r == true, '存储运营数据测试不通过');
		
		$operates = $this->operate->getAllOperateFromCache();
		$operates_db = $this->operate->getAllOperate();
		$this->assertSame($operates, $operates_db, '取所有的缓存运营数据和取所有的数据库中的运营数据不一致');
		
		$tmp = array_pop(array_pop(array_pop($operates)));
		$this->assertTrue($tmp['subject'] == $operate['subject'], '添加的运营数据不正确');
		
		$operate['operate_id'] = $tmp['operate_id'];
		$operate['subject']	  .= '_t';
		$r = $this->operate->saveOperate($operate);
		
		$operates = $this->operate->getOperateByCategoryFromCache(CATEGORY_COMMON, CATEGORY_INDEX_ACTIVITYRECOMMAND);
		$operates_db = $this->operate->getOperateByCategory(CATEGORY_COMMON, CATEGORY_INDEX_ACTIVITYRECOMMAND);
		$this->assertSame($operates, $operates_db, '取指定分类下所有的缓存运营数据和取指定分类下所有的数据库中的运营数据不一致');
		
		$tmp = array_pop($operates);
		$this->assertTrue($tmp['subject'] == $operate['subject'], '修改的运营数据不正确');
		
		return $tmp['operate_id'];
	}
	
	/**
	 * 
	 * @depends testSaveOperate
	 */
	public function testDelOperateByOperateIds($id){
		$r = $this->operate->delOperateByOperateIds(array($id));
		$operates = $this->operate->getAllOperateFromCache();
		$this->assertTrue($r == true && empty($operates), '删除运营数据测试不通过');
	}
}

