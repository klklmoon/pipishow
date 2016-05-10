<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: supeng $>
 * @author Su Peng <supeng@pipi.cn>
 * @version $Id: DoteyController.php 10460 2013-05-20 12:52:25Z supeng $ 
 */
class DoteyController extends PipiAdminController {
	
	/**
	 * @var DoteyService 主播服务层
	 */
	public $doteySer;
	
	/**
	 * @var UserService 用户服务层
	 */
	public $userSer;

	/**
	 * @var array 允许的操作
	 */
	public $allowOp = array('dlInGiftExcel', 'dlDoteyListExcel', 'editDoteyBase', 'changeLiveStatus', 'editLiveRecords', 
		'dlOnliveSearchExcel', 'dlIncomeSearchExcel', 'dlStopLiveExcel', 'restoreStopLive', 'addAward', 'checkDoteyInfo', 
		'unAward', 'addAllowance', 'unAllowance', 'addManager', 'addProxy', 'getApplyInfo', 'authDoteyApply', 
		'contractDoteyApply', 'refuseDoteyApply', 'delDoteyApply', 'revokedDoteyApply', 'addRewardPolicy', 
		'delRewardMonth', 'dlRewardsExcel','editArchives','editManager','removeUserAvatar','editApplyInfo',
		'dlVODQueryExcel','dlVODStatExcel','proxyChange','checkSignFamily','delOperate','addFamily');

	/**
	 * @var string 当前操作
	 */
	public $op;

	/**
	 * @var boolean 是否是Ajax请求
	 */
	public $isAjax;

	public $pageSize = 20;

	public $offset;

	/**
	 * @var int page lable
	 */
	public $p;
	
	public function init(){
		parent::init();
		$this->doteySer = new DoteyService();
		$this->userSer = new UserService();
		$this->op = Yii::app()->request->getParam('op');
		$this->isAjax = Yii::app()->request->isAjaxRequest;
		if(!($this->p = Yii::app()->request->getParam('page'))){
			$this->p = 1;
		}
		$this->offset = ($this->p -1)*$this->pageSize;
	}
	
	/**
	 * 主播申请 
	 */
	public function actionDoteyApply(){
		//是否是查看与编辑申请信息
		if($this->isAjax && $this->op == 'getApplyInfo' && in_array($this->op, $this->allowOp)){
			$this->getApplyInfo();
		}
		
		//是否授权
		if($this->isAjax && $this->op == 'authDoteyApply' && in_array($this->op, $this->allowOp)){
			$this->editDoteyApplyStatus(APPLY_STATUS_FACE);
		}
		
		//是否签约
		if($this->isAjax && $this->op == 'contractDoteyApply' && in_array($this->op, $this->allowOp)){
			$this->editDoteyApplyStatus(APPLY_STATUS_SUCCESS);
		}
		
		//拒绝
		if($this->isAjax && $this->op == 'refuseDoteyApply' && in_array($this->op, $this->allowOp)){
			$this->editDoteyApplyStatus(APPLY_STATUS_REFUES);
		}
		
		//撤销拒绝
		if($this->isAjax && $this->op == 'revokedDoteyApply' && in_array($this->op, $this->allowOp)){
			$this->editDoteyApplyStatus(APPLY_STATUS_WAITING);
		}
		
		//删除申请操作
		if($this->isAjax && $this->op == 'delDoteyApply' && in_array($this->op, $this->allowOp)){
			$this->delDoteyApply();
		}
		
		//编辑申请操作信息
		if($this->op == 'editApplyInfo' && in_array($this->op, $this->allowOp)){
			$this->editApplyInfo();
		}
		
		$this->assetsMy97Date();
		$condition = array();
		$condition = $this->getDoteySearchCondition();
		
		$isFilterSource = false;
		if ($this->authDoteyManager()){
			$isFilterSource = true;
			$condition['sources'] = DOTEY_MANAGER_TUTOR.'#XX#'.$this->op_uid;
		}
		
		$result = $this->doteySer->getDoteyApplyList($this->p,$this->pageSize,$condition);
		$count = $result['count'];
		$list = $result['list'];

		$pager = new CPagination($count);
		$pager->pageSize = $this->pageSize;
		$pager->params = $condition;
		
		$this->render('dotey_apply_list' ,array('pager'=>$pager,'list'=>$list,'condition'=>$condition,'isFilterSource'=>$isFilterSource));
	}
	
	/**
	 * 平台奖励
	 */
	public function actionAward(){
		$consumeSer = new ConsumeService();
		$condition = $this->getDoteySearchCondition();
		if (!isset($condition['type'])){
			$condition['type'] = AWARD_TYPE_CHARM;
		}
		
		$result = array();
		$result['count'] = 0;
		$result['list'] = array();
		if ($condition['type'] == AWARD_TYPE_CASH){
			$condition['ex_type'] = EXCHANGE_ADMIN;
			$result = $consumeSer->getCashAwardByCondition($condition,$this->offset,$this->pageSize);
		}elseif ($condition['type'] == AWARD_TYPE_CHARMPOINTS){
			$result = $consumeSer->getCharmPointsAwardByCondition($condition,$this->offset,$this->pageSize);
		}elseif ($condition['type'] == AWARD_TYPE_CHARM){
			$result = $consumeSer->getCharmAwardByCondition($condition,$this->offset,$this->pageSize);
		}
		
		$list = $this->formatAwardList($result['list'],$condition['type'],$consumeSer);
		
		$doteyInfo = $this->getDoteyInfo($list);
		
		$count = $result['count'];
		$pager = new CPagination($count);
		$pager->pageSize = $this->pageSize;
		$pager->params = $condition;
		
		$this->render('dotey_award_list',array('pager'=>$pager,'condition'=>$condition,'list'=>$list,'doteyInfo'=>$doteyInfo));
	}
	
	/**
	 * 新增平台奖励
	 */
	public function actionAddAward(){
		//检查主播信息的合法性
		if($this->op == 'checkDoteyInfo' && in_array($this->op,$this->allowOp)){
			$this->checkDoteyInfo();
		}
		
		//是否是添加动作
		$notices = array();
		if ($this->op == 'addAward' && in_array($this->op, $this->allowOp)){
			$notices = $this->addAwardDo();
		}
		
		//是否是撤销动作
		if ($this->op == 'unAward' && in_array($this->op, $this->allowOp)){
			$this->unAwardDo();
		}
		
		if ($this->isAjax) {
			exit($this->renderPartial('dotey_add_award'));			
		}else{
			$this->render('dotey_add_award',array('notices'=>$notices));
		}
	}
	
	/**
	 * 才艺补贴列表
	 */
	public function actionAllowance(){
		$consumeSer = new ConsumeService();
		$condition = $this->getDoteySearchCondition();
		
		$result = array();
		$result['count'] = 0;
		$result['list'] = array();
		$condition['ex_type'] = EXCHANGE_ART;
		$result = $consumeSer->getCashAwardByCondition($condition,$this->offset,$this->pageSize);
		
		$list = $this->formatAwardList($result['list'],AWARD_TYPE_CASH,$consumeSer);
		
		$doteyInfo = $this->getDoteyInfo($list);
		
		$count = $result['count'];
		$pager = new CPagination($count);
		$pager->pageSize = $this->pageSize;
		$pager->params = $condition;
		
		$this->render('dotey_allowance_list',array('pager'=>$pager,'condition'=>$condition,'list'=>$list,'doteyInfo'=>$doteyInfo));
	}
	
	/**
	 * 添加才艺补贴 
	 */
	public function actionAddAllowance(){
		//检查主播信息的合法性
		if($this->op == 'checkDoteyInfo' && in_array($this->op,$this->allowOp)){
			$this->checkDoteyInfo();
		}
		
		//是否是添加动作
		$notices = array();
		if ($this->op == 'addAllowance' && in_array($this->op, $this->allowOp)){
			$notices = $this->addAllowanceDo();
		}
		
		//是否是撤销动作
		if ($this->op == 'unAllowance' && in_array($this->op, $this->allowOp)){
			$this->unAllowanceDo();
		}
		
		if ($this->isAjax) {
			exit($this->renderPartial('dotey_add_allowance'));
		}else{
			$this->render('dotey_add_allowance',array('notices'=>$notices));
		}
	}
	
	/**
	 * 主播报酬列表
	 */
	public function actionRewards(){
		$archivesSer = new ArchivesService();
		$consumeSer = new ConsumeService();
		$giftSer = new GiftService();
		
		//不为空的主播类型
		$condition = $this->getDoteySearchCondition();
		if (!isset($condition['dotey_type'])){
			$condition['dotey_type'] = DOTEY_TYPE_DIRECT;
		}
		
		if (!isset($condition['pay_time_start']) || empty($condition['pay_time_start'])){
			$condition['pay_time_start'] = date('Y-m-d',strtotime('-1 months',time()));
		}
		
		if (!isset($condition['pay_time_end']) || empty($condition['pay_time_end'])){
			$condition['pay_time_end'] = date('Y-m-d',time());
		}
		
		if (!isset($condition['status']) || empty($condition['status'])){
			$condition['_status'] = array_keys($this->doteySer->getDoteyBaseStatus(true));
		}
		
		$isLimit = true;
		if($this->op == 'dlRewardsExcel' && in_array($this->op, $this->allowOp)){
			$isLimit = false;
		}
		
		//是否是主播经理
		$isFilterSource = false;
		if ($this->authDoteyManager()){
			$isFilterSource = true;
			if(empty($condition['sources'])){
				$condition['sources'] = DOTEY_MANAGER_TUTOR.'#XX#'.$this->op_uid;
			}
		}
		
		if(!empty($condition['sources'])){
			$_condition = array();
			$_condition['sources'] = $condition['sources'];
			$list = $this->doteySer->searchDoteyList($this->p,$this->pageSize,$_condition,false);
			if(isset($list['list'])){
				$condition['uids'] = array_keys($list['list']);
			}
			if(empty($condition['uids'])){
				$result = array('count'=>0,'list'=>array());
			}
			unset($condition['sources']);
		}
		
		if (!isset($result)){
			$result = $this->doteySer->searchDoteyBase($condition,$this->offset,$this->pageSize,$isLimit);
		}
		
		$count = array_shift($result['count']);
		$list = $result['list'];
		$uids = array_keys($list);

		#过虑掉相关用户送的礼物魅力点
		$filter_uids = $this->getFilterUids();
		if ($uids){
			$filterArr = array();
			$start_time = $condition['pay_time_start'].' 00:00:00';
			$end_time = $condition['pay_time_end'].' 23:59:59';
			if($filter_uids){
				$_condition2 = array();
				$_condition2['to_uid'] = $uids;
				$_condition2['start_time'] = $start_time;
				$_condition2['end_time'] = $end_time;
				$filterGiftStat = $giftSer->getUserGiftStatByUid($filter_uids,$this->offset,$this->pageSize,$_condition2,false);
				if (isset($filterGiftStat['list'])){
					foreach ($filterGiftStat['list'] as $v){
						$filterArr[$v['to_uid']] += $v['sum_charm_points'];
					}
				}
			}
			
			//允许的主播类型
			$allowDoteyType = array_keys($this->doteySer->getDoteyType());
			//直播统计信息
			$archivesIds = array();
			$archivesInfo = $archivesSer->getArchivesByUids($uids);
			if($archivesInfo){
				//有效天的单位
				$effectDays = $archivesSer->getLiveEffectDaysUnit($uids);
				//获取提现公式
				$scales = $this->doteySer->getDoteyCashConfig($uids);
				$archivesRelation = array();
				foreach ($archivesInfo as $archivesId=>$v){
					if (isset($list[$v['uid']])){
						$list[$v['uid']]['archives']['archives_id'] = $archivesId;
						$list[$v['uid']]['archives']['cat_id'] = $v['cat_id'];
						$list[$v['uid']]['archives']['title'] = $v['title'];
						$list[$v['uid']]['archives']['have_days'] = 0;
						$list[$v['uid']]['archives']['have_hours'] = 0;
						$list[$v['uid']]['archives']['old_charm_points'] = 0;
						$list[$v['uid']]['archives']['charm_points'] = isset($filterArr[$v['uid']])?-$filterArr[$v['uid']]:0;
						$list[$v['uid']]['archives']['invalid_charm_points'] = isset($filterArr[$v['uid']])?$filterArr[$v['uid']]:0;
						$list[$v['uid']]['archives']['invalid_money'] = (isset($filterArr[$v['uid']])?$filterArr[$v['uid']]:0)*$scales[$v['uid']];
						$list[$v['uid']]['archives']['basic_salary'] = 0;
						$list[$v['uid']]['archives']['bonus'] = 0;
						$archivesRelation[$archivesId] = $v['uid'];
					}
				}
				
				//按月统计直播时长及有效天
				$archivesIds = array_keys($archivesInfo);
				$liveResult = $archivesSer->searchLiveRecordByArchivesIds($archivesIds,array('live_time_start' => $start_time, 'live_time_end' => $end_time));
				if ($liveResult){
					foreach($liveResult as $v){
						$uid =  isset($archivesRelation[$v['archives_id']])?$archivesRelation[$v['archives_id']]:null;
						if($uid && isset($list[$uid])){
							$list[$uid]['archives']['have_hours'] += $v['duration'];
							if ($effectDays[$uid] <= number_format($v['duration']/3600,2)){
								$list[$uid]['archives']['have_days'] += 1;
							}
						}
					}
				}
			}
			
			//本月魅力点收入
			$charmPointResult = $consumeSer->getMonthDoteyCharmPoints($uids,array('start_time' => $start_time, 'end_time' => $end_time));
			if($charmPointResult){
				foreach ($charmPointResult as $v){
					$list[$v['uid']]['archives']['old_charm_points'] = $v['points'];
					$list[$v['uid']]['archives']['charm_points'] += $v['points'];
					$payType = $list[$v['uid']]['dotey_type'];
					if(in_array($payType, $allowDoteyType)){
						//底薪及奖金
						$days = $list[$v['uid']]['archives']['have_days'];
						$hours = number_format($list[$v['uid']]['archives']['have_hours']/3600,2);
						$charm_points = $list[$v['uid']]['archives']['charm_points'];
						if($days && $hours && $charm_points && isset($list[$v['uid']])){
							$conf = $consumeSer->getAllowDoteyPay($v['uid'],$payType,$hours,$days,$charm_points);
							if($conf){
								$list[$v['uid']]['archives']['basic_salary'] = $conf[0]['basic_salary'];
								$list[$v['uid']]['archives']['bonus'] = $conf[0]['bonus'];
							}
						}
					}
				}
			}
			//本月的才艺补贴
			$consumeSer = new ConsumeService();
			$_condition = array(
				'uid' => $uids, 
				'handle_type' => 1,
				'create_time_on' => $start_time,
				'create_time_end' => $end_time,
				'ex_type' => EXCHANGE_ART,
			);
			
			$artRs = $consumeSer->getCashAwardByCondition($_condition,$this->offset,$this->pageSize,false);
			if($artRs['list']){
				foreach($artRs['list'] as $v){
					$list[$v['uid']]['artRs'] += $v['dst_amount'];
				}
			}
			//本月平台奖励
			$_condition['ex_type'] = EXCHANGE_ADMIN;
			$awardRs = $consumeSer->getCashAwardByCondition($_condition,$this->offset,$this->pageSize,false);
			if($awardRs['list']){
				foreach($awardRs['list'] as $v){
					$list[$v['uid']]['awardRs'] += $v['dst_amount'];
				}
			}
			//已兑换记录
			$_condition['ex_type'] = EXCHANGE_MONEY;
			$transRs = $consumeSer->getCashAwardByCondition($_condition,$this->offset,$this->pageSize,false);
			if($transRs['list']){
				foreach($transRs['list'] as $v){
					$list[$v['uid']]['transRs'] += $v['dst_amount'];
				}
			}
		}
		//导出EXCEL
		if(!$isLimit) $this->dlRewardsExcel($list);
		
		if (isset($condition['uids'])){
			unset($condition['uids']);
		}
		
		//分页
		$pager = new CPagination($count);
		$pager->pageSize = $this->pageSize;
		$pager->params = $condition;
		$this->render('dotey_rewards_list',array('condition'=>$condition,'pager'=>$pager,'list'=>$list,'isFilterSource'=>$isFilterSource));
	}
	
