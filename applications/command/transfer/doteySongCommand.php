<?php
class doteySongCommand extends CConsoleCommand {
	public $showDb;
	
	public $consumeDb;
	
	public $archivesDb;
	
	public $pageSize=10;
	
	public function actionDoteySong(){
		$this->getReadDbConnect();
		$showCommand=$this->showDb->createCommand();
		$consumeCommand=$this->consumeDb->createCommand();
		$consumeCommand->setText('delete from  web_dotey_song;ALTER TABLE `web_dotey_song` AUTO_INCREMENT=1;');
		$consumeCommand->execute();
		$showCommand->setText("select count(*) as count from web_dotey_song");
		$count=$showCommand->queryAll();
		$page=ceil($count[0]['count']/$this->pageSize);
		for($i=1;$i<=$page;$i++){
			$showCommand->setText('select * from web_dotey_song order by song_id asc limit '.(($i-1)*$this->pageSize).','.$this->pageSize);
			$song=$showCommand->queryAll();
			$songData='';
			foreach($song as $_song){
				$title=htmlspecialchars($_song['title'],ENT_QUOTES);
				$name=htmlspecialchars($_song['name'],ENT_QUOTES);
				$songData.=($songData?',':'')."({$_song['dotey_id']},'{$title}','{$name}',{$_song['price']},{$_song['charm']},{$_song['charm']},{$_song['charm']},{$_song['charm']},{$_song['create_time']})";
			}
			
			$consumeCommand->setText('insert into web_dotey_song (`dotey_id`,`name`,`singer`,`pipiegg`,`charm`,`charm_points`,`dedication`,`egg_points`,`create_time`) values '.$songData);
			$consumeCommand->execute();
		}
		
	}
	
	public function actionUserSong(){
		$this->getReadDbConnect();
		$showCommand=$this->showDb->createCommand();
		$consumeCommand=$this->consumeDb->createCommand();
		$archivesCommand=$this->archivesDb->createCommand();
		$consumeCommand->setText('delete from  web_user_song;ALTER TABLE `web_user_song` AUTO_INCREMENT=1;');
		$consumeCommand->execute();
		
		$showCommand->setText("select count(*) as count from web_song_record");
		$count=$showCommand->queryAll();
		$page=ceil($count[0]['count']/$this->pageSize);
		for($i=1;$i<=$page;$i++){
			echo "正在写入第".$i."条数据\n";
			$showCommand->setText('select * from web_song_record order by id asc limit '.(($i-1)*$this->pageSize).','.$this->pageSize);
			$songRerocd=$showCommand->queryAll();
			$songData='';
			foreach($songRerocd as $_songRerocd){
				$title=htmlspecialchars($_songRerocd['song_title'],ENT_QUOTES);
				$name=htmlspecialchars($_songRerocd['song_name'],ENT_QUOTES);
				$archivesCommand->setText("select * from web_archives where uid={$_songRerocd['dotey_id']}");
				$archives=$archivesCommand->queryAll();
				$archives_id=isset($archives[0]['archives_id'])?$archives[0]['archives_id']:0;
				$songData.=($songData?',':'')."({$_songRerocd['song_id']},{$archives_id},{$_songRerocd['uid']},{$_songRerocd['dotey_id']},'{$title}','{$name}',{$_songRerocd['price']},{$_songRerocd['charm']},{$_songRerocd['charm']},{$_songRerocd['charm']},{$_songRerocd['charm']},{$_songRerocd['status']},{$_songRerocd['create_time']},{$_songRerocd['update_time']})";
			}
			$consumeCommand->setText('insert into web_user_song (`song_id`,`target_id`,`uid`,`to_uid`,`name`,`singer`,`pipiegg`,`charm`,`charm_points`,`dedication`,`egg_points`,`is_handle`,`create_time`,`update_time`) values '.$songData);
			$consumeCommand->execute();
		}
		
		
		
	}
	
	/**
	 * 修改用户已点歌，主播未确认的点歌记录
	 */
	public function actionChangeDoteySongRecord(){
		$this->getReadDbConnect();
		$consumeCommand=$this->consumeDb->createCommand();
		$consumeCommand->setText("select *  from web_user_song where is_handle=0");
		$record=$consumeCommand->queryAll();
		foreach($record as $key=>$row){
			echo "正在修改点歌未确认第".$key."条数据\n";
			$consumeCommand->setText("update web_user_song set pipiegg=pipiegg*100,dedication=dedication*2 where record_id=".$row['record_id']);
			$consumeCommand->execute();
		}
		echo "点歌未确认数据修改完成\n";
	}
	
	
	private function getReadDbConnect(){
		$this->showDb=Yii::app()->db_read_pipishow;
		$this->consumeDb=Yii::app()->db_consume;
		$this->archivesDb=Yii::app()->db_archives;
	}
	
}

?>