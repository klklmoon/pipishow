<?php
/**
 * 皮皮乐天基础服务层，所有应用服务层基类
 *
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author Su Qian <aoxue.1988.su.qian@163.com>
 * @version $Id: PipiService.php 17334 2014-01-09 02:49:39Z hexin $
 * @package 
 */
class PipiService extends CComponent{
	
	/**
	 * @var PipiZmq ZMQ队列发送
	 */
	public static $pipiZmq = NULL;
	/**
	 * @var PipiController
	 */
	protected $controller;
	/**
	 * @var array 记录错误信息
	 */
	protected $errors = array();
	
	/**
	 * @var 用户界面友好提提示
	 */
	protected $notice = array();
	
	public function __construct(PipiController $pipiController = null){
		$this->controller = $pipiController;
	}
	
	/**
	 * 获取Zmq对象
	 * @return PipiZmq
	 */
	public function getZmq(){
		if(self::$pipiZmq == null){
			self::$pipiZmq = new PipiZmq();
		}
		return self::$pipiZmq;
	}
	/**
	 * 设置错误信息,并返回相应的错误类型状态，一般为false,0,'',null,array()
	 * 
	 * @param mix $error
	 * @param mix $returnValue
	 * @return mix
	 */
	public function setError($error,$returnValue = false){
		Yii::log($error,CLogger::LEVEL_ERROR,'bll.service.'.get_class($this));
		if(YII_DEBUG){
			trigger_error($error,E_USER_ERROR);
		}
		if(is_array($error)){
			foreach ($error as $key=>$_error){
				is_int($key) ? $this->errors[$key] = $_error : $this->errors[$key] = $_error;
			}
		}else{
			$this->errors[] = $error;
		}
		return $returnValue;
	}
	
	
	/**
	 * 设置提示信息
	 * 
	 * @param string $key
	 * @param string $value
	 * @param mixed $status
	 * @return mixed
	 */
	public function setNotice ($key,$value,$status = NULL){
		$this->notice[$key] = $value;
		return $status;
	}
	
	public function setNotices(array $notices ,$status = NULL){
		
		foreach($notices as $key=>$notice){
			$this->notice[$key] = $notice;
		}
		return $status;
	}
	/**
	 * 取得单条错误信息
	 * 
	 * @return string
	 */
	public function getError(){
		if($this->errors)
			return array_pop($this->errors);
	}
	
	/**
	 * 获取所有错误类型
	 * @return array
	 */
	public function getErrors(){
		return $this->errors;
	}
	
	/**
	 * @return 获取用户界面友好提提示
	 */
	public function getNotice(){
		return $this->notice;
	}
	
	/**
	 * 取得缓存组件
	 * 
	 * @return CCache
	 */
	public function getCacheComponent(){
		return Yii::app()->cache;
	}
	
	/**
	 * 将AR集合对象转换成数组格式
	 * @param array $arCollection
	 * @return array
	 */
	public function arToArray(array $arCollection){
		if(empty($arCollection)) return array();
		$_array = array();
		//var_dump($arCollection);
		foreach($arCollection as $ar){
			$_array[] = $ar->attributes;
			
		}
		return $_array;
	}
	
	/**
	 * 按指定数组中已经存在的字段名做为索引（key）重建数组
	 * 注意：
	 * 该方法适合$resultIndex是唯一键的场景，不唯一时使用下面的buildDataByKey方法，代码有个坑，以后有机会再优化下
	 * 
	 * @param array $data    数组
	 * @param string $resultIndex    指定的字段
	 * @param string $returnData     返回的字段
	 * @author su qian
	 * @return array   返回重建后的数组
	 */
	public function buildDataByIndex(array $data, $resultIndex, $returnData = null) {
		if (empty($resultIndex)) 
			return $data;
		$_data = array();
		foreach ($data as $key => $value) {
			if (!isset($_data[$value[$resultIndex]]))
				$_data[$value[$resultIndex]] = $returnData ? $value[$returnData] : $value;
			else {
				$_tmp = $_data[$value[$resultIndex]];
				$_data[$value[$resultIndex]] = (!is_array($_tmp) || isset($_tmp[$resultIndex]) && $_tmp[$resultIndex]) ? array($_tmp) : $_tmp;
				array_push($_data[$value[$resultIndex]], $returnData ? $value[$returnData] : $value);
			}
		}
		return $_data;
	}
	
	
	
