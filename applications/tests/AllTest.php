<?php

/**
 * 所有测试套件
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author Su qian <aoxue.1988.su.qian@163.com>
 * @version $Id: AllTest.php 9852 2013-05-09 02:05:14Z hexin $ 
 * @package 
 */
class AllTest {
	
	public static function main(){
		return PHPUnit_TextUI_TestRunner::run(self::suite());
	}
	
	public static function suite(){
		$suite = new PHPUnit_Framework_TestSuite();
		$suite->setName(__CLASS__);
		$suite->addTest(AllServiceSuite::suite());
		//$suite->addTest(AllApiSuite::suite());
		return $suite;
	}
}

