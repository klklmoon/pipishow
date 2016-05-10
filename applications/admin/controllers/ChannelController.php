<?php
class ChannelController extends PipiAdminController {

	/**
	 * @var ChannelService 道具服务层
	 */
	public $channelSer;
	
	/**
	 * @var array 允许的操作
	 */
	public $allowOp = array('addChannel', 'addSubChannel', 'editSubChannel', 'delSubChannelTheme', 'delChannelTheme', 
		'editChannel', 'addChannelArea', 'getSubChannel', 'getChannelArea', 'delChannelAreaCity', 'updateSymbol',
		'delChannelAreaProvince', 'delChannelArea', 'addDoteySong', 'checkDoteyInfo','getDoteyList','delDoteyChannel','addAreaDotey');
	
	/**
	 * @var string 当前操作
	 */
	public $op;
	
	/**
	 * @var boolean 是否是Ajax请求
	 */
	public $isAjax;
	
	public $pageSize = 20;
	
	public $offset;
	
	/**
	 * @var int page lable
	 */
	public $p;
	
	public function init(){
		parent::init();
		$this->channelSer = new ChannelService();
		$this->op = Yii::app()->request->getParam('op');
		$this->isAjax = Yii::app()->request->isAjaxRequest;
		if(!($this->p = Yii::app()->request->getParam('page'))){
			$this->p = 1;
		}
		$this->offset = ($this->p -1)*$this->pageSize;
	}
	
	/**
	 * 主题频道管理 
	 * 	主题管理
	 */
	public function actionThemeChannel(){
		$allSubTheme = $this->channelSer->getAllChannel();
		$allParentTheme = $this->channelSer->getAllParentChannel();
		$this->render('theme_channel_list',array('allSubTheme'=>$allSubTheme,'allParentTheme'=>$allParentTheme));
	}
	
	/**
	 * 主题频道管理 
	 * 	创建主题
	 */
	public function actionCreateTheme(){
		//是否是删除父频道操作
		if($this->op == 'delChannelTheme' && in_array($this->op,$this->allowOp)){
			$this->delChannelThemeDo();
		}
		
		//是否是删除子频道操作
		if($this->op == 'delSubChannelTheme' && in_array($this->op,$this->allowOp)){
			$this->delSubChannelThemeDo();
		}
		
		//是否添加父频道操作
		$notices = array();
		if($this->op == 'addChannel' && in_array($this->op,$this->allowOp)){
			$notices = $this->addChannelDo();
		}
		
		//是否添加子频道
		if($this->op == 'addSubChannel' && in_array($this->op,$this->allowOp)){
			if(!$this->addSubChannelDo()){
				$notices = $this->channelSer->getNotice();
			}else{
				$this->redirect($this->createUrl('channel/themechannel'));
			}
		}
		
		//是否是修改父频道操作
		$isPChannel = false;
		$pcinfo = array();
		if($this->op == 'editChannel' && in_array($this->op,$this->allowOp)){
			$isPChannel = true;
			$channelId = Yii::app()->request->getParam('channelId');
			if($isPChannel && $channelId){
				$pcinfo = $this->channelSer->getAllParentChannel($channelId);
				$pcinfo = $pcinfo[$channelId];
			}else{
				exit('缺少参数');
			}
		}
		
		//编辑子频道 
		$subInfo = array();
		if($this->op == 'editSubChannel' && in_array($this->op,$this->allowOp)){
			$subChannelId = Yii::app()->request->getParam('subChannelId');
			$channelId = Yii::app()->request->getParam('channelId');
			if($subChannelId && $channelId){
				$subInfo = $this->channelSer->getChannelByCateId($channelId,$subChannelId);
			}
		}

		$pchannel = $this->formatParentChannel($this->channelSer->getAllParentChannel());
		if($this->isAjax){
			$this->renderPartial('theme_create_channel',array('pchannel'=>$pchannel,'notices'=>$notices,'subInfo'=>$subInfo,'pcinfo'=>$pcinfo,'ispc'=>$isPChannel));
		}else{
			$this->render('theme_create_channel',array('pchannel'=>$pchannel,'notices'=>$notices,'subInfo'=>$subInfo,'pcinfo'=>$pcinfo,'ispc'=>$isPChannel));
		}
	}
	
	/**
	 * 频道地区管理 
	 * 	地区管理
	 */
	public function actionChannelArea(){
		$this->assetsArea();
		$areaChannel = $this->formatAreaChannel($this->channelSer->getAllAreaChannel());
		$this->render('theme_channel_area',array('areaChannel'=>$areaChannel));
	}
	
