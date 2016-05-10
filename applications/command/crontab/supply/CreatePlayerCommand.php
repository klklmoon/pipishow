<?php
/**
 * 提供给www.pipi.cn的数据
 * the last known user to change this file in the repository  <$LastChangedBy: guo shaobo $>
 * @author guo shaobo <guoshaobo@pipi.cn>
 * @version $Id: CreatePlayerCommand.php 894 2010-12-28 07:55:25Z guo shaobo  $ 
 * @package 
 */
class CreatePlayerCommand extends PipiConsoleCommand {
	/**
	 * @var CDbConnection
	 */
	public $archives_db;
	public $user_db;
	
	/**
	 * @var ArchivesService 档期服务层
	 */
	public $archivesService;
	
	public $userService;
	
	public function beforeAction($action,$params){
		parent::beforeAction($action, $params);
		$this->archives_db=Yii::app()->db_archives;
		$this->user_db = Yii::app()->db_user;
		$this->archivesService = new ArchivesService();
		$this->userService = new UserService();
		$path = IMAGES_PATH . 'supply'.DIR_SEP.'create_js'.DIR_SEP;
		if(!is_dir($path)){
			$this->createFolder($path);
		}
		return true;
	}
	
	/**
	 * pipi主页的数据提供
	 * 推荐直播 + 今日魅力榜
	 */
	public function actionGetDoteyDayTop(){
		// 魅力榜只取4个
		$charmList = $this->userService->getUserCharmRank('today',0);
		// 获取正在直播的主播, 3条数据
		$liveRes = $this->archivesService->getLivingArchives(0,true,false);
		if($liveRes['living']){
			$living_num = count($liveRes['living']);
			$living = $liveRes['living'];
			
			if($living_num >= 3){
				$keys = array_rand($living,3);
				$_tmp = array();
				foreach($keys as $v){
					$_tmp[] = $living[$v]; 
				}
				$living = $_tmp;
				$will = array();
			}else{
				$willRes = $this->archivesService->getWillLiveArchives(0,true,false);
				if(isset($willRes['wait']) && (count($willRes['wait'])>0) ){
					if(count($willRes['wait']) > (3 - $living_num)){
						$will = array_slice($willRes['wait'], 0, (3 - $living_num));
					}else{
						$will = $willRes['wait'];
					}
				}else{
					$will = array();
				}
			}
			$living = array_merge($living,$will);
		}else{
			$living_num = 0;
			$liveRes = $this->archivesService->getWillLiveArchives(0,true,false);
			$living = array_slice($liveRes['wait'],0,3);
		}
		$jsonStr = 'pipi_transfer={dotey_str:[';
		$nums = count($living) - 1;
		$url = Yii::app()->params['images_server']['cdn_open'] ? Yii::app()->params['images_server']['cdn_url'] : Yii::app()->params['images_server']['url'];
		$http = trim($url,DIR_SEP);
		foreach($living as $k=>$v){
			// 正在直播
			$_status = ($k + 1) <= $living_num ? '1' : '0';
			$ptitle = "{ptitle:'".$v['title']."',";
			$title = "title:'".$v['sub_title']."',";
			$status = "status:'".$_status."',";
			$pic =  preg_match('/http/is', $v['display_small']) ? $v['display_small'] : $http . $v['display_small'];
			$big_pic ="big_pic:'".$pic."',"; 
			$act_play_url = "act_play_url:'http://show.pipi.cn/".$v['uid']."'}";
			$_tmp = $k == $nums ? '' : ',';
			$jsonStr .= $ptitle . $title . $status . $big_pic . $act_play_url . $_tmp;
		}
		$jsonStr .= '],charm_str:[';
		$nums = count($charmList) - 1;
		foreach($charmList as $k=>$v){
			// 今日魅力榜
			$nickname = "{nickname:'".$v['d_nickname']."',";
			$rank = "rank:'".$v['d_rank']."',";
			$count = "count:'".$v['charm']."',";
			$act_play_url = "act_play_url:'http://show.pipi.cn/".$v['d_uid']."'}";
			$_tmp = $k == $nums ? '' : ',';
			$jsonStr .= $nickname . $rank . $count . $act_play_url . $_tmp;
		}
		$jsonStr .= ']};';
		
// 		print_r($living);
// 		print_r($charmList);
		$filePath = IMAGES_PATH . 'supply'.DIR_SEP.'create_js'.DIR_SEP.'transfer.js'; 
		file_put_contents($filePath, $jsonStr);
// 		echo $jsonStr;
		exit;
	}
	