	/**
	 * 附加属性值， 非字段属性不会赋值，避免报错
	 * 
	 * @param PipiActiveRecord $pipiActiveRecord
	 * @param array $array
	 */
	public function attachAttribute(PipiActiveRecord $pipiActiveRecord ,array $array){
		$attribute = $pipiActiveRecord->attributeNames();
		foreach($array as $key=>$value){
			if(in_array($key, $attribute)){
				$pipiActiveRecord->$key = $value;
			}
		}
	}
	
    /**
	 * 获取数组中的值，并注销掉该值
	 * 
	 * @param array $array 被提取的数组
	 * @param int|string $key 数组中的KEY
	 * @return string
	 */
	public function array_get(array &$array,$key){
		if(isset($array[$key])){
			$value = $array[$key];
			unset($array[$key]);
			return $value;
		}
		return '';
	 }
	 
	 
	  /**
	  * 授予某项权限
	  * 
	  * @param int $orgBit 原始值
	  * @param int $revokeBit 撤销的值
	  * @return boolean
	  */
	 public function grantBit($orgBit,$addBit){
	 	if(!is_int($orgBit) || !is_int($addBit)){
	 		return $this->setError(Yii::t('common','Parameter is error'),false);
	 	}
	 	if($orgBit < 0 || $addBit < 0){
	 		return $this->setError(Yii::t('common','Parameter is error'),false);
	 	}
	 	return $orgBit | $addBit;
	 }
	 
	  /**
	  * 撤销某项权限
	  * 
	  * @param int $orgBit 原始值
	  * @param int $revokeBit 撤销的值
	  * @return boolean
	  */
	 public function revokeBit($orgBit,$revokeBit){
	 	if(!is_int($orgBit) || !is_int($revokeBit)){
	 		return $this->setError(Yii::t('common','Parameter is error'),false);
	 	}
	 	
	 	if($orgBit < 0 || $revokeBit < 0){
	 		return $this->setError(Yii::t('common','Parameter is error'),false);
	 	}
	 	
	 	if($revokeBit > $orgBit){
	 		return $this->setError(Yii::t('common','Parameter is error'),false);
	 	}
	 	
	 	return $orgBit ^ $revokeBit;
	 }
	 
	 /**
	  * 判断是否具有某项权限
	  * 
	  * @param int $orgBit 原始值
	  * @param int $bit 是否具有的值
	  * @return boolean
	  */
	 public function hasBit($orgBit,$bit){
	 	if(!is_int($orgBit) || !is_int($bit)){
	 		return $this->setError(Yii::t('common','Parameter is error'),false);
	 	}
	 	
	 	if($orgBit < 0 || $bit < 0){
	 		return $this->setError(Yii::t('common','Parameter is error'),false);
	 	}
	   
	 	return ($orgBit & $bit) === $bit && $bit <= $orgBit;
	 }
	 
	/**
	  * 授予多项权限
	  * 
	  * @param int $orgBit　原始值
	  * @param int $mixed 多个参数
	  * @return boolean
	  */
	 public function grantMoreBit($orgBit){
	 	$args =func_get_args();
	 	$count = count($args);
	 	if(!is_int($orgBit) || $count < 2){
	 		return $this->setError(Yii::t('common','Parameter is error'),false);
	 	}
	 	$moreBit = 0;
	 	array_shift($args);
	 	$args = is_array($args[0]) ? $args[0] : $args;
	 	foreach($args as $arg){
	 		if(!is_int($arg) || ($arg != 1 && !preg_match('/^\d+$/',log($arg,2)) )){
	 			return $this->setError(Yii::t('common','Parameter is error'),false);
	 		}
	 		$moreBit |= $arg;
	 	}
	 	return $moreBit;
	 }
	 
