<?php
/**
 * @author zhangzhifan <zhangzhifan@pipi.cn> 2010-11-2
 * @link http://show.pipi.cn
 * @copyright Copyright &copy; 2003-2010 show.pipi.cn
 * @license
 */
define('AGENT_STATUS_USE', 0);
define('AGENT_STATUS_STOP', 1);

class AgentsService extends PipiService {
	const DEFAULT_CONFIG_KEY = 'AGENT_GLOBAL_CONF';
	protected static $userService;
	protected static $userJsonInfoService;
	protected static $bbsSer;
	
	public function __construct(PipiController $pipiController = null)
	{
		parent::__construct($pipiController);
		if(empty(self::$userService))
		{
			self::$userService=new UserService();
		}
		
		if(empty(self::$userJsonInfoService))
		{
			self::$userJsonInfoService=new UserJsonInfoService();
		}
		
		if(empty(self::$bbsSer))
			self::$bbsSer=new BbsbaseService();
	}
	
	/**
	 * 保存代理全局配置信息
	 *
	 * @param array $c_value
	 * @return boolean
	 */
	public static function saveGlobalConfig(Array $c_value = array()){
		if (!$c_value){
			$c_value = self::getDefaultConfig();
		}
		$service = new WebConfigService();
		$conf['c_key'] = self::DEFAULT_CONFIG_KEY;
		$conf['c_type'] = 'array';
		$conf['c_value'] = $c_value;
		if($service->saveWebConfig($conf)){
			return true;
		}
		return false;
	}
	
	/**
	 * 获取代理全局配置
	 *
	 * @return array
	 */
	public static function getGlobalConfig(){
		$service = new WebConfigService();
		$setInfo = $service->getWebConfig(self::DEFAULT_CONFIG_KEY);
		if(!$setInfo){
			$setInfo = self::getDefaultConfig();
		}else{
			$setInfo = $setInfo['c_value'];
		}
		return $setInfo;
	}
	
	/**
	 * 获取家族全局配置的默认值
	 *
	 * @author supeng
	 * @return array
	 */
	public static function getDefaultConfig(){
		$info = array();
		$info['global_enable'] = true;
		$info['global_lightup_condition'] = 300000;
		$info['global_rate'] = 0.003;
		return $info;
	}
	
