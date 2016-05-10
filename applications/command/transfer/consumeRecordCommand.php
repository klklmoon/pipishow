<?php
define('TRANSFER_FILE',dirname(dirname(dirname(dirname(__FILE__)))).DIR_SEP."data".DIR_SEP);
class consumeRecordCommand extends CConsoleCommand {
	public $showDb;

	public $consumeDb;

	public $ucenterDb;

	public $archviesDb;

	public $pageSize=1000;
	
	public function actionUserBagRecords(){
		$this->getReadDbConnect();
		$showCommand=$this->showDb->createCommand();
		$consumeCommand=$this->consumeDb->createCommand();
		$consumeCommand->setText('delete from  web_user_bag_records;ALTER TABLE `web_user_bag_records` AUTO_INCREMENT=1;');
		$consumeCommand->execute();
		$showCommand->setText("select count(*) as count from web_bag_record");
		$count=$showCommand->queryAll();
		$page=ceil($count[0]['count']/$this->pageSize);
		for($i=1;$i<=$page;$i++){
			echo "正在写入第".$i."条入背包记录数据\n";
			$showCommand->setText('select * from web_bag_record order by brecord_id asc limit '.(($i-1)*$this->pageSize).','.$this->pageSize);
			$bagRecord=$showCommand->queryAll();
			$bagData='';
			foreach($bagRecord as $_bagRecord){
				$info=array('uid'=>$_bagRecord['uid'],'nickname'=>$_bagRecord['username'],'from_uid'=>$_bagRecord['from_uid'],'from_nickname'=>$_bagRecord['from_name'],'gift_id'=>$_bagRecord['present_id'],'gift_name'=>$_bagRecord['giftname'],'num'=>$_bagRecord['giftname'],'remark'=>$_bagRecord['remark']);
				$info=serialize($info);
				if($_bagRecord['source']==1){
					$source=3;
				}
				if($_bagRecord['source']==2){
					$source=2;
				}
				if($_bagRecord['source']==3){
					$source=0;
				}
				$bagData.=($bagData?"\n":"")."{$_bagRecord['brecord_id']},{$_bagRecord['uid']},{$_bagRecord['present_id']},{$_bagRecord['quantity']},{$info},{$source},{$_bagRecord['create_time']}";
			}
			$this->writeDbtext(TRANSFER_FILE.'web_user_bag_records.txt',$bagData);
			$consumeCommand->setText('insert into web_user_bag_records (`record_id`,`uid`,`gift_id`,`num`,`info`,`source`,`create_time`) values '.$bagData);
			$consumeCommand->execute();
		}

	}

	public function actionUserGiftSendRecords(){
		$this->getReadDbConnect();
		$showCommand=$this->showDb->createCommand();
		$consumeCommand=$this->consumeDb->createCommand();
		$archivesCommand=$this->archviesDb->createCommand();
		//$consumeCommand->setText('delete from  web_user_giftsend_records;ALTER TABLE `web_user_giftsend_records` AUTO_INCREMENT=1;');
		//$consumeCommand->execute();
		$showCommand->setText("select count(*) as count from web_user_gift_records");
		$count=$showCommand->queryAll();
		$page=ceil($count[0]['count']/$this->pageSize);
		for($i=1;$i<=$page;$i++){
			echo "正在写入第".$i."条用户送礼记录数据\n";
			$showCommand->setText('select * from web_user_gift_records order by record_id asc limit '.(($i-1)*$this->pageSize).','.$this->pageSize);
			$giftRecord=$showCommand->queryAll();
			$giftData='';
			foreach($giftRecord as $_giftRecord){
				$showCommand->setText("select * from web_user_gift_records_relation where record_id={$_giftRecord['record_id']} and is_onwer=0");
				$giftRelation=$showCommand->queryAll();
				if($_giftRecord['gift_type']==1){
					$showCommand->setText("select * from web_gift_bag where uid={$_giftRecord['uid']} and  present_id={$_giftRecord['present_id']}");
					$giftBag=$showCommand->queryAll();
					if($giftBag){
						$record_sid=$giftBag[0]['giftbag_id'];
					}
				}else{
					$record_sid=0;
				}
				$info=str_replace("'",'&#039;',$_giftRecord['info']);
				if(isset($giftRelation[0]['uid'])){
					$archivesCommand->setText("select * from web_archives where uid={$giftRelation[0]['uid']}");
					$archives=$archivesCommand->queryAll();
					$to_uid=$giftRelation[0]['uid'];
					$target_id=isset($archives[0]['archives_id'])?$archives[0]['archives_id']:0;
				}else{
					$to_uid=0;
					$target_id=0;
				}
				$giftData.=($giftData?"\n":"")."{$_giftRecord['record_id']},{$_giftRecord['uid']},{$to_uid},{$_giftRecord['present_id']},{$target_id},{$record_sid},{$_giftRecord['num']},{$_giftRecord['pipiegg']},{$_giftRecord['charm']},{$_giftRecord['egg_points']},{$_giftRecord['charm_points']},{$_giftRecord['dedication']},{$_giftRecord['gift_type']},{$_giftRecord['recevier_type']},{$_giftRecord['source']},{$info},{$_giftRecord['create_time']}\n";
			}
			$this->writeDbtext(TRANSFER_FILE.'web_user_giftsend_records.txt',$giftData);
			//$consumeCommand->setText('insert into web_user_giftsend_records (`record_id`,`uid`,`to_uid`,`gift_id`,`target_id`,`record_sid`,`num`,`pipiegg`,`charm`,`egg_points`,`charm_points`,`dedication`,`gift_type`,`recevier_type`,`source`,`info`,`create_time`) values '.$giftData);
			//$consumeCommand->execute();

		}
	}

