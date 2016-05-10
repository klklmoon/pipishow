<?php
class ArchivesCommand extends CConsoleCommand{
	
	/**
	 * @var ArchivesService 档期服务层
	 */
	public $archivesSer;
	
	public function init(){
		parent::init();
		$this->archivesSer = new ArchivesService();
	}
	
	/**
	 * 每隔10分钟去获取一次现在正在直播的档期的在线人数
	 * 	替换旧的任务脚本：http://show.pipi.cn/sessStat/stat
	 */
	public function actionSessStat(){
		$start_time = microtime(true);
		$archivesSer = new ArchivesService();
		$result = $archivesSer->searchLiveRecordByCondition(array('status'=>1),null,null,false);
		$list = $result['list'];
		if(!empty($list)){
			$aids = array();
			foreach($list as $v){
				if(isset($v['archives_id']) && $v['archives_id']){
					$data=array();
					$aid=$v['archives_id'];
					$aids[] = $aid;
				}
			}
				
			if ($aids){
				$infos = $archivesSer->getSessTotalSumByCondition($aids);
				if ($infos){
					foreach ($infos as $info){
						if(!empty($info) && !empty($info['total'])){
							if (isset($info['domain'])){
								unset($info['domain']);
							}
							$info['create_time']=time();
							$info['archives_id']=$info['archives_id'];
							$archivesSer->saveSessStat($info);
						}
					}
				}
			}
		}
		unset($list);
		
		$end_time = microtime(true);
		echo date("Y-m-d H:i:s").' '.__CLASS__.':'.__FUNCTION__.' 脚本运行'.round($end_time-$start_time, 4).'秒'."\n";
	}
	
}