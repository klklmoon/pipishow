<?php
/**
 * 统计富豪1以上的注册用户的消费、充值情况
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: 2013-7-22 下午3:15:39 hexin $ 
 * @package
 */
class IncomeAndExpensesCommand extends PipiConsoleCommand {
	/**
	 * @var CDbConnection 消费库操作
	 */
	protected $consume_db;
	/**
	 * @var CDbConnection 消费库记录操作
	 */
	protected $consume_records_db;
	/**
	 * @var CDbConnection 用户库操作
	 */
	protected $user_db;
	/**
	 * @var CDbConnection 用户充值记录库操作
	 */
	protected $user_records_db;
	/**
	 * @var CDbConnection 统计库操作
	 */
	protected $stat_db;
	/**
	 * @var array 邮件列表
	 */
	protected $mail_list = array();
	protected $pageSize = 5000;
	protected $day;
	protected $week;
	protected $month;
	
	public function beforeAction($action,$params){
		parent::beforeAction($action, $params);
		$this->consume_db = Yii::app()->db_consume_slave;
		$this->consume_records_db =  Yii::app()->db_consume_records_slave;
		$this->user_db = Yii::app()->db_user_slave;
		$this->user_records_db = Yii::app()->db_user_records_slave;
		$this->stat_db = Yii::app()->db_common;
		$this->mail_list = array(
 			"liyong@pipi.cn",
			"hexin@pipi.cn",
		);
		$this->day = date('Y-m-d', strtotime('-1 day'));
		$this->week = date('Y-m-d', strtotime('-'.date('N', strtotime($this->day)).' days'));
		$this->month = date('Y-m-d', strtotime('-'.date('j', strtotime($this->day)).' days'));
		return true;
	}
	
	public function actionTestMailer(){
		$this->sendMail(array("hexin@pipi.cn"), "test", "测试邮件");
	}
	
	/**
	 * 统计每月的皮蛋总充值和总消费情况，并统计用户所有的总充值和总消费的情况
	 */
	public function actionSendEveryday(){
		$files = array();
		$files[] = $this->statByMonth();
		$files[] = $this->statByUser();
		$files[] = $this->statPipieggChangeToday();
		$body = '附件是'.$this->day."日收入支出情况";
		$this->sendMail($this->mail_list, "皮蛋收入支出报告-".$this->day, $body, $files);
	}
	
	/**
	 * 统计每月的皮蛋总充值和总消费
	 * @return string
	 */
	private function statByMonth(){
		$filePath=DATA_PATH."runtimes".DIR_SEP."stat".DIR_SEP;
		$fileName=$filePath."IncomeAndExpenses_EveryMonth.csv";
		$this->createdir($filePath);
		
		if(is_file($fileName)){
			$csv = $this->readCsv($fileName);
			array_shift($csv);
			if(isset($csv[$this->month])) unset($csv[$this->month]);
			$sql = "SELECT sum(if(pipiegg>0, pipiegg, 0)) as recharge, sum(if(pipiegg<0, pipiegg, 0)) as consume, sum(pipiegg) as balance FROM web_user_pipiegg_records WHERE uid not in(11075454,11076095,10966054) and consume_time >= ".strtotime($this->month)." and consume_time < ".strtotime('+1 months', strtotime($this->month));
// 			echo $sql."\n";
			$userCommand=$this->consume_records_db->createCommand();
			$userCommand->setText($sql);
			$row = $userCommand->queryRow();
			$csv[$this->month] = array($this->month, $row['recharge'], $row['consume'], $row['balance']);
			$file = fopen($fileName,"w");
			$title = array('月份', '总充值', '总消费', '差额');
			fputcsv($file,$this->dataUtf8toGbk($title));
			foreach($csv as $v){
				fputcsv($file, $v);
			}
		}else{
			$months = date('n', strtotime($this->month)) + 12;
			$file = fopen($fileName,"w");
			$title = array('月份', '总充值', '总消费', '差额');
			fputcsv($file,$this->dataUtf8toGbk($title));
			for($i = $months-1; $i >= 0; $i--){
				$sql = "SELECT sum(if(pipiegg>0, pipiegg, 0)) as recharge, sum(if(pipiegg<0, pipiegg, 0)) as consume, sum(pipiegg) as balance FROM web_user_pipiegg_records WHERE uid not in(11075454,11076095,10966054) and consume_time >= ".strtotime((-1)*$i.' months', strtotime($this->month))." and consume_time < ".strtotime((-1)*($i-1).' months', strtotime($this->month));
// 				echo $sql."\n";
				$userCommand=$this->consume_records_db->createCommand();
				$userCommand->setText($sql);
				$row = $userCommand->queryRow();
				$month = date('Y-m-d', strtotime('-'.$i.' months', strtotime($this->month)));
				fputcsv($file, array($month, $row['recharge'], $row['consume'], $row['balance']));
			}
		}
		fclose($file);
		return $fileName;
	}
	