	/**
	  * 撤销多项权限
	  * 
	  * @param int $orgBit　原始值
	  * @param int $mixed 多个参数
	  * @return boolean
	  */
	 public function revokeMoreBit($orgBit){
	 	$args =func_get_args();
	 	$count = count($args);
	 	if(!is_int($orgBit) || $count < 2){
	 		return $this->setError(Yii::t('common','Parameter is error'),false);
	 	}
	 	$revokeBit = 0;
	 	array_shift($args);
	 	foreach($args as $arg){
	 		if(!is_int($arg) || ($arg != 1 && !preg_match('/^\d+$/',log($arg,2)) )){
	 			return $this->setError(Yii::t('common','Parameter is error'),false);
	 		}
	 		$revokeBit |= $arg;
	 	}
		 if($revokeBit > $orgBit){
	 		return $this->setError(Yii::t('common','Parameter is error'),false);
	 	}
	 	return $orgBit ^ $revokeBit;
	 }
	 /**
	  * 是否具有多项权限
	  * 
	  * @param int $orgBit　原始值
	  * @param int $mixed 多个参数
	  * @return boolean
	  */
	 public function hasMoreBit($orgBit){
	 	$args =func_get_args();
	 	$count = count($args);
	 	if(!is_int($orgBit) || $count < 2){
	 		return $this->setError(Yii::t('common','Parameter is error'),false);
	 	}
	 	$moreBit = 0;
	 	array_shift($args);
	 	foreach($args as $arg){
	 		if(!is_int($arg) || ($arg != 1 && !preg_match('/^\d+$/',log($arg,2)) )){
	 			return $this->setError(Yii::t('common','Parameter is error'),false);
	 		}
	 		$moreBit |= $arg;
	 	}
	 	return ($orgBit & $moreBit) === $moreBit && $moreBit<=$orgBit;
	 	
	 }
	 
	 /**
	  * 二维数组排序
	  * @author leiwei
	  * @param array $arr   待排序的数组
	  * @param string $keys 待排序的键值
	  * @param string $type 顺序或倒序
	  * @return array 
	  */
	 public function array_sort($arr, $keys, $type = 'desc') {
	 	$keysvalue = $new_array = array ();
	 	foreach ( $arr as $k => $v ) {
	 		$keysvalue [$k] = $v [$keys];
	 	}
	 	if ($type == 'asc') {
	 		asort ( $keysvalue );
	 	} else {
	 		arsort ( $keysvalue );
	 	}
	 	reset ( $keysvalue );
	 	foreach ( $keysvalue as $k => $v ) {
	 		$new_array [] = $arr [$k];
	 	}
	 
	 	return $new_array;
	 }
	
	 public function getBitCondition($orgBit,$minBit,$maxBit,array &$condition = array()){
	 	$data = $this->bitCondition($minBit,$maxBit,$condition);
	 	$returnData = array();
	 	foreach ($data as $value){
	 		if($this->hasBit($value,$orgBit)){
	 			$returnData[] = $value;
	 		}
	 	}
	 	return $returnData;
	 }
	 