	/**
	 * 存储用户通过代理购买物品
	 *
	 * @param  array $saleRecords　销售记录
	 * @return int
	 */
	public function saveSaleRecords(array $saleRecords)
	{
		if(($saleRecords['agent_id']<=0 || $saleRecords['uid']) <= 0 || $saleRecords['goods_id'] <= 0 ||$saleRecords['pipieggs']<=0)
		{
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		
		if(!$this->checkAgentByUid($saleRecords['agent_id']))
			return $this->setError(Yii::t('agent','Agent is error'),0);
		
		$saleRecords['create_time']=time();
		$saleRecords['sale_year']=date("Y",$saleRecords['create_time']);
		$saleRecords['sale_month']=date("m",$saleRecords['create_time']);
		$saleRecords['sale_day']=date("d",$saleRecords['create_time']);
		$agentSaleRecordsModel=new AgentSaleRecordsModel();
		$this->attachAttribute($agentSaleRecordsModel,$saleRecords);
		$flag=$agentSaleRecordsModel->save();
		if($flag)
		{
			$userAgent=UserAgentModel::model()->findByPk($saleRecords['agent_id']);
			$userAgent->sale_pipieggs=$userAgent->sale_pipieggs+$saleRecords['pipieggs'];
			if($userAgent->sale_pipieggs>$this->getLightUpConditionByUid($saleRecords['agent_id']))
				$userAgent->agent_type=2;
			if($userAgent->save())
				$this->updateAgentJsonInfo($saleRecords['agent_id']);
			
		}
		return $agentSaleRecordsModel->getPrimaryKey();
	}
	
	/**
	 * 授权新代理
	 *
	 * @param  int $uid　用户id
	 * @return int
	 */
	public function addAgent($uid)
	{
		$userAgent=new UserAgentModel();
		$users=self::$userService->getUserBasicByUids(array($uid));
		$ext = self::$userService->getUserExtendByUids(array($uid));
		if(isset($users[$uid]))
		{
			$userAgent->uid=$uid;
			$userAgent->agent_nickname=$users[$uid]['nickname'];
			$userAgent->agent_name=$users[$uid]['realname'];
			$userAgent->agent_mobile=isset($ext[$uid]['mobile'])?$ext[$uid]['mobile']:'';
			$userAgent->agent_qq=isset($ext[$uid]['qq'])?$ext[$uid]['qq']:'';
			$userAgent->update_time=time();
			$userAgent->agent_type=1;
			$userAgent->lightup_condition=$this->getLightUpConditionByUid($uid);
			$userAgent->rate=$this->getRateByUid();
			$userAgent->create_time=time();
			if($userAgent->save())
			{
				$this->updateAgentJsonInfo($uid);
			}
		}
		return $userAgent->getPrimaryKey();
	}
	
	/**
	 * 更新代理资料
	 *
	 * @param  int $uid　用户id
	 * @return int
	 */
	public function updateAgent(array $agent)
	{
		if(($agent['uid']) <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$userAgent=UserAgentModel::model()->findByPk($agent['uid']);
		$this->attachAttribute($userAgent, $agent);
		$result=$userAgent->save();
		if($result)
		{
			$this->updateAgentJsonInfo($agent['uid']);
		}
		return $result;
	}
	
	/**
	 * 更新代理json信息
	 *
	 * @param  int $uid　用户id
	 * @return int
	 */
	public function updateAgentJsonInfo($agent_id)
	{
		$newUserAgent=UserAgentModel::model()->findByPk($agent_id);
		$newdata=array('agent'=>array(
			'at'=>$newUserAgent->agent_type,
			'as'=>$newUserAgent->agent_status,
			'lc'=>$newUserAgent->lightup_condition,
			'sp'=>$newUserAgent->sale_pipieggs,
		));
		
		self::$userJsonInfoService->setUserInfo($agent_id,$newdata);
		$zmq=new PipiZmq();
		return $zmq->sendZmqMsg(609,array('type'=>'update_json','uid'=>$agent_id,'json_info'=>$newdata));
	}
	
	/**
	 * 获取代理列表
	 *
	 * @param  int $uid　用户id
	 * @return int
	 */
	public function getAgentList($agent_status=0)
	{
		$userAgentModel=new UserAgentModel();
		$userAgents=$userAgentModel->findAll('agent_status = :agent_status',array(':agent_status'=>$agent_status));
		$userAgentList=$this->arToArray($userAgents);
		$agent_ids=array();
		foreach ($userAgentList as $row)
		{
			$agent_ids[]=$row['uid'];
		}
		$users=self::$userService->getUserBasicByUids($agent_ids);
		
		$lastSalesTop=$this->getLastMonthSalesTop();
		if($lastSalesTop)
			$lastSalesTop=$this->buildDataByIndex($lastSalesTop, 'agent_id');
		
		$result_arr=array();
		foreach ($userAgentList as $k=>$v)
		{
			$userAgentList[$k]['agent_nickname']=$users[$v['uid']]['nickname'];
			if(isset($lastSalesTop[$v['uid']]['sum_pipieggs']))
			{
				$userAgentList[$k]['sum_pipieggs']=$lastSalesTop[$v['uid']]['sum_pipieggs'];
				$result_arr[]=$userAgentList[$k];
				unset($userAgentList[$k]);
			}
		}
		usort($userAgentList,array($this,'sortAgentByCreateTime'));
		usort($result_arr,array($this,'sortAgentByCreateTime'));
		usort($result_arr,array($this,'sortAgentBySalesTop'));
		
		return array_merge($result_arr,$userAgentList);
	}
	
	/**
	 * 获取所有代理
	 * @param int $page
	 * @param int $pageSize
	 * @param array $conditions
	 */
	public function getAllAgent($page, $pageSize, $conditions = array()){
		$page = intval($page) < 1 ? 1 : intval($page);
		$userAgentModel=new UserAgentModel();
		$return = $userAgentModel->getAgentList($conditions, ($page - 1) * $pageSize, $pageSize);
		$pager = new CPagination($return['count']);
		$pager->pageSize = $pageSize;
		$pager->params = $conditions;
		$return['pager'] = $pager;
		
		$agent_ids=array();
		foreach ($return['list'] as $row)
		{
			$agent_ids[]=$row['uid'];
		}
		$users=self::$userService->getUserBasicByUids($agent_ids);
		foreach ($return['list'] as $k=>$v)
		{
			$return['list'][$k]['agent_nickname']=$users[$v['uid']]['nickname'];
		}
		
		return $return;
	}
	
	/**
	 * 取得代理登记信息
	 *
	 * @param array $uids
	 * @return array
	 */
	public function getAgentByUids($uids){
		if(empty($uids)){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$uids = is_array($uids) ? $uids : array($uids);
		$userAgentModel = UserAgentModel::model();
		$models = $userAgentModel->getAgentByUids($uids);
		$data = $this->arToArray($models);
		return $this->buildDataByIndex($data,'uid');
	}
	
	
	//检测指定uid用户是否具有代理身份
	public function checkAgentByUid($uid)
	{
		$agents=$this->getAgentByUids(array($uid));
		if(isset($agents[$uid]))
		{
				return $agents[$uid]['agent_status']==0?1:0;
		}
		return 0;
	}
	
	/**
	 * 取得代理点亮售字所需皮蛋数
	 *
	 * @param int $uid
	 * @return array
	 */
	public function getLightUpConditionByUid($uid=0)
	{
		$globalConfig=self::getGlobalConfig();
		if(!empty($uid) && !$globalConfig['global_enable'])
		{
			$userAgents=$this->getAgentByUids(array($uid));
			$userAgentInfo=$userAgents[$uid];
			return $userAgentInfo['lightup_condition'];
		}
		else 
		{
			return $globalConfig['global_lightup_condition'];
		}
	}
	
	/**
	 * 取得代理提成比例
	 *
	 * @param int $uid
	 * @return array
	 */
	public function getRateByUid($uid=0)
	{
		$globalConfig=self::getGlobalConfig();
		if(!empty($uid) && !$globalConfig['global_enable'])
		{
			$userAgents=$this->getAgentByUids(array($uid));
			$userAgentInfo=$userAgents[$uid];
			return $userAgentInfo['rage'];
		}
		else
		{
			return $globalConfig['global_rate'];
		}
	}
	
	//获取指定代理的月统计
	public function getMonthSaleStatByAgentId($saleYear,$agent_id)
	{
		if(empty($saleYear))
			$saleYear=date("Y");
		$agentSaleRecordsModel=new AgentSaleRecordsModel();
		return $agentSaleRecordsModel->getMonthSaleStatByAgentId($saleYear, $agent_id);
		
	}
	
	//统计本月代理销售提成
	public function getThisMonthSaleIncome($agent_id)
	{
		$agentSaleRecordsModel=new AgentSaleRecordsModel();
		return $agentSaleRecordsModel->getThisMonthSaleIncome($agent_id);
	}
	
	//按月查询代理销售记录
	public function getRecordsByMonth($yearMonth,$agent_id,$page=1, $pageSize=10)
	{
		$offset = ($page >= 1 ? ($page-1) : 0 ) * $pageSize;
		$agentSaleRecordsModel=new AgentSaleRecordsModel();
		$records=$agentSaleRecordsModel->getRecordsByMonth($yearMonth, $agent_id,$offset,$pageSize);
		$records['list']=$this->arToArray($records['list']);
		
		
		$records['page'] = $page;
		$records['page_num'] = ceil($records['count'] / $pageSize);
		return $records;
	}
	
	//按玩家查询代理销售记录
	public function getRecordsByUser($user_id,$agent_id,$page=1,$pageSize=10)
	{
		$offset = ($page >= 1 ? ($page-1) : 0 ) * $pageSize;
		$agentSaleRecordsModel=new AgentSaleRecordsModel();
		$records=$agentSaleRecordsModel->getRecordsByUser($user_id, $agent_id,$offset,$pageSize);
		$records['list']=$this->arToArray($records['list']);
		$records['page'] = $page;
		$records['page_num'] = ceil($records['count'] / $pageSize);
		return $records;
	}
	
	//查询销售记录
	public function getRecordsByCondition($condition,$page=1,$pageSize=10)
	{
		$offset = ($page >= 1 ? ($page-1) : 0 ) * $pageSize;
		$agentSaleRecordsModel=new AgentSaleRecordsModel();
		$records=$agentSaleRecordsModel->getRecordsByCondition($condition,$offset,$pageSize);
		$records['list']=$this->arToArray($records['list']);
		
		$pager = new CPagination($records['count']);
		$pager->pageSize = $pageSize;
		$pager->params = $condition;
		$records['pager'] = $pager;
		return $records;
	}
	
	//统计销售金额和提成
	public function getSaleStatByCondition($condition)
	{
		$agentSaleRecordsModel=new AgentSaleRecordsModel();
		return $agentSaleRecordsModel->getSaleStatByCondition($condition);
	}
	
	//取近6个月列表
	public function  getRecentSixMonthList()
	{
		$sdate=date("Y-m-d");
		$monthList=array();
		for($i=0;$i<6;$i++)
		{
			$monthList[$i]=array(
				'value'=> date("Y-m",strtotime("$sdate -{$i} month")),
				'text'=>date("Y年m月",strtotime("$sdate -{$i} month"))
			);
		}
		return $monthList;
	}
	
	//取近3年列表
	public function getRecentThreeYearList()
	{
		$sdate=date("Y-m-d");
		$yearList=array();
		for($i=0;$i<3;$i++)
		{
			$yearList[$i]=array(
				'value'=> date("Y",strtotime("$sdate -{$i} year")),
				'text'=>date("Y年",strtotime("$sdate -{$i} year"))
				);
		}
		return $yearList;
	}
	
	//为销售记录获取玩家和道具信息
	public function getUserInfoForSaleRecords(&$records)
	{
		$uids=array();
		$prop_ids=array();
		foreach ($records as $row1)
		{
			$uids[]=$row1['uid'];
			if($row1['goods_type']==0)
				$prop_ids[]=$row1['goods_id'];
		}
		$uids=array_unique($uids);
		$prop_ids=array_unique($prop_ids);
	
		$userInfoList=self::$userService->getUserBasicByUids($uids);
		$propsService=new PropsService();
		$propInfoList=$propsService->getPropsByIds($prop_ids);
	
		foreach($records as &$row2)
		{
			$row2['user_nickname']=$userInfoList[$row2['uid']]['nickname'];
			if($row2['goods_type']==0)
				$row2['goods_name']=$propInfoList[$row2['goods_id']]['name'];
			elseif($row2['goods_type']==1)
				$row2['goods_name']='靓号'.$row2['goods_id'];
		}
	
	}
	
	/**
	 * 统计销售金额和提成，后台用
	 * @author hexin
	 * @param unknown_type $condition
	 * @param unknown_type $page
	 * @param unknown_type $pageSize
	 * @return Ambigous <multitype:, mixed>
	 */
	public function getStatByCondition($conditions, $page = 1, $pageSize = 20){
		$page = intval($page) < 1 ? 1 : intval($page);
		$agentSaleRecordsModel=new AgentSaleRecordsModel();
		$return = $agentSaleRecordsModel->getStatByCondition($conditions, ($page - 1) * $pageSize , $pageSize);
		$pager = new CPagination($return['count']);
		$pager->pageSize = $pageSize;
		$pager->params = $conditions;
		$return['pager'] = $pager;
		return $return;
	}
	
	/**
	 * 获取代理政策的子板块ID
	 *
	 * @return boolean
	 */
	public function getAgentPolicyForumSubId(){
		$forum_sid = false;
		$conditions = array(
			'forum_name'=>'运营 CMS',
			'forum_sname'=>OPERATORS_CMS_AGENTPOLICY_FORUMNAME,
			'ower_uid'=>OPERATORS_CMS_AGENTPOLICY_OWERUID,
			'from'=>FORUM_FROM_TYPE_ADMIN
		);
	
		$forum = self::$bbsSer->getFormByConditions($conditions);
		if(empty($forum))
		{
			self::$bbsSer->createForum(OPERATORS_CMS_AGENTPOLICY_OWERUID, OPERATORS_CMS_AGENTPOLICY_FORUMNAME,FORUM_FROM_TYPE_ADMIN,OPERATORS_CMS_AGENTPOLICY_OWERUID);
			$forum = self::$bbsSer->getFormByConditions($conditions);
		}
		else
		{
			$forum_sid = $forum[0]['forum_sid'];
		}
	
		return $forum_sid;
	}
	
	//获取代理政策
	public function getAgentPloicies($page,$pageSize=10)
	{
		$threadList = array();
		$threadList['count'] = 0;

		$forum_sid = $this->getAgentPolicyForumSubId();

		if(!empty($forum_sid)){
		
			$threadList = self::$bbsSer->getThreadList($forum_sid,$page,$pageSize);
		}
		$threadList['forum_sid']=$forum_sid;
		return $threadList;
	}
	
	//上月销量榜
	public function getLastMonthSalesTop()
	{
		$otherRedisModel=new OtherRedisModel();
		$salesTop=$otherRedisModel->getAgentSalesTop();
		if(empty($salesTop))
		{
			$sdate=date("Y-m-d");
			$lastMonth=date("Y-m",strtotime("{$sdate} -1 month"));
			list($year,$month) = explode("-", $lastMonth);
			$agentSaleRecordsModel=new AgentSaleRecordsModel();
			$salesTop=$agentSaleRecordsModel->getAgentSalesTopByMonth($year,$month);
			if(empty($salesTop))
			{
				$year=date("Y");
				$month=date("m");
				$salesTop=$agentSaleRecordsModel->getAgentSalesTopByMonth($year,$month);
			}
			if(isset($salesTop) && count($salesTop)>0)
			{
				$otherRedisModel->setAgentSalesTop($salesTop);
			}
		}
		return $salesTop;
	}
	
	/**
	 * 按销量排序代理列表
	 *
	 * @param $prev
	 * @param $next
	 * @author zhangzhifan
	 * @return int
	 */
	public function sortAgentBySalesTop(array $prev,array $next){
		if($prev['sum_pipieggs'] == $next['sum_pipieggs']){
			return 0;
		}
		return $prev['sum_pipieggs'] < $next['sum_pipieggs'] ? 1 : -1;
	}
	
	/**
	 * 按授权时间排序代理列表
	 *
	 * @param $prev
	 * @param $next
	 * @author zhangzhifan
	 * @return int
	 */
	public function sortAgentByCreateTime(array $prev,array $next){
		if($prev['create_time'] == $next['create_time']){
			return 0;
		}
		return $prev['create_time'] < $next['create_time'] ? -1 : 1;
	}
}
?>