<?php
/**
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: 2013-11-7 上午10:59:29 hexin $ 
 * @package
 */
class AgentController extends PipiAdminController {
	/**
	 * @var string 当前操作
	 */
	protected $op;
	/**
	 * @var array
	 */
	protected $allowOp = array('test','grant','ungrant','checkAgent','config','edit','editDo','exportStat','exportRecords','addAgentPolicy','delAgentPolicy','getThreadInfo');
	/**
	 * @var boolean 是否是Ajax请求
	 */
	protected $isAjax;
	public $p;
	protected $pageSize = 20;
	protected $offset;
	
	protected $userService;
	protected $agentService;
	protected $bbsSer;
	
	public function init(){
		parent::init();
		$this->op = Yii::app()->request->getParam('op');
		$this->isAjax = Yii::app()->request->isAjaxRequest;
		if(!($this->p = Yii::app()->request->getParam('page'))){
			$this->p = 1;
		}
		$this->offset = ($this->p -1)*$this->pageSize;
		$this->userService = new UserService();
		$this->agentService = new AgentsService();
		$this->bbsSer = new BbsbaseService();
	}
	
	/**
	 * 代理团队列表
	 */
	public function actionList(){
		if($this->op == 'checkAgent' && in_array($this->op, $this->allowOp)){
			$this->_checkAgent();
		}
		if($this->op == 'config' && in_array($this->op, $this->allowOp)){
			$this->_config();
		}
		if($this->op == 'grant' && in_array($this->op, $this->allowOp)){
			$this->_grant();
		}
		if($this->op == 'ungrant' && in_array($this->op, $this->allowOp)){
			$this->_ungrant();
		}
		if($this->op == 'edit' && in_array($this->op, $this->allowOp)){
			$this->_edit();
		}
		if($this->op == 'editDo' && in_array($this->op, $this->allowOp)){
			$this->_editDo();
		}
		
		$conditions = array();
		if(Yii::app()->request->isPostRequest){
			$agent_status = Yii::app()->request->getParam('agent_status');
			if($agent_status !== '') $conditions['agent_status'] = $agent_status;
		}
		
		$list = $this->agentService->getAllAgent($this->p, 15, $conditions);
		$config = $this->agentService->getGlobalConfig();
		$this->render('agent_list',array('list'=>$list, 'conditions'=>$conditions, 'config' => $config));
	}
	
	/**
	 * AJAX 检验代理uid信息的合法性
	 */
	private function _checkAgent(){
		if (!$this->isAjax){
			exit('不合法请求');
		}
	
		$uid = Yii::app()->request->getParam('uid');
		if(!intval($uid)){
			exit('请输入uid后进行校验 ');
		}
		
		if(!($userInfo = $this->userService->getUserBasicByUids(array($uid)))){
			exit('不合法用户，请重新输入');
		}else{
			$userInfo = $userInfo[$uid];
		}
		if($this->agentService->checkAgentByUid($uid)){
			exit('该用户已是代理');
		}

		exit('1'.'#xx#'.$userInfo['uid'].'#xx#'.$userInfo['username'].'#xx#'.$userInfo['nickname']);
	}
	
	/**
	 * 授权代理及恢复授权
	 */
	private function _grant(){
		$uid = Yii::app()->request->getParam('uid');
		if($uid > 0){
			$agent = $this->agentService->getAgentByUids(array($uid));
			if(empty($agent)){
				$this->agentService->addAgent($uid);
			}else{
				$agent = array(
					'uid'	=> $uid,
					'agent_status'	=> AGENT_STATUS_USE,
				);
				$this->agentService->updateAgent($agent);
			}
		}
	}
	
	/**
	 * 停用授权
	 */
	private function _ungrant(){
		$uid = Yii::app()->request->getParam('uid');
		if($uid > 0){
			$agent = array(
				'uid'	=> $uid,
				'agent_status'	=> AGENT_STATUS_STOP,
			);
			$this->agentService->updateAgent($agent);
		}
	}
	
	/**
	 * 修改配置
	 */
	private function _config(){
		if(Yii::app()->request->isPostRequest){
			$config = Yii::app()->request->getParam('config');
			$global = $this->agentService->getGlobalConfig();
			foreach($global as $k => $v){
				if(isset($config[$k]) && $config[$k]!=$global[$k]){
					$global[$k] = $config[$k];
				}
			}
			$this->agentService->saveGlobalConfig($global);
		}
	}
	