	/**
	 * 本周魅力榜前十位
	 */ 
	public function actionGetDoteyWeekTop()
	{
		$charmList = $this->userService->getUserCharmRank('week',0);
		foreach($charmList as $k=>$v){
			$charm_str[] = "'http://show.pipi.cn/" . $v['d_uid'] . "'";
		}
		$Topcharm_str='live_arr=[';
		if(!empty($charm_str)){
			$Topcharm_str.=implode(',',$charm_str);
		}
		$Topcharm_str.=']';
		$filePath = IMAGES_PATH . 'supply'.DIR_SEP.'create_js'.DIR_SEP.'live_recomm.js';
		file_put_contents($filePath, $Topcharm_str);
// 		echo $Topcharm_str;
		exit;
	}
	
	/**
	 * 对外支持, 正在直播的主播列表
	 */ 
	public function actionIndex()
	{	
		$liveRes = $this->archivesService->getLivingArchives(0,true,false);
		$living = $liveRes['living'];
		$living = $this->userService->buildDataByIndex($living, 'uid');
		$doteyIds = array_keys($living);
		$doteyInfos = $this->userService->getUserExtendByUids($doteyIds);
// 		print_r($living);
// 		exit;
		$userJsonService = new UserJsonInfoService();
		$js_str='pipi_show=[';
		foreach($living as $k=>$v){
			$archiveIds[] = $v['archives_id'];
			// 获取主播信息
			$_uinfo = $userJsonService->getUserInfo($v['uid'],false);
			// 获取主播扩展信息
			$living[$k]['doteyInfo'] = $_doteyInfo = $doteyInfos[$k];
			
			// @todo 获取主播的今日fans榜
			$living[$k]['dayFans'] = $dayFans = $this->archivesService->getArchivesRelationData($v['archives_id'],'archives_dedication');
			$dfan_str = '[ ';
			foreach($dayFans as $k=>$v){
				$dfan_str.="{";
				$dfan_str.="name:'".addslashes($v['nickname'])."',";
				$dfan_str.="score:'".$v['dedication']."'";
				$dfan_str.="},";
			}
			$dfan_str = substr($dfan_str,0,strlen($dfan_str)-1);
			$dfan_str .= ']';
			
			// @todo 获取主播的本周fans榜
			$living[$k]['weekFans'] = $weekFans = $this->archivesService->getArchivesRelationData($v['archives_id'],'week_dedication');
			$wfan_str = '[';
			foreach($weekFans as $k=>$v){
				$wfan_str.="{";
				$wfan_str.="name:'".addslashes($v['nickname'])."',";
				$wfan_str.="score:'".$v['dedication']."'";
				$wfan_str.="},";
			}
			$wfan_str = substr($wfan_str,0,strlen($wfan_str)-1);
			$wfan_str .= '],';
			
			
			$js_str.="{";
   			$js_str.="act_name:'".$v['sub_title']."',";
   			$js_str.="info:{";
   			$js_str.='act_url:"http://show.pipi.cn/'.$k.'",';
   			$js_str.='act_play_url:"http://show.pipi.cn/'.$k.'",';
   			$js_str.='Anchorwoman_pic:"'.$v['display_small'].'",';
   			$js_str.="Anchorwoman_nick:'".$_uinfo['nk']."',";
   			$js_str.="total_fans:'100',";
   			$js_str.="title:'".$v['title']."',";
   			$js_str.="level:'".$_uinfo['rk']."',";
   			$js_str.="age:'".($_doteyInfo['birthday'] > 0 ? (date('Y',time())-date('Y',$_doteyInfo['birthday'])) : 0)."',";
   			$js_str.="nativeplace:'".$_doteyInfo['province']." ".$_doteyInfo['city']."',";
   			$js_str.="career:'".addslashes($_doteyInfo['profession'])."',";
   			$js_str.="desc:'".addslashes(preg_replace("/\s+/", "",$_doteyInfo['description']))."',";
   			$js_str.="status:'1',";
   			$js_str.="start_time:'".date('Y-m-d H:i:s',$v['live_time'])."',";
   			$js_str.="recent_playInfos:[],";
   			$js_str.="top_fans:$dfan_str";
   			$js_str.="total_top_fans:$wfan_str";
   			$js_str.="}";
   			$js_str.="},";
		}
		$js_str=substr($js_str,0,strlen($js_str)-1);
		$js_str.='];';
		
		$filePath = IMAGES_PATH . 'supply'.DIR_SEP.'create_js'.DIR_SEP.'play_recommend.js';
		file_put_contents($filePath, $js_str);
// 		echo $Topcharm_str;
		exit;
	}
	
