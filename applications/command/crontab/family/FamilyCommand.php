<?php
/**
 * 家族
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: 2013-8-23 上午9:54:24 hexin $ 
 * @package
 */
class FamilyCommand extends PipiConsoleCommand {
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
	 * @var CDbConnection 支付库操作
	 */
	protected $account_db;
	/**
	 * @var CDbConnection 家族库操作
	 */
	protected $family_db;
	
	public function beforeAction($action,$params){
		parent::beforeAction($action, $params);
		$this->consume_db = Yii::app()->db_consume;
		$this->consume_records_db =  Yii::app()->db_consume_records;
		$this->user_db = Yii::app()->db_user;
		$this->user_records_db = Yii::app()->db_user_records;
		$this->account_db = Yii::app()->db_account;
		$this->family_db = Yii::app()->db_family;
		return true;
	}
	
	/**
	 * 每5分钟自动筹备
	 */
	public function actionAutoPrepare(){
		$prepare_members = 9; //筹备需满足8名成员, 不包含族长
		$prepare_time = 86400 * 3; //筹备时间3天
		$sql = "SELECT id,create_time FROM web_family WHERE status = 0";
		$command=$this->family_db->createCommand();
		$command->setText($sql);
		$family = $command->queryAll();
		
		if(!empty($family)){
			$family_ids = array();
			foreach($family as $f){
				$family_ids[] = $f['id'];
			}
			$sql = "SELECT family_id,count(*) as count FROM web_family_member WHERE family_id in (".implode(',', $family_ids).") group by family_id";
			$tmp = $this->family_db->createCommand()->setText($sql)->queryAll();
			$member = array();
			foreach($tmp as $t){
				$member[$t['family_id']] = $t['count'];
			}
			
			$success = $failed = array();
			foreach($family as $f){
				if($f['create_time'] < time() - $prepare_time){
					$failed[] = $f['id'];
				}elseif($member[$f['id']] >= $prepare_members && $f['create_time'] >= time() - $prepare_time){
					$success[] = $f['id'];
				}
			}
			$service = FamilyService::getInstance();
			if(!empty($success)){
				foreach($success as $fid){
					$service->changeFamilyStatus($fid, 2, '筹备成功');
					
					$family = $service->getFamily($fid);
					//家族状态改变提醒
					$title = '家族状态提醒';
					$content = '您的 '.$family['name'].' 筹备成功！';
					$url = '/index.php?r=family/prepare&family_id='.$family['id'];
					
					$messageService = new MessageService();
					$message['uid'] = 0;
					$message['to_uid'] = $family['uid'];
					$message['title'] = $title;
					$message['category'] = MESSAGE_CATEGORY_FAMILY;
					$message['sub_category'] = MESSAGE_CATEGORY_FAMILY_MANAGE;
					$message['target_id'] =  $family['id'];
					$message['content'] = $content;
					$message['extra']= array('from'=>$family['name'],'href'=>$url);
					$messageService->sendMessage($message);
				}
			}
			if(!empty($failed)){
				foreach($failed as $fid){
					$service->changeFamilyStatus($fid, -2, '筹备失败');
					
					$family = $service->getFamily($fid);
					//家族状态改变提醒
					$title = '家族状态提醒';
					$content = '您的 '.$family['name'].' 筹备失败！';
					$url = '';
						
					$messageService = new MessageService();
					$message['uid'] = 0;
					$message['to_uid'] = $family['uid'];
					$message['title'] = $title;
					$message['category'] = MESSAGE_CATEGORY_FAMILY;
					$message['sub_category'] = MESSAGE_CATEGORY_FAMILY_MANAGE;
					$message['target_id'] =  $family['id'];
					$message['content'] = $content;
					$message['extra']= array('from'=>$family['name'],'href'=>$url);
					$messageService->sendMessage($message);
				}
			}
		}
	}
	
