<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author Su qian <aoxue.1988.su.qian@163.com>
 * @version $Id: IndustyServiceTest.php 8751 2013-04-17 09:01:50Z hexin $ 
 * @package 
 */
class IndustyServiceTest extends BaseTest{

	public function testAddIndustry(){
		
	}
	
	public function testCheckData(){
		
	}
	
	public function testTarea(){
		$this->fail('aaa');
	}
	
	public function testBb(){
		$a = new A();
		$b= $a->bb(3,4);
		echo $b;
		$this->assertTrue(7 == $b,'测试通过了');
		
	}
}


class A{
	public function bb($a,$b){
		return $a+$b;
	}
}