	/**
	 * 报酬政策
	 */
	public function actionRewardPolicy(){
		//检查主播信息
		if($this->op == 'checkDoteyInfo' && in_array($this->op,$this->allowOp)){
			$this->checkDoteyInfo();
		}
		
		//添加主播政策 
		if($this->op == 'addRewardPolicy' && in_array($this->op,$this->allowOp)){
			$this->addRewardPolicyDo();
		}
		
		//删除月度奖金政策
		if($this->op == 'delRewardMonth' && in_array($this->op,$this->allowOp)){
			$this->delRewardMonthDo();
		}
		
		//不为空的主播类型
		$condition = $this->getDoteySearchCondition();
		if (!isset($condition['dotey_type'])){
			$condition['dotey_type'] = DOTEY_TYPE_DIRECT;
		}
		
		$result = array();
		$uids = array();
		$consumeSer = new ConsumeService();
		$webConfigSer = new WebConfigService();
		$doteyPayKeys = $webConfigSer->getDoteyPayKey($this->doteySer);
		$doteyTypes = $this->doteySer->getDoteyType();
		
		//获取魅力点提现公式集合
		$scaleKey = $doteyPayKeys[$condition['dotey_type']]['scale'];
		$rs = $webConfigSer->getWebConfig($scaleKey);
		if ($rs){
			$result['scaleList'] = $rs['c_value'];
			$_uids = array_keys($rs['c_value']);
			foreach ($_uids as $uid){
				$uids[$uid] = $uid;
			}
		}
		
		//获取活跃天集合
		$effectDayKey = $doteyPayKeys[$condition['dotey_type']]['effectDay'];
		$rs = $webConfigSer->getWebConfig($effectDayKey);
		$_effectDayUids = array();
		if ($rs){
			$result['effectDayList'] = $rs['c_value'];
			$_uids = array_keys($rs['c_value']);
			foreach ($_uids as $uid){
				$uids[$uid] = $uid;
			}
		}
		
		//获取底薪奖金集合
		$rs = $consumeSer->getDoteyPayConfig(array('pay_type' => $condition['dotey_type']));
		$_monthRewardUids = array();
		if ($rs){
			$result['monthReward'] = $rs;
			foreach ($rs as $v){
				if($v['uid'] > 0){
					$uids[$v['uid']] = $v['uid'];
				}
			}
		}
		
		$doteyInfos = $this->userSer->getUserBasicByUids($uids);
		$this->render('dotey_rewards_policy_list',array('condition'=>$condition,'result'=>$result,'doteyInfo'=>$doteyInfos));
		//魅力点提现公式
		//根据签约类型来分别设置不同的报酬政策
		//修改 删除 添加
	}
	
	/**
	 * 主播经理 
	 */
	public function actionManager(){
		//导入经理人
		if($this->op == 'addManager' && in_array($this->op, $this->allowOp)){
			$this->addManagerDo();
		}
		
		//修改信息
		if($this->op == 'editManager' && in_array($this->op, $this->allowOp)){
			$this->editManagerDo();
		}
		
		$uids = array();
		$haveUids = array();
		$managerList = array();
		//已经存在的经理人
		$haveList = $this->doteySer->getProxyOrTutorList(DOTEY_MANAGER_TUTOR,false,true);
		if ($haveList){
			foreach ($haveList as $v){
				$haveUids[] = $v['uid'];
				$managerList[$v['uid']]['uid'] = $v['uid'];
				$managerList[$v['uid']]['is_display'] = $v['is_display']?true:false;
				$managerList[$v['uid']]['username'] = $v['user']['username'];
				$managerList[$v['uid']]['nickname'] = $v['user']['nickname'];
				$managerList[$v['uid']]['realname'] = $v['user']['realname'];
				$managerList[$v['uid']]['qq'] = $v['extend']['qq'];
				$managerList[$v['uid']]['mobile'] = $v['extend']['mobile'];
				$managerList[$v['uid']]['total_dotey'] = 0;
				$managerList[$v['uid']]['is_new'] = false;
			}
		}
		
		//管辖的主播
		if($haveUids){
			$mTotal = $this->doteySer->getProxyOrTutorManagerTotal(DOTEY_MANAGER_TUTOR,$haveUids);
			if($mTotal){
				foreach ($mTotal as $v){
					$managerList[$v['tutor_uid']]['total_dotey'] = $v['total_dotey'];
				}
			}
		}
		
		//可能新增的经理人
		$roleInfo = $this->purSer->getRoleByName(self::DOTEY_MANAGER_FLAG,PURVIEW_ROLETYPE_ADMIN);
		if($roleInfo){
			$newList = $this->purSer->getRoleUserByRoleId($roleInfo['role_id']);
			if ($newList){
				$_list = array();
				foreach ($newList as $k=>$v){
					if (!isset($managerList[$v['uid']])){
						$_list[$k]['uid'] = $uids[] = $v['uid'];
						$managerList[$v['uid']]['uid'] = $v['uid'];
						$managerList[$v['uid']]['is_display'] = true;
						$managerList[$v['uid']]['username'] = '';
						$managerList[$v['uid']]['nickname'] = '';
						$managerList[$v['uid']]['realname'] = '';
						$managerList[$v['uid']]['qq'] = '';
						$managerList[$v['uid']]['mobile'] = '';
						$managerList[$v['uid']]['total_dotey'] = 0;
						$managerList[$v['uid']]['is_new'] = true;
					}
				}
				$doteyInfo = $this->getDoteyInfo($_list);
				if ($doteyInfo){
					foreach ($doteyInfo as $v){
						$managerList[$v['uid']]['username'] = $v['username'];
						$managerList[$v['uid']]['nickname'] = $v['nickname'];
						$managerList[$v['uid']]['realname'] = $v['realname'];
					}
						
					//主播的扩展信息
					$extendInfo = $this->userSer->getUserExtendByUids($uids);
					if ($extendInfo){
						foreach($extendInfo as $v){
							$managerList[$v['uid']]['qq'] = $v['qq'];
							$managerList[$v['uid']]['mobile'] = $v['mobile'];
						}
					}
				}
			}
		}
		
		$this->render('dotey_manager',array('managerList'=>$managerList));
	}
	
	/**
	 * 主播代理 
	 */
	public function actionProxy(){
		$uids = array();
		$haveUids = array();
		$proxyList = array();
		
		if($this->op == 'proxyChange' && in_array($this->op, $this->allowOp)){
			$uid =  Yii::app()->request->getParam('uid');
			if($uid){
				$apply['uid'] = $uid;
				$apply['type'] = DOTEY_MANAGER_PROXY;
				$apply['status'] = 2;
				if($this->doteySer->saveDoteyApply($apply)){
					$add['type'] = DOTEY_MANAGER_PROXY;
					$add['is_display'] = 1;
					$add['query_allow'] = 1;
					$add['uid'] = $uid;
					$add['agency'] = 'empty';
					$add['company'] = 'empty';
					$this->doteySer->saveDoteyProxy($add);
					exit('1');
				}
				exit('审核失败');
			}else{
				exit('审核失败');
			}
		}
		
		$haveList = $this->doteySer->getProxyOrTutorList(DOTEY_MANAGER_PROXY,false,true);
		if ($haveList){
			foreach ($haveList as $v){
				$haveUids[] = $v['uid'];
				$proxyList[$v['uid']]['uid'] = $v['uid'];
				$proxyList[$v['uid']]['is_display'] = $v['is_display']?'显示':'隐藏';
				$proxyList[$v['uid']]['username'] = $v['user']['username'];
				$proxyList[$v['uid']]['nickname'] = $v['user']['nickname'];
				$proxyList[$v['uid']]['realname'] = $v['user']['realname'];
				$proxyList[$v['uid']]['agency'] = $v['agency'];
				$proxyList[$v['uid']]['company'] = $v['company'];
				$proxyList[$v['uid']]['query_allow'] = $v['query_allow']?'允许':'禁止';
				$proxyList[$v['uid']]['qq'] = $v['extend']['qq'];
				$proxyList[$v['uid']]['mobile'] = $v['extend']['mobile'];
				$proxyList[$v['uid']]['total_dotey'] = 0;
				$proxyList[$v['uid']]['status'] = 2;//通过
			}
		}
		
		//状态
		$infos = $this->doteySer->getApplyInfos($haveUids,DOTEY_MANAGER_PROXY);
		if($infos){
			foreach ($infos as $v){
				$proxyList[$v['uid']]['status'] = $v['status'];
			}
		}
		
		//管辖的主播
		if($haveUids){
			$mTotal = $this->doteySer->getProxyOrTutorManagerTotal(DOTEY_MANAGER_PROXY,$haveUids);
			if($mTotal){
				foreach ($mTotal as $v){
					$proxyList[$v['proxy_uid']]['total_dotey'] = $v['total_dotey'];
				}
			}
		}
		$this->render('dotey_proxy',array('proxyList'=>$proxyList));
	}
	
	/**
	 * 添加主播代理
	 */
	public function actionAddProxy(){
		//检查主播信息
		if($this->op == 'checkDoteyInfo' && in_array($this->op, $this->allowOp)){
			$this->checkDoteyInfo(false);
		}
	
		$info = array();
		$extendInfo = array();
		$uid = Yii::app()->request->getParam('uid');
		if ($uid){
			$info = $this->doteySer->getProxy($uid,DOTEY_MANAGER_PROXY);
			if ($info){
				$extendInfo = $this->userSer->getUserExtendByUids(array($uid));
			}
		}
	
		$notices = array();
		//添加
		if($this->op == 'addProxy' && in_array($this->op, $this->allowOp)){
			$notices = $this->addProxyDo();
		}
	
		if($this->isAjax){
			exit($this->renderPartial('dotey_add_proxy',array('info'=>$info,'extendInfo'=>$extendInfo)));
		}else{
			$this->render('dotey_add_proxy',array('info'=>$info,'notices'=>$notices,'extendInfo'=>$extendInfo));
		}
	}
	
	/**
	 * 主播星探
	 */
	public function actionStar(){
		$uids = array();
		$haveUids = array();
		$proxyList = array();
	
		if($this->op == 'proxyChange' && in_array($this->op, $this->allowOp)){
			$uid =  Yii::app()->request->getParam('uid');
			if($uid){
				$apply['uid'] = $uid;
				$apply['type'] = DOTEY_MANAGER_STAR;
				$apply['status'] = 2;
				if($this->doteySer->saveDoteyApply($apply)){
					$add['type'] = DOTEY_MANAGER_STAR;
					$add['is_display'] = 1;
					$add['query_allow'] = 1;
					$add['uid'] = $uid;
					$add['agency'] = 'empty';
					$add['company'] = 'empty';
					$this->doteySer->saveDoteyProxy($add);
					exit('1');
				}
				exit('审核失败');
			}else{
				exit('审核失败');
			}
		}
	
		$haveList = $this->doteySer->getProxyOrTutorList(DOTEY_MANAGER_STAR,false,true);
		if ($haveList){
			foreach ($haveList as $v){
				$haveUids[] = $v['uid'];
				$proxyList[$v['uid']]['uid'] = $v['uid'];
				$proxyList[$v['uid']]['is_display'] = $v['is_display']?'显示':'隐藏';
				$proxyList[$v['uid']]['username'] = $v['user']['username'];
				$proxyList[$v['uid']]['nickname'] = $v['user']['nickname'];
				$proxyList[$v['uid']]['realname'] = $v['user']['realname'];
				$proxyList[$v['uid']]['agency'] = $v['agency'];
				$proxyList[$v['uid']]['company'] = $v['company'];
				$proxyList[$v['uid']]['query_allow'] = $v['query_allow']?'允许':'禁止';
				$proxyList[$v['uid']]['qq'] = $v['extend']['qq'];
				$proxyList[$v['uid']]['mobile'] = $v['extend']['mobile'];
				$proxyList[$v['uid']]['total_dotey'] = 0;
				$proxyList[$v['uid']]['status'] = 2;//通过
			}
		}
	
		//状态
		$infos = $this->doteySer->getApplyInfos($haveUids,DOTEY_MANAGER_STAR);
		if($infos){
			foreach ($infos as $v){
				$proxyList[$v['uid']]['status'] = $v['status'];
			}
		}
	
		//管辖的主播
		if($haveUids){
			/* $mTotal = $this->doteySer->getProxyOrTutorManagerTotal(DOTEY_MANAGER_PROXY,$haveUids);
			if($mTotal){
				foreach ($mTotal as $v){
					$proxyList[$v['proxy_uid']]['total_dotey'] = $v['total_dotey'];
				}
			} */
		}
		$this->render('dotey_star',array('proxyList'=>$proxyList));
	}
	
	/**
	 * 添加主播代理
	 */
	public function actionAddStar(){
		//检查主播信息
		if($this->op == 'checkDoteyInfo' && in_array($this->op, $this->allowOp)){
			$this->checkDoteyInfo(false);
		}
	
		$info = array();
		$extendInfo = array();
		$uid = Yii::app()->request->getParam('uid');
		if ($uid){
			$info = $this->doteySer->getProxy($uid,DOTEY_MANAGER_STAR);
			if ($info){
				$extendInfo = $this->userSer->getUserExtendByUids(array($uid));
			}
		}
	
		$notices = array();
		//添加
		if($this->op == 'addProxy' && in_array($this->op, $this->allowOp)){
			$notices = $this->addProxyDo($this->createUrl('dotey/star'));
		}
	
		if($this->isAjax){
			exit($this->renderPartial('dotey_add_star',array('info'=>$info,'extendInfo'=>$extendInfo)));
		}else{
			$this->render('dotey_add_star',array('info'=>$info,'notices'=>$notices,'extendInfo'=>$extendInfo));
		}
	}
	
	/**
	 * 主播查询 
	 */
	public function actionDoteyList(){
		//是否下载Excel
		$isLimit = true;
		if ($this->op == 'dlDoteyListExcel' && in_array($this->op, $this->allowOp)){
			$condition = json_decode(Yii::app()->request->getParam('condition'),true);
			$isLimit = false;
		}
		$this->assetsMy97Date();
		$this->assetsArea();
		$condition = $this->getDoteySearchCondition();
		
		//是否是主播经理
		$isFilterSource = false;
		if ($this->authDoteyManager()){
			$isFilterSource = true;
			$_condition['sources'] = DOTEY_MANAGER_TUTOR.'#XX#'.$this->op_uid;
		}
		
		if (!empty($condition['dotey_type'])){
			$_condition['sources'] = $condition['dotey_type'];
		}
		
		if (isset($_condition['sources'])){
			$list = $this->doteySer->searchDoteyList($this->p,$this->pageSize,$_condition,false);
			if(isset($list['list'])){
				$condition['uids'] = array_keys($list['list']);
			}
			if(empty($condition['uids'])){
				$result = array('count'=>0,'list'=>array());
			}
		}
		
		//是否隐藏及节目搜索
		$archivesInfo = $this->searchArchivesInfo($condition,'common');
		if($archivesInfo){
			if ($archivesInfo != 1){
				$uids = array_keys($archivesInfo);
				if(isset($condition['uids'])){
					$condition['uids'] = array_intersect($condition['uids'], $uids);
					if(!$condition['uids']){
						$result = array('count'=>0,'list'=>array());
					}
				}else{
					$condition['uids'] = $uids;
				}
			}
		}else{
			$result = array('count'=>0,'list'=>array());
		}
		
		if(!isset($result)){
			$condition['sign_type'] = SIGN_TYPE_SHOW;
			$condition['status'] = array(APPLY_STATUS_SUCCESS,APPLY_STATUS_FACE,APPLY_STATUS_REFUES);
			$result = $this->doteySer->searchDoteyBase($condition,$this->offset,$this->pageSize,$isLimit);
		}
		
		$count = array_shift($result['count']);
		$list = $result['list'];
		#档期信息
		$this->getArchivesForCatInfo($list,'common');
		#消费信息
		$this->getConsumeInfo($list);
		#统计开播
		$this->getLiveRecordsInfo($list,'common');
		#家族信息
		$this->getFamilyInfo($list);
		if (!$isLimit){
			$this->dlDoteyListExcel($list);
		}
		
		if (isset($condition['uids'])){
			unset($condition['uids']);
		}
		
		$pager = new CPagination($count);
		$pager->pageSize = $this->pageSize;
		$pager->params =  $condition;
		$this->render('dotey_search_list', array('isFilterSource'=>$isFilterSource,'list' => $list, 'pager' => $pager, 'condition' => $condition));
	}
	
	/**
	 * 主播收礼明细记录 
	 */
	public function actionDoteyInGift(){
		if(!($uid = Yii::app()->request->getParam('uid'))){
			exit("缺少参数");
		}
		$condition = array();
		
		//是否下载Excel
		if ($this->op == 'dlInGiftExcel' && in_array($this->op, $this->allowOp)){
			$condition = json_decode(Yii::app()->request->getParam('condition'),true);
			$condition['uid'] = $uid;
			$this->dlInGiftRecordsExcel($uid,$condition);
		}

		if(Yii::app()->request->getParam('ingift')){
			$condition = Yii::app()->request->getParam('ingift');
			foreach($condition as $k=>$c){
				if (empty($c)){
					unset($condition[$k]);
				}
			}
		}else{
			if(Yii::app()->request->getParam('start_time')){
				$condition['start_time'] =  Yii::app()->request->getParam('start_time');
			}
			if(Yii::app()->request->getParam('end_time')){
				$condition['end_time'] =  Yii::app()->request->getParam('end_time');
			}
		}
		
		$condition['uid'] = $uid;
		
		//获取记录列表
		$giftSer = new GiftService();
		$inRecords = $giftSer->getUserGiftReceiveRecordsByUid($uid,$this->offset,$this->pageSize,$condition);
		
		//分页实例化
		$pager = new CPagination($inRecords['count']);
		$pager->pageSize= $this->pageSize;
		$pager->params = $condition;
		$this->render('dotey_ingift_records',array('pager'=>$pager,'records'=>$inRecords['list'],'condition'=>$condition));
	}
	
