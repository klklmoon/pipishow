<?php
 session_start();
 $_SESSION["session_id"] = " ";
 $_SESSION["ReturnString"] = " ";
 $_SESSION["activeid"] = " ";
function   httpPost($sURL,$aPostVars,$sessid,$nMaxReturn)
{ 
  $srv_ip = '219.136.252.188';//你的目标服务地址或频道.
  $srv_port = 80;
  $url = $sURL; //接收你post的URL具体地址 
  $fp = '';
  $resp_str = '';
  $errno = 0;
  $errstr = '';
  $timeout = 300;
  $post_str = $aPostVars;//要提交的内容.
    
  $fp = fsockopen($srv_ip,$srv_port,$errno,$errstr,$timeout);
  if (!$fp)
  {
   echo('fp fail');
  }
  
  $content_length = strlen($post_str);
  $post_header = "POST $url HTTP/1.1\r\n";
  $post_header .= "Content-Type:application/x-www-form-urlencoded\r\n";
  $post_header .= "User-Agent: MSIE\r\n";
  $post_header .= "Host: ".$srv_ip."\r\n";
  $post_header .= "Cookie: ".$sessid."\r\n";
  $post_header .= "Content-Length: ".$content_length."\r\n";
  $post_header .= "Connection: close\r\n\r\n";
  $post_header .= $post_str."\r\n\r\n";
  
  //echo $post_header;
  fwrite($fp,$post_header);

  $inheader = 1;
  while(!feof($fp)){
  	echo $resp_str .= fgets($fp,4096);//返回值放入$resp_str
   if ($inheader && ($resp_str == "\n" || $resp_str == "\r\n")){        
	  $inheader = 0;     
	}     
	if ($inheader == 0) {       
	  $resp_str;     
	} 
  } 
   
 //echo $resp_str; 
  echo "\r\n";
  
  if($nMaxReturn==0)
  {
     $_SESSION["session_id"] = substr( $resp_str,strpos($resp_str,"Set-Cookie: ")+12,45);
	 //echo $_SESSION["session_id"];
	 if( substr( $resp_str,strpos($resp_str,"<ErrorNum>")+10,strpos($resp_str,"</ErrorNum>") -strpos($resp_str,"<ErrorNum>")-10) ==0)
	 {
		$_SESSION["activeid"] = substr( $resp_str,strpos($resp_str,"<ActiveID>")+10,strpos($resp_str,"</ActiveID>") -strpos($resp_str,"<ActiveID>")-10);
	 }

  }
  else
  {
     if( substr( $resp_str,strpos($resp_str,"<ErrorNum>")+10,strpos($resp_str,"</ErrorNum>") -strpos($resp_str,"<ErrorNum>")-10) ==0)
	 {
              echo "\r\n";
		echo "操作成功";
	 }
	 else
	 {
	     echo "\r\n";
		 echo substr( $resp_str,strpos($resp_str,"<ErrorNum>")+10,strpos($resp_str,"</ErrorNum>") -strpos($resp_str,"<ErrorNum>")-10);//处理返回值.
		 $_SESSION["ReturnString"] = substr( $resp_str,strpos($resp_str,"<ErrorNum>")+10,strpos($resp_str,"</ErrorNum>") -strpos($resp_str,"<ErrorNum>")-10);
	 } 
  }  
   fclose($fp);
 }

  httpPost( "/LANZGateway/Login.asp","UserID=995050&Account=hongri&Password=8A38231F964A73D71277EEF0893F9FCB3700B8B5","",0); 

//   $phone=array('丁建新'=>13858178796,'阮征'=>15868473894);
   $content=urlencode(iconv('UTF-8','gb2312','您好，恭喜你成为皮皮乐天超级色狼用户，乐天的主播今晚随你挑，陪你共度良宵'));
//   //echo $_SESSION["session_id"].'\r\n';
//   foreach($phone as $key=>$row){
//   	httpPost( "/LANZGateway/SendSMS.asp","SMSType=1&Phone=".$row."&Content=".$content."&ActiveID=".$_SESSION["activeid"],$_SESSION["session_id"],1);
//   	echo $key."短信发送成功";
//   }	
  httpPost("/LANZGateway/GetSMSStock.asp","ActiveID=".$_SESSION["activeid"],$_SESSION["session_id"],2);
 httpPost( "/LANZGateway/Logoff.asp","ActiveID=".$_SESSION["activeid"],$_SESSION["session_id"],2); 
 //httpPost( "/LANZGateway/DirectSendSMSs.asp","UserID=995050&Account=hongri&Password=8A38231F964A73D71277EEF0893F9FCB3700B8B5&SMSType=1&Content=".$content."&Phones=15868473894;13858178796",'',0);
?> 