	/**
	 * 各种top榜单
	 */ 
	public function actionTop()
	{
		// 本周fans榜
		$weekFans = $this->userService->getUserRichRank('week');
		$fan_str = '[';
		foreach($weekFans as $k=>$v){
			$fan_str.="{";
			$fan_str.="name:'".$v['nickname']."',";
			$fan_str.="score:'".$v['dedication']."'";
			$fan_str.="},";
		}
		$fan_str=substr($fan_str,0,strlen($fan_str)-1);
		$fan_str.=']';
		
		// 超级fans榜
		$superFans = $this->userService->getUserRichRank('super');
		$topfan_str='[';
		foreach($superFans as $k=>$v){
			$topfan_str.="{";
			$topfan_str.="name:'".$v['nickname']."',";
			$topfan_str.="score:'".$v['dedication']."'";
			$topfan_str.="},";
		}
		$topfan_str=substr($topfan_str,0,strlen($topfan_str)-1);
	   	$topfan_str.=']';
	   	
		// 周魅力榜
		$weekCharm = $this->userService->getUserCharmRank('week',0);
		$charm_str='[';
		foreach($weekCharm as $k=>$v){
			$charm_str.="{";
			$charm_str.="name:'".$v['d_nickname']."',";
			$charm_str.="score:'".$v['charm']."'";
			$charm_str.="},";
		}
		$charm_str=substr($charm_str,0,strlen($charm_str)-1);
		$charm_str.=']';
		// 总魅力榜
		$superCharm = $this->userService->getUserCharmRank('super',0);
		$Topcharm_str='[';
		foreach($superCharm as $k=>$v){
			$Topcharm_str.="{";
			$Topcharm_str.="name:'".addslashes($v['d_nickname'])."',";
			$Topcharm_str.="score:'".$v['charm']."'";
			$Topcharm_str.="},";
		}
		$Topcharm_str=substr($Topcharm_str,0,strlen($Topcharm_str)-1);
		$Topcharm_str.=']';
		
		$js_str='pipi_show_list={';
		$js_str.="url:'http://show.pipi.cn/?ml',";
		$js_str.="pic:'http://long.pipi.cn/mini4/images/mm-cover.jpg',";
		$js_str.="top_fans:{$fan_str},";
		$js_str.="total_top_fans:{$topfan_str},";
		$js_str.="top_charm:{$charm_str},";
		$js_str.="total_top_charm:{$Topcharm_str}";
		$js_str.='};';
		
		$filePath = IMAGES_PATH . 'supply'.DIR_SEP.'create_js'.DIR_SEP.'top.js';
		file_put_contents($filePath, $js_str);
// 		echo $js_str;
		exit;
	}
	
	public function createFolder($path)
	{
		if (!is_dir($path))
		{
			@mkdir($path, 0755, true);
		}
	}
	
}