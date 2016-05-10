<?php
/**
 * 第三方应用管理服务层
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: AppService.php 9671 2013-05-06 13:51:21Z suqian $ 
 * @package
 */
class AppService extends PipiService {

	/**
	 * 存储App信息
	 * 
	 * @param array $app
	 * @return array
	 */
	public function saveApp(array $app){
		if(empty($app)){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		
		$appModel = new AppModel();
		if(isset($app['app_id'])){
			$orgAppModel = $appModel->findByPk($app['app_id']);
			if(empty($orgAppModel)){
				return $this->setNotice('user',Yii::t('user','The app does not exist'),0);
			}
			if(isset($app['app_secret']) && $app['app_secret'] != ''){
				$app['app_secret'] = $this->buildAppSecret();
			}
			$this->attachAttribute($orgAppModel,$app);
			if(!$orgAppModel->validate()){
				return $this->setNotices($orgAppModel->getErrors(),0);
			}
			$orgAppModel->save();
		}else{
			if(!isset($app['app_secret'])){
				$app['app_secret'] = $this->buildAppSecret();
			}
			
			$this->attachAttribute($appModel,$app);
			$appModel->create_time = time();
			if(!$appModel->validate()){
				return $this->setNotices($appModel->getErrors(),0);
			}
			$appModel->save();
		}
		return $appModel->getPrimaryKey();
	}
	
	/**
	 * 为APP　创建token信息
	 * 
	 * @param array $token
	 * @return array
	 */
	public function createAppToken(array $token){
		if((isset($token['uid']) && $token['uid'] <= 0) || empty($token) || $token['app_id'] <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),array());
		}
		
		$tokenModel = new AppTokenModel();
		$this->attachAttribute($tokenModel,$token);
		$tokenModel->save();
		return $token;
	}
	
	/**
	 * 按APP 标识取得APP信息
	 * 
	 * @param int $appId
	 * @return array
	 */
	public function getAppInfoById($appId){
		if($appId <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),array());
		}
		return AppModel::model()->findByPk($appId)->attributes;
	}
	
	/**
	 * 按APP英文名取得APP信息
	 * 
	 * @param string $enname app英文名
	 * @return array
	 */
	public function getAppInfoByEname($enname){
		if(empty($enname)){
			return $this->setError(Yii::t('common','Parameter is empty'),array());
		}
		return AppModel::model()->findByAttributes(array('app_enname'=>$enname))->attributes;
	}
	
	/**
	 * 取得有效的token信息
	 * 
	 * @param int $uid 用户ＩＤ
	 * @param int  $appId APPID
	 * @param strng $token token值
	 * @return array
	 */
	public function getAppTokenByUid($uid,$appId,$token){
		if($uid <= 0 || $appId<=0 || empty($token)){
			return $this->setError(Yii::t('common','Parameter is empty'),array());
		}
		$attribute['uid'] = $uid;
		$attribute['app_id'] = $appId;
		$attribute['token'] = $token;
		$tokenModel = new AppTokenModel();
		$model = $tokenModel->findByAttributes($attribute);
		if(empty($model)){
			return array();
		}
		return $model->attributes;
	}
	
	/**
	 * 取得有效的token信息
	 * 
	 * @param int $appId APPID
	 * @param strng $token token值
	 * @return array
	 */
	public function getAppTokenByAppId($appId,$token){
		if(empty($appId) || empty($token)){
			return $this->setError(Yii::t('common','Parameter is empty'),array());
		}
		$attribute['app_id'] = $appId;
		$attribute['token'] = $token;
		$tokenModel = new AppTokenModel();
		$model = $tokenModel->findByAttributes($attribute);
		if(empty($model)){
			return array();
		}
		return $model->attributes;
	}
	
	/**
	 * 生成一个app secret
	 * 
	 * @return string 返回secret 
	 */
	public function buildAppSecret(){
		return md5(md5(microtime(true)).uniqid());
	}
}

