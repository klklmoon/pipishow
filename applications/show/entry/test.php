<?php
pushNotice();
function pushNotice(){
		$deviceToken= '71aca2ba466a8bc97231987f917a77ce68890f217007aca5117a090214dcf918'; //没有空格
		$body = array("aps" => array("alert" => 'hello word',"badge" => 2,"sound"=>'default'));  //推送方式，包含内容和声音
		$ctx = stream_context_create();
		$certPath=dirname(dirname(dirname(dirname(__FILE__)))).'/statics/cert/ck.pem';
		stream_context_set_option($ctx,"ssl","local_cert",$certPath);
		stream_context_set_option($ctx, 'ssl', 'passphrase', 'letian');
		//$fp = stream_socket_client("ssl://gateway.push.apple.com:2195", $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
		$fp = stream_socket_client("ssl://gateway.sandbox.push.apple.com:2195", $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
		if (!$fp) {
			echo "Failed to connect $err $errstrn";
			return;
		}
		print "Connection OK\n";
		$payload = json_encode($body);
		$msg = chr(0) . pack("n",32) . pack("H*", str_replace(' ', '', $deviceToken)) . pack("n",strlen($payload)) . $payload;
		echo "sending message :" . $payload ."\n";
		echo fwrite($fp, $msg);
		fclose($fp);
		
	}