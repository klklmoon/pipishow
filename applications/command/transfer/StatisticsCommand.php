<?php
class StatisticsCommand extends CConsoleCommand {
	/**
	 * @var CDbConnection 老版乐天读库操作
	 */
	protected $show_db;
	/**
	 * @var CDbConnection 新版消费库操作
	 */
	protected $consume_db;
	/**
	 * @var CDbConnection 新版消费库记录操作
	 */
	protected $consume_records_db;
	/**
	 * @var CDbConnection 新版档期库操作
	 */
	protected $archives_db;
	/**
	 * @var int 分页循环数
	 */
	protected $limit = 2000;
	
	public function beforeAction($action,$params){
		$this->show_db = Yii::app()->db_read_pipishow;
		$this->consume_db = Yii::app()->db_consume;
		$this->consume_records_db =  Yii::app()->db_consume_records;
		$this->archives_db = Yii::app()->db_archives;
		return true;
	}
	
	//迁移消费星级数据
	public function actionStars(){
		$showDB = $this->show_db->createCommand();
		$consumeDB = $this->consume_db->createCommand();
		$recordDB = $this->consume_records_db->createCommand();
		
		$ranks = $showDB->from('web_stars_rank')->queryAll();
		$showDB->reset();
		//$consumeDB->truncateTable('web_stars_rank'); //权限不足
		$consumeDB->setText('delete from web_stars_rank;ALTER TABLE `web_stars_rank` AUTO_INCREMENT=1;');
		$consumeDB->execute();
		foreach($ranks as $k => &$v){
			$v['pipiegg'] = $v['charm_num'];
			unset($v['charm_num']);
		}
		$this->batchInsert($consumeDB, 'web_stars_rank', $ranks);
		echo 'web_stars_rank表插入完成'."\n";
		
		$maxId = $showDB->select('max(id)')->from('web_stars_record')->queryScalar();
		$showDB->reset();
		$recordDB->setText('delete from web_stars_record;ALTER TABLE `web_stars_record` AUTO_INCREMENT=1;');
		$recordDB->execute();
		$id = 0;
		$list = array();
		while($id < $maxId){
			$list = $showDB->from('web_stars_record')->where('id > '.$id.' and id <= '.($id+$this->limit))->order('id ASC')->queryAll();
			echo $showDB->getText()."\n";
			$showDB->reset();
			$id += $this->limit;
			if(empty($list)) continue;
			
			foreach($list as $k => &$v){
				$v['record_id']= $v['id'];
				$v['stars_id'] = $v['stars'];
				unset($v['id']);
				unset($v['stars']);
			}
			$this->batchInsert($recordDB, 'web_stars_record', $list);
			//echo $recordDB->getText()."\n";
		}
		echo 'web_stars_record表插入完成'."\n";
	}
	
	//迁移主播时长的统计和记录数据
	public function actionSess(){
		$showDB = $this->show_db->createCommand();
		$archivesDB = $this->archives_db->createCommand();
		
		$temp = $archivesDB->select('uid, archives_id')->from('web_archives')->queryAll();
		$archivesDB->reset();
		$archives = $this->getIdsFromArray($temp, 'archives_id', 'uid');
		
		$maxId = $showDB->select('max(sess_stat_id)')->from('web_sess_stat')->queryScalar();
		$showDB->reset();
		$archivesDB->setText('delete from web_sess_stat;ALTER TABLE `web_sess_stat` AUTO_INCREMENT=1;');
		$archivesDB->execute();
		$id = 0;
		$list = array();
		while($id < $maxId){
			$list = $showDB->from('web_sess_stat')->where('sess_stat_id > '.$id.' and sess_stat_id <= '.($id+$this->limit))->order('sess_stat_id ASC')->queryAll();
			echo $showDB->getText()."\n";
			$showDB->reset();
			$id += $this->limit;
			if(empty($list)) continue;
			
			$o_archives_ids = $this->getIdsFromArray($list, 'archives_id');
			$o_archives = $showDB->select('archives_id, uid')->from('web_archives')->where('archives_id in ('.implode(',', $o_archives_ids).')')->queryAll();
			$showDB->reset();
			$o_archives_uid = $this->getIdsFromArray($o_archives, 'uid', 'archives_id');
			$bad = 0;
			foreach($list as $k => &$v){
				if(!isset($o_archives_uid[$v['archives_id']]) || !isset($archives[$o_archives_uid[$v['archives_id']]])){
					$bad++;
					unset($list[$k]);
					continue;
				}
				$v['archives_id']= $archives[$o_archives_uid[$v['archives_id']]];
			}
			$this->batchInsert($archivesDB, 'web_sess_stat', $list);
		}
		echo 'web_sess_stat表插入完成，过滤掉了原始表中无法迁移的'.$bad.'条坏数据'."\n";
		
		$maxId = $showDB->select('max(archives_id) as max')->from('web_sess_total')->queryScalar();
		$showDB->reset();
		$archivesDB->setText('delete from web_sess_total;ALTER TABLE `web_sess_total` AUTO_INCREMENT=1;');
		$archivesDB->execute();
		$id = $maxId + 1;
		$list = $in = array();
		$bad = $in_bad = 0;
		while($id > 0){
			$list = $showDB->from('web_sess_total')->where('archives_id >= '.($id - $this->limit).' and archives_id < '.$id)->order('archives_id DESC')->queryAll();
			echo $showDB->getText()."\n";
			$showDB->reset();
			$id -= $this->limit;
			if(empty($list)) continue;
			
			$o_archives_ids = $this->getIdsFromArray($list, 'archives_id');
			$o_archives = $showDB->select('archives_id, uid')->from('web_archives')->where('archives_id in ('.implode(',', $o_archives_ids).')')->queryAll();
			$showDB->reset();
			$o_archives_uid = $this->getIdsFromArray($o_archives, 'uid', 'archives_id');
			
			foreach($list as $k => &$v){
				if(!isset($o_archives_uid[$v['archives_id']]) || !isset($archives[$o_archives_uid[$v['archives_id']]])){
					$bad++;
					unset($list[$k]);
					continue;
				}
				$v['archives_id'] = $archives[$o_archives_uid[$v['archives_id']]];
				if(in_array($v['archives_id'], $in)){
					$in_bad++;
					unset($list[$k]);
					continue;
				}
				$in[] = $v['archives_id'];
				$v['domain'] = 'pipi.cn';
				unset($v['room_num']);
			}
			$this->batchInsert($archivesDB, 'web_sess_total', $list);
		}
		echo 'web_sess_total表插入完成，过滤掉了原始表中无法迁移的'.$bad.'条坏数据，包含有'.$in_bad.'条档期重复的数据'."\n";
		echo '之前的档期号同一个主播操作开始、结束会产生很多档期，现在只有一个，所以有'.$in_bad.'条档期重复数据，解决办法历史数据只取最后一条'."\n";
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
		return empty($index) ? array_unique($ids) : $ids;
	}
}