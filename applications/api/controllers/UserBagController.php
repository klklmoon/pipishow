<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: lei wei $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z lei wei  $ 
 * @package api
 * @subpackage userBag
 */
class UserBagController extends PipiApiController {
	public function actionUpdateUserBag(){
		$gift_id= Yii::app()->request->getParam('gift_id');
		$suid= Yii::app()->request->getParam('suid');
		$num= Yii::app()->request->getParam('num');
		$time= Yii::app()->request->getParam('time');
		if($gift_id<=0||$suid<=0||$num<=0){
			Yii::app()->end('-1',true);
		}
		$userGiftBagModel=new UserGiftBagModel();
		try{
			$result=$userGiftBagModel->updateCounters(array('num'=>$num),'uid=:uid AND gift_id=:gift_id',array(':uid'=>$suid,'gift_id'=>$gift_id));
			if($result){
				$filename = DATA_PATH.'runtimes/handel_userbag_exception.txt';
				$data=json_encode(array('gift_id'=>$gift_id,'uid'=>$suid,'num'=>$num,'time'=>$time));
				error_log( date('Y-m-d H:i',time()).' '.$data.'/n',3,$filename);
				Yii::app()->end('0',true);
			}else{
				Yii::app()->end('-2',true);
			}
			
		}catch(Exception $e){
			Yii::app()->end('-3',true);		
		}
	}
}

?>