	/**
	 * 频道地区管理 
	 * 	创建地区
	 */
	public function actionCreateChannelArea(){
		//删除频道地区所有关联的明细
		if ($this->op == 'delChannelArea' && in_array($this->op, $this->allowOp)){
			$this->delChannelAreaDo();
		}
		
		//删除频道地区相关省份明细
		if ($this->op == 'delChannelAreaProvince' && in_array($this->op, $this->allowOp)){
			$this->delChannelAreaProvinceDo();
		}
		
		//是否是删除频道地区关联的明细操作
		if ($this->op == 'delChannelAreaCity' && in_array($this->op, $this->allowOp)){
			$this->delChannelAreaCityDo();
		}
		
		//是否是获取子频道数据
		if ($this->op == 'getSubChannel' && in_array($this->op, $this->allowOp)){
			$this->getSubChannelDo();
		}
		
		//是否是添加频道地区动作
		$notices = array();
		if ($this->op == 'addChannelArea' && in_array($this->op, $this->allowOp)){
			$notices = $this->addChannelAreaDo();
		}
		
		//是否有编辑操作
		$subInfo = array();
		if ($this->op == 'getChannelArea' && in_array($this->op, $this->allowOp)){
			$subInfo = $this->getChannelAreaDo();
		}
		
		$this->assetsArea();
		$pchannel = $this->formatParentChannel($this->channelSer->getAllParentChannel(),false);
		
		if ($this->isAjax){
			exit($this->renderPartial('theme_create_channel_area',array('pchannel'=>$pchannel,'notices'=>$notices,'subInfo'=>$subInfo)));
		}else{
			$this->render('theme_create_channel_area',array('pchannel'=>$pchannel,'notices'=>$notices,'subInfo'=>$subInfo));
		}
	}
	
	/**
	 * 点唱专区管理
	 */
	public function actionDoteySong(){
		$condition = array('channel_name'=>CHANNEL_THEME,'sub_name'=>CHANNEL_THEME_SONG);
		if($sub_channel_id = Yii::app()->request->getParam('sub_channel_id')){
			$condition['sub_channel_id'] = $sub_channel_id;
		}
		
		$search = Yii::app()->request->getParam('search');
		if($search){
			$condition = array_merge($condition,$search);
		}
		
		$data = $this->channelSer->getChannelThemeByConditions($condition,$this->offset,$this->pageSize);
		$count = $data['count'];
		$list = $data['list'];
		
		$doteyInfos = array();
		$doteyConsumes = array();
		$allDoteyRanks = $this->getDoteyRank();
		if($list){
			$uids = array();
			foreach($list as $v){
				$uids[] = $v['uid'];
			}
			if ($uids){
				$userSer = new UserService();
				$doteyInfos = $userSer->getUserBasicByUids($uids);
				$consumeSer = new ConsumeService();
				$doteyConsumes = $consumeSer->getConsumesByUids($uids);
			}
		}
		
		$pager = new CPagination($count);
		$pager->pageSize = $this->pageSize;
		$this->render('theme_dotey_song',array('pager'=>$pager,'condition'=>$condition,'doteyInfos'=>$doteyInfos,'songList'=>$list,'doteyConsumes'=>$doteyConsumes,'allDoteyRanks'=>$allDoteyRanks));
	}
	
	/**
	 * 创建点唱专区 
	 */
	public function actionCreateDoteySong(){
		//根据条件获取主播列表
		if ($this->op == 'getDoteyList' && in_array($this->op, $this->allowOp)){
			$this->getDoteyListDo();
		}
		
		$notices = array();
		//添加主播唱区
		if ($this->op == 'addDoteySong' && in_array($this->op, $this->allowOp)){
			$notices = $this->addDoteySongDo();
		}
		
		//是否是删除动作
		if ($this->op == 'delDoteyChannel' && in_array($this->op, $this->allowOp)){
			$this->delDoteyChannel();
		}
		
		if ($this->isAjax){
			exit($this->renderPartial('theme_create_dotey_song'));
		}else{
			$this->render('theme_create_dotey_song',array('notices'=>$notices));
		}
	}
	