	/**
	 * 统计每天富豪1以上用户的消费情况
	 * @return string
	 */
	private function statByUser(){
		$filePath=DATA_PATH."runtimes".DIR_SEP."stat".DIR_SEP;
		$fileName=$filePath."IncomeAndExpenses_Users.csv";
		$this->createdir($filePath);
		$users = $this->getUsers();
		
		//检查当日富豪1以上用户的消费余额记录是否已写，如果未写就写一次记录 ，如果已写就读出记录
		$sql = "SELECT * FROM web_stat_user_balance WHERE `date` like '".date("Y-m-d")."%'";
		$userCommand=$this->stat_db->createCommand();
		$userCommand->setText($sql);
		$list = $userCommand->queryAll();
		if(count($list) < 1){
			$sql = "SELECT uid, sum(if(pipiegg>0, pipiegg, 0)) as recharge, sum(if(pipiegg<0, pipiegg, 0)) as consume, sum(pipiegg) as balance FROM web_user_pipiegg_records WHERE uid in(".implode(',', array_keys($users)).") group by uid";
			// 		echo $sql."\n";
			$userCommand=$this->consume_records_db->createCommand();
			$userCommand->setText($sql);
			$list = $userCommand->queryAll();
			
			//写入统计记录
			$sql = 'INSERT INTO web_stat_user_balance(uid, pipiegg, recharge, consume, balance, `date`) VALUES';
			foreach($list as $u){
				$sql .= '('.$u['uid'].', '.$users[$u['uid']]['pipiegg'].', '.$u['recharge'].', '.$u['consume'].', '.$u['balance'].", '".date("Y-m-d H:i:s")."'),";
			}
			$userCommand=$this->stat_db->createCommand();
			$userCommand->setText(rtrim($sql,','));
			$userCommand->execute();
		}
		
		//查询前一天的消费余额记录
		$sql = "SELECT * FROM web_stat_user_balance WHERE `date` like '".$this->day."%'";
		$userCommand=$this->stat_db->createCommand();
		$userCommand->setText($sql);
		$tmp = $userCommand->queryAll();
		if(empty($tmp)) return null;
		$records = array();
		foreach($tmp as $r){
			$records[$r['uid']] = $r;
		}
		
		//查询用户当日的所有点歌量及处理量
		$start_time = strtotime('-1 day');
		$end_time = time();
		$sql = "SELECT uid,is_handle,pipiegg,create_time,update_time FROM web_user_song WHERE uid in(".implode(',', array_keys($users)).") and create_time between ".$start_time." and ".$end_time." or update_time between ".$start_time." and ".$end_time;
		$userCommand=$this->consume_db->createCommand();
		$userCommand->setText($sql);
		$tmp = $userCommand->queryAll();
		$songs = $today_songs = array();
		foreach($tmp as $t){
			if(!isset($songs[$t['uid']])){
				$songs[$t['uid']] = array(0=>0, 1=>0, 2=>0);
				$today_songs[$t['uid']] = 0;
				$today_cancel[$t['uid']] = 0;
			}
			$songs[$t['uid']][$t['is_handle']] += $t['pipiegg'];
			if($t['is_handle'] == 1 && $t['create_time'] >= $start_time && $t['create_time'] < $end_time){
				$today_songs[$t['uid']] += $t['pipiegg'];
			}
			if($t['is_handle'] == 2 && $t['create_time'] >= $start_time && $t['create_time'] < $end_time){
				$today_cancel[$t['uid']] += $t['pipiegg'];
			}
		}
		
		//拼装数据
		foreach($list as $k => &$u){
			$u['username'] = $users[$u['uid']]['username'];
			$u['reg'] = date('Y-m-d H:i:s', intval($users[$u['uid']]['create_time']));
			$u['rank'] = $users[$u['uid']]['rank'];
			if(!isset($u['pipiegg'])){
				$u['pipiegg'] = isset($users[$u['uid']]) ? $users[$u['uid']]['pipiegg'] : 0;
			}
			if(!isset($records[$u['uid']])){
				$records[$u['uid']]['pipiegg'] = $records[$u['uid']]['recharge'] = $records[$u['uid']]['consume'] = $records[$u['uid']]['balance'] = 0;
			}
			$u['prev_pipiegg'] = $records[$u['uid']]['pipiegg'];
			$u['prev_recharge'] = $records[$u['uid']]['recharge'];
			$u['prev_consume'] = $records[$u['uid']]['consume'];
			$u['prev_balance'] = $records[$u['uid']]['balance'];
			$u['diff_pipiegg'] = bcsub($u['pipiegg'], $records[$u['uid']]['pipiegg'], 2);
			$u['diff_recharge'] = bcsub($u['recharge'], $records[$u['uid']]['recharge'], 2);
			$u['diff_consume'] = bcsub($u['consume'], $records[$u['uid']]['consume'], 2);
			
			if(!isset($songs[$u['uid']])){
				$u['songs'] = array(0=>0, 1=>0, 2=>0);
				$u['today_songs'] = 0;
				$u['today_cancel'] = 0;
			}else{
				$u['songs'] = $songs[$u['uid']];
				$u['today_songs'] = $today_songs[$u['uid']];
				$u['today_cancel'] = $today_cancel[$u['uid']];
			}
			$diff_song = ($u['songs'][2] - $u['today_cancel']) - $u['songs'][0] + $u['songs'][1] - $u['today_songs'];
			
			$u['diff_balance'] = bcadd(bcsub(bcsub($u['balance'], $records[$u['uid']]['balance'], 2), $u['diff_pipiegg'], 2), $diff_song, 2);
			if($u['diff_pipiegg'] == 0 && $u['diff_recharge'] == 0 && $u['diff_consume'] == 0 && $u['diff_balance'] == 0 && $u['songs'][0] == 0 && $u['songs'][1] == 0 && $u['songs'][2] == 0)
				unset($list[$k]);
		}
		usort($list, array($this, 'sortByConsume'));
		
		//写入附件
		$file = fopen($fileName,"w");
		$title = array(
			'UID', '用户名', '注册时间', '等级', 
			'前一天余额', '前一天总充值', '前一天总消费', 
			'当天余额', '当天总充值', '当天总消费', 
			'余额差', '总充值差', '总消费差', 
			'未处理点歌', '已处理点歌', '当天点歌当天处理', '已取消点歌', '当天点歌当天取消', '收支总和差');
		fputcsv($file,$this->dataUtf8toGbk($title));
		foreach($list as $u){
			fputcsv($file, $this->dataUtf8toGbk(array(
				$u['uid'], $u['username'], $u['reg'], $u['rank'], 
				$u['prev_pipiegg'], $u['prev_recharge'], $u['prev_consume'], 
				$u['pipiegg'], $u['recharge'], $u['consume'], 
				$u['diff_pipiegg'], $u['diff_recharge'], $u['diff_consume'], 
				$u['songs'][0]*(-1), $u['songs'][1]*(-1), $u['today_songs']*(-1), $u['songs'][2], $u['today_cancel'], $u['diff_balance']
			)));
		}
		fclose($file);
		return $fileName;
	}
	
