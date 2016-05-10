<?php
/**
 * Vip计时规则更新
 * @author zhangzhifan <zhangzhifan@pipi.cn> 2010-11-2
 * @link http://show.pipi.cn
 * @copyright Copyright &copy; 2003-2010 show.pipi.cn
 * @license
 */
class VipTimingUpdateCommand extends PipiConsoleCommand {
	/**
	 * @var CDbConnection 新版用户数据库
	 */
	protected $user_db;
	
	/**
	 * @var CDbConnection 新版消费数据库
	 */
	protected $consume_db;
	
	/**
	 * @var CDbConnection 新版消费记录数据库
	 */
	protected $consume_records_db;
	
	protected $propsService;
	
	
	public function beforeAction($action,$params){
		parent::beforeAction($action, $params);
		$this->user_db = Yii::app()->db_user;
		$this->consume_db = Yii::app()->db_consume;
		$this->consume_records_db = Yii::app()->db_consume_records;
		$this->propsService=new PropsService();
		return true;
	}
	
	//发初始化消息
	public function actionSendVipMsg()
	{
		$currentTime=time();
		$propsService=$this->propsService;
		//取得vip道具信息
		$yellowProps = $propsService->getPropsByEnName('vip_yellow');
		$purpleProps = $propsService->getPropsByEnName('vip_purple');
		//检测背包中是否有已处于启用状态的vip
		$category = $propsService->getPropsCategoryByEnName('vip');
		$cat_id = $category['cat_id'];
		
		//停用
		$consumeCommand = $this->consume_db->createCommand ();
		$sql1="select * from `web_user_props_bag` where `prop_id` in ({$yellowProps['prop_id']},{$purpleProps['prop_id']}) 
		and `cat_id`={$cat_id} and  `use_status`=1 ";
		$consumeCommand->setText ( $sql1);
		$vipList1=$consumeCommand->queryAll();
		foreach ($vipList1 as $vipRow)
		{
				$propsService->updateUserJsonOfVip($vipRow['uid'], $vipRow['prop_id'],1);
		}
		
		//启用
		$consumeCommand = $this->consume_db->createCommand ();
		$sql2="select * from `web_user_props_bag` where `prop_id` in ({$yellowProps['prop_id']},{$purpleProps['prop_id']}) 
		and `cat_id`={$cat_id} and  `use_status`=0 ";
		$consumeCommand->setText ( $sql2);
		$vipList2=$consumeCommand->queryAll();
		foreach ($vipList2 as $vipRow)
		{
			$propsService->checkAndStopVip($vipRow['uid'], $vipRow['prop_id']);
			$propsService->updateUserJsonOfVip($vipRow['uid'], $vipRow['prop_id']);
		}
		
	}
	
