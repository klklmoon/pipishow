<?php
/**
 * 敏感词service层
 *
 * the last known user to change this file in the repository  <$LastChangedBy: guoshaobo $>
 * @author guoshaobo <guoshaobo@pipi.cn>
 * @version $Id: WordService.php 10001 2013-05-10 07:34:22Z guoshaobo $
 * @package service
 * @subpackage common
 */
define('WORD_TYPE_CHAT', 0);
define('WORD_TYPE_NICKNAME', 1);


class WordService extends PipiService{
	
	private static $blackWordModel;
	
	private static $jsCreate = true;
	
	/**
	 * 获取全部敏感词列表
	 * @param int $offset
	 * @param int $limit
	 * @return array
	 */
	public function getAllChatWordList()
	{
		$blackWord = new BlackWordModel();
		$res = $blackWord->findAllByAttributes(array('status'=>1));
		$data = array();
		foreach($res as $k=>$v){
			$data[] = $v->attributes;
		}
		return $data;
	}
	
	/**
	 * 保存聊天敏感词
	 * @param unknown_type $word
	 * @param unknown_type $uid
	 * @return mix|boolean
	 */
	public function saveCharWord($word, $uid, $word_type = 0)
	{
		if(($wordId = $word['id'])<0){
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		}
		$redis = new OtherRedisModel();
		$wordList = json_decode($redis->getChatWord(),true);
		
		$blackWord = new BlackWordModel();
		$ordBlackWord = $blackWord->findByPk($wordId);
		if($ordBlackWord && $wordId >0){
			$this->attachAttribute($ordBlackWord,$word);
			if(!$ordBlackWord->validate()){
				return $this->setNotices($ordBlackWord->getErrors(),false);
			}
			$flag = $ordBlackWord->save();
			$word = $ordBlackWord->attributes;
		}else{
			unset($word['id']);
			$this->attachAttribute($blackWord,$word);
			if(!$blackWord->validate()){
				return $this->setNotices($blackWord->getErrors(),false);
			}
			$flag = $blackWord->save();
			$newWord = $blackWord->attributes;
		}
		if($flag && $word_type==0){
			if($ordBlackWord){
				if(isset($word['status']) && $word['status'] == '0'){
					unset($wordList[$wordId]);
				}else{
					unset($word['id']);
					$wordList[$wordId] = $word;
				}
			}else{
				$wordList[$newWord['id']] = $word;
			}
			$res = $redis->saveChatWord($wordList);
			if($res){
				// 修改成功, 重新生成js文件;
				if(self::$jsCreate){
					$this->createBadWordJs();
				}
// 				$zmq = $this->getZmq();
// 				$zmqWord = array('id'=>($wordId > 0 ? $wordId : $newWord['id']));
// 				if(!isset($word['status']) || $word['status'] == 1){
// 					$zmqWord = array($zmqWord['id']=>array('name'=>$word['name'],'type'=>$word['type'],'replace'=>$word['replace']));
// 				}
// 				$zmq->sendZmqMsg('609', array('type'=>'update_bad_word','uid'=>$uid, 'json_info'=>$zmqWord));
			}
		}
		
		if($flag){
			if($this->isAdminAccessCtl()){
				$id = $wordId > 0 ? $wordId : $newWord['id'];
				if($ordBlackWord && $wordId >0){
					$op_desc = '编辑 聊天敏感词('.$word['name'].') ID('.$id.')';
				}else{
					$op_desc = '新增 聊天敏感词('.$word['name'].') ID('.$id.')';
				}
				$this->saveAdminOpLog($op_desc);
			}
		}
		return $flag;
	}
	