	public function actionUserGiftRecordsRelation(){
		$this->getReadDbConnect();
		$showCommand=$this->showDb->createCommand();
		$consumeCommand=$this->consumeDb->createCommand();
		//$consumeCommand->setText('delete from  web_user_giftsend_relation_records;ALTER TABLE `web_user_giftsend_relation_records` AUTO_INCREMENT=1;');
		//$consumeCommand->execute();
		$showCommand->setText("select count(*) as count from web_user_gift_records_relation");
		$count=$showCommand->queryAll();
		$page=ceil($count[0]['count']/$this->pageSize);
		for($i=1;$i<=$page;$i++){
			echo "正在写入第".$i."条用户送礼关系记录数据\n";
			$showCommand->setText('select * from web_user_gift_records_relation order by rrecord_id asc limit '.(($i-1)*$this->pageSize).','.$this->pageSize);
			$giftRecord=$showCommand->queryAll();
			$giftData='';
			foreach($giftRecord as $_giftRecord){
				$giftData.=($giftData?"\n":"")."{$_giftRecord['rrecord_id']},{$_giftRecord['uid']},{$_giftRecord['record_id']},{$_giftRecord['is_onwer']},{$_giftRecord['create_time']}";
			}
			$this->writeDbtext(TRANSFER_FILE.'web_user_giftsend_relation_records.txt',$giftData);
			//$consumeCommand->setText('insert into web_user_giftsend_relation_records (`relation_id`,`uid`,`record_id`,`is_onwer`,`create_time`) values '.$giftData);
			//$consumeCommand->execute();
		}

	}

