<?php
/** 这个文件的目的是使上线的配置环境在不同的app下统一配置，从而使上线发布流程从本地到test再到beta再到online
 * 不同环境的代码不需要每次都到不同机器上的不同app下修改index.php文件
 * 如需本地开发需要不同的app的环境，也可以独个修改某个需要的index.php，但请不要提交，保证svn里代码始终是统一的环境
 * @author He xin <hexin@pipi.cn> 2013-6-3
 * @link http://show.pipi.cn
 * @copyright Copyright &copy; 2003-2010 show.pipi.cn
 * @license
 */
if(!defined('DEV_ENVIRONMENT')){
	define('DEV_ENVIRONMENT','local');
}