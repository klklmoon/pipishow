<?php
/**
 * 皮皮乐天独有的解析服务层
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: ParseService.php 9671 2013-05-06 13:51:21Z suqian $ 
 * @package
 */
class ParseService extends PipiService {

	/**
	 * 解析@功能
	 * 
	 * @param string $content
	 * @return string
	 */
	public function parseAt($content,array &$atUserList = array()){
		
		return $content;
	}
	
	/**
	 * 解析表情
	 * @param string $content
	 * @return string
	 */
	public function parseFace($content){
		return $content;
	}
	
	/**
	 * 解析ＵＲＬ
	 * 
	 * @param string $content
	 * @return string
	 */
	public function parseUrl($content){
		return $content;
	}
	
	/**
	 * 解析内容
	 * 
	 * @param string $content
	 * @return string
	 */
	public function parseVideo($content){
		return $content;
	}
}

?>