	/**
	 * 编辑代理页面
	 */
	private function _edit(){
		$uid = Yii::app()->request->getParam('uid');
		$agent = $this->agentService->getAgentByUids(array($uid));
		$agent = array_pop($agent);
		$user = $this->userService->getUserBasicByUids(array($uid));
		$user = array_pop($user);
		$this->renderPartial('agent_edit', array('agent' => $agent, 'user' => $user));
		Yii::app()->end();
	}
	
	/**
	 * 编辑代理
	 */
	private function _editDo(){
		if(Yii::app()->request->isPostRequest){
			$agent = Yii::app()->request->getParam('form');
			$this->agentService->updateAgent($agent);
		}
	}
	
	/**
	 * 销售统计
	 */
	public function actionStat(){
		if($this->op == 'exportStat' && in_array($this->op, $this->allowOp)){
			$this->exportStat();
		}
		
		$dateCal = new PipiDateCal();
		$monthList = $dateCal->getCurrentYearPrevMonth();
		$month = Yii::app()->request->getParam('month', date('Y-m'));
		$exp = explode('-', $month);
		$conditions = array(
			'sale_year'	=> $exp[0],
			'sale_month' => $exp[1],
		);
		$list = $this->agentService->getStatByCondition($conditions, $this->p, $this->pageSize);
		$agent_ids = $this->agentService->buildDataByIndex($list['list'], 'agent_id');
		$agents = $this->agentService->getAgentByUids(array_keys($agent_ids));

		foreach($list['list'] as $k=>$v){
			$list['list'][$k]['agent_nickname'] = $agents[$v['agent_id']]['agent_nickname'];
			$list['list'][$k]['agent_status'] = $agents[$v['agent_id']]['agent_status'];
		}
		$amount = $this->agentService->getSaleStatByCondition($conditions);
		$this->render('agent_stat', array('monthList' => $monthList, 'month' => $month, 'list' => $list, 'amount' => $amount));
	}
	
	private function exportStat(){
		
		$dateCal = new PipiDateCal();
		$monthList = $dateCal->getCurrentYearPrevMonth();
		$month = Yii::app()->request->getParam('month', date('Y-m'));
		$exp = explode('-', $month);
		$conditions = array(
			'sale_year'	=> $exp[0],
			'sale_month' => $exp[1],
		);

		$list = $this->agentService->getStatByCondition($conditions, $this->p, 0);
		$agent_ids = $this->agentService->buildDataByIndex($list['list'], 'agent_id');
		$agents = $this->agentService->getAgentByUids(array_keys($agent_ids));
		
		foreach($list['list'] as $k=>$v){
			$list['list'][$k]['month']=$month;
			$list['list'][$k]['agent_nickname'] = $agents[$v['agent_id']]['agent_nickname'];
			$list['list'][$k]['agent_status'] = $agents[$v['agent_id']]['agent_status'];
		}

		$this->statExcel($list['list']);
	}
	
	/**
	 * 销售记录
	 */
	public function actionRecord(){
		
		if($this->op == 'exportRecords' && in_array($this->op, $this->allowOp)){
			$this->exportRecords();
		}
		$month = Yii::app()->request->getParam('month', date('Y-m'));
		$exp = explode('-', $month);
		
		$agent_id=Yii::app()->request->getParam('agent_id');

		$conditions = array(
			'agent_id'=>$agent_id,
			'sale_year'	=> $exp[0],
			'sale_month' => $exp[1],
		);
		$list = $this->agentService->getRecordsByCondition($conditions, $this->p, $this->pageSize);
		if($list['count']>0)
		{
			$uids=array();
			$prop_ids=array();
			foreach ($list['list'] as $row)
			{
				$uids[]=$row['uid'];
				$uids[]=$row['agent_id'];
				if($row['goods_type']==0)
					$prop_ids[]=$row['goods_id'];
			}
			$uids=$uids=array_unique($uids);
			$userInfoList=$this->userService->getUserBasicByUids($uids);
			$propsService=new PropsService();
			$propInfoList=$propsService->getPropsByIds($prop_ids);

			foreach ($list['list'] as $key=>$value)
			{
				$list['list'][$key]['agent_nickname']=$userInfoList[$value['agent_id']]['nickname'];
				$list['list'][$key]['user_nickname']=$userInfoList[$value['uid']]['nickname'];
				if($list['list'][$key]['goods_type'] == 0)
					$list['list'][$key]['goods_name']=$propInfoList[$value['goods_id']]['name'];
				elseif($list['list'][$key]['goods_type'] == 1)
					$list['list'][$key]['goods_name']='靓号'.$value['goods_id'];
			}
		}

		$this->render('agent_record', array('list'=>$list,'condition'=>$conditions));
	}
	
