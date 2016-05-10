<?php

/**
 * 用户道具购买服务层。提供购买道具模板方法
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: leiwei $>
 * @author Su qian <aoxue.1988.su.qian@163.com>
 * @version $Id: UserBuyPropsService.php 17244 2014-01-04 07:48:11Z leiwei $ 
 * @package　service
 * @subpackage props
 */
class UserBuyPropsService extends PipiService{
	
	/**
	 * @var boolean 是否检查道具过期，默认检查
	 */
	public $isCheckExpired = true;
	
	/**
	 * @var boolean 是否检查已经购买道具，默认检查
	 */
	public $isCheckBuy = true;
	
	/**
	 * @var boolean 是 否检查永久价道具
	 */
	public $isCheckForeverPrice = true;
	/**
	 * @var boolean 是否存储使用道具使用
	 */
	public $isSavePropsUse = false;
	
	/**
	 * @var boolean 是否存储道具到用户背包
	 */
	public $isSavePropsBag = true;
	/**
	 * @var array 用户道具原始信息
	 */
	protected $props = array();
	/**
	 * @var array 用户消费属性
	 */
	protected $users = array();
	/**
	 * @var array 用户已购买的道具，如果没购买为空
	 */
	protected $exist = array();
	
	/**
	 * @var int 时间缀
	 */
	protected $timeStamp = 0;
	
	/**
	 * @var int 购买数量
	 */
	protected $num = 1;
	
	/**
	 * @var int 道具流水线记录ＩＤ
	 */
	protected $recordSid = 0;
	/**
	 * @var int 错误码
	 */
	protected $errorCode = 0;
	/**
	 * @var ConsumeService 消费服务层
	 */
	protected static $consumeService = null;
	
	/**
	 * @var PropsService　道具服务层
	 */
	protected static $propsService = null;
	
	/**
	 * @var userPropsService　用户道具服务层
	 */
	protected static $userPropsService = null;
	
	public function __construct($uid,$propId,$num = 1){
// 		if(Yii::app()->user->isGuest){
// 			return $this->errorCode =  Yii::t('user','You are not logged');
// 		}
		
		if($uid <= 0 || $propId <= 0 || $num <= 0){
			return $this->errorCode =  Yii::t('user','Parameters are wrong');
		}
		
		if(self::$consumeService == null)
			self::$consumeService = new ConsumeService();
		
		$users = self::$consumeService->getConsumesByUids($uid);
		if(empty($users)){
			return $this->errorCode =  Yii::t('user','The user does not exist');
		}
		
		if(self::$propsService == null)
			self::$propsService  = new PropsService();
			
		$props = self::$propsService ->getPropsByIds($propId,true,true);
		if(empty($props)){
			return $this->errorCode =  Yii::t('props','The props does not exist');
		}
		
		if(self::$userPropsService == null)
			self::$userPropsService = new UserPropsService();
		
		$exist = self::$userPropsService->getUserValidPropsOfBagByPropId($uid,$propId);
		if($exist){
			$this->exist = array_pop($exist);
		}
		$this->users = $users[$uid];
		$this->props = $props[$propId];
		$this->timeStamp = time();
		$this->num = $num;
	}
	
	
	/**
	 * 购买道具模板方法 
	 */
	public function buyProps(){
		
		if($this->errorCode){
			return false;
		}
		if(!$this->checkData()){
			return false;
		}
		if(!$this->isPurchased()){
			return false;
		}
		
		$price = $this->getPropsPrice();
		if($price <= 0){
			return false;
		}
		
	
		if(self::$consumeService->consumeEggs($this->users['uid'],$price)){
			//存储用户道具购买记录必须是第一步，要生产流水线记录
			$this->saveUserPropsRecords();
			$this->saveUserPipieggRecords();
			$this->saveUserDeidcationRecords();
			if($this->isSavePropsBag){
				$this->savePropsToBag();
			}
			$this->saveUserConsumeAttribute();
			$this->saveUserPropsAttribute();
			if($this->isSavePropsUse){
				$this->saveUserPropsUse();
			}
			$this->afterBuy();
			return true;
		}
		return false;
	}
	