	public function actionDoteyCharmRecords(){
		$this->getReadDbConnect();
		$showCommand=$this->showDb->createCommand();
		$consumeCommand=$this->consumeDb->createCommand();
		$archivesCommand=$this->archviesDb->createCommand();
		//$consumeCommand->setText('delete from  web_dotey_charm_records;ALTER TABLE `web_dotey_charm_records` AUTO_INCREMENT=1;');
		//$consumeCommand->execute();

		$giftList=$this->getAllGift();
		//送礼产生的魅力值记录
		$showCommand->setText("select count(*) as count from  web_user_gift_records");
		$count=$showCommand->queryAll();
		$page=ceil($count[0]['count']/$this->pageSize);
		$number=1;
		for($i=1;$i<=$page;$i++){
			echo "正在写入第".$i."条主播送礼产生的魅力值记录数据\n";
			$showCommand->setText('select * from  web_user_gift_records order by record_id asc limit '.(($i-1)*$this->pageSize).','.$this->pageSize);
			$giftRecord=$showCommand->queryAll();
			$giftData='';
			foreach($giftRecord as $_giftRecord){
				$showCommand->setText("SELECT * FROM web_user_gift_records_relation where record_id={$_giftRecord['record_id']} AND is_onwer=0");
				$relationRecord=$showCommand->queryAll();
				$sender_uid=isset($relationRecord[0]['uid'])?$relationRecord[0]['uid']:0;
				if($_giftRecord['gift_type']==0){
					$sub_source='buyGifts';
				}
				if($_giftRecord['gift_type']==1){
					$sub_source='bagGifts';
				}
				if($_giftRecord['present_id']){
					$info=$giftList[$_giftRecord['present_id']]['zh_description'].'x'.$_giftRecord['num'];
				}
				$archivesCommand->setText("select * from web_archives where uid={$sender_uid}");
				$archives=$archivesCommand->queryAll();
				$target_id=isset($archives[0]['archives_id'])?$archives[0]['archives_id']:0;
				$giftData.=($giftData?"\n":"")."{$number},{$sender_uid},{$_giftRecord['uid']},{$target_id},{$_giftRecord['record_id']},{$_giftRecord['charm']},{$_giftRecord['num']},gifts,{$sub_source},{$_giftRecord['source']},{$info},{$_giftRecord['create_time']}";
				$number++;
			}
			
			$this->writeDbtext(TRANSFER_FILE.'web_dotey_charm_records.txt',$giftData);
			//$consumeCommand->setText('insert into web_dotey_charm_records (`uid`,`sender_uid`,`target_id`,`record_sid`,`charm`,`num`,`source`,`sub_source`,`client`,`info`,`create_time`) values '.$giftData);
			//$consumeCommand->execute();
		}

		//点歌产生的魅力值记录
		$showCommand->setText("select count(*) as count from  web_song_record where status='1'");
		$song_count=$showCommand->queryAll();
		$song_page=ceil($song_count[0]['count']/$this->pageSize);
		for($k=1;$k<=$song_page;$k++){
			echo "正在写入第".$k."条点歌产生的魅力点记录数据\n";
			$showCommand->setText("select * from  web_song_record where status='1' order by id asc limit ".(($k-1)*$this->pageSize).','.$this->pageSize);
			$songRecord=$showCommand->queryAll();
			$songData='';

			foreach($songRecord as $_songRecord){
				$info=htmlspecialchars($_songRecord['song_title'],ENT_QUOTES).'x1';
				$archivesCommand->setText("select * from web_archives where uid={$_songRecord['dotey_id']}");
				$archives=$archivesCommand->queryAll();
				$target_id=isset($archives[0]['archives_id'])?$archives[0]['archives_id']:0;
				$songData.=($songData?"\n":"")."{$number},{$_songRecord['dotey_id']},{$_songRecord['uid']},{$target_id},{$_songRecord['id']},{$_songRecord['charm']},1,songs,demondSongs,0,{$info},{$_songRecord['update_time']}";
			}
			$this->writeDbtext(TRANSFER_FILE.'web_dotey_charm_records.txt',$songData);
			$number++;
			//$consumeCommand->setText('insert into web_dotey_charm_records (`uid`,`sender_uid`,`target_id`,`record_sid`,`charm`,`num`,`source`,`sub_source`,`client`,`info`,`create_time`) values '.$songData);
			//$consumeCommand->execute();
		}

		//后台赠送产生的魅力值记录
		$showCommand->setText('select * from  web_exper_record where type="dotey" order by exrecord_id asc');
		$experRecord=$showCommand->queryAll();
		$j=1;
		$experData='';
		foreach($experRecord as $_experRecord){
			echo "正在写入第".$j."条主播后台赠送产生的魅力值记录数据\n";
			$experData.=($experData?"\n":"")."{$number},{$_experRecord['uid']},{$_experRecord['from_uid']},0,{$_experRecord['exrecord_id']},{$_experRecord['quantity']},1,sends,sendCharm,2,{$_experRecord['remark']},{$_experRecord['create_time']})";
			$this->writeDbtext(TRANSFER_FILE.'web_dotey_charm_records.txt',$experData);
			//$consumeCommand->setText("insert into web_dotey_charm_records (`uid`,`sender_uid`,`target_id`,`record_sid`,`charm`,`num`,`source`,`sub_source`,`client`,`info`,`create_time`) values ({$_experRecord['uid']},{$_experRecord['from_uid']},0,{$_experRecord['exrecord_id']},{$_experRecord['quantity']},1,'sends','sendCharm',2,'{$_experRecord['remark']}',{$_experRecord['create_time']})");
			//$consumeCommand->execute();
			$j++;
			$number++;
		}


	}

