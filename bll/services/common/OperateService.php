<?php
define('CATEGORY_COMMON',1);
define('CATEGORY_INDEX',2);
define('CATEGORY_CHANNEL',3);
define('CATEGORY_INDEX_STARCOLLEGE',1);#明星主播版块
define('CATEGORY_INDEX_SHOWCASE',2);#首页厨窗
define('CATEGORY_INDEX_ACTIVITYRECOMMAND',3);#活动推荐
define('CATEGORY_INDEX_COLUMNSRECOMMAND',4);#新秀主播版块
define('CATEGORY_INDEX_LIVESRECOMMAND',5);#直播强推
define('CATEGORY_INDEX_TODAYRECOMMAND',6);#今日推荐
define('CATEGORY_INDEX_BANNER',7);
define('CATEGORY_INDEX_NEWSNOTICE',8);#首页公告
define('CATEGORY_INDEX_DOTEY_RECOMMAND',9);#首页轮播 本站明星
define('CATEGORY_INDEX_NEWDOTEY',10);#最新加入版块
define('CATEGORY_CHANNEL_SONG_CAROUSEL',1);
define('CATEGORY_CHANNEL_SONG_NOTICE',2);
define('CATEGORY_COMMON_TOPBANNER',1);
define('CATEGORY_COMMON_NAVIGATION',2);
define('CATEGORY_COMMON_VIDEO',3);
define('CATEGORY_COMMON_LIVE',4);

define('KEFU_QQ',1);
define('KEFU_QQ_WORK',1);
define('KEFU_QQ_RECHARGE',2);
define('KEFU_QQ_DOTEY',3);
define('KEFU_QQ_PROXY_RECRUIT',4);
define('KEFU_QQ_TEC_SUPPORT',5);
define('KEFU_QQ_FAMILY',6);
define('KEFU_QQ_SUGGEST',7);
define('KEFU_TEL',2);

define('SUGGEST_TYPE_INDEX',0);
define('SUGGEST_TYPE_LIVEROOM',1);
define('SUGGEST_TYPE_GAME',2);
define('SUGGEST_TYPE_OTHER',3);
define('SUGGEST_TYPE_COMPLAIN',4);
define('SUGGEST_TYPE_PHONE',5);      //手机端建议
define('SUGGEST_HANDLER_NO',0);
define('SUGGEST_HANDLER_YES',1);

define('INDEX_RIGHT_DATA_TYPE_ROOKIEDOTEY',0);#新秀主播版块
define('INDEX_RIGHT_DATA_TYPE_NEWDOTEY',1);#最近加入主播版块
define('INDEX_RIGHT_DATA_TYPE_STARDOTEY',2);#明星主播版块
/**
 * 运营服务层 什么公告 什么链接
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: OperateService.php 17568 2014-01-16 06:30:59Z hexin $ 
 * @package
 */
class OperateService extends PipiService {
	/**
	 * 
	 * @var OtherRedisModel
	 */
	private static $cacheRedisModel = null;
	
	/**
	 * 存储运营数据
	 * @param arary $operate
	 * @return boolean
	 */
	public function saveOperate(array $operate){
		if(!isset($operate['category']) || !isset($operate['sub_category']) || !isset($operate['subject'])){
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		}
		if(isset($operate['content'])){
			if(!is_array($operate['content']))
				return $this->setError(Yii::t('common','Parameter is empty'),false);
			else 
				$operate['content'] = serialize($operate['content']);
		}
		$operateModel = new OperateModel();
		if(isset($operate['operate_id'])){
			$orgOperateModel = $operateModel->findByPk($operate['operate_id']);
			$this->attachAttribute($orgOperateModel,$operate);
			if(!$orgOperateModel->validate()){
				return $this->setNotices($orgOperateModel->getErrors(),false);
			}
			$flag = $orgOperateModel->save();
		}else{
			$operate['create_time'] = time();
			$this->attachAttribute($operateModel,$operate);
			if(!$operateModel->validate()){
				return $this->setNotices($operateModel->getErrors(),false);
			}
			$flag = $operateModel->save();
		}
		$cacheRedisModel = $this->getCacheRedisModel();
		$cacheRedisModel->setOperateToRedis($this->getAllOperate());
		if($flag && $this->isAdminAccessCtl()){
			if (!empty($operate['operate_id'])){
				$op_desc = '编辑 运营数据(ID='.$operate['operate_id'].')';
			}else{
				$op_desc = '新增 运营数据';
			}
			$this->saveAdminOpLog($op_desc);
		}
		return $flag;
	}
	
