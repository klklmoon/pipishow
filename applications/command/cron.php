<?php

define('SYSTEM_NAME','command');
define('ACCESS_ENTRANCE','command');
//统一定义全局环境标识，请在configs/environment.php内统一修改，不需要在每个index.php上做配置
defined('STDIN') or define('STDIN', fopen('php://stdin', 'r'));

require_once '../../lib/core/bootStrap.php';

define('YII_DEBUG',false);