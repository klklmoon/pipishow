<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <aoxue.1988.su.qian@163.com>
 * @version $Id: PublicController.php 8317 2013-03-29 01:19:47Z suqian $ 
 * @package api
 * @subpackage consume
 */
class ConsumeController extends PipiApiController {

	/**
	 * 加皮蛋
	 */
	public function actionAddEggsPipiEggs(){
	
		$pipiegg = Yii::app()->request->getParam('pipiegg');
		$source = Yii::app()->request->getParam('source');
		if($pipiegg <= 0 || empty($source) || $this->uid <= 0){
			$this->responseClient('fail',Yii::t('common','Parameter is empty'));
		}
		
		$consumeService = new ConsumeService();
		$filename = DATA_PATH.'runtimes/addEggsPipiEggs_consume.log';
		$jsonString = json_encode($_REQUEST);
		if($consumeService->addEggs($this->uid,$pipiegg)){
			$log['uid'] = $this->uid;
			$log['from_target_id'] = $this->app_id;
			$log['source'] = $this->app['app_enname'];
			$log['sub_source'] = $source;
			$log['pipiegg'] = $pipiegg;
			$log['record_sid'] = Yii::app()->request->getParam('record_sid');
			error_log( date('Y-m-d H:i',time()).' success '.$jsonString."\r\n",3,$filename);
			if($consumeService->saveUserPipiEggRecords($log,1)){
				$newArray = array('uid'=>$this->uid,'pipiegg'=>$pipiegg);
				$consumeService->appendConsumeData($newArray);
				$this->responseClient('success','');
			}else{
				$this->responseClient('fail',$consumeService->getError());
			}
		}else{
			error_log( date('Y-m-d H:i',time()).' failed '.$jsonString."\r\n",3,$filename);
			$this->responseClient('fail',Yii::t('common','Pipiegg add failed'));
		}
		
	}
	
	/**
	 * 消费皮蛋
	 */
	public function actionConsumeEggs(){
		
		$pipiegg = Yii::app()->request->getParam('pipiegg');
		$source = Yii::app()->request->getParam('source');
		if($pipiegg <= 0 || empty($source) || $this->uid <= 0){
			$this->responseClient('fail',Yii::t('common','Parameter is empty'));
		}
		
		$consumeService = new ConsumeService();
		$consume = $consumeService->getConsumesByUids($this->uid);
		if(empty($consume)){
			$this->responseClient('fail',Yii::t('user','The user does not exist'));
		}
		
		$consume = $consume[$this->uid];
		$cbalance = $consume['pipiegg'] - $consume['freeze_pipiegg'] - $pipiegg;
		if($cbalance < 0){
			$this->responseClient('fail',Yii::t('props','You do not have a sufficient share of balance, recharge'));
		}
		
		$filename = DATA_PATH.'runtimes/consumeEggs_consume.log';
		$jsonString = json_encode($_REQUEST);
		if($consumeService->consumeEggs($this->uid,$pipiegg)){
			$log['uid'] = $this->uid;
			$log['from_target_id'] = $this->app_id;
			$log['source'] = $this->app['app_enname'];
			$log['sub_source'] = $source;
			$log['pipiegg'] = $pipiegg;
			$log['record_sid'] = Yii::app()->request->getParam('record_sid');
			error_log( date('Y-m-d H:i',time()).' success '.$jsonString."\r\n",3,$filename);
			if($consumeService->saveUserPipiEggRecords($log,0)){
				$newArray = array('uid'=>$this->uid,'pipiegg'=>$pipiegg,'dedication'=>$pipiegg*Yii::app()->params['change_relation']['pipiegg_to_dedication']);
				$consumeService->saveUserConsumeAttribute($newArray);
				$this->responseClient('success','');
			}else{
				$this->responseClient('fail',$consumeService->getError());
			}
		}else{
			error_log( date('Y-m-d H:i',time()).' failed '.$jsonString."\r\n",3,$filename);
			$this->responseClient('fail',Yii::t('common','Pipiegg reduce failed'));
		}
		
	}
	
	/**
	 * 批量保存消费星级，只更新redis和数据库，无须发zmq
	 */
	public function actionSaveStars(){
		$uid 		= Yii::app()->request->getParam('suid');
		$stars 		= Yii::app()->request->getParam('stars');
		$newstars   = Yii::app()->request->getParam('newstars');
		$period 	= Yii::app()->request->getParam('period');
		$start_time = Yii::app()->request->getParam('start_time');
		$end_time	= Yii::app()->request->getParam('end_time');
		if(count($uid) <= 0 || count($uid) != count($stars) ||count($uid) != count($newstars) || count($uid) != count($period) || count($uid) != count($start_time) || count($uid) != count($end_time)){
			Yii::app()->end(-1, true);
		}
		foreach($uid as $k => $v){
			if($v <= 0){
				unset($stars[$k]);
				unset($newstars[$k]);
				unset($period[$k]);
				unset($start_time[$k]);
				unset($end_time[$k]);
			}
		}
		if(empty($uid)){
			Yii::app()->end(-2, true);
		}
		
		$consumeService = new ConsumeService();
		$flag = -3;
		foreach($uid as $k => $v){
			$stars_id = $newstars[$k];
			$stars_value = $stars[$k];
			$record = array(
				'uid'		=> $v,
				'period'	=> $period[$k],
				'stars_id'	=> $stars_id,
				'start_time'=> $start_time[$k],
				'end_time'	=> $end_time[$k],
				'create_time' => time(),
			);
			if($consumeService->saveStars($v, $stars_value, $record)){
				$flag = 0;
			}
		}
		Yii::app()->end($flag, true);
	}
	