	/**
	 * 地区主播管理
	 */
	public function actionAreaDotey(){
		$this->assetsArea();
		$condition = array('channel_name'=>CHANNEL_AREA);
		$sub_channel_id = Yii::app()->request->getParam('sub_channel_id');
		if($sub_channel_id){
			$condition['sub_channel_id'] = $sub_channel_id;
		}
		
		$search = Yii::app()->request->getParam('search');
		if($search){
			$condition = array_merge($condition,$search);
		}
		
		$data = $this->channelSer->getChannelThemeByConditions($condition,$this->offset,$this->pageSize);
		$count = $data['count'];
		$list = $data['list'];

		$doteyInfos = array();
		$doteyConsumes = array();
		$allDoteyRanks = $this->getDoteyRank();
		if($list){
			$uids = array();
			foreach($list as $v){
				$uids[] = $v['uid'];
			}
			if ($uids){
				$userSer = new UserService();
				$doteyInfos = $userSer->getUserBasicByUids($uids);
				$consumeSer = new ConsumeService();
				$doteyConsumes = $consumeSer->getConsumesByUids($uids);
			}
		}
		
		$pager = new CPagination($count);
		$pager->pageSize = $this->pageSize;

		$this->render('theme_area_dotey',array('pager'=>$pager,'condition'=>$condition,'doteyInfos'=>$doteyInfos,'areaList'=>$list,'doteyConsumes'=>$doteyConsumes,'allDoteyRanks'=>$allDoteyRanks));
	}
	
	/**
	 * 创建地区主播
	 */
	public function actionCreateAreaDotey(){
		$this->assetsArea();
		//根据条件获取主播列表
		if ($this->op == 'getDoteyList' && in_array($this->op, $this->allowOp)){
			$this->getDoteyListDo(true);
		}
		
		//添加主播唱区
		$notices = array();
		if ($this->op == 'addAreaDotey' && in_array($this->op, $this->allowOp)){
			$notices = $this->addAreaDoteyDo();
		}
		
		//是否是删除动作
		if ($this->op == 'delDoteyChannel' && in_array($this->op, $this->allowOp)){
			$this->delDoteyChannel();
		}
		if($this->isAjax){
			exit($this->renderPartial('theme_create_area_dotey'));			
		}else{
			$this->render('theme_create_area_dotey',array('notices'=>$notices));
		}
	}
	
	/**
	 * 标志管理
	 */
	public function actionSymbolManage(){
		//更新标志管理
		if ($this->op == 'updateSymbol' && in_array($this->op, $this->allowOp)){
			$this->updateSymbol();
		}
		
		$webConfigSer = new WebConfigService();
		$keyInfo = $webConfigSer->getChannelSymbol();
		if(!$keyInfo['c_value']){
			$keyInfo['sing_area']['flag'] = 'sing_area';
			$keyInfo['sing_area']['desc'] = '';
			$keyInfo['sing_area']['pic'] = '';
			$keyInfo['sing_general']['flag'] = 'sing_general';
			$keyInfo['sing_general']['desc'] = '';
			$keyInfo['sing_general']['pic'] = '';
		}else{
			$keyInfo = $keyInfo['c_value'];
		}
		$adminUrl = $webConfigSer->getShowAdminUrl();
		$this->render('theme_symbol',array('info'=>$keyInfo,'adminUrl'=>$adminUrl));
	}
	
	/**
	 * ajax 获取子频道列表
	 */
	public function getSubChannelDo(){
		if (!$this->isAjax){
			exit('1');
		}	
		
		if (!$channelId = Yii::app()->request->getParam('channelId')){
			exit('2');
		}
		
		if(!$channelArea = $this->formatChannelArea($this->channelSer->getChannelByCateId($channelId))){
			exit('3');
		}
		
		exit(CHtml::checkBoxList('channelarea[area_channel_id]', '', $channelArea,array('separator'=>'&nbsp;&nbsp;&nbsp;&nbsp;','labelOptions'=>array('class'=>'checkbox inline'),)));
	}
	
	/**
	 * 获取频道地区信息
	 */
	public function getChannelAreaDo(){
		$subChannelId = Yii::app()->request->getParam('subChannelId');
		$provinceName = Yii::app()->request->getParam('provinceName');
		if (!$subChannelId || !$provinceName){
			exit('缺少参数');
		}
		
		if(!($info = $this->channelSer->getAllAreaChannel(array('area_channel_id'=>$subChannelId,'province'=>$provinceName)))){
			exit('获取信息失败！');
		}
		
		$detail = array();
		$detail['province'] = $provinceName;
		$detail['area_channel_id'] = $subChannelId;
		
		$this->formatChannelAreaDetail($info,$detail);
		return $detail;
	}
	
	/**
	 * 获取主播唱区频道信息
	 */
	public function getDoteySongChannel(){
		return $this->channelSer->getChannelIdByChannelName(array(CHANNEL_THEME,CHANNEL_THEME_SONG),'name');
	}
	