	public function actionDoteyCharmPointsRecords(){
		$this->getReadDbConnect();
		$showCommand=$this->showDb->createCommand();
		$consumeCommand=$this->consumeDb->createCommand();
		$archivesCommand=$this->archviesDb->createCommand();
		//$consumeCommand->setText('delete from  web_dotey_charmpoints_records;ALTER TABLE `web_dotey_charmpoints_records` AUTO_INCREMENT=1;');
		//$consumeCommand->execute();
		$showCommand->setText("select count(*) as count from  web_user_gift_records");
		$count=$showCommand->queryAll();
		$page=ceil($count[0]['count']/$this->pageSize);
		$giftList=$this->getAllGift();
		$number=1;
		for($i=1;$i<=$page;$i++){
			echo "正在写入第".$i."条主播魅力值记录数据\n";
			$showCommand->setText('select * from  web_user_gift_records order by record_id asc limit '.(($i-1)*$this->pageSize).','.$this->pageSize);
			$giftRecord=$showCommand->queryAll();
			$giftData='';
			foreach($giftRecord as $_giftRecord){
				$showCommand->setText("SELECT * FROM web_user_gift_records_relation where record_id={$_giftRecord['record_id']} AND is_onwer=0");
				$relationRecord=$showCommand->queryAll();
				$sender_uid=isset($relationRecord[0]['uid'])?$relationRecord[0]['uid']:0;
				if($_giftRecord['gift_type']==0){
					$sub_source='buyGifts';
				}
				if($_giftRecord['gift_type']==1){
					$sub_source='bagGifts';
				}
				if($_giftRecord['present_id']){
					$info=$giftList[$_giftRecord['present_id']]['zh_description'].'x'.$_giftRecord['num'];
				}
				$archivesCommand->setText("select * from web_archives where uid={$sender_uid}");
				$archives=$archivesCommand->queryAll();
				$target_id=isset($archives[0]['archives_id'])?$archives[0]['archives_id']:0;
				$giftData.=($giftData?"\n":"")."{$number},{$sender_uid},{$_giftRecord['uid']},{$target_id},{$_giftRecord['record_id']},{$_giftRecord['charm_points']},{$_giftRecord['num']},gifts,{$sub_source},{$_giftRecord['source']},{$info},{$_giftRecord['create_time']}";
				$number++;
			}
			$this->writeDbtext(TRANSFER_FILE.'web_dotey_charmpoints_records.txt',$giftData);
			//$consumeCommand->setText('insert into web_dotey_charmpoints_records (`uid`,`sender_uid`,`target_id`,`record_sid`,`charm_points`,`num`,`source`,`sub_source`,`client`,`info`,`create_time`) values '.$giftData);
			//$consumeCommand->execute();
		}

		//点歌产生的贡献值记录
		$showCommand->setText("select count(*) as count from  web_song_record where status='1'");
		$song_count=$showCommand->queryAll();
		$song_page=ceil($song_count[0]['count']/$this->pageSize);
		for($k=1;$k<=$song_page;$k++){
			echo "正在写入第".$k."条点歌产生的魅力点记录数据\n";
			$showCommand->setText("select * from  web_song_record where status='1' order by id asc limit ".(($k-1)*$this->pageSize).','.$this->pageSize);
			$songRecord=$showCommand->queryAll();
			$songData='';

			foreach($songRecord as $_songRecord){
				$info=htmlspecialchars($_songRecord['song_title'],ENT_QUOTES).'x1';
				$archivesCommand->setText("select * from web_archives where uid={$_songRecord['dotey_id']}");
				$archives=$archivesCommand->queryAll();
				$target_id=isset($archives[0]['archives_id'])?$archives[0]['archives_id']:0;
				$songData.=($songData?"\n":"")."{$number},{$_songRecord['dotey_id']},{$_songRecord['uid']},{$target_id},{$_songRecord['id']},{$_songRecord['charm']},1,songs,demondSongs,0,{$info},{$_songRecord['update_time']}";
				$number++;
			}
			$this->writeDbtext(TRANSFER_FILE.'web_dotey_charmpoints_records.txt',$songData);
			//$consumeCommand->setText('insert into web_dotey_charmpoints_records (`uid`,`sender_uid`,`target_id`,`record_sid`,`charm_points`,`num`,`source`,`sub_source`,`client`,`info`,`create_time`) values '.$songData);
			//$consumeCommand->execute();
		}

		//后台赠送产生的魅力点记录
		$showCommand->setText('select * from  web_exper_record where type="dotey" order by exrecord_id asc');
		$experRecord=$showCommand->queryAll();
		$experData='';
		$j=1;
		foreach($experRecord as $_experRecord){
			echo "正在写入第".$j."条主播后台赠送产生的魅力点记录数据\n";
			$experData.=($experData?"\n":"")."{$number},{$_experRecord['uid']},{$_experRecord['from_uid']},0,{$_experRecord['exrecord_id']},{$_experRecord['quantity']},1,sends,sendCharm,2,{$_experRecord['remark']},{$_experRecord['create_time']}";
			$this->writeDbtext(TRANSFER_FILE.'web_dotey_charmpoints_records.txt',$songData);
			//$consumeCommand->setText("insert into web_dotey_charmpoints_records (`uid`,`sender_uid`,`target_id`,`record_sid`,`charm_points`,`num`,`source`,`sub_source`,`client`,`info`,`create_time`) values ({$_experRecord['uid']},{$_experRecord['from_uid']},0,{$_experRecord['exrecord_id']},{$_experRecord['quantity']},1,'sends','sendCharm',2,'{$_experRecord['remark']}',{$_experRecord['create_time']})");
			//$consumeCommand->execute();
			$j++;
			$number++;
		}

	}
	public function  actionRepairDedication(){
		$this->getReadDbConnect();
		$consumeCommand=$this->consumeDb->createCommand();
		$consumeCommand->setText('select record_sid from  web_user_dedication_records where client =0 and to_target_id = 0 and source="gifts" and create_time>=1371470400');
		$dedicationRecord=$consumeCommand->queryAll();
		foreach($dedicationRecord as $row){
			$consumeCommand->setText("select record_id,target_id from  web_user_giftsend_records where record_id={$row['record_sid']}");
			$giftRecord=$consumeCommand->queryAll();
			if($giftRecord){
				if($giftRecord[0]['target_id']>0){
					echo "update web_user_dedication_records set to_target_id={$giftRecord[0]['target_id']} where record_sid={$giftRecord[0]['record_id']}\n";
					$consumeCommand->setText("update web_user_dedication_records set to_target_id={$giftRecord[0]['target_id']} where  client =0 and to_target_id = 0 and record_sid={$giftRecord[0]['record_id']}");
					$consumeCommand->execute();
				}
					
			}
		}
    }
    