	/**
	 * 修改主播信息
	 */
	public function actionEditDotey(){
		if(!($uid = Yii::app()->request->getParam('uid'))){
			exit("缺少参数");
		}
		
		//是否修改
		$notices = array();
		if ($this->op == 'editDoteyBase' && in_array($this->op, $this->allowOp)){
			$notices = $this->editDoteyBase();
		}

		if(!($uinfo = $this->doteySer->searchDoteyBase(array('uid'=>$uid)))){
			exit('请求有误，无法操作');
		}
		
		//是否清除用户图像 
		if ($this->op == 'removeUserAvatar' && in_array($this->op, $this->allowOp)){
			exit($this->removeUserAvatar($uid));
		}
		
		//检查是否是签约主播
		if ($this->op == 'checkSignFamily' && in_array($this->op, $this->allowOp)){
			$famService = new FamilyService();
			if($famService->getDoteyMembers($uid)){
				exit('1');
			}
		}
		
		$this->assetsMy97Date();
		$this->assetsArea();
		$archivesSer = new ArchivesService();
		$catInfo = $archivesSer->getAllArchiveCatByEnName('common');
		$archivesInfo = array();
		if($catInfo){
			$cat_id = $catInfo['cat_id'];
			$archivesInfo = $archivesSer->getArchivesBycondition(array('uid'=>$uid,'cat_id'=>$cat_id),'uid');
			if($archivesInfo){
				$archivesInfo = $archivesInfo[$uid];
			}
		}
		if($this->isAjax){
			exit($this->renderPartial('dotey_edit_base',array('uinfo'=>$uinfo['list'][$uid],'archivesInfo'=>$archivesInfo,'catInfo'=>$catInfo,'notices'=>$notices)));
		}else{
			$this->render('dotey_edit_base',array('uinfo'=>$uinfo['list'][$uid],'archivesInfo'=>$archivesInfo,'catInfo'=>$catInfo,'notices'=>$notices));
		}
	}
	
	/**
	 * 主播详情
	 */
	public function actionDoteyDetail(){
		//收礼明细记录 导出Excel
		//筛选
	}
	
	/**
	 * 直播管理 
	 */
	public function actionOnLive(){
		$archivesSer = new ArchivesService();
		$this->assetsMy97Date();
		//关闭直播
		if($this->op == 'changeLiveStatus' && in_array($this->op, $this->allowOp)){
			$this->changeLiveStatus($archivesSer);
		}
		
		//改变直播信息
		if($this->op == 'editLiveRecords' && in_array($this->op, $this->allowOp)){
			$this->editLiveRecords($archivesSer);
		}
		
		$condition = $this->getOnLiveSearchCondition();
		$condition['no_status'] = array(INVALID_LIVE);//去掉无效的直播记录
		if(!isset($condition['status'])){
			$condition['status'] = START_LIVE;
		}
		
		//是否是主播经理
		$isFilterSource = false;
		if ($this->authDoteyManager()){
			$isFilterSource = true;
			if(empty($condition['sources'])){
				$condition['sources'] = DOTEY_MANAGER_TUTOR.'#XX#'.$this->op_uid;
			}
		}
		
		if(!empty($condition['sources'])){
			$_condition = array();
			$_condition['sources'] = $condition['sources'];
			$list = $this->doteySer->searchDoteyList($this->p,$this->pageSize,$_condition,false);
			if(isset($list['list'])){
				$condition['uids'] = array_keys($list['list']);
			}
			if(empty($condition['uids'])){
				$result = array('count'=>0,'list'=>array());
			}
		}
		
		if(!isset($result)){
			$result = $archivesSer->searchLiveRecordByCondition($condition,$this->offset,$this->pageSize);
		}
		
		$count = $result['count'];
		$list = $result['list'];
		$doteyInfo = $this->getDoteyInfo($list); 
	
		if (isset($condition['uids'])){
			unset($condition['uids']);
		}
		
		$pager = new CPagination($count);
		$pager->pageSize = $this->pageSize;
		$pager->params = $condition;
		
		$cates = $this->formatArchivesCat($archivesSer->getAllArchiveCat());
		$this->render('dotey_onlive_list', array('list' => $list, 'pager' => $pager, 'condition' => $condition, 
			'archivesSer' => $archivesSer, 'doteyInfo' => $doteyInfo, 'cates' => $cates,'isFilterSource'=>$isFilterSource));
	}
	
	/**
	 * 直播数据
	 * 	直播在线统计 
	 */
	public function actionLiveOnline(){
		$archivesSer = new ArchivesService();
		$condition = $this->getOnLiveSearchCondition();
		$condition['status'] = 2;
		
		//是否是主播经理
		if ($this->authDoteyManager()){
			$_condition = array();
			$_condition['sources'] = DOTEY_MANAGER_TUTOR.'#XX#'.$this->op_uid;;
			$list = $this->doteySer->searchDoteyList($this->p,$this->pageSize,$_condition,false);
			if(isset($list['list'])){
				$condition['uids'] = array_keys($list['list']);
			}
			if(empty($condition['uids'])){
				$result = array('count'=>0,'list'=>array());
			}
		}
		
		$isDuplicate = false;
		if(!isset($result)){
			if (isset($condition['remDuplicate']) && $condition['remDuplicate']){
				$isDuplicate = true;
				$result = $archivesSer->searchDuplicateLiveRecordsByCondition($condition,$this->offset,$this->pageSize);
			}else{
				$result = $archivesSer->searchLiveRecordByCondition($condition,$this->offset,$this->pageSize);
			}
		}
		
		$count = $result['count'];
		$list = $result['list'];
		$uids = array();
		$archivesIds = array();
		
		//主播信息
		$doteyInfo = $this->getDoteyInfo($list,$uids,$archivesIds);
		$transArchives = array_flip($archivesIds);
		$result = $archivesSer->getSessStatSumByCondition($archivesIds);
		
		$sessStatInfo = array();
		if ($result){
			foreach ($result as $k=>$v){
				$k = $v['archives_id'];
				$sessStatInfo[$k]['avg']=round($v['sum']/$v['count']);//平均在线人数
				$sessStatInfo[$k]['total'] = 0;
				$sessStatInfo[$k]['create_time'] = 0;
				$sessStatInfo[$k]['consume_many'] = 0;
				$sessStatInfo[$k]['send_total'] = 0;
				$sessStatInfo[$k]['send_avg'] = 0;
				$sessStatInfo[$k]['nickname'] = '';
			}
		}
		
		$archivePv = array();
		if(!$isDuplicate){
			//统计最高值
			$sortSessInfo = $archivesSer->getSessStatSumByCondition($archivesIds,array(),true);
			foreach ($sortSessInfo as $v){
				$sessStatInfo[$v['archives_id']]['total'] = $v['total'];
				$sessStatInfo[$v['archives_id']]['create_time'] = $v['create_time'];
			}
			//PV统计
			$archivePv = $this->getArchivesPv($list);
		}
		
		
		//昵称
		if($transArchives){
			foreach($transArchives as $archivesid => $uid){
				$sessStatInfo[$archivesid]['nickname'] = isset($doteyInfo[$uid]['nickname'])?$doteyInfo[$uid]['nickname']:'';
			}
		}
		
		//送礼人数 皮蛋数
		$giftSer =  new GiftService(); 
		$giftConsume = $giftSer->getGiftRecordsSumByTargetIds($archivesIds);
		if($giftConsume){
			foreach ($giftConsume as $consume){
				$sessStatInfo[$consume['target_id']]['consume_many'] = $consume['consume_many'];
				$sessStatInfo[$consume['target_id']]['send_total'] = $consume['send_total'];
				//人均单价
				$sessStatInfo[$consume['target_id']]['send_avg'] = round($consume['consume_many']/$consume['send_total'],2);
			}
		}
		
		if (isset($condition['uids'])){
			unset($condition['uids']);
		}
		
		$pager = new CPagination($count);
		$pager->pageSize = $this->pageSize;
		$pager->params = $condition;
		
		$tmp = $isDuplicate?'dotey_live_data_duplicate':'dotey_live_data';
		$this->render($tmp,array('pager'=>$pager,'list'=>$list,'sessStatInfo'=>$sessStatInfo,'condition'=>$condition,'archivePv'=>$archivePv));
	}
	
	/**
	 * 开播查询 
	 */
	public function actionOnLiveSearch(){
		$archives = new ArchivesService();
		
		$isLimit = true;
		//下载Excel
		if($this->op == 'dlOnliveSearchExcel' && in_array($this->op, $this->allowOp)){
			$isLimit = false;
		}
		
		//分页查询档期信息
		$searchCondition = array();
		$searchCondition = $condition = $this->getOnLiveSearchCondition();
		$searchCondition['group'] = 'a.archives_id';
		unset($searchCondition['live_time_on']);
		
		//是否是主播经理
		$isFilterSource = false;
		if ($this->authDoteyManager()){
			$isFilterSource = true;
			if(empty($searchCondition['sources'])){
				$searchCondition['sources'] = DOTEY_MANAGER_TUTOR.'#XX#'.$this->op_uid;
			}
		}
		
		if(!empty($searchCondition['sources'])){
			$_condition = array();
			$_condition['sources'] = $searchCondition['sources'];
			$list = $this->doteySer->searchDoteyList($this->p,$this->pageSize,$_condition,false);
			if(isset($list['list'])){
				$searchCondition['uids'] = array_keys($list['list']);
			}
			if(empty($searchCondition['uids'])){
				$result = array('count'=>0,'list'=>array());
			}
		}
		if (!isset($result)){
			$result = $archives->searchLiveRecordByCondition($searchCondition,$this->offset,$this->pageSize,$isLimit);
		}
		$count = $result['count'];
		$list = array();
		if($count){
			//主播信息和档期结果集
			$archivesIds = array();
			$uids = array();
			$doteyInfo = $this->getDoteyInfo($result['list'],$uids,$archivesIds);
			$effectDayUnit = $archives->getLiveEffectDaysUnit($uids);
			//组装数据
			if ($result['list']){
				foreach ($result['list'] as $v){
					$list[$v['archives_id']]['uid'] = $v['uid'];
					$list[$v['archives_id']]['title'] = $v['title'];
					$list[$v['archives_id']]['archives_id'] = $v['archives_id'];
					$list[$v['archives_id']]['nickname'] = isset($doteyInfo[$v['uid']])?$doteyInfo[$v['uid']]['nickname']:'';
					$list[$v['archives_id']]['has_days_unit'] = isset($effectDayUnit[$v['uid']])?$effectDayUnit[$v['uid']]:2;
					$list[$v['archives_id']]['has_days'] = array();
					$list[$v['archives_id']]['has_hours'] = 0;
					$list[$v['archives_id']]['detail'] = array();
				}
			}
			//档期直播时长明细
			if (!isset($condition['live_time_on']) || empty($condition['live_time_on'])){
				$condition['live_time_on'] = date('Y-m',strtotime('-1 months'));
			}
			$detailResult = $archives->searchLiveRecordByArchivesIds($archivesIds,$condition);
			if ($detailResult){
				foreach($detailResult as $v){
					$k =ltrim(substr($v['end_time'], -2),'0');
					if (!isset($list[$v['archives_id']]['detail'][$k])){
						$list[$v['archives_id']]['detail'][$k] = $v['duration'];
					}else{
						$list[$v['archives_id']]['detail'][$k] += $v['duration'];
					}
			
					$list[$v['archives_id']]['has_hours'] += $v['duration'];
					if ($list[$v['archives_id']]['has_days_unit']*3600 <= $v['duration']){
						if (!isset($list[$v['archives_id']]['has_days'][$v['end_time']])){
							$list[$v['archives_id']]['has_days'][$v['end_time']] = $v['duration'];
						}else{
							$list[$v['archives_id']]['has_days'][$v['end_time']] += $v['duration'];
						}
					}
				}
			}
		}
		
		//下载Excel
		if($this->op == 'dlOnliveSearchExcel' && in_array($this->op, $this->allowOp)){
			$this->dlOnliveSearchExcel($list,$condition['live_time_on']);
		}
		
		if (isset($condition['uids'])){
			unset($condition['uids']);
		}
		
		$pager = new CPagination($count);
		$pager->pageSize = $this->pageSize;
		$pager->params = $condition;
		
		$this->render('dotey_onlive_search',array('condition'=>$condition,'list'=>$list,'pager'=>$pager,'isFilterSource'=>$isFilterSource));
	}
	
	/**
	 * 收入查询 
	 */
	public function actionIncomeSearch(){
		$archives = new ArchivesService();
		$consumeSer = new ConsumeService();
		$giftSer = new GiftService();
		
		$isLimit = true;
		//下载Excel
		if($this->op == 'dlIncomeSearchExcel' && in_array($this->op, $this->allowOp)){
			$isLimit = false;
		}
		
		//查询直播信息
		$searchCondition = array();
		$searchCondition = $condition = $this->getOnLiveSearchCondition();
		$searchCondition['group'] = 'a.archives_id';
		unset($searchCondition['live_time_on']);
		
		//是否是主播经理
		$isFilterSource = false;
		if ($this->authDoteyManager()){
			$isFilterSource = true;
			if(empty($searchCondition['sources'])){
				$searchCondition['sources'] = DOTEY_MANAGER_TUTOR.'#XX#'.$this->op_uid;
			}
		}
		
		if(!empty($searchCondition['sources'])){
			$_condition = array();
			$_condition['sources'] = $searchCondition['sources'];
			$list = $this->doteySer->searchDoteyList($this->p,$this->pageSize,$_condition,false);
			if(isset($list['list'])){
				$searchCondition['uids'] = array_keys($list['list']);
			}
			if(empty($searchCondition['uids'])){
				$result = array('count'=>0,'list'=>array());
			}
		}
		
		if (!isset($result)){
			$result = $archives->searchLiveRecordByCondition($searchCondition,$this->offset,$this->pageSize,$isLimit);
		}
		
		$count = $result['count'];
		$list = array();
		if($count){
			//主播信息和档期结果集
			$archivesIds = array();
			$uids = array();
			$doteyInfo = $this->getDoteyInfo($result['list'],$uids,$archivesIds);

			//组装数据
			if ($result['list']){
				foreach ($result['list'] as $v){
					$list[$v['archives_id']]['uid'] = $v['uid'];
					$list[$v['archives_id']]['title'] = $v['title'];
					$list[$v['archives_id']]['archives_id'] = $v['archives_id'];
					$list[$v['archives_id']]['nickname'] = isset($doteyInfo[$v['uid']])?$doteyInfo[$v['uid']]['nickname']:'';
					$list[$v['archives_id']]['total_charm_point'] = 0;
					$list[$v['archives_id']]['detail'] = array();
				}
			}
				
			if (!isset($condition['live_time_on']) || empty($condition['live_time_on'])){
				$condition['live_time_on'] = date('Y-m',strtotime('-1 months',time()));
			}
			//档期送礼明细
			$giftDetail = $giftSer->searchGiftRecordsByTargetIds($archivesIds,$condition);
			//档期点歌明细
			$songDetail = $consumeSer->searchSongByTargetIds($archivesIds,$condition);
			
			if ($giftDetail){
				foreach($giftDetail as $v){
					$k =ltrim(substr($v['create_time'], -2),'0');
					if (!isset($list[$v['target_id']]['detail'][$k])){
						$list[$v['target_id']]['detail'][$k] = round($v['charm_points']/TRANS_CHARMPOINTS_TO_PIPIEGGS,2);
					}else{
						$list[$v['target_id']]['detail'][$k] += round($v['charm_points']/TRANS_CHARMPOINTS_TO_PIPIEGGS,2);
					}
						
					$list[$v['target_id']]['total_charm_point'] += round($v['charm_points']/TRANS_CHARMPOINTS_TO_PIPIEGGS,2);
				}
			}
			
			if ($songDetail){
				foreach($songDetail as $v){
					$k =ltrim(substr($v['update_time'], -2),'0');
					if (!isset($list[$v['target_id']]['detail'][$k])){
						$list[$v['target_id']]['detail'][$k] = round($v['charm_points']/TRANS_CHARMPOINTS_TO_PIPIEGGS,2);
					}else{
						$list[$v['target_id']]['detail'][$k] += round($v['charm_points']/TRANS_CHARMPOINTS_TO_PIPIEGGS,2);
					}
			
					$list[$v['target_id']]['total_charm_point'] += round($v['charm_points']/TRANS_CHARMPOINTS_TO_PIPIEGGS,2);
				}
			}
		}
		
		//下载Excel
		if($this->op == 'dlIncomeSearchExcel' && in_array($this->op, $this->allowOp)){
			$this->dlIncomeSearchExcel($list,$condition['live_time_on']);
		}
		
		if (isset($condition['uids'])){
			unset($condition['uids']);
		}
		
		$pager = new CPagination($count);
		$pager->pageSize = $this->pageSize;
		$pager->params = $condition;
		
		$this->render('dotey_income_search',array('condition'=>$condition,'list'=>$list,'pager'=>$pager,'isFilterSource'=>$isFilterSource));
	}
	