	private function exportRecords()
	{
		$month = Yii::app()->request->getParam('month', date('Y-m'));
		$exp = explode('-', $month);
		
		$agent_id=Yii::app()->request->getParam('agent_id');
		
		$conditions = array(
			'agent_id'=>$agent_id,
			'sale_year'	=> $exp[0],
			'sale_month' => $exp[1],
		);
		$list = $this->agentService->getRecordsByCondition($conditions, $this->p, 0);
		if($list['count']>0)
		{
			$uids=array();
			$prop_ids=array();
			foreach ($list['list'] as $row)
			{
				$uids[]=$row['uid'];
				$uids[]=$row['agent_id'];
				if($row['goods_type']==0)
					$prop_ids[]=$row['goods_id'];
			}
			$uids=$uids=array_unique($uids);
			$userInfoList=$this->userService->getUserBasicByUids($uids);
			$propsService=new PropsService();
			$propInfoList=$propsService->getPropsByIds($prop_ids);
		
			foreach ($list['list'] as $key=>$value)
			{
				$list['list'][$key]['agent_nickname']=$userInfoList[$value['agent_id']]['nickname'];
				$list['list'][$key]['user_nickname']=$userInfoList[$value['uid']]['nickname'];
				$list['list'][$key]['goods_name']=$propInfoList[$value['goods_id']]['name'];
			}
		}
		$this->recordsExcel($list['list']);
	}
	
	/**
	 * 代理政策
	 */
	public function actionPolicy(){
		$this->assetsCKEditor();
		$threadList = array();
		$threadList['count'] = 0;
		$forum_sid = $this->getAgentPolicyForumSubId();
		if(!empty($forum_sid)){
			
			$threadList = $this->bbsSer->getThreadList($forum_sid,$this->p,$this->pageSize);
		}
		//分页实例化
		$pager = new CPagination($threadList['count']);
		$pager->pageSize= $this->pageSize;
		$this->render('agent_policy', array('threadList'=>$threadList,'pager'=>$pager,'forum_sid'=>$forum_sid));
	}
	
	
	/**
	 * 添加代理政策
	 */
	public function actionAgentPolicy(){
		$this->assetsCKEditor();
		//是否删除
		if($this->op == 'delAgentPolicy' && in_array($this->op,$this->allowOp)){
			$agentpolicys = $this->delAgentPolicyDo();
		}
	
		$agentpolicys = array();
		//是否是添加动作
		if($this->op == 'addAgentPolicy' && in_array($this->op,$this->allowOp)){
			$agentpolicys = $this->addAgentPolicyDo();
		}
	
		//是否是修改
		$info = array();
		$postInfo = array();
		if($this->op == 'getThreadInfo' && in_array($this->op,$this->allowOp)){
			$info = $this->getThreadInfo();
			$postInfo = $this->getPostInfo();
		}
	
		if($this->isAjax){
			$this->renderPartial('add_agent_policy',array('agentpolicys'=>$agentpolicys,'info'=>$info,'postInfo'=>$postInfo));
		}else{
			$this->render('add_agent_policy',array('agentpolicys'=>$agentpolicys,'info'=>$info,'postInfo'=>$postInfo));
		}
	}
	
	/**
	 * 删除代理政策操作
	 */
	public function delAgentPolicyDo(){
		if(!$this->isAjax){
			exit('不合法请求');
		}
		if(!($threadId = Yii::app()->request->getParam('threadId'))){
			exit('缺少参数');
		}
		if(!($info = $this->bbsSer->getThreadInfo($threadId))){
			exit('要删除的数据不存在');
		}
		if($info['forum_sid'] == $this->getAgentPolicyForumSubId()){
			if($this->bbsSer->deleteThread($threadId)){
				exit('1');
			}else{
				exit("删除失败");
			}
		}else{
			exit("你没有权限删除此代理政策");
		}
	}
	
	/**
	 * 添加或修改代理政策动作
	 *
	 * @return Ambigous <获取用户界面友好提提示, 用户界面友好提提示>|multitype:multitype:string
	 */
	public function addAgentPolicyDo(){
		$data = Yii::app()->request->getParam('agentpolicy');
		$forum_sid = $this->getAgentPolicyForumSubId();
		$redirect = $this->createUrl('agent/policy');
		$post_id = Yii::app()->request->getParam('post_id');
		return $this->addArticle($data, $forum_sid, $redirect, $post_id);
	}
	
	/**
	 * 获取代理政策的子板块ID
	 *
	 * @return boolean
	 */
	public function getAgentPolicyForumSubId(){
		$forum_sid=$this->agentService->getAgentPolicyForumSubId();	
		return $forum_sid;
	}
	
