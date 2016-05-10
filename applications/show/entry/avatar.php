<?php
/**
 * @author Su qian <suqian@pipi.cn> 2013-4-17
 * @link http://show.pipi.cn
 * @copyright Copyright &copy; 2003-2110 show.pipi.cn
 * @license 
 */

/**
 * @var string 定义程序运行环境为单元测试
 */
define('DEV_ENVIRONMENT','local');

/**
 * @var string 定义程序访问入口为单元测试
 */
define('ACCESS_ENTRANCE','other');

/**
 * @var string 定义系统
 */
define('SYSTEM_NAME','tests');

require_once '../../../lib/core/bootStrap.php';
$uid = Yii::app()->request->getParam('uid');
$input = Yii::app()->request->getParam('input');
$step = Yii::app()->request->getParam('a');
$type = Yii::app()->request->getParam('avatartype');
$isLogin =(int) !Yii::app()->user->isGuest ;
$uid = $uid ? $uid : Yii::app()->user->id;
if(!$isLogin || Yii::app()->user->id != $uid){
	echo '您还没有登录哦！';
	Yii::app()->request->redirect('index.php?r=user/login');
	die();
}
$upload = new PipiFlashUpload();
if ($upload->processRequest($input,$step) ) {
	exit();
}


$urlAvatarBig    = $upload->getFileUrl($uid , 'big' );
$urlAvatarMiddle = $upload->getFileUrl( $uid, 'middle' );
$urlAvatarSmall  = $upload->getFileUrl( $uid, 'small' );
$urlCameraFlash = $upload->renderHtml( $uid );


?>
<script type="text/javascript">
function updateavatar() {
	window.location.reload();
}
</script>
<img src="<?php echo $urlAvatarBig ?>">
<img src="<?php echo $urlAvatarMiddle ?>">
<img src="<?php echo $urlAvatarSmall ?>">
<hr>
<?php  echo $urlCameraFlash ?>