	/**
	 * 每5分钟定时检查家族成员的普通主播身份是否变更，家族主播身份不允许变更，普通主播不发族徽，家族主播发放族徽
	 * 普通用户变为普通主播，不能变为家族主播，要退出后再进入才可称为家族主播
	 * 普通主播变普通用户，这个改变不影响家族，可随时改变
	 * 家族主播变普通用户，这个不允许改变，影响家族的结算
	 */
	public function actionCheckDotey(){
		//当前在直播的真正主播
		$sql = "SELECT a.uid FROM web_dotey_base as a LEFT JOIN web_user_base u ON u.uid = a.uid WHERE u.user_status = 0 and u.user_type & 2";
		$dotey = $this->user_db->createCommand()->setText($sql)->queryColumn();
		
		$sql = "SELECT * FROM web_family_member WHERE uid in(".implode(',', $dotey).") and is_dotey = 0";
		$members = $this->family_db->createCommand()->setText($sql)->queryAll();
		
		$sql = "SELECT * FROM web_family WHERE sign = 1 and status > 0";
		$temp = $this->family_db->createCommand()->setText($sql)->queryAll();
		$familys = array();
		foreach($temp as $t){
			$familys[$t['id']] = $t;
		}
		
		//原先不是主播后来成为主播的用户在家族中自动转变主播身份
		if(!empty($members)){
			$update = $uids = $family_dotey = array();
			foreach($members as $m){
				//需要更新普通主播身份
				if(!in_array($m['uid'], $uids)){
					$uids[] = $m['uid'];
				}
				//需要更新没有拥有族徽的家族主播
				if(isset($familys[$m['family_id']]) && $m['family_dotey'] == 1 && $m['have_medal'] == 0){
					$update[] = $m['id'];
				}
			}
			if(!empty($uids)){
				$sql = "UPDATE web_family_member SET is_dotey = 1 WHERE uid in(".implode(',', $uids).")";
				$this->family_db->createCommand()->setText($sql)->execute();
			}
			if(!empty($update)){
				$sql = "UPDATE web_family_member SET have_medal = 1, buy_type = 0, buy_time = ".time()." WHERE id in(".implode(',', $update).")";
				$this->family_db->createCommand()->setText($sql)->execute();
			}
		}
		
		//原先是主播后来不再是主播的用户在家族中自动转变非主播身份
		$sql = "SELECT uid,family_id FROM web_family_member WHERE uid not in(".implode(',', $dotey).") and is_dotey = 1";
		$user = $this->family_db->createCommand()->setText($sql)->queryAll();
		$update = array();
		if(!empty($user)){
			$uids = array();
			foreach($user as $u){
				if(!in_array($u['uid'], $uids)) $uids[] = $u['uid'];
			}
			if(!empty($uids)){
				$sql = "UPDATE web_family_member SET is_dotey = 0 WHERE uid in(".implode(',', $uids).")";
				$this->family_db->createCommand()->setText($sql)->execute();
			}
		}
		
		//未佩戴族徽的家族主播自动发放族徽
		$sql = "SELECT * FROM web_family_member WHERE uid in(".implode(',', $dotey).") and family_dotey = 1 and have_medal = 0";
		$dotey = $this->family_db->createCommand()->setText($sql)->queryAll();
		if(!empty($dotey)){
			$family_ids = $dotey_ids = array();
			foreach($dotey as $d){
				if(!in_array($d['family_id'], $family_ids)) $family_ids[] = $d['family_id'];
				$dotey_ids[] = $d['id'];
			}
			$sql = "UPDATE web_family_member SET have_medal = 1, buy_time = ".time()." WHERE id in(".implode(',', $dotey_ids).")";
			$this->family_db->createCommand()->setText($sql)->execute();
			$familys = FamilyService::getInstance()->getFamilyIds($family_ids);
			foreach($familys as $f){
				FamilyService::getInstance()->saveFamily($f);
			}
		}
	}
	