	/**
	 * 是否可购买，比如皮蛋余额是否足购等,子类可重载
	 * 
	 * @return boolean
	 */
	public function isPurchased(){
		$price = $this->getPropsPrice();
		
		//判断购买道具余额是否足购
		if($price<=0 || ($this->users['pipiegg'] - $price < 0)){
			$this->errorCode =  Yii::t('props','You do not have a sufficient share of balance, recharge');
			return false;
		}
		
		//判断用户是否具有购买权限
/* 		if($this->props['rank'] && $this->users['rank'] < $this->props['rank']){
			$this->errorCode = Yii::t('props','You buy props level is not enough');
			return false;
		} */
		
		//检查道具是否存在，已存在不能做购买操作
		if($this->isCheckBuy && $this->exist){
			$this->errorCode = Yii::t('props','You have already purchased the props');
			return false;
		}
		//检查道具是否过期，如果没有过期就不能购买
		if($this->isCheckExpired && $this->exist){
			$validateTime = $this->exist['valid_time'];
			if($validateTime > $this->timeStamp){
				$this->errorCode = Yii::t('props','You purchased props temporarily not expired, you can not buy again');
				return false;
			}
		}
		//购买过永久的道具，也不能购买永久价格的道具
		if($this->exist && $this->isCheckForeverPrice){
			$validateTime = $this->exist['valid_time'];
			if($validateTime == 0){
				$this->errorCode = Yii::t('props','You have already purchased the props permanent price, no need to buy this');
				return false;
			}
			
		}
		//不能购买赠品You buy props gifts, can not buy in the mall
		if($this->props['status'] != PROPS_STATUS_USE){
			$this->errorCode = Yii::t('props','You buy props gifts, can not buy in the mall');
			return false;
		}
		
		return true;
	}
	
	/**
	 * 取得道具所花费的价格，子类可重载
	 * 
	 * @return number
	 */
	public function getPropsPrice(){
		return $this->props['pipiegg']*$this->num;
	}
	
	/**
	 * 取得道具所得到的贡献值，子类可重载
	 * 
	 * @return number
	 */
	public function getPropsDedication(){
		if($this->props['dedication']){
			return $this->props['dedication']*$this->num;
		}
		return $this->getPropsPrice() * Yii::app()->params['change_relation']['pipiegg_to_dedication'];
	}
	
	/**
	 * 取得道具所得到的皮点，子类可重载
	 * 
	 * @return number
	 */
	public function getPropsEggPoints(){
		return $this->props['egg_points']*$this->num;
	}
	
	/**
	 * 取得道具所得到的魅力值，子类可重载
	 * 
	 * @return number
	 */
	public function getPropsCharm(){
		return $this->props['charm']*$this->num;
	}
	
	/**
	 * 取得道具所得到的魅力点，子类可重载
	 * 
	 * @return number
	 */
	public function getPropsCharmPoints(){
		return $this->props['charm_points']*$this->num;
	}
	
	/**
	 * 取得道具的有期时间，０表示永久，子类可重载
	 * 
	 * @return number
	 */
	public function getPropsValidTime(){
		return 0;
	}
	
	/**
	 * 取得道具所作用的对象ＩＤ，如购守护时，守护的的主播ＩＤ，子类可重载
	 * 
	 * @return number
	 */
	public function getToTargetId(){
		return 0;
	}
	/**
	 * 获取被操作用户ID
	 * 
	 * @return number
	 */
	public function getToUid(){
		return 0;
	}
	/**
	 * 获取操作位置
	 * 
	 * @return number
	 */
	public function getOperatePage(){
		return CLIENT_SHOP;
	}
	/**
	 * 获取购买数量
	 */
	public function getNum(){
		return $this->num;
	}
	/**
	 * 取得道具的购买信息，子类可重载
	 * 
	 * @return number
	 */
	public function getPropsInfo(){
		return $this->props['category']['name'].'('.$this->props['name'].')';
	}
	
	
	/**
	 * 获取道具属性，数据按属性ＩＤ排列
	 * 
	 * @return array
	 */
	public function getPropsAttriubte(){
		return self::$propsService->buildDataByIndex($this->props['attribute'],'attr_id');
	}
	
	/**
	 * 获取道具属性，数据按属性英文名称排列
	 * 
	 * @return array
	 */
	public function getPropsEnAttriubte(){
		return self::$propsService->buildDataByIndex($this->props['attribute'],'attr_enname');
	}
	
	/**
	 * 获取错误代码
	 * 
	 * @return number
	 */
	public function getErrorCode(){
		return $this->errorCode;
	}
	/**
	 * 取得用户购买道具真实的有效时间
	 * 
	 * @return int
	 */
	public function getExistValidTime(){
		$newValidateTime = $this->getPropsValidTime();
		if($this->exist){
			$orgValidateTime = $this->exist['valid_time'];
			if($orgValidateTime == 0){
				return 0;//永久价道具
			}else if($newValidateTime > 0 && $orgValidateTime > $this->timeStamp){
				//如果以前购买的道具不是永久价，但还未过期，则在上面叠加
				return $orgValidateTime+($newValidateTime-$this->timeStamp);
			}
		}
		return $newValidateTime;
	}
	/**
	 * 存储用户消费属性,，子类可重载
	 */
	protected function saveUserConsumeAttribute(){
		$attribute['uid'] = $this->users['uid'];
		$attribute['pipiegg'] = $this->getPropsPrice();
		$attribute['consume_pipiegg'] = $this->getPropsPrice();
		$attribute['dedication'] = $this->getPropsDedication();
		if($this->getPropsEggPoints()){
			$attribute['egg_points'] = $this->getPropsEggPoints();
		}
		self::$consumeService->saveUserConsumeAttribute($attribute);
	}
	
