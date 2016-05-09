<?php
/**
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z suqian $ 
 * @package
 */
class PipiDateCal extends CApplicationComponent{
	/**
	 * 取得本周的开始与结束时间
	 * @return array
	 */
	public function getThisWeekTime(){
		return $this->pushDownWeekTime(0,false);
	}
	/**
	 * 取得上周的开始与结束时间
	 * @return array
	 */
	public function getLastWeekTime(){
		return $this->pushDownWeekTime(1,false);
	}
	/**
	 * 推算第几周
	 * @param int $week 第几周
	 * @param in $plus 推算方向 false表示向后推 true表示向前推
	 * @return array
	 */
	public function pushDownWeekTime($week = 1,$plus = false){
		$weekDays = date('N');
		$pushTime = ($week*1)*7*24*3600;
		if($plus)
			$timeStamp = time() + $pushTime;
		else
			$timeStamp = time() - $pushTime;
			
		$startTime = $timeStamp-($weekDays-1)*3600*24;
		$startTime = strtotime(date('Y-m-d',$startTime).' 00:00:00');
		$endTime = $timeStamp+(7-$weekDays)*3600*24;
		$endTime =  strtotime(date('Y-m-d',$endTime).' 23:59:59');
		return array($startTime,$endTime);
	}
	
	/**
	 * 推算第几月
	 * @param int $month 第几月
	 * @param in $plus 推算方向 false表示向后推 true表示向前推
	 * @return array
	 */
	public function pushDownMonthTime($month,$plus =false){
		if(!$month){
			$timeStamp = time();
		}else{
			if($month < 0) $month = 12 + $month;
			if($plus){
				$timeStamp = strtotime(date('Y-m-d',strtotime("+{$month} month")));
			}else{
				$timeStamp = strtotime(date('Y-m-d',strtotime("-{$month} month")));
			}
		}
		$monthDays = date('j',$timeStamp);//本月中的 第几天
		$localMonthDays = date('t',$timeStamp);//当前月总共有多少天
		$remainderDays = $localMonthDays-$monthDays;//本月剩余天数
		
		$startTime = $timeStamp-($monthDays-1)*3600*24;
		$startTime = strtotime(date('Y-m-d',$startTime).' 00:00:00');
		$endTime = $timeStamp+($remainderDays)*3600*24;
		$endTime =  strtotime(date('Y-m-d',$endTime).' 23:59:59');
		return array($startTime,$endTime);
	}
	
	/**
	 * 推算第几天
	 * @param int $day 第几天
	 * @param in $plus 推算方向 false表示向后推 true表示向前推
	 * @return array
	 */
	public function pushDownDaysTime($day ,$plus = false){
		if(!$day){
			$timeStamp = time();
		}else{
			if($plus){
				$timeStamp = strtotime(date('Y-m-d',strtotime("+{$day} day")));
			}else{
				$timeStamp = strtotime(date('Y-m-d',strtotime("-{$day} day")));
			}
		}
		$startTime = strtotime(date('Y-m-d',$timeStamp).' 00:00:00');
		$endTime =  strtotime(date('Y-m-d',$timeStamp).' 23:59:59');
		return array($startTime,$endTime);
	}
	
	/**
	 * 获取当月之后的共12个月份列表
	 * 
	 * @return array
	 */
	public function getCurrentYearPrevMonth($fix = false){
		$list = array();
		$list[date('Y-m')] = date('Y年m月');
		if($fix) $cMonth = 12;
		else $cMonth = date('n');
		for($i = 1;$i< $cMonth;$i++){
			list($startTime,$endTime) = $this->pushDownMonthTime($i,false);
			$list[date('Y-m',$startTime)] = date('Y年m月',$startTime);
		}
		return $list;
	}
	
	/**
	 * 取得相对当前时间 间隔月份的开始，结束时间缀
	 * @param string $month 月份 格式如2013－07
	 * @return array
	 */
	public function getCurPointMonthTime($month){
		$monthTime = strtotime($month);
		$timeStamp = time();
		if($monthTime > $timeStamp){
			return array();
		}
		$m = date('n',$monthTime);
		$cm = date('n',$timeStamp);
		return $this->pushDownMonthTime($cm-$m,false);
	}
}