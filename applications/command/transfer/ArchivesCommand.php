<?php
class ArchivesCommand extends CConsoleCommand {
	public $showDb;
	
	public $consumeDb;
	
	public $uncenterDb;
	
	public $archives;
	
	public $purview;
	
	public $pageSize=1000;
	
	public function actionArchives(){
		$this->getReadDbConnect();
		$showCommand=$this->showDb->createCommand();
		$consumeCommand=$this->consumeDb->createCommand();
		$uncenterCommand=$this->uncenterDb->createCommand();
		$consumeCommand->setText('delete from  web_archives;');
		$consumeCommand->execute();
		$consumeCommand->setText('delete from  web_archives_category;');
		$consumeCommand->execute();
		$consumeCommand->setText("insert into web_archives_category (`cat_id`,`name`,`en_name`) values (1,'普通直播间','common')");
		$consumeCommand->execute();
		$showCommand->setText("SELECT b.uid,b.nickname,a.create_time FROM `web_dotey_info` as a,web_user_base as b where a.state=1 AND a.uid=b.uid AND b.comment_status='0'");
		$doteyInfo=$showCommand->queryAll();
		$archivesData='';
		$i=1000;
		foreach($doteyInfo as $key=>$dotey){
				$showCommand->setText("SELECT * FROM web_archives where uid={$dotey['uid']} ORDER BY create_time DESC limit 1");
				$archives=$showCommand->queryAll();
				if($archives){
					$create_time=$archives[0]['create_time'];
				}else{
					$create_time=$dotey['create_time'];
				}
				$public_notice=$private_notice='';
				$showCommand->setText("SELECT b.* FROM `web_archives` as a JOIN web_notice as b ON a.archives_id=b.archives_id where a.uid={$dotey['uid']} and b.content!='' ORDER BY b.notice_id  DESC LIMIT 1");
				$notice=$showCommand->queryAll();
				if($notice){
					$public_notice=empty($notice[0]['content'])?'':serialize(array('content'=>htmlspecialchars($notice[0]['content'],ENT_QUOTES)));
					$private_notice=empty($notice[0]['pr_content'])?'':serialize(array('content'=>htmlspecialchars($notice[0]['pr_content'],ENT_QUOTES)));
				}
				$showCommand->setText("SELECT * FROM `web_programe` where uid={$dotey['uid']}");
				$programe=$showCommand->queryAll();
				if($programe){
					$programe=htmlspecialchars($programe[0]['title'],ENT_QUOTES);
				}else{
					if(empty($dotey['nickname'])){
						$uncenterCommand->setText("select * from uc_memberfields where uid={$dotey['uid']}");
						$userBase=$uncenterCommand->queryAll();
						$programe=$userBase[0]['nickname'].'的直播间';
					}else{
						$programe=$dotey['nickname'].'的直播间';
					}
					
				}
				$consumeCommand->setText("select * from web_archives where uid={$dotey['uid']}");
				$newArchives=$consumeCommand->queryAll();
				if(!$newArchives){
					$consumeCommand->setText("insert into web_archives (`archives_id`,`uid`,`title`,`cat_id`,`notice`,`private_notice`,`create_time`) values ({$i},{$dotey['uid']},'{$programe}',1,'{$public_notice}','{$private_notice}',{$create_time})");
					$consumeCommand->execute();
				}
				$i++;
			
		}
		
		
	}
	
	public function actionArchivesUser(){
		$this->getReadDbConnect();
		$consumeCommand=$this->consumeDb->createCommand();
		$consumeCommand->setText("select * from web_archives where uid in (12734858,12738546,12748800,12748954,12748992,12749451,12749844,12750128,12750702,12751178,12751388)");
		$archives=$consumeCommand->queryAll();
		$userData='';
		foreach($archives as $_archives){
			$userData.=($userData?',':'')."({$_archives['uid']},{$_archives['archives_id']})";
		}
		$consumeCommand->setText('insert into web_archives_user (`uid`,`archives_id`) values '.$userData);
		$consumeCommand->execute();
	}
	
