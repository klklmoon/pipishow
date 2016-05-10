<?php

require_once ('PHPUnit/Framework/TestCase.php');

/**
 * 皮皮乐天所有单元测试套间基类
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <aoxue.1988.su.qian@163.com>
 * @version $Id: BaseTestSuite.php 8317 2013-03-29 01:19:47Z suqian $ 
 * @package 
 */
class BaseTestSuite extends PHPUnit_Framework_TestSuite {
	
	public function __construct(){
		$this->setName(get_called_class());
	}

}