	/**
	 * 购买之后的操作，这个方法可有可无，子类可重载
	 */
	protected function afterBuy(){
		
	}
	
	/**
	 * 将用户购买的道具放入背包，子类可重载
	 * 
	 * @return number 返回背包ＩＤ
	 */
	protected function savePropsToBag(){
		$bag['uid'] = $this->users['uid'];
		$bag['target_id'] = $this->getToTargetId();
		$bag['prop_id'] = $this->props['prop_id'];
		$bag['cat_id'] = $this->props['cat_id'];
		$bag['record_sid'] = $this->recordSid;
		$bag['num'] = $this->getNum();
		$bag['valid_time'] = $this->getPropsValidTime();
		return self::$userPropsService->saveUserPropsBag($bag,$this->props);
	}
	
	/**
	 * 存储用户贡献值
	 * 
	 * @return number　返回流水线记录ＩＤ
	 */
	private function saveUserDeidcationRecords(){
		$records['uid'] = $this->users['uid'];
		$records['from_target_id'] = $this->props['prop_id'];
		$records['to_target_id'] = $this->getToTargetId();
		$records['dedication'] = $this->getPropsDedication();
		$records['info'] = $this->getPropsInfo();
		$records['record_sid'] = $this->recordSid;
		$records['num'] = $this->num;
		$records['source'] = SOURCE_PROPS;
		$records['sub_source'] = $this->props['category']['en_name'];
		$records['client'] = $this->getOperatePage();
		return self::$consumeService->saveUserDedicationRecords($records,1);
	}
	
	/**
	 * 存储用户道具购买记录
	 * 
	 * @return number　返回流水线记录ＩＤ
	 */
	private function saveUserPropsRecords(){
		$records['uid'] = $this->users['uid'];
		$records['cat_id'] = $this->props['cat_id'];
		$records['prop_id'] = $this->props['prop_id'];
		$records['pipiegg'] = '-'.$this->getPropsPrice();
		$records['dedication'] = $this->getPropsDedication();
		$records['egg_points'] = $this->getPropsEggPoints();
		$records['charm'] = $this->getPropsCharm();
		$records['charm_points'] = $this->getPropsCharmPoints();
		$records['vtime'] = $this->getPropsValidTime();
		$records['info'] = $this->getPropsInfo();
		$records['source'] = 0;
		$records['amount'] = $this->num;
		return $this->recordSid = self::$userPropsService->saveUserPropsRecords($records,$this->props);
	}
	
	/**
	 * 存储用户皮蛋变化
	 * 
	 *  @return number　返回流水线记录ＩＤ
	 */
	private function saveUserPipieggRecords(){
		$records['uid'] = $this->users['uid'];
		$records['from_target_id'] = $this->props['prop_id'];
		$records['to_target_id'] = $this->getToTargetId();
		$records['pipiegg'] = $this->getPropsPrice();
		$records['record_sid'] = $this->recordSid;
		$records['num'] = $this->num;
		$records['source'] = SOURCE_PROPS;
		$records['extra'] = $this->getPropsInfo();
		$records['sub_source'] = $this->props['category']['en_name'];
		$records['client'] = $this->getOperatePage();
		self::$consumeService->saveUserPipiEggRecords($records,0);
	}
	
	/**
	 * 存储用户道具属性，子类可重载
	 */
	protected function saveUserPropsAttribute(){
		
	}
	/**
	 * 存储用户道具使用记录
	 */
	protected function saveUserPropsUse(){
		$records['uid'] = $this->users['uid'];
		$records['target_id'] = $this->getToTargetId();
		$records['num'] = $this->num;
		$records['valid_time'] = $this->getPropsValidTime();
		$records['to_uid'] =  $this->getToUid();
		$records['record_sid'] = $this->recordSid;
		$records['prop_id'] = $this->props['prop_id'];
		$records['cat_id'] = $this->props['cat_id'];
		$records['use_type'] = $this->getOperatePage();
		self::$userPropsService->saveUserPropsUse($records);
	}
	
	/**
	 * 检查是否存在用户相关、道具相关的信息
	 * 
	 * @return boolean
	 */
	private function checkData(){
		//用户和道具基本信息应该存在
		if($this->users['uid'] <= 0 || $this->props['prop_id']<=0){
			$this->errorCode = Yii::t('common','Parameters are wrong');
			return false;
		}
		//不管有没有等级限制，必须有Rank值
		if(!isset($this->props['rank']) ||  !isset($this->users['rank'])){
			$this->errorCode =  Yii::t('props','You buy props level is not enough');
			return false;
		}
		return true;
	}
}