	/**
	 * 获取地区频道信息
	 * 
	 * @param unknown_type $sub_channel
	 * @return Ambigous <multitype:, mix, multitype:unknown Ambigous <multitype:, unknown> >
	 */
	public function getAreaChannel($sub_channel){
		$condition = array();
		if ($sub_channel){
			$area_channel = $this->channelSer->getChannelCateConfig(CHANNEL_AREA);
			if(in_array($sub_channel, $area_channel)){
				$condition = array(CHANNEL_AREA,$sub_channel);
			}
		}
		if ($condition){
			return $this->channelSer->getChannelIdByChannelName($condition,'name');
		}
		return array();
	}
	
	/**
	 * 获取主播等级
	 */
	public function getDoteyRank(){
		$consumeSer = new ConsumeService();
		$ranks = $consumeSer->getDoteyAllRank();
		$doteyRanks = array();
		if($ranks){
			foreach ($ranks as $rank){
				$doteyRanks[$rank['rank']] = $rank['name'];
			}
		}
		return $doteyRanks;
	}
	
	/**
	 * 根据条件获取主播列表
	 * 
	 * @param $isArea 是否是地区频道 
	 */
	public function getDoteyListDo($isArea = false){
		if(!$this->isAjax){
			exit('非法请求');
		}
		
		$dotey_name = Yii::app()->request->getParam('dotey_name');
		$dotey_rank = Yii::app()->request->getParam('dotey_rank');
		$start_time = Yii::app()->request->getParam('start_time');
		$dotey_area = Yii::app()->request->getParam('dotey_area');
		$province = Yii::app()->request->getParam('province');
		$city = Yii::app()->request->getParam('city');
		$doteySer = new DoteyService();
		
		if(empty($dotey_name) && ($dotey_rank < 0) && empty($start_time) && empty($dotey_area) && empty($province) && empty($city)){
			exit('缺少参数');
		}
		
		//主播集合
		$doteyUids= array();
		
		//所有地区分类列表
		$areaCatList = array();
		$channelId = '';
		if ($isArea){
			$info = array_keys($this->channelSer->getAllParentChannel('',CHANNEL_AREA));
			if($info){
				$channelId = array_shift($info);
				$subInfo = $this->channelSer->getChannelByCateId($channelId);
				if ($subInfo){
					foreach ($subInfo as $v){
						$areaCatList[$v['sub_channel_id']] = $v['sub_name'];
					}
				}
				
				if ($province && $city){
					//主播用户
					if($data = $doteySer->searchDoteyArea($province,$city)){
						$uids = array();
						$_dotey_area = array_keys($data);
						foreach($_dotey_area as $uid){
							if(!in_array($uid, $uids)){
								$uids[] = $uid;
							}
						}
				
						if ($doteyUids){
							foreach($uids as $uid){
								if(!key_exists($uid, $doteyUids)){
									unset($doteyUids[$uid]);
								}
							}
						}else{
							$doteyUids = array_flip($uids);
						}
					}
					//可选的地区分类
					$area = $this->channelSer->getAreaChannelGroups($channelId,$province,$city);
					$areaList = array();
					if($area){
						foreach ($area as $v){
							$areaList[$v['sub_channel_id']] = $v['sub_name'];
						}
					}
					if($areaList){
						$areaCatList = $areaList;
					}
				}
				
			}else{
				exit('没有匹配的数据');
			}
		}
		
		//用户名或ID模糊匹配
		if ($dotey_name){
			if($_dotey_name = array_keys($this->getDoteyInfo($dotey_name))){
				if ($doteyUids){
					foreach($_dotey_name as $uid){
						if(!key_exists($uid, $doteyUids)){
							unset($doteyUids[$uid]);
						}
					}
				}else{
					$doteyUids = array_flip($_dotey_name);
				}
			}else{
				exit('没有匹配的数据');
			}
		}
		//开播时间
		if($start_time){
			$condition = array();
			$condition['start_time'] = strtotime($start_time);
			$archivesSer = new ArchivesService();
			if($_start_time = array_keys($archivesSer->getLiveRecordsByFilter($condition))){
				if ($doteyUids){
					foreach($_start_time as $uid){
						if(!key_exists($uid, $doteyUids)){
							unset($doteyUids[$uid]);
						}
					}
				}else{
					$doteyUids = array_flip($_start_time);
				}
			}else{
				exit('没有匹配的数据');
			}
		}
		//主播等级
		if ($dotey_rank){
			$consumeSer = new ConsumeService();
			$result = $consumeSer->getConsumesByConditions(array('dotey_rank'=>$dotey_rank),NULL,NULL,FALSE);
			if($_dotey_rank = array_keys($result['list'])){
				if ($doteyUids && $_dotey_rank['list']){
					foreach($_dotey_rank['list'] as $uid){
						if(!key_exists($uid, $doteyUids)){
							unset($doteyUids[$uid]);
						}
					}
				}else{
					$doteyUids = array_flip($_dotey_rank);
				}
			}else{
				exit('没有匹配的数据');
			}
		}
		
		$doteyUids = array_flip($doteyUids);
		if($doteyUids){
			$doteyInfos = $this->formatCheckBoxDoteyInfos($this->getDoteyInfos($doteyUids));
			exit($this->renderPartial('_theme_dotey_list',array('doteyInfos'=>$doteyInfos,'areaCatList'=>$areaCatList,'channelId'=>$channelId)));
		}
		exit('没有匹配的数据');
	}
	