	/**
	 * 停播管理
	 */
	public function actionStopLive(){
		$archivesSer = new ArchivesService();
		$consumeSer = new ConsumeService();
		$giftSer = new GiftService();
		
		$isLimit = true;
		$condition = $this->getDoteySearchCondition();
		$condition['user_status'] = USER_STATUS_OFF;
		//恢复账号
		if($this->op == 'restoreStopLive' && in_array($this->op, $this->allowOp)){
			$this->restoreAccount();
		}
		
		//下载Excel
		if($this->op == 'dlStopLiveExcel' && in_array($this->op, $this->allowOp)){
			$isLimit = false;
		}
		
		//是否是主播经理
		$isFilterSource = false;
		if ($this->authDoteyManager()){
			$isFilterSource = true;
			if(empty($condition['sources'])){
				$condition['sources'] = DOTEY_MANAGER_TUTOR.'#XX#'.$this->op_uid;
			}
		}
		
		if(!empty($condition['sources'])){
			$_condition = array();
			$_condition['sources'] = $condition['sources'];
			$list = $this->doteySer->searchDoteyList($this->p,$this->pageSize,$_condition,false);
			if(isset($list['list'])){
				$condition['uids'] = array_keys($list['list']);
			}
			if(empty($condition['uids'])){
				$result = array('count'=>0,'list'=>array());
			}
		}
		
		if (!isset($result)){
			$result = $this->doteySer->searchDoteyBase($condition,$this->offset,$this->pageSize,$isLimit);
		}
		
		$count = array_shift($result['count']);
		$list = array();
		$uids = array();
		
		if ($result['list']){
			$uids = array_keys($result['list']);
			foreach ($result['list'] as $uid=>$v){
				$list[$v['uid']]['username'] = $v['username'];
				$list[$v['uid']]['nickname'] = $v['nickname'];
				$list[$v['uid']]['realname'] = $v['realname'];
				$list[$v['uid']]['uid'] = $v['uid'];
				$list[$v['uid']]['reg_time'] = $v['create_time'];
				$list[$v['uid']]['archives_name'] = '';
				$list[$v['uid']]['dotey_rank'] = '';
				$list[$v['uid']]['user_rank'] = '';//富豪等级
				$list[$v['uid']]['total_pipieggs'] = 0; //总皮蛋
				$list[$v['uid']]['consume_pipiegg'] = 0;//消费的皮蛋
				$list[$v['uid']]['prev_charm_points'] = 0;//上月魅力点收入
				$list[$v['uid']]['15days_charm_points'] = 0;//最近15天魅力点收入
				$list[$v['uid']]['last_live_time'] = '';//最后一次直播
				$list[$v['uid']]['stop_time'] = $v['update_time'];
			}
			
			//理由
			$reason = $this->userSer->getUserOperatedByUids($uids,USER_OPERATED_TYPE_USERSTATUS,USER_STATUS_OFF);
			if($reason){
				$uids2 = array();
				foreach ($reason as $v){
					$uids2[$v['op_uid']] = $v['op_uid'];
					$list[$v['uid']]['reason'] = $v;
				}
				if ($uids2){
					$uinfos2 = $this->userSer->getUserBasicByUids($uids2);
					if ($uinfos2){
						foreach ($uinfos2 as $uinfo){
							$list[$v['uid']]['op_uinfo'] = $uinfo;
						}
					}
				}
			}
			
			//节目 信息
			/* $archivesInfo = $archivesSer->getArchivesBycondition(array('uids'=>$uids),'uid');
			if ($archivesInfo){
				foreach($archivesInfo as $uid => $info){
					$list[$uid]['archives_name'] = $info['title'];
				}
			} */
			//最后一次开播记录
			$lastLiveInfo = $archivesSer->getArchivesByUids($uids);
			if ($lastLiveInfo){
				foreach ($lastLiveInfo as $info){
					$list[$info['uid']]['last_live_time'] = isset($info['live_record']['live_time'])?$info['live_record']['live_time']:'';
					$list[$info['uid']]['archives_name'] = $info['title'];
				}
			}
			
			//等级相关
			$consumeInfo = $consumeSer->getConsumesByUids($uids);
			$userRanks = $consumeSer->getUserRankFromRedis();
			$doteyRanks = $consumeSer->getDoteyRankFromRedis();
			if ($consumeInfo){
				foreach ($consumeInfo as $uid => $info){
					$list[$uid]['total_pipieggs'] = $info['pipiegg']-$info['freeze_pipiegg'];
					$list[$uid]['consume_pipiegg'] = $info['consume_pipiegg'];
					$list[$uid]['user_rank'] = isset($userRanks[$info['rank']])?$userRanks[$info['rank']]['name']:'无';
					$list[$uid]['dotey_rank'] = isset($doteyRanks[$info['dotey_rank']])?$doteyRanks[$info['dotey_rank']]['name']:'新人';
				}
			}
			
			//销售 送礼 前一个月
			$pre_month = date('Y-m',strtotime('-1 months',time()));
			$cur_month = date('Y-m',time());
			$giftInfo =$giftSer->searchGiftRecordsByUids($uids,array('live_time_on'=>$pre_month,'live_time_end'=>$cur_month));
			if ($giftInfo){
				foreach ($giftInfo as $uid => $info){
					$list[$uid]['prev_charm_points'] += $info['charm_points'];
				}
			}
			
			//销售 点歌 前一个月
			$songInfo = $consumeSer->searchSongByToUids($uids,array('live_time_on'=>$pre_month,'live_time_end'=>$cur_month));
			if ($songInfo){
				foreach ($songInfo as $uid => $info){
					$list[$uid]['prev_charm_points'] += $info['charm_points'];
				}
			}
			
			//销售送礼 最近15天
			$pre15_time = date('Y-m-d',strtotime('-15 days',time()));
			$cur_time = date('Y-m-d',time());
			$giftInfo =$giftSer->searchGiftRecordsByUids($uids,array('live_time_on'=>$pre15_time,'live_time_end'=>$cur_time));
			if ($giftInfo){
				foreach ($giftInfo as $uid => $info){
					$list[$uid]['15days_charm_points'] += $info['charm_points'];
				}
			}
			
			//销售 点歌 前十五天
			$songInfo = $consumeSer->searchSongByToUids($uids,array('live_time_on'=>$pre15_time,'live_time_end'=>$cur_time));
			if ($songInfo){
				foreach ($songInfo as $uid => $info){
					$list[$uid]['15days_charm_points'] += $info['charm_points'];
				}
			}
		}
		
		//下载Excel
		if($this->op == 'dlStopLiveExcel' && in_array($this->op, $this->allowOp)){
			$this->dlStopLiveExcel($list);
		}
		
		if (isset($condition['uids'])){
			unset($condition['uids']);
		}
		
		$pager = new CPagination($count);
		$pager->pageSize = $this->pageSize;
		$pager->params = $condition;
		
		$this->render('dotey_stop_live',array('pager'=>$pager,'list'=>$list,'condition'=>$condition,'isFilterSource'=>$isFilterSource));
	}
	
	/**
	 * 直播间管理
	 */
	public function actionArchivesList(){
		$archivesSer = new ArchivesService();
		$recommond = $archivesSer->getArchivesRecommond();
		$is_hide  = $archivesSer->getArchivesIsHide();
		$cat_ids = $this->formatArchivesCat($archivesSer->getAllArchiveCat());
		$condition = $this->getDoteySearchCondition();
		$result = $archivesSer->searchArchivesByCondition($condition,$this->offset,$this->pageSize);
		$count = $result['count'];
		$list = $result['list'];

		$uids = array();
		$archivesIds = array();
		$doteyInfo = $this->getDoteyInfo($list,$uids,$archivesIds);
				
		$pager = new CPagination($count);
		$pager->pageSize = $this->pageSize;
		$pager->params = $condition;
		
		$this->render('dotey_archives_list', array('cat_ids' => $cat_ids, 'condition' => $condition, 'pager' => $pager, 
			'list' => $list, 'doteyInfo' => $doteyInfo, 'recommond' => $recommond,'is_hide'=>$is_hide));
	}
	
	/**
	 * 编辑直播间
	 */
	public function actionEditArchives(){
		$archivesId = Yii::app()->request->getParam('archives_id');
		if (!$archivesId){
			exit('缺少参数');
		}
		
		$archivesSer = new ArchivesService();
		$info = $archivesSer->getArchivesByArchivesId($archivesId);
		if(!$info){
			exit('获取信息失败，请核查');
		}
		
		$uid = $info['uid'];
		$uinfo = $this->userSer->getUserBasicByUids(array($uid));
		$info['uinfo'] = $uinfo[$uid];
		
		$info['dinfo'] = $this->doteySer->getDoteyInfoByUid($uid);

		$info['live_server'] = $this->getLiveServices(2);
		$info['server_id'] = '';
		$info['server_rel_id'] = '';
		$serverInfo = $archivesSer->getArchivesLiveServerByArchivesId($archivesId);
		if($serverInfo){
			$info['server_id'] = $serverInfo[0]['server_id'];
			$info['server_rel_id'] = $serverInfo[0]['id'];
		}
		
		$notices = array();
		if($this->op == 'editArchives' && in_array($this->op,$this->allowOp)){
			$notices = $this->editArchivesDo();
		}
		
		if($this->isAjax){
			$this->renderPartial('dotey_edit_archives',array('info'=>$info,'archivesSer'=>$archivesSer));
		}else{
			$this->render('dotey_edit_archives',array('info'=>$info,'archivesSer'=>$archivesSer,'notices'=>$notices));
		}
		
	}
	
	/**
	 * 点歌查询 
	 */
	public function actionVODQuery(){
		$this->assetsMy97Date();
		$condition = $this->getVodSearchCondition();
		
		//是否下载Excel
		$isLimit = true;
		if ($this->op == 'dlVODQueryExcel' && in_array($this->op, $this->allowOp)){
			$isLimit = false;
		}
		
		//是否是主播经理
		$isFilterSource = false;
		if ($this->authDoteyManager()){
			$isFilterSource = true;
			if(empty($condition['sources'])){
				$condition['sources'] = DOTEY_MANAGER_TUTOR.'#XX#'.$this->op_uid;
			}
		}
		
		if(!empty($condition['sources'])){
			$_condition = array();
			$_condition['sources'] = $condition['sources'];
			$list = $this->doteySer->searchDoteyList($this->p,$this->pageSize,$_condition,false);
			if(isset($list['list'])){
				$condition['to_uids'] = array_keys($list['list']);
			}
			if(empty($condition['to_uids'])){
				$songRecords = array('count'=>0,'list'=>array());
			}
		}
		$doteySongSer = new DoteySongService();
		if(!isset($songRecords)){
			//获取记录列表
			$songRecords = array();
			$songRecords = $doteySongSer->searchVODRecordsByCondition($this->offset,$this->pageSize,$condition,$isLimit);
		}
		$songRecords['handlers'] = $doteySongSer->getDoteySongHandler();
		
		if (!empty($songRecords['list'])){
			$uids = array();
			$to_uids = array();
			
			foreach ($songRecords['list'] as $v){
				$uids[$v['uid']] = $v['uid'];
				$to_uids[$v['to_uid']] = $v['to_uid'];
			}
			if($to_uids){
				$songRecords['doteyInfos'] = $this->userSer->getUserBasicByUids($to_uids);
			}
			if($uids){
				$songRecords['userInfos'] = $this->userSer->getUserBasicByUids($uids);
			}
		}
		if (!$isLimit){
			$this->dlVODQueryExcel($songRecords);
		}

		if(isset($condition['to_uids'])){
			unset($condition['to_uids']);
		}
		//分页实例化
		$pager = new CPagination($songRecords['count']);
		$pager->pageSize= $this->pageSize;
		$pager->params = $condition;
		$this->render('dotey_vod_query',array('pager'=>$pager,'records'=>$songRecords,'condition'=>$condition));
	}
	
	/**
	 * 有播点歌统计
	 */
	public function actionVODStat(){
		$this->assetsMy97Date();
		$condition = $this->getVodSearchCondition();
		
		//是否下载Excel
		$isLimit = true;
		if ($this->op == 'dlVODStatExcel' && in_array($this->op, $this->allowOp)){
			$isLimit = false;
		}
		
		//是否是主播经理
		$isFilterSource = false;
		if ($this->authDoteyManager()){
			$isFilterSource = true;
			if(empty($condition['sources'])){
				$condition['sources'] = DOTEY_MANAGER_TUTOR.'#XX#'.$this->op_uid;
			}
		}
		
		if(!empty($condition['sources'])){
			$_condition = array();
			$_condition['sources'] = $condition['sources'];
			$list = $this->doteySer->searchDoteyList($this->p,$this->pageSize,$_condition,false);
			if(isset($list['list'])){
				$condition['to_uids'] = array_keys($list['list']);
			}
			if(empty($condition['to_uids'])){
				$songRecords = array('count'=>0,'list'=>array());
			}
		}
		$doteySongSer = new DoteySongService();
		if(!isset($songRecords)){
			//是否是唱区主播
			if(isset($condition['dotey_cat']) && $condition['dotey_cat'] == 2){
				$channelSer = new ChannelService();
				$songCondition['channel_name'] = CHANNEL_THEME;
				$songCondition['sub_name'] = CHANNEL_THEME_SONG;
				if(isset($condition['to_uids'])){
					$songCondition['uid'] = $condition['to_uids'];
				}
				if(isset($condition['to_uid'])){
					$songCondition['uid'] = $condition['to_uid'];
				}
				$info = $channelSer->getDoteysOfSong();
				if($info){
					foreach($info as $v){
						$condition['to_uids'][] = $v['uid'];
					}
				}
			}
			//获取记录列表
			$songRecords = $doteySongSer->searchVODStatByCondition($this->offset,$this->pageSize,$condition,$isLimit);
		}
		
		if (!empty($songRecords['list'])){
			$uids = array();
			$to_uids = array();
			foreach ($songRecords['list'] as $v){
				if (isset($v['uid'])){
					$uids[$v['uid']] = $v['uid'];
				}
				$to_uids[$v['to_uid']] = $v['to_uid'];
			}
			if($to_uids){
				$songRecords['doteyInfos'] = $this->userSer->getUserBasicByUids($to_uids);
			}
			if($uids){
				$songRecords['userInfos'] = $this->userSer->getUserBasicByUids($uids);
			}
		}
		
		if (!$isLimit){
			$this->dlVODStatExcel($songRecords);
		}
		
		if(isset($condition['to_uids'])){
			unset($condition['to_uids']);
		}
		//分页实例化
		$pager = new CPagination($songRecords['count']);
		$pager->pageSize= $this->pageSize;
		$pager->params = $condition;
		$this->render('dotey_vod_stat',array('pager'=>$pager,'records'=>$songRecords,'condition'=>$condition));
	}
	
	public function actionEditFamily(){
		$famService = new FamilyService();
		
		//踢出家族
		if ($this->op == 'delOperate' && in_array($this->op, $this->allowOp)){
			$operateId = Yii::app()->request->getParam('operateId',false);
			if ($operateId){
				$operateId = explode('_', $operateId);
				if (count($operateId) != 2) exit('参数有误');
				$familyId = intval($operateId[0])?intval($operateId[0]):0;
				$uid = intval($operateId[1])?intval($operateId[1]):0;
				if ($familyId && $uid){
					if($famService->kick($familyId, array($uid),Yii::app()->user->id,0)){
						exit('1');
					}else{
						exit('踢出家族失败');
					}
				}
			}
			exit('操作失败');
		}
		
		//添加到新的家族
		if ($this->op == 'addFamily' && in_array($this->op, $this->allowOp)){
			$uid = intval(Yii::app()->request->getParam('uid',false));
			$familyId = intval(Yii::app()->request->getParam('familyId',false));
			if ($uid && $familyId){
				if ($familyId && $uid){
					if($famService->join($familyId, $uid,false)){
						exit('1');
					}else{
						$error = $famService->getNotice();
						foreach($error as $e){
							echo $e."\n";
						}
						exit('加入家族失败');
					}
				}
			}
			exit('操作失败');
		}
		
		//显示我所有的家族及操作界面
		$uid = intval(Yii::app()->request->getParam('uid',0));
		$type = intval(Yii::app()->request->getParam('type','dotey'));
		if($uid){
			$myFamilyInfo = $famService->getMyFamily($uid);
			$userInfos = array();
			if ($myFamilyInfo){
				$uids = array();
				if(!empty($myFamilyInfo['create'])){
					$uids[$myFamilyInfo['create']['uid']] = $myFamilyInfo['create']['uid'];
				}
				if(!empty($myFamilyInfo['join'])){
					foreach($myFamilyInfo['join'] as $v){
						$uids[$v['uid']] =$v['uid'];
					}
				}
				if($uids){
					$userInfos = $this->userSer->getUserBasicByUids($uids);
				}
			}
			exit($this->renderPartial('dotey_family_edit',array('myFamilyInfos'=>$myFamilyInfo,'userInfos'=>$userInfos,'uid'=>$uid,'type'=>$type)));
		}
		exit('参数有误');
	}
	
	/**
	 * 获取主播查的的条件
	 * @return Ambigous <multitype:mixed Ambigous <mixed, unknown> , mixed, unknown>
	 */
	public function getDoteySearchCondition(){
		$condition = array();
		if(Yii::app()->request->getParam('form')){
			$condition = Yii::app()->request->getParam('form');
			return $condition;
		}
		
		if(Yii::app()->request->getParam('username')){
			$condition['username'] = Yii::app()->request->getParam('username');
		}
		
		if(Yii::app()->request->getParam('nickname')){
			$condition['nickname'] = Yii::app()->request->getParam('nickname');
		}
		
		if(Yii::app()->request->getParam('uid')){
			$condition['uid'] = Yii::app()->request->getParam('uid');
		}
		
		if(Yii::app()->request->getParam('realname')){
			$condition['realname'] = Yii::app()->request->getParam('realname');
		}
		
		if(Yii::app()->request->getParam('create_time_start')){
			$condition['create_time_start'] = Yii::app()->request->getParam('create_time_start');
		}
		if(Yii::app()->request->getParam('status')){
			$condition['status'] = Yii::app()->request->getParam('status');
		}
		
		if(Yii::app()->request->getParam('create_time_end')){
			$condition['create_time_end'] = Yii::app()->request->getParam('create_time_end');
		}
		
		if(Yii::app()->request->getParam('type')){
			$condition['type'] = Yii::app()->request->getParam('type');
		}
		
		if(is_numeric(Yii::app()->request->getParam('sources'))){
			$condition['sources'] = Yii::app()->request->getParam('sources');
		}
		
		if(Yii::app()->request->getParam('has_experience')){
			$condition['has_experience'] = Yii::app()->request->getParam('has_experience');
		}
		
		if(Yii::app()->request->getParam('dotey_type')){
			$condition['dotey_type'] = Yii::app()->request->getParam('dotey_type');
		}
		
		if(Yii::app()->request->getParam('pay_time')){
			$condition['pay_time'] = Yii::app()->request->getParam('pay_time');
		}
		
		if(Yii::app()->request->getParam('pay_time_start')){
			$condition['pay_time_start'] = Yii::app()->request->getParam('pay_time_start');
		}
		
		if(Yii::app()->request->getParam('pay_time_end')){
			$condition['pay_time_end'] = Yii::app()->request->getParam('pay_time_end');
		}
		
		if(Yii::app()->request->getParam('sign_type')){
			$condition['sign_type'] = Yii::app()->request->getParam('sign_type');
		}
		
		if(Yii::app()->request->getParam('cat_id')){
			$condition['cat_id'] = Yii::app()->request->getParam('cat_id');
		}
		
		if(Yii::app()->request->getParam('recommond')){
			$condition['recommond'] = Yii::app()->request->getParam('recommond');
		}
		
		if(Yii::app()->request->getParam('archives_id')){
			$condition['archives_id'] = Yii::app()->request->getParam('archives_id');
		}
		
		if(Yii::app()->request->getParam('city')){
			$condition['city'] = Yii::app()->request->getParam('city');
		}
		
		if(Yii::app()->request->getParam('archives_title')){
			$condition['archives_title'] = Yii::app()->request->getParam('archives_title');
		}
		
		return $condition;
	}
	