	public function actionArchivesPurview(){
		$this->getReadDbConnect();
		$showCommand=$this->showDb->createCommand();
		$consumeCommand=$this->consumeDb->createCommand();
		//$consumeCommand->setText('delete from  web_archives_purview;');
		//$consumeCommand->execute();
		$showCommand->setText("select * from web_user_purview uid in (12734858,12738546,12748800,12748954,12748992,12749451,12749844,12750128,12750702,12751178,12751388)");
		$purview=$showCommand->queryAll();
		$purviewData='';
		foreach($purview as $_purview){
			$consumeCommand->setText("select * from web_archives where uid={$_purview['dotey_id']}");
			$archives=$consumeCommand->queryAll();
			if($archives){
				$purviewData.=($purviewData?',':'')."({$_purview['uid']},{$archives[0]['archives_id']})";
			}
			
		}
		$consumeCommand->setText('insert into web_archives_purview (`uid`,`archives_id`) values '.$purviewData);
		$consumeCommand->execute();
	}
	
	
	public function actionLiveRecords(){
		$this->getReadDbConnect();
		$showCommand=$this->showDb->createCommand();
		$consumeCommand=$this->consumeDb->createCommand();
		$showCommand->setText("select * from web_archives where status!='-1' and uid in (12734858,12738546,12748800,12748954,12748992,12749451,12749844,12750128,12750702,12751178,12751388)");
		$archives=$showCommand->queryAll();
		$liveData='';
		foreach($archives as $_archives){
			$consumeCommand->setText("select * from web_archives where uid={$_archives['uid']}");
			$newArchives=$consumeCommand->queryAll();
			if($newArchives){
				$liveData.=($liveData?',':'')."({$newArchives[0]['archives_id']},{$_archives['status']},{$_archives['live_time']},{$_archives['update_time']},{$_archives['live_time']},{$_archives['create_time']})";
			}
		}
		$consumeCommand->setText('insert into web_live_records (`archives_id`,`status`,`start_time`,`end_time`,`live_time`,`create_time`) values '.$liveData);
		$consumeCommand->execute();
	}

	public function actionDuration(){
		$this->getReadDbConnect();
		$showCommand=$this->showDb->createCommand();
		$consumeCommand=$this->consumeDb->createCommand();
		$consumeCommand->setText("select * from web_live_records");
		$liveRecord=$consumeCommand->queryAll();
		foreach($liveRecord as $row){
			$consumeCommand->setText("select * from web_archives where archives_id={$row['archives_id']}");
			$archives=$consumeCommand->queryAll();
			if($archives){
				$showCommand->setText("select * from web_archives where create_time={$row['create_time']} and uid={$archives[0]['uid']}");
				$duration=$showCommand->queryAll();
				if($duration){
					if($duration[0]['online_time']>0){
						echo "update web_live_records set duration={$duration[0]['online_time']} where record_id={$row['record_id']} \n";
						$consumeCommand->setText("update web_live_records set duration={$duration[0]['online_time']} where record_id={$row['record_id']}");
						$consumeCommand->execute();
					}
				}
			}
		}
	}
	
	public function actionLiveServer(){
		$this->getReadDbConnect();
		$showCommand=$this->showDb->createCommand();
		$consumeCommand=$this->consumeDb->createCommand();
		$consumeCommand->setText("select * from web_archives where uid in (12734858,12738546,12748800,12748954,12748992,12749451,12749844,12750128,12750702,12751178,12751388)");
		$archives=$consumeCommand->queryAll();
		foreach($archives as $row){
			$consumeCommand->setText('SELECT * FROM `web_live_server` ORDER BY use_num asc LIMIT 1');
			$live_server=$consumeCommand->queryAll();
			echo "档期写入分配的视频服务器";
			$consumeCommand->setText("insert into web_archives_live_server (`archives_id`,`server_id`) values ({$row['archives_id']},{$live_server[0]['server_id']})");
			$consumeCommand->execute();
			echo "更新视频服务器使用数";
			$consumeCommand->setText("UPDATE `web_live_server` SET use_num=use_num+1 WHERE server_id={$live_server[0]['server_id']}");
			$consumeCommand->execute();
			$consumeCommand->setText('SELECT * FROM `web_global_server` ORDER BY use_num asc LIMIT 1');
			$chat_server=$consumeCommand->queryAll();
			echo "档期写入聊天服务器地址";
			$consumeCommand->setText("insert into web_chat_server (`archives_id`,`domain`) values ({$row['archives_id']},'{$chat_server[0]['domain']}')");
			$consumeCommand->execute();
			echo "更新聊天服务器使用数";
			$consumeCommand->setText("UPDATE `web_global_server` SET use_num=use_num+1 WHERE global_server_id={$chat_server[0]['global_server_id']}");
			$consumeCommand->execute();
		}
		
		
	}
	