	/**
	 * 根据用户名或用户ID来获取主播信息
	 * @param unknown_type $doteyName
	 * @return Ambigous <multitype:, mixed>|multitype:
	 */
	public function getDoteyInfo($doteyName){
		$doteySer = new DoteyService();
		$userSer = new UserService();
		$condition = array();
		if(!is_numeric($doteyName)){
			$condition = array('username'=>$doteyName);
		}else{
			$condition = array('uid'=>(int)$doteyName);
		}
		
		if($userInfo = $doteySer->searchDoteyBase($condition,$this->offset,$this->pageSize,false)){
			if($userInfo['list']){
				return $userInfo['list'];
			}
		}
		return array();
	}
	
	/**
	 * 根据主播IDS来批量获取主播信息
	 * @param array $uids
	 * @return Ambigous <multitype:, mixed>|multitype:
	 */
	public function getDoteyInfos(Array $uids){
		$doteySer = new DoteyService();
		$userSer = new UserService();
		
		if ($uids){
			if($userInfos = $userSer->getUserBasicByUids($uids)){
				return $userInfos;
			}
		}
		return array();
	}
	
	/**
	 * 获取地区频道所有子类
	 * 
	 * @param string $formatType all|option
	 * @return string
	 */
	public function getAreaChannelSubCat($formatType = 'all'){
		$result = array();
		$areaChannel = array_shift($this->channelSer->getAllParentChannel(null,CHANNEL_AREA));
		if($areaChannel){
			$channelId = $areaChannel['channel_id'];
			$subInfos = $this->channelSer->getChannelByCateId($channelId);
			if ($subInfos){
				foreach($subInfos as $v){
					if ($formatType == 'all'){
						$result[$v['sub_channel_id']] = $v;
					}
					if ($formatType == 'option'){
						$result[$v['sub_channel_id']] = $v['sub_name'];
					}
				}
			}
		}
		return $result;
	}
	
	/**
	 * 添加父频道
	 */
	public function addChannelDo(){
		if (!$this->isAjax){
			//form表单提交处理
			if(!$channel = (Yii::app()->request->getParam('channel'))){
				exit(1);
			}else{
				if($this->channelSer->saveChannel($channel)){
					$this->redirect($this->createUrl('channel/themechannel'));
				}else{
					return $this->channelSer->getNotice();
				}
			}
		}
		
		//ajax提交
		$channel_name = Yii::app()->request->getParam('channel_name');
		$is_show_index = Yii::app()->request->getParam('is_show_index');
		$index_sort = Yii::app()->request->getParam('index_sort');
		if(empty($channel_name)){
			exit('2');
		}
		
		$addData = array();
		$addData['channel_name'] = $channel_name;
		$addData['is_show_index'] = $is_show_index;
		$addData['index_sort'] = $index_sort;
		
		if($insertId = $this->channelSer->saveChannel($addData)){
			exit('<option value="'.$insertId.'" selected="selected">'.$channel_name.'</option>');
		}else{
			exit('4');
		}
	}
	
	/**
	 * 添加子频道
	 */
	public function addSubChannelDo(){
		$sub = Yii::app()->request->getParam('channelsub');
		if($sub){
			if($insertId = $this->channelSer->saveSubChannel($sub)){
				return $insertId;
			}
		}
		return false;
	}
	
	/**
	 * 添加频道地区操作
	 */
	public function addChannelAreaDo(){
		if(!$channelArea = Yii::app()->request->getParam('channelarea')){
			return array('info'=>array('没有任何的提交数据，请确认'));
		}
		$channel = $channelArea['area_channel_id'];
		$area = array();
		foreach ($channelArea['city'] as $city){
			$area[$channelArea['province']][] = $city;
		}
		if(!$this->channelSer->saveAreaChannel($area, $channel)){
			return $this->channelSer->getNotice();
		}
		$this->redirect($this->createUrl('channel/channelArea'));
	}
	