	/**
	 * 获取直播查询条件
	 * @return Ambigous <multitype:mixed Ambigous <mixed, unknown> , mixed, unknown>
	 */
	public function getOnLiveSearchCondition(){
		$condition = array();
		if(Yii::app()->request->getParam('form')){
			$condition = Yii::app()->request->getParam('form');
		}
	
		if(Yii::app()->request->getParam('realname')){
			$condition['realname'] = Yii::app()->request->getParam('realname');
		}
		
		if(Yii::app()->request->getParam('username')){
			$condition['username'] = Yii::app()->request->getParam('username');
		}
		
		if(Yii::app()->request->getParam('nickname')){
			$condition['nickname'] = Yii::app()->request->getParam('nickname');
		}
	
		if(Yii::app()->request->getParam('live_time_on')){
			$condition['live_time_on'] = Yii::app()->request->getParam('live_time_on');
		}
		
		if(Yii::app()->request->getParam('status')){
			$condition['status'] = Yii::app()->request->getParam('status');
		}
	
		if(Yii::app()->request->getParam('live_time_end')){
			$condition['live_time_end'] = Yii::app()->request->getParam('live_time_end');
		}
		
		if(Yii::app()->request->getParam('uid')){
			$condition['uid'] = Yii::app()->request->getParam('uid');
		}
		
		if(Yii::app()->request->getParam('record_id')){
			$condition['record_id'] = Yii::app()->request->getParam('record_id');
		}
		
		if(Yii::app()->request->getParam('is_hide')){
			$condition['is_hide'] = Yii::app()->request->getParam('is_hide');
		}
		
		if(Yii::app()->request->getParam('remDuplicate')){
			$condition['remDuplicate'] = Yii::app()->request->getParam('remDuplicate');
		}
		return $condition;
	}
	
	/**
	 * 获取主播信息
	 * 
	 * @param array $list
	 * @param unknown_type $uids
	 * @param unknown_type $archivesIds
	 * @return Ambigous <multitype:, mix, multitype:NULL >
	 */
	public function getDoteyInfo(Array $list,&$uids = array(),&$archivesIds = array(),$isUserInfo = true){
		if ($list) {
			foreach ($list as $v){
				if (isset($v['uid'])){
					if(!in_array($v['uid'], $uids)){
						$uids[] = $v['uid'];
					}
					if (isset($v['archives_id'])){
						if(!in_array($v['archives_id'], $archivesIds)){
							$archivesIds[$v['uid']] = $v['archives_id'];
						}
					}
				}
			}
			
			if($uids && $isUserInfo){
				return $this->userSer->getUserBasicByUids($uids);
			}
		}
		return array();
	}
	
	/**
	 * 获取平台奖励类型
	 */
	public function getAwardType(){
		$consume = new ConsumeService();
		return $consume->getAwardType();
	}
	
	/**
	 * 获取代理和导师的列表项
	 */
	public function getProxyAndTutorListOption($isFilter = false,$type = null){
		$proxyList = array();
		$tutorList = array();
		$types = array(DOTEY_MANAGER_PROXY,DOTEY_MANAGER_TUTOR);
		if($type && in_array($type, $types)){
			$types = array($type);
		}
		$list = $this->doteySer->getProxyOrTutorList(0,false,false);
		if ($list){
			foreach ($list as $v){
				if(in_array($v['type'], $types)){
					if($v['type'] == DOTEY_MANAGER_PROXY){
						$proxyList[$v['type'].'#XX#'.$v['uid']] = '代理-'.$v['agency'];
					}else{
						if(!$isFilter){
							$tutorList[$v['type'].'#XX#'.$v['uid']] = '导师-'.$v['user']['nickname'];
						}else{
							if($v['uid'] == $this->op_uid){
								$tutorList[$v['type'].'#XX#'.$v['uid']] = '导师-'.$v['user']['nickname'];
							}
						}
					}
				}
			}
		}
		return array_merge($proxyList,$tutorList);
	}
	
	/**
	 * 获取申请信息
	 */
	public function getApplyInfo(){
		$uid = Yii::app()->request->getParam('uid');
		if (intval($uid)) {
			$info = $this->doteySer->getApplyDoteyInfo($uid);
			if($info){
				$doteyInfo = $this->doteySer->getDoteyInfoByUid($uid);
				$extInfo = $this->userSer->getUserExtendByUids(array($uid));
				$userInfo = $this->userSer->getUserBasicByUids(array($uid));
				exit($this->renderPartial('dotey_apply_info',array('info'=>$info,'doteyInfo'=>$doteyInfo,'userInfo'=>$userInfo[$uid],'extInfo'=>$extInfo[$uid])));
			}else{
				exit('获取数据失败，可能是旧主播没有申请信息的缘故');
			}
		}else{
			exit('缺少参数');
		}
	}
	
	/**
	 * 获取直播间链接
	 */
	public function getArchivesUrl(){
		return 'http://show.'.trim(DOMAIN,'.').'/index.php?r=archives/index/uid/';
	}
	
	/**
	 * 获取每日开播的PV统计
	 * 
	 * @param array $list
	 */
	public function getArchivesPv(Array $list){
		$archiveArr = array();
		if($list){
			$archivePath = SHOWSTAT.'chatview/';
			$_tmp = array();
			$archivesIds = array();
			foreach($list as $v){
				$archiveFile = $archivePath . 'pv.'.date('Y-m-d', $v['live_time']);
				if (file_exists($archiveFile)) {
					$_fdate = date('Ymd', $v['live_time']);
					$_tmp [$_fdate] = $archiveFile;
					$archivesIds[$_fdate][$v['archives_id']] = $v['archives_id'];
				}
			}
			if ($_tmp){
				foreach($archivesIds as $_fdate=>$ids){
					if(isset($_tmp[$_fdate])){
						$filePath = $_tmp[$_fdate];
						$archiveContent = file_get_contents($filePath);
						if($archiveContent){
							foreach ($ids as $id){
								preg_match_all('/archives_id:' . $id . ',pv:(\d+),login_pv:(\d+),ip:(\d+)/is', $archiveContent,$archiveContentArr);
								if (isset($archiveContentArr[0])) {
									$archiveArr[$id] = array( 'id' => $id, 'pv' => 0, 'login_pv' => 0, 'ip' => 0 ) ;
									foreach ($archiveContentArr[0] as $key => $archive) {
										$archiveArr[$id]['pv'] += $archiveContentArr[1][$key];
										$archiveArr[$id]['login_pv'] += $archiveContentArr[2][$key];
										$archiveArr[$id]['ip'] += $archiveContentArr[3][$key];
									}
								}
							}
						}
					}
				}
			}
		}
		return $archiveArr;
	}
	
	/**
	 * 获取档期信息
	 * 
	 * @param array $list
	 * @param unknown_type $type
	 */
	public function getArchivesForCatInfo(Array &$list,$catEnName = 'common'){
		if($list){
			$uids = array_keys($list);
			$archivesSer = new ArchivesService();
			$catInfo = $archivesSer->getAllArchiveCatByEnName($catEnName);
			if ($catInfo && $uids){
				$cat_id = $catInfo['cat_id'];
				$condition = array();
				$condiiton['uids'] = $uids;
				$condiiton['cat_id'] = $cat_id;
				$result = $archivesSer->getArchivesBycondition($condiiton,'uid');
				
				if ($result){
					foreach($result as $uid => $v){
						$list[$uid]['archivesInfo'] = $v; 
					}
				}
			}
		}
	}
	
	/**
	 * 获取消费信息
	 * 
	 * @param array $list
	 */
	public function getConsumeInfo(Array &$list){
		if($list){
			$uids = array_keys($list);
			$consumeSer = new ConsumeService();
			$doteyRanks = $consumeSer->getDoteyRankFromRedis();
			$userRanks = $consumeSer->getUserRankFromRedis();
			$info = $consumeSer->getConsumesByUids($uids);
			if($info){
				foreach ($info as $k=>&$v){
					if(is_numeric($v['dotey_rank'])){
						$info[$k]['dotey_rank'] = isset($doteyRanks[$v['dotey_rank']])?$doteyRanks[$v['dotey_rank']]['name']:'';
					}
					if(is_numeric($v['rank'])){
						$info[$k]['rank'] = isset($userRanks[$v['rank']])?$userRanks[$v['rank']]['name']:'';
					}
					$list[$v['uid']]['consumeInfo'] = $v; 
				}
			}
		}
	}
	
	/**
	 * 获取家族信息
	 *
	 * @param array $list
	 */
	public function getFamilyInfo(Array &$list){
		if($list){
			$uids = array_keys($list);
			$famService = new FamilyService();
			$infos = $famService->getMembersGroupByUids($uids);
			if($infos){
				$famMembers = array();
				$familyIds = array();
				foreach ($infos as $k=>$info){
					$familyIds[$info['family_id']] =  $info['family_id'];
					$famMembers[$info['uid']][$info['family_id']] = $info['family_id'];
				}
				if ($familyIds){
					$famInfos = $famService->getFamilyIds($familyIds);
					if ($familyIds){
						foreach ($famMembers as $uid=>$v){
							foreach ($v as $_familyId){
								$list[$uid]['family'][$_familyId]['family_id'] = $_familyId;
								$list[$uid]['family'][$_familyId]['family_name'] = isset($famInfos[$_familyId])?$famInfos[$_familyId]['name']:'';
							}
						}
					}
				}
			}
		}
	}
	
	/**
	 * @param array $list
	 */
	public function getLiveRecordsInfo(Array &$list,$catEnName = 'common'){
		if ($list){
			$uids = array_keys($list);
			$archivesSer = new ArchivesService();
			$catInfo = $archivesSer->getAllArchiveCatByEnName($catEnName);
			if($catInfo && $uids){
				$cat_id = $catInfo['cat_id'];
				$liveStatitics = $archivesSer->sataticsLiveRecords($uids,$cat_id);
				if($liveStatitics){
					foreach($liveStatitics as $statitic){
						$list[$statitic['uid']]['liveStatitics'] = $statitic;
					}
				}
			}
		}
	}
	
	/**
	 * 获取档期隐藏状态
	 * @return Ambigous <multitype:string, multitype:string >
	 */
	public function getArchivesIsHide(){
		$archivesSer = new ArchivesService();
		return $archivesSer->getArchivesIsHide();
	}
	
	/**
	 * 获取直播视频服务器列表
	 * 
	 * @param int $type
	 * @return Ambigous <multitype:, multitype:unknown Ambigous <multitype:unknown , unknown> >|multitype:unknown |multitype:
	 */
	public function getLiveServices($type = 1){
		$archivesSer = new ArchivesService();
		$list = $archivesSer->getLiveServer();
		if ($list){
			if ($type == 1){
				return $list;
			}elseif ($type == 2){
				$arr = array();
				foreach ($list as $v){
					$arr[$v['server_id']] = $v['import_host'];
				}
				return $arr;
			}
		}
		return array();
	}
	
	/**
	 * 新增平台奖励动作
	 */
	public function addAwardDo(){
		$form = Yii::app()->request->getParam('form');
		if (!$form || !isset($form['uid']) || empty($form['uid']) || empty($form['quantity']) || empty($form['reason']) || $form['type'] < 0){
			return array('info'=>array('提交的数据不完整，请核对'));
		}
		$consumeSer = new ConsumeService();
		
		$type = $form['type'];
		if($type == AWARD_TYPE_CHARM){
			foreach ($form['uid'] as $uid){
				$consumeAttibute = array();
				$consumeAttibute['uid'] = $uid;
				$consumeAttibute['charm'] = abs(intval($form['quantity']));
				if($consumeSer->saveUserConsumeAttribute($consumeAttibute)){
					$addRecords = array();
					$addRecords['uid'] = $uid;
					$addRecords['charm'] =  abs(intval($form['quantity']));
					$addRecords['sender_uid'] =  Yii::app()->user->getId();
					$addRecords['num'] = 1;
					$addRecords['source'] = SOURCE_SENDS;
					$addRecords['sub_source'] = SUBSOURCE_SENDS_ADMIN;
					$addRecords['client'] = CLIENT_ADMIN;
					$addRecords['info'] = $form['reason'];
					$consumeSer->saveDoteyCharmRecords($addRecords);
				}
			}
			$op_desc = '新增 平台奖励[魅力值:'.abs(intval($form['quantity'])).']到UIDS['.implode(',', $form['uid']).']';
			$this->doteySer->saveAdminOpLog($op_desc);
			$this->redirect($this->createUrl('dotey/award',array('type'=>AWARD_TYPE_CHARM)));
		}elseif ($type == AWARD_TYPE_CHARMPOINTS){
			//新增魅力点
			foreach ($form['uid'] as $uid){
				$consumeAttibute = array();
				$consumeAttibute['uid'] = $uid;
				$consumeAttibute['charm_points'] = abs(intval($form['quantity']));
				if($consumeSer->saveUserConsumeAttribute($consumeAttibute)){
					$addRecords = array();
					$addRecords['uid'] = $uid;
					$addRecords['charm_points'] =  abs(intval($form['quantity']));
					$addRecords['sender_uid'] =  Yii::app()->user->getId();
					$addRecords['num'] = 1;
					$addRecords['source'] = SOURCE_SENDS;
					$addRecords['sub_source'] = SUBSOURCE_SENDS_ADMIN;
					$addRecords['client'] = CLIENT_ADMIN;
					$addRecords['info'] = $form['reason'];
					$consumeSer->saveDoteyCharmPointsRecords($addRecords);
				}
			}
			$op_desc = '新增 平台奖励[魅力点:'.abs(intval($form['quantity'])).']到UIDS['.implode(',', $form['uid']).']';
			$this->doteySer->saveAdminOpLog($op_desc);
			$this->redirect($this->createUrl('dotey/award',array('type'=>AWARD_TYPE_CHARMPOINTS)));
		}elseif ($type == AWARD_TYPE_CASH){
			//新增现金
			foreach ($form['uid'] as $uid){
				$addRecords = array();
				$addRecords['uid'] = $uid;
				$addRecords['quantity'] = $form['quantity'];
				$addRecords['reason'] = $form['reason'];
				$consumeSer->saveCashAwardRecords($addRecords);
			}
			$op_desc = '新增 平台奖励[现金:'.abs(intval($form['quantity'])).']到UIDS['.implode(',', $form['uid']).']';
			$this->doteySer->saveAdminOpLog($op_desc);
			$this->redirect($this->createUrl('dotey/award',array('type'=>AWARD_TYPE_CASH)));
		}else{
			return array('info'=>array('奖励类型不符合规定，请核对'));
		}
	}
	
	/**
	 * 添加才艺补贴 
	 */
	public function addAllowanceDo(){
		$form = Yii::app()->request->getParam('form');
		if (!$form || !isset($form['uid']) || empty($form['uid']) || empty($form['quantity']) || empty($form['reason'])){
			return array('info'=>array('提交的数据不完整，请核对'));
		}
		$consumeSer = new ConsumeService();
		
		//新增现金
		foreach ($form['uid'] as $uid){
			$addRecords = array();
			$addRecords['uid'] = $uid;
			$addRecords['quantity'] = $form['quantity'];
			$addRecords['reason'] = $form['reason'];
			$consumeSer->saveCashAwardRecords($addRecords,EXCHANGE_ART);
		}
		
		$op_desc = '新增 才艺补贴['.$form['quantity'].'] 到用户UIDS['.implode(',', $form['uid']).']';
		$consumeSer->saveAdminOpLog($op_desc);
		
		$this->redirect($this->createUrl('dotey/allowance',array('uid'=>$uid)));
	}
	
	/**
	 * 导入主播经理人关系
	 */
	public function addManagerDo(){
		if (!$this->isAjax){
			exit('非法请求');
		}
		$user = Yii::app()->request->getParam('user');
		if ($user){
			$addData = array();
			$addData['uid'] = $user['uid'];
			$addData['type'] = DOTEY_MANAGER_TUTOR;
			if($this->doteySer->saveDoteyProxy($addData) > 0){
				exit('1');
			}else{
				print_r($this->doteySer->getNotice());exit;
			}
		}else{
			exit('缺少参数，请求失败');
		}
	}
	
