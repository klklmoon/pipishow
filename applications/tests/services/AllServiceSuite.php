<?php
/**
 * 皮皮乐天所有服务层测试套件
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author Su qian <aoxue.1988.su.qian@163.com>
 * @version $Id: AllServiceSuite.php 9496 2013-05-02 13:50:44Z hexin $ 
 * @package 
 */
class AllServiceSuite extends BaseTestSuite {
	/**
	 * Enter description here ...
	 * @return phpunit_framework_testsuite
	 */
	public static function suite() {
		$suite = new self();
		$suite->addTestSuite('AppServiceTest');
		$suite->addTestSuite('ArchivesServiceTest');
		$suite->addTestSuite('BbsBaseServiceTest');
// 		$suite->addTestSuite('IndustyServiceTest');
		$suite->addTestSuite('ConsumeServiceTest');
// 		$suite->addTestSuite('ParseServiceTest');
// 		$suite->addTestSuite('TagServiceTest');
		$suite->addTestSuite('OperateServiceTest');
		$suite->addTestSuite('DoteyServiceTest');
		$suite->addTestSuite('DoteySongServiceTest');
		$suite->addTestSuite('ChannelServiceTest');
		$suite->addTestSuite('GiftBagServiceTest');
		$suite->addTestSuite('GiftServiceTest');
// 		$suite->addTestSuite('MessageServiceTest');
		$suite->addTestSuite('PropsServiceTest');
		$suite->addTestSuite('UserBuyPropsServiceTest');
		$suite->addTestSuite('UserPropsServiceTest');
		$suite->addTestSuite('PurviewServiceTest');
		$suite->addTestSuite('UserJsonInfoServiceTest');
		$suite->addTestSuite('UserServiceTest');
// 		$suite->addTestSuite('WeiBoServiceTest');
		return $suite;
	}
}

