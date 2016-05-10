<?php
/**
 * 用户统计
 * the last known user to change this file in the repository  <$LastChangedBy: zfzhang $>
 * @author Zhang Zhi fan <zhangzhifan@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z zfzhang $
 * @package
 */
class UserStatCommand extends PipiConsoleCommand {

	const PAGE_SIZE=3000;
	/**
	 * @var CDbConnection 新版用户数据库
	 */
	protected $user_db;
	
	/**
	 * @var CDbConnection 新版用户记录库
	 */
	protected $user_records_db;
	
	/**
	 * @var CDbConnection 新版消费数据库
	 */
	protected $consume_db;
	
	/**
	 * @var CDbConnection 新版消费记录数据库
	 */
	protected $consume_records_db;
	
	/**
	 * @var array 邮件列表
	 */
	protected $emailList;
	
	
	public function beforeAction($action,$params){
		parent::beforeAction($action, $params);
		$this->user_records_db =  Yii::app()->db_user_records;
		$this->user_db = Yii::app()->db_user;
		$this->consume_db = Yii::app()->db_consume;
		$this->consume_records_db = Yii::app()->db_consume_records;
		$this->emailList=array(
		//		"ylh@caitong.net",
				"jinziwen@pipi.cn",
				"liyong@pipi.cn",
				"zhangzhifan@pipi.cn",
				"hexin@pipi.cn",
				"gaolifang@pipi.cn",
				"shuxufei@pipi.cn",
				"luojie@pipi.cn",
				"zhanghan@pipi.cn"
		);
		return true;
	}
	
	/*
	* 一个能创建多级目录的PHP函数
	* @param string $path 要创建的目录
	*/
	private  function createdir($path,$mode=0755){
		if (is_dir($path)){  //判断目录存在否，存在不创建
			return false;
		}else{ //不存在创建
			$re=mkdir($path,$mode,true); //第三个参数为true即可以创建多极目录
			if ($re){
				return true;
			}else{
				return false;
			}
		}
	}
	
	/*
	 * 读取的csv报表返回数组
	* @param string $csvFileName 要读取的csv文档
	*/
	private function getCsvReport($csvFileName)
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
	
	//数组的元素中的utf8转为gb2312
	private function dataUtf8toGbk($dataRow)
	{
		$newRow=array();
		foreach ($dataRow as $value)
		{
			$newRow[]=mb_convert_encoding($value, "GB2312", "auto");
		}
		return $newRow;
	}
	
	/*
	 * 根据时间戳取得uid最大值最小值或者数量
	* @param int $stime 起始时间戳
	* @param int $etime 终止时间戳
	* @param bool $stime 是否返回数量
	*/
	private function 	getUidRangByTime($stime=null,$etime,$isCount=false)
	{
		if(empty($stime))
		{
			if($isCount)
			{
				$sqlstr="select min(uid) as min_uid,max(uid) as max_uid, count(*) as uid_num from web_user_base 
				where create_time<={$etime}";
			}
			else
			{
				$sqlstr="select min(uid) as min_uid,max(uid) as max_uid from web_user_base where create_time<={$etime}";
			}
		}
		else
		{
			if($isCount)
			{
				$sqlstr="select min(uid) as min_uid,max(uid) as max_uid, count(*) as uid_num from web_user_base
				where create_time>={$stime} and create_time<={$etime}";
			}
			else
			{
				$sqlstr="select min(uid) as min_uid,max(uid) as max_uid from web_user_base where 
				create_time>={$stime} and create_time<={$etime}";
			}
		}
		$userCommand=$this->user_db->createCommand();
		$userCommand->setText($sqlstr);
		return $userCommand->queryRow();
	}
	
	//获取用户级别
	private function getUserRank()
	{
		$consumeCommand = $this->consume_db->createCommand ();
		$consumeCommand->setText ( " select max(rank) as maxRank from web_user_consume_attribute" );
		$maxRank = $consumeCommand->queryScalar ();
		$consumeCommand->setText ( "select rank,name from web_user_rank where rank<={$maxRank} order by rank asc" );
		$userRank = $consumeCommand->queryAll ();
		return $userRank;
	}
	
	//输出出提示
	private function displayMsg($result,$msg)
	{
		if($result)
		{
			echo date("Y-m-d H:i:s")."    ".$msg."发送成功 \n";
		}
		else
		{
			echo date("Y-m-d H:i:s")."    ".$msg."发送失败 \n";
		}
	}
	