	//新旧规则转换，只能上线时执行一次
	public function actionVipUpgrade()
	{
		$currentTime=time();
		$consumeCommand = $this->consume_db->createCommand ();
		$consumeRecordsCommand = $this->consume_records_db->createCommand ();
		//有效期内的vip默认设为启用状态
		$propsService=$this->propsService;
		//取得vip道具信息
		$yellowProps = $propsService->getPropsByEnName('vip_yellow');
		$purpleProps = $propsService->getPropsByEnName('vip_purple');
		//检测背包中是否有已处于启用状态的vip
		$category = $propsService->getPropsCategoryByEnName('vip');
		$cat_id = $category['cat_id'];
		
		//获背包中Vip总天数
		$this->createVipOfBagTotalDays($cat_id, $yellowProps['prop_id'], $purpleProps['prop_id']);
		
		//扫描有效的紫色vip
		$sqlPurpleVip="select *  from `web_user_props_bag` where `prop_id`={$purpleProps['prop_id']} and 
		`cat_id`={$cat_id} and `valid_time`>{$currentTime}";
		$consumeCommand->setText ( $sqlPurpleVip);
		$purpleVipList = $consumeCommand->queryAll ();
		$purpleVipList=$propsService->buildDataByIndex($purpleVipList, 'uid');
		$purpleVipUids=array_keys($purpleVipList);
		
		//扫描有效的黄色vip
		$sqlYellowVip="select *  from `web_user_props_bag` where `prop_id`={$yellowProps['prop_id']} and 
		`cat_id`={$cat_id} and `valid_time`>{$currentTime}";
		$consumeCommand->setText ( $sqlYellowVip);
		$yellowVipList = $consumeCommand->queryAll ();
		$yellowVipList=$propsService->buildDataByIndex($yellowVipList,'uid');
		$yellowVipUids=array_keys($yellowVipList);
		
		//扫描有效的紫色vip，将状态设为启用，启用时间为第一次购买该vip的时间
		foreach ($purpleVipList as $purpleVipRow)
		{
			$sqlMinVipBuyTime="select min(ctime) from `web_user_props_records` where `uid`={$purpleVipRow['uid']} 
			and `prop_id`={$purpleVipRow['prop_id']} and `cat_id`={$purpleVipRow['cat_id']}";
			$consumeRecordsCommand->setText($sqlMinVipBuyTime);
			$purpleBuyTime=$consumeRecordsCommand->queryScalar();
			if(isset($purpleBuyTime) && $purpleBuyTime>0)
			{
				$purpleVipBagUpdateSql="update `web_user_props_bag` set `use_status`=0,
				`update_time`={$purpleBuyTime} where `uid`={$purpleVipRow['uid']} and 
				`prop_id`={$purpleVipRow['prop_id']} and `cat_id`={$purpleVipRow['cat_id']}";
				$consumeCommand->setText ( $purpleVipBagUpdateSql);
				$consumeCommand->execute();
			}
		}
		
		//扫描有效的黄色vip，如果该用户没有有效的紫色vip将状态设为启用，启用时间为第一次购买该vip的时间
		foreach ($yellowVipList as $yellowVipRow)
		{
			$sqlMinVipBuyTime="select min(ctime) from `web_user_props_records` where 
			`uid`={$yellowVipRow['uid']} and `prop_id`={$yellowVipRow['prop_id']} and 
			`cat_id`={$yellowVipRow['cat_id']}";
			$consumeRecordsCommand->setText($sqlMinVipBuyTime);
			$yellowBuyTime=$consumeRecordsCommand->queryScalar();
			if(isset($yellowBuyTime) && $yellowBuyTime>0)
			{
				//如果用户已有启用的紫色vip，则将该黄色vip设为停用状态
				if(in_array($yellowVipRow['uid'], $purpleVipUids))
				{
					$tempTime=time();
					$yellowVipBagUpdateSql="update `web_user_props_bag` set `use_status`=1,
					`update_time`={$tempTime} where `uid`={$yellowVipRow['uid']} and 
					`prop_id`={$yellowVipRow['prop_id']} and `cat_id`={$yellowVipRow['cat_id']}";
					$consumeCommand->setText ( $yellowVipBagUpdateSql);
					$consumeCommand->execute();
				}
				else
				{
					$yellowVipBagUpdateSql="update `web_user_props_bag` set `use_status`=0,
					`update_time`={$yellowBuyTime} where `uid`={$yellowVipRow['uid']} and 
					`prop_id`={$yellowVipRow['prop_id']} and `cat_id`={$yellowVipRow['cat_id']}";
					$consumeCommand->setText ( $yellowVipBagUpdateSql);
					$consumeCommand->execute();
				}
			}
		}
		
		//检测所有停用但还没失效的黄色vip，生成使用记录
		$sql1yellow="select * from `web_user_props_bag` where `prop_id`={$yellowProps['prop_id']} 
		and `cat_id`={$cat_id} and `use_status`=1 and `valid_time`>{$currentTime}";
		$consumeCommand->setText ( $sql1yellow);
		$validYellowVipList=$consumeCommand->queryAll ();
		
		foreach ($validYellowVipList as $validYellowVipRow)
		{
			$sqlMinVipBuyTime="select min(ctime) from `web_user_props_records` where 
			`uid`={$validYellowVipRow['uid']} and `prop_id`={$validYellowVipRow['prop_id']} and 
			`cat_id`={$validYellowVipRow['cat_id']}";
			$consumeRecordsCommand->setText($sqlMinVipBuyTime);
			$yellowBuyTime=$consumeRecordsCommand->queryScalar();
			
			/*
			 *插入vip使用计时记录
			*/
			$this->createVipUsedRecords($validYellowVipRow['uid'], $validYellowVipRow['prop_id'], $validYellowVipRow['cat_id'], $yellowBuyTime, $validYellowVipRow['update_time']);
			
		}
		
		
		//检测所有失效的vip，设为停用并生成使用记录
		$sql2OverdueVip="update `web_user_props_bag` set `use_status`=1,`update_time`=`valid_time` 
		where `prop_id` in ({$yellowProps['prop_id']},{$purpleProps['prop_id']}) and `cat_id`={$cat_id} and 
		`valid_time`<{$currentTime} and `valid_time`>0";
		$consumeCommand->setText ( $sql2OverdueVip);
		$consumeCommand->execute();
		
		$sql2vip="select * from `web_user_props_bag` where `prop_id` in ({$yellowProps['prop_id']},
		{$purpleProps['prop_id']}) and `cat_id`={$cat_id} and `valid_time`<{$currentTime} and `valid_time`>0";
		$consumeCommand->setText ( $sql2vip);
		$overdueVipList=$consumeCommand->queryAll();
		$userPropsUseModel = new UserPropsUseModel();
		foreach ($overdueVipList as $overdueVipRow)
		{
			$sqlMinVipBuyTime="select min(ctime) from `web_user_props_records` where 
			`uid`={$overdueVipRow['uid']} and `prop_id`={$overdueVipRow['prop_id']} and 
			`cat_id`={$overdueVipRow['cat_id']}";
			$consumeRecordsCommand->setText($sqlMinVipBuyTime);
			$BuyTime=$consumeRecordsCommand->queryScalar();
				
			/*
			 *插入vip使用计时记录
			*/
			$this->createVipUsedRecords($overdueVipRow['uid'], $overdueVipRow['prop_id'], $overdueVipRow['cat_id'], $BuyTime, $overdueVipRow['valid_time']);	
		}
	}
	
	//生成vip使用记录
	protected function createVipUsedRecords($uid,$prop_id,$cat_id,$openTime,$stopTime)
	{
		$propsService=$this->propsService;
		/*
		*插入vip使用计时记录
		*/
		$records=array();
		$records['uid'] = $uid;
		$records['prop_id'] = $prop_id;
		$records['cat_id'] = $cat_id;
		$records['use_type'] = 1;			//vip
		$records['create_time'] = $stopTime;		//此次停用时间
		$records['valid_time']=$openTime;//此次计时启用时间
		$records['num'] = $propsService->getVipTimingDays($records['valid_time'],$records['create_time']);	//vip记时天数
		//存储计时记录
		$userPropsUseModel1 = new UserPropsUseModel();
		$propsService->attachAttribute($userPropsUseModel1,$records);
		$flag = $userPropsUseModel1->save();
	}
	
	//生成背包中Vip总天数
	protected  function createVipOfBagTotalDays($cat_id,$yellowVip_id,$purpleVip_id)
	{
		$consumeCommand = $this->consume_db->createCommand ();
		$consumeRecordsCommand = $this->consume_records_db->createCommand ();
		$propsService=$this->propsService;
		
		//获取背包中vip列表
		$sql2vip="select * from `web_user_props_bag` where `prop_id` in ({$yellowVip_id},
		{$purpleVip_id}) and `cat_id`={$cat_id}";
		$consumeCommand->setText ( $sql2vip);
		$VipList=$consumeCommand->queryAll ();
		
		//扫描背包中vip总天数
		foreach ($VipList as $vipRow) {
			$sqlMinVipBuyTime="select min(ctime) from `web_user_props_records` where 
			`uid`={$vipRow['uid']} and `prop_id`={$vipRow['prop_id']} and 
			`cat_id`={$vipRow['cat_id']}";
			$consumeRecordsCommand->setText($sqlMinVipBuyTime);
			$BuyTime=$consumeRecordsCommand->queryScalar();
			$totalDays=$propsService->getVipTimingDays($BuyTime,$vipRow['valid_time']);
			
			$sql3Vip="update `web_user_props_bag` set `num`={$totalDays}
			where `uid`={$vipRow['uid']} and `prop_id`={$vipRow['prop_id']} and 
			`cat_id`={$vipRow['cat_id']}";
			$consumeCommand->setText ( $sql3Vip);
			$consumeCommand->execute();
		}
	}
}