	 public function bitCondition($minBit,$maxBit,array &$condition = array()){
	 	if($minBit <= 0 || $maxBit <= 0 ||$minBit > $maxBit){
	 		return $this->setError(Yii::t('common','Parameter is error'),array());
	 	}
	 	if($maxBit == $minBit || $maxBit == 1){
	 		return array($maxBit);
	 	}
	 	$count = log($maxBit,2);
	 	if(!preg_match('/^\d+$/',$count) || ($minBit !=1 && !preg_match('/^\d+$/',$minBit))){
	 		return $this->setError(Yii::t('common','Parameter is error'),array());
	 	}
	 	$condition = array();
	 	for($i = 0; $i <= $count;$i++){
	 		$condition[] = pow(2,$i);
	 	}
	 	if(empty($condition)){
	 		return array();
	 	}
	 	$returnArray = array();
	 	$tmp =  $condition;
	 	foreach ($tmp as $key=>$_codition){
	 		if($_codition >= $minBit){
	 			$returnArray[] = $_codition;
	 			unset($tmp[$key]);
	 			$_tmp = array_values($tmp);
	 			//todo
	 			for($i=0,$tmpCount = count($tmp);$i<$tmpCount;$i++){
	 				$returnArray[] = $_codition+$_tmp[$i];
	 				for($j=$i+1;$j<$tmpCount;$j++){
	 					$returnArray[] = $_codition+$_tmp[$i]+$_tmp[$j];
	 					for($a=$j+1;$a<$tmpCount;$a++){
	 						$returnArray[] = $_codition+$_tmp[$i]+$_tmp[$j]+$_tmp[$a];
	 						for($b=$a+1;$b<$tmpCount;$b++){
	 							$returnArray[] = $_codition+$_tmp[$i]+$_tmp[$j]+$_tmp[$a]+$_tmp[$b];
	 							for($c=$b+1;$c<$tmpCount;$c++){
	 								$returnArray[] = $_codition+$_tmp[$i]+$_tmp[$j]+$_tmp[$a]+$_tmp[$b]+$_tmp[$c];
	 							}
	 						}
	 					}
	 				}
	 				
	 			}
	 		}else{
	 			unset($tmp[$key]);;
	 		}
	 	}
	 	return $returnArray;
	 }
	 /**
	  * 按照数组中的某个键值来重建数组
	  * @param array $data  需要重建的数组
	  * @param string $key  指定的字段
	  * @param string $pk   主键字段
	  * @return array 		返回重建后的数组
	  */
	 public function buildDataByKey(array $data,$key,$pk='',$desc='desc'){
	 	if(empty($data)) return array();
	 	$list=$this->array_sort($data, $key,$desc);
	 	$newData=array();
	 	foreach($list as $row){
	 		if($pk){
	 			$newData[$row[$key]][$row[$pk]]=$row;
	 		}else{
	 			$newData[$row[$key]][]=$row;
	 		}
	 	}
	 	return $newData;
	 }
	 
	 /**
	  * 获取去数组的维数
	  * @param mixed $array 
	  * @return number
	  */
	 public function getArrayDim($array){
	 	if(!is_array($array)){
	 		return 0;
	 	}else{
	 		$max = 0;
	 		foreach($array as $_array){
	 			$t1 = $this->getArrayDim($_array);
	 			if( $t1 > $max) $max = $t1;
	 		}
	 		return $max + 1;
	 	}
	 }
	 
	public function getUploadUrl(){
		return trim(Yii::app()->params['images_server']['url'],DIR_SEP).'/';
	}
	
	public function getCdnUrl(){
		return trim(Yii::app()->params['images_server']['cdn_url'],DIR_SEP).'/';
	}
	
	/**
	 * 获取秀场后台图片地址
	 * @return string
	 */
	public function getShowAdminUrl(){
		return 'http://showadmin'.DOMAIN.'/images/';
	}
	