    public function actionRepairSendGift(){
    	$this->getReadDbConnect();
    	$consumeCommand=$this->consumeDb->createCommand();
    	$archivesCommand=$this->archviesDb->createCommand();
    	$archivesCommand->setText("select * from web_archives");
    	$archives=$archivesCommand->queryAll();
    	$doteyList=array();
    	foreach($archives as $row){
    		$doteyList[$row['uid']]=$row['archives_id'];
    	}
    	$consumeCommand->setText("select count(*) as count from `web_user_giftsend_records` where target_id=0");
    	$count=$consumeCommand->queryAll();
    	$page=ceil($count[0]['count']/$this->pageSize);
    	echo "总页数".$page."\n";
    	for($i=1;$i<=$page;$i++){
    		echo "正在写入第".$i."页\n";
    		$consumeCommand->setText('select * from `web_user_giftsend_records` where target_id=0 limit '.(($i-1)*$this->pageSize).','.$this->pageSize);
    		$giftRecord=$consumeCommand->queryAll();
    		foreach($giftRecord as $row){
    			if(isset($doteyList[$row['to_uid']])){
    				echo "update `web_user_giftsend_records` set target_id={$doteyList[$row['to_uid']]} where record_id={$row['record_id']}\n";
    				$consumeCommand->setText("update `web_user_giftsend_records` set target_id={$doteyList[$row['to_uid']]} where record_id={$row['record_id']}");
    				$consumeCommand->execute();
    			}
    		}
    	}
    }
	
	public function actionUpdateDedication(){
		$this->getReadDbConnect();
		$consumeCommand=$this->consumeDb->createCommand();
		$consumeCommand->setText("select count(*) as count from  web_user_dedication_records where client =0 and to_target_id = 0 and  create_time > 1371470400 and source='gifts' ");
		$count=$consumeCommand->queryAll();
		$page=ceil($count[0]['count']/$this->pageSize);
		for($i=1;$i<=$page;$i++){
			$consumeCommand->setText('select * from  web_user_dedication_records where  client =0 and to_target_id = 0 and  create_time > 1371470400 and source="gifts"  limit '.(($i-1)*$this->pageSize).','.$this->pageSize);
			$dedicationRecord=$consumeCommand->queryAll();
			foreach($dedicationRecord as $row){
				$consumeCommand->setText("select * from  web_user_giftsend_records where  record_id={$row['record_sid']}");
				$giftRecord=$consumeCommand->queryAll();
				if($giftRecord){
					echo "update web_user_dedication_records set to_target_id={$giftRecord[0]['target_id']} where record_sid={$giftRecord[0]['record_id']}\n";
					$consumeCommand->setText("update web_user_dedication_records set to_target_id={$giftRecord[0]['target_id']} where  client =0 and to_target_id = 0 and record_sid={$giftRecord[0]['record_id']}");
					$consumeCommand->execute();
				}
			}
			
		}
	}