	public function actionWriteConsumeFailAttribute(){
		$uid= Yii::app()->request->getParam('suid');
		$charm= Yii::app()->request->getParam('charm');
		$dedication= Yii::app()->request->getParam('dedication');
		$egg_points= Yii::app()->request->getParam('egg_points');
		$charm_points= Yii::app()->request->getParam('charm_points');
		
		if($uid <= 0){
			echo -1;
			Yii::app()->end(-1,true);
		}
	
		if($charm){
			$counters['charm'] = $charm;
		}
		if($dedication){
			$counters['dedication'] = $dedication;
		}
		
		if($egg_points){
			$counters['egg_points'] = $egg_points;
		}
		
		if($charm_points){
			$counters['charm_points'] = $charm_points;
		}
		$jsonData = $counters;
		$jsonData['time'] = Yii::app()->request->getParam('time');
		$userConsumeModel = new ConsumeModel();
		$consumeService = new ConsumeService();
		if($counters){
			//捕获并发时事务死锁异常
			try{
				$userConsumeModel->updateAttributeByUid($uid,$counters);
				$filename = DATA_PATH.'runtimes/handel_consume_exception.txt';
				$jsonString = json_encode($jsonData);
				error_log( date('Y-m-d H:i',time()).' '.$jsonString."\r\n",3,$filename);
				echo 0;
				Yii::app()->end(0,true);	
			}catch(Exception $e){
				echo -2;
				Yii::app()->end(-2,true);		
			}
		}
	}
	
	public function actionSaveConsumeRecords(){
		$data=array();
		$seq=false;
		foreach(array_merge($_GET,$_POST) as $key=>$val){
             $data[$key]=$val;
        }
       
        unset($data['r']);
        unset($data['app_id']);
        unset($data['timestamp']);
        unset($data['token']);
        if(isset($data['seq'])){
        	unset($data['seq']);
        	$seq=true;
        }
        $data['uid']=$data['suid'];
        unset($data['suid']);
         
        $filename = DATA_PATH.'runtimes/saveConsumeRecords.txt';
		error_log(date("Y-m-d H:i:s")."存储异常消费记录:".json_encode($data)."\n\r",3,$filename);
		$giftService=new GiftService();
		$consumeService = new ConsumeService();
		$recordId=0;
		switch($data['step']){
			case 1:
				unset($data['step']);
				$recordId=$giftService->saveUserGiftRecords($data);
				break;
			case 2:
				unset($data['step']);
				$consumeService->saveUserPipiEggRecords($data, false);
				if($seq){
					$consumeService->saveUserConsumeAttribute(array('uid'=>$data['uid'],'pipiegg'=>$data['pipiegg']));
				}
				break;
			case 3:
				unset($data['step']);
				$consumeService->saveUserDedicationRecords($data, true);
				if($seq){
					$consumeService->saveUserConsumeAttribute(array('uid'=>$data['uid'],'dedication'=>$data['dedication']));
				}
				break;
			case 4:
				unset($data['step']);
				$consumeService->saveDoteyCharmRecords($data, true);
				if($seq){
					$consumeService->saveUserConsumeAttribute(array('uid'=>$data['uid'],'charm'=>$data['charm']));
				}
				break;
			case 5:
				unset($data['step']);
				$consumeService->saveDoteyCharmPointsRecords($data, true);
				if($seq){
					$consumeService->saveUserConsumeAttribute(array('uid'=>$data['uid'],'charm_points'=>$data['charm_points']));
				}
				break;
			case 6:
				unset($data['step']);
				$consumeService->saveUserEggPointsRecords($data, true);
				if($seq){
					$consumeService->saveUserConsumeAttribute(array('uid'=>$data['uid'],'egg_points'=>$data['egg_points']));
				}
				break;
			default :break;						
		}
		if($recordId>=0){
			error_log(date("Y-m-d H:i:s")."异常消费记录存储成功:".json_encode($data)."\n\r",3,$filename);
			if($recordId===0){
				Yii::app()->end('0',true);
			}else{
				Yii::app()->end($recordId,true);
			}
		}else{
			error_log(date("Y-m-d H:i:s")."异常消费记录存储失败:".json_encode($data)."\n\r",3,$filename);
			Yii::app()->end('-2',true);
		}
		
	}
		
}

?>