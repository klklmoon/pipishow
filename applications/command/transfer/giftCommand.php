<?php
define('GIFT_PAHT', dirname(ROOT_PATH).DIR_SEP.'uploadimg'.DIR_SEP);
define('TARGET_GIFT_PAHT',IMAGES_PATH."gift".DIR_SEP);
class giftCommand extends CConsoleCommand {
	public $showDb;
	
	public $consumeDb;
	
	public $pageSize=1000;
	
	public function actionGiftCat(){
		$this->getReadDbConnect();
		$showCommand=$this->showDb->createCommand();
		$consumeCommand=$this->consumeDb->createCommand();
		$consumeCommand->setText('delete from  web_gift_category');
		$consumeCommand->execute();
		$showCommand->setText("select * from web_present where parent_id=0 and status=1");
		$giftCategory=$showCommand->queryAll();
		echo "查询礼物分类...\n";
		echo "写入礼物分类...\n";
		$cateSql="insert into web_gift_category (`category_id`,`cat_name`,`cat_enname`) VALUES ";
		$i=1;
		foreach($giftCategory as $row){
			$cate[]="({$i},'{$row['zh_description']}','{$row['en_description']}')";
			$i++;
		}
		
		$cateSql.=implode(',',$cate);
		
		$consumeCommand->setText($cateSql);
		$consumeCommand->execute();
		
		
	}
	
	
	
