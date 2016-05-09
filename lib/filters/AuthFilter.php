<?php
/**
 * @author Su qian <aoxue.1988.su.qian@163.com> 2010-11-2
 * @link http://show.pipi.cn
 * @copyright Copyright &copy; 2003-2013 show.pipi.cn
 * @license
 */

class AuthFilter extends PipiFilter{
	
	public $time = 1;
	public function preFilter($filterChain){
		echo 'pre';
		echo $this->time;
		return true;
	}
	
	public function postFilter($filterChain){
		echo 'post';
	}
}