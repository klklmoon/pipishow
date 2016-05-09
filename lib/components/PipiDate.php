<?php
/**
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z suqian $ 
 * @package
 */
class PipiDate extends CApplicationComponent{

	/**

	 * 获取时区
	 * 

	 * @return string

	 */
	public static function getTimeZone() {
		return function_exists('date_default_timezone_get') ? date_default_timezone_get() : date('e');
	}

	/**

	 * 设置时区
	 * 

	 * @param string $timezone 时区

	 */
	public static function setTimezone($timezone) {
		function_exists('date_default_timezone_set') ? date_default_timezone_set($timezone) : putenv("TZ={$timezone}");
	}

	/**

	 * 格式化输出
	 * 

	 * @param string $format 目标格式,默认为null则以Y-m-d H:i:s格式输出

	 * @param int $dateTime unix时间戳，默认为null则用当前时间

	 * @return string

	 */
	public static function format($format = null, $dateTime = null) {
		return date($format ? $format : 'Y-m-d H:i', self::getTimeStamp($dateTime));
	}

	/**

	 * 获取日期的某部分
	 * 

	 * @param string $interval 字符串表达式 ,时间间隔类型

	 * @param mixed $dateTime 表示日期的文字，默认为null则用当前时间

	 * @return string 返回日期的某部分

	 */
	public static function datePart($interval, $dateTime = null) {
		return date($interval, self::getTimeStamp($dateTime));
	}

	/**

	 * 获取两个日期的差
	 * 

	 * @param string $interval 返回两个日期差的间隔类型

	 * @param mixed $startDateTime 开始日期

	 * @param mixed $endDateTime   结束日期

	 * @return string 

	 */
	public static function dateDiff($interval, $startDateTime, $endDateTime) {
		$diff = self::getTimeStamp($endDateTime) - self::getTimeStamp($startDateTime);
		$retval = 0;
		switch ($interval) {
			case "y":
				$retval = bcdiv($diff, (60 * 60 * 24 * 365));
				break;
			case "m":
				$retval = bcdiv($diff, (60 * 60 * 24 * 30));
				break;
			case "w":
				$retval = bcdiv($diff, (60 * 60 * 24 * 7));
				break;
			case "d":
				$retval = bcdiv($diff, (60 * 60 * 24));
				break;
			case "h":
				$retval = bcdiv($diff, (60 * 60));
				break;
			case "n":
				$retval = bcdiv($diff, 60);
				break;
			case "s":
			default:
				$retval = $diff;
				break;
		}
		return $retval;
	}

	/**

	 * 返回向指定日期追加指定间隔类型的一段时间间隔后的日期  
	 * 

	 * @param string $interval 字符串表达式，是所要加上去的时间间隔类型。

	 * @param int $value 数值表达式，是要加上的时间间隔的数目。其数值可以为正数（得到未来的日期），也可以为负数（得到过去的日期）。 

	 * @param string $dateTime 表示日期的文字，这一日期还加上了时间间隔。 

	 * @param mixed $format 格式化输出

	 * @return string 返回追加后的时间

	 */
	public static function dateAdd($interval, $value, $dateTime, $format = null) {
		$date = getdate(self::getTimeStamp($dateTime));
		switch ($interval) {
			case "y":
				$date["year"] += $value;
				break;
			case "q":
				$date["mon"] += ($value * 3);
				break;
			case "m":
				$date["mon"] += $value;
				break;
			case "w":
				$date["mday"] += ($value * 7);
				break;
			case "d":
				$date["mday"] += $value;
				break;
			case "h":
				$date["hours"] += $value;
				break;
			case "n":
				$date["minutes"] += $value;
				break;
			case "s":
			default:
				$date["seconds"] += $value;
				break;
		}
		return self::format($format, mktime($date["hours"], $date["minutes"], $date["seconds"], $date["mon"], $date["mday"], $date["year"]));
	}