	/**
	 * 存储客服数据
	 * 
	 * @author supeng
	 * @param array $operate
	 * @return mix|boolean
	 */
	public function saveKefu(array $kefu){
		if(!isset($kefu['kefu_type']) || !isset($kefu['contact_type']) || !isset($kefu['contact_name']) || !isset($kefu['contact_account'])){
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		}
		
		$kefuModel = new KefuModel();
		if(isset($kefu['id'])){
			$orgKefuModel = $kefuModel->findByPk($kefu['id']);
			$this->attachAttribute($orgKefuModel,$kefu);
			if(!$orgKefuModel->validate()){
				return $this->setNotices($orgKefuModel->getErrors(),false);
			}
			$flag = $orgKefuModel->save();
		}else{
			$kefu['create_time'] = time();
			$this->attachAttribute($kefuModel,$kefu);
			if(!$kefuModel->validate()){
				return $this->setNotices($kefuModel->getErrors(),false);
			}
			$flag = $kefuModel->save();
		}
		$cacheRedisModel = $this->getCacheRedisModel();
		$cacheRedisModel->setKefuToRedis($this->getAllKefu());
		if($flag && $this->isAdminAccessCtl()){
			if (!empty($kefu['id'])){
				$op_desc = '保存 客服数据(ID='.$kefu['id'].')';
			}else{
				$op_desc = '新增 客服数据';
			}
			$this->saveAdminOpLog($op_desc);
		}
		return $flag;
	
	}
	/**
	 * 取所有的缓存运营数据，没有在从数据库取
	 * @return array
	 */
	public function getAllOperateFromCache(){
		$cacheRedisModel = $this->getCacheRedisModel();
		$allOperate = $cacheRedisModel->getOperateFromRedis();
		if(empty($allOperate)){
			$allOperate = $this->getAllOperate();
			$cacheRedisModel->setOperateToRedis($allOperate);
		}
		return $allOperate;
	}
	
	/**
	 * 取所有的缓存客服数据，没有在从数据库取
	 * 
	 * @author supeng
	 * @return array
	 */
	public function getAllKefuFromCache(){
		$cacheRedisModel = $this->getCacheRedisModel();
		$allKefu = $cacheRedisModel->getKefuFromRedis();
		if(empty($allKefu)){
			$allKefu = $this->getAllKefu();
			$cacheRedisModel->setKefuToRedis($allKefu);
		}
		return $allKefu;
	}
	
	/**
	 * 通过客服ID获取客服信息
	 * 
	 * @author supeng
	 * @param int $id
	 * @return mix|Ambigous <NULL, unknown, multitype:unknown Ambigous <unknown, NULL> , mixed, multitype:, multitype:unknown >
	 */
	public function getKefuInfoById($id){
		if(empty($id)){
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		}
		
		$kefuModel = KefuModel::model();
		$info = $kefuModel->findByPk($id);
		return $info->attributes;
	}
	
	/**
	 * 获取所有的运营数据
	 * 
	 * @return array
	 */
	public function getAllOperate(){
		$operateModel = OperateModel::model();
		$allOperate = $operateModel->getAllOperate();
		return $this->buildOperate($allOperate);
	}
	
	/**
	 * 获取所有的运营数据
	 *
	 * @author supeng
	 * @return array
	 */
	public function getAllKefu(){
		$kefuModel = KefuModel::model();
		$allKefu = $kefuModel->getAllKefu();
		return $this->arToArray($allKefu);
	}
	
	/**
	 * 获取客服列表数据
	 * 
	 * @author supeng
	 * @param array $condition
	 * @param unknown_type $offset
	 * @param unknown_type $pageSize
	 */
	public function getKefuList(Array $condition = array(),$offset=0,$pageSize=10){
		$kefuModel = KefuModel::model();
		$result = $kefuModel->getKefuList($condition,$offset,$pageSize);	
		if($result['list']){
			$result['list'] = $this->arToArray($result['list']);
		}
		return $result;
	}
	
	/**
	 * 取指定分类下所有的缓存运营数据，没有在从数据库取
	 * 
	 * @param int $category
	 * @param int $subCategory
	 * @return array
	 */
	public function getOperateByCategoryFromCache($category,$subCategory = null){
		$cacheRedisModel = $this->getCacheRedisModel();
		$allOperate = $cacheRedisModel->getOperateFromRedis();
		if(empty($allOperate)){
			return $this->getOperateByCategory($category,$subCategory);
		}
		if(isset($allOperate[$category])){
			return isset($allOperate[$category][$subCategory]) ? $allOperate[$category][$subCategory] : $allOperate[$category];
		}
		return array();
		
	}
	/**
	 * 获取指定的分类的运营数据
	 * 
	 * @param int $category 分类名称
	 * @param int $subCategory 子分类名称
	 * @return array
	 */
	public function getOperateByCategory($category,$subCategory = null){
		$operateModel = OperateModel::model();
		$operates = $operateModel->getOperateByCategory($category,$subCategory);
		$operates = $this->buildOperate($operates);
		if(isset($operates[$category])){
			return isset($operates[$category][$subCategory]) ? $operates[$category][$subCategory] : $operates[$category];
		}
		return array();
	}
	
