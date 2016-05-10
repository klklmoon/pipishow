<?php
class PartnerCronCommand extends PipiConsoleCommand {
	/**
	 * @var CDbConnection $partner_db
	 */
	protected $partner_db;
	/**
	 * @var CDbConnection $user_db;
	 */
	protected $user_db;
	/**
	 * @var CDbConnection 新版消费库记录操作
	 */
	protected $user_records_db;
	protected $pagesize = 10000;
	
	public function beforeAction($action,$params){
		parent::beforeAction($action, $params);
		$this->partner_db = Yii::app()->db_partner;
		$this->user_db = Yii::app()->db_user;
		$this->user_records_db = Yii::app()->db_user_records;
		return true;
	}
	
	/**
	 * 每小时跑一次的脚本
	 * 	1.取得新注册用户集合
	 *  2.取得注册渠道信息标识符大于8个字符，并且过虑
	 *  3.渠道标识是否符合内容推广标准
	 *  4.更新插入渠道信息表
	 *  5.更新插入渠道ID-NAME信息表
	 */
	public function actionGetNewChannelEveryHour(){
		$partnerDB = $this->partner_db->createCommand();
		$userDB = $this->user_db->createCommand();
		$time = time();
		$time_begin = $time - 3600 - 60;
		$time_end = $time;
		
		$partner_info_list = $partnerDB->select('channel_prefix,partner_id')->from('web_partner_info')->queryAll();
		$prefix_map = $this->getIdsFromArray($partner_info_list, 'partner_id', 'channel_prefix');
		$partnerDB->reset();
		
		$user = $userDB->select('uid')->from('web_user_base')->where('create_time >=' . $time_begin . ' and create_time < ' . $time_end)->queryAll();
		$uids = $this->getIdsFromArray($user, 'uid');
		$userDB->reset();
		
		if(!empty($uids)){
			$partnerDB->setText("select distinct(sign),uid from web_reg_log where uid in(" .implode(',', $uids). ") and length(sign)>=8 
			and sign not in (select channel_id from web_partner_channel union select channel_id from web_partner_shield_channel)");
			$sign_list = $partnerDB->queryAll();
			$partnerDB->reset();
			foreach($sign_list as $sign_id){
				if(strlen($sign_id['sign']) == 8 || substr($sign_id['sign'], 7, 1) == '_'){
					$temp_str = substr($sign_id['sign'], 0, 2);
					$param = array();
					$param[':sign'] = $param1[':sign'] = $sign_id['sign'];
					$param[':channel'] = $param1[':channel'] = $sign_id['sign'];
					if(in_array($temp_str, array_keys($prefix_map))){
						$partnerDB -> setText("REPLACE INTO web_partner_channel(channel_id,channel_id_comment,partner_id,create_time) values(:sign, :channel, :partner_id, ".$time.")");
						//echo $partnerDB->getText()."\n";
						$param[':partner_id'] = $prefix_map[$temp_str];
						$partnerDB -> execute($param);
						$partnerDB -> setText("REPLACE INTO web_reg_channel_id_name(channel_id,channel_name,person_in_charge) values(:sign, :channel,'无')");
						//echo $partnerDB->getText()."\n";
						$partnerDB -> execute($param1);
					}
				}
			}
		}
	}
	
	/**
	 * 每天跑一次的脚本
	 * 	注册推广统计信息
	 */
	public function actionRegPromoteStatEveryDay($start = ''){
		$partnerDB = $this->partner_db->createCommand();
		
		$time = $start != '' ? $start : strtotime('-1 day');
		$day_begin = strtotime(date('Y-m-d', $time).' 00:00:00');
		$day_end = strtotime(date('Y-m-d', strtotime("+1 day", $time)).'00:00:00');
		
		$channel_info_list = $partnerDB->select('channel_id,user_id')->from('web_regpromote_user_channel')->queryAll();
		$partnerDB->reset();
		foreach($channel_info_list as $channel_info){
			$stat_record = array();
			$stat_record['stat_date'] = date('Ymd', $time);
			$stat_record['channel_id'] = $channel_info['channel_id'];
			$stat_record['user_id']= $channel_info['user_id'];
			
			#当日注册人数
			$user = $this->user_db->createCommand()->select('uid')->from('web_user_base')->where('create_time >=' . $day_begin . ' and create_time < ' . $day_end)->queryAll();
			$uids = $this->getIdsFromArray($user, 'uid');
			
			if(empty($uids)){
				$stat_record['new_reg'] = 0;
			}else{
				$partnerDB->setText("select count(uid) from web_reg_log where sign='".$stat_record['channel_id']."' and uid in (".implode(',', $uids).")");
				$stat_record['new_reg'] = $partnerDB->queryScalar();
			}
			
			$stat_record['new_recharge'] = 0;
			$stat_record['recharge_money'] = floatval(0);
			
			#所有通过该渠道注册的人
			//取出的量过大，超过了php的内存限制，此情况发生在mysql能够正常取出数据，php计算超限的情况，为优化查询的扫描行数
			$count = $partnerDB->setText("select count(*) from web_reg_log where sign=:sign")->queryScalar(array(':sign' => $channel_info['channel_id']));
			$partnerDB->reset();
			$page = ceil($count/$this->pagesize);
			$id = 0;
			for($i = 0; $i < $page; $i++){
				$sql = "select uid from web_reg_log where uid > ".$id." and sign=:sign order by uid asc limit ".$this->pagesize;
				$partnerDB->setText($sql);
				$partner_user = $partnerDB->queryAll(true, array(':sign' => $channel_info['channel_id']));
				$partnerDB->reset();
				$user_id_arr = $this->getIdsFromArray($partner_user, 'uid');
				$id = $user_id_arr[count($user_id_arr)-1];
// 				echo $channel_info['channel_id'].'='.count($user_id_arr)."\n";
			
				$sql = "select uid,money,currencycode from web_user_recharge_records where uid in (".implode(',', $user_id_arr).") and issuccess=2 and rtime >= ".$day_begin." and rtime < ".$day_end;
				if(!$this->user_records_db->getActive()){
					$this->user_records_db->setActive(true);
				}
				$rs = $this->user_records_db->createCommand()->setText($sql)->queryAll();
				$new_recharge = array();
				foreach($rs as $r){
					if($r == 'USD'){
						$r['money'] *= 6;
					}
					$new_recharge[$r['uid']] = 1;
					#当日充值金额
					$stat_record['recharge_money'] += $r['money'];
				}
				#新增充值用户数
				$stat_record['new_recharge'] = count($new_recharge);
			}
			
			#算扣量后数值
			$partnerDB->setText('select percent from web_regpromote_user where user_id='. $stat_record['user_id']);
			$percent = $partnerDB->queryScalar();
			$percent = 1 - $percent;
			$stat_record['new_reg_after'] = intval($percent*$stat_record['new_reg']);
			$stat_record['new_recharge_after'] = intval($percent*$stat_record['new_recharge']);
			$stat_record['recharge_money_after'] = $percent*$stat_record['recharge_money'];
			
			#算累计数值：今日新增+昨日累计
			$stat_record['accu_new_reg'] = $stat_record['new_reg'];
			$stat_record['accu_new_recharge'] = $stat_record['new_recharge'];
			$stat_record['accu_recharge_money'] = $stat_record['recharge_money'];
			$stat_record['accu_new_reg_after'] = $stat_record['new_reg_after'];
			$stat_record['accu_new_recharge_after'] = $stat_record['new_recharge_after'];
			$stat_record['accu_recharge_money_after'] = $stat_record['recharge_money_after'];
			$last_day = date('Ymd', strtotime("-1 day", $time));
			
			$last_info = $partnerDB->setText("select accu_new_reg,accu_new_recharge,accu_recharge_money,accu_new_reg_after,accu_new_recharge_after,accu_recharge_money_after from web_regpromote_stat where channel_id='".$stat_record['channel_id']."' and stat_date='".$last_day."'")->queryRow();
			$partnerDB->reset();
			if($last_info){
				$stat_record['accu_new_reg'] += $last_info['accu_new_reg'];
				$stat_record['accu_new_recharge'] += $last_info['accu_new_recharge'];
				$stat_record['accu_recharge_money'] += $last_info['accu_recharge_money'];
				$stat_record['accu_new_reg_after'] += $last_info['accu_new_reg_after'];
				$stat_record['accu_new_recharge_after'] += $last_info['accu_new_recharge_after'];
				$stat_record['accu_recharge_money_after'] += $last_info['accu_recharge_money_after'];
			}
			
			$partnerDB->setText("INSERT INTO web_regpromote_stat(stat_date, channel_id, user_id, new_reg, accu_new_reg, new_recharge, accu_new_recharge, recharge_money, 
                accu_recharge_money, new_reg_after, accu_new_reg_after, new_recharge_after, accu_new_recharge_after, recharge_money_after, accu_recharge_money_after) 
                values('".$stat_record['stat_date']."', '".$stat_record['channel_id']."', '".$stat_record['user_id']."', '".$stat_record['new_reg']."',
                '".$stat_record['accu_new_reg']."', '".$stat_record['new_recharge']."', '".$stat_record['accu_new_recharge']."', '".$stat_record['recharge_money']."', '".$stat_record['accu_recharge_money']."',
                '".$stat_record['new_reg_after']."', '".$stat_record['accu_new_reg_after']."', '".$stat_record['new_recharge_after']."', '".$stat_record['accu_new_recharge_after']."',
                '".$stat_record['recharge_money_after']."', '".$stat_record['accu_recharge_money_after']."');");
// 			echo $partnerDB->getText()."\n";
			$partnerDB->execute();
		}
	}
	
	/**
	 * 每天跑一次的脚本
	 */
	public function actionStatPopularizationEveryDay($date = ''){
		$date = empty($date) ? date('Y-m-d', strtotime('-1 day')) : $date;
		$partnerDB = $this->partner_db->createCommand();
		$userDB = $this->user_db->createCommand();
		$recordDB = $this->user_records_db->createCommand();
		$base = 3;
		$percent = 0.2;
		
		$day_begin = strtotime(date('Y-m-d', strtotime($date)).' 00:00:00');
		$day_end = strtotime(date('Y-m-d', strtotime('+1 day', strtotime($date))).'00:00:00');
		
		$partnerDB->setText("select channel_id,partner_id from web_partner_channel order by channel_id asc");
		$channel_info_list = $partnerDB->queryAll();
		
		$id_array = array();
		foreach($channel_info_list as $channel_info){
			$stat_record = array();
			$stat_record['stat_date'] = date('Ymd');
			$stat_record['channel_id']= $channel_info['channel_id'];
			$stat_record['partner_id']= $channel_info['partner_id'];
		
			#当日注册人数
			$user = $userDB->select('uid')->from('web_user_base')->where('create_time >=' . $day_begin . ' and create_time < ' . $day_end)->queryAll();
			$uids = $this->getIdsFromArray($user, 'uid');
			$userDB->reset();
				
			if(empty($uids)){
				$stat_record['new_reg'] = 0;
			}else{
				$partnerDB->setText("select count(uid) from web_reg_log where sign='".$stat_record['channel_id']."' and uid in (".implode(',', $uids).")");
				$stat_record['new_reg'] = $partnerDB->queryScalar();
				$partnerDB->reset();
			}
			
			$stat_record['new_recharge'] = 0;
			$stat_record['recharge_money'] = floatval(0);
			$stat_record['percentage_money'] = 0;
			
			#所有通过该渠道注册的人
			//取出的量过大，超过了php的内存限制，此情况发生在mysql能够正常取出数据，php计算超限的情况
			$count = $partnerDB->setText("select count(*) from web_reg_log where sign=:sign")->queryScalar(array(':sign' => $channel_info['channel_id']));
			$partnerDB->reset();
			$page = ceil($count/$this->pagesize);
			$id = 0;
			$flag = false;
			for($i = 0; $i < $page; $i++){
				$partnerDB->setText("select uid from web_reg_log where uid > ".$id." and sign=:sign order by uid asc limit ".$this->pagesize);
				$partner_user = $partnerDB->queryAll(true, array(':sign' => $channel_info['channel_id']));
				$partnerDB->reset();
				$user_id_arr = $this->getIdsFromArray($partner_user, 'uid');
				$flag = true;
				$id = $user_id_arr[count($user_id_arr)-1];
				
				$rs = $recordDB->setText("select uid,money,currencycode from web_user_recharge_records where uid in (".implode(',', $user_id_arr).") and issuccess=2 and rtime >= ".$day_begin." and rtime < ".$day_end)->queryAll();
				$recordDB->reset();
				$new_recharge = array();
				foreach($rs as $r){
					if($r == 'USD'){
						$r['money'] *= 6;
					}
					$new_recharge[$r['uid']] = 1;
					#当日充值金额
					$stat_record['recharge_money'] += $r['money'];
				}
				#新增充值用户数
				$stat_record['new_recharge'] = count($new_recharge);
			}
			if($flag){
				#分成金额
				$temp_money = $base*$stat_record['new_recharge'];
				$stat_record['percentage_money'] = $temp_money + $percent*(floatval($stat_record['recharge_money'])-$temp_money);
			}
			
			$partnerDB->setText("REPLACE INTO web_stat_popularize(stat_date, channel_id, partner_id, new_reg, new_recharge, recharge_money, percentage_money) 
                values('".$stat_record['stat_date']."', '".$stat_record['channel_id']."', '".$stat_record['partner_id']."', '".$stat_record['new_reg']."', 
		                '".$stat_record['new_recharge']."', '".$stat_record['recharge_money']."', '".$stat_record['percentage_money']."');");
		    //echo $partnerDB->getText();
			$partnerDB->execute();
		}
	}
	
	/**
	 * 每小时跑一次的脚本
	 */
	public function actionStatPopulariationEveryHour(){
		$this->actionStatPopularizationEveryDay(date('Y-m-d'));
	}
	
	/**
	 * 渠道每日登录统计
	 * @author supeng
	 */
	public function actionLoginStatEveryDay(){
		$partnerDB = $this->partner_db->createCommand();
		$recordDB = $this->user_records_db->createCommand();
		
		$day_begin = False;
		$day_end = False;
		
		//渠道注册表记录是否为空
		$sql = "select count(1) as total from web_login_stat_partner";
		$partnerDB->setText($sql);
		$rs = $partnerDB->queryScalar();
		$partnerDB->reset();
		
		//检索全部数据
		$sql = "select count(1) as total from web_user_login_records";
		
		if($rs > 0){
			$time = strtotime('-1 day');
			$day_begin = strtotime(date('Y-m-d', $time).' 00:00:00');
			$day_end = strtotime(date('Y-m-d', strtotime("+1 day", $time)).'00:00:00');
			$currDate = date("Ymd",time());
			
			$delSql = "delete from web_login_stat_partner where create_time = {$currDate}";
			$partnerDB->setText($delSql);
			$partnerDB->execute();
			$partnerDB->reset();
			//检索时间范围内的数据
			$sql .= " where login_time >= {$day_begin} and login_time < {$day_end}";
		}
		$recordDB->setText($sql);
		$count = $recordDB->queryScalar();

		//分页分批处理
		$pageSize = 2000;
		$pageTotal = ceil($count/$pageSize)+1;
		
		$run = true;
   	 	$page = 0;
   	 	
   	 	while ($run){
   	 		$page = $page+1;
   	 		if($page > $pageTotal){
   	 			$run = false;
   	 		}
   	 		
   	 		$loginRs = $this->_getLoginStatForPage($recordDB,$page,$pageSize,$day_begin,$day_end);
   	 		if($loginRs && count($loginRs) > 0){
   	 			$uids = array();
   	 			$uidTimes = array();
   	 			
   	 			foreach($loginRs as $login){
   	 				$uid = $login['uid'];
   	 				$create_time = $login['create_time'];
   	 				
   	 				if(!in_array($uid, $uids)){
   	 					$uids[] = $uid;
   	 				}
   	 				
   	 				if (!key_exists($uid, $uidTimes)){
   	 					$uidTimes[$uid][$create_time] = 1;
   	 				}elseif (!key_exists($create_time, $uidTimes[$uid])){
   	 					$uidTimes[$uid][$create_time] = 1;
   	 				}else{
   	 					$uidTimes[$uid][$create_time] += 1;
   	 				}
   	 				
   	 			}
   	 			
   	 			if($uids){
   	 				$inUids = implode(',', $uids);
   	 				//匹配渠道的用户
   	 				$sql = "SELECT rl.uid,pc.partner_id,pc.channel_id
   	 				FROM web_partner_channel pc
   	 				LEFT JOIN web_reg_log rl ON rl.sign = pc.channel_id
   	 				WHERE rl.uid IN ({$inUids}) and length(pc.channel_id) >= 8";
   	 			
   	 				$partnerDB->setText($sql);
   	 				$channelRs = $partnerDB->queryAll();
   	 				$partnerDB->reset();
   	 				
   	 				if(count($channelRs) > 0){
	   	 				foreach($channelRs as $rs){
	   	 					$uid = $rs['uid'];
	   	 					$partner_id = $rs['partner_id'];
	   	 					$channel_id = $rs['channel_id'];
	   	 			
	   	 					foreach($uidTimes[$uid] as $create_time=>$times){
	   	 						$sql = "INSERT INTO web_login_stat_partner(uid, channel_name, partner_id, times, create_time)
	   	 							VALUES({$uid},'{$channel_id}',{$partner_id},{$times},{$create_time})";
	   	 						
		   	 					$partnerDB->setText($sql);
		   	 					$partnerDB->execute();
		   	 					$partnerDB->reset();
	   	 					}
	   	 				}
   	 				}
   	 			}
   	 		}
   	 	}
	}
	
	/**
	 * 渠道每日在线时长统计
	 * 	以在直播间的在线时间为准
	 * @author supeng
	 */
	public function actionOnlineStatEveryDay(){
		$partnerDB = $this->partner_db->createCommand();
		$recordDB = $this->user_records_db->createCommand();
		
		$time = strtotime('-1 day');
		$day_begin = strtotime(date('Y-m-d', $time).' 00:00:00');
		$day_end = strtotime(date('Y-m-d', strtotime("+1 day", $time)).'00:00:00');
		
		$formatTime = "FROM_UNIXTIME(lso.create_time,'%Y%m%d') as create_time";
		$sql = "SELECT pi.partner_id, pc.channel_id, lso.uid, {$formatTime} , sum(lso.time_online) as time_online
		FROM web_partner_info pi
		LEFT JOIN web_partner_channel pc ON pc.partner_id = pi.partner_id
		LEFT JOIN web_reg_log rl ON rl.sign = pc.channel_id
		LEFT JOIN web_login_stat_online lso ON lso.uid = rl.uid
		WHERE length(pc.channel_id) >=8 and lso.create_time>={$day_begin} and lso.create_time<{$day_end}  group by create_time,lso.uid";
		
		$partnerDB->setText($sql);
		$dayInfoList = $partnerDB->queryAll();
		$partnerDB->reset();
		
		if($dayInfoList){
			foreach ($dayInfoList as $dayInfo){
				$sql = "INSERT INTO web_login_stat_online_day(uid, channel_name, partner_id, time_online, create_time)
				VALUES({$dayInfo['uid']},'{$dayInfo['channel_id']}',{$dayInfo['partner_id']},{$dayInfo['time_online']},{$dayInfo['create_time']})";
				
				$partnerDB->setText($sql);
				$partnerDB->execute();
				$partnerDB->reset();
			}
		}
	}
	
	protected function batchInsert(CDbCommand &$db, $table, $data){
		if(empty($data)) return false;
		sort($data);
		$keys = array_keys($data[0]);
		if(empty($keys)) return false;
		$sql = "INSERT INTO {$table}(".implode(',', $keys).") VALUES";
		$values = array();
		foreach($data as $d){
			$sql .= '('.rtrim(str_repeat('?,', count($keys)), ',').'),';
			foreach($d as $v) array_push($values, $v);
		}
		$sql = rtrim($sql, ',').';';
		$db->setText($sql);
		return $db->execute($values);
	}
	
	protected function getIdsFromArray($array, $column, $index = ''){
		$ids = array();
		foreach($array as $k => $v){
			if(empty($index)) $ids[] = $v[$column];
			else $ids[$v[$index]] = $v[$column];
		}
		//array_unique 会发生内存不足
		if(empty($index)){
			$temp = $ids;
			$ids = array();
			foreach($temp as $v){
				$ids[$v] = 1;
			}
			$ids = array_keys($ids);
		}
		return $ids;
	}
	
	protected function buildArray($array, $index){
		$data = array();
		foreach($array as $k => $v){
			$data[$v[$index]] = $v;
		}
		return $data;
	}
	
	private function _getLoginStatForPage($db,$page,$limit,$day_begin=false,$day_end = false){
		$offset = ($page-1)*$limit;
		$endset = $page*$limit;
		
		$format = " FROM_UNIXTIME(login_time,'%Y%m%d') as create_time ";
		$tableName = ' web_user_login_records ';
		$sql = "select uid,{$format} from {$tableName} limit {$offset},{$limit}";
		if($day_begin && $day_end){
			$sql = "select uid,{$format} from {$tableName} where login_time >= {$day_begin} and login_time < {$day_end} limit {$offset},{$limit}";
		}
		
		$db->setText($sql);
		$result = $db->queryAll();
		$db->reset();
		//echo "Start batch data between {$offset} to {$endset} region \r\n";
		return $result;
	} 
}