	/**
	 * 添加或修改唱区频道
	 * @return multitype:multitype:string  |Ambigous <获取用户界面友好提提示, 用户界面友好提提示>
	 */
	public function addDoteySongDo(){
		if(!$doteys = Yii::app()->request->getParam('doteylist')){
			return array('info'=>array('没有任何的提交数据，请确认'));
		}
		list($channelId,$subChannelId) = $this->getDoteySongChannel();
		if($channelId && $subChannelId){
			if($this->addChannelDotey($channelId,$subChannelId,$doteys)){
				$this->redirect($this->createUrl('channel/doteysong'));
			}else{
				return $this->channelSer->getNotice();
			}
		}
	}
	
	/**
	 * 添加地区频道主播
	 * 
	 * @return multitype:multitype:string  |Ambigous <获取用户界面友好提提示, 用户界面友好提提示>
	 */
	public function addAreaDoteyDo(){
		$doteys = Yii::app()->request->getParam('doteylist');
		$subChannelIds = Yii::app()->request->getParam('areacat');
		$channelId = Yii::app()->request->getParam('channelId');
		if(!$doteys || !$subChannelIds || !$channelId){
			return array('info'=>array('数据有误，提交失败，请确认'));
		}
		
		if($channelId && $subChannelIds){
			$notices = array();
			foreach($subChannelIds as $subChannelId){
				if(!$this->addChannelDotey($channelId,$subChannelId,$doteys)){
					array_push($notices, $this->channelSer->getNotice());
				}
			}
			if(!$notices){
				$this->redirect($this->createUrl('channel/areadotey'));
			}else{
				return $notices;
			}
		}
	}
	
	/**
	 * 添加频道主播
	 * @param unknown_type $channelId
	 * @param unknown_type $subChannelId
	 * @param array $doteys
	 * @return Ambigous <number, mix, boolean>|boolean
	 */
	public function addChannelDotey($channelId,$subChannelId,Array $doteys){
		$doteySer = new DoteyService();
	
		$channel = array();
		$channel[$channelId]['sub_channel_id'] = $subChannelId;
		$channel[$channelId]['target_relation_id'] = 0;
	
		//叠加地区频道身份
		$_doteys = array();
		$doteyInfo = $doteySer->getDoteyInfoByUids($doteys);
		if($doteyInfo){
			foreach ($doteyInfo as $uid=>$uinfo){
				$_doteys[] = $uinfo['uid'];
				$_sub_channel = $doteySer->grantBit(intval($uinfo['sub_channel']), intval($subChannelId));
				if ($_sub_channel != intval($uinfo['sub_channel'])){
					$updata = array();
					$updata['uid'] = $uinfo['uid'];
					$updata['sub_channel'] = $_sub_channel;
					$doteySer->saveUserDoteyBase($updata);
				}
			}
		}
	
		if($_doteys){
			return $this->channelSer->saveDoteyChannel($_doteys, $channel);
		}
		return false;
	}
	
	/**
	 * 删除子频道操作
	 */
	public function delSubChannelThemeDo(){
		if(!$this->isAjax){
			exit('不合法请求');
		}
		
		$channelId = Yii::app()->request->getParam('channelId');
		$subChannelId = Yii::app()->request->getParam('subChannelId');
		if($channelId && $subChannelId){
			if($this->channelSer->delSubChannelByIds(array($subChannelId))){
				exit('1');
			}else{
				exit("删除失败");
			}
		}else{
			exit('缺少参数');
		}
	}
	
	/**
	 * 删除父频道操作
	 */
	public function delChannelThemeDo(){
		if(!$this->isAjax){
			exit('不合法请求');
		}
	
		$channelId = Yii::app()->request->getParam('channelId');
		if($channelId){
			if($this->channelSer->delChannelByIds(array($channelId))){
				exit('1');
			}else{
				exit("删除失败");
			}
		}else{
			exit('缺少参数');
		}
	}
	
	/**
	 * 删除市级频道地区
	 */
	public function delChannelAreaCityDo(){
		if(!$this->isAjax){
			exit('不合法请求');
		}
		
		$provinceName = Yii::app()->request->getParam('provinceName');
		$subChannelId = Yii::app()->request->getParam('subChannelId');
		$cityName = Yii::app()->request->getParam('cityName');
		
		if($provinceName && $subChannelId && $cityName){
			$condition = array('area_channel_id'=>$subChannelId,'province'=>$provinceName,'city'=>$cityName);
			if($this->channelSer->delChannelAreaRel($condition)){
				exit('1');
			}else{
				exit('删除失败');
			}
		}else{
			exit("缺少参数");
		}
	}
	