	/**
	 * 删除运营数据
	 * 
	 * @param array $operateIds 运营标识ID
	 * @return int 影响行数
	 */
	public function delOperateByOperateIds(array $operateIds){
		if(empty($operateIds)){
			return array();
		}
		$operateModel = OperateModel::model();
		$flag = $operateModel->delOperateByIds($operateIds);
		if($flag){
			$cacheRedisModel = $this->getCacheRedisModel();
			$cacheRedisModel->setOperateToRedis($this->getAllOperate());
			
			if($this->isAdminAccessCtl()){
				$op_desc = '删除 运营数据(IDS='.implode(',', $operateIds).')';
				$this->saveAdminOpLog($op_desc);
			}
		}
		
		return $flag;
	}
	
	/**
	 * 删除客服数据
	 * 
	 * @author supeng
	 * @param int $id
	 * @return mix
	 */
	public function delKefuById($id){
		if(empty($id)){
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		}
		
		$kefuModel = KefuModel::model();
		if($kefuModel->deleteByPk($id)){
			$cacheRedisModel = $this->getCacheRedisModel();
			$allKefu = $this->getAllKefu();
			$cacheRedisModel->setKefuToRedis($allKefu);
			
			if($this->isAdminAccessCtl()){
				$op_desc = '删除 客服数据(ID='.$id.')';
				$this->saveAdminOpLog($op_desc);
			}
			return true;
		}
				
		return false;
	}
	
	/**
	 * 取得分类列表
	 * 
	 * @param int $category
	 * @return string
	 */
	public function getCategoryList($category = null){
		$list = array(
			CATEGORY_COMMON => '通用栏目',
			CATEGORY_INDEX => '乐天首页',
		);
		return $category ? isset($list[$category]) : $list;
	}
	/**
	 * 取得子分类项列表
	 * 
	 * @param int $category
	 * @param int $subCategory
	 * @return array
	 */
	public function getSubCategoryList($category,$subCategory = null){
		$list = array(
			 CATEGORY_COMMON => array(
			 		CATEGORY_COMMON_TOPBANNER	=>	'顶部通栏',
			 		CATEGORY_COMMON_NAVIGATION	=>	'导航',
			 		CATEGORY_COMMON_VIDEO		=>	'视频前贴',
			 		CATEGORY_COMMON_LIVE		=>	'直播间广告',
			 ),
			 CATEGORY_INDEX => array(
			 		CATEGORY_INDEX_ACTIVITYRECOMMAND  => '活动推荐',
			 		CATEGORY_INDEX_COLUMNSRECOMMAND  => '侧栏推荐二',
			 		CATEGORY_INDEX_LIVESRECOMMAND  => '直播强推',
			 		CATEGORY_INDEX_SHOWCASE   => '首页橱窗',
			 		CATEGORY_INDEX_STARCOLLEGE => '侧栏推荐一',
			 		CATEGORY_INDEX_TODAYRECOMMAND => '今日推荐',
			 		CATEGORY_INDEX_BANNER => '首页通栏',
			 		CATEGORY_INDEX_NEWSNOTICE => '首页公告',
			 		CATEGORY_INDEX_DOTEY_RECOMMAND => '本站明星',
			 ),
			 CATEGORY_CHANNEL => array(
			 	   CATEGORY_CHANNEL_SONG_CAROUSEL =>'唱区轮播图',
			 	   CATEGORY_CHANNEL_SONG_NOTICE => '唱区公告',
			 )
		);
		
		if(isset($list[$category][$subCategory])){
			return $list[$category][$subCategory];
		}
		
		if(isset($list[$category])){
			return $list[$category];
		}
		
		return $list;
	}
	/**
	 * 取得运营相关图片的位置
	 * 
	 * @return string
	 */
	public function getOperateUrl(){
		return $this->getUploadUrl().'operate'.DIR_SEP;
	}
	
	/**
	 * 获取客服类型
	 * @param int $type
	 * @author supeng
	 * @return array
	 */
	public function getKefuType($type = null){
		$list = array(
				KEFU_QQ_WORK => '官方/充值',
				KEFU_QQ_RECHARGE => '充值客服',
				KEFU_QQ_DOTEY => '主播招募',
				KEFU_QQ_PROXY_RECRUIT => '代理招募',
				KEFU_QQ_TEC_SUPPORT => '技术支持',
				KEFU_QQ_FAMILY => '家族招募',
				KEFU_QQ_SUGGEST => '意见建议');
		return  $type && isset($list[$type]) ? $list[$type] : $list;
	}
	