	/**

	 * 得到一年中每个月真实的天数
	 * 

	 * @param string $year 需要获得的月份天数的年份

	 * @return array 每月的天数组成的数组

	 */
	public static function getRealDaysInMonthsOfYear($year) {
		$months = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		if (self::isLeapYear($year)) {
			$months[1] = 29;
		}
		return $months;
	}

	/**

	 * 获取该月的天数
	 * 

	 * @param int $month 月份

	 * @param int $year 年份

	 * @return int

	 */
	public static function getDaysInMonth($month, $year) {
		if (1 > $month || 12 < $month) {
			return 0;
		}
		if (!($daysInmonths = self::getRealDaysInMonthsOfYear($year))) {
			return 0;
		}
		return $daysInmonths[$month - 1];
	}

	/**

	 * 获取该年的天数 
	 * 

	 * @return int 

	 */
	public static function getDaysInYear($year) {
		return self::isLeapYear($year) ? 366 : 365;
	}

	/**

	 * 取得RFC格式的日期与时间
	 * 
	 * @param string $data 需要获取的时间,默认为null则获取当前时间

	 * @return string

	 */
	public static function getRFCDate($date = null) {
		$time = $date ? is_int($date) ? $date : strtotime($date) : time();
		$tz = date('Z', $time);
		$tzs = ($tz < 0) ? '-' : '+';
		$tz = abs($tz);
		$tz = (int) ($tz / 3600) * 100 + ($tz % 3600) / 60;
		return sprintf("%s %s%04d", date('D, j M Y H:i:s', $time), $tzs, $tz);
	}

	/**

	 * 取得中国日期时间
	 * 

	 * @param int $time 需要使用的时间戳,默认为null则获取当前时间戳 

	 * @return string

	 */
	public static function getChinaDate($time = null) {
		list($y, $m, $d, $w, $h, $_h, $i) = explode(' ', date('Y n j w G g i', $time ? $time : time()));
		return sprintf('%s年%s月%s日(%s) %s%s:%s', $y, $m, $d, self::getChinaWeek($w), self::getPeriodOfTime($h), $_h, $i);
	}

