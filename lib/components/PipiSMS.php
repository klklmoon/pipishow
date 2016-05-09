<?php
/**
 * 短信平台接口
 * @author leiwei <leiwei@pipi.cn> 2013-11-26
 * @link http://show.pipi.cn
 * @copyright Copyright &copy; 2003-2010 show.pipi.cn
 * @license
 */
class PipiSMS extends CApplicationComponent{
	
	public $srv_ip='219.136.252.188';
	
	public $srv_port = 80;
	
	public  $errno = 0;
    
	public $errstr = '';
	
	public $timeout = 300;
	
	public $validtime=180;
	
	private $SMSConfig=array('userId'=>885653,
						'account'=>'soushi123',
						'password'=>'D8A0C6D76B054960D16966CC2FAF2E6F3F64D3B4',
						'SMSType'=>1);
	private $appConnect;
	
	private $session_id;
	
	private $return;
	
	private $activeid;
	
	public function init(){
		
	}
	
	/**
	 * 登陆短信平台
	 * @return boolean
	 */
	public function login(){
		$params=array('UserId'=>$this->SMSConfig['userId'],
				'Account'=>$this->SMSConfig['account'],
				'Password'=>$this->SMSConfig['password']);
		$post_str=http_build_query($params);
		return $this->httpPost("/LANZGateway/Login.asp", $post_str);
	}
	
	/**
	 * 退出短信平台
	 * @return boolean
	 */
	public function loginOut(){
		$params=array('ActiveID'=>$this->activeid);
		$post_str=http_build_query($params);
		return $this->httpPost("/LANZGateway/Logoff.asp", $post_str);
	}
	
	/**
	 * 获取当前账号可发送的短信数量
	 */
	public function getSMSStock(){
		$params=array('ActiveID'=>$this->activeid);
		$post_str=http_build_query(array_filter($params));
		return $this->httpPost("/LANZGateway/GetSMSStock.asp",$post_str,true);
	}
	
	/**
	 * 发送单条短信
	 * @param int $phone        手机号
	 * @param string $content   短信内容
	 * @param string $sendDate  定时发送的日期
	 * @param string $sendTime  定时发送的时间
	 * @return boolean
	 */
	public function sendSMS($phone,$content,$sendDate='',$sendTime=''){
		if(!$phone||!$content||!is_numeric($phone)||strlen($content)>150){
			return false;
		}
		$content=iconv('UTF-8','gb2312',$content);
		$params=array('SMSType'=>$this->SMSConfig['SMSType'],
					'Phone'=>$phone,
					'Content'=>$content,
					'ActiveID'=>$this->activeid,
					'SendDate'=>$sendDate,
					'SendTime'=>$sendTime);
		$post_str=http_build_query(array_filter($params));
		if($this->httpPost("/LANZGateway/SendSMS.asp", $post_str)){
			$this->loginOut();
		}
		return false;
	}
	
	
	
	/**
	 * 申请群发短信
	 * @param string $content   群发短信内容
	 * @param string $sendDate  定时发送的日期
	 * @param string $sendTime  定时发送的时间
	 * @return string
	 */
	public function messSMSQuery($content,$sendDate='',$sendTime=''){
		$content=iconv('UTF-8','gb2312',$content);
		$params=array('ActiveID'=>$this->activeid,
					'SMSType'=>$this->SMSConfig['SMSType'],
					'Content'=>$content,
					'SendDate'=>$sendDate,
					'SendTime'=>$sendTime);
		$post_str=http_build_query(array_filter($params));
		$result=$this->httpPost("/LANZGateway/MessSMSQuery.asp", $post_str,true);
		$jobId='';
		if( substr( $result,strpos($result,"<ErrorNum>")+10,strpos($result,"</ErrorNum>") -strpos($result,"<ErrorNum>")-10) ==0){
			$jobId=substr( $result,strpos($result,"<JobID>")+7,strpos($result,"</JobID>") -strpos($result,"<JobID>")-7);
		}
		return $jobId;
	}
	