	/**
	 * 获取客服联系方式
	 * @author supeng
	 * @return array
	 */
	public function getKefuContactType(){
		return array(
				KEFU_QQ => 'QQ',
				KEFU_TEL => '电话',
			);
	}
	
	/**
	 * @author supeng
	 * @param unknown_type $fileName
	 * @return string
	 */
	public function getOperateImgUrl($fileName){
		$imgDomain = Yii::app()->params['images_server']['url'];
		$imgUrl = $imgDomain.'/operate/'.$fileName;
		return $imgUrl;
	}
	
	/**
	 * 保存或修改意见
	 * 
	 * @author supeng
	 * @param array $data
	 * @return mix|boolean
	 */
	public function saveSuggest(Array $data){
		if(empty($data)){
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		}
		
		$suggestModel = new SuggestModel();
		if(isset($data['content'])){
			$data['content'] = mysql_escape_string(htmlspecialchars($data['content']));
		}
		if(isset($data['info'])){
			$data['info']=serialize($data['info']);
		}
		if(isset($data['suggest_id'])){
			$orgSuggestModel = $suggestModel->findByPk($data['suggest_id']);
			$this->attachAttribute($orgSuggestModel,$data);
			if(!$orgSuggestModel->validate()){
				return $this->setNotices($orgSuggestModel->getErrors(),false);
			}
			$flag = $orgSuggestModel->save();
		}else{
			$data['create_time'] = time();
			$this->attachAttribute($suggestModel,$data);
			if(!$suggestModel->validate()){
				return $this->setNotices($suggestModel->getErrors(),false);
			}
			$flag = $suggestModel->save();
		}
		
		if($flag && $this->isAdminAccessCtl()){
			if(!empty($data['suggest_id'])){
				$op_desc = '编辑 意见数据(ID='.$data['suggest_id'].')';
			}else{
				$op_desc = '新增 意见数据';
			}
			$this->saveAdminOpLog($op_desc);
		}
		return $flag;
	}
	
	/**
	 * 保存直播在线统计
	 * 
	 * @author supeng
	 * @param array $data
	 * @return mix|boolean
	 */
	public function saveShowStat(Array $data){
		if(empty($data)){
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		}
	
		$showStatOnlineModel = new ShowStatOnlineModel();
		if(isset($data['id'])){
			$data['update_time'] = time();
			$orgShowStatOnlineModel = $showStatOnlineModel->findByPk($data['id']);
			$this->attachAttribute($orgShowStatOnlineModel,$data);
			if(!$orgShowStatOnlineModel->validate()){
				return $this->setNotices($orgShowStatOnlineModel->getErrors(),false);
			}
			$flag = $orgShowStatOnlineModel->save();
		}else{
			$data['create_time'] = time();
			$orgData = $showStatOnlineModel->getAllShowStatForTime((int)$data['time']);
			if($orgData){
				$dataArrNew = array();
				$dataArrNew['id'] = $orgData['id'];
				$dataArrNew['total_num'] = $data['total_num'] + $orgData['total_num'];
				$dataArrNew['tel_num'] = $data['tel_num'] + $orgData['tel_num'];
				$dataArrNew['cnc_num'] = $data['cnc_num'] + $orgData['cnc_num'];
				$dataArrNew['yd_num'] = $data['yd_num'] + $orgData['yd_num'];
				$dataArrNew['edu_num'] = $data['edu_num'] + $orgData['edu_num'];
				$dataArrNew['update_time'] = $data['update_time'] + strtotime(date('Y-m-d H:i:s'));
				$orgShowStatOnlineModel = $showStatOnlineModel->findByPk($orgData['id']);
				$data = $dataArrNew;
			}else{
				$orgShowStatOnlineModel = $showStatOnlineModel;
			}
			$this->attachAttribute($orgShowStatOnlineModel,$data);
			if(!$orgShowStatOnlineModel->validate()){
				return $this->setNotices($showStatOnlineModel->getErrors(),false);
			}
			$flag = $orgShowStatOnlineModel->save();
		}
		return $flag;
	}
	
	/**
	 * 获取直播在线人数数据 根据时间
	 * 
	 * @author supeng
	 * @param unknown_type $time
	 * @return mix|Ambigous <multitype:, multitype:unknown Ambigous <multitype:unknown , unknown> >
	 */
	public function getAllShowStatForTime($time){
		if(empty($time)){
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		}
		
		$showStatOnlineModel = new ShowStatOnlineModel();
		$result = $showStatOnlineModel->getAllShowStatForTime($time);
		return $this->arToArray($result);
	}
	
