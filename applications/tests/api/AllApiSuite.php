<?php
/**
 * 皮皮乐天所有API测试套件
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author Su qian <aoxue.1988.su.qian@163.com>
 * @version $Id: AllApiSuite.php 9549 2013-05-04 02:09:57Z hexin $ 
 * @package 
 */
class AllApiSuite extends BaseTestSuite {
	/**
	 * Enter description here ...
	 * @return phpunit_framework_testsuite
	 */
	public static function suite() {
		$suite = new self();
		$suite->addTestSuite('ArchivesApiTest');
		$suite->addTestSuite('ConsumeApiTest');
// 		$suite->addTestSuite('IndexApiTest');
		$suite->addTestSuite('TokenApiTest');
		$suite->addTestSuite('UserApiTest');
		return $suite;
	}
}

