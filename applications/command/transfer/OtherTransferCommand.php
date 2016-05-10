<?php
/**
 * 转移主播数据
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z suqian $ 
 * @package
 */
class OtherTransferCommand extends CConsoleCommand {
		/**
		 * @var CDbConnection 老版乐天读库操作
		 */
		protected $show_db;
		
		/**
		 * @var CDbConnection 新版用户写库操作
		 */
		protected $user_db;
		
		/**
		 * @var CDbConnection
		 */
		protected $weibo_db;
		
		/**
		 * @var CDbConnection 新版消费库操作
		 */
		protected $consume_db;
		
		public function beforeAction($action,$params){
			$this->show_db = Yii::app()->db_read_pipishow;
			$this->user_db = Yii::app()->db_user;;
			$this->weibo_db = Yii::app()->db_weibo;;
			$this->consume_db = Yii::app()->db_consume;
			return true;
		}
		
		public function actionUserFans(){
			$service = new PipiService();
			$showDbCommand = $this->show_db->createCommand();
			$userDbCommand =  $this->user_db->createCommand();
			$userDbDoteyCommand =  $this->user_db->createCommand();
			$weiboDbCommand = $this->weibo_db->createCommand();
			$userDbCommand->setText('delete from web_user_fans');
			$userDbCommand->execute();
			
			$userDbDoteyCommand->setText('delete from web_dotey_fans');
			$userDbDoteyCommand->execute();
			
			$showDbCommand->setText('select count(*) from web_user_attention');
			$count = $showDbCommand->queryScalar();
			$pageSize = 1000;
			$pages = ceil($count / $pageSize);
			echo "开始转移主播粉丝数据\r\n";
			$userDbCommand->setText('replace into web_user_fans(uid,fans_uid,create_time)values(:uid,:fans_uid,:create_time)');
			$userDbDoteyCommand->setText('replace into web_dotey_fans(uid,fans_uid,create_time)values(:uid,:fans_uid,:create_time)');
			for($i=1;$i<=$pages;$i++){
				echo "开始转移第{$i}组数据,每组{$pageSize}人\n\r";
				
				$showDbCommand->setText('');
				$offset = ($i-1)*$pageSize;
				$allFans = $showDbCommand->from('web_user_attention')->limit($pageSize,$offset)->queryAll();
				foreach($allFans as $fans){
					$userDbCommand->bindValue(':uid',$fans['dotey_id']);
					$userDbCommand->bindValue(':fans_uid',$fans['uid']);
					$userDbCommand->bindValue(':create_time',$fans['create_time']);
					$userDbCommand->execute();
					
					$userDbDoteyCommand->bindValue(':uid',$fans['dotey_id']);
					$userDbDoteyCommand->bindValue(':fans_uid',$fans['uid']);
					$userDbDoteyCommand->bindValue(':create_time',$fans['create_time']);
					$userDbDoteyCommand->execute();
				}
				echo "开始转移第{$i}组数据结束\n\r";
				
				
			}
			echo "转移主播粉丝数据结束\r\n";
		}
		
		public function actionUserFansStatics(){
			echo "开始转移关注数粉丝统计\r\n";
			$service = new PipiService();
			$weiboDbCommand = $this->weibo_db->createCommand();
			$showDbCommand = $this->show_db->createCommand();
			$weiboDbCommand->setText('delete from web_user_weibo_statistics');
			$weiboDbCommand->execute();
			$showDbCommand->setText('SELECT uid,count(dotey_id) a FROM `web_user_attention` group by uid ORDER BY a DESC');
			$allAttentions  = $showDbCommand->queryAll();
			$showDbCommand->setText('SELECT dotey_id,count(uid) f from `web_user_attention` group by dotey_id ORDER BY f DESC');
			$allFans = $showDbCommand->queryAll();
			$allFans = $service->buildDataByIndex($allFans,'dotey_id');
			$weiboDbCommand->setText('insert into web_user_weibo_statistics (uid,fans,attentions)values(:uid,:fans,:attentions)');
			foreach($allAttentions as $attentions){
				$uid = $attentions['uid'];
				$fans = isset($allFans[$uid]) ? $allFans[$uid]['f']  : 0;
				$weiboDbCommand->bindValue(':uid',$uid);
				$weiboDbCommand->bindValue(':fans',$fans);
				$weiboDbCommand->bindValue(':attentions',$attentions['a']);
				$weiboDbCommand->execute();
				if(isset($allFans[$uid]))  unset($allFans[$uid]);
				//$userDbCommand
			}
			$weiboDbCommand->setText('insert into web_user_weibo_statistics (uid,fans)values(:uid,:fans)');
			foreach($allFans as $fans){
				$weiboDbCommand->bindValue(':uid',$fans['dotey_id']);
				$weiboDbCommand->bindValue(':fans',$fans['f']);
				$weiboDbCommand->execute();
			}
			echo "转移关注数粉丝统计结束";
		}
		