	/**
	 * 获取直播在线人数数据
	 * 
	 * @author supeng
	 * @param array $condition
	 * @param unknown_type $offset
	 * @param unknown_type $pageSize
	 * @return Ambigous <multitype:, multitype:NULL , Ambigous, multitype:multitype: number Ambigous <NULL, unknown, multitype:unknown Ambigous <unknown, NULL> , mixed, multitype:, multitype:unknown > Ambigous <string, unknown, mixed> >
	 */
	public function getShowStatList(Array $condition = array(),$offset=0,$pageSize=10,$isLimit=true){
		$showStatOnlineModel = new ShowStatOnlineModel();
		$result = $showStatOnlineModel->getShowStatList($condition,$offset,$pageSize,$isLimit);
		if (isset($result)){
			$result = $this->arToArray($result);
		}
		return $result;
	}
	
	public function getShowStatListCount(Array $condition = array()){
		$showStatOnlineModel = new ShowStatOnlineModel();
		return $showStatOnlineModel->getShowStatListCount($condition);
	}
	
	/**
	 * 获取意见类型
	 * 
	 * @author supeng
	 * @return array
	 */
	public function getSuggestType(){
		return array(
				SUGGEST_TYPE_INDEX => '首页',
				SUGGEST_TYPE_LIVEROOM => '直播间',
				SUGGEST_TYPE_GAME => '游戏',
				SUGGEST_TYPE_COMPLAIN =>'投诉',
				SUGGEST_TYPE_OTHER => '其它',
			);
	}
	
	/**
	 * 获取意见反馈的处理标识
	 * 
	 * @author supeng
	 * @return array
	 */
	public function getSuggestHandler(){
		return array(
				SUGGEST_HANDLER_NO => '未处理',
				SUGGEST_HANDLER_YES => '已处理'
			);
	}
	
	/**
	 * 获取意见列表
	 * 
	 * @author supeng
	 * @param array $condition
	 * @param unknown_type $offset
	 * @param unknown_type $pageSize
	 * @return multitype:number multitype: |Ambigous <Ambigous, multitype:multitype: number Ambigous <NULL, unknown, multitype:unknown Ambigous <unknown, NULL> , mixed, multitype:, multitype:unknown > Ambigous <string, unknown, mixed> >
	 */
	public function getSuggestByCondition(Array $condition = array(),$offset=0,$pageSize=10){
		if (!empty($condition['username']) || !empty($condition['nickname']) || !empty($condition['realname'])){
			$_condition = array();
			if(!empty($condition['username'])){
				$_condition['username'] = $condition['username'];
			}
			if(!empty($condition['nickname'])){
				$_condition['nickname'] = $condition['nickname'];
			}
			if(!empty($condition['realname'])){
				$_condition['realname'] = $condition['realname'];
			}
			$userSer = new UserService();
			$info = $userSer->searchUserList(null,null,$_condition,false);
			if($info['list']){
				$condition['uid'] = array_keys($info['list']);
			}else{
				return array('count'=>0,'list'=>array());
			}
		}
		$suggestModel = new SuggestModel();
		return $suggestModel->getSuggestList($condition,$offset,$pageSize);
	}
	
	/**
	 * 获取附件地址
	 * 
	 * @author supeng
	 * @param string $name
	 * @return string
	 */
	public function getSuggestAttach($name){
		return $this->getUploadUrl().'suggest/'.$name;
	}
	
	/**
	 * 删除意见
	 * 
	 * @author supeng
	 * @param int $pk
	 * @return boolean
	 */
	public function delUserSuggest($pk){
		if (empty($pk) || intval($pk)<=0){
			return false;
		}
		$suggestModel = new SuggestModel();
		if($suggestModel->deleteByPk($pk)){
			if($this->isAdminAccessCtl()){
				$op_desc = '删除 意见数据(ID='.$pk.')';
				$this->saveAdminOpLog($op_desc);
			}
			return true;
		}
		return false;
	}
	
	/**
	 * 上传主播大图，新首页上线后，该方法废弃
	 * 
	 * @param unknown_type $form
	 * @param array $doteys
	 * @return mix|boolean
	 */
	public function uploadDoteyDisplayBig($form,Array $doteys){
		if(!isset($form) || !isset($doteys) ){
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		}
		
		$doteySer = new DoteyService();
		if($effectFiles = CUploadedFile::getInstancesByName($form)){
			foreach ($effectFiles as $effectFile){
				if($filename = $effectFile->getName()){
					$doteyUid = array_shift($doteys);
					$extName = $effectFile->getExtensionName();
					$uploadfile = $doteySer->getDoteySaveFile($doteyUid,'big','display');
					$uploadDir = $doteySer->getDoteySavePath($doteyUid);
					if (!file_exists($uploadDir)){
						mkdir($uploadDir,0777,true);
					}
					if($effectFile->saveAs($uploadfile,true)){
						return true;
					}
				}
			}
			return true;
		}
		return false;
		
	}
	
	
	/**
	 * 重建资料
	 * 
	 * @param $operate
	 * @return array
	 */
	protected function buildOperate(array $operate = array()){
		if(empty($operate)){
			return array();
		}
		$_operates = array();
		foreach($operate as $key=>$_operate){
			if($_operate->content){
				$_operate->content = unserialize($_operate->content);
			}
			$category = $_operate->category;
			$sub_category = $_operate->sub_category;
			$_operates[$category][$sub_category][] = $_operate->attributes;
		}
		return $_operates;
	}
	
