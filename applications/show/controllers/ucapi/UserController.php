<?php
/**
 * 用户相关的控制层，用来替代ucenter，
 * 目前安全上的解决办法：
 * 1.每个请求都经过签名认证
 * 2.本方法都仅限线上内部的20台机器内部局域网访问，不提供公网访问方式
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <aoxue.1988.su.qian@163.com>
 * @version $Id: PublicController.php 8317 2013-03-29 01:19:47Z suqian $ 
 * @package 
 */
class UserController extends PipiController {
	
	/**
	 * 每个请求都经过签名认证
	 * @see CController::beforeAction()
	 */
	public function beforeAction($action){
		$data = $_REQUEST;
		unset($data['r']);
		$time = intval(Yii::app()->request->getParam('request_time'));
		$verify = Yii::app()->request->getParam('request_verify');
		unset($data['request_time']);
		unset($data['request_verify']);
		if(empty($time) || empty($verify)){
			$this->renderToJson(-98, '签名不能为空');
		}
		if(time() - $time > 60 * 10){
			$this->renderToJson(-97, '超时');
		}
		$str = '';
		foreach($data as $key => $post){
			if(is_array($post)) $post = serialize($post);
			$str .= "$post";
		}
		$md5 = md5($str.$time.Yii::app()->params['verification_code']);
		if($md5 != $verify){
			$this->renderToJson(-99, '签名验证不通过');
		}
		return true;
	}
	
	public function actionLogin(){
		$username = Yii::app()->request->getPost('username');
		$password = Yii::app()->request->getPost('password');
		if(empty($username) || empty($password)){
			$this->renderToJson(-1, '用户名密码不能为空');
		}
		$identity = new PipiUserIdentity($username,$password);
		var_dump($identity);exit;
	    if($identity->authenticate()){
			$this->renderToJson(-2, '错误的用户名或密码');
		}else{
			Yii::app()->user->login($identity);
			$userService = new UserService();
			$user = $userService->getUserBasicByUids(array(Yii::app()->user->id));
			$this->renderToJson(Yii::app()->user->id, '登陆成功', array('email' => $user[Yii::app()->user->id]['reg_email']));
		}
	}
	
	public function actionCheckUserName(){
		$username = Yii::app()->request->getPost('username');
		if(empty($username)){
			$this->renderToJson(-1, '用户名不能为空');
		}
		$userService = new UserService();
		$users = $userService -> getUserBasicByUserNames(array($username));
		$users = $userService -> buildDataByIndex($users, 'username');
		if(isset($users[$username])){
			$this->renderToJson(-3, '用户名已存在');
		}else{
			//@todo 禁用词过滤
			$this->renderToJson(1, '用户名不存在');
		}
	}
	
	public function actionCheckNickName(){
		$nickname = Yii::app()->request->getPost('nickname');
		if(empty($nickname)){
			$this->renderToJson(-1, '昵称不能为空');
		}
		$userService = new UserService();
		$users = $userService -> getUserBasicByUserNames(array($nickname));
		$users = $userService -> buildDataByIndex($users, 'nickname');
		if(isset($users[$nickname])){
			$this->renderToJson(-3, '昵称已存在');
		}else{
			//@todo 禁用词过滤
			$this->renderToJson(1, '昵称不存在');
		}
	}
	
	public function actionRegister(){
		$username = Yii::app()->request->getPost('username');
		$password = Yii::app()->request->getPost('password');
		$nickname = Yii::app()->request->getPost('nickname');
		if(empty($username) || empty($password) || empty($nickname)){
			$this->renderToJson(-1, '用户名密码昵称不能为空');
		}
	
		$user['nickname'] = $nickname;
		$user['username'] = $username;
		$user['password'] = $password;
		$user['user_type'] = 1;
		
		$userService = new UserService();
		$userService->saveUserBasic($user);
		if($userService->getNotice()){
			$notices = $userService->getNotice();
			$this->renderToJson(-1, array_pop($notices));
		}else{
			$identify = new PipiUserIdentity($username,$password);
			if($identify->authenticate()){
				Yii::app()->user->login($identify,3600);
				$this->renderToJson(Yii::app()->user->id, '注册成功');
			}else{
				$this->renderToJson(-4, $identify->errorMessage);
			}
		}
	}
	
	public function actionEditNickName(){
		$uid = Yii::app()->request->getPost('uid');
		$nickname = Yii::app()->request->getPost('nickname');
		if(empty($uid) || empty($nickname)){
			$this->renderToJson(-2, '昵称不能为空');
		}
		$userService = new UserService();
		if($userService -> saveUserNickname($uid, $nickname)){
			//@todo 禁用词过滤
			$this->renderToJson($uid, '昵称修改成功');
		}else{
			$this->renderToJson(-1, '昵称修改失败');
		}
	}
	
	public function actionEditPassword(){
		$username = Yii::app()->request->getPost('username');
		$current  = Yii::app()->request->getParam('current');
		$password = Yii::app()->request->getParam('password');
		if(empty($username) || empty($current) || empty($password)){
			$this->renderToJson(-2, '用户名密码不能为空');
		}
		
		$userService = new UserService();
		$userInfo = $userService->getUserBasicByUserNames(array($username));
		if($userInfo){
			$user = array_pop($userInfo);
			$current = $userService->encryPassword($current, $user['reg_salt']);
			if($current != $user['password']){
				$this->renderToJson(-1, '原密码错误');
			}else{
				$userInfoChange = array('uid'=>$user['uid'], 'password'=>$password);
				$newUserInfo = $userService->saveUserBasic($userInfoChange);
				if($newUserInfo['password']==$userService->encryPassword($password,$user['reg_salt'])){
					$this->renderToJson(1, '密码修改成功');
				}else{
					$this->renderToJson(-7, '修改密码失败');
				}
			}
		}else{
			$this->renderToJson(-3, '用户名不存在');
		}
	}
	
	/**
	 * 批量更新某些用户由于更新数据库成功后需要再更新userInfo中皮蛋值的redis情况
	 */
	public function actionUpdatePipiEgg(){
		$uids = Yii::app()->request->getPost('uid');
		if(!is_array($uids)) $this->renderToJson(-2, '参数错误');
		$update = array('pipiegg' => true);
		$consumeService = new ConsumeService();
		$result = false;
		foreach($uids as $uid){
			$result = $consumeService->updateUserJsonInfo($uid, $update);
		}
		if($result){
			$this->renderToJson(1, '更新成功');
		}else{
			$this->renderToJson(-1, '更新失败');
		}
	}
}

?>