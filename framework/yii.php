<?php
/**
 * Yii bootstrap file.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2008-2011 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 * @package system
 * @since 1.0
 */

require(dirname(__FILE__).'/YiiBase.php');

/**
 * Yii is a helper class serving common framework functionalities.
 *
 * It encapsulates {@link YiiBase} which provides the actual implementation.
 * By writing your own Yii class, you can customize some functionalities of YiiBase.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @package system
 * @since 1.0
 */
class Yii extends YiiBase
{
	/**
	 * 获取Key的配置
	 * 
	 * @param string $key
	 * @param string $subKey
	 * @return mixed
	 */
	public static function getKeyConfig($key,$subKey = ''){
		global $keyConfig;
		if(empty($key) || empty($keyConfig)){
			return Yii::log(Yii::t('common','{config} config is empty',array('{config}'=>'(redis login key)')),CLogger::LEVEL_ERROR);
		}
		$config = $keyConfig[$key];
		return $subKey ? $config[$subKey] : $config;
	}
}