	/**
	 * 删除省级频道地区
	 */
	public function delChannelAreaProvinceDo(){
		if(!$this->isAjax){
			exit('不合法请求');
		}
	
		$provinceName = Yii::app()->request->getParam('provinceName');
		$subChannelId = Yii::app()->request->getParam('subChannelId');
	
		if($provinceName && $subChannelId){
			$condition = array('area_channel_id'=>$subChannelId,'province'=>$provinceName);
			if($this->channelSer->delChannelAreaRel($condition)){
				exit('1');
			}else{
				exit('删除失败');
			}
		}else{
			exit("缺少参数");
		}
	}
	
	/**
	 * 删除频道地区所有关系
	 */
	public function delChannelAreaDo(){
		if(!$this->isAjax){
			exit('不合法请求');
		}
	
		$subChannelId = Yii::app()->request->getParam('subChannelId');
	
		if($subChannelId){
			$condition = array('area_channel_id'=>$subChannelId);
			if($this->channelSer->delChannelAreaRel($condition)){
				exit('1');
			}else{
				exit('删除失败');
			}
		}else{
			exit("缺少参数");
		}
	}
	
	/**
	 * 删除频道与主播关系
	 */
	public function delDoteyChannel(){
		if(!$this->isAjax){
			exit('不合法请求');
		}
	
		$refInfos = Yii::app()->request->getParam('refInfos');
	
		if($refInfos){
			list($uid,$channel_id,$sub_channel_id,$target_relation_id) = explode('_', $refInfos);
			if($uid && $channel_id && $sub_channel_id){
				//取消用户的叠加身份sub_channel
				$doteySer = new DoteyService();
				$uinfo = $doteySer->getDoteyInfoByUid($uid);
				if($uinfo){
					$sub_channel = $doteySer->revokeBit(intval($uinfo['sub_channel']), intval($sub_channel_id));
					if($sub_channel != $uinfo['sub_channel']){
						$updata = array();
						$updata['uid'] =$uid;
						$updata['sub_channel'] = $sub_channel;
						$doteySer->saveUserDoteyBase($updata);
					}
				}
				
				if($this->channelSer->delDoteyChannelRel($uid,$channel_id,$sub_channel_id,$target_relation_id)){
					exit('1');
				}else{
					exit('删除失败');
				}
			}else{
				exit("缺少参数 无法操作");
			}
		}else{
			exit("缺少参数");
		}
	}
	
	/**
	 * 更新标志图片
	 */
	public function updateSymbol(){
		$sing_area = Yii::app()->request->getParam('sing_area');
		$sing_general = Yii::app()->request->getParam('sing_general');
		$areaPic = $this->uploadPic('sing_area','sing_area','channel','symbol');
		$sing_area['pic'] = $areaPic?$areaPic:'channel/symbol/sing_area.jpg';
		$generalPic = $this->uploadPic('sing_general','sing_general','channel','symbol');
		$sing_general['pic'] = $generalPic?$generalPic:'channel/symbol/sing_general.jpg';
		
		$webConf = new WebConfigService();
		$key = $webConf->getChannelSymbolKey();
		
		$data['c_type'] = 'array';
		$data['c_key'] = $key;
		$data['c_value'][$sing_area['flag']] = $sing_area;
		$data['c_value'][$sing_general['flag']] = $sing_general;
		$webConf->saveWebConfig($data);
		$this->redirect($this->createUrl('channel/symbolManage'));
	}
	
	/**
	 * 上传图片
	 * @param unknown_type $formName
	 * @param unknown_type $dir
	 * @param unknown_type $sub_dir
	 * @return string
	 */
	public function uploadPic($formName,$newName=null,$dir='channel',$sub_dir=null){
		$imgFiles = CUploadedFile::getInstancesByName($formName);
		if($imgFiles){
			$uploadDir = ROOT_PATH."images".DIR_SEP.$dir.DIR_SEP;
			$uploadDir = $sub_dir?$uploadDir.$sub_dir.DIR_SEP:$uploadDir;
			foreach ($imgFiles as $imgFile){
				$filename = $imgFile->getName();
				if($filename){
					$extName = $imgFile->getExtensionName();
					$newName = $newName?$newName.'.'.$extName:uniqid().'.'.$extName;
					
					if (!file_exists($uploadDir)){
						mkdir($uploadDir,0777,true);
					}
					$uploadfile = $uploadDir.$newName;
					if($imgFile->saveAs($uploadfile,true)){
						return $dir.DIR_SEP.($sub_dir?$sub_dir.DIR_SEP:'').$newName;
					}
				}
			}
		}
		return '';
	}
	