	private function statPipieggChangeToday(){
		$filePath=DATA_PATH."runtimes".DIR_SEP."stat".DIR_SEP;
		$fileName=$filePath."PipieggChangeToday.csv";
		$day = strtotime($this->day);
		$file = fopen($fileName,"w");
		$add = $sub = 0;
		
		fputcsv($file, $this->dataUtf8toGbk(array('昨日增加皮蛋', '', '')));
		$title = array('source', 'sub_source', 'pipiegg');
		fputcsv($file,$title);
		$sql = "SELECT source,sub_source,sum(pipiegg) as sum FROM `web_user_pipiegg_records` WHERE pipiegg > 0 and consume_time >= ".$day." and consume_time < ".strtotime("+1 day", $day)." group by source, sub_source order by sum desc";
		$add_list = $this->consume_records_db->createCommand()->setText($sql)->queryAll();
		if(!empty($add_list)){
			foreach($add_list as $d){
				$add += $d['sum'];
				fputcsv($file, array($d['source'], $d['sub_source'], $d['sum']));
			}
		}
		fputcsv($file, $this->dataUtf8toGbk(array('共计', '', $add)));
		fputcsv($file, array('', '', ''));
		
		fputcsv($file, $this->dataUtf8toGbk(array('昨日消费皮蛋', '', '')));
		$title = array('source', 'sub_source', 'pipiegg');
		fputcsv($file,$title);
		$sql = "SELECT source,sub_source,sum(pipiegg) as sum FROM `web_user_pipiegg_records` WHERE pipiegg < 0 and consume_time >= ".$day." and consume_time < ".strtotime("+1 day", $day)." group by source, sub_source order by sum desc";
		$sub_list = $this->consume_records_db->createCommand()->setText($sql)->queryAll();
		if(!empty($sub_list)){
			foreach($sub_list as $d){
				$sub += $d['sum'];
				fputcsv($file, array($d['source'], $d['sub_source'], $d['sum']));
			}
		}
		fputcsv($file, $this->dataUtf8toGbk(array('共计', '', $sub)));
		fputcsv($file, array('', '', ''));
		
		$sql = "SELECT sum(if(currencycode = 'USD', money * 6, money)) as money, sum(pipiegg) as pipiegg FROM `web_user_recharge_records` WHERE issuccess = 2 and ctime >= ".$day." and ctime < ".strtotime("+1 day", $day);
		$recharge = $this->user_records_db->createCommand()->setText($sql)->queryRow();
		fputcsv($file, $this->dataUtf8toGbk(array('昨日充值', $recharge['money'].'元', $recharge['pipiegg'].'皮蛋')));
		
		fclose($file);
		return $fileName;
	}
	