	/**
	 * 根据id获取运营数据
	 * @author guoshaobo
	 * @param int $operateId
	 * @return mix|multitype:
	 */
	public function getOperateById($operateId)
	{
		if($operateId<=0){
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		}
		$operateModel = new OperateModel();
		$res = $operateModel->findByPk($operateId);
		if($res){
			$data = $res->attributes;
			$data['content'] = unserialize($data['content']); 
			return $data;
		}
		return false;
	}
	
	/**
	 * 返回直播间的视频前贴
	 * @author guoshaobo
	 * @param unknown_type $liveId
	 * @param unknown_type $doteyId
	 */
	public function getLiveAdv($liveId, $doteyId)
	{
		$list = array();
		$all = $this->getAllOperateFromCache();
		if(isset($all[CATEGORY_COMMON]) && isset($all[CATEGORY_COMMON][CATEGORY_COMMON_VIDEO])){
			$list = $all[CATEGORY_COMMON][CATEGORY_COMMON_VIDEO];
		}
		if($list){
			$data = array();
			$channelService = new ChannelService();
			
			$data_all = $data_channel = $data_live = array();
			$src = Yii::app()->params['images_server']['url'].'/operate/';
			foreach($list as $k=>$v){
				$_tmp = array();
				if($v['content']['status']==1){
					if($v['content']['position']==0){
						$_tmp['url'] = $v['textlink'];
							$_tmp['src'] = $src.$v['piclink'];
						$_tmp['type'] = $v['content']['type'];
						$_tmp['time'] = $v['content']['time']; 
						$data_all[] = $_tmp;
					}elseif($v['content']['position']==1){
						$channels = $channelService->getAllChannel(array('sub_channel_id'=>$v['content']['channels']));
						$key = array_keys($channels);
						$channel_id = $key[0];
						if($channelService->getChannelDoteyByUids(array($doteyId), $channel_id, $v['content']['channels'])){
							$_tmp['url'] = $v['textlink'];
							$_tmp['src'] = $src.$v['piclink'];
							$_tmp['type'] = $v['content']['type'];
							$_tmp['time'] = $v['content']['time'];
							$data_channel[] = $_tmp;
						}
					}elseif($v['content']['position']==2 && in_array($liveId, explode(',',$v['content']['target']))){
						$_tmp['url'] = $v['textlink'];
							$_tmp['src'] = $src.$v['piclink'];
						$_tmp['type'] = $v['content']['type'];
						$_tmp['time'] = $v['content']['time'];
						$data_live[] = $_tmp;
					}
				}
			}
			if(count($data_live)>0){
				return $data_live[array_rand($data_live)];
			}
			if(count($data_channel)>0){
				return $data_channel[array_rand($data_channel)];
			}
			if(count($data_all)>0){
				return $data_all[array_rand($data_all)];
			}
		}
		return false;
	}
	