	/**
	 * 定时计算家族消费
	 */
	public function actionFamilyConsume(){
		$end_time = time();
		$sql = "SELECT create_time FROM web_family_consume_records ORDER BY id DESC LIMIT 1";
		$start_time = $this->family_db->createCommand()->setText($sql)->queryScalar();
		//没有最后时间或最后时间有异常的情况下初始化开始时间
 		if(empty($start_time) || $start_time < strtotime('-5 minutes', $end_time)) $start_time = strtotime('-5 minutes', $end_time);
		
		$dedication = $charm = $recharge = $proxy = $medal = $quit = $unload = $member = array();
		//贡献纪录
		$sql = "SELECT uid, dedication, create_time FROM web_user_dedication_records WHERE create_time > ".$start_time." and create_time <=".$end_time;
// 		echo $sql."\n";
		$tmp = $this->consume_records_db->createCommand()->setText($sql)->queryAll();
		if(!empty($tmp)){
			foreach($tmp as $t){
				$dedication[$t['uid']][] = $t;
			}
		}
		
		//魅力纪录
		$sql = "SELECT uid, charm, create_time FROM web_dotey_charm_records WHERE create_time > ".$start_time." and create_time <=".$end_time;
// 		echo $sql."\n";
		$tmp = $this->consume_records_db->createCommand()->setText($sql)->queryAll();
		if(!empty($tmp)){
			foreach($tmp as $t){
				$charm[$t['uid']][] = $t;
			}
		}
		
		//充值记录
		$sql = "SELECT uid, pipiegg, ctime FROM web_user_recharge_records WHERE ctime > ".$start_time." and ctime <=".$end_time." and issuccess = 2";
// 		echo $sql."\n";
		$tmp = $this->user_records_db->createCommand()->setText($sql)->queryAll();
		if(!empty($tmp)){
			foreach($tmp as $t){
				$recharge[$t['uid']][] = $t;
			}
		}
		
		//代充记录
		$sql = "SELECT uid, pipiegg, otime FROM uc_proxyrechargelog WHERE otime > ".$start_time." and otime <=".$end_time." and patype = 2";
// 		echo $sql."\n";
		$tmp = $this->account_db->createCommand()->setText($sql)->queryAll();
		if(!empty($tmp)){
			foreach($tmp as $t){
				$proxy[$t['uid']][] = $t;
			}
		}
		
		//族徽销售记录
		$sql = "SELECT to_target_id, count(*) as medal FROM web_user_pipiegg_records WHERE source = 'family' and sub_source = 'medal' and consume_time > ".$start_time." and consume_time <=".$end_time." group by to_target_id";
// 		echo $sql."\n";
		$tmp = $this->consume_records_db->createCommand()->setText($sql)->queryAll();
		if(!empty($tmp)){
			foreach($tmp as $t){
				$medal[$t['to_target_id']] = $t['medal'];
			}
		}
		
		//家族成员
		$sql = "SELECT m.family_id, m.uid, m.family_dotey, m.create_time, m.equip_time, m.medal_enable FROM web_family as f LEFT JOIN web_family_member as m ON f.id = m.family_id WHERE f.status = 1";
		$tmp = $this->family_db->createCommand()->setText($sql)->queryAll();
		$family_ids = array();
		//按家族id排序家族的族徽成员和族徽家族主播
		if(!empty($tmp)){
			foreach($tmp as $t){
				$family_ids[] = $t['family_id'];
				if(!isset($member[$t['family_id']])) 
					$member[$t['family_id']] = array('member' => array(), 'dotey' => array());
				
				$member[$t['family_id']]['member'][] = $t;
				if($t['family_dotey']){
					$member[$t['family_id']]['dotey'][] = $t;
				}
			}
			$family_ids = array_unique($family_ids);
		}
		
		//退出成员记录
		$sql = "SELECT family_id, uid, sum(charm) as charm, sum(dedication) as dedication FROM web_family_quit_records WHERE quit_time > ".$start_time." and quit_time <=".$end_time." group by family_id,uid";
		$tmp = $this->family_db->createCommand()->setText($sql)->queryAll();
		if(!empty($tmp)){
			foreach($tmp as $t){
				$quit[$t['family_id']][$t['uid']] = $t;
			}
		}
		
		//卸下族徽记录
		$sql = "SELECT family_id, uid, sum(recharge) as recharge FROM web_family_unload_records WHERE unload_time > ".$start_time." and unload_time <=".$end_time." group by family_id,uid";
		$tmp = $this->family_db->createCommand()->setText($sql)->queryAll();
		if(!empty($tmp)){
			foreach($tmp as $t){
				$unload[$t['family_id']][$t['uid']] = $t;
			}
		}
		
		if(!empty($member)){
			$records = array();
			$family = FamilyService::getInstance()->getFamilyIds($family_ids);
			foreach($member as $id => $f){
				if($family[$id]['status'] != 1) continue;
				if(!isset($records[$id])){
					$records[$id] = array(
						'family_id'	=> $id,
						'charm'		=> 0,
						'dedication'=> 0,
						'medal'		=> isset($medal[$id]) ? $medal[$id] : 0,
						'recharge'	=> 0,
						'create_time'=> $end_time
					);
				}
				foreach($f['dotey'] as $d){
					$uid = $d['uid'];
					if(isset($charm[$uid])){
						foreach($charm[$uid] as $c){
							if($c['create_time'] >= $family[$id]['create_time'] && $c['create_time'] >= $d['create_time'])
								$records[$id]['charm'] += $c['charm'];
						}
					}
					if(isset($quit[$id][$uid])) $records[$id]['charm'] += $quit[$id][$uid]['charm'];
				}
				foreach($f['member'] as $m){
					$uid = $m['uid'];
					if(isset($dedication[$uid])){
						foreach($dedication[$uid] as $d){
							if($d['create_time'] >= $family[$id]['create_time'] && $d['create_time'] >= $m['create_time'])
								$records[$id]['dedication'] += $d['dedication'];
						}
					}	
					if(isset($quit[$id][$uid])) $records[$id]['dedication'] += $quit[$id][$uid]['dedication'];
					
					//和家族升级有关的是充值，充值时间有关的是族徽的佩戴时间
					if($m['medal_enable']){
						if(isset($recharge[$uid])){
							foreach($recharge[$uid] as $r){
								if($r['ctime'] >= $family[$id]['create_time'] && $r['ctime'] >= $m['equip_time'])
									$records[$id]['recharge'] += $r['pipiegg'];
							}
						}
						if(isset($proxy[$uid])){
							foreach($proxy[$uid] as $p){
								if($p['ctime'] >= $family[$id]['create_time'] && $p['ctime'] >= $m['equip_time'])
									$records[$id]['recharge'] += $p['pipiegg'];
							}
						}
					}
					if(isset($unload[$id][$uid])) $records[$id]['recharge'] += $unload[$id][$uid]['recharge'];
				}
			}
			
			$flag = false;
			$sql = "INSERT INTO web_family_consume_records(family_id, charm, dedication, medal, recharge, create_time) VALUES";
			foreach($records as $r){
				if(!($r['charm'] == 0 && $r['dedication'] == 0 && $r['medal'] == 0 && $r['recharge'] == 0)){
					$sql .= "('".$r['family_id']."','".$r['charm']."','".$r['dedication']."','".$r['medal']."','".$r['recharge']."','".$r['create_time']."'),";
					$flag = true;
				}
			}
			$sql = rtrim($sql, ',');
			if($flag) $this->family_db->createCommand()->setText($sql)->execute();
			
			foreach($records as $r){
				$sql = "UPDATE web_family_extend SET charm_total = charm_total + ".$r['charm'].", dedication_total = dedication_total + ".$r['dedication'].", medal_total = medal_total + ".$r['medal'].", recharge_total = recharge_total + ".$r['recharge']." WHERE family_id = ".$r['family_id'];
// 				echo $sql."\n";
				$this->family_db->createCommand()->setText($sql)->execute();
			}
		}	
	}
	
