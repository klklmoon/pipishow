<?php
/**
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z suqian $ 
 * @package
 */
class PipiConsoleCommand extends CConsoleCommand {
	protected $start_time = 0;
	
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
	 * 发送邮件
	 * @param string|array $sendto_email 邮件发送地址
	 * @param string $subject 邮件主题  
	 * @param string $body 邮件正文内容  
	 * @param string|array $attachment 附件
	 * @param string $sendto_name 邮件接受方的姓名，发送方起的名字
	 */
	public function sendMail($sendto_email, $subject, $body='', $attachment=null, $sendto_name=null){
		Yii::import('lib.vendor.phpmailer.*');
		require_once('class.phpmailer.php');
		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->Host 	= Yii::app()->params['smtp']['host'];
		$mail->SMTPAuth = true;
		$mail->Username = Yii::app()->params['smtp']['username'];
		$mail->Password = Yii::app()->params['smtp']['password'];
		$mail->From 	= Yii::app()->params['smtp']['username'];
		$mail->FromName = "pipi letian";
		$mail->CharSet 	= "utf-8";
		$mail->Encoding = "base64";
		
		if(!is_array($sendto_email)) $sendto_email = array($sendto_email);
		foreach($sendto_email as $name => $emailadd){
			if(intval($name) == $name) $name = null;
			$mail->AddAddress($emailadd, $name);
		}
		
		if(!empty($attachment)){
			if(!is_array($attachment)) $attachment = array($attachment);
			foreach($attachment as $att){
				$mail->AddAttachment($att);
			}
		}
	
		$mail->IsHTML(true);
		$mail->Subject = $subject;
		$mail->Body = $body;
		$mail->AltBody ="text/html";
		$return = $mail->Send();
		if($mail->IsError()){
			echo date("Y-m-d H:i:s").": ".$mail->ErrorInfo."\n";
		}
		return $return;
	}
	
	public function beforeAction($action,$params){
		$this->start_time = microtime(true);
		return true;	
	}
	
	public function afterAction($action, $params,$exitCode = 0){
		if($this->start_time > 0){
			$end_time = microtime(true);
			$sec = round(($end_time-$this->start_time), 4);
			$d = floor($sec/86400);
			$h = floor($sec%86400/3600);
			$m = floor($sec%86400%3600/60);
			$s = bcsub(bcsub(bcsub($sec, $d*86400, 4), $h*3600, 4), $m*60, 4);
			echo date("Y-m-d H:i:s").' '.ucfirst($this->getName()).'::'.$action.' 脚本运行'.($d > 0 ? $d.'天' : '').$h.'时'.$m.'分'.$s.'秒'."\n\n";
		}
	}
}

?>