	/**
	 * 获取顶部通栏的广告
	 * @author guoshaobo
	 * @param unknown_type $liveId
	 * @param unknown_type $doteyId
	 * @return multitype:unknown |boolean
	 */
	public function getTopBannerAdv($doteyId = null, $controller = '', $action = '')
	{
		$list = array();
		$all = $this->getAllOperateFromCache();
		if(isset($all[CATEGORY_COMMON]) && isset($all[CATEGORY_COMMON][CATEGORY_COMMON_TOPBANNER])){
			$list = $all[CATEGORY_COMMON][CATEGORY_COMMON_TOPBANNER];
		}
		if($list){
			$liveId = 0;
			if($doteyId > 0){
				$doteyServ = new DoteyService();
				$archiveServ = new ArchivesService();
				
				$doteyInfo = $doteyServ->getDoteyInfoByUid($doteyId);
				$sub_channel = $doteyInfo['sub_channel'];
				
				$archiveInfo = $archiveServ->getArchivesByUids(array($doteyId));
				if($archiveInfo){
					$archiveInfo = array_pop($archiveInfo);
				}
				$liveId = $archiveInfo['archives_id'];
			}
			
			$data_index = $data_all = $data_channel = $data_live = array();
			$channelService = new ChannelService();
			foreach($list as $k=>$v){
				if($v['content']['position']==999 && ($controller == 'index' && $action=='index')){ //指定首页
					$data_index[] = $v;
				}elseif($v['content']['position']==0){
					$data_all[] = $v;
				}elseif($v['content']['position']==1){
					if(!empty($doteyId)){
						$channel_id = $v['content']['channels'];
						if($this->hasBit(intval($sub_channel), intval($channel_id))){
							$data_channel[] = $v;
						}
					}
				}elseif($v['content']['position']==2 && $liveId >0 && in_array($liveId, explode(',',$v['content']['target']))){
					$data_live[] = $v;
				}
			}
			if(count($data_index)>0){
				return $data_index[array_rand($data_index)];
			}elseif(count($data_live)>0){
				return $data_live[array_rand($data_live)];
			}elseif(count($data_channel)>0){
				return $data_channel[array_rand($data_channel)];
			}elseif(count($data_all)>0){
				return $data_all[array_rand($data_all)];
			}
		}
		return array();
	}
	/**
	 * 获取直播间页面上的广告
	 * @author guoshaobo
	 * @param unknown_type $liveId
	 * @param unknown_type $doteyId
	 * @return boolean|multitype:unknown
	 */
	public function getLivePageAdv($liveId, $doteyId, $getRand = false)
	{
		if($liveId <= 0 || $doteyId <= 0){
			return array();
		}
		$list = array();
		$all = $this->getAllOperateFromCache();
		if(isset($all[CATEGORY_COMMON]) && isset($all[CATEGORY_COMMON][CATEGORY_COMMON_LIVE])){
			$list = $all[CATEGORY_COMMON][CATEGORY_COMMON_LIVE];
		}
		if($list){
			$data_all = $data_channel = $data_live = array();
			$channelService = new ChannelService();
			$src = Yii::app()->params['images_server']['url'].'/operate/';
			foreach($list as $k=>$v){
				$_tmp = array();
				if($v['content']['position']==0){
					$_tmp = $v;
					$_tmp['src'] = $src . $v['piclink'];
					$data_all[] = $_tmp;
				}elseif($v['content']['position']==1){
					$channels = $channelService->getAllChannel(array('sub_channel_id'=>$v['content']['channels']));
					$key = array_keys($channels);
					$channel_id = $key[0];
					if($channelService->getChannelDoteyByUids(array($doteyId), $channel_id, $v['content']['channels'])){
						$_tmp = $v;
						$_tmp['src'] = $src . $v['piclink'];
						$data_channel[] = $_tmp;
					}
				}elseif($v['content']['position']==2 && in_array($liveId, explode(',',$v['content']['target']))){
					$_tmp = $v;
					$_tmp['src'] = $src . $v['piclink'];
					$data_live[] = $_tmp;
				}
			}
			if($getRand){
				$lc = count($data_live);
				$cc = count($data_channel);
				$ac = count($data_all);
				$num = 5;
				if($lc >= $num){
					$data = $this->getFiveData($data_live,$num);
				}else{
					if(($lc + $cc) >= $num){
						$data = array_merge($data_live,$this->getFiveData($data_channel, $num - $lc));
					}elseif(($lc + $cc + $ac) >= $num){
						$data = array_merge($data_live,$data_channel,$this->getFiveData($data_all, $num - $lc - $cc));
					}else{
						$data = array_merge($data_live,$data_channel,$data_all);
					}
				}
				return $data;
			}
			if(count($data_live)>0){
				return $data_live[array_rand($data_live)];
			}
			if(count($data_channel)>0){
				return $data_channel[array_rand($data_channel)];
			}
			if(count($data_all)>0){
				return $data_all[array_rand($data_all)];
			}
		}
		return $list;
	}
	
	/**
	 * 获取导航
	 * @author guoshaobo
	 * @return multitype:unknown |boolean
	 */
	public function getNavigate()
	{
		$list = array();
		$all = $this->getAllOperateFromCache();
		if(isset($all[CATEGORY_COMMON]) && isset($all[CATEGORY_COMMON][CATEGORY_COMMON_NAVIGATION])){
			$list = $all[CATEGORY_COMMON][CATEGORY_COMMON_NAVIGATION];
		}
		if($list){
			$data = array();
			foreach($list as $k=>$v){
				$_tmp['name'] = $v['subject'];
				$_tmp['link'] = $v['textlink'];
				$data[] = $_tmp;
			}
			return $data;
		}
		return $list;
	}
	