	/**
	 * 获取全部聊天敏感词
	 * @param unknown_type $getArray
	 * @return mixed|unknown
	 */
	public function getChatWord($getArray = false)
	{
		$redis = new OtherRedisModel();
		$wordList = $redis->getChatWord();
		if($wordList){
			if($getArray){
				return json_decode($wordList, true);
			}
		}else{
			$blackWord = new BlackWordModel();
			$res = $blackWord->getChatWordList(WORD_TYPE_CHAT, 0, 0, true);
			$wordList = $this->arToArray($res['list']);
			$wordList = $this->buildDataByIndex($wordList, 'id');
			$word = array();
			foreach($wordList as $k=>$v){
				$_tmp['name'] = $v['name'];
				$_tmp['type'] = $v['type'];
				$_tmp['replace'] = $v['replace'];
				$word[$v['id']] = $_tmp;
			}
			$redis->saveChatWord($word);
			if(self::$jsCreate){
				$this->createBadWordJs();
			}
		}
		return $wordList;
	}
	
	public function createBadWordJs()
	{
		self::$jsCreate = false;		// 防止递归死循环;
		$words = $this->getChatWord(true);
		foreach($words as $k=>$v){
			$_word[] = $v;
		}
		$json_str = 'var word = ' . json_encode($_word) . ';';
		$filePath = IMAGES_PATH . 'supply'.DIR_SEP.'create_js'.DIR_SEP.'sensWord.js';
		file_put_contents($filePath, $json_str);
	}
	
	/**
	 * 根据条件获取聊天敏感词
	 * @param array $attribute
	 */
	public function getChatWordByAttribute(array $attribute = array())
	{
		if(count($attribute)<=0){
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		}
		$chatWrodModel = new BlackWordModel();
		$res = $chatWrodModel->findByAttributes($attribute);
		if($res){
			return $res->attributes;
		}
		return false;
	}
	
	/**
	 * 获取昵称敏感词
	 */
	public function getNickNameBadWord()
	{
		$blackWordModel = new BlackWordModel();
		$attributes = array('word_type'=>1, 'status'=>1);
		$count = $blackWordModel->countByAttributes($attributes);
		$res = $blackWordModel->findAllByAttributes($attributes);
		$data = array();
		if($res){
			foreach($res as $v){
				$data[$v['id']] = $v->attributes;
			}
		}
		return array('count'=>$count, 'list'=>$data);
	}
	
	/**
	 * 敏感词过滤
	 * @author leiwei
	 * @param string $content  过滤的内容
	 * @param boolean $is_editor 是否来自富文本编辑器, 富文本编辑器需要html标签
	 * @return string
	 */
	public function wordFilter($content, $is_editor = false){
		$wordList=$this->getChatWord(true);
		$_words = array();
		$search = array (
			"'<script[^>]*?>.*?</script>'si",          
			"'([\r\n])[\s]+'",                
			"'&(quot|#34);'i",                
			"'&(amp|#38);'i",
			"'&(lt|#60);'i",
			"'&(gt|#62);'i",
			"'&(nbsp|#160);'i",
			"'&(iexcl|#161);'i",
			"'&(cent|#162);'i",
			"'&(pound|#163);'i",
			"'&(copy|#169);'i",
			"'&#(\d+);'e");
		$replace = array (
			"",
			"\\1",
			"\"",
			"&",
			"<",
			">",
			" ",
			chr(161),
			chr(162),
			chr(163),
			chr(169),
			"chr(\\1)");
		if(!$is_editor){
			$search[] = "'<[\/\!]*?[^<>]*?>'si";
			$replace[] = "";
		}
		
		foreach($wordList as $key=>$row){
			$_tmp['type'] =  $row['type'];
			$_tmp['name'] =  str_replace('*','(.*)',$row['name']);
			$_tmp['replace'] =  $row['replace'];
			$_words[] = $_tmp;
		}
		$content=preg_replace ($search, $replace, $content);
		$matches=array();
		foreach($_words as $row){
			if($row['type']==0){
				$content=str_replace($row['name'], $row['replace'], $content);
			}elseif($row['type']==1){
				if(preg_match('/'.$row['name'].'/i', $content)){
			 		$content=$row['replace'];
			 	}
				
			}
		}
		return $content;
	}
}

?>