	/**
	 * 定时生成家族的三种榜单，每种4个榜单，并生成家族榜单荣誉
	 */
	public function actionMakeTop(){
		$now = time();
		$day = date('Y-m-d', $now);
		$week_start = date('Y-m-d', strtotime('-'.(date('N')-1).' days', $now));
		$week_end = date('Y-m-d', strtotime('+6 days', strtotime($week_start)));
		$month_start = date('Y-m', $now).'-01';
		$month_end = date('Y-m-d', strtotime("+".(date('t') -1)." days", strtotime($month_start)));
		
		$sql = "SELECT e.family_id, sum(e.charm) as charm, sum(e.dedication) as dedication, sum(e.medal) as medal, sum(e.recharge) as recharge FROM web_family_consume_records as e LEFT JOIN web_family as f ON e.family_id = f.id WHERE f.status = 1 and f.hidden = 0 and f.forbidden = 0 and e.create_time BETWEEN ".strtotime($day.' 00:00:00')." AND ".strtotime($day.' 23:59:59')." GROUP BY e.family_id";
		$day_top = $this->family_db->createCommand()->setText($sql)->queryAll();
		
		$sql = "SELECT e.family_id, sum(e.charm) as charm, sum(e.dedication) as dedication, sum(e.medal) as medal, sum(e.recharge) as recharge FROM web_family_consume_records as e LEFT JOIN web_family as f ON e.family_id = f.id WHERE f.status = 1 and f.hidden = 0 and f.forbidden = 0 and e.create_time BETWEEN ".strtotime($week_start.' 00:00:00')." AND ".strtotime($week_end .' 23:59:59')." GROUP BY e.family_id";
		$week_top = $this->family_db->createCommand()->setText($sql)->queryAll();
		
		$sql = "SELECT e.family_id, sum(e.charm) as charm, sum(e.dedication) as dedication, sum(e.medal) as medal, sum(e.recharge) as recharge FROM web_family_consume_records as e LEFT JOIN web_family as f ON e.family_id = f.id WHERE f.status = 1 and f.hidden = 0 and f.forbidden = 0 and e.create_time BETWEEN ".strtotime($month_start.' 00:00:00')." AND ".strtotime($month_end .' 23:59:59')." GROUP BY e.family_id";
		$month_top = $this->family_db->createCommand()->setText($sql)->queryAll();
		
// 		$sql = "SELECT e.family_id,e.charm_total FROM web_family_extend as e LEFT JOIN web_family as f ON e.family_id = f.id WHERE f.status = 1 and f.hidden = 0 and f.forbidden = 0 ORDER BY e.charm_total desc limit 10";
// 		$super_top_charm = $this->family_db->createCommand()->setText($sql)->queryAll();
		
		$sql = "SELECT e.family_id,e.dedication_total FROM web_family_extend as e LEFT JOIN web_family as f ON e.family_id = f.id WHERE f.status = 1 and f.hidden = 0 and f.forbidden = 0 ORDER BY e.dedication_total desc limit 10";
		$super_top_dedication = $this->family_db->createCommand()->setText($sql)->queryAll();
		
		$sql = "SELECT e.family_id,e.recharge_total FROM web_family_extend as e LEFT JOIN web_family as f ON e.family_id = f.id WHERE f.status = 1 and f.hidden = 0 and f.forbidden = 0 ORDER BY e.recharge_total desc limit 10";
		$super_top_recharge = $this->family_db->createCommand()->setText($sql)->queryAll();
		
		$sql = "SELECT e.family_id,e.medal_total FROM web_family_extend as e LEFT JOIN web_family as f ON e.family_id = f.id WHERE f.status = 1 and f.hidden = 0 and f.forbidden = 0 ORDER BY e.medal_total desc limit 10";
		$super_top_medal = $this->family_db->createCommand()->setText($sql)->queryAll();
		
		$this->saveFamilyTop($day_top, 'recharge', 'day');
		$this->saveFamilyTop($day_top, 'dedication', 'day');
		$this->saveFamilyTop($day_top, 'medal', 'day');
		$this->saveFamilyTop($week_top, 'recharge', 'week');
		$this->saveFamilyTop($week_top, 'dedication', 'week');
		$this->saveFamilyTop($week_top, 'medal', 'week');
		$this->saveFamilyTop($month_top, 'recharge', 'month');
		$this->saveFamilyTop($month_top, 'dedication', 'month');
		$this->saveFamilyTop($month_top, 'medal', 'month');
		
		$family_ids = array();
		
// 		$family_ids = array_merge($family_ids, array_keys($this->buildByKey($super_top_charm, 'family_id')));
		$family_ids = array_merge($family_ids, array_keys($this->buildByKey($super_top_dedication, 'family_id')));
		$family_ids = array_merge($family_ids, array_keys($this->buildByKey($super_top_recharge, 'family_id')));
		$family_ids = array_merge($family_ids, array_keys($this->buildByKey($super_top_medal, 'family_id')));
		$family_ids = array_unique($family_ids);
		$family = FamilyService::getInstance()->getFamilyIds($family_ids);
		
// 		$data = array();
// 		foreach($super_top_charm as $top){
// 			$id = $top['family_id'];
// 			$data[] = array(
// 				'family_id'	=> $id,
// 				'name'		=> $family[$id]['name'],
// 				'medal_name'=> $family[$id]['medal'],
// 				'level'		=> $family[$id]['level'],
// 				'value'		=> $top['charm_total'],
// 			);
// 		}
// 		OtherRedisModel::getInstance()->setFamilyTop($data, 'charm', 'super');
		
		$data = array();
		foreach($super_top_dedication as $top){
			$id = $top['family_id'];
			$data[] = array(
				'family_id'	=> $id,
				'name'		=> $family[$id]['name'],
				'medal_name'=> $family[$id]['medal'],
				'level'		=> $family[$id]['level'],
				'value'		=> $top['dedication_total'],
			);
		}
		OtherRedisModel::getInstance()->setFamilyTop($data, 'dedication', 'super');
		
		$data = array();
		foreach($super_top_recharge as $top){
			$id = $top['family_id'];
			$data[] = array(
					'family_id'	=> $id,
					'name'		=> $family[$id]['name'],
					'medal_name'=> $family[$id]['medal'],
					'level'		=> $family[$id]['level'],
					'value'		=> $top['recharge_total'],
			);
		}
		OtherRedisModel::getInstance()->setFamilyTop($data, 'recharge', 'super');
		
		$data = array();
		foreach($super_top_medal as $top){
			$id = $top['family_id'];
			$data[] = array(
				'family_id'	=> $id,
				'name'		=> $family[$id]['name'],
				'medal_name'=> $family[$id]['medal'],
				'level'		=> $family[$id]['level'],
				'value'		=> $top['medal_total'],
			);
		}
		OtherRedisModel::getInstance()->setFamilyTop($data, 'medal', 'super');
		
		$this->saveTopHonor($week_top, $now);
	}
	