	/**
	 * 根据后台配置项获取相应的推广的直播间地址
	 * @return string
	 * @author leiwei
	 */
	public function getSpreadPrograme(){
		$uids=array();
		$webConfigService = new WebConfigService();
		$cData= $webConfigService->getLivePush();
		if($cData['custom']){
			$custom=array();
			$custom[0]=$this->getSpreadUids($cData['custom'][0]);
			if(isset($cData['custom'][1])){
				$custom[1]=$this->getSpreadUids($cData['custom'][1]);
				$uids=array_merge($custom[0],$custom[1]);
			}else{
				$uids=$custom[0];
			}
			
		}else{
			$uids=$this->getSpreadUids($cData['global']);
		}
		$archivesService=new ArchivesService();
		return empty($uids)?'http://'.$_SERVER['HTTP_HOST']:$archivesService->getArchivesShortUrl($uids[rand(0,(count($uids)-1))]);
	}
	
	
	/**
	 * 根据配置项获取符合条件的用户uid
	 * @param int $config
	 * @return array
	 * @author leiwei
	 */
	public function getSpreadUids($config){
		$uids=array();
		if($config==WEB_LIVE_PUSH_GLOGAL){
			$otherRedisModel=new OtherRedisModel();
			$archives=$otherRedisModel->getLivingFromRedis();
			if($archives){
				foreach($archives as $row){
					$archivesIds[]=$row['archives_id'];
				}
				$archivesService=new ArchivesService();
				$archivesList=$archivesService->getArchivesByArchivesIds($archivesIds);
				foreach($archivesList as $val){
					$uids[]=$val['uid'];
				}
			}
		}
		if($config==WEB_LIVE_PUSH_CUSTOM_TODYARMD){
			$todayRecommandModel=new DoteyTodayRecommandModel();
			$todayRecommandArchives = $todayRecommandModel->getAllTodayRecommand();
			$todayRecommandArchives = $this->arToArray($todayRecommandArchives);
			foreach($todayRecommandArchives as $row){
				$uids[]=$row['uid'];
			}
		}
		if($config==WEB_LIVE_PUSH_CUSTOM_DOTEY){
			$livePush = $this->getOperateByCategory(CATEGORY_INDEX,CATEGORY_INDEX_LIVESRECOMMAND);
			if($livePush){
				foreach($livePush as $row){
					$uids[]=$row['target_id'];
				}
			}
		}
		return $uids;
	}
	
	/**
	 * 获取首页右侧推荐数据
	 * 
	 * @author supeng
	 * @param unknown_type $type
	 * @return Ambigous <multitype:, multitype:NULL >|multitype:
	 */
	public function getIndexRightData($type=0){
		$indexRightModel = new IndexRightDataModel();
		$data = $indexRightModel->getIndexData($type);
		if ($data) {
			return $this->buildDataByIndex($this->arToArray($data),'uid');
		}
		return array();
	}
	
	protected function getCacheRedisModel(){
		if(self::$cacheRedisModel == null){
			self::$cacheRedisModel = new OtherRedisModel();
		}
		return self::$cacheRedisModel;
	}
	
	/**
	 * 获取首页右侧导航的新秀主播推荐描述
	 * @author supeng
	 */
	public function getIndexRightDataForPookieDotey(){
		$redisModel = $this->getCacheRedisModel();
		return $redisModel->getIndexRightDataForPookieDotey();
	}
	
	/**
	 * 定入首页右侧导航的新秀主播推荐描述
	 * @author supeng
	 */
	public function setIndexRightDataForPookieDotey($info){
		if (!is_string($info))
			return $this->setError(Yii::t('common','Parameter is error'),false);
		$redisModel = $this->getCacheRedisModel();
		return $redisModel->setIndexRightDataForPookieDotey($info);
	}
	
	/**
	 * 获取首页右侧导航的最新加入推荐描述
	 * @author supeng
	 */
	public function getIndexRightDataForNewDotey(){
		$redisModel = $this->getCacheRedisModel();
		return $redisModel->getIndexRightDataForNewDotey();
	}
	
	public function setIndexRightDataForNewDotey($info){
		if (!is_string($info))
			return $this->setError(Yii::t('common','Parameter is error'),false);
		$redisModel = $this->getCacheRedisModel();
		return $redisModel->setIndexRightDataForNewDotey($info);
	}
	
	/**
	 * 获取首页右侧导航的明星主播版块推荐描述
	 * @author supeng
	 */
	public function getIndexRightDataForStarDotey(){
		$redisModel = $this->getCacheRedisModel();
		return $redisModel->getIndexRightDataForStarDotey();
	}
	
	public function setIndexRightDataForStarDotey($info){
		if (!is_string($info)) 
			return $this->setError(Yii::t('common','Parameter is error'),false);
		$redisModel = $this->getCacheRedisModel();
		return $redisModel->setIndexRightDataForStarDotey($info);
	}
	
	/**
	 * 随机获取数组中的5个数据
	 * @author guoshaobo
	 * @param array $data
	 * @param int $num
	 * @return array
	 */
	private function getFiveData($data = array(), $num = 5)
	{
		if(!empty($data)){
			shuffle($data);
			$data = array_slice($data, 0, $num);
		}
		return $data;
	}
}

?>