	public function actionUpdateArchivesLive(){
		$this->getReadDbConnect();
		$archivesDb=$this->archives->createCommand();
		$purviewDb=$this->purview->createCommand();
		echo "统计均衡型视频服务器使用情况\n";
		echo "SELECT server_id,COUNT(server_id) as use_num FROM `web_archives_live_server` where server_id>4 GROUP BY server_id\n";
		$archivesDb->setText('SELECT server_id,COUNT(server_id) as use_num FROM `web_archives_live_server` where server_id>4 GROUP BY server_id');
		$liveServer=$archivesDb->queryAll();
		foreach($liveServer as $row){
			echo "更新视频服务器使用数量\n";
			echo "UPDATE `web_live_server` SET use_num=".$row['use_num']." where server_id=".$row['server_id']."\n";
			$archivesDb->setText('UPDATE `web_live_server` SET use_num='.$row['use_num'].' where server_id='.$row['server_id']);
			$archivesDb->execute();
		}
		echo "找出分配的为指定型的所有主播\n";
		echo "SELECT * FROM `web_archives_live_server` where server_id<=4\n";
		$archivesDb->setText('SELECT * FROM `web_archives_live_server` where server_id<=4');
		$doteyLiveServer=$archivesDb->queryAll();
		$i=1;
		$archivesLiveing=array();
		foreach($doteyLiveServer as $row){
			echo "后台找出已经指定过指定型视频服务器的操作记录\n";
			echo "SELECT * FROM `web_purview_userop_records` where purview_id=115 AND REPLACE(substring_index(op_desc,'(id=',-1),')','')=".$row['id']." ORDER BY op_time DESC LIMIT 1\n";
			$purviewDb->setText("SELECT * FROM `web_purview_userop_records` where purview_id=115 AND REPLACE(substring_index(op_desc,'(id=',-1),')','')=".$row['id']." ORDER BY op_time DESC LIMIT 1");
			$purview=$purviewDb->queryAll();
			echo "SELECT * FROM `web_live_records` where archives_id=".$row['archives_id']." AND `status`=1 ORDER BY record_id DESC LIMIT 1\n";
			$archivesDb->setText('SELECT * FROM `web_live_records` where archives_id='.$row['archives_id'].' AND `status`=1 ORDER BY record_id DESC LIMIT 1');
			$liveingArchives=$archivesDb->queryAll();
			if($liveingArchives){
				$archivesLiveing[]=array('archives_id'=>$row['archives_id'],'server_id'=>$row['server_id']);
			}
			
			if(!$purview&&!$liveingArchives){
				echo "后台中无指定指定型视频服务器的操作记录\n";
				echo "找出视频服务器使用最少的均衡型服务器\n";
				echo "SELECT * FROM `web_live_server` WHERE server_id>4 ORDER BY use_num ASC LIMIT 1\n";
				$archivesDb->setText('SELECT * FROM `web_live_server` WHERE server_id>4 ORDER BY use_num ASC LIMIT 1');
				$useLiveServer=$archivesDb->queryAll();
				$useLiveServer=array_pop($useLiveServer);
				echo "从指定型视频服务器移出新的均衡型视频服务器\n";
				echo "UPDATE `web_archives_live_server` SET server_id=".$useLiveServer['server_id']." WHERE archives_id=".$row['archives_id']."\n";
				$archivesDb->setText('UPDATE `web_archives_live_server` SET server_id='.$useLiveServer['server_id'].' WHERE archives_id='.$row['archives_id']);
				$archivesDb->execute();
				echo "UPDATE `web_live_server` SET use_num=use_num+1 WHERE server_id=".$useLiveServer['server_id']."\n";
				$archivesDb->setText('UPDATE `web_live_server` SET use_num=use_num+1 WHERE server_id='.$useLiveServer['server_id']);
				$archivesDb->execute();
				$i++;
			}
		}
		
		echo "统计指定型视频服务器使用情况\n";
		echo "SELECT server_id,COUNT(server_id) as use_num FROM `web_archives_live_server` where server_id<=4 GROUP BY server_id\n";
		$archivesDb->setText('SELECT server_id,COUNT(server_id) as use_num FROM `web_archives_live_server` where server_id<=4 GROUP BY server_id');
		$specLiveServer=$archivesDb->queryAll();
		foreach($specLiveServer as $row){
			echo "更新指定型视频服务器的使用数量\n";
			echo "UPDATE `web_live_server` SET use_num=".$row['use_num']." where server_id=".$row['server_id']."\n";
			$archivesDb->setText('UPDATE `web_live_server` SET use_num='.$row['use_num'].' where server_id='.$row['server_id']);
			$archivesDb->execute();
		}
		if($archivesLiveing){
			echo "当前正在直播的主播没有做迁移处理共".count($archivesLiveing)."个主播,分别为：".json_encode($archivesLiveing)."\n";
		}
		echo "总共从指定行视频服务器迁移出".$i."个主播到均衡视频服务器中\n";
	}
	
	private function getReadDbConnect(){
		$this->showDb=Yii::app()->db_read_pipishow;
		$this->consumeDb=Yii::app()->db_archives;
		$this->uncenterDb=Yii::app()->db_read_ucenter;
		$this->purview=Yii::app()->db_purview;
		$this->archives=Yii::app()->db_archives;
	}
}

?>