	/**
	 * 上传单个附件
	 * 
	 * @param string $formName 表单名子
	 * @param string $forder images目录下的那个文件夹
	 * @param string $newFile 是否是新文件
	 * @return string 上传成功的文件名
	 */
	public function uploadSingleImages($formName,$forder = 'suggest',$newFile = true){
		$imgFile = CUploadedFile::getInstanceByName($formName);
		if(empty($imgFile)){
			return '';
		}
		$filename = $imgFile->getName();
		$extName = $imgFile->getExtensionName();
		$filename = $newFile ? uniqid().'.'.$extName : $filename;
		$uploadDir = IMAGES_PATH.$forder.DIR_SEP;
		if (!is_dir($uploadDir)){
			mkdir($uploadDir,0777,true);
		}
		$uploadfile = $uploadDir.$filename;
		if($imgFile->saveAs($uploadfile,true)){
			return $filename;
		}
		return '';
		
	}
	
	/**
	 * 上传主播图片
	 *
	 * @author supeng
	 * @param string $formName 表单名子
	 * @param string $forder images目录下的那个文件夹
	 * @param string $newFile 是否是新文件
	 * @return string 上传成功的文件名
	 */
	public function uploadDoteyImages($formName,$forder = 'dotey',$fileName){
		$imgFile = CUploadedFile::getInstanceByName($formName);
		if(empty($imgFile)){
			return '';
		}
		if (!is_dir(dirname($fileName))){
			mkdir(dirname($fileName),0777,true);
		}
		if($imgFile->saveAs($fileName,true)){
			return $fileName;
		}
		return '';
	
	}
	
	/**
	 * 是否是后台访问控制
	 * @author supeng
	 */
	public function isAdminAccessCtl(){
		if (strtolower(php_sapi_name()) != 'cli'){
			$hostInfo = Yii::app()->request->getHostInfo();
			if(!empty($hostInfo)){
				$parseUrl = parse_url($hostInfo);
				$parseStr = 'showadmin.';
				$hostname = $parseUrl['host'];
				$pos = stripos($hostname, $parseStr,0);
				return ($pos === 0)?true:false;
			}
		}
		return false;
	}
	
	/**
	 * 记录后台操作日志
	 * 
	 * @author supeng
	 * @param string $op_desc
	 * @param int $uid
	 * @param int $sub_id
	 * @param int $role_id
	 */
	public function saveAdminOpLog($op_desc,$uid=0,$sub_id=0,$role_id=0){
		$data = array();
		$purviewSer = new PurviewService();
		$data['uid'] = $uid;
		$data['sub_id'] = $sub_id;
		$data['role_id'] = $role_id;
		$data['purview_id'] = ADMIN_PURVIEW_ID;
		$data['op_desc'] = $op_desc;
		$data['op_uid'] = ADMIN_OP_UID;
		$data['op_role_id'] = ADMIN_OP_ROLE_ID;
		$data['op_sub_id'] = ADMIN_OP_SUB_ID;
		$data['op_ip'] = Yii::app()->request->getUserHostAddress();
		$data['params'] = Yii::app()->request->getQueryString();
		$purviewSer->saveRecord($data);
	}
	
	public function sendPhoneSms($phone,$content,$checkcontent = 0){
		if(empty($phone) || empty($content)){
			return array('status'=>'fail','info'=>'参数错误');
		}
		$return = array();
		$smsConfig = Yii::app()->params['fftkj'];
		$url = 'http://sms.106vip.com/sms.aspx?action=send&userid='.$smsConfig['id'].'&account='.$smsConfig['username'].'&password='.$smsConfig['password'];
		
		$post['mobile'] = $phone;
		$post['content'] = $content;
		$post['checkcontent'] = $checkcontent;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		$output = curl_exec($ch);
		$error = curl_errno($ch);
		$errorString = curl_error($ch);
		curl_close($ch);
		if($error){
			$return = array('status'=>'fail','info'=>$errorString);
		}else{
			$xmlParser = new PipiXmlParser();
			$xml = simplexml_load_string($output);
			if($xml->returnstatus == 'Success'){
				$return = array('status'=>'success','info'=>$xml->remainpoint);
			}else{
				$return = array('status'=>'fail','info'=>$xml->message);
			}
		}
		return $return;
	}
	 
}

