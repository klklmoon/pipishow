<?php

/**
 * 皮皮乐天文件操作
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <aoxue.1988.su.qian@163.com>
 * @version $Id: PipiFile.php 8404 2013-04-03 03:06:37Z suqian $ 
 * @package componets
 */
class PipiFile extends CComponent{

	/**
	 * @var string 写入目录
	 */
	public static $directory = 'data.caches.categories';
	
	/**
	 * @var string 文件后缀
	 */
	public static $suffix = '.php';
	
	/**
	 * @var string 以读的方式打开文件
	 */
	const R = 'rb';

	/**
	 * @var string 以读写的方式打开文件，具有较强的平台移植性
	 */
	const RB = 'rb+';

	/**
	 * @var string 以写的方式打开文件
	 */
	const W = 'wb';

	/**
	 * @var string 以读写的方式打开文件
	 */
	const WRB = 'wb+';

	/**
	 * @var string 以追加写入方式打开文件，具有较强的平台移植性
	 */
	const AR = 'ab';

	/**
	 * @var string 以追加读写入方式打开文件，具有较强的平台移植性
	 */
	const ARB = 'ab+';
	
	/**
	 * 写文件
	 * 
	 * @param string $fileName 文件绝对路径
	 * @param string $data 数据
	 * @param string $method 读写模式
	 * @param bool $ifLock 是否锁文件
	 * @param bool $ifChmod 是否将文件属性改为可读写
	 * @return int 返回写入的字节数
	 */
	public static function write($fileName, $data, $method = self::ARB, $ifLock = true, $ifChmod = true) {
		$fileName = Yii::getPathOfAlias(self::$directory).'/'.$fileName.self::$suffix;
		touch($fileName);
		if (!$handle = fopen($fileName, $method))
			 return false;
		$ifLock && flock($handle, LOCK_EX);
		$writeCheck = fwrite($handle, $data);
		($method == self::ARB || $method == self::AR) && ftruncate($handle, strlen($data));
		fclose($handle);
		$ifChmod && chmod($fileName, 0777);
		return $writeCheck;
	}
	
	/**
	 * 读文件
	 *
	 * @param string $fileName 文件绝对路径
	 * @param string $method 读取模式
	 * @return string
	 */
	public static function read($fileName, $method = self::R) {
		if (false !== ($handle = fopen($fileName, $method))) {
			flock($handle, LOCK_SH);
			$data = fread($handle, filesize($fileName));
			fclose($handle);
		}
		return $data;
	}
	
	/**
	 * 存储PHP格式的文件
	 * @param string $fileName
	 * @param mixed $data
	 * @return string
	 */
	public static function phpData($fileName,$data){
		$string = "<?php\r\n return " ;
		if(is_array($data))
			$string .= var_export($data,true);
		else 
			$string .= $data;
		$string .= ';';
		return self::write($fileName, $string,self::W);
	}
}

?>