	/**
	 * 发送群发短信
	 * @param array $phones 群发手机号码
	 * @param string $content 群发内容
	 * @param string $sendDate  定时发送的日期
	 * @param string $sendTime  定时发送的时间
	 * @return boolean
	 */
	public function sendMessSMS(array $phones,$content,$sendDate='',$sendTime=''){
		$jobId=$this->messSMSQuery($content,$sendDate,$sendTime);
		if($jobId){
			$phones=implode(';',$phones);
			$params=array('ActiveID'=>$this->activeid,
				'JobID'=>$jobId,
				'Phones'=>$phones);
			$post_str=http_build_query(array_filter($params));
			if($this->httpPost("/LANZGateway/SendMessSMS.asp", $post_str)){
				$this->loginOut();
			}
		}
		return false;
	}
	
	
	/**
	 * 用于网站直接发送短信，无需登录
	 * @param array $phones 手机号
	 * @param string $content 群发内容
	 * @param string $sendDate  定时发送的日期
	 * @param string $sendTime  定时发送的时间
	 * @return string
	 */
	public function directSendSMSs(array $phones,$content,$sendDate='',$sendTime=''){
		$content=iconv('UTF-8','gb2312',$content);
		$phones=implode(';',$phones);
		$params=array('UserID'=>$this->SMSConfig['userId'],
			'Account'=>$this->SMSConfig['account'],
			'Password'=>$this->SMSConfig['password'],
			'SMSType'=>$this->SMSConfig['SMSType'],
			'Content'=>$content,
			'Phones'=>$phones,
			'SendDate'=>$sendDate,
			'SendTime'=>$sendTime);
		$post_str=http_build_query(array_filter($params));
		return $this->httpPost("/LANZGateway/DirectSendSMSs.asp", $post_str,true);
	}
	
	public function getSMSCode($num=5){
		$numArr = array(0,1,2,3,4,5,6,7,8,9);
		$code = array_rand($numArr,$num);
		$code = implode('',$code);
		$_SESSION['phone_code']=md5($code);
		$_SESSION['code_validtime']=time()+$this->validtime;
		return $code;
	}
	
	/**
	 * 验证手机验证码正确与否
	 * @param string $code 
	 * @return boolean
	 */
	public function validSMSCode($code){
		if(isset($_SESSION['phone_code'])){
			if(md5($code)==$_SESSION['phone_code']&&time()<$_SESSION['code_validtime']){
				return true;
			}
		}
		return false;
	}
	
	private function httpPost($url,$post_str,$isJobId=false){
		$this->appConnect = fsockopen($this->srv_ip,$this->srv_port,$this->errno,$this->errstr,$this->timeout);
		if(!$this->appConnect){
			return 'Connection failed';
		}
		$content_length = strlen($post_str);
		$post_header = "POST $url HTTP/1.1\r\n";
		$post_header .= "Content-Type:application/x-www-form-urlencoded\r\n";
		$post_header .= "User-Agent: MSIE\r\n";
		$post_header .= "Host: ".$this->srv_ip."\r\n";
		$post_header .= "Cookie: ".$this->session_id."\r\n";
		$post_header .= "Content-Length: ".$content_length."\r\n";
		$post_header .= "Connection: close\r\n\r\n";
		$post_header .= $post_str."\r\n\r\n";
		fwrite($this->appConnect,$post_header);
		$inheader = 1;
		$resp_str='';
		while(!feof($this->appConnect)){
			$resp_str .= fgets($this->appConnect,4096);
			if ($inheader && ($resp_str == "\n" || $resp_str == "\r\n")){
				$inheader = 0;
			}
			if ($inheader == 0) {
				$resp_str;
			}
		}
		//echo $resp_str;
		fclose($this->appConnect);
		if(!$this->session_id){
			$this->session_id=substr( $resp_str,strpos($resp_str,"Set-Cookie: ")+12,45);
			if( substr( $resp_str,strpos($resp_str,"<ErrorNum>")+10,strpos($resp_str,"</ErrorNum>") -strpos($resp_str,"<ErrorNum>")-10) ==0){
				$this->activeid = substr( $resp_str,strpos($resp_str,"<ActiveID>")+10,strpos($resp_str,"</ActiveID>") -strpos($resp_str,"<ActiveID>")-10);
			}
		}
		if(substr( $resp_str,strpos($resp_str,"<ErrorNum>")+10,strpos($resp_str,"</ErrorNum>") -strpos($resp_str,"<ErrorNum>")-10)==0){
			if($isJobId){
				return substr( $resp_str,strpos($resp_str,"<JobID>")+7,strpos($resp_str,"</JobID>") -strpos($resp_str,"<JobID>")-7);
			}
			return substr( $resp_str,strpos($resp_str,"<ErrorNum>")+10,strpos($resp_str,"</ErrorNum>") -strpos($resp_str,"<ErrorNum>")-10);
		}
		return false;
	}
	
}