<?php

/**
 * 提供给快播_图丽的数据
 * the last known user to change this file in the repository  <$LastChangedBy: guo shaobo $>
 * @author guo shaobo <guoshaobo@pipi.cn>
 * @version $Id: TulicreateXmlCpmmand.php 894 2010-12-28 07:55:25Z guo shaobo  $
 * @package
 */

class TuliCreateXmlCommand extends PipiConsoleCommand {
	
	/**
	 * 生成对方需要xml数据(需求是1分钟更新一次, 鉴于压力, 可以5到10分钟一次)
	 */
	public function actionUpdateXml()
	{
		$time = time();
		$title = array(
				'site_name'		=> "皮皮乐天",
				'site_url'		=> 'http://co.pipi.cn/index.php?from=tuli',
				'update_time'	=> $time,
		);
// 		$hostname = Yii::app()->request->hostInfo;
		$doteyServ = new DoteyService();
		$userServ = new UserService();
		$userJsonServ = new UserJsonInfoService();
		$archiveServ = new ArchivesService();
		$userListService=new UserListService();
		$doteys = $doteyServ->getDoteysByCondition(array('status'=>1));
		$doteyIds = array_keys($doteys);
		$userExtends = $userServ->getUserExtendByUids($doteyIds);
		$archiveInfos = $archiveServ->getArchivesByUids($doteyIds);
		$archiveIds = array_keys($archiveInfos);
		$archiveInfos = $archiveServ->buildDataByIndex($archiveInfos,'uid');
// 		$nums = $archiveServ->getSessTotalSumByCondition($archiveIds);
		$record = $archiveServ->getArchivesByUids($doteyIds);
		$infos = $userJsonServ->getUserInfos($doteyIds, false);
		$data = array();
		foreach($doteyIds as $k=>$v){
			if(isset($infos[$v])){
				$_info = is_array($infos[$v]) ? $infos[$v] : json_decode($infos[$v], true);
				if($userServ->hasBit(intval($_info['ut']),USER_TYPE_DOTEY) && $_info['us']!=USER_STATUS_OFF){
					$_record = $record[$archiveInfos[$v]['archives_id']]['live_record'];
					$_tmp['user_id'] = $v;
					$_tmp['user_avatar'] = $userServ->getUserAvatar($v,'middle');
					$_tmp['user_nickname'] = $_info['nk'];
					$_tmp['user_level'] = (int) $_info['dk'];
					$_tmp['room_cover'] = $doteyServ->getDoteyUpload($v,'small');
					$room_online=$userListService->getUserList($archiveInfos[$v]['archives_id']);
					$_tmp['room_online'] = (int)isset($room_online['total'])?$room_online['total']:0;
					$_tmp['room_url'] = 'http://co.pipi.cn/'.$v . '?from=tuli';
					$_tmp['room_status'] = ($_record['status']==1) ? (int) $_record['status'] : 0; // 直播状态
					$_tmp['start_time'] = $_record['start_time'] > 0 ? (int) $_record['start_time'] : 0;
					$_tmp['tag'] = '';
					$_tmp['recommend'] = 0;
					$_tmp['room_cover_mobi'] = '';
					$data[$v] = $_tmp;
				}
			}
		}
		$this->createXml($title, $data);
	}
	
	protected function createXml($title = array(),$data = array())
	{
		$path = ROOT_PATH."images".DIR_SEP.'tuli'.DIR_SEP;
		$fileDir = $path .'tuli.xml';
		if(!is_dir($path)){
			$this->createFolder($path);
		}
		$xml = new XMLWriter();
		// 		$xml->openUri("php://output");
		// 		$xml->openMemory();
		$xml->openUri($fileDir);
		$xml->setIndentString('  ');
		$xml->setIndent(true);
	
		$xml->startDocument('1.0', 'utf-8');
		$xml->startElement('document');
		foreach($title as $k=>$v){
			$xml->startElement($k);
			$xml->writeCData($v);
			$xml->endElement();
		}
		$xml->startElement('items');
		if($data){
			foreach($data as $k=>$v){
				$xml->startElement('item');
				foreach($v as $t=>$n){
					$xml->startElement($t);
					$xml->writeCData($n);
					$xml->endElement();
				}
				$xml->endElement();
			}
		}
		$xml->endElement();
		$xml->endElement();
		$xml->endDocument();
		$xml->flush();
	}

	public function createFolder($path)
	{
		if (!is_dir($path))
		{
			@mkdir($path, 0755, true);
		}
	}
}