	/**
	 * 执行添加主播代理
	 */
	public function addProxyDo($rediect = null){
		$form = Yii::app()->request->getParam('form');
		if (!$form){
			return array('info'=>array('添加的数据不能为空'));		
		}
		//身份证复印件上传
		$id_card_pic = $this->uploadDoteyProxy('id_card_pic');
		if($id_card_pic){
			$form['id_card_pic'] = $id_card_pic;
		}
		
		//营业执照上传
		$business_license = $this->uploadDoteyProxy('business_license');
		if($id_card_pic){
			$form['business_license'] = $business_license;
		}
		
		$uid = $form['uid'];
		if($uid){
			$form['type'] = isset($form['type'])?$form['type']:DOTEY_MANAGER_PROXY;
			if($this->doteySer->updateProxy($uid,$form)){
				$rediect = $rediect?$rediect:$this->createUrl('dotey/proxy');
				$this->redirect($rediect);
			}else{
				return $this->doteySer->getNotice();
			}
		}else{
			return array('info'=>array('缺少提交的对象'));
		}
	}
	
	/**
	 * 执行添加报酬政策动作
	 */
	public function addRewardPolicyDo(){
		$webConfigSer = new WebConfigService();
		$doteyPayKeys = $webConfigSer->getDoteyPayKey($this->doteySer);
		$doteyTypes = $this->doteySer->getDoteyType();
		$doteyKey = '';
		
		$scale = Yii::app()->request->getParam('scale');//魅力点兑换公式
		$effectDay = Yii::app()->request->getParam('effectday');//有效天
		$reward = Yii::app()->request->getParam('reward');//月度奖金
		
		//报酬公式
		if (is_array($scale)){
			$_scale = array();
			foreach ($scale as $k=>$list){
				if(key_exists($list['dotey_type'], $doteyTypes)){
					$doteyKey = $list['dotey_type'];
					$_scaleKey = $doteyPayKeys[$list['dotey_type']]['scale'];
					unset($list['dotey_type']);
					$_scale['c_key'] = $_scaleKey;
					$_scale['c_type'] = 'array';
					$_scale['c_value'][$k] = array('scale'=>$list['scale']);
				}
			}
			if ($_scale){
				$webConfigSer->saveWebConfig($_scale);
			}
		}
		
		//有效天
		if (is_array($effectDay)){
			$_effectDay = array();
			foreach ($effectDay as $k=>$list){
				if(key_exists($list['dotey_type'], $doteyTypes)){
					$doteyKey = $list['dotey_type'];
					$_effectKey = $doteyPayKeys[$list['dotey_type']]['effectDay'];
					unset($list['dotey_type']);
					$_effectDay['c_key'] = $_effectKey;
					$_effectDay['c_type'] = 'array';
					$_effectDay['c_value'][$k] = array('day'=>$list['day']);
				}
			}
			
			if($_effectDay){
				$webConfigSer->saveWebConfig($_effectDay);
			}
		}
		
		//月度奖金
		if($reward){
			$consumeSer = new ConsumeService();
			$_formatReward = array();
			foreach($reward as $uid=>$list){
				foreach($list as $c=>$vs){
					foreach ($vs as $k2=>$v){
						$_formatReward[$uid][$k2][$c]=$v;
					}
					
				}
			}
			if ($_formatReward){
				foreach ($_formatReward as $uid=>$v){
					foreach($v as $list){
						$consumeSer->saveDoteyPayConfig($list);
					}
				}
			}
		}
		$this->redirect($this->createUrl('dotey/rewardpolicy',array('dotey_type'=>$doteyKey)));
	}
		
	/**
	 * 撤消平台奖励动作
	 */
	public function unAwardDo(){
		if (!$this->isAjax){
			exit('不合法请求');
		}
		$type = Yii::app()->request->getParam('type');
		$recordId = Yii::app()->request->getParam('recordId');
		if ($type < 0 || (empty($type) && $type != 0) || empty($recordId)){
			return array('info'=>array('提交的数据不完整，请核对'));
		}
		$consumeSer = new ConsumeService();
	
		if($type == AWARD_TYPE_CHARM){
			//撤销平台赠送的魅力值
			$info = $consumeSer->getCharmAwardByCondition(array('record_id'=>$recordId));
			if($info['list']){
				$list = array_shift($info['list']);
				$charm = $list['charm'];
				$uid = $list['uid'];
				if ($uid && $charm){
					if($consumeSer->exchangeCharm($uid,$charm)){
						$consumeAttr = array();
						$consumeAttr['uid'] = $uid;
						$consumeAttr['charm'] = -$charm;
						if($consumeSer->saveUserConsumeAttribute($consumeAttr)){
							//写撤销记录
							unset($list['record_id']);
							$list['target_id'] = $recordId;
							$list['create_time'] = time();
							if($consumeSer->saveDoteyCharmRecords($list,0)){
								exit('1');
							}else{
								exit('撤销成功,撤销记录写入失败');
							}
						}else{
							exit('撤销成功,消息发送失败');
						}
					}else{
						exit('撤销失败');
					}
				}
			}else{
				exit('撤销失败');
			}
		}elseif ($type == AWARD_TYPE_CHARMPOINTS){
			$info = $consumeSer->getCharmPointsAwardByCondition(array('record_id'=>$recordId));
			if($info['list']){
				$list = array_shift($info['list']);
				$charm_points = $list['charm_points'];
				$uid = $list['uid'];
				if ($uid && $charm_points){
					if($consumeSer->exchangeCharmPoint($uid,$charm_points)){
						$consumeAttr = array();
						$consumeAttr['uid'] = $uid;
						$consumeAttr['charm_points'] = -$charm_points;
						if($consumeSer->saveUserConsumeAttribute($consumeAttr)){
							//写撤销记录
							unset($list['record_id']);
							$list['target_id'] = $recordId;
							$list['create_time'] = time();
							if($consumeSer->saveDoteyCharmPointsRecords($list,0)){
								exit('1');
							}else{
								exit('撤销成功,撤销记录写入失败');
							}
						}else{
							exit('撤销成功,消息发送失败');
						}
					}else{
						exit('撤销失败');
					}
				}
			}else{
				exit('撤销失败');
			}
		}elseif ($type == AWARD_TYPE_CASH){
			//撤消平台现金奖励
			if($consumeSer->saveUnCashAwardRecords($recordId)){
				exit('1');
			}else{
				exit('撤销失败');
			}
		}else{
			exit('参数错误，请核查');
		}
	}
	
	/**
	 * 撤销才艺补贴
	 * 
	 * @return multitype:multitype:string  
	 */
	public function unAllowanceDo(){
		if (!$this->isAjax){
			exit('不合法请求');
		}
		$recordId = Yii::app()->request->getParam('recordId');
		if (empty($recordId)){
			return array('info'=>array('提交的数据不完整，请核对'));
		}
		$consumeSer = new ConsumeService();
		
		//撤消平台现金奖励
		if($consumeSer->saveUnCashAwardRecords($recordId,4)){
			exit('1');
		}else{
			exit('撤销失败');
		}
	}
	
	/**
	 * 编辑主播资料信息
	 */
	public function editDoteyBase(){
		$uid = Yii::app()->request->getParam('uid');
		$user = Yii::app()->request->getParam('user');
		$userextend = Yii::app()->request->getParam('userextend');
		$dotey = Yii::app()->request->getParam('dotey');
		$archives = Yii::app()->request->getParam('archives');
		if(!$uid || !$dotey || !$user || !$userextend || !$archives){
			exit('提交数据为空无法提交 ');
		}

		$notices = $this->updateUserInfo($uid, $user);
		if(is_array($notices)){
			return $notices;
		}
		
		//修改扩展信息
		$userextend['uid'] = $uid;
		if (!empty($userextend['birthday'])){
			$userextend['birthday'] = strtotime($userextend['birthday']);
		}
		$this->userSer->saveUserExtend($userextend);
		//主播明星图上传
		$bigDisplayFile = $this->doteySer->getDoteySaveFile($uid,'big','display');
		if($this->doteySer->uploadDoteyImages('dotey_display_big','dotey',$bigDisplayFile)){
			$dotey['update_desc']['display_big'] = time();
			$this->doteySer->saveAdminOpLog('更新 主播明星图片('.$uid.')',$uid);
		}
		//主播封面上传
		$smallDisplayFile = $this->doteySer->getDoteySaveFile($uid,'small','display');
		if($this->doteySer->uploadDoteyImages('dotey_display_small','dotey',$smallDisplayFile)){
			$dotey['update_desc']['display_small'] = time();
			$this->doteySer->saveAdminOpLog('更新 主播封面图片('.$uid.')',$uid);
		}

		//修改主播信息
		$dotey['uid'] = $uid;
		if (isset($dotey['sign_type'])){
			$signTypes = array();
			foreach($dotey['sign_type'] as $signType){
				$signTypes[] = intval($signType);
			}
			$dotey['sign_type'] = $this->doteySer->grantMoreBit(0,$signTypes);
		}
		if (isset($dotey['tutor_uid'])){
			$tutorStr = explode('#XX#', $dotey['tutor_uid']);
			$dotey['tutor_uid'] = intval($tutorStr[1]);
		}
		if (isset($dotey['proxy_uid'])){
			$proxyStr = explode('#XX#', $dotey['proxy_uid']);
			$dotey['proxy_uid'] = intval($proxyStr[1]);
		}
		
		if($this->doteySer->saveUserDoteyBase($dotey)){
			$this->redirect($this->createUrl('dotey/doteylist',array('uid'=>$uid)));
		}else{
			return $this->doteySer->getNotice();
		}
	}
	
	/**
	 * 编辑申请信息
	 * @return Ambigous <boolean, multitype:multitype:string  , 获取用户界面友好提提示, 用户界面友好提提示>|Ambigous <获取用户界面友好提提示, 用户界面友好提提示>
	 */
	public function editApplyInfo(){
		$uid = Yii::app()->request->getParam('uid');
		$user = Yii::app()->request->getParam('user');
		$userextend = Yii::app()->request->getParam('ext');
		if(!$uid || !$user || !$userextend){
			exit('提交数据为空无法提交 ');
		}
		
		$user['uid'] = $uid;
		
		if(!$this->userSer->saveUserBasic($user)){
			return $this->userSer->getNotice();
		}
		
		//修改扩展信息
		$userextend['uid'] = $uid;
		$this->userSer->saveUserExtend($userextend);
		
		$this->redirect($this->createUrl('dotey/doteyApply',array('uid'=>$uid)));
	}
	
	/**
	 * 修改主播经理人资料
	 */
	public function editManagerDo(){
		if (!$this->isAjax){
			exit('非法请求');
		}
		
		$user = Yii::app()->request->getParam('user');
		if ($user){
			$uid =  isset($user['uid'])?$user['uid']:exit('参数有误');
			$nickname = isset($user['nickname'])?$user['nickname']:exit('昵称不能为空');
			$realname = isset($user['realname'])?$user['realname']:exit('姓名不能为空');
			$qq = isset($user['qq'])?$user['qq']:exit('QQ不能为空');
			$mobile = isset($user['mobile'])?$user['mobile']:exit('手机号码不能为空');
			$is_display = (isset($user['is_display']) && $user['is_display']>=0)?$user['is_display']:exit('前台显示状态不能为空');
			$data = array();
			$data['nickname'] = $nickname;
			$data['realname'] = $realname;
			$data['qq'] = $qq;
			$data['mobile'] = $mobile;
			$data['is_display'] = $is_display;
			$data['type'] = DOTEY_TYPE_DIRECT;
			$flag = $this->doteySer->updateProxy($uid, $data);
			if(!is_array($flag)){
				exit('1');
			}else{
				$info = array_shift($flag);
				exit($info[0]);
			}
		}else{
			exit('提交信息有误');
		}
		exit('请求失败');
	}
	
	/**
	 * 编辑直播记录
	 * 
	 * @param ArchivesService $archivesSer
	 */
	public function editLiveRecords(ArchivesService $archivesSer){
		$condition = Yii::app()->request->getParam('condition');
		//是否是有修改数据
		$data = Yii::app()->request->getParam('live');
		if($data){
			if (isset($data['record_id'])){
				if(isset($data['start_time'])){
					$data['start_time'] = strtotime($data['start_time']);
				}
				if($archivesSer->saveArchivesLiveRecords($data)){
					$this->redirect($this->createUrl('dotey/onlive'));
				}
			}
			exit('修改失败');
		}
		
		if (!$this->isAjax){
			exit('不合法请求');
		}
		
		$record_id = Yii::app()->request->getParam('record_id');
		if(!$record_id){
			exit('缺少参数');
		}
		$recordInfo = $archivesSer->getLiveRecordByRecordIds(array($record_id));
		if(!$recordInfo){
			exit('获取信息失败');
		}
		$recordInfo = $recordInfo[$record_id];
		
		exit($this->renderPartial('dotey_edit_onlive_record',array('uinfo'=>$recordInfo,'condition'=>$condition)));
	}
	
	/**
	 * 编辑直播间信息操作
	 */
	public function editArchivesDo(){
		$archives = Yii::app()->request->getParam('archives');
		$server = Yii::app()->request->getParam('server');
		if (!$archives || !$server){
			return array('info'=>array('没有提交任何数据'));
		}
		
		if(!isset($archives['archives_id']) || !isset($server['archives_id']) || !isset($server['server_id'])){
			return array('info' => array('提交的参数有误'));
		}
		$archivesSer = new ArchivesService();
		$archivesSer->saveArchivesLiveServer($server);
		if($archivesSer->saveArchives($archives)){
			$this->redirect($this->createUrl('dotey/archiveslist',array('archives_id'=>$archives['archives_id'])));
		}else{
			return array('info' => array('修改失败！'));
		}
	}
	
	/**
	 * 改变正在直播中的状态
	 * 
	 * @param ArchivesService $archivesSer
	 */
	public function changeLiveStatus(ArchivesService $archivesSer){
		if (!$this->isAjax){
			exit('不合法请求');
		}
		
		$record_id = Yii::app()->request->getParam('record_id');
		$type = Yii::app()->request->getParam('type');
		if(!$record_id || !$type || !in_array($type, array('on','off'))){
			exit('缺少参数');
		}
		
		$recordInfo = $archivesSer->getLiveRecordByRecordIds(array($record_id));
		if(!$recordInfo){
			exit('获取信息失败');
		}
		
		$archivesInfo = $archivesSer->getArchivesByArchivesId($recordInfo[$record_id]['archives_id']);
		if(!$archivesInfo){
			exit('获取信息失败');
		}
		
		//更改状态并发ZMQ事件包
		if ($type == 'on'){
			$method = 'startArchivesLive';
		}else{
			$method = 'stopArchivesLive';
		}
		if($archivesSer->$method ($archivesInfo['uid'], $archivesInfo['archives_id']) ){
			exit('1');
		}else{
			exit('操作失败');
		}
	}
	
	/**
	 * 下载收礼记录明细表
	 */
	public function dlInGiftRecordsExcel($uid,$condition){
		header("content-Type: text/html; charset=UTF8");
		$giftSer = new GiftService();
		$inGift = $giftSer->getUserGiftReceiveRecordsByUid($uid,$this->offset,$this->pageSize,$condition,false);
		if($inGift){
			$fileName = "主播收礼明细记录(".$uid.")_".date('Ymd',time()).'.csv';
			$this->doteySer->saveAdminOpLog('下载报表(file='.$fileName.')');
			
			$output = fopen('php://output','w') or die("Can't open php://output");
			header("Content-Type: application/force-download");
			header('Content-Disposition: attachment;filename="'.$fileName.'"');
			header('Cache-Control: max-age=0');
			header("Content-Transfer-Encoding: binary");
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Pragma: no-cache");
				
			$temp_arr = array('发送者(UID)','接收者(UID)','礼物名称','礼物数量','消费皮蛋','送礼时间','魅力点','魅力值');
			foreach($temp_arr as $k=>$v){
				$temp_arr[$k] = mb_convert_encoding($v, "GBK", "UTF-8");
			}
			fputcsv($output,$temp_arr);
				
			$list = $inGift['list'];
			foreach ($list as $v) {
				$info = unserialize($v['info']);
				$sendTime = date('Y-m-d',$v['create_time']);
				$sender = isset($info['sender'])?$info['sender']:'';
				$receiver = isset($info['receiver'])?$info['receiver']:'';
				$giftName = isset($info['gift_zh_name'])?$info['gift_zh_name']:'';
				$num = isset($v['num'])?$v['num']:'';
				$pipiegg = isset($v['pipiegg'])?$v['pipiegg']:'';
				$charm = isset($v['charm'])?$v['charm']:'';
				$charm_points = isset($v['charm_points'])?$v['charm_points']:'';
	
				$temp_arr = array($sender."({$v['uid']})",$receiver."({$v['to_uid']})",$giftName,$num,$pipiegg,$sendTime,$charm_points,$charm);
				foreach($temp_arr as $k=>$v){
					$temp_arr[$k] = mb_convert_encoding($v, "GBK", "UTF-8");
				}
				fputcsv($output,$temp_arr);
			}
			fclose($output) or die("Can't close php://output");
		}
		exit;
	}
	