	/**
	 * 保存家族榜单
	 * @param array $data
	 * @param string $type 榜单种类
	 * @param string $date 时间类型
	 * @return boolean
	 */
	protected function saveFamilyTop($data, $type, $date){
		if(empty($data) || !is_array($data)) return false;
		$family_ids = array();
		usort($data, array($this, 'sortBy'.ucfirst($type)));
		$data = array_slice($data, 0, 10);
		$family_ids = array_keys($this->buildByKey($data, 'family_id'));
		$family = FamilyService::getInstance()->getFamilyIds($family_ids);
		
		$array = array();
		foreach($data as $top){
			$id = $top['family_id'];
			$array[] = array(
				'family_id'	=> $id,
				'name'		=> $family[$id]['name'],
				'medal_name'=> $family[$id]['medal'],
				'level'		=> $family[$id]['level'],
				'value'		=> $top[$type],
			);
		}
		return OtherRedisModel::getInstance()->setFamilyTop($array, $type, $date);
	}
	
	/**
	 * 生成家族榜单荣誉
	 * @param array $data
	 * @param int $time 当前脚本执行的时刻
	 */
	protected function saveTopHonor($data, $time){
		if(date('N', $time) == 7 && date('H', $time) == 23 && !empty($data)){
			$fids = array();
			foreach($data as $v){
				$fids[] = $v['family_id'];
			}
			$sql = "SELECT family_id FROM web_family_honor WHERE family_id in(".implode(',', $fids).") AND type = 'top' AND create_time BETWEEN ".strtotime(date('Y-m-d', $time)." 23:00:00")." AND ".strtotime(date('Y-m-d', $time)." 23:59:59");
			$exist_ids = $this->family_db->createCommand()->setText($sql)->queryColumn();
			$fids = array_diff($fids, $exist_ids);
			
			if(!empty($fids)){
				$week_start = date('Y-m-d', strtotime('+1 days', $time)); //周一的日期
				$firt_week = 7 - date('N', strtotime(date('Y-m', strtotime($week_start)).'-01')) + 1; //当月第一周的天数
				if($firt_week > 0) $num = 1;
				else $num = 0;
				$num += ceil((date('j', strtotime($week_start)) - $firt_week)/7); //当月第几周
				
				$top_charm = $top_dedication = $top_recharge = $top_medal = $data;
//	 			usort($top_charm, array($this, 'sortByCharm'));
				usort($top_dedication, array($this, 'sortByDelication'));
				usort($top_recharge, array($this, 'sortByRecharge'));
				usort($top_medal, array($this, 'sortByMedal'));
				$service = FamilyService::getInstance();
				foreach($top_dedication as $k => $v){
					if(!in_array($v['family_id'], $fids)) continue;
					$top = array(
						'num' => $num,
//	 					'charm'	=> $k+1,
						'dedication' => $k + 1,
						'recharge' => array_search($v, $top_recharge) + 1,
						'medal'	=> array_search($v, $top_medal) +1,	
					);
					$honor = array(
						'family_id'	=> $v['family_id'],
						'type'		=> 'top',
						'honor'		=> json_encode($top),
					);
					$service->saveHonor($honor);
					
					//家族升级提醒
					$family = $service->getFamily($v['family_id']);
					$members = $service->getMembers($v['family_id']);
					$uids = array_keys($service->buildDataByIndex($members, 'uid'));
					$title = '家族荣誉提醒';
					$content = $family['name'].' 晋升至Lv'.$family['level'].'！全族同庆，可喜可贺！';
					$url = '/index.php?r=family/home&family_id='.$family['id'];
						
					$messageService = new MessageService();
					$message['uid'] = 0;
					$message['to_uid'] = $uids;
					$message['title'] = $title;
					$message['category'] = MESSAGE_CATEGORY_FAMILY;
					$message['sub_category'] = MESSAGE_CATEGORY_FAMILY_UPGRADE;
					$message['target_id'] =  $family['id'];
					$message['content'] = $content;
					$message['extra']= array('from'=>$family['name'],'href'=>$url);
					$messageService->sendMessage($message);
				}
			}
		}
	}
	
