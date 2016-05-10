<?php

/**
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z suqian $ 
 * @package
 */
class TestController extends PipiController {

	public function actions() {
		return array('run' => 'lib.actions.ajaxs.ajaxAction', 'filter' => 'lib.actions.ajaxs.ajaxAction');
	}
	
	/**
	 * 和其他部门的联调下flash用
	 * 这个请不要删除
	 */
	function actionTestFlash(){
		$clientScript = Yii::app()->getClientScript();
		$clientScript->registerScriptFile($this->pipiFrontPath.'/swf/archives/swfobject.js?token='.$this->hash,CClientScript::POS_HEAD);
	
		$this->render('test');
	}
	
	/**
	 * flash上传
	 * 这个请不要删除
	 */
	function actionUpload(){
		$filename=DATA_PATH."runtimes".DIR_SEP."flash.log";
		$file = fopen($fileName,"w");
		fwrite($file, var_export($_REQUEST, true));
		fclose($file);
	}

	public function actionTest(){
		$giftPoolModel=new GiftPoolModel();
		$data=$giftPoolModel->getGiftPoolByValue(500);
		print_r($data);
	}
	
	public function actionIndex() {
		
		$data['action']='dynamic';
		$data['uid']=9888825;
		$data['type']='dotey';
		$data['title']='dfsadfasdfs';
		$data['content']='ffffffffffff';
		$data['open_id']='8BD1CBF12749DA5565065A704869D04C';
		$data['appkey']='show_iphone';
		$data['appsecret']='c558f9d134b957107c0c61e9853e68d3';
		$data['timestamp']=time();
		$data['session_id']='';
		$sign=$this->sign($data);
		$data['sign']=$sign;
		$arr=array();
		foreach($data as $key=>$row){
			$arr[]=$key.'='.$row;
		}
		$string=implode('&',$arr);
		$url='http://show.test.cn/index.php?r=api/index&'.$string;
		
		
		echo "<form action='".$url."' enctype='multipart/form-data' method='POST'>";
		echo "<input name='photo' type='file'/>";
		echo "<input name='submit' type='submit'/>";
		echo "</form>";
		
	}
	
	
	public function actionTest1(){
		$truckGiftService=new TruckGiftService();
        $truckGiftRecord=$truckGiftService->getTruckGiftRecord();
        print_r($truckGiftRecord);
	}

	function sign(array $params){
		$sign=array();
		unset($params['sign']);
		unset($params['PHPSESSID']);
		ksort($params);
	
		foreach($params as $key=>$row){
			$sign[]=$key.'='.$row;
		}
		return md5(urlencode(implode('&',$sign)));
	}
}

?>