	//用户付费转化概况
	public function actionPaymentInfo()
	{
		$yesterday=date("Y-m-d",strtotime("-1 days", time()));
		
		$filePath=DATA_PATH."runtimes".DIR_SEP."stat".DIR_SEP;
		$fileName=$filePath."PaymentInfo.csv";
		//读取旧报表
		$oldCsvData=$this->getCsvReport($fileName);
		if(isset($oldCsvData[$yesterday]))
		{
			$result=$this->sendMail($this->emailList, "用户付费转化概况{$yesterday}", '用户付费转化概况',$fileName);
			$this->displayMsg($result,"用户付费转化概况{$yesterday}");		
			return ;
		}

		$dataRow=$this->paymentInfo($yesterday);
		$this->createdir($filePath);
		if(file_exists($fileName))
		{
			$file = fopen($fileName,"a");
			fputcsv($file,$this->dataUtf8toGbk($dataRow));
		}
		else
		{
			$titleRow=array("日期","总注册帐号数","回访登录数","新注册数","今日总活跃数","今日总充值数",
				"今日新增初次充值帐号数","今日重复充值帐号数","付费帐号总数","近30天未充值付费帐号数"
			);
			
			$file = fopen($fileName,"w");
			fputcsv($file,$this->dataUtf8toGbk($titleRow));
			fputcsv($file,$this->dataUtf8toGbk($dataRow));
		}
		fclose($file);
		
		$this->sendMail(array("ylh@caitong.net", "zhangkun@pipi.cn"), "用户付费转化概况{$yesterday}", '用户付费转化概况',$fileName);
		$result=$this->sendMail($this->emailList, "用户付费转化概况{$yesterday}", '用户付费转化概况',$fileName);
		$this->displayMsg($result,"用户付费转化概况{$yesterday}");	
	}
	