	/**
	 * 格式化父频道
	 * 
	 * @param array $pchannel
	 * @return array
	 */
	public function formatParentChannel(Array $pchannel,$isFilter = true){
		$result = array();
		$allow = $this->channelSer->getAllowChannelArea();
		if ($pchannel){
			foreach ($pchannel as $pid=>$channel){
				if($isFilter){
					$result[$pid] = $channel['channel_name'];
				}else{
					if(in_array($channel['channel_name'], $allow)){
						$result[$pid] = $channel['channel_name'];
					}
				}
			}
		}
		return $result;
	}
	
	/**
	 * 格式化子频道数据
	 * 
	 * @param array $subChannel
	 * @return multitype:unknown 
	 */
	public function formatSubChannel(Array $subChannel){
		$result = array();
		if ($subChannel){
			foreach ($subChannel as $pid=>$channel){
				foreach($channel as $schannel){
					$result[$schannel['sub_channel_id']] = $schannel['sub_name'];
				}
			}
		}
		return $result;
	}
	
	/**
	 * 格式化地区数据
	 * 
	 * @param array $data
	 */
	public function formatAreaChannel(Array $data){
		$areaChannel = array();
		if ($data) {
			foreach($data as $k=>$value){
				$areaChannel[$value['sub_channel_id']]['channel_id'] = $value['channel_id']; 
				$areaChannel[$value['sub_channel_id']]['sub_channel_id'] = $value['sub_channel_id']; 
				$areaChannel[$value['sub_channel_id']]['area_relation_id'] = $value['area_relation_id']; 
				$areaChannel[$value['sub_channel_id']]['sub_name'] = $value['sub_name']; 
				$areaChannel[$value['sub_channel_id']]['channel_name'] = $value['channel_name']; 
				$areaChannel[$value['sub_channel_id']]['area'][$value['province']][] = $value['city']; 
			}
		}
		return $areaChannel;
	}
	
	/**
	 * 格式化频道地区数据
	 * 
	 * @param array $data
	 * @return array  $areaChannel
	 */
	public function formatChannelArea(Array $data){
		$areaChannel = array();
		if ($data) {
			foreach($data as $k=>$value){
				$areaChannel[$value['sub_channel_id']] = $value['sub_name'];
			}
		}
		return $areaChannel;
	}
	
	
	/**
	 * 组装频道地区详情
	 * 
	 * @param array $data
	 * @param array $detail
	 * @return multitype:
	 */
	public function formatChannelAreaDetail(Array $data,Array &$detail){
		if ($data){
			$city = array();
			foreach ($data as $v){
				if(!isset($detail['sub_name'])){
					$detail['sub_name'] = $v['sub_name'];
				}
				if(!isset($detail['channel_name'])){
					$detail['channel_name'] = $v['channel_name'];
				}
				if(!isset($detail['channel_id'])){
					$detail['channel_id'] = $v['channel_id'];
					$detail['sub_channel_list'] = $this->formatChannelArea($this->channelSer->getChannelByCateId($v['channel_id']));
				}
				$city[] = $v['city'];
			}
			$detail['city'] = json_encode($city);
		}
	}
	
	/**
	 * 格式化出Checkbox专用的信息
	 * 
	 * @param array $doteyInfos
	 * @return multitype:string 
	 */
	public function formatCheckBoxDoteyInfos(Array $doteyInfos){
		$formatInfo = array();
		foreach($doteyInfos as $uid=>$info){
			$formatInfo[$uid] = $info['username'].'('.$info['realname'].')'.'('.$info['nickname'].')';
		}
		return $formatInfo;
	}
	/**
	 * AJAX 检验主播信息的合法性
	 */
	public function checkDoteyInfo(){
		if (!$this->isAjax){
			exit('不合法请求');
		}
	
		$doteyName = Yii::app()->request->getParam('dotey_name');
		if(empty($doteyName)){
			exit('请输入主播信息后进行校验 ');
		}
	
		$doteySer = new DoteyService();
		$userSer = new UserService();
		if(!is_numeric($doteyName)){
			if(!($userInfo = $userSer->getVadidatorUser($doteyName,0))){
				exit('不合法用户，请重新输入');
			}
			$uid = $userInfo['uid'];
		}else{
			$uid = (int)$doteyName;
		}
	
		if ($uid){
			if(!($doteyInfo = $doteySer->getDoteyInfoByUid($uid))){
				exit('该用户不是主播，请确认');
			}else{
				if(!isset($userInfo)){
					if(!($userInfo = $userSer->getUserBasicByUids(array($uid)))){
						exit('不合法用户，请重新输入');
					}else{
						$userInfo = $userInfo[$uid];
					}
				}
	
				exit('1'.'#xx#'.$userInfo['uid'].'#xx#'.$userInfo['username']);
			}
				
		}else{
			exit('不合法用户，请重新输入');
		}
	
	}
}
