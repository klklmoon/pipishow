<?php
class OperateCommand extends CConsoleCommand{
	const SHOWSTAT = '/webservice/showstat/';
	/**
	 * @var ArchivesService 档期服务层
	 */
	public $operateSer;
	
	public function init(){
		parent::init();
		$this->operateSer = new OperateService();
	}
	
	/**
	 * 直播在线人数统计 每半小时跑一次
	 */
	public function actionShowstatOnline(){
		$start_time = microtime(true);
		
		$filePath = self::SHOWSTAT . "online/";
		$filePathBackup = self::SHOWSTAT . "online_backup/";
		if (!is_dir($filePath))
			mkdir($filePath, 0755,true);
		if (!is_dir($filePathBackup))
			mkdir($filePathBackup, 0755,true);
		
		$type = Yii::app()->request->getParam('type','online');
		if ($type == 'online_backup') {
			$pathFp = opendir ($filePathBackup);
			if ($pathFp) {
				$fileName = readdir($pathFp);
				while(false !== $fileName) {
					if (preg_match ('/^(\d{8})(\d{4})\-/i',$fileName,$fileArr)){
						$filePathBackupDate = $filePathBackup.$fileArr[1]."/";
						if (!is_dir ($filePathBackupDate))
							mkdir ($filePathBackupDate,0755,true);
						rename($filePathBackup.$fileName,$filePathBackupDate.$fileName );
						continue;
					}
				}
				closedir ($pathFp);
				unset ($statNum);
			} else {
				echo $pathFp;
				exit ( "'$filePathBackup' is not a right directory! \r\n" );
			}
			return;
		}
		
		//统计直播在线人数
		$arr = array();
		$pathFp = opendir($filePath);
		if ($pathFp) {
			while (false !== ($fileName = readdir($pathFp))) {
				if (preg_match('/^(\d{8})(\d{4})\-([a-z]{2,3})\-(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})\.txt$/i', $fileName, $fileArr)) {
					$filePathBackupDate = $filePathBackup . $fileArr[1] . "/";
					if (!is_dir($filePathBackupDate))
						mkdir($filePathBackupDate, 0755,TRUE);
					if (file_exists($filePathBackupDate . $fileName)) {
						$randNum = rand(1,100);
						rename($filePath . $fileName, $filePathBackupDate . $fileName . " - $randNum");
						continue;
					} else {
						$statNum = trim(file_get_contents($filePath . $fileName));
						$statNum = (int) $statNum;
						$time_name = $fileArr[1] . $fileArr[2];
						$arr[$time_name][$fileArr[3]][$fileArr[4]] = $statNum;
						rename($filePath . $fileName, $filePathBackupDate . $fileName);
					}
				}
			}
			closedir($pathFp);
			unset($statNum);
			
			if (empty($arr)) {
				exit("没有文件需要统计! $filePath \r\n");
			}
		} else {
			echo $pathFp;
			exit("'$filePath' is not a right directory! \r\n");
		}
		
		// 将数据分类及计算,放入一个新数组
		$dataArrs = array();
		foreach ($arr as $date => $arrTmp1) {
			$currTime = strtotime(date('Y-m-d H:i:s'));
			// 数据单个数组,初始化
			$dataArr = array(
				'time'			=>	$date,
				'total_num'		=>	0,
				'tel_num'		=>	0,
				'cnc_num'		=>	0,
				'yd_num'		=>	0,
				'edu_num'		=>	0,
				'create_time'	=>	$currTime,
				'update_time'	=>	$currTime
			);
				
			foreach ($arrTmp1 as $cate => $arrTmp2) {
				$cateField = $cate . '_num';
				foreach ($arrTmp2 as $ip => $statNum) {
					$dataArr[$cateField] += $statNum;// 某分类在线人数相加
					$dataArr['total_num'] += $statNum;// 总在线人数相加
				}
			}
			$dataArrs[] = $dataArr;// 放入大数组,后面统一处理
		}
		if (!empty($dataArrs[0]))
			$this->showstatOnlineInsert($dataArrs);
		$end_time = microtime(true);
		echo date("Y-m-d H:i:s").' '.__CLASS__.':'.__FUNCTION__.' 脚本运行'.round($end_time-$start_time, 4).'秒'."\n";
	}
	
	/**
	 * 统计直播在线人数 -- 统一计算
	 * @param array $dataArrs 数据大数组
	 */
	public function showstatOnlineInsert($dataArrs) {
		foreach ($dataArrs as $key => $dataArr) {
			$this->operateSer->saveShowStat($dataArr);
		}
	}
}