	/**
	 * 用户付费转化概况
	 * @param string $yesterday 统计日期
	 * @return array
	 */
	private function paymentInfo($yesterday)
	{
		$stime=strtotime($yesterday." 00:00:00");
		$etime=strtotime($yesterday." 23:59:59");
		
		$dataRow=array($yesterday);
		//总注册帐号数
		$totalUidRow=$this->getUidRangByTime(0,$etime);
		$consumeCommand = $this->consume_db->createCommand ();
		$consumeCommand->setText ( " select count(*) as user_num from web_user_consume_attribute where 
				uid>={$totalUidRow['min_uid']} and uid<={$totalUidRow['max_uid']}" );
		$dataRow[]=$consumeCommand->queryScalar();
		
		//新注册用户列表
		$todayUidRow=$this->getUidRangByTime($stime,$etime);
		$consumeCommand->setText ( " select uid from web_user_consume_attribute where
				uid>={$todayUidRow['min_uid']} and uid<={$todayUidRow['max_uid']}" );
		$today_reg_uids=$consumeCommand->queryAll();
		//新注册数
		$today_reg_num=count($today_reg_uids);
		$uid_list=array();
		foreach ($today_reg_uids as $row_uid)
		{
			$uid_list[]=$row_uid['uid'];
		}
		
		$userRecordsCommand=$this->user_records_db->createCommand();
		//今日总活跃数
		$userRecordsCommand->setText("select count(distinct uid) as total_login_num from web_user_login_records
				where login_time>={$stime} and login_time<={$etime}");
		$total_login_num=$userRecordsCommand->queryScalar();
		//今日登录用户列表
		$userRecordsCommand->setText("select distinct uid from web_user_login_records
		where login_time>={$stime} and login_time<={$etime}");
		$total_login_uids=$userRecordsCommand->queryAll();
		
		//回访登录数
		$payReturnVisitNum=0;
		foreach ($total_login_uids as $row_login_uid)
		{
			if(!in_array($row_login_uid['uid'],$uid_list))
				$payReturnVisitNum++;
		}
		$dataRow[]=$payReturnVisitNum;
		$dataRow[]=$today_reg_num;
		$dataRow[]=$total_login_num;
		
		//今日总充值数
		$userRecordsCommand->setText("select distinct uid from web_user_recharge_records where
		rtime>={$stime} and rtime<={$etime} and issuccess=2");
		$recharge_uids=$userRecordsCommand->queryAll();
		$today_recharge_num=count($recharge_uids);
		$dataRow[]=$today_recharge_num;
		
		//今日新增初次充值帐号数
		$userRecordsCommand->setText("select uid,min(rtime) as min_rtime from web_user_recharge_records
		where rtime<={$etime} and issuccess=2 group by uid having min_rtime>={$stime} and min_rtime<={$etime}");
		$today_first_recharge_num=count($userRecordsCommand->queryAll());
		$dataRow[]=$today_first_recharge_num;
		
		//今日重复充值帐号数
		$userRecordsCommand->setText("select uid,count(*) as uid_counts from web_user_recharge_records
		where rtime>={$stime} and rtime<={$etime} and issuccess=2 group by uid having uid_counts>1");
		$today_many_recharge_num=count($userRecordsCommand->queryAll());
		$dataRow[]=$today_many_recharge_num;
		
		//付费帐号总数
		$userRecordsCommand->setText("select count(distinct uid) as total_recharge_num from web_user_recharge_records
		where rtime<={$etime} and issuccess=2");
		$total_recharge_num=$userRecordsCommand->queryScalar();
		$dataRow[]=$total_recharge_num;
		
		//近30天未充值付费帐号数
		$lastMonthTime=$etime-(86400*30);
		$userRecordsCommand->setText("select uid,max(rtime) as max_rtime from web_user_recharge_records
		where rtime<={$etime} and issuccess=2 group by uid having max_rtime<={$lastMonthTime}");
		$lastMonthRechargeNum=count($userRecordsCommand->queryAll());
		$dataRow[]=$lastMonthRechargeNum;
		return $dataRow;
	}
	
	
	//用户功能使用情况
	public function actionFunctionInfo()
	{
		$yesterday=date("Y-m-d",strtotime("-1 days", time()));

		$dataList=$this->functionInfo($yesterday);
		
		//发邮件
		$filePath=DATA_PATH."runtimes".DIR_SEP."stat".DIR_SEP;
		$fileName=$filePath."FunctionInfo_{$yesterday}.csv";
		$this->createdir($filePath);
	
		$file = fopen($fileName,"w");
		$titleRow=array("等级","帐号数","15天登录数","送礼使用人数","送礼15天使用次数","送礼15天使用率",
				"签到使用人数","签到15天使用次数","签到15天使用率","关注使用人数","关注15天使用次数","关注15天使用率"
		);
		fputcsv($file,$this->dataUtf8toGbk($titleRow));
		foreach ($dataList as $dataRow)
		{
			if(isset($dataRow[2]) && $dataRow[2]>0)
			{
				$dataRow[5]=round(($dataRow[4]/$dataRow[2])*100,2)."%";
				$dataRow[8]=round(($dataRow[7]/$dataRow[2])*100,2)."%";
				$dataRow[11]=round(($dataRow[10]/$dataRow[2])*100,2)."%";
			}
			fputcsv($file,$this->dataUtf8toGbk($dataRow));
		}

		fclose($file);
		
		$this->sendMail(array("ylh@caitong.net", "zhangkun@pipi.cn"), "用户功能使用情况{$yesterday}", '用户功能使用情况',$fileName);
		$result=$this->sendMail($this->emailList, "用户功能使用情况{$yesterday}", '用户功能使用情况',$fileName);
		$this->displayMsg($result,"用户功能使用情况{$yesterday}");	

	}
	
	/**
	 * 用户功能使用情况
	 * @param string $yesterday 统计日期
	 * @return array
	 */
	private function functionInfo($yesterday)
	{
		$stime = strtotime ( $yesterday . " 00:00:00" );
		$etime = strtotime ( $yesterday . " 23:59:59" );
		$fifteen_days_stime = $etime - 86400 * 15;
		
		$consumeRecordsCommand = $this->consume_records_db->createCommand ();
		$userRecordsCommand = $this->user_records_db->createCommand ();
		
		$consumeCommand = $this->consume_db->createCommand ();
		$userRank = $this->getUserRank();
		
		// 初始化输出数组
		$dataList = array ();
		foreach ( $userRank as $rankRow ) {
			$dataList [$rankRow ['rank']] = array (
					$rankRow ['name'],
					0,
					0,
					0,
					0,
					0,
					0,
					0,
					0,
					0,
					0,
					0 
			);
		}
		
		// 总注册帐号列表
		$uidRow=$this->getUidRangByTime(0,$etime);
		
		$userCommand=$this->user_db->createCommand();
		
		// 分页查uid,分页统计各项数据
		$start_uid=$uidRow['min_uid']-1;
		while($start_uid<$uidRow['max_uid'] ) {

			// 按统计时间取uid
			$pageEndUid=$start_uid+self::PAGE_SIZE;
			if($pageEndUid>$uidRow['max_uid'])
			{
				$pageEndUid=$uidRow['max_uid'];
			}

			// 分页取uid对应的rank
			$consumeCommand->setText ( "select uid,rank from web_user_consume_attribute where uid>{$start_uid} and uid<={$pageEndUid}" );
			$users = $consumeCommand->queryAll ();
			
			$uidCounts=count($users);
			if($uidCounts<1)
			{
				$start_uid=$pageEndUid;
				continue;
			}
			
			$user_list = array ();
			foreach ( $users as $user_row ) {
				$user_list [$user_row ['uid']] = $user_row;
			}
			
			// 统计本页数据各等级的数量
			$consumeCommand->setText ( "select rank,count(*) as rankcounts from web_user_consume_attribute where 
					uid>{$start_uid} and uid<={$pageEndUid} group by rank " );
			$userRanks = $consumeCommand->queryAll ();
			// print_r($userRanks);
			
			foreach ( $userRanks as $userRankRow ) {
				foreach ( $userRank as $rankRow ) {
					if ($userRankRow ['rank'] == $rankRow ['rank'])
						$dataList [$rankRow ['rank']] [1] = $dataList [$rankRow ['rank']] [1] + $userRankRow ['rankcounts'];
				}
			}
			
			// 15天登录数
			$userRecordsCommand->setText ( "select distinct uid from web_user_login_records where 
					uid>{$start_uid} and uid<={$pageEndUid} and login_time>={$fifteen_days_stime} and login_time<={$etime}" );
			$userFifteenDayslogins = $userRecordsCommand->queryAll ();
			foreach ( $userFifteenDayslogins as $userFifteenDaysloginRow ) {
				foreach ( $userRank as $rankRow ) {
					if (isset ( $user_list [$userFifteenDaysloginRow ['uid']] ) && $user_list [$userFifteenDaysloginRow ['uid']] ['rank'] == $rankRow ['rank'])
						$dataList [$rankRow ['rank']] [2] = $dataList [$rankRow ['rank']] [2] + 1;
				}
			}
			
			// 送礼使用人数
			$consumeRecordsCommand->setText ( "select distinct uid from web_user_giftsend_records where 
					uid>{$start_uid} and uid<={$pageEndUid} and create_time<={$etime}" );
			$userGiftSends = $consumeRecordsCommand->queryAll ();
			foreach ( $userGiftSends as $userGiftSendRow ) {
				foreach ( $userRank as $rankRow ) {
					if (isset ( $user_list [$userGiftSendRow ['uid']] ) && $user_list [$userGiftSendRow ['uid']] ['rank'] == $rankRow ['rank'])
						$dataList [$rankRow ['rank']] [3] = $dataList [$rankRow ['rank']] [3] + 1;
				}
			}
			
			// 送礼15天使用次数
			$consumeRecordsCommand->setText ( "select record_id,uid from web_user_giftsend_records where
					uid>{$start_uid} and uid<={$pageEndUid} and create_time>={$fifteen_days_stime} and create_time<={$etime}" );
			$fifteenDaysGiftSendRecords = $consumeRecordsCommand->queryAll ();
			foreach ( $fifteenDaysGiftSendRecords as $fifteenDaysGiftSendRow ) {
				foreach ( $userRank as $rankRow ) {
					if (isset ( $user_list [$fifteenDaysGiftSendRow ['uid']] ) && $user_list [$fifteenDaysGiftSendRow ['uid']] ['rank'] == $rankRow ['rank'])
						$dataList [$rankRow ['rank']] [4] = $dataList [$rankRow ['rank']] [4] + 1;
				}
			}
			
			// 签到使用人数
			$consumeCommand->setText ( "select distinct uid from web_user_checkin where uid>{$start_uid} and uid<={$pageEndUid} 
				 and create_time<={$etime} " );
			$userCheckinList = $consumeCommand->queryAll ();
			foreach ( $userCheckinList as $userCheckinRow ) {
				foreach ( $userRank as $rankRow ) {
					if (isset ( $user_list [$userCheckinRow ['uid']] ) && $user_list [$userCheckinRow ['uid']] ['rank'] == $rankRow ['rank'])
						$dataList [$rankRow ['rank']] [6] = $dataList [$rankRow ['rank']] [6] + 1;
				}
			}
			
			// 签到15天使用次数
			$consumeCommand->setText ( "select checkin_id,uid from web_user_checkin where uid>{$start_uid} and 
				uid<={$pageEndUid} and create_time>={$fifteen_days_stime} and create_time<={$etime}" );
			$userFifteenDaysCheckinList = $consumeCommand->queryAll ();
			foreach ( $userFifteenDaysCheckinList as $userFifteenDaysCheckinRow ) {
				foreach ( $userRank as $rankRow ) {
					if (isset ( $user_list [$userFifteenDaysCheckinRow ['uid']] ) && $user_list [$userFifteenDaysCheckinRow ['uid']] ['rank'] == $rankRow ['rank'])
						$dataList [$rankRow ['rank']] [7] = $dataList [$rankRow ['rank']] [7] + 1;
				}
			}
			
			// 关注使用人数
			$userCommand->setText ( "select distinct fans_uid from web_dotey_fans where fans_uid>={$start_uid}
			 and fans_uid<={$pageEndUid}" );
			$doteyFansList = $userCommand->queryAll ();
			foreach ( $doteyFansList as $doteyFansRow ) {
				foreach ( $userRank as $rankRow ) {
					if (isset ( $user_list [$doteyFansRow ['fans_uid']] ) && $user_list [$doteyFansRow ['fans_uid']] ['rank'] == $rankRow ['rank'])
						$dataList [$rankRow ['rank']] [9] = $dataList [$rankRow ['rank']] [9] + 1;
				}
			}
			
			// 关注15天使用次数
			$userCommand->setText ( "select uid,fans_uid from web_dotey_fans where fans_uid>={$start_uid} and
			 fans_uid<={$pageEndUid} and create_time>={$fifteen_days_stime} and create_time<={$etime}" );
			$doteyFansFifteenDaysList = $userCommand->queryAll ();
			foreach ( $doteyFansFifteenDaysList as $doteyFansFifteenDaysRow ) {
				foreach ( $userRank as $rankRow ) {
					if (isset ( $user_list [$doteyFansFifteenDaysRow ['fans_uid']] ) && $user_list [$doteyFansFifteenDaysRow ['fans_uid']] ['rank'] == $rankRow ['rank'])
						$dataList [$rankRow ['rank']] [10] = $dataList [$rankRow ['rank']] [10] + 1;
				}
			}
			$start_uid=$pageEndUid;
		}
		return 	$dataList;	
	}

	//活跃度概况
	public function actionActiveInfo()
	{
		$yesterday = date ( "Y-m-d", strtotime ( "-1 days", time () ) );
		
		$filePath = DATA_PATH . "runtimes" . DIR_SEP . "stat" . DIR_SEP;
		$fileName = $filePath . "ActiveInfo.csv";
		//读取旧报表
		$oldCsvData=$this->getCsvReport($fileName);
		if(isset($oldCsvData[$yesterday]))
		{
			$result=$this->sendMail ( $this->emailList, "活跃度概况{$yesterday}", '活跃度概况', $fileName );
			$this->displayMsg($result,"活跃度概况{$yesterday}");			
			return ;
		}
		$dataRow = $this->activeInfo ( $yesterday );
		$dataRow [7] = round ( ($dataRow [3] / $dataRow [1]) * 100, 2 ) . "%";
		$dataRow [8] = round ( ($dataRow [4] / $dataRow [1]) * 100, 2 ) . "%";
		$dataRow [9] = round ( ($dataRow [5] / $dataRow [1]) * 100, 2 ) . "%";
		$dataRow [10] = round ( ($dataRow [6] / $dataRow [1]) * 100, 2 ) . "%";
		
		// 发邮件
		$this->createdir ( $filePath );
		
		if (file_exists ( $fileName )) {
			$file = fopen ( $fileName, "a" );
			fputcsv ( $file, $this->dataUtf8toGbk($dataRow) );
		} else {
			$titleRow = array (
					"日期",
					"累计注册数",
					"新注册数",
					"近3天回访数",
					"近7天回访数",
					"近15天回访数",
					"近30天未回访数",
					"近3天活跃比例",
					"近7天活跃比例",
					"近15天活跃比例",
					"近30天流失比例" 
			);
			
			$file = fopen ( $fileName, "w" );
			fputcsv ( $file, $this->dataUtf8toGbk($titleRow) );
			fputcsv ( $file, $this->dataUtf8toGbk($dataRow) );
		}
		fclose ( $file );

		$this->sendMail(array("ylh@caitong.net", "zhangkun@pipi.cn"), "活跃度概况{$yesterday}", '活跃度概况', $fileName);
		$result=$this->sendMail ( $this->emailList, "活跃度概况{$yesterday}", '活跃度概况', $fileName );
		$this->displayMsg($result,"活跃度概况{$yesterday}");
	}
	
	/**
	 * 活跃度概况
	 * 
	 * @param string $yesterday
	 *        	统计日期
	 * @return array
	 */
	private function activeInfo($yesterday) {
		$stime = strtotime ( $yesterday . " 00:00:00" );
		$etime = strtotime ( $yesterday . " 23:59:59" );
		
		// 输出数据
		$dataRow = array (
				$yesterday,
				0,
				0,
				0,
				0,
				0,
				0,
				0,
				0,
				0,
				0 
		);
		
		$userCommand = $this->user_db->createCommand ();
		$consumeCommand = $this->consume_db->createCommand ();
		
		//总注册帐号最大uid
		$totalUidRow=$this->getUidRangByTime(0,$etime);
		$totalMaxUid=$totalUidRow['max_uid'];
		//总注册帐号数
		$consumeCommand->setText("select count(*) as total_user_num from web_user_consume_attribute where uid<={$totalMaxUid}");
		$dataRow [1] = $consumeCommand->queryScalar ();
		
		// 今日注册用户最大和最小uid
		$todayUidRow=$this->getUidRangByTime($stime,$etime);
		//今注册用户数
		$consumeCommand->setText("select count(*) as today_user_num from web_user_consume_attribute where 
				uid>={$todayUidRow['min_uid']} and uid<={$todayUidRow['max_uid']}");
		$dataRow [2] = $consumeCommand->queryScalar ();
		// 往日注册总数
		$dataRow [6] = $dataRow [1]  - $dataRow [2];
		
		// 住日注册用户最大和最小uid
		$uidRow = $this->getUidRangByTime(0,$stime);
		
		$userRecordsCommand=$this->user_records_db->createCommand();
		$startUid=$uidRow['min_uid']-1;
		while($startUid<$uidRow['max_uid'])
		{
			//分页查uid
			$pageEndUid=$startUid+self::PAGE_SIZE;
			if($pageEndUid>$uidRow['max_uid'])
			{
				$pageEndUid=$uidRow['max_uid'];
			}
			
			$consumeCommand->setText("select count(*) from web_user_consume_attribute where uid>{$startUid} and uid<={$pageEndUid}");
			$uidCouns=$consumeCommand->queryScalar();
				
			if($uidCouns<1)
			{
				$startUid=$pageEndUid;
				continue;
			}
			
			//近3天登录用户列表
			$threeDaysAgo=$stime-86400*3;
			$userRecordsCommand->setText("select count(distinct uid) from web_user_login_records where uid>{$startUid} and
			 uid<={$pageEndUid} and login_time>={$threeDaysAgo} and login_time<{$stime}");
			//近3天回访数计数
			$dataRow[3]=$dataRow[3]+$userRecordsCommand->queryScalar();
			
			//近7天登录用户列表
			$sevenDaysAgo=$stime-86400*7;
			$userRecordsCommand->setText("select count(distinct uid) from web_user_login_records where uid>{$startUid} and
			 uid<={$pageEndUid} and login_time>={$sevenDaysAgo} and login_time<{$stime}");
			//近7天回访数计数
			$dataRow[4]=$dataRow[4]+$userRecordsCommand->queryScalar();
			
			//近15天登录用户列表
			$fifteenDaysAgo=$stime-86400*15;
			$userRecordsCommand->setText("select count(distinct uid) from web_user_login_records where uid>{$startUid} and
			 uid<={$pageEndUid} and login_time>={$fifteenDaysAgo} and login_time<{$stime}");
			//近15天回访数计数
			$dataRow[5]=$dataRow[5]+$userRecordsCommand->queryScalar();
			
			//30天内登录用户列表
			$thirtyDaysAgo=$etime-86400*30;
			$userRecordsCommand->setText("select count(distinct uid) from web_user_login_records where uid>{$startUid} and
			 uid<={$pageEndUid} and login_time>={$thirtyDaysAgo} and login_time<{$stime}");
			//近30天未回访数计数
			$dataRow[6]=$dataRow[6]-$userRecordsCommand->queryScalar();
			$startUid=$pageEndUid;
		}		
		
		return $dataRow;
	}

	//等级活跃度
	public function actionRankActiveInfo()
	{
		$yesterday=date("Y-m-d",strtotime("-1 days", time()));
		$dataList=$this->rankActiveInfo($yesterday);
		
		//发邮件
		$filePath=DATA_PATH."runtimes".DIR_SEP."stat".DIR_SEP;
		$this->createdir($filePath);
		$fileName=$filePath."RankActiveInfo_{$yesterday}.csv";
		
		$file = fopen($fileName,"w");
		$titleRow=array("等级","帐号数","昨日活跃","近3天活跃","近7天活跃","近15天活跃","近30天未登录");
		fputcsv($file,$this->dataUtf8toGbk($titleRow));
		foreach ($dataList as $dataRow)
		{
			fputcsv($file,$this->dataUtf8toGbk($dataRow));
		}
		
		fclose($file);
		
		$this->sendMail(array("ylh@caitong.net", "zhangkun@pipi.cn"), "等级活跃度{$yesterday}", '等级活跃度',$fileName);
		$result=$this->sendMail($this->emailList, "等级活跃度{$yesterday}", '等级活跃度',$fileName);
		$this->displayMsg($result,"等级活跃度{$yesterday}");		
	}
	
	/**
	 * 等级活跃度
	 * @param string $yesterday 统计日期
	 * @return array
	 */
	private function rankActiveInfo($yesterday)
	{
		$stime = strtotime ( $yesterday . " 00:00:00" );
		$etime = strtotime ( $yesterday . " 23:59:59" );
		
		$userRecordsCommand = $this->user_records_db->createCommand ();
		$consumeCommand = $this->consume_db->createCommand ();
		//获取用户等级
		$userRank = $this->getUserRank();
		
		// 初始化输出数组
		$dataList = array ();
		foreach ( $userRank as $rankRow ) {
			$dataList [$rankRow ['rank']] = array (
					$rankRow ['name'],
					0,
					0,
					0,
					0,
					0,
					0 
			);
		}
		
		$userCommand = $this->user_db->createCommand ();
		// 取统计日期当天的最大和最小uid
		$todayUidRow=$this->getUidRangByTime($stime,$etime);
		
		//总注册帐号列表
		$uidRow = $this->getUidRangByTime(0,$stime);
		
		// 分页查uid,分页统计各项数据
		$startUid = $uidRow ['min_uid'] - 1;
		while ( $startUid < $uidRow ['max_uid'] ) {
			
			// 按统计时间取uid
			$pageEndUid = $startUid + self::PAGE_SIZE;
			if($pageEndUid>$uidRow ['max_uid'])
			{
				$pageEndUid=$uidRow ['max_uid'];
			}
			
			// 取uid对应的rank
			$consumeCommand->setText ( "select uid,rank from web_user_consume_attribute where uid>{$startUid} and uid<={$pageEndUid}" );
			$users = $consumeCommand->queryAll ();
						
			if (count ( $users ) < 1) {
				$startUid = $pageEndUid;
				continue;
			}
			
			$user_list = array ();
			foreach ( $users as $user_row ) {
				$user_list [$user_row ['uid']] = $user_row;
			}
			
			// 统计本页数据各等级的数量
			$consumeCommand->setText ( "select rank,count(*) as rankcounts from web_user_consume_attribute where 
					uid>{$startUid} and uid<={$pageEndUid} group by rank " );
			$userRanks = $consumeCommand->queryAll ();
			
			foreach ( $userRanks as $userRankRow ) {
				foreach ( $userRank as $rankRow ) {
					if ($userRankRow ['rank'] == $rankRow ['rank'])
						$dataList [$rankRow ['rank']] [1] = $dataList [$rankRow ['rank']] [1] + $userRankRow ['rankcounts'];
				}
			}
			
			// 近1天登录用户列表
			$oneDaysAgo = $stime - 86400;
			$userRecordsCommand->setText ( "select distinct uid from web_user_login_records where uid>{$startUid} and uid<={$pageEndUid}
					and login_time>={$oneDaysAgo} and login_time<{$stime}" );
			$oneDaysAgoUidList = $userRecordsCommand->queryAll ();
			// 近1天活跃计数
			foreach ( $oneDaysAgoUidList as $oneDaysAgoUidRow ) {
				foreach ( $userRank as $rankRow ) {
					if (isset ( $user_list [$oneDaysAgoUidRow ['uid']] ) && $user_list [$oneDaysAgoUidRow ['uid']] ['rank'] == $rankRow ['rank'])
						$dataList [$rankRow ['rank']] [2] = $dataList [$rankRow ['rank']] [2] + 1;
				}
			}
			
			// 近3天登录用户列表
			$threeDaysAgo = $stime - 86400 * 3;
			$userRecordsCommand->setText ( "select distinct uid from web_user_login_records where uid>{$startUid} and uid<={$pageEndUid}
					and login_time>={$threeDaysAgo} and login_time<{$stime}" );
			$threeDaysAgoUidList = $userRecordsCommand->queryAll ();
			// 近3天活跃计数
			foreach ( $threeDaysAgoUidList as $threeDaysAgoUidRow ) {
				foreach ( $userRank as $rankRow ) {
					if (isset ( $user_list [$threeDaysAgoUidRow ['uid']] ) && $user_list [$threeDaysAgoUidRow ['uid']] ['rank'] == $rankRow ['rank'])
						$dataList [$rankRow ['rank']] [3] = $dataList [$rankRow ['rank']] [3] + 1;
				}
			}
			
			// 近7天登录用户列表
			$sevenDaysAgo = $stime - 86400 * 7;
			$userRecordsCommand->setText ( "select distinct uid from web_user_login_records where uid>{$startUid} and uid<={$pageEndUid}
					and login_time>={$sevenDaysAgo} and login_time<{$stime}" );
			$sevenDaysAgoUidList = $userRecordsCommand->queryAll ();
			// 近7天活跃计数
			foreach ( $sevenDaysAgoUidList as $sevenDaysAgoUidRow ) {
				foreach ( $userRank as $rankRow ) {
					if (isset ( $user_list [$sevenDaysAgoUidRow ['uid']] ) && $user_list [$sevenDaysAgoUidRow ['uid']] ['rank'] == $rankRow ['rank'])
						$dataList [$rankRow ['rank']] [4] = $dataList [$rankRow ['rank']] [4] + 1;
				}
			}
			
			// 近15天登录用户列表
			$fifteenDaysAgo = $stime - 86400 * 15;
			$userRecordsCommand->setText ( "select distinct uid from web_user_login_records where uid>{$startUid} and uid<={$pageEndUid}
			and login_time>={$fifteenDaysAgo} and login_time<{$stime}" );
			$fifteenDaysAgoUidList = $userRecordsCommand->queryAll ();
			// 近15天活跃计数
			foreach ( $fifteenDaysAgoUidList as $fifteenDaysAgoUidRow ) {
				foreach ( $userRank as $rankRow ) {
					if (isset ( $user_list [$fifteenDaysAgoUidRow ['uid']] ) && $user_list [$fifteenDaysAgoUidRow ['uid']] ['rank'] == $rankRow ['rank'])
						$dataList [$rankRow ['rank']] [5] = $dataList [$rankRow ['rank']] [5] + 1;
				}
			}
			
			// 30天内登录用户列表
			$thirtyDaysAgo = $etime - 86400 * 30;
			$userRecordsCommand->setText ( "select distinct uid from web_user_login_records where uid>{$startUid} and
			 uid<={$pageEndUid} and login_time>={$thirtyDaysAgo} and login_time<{$etime}" );
			$thirtyDaysAgoUidList = $userRecordsCommand->queryAll ();
			// 近30天活跃计数
			foreach ( $thirtyDaysAgoUidList as $thirtyDaysAgoUidRow ) {
				foreach ( $userRank as $rankRow ) {
					if (isset ( $user_list [$thirtyDaysAgoUidRow ['uid']] ) && $user_list [$thirtyDaysAgoUidRow ['uid']] ['rank'] == $rankRow ['rank'])
						$dataList [$rankRow ['rank']] [6] = $dataList [$rankRow ['rank']] [6] + 1;
				}
			}
			$startUid = $pageEndUid;
		}
		
		// 计算往日数据比例
		foreach ( $userRank as $rankRow ) {
			$dataList [$rankRow ['rank']] [2] = round ( ($dataList [$rankRow ['rank']] [2] / $dataList [$rankRow ['rank']] [1]) * 100, 2 ) . "%";
			$dataList [$rankRow ['rank']] [3] = round ( ($dataList [$rankRow ['rank']] [3] / $dataList [$rankRow ['rank']] [1]) * 100, 2 ) . "%";
			$dataList [$rankRow ['rank']] [4] = round ( ($dataList [$rankRow ['rank']] [4] / $dataList [$rankRow ['rank']] [1]) * 100, 2 ) . "%";
			$dataList [$rankRow ['rank']] [5] = round ( ($dataList [$rankRow ['rank']] [5] / $dataList [$rankRow ['rank']] [1]) * 100, 2 ) . "%";
		}
		
		// 取统计日期当天uid对应的rank
		$consumeCommand->setText ( "select uid,rank from web_user_consume_attribute where 
				uid>={$todayUidRow['min_uid']} and uid<={$todayUidRow['max_uid']}" );
		$users = $consumeCommand->queryAll ();
		
		if(count($users)>0)
		{
			$user_list = array ();
			foreach ( $users as $user_row ) {
				$user_list [$user_row ['uid']] = $user_row;
			}
			
			// 统计本页数据各等级的数量
			$consumeCommand->setText ( "select rank,count(*) as rankcounts from web_user_consume_attribute where
					 uid>={$todayUidRow['min_uid']} and uid<={$todayUidRow['max_uid']} group by rank " );
			$userRanks = $consumeCommand->queryAll ();
			
			foreach ( $userRanks as $userRankRow ) {
				foreach ( $userRank as $rankRow ) {
					if ($userRankRow ['rank'] == $rankRow ['rank'])
						$dataList [$rankRow ['rank']] [1] = $dataList [$rankRow ['rank']] [1] + $userRankRow ['rankcounts'];
				}
			}
			
			// 当天注册用户30天内登录用户列表
			$thirtyDaysAgo = $etime - 86400 * 30;
			$userRecordsCommand->setText ( "select distinct uid from web_user_login_records where uid>={$todayUidRow['min_uid']}
			 and uid<={$todayUidRow['max_uid']} and login_time>={$thirtyDaysAgo} and login_time<{$stime}" );
			$thirtyDaysAgoUidList = $userRecordsCommand->queryAll ();
			// 当天登录活跃计数
			foreach ( $thirtyDaysAgoUidList as $thirtyDaysAgoUidRow ) {
				foreach ( $userRank as $rankRow ) {
					if (isset ( $user_list [$thirtyDaysAgoUidRow ['uid']] ) && $user_list [$thirtyDaysAgoUidRow ['uid']] ['rank'] == $rankRow ['rank'])
						$dataList [$rankRow ['rank']] [6] = $dataList [$rankRow ['rank']] [6] + 1;
				}
			}
		}
		
		// 计算近30天未登录
		foreach ( $userRank as $rankRow ) {
			$dataList [$rankRow ['rank']] [6] = round ( (($dataList [$rankRow ['rank']] [1] - $dataList [$rankRow ['rank']] [6]) / $dataList [$rankRow ['rank']] [1]) * 100, 2 ) . "%";
		}
		
		return $dataList;		
		
	}
	
	//签到统计
	public function actionCheckInInfo()
	{
		$yesterday = date ( "Y-m-d", strtotime ( "-1 days", time () ) );
		$filePath = DATA_PATH . "runtimes" . DIR_SEP . "stat" . DIR_SEP;
		$fileName = $filePath . "CheckInInfo.csv";
		//读取旧报表
		$oldCsvData=$this->getCsvReport($fileName);
		if(isset($oldCsvData[$yesterday]))
		{
			$result=$this->sendMail ( $this->emailList, "签到统计{$yesterday}", '签到统计', $fileName );
			$this->displayMsg($result,"签到统计{$yesterday}");		
			return ;
		}
		
		$dataRow = $this->checkInInfo ( $yesterday );
		array_unshift($dataRow,$yesterday);
		
		// 发邮件
		$this->createdir ( $filePath );
		
		if (file_exists ( $fileName )) {
			$file = fopen ( $fileName, "a" );
			fputcsv ( $file, $this->dataUtf8toGbk($dataRow) );
		} else {
			$titleRow = array (
					"日期",
					"普通签到人数",
					"普通签到皮蛋数",
					"月卡签到人数",
					"月卡签到皮蛋数"
			);
				
			$file = fopen ( $fileName, "w" );
			fputcsv ( $file, $this->dataUtf8toGbk($titleRow) );
			fputcsv ( $file, $this->dataUtf8toGbk($dataRow) );
		}
		fclose ( $file );

		$this->sendMail(array("ylh@caitong.net", "zhangkun@pipi.cn"), "签到统计{$yesterday}", '签到统计', $fileName);
		$result=$this->sendMail ( $this->emailList, "签到统计{$yesterday}", '签到统计', $fileName );
		$this->displayMsg($result,"签到统计{$yesterday}");	
	}
	
	/**
	 * 签到统计
	 * @param string $yesterday 统计日期
	 * @return array
	 */
	private function checkInInfo($yesterday)
	{
		$stime = strtotime ( $yesterday . " 00:00:00" );
		$etime = strtotime ( $yesterday . " 23:59:59" );
		$consumeCommand = $this->consume_db->createCommand ();
		$consumeCommand->setText("select count(distinct uid) as uid_counts,sum(pipiegg) as sum_pipiegg from 
				web_user_checkin where create_time>={$stime} and create_time<={$etime} and type=1");
		$dataRow1=$consumeCommand->queryRow();
		$consumeCommand->setText("select count(distinct uid) as uid_counts,sum(pipiegg) as sum_pipiegg from
			web_user_checkin where create_time>={$stime} and create_time<={$etime} and type=2");
		$dataRow2=$consumeCommand->queryRow();
		$resultRow=array($dataRow1['uid_counts'],$dataRow1['sum_pipiegg'],
			$dataRow2['uid_counts'],$dataRow2['sum_pipiegg']);
		return $resultRow;
	}
	
}
?>