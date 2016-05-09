<?php
/**
 * 皮皮乐天安全类库
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <aoxue.1988.su.qian@163.com>
 * @version $Id: PipiSecurity.php 8404 2013-04-03 03:06:37Z suqian $ 
 * @package componets
 */
class PipiSecurity extends CComponent {

	/**
	 * 获取安全的文本
	 * @param string $content
	 * @return string
	 */
	public static function securityText($content,$br = true){
		if(empty($content)) 
			return '';
			
		if(is_int($content))
			return (string)$content;
		
		if(is_object($content))
			$content = (string)$content;
			
		if($br)
			$content = nl2br($content);
		
		return self::convertString(self::escape($content));		
	}
	
	/**
	 * 去除标签
	 * @param string $content
	 * @param string $allowTag
	 * @return string
	 */
	public static function stripTags($content,$allowTag = ''){
		return strip_tags($content,$allowTag);
	}
	
	/**
	 * 转义字符
	 * @param string $content
	 * @return string
	 */
	public static function escape($content){
		return htmlspecialchars($content,ENT_QUOTES,'UTF-8');
	}
	
	/**
	 * 去除反斜线
	 * @param string $content
	 * @return string
	 */
	public static function stripSlashes($content){
		return stripslashes($content);
	}
	
	/**
	 * 字符转换
	 * @param string $string
	 * @return string
	 */
	public static function convertString($string) {
		$replace = array(
			"\0" => '', 
			"%00" => '', 
			"\t" => '    ', 
			'  ' => '&nbsp;&nbsp;', 
			"\r" => '', 
			"\r\n" => '',
			"\n" => '', 
			"%3C" => '&lt;', 
			'<' => '&lt;', 
			"%3E" => '&gt;', 
			'>' => '&gt;', 
			'"' => '&quot;', 
			"'" => '&#39;',
			'%22'=>'&quot;',
			'%27'=>'&#39;',
		);
		
		return preg_replace(array('/[\\x00-\\x08\\x0B\\x0C\\x0E-\\x1F]/', '/&(?!(#[0-9]+|[a-z]+);)/is'), array('', 
			'&amp;'),  strtr($string, $replace));
	}
	
	/**
	 * 保持输入是整数
	 * @param string $string
	 * @return string
	 */
	public static function convertInt($string){
		return number_format($string,0,'','');
	}
	
	
	/**
	 * 过滤路径
	 * @param string $path
	 * @return string
	 */
	public static function filterPath($path){
		$filter =  array(
			"'" => '', 
			'"'=>'',
			'#' => '', 
			'=' => '', 
			'`' => '', 
			'$' => '', 
			'%' => '', 
			'&' => '', 
			';' => '',
			'^'=>'',
			'..'=>'',
			'://'=>'',
			"\0"=>''
		);
		return rtrim(preg_replace('/(\/){2,}|(\\\){1,}/', '/', strtr($path,$filter)), '/');
	}
}