	/**
	 * 获取所有用户信息
	 * @return array
	 */
	protected function getUsers(){
		//只取富豪1以上的用户，目前富豪1以上用户只有4千多个
		$sql = "SELECT uid,pipiegg,rank FROM web_user_consume_attribute WHERE rank >= 7";
		$userCommand=$this->consume_db->createCommand();
		$userCommand->setText($sql);
		$list = $userCommand->queryAll();
		
		$userService = new UserService();
		$list = $userService->buildDataByIndex($list, 'uid');
		$users = $userService->getUserBasicByUids(array_keys($list));
		$consumeService = new ConsumeService();
		$ranks = $consumeService->getAllUserRanks('rank');
		
		$wrong = array(11075454,11076095,10966054); //过滤掉错误用户
		foreach($users as $uid => &$u){
			if(in_array($uid, $wrong)){
				unset($users[$uid]);
				continue;
			}
			$u['pipiegg'] = $list[$u['uid']]['pipiegg'];
			$u['rank'] = $ranks[$list[$u['uid']]['rank']]['name'];
		}
		return $users;
	}
	
	/**
	 * 结果集排序
	 * @param array $a
	 * @param array $b
	 * @return number
	 */
	protected function sortByConsume($a, $b){
		if($a['diff_balance'] == $b['diff_balance']) return 0;
		else return $a['diff_balance'] > $b['diff_balance'] ? 1 : -1;
	}
	
	private function readCsv($csvFileName)
	{
		if(!file_exists($csvFileName))
			return array();
		$file = fopen($csvFileName,"r");
		$csvDataArr=array();
		while(! feof($file))
		{
			$dataRow=fgetcsv($file);
			if(isset($dataRow[0]))
			{
				$csvDataArr[$dataRow[0]]=$dataRow;
			}
		}
		fclose($file);
		return $csvDataArr;
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
	
	private function dataUtf8toGbk($dataRow){
		$newRow=array();
		foreach ($dataRow as $value)
		{
			$newRow[]=mb_convert_encoding($value, "GB2312", "auto");
		}
		return $newRow;
	}
	
}