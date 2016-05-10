<?php

/**
 * 微博服务层
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: leiwei $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: WeiboService.php 17312 2014-01-08 08:13:19Z leiwei $ 
 * @package service
 */
class WeiboService extends PipiService {

	
	/**
	 * 存储用户微博相关统计
	 * 
	 * @param array $statistics 统计信息
	 * @return boolean
	 */
	public function  saveUserWeiboStatistics(array $statistics){
		if(($uid=$statistics['uid']) <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		
		$userWeiboStaisModel = new UserWeiboStatisticsModel();
		$_userStaticsModel = $userWeiboStaisModel->findByPk($uid);
		if(empty($_userStaticsModel )){
			$this->attachAttribute($userWeiboStaisModel,$statistics);
			$flag = $userWeiboStaisModel->save();
		}else{
			unset($statistics['uid']);
			$flag=$userWeiboStaisModel->updateCounters($statistics,'uid = '.$uid);
		}
		return $flag;
	}
	
	/**
	 * 关注用户
	 * 
	 * @param int $uid　被关注者
	 * @param int $fansUid　关注者
	 * @return boolean
	 */
	public function attentionUser($uid,$fansUid){
		if($uid <= 0 || $fansUid <= 0 || $uid === $fansUid){
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		}
		$weiboFansModel = new WeiboFansModel();
		$_fans = $weiboFansModel->findByAttributes(array('uid'=>$uid,'fans_uid'=>$fansUid));
		if($_fans){
			return $this->setNotice('weibo_attention',Yii::t('weibo','You have been concerned about the user'),false);
		}
		$weiboFansModel->uid = $uid;
		$weiboFansModel->fans_uid = $fansUid;
		$weiboFansModel->create_time = time();
		if($weiboFansModel->save()){
			$this->saveUserWeiboStatistics(array('uid'=>$uid,'fans'=>1));
			$this->saveUserWeiboStatistics(array('uid'=>$fansUid,'attentions'=>1));
			return $this->setNotice('weibo_attention',Yii::t('weibo','concerned success'),true);
		}
		return $this->setNotice('weibo_attenion',Yii::t('weibo','concerned faield'),false);
	}
	
	/**
	 * 关注主播
	 * 
	 * @param int $uid　被关注者
	 * @param int $fansUid　关注者
	 * @return boolean
	 */
	public function attentionDotey($uid,$fansUid){
		if($uid <= 0 || $fansUid <= 0 || $uid === $fansUid){
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		}
		$weiboDoteyFansModel = new WeiboDoteyFansModel();
		$_fans = $weiboDoteyFansModel->findByAttributes(array('uid'=>$uid,'fans_uid'=>$fansUid));
		if($_fans){
			return $this->setNotice('weibo_attention',Yii::t('weibo','You have been concerned about the user'),false);
		}
		$weiboDoteyFansModel->uid = $uid;
		$weiboDoteyFansModel->fans_uid = $fansUid;
		$weiboDoteyFansModel->create_time = time();
		if($weiboDoteyFansModel->save()){
			return $this->setNotice('weibo_attention',Yii::t('weibo','concerned success'),true);
		}
		return $this->setNotice('weibo_attenion',Yii::t('weibo','concerned faield'),false);
	}
	
	/**
	 * 取消关注
	 * 
	 * @param int $uid　被关注者
	 * @param int $fansUid　 关注者
	 * @return int
	 */
	public function cancelAttentionedUser($uid,$fansUid){
		if($uid <= 0 || $fansUid <= 0 || $uid === $fansUid){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$weiboFansModel =  WeiboFansModel::model();
		$flag = $weiboFansModel->deleteByPk(array('uid'=>$uid,'fans_uid'=>$fansUid));
		if($flag){
			$this->saveUserWeiboStatistics(array('uid'=>$uid,'fans'=>-1));
			$this->saveUserWeiboStatistics(array('uid'=>$fansUid,'attentions'=>-1));
		}
		return $flag;
	}
	
	/**
	 * 取消主播关注
	 * 
	 * @param int $uid　被关注者
	 * @param int $fansUid　关注者
	 * @return int
	 */
	public function cancelDoteyAttentionedUser($uid,$fansUid){
		if($uid <= 0 || $fansUid <= 0 || $uid === $fansUid){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$weiboDoteyFansModel =  WeiboDoteyFansModel::model();
		$flag = $weiboDoteyFansModel->deleteByPk(array('uid'=>$uid,'fans_uid'=>$fansUid));
		return $flag;
	}
	
	
	/**
	 * 是否关注
	 * 
	 * @param int $uid　被关注者
	 * @param int $fansUid　关注者
	 * @return int
	 */
	public function isAttentionUser($uid,$fansUid){
		$weiboFansModel =  WeiboFansModel::model();
		return $weiboFansModel->findByPk(array('uid'=>$uid,'fans_uid'=>$fansUid));
	}
	
	/**
	 * 是否关注该主播
	 * 
	 * @param int $uid　被关注者
	 * @param int $fansUid　关注者
	 * @return int
	 */
	public function isAttentionDotey($uid,$fansUid){
		$weiboFansModel =  WeiboDoteyFansModel::model();
		$attentions = $weiboFansModel->findByPk(array('uid'=>$uid,'fans_uid'=>$fansUid));
		if(empty($attentions)){
			return array();
		}
		return $attentions->attributes;
	}
	/**
	 * 发微博
	 * 
	 * @param array $weibo　微博数据
	 * @return int 返回新微博ＩＤ
	 */
	public function sendWeibo(array $weibo){
		
	}
	
	
	/**
	 * 评论微博
	 * 
	 * @param array $comment
	 * @return int 返回评论ＩＤ
	 */
	public function commentWeibo(array $comment){
		
	}
	
	/**
	 * 赞美微博
	 * 
	 * @param int $uid　赞美人
	 * @param int $weiboId　微博ＩＤ
	 * @return boolean
	 */
	public function praiseWeibo($uid,$weiboId){
		
	}
	
	/**
	 * 取得用户微博的配置
	 * 
	 * @param array $uids
	 * @return array
	 */
	public function getWeiboConfigByUids(array $uids){
		
	}
	
	/**
	 * 根据用户uid获取微博的统计数据
	 * @author leiwei
	 * @param int $uid
	 * @return array
	 */
	public function getWeiboStatisticsByUid($uid){
		if($uid<=0) 
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		$userWeiboStatisticsModel=new UserWeiboStatisticsModel();
		$data=$userWeiboStatisticsModel->getWeiboStatisticsByUid($uid);
		if($data){
			return $data->attributes;
		}
		return array('weibos'=>'0','attentions'=>0,'fans'=>0);
	}
	
	/**
	 * 获取用户的粉丝
	 * 
	 * @param int $uid
	 * @return array
	 */
	public function getUserFansByUid($uid){
		if($uid <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		}
		$weiboFansModel =  WeiboFansModel::model();
		$userFans = $weiboFansModel->getUserFansByUid($uid);
		if($userFans){
			return $this->arToArray($userFans);
		}
		return array();
	}
	
	/**
	 * 获取主播的粉丝
	 * 
	 * @param int $uid
	 * @return array
	 */
	public function getDoteyFansByUid($uid, $offset = 0, $limit = 'all'){
		if($uid <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		}
		$weiboFansModel =  WeiboDoteyFansModel::model();
		$userFans = $weiboFansModel->getDoteyFansByUid($uid, $offset, $limit);
		if($userFans){
			return $this->arToArray($userFans);
		}
		return array();
	}
	
	/**
	 * 获取用户的关注数
	 * 
	 * @param int $uid
	 * @return array
	 */
	public function getUserAttentionsByUid($uid){
		if($uid <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		}
		$weiboFansModel =  WeiboFansModel::model();
		$userFans = $weiboFansModel->getUserAttentionsByUid($uid);
		if($userFans){
			return $this->arToArray($userFans);
		}
		return array();
	}
	
	public function getUserAttentionsByCondition(array $condition){
		$weiboFansModel =  WeiboFansModel::model();
		$userFans = $weiboFansModel->getUserAttentionsByCondition($condition);
		if($userFans[0]){
			$userFans[0] = $this->arToArray($userFans[0]);
		}
		return $userFans;
	}
	
	public function getUserFansByCondition(array $condition){
		$weiboFansModel =  WeiboFansModel::model();
		$userFans = $weiboFansModel->getUserFansByCondition($condition);
		if($userFans[0]){
			$userFans[0] = $this->arToArray($userFans[0]);
		}
		return $userFans;
	}
	/**
	 * 获取主播的关注数
	 * 
	 * @param int $uid
	 * @return array
	 */
	public function getDoteyAttentionsByUid($uid){
		if($uid <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		}
		$weiboFansModel =  WeiboDoteyFansModel::model();
		$userFans = $weiboFansModel->getDoteyAttentionsByUid($uid);
		if($userFans){
			return $this->arToArray($userFans);
		}
		return array();
	}
	
	/**
	 * 获取主播的关注数
	 * 
	 * @param int $uid
	 * @return array
	 */
	public function getDoteyAttentionsByCondition(array $condition){
		$weiboFansModel =  WeiboDoteyFansModel::model();
		$userFans = $weiboFansModel->getDoteyAttentionsByCondition($condition);
		if($userFans[0]){
			$userFans[0] = $this->arToArray($userFans[0]);
		}
		return $userFans;
	}
	/**
	 * 统计有多少人关注主播
	 */
	public function countDoteyFans($doteyIds)
	{
		if($doteyIds <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$weiboFansModel =  new WeiboDoteyFansModel();
		$res = $weiboFansModel->countDoteyFans($doteyIds);
		return $this->buildDataByIndex($res, 'uid');
	}
	
	/**
	 * 获取主播的关注
	 * 
	 * @param int $uid
	 * @param array $attentionIds
	 * @return array
	 */
	public function getPointDoteyAttentionsByUid($uid,array $attentionIds){
		if($uid <= 0 || empty($attentionIds)){
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		}
		$weiboFansModel =  WeiboDoteyFansModel::model();
		$attentions = $weiboFansModel->getPointDoteyAttentionsByUid($uid,$attentionIds);
		if($attentions){
			return $this->buildDataByIndex($this->arToArray($attentions),'uid');
		}
		return array();
	}
	
	/**
	 * 统计用户对主播的贡献值排行榜
	 * @edit by guoshaobo
	 * @param unknown_type $dotey_id
	 * @return mix|Ambigous <multitype:, mixed>
	 */
	public function countDoteyCharmPointsBuSendUid($dotey_id, $offset = 0, $limit = 10)
	{
		if($dotey_id <= 0 ){
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		}
		$doteyModel = new DoteyCharmRecordsModel();// DoteyCharmPointRecordsModel();
		return $doteyModel->countDoteyCharmBuSendUid($dotey_id, $offset, $limit);
	}
	
	/**
	 * 获取用户的微博
	 * 
	 * @param array $uid 用户ＩＤ
	 * @param int $type　微博类型
	 * @param array $condition　扩展条件
	 * @return array
	 */
	public function getUserWeibosByUids(array $uids,$type = null,array $condition = array()){
		
	}
	
	/**
	 * 获取用户关注的微博
	 * 
	 * @param int $uid 用户ＩＤ
	 * @param int $type　微博类型
	 * @param array $condition　扩展条件
	 * @return array
	 */
	public function getUserAttentionedWeibosByUid($uid,$type = null,array $condition = array()){
		
	}
	
	/**
	 * 获取用户管理的微博
	 * 
	 * @param int $uid 用户ＩＤ
	 * @param int $type　微博类型
	 * @param array $condition　扩展条件
	 * @return array
	 */
	public function getUserManagerWeibosByUid($uid,$type = null,array $condition = array()){
		
	}
	
	/**
	 * 获取用户家族的微博
	 * 
	 * @param int $uid 用户ＩＤ
	 * @param int $type　微博类型
	 * @param array $condition　扩展条件
	 * @return array
	 */
	public function getUserFamilyWeibosByUid($uid,$type = null,array $condition = array()){
		
	}
	
	/**
	 * 获取回应我的微博
	 * 
	 * @param int $uid 用户ＩＤ
	 * @param int $type　微博类型
	 * @param array $condition　扩展条件
	 * @return array
	 */
	public function getResponseMeWeibosByUid($uid,$type = null,array $condition = array()){
		
	}
	
	/**
	 * 随便看看　微博
	 * 
	 * @param array $conditon　查询条件
	 * @return array
	 */
	public function casualLookAtWeibos(array $conditon){
		
	}
	
	/**
	 * 到得某条微博的评论
	 * 
	 * @param int $weiboId
	 * @return array
	 */
	public function getWeiboCommentsByWeiboId($weiboId){
		
	}
	
	/**
	 * 删除某用户的微博
	 * 
	 * @param int $weiboId　微博ＩＤ
	 * @return boolean
	 */
	public function delUserWeibosByIds($weiboId){
		
	}
	
	/**
	 * 删除指定微博的的所有评论　
	 * 
	 * @param int $weiboId　微博ＩＤ
	 * @return int
	 */
	public function delUserCommentsByWeiboId($weiboId){
		
	}
	
	/**
	 * 删除某条微博的单条评论
	 * 
	 * @param array $commentIds 评论ＩＤ
	 * @return int
	 */
	public function delUserCommentsByIds(array $commentIds){
		
	}
	
	/**
	 * 取得主播粉丝排行榜
	 * 
	 * @param string $type 粉丝排行榜类型 今日 本周 本月 超级
	 * @return array
	 */
	public function getDoteyFansRank($type,$isAvatar = true){
		$keyConfig = Yii::getKeyConfig('redis','other');
		$list = array(
			'super'=>$keyConfig['dotey_fans_super_rank'],
			'new'=>$keyConfig['dotey_fans_new_rank'],
		);
	
		$type = !$type || in_array($type,array_keys($list)) ? $type : 'super';
		$redisModel = new OtherRedisModel();
		$rank = $redisModel->getDoteyFansRank($list[$type],true);
		if($isAvatar){
			$uids = array();
			foreach($rank as $_rank){
				$uids[] = $_rank['d_uid'];
			}
			$userService = new UserService();
			$avatars = $userService->getUserAvatarsByUids($uids,'small');
			foreach($rank as $key=>$_rank){
				$rank[$key]['d_avatar'] = $avatars[$_rank['d_uid']];
			}
		}
		return $rank;
	}
	
	protected function buildWeibos(PipiActiveRecord $weibos){
		
	}
	
	protected function buildComments(PipiActiveRecord  $records){
		
	}
	
	
	
}

?>