	/**
	 * 下载主播列表
	 * 
	 * @param array $condition
	 */
	public function dlDoteyListExcel(Array $list){
		if($list){
			header("content-Type: text/html; charset=UTF8");
			
			$doteyStatus = $this->doteySer->getDoteyBaseStatus();
			$doteyRegSource = $this->userSer->getUserRegSource();
			$doteyTypes = $this->getProxyAndTutorListOption();
			$doteySource = $this->doteySer->getDoteyType();
			$isHide = $this->getArchivesIsHide();
			$userStatus = $this->userSer->getUserStatus();
			$userStatus[USER_STATUS_OFF] = '停播';
			$userStatus[USER_STATUS_ON] = '开播';
			
			$fileName = "主播列表_".date('Ymd',time()).'.csv';
			$this->doteySer->saveAdminOpLog('下载报表(file='.$fileName.')');
			
			$output = fopen('php://output','w') or die("Can't open php://output");
			header("Content-Type: application/force-download");
			header('Content-Disposition: attachment;filename="'.$fileName.'"');
			header('Cache-Control: max-age=0');
			header("Content-Transfer-Encoding: binary");
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Pragma: no-cache");
			
			$temp_arr = array('用户名','昵称','UID','注册时间','签约状态','签约类型','用户类型','来源','推广来源','节目名','显示','来源类型','魅力值','魅力点','主播等级','开播状态','开播总时长','开播总数','初次开播','最近开播');
			foreach($temp_arr as $k=>$v){
				$temp_arr[$k] = mb_convert_encoding($v, "GBK", "UTF-8");
			}
			fputcsv($output,$temp_arr);
			
			foreach ($list as $uinfo) {
				$c1 = $uinfo['realname'];
				$c2 = $uinfo['nickname'];
				$c3 = $uinfo['uid'];
				$c4 = date('Y-m-d H:i:s',$uinfo['create_time']);
				$c5 = $doteyStatus[$uinfo['status']];
				$c6 = implode(',', $this->doteySer->checkSignType($uinfo['sign_type'],true)) ;
				$c7 = implode(',', $this->userSer->checkUserType($uinfo['user_type'],true));
				$c8 = '';
				$c9 = $doteyRegSource[$uinfo['reg_source']];
				if($uinfo['proxy_uid']){
					$_dk = DOTEY_MANAGER_PROXY.'#XX#'.$uinfo['proxy_uid'];
					$c8 .= isset($doteyTypes[$_dk])?$doteyTypes[$_dk]:'';
				}
				if($uinfo['proxy_uid'] && $uinfo['tutor_uid']){
					$c8 .= ",";
				}
				if ($uinfo['tutor_uid']){
					$_dk = DOTEY_MANAGER_TUTOR.'#XX#'.$uinfo['tutor_uid'];
					$c8 .= isset($doteyTypes[$_dk])?$doteyTypes[$_dk]:'';
				}
				$c10 = '';
				if(isset($uinfo['archivesInfo'])){
					$c10 = $uinfo['archivesInfo']['title'];
					$c11 = $isHide[$uinfo['archivesInfo']['is_hide']];
				}
				$c12 = $doteySource[$uinfo['status']];
				if(isset($uinfo['consumeInfo'])){
  					$c13 = $uinfo['consumeInfo']['charm']?$uinfo['consumeInfo']['charm']:'null';
  					$c14 = $uinfo['consumeInfo']['charm_points']?$uinfo['consumeInfo']['charm_points']:'null';
  					$c15 = $uinfo['consumeInfo']['dotey_rank'];
  				}
  				$c16 = $userStatus[$uinfo['user_status']];
  				
  				if(isset($uinfo['liveStatitics'])){
  					$c17 =  $uinfo['liveStatitics']['sum_duration'];
  					$c18 = $uinfo['liveStatitics']['count_lives'];
  					$c19 = date('Y-m-d',$uinfo['liveStatitics']['first_live_time']);
  					$c20 = date('Y-m-d',$uinfo['liveStatitics']['last_live_time']);
  				}
  				
				$temp_arr = array($c1,$c2,$c3,$c4,$c5,$c6,$c7,$c8,$c9,$c10,$c11,$c12,$c13,$c14,$c15,$c16,$c17,$c18,$c19,$c20);
				foreach($temp_arr as $k=>$v){
					$temp_arr[$k] = mb_convert_encoding($v, "GBK", "UTF-8");
				}
				fputcsv($output,$temp_arr);
			}
			fclose($output) or die("Can't close php://output");
		}
		exit;
	}

	/**
	 * 下载直播查询列表
	 * @param array $list
	 */
	public function dlOnliveSearchExcel(Array $list,$live_time){
		if ($live_time){
			$days = date('t',strtotime($live_time));
			
			if($list){
				$fileName = "开播查询_".$live_time.'.csv';
				$this->doteySer->saveAdminOpLog('下载报表(file='.$fileName.')');
				$output = fopen('php://output','w') or die("Can't open php://output");
				header("Content-Type: application/force-download");
				header('Content-Disposition: attachment;filename="'.$fileName.'"');
				header('Cache-Control: max-age=0');
				header("Content-Transfer-Encoding: binary");
				header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				header("Pragma: no-cache");
			
				$temp_arr = array('节目名','昵称','有效天数','小时数');
				for ($i=1;$i<=$days;$i++){
					$temp_arr[] = $live_time.'_'.$i; 
				}
				
				foreach($temp_arr as $k=>$v){
					$temp_arr[$k] = mb_convert_encoding($v, "GBK", "UTF-8");
				}
				fputcsv($output,$temp_arr);
			
				foreach ($list as $uinfo) {
					$c1 = $uinfo['title'];
					$c2 = $uinfo['nickname'];
					$c3 = count($uinfo['has_days']);
					$c4 = number_format($uinfo['has_hours']/3600,2);
			
					$temp_arr = array($c1,$c2,$c3,$c4);
					for ($i=1;$i<=$days;$i++){
						$temp_arr[] = isset($uinfo['detail'][$i])?number_format($uinfo['detail'][$i]/3600,2):0;
					}
					
					foreach($temp_arr as $k=>$v){
						$temp_arr[$k] = mb_convert_encoding($v, "GBK", "UTF-8");
					}
					fputcsv($output,$temp_arr);
				}
				fclose($output) or die("Can't close php://output");
			}
			exit;
		}
		
	}
	
	/**
	 * 下载主播收入查询
	 * 
	 * @param array $list
	 * @param int $live_time
	 */
	public function dlIncomeSearchExcel(Array $list,$live_time){
		if ($live_time){
			$days = date('t',strtotime($live_time));
				
			if($list){
				$fileName = "收入查询_".$live_time.'.csv';
				$this->doteySer->saveAdminOpLog('下载报表(file='.$fileName.')');
				$output = fopen('php://output','w') or die("Can't open php://output");
				header("Content-Type: application/force-download");
				header('Content-Disposition: attachment;filename="'.$fileName.'"');
				header('Cache-Control: max-age=0');
				header("Content-Transfer-Encoding: binary");
				header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				header("Pragma: no-cache");
					
				$temp_arr = array('节目名','昵称','总皮蛋');
				for ($i=1;$i<=$days;$i++){
					$temp_arr[] = $live_time.'_'.$i;
				}
	
				foreach($temp_arr as $k=>$v){
					$temp_arr[$k] = mb_convert_encoding($v, "GBK", "UTF-8");
				}
				fputcsv($output,$temp_arr);
					
				foreach ($list as $uinfo) {
					$c1 = $uinfo['title'];
					$c2 = $uinfo['nickname'];
					$c3 = $uinfo['total_charm_point'];
						
					$temp_arr = array($c1,$c2,$c3);
					for ($i=1;$i<=$days;$i++){
						$temp_arr[] = isset($uinfo['detail'][$i])?$uinfo['detail'][$i]:0;
					}
					
					foreach($temp_arr as $k=>$v){
						$temp_arr[$k] = mb_convert_encoding($v, "GBK", "UTF-8");
					}
					fputcsv($output,$temp_arr);
				}
				fclose($output) or die("Can't close php://output");
			}
			exit;
		}
	}
	
	/**
	 * 下载已经停播的Excel
	 * 
	 * @param array $list
	 */
	public function dlStopLiveExcel(Array $list){
		if($list){
			$fileName = "停播管理_".date('Y-m-d',time()).'.csv';
			$this->doteySer->saveAdminOpLog('下载报表(file='.$fileName.')');
			$output = fopen('php://output','w') or die("Can't open php://output");
			header("Content-Type: application/force-download");
			header('Content-Disposition: attachment;filename="'.$fileName.'"');
			header('Cache-Control: max-age=0');
			header("Content-Transfer-Encoding: binary");
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Pragma: no-cache");
				
			$temp_arr = array('用户名','昵称','真实姓名','注册时间','停播时间','最近一次开播时间','主播等级','富豪等级','总皮蛋','消费皮蛋','上月魅力点','近15天魅力点');
			foreach($temp_arr as $k=>$v){
				$temp_arr[$k] = mb_convert_encoding($v, "GBK", "UTF-8");
			}
			fputcsv($output,$temp_arr);
				
			foreach ($list as $uinfo) {
				$c1 = $uinfo['username'];
				$c2 = $uinfo['nickname'];
				$c3 = $uinfo['realname'];
				$c4 = date('Y-m-d',$uinfo['reg_time']);
				$c5 = date('Y-m-d',$uinfo['stop_time']);
				$c6 = !empty($uinfo['last_live_time'])?date('Y-m-d',$uinfo['last_live_time']):'';
				$c7 = $uinfo['dotey_rank'];
				$c8 = $uinfo['user_rank'];
				$c9 = $uinfo['total_pipieggs'];
				$c10 = $uinfo['consume_pipiegg'];
				$c11 = $uinfo['prev_charm_points'];
				$c12 = $uinfo['15days_charm_points'];
	
				$temp_arr = array($c1,$c2,$c3,$c4,$c5,$c6,$c7,$c8,$c9,$c10,$c11,$c12);
				foreach($temp_arr as $k=>$v){
					$temp_arr[$k] = mb_convert_encoding($v, "GBK", "UTF-8");
				}
				fputcsv($output,$temp_arr);
			}
			fclose($output) or die("Can't close php://output");
		}
		exit;
	}
	
	
	/**
	 * 下载主播报酬结算报表
	 * 
	 * @param array $list
	 */
	public function dlRewardsExcel(Array $list){
		if($list){
			$fileName = "主播报酬_".date('Y-m-d',time()).'.csv';
			$this->doteySer->saveAdminOpLog('下载报表(file='.$fileName.')');
			$output = fopen('php://output','w') or die("Can't open php://output");
			header("Content-Type: application/force-download");
			header('Content-Disposition: attachment;filename="'.urlencode($fileName).'"');
			header('Cache-Control: max-age=0');
			header("Content-Transfer-Encoding: binary");
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Pragma: no-cache");
	
			$userStatus = $this->userSer->getUserStatus();
			$userStatus[USER_STATUS_OFF] = '停播';
			$userStatus[USER_STATUS_ON] = '开播';
			
			$temp_arr = array('ID', '账号', '昵称', '姓名', '开户银行', '卡号', '来源', '签约', '开播状态', '有效天', '小时数', '本月原始魅力点', 
				'本月有效魅力点','本月无效魅力点','本月无效提现', '底薪收入', '奖金', '已兑换金额', '平台奖励', '才艺补贴', '原始合计金额','有效合计金额');
			foreach($temp_arr as $k=>$v){
				$temp_arr[$k] = mb_convert_encoding($v, "GBK", "UTF-8");
			}
			fputcsv($output,$temp_arr);
			$sources = $this->getProxyAndTutorListOption();
			$types = $this->doteySer->getDoteyBaseStatus(true);
			foreach ($list as $uinfo) {
				$c1 = $uinfo['uid'];
				$c2 = $uinfo['username'];
				$c3 = $uinfo['nickname'];
				$c4 = $uinfo['realname'];
				$c5 = $uinfo['bank'];
				$c6 = strval('IC:'.$uinfo['bank_account']);
				$c7 = '';
				if ($uinfo['proxy_uid'] > 0){
					$k = DOTEY_MANAGER_PROXY.'#XX#'.$uinfo['proxy_uid'];
					$c7 .= isset($sources[$k])?$sources[$k]:'无';
					$c7 .= $uinfo['tutor_uid']?' ':'';
				}
				
				if ($uinfo['tutor_uid'] > 0){
					$k = DOTEY_MANAGER_TUTOR.'#XX#'.$uinfo['tutor_uid'];
					$c7 .= isset($sources[$k])?$sources[$k]:'无';
				}
				
				$c8 = $types[$uinfo['status']];
				$c9 = $userStatus[$uinfo['user_status']];
				$c10 = isset($uinfo['archives']['have_days'])?$uinfo['archives']['have_days']:0;
				$c11 = isset($uinfo['archives']['have_hours'])?number_format($uinfo['archives']['have_hours']/3600,2):0;
				$c12 = isset($uinfo['archives']['old_charm_points'])?$uinfo['archives']['old_charm_points']:0;
				$c13 = isset($uinfo['archives']['charm_points'])?$uinfo['archives']['charm_points']:0;
				$c14 = isset($uinfo['archives']['invalid_charm_points'])?$uinfo['archives']['invalid_charm_points']:0;
				$c15 = isset($uinfo['archives']['invalid_money'])?$uinfo['archives']['invalid_money']:0;
				$c16 = isset($uinfo['archives']['basic_salary'])?$uinfo['archives']['basic_salary']:0;
				$c17 = isset($uinfo['archives']['bonus'])?$uinfo['archives']['bonus']:0;
				$c18 = isset($uinfo['transRs'])?$uinfo['transRs']:'0';
				$c19 = isset($uinfo['awardRs'])?$uinfo['awardRs']:'0';
				$c20 = isset($uinfo['artRs'])?$uinfo['artRs']:'0';
				$c21 = $c17+$c16+$c18+$c19+$c20;
				$c22 = $c21-$c15;
				
				$temp_arr = array($c1,$c2,$c3,$c4,$c5,$c6,$c7,$c8,$c9,$c10,$c11,$c12,$c13,$c14,$c15,$c16,$c17,$c18,$c19,$c20,$c21,$c22);
				foreach($temp_arr as $k=>$v){
					$temp_arr[$k] = mb_convert_encoding($v, "GBK", "UTF-8");
				}
				fputcsv($output,$temp_arr);
			}
			fclose($output) or die("Can't close php://output");
		}
		exit;
	}
	
	/**
	 * 下载主播点歌明细记录
	 *
	 * @param int $uid
	 * @param array $condition
	 */
	public function dlVODQueryExcel($songRecords){
		header("content-Type: text/html; charset=UTF8");
		if($songRecords){
			$fileName = "主播点歌明细记录_".date('Ymd',time()).'.csv';
			$this->userSer->saveAdminOpLog('下载报表(file='.$fileName.')');
				
			$output = fopen('php://output','w') or die("Can't open php://output");
			header("Content-Type: application/force-download");
			header('Content-Disposition: attachment;filename="'.$fileName.'"');
			header('Cache-Control: max-age=0');
			header("Content-Transfer-Encoding: binary");
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Pragma: no-cache");
				
			$temp_arr = array('合计：'.$songRecords['count']);
			foreach($temp_arr as $k=>$v){
				$temp_arr[$k] = mb_convert_encoding($v, "GBK", "UTF-8");
			}
			fputcsv($output,$temp_arr);
				
			$temp_arr = array('接收者(UID)','点歌者(UID)','档期ID','歌曲名','歌手','魅力值','魅力点','皮蛋消费','贡献值','皮点','点歌时间','状态');
			foreach($temp_arr as $k=>$v){
				$temp_arr[$k] = mb_convert_encoding($v, "GBK", "UTF-8");
			}
			fputcsv($output,$temp_arr);
				
			$list = $songRecords['list'];
			foreach ($list as $r) {
				$c1 = $songRecords['doteyInfos'][$r['to_uid']]['nickname'].'('.$r['to_uid'].')';
				$c2 = $songRecords['userInfos'][$r['uid']]['nickname'].'('.$r['uid'].')';
				$c3 = $r['target_id'];
				$c4 = $r['name'];
				$c5 = $r['singer'];
				$c6 = $r['charm'];
				$c7 = $r['charm_points'];
				$c8 = $r['pipiegg'];
				$c9 = $r['dedication'];
				$c10 = $r['egg_points'];
				$c11 = date('Y-m-d H:i:s',$r['create_time']);
				$c12 = $songRecords['handlers'][$r['is_handle']];
	
				$temp_arr = array($c1,$c2,$c3,$c4,$c5,$c6,$c7,$c8,$c9,$c10,$c11,$c12);
				foreach($temp_arr as $k=>$v){
					$temp_arr[$k] = mb_convert_encoding($v, "GBK", "UTF-8");
				}
				fputcsv($output,$temp_arr);
			}
			fclose($output) or die("Can't close php://output");
		}
		exit;
	}
	