		public function actionUserMedals(){
			
			$showDbCommand = $this->show_db->createCommand();
			$consumeDbCommand =  $this->consume_db->createCommand();
			$consumeDbCommand->setText('delete from web_medal_list');
			$consumeDbCommand->execute();//web_medal_list web_user_medal
			
			$showDbCommand->setText('select * from web_medal_list');
			$medalList =  $showDbCommand->queryAll();
			$consumeDbCommand->setText('insert into web_medal_list(mid,`name`,type,`desc`,icon,ctime)values(:mid,:name,:type,:desc,:icon,:ctime)');
			echo "转移勋章开始\r\n";
			foreach ($medalList as $medal){
				$consumeDbCommand->bindValue(':mid',$medal['mid']);
				$consumeDbCommand->bindValue(':name',$medal['name']);
				$consumeDbCommand->bindValue(':type',$medal['type']);
				$consumeDbCommand->bindValue(':desc',$medal['desc']);
				$consumeDbCommand->bindValue(':icon',$medal['icon']);
				$consumeDbCommand->bindValue(':ctime',$medal['ctime']);
				$consumeDbCommand->execute();
			}
			echo "转移勋章结束\r\n";
			$showDbCommand->setText('select * from web_user_medal');
			$consumeDbCommand->setText('delete from web_user_medal');
			$consumeDbCommand->execute();//web_medal_list web_user_medal
			$userMedalList =  $showDbCommand->queryAll();
			$consumeDbCommand->setText('insert into web_user_medal(rid,mid,uid,type,ctime,vtime)values(:rid,:mid,:uid,:type,:ctime,:vtime)');
			echo "转移用户勋章开始\r\n";
			foreach($userMedalList as $umedal){
				$consumeDbCommand->bindValue(':rid',$umedal['rid']);
				$consumeDbCommand->bindValue(':mid',$umedal['mid']);
				$consumeDbCommand->bindValue(':uid',$umedal['uid']);
				$consumeDbCommand->bindValue(':type',$umedal['type']);
				$consumeDbCommand->bindValue(':ctime',$umedal['ctime']);
				$consumeDbCommand->bindValue(':vtime',$umedal['vtime']);
				$consumeDbCommand->execute();
			}
			echo "转移用户勋章结束\r\n";
			
		}
		
		public function actionUserSuggest(){
			$showDbCommand = $this->show_db->createCommand();
			$common_db = Yii::app()->db_common;
			$commonDbCommand = $common_db->createCommand() ;
			$commonDbCommand->delete('web_user_suggest');
			$showDbCommand->setText('SELECT * FROM  web_user_suggest');
			$suggests = $showDbCommand->queryAll();
			echo "开始转移用户反馈数据\n\r";
			$newData = '';
			foreach($suggests as $suggest){
				$newData .= ($newData ? ',' : '').'('.$suggest['suggest_id'].','.$suggest['uid'].','.$suggest['type'].','.$suggest['is_handle'].',"'.addslashes($suggest['contact']).'","'.addslashes($suggest['content']).'","'.$suggest['attach'].'",'.$suggest['create_time'].')';
			}
			$commonDbCommand->setText('insert into web_user_suggest (suggest_id,uid,type,is_handle,contact,content,attach,create_time) values '.$newData);
			$commonDbCommand->execute();
		}
		
		
		public function actionUserRank(){
			$showDbCommand = $this->show_db->createCommand();
			$consumeDbCommand = $this->consume_db->createCommand() ;
			$consumeDbCommand->delete('web_user_rank');
			$showDbCommand->setText('SELECT * FROM  web_user_rank');
			$ranks = $showDbCommand->queryAll();
			echo "开始转移用户等级数据\n\r";
			$newData = '';
			foreach($ranks as $rank){
				$newData .= ($newData ? ',' : '').'('.$rank['urank_id'].','.$rank['grade'].',"'.$rank['name'].'",'.$rank['dedication'].','.$rank['mgr_num'].')';
			}
			$consumeDbCommand->setText('insert into web_user_rank (rank_id,rank,name,dedication,house_m_num) values '.$newData);
			$consumeDbCommand->execute();
		}
		
		public function actionDoteyRank(){
			$showDbCommand = $this->show_db->createCommand();
			$consumeDbCommand = $this->consume_db->createCommand() ;
			$consumeDbCommand->delete('web_dotey_rank');
			$showDbCommand->setText('SELECT * FROM  web_dotey_rank');
			$ranks = $showDbCommand->queryAll();
			echo "开始转移用户等级数据\n\r";
			$newData = '';
			foreach($ranks as $rank){
				$newData .= ($newData ? ',' : '').'('.$rank['drank_id'].','.$rank['grade'].',"'.$rank['name'].'",'.$rank['charm_num'].','.$rank['mgr_num'].')';
			}
			$consumeDbCommand->setText('insert into web_dotey_rank (rank_id,rank,name,charm,house_m_num) values '.$newData);
			$consumeDbCommand->execute();
		}
		
		public function actionBlackWord()
		{
			$showDbCommand = $this->show_db->createCommand();
			$common_db = Yii::app()->db_common;
			$commonDbCommand = $common_db->createCommand() ;
			$commonDbCommand->delete('web_black_word');
			$showDbCommand->setText('SELECT * FROM  web_black_word');
			$words = $showDbCommand->queryAll();
			echo "开始转移黑名单\n\r";
			$newData = '';
			foreach($words as $word){
				$newData .= ($newData ? ',' : '').'(\''.$word['name'].'\','.($word['type'] - 1).',\''.$word['replace'].'\',0,1,0)';
			}
			$showDbCommand->setText('SELECT * FROM web_badwords');
			$nicknames = $showDbCommand->queryAll();
			foreach($nicknames as $nk){
				$newData .= ($newData ? ',' : '')."('".$nk['find']."',1,'".$nk['replacement']."',1,1,0)";
			}
			$sql = 'insert into web_black_word (`name`,`type`,`replace`,`word_type`,`status`,`displayorder`) values '.$newData;
			$commonDbCommand->setText($sql);
			$commonDbCommand->execute();
			echo "转移黑名单结束\n\r";
			
		}
}