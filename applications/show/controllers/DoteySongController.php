<?php

class DoteySongController extends PipiController{
	
	/**
	 * 获取主播的歌单列表
	 */
	public function actionDoteySongList(){
		$archives_id=Yii::app()->request->getParam('archives_id');
		$uid=Yii::app()->request->getParam('uid');
		$page=Yii::app()->request->getParam('page');
		$page=empty($page)?0:$page;
		if($page>0){
			$page=$page-1;
		}
		if(!$this->isLogin){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('user','You are not logged'))));
		}
		$forbidenService=new ForbidenService();
		if($forbidenService->getArchivesKickout($archives_id,Yii::app()->user->id)){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('archives','You have been kicked out of archives'))));
		}
		$doteySongService=new DoteySongService();
		$song=$doteySongService->getDoteySongByDoteyIdLimit($uid,$page,10);
		$songList=array();
		foreach($song['list'] as $key=>$row){
			$songList[$key]['song_id']=$row['song_id'];
			$songList[$key]['name']=$row['name'];
			$songList[$key]['singer']=$row['singer'];
			$songList[$key]['create_time']=date('Y/m/d',$row['create_time']);
		}
		$song=array('count'=>$song['count'],'list'=>$songList);
		exit(json_encode(array('flag'=>1,'data'=>$song)));
	}
	
	
	/**
	 * 获取主播未处理的点歌记录
	 */
	public function actionDoteySongRecord(){
		$uid=Yii::app()->request->getParam('uid');
		$archives_id=Yii::app()->request->getParam('archives_id');
		if($uid<=0||$archives_id<=0){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('common','Parameters are wrong'))));
		}
		$archivesService=new ArchivesService();
		$archives=$archivesService->getArchivesByarchivesId($archives_id);
		if($archives['uid']!=$uid){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('doteySong','Song record is error'))));
		}
		$doteySongService=new DoteySongService();
		$song=$doteySongService->getUnhandleUserSongRecordByDoteyId($uid);
		$songRecord=array();
		$uids=array();
		foreach($song as $row){
			$uids[]=$row['uid'];
		}
		$userService=new UserService();
		$userBase=$userService->getUserBasicByUids($uids);
		if($song){
			$i=0;
			foreach($song as $key=>$_song){
				$songRecord[$i]['record_id']=$_song['record_id'];
				$songRecord[$i]['name']=$_song['name'];
				$songRecord[$i]['singer']=$userBase[$_song['uid']]['nickname'];
				$songRecord[$i]['is_handle']=$_song['is_handle'];
				$songRecord[$i]['create_time']=date('m/d',$_song['create_time']);
				$i++;
			}
		}
		$return['flag']=1;
		$return['data']=$songRecord;
		exit(json_encode($return));
	}
	
	/**
	 * 用户点歌
	 */
	public function actionDemandSong(){
		$song_id=Yii::app()->request->getParam('song_id');
		$archives_id=Yii::app()->request->getParam('archives_id');
		$dotey_id=Yii::app()->request->getParam('dotey_id');
		$song_name=Yii::app()->request->getParam('song_name');
		$song_singer=Yii::app()->request->getParam('song_singer');
		$uid=Yii::app()->user->id;
		if($archives_id<=0||$dotey_id<=0){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('common','Parameters are wrong'))));
		}
		if(!$this->isLogin){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('user','You are not logged'))));
		}
		$forbidenService=new ForbidenService();
		if($forbidenService->getArchivesKickout($archives_id,$uid)){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('archives','You have been kicked out of archives'))));
		}
		$doteySongService=new DoteySongService();
		$allow=$doteySongService->getArchivesAllowSong($archives_id);
		if($allow==2){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('doteySong','Archives forbid demand song'))));
		}
		$consumeService=new ConsumeService();
		$userConsume=$consumeService->getConsumesByUids($uid);
		if($userConsume[$uid]['pipiegg']-$userConsume[$uid]['freeze_pipiegg']-SONG_PIPIEGG<0){
			exit(json_encode(array('flag'=>-1,'message'=>Yii::t('common','Pipiegg not enough'))));
		}
		$songs=array();
		if($song_id>0){
			$songs['song_id']=$song_id;
			$song=$doteySongService->getDoteySongBySongId($songs['song_id']);
			$songs['name']=$song['name'];
		}
		if($song_name && $song_singer){
			$songs['name']=$song_name;
			$songs['singer']=$song_singer;
		}
		$result=$doteySongService->demandSong($uid,$dotey_id,$archives_id,$songs);
		if($result){
			$count=$doteySongService->getCountUserSongRecordsByRecordId($result,$dotey_id);
			exit(json_encode(array('flag'=>1,'data'=>array('song'=>$songs['name'],'count'=>$count),'message'=>Yii::t('doteySong','Song demand successed'))));
		}else{
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('doteySong','Song demand failed'))));
		}
	}
	
	public function actionAddSong(){
		$song_name=Yii::app()->request->getParam('song_name');
		$song_singer=Yii::app()->request->getParam('song_singer');
		$archives_id=Yii::app()->request->getParam('archives_id');
		$uid=Yii::app()->user->id;
		if(empty($song_name)||empty($song_name)||$archives_id<=0){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('common','Parameters are wrong'))));
		}
		if(!$this->isLogin){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('user','You are not logged'))));
		}
		$doteySongService=new DoteySongService();
		$doteySong=$doteySongService->getDoteySongByCondition(array('dotey_id'=>$uid,'name'=>$song_name,'singer'=>$song_singer));
		if($doteySong){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('doteySong','The song has been in existence'))));
		}
		$archviesService=new ArchivesService();
		$archives=$archviesService->getArchivesByArchivesId($archives_id);
		if($archives['uid']!=$uid){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('doteySong','No permission to operate'))));
		}
		
		$song['dotey_id']=$uid;
		$song['name']=$song_name;
		$song['singer']=$song_singer;
		$song['pipiegg']=SONG_PIPIEGG;
		$song['charm']=Yii::app()->params['change_relation']['pipiegg_to_charm']*SONG_PIPIEGG;
		$song['charm_points']=Yii::app()->params['change_relation']['pipiegg_to_charmpoints']*SONG_PIPIEGG;
		$song['dedication']=Yii::app()->params['change_relation']['pipiegg_to_dedication']*SONG_PIPIEGG;
		//$song['egg_points']=Yii::app()->params['pipiegg_to_eggpoints']*SONG_PIPIEGG;
		$songId=$doteySongService->saveDoteySong($song);
		if($songId){
			exit(json_encode(array('flag'=>1,'message'=>Yii::t('doteySong','Song add successed'))));
		}else{
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('doteySong','Song add failed'))));
		}
	}
	
	public function actionDelSong(){
		$song_id=Yii::app()->request->getParam('song_id');
		$uid=Yii::app()->user->id;
		if($song_id<=0){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('common','Parameters are wrong'))));
		}
		if(!$this->isLogin){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('user','You are not logged'))));
		}
		$doteySongService=new DoteySongService();
		$song=$doteySongService->getDoteySongBySongId($song_id);
		if($uid!=$song['dotey_id']){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('doteySong','No permission to operate'))));
		}
		$result=$doteySongService->delDoteySongBySongId($song_id);
		if(!$result){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('doteySong','Song del failed'))));
		}else{
			exit(json_encode(array('flag'=>1,'message'=>Yii::t('doteySong','Song del successed'))));
		}
	}
	
	public function actionBatchAddSong(){
		$archives_id=Yii::app()->request->getParam('archives_id');
		$song_name=Yii::app()->request->getParam('song_name');
		$song_singer=Yii::app()->request->getParam('song_singer');
		$uid=Yii::app()->user->id;
		if($archives_id<=0||empty($song_name)||empty($song_singer)){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('common','Parameters are wrong'))));
		}
		if(!$this->isLogin){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('user','You are not logged'))));
		}
		$doteySongService=new DoteySongService();
		$songs=array();
		foreach($song_name as $key=>$row){
			$songs[$key]['name']=$row;
			$songs[$key]['singer']=$song_singer[$key];
			$songs[$key]['pipiegg']=SONG_PIPIEGG;
			$songs[$key]['charm']=Yii::app()->params['change_relation']['pipiegg_to_charm']*SONG_PIPIEGG;
			$songs[$key]['charm_points']=Yii::app()->params['change_relation']['pipiegg_to_charmpoints']*SONG_PIPIEGG;
			$songs[$key]['dedication']=Yii::app()->params['change_relation']['pipiegg_to_dedication']*SONG_PIPIEGG;
			//$songs[$key]['egg_points']=500;
		}
		$result=$doteySongService->batchSaveDoteySong($uid, $songs);
		if($result<=0){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('doteySong','Song add failed'))));
		}
		exit(json_encode(array('flag'=>1,'message'=>Yii::t('doteySong','Song add successed'))));
	}
	
	public function actionActSong(){
		$record_id=Yii::app()->request->getParam('record_id');
		$archives_id=Yii::app()->request->getParam('archives_id');
		$uid=Yii::app()->user->id;
		if($archives_id<=0||$record_id<=0){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('common','Parameters are wrong'))));
		}
		if(!$this->isLogin){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('user','You are not logged'))));
		}
		
		$doteySongService=new DoteySongService();
		$result=$doteySongService->actSong($record_id, $uid, $archives_id);
		if($result<=0){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('doteySong','Singing failed'))));
		}
		exit(json_encode(array('flag'=>1,'message'=>Yii::t('doteySong','Singing success'))));
	}
	
	public function actionCancelSong(){
		$record_id=Yii::app()->request->getParam('record_id');
		$archives_id=Yii::app()->request->getParam('archives_id');
		$uid=Yii::app()->user->id;
		if($archives_id<=0||$record_id<=0){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('common','Parameters are wrong'))));
		}
		if(!$this->isLogin){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('user','You are not logged'))));
		}
		$doteySongService=new DoteySongService();
		$result=$doteySongService->cancelSong($record_id, $uid, $archives_id);
		if($result<=0){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('doteySong','Songs cancelled failed'))));
		}
		exit(json_encode(array('flag'=>1,'message'=>Yii::t('doteySong','Songs cancelled success'))));
	}
	
	public function actionAlreadySong(){
		$archives_id=Yii::app()->request->getParam('archives_id');
		$doteyId=Yii::app()->request->getParam('doteyId');
		$uid=Yii::app()->user->id;
		if($archives_id<=0){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('common','Parameters are wrong'))));
		}
		if(!$this->isLogin){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('user','You are not logged'))));
		}
		$doteySongService=new DoteySongService();
		$doteyRecord=$doteySongService->getUserSongRecordsByDoteyId($doteyId,0,10,array('is_handle'=>1));
		$doteyList=array();
		$userJson=new UserJsonInfoService();
		foreach($doteyRecord['list'] as $key=>$row){
			$doteyList[$key]['name']=$row['name'];
			$userInfo=$userJson->getUserInfo($row['uid'],false);
			$doteyList[$key]['nickname']=$userInfo['nk'];
		}
		exit(json_encode($doteyList));
	}
	
}
?>