	public function actionGift(){
		$this->getReadDbConnect();
		$showCommand=$this->showDb->createCommand();
		$consumeCommand=$this->consumeDb->createCommand();
		
		echo "清空礼物信息表\n\r";
		$consumeCommand->setText('delete from  web_gift_info;alter table web_gift_info auto_increment=1');
		$consumeCommand->execute();
		
		$showCommand->setText("select present_id,en_description,zh_description from web_present where parent_id=0");
		$oldCat=$showCommand->queryAll();
		foreach($oldCat as $row){
			$oldCatList[$row['present_id']]=$row['en_description'];
		}
		
		$consumeCommand->setText("select * from web_gift_category");
		$newCat=$consumeCommand->queryAll();
		foreach ($newCat as $_newCat){
			$newCatList[$_newCat['cat_enname']]=$_newCat['category_id'];
		}
		
		
		$showCommand->setText("select * from web_present where parent_id>0");
		$gift=$showCommand->queryAll();
		$giftPath=  dirname(dirname(dirname(dirname(__FILE__)))).DIR_SEP."images".DIR_SEP."gift".DIR_SEP;
		
		
		echo "查询礼物...\n";
		$giftData='';
		foreach($gift as $row){
			$gift_type=($row['is_shop']==1)?4:1;
			if($row['recom']>0){
				$shop_type=8;
			}else if($row['tip']==1){
				$shop_type=4;
			}else if($row['tip']==2){
				$shop_type=2;
			}else{
				$shop_type=1;
			}
			$picture=explode('/',$row['picture']);
			if($row['picture']){
				@copy(GIFT_PAHT.$row['picture'],TARGET_GIFT_PAHT.$picture[1]);
			}
			$is_display=empty($row['status'])?0:1;
			if(isset($newCatList[$oldCatList[$row['parent_id']]])){
				$catId=$newCatList[$oldCatList[$row['parent_id']]];
			}else{
				$catId=0;
				$is_display=0;
			}
			$giftData.=($giftData ? ',' : '')."({$row['present_id']},'{$catId}','{$row['zh_description']}','{$row['en_description']}',{$gift_type},{$shop_type},{$is_display},'{$picture[1]}',{$row['price']},{$row['charm']},{$row['charm_points']},{$row['dedication']},{$row['egg_points']},{$row['buy_limit']},{$row['quantity']},{$row['grade']},{$row['sort']},{$row['update_time']})";
		}
		$consumeCommand->setText('insert into web_gift_info (`gift_id`,`cat_id`,`zh_name`,`en_name`,`gift_type`,`shop_type`,`is_display`,`image`,`pipiegg`,`charm`,`charm_points`,`dedication`,`egg_points`,`buy_limit`,`sell_nums`,`sell_grade`,`sort`,`update_time`) VALUES '.$giftData);
		$result=$consumeCommand->execute();
		echo "写入礼物记录\n";
		if($result){
			echo "共写入".$result."条礼物";
		}else{
			echo "礼物写入失败";
		}
	}
	
	
	public function actionGiftEffect(){
		$this->getReadDbConnect();
		$showCommand=$this->showDb->createCommand();
		$consumeCommand=$this->consumeDb->createCommand();
		
		echo "清空礼物特效表\n\r";
		$consumeCommand->setText('delete from  web_gift_effect;alter table web_gift_effect auto_increment=1');
		$consumeCommand->execute();
		$giftPath=  dirname(dirname(dirname(dirname(__FILE__)))).DIR_SEP."images".DIR_SEP."gift".DIR_SEP."effect".DIR_SEP;
		
		$showCommand->setText('select * from web_gift_effect');
		$oldGiftEffect=$showCommand->queryAll();
		$effectData='';
		$i=1;
		foreach($oldGiftEffect as $row){
			if($row['picture']){
				$effect=explode('/',$row['picture']);
				@copy(GIFT_PAHT.$row['picture'],TARGET_GIFT_PAHT.'effect/'.$effect[1]);
				$effectData.=($effectData?',':'')."({$i},{$row['present_id']},1,{$row['quantity']},{$row['timeout']},{$row['position']},'{$effect[1]}')";
				$i++;
			}
		}
		
		$showCommand->setText('select * from web_present');
		$oldGift=$showCommand->queryAll();
		
		foreach($oldGift as $_oldGift){
			if($_oldGift['gift_img']){
				$effect=explode('/',$_oldGift['gift_img']);
				@copy(GIFT_PAHT.$_oldGift['gift_img'],TARGET_GIFT_PAHT.'effect/'.$effect[1]);
				$effectData.=($effectData?',':'')."({$i},{$_oldGift['present_id']},2,1,{$_oldGift['timeout']},1,'{$effect[1]}')";
				$i++;
			}
			
		}
		
		$consumeCommand->setText('insert into web_gift_effect (`effect_id`,`gift_id`,`effect_type`,`num`,`timeout`,`position`,`effect`) values '.$effectData);
		$result=$consumeCommand->execute();
		if($result){
			echo "共写入".$result."条礼物特效记录";
		}else{
			echo "礼物特效写入失败";
		}
		
	}
	
	
	public function actionUserBag(){
		$this->getReadDbConnect();
		$showCommand=$this->showDb->createCommand();
		$consumeCommand=$this->consumeDb->createCommand();
		
		echo "清空用户背包表\n\r";
		$consumeCommand->setText('delete from  web_user_gift_bag;alter table web_user_gift_bag auto_increment=1');
		$consumeCommand->execute();
		$showCommand->setText("select count(*) as count from web_gift_bag");
		$count=$showCommand->queryAll();
		$page=ceil($count[0]['count']/$this->pageSize);
		for($i=1;$i<=$page;$i++){
			$showCommand->setText('SELECT * FROM `web_gift_bag` limit '.(($i-1)*$this->pageSize).','.$this->pageSize);
			$oldUserBag=$showCommand->queryAll();
			$userBagData='';
			foreach($oldUserBag as $_oldUserBag){
				$userBagData.=($userBagData?',':'')."({$_oldUserBag['uid']},{$_oldUserBag['present_id']},{$_oldUserBag['quantity']})";
			}
			
			$consumeCommand->setText('insert into web_user_gift_bag (`uid`,`gift_id`,`num`) values '.$userBagData);
			$result=$consumeCommand->execute();
		}
	}
	
	public function actionReduceUserBagRecord(){
		$this->getReadDbConnect();
		$consumeCommand=$this->consumeDb->createCommand();
		echo "删除背包表中多余的用户礼物记录\n\r";
		$consumeCommand->setText('select uid,gift_id,count(*) as aa from web_user_gift_bag group by uid,gift_id HAVING aa>1');
		$record=$consumeCommand->queryAll();
		foreach($record as $row){
			echo 'select * from web_user_gift_bag where uid='.$row['uid'].' and gift_id='.$row['gift_id']."\n\r";
			$consumeCommand->setText('select * from web_user_gift_bag where uid='.$row['uid'].' and gift_id='.$row['gift_id']);
			$bag=$consumeCommand->queryAll();
			if(count($bag)>1){
				foreach($bag as $key=>$val){
					if($key>0){
						echo 'delete from web_user_gift_bag where bag_id='.$val['bag_id']."\n\r";
						//$consumeCommand->setText('delete from web_user_gift_bag where bag_id='.$val['bag_id']);
						//$consumeCommand->execute();
					}
					
				}
			}
		}
		
	}
	
	
	private function getReadDbConnect(){
		$this->showDb=Yii::app()->db_read_pipishow;
		$this->consumeDb=Yii::app()->db_consume;
		
	}
	
}

?>