	/**
	 * 添加文章的总接口
	 */
	public function addArticle($data,$forum_sid,$redirect,$post_id){
		if ($data && $forum_sid && $redirect){
			$data['uid'] = Yii::app()->user->getId();
			$data['forum_sid'] = $forum_sid;
			$data['create_time'] = time();
				
			if (isset($data['thread_id'])){
				if($post_id){
					$post = array();
					$post['post_id'] = $post_id;
					$post['content'] = $data['content'];
					$post['create_time'] = time();
					if($this->bbsSer->editThread($data) && $this->bbsSer->editPost($post)){
						$this->redirect($redirect);
					}else{
						return $this->bbsSer->getNotice();
					}
				}
			}else{
				$title = $data['title'];
				$uid = Yii::app()->user->getId();
				$content = $data['content'];
				if($forum_sid){
					if($this->bbsSer->releaseThread($forum_sid,$title,$uid,$content)){
						$this->redirect($redirect);
					}else{
						return $this->bbsSer->getNotice();
					}
				}
			}
		}
		return array('info'=>array('请输入数据'));
	}
	
	/**
	 * 获取主题信息
	 */
	public function getThreadInfo(){
		$info = array();
		if(!($threadId = Yii::app()->request->getParam('threadId'))){
			exit('缺少参数');
		}
		if(!($info = $this->bbsSer->getThreadInfo($threadId))){
			exit('要修改的数据不存在');
		}
		return $info;
	}
	
	/**
	 * 获取主题内容
	 * @return Ambigous <NULL>
	 */
	public function getPostInfo(){
		$info = array();
		if(!($threadId = Yii::app()->request->getParam('threadId'))){
			exit('缺少参数');
		}
	
		if(!($info = $this->bbsSer->getPostList($threadId))){
			exit('要修改的数据不存在');
		}
		return $info[0];
	}
	
	/**
	 * 下载销售统计
	 *
	 * @param array $list
	 */
	public function statExcel(Array $list){
		if($list){
			$fileName = "代理销售月统计_".date('Y-m-d',time()).'.csv';
			$output = fopen('php://output','w') or die("Can't open php://output");
			header("Content-Type: application/force-download");
			header('Content-Disposition: attachment;filename="'.$fileName.'"');
			header('Cache-Control: max-age=0');
			header("Content-Transfer-Encoding: binary");
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Pragma: no-cache");
			
			$temp_arr = array('月份','代理人', '授权状态', '销售笔数', '销售金额（皮蛋）', '提成收入（元）');
			foreach($temp_arr as $k=>$v){
				$temp_arr[$k] = mb_convert_encoding($v, "GBK", "UTF-8");
			}
			fputcsv($output,$temp_arr);

			foreach ($list as $row) {
					$c1=date("Y年m月",strtotime($row['month']));
					$c2=(isset($row['agent_nickname'])?$row['agent_nickname']:"")."(".$row['agent_id'].")";
					$c3=$row['agent_status'] == 0 ? '正常':'停用';
					$c4=$row['sale_count'];
					$c5=$row['sale_pipieggs'];
					$c6=$row['sum_income'];
					
				$temp_arr = array($c1,$c2,$c3,$c4,$c5,	$c6);
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
	 * 下载销售记录
	 *
	 * @param array $list
	 */
	public function recordsExcel(Array $list){
		if($list){
			$fileName = "代理销售记录_".date('Y-m',time()).'.csv';
			$output = fopen('php://output','w') or die("Can't open php://output");
			header("Content-Type: application/force-download");
			header('Content-Disposition: attachment;filename="'.$fileName.'"');
			header('Cache-Control: max-age=0');
			header("Content-Transfer-Encoding: binary");
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Pragma: no-cache");
	
			$temp_arr = array('购买时间', '代理人', '玩家', '购买道具', '购买数量', '购买价格（皮蛋）', '提成金额（元）');
			foreach($temp_arr as $k=>$v){
				$temp_arr[$k] = mb_convert_encoding($v, "GBK", "UTF-8");
			}
			fputcsv($output,$temp_arr);
	
			foreach ($list as $row) {
				$c1=date("Y-m-d H:i:s",$row['create_time']);
				$c2=(isset($row['agent_nickname'])?$row['agent_nickname']:"")."(".$row['agent_id'].")";
				$c3=isset($row['user_nickname'])?$row['user_nickname']:""."(".$row['uid'].")";
				$c4=isset($row['goods_name'])?$row['goods_name']:"";
				$c5=$row['goods_num'];
				$c6=$row['pipieggs'];
				$c7=$row['agent_income'];
					
				$temp_arr = array($c1,$c2,$c3,$c4,$c5,	$c6,$c7);
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