	public function actionUserDedicationRecords(){
		$this->getReadDbConnect();
		$showCommand=$this->showDb->createCommand();
		$consumeCommand=$this->consumeDb->createCommand();
		$archivesCommand=$this->archviesDb->createCommand();
		//$consumeCommand->setText('delete from  web_user_dedication_records;ALTER TABLE `web_user_dedication_records` AUTO_INCREMENT=1;');
		//$consumeCommand->execute();
		$showCommand->setText("select count(*) as count from  web_user_gift_records");
		$count=$showCommand->queryAll();
		$page=ceil($count[0]['count']/$this->pageSize);
		$giftList=$this->getAllGift();
		$number=1;
		for($i=1;$i<=$page;$i++){
			echo "正在写入第".$i."条用户贡献值记录数据\n";
			$showCommand->setText('select * from  web_user_gift_records order by record_id asc limit '.(($i-1)*$this->pageSize).','.$this->pageSize);
			$giftRecord=$showCommand->queryAll();
			$giftData='';
			foreach($giftRecord as $_giftRecord){
				if($_giftRecord['gift_type']==0){
					$sub_source='buyGifts';
				}
				if($_giftRecord['gift_type']==1){
					$sub_source='bagGifts';
				}
				if($_giftRecord['present_id']){
					$info=$giftList[$_giftRecord['present_id']]['zh_description'].'x'.$_giftRecord['num'];
				}
				$target_id=0;
				if(isset($giftRelation[0]['uid'])){
					$showCommand->setText("select * from web_user_gift_records_relation where record_id={$_giftRecord['record_id']} and is_onwer=0");
					$giftRelation=$showCommand->queryAll();
					$archivesCommand->setText("select * from web_archives where uid={$giftRelation[0]['uid']}");
					$archives=$archivesCommand->queryAll();
					$target_id=isset($archives[0]['archives_id'])?$archives[0]['archives_id']:0;
				}
				$giftData.=($giftData?"\n":"")."{$number},{$_giftRecord['uid']},{$_giftRecord['present_id']},{$target_id},{$_giftRecord['record_id']},{$_giftRecord['dedication']},{$_giftRecord['num']},gifts,{$sub_source},{$_giftRecord['source']},{$info},{$_giftRecord['create_time']}";
				$number++;
			}
			$this->writeDbtext(TRANSFER_FILE.'web_user_dedication_records.txt',$giftData);
			//$consumeCommand->setText('insert into web_user_dedication_records (`uid`,`from_target_id`,`to_target_id`,`record_sid`,`dedication`,`num`,`source`,`sub_source`,`client`,`info`,`create_time`) values '.$giftData);
			//$consumeCommand->execute();
		}

		//后台赠送产生的贡献值记录
		$showCommand->setText('select * from  web_exper_record where type="dotey" order by exrecord_id asc');
		$experRecord=$showCommand->queryAll();
		$j=1;
		$experData='';
		foreach($experRecord as $_experRecord){
			echo "正在写入第".$j."条后台赠送产生的贡献记录数据\n";
			$experData.=($experData?"\n":"")."{$number},{$_experRecord['uid']},0,0,{$_experRecord['exrecord_id']},{$_experRecord['quantity']},1,sends,sendDedication,2,{$_experRecord['remark']},{$_experRecord['create_time']}";
			$this->writeDbtext(TRANSFER_FILE.'web_user_dedication_records.txt',$experData);
			//$consumeCommand->setText("insert into web_user_dedication_records (`uid`,`from_target_id`,`to_target_id`,`record_sid`,`dedication`,`num`,`source`,`sub_source`,`client`,`info`,`create_time`) values ({$_experRecord['uid']},0,0,{$_experRecord['exrecord_id']},{$_experRecord['quantity']},1,'sends','sendDedication',2,'{$_experRecord['remark']}',{$_experRecord['create_time']})");
			//$consumeCommand->execute();
			$j++;
			$number++;
		}


		//点歌产生的贡献值记录
		$showCommand->setText("select count(*) as count from  web_song_record where status='1'");
		$song_count=$showCommand->queryAll();
		$song_page=ceil($song_count[0]['count']/$this->pageSize);
		for($k=1;$k<=$song_page;$k++){
			echo "正在写入第".$k."条点歌产生的贡献值记录数据\n";
			$showCommand->setText("select * from  web_song_record where status='1' order by id asc limit ".(($k-1)*$this->pageSize).','.$this->pageSize);
			$songRecord=$showCommand->queryAll();
			$songData='';

			foreach($songRecord as $_songRecord){
				$info=htmlspecialchars($_songRecord['song_title'],ENT_QUOTES).'x1';
				$archivesCommand->setText("select * from web_archives where uid={$_songRecord['dotey_id']}");
				$archives=$archivesCommand->queryAll();
				$target_id=isset($archives[0]['archives_id'])?$archives[0]['archives_id']:0;
				$songData.=($songData?"\n":"")."{$number},{$_songRecord['uid']},{$_songRecord['song_id']},{$target_id},{$_songRecord['id']},{$_songRecord['charm']},1,songs,demondSongs,0,{$info},{$_songRecord['update_time']}";
				$number++;
			}
			$this->writeDbtext(TRANSFER_FILE.'web_user_dedication_records.txt',$songData);
			//$consumeCommand->setText('insert into web_user_dedication_records (`uid`,`from_target_id`,`to_target_id`,`record_sid`,`dedication`,`num`,`source`,`sub_source`,`client`,`info`,`create_time`) values '.$songData);
			//$consumeCommand->execute();
		}
	}


