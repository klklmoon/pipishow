<?php
/**
 * 直播间权限操作
 * the last known user to change this file in the repository  <$LastChangedBy: lei wei $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z lei wei  $
 * @package
 */
class PurviewController extends PipiController{
	const SHIELD_RANK = 7;
	const EXPIRE = 86400;
	/**
	 * 设房管
	 */
	public function actionSetPurview(){
		$archives_id=Yii::app()->request->getParam('archives_id');
		$uid=Yii::app()->request->getParam('uid');
		$nickname=Yii::app()->request->getParam('nickname');
		$doteyId=Yii::app()->user->id;
		if($archives_id<=0||$uid<=0){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('common','Parameters are wrong'))));
		}
		if(!$this->isLogin){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('user','You are not logged'))));
		}
		$archivesService=new ArchivesService();
		$result=$archivesService->addManage($uid,$doteyId,$archives_id);
		if(!$result){
			$msg=$archivesService->getError();
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('archives',$msg))));
		}
		exit(json_encode(array('flag'=>1,'message'=>Yii::t('archives','Archives add manage successed'))));
	}

	public function actionRemovePurview(){
		$archives_id=Yii::app()->request->getParam('archives_id');
		$uid=Yii::app()->request->getParam('uid');
		$nickname=Yii::app()->request->getParam('nickname');
		$doteyId=Yii::app()->user->id;
		if($archives_id<=0||$uid<=0){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('common','Parameters are wrong'))));
		}
		if(!$this->isLogin){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('user','You are not logged'))));
		}
		$archivesService=new ArchivesService();
		$result=$archivesService->removeManage($uid,$doteyId,$archives_id);
		if(!$result){
			$msg=$archivesService->getError();
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('archives',$msg))));
		}
		exit(json_encode(array('flag'=>1,'message'=>Yii::t('archives','Archives remove manage success'))));
	}

	public function actionForbiden(){
		$archives_id=Yii::app()->request->getParam('archives_id');
		$to_uid=Yii::app()->request->getParam('to_uid');
		$to_nickname=Yii::app()->request->getParam('to_nickname');
		$type=Yii::app()->request->getParam('type');
		$period=Yii::app()->request->getParam('period');
		$uid=Yii::app()->user->id;
		if($archives_id<=0){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('common','Parameters are wrong'))));
		}
		if(!$this->isLogin){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('user','You are not logged'))));
		}
		$forbidenService=new ForbidenService();
		$result=$forbidenService->forbidenOperate($archives_id,$uid,$to_uid,$to_nickname,$type,$period);
		if(!$result||$result<=0){
			if($type==0){
				$msg='Archives remove forbiden Ip failed';
			}else if($type==1){
				$msg='Archives forbiden Ip failed';
			}else if($type==4){
				$msg='Archives remove  forbiden failed';
			}else if($type==5){
				$msg='Archives forbiden failed';
			}else if($type==8){
				$msg='Archives kick out failed';
			}else if($type==10){
				$msg='Archives all forbiden failed';
			}else if($type==11){
				$msg='Archives remove all forbiden failed';
			}
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('archives',$msg))));
		}elseif($result==2){
			$msg=Yii::t('archives','The other successful defense of your operation');
			exit(json_encode(array('flag'=>0,'message'=>$msg)));
		}else{
			if($type==0){
				$msg='Archives remove forbiden Ip successed';
			}else if($type==1){
				$msg='Archives forbiden Ip successed';
			}else if($type==4){
				$msg='Archives remove  forbiden successed';
			}else if($type==5){
				$msg='Archives forbiden successed';
			}else if($type==8){
				$msg='Archives kick out successed';
			}else if($type==10){
				$msg='Archives all forbiden successed';
			}else if($type==11){
				$msg='Archives remove all forbiden successed';
			}
			exit(json_encode(array('flag'=>1,'message'=>Yii::t('archives',$msg))));
		}
	}


	public function actionShieldChat() {
		$uid=Yii::app()->request->getParam('uid');
		$archives_id=Yii::app()->request->getParam('archives_id');
		$sheildList=Yii::app()->request->cookies['sheildList'];
		$sheildList = json_decode ( $sheildList, true );
		$userJson=new UserJsonInfoService();
		$userInfo=$userJson->getUserInfo($uid,false);
		$purviewrank=self::getPurviewRank($archives_id,$userInfo['pk']);
		if ($userInfo['rk'] >= self::SHIELD_RANK || $purviewrank>=1) {
			if ($sheildList) {
				if (in_array ( $uid, $sheildList ) == false) {
					array_push ( $sheildList, $uid );
				}
			} else {
				$sheildList = array ($uid );
			}
			$cookie =new CHttpCookie('sheildList',json_encode ( $sheildList ));
			$cookie->expire = time()+self::EXPIRE;
			Yii::app()->request->cookies['sheildList']=$cookie;
			exit(json_encode(array('flag'=>1,'message'=>Yii::t('archives','Archives sheild chat success'))));
		} else {
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('archives','Archives sheild chat failed'))));
		}
	}

	public function actionRecoverChat() {
		$uid=Yii::app()->request->getParam('uid');
		$archives_id=Yii::app()->request->getParam('archives_id');
		$sheildList=Yii::app()->request->cookies['sheildList'];
		$sheildList = json_decode ( $sheildList, true );
		$userJson=new UserJsonInfoService();
		$userInfo=$userJson->getUserInfo($uid,false);
		$purviewrank=self::getPurviewRank($archives_id,$userInfo['pk']);
		if ($userInfo['rk'] >= self::SHIELD_RANK || $purviewrank>=1) {
			foreach ( $sheildList as $key => $row ) {
				if ($row == $uid) {
					array_splice ( $sheildList, $key, 1 );
				}
			}
			$cookie =new CHttpCookie('sheildList',json_encode ( $sheildList ));
			Yii::app()->request->cookies['sheildList']=$cookie;
			exit(json_encode(array('flag'=>1,'message'=>Yii::t('archives','Archives recover chat user success'))));
		} else {
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('archives','Archives recover chat failed'))));
		}
	}
	/**
	 * 获取档期档期内用户的操作权限
	 * @param int $archivesId  档期Id
	 * @param int $data        操作权限数组
	 * @return int
	 */
	private function getPurviewRank($archivesId,$data){
		$purviewRank=1;
		//是否存在房管权限
		if(isset($data[2])){
			if(in_array($archivesId,$data[2])){
				$purviewRank=2;
			}
		}
		if(isset($data[3])){
			if(in_array($archivesId,$data[3])){
				$purviewRank=3;
			}
		}

		if(isset($data[4])){
			$purviewRank=4;
		}
		return $purviewRank;
	}

}

?>