	/**
	 * 下载主播点歌统计
	 *
	 * @param int $uid
	 * @param array $condition
	 */
	public function dlVODStatExcel($songRecords){
		header("content-Type: text/html; charset=UTF8");
		if($songRecords){
			$fileName = "主播点歌统计记录_".date('Ymd',time()).'.csv';
			$this->userSer->saveAdminOpLog('下载报表(file='.$fileName.')');
	
			$output = fopen('php://output','w') or die("Can't open php://output");
			header("Content-Type: application/force-download");
			header('Content-Disposition: attachment;filename="'.$fileName.'"');
			header('Cache-Control: max-age=0');
			header("Content-Transfer-Encoding: binary");
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Pragma: no-cache");
	
			$temp_arr = array('合计：'.$songRecords['count']);
			foreach($temp_arr as $k=>$v){
				$temp_arr[$k] = mb_convert_encoding($v, "GBK", "UTF-8");
			}
			fputcsv($output,$temp_arr);
	
			$temp_arr = array('接收者(UID)','魅力值','魅力点','皮蛋消费','贡献值','皮点','总点歌数');
			foreach($temp_arr as $k=>$v){
				$temp_arr[$k] = mb_convert_encoding($v, "GBK", "UTF-8");
			}
			fputcsv($output,$temp_arr);
	
			$list = $songRecords['list'];
			foreach ($list as $r) {
				$c1 = $songRecords['doteyInfos'][$r['to_uid']]['nickname'].'('.$r['to_uid'].')';
				$c2 = $r['sum_charm'];
				$c3 = $r['sum_charm_points'];
				$c4 = $r['sum_pipiegg'];
				$c5 = $r['sum_dedication'];
				$c6 = $r['sum_egg_points'];
				$c7 = $r['count'];
	
				$temp_arr = array($c1,$c2,$c3,$c4,$c5,$c6,$c7);
				foreach($temp_arr as $k=>$v){
					$temp_arr[$k] = mb_convert_encoding($v, "GBK", "UTF-8");
				}
				fputcsv($output,$temp_arr);
			}
			fclose($output) or die("Can't close php://output");
		}
		exit;
	}
	
	/**
	 * 格式化档期分类
	 * 
	 * @param array $cats
	 * @return multitype:unknown 
	 */
	public function formatArchivesCat(Array $cats){
		$newCats = array();
		if ($cats){
			foreach ($cats as $cat){
				$newCats[$cat['cat_id']] =$cat['name'];
			}
		}
		return $newCats;
	}
	
	/**
	 * 统一格式化平台奖励数据
	 * 
	 * @param array $list
	 * @param unknown_type $type
	 * @return Ambigous <multitype:, string>
	 */
	public function formatAwardList(Array $list,$type,ConsumeService $consumeSer){
		$result = array();
		if ($type == AWARD_TYPE_CASH){
			foreach ($list as $v){
				$result[$v['record_id']]['record_id'] = $v['record_id'];
				$result[$v['record_id']]['uid'] = $v['uid'];
				$result[$v['record_id']]['reason'] = $v['info'];
				$result[$v['record_id']]['type'] = AWARD_TYPE_CASH;
				$result[$v['record_id']]['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
				$result[$v['record_id']]['quantity'] = $v['dst_amount'].'(现金)';
				$result[$v['record_id']]['status'] = ( $v['handle_type'] == 1)?'已处理':'已撤销';
				$result[$v['record_id']]['isclick'] = ( $v['handle_type'] == 1)?true:false;
			}
		}
		
		if ($type == AWARD_TYPE_CHARMPOINTS){
			$recordIds = array();
			foreach ($list as $v){
				$recordIds[] = $v['record_id'];
				$result[$v['record_id']]['record_id'] = $v['record_id'];
				$result[$v['record_id']]['uid'] = $v['uid'];
				$result[$v['record_id']]['reason'] = $v['info'];
				$result[$v['record_id']]['type'] = AWARD_TYPE_CHARMPOINTS;
				$result[$v['record_id']]['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
				$result[$v['record_id']]['quantity'] = $v['charm_points'].'(魅力点)';
				$result[$v['record_id']]['status'] = '已处理';
				$result[$v['record_id']]['isclick'] = true;
			}
			$_list = $consumeSer->getCharmPointsAwardByCondition(array('target_id'=>$recordIds));
			if ($_list['list']){
				foreach ($_list['list'] as $v){
					$result[$v['target_id']]['status'] = '已撤销';
					$result[$v['target_id']]['isclick'] = false;
				}
			}
		}
		
		if ($type == AWARD_TYPE_CHARM){
			$recordIds = array();
			foreach ($list as $v){
				$recordIds[] = $v['record_id'];
				$result[$v['record_id']]['record_id'] = $v['record_id'];
				$result[$v['record_id']]['uid'] = $v['uid'];
				$result[$v['record_id']]['reason'] = $v['info'];
				$result[$v['record_id']]['type'] = AWARD_TYPE_CHARM;
				$result[$v['record_id']]['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
				$result[$v['record_id']]['quantity'] = $v['charm'].'(魅力值)';
				$result[$v['record_id']]['status'] = '已处理';
				$result[$v['record_id']]['isclick'] = true;
			}
			$_list = $consumeSer->getCharmAwardByCondition(array('target_id'=>$recordIds));
			if ($_list['list']){
				foreach ($_list['list'] as $v){
					$result[$v['target_id']]['status'] = '已撤销';
					$result[$v['target_id']]['isclick'] = false;
				}
			}
		}
		
		return $result;
	}
	
	/**
	 * AJAX 检验主播信息的合法性
	 */
	public function checkDoteyInfo($isDotey = true){
		if (!$this->isAjax){
			exit('不合法请求');
		}
	
		$doteyName = Yii::app()->request->getParam('doteyName');
		if(empty($doteyName)){
			exit('请输入主播信息后进行校验 ');
		}
	
		if(!is_numeric($doteyName)){
			if(!($userInfo = $this->userSer->getVadidatorUser($doteyName,0))){
				exit('不合法用户，请重新输入');
			}
			$uid = $userInfo['uid'];
		}else{
			$uid = (int)$doteyName;
		}
	
		if ($uid){
			if($isDotey){
				if(!($doteyInfo = $this->doteySer->getDoteyInfoByUid($uid))){
					exit('该用户不是主播，请确认');
				}
			}
			
			if(!isset($userInfo)){
				if(!($userInfo = $this->userSer->getUserBasicByUids(array($uid)))){
					exit('不合法用户，请重新输入');
				}else{
					$userInfo = $userInfo[$uid];
				}
			}
			exit('1'.'#xx#'.$userInfo['uid'].'#xx#'.$userInfo['username'].'#xx#'.$userInfo['nickname'].'#xx#'.$userInfo['realname']);
		}else{
			exit('不合法用户，请重新输入');
		}
	}
	
	
	/**
	 * 上传主播代理图片
	 * 
	 * @param unknown_type $formName
	 * @return string|boolean
	 */
	public function uploadDoteyProxy($formName){
		$imgFiles = CUploadedFile::getInstancesByName($formName);
		if($imgFiles){
			foreach ($imgFiles as $imgFile){
				$filename = $imgFile->getName();
				if($filename){
					$extName = $imgFile->getExtensionName();
					$newName = uniqid().'.'.$extName;
					$uploadDir = ROOT_PATH."images".DIR_SEP.'doteyproxy'.DIR_SEP;
					if (!file_exists($uploadDir)){
						mkdir($uploadDir,0777,true);
					}
					$uploadfile = $uploadDir.$newName;
					if($imgFile->saveAs($uploadfile,true)){
						return $newName;
					}
				}
			}
		}
		return false;
	}
	
	/**
	 * 变更申请状态
	 * @param unknown_type $status
	 */
	public function editDoteyApplyStatus($status = APPLY_STATUS_FACE){
		$uid = Yii::app()->request->getParam('uid');
		if (intval($uid)) {
			$doteyInfo = $this->doteySer->getDoteyInfoByUid($uid);
			$userInfo = $this->userSer->getUserBasicByUids(array($uid));
			if($doteyInfo && $userInfo){
				$userInfo = $userInfo[$uid];
				$oldUserType = $userInfo['user_type'];
				$oldProxyUid = $doteyInfo['proxy_uid'];
				
				if($status == APPLY_STATUS_FACE){
					//授权
					if($doteyInfo['status'] == APPLY_STATUS_WAITING){
						$doteyBase = array();
						$doteyBase['uid'] = $uid;
						$doteyBase['status'] = $status;
						$doteyBase['update_time'] = time();
						if($oldProxyUid > 0){
							$doteyBase['tutor_uid'] = 0;#解除导师关系
						}
						//用户身份叠加
						if(!$this->userSer->hasBit(intval($oldUserType), USER_TYPE_DOTEY)){
							$newUserType = $this->userSer->grantBit(intval($oldUserType), USER_TYPE_DOTEY);
							if(!$this->userSer->saveUserJson($uid, array('user_type'=>$newUserType))){
								exit('授权成为主播用户失败');
							}
						}
						//保存主播信息
						if($this->doteySer->saveUserDoteyBase($doteyBase)){
							//创建授权直播间
							$this->createArchives($uid,true);
							exit('1');
						}else{
							exit('授权失败');
						}
					}else{
						exit('授权失败，请求不合法');
					}
				}elseif($status == APPLY_STATUS_SUCCESS){
					//签约
					if($doteyInfo['status'] == APPLY_STATUS_FACE){
						$doteyBase = array();
						$doteyBase['uid'] = $uid;
						$doteyBase['status'] = $status;
						if($oldProxyUid > 0){
							$doteyBase['tutor_uid'] = 0;#解除导师关系
						}
						//用户身份叠加
						if(!$this->userSer->hasBit(intval($oldUserType), USER_TYPE_DOTEY)){
							$newUserType = $this->userSer->grantBit(intval($oldUserType), USER_TYPE_DOTEY);
							if(!$this->userSer->saveUserJson($uid, array('user_type'=>$newUserType))){
								exit('授权成为主播用户失败');
							}
						}
						
						if($this->doteySer->saveUserDoteyBase($doteyBase)){
							//修改档期信息
							$this->createArchives($uid,false);
							$op_desc = '主播申请签约(UID='.$uid.')';
							$this->doteySer->saveAdminOpLog($op_desc,$uid);
							exit('1');
						}else{
							exit('签约失败');
						}
					}else{
						exit('签约失败，请求不合法');
					}
				}elseif($status == APPLY_STATUS_REFUES){
					//拒绝
					if($doteyInfo['status'] == APPLY_STATUS_WAITING){
						$reason = Yii::app()->request->getParam('reason');
						if (empty($reason)){
							exit('撤消理由不能为空');
						}
						$doteyBase = array();
						$doteyBase['uid'] = $uid;
						$doteyBase['status'] = $status;
						if($this->doteySer->saveUserDoteyBase($doteyBase)){
							$this->doteySer->saveDoteyApply(array('uid'=>$uid,'reason'=>$reason));
							//用户身份叠加
							if($this->userSer->hasBit(intval($oldUserType), USER_TYPE_DOTEY)){
								$newUserType = $this->userSer->revokeBit(intval($oldUserType), USER_TYPE_DOTEY);
								$this->userSer->saveUserJson($uid, array('user_type'=>$newUserType));
							}
							$op_desc = '拒绝主播申请(UID='.$uid.')';
							$this->doteySer->saveAdminOpLog($op_desc,$uid);
							exit('1');
						}else{
							exit('拒绝失败');
						}
					}else{
						exit('拒绝失败，请求不合法');
					}
				}elseif($status === APPLY_STATUS_WAITING){
					//撤销拒绝
					exit('暂功能关闭，无法进行反撤消操作');
					if($doteyInfo['status'] === APPLY_STATUS_REFUES){
						$doteyBase = array();
						$doteyBase['uid'] = $uid;
						$doteyBase['status'] = $status;
						if($this->doteySer->saveUserDoteyBase($doteyBase)){
							//用户身份叠加
							if($this->userSer->hasBit(intval($oldUserType), USER_TYPE_DOTEY)){
								$newUserType = $this->userSer->revokeBit(intval($oldUserType), USER_TYPE_DOTEY);
								$this->userSer->saveUserJson($uid, array('user_type'=>$newUserType));
							}
							$op_desc = '撤销已拒绝的主播申请(UID='.$uid.')';
							$this->doteySer->saveAdminOpLog($op_desc,$uid);
							exit('1');
						}else{
							exit('撤销拒绝失败');
						}
					}else{
						exit('撤销拒绝失败，请求不合法');
					}
				}
			}else{
				exit('参数有误，操作失败');
			}
		}else{
			exit('缺少参数');
		}
	}
	
	/**
	 * 删除主播申请记录
	 */
	public function delDoteyApply(){
		$uid = Yii::app()->request->getParam('uid');
		if (intval($uid)) {
			//没有直播记录的情况下才会删除
			$archivesSer = new ArchivesService();
			$info = $archivesSer->searchLiveRecordByCondition(array('uid'=>array($uid)));
			if ($info['count'] == 0){
				//删除申请记录
				if($this->doteySer->deleteApplyInfo($uid)){
					exit('1');
				}else{
					exit('删除失败');
				}
			}else{
				exit('已有开播记录，无法删除申请');
			}
		}else{
			exit('删除失败，缺少参数');
		}
	}
	
	/**
	 * 删除月度奖金配置
	 */
	public function delRewardMonthDo(){
		$pay_id = Yii::app()->request->getParam('pay_id');
		if (intval($pay_id)) {
			$config = array();
			$config['pay_id'] = $pay_id;
			$config['is_del'] = 1;
			
			//没有直播记录的情况下才会删除
			$consumeSer = new ConsumeService();
			if ($consumeSer->saveDoteyPayConfig($config)){
				exit('1');
			}else{
				exit('删除失败,该对象不存在');
			}
		}else{
			exit('删除失败，缺少参数');
		}
	}
	
	/**
	 * 创建档期
	 * 
	 * @param int $uid
	 * @param boolean $isHide
	 */
	public function createArchives($uid,$isHide = true){
		$archivesSer = new ArchivesService();
		//默认的档期类型
		$cats = array_shift($archivesSer->getAllArchiveCat());
		$cat_id = $cats['cat_id'];
		if($cat_id){
			$uinfo = $this->userSer->getUserBasicByUids(array($uid));
			if ($uinfo){
				$uinfo = $uinfo[$uid];
				$oldUserType = $uinfo['user_type'];
				
				$archivesInfo = array();
				$archivesInfo['uid'] = $uid;
				$archivesInfo['title'] = $uinfo['nickname'].'的直播间';
				$archivesInfo['cat_id'] = $cat_id;
				$archivesInfo['is_hide'] = $isHide?1:0;
				$archivesInfo['create_time'] = time();
				
				//直播间是否已经创建
				$_info = $archivesSer->getArchivesBycondition(array('uid'=>$uid,'cat_id'=>$cat_id));
				if(!$_info){
					$archivesSer->createArchives($archivesInfo);
				}else{
					$_info = array_shift($_info);
					$archivesInfo['archives_id'] = $_info['archives_id'];
					unset($archivesInfo['title']);
					return $archivesSer->saveArchives($archivesInfo);
				}
			}
		}
	}
	
	/**
	 * 搜索档期信息
	 * 
	 * @param array $condition
	 * @param unknown_type $catEnName
	 * @return Ambigous <multitype:, multitype:unknown Ambigous <multitype:unknown , unknown> >
	 */
	public function searchArchivesInfo(Array $condition,$catEnName = 'common'){
		$result = 1;
		if($condition){
			$archivesSer = new ArchivesService();
			$catInfo = $archivesSer->getAllArchiveCatByEnName($catEnName);
			if ($catInfo){
				$cat_id = $catInfo['cat_id'];
				$_con = array();
				
				$_con['cat_id'] = $cat_id;
				if (isset($condition['is_hide']) && is_numeric($condition['is_hide'])){
					$_con['is_hide'] = intval($condition['is_hide']);
				}
				
				if (isset($condition['archives_title'])){
					$_con['title'] = $condition['archives_title'];
				}
				
				if(count($_con)>1){
					$result = $archivesSer->getArchivesBycondition($_con,'uid');
				}
			}
			
		}
		return $result;
	}
	
	public function formatLiveRecords(Array $list = array(),&$liveRecords){
		if($list){
			foreach($list as $v){
				$liveRecords[$v['archives_id']][$v['record_id']]['live_time'] = $v['live_time']; 
				$liveRecords[$v['archives_id']][$v['record_id']]['end_time'] = $v['end_time']; 
			}
		}
	}
	
	public function getFilterUids(){
		$upFile = CUploadedFile::getInstanceByName('filter_uids');
		if($upFile){
			if(strtolower($upFile->getExtensionName()) == 'txt'){
				$tmpName = $upFile->getTempName();
				if($tmpName){
					return file($tmpName);
				}
			}
		}
		return array();
	}
	
	public function getVodSearchCondition(){
		$condition = Yii::app()->request->getParam('vod');
		if($condition){
			return $condition;
		}
		
		if(Yii::app()->request->getParam('to_uid')){
			$condition['to_uid'] = Yii::app()->request->getParam('to_uid');
		}
		if(Yii::app()->request->getParam('nickname')){
			$condition['nickname'] = Yii::app()->request->getParam('nickname');
		}
		if(Yii::app()->request->getParam('username')){
			$condition['username'] = Yii::app()->request->getParam('username');
		}
		if(Yii::app()->request->getParam('realname')){
			$condition['realname'] = Yii::app()->request->getParam('realname');
		}
		
		if(Yii::app()->request->getParam('dotey_cat')){
			$condition['dotey_cat'] = (int)Yii::app()->request->getParam('dotey_cat');
		}
		
		if(Yii::app()->request->getParam('start_time')){
			$condition['start_time'] = Yii::app()->request->getParam('start_time');
		}
		
		if(Yii::app()->request->getParam('end_time')){
			$condition['end_time'] = Yii::app()->request->getParam('end_time');
		}
		
		if(Yii::app()->request->getParam('s_update_time')){
			$condition['s_update_time'] = Yii::app()->request->getParam('s_update_time');
		}
		
		if(Yii::app()->request->getParam('e_update_time')){
			$condition['e_update_time'] = Yii::app()->request->getParam('e_update_time');
		}
		
		if(Yii::app()->request->getParam('is_handle') >= 0 ){
			$condition['is_handle'] = Yii::app()->request->getParam('is_handle');
		}
		
		return $condition?$condition:array();
	}
}
