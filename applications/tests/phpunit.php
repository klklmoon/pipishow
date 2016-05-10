<?php
/**
 * etop单元测试入口文件夹
 * @author Su qian <aoxue.1988.su.qian@163.com> $date$
 * @link http://www.yiijob.com
 * @copyright Copyright &copy; 2003-2010 topchoice.com.cn
 * @license
 *

/**
 * @var string 定义程序运行环境为单元测试
 */
define('DEV_ENVIRONMENT','unit');

/**
 * @var string 定义程序访问入口为单元测试
 */
define('ACCESS_ENTRANCE','phpunit');

/**
 * @var string 定义系统
 */
define('SYSTEM_NAME','tests');

//加载Etop脚本启动文件
require_once '../../lib/core/bootStrap.php';

/**
 * 所有入口测试类
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author Su qian <aoxue.1988.su.qian@163.com>
 * @version $Id: phpunit.php 8791 2013-04-18 13:34:11Z hexin $ 
 * @package 
 */
class PipiTest extends PHPUnit_Framework_TestSuite {
	public static function main() {
		PHPUnit_TextUI_TestRunner::run(self::suite());
	}
	
	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite();
		$suite->setName(__CLASS__);
		$suite->addTest(AllTest::suite());
		return $suite;
	}
}

//AllTest::main();