	protected function sortByCharm($a, $b){
		if($a['charm'] == $b['charm']) return 0;
		else return $a['charm'] > $b['charm'] ? -1 : 1;
	}
	
	protected function sortByDedication($a, $b){
		if($a['dedication'] == $b['dedication']) return 0;
		else return $a['dedication'] > $b['dedication'] ? -1 : 1;
	}
	
	protected function sortByRecharge($a, $b){
		if($a['recharge'] == $b['recharge']) return 0;
		else return $a['recharge'] > $b['recharge'] ? -1 : 1;
	}
	
	protected function sortByMedal($a, $b){
		if($a['medal'] == $b['medal']) return 0;
		else return $a['medal'] > $b['medal'] ? -1 : 1;
	}
	
	protected function buildByKey($list, $key){
		if(empty($list)) return array();
		$array = array();
		foreach($list as $l){
			if(isset($l[$key])){
				$array[$l[$key]] = $l;
			}
		}
		return $array;
	}
	
	/**
	 * 每小时定时检查家族是否可以升级，并检查已升级的是否保级，必须每小时执行一次
	 */
	public function actionFamilyUpgrade(){
		$sql = "SELECT e.family_id, e.recharge_total, f.level, f.medal FROM web_family_extend as e LEFT JOIN web_family as f ON e.family_id = f.id WHERE f.status = 1 and f.hidden = 0 and f.forbidden = 0 and sign = 0";
		$family = $this->family_db->createCommand()->setText($sql)->queryAll();
		$service = FamilyService::getInstance();
		$level = $service->getAllLevel();
		$fCheck = array();
		foreach($family as $f){
			if(isset($level[$f['level']+1]) && $f['recharge_total'] >= $level[$f['level']+1]['upgrade']){
				$family = array(
					'id'	=> $f['family_id'],
					'level'		=> $f['level']+1,
				);
				$service->saveFamily($family);
				$record = array(
					'family_id'	=> $f['family_id'],
					'type'		=> 'upgrade',	
				);
				$service->saveLevelRecord($record);
				
				//生成族徽
				$src = "fontimg".DIR_SEP."family".DIR_SEP.$family['level'];
				$dst = "family".DIR_SEP.$family['id'].DIR_SEP."medal_".$family['level'];
				$service->makeMedal($f['medal'], STATIC_PATH.$src."1.png", IMAGES_PATH.$dst."1.jpg");
				$service->makeMedal($f['medal'], STATIC_PATH.$src."2.png", IMAGES_PATH.$dst."2.jpg");
				$service->makeMedal($f['medal'], STATIC_PATH.$src."3.png", IMAGES_PATH.$dst."3.jpg");
				
				$family = $service->getFamily($f['family_id']);
				$members = $service->getMedalMemberByFamily($f['family_id']);
				if(!empty($members)){
					foreach($members as $m){
						$service->saveMyMedal($m['uid'], $family, $m['role_id']);
					}
				}
				
				//家族升级提醒
				$members = $service->getMembers($f['family_id']);
				$uids = array_keys($service->buildDataByIndex($members, 'uid'));
				$title = '家族升级提醒';
				$content = $family['name'].' 晋升至Lv'.$family['level'].'！全族同庆，可喜可贺！';
				$url = '/index.php?r=family/home&family_id='.$family['id'];
					
				$messageService = new MessageService();
				$message['uid'] = 0;
				$message['to_uid'] = $uids;
				$message['title'] = $title;
				$message['category'] = MESSAGE_CATEGORY_FAMILY;
				$message['sub_category'] = MESSAGE_CATEGORY_FAMILY_UPGRADE;
				$message['target_id'] =  $family['id'];
				$message['content'] = $content;
				$message['extra']= array('from'=>$family['name'],'href'=>$url);
				$messageService->sendMessage($message);
			}elseif($f['level'] > 1){
				$fCheck[$f['family_id']] = $f;
			}
		}
		
// 		$now = time();
// 		//该判断只允许每小时执行一次
// 		if(date('d', $now) == '01' && date('H', $now) == '00'){
// 			if(!empty($fCheck)){
// 				//除去上个月刚刚升级，本月进入保护期的家族，其他都做是否保级的判断
// 				$month_start = date('Y-m', strtotime('-1 day', $now)).'-01';
// 				$month_end = date('Y-m-d', strtotime('-1 day', $now));
// 				$sql = "SELECT family_id FROM web_family_level_records WHERE type = 'upgrade' and create_time BETWEEN ".strtotime($month_start." 00:00:00")." AND ".strtotime($month_end." 23:59:59");
// 				$filter = $this->family_db->createCommand()->setText($sql)->queryColumn();
// 				$fids = array_diff(array_keys($fCheck), $filter);
				
// 				if(!empty($fids)){
// 					$sql = "SELECT family_id, sum(recharge) as recharge FROM web_family_consume_records WHERE family_id in (".implode(',', $fids).") AND create_time BETWEEN ".strtotime($month_start." 00:00:00")." AND ".strtotime($month_end." 23:59:59")." group by family_id";
// 					$list = $this->family_db->createCommand()->setText($sql)->queryAll();
// 					foreach($list as $f){
// 						$lv = $fCheck[$f['family_id']]['level'];
// 						if($f['recharge'] >= $level[$lv]['keep']){
// 							$record = array(
// 								'family_id'	=> $f['family_id'],
// 								'type'		=> 'keep',
// 							);
// 							$service->saveLevelRecord($record);
// 						}else{
// 							$family = array(
// 								'id'	=> $f['family_id'],
// 								'level'		=> $lv-1,
// 							);
// 							$service->saveFamily($family);
// 							$record = array(
// 								'family_id'	=> $f['family_id'],
// 								'type'		=> 'degrade',
// 							);
// 							$service->saveLevelRecord($record);
							
// 							$members = $service->getMedalMemberByFamily($f['family_id']);
// 							if(!empty($members)){
// 								$family = $service->getFamily($f['family_id']);
// 								foreach($members as $m){
// 									$service->saveMyMedal($m['uid'], $family, $m['role_id']);
// 								}
// 							}
							
// 							//生成族徽
// 							$src = "fontimg".DIR_SEP."family".DIR_SEP.$family['level'];
// 							$dst = "family".DIR_SEP.$family['id'].DIR_SEP."medal_".$family['level'];
// 							$service->makeMedal($f['medal'], STATIC_PATH.$src."1.png", IMAGES_PATH.$dst."1.jpg");
// 							$service->makeMedal($f['medal'], STATIC_PATH.$src."2.png", IMAGES_PATH.$dst."2.jpg");
// 							$service->makeMedal($f['medal'], STATIC_PATH.$src."3.png", IMAGES_PATH.$dst."3.jpg");
// 						}
// 					}
// 				}
// 			}
// 		}
	}
	
