<?php
/**
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z suqian $ 
 * @package
 */
class ArchiveCommand extends PipiConsoleCommand{
	/**
	 * @var CDbConnection
	 */
	public $archives_db;
	/**
	 * @var CDbConnection
	 */
	public $archives_read;
	/**
	 * @var ArchivesService 档期服务层
	 */
	public $archivesService;
	protected $mail_list = array();
	
	public function beforeAction($action,$params){
		parent::beforeAction($action, $params);
		$this->archives_db=Yii::app()->db_archives;
		$this->archives_read = Yii::app()->db_archives_slave;
		$this->archivesService = new ArchivesService();
		$this->mail_list = array(
			"liyong@pipi.cn",
			"wuyaobo@pipi.cn",
			"hexin@pipi.cn",
		);
		return true;
	}
	
	/**
	 * 清理观看记录里无校的 和结束直播的记录，每天早上6点钟跑一次
	 */
	public function actionLatestSeeView(){
		$archivesDbCommand = $this->archives_db->createCommand();
		$archivesDbCommand->setText(' SELECT GROUP_CONCAT(DISTINCT a.archives_record_id) FROM `web_user_archives_view` a LEFT JOIN `web_live_records` b ON a.archives_id=b.archives_id AND a.archives_record_id=b.record_id WHERE status in (-1,2)');
		$archives_ids =  $archivesDbCommand->queryScalar();
		echo $archives_ids."\r\n";
		if($archives_ids){
			$archivesDbCommand->setText('DELETE FROM web_user_archives_view WHERE archives_record_id IN ('.$archives_ids.')');
			echo $archivesDbCommand->execute();
		}
	}
	
	/**
	 * 直播间的超级榜，手机端使用
	 */
	public function actionSuperTop(){
		$sql = "SELECT archives_id FROM web_archives WHERE is_hide = 0";
		$archives = $this->archives_db->createCommand()->setText($sql)->queryColumn();
		
		$record_db = Yii::app()->db_consume_records;
		$consume_db = Yii::app()->db_consume;
		$user_db = Yii::app()->db_user;
		$tops = $uids = $user = $consume = array();
		foreach($archives as $id){
			$sql = "SELECT uid, sum(dedication) as dedication FROM `web_user_dedication_records` where to_target_id = ".$id." and (source = 'gifts' or source = 'songs') and client = 0 group by uid order by dedication desc limit 10";
			$rs = $record_db->createCommand()->setText($sql)->queryAll();
			if(!empty($rs)){
				$tops[$id] = $rs;
				foreach($tops[$id] as $v){
					if(!in_array($v['uid'], $uids)) $uids[] = $v['uid'];
				}
			}
		}
		
		if(!empty($uids)){
			$sql = 'SELECT uid, nickname FROM web_user_base WHERE uid in ('.implode(',', $uids).')';
			$temp = $user_db->createCommand()->setText($sql)->queryAll();
			foreach($temp as $t){
				$user[$t['uid']] = $t['nickname'];
			}
			
			$sql = 'SELECT uid, rank FROM web_user_consume_attribute WHERE uid in ('.implode(',', $uids).')';
			$temp = $consume_db->createCommand()->setText($sql)->queryAll();
			foreach($temp as $t){
				$consume[$t['uid']] = $t['rank'];
			}
		}
		foreach($tops as $id => $top){
			$data = array();
			foreach($top as $t){
				$data[] = array(
					'uid'		 => $t['uid'],
					'dedication' => $t['dedication'],
					'nickname'	 => $user[$t['uid']],
					'rank'		 => $consume[$t['uid']]
				);
			}
			OtherRedisModel::getInstance()->setArchivesRelationData($id, $data);
		}
	}
	
	/**
	 * 每天查看视频资源的分布情况，尤其是bgp资源，每天的6点，12点，17点检测
	 */
	public function actionViewGbp(){
		$hour = date('H');
		if($hour == '06' || $hour == '12' || $hour == '17'){
			$filePath=DATA_PATH."runtimes".DIR_SEP."stat".DIR_SEP;
			$fileName=$filePath."VideoHost.csv";
			$this->createdir($filePath);
			
			$sql = 'SELECT a.server_id, count(*) as count, l.import_host, l.export_host, use_num FROM `web_archives_live_server` a LEFT JOIN web_live_server l on l.server_id = a.server_id group by a.server_id;';
			$list = $this->archives_read->createCommand()->setText($sql)->queryAll();
			
			//写入附件
			$file = fopen($fileName,"w");
			$title = array_keys($list[0]);
			fputcsv($file,$title);
			foreach($list as $v){
				fputcsv($file, $v);
			}
			fclose($file);
			
			$files = array();
			$files[] = $fileName;
			$body = '附件是'.date('Y-m-d')."日 ".$hour."时的视频服务器地址分布情况";
			$this->sendMail($this->mail_list, date('Y-m-d日H时')."视频服务器地址分布情况", $body, $files);
		}
	}
	
	private  function createdir($path,$mode=0755){
		if (is_dir($path)){
			return true;
		}else{
			$re=mkdir($path,$mode,true);
			if ($re){
				return true;
			}else{
				return false;
			}
		}
	}
}