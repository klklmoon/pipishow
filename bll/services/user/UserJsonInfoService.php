<?php
/**
 * 系统使用频繁的user_info数据的服务层
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: UserJsonInfoService.php 15588 2013-09-24 06:52:35Z hexin $ 
 * @package service
 * @subpackage user
 */
class UserJsonInfoService extends PipiService{
	private static $instance;
	
	/**
	 * 返回UserJsonInfoService对象的单例
	 * @return UserJsonInfoService
	 */
	public static function getInstance(){
		if(self::$instance == null){
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	/**
	 * 存储user_info信息，可用来新增或部分更新
	 * @param int $uid 用户id
	 * @param array $data user_info标准数据，部分属性即可，参考wiki上user_info文档，需要删除某个key的时候，传入array($key => array()) 或 array($key => null) 或 array($key => '')即可
	 * @return boolean
	*/
	public function setUserInfo($uid, $data){
		//更新userInfo处加入该userInfo是否存在的判断，避免更新的有不完整的userInfo，初始化userInfo在python代码里
		$userInfo = $this->getUserInfo($uid, false);
		if(empty($userInfo)) return false;
		//对已产生的不完整userInfo做删除操作
		if(!isset($userInfo['uid'])){
			$this->deleteUserInfoKey($uid);
			return false;
		}
		
		$userInfoModel = UserJsonInfoRedisModel::model();
		return $userInfoModel->setUserInfo($uid, $data);
	}

	/**
	 * 获取完整的user_info信息
	 * @param int $uid 用户id
	 * @param bool $returnJson 是否返回json串
	 * @return json | array
	 */
	public function getUserInfo($uid, $returnJson = true){
		$userInfoModel = UserJsonInfoRedisModel::model();
		$json = $userInfoModel->getUserInfo($uid);
		if(empty($json) || $json == '{}'){
			return $returnJson ? "{}" : array();
		}
		return $returnJson ? $json : json_decode($json, true);
	}
	
	/**
	 * 批量获取user_info
	 * @param array $uids
	 * @param bool $returnJson
	 * @return array
	 */
	public function getUserInfos(array $uids, $returnJson = true){
		$uids = array_values($uids);
		$tmp = UserJsonInfoRedisModel::model()->getUserInfos($uids);
		$jsons = array();
		foreach($tmp as $k => $json){
			$jsons[$uids[$k]] = $returnJson ? $json : json_decode($json, true);
		}
		return $jsons;
	}
	
	/**
	 * 删除user_info的某部分key的信息
	 * @param int $uid
	 * @param array|string $keys = array(key, key, ...) | key 其中key是user_info定义好的熟悉key，请参考wiki
	 * @return boolean
	 */
	public function deleteUserInfo($uid, $keys){
		if(empty($keys)) return false;
		$keys = is_array($keys) ? $keys : array($keys);
		$userInfoModel = UserJsonInfoRedisModel::model();
		$property = $userInfoModel->getProperty();
		foreach($keys as $k => $key){
			if(in_array($key, $property)){
				$userInfo[$key] = null;
			}else{
				unset($keys[$k]);
			}
		}
		if(empty($keys)) return false;
		else return $this->setUserInfo($uid, $userInfo);
	}
	
	/**
	 * 删除某用户的user_info
	 * @param int $uid
	 * @return boolean
	 */
	public function deleteUserInfoKey($uid){
		return UserJsonInfoRedisModel::model()->deleteUserInfos(array(intval($uid)));
	}
	
	/**
	 * 批量删除某些用户的user_info
	 * @param array $uids
	 * @return boolean
	 */
	public function deleteUserInfoKeys(array $uids){
		return UserJsonInfoRedisModel::model()->deleteUserInfos($uids);
	}
}