	public function actionUserPipieggLog(){
		$this->getReadDbConnect();
		$showCommand=$this->showDb->createCommand();
		$consumeCommand=$this->consumeDb->createCommand();
		$ucenterCommand=$this->ucenterDb->createCommand();
		//$consumeCommand->setText('delete from  web_user_pipiegg_records;ALTER TABLE `web_user_pipiegg_records` AUTO_INCREMENT=1;');
		//$consumeCommand->execute();
		$ucenterCommand->setText("select count(*) as count from  uc_pipiegglog");
		$count=$ucenterCommand->queryAll();
		$page=ceil($count[0]['count']/$this->pageSize);

		$pipiSource=array(
			'activity_award'=>array('source'=>'recharge','sub_source'=>'activity_award'), //活动奖励皮蛋，由张晗代充
			'addpipiegg'=>array('source'=>'recharge','sub_source'=>'addpipiegg'), //真正的皮蛋充值，用户付费后的充值
			'BreakGoldegg'=>array('source'=>'games','sub_source'=>'BreakGoldegg'),
			'car'=>array('source'=>'props','sub_source'=>'car'),
			'check_in_bu'=>array('source'=>'others','sub_source'=>'check_in_bu'),
			'endday'	=>array('source'=>'activity','sub_source'=>'endday'),
			'freebag'	=>array('source'=>'activity','sub_source'=>'freebag'),
			'OpenBox'	=>array('source'=>'games','sub_source'=>'OpenBox'),
			'other_recharge'	=>array('source'=>'recharge','sub_source'=>'other_recharge'), //陪玩充值
			'props'	=>array('source'=>'props','sub_source'=>'flyscreen'),
			'proxy_recharge'	=>array('source'=>'recharge','sub_source'=>'proxy_recharge'), //客服代充，由杨赛负责
			'ScratchCard'	=>array('source'=>'games','sub_source'=>'ScratchCard'), //游戏，刮刮卡
			'ScratchCardPrize'	=>array('source'=>'games','sub_source'=>'ScratchCardPrize'), //游戏，刮刮卡
			'show_checkin'	=>array('source'=>'others','sub_source'=>'checkin'),
			'show_label_stick'	=>array('source'=>'props','sub_source'=>'label'),
			'show_lovebm'	=>array('source'=>'others','sub_source'=>'show_lovebm'),
			'show_remove_label'	=>array('source'=>'props','sub_source'=>'remove_label'),
			'show_sendgift'	=>array('source'=>'gifts','sub_source'=>'sendGifts'),
			'show_shop_gift'	=>array('source'=>'gifts','sub_source'=>'shopGifts'),
			'show_shop_monthcard'	=>array('source'=>'props','sub_source'=>'monthcard'),
			'show_song'	=>array('source'=>'songs','sub_source'=>'demondSongs'),
			'show_task'	=>array('source'=>'tasks','sub_source'=>'task'),
			'show_tiwen'	=>array('source'=>'activity','sub_source'=>'tiwen'),
			'show_topvote'	=>array('source'=>'others','sub_source'=>'topvote'),
			'show_chat'	=>array('source'=>'others','sub_source'=>'show_chat'),
			'SofaBet'	=>array('source'=>'games','sub_source'=>'SofaBet'),
			'SofaDoAction'	=>array('source'=>'games','sub_source'=>'SofaDoAction'),
			'SofaLottery'	=>array('source'=>'games','sub_source'=>'SofaLottery'),
			'SofaSitdown'	=>array('source'=>'games','sub_source'=>'SofaSitdown'),
			'system'	=>array('source'=>'recharge','sub_source'=>'system'), //系统测试充值，目前只有张芝凡有权限充
			'transfer_accounts'	=>array('source'=>'recharge','sub_source'=>'transfer_accounts'),
			'vip'	=>array('source'=>'props','sub_source'=>'vip'),
			'yearfree'	=>array('source'=>'activity','sub_source'=>'yearfree'),
			'yiruite'	=>array('source'=>'activity','sub_source'=>'yiruite'),
			'主播调查局活动提问'	=>array('source'=>'activity','sub_source'=>'tiwen'),
			'屌丝女神活动抽奖'	=>array('source'=>'activity','sub_source'=>'diosDraw'),
			'秀场'=>array('source'=>'gifts','sub_source'=>'sendGifts')
			);
		for($i=1;$i<=$page;$i++){
			echo "正在写入第".$i."条收支记录数据\n";
			$ucenterCommand->setText("select * from  uc_pipiegglog order by id asc limit ".(($i-1)*$this->pageSize).','.$this->pageSize);
			$pipieggLog=$ucenterCommand->queryAll();
			$pipieggData='';
			foreach($pipieggLog as $_pipieggLog){
				if($_pipieggLog['psource']=='show_use_common_labe'||$_pipieggLog['psource']=='show_use_vip') break;
				$source=$pipiSource[$_pipieggLog['psource']]['source'];
				$sub_source=$pipiSource[$_pipieggLog['psource']]['sub_source'];
				$to_target_id=$record_sid=0;
				if($_pipieggLog['psource']=='car'||$_pipieggLog['psource']=='props'||$_pipieggLog['psource']=='show_shop_monthcard'||$_pipieggLog['psource']=='vip'){
					$from_target_id=isset($_pipieggLog['porderid'])?$_pipieggLog['porderid']:0;
					$showCommand->setText("select * from web_user_props where uid={$_pipieggLog['puid']}  and prop_id={$_pipieggLog['porderid']} and ctime={$_pipieggLog['ptime']}");
					$props=$showCommand->queryAll();
					$num=isset($props[0]['amount'])?$props[0]['amount']:0;
					$record_sid=isset($props[0]['rpid'])?$props[0]['rpid']:0;
				}else if($_pipieggLog['psource']=='show_label_stick'||$_pipieggLog['psource']=='show_remove_label'){
					$record_sid=isset($_pipieggLog['porderid'])?$_pipieggLog['porderid']:0;
					$showCommand->setText("select * from web_user_props where rpid={$_pipieggLog['porderid']}");
					$props=$showCommand->queryAll();
					$from_target_id=isset($props[0]['prop_id'])?$props[0]['prop_id']:0;
					$num=isset($props[0]['amount'])?$props[0]['amount']:0;
				}else if($_pipieggLog['psource']=='show_sendgift'||$_pipieggLog['psource']=='秀场'){
					$record_sid=isset($_pipieggLog['porderid'])?$_pipieggLog['porderid']:0;
					$showCommand->setText("select * from web_user_gift_records where record_id={$_pipieggLog['porderid']}");
					$gifts=$showCommand->queryAll();
					$showCommand->setText("select * from web_user_gift_records_relation where record_id={$_pipieggLog['porderid']} AND is_onwer=0");
					$giftRelation=$showCommand->queryAll();
					if($giftRelation){
						$to_target_id=$giftRelation[0]['uid'];
					}
					if($gifts){
						$from_target_id=$gifts[0]['present_id'];
						$num=isset($gifts[0]['num'])?$gifts[0]['num']:0;
					}

				}else if($_pipieggLog['psource']=='show_shop_gift'){
					if($_pipieggLog['porderid']!=1){
						$record_sid=isset($_pipieggLog['porderid'])?$_pipieggLog['porderid']:0;
						$showCommand->setText("select * from web_bag_record where brecord_id={$_pipieggLog['porderid']}");
						$bagRecord=$showCommand->queryAll();
						$from_target_id=$bagRecord[0]['present_id'];
						$num=isset($bagRecord[0]['quantity'])?$bagRecord[0]['quantity']:0;
					}
					$client=1;
				}else{
					$to_target_id=0;
					$from_target_id=0;
					$num=0;
				}
				$client=0;
				$pipiegg=($_pipieggLog['money']>0)?'-'.abs($_pipieggLog['money']):abs($_pipieggLog['money']);
				$pipieggData.=($pipieggData?"\n":"")."{$_pipieggLog['id']},{$_pipieggLog['puid']},{$from_target_id},{$to_target_id},{$record_sid},{$pipiegg},{$source},{$sub_source},{$client},{$num},{$_pipieggLog['pip']},{$_pipieggLog['summary']},{$_pipieggLog['ptime']},{$_pipieggLog['cbalance']}";
			}
			$this->writeDbtext(TRANSFER_FILE.'web_user_pipiegg_records.txt',$pipieggData);
			//$consumeCommand->setText('insert into web_user_pipiegg_records (`record_id`,`uid`,`from_target_id`,`to_target_id`,`record_sid`,`pipiegg`,`source`,`sub_source`,`client`,`num`,`ip_address`,`extra`,`consume_time`,`cbalance`) values '.$pipieggData);
			//$consumeCommand->execute();
		}
	}
	
	function writeDbtext($file_name,$data,$method="a+") {
		$filenum=@fopen($file_name,$method);
		flock($filenum,LOCK_EX);
		$file_data=fwrite($filenum,$data);
		fclose($filenum);
	}

	public function getAllGift(){
		$this->getReadDbConnect();
		$showCommand=$this->showDb->createCommand();
		//获取所有礼物
		$showCommand->setText("select * from  web_present where parent_id>0");
		$gift=$showCommand->queryAll();
		$giftList=array();
		foreach($gift as $_gift){
			$giftList[$_gift['present_id']]=$_gift;
		}
		return $giftList;
	}

	private function getReadDbConnect(){
		$this->showDb=Yii::app()->db_read_pipishow;
		$this->consumeDb=Yii::app()->db_consume_records;
		$this->ucenterDb=Yii::app()->db_read_ucenter;
		$this->archviesDb=Yii::app()->db_archives;
	}

}

?>