	/**

	 * 取得中国的星期
	 * 

	 * @param int $week 处国人的星期，是一个数值，默认为null则使用当前时间

	 * @return string

	 */
	public static function getChinaWeek($week = null) {
		$week = $week ? $week : (int) date('w', time());
		$weekMap = array("星期天", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六");
		return $weekMap[$week];
	}

	/**

	 * 取得一天中的时段
	 * 

	 * @param int $hour 小时，默认为null则获取当前时间

	 * @return string

	 */
	public static function getPeriodOfTime($hour = null) {
		$hour = $hour ? $hour : (int) date('G', time());
		$period = '';
		if (0 <= $hour && 6 > $hour) {
			$period = '凌晨';
		} elseif (6 <= $hour && 8 > $hour) {
			$period = '早上';
		} elseif (8 <= $hour && 11 > $hour) {
			$period = '上午';
		} elseif (11 <= $hour && 13 > $hour) {
			$period = '中午';
		} elseif (13 <= $hour && 15 > $hour) {
			$period = '响午';
		} elseif (15 <= $hour && 18 > $hour) {
			$period = '下午';
		} elseif (18 <= $hour && 20 > $hour) {
			$period = '傍晚';
		} elseif (20 <= $hour && 22 > $hour) {
			$period = '晚上';
		} elseif (22 <= $hour && 23 >= $hour) {
			$period = '深夜';
		}
		return $period;
	}

	/**

	 * 获取UTC日期格式
	 * 

	 * @param mixed $dateTime 时间，默认为null则获取当前时间

	 * @return string

	 */
	public static function getUTCDate($dateTime = null) {
		$oldTimezone = self::getTimezone();
		if ('UTC' !== strtoupper($oldTimezone)) {
			self::setTimezone('UTC');
		}
		$date = date('D, d M y H:i:s e', self::getTimeStamp($dateTime));
		if ('UTC' !== strtoupper($oldTimezone)) {
			self::setTimezone($oldTimezone);
		}
		return $date;
	}

	/**

	 * 获取微秒数
	 * 
	 * @param string $mircrotime  微妙时间，默认为null则获取当前时间
	 * @param string $get_as_float 获取微妙时间是否以浮点数返回,默认为false即不以浮点数方式返回

	 * @return int

	 */
	public static function getMicroTime($mircrotime = null, $get_as_float = false) {
		return array_sum(explode(' ', $mircrotime ? $mircrotime : microtime($get_as_float)));
	}

	/**

	 * 判断是否是闰年
	 * 

	 * @param int $year 需要判断的年份

	 * @return boolean 如果是润年则返回true

	 */
	public static function isLeapYear($year) {
		return (0 == $year % 4 && 0 != $year % 100 || 0 == $year % 400);
	}

	/**
	 * 获得时间戳
	 *
	 * @param int $dateTime 时间戳,默认为null则以当前时间戳返回
	 * @return int
	 */
	public static function getTimeStamp($dateTime = null) {
		return $dateTime ? is_int($dateTime) ? $dateTime : strtotime($dateTime) : time();
	}

	/**
	 * 比较两个时间返回离现在最近的一个时间
	 * 

	 * @param int $time 比较的时间戳

	 * @param int $timestamp 当前时间戳,默认为null则获取当前时间戳 $timestamp >= $time

	 * @param string $format 格式化当前时间戳,默认为null则转化为格式Y-m-d H:i:s

	 * @param array $type 要返回的时间类型，默认为 1则只返回Y-m-d否则返回Y-m-d m-d H:i

	 * @return array

	 */
	public static function getLastDate($time, $timestamp = null, $format = null, $type = 1) {
		$timelang = array('second' => '秒前', 'yesterday' => '昨天', 'hour' => '小时', 'minute' => '分钟前', 'qiantian' => '前天');
		$timestamp = $timestamp ? $timestamp : time();
		$currentTime = strtotime(self::format('Y-m-d', $timestamp));
		$compareTime = strtotime(self::format('Y-m-d', $time));
		$decrease = $timestamp - $time;
		$result = self::format($format, $time);
		if (0 >= $decrease) {
			return array(self::format('Y-m-d H:i', $time), $result);
		}
		if ($currentTime == $compareTime) {
			if (1 == $type) {
				if (60 >= $decrease) {
					return array($decrease . $timelang['second'], $result);
				}
				if(3600 >= $decrease){
					return array(ceil($decrease / 60) . $timelang['minute'], $result);
				}else{
					$hours = floor($decrease / 3600);
					$minitues = ceil(($decrease - $hours*3600) / 60);
					return array(
						$hours . $timelang['hour'].$minitues.$timelang['minute'], $result
					 );
				}
				
			}
			return array(self::format('H:i', $time), $result);
		} elseif ($currentTime-86400 == $compareTime) {
			return 1 == $type ? array($timelang['yesterday'] . " " . self::format('H:i', $time), $result) : array(
				self::format('m-d H:i', $time), $result);
		} elseif ($currentTime-172800 == $compareTime) {
			return 1 == $type ? array($timelang['qiantian'] . " " . self::format('H:i', $time), $result) : array(
				self::format('m-d H:i', $time), $result);
		} elseif (strtotime(self::format('Y', $time)) == strtotime(self::format('Y', $timestamp))) {
			return 1 == $type ? array(self::format('m-d', $time), $result) : array(self::format('m-d H:i', $time), 
				$result);
		}
		return 1 == $type ? array(self::format('Y-m-d', $time), $result) : array(self::format('Y-m-d H:i', $time), 
			$result);
	}
	
	/**
	 * 比较两个时间返回离现在最近的一个时间
	 * 

	 * @param int $time 比较的时间戳,一般时数据库里提取

	 * @param int $timestamp  当前时间戳 ,,默认为null则获取当前时间戳  $time >= $timestamp

	 * @param string $format 格式化当前时间戳,默认为null则转化为格式Y-m-d H:i:s

	 * @param array $type 要返回的时间类型，默认为 1则只返回Y-m-d否则返回Y-m-d m-d H:i

	 * @return array

	 */
	public static function getFurtureDate($time, $timestamp = null, $format = null, $type = 1){
		$timelang = array('second' => '秒后','today'=>'今天', 'tommorrow' => '明天', 'hour' => '小时', 'minute' => '分钟后', 'houtian' => '后天');
		$timestamp = $timestamp ? $timestamp : time();
		$currentTime = strtotime(self::format('Y-m-d', $timestamp));
		$compareTime = strtotime(self::format('Y-m-d', $time));
		$decrease =  $time-$timestamp;
		$result = self::format($format, $time);
		if (0 >= $decrease) {
			return  array(self::format('H:i', $time), $result);
		}
		if ($currentTime == $compareTime) {
			if (1 == $type) {
				if (60 >= $decrease) {
					return array($decrease . $timelang['second'], $result);
				}
				if(3600 >= $decrease){
					return array(ceil($decrease / 60) . $timelang['minute'], $result);
				}else{
					if(3600*2 >= $decrease){
						$hours = floor($decrease / 3600);
						$minitues = ceil(($decrease - $hours*3600) / 60);
						return array(
							$hours . $timelang['hour'].$minitues.$timelang['minute'], $result
						 );
					}else{
						return array(
							 $timelang['today'].' '.date('H:i',$time),$result
						 );
					}
				}
				
			}
			return array(self::format('H:i', $time), $result);
		} elseif ($compareTime-86400 == $currentTime) {
			return 1 == $type ? array($timelang['tommorrow'] . " " . self::format('H:i', $time), $result) : array(
				self::format('m-d H:i', $time), $result);
		} elseif ($compareTime-172800 == $currentTime) {
			return 1 == $type ? array($timelang['houtian'] . " " . self::format('H:i', $time), $result) : array(
				self::format('m-d H:i', $time), $result);
		} elseif (strtotime(self::format('Y', $time)) == strtotime(self::format('Y', $timestamp))) {
			return 1 == $type ? array(self::format('m-d', $time), $result) : array(self::format('m-d H:i', $time), 
				$result);
		}
		return 1 == $type ? array(self::format('Y-m-d', $time), $result) : array(self::format('Y-m-d H:i', $time), 
			$result);
	}
	
	/**
	 * 测试时间是否在特定的时间范围内
	 *
	 * @param int $currentTime 测试基准时间，一般为当前时间
	 * @param int $testTime  要测试的时间
	 * @param bool $direction 时间测试方向，ture为向后推，false为向前推
	 * @param int $DaysRange 测试天数，默认为当天
	 * @param bool $isTimeStamp 时间格式，ture为时间戳，false为时间字符串（Y-m-d H:i:s）	
	 * @return bool
	 */	
	public static function checkTimeRange($currentTime,$testTime,$direction=true,$DaysRange=0,$isTimeStamp=true)
	{
		if(!$isTimeStamp)
		{
			$currentTime=strtotime($currentTime);
			$testTime=strtotime($testTime);
		}
		
		if($direction)
		{
			$timeStampStart=strtotime(date("Y-m-d 00:00:00",$currentTime));
			$timeStampEnd=strtotime(date("Y-m-d 23:59:59",$currentTime))+86400*$DaysRange;
		}
		else
		{
			$timeStampStart=strtotime(date("Y-m-d 00:00:00",$currentTime))-86400*$DaysRange;
			$timeStampEnd=strtotime(date("Y-m-d 23:59:59",$currentTime));
		}
			
		return $testTime>=$timeStampStart && $testTime<=$timeStampEnd;
	}
}