	/**
	 * 每5分钟计算一次家族榜单的名次
	 */
	public function actionTopNumber(){
		$service = FamilyService::getInstance();
		$sql = 'select id from web_family where status = 1';
		$family_ids = $this->family_db->createCommand()->setText($sql)->queryColumn();
		if(empty($family_ids)) return ;
		$top_dedication = $top_members = $top_medal = $top_rank = $top_rank1 = $top_rank2 = $top_rank3 = array();
		
		//计算贡献值排行榜名次
		$sql = 'select family_id, dedication_total from web_family_extend where family_id in ('.implode(',', $family_ids).') and dedication_total > 0 order by dedication_total desc, family_id asc';
		$list = $this->family_db->createCommand()->setText($sql)->queryAll();
		if(!empty($list)){
			foreach($list as $i => $f){
				$top_dedication[$f['family_id']] = $i + 1;
			}
		}
		
		//计算家族成员人数排行榜名次
		$sql = 'select family_id, count(uid) as members from web_family_member where family_id in ('.implode(',', $family_ids).') group by family_id order by members desc, family_id asc';
		$list = $this->family_db->createCommand()->setText($sql)->queryAll();
		if(!empty($list)){
			foreach($list as $i => $f){
				$top_members[$f['family_id']] = $i + 1;
			}
		}
		
		//计算家族族徽成员排行榜名次
		$sql = 'select family_id, count(uid) as members from web_family_member where family_id in ('.implode(',', $family_ids).') and medal_enable = 1  group by family_id order by members desc, family_id asc';
		$list = $this->family_db->createCommand()->setText($sql)->queryAll();
		if(!empty($list)){
			foreach($list as $i => $f){
				$top_medal[$f['family_id']] = $i + 1;
			}
		}
		
		//计算家族的家族主播等级排行榜名次
		$sql = 'select family_id, uid from web_family_member where family_id in ('.implode(',', $family_ids).') and family_dotey = 1';
		$doteys = $this->family_db->createCommand()->setText($sql)->queryAll();
		$dotey_uids = array_keys($service->buildDataByIndex($doteys, 'uid'));
		$sql = 'select uid, dotey_rank from web_user_consume_attribute where uid in ('.implode(',', $dotey_uids).')';
		$temp = $this->consume_db->createCommand()->setText($sql)->queryAll();
		$rank = array();
		foreach($temp as $r){
			$rank[$r['uid']] = $r['dotey_rank'];
		}
		foreach($doteys as $d){
			if(!isset($rank[$d['uid']]) || $rank[$d['uid']] <= 5){
				if(!isset($top_rank[0][$d['family_id']])) $top_rank[0][$d['family_id']] = array('family_id' => $d['family_id'], 'num' => 0);
				$top_rank[0][$d['family_id']]['num'] = $top_rank[0][$d['family_id']]['num'] + 1;
			}elseif($rank[$d['uid']] <= 10){
				if(!isset($top_rank[1][$d['family_id']])) $top_rank[1][$d['family_id']] = array('family_id' => $d['family_id'], 'num' => 0);
				$top_rank[1][$d['family_id']]['num'] = $top_rank[1][$d['family_id']]['num'] + 1;
			}else{
				if(!isset($top_rank[2][$d['family_id']])) $top_rank[2][$d['family_id']] = array('family_id' => $d['family_id'], 'num' => 0);
				$top_rank[2][$d['family_id']]['num'] = $top_rank[2][$d['family_id']]['num'] + 1;
			}
		}
		uasort($top_rank[0], array($this, 'doteyTopSort'));
		$i = 1;
		foreach($top_rank[0] as $id => $v){
			$top_rank1[$id] = $i++;
		}
		uasort($top_rank[1], array($this, 'doteyTopSort'));
		$i = 1;
		foreach($top_rank[1] as $id => $v){
			$top_rank2[$id] = $i++;
		}
		uasort($top_rank[2], array($this, 'doteyTopSort'));
		$i = 1;
		foreach($top_rank[2] as $id => $v){
			$top_rank3[$id] = $i++;
		}
		
		$top = array();
		foreach($family_ids as $id){
			$top = array(
				'dedication'=> isset($top_dedication[$id]) ? $top_dedication[$id] : '--',
				'members'	=> isset($top_members[$id]) ? $top_members[$id] : '--',
				'medal'		=> isset($top_medal[$id]) ? $top_medal[$id] : '--',
				'rank1'		=> isset($top_rank1[$id]) ? $top_rank1[$id] : '--',
				'rank2'		=> isset($top_rank2[$id]) ? $top_rank2[$id] : '--',
				'rank3'		=> isset($top_rank3[$id]) ? $top_rank3[$id] : '--',
			);
			$sql = "UPDATE web_family_extend SET top = '".json_encode($top)."' where family_id = ".$id;
			$this->family_db->createCommand()->setText($sql)->execute();
		}
	}
	
	protected function doteyTopSort($a, $b){
		if($a['num'] > $b['num']) return -1;
		elseif($a['num'] == $b['num'] && $a['family_id'] < $b['family_id']) return -1;
		else return 1; 
	}
	
	//修复用户数据库中昵称为空的现象，并清理redis
	public function actionBuding(){
		$lastId = 0;
		while($list = $this->user_db->createCommand()->setText("select uid,username from web_user_base where nickname='' and uid > $lastId order by uid asc limit 5000")->queryall()){
			foreach($list as $u){
				$lastId = $u['uid'];
				$sql = "update web_user_base set nickname = '".PipiCommon::truncate_utf8_string(str_replace('\\', "", $u['username']), 16)."' where uid = ".$u['uid'];
				echo $sql."\n";
				$this->user_db->createCommand()->setText($sql)->execute();
				echo 'uid_'.$u['username']. ':' . intval(Yii::app()->redis_user->delete('uid_'.$u['username']))."\n";
				echo 'user_info:'.$u['uid']. ':' . intval(Yii::app()->redis_userinfo->delete('user_info:'.$u['uid']))."\n";
			}
		}
	}
}