<?php
/**
 * 主播节目分类搜索计划任务脚本
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z suqian $ 
 * @package controllers
 * @subpackage days
 */
class AgentsCommand extends PipiConsoleCommand {
	
		public function beforeAction($action,$params){
			parent::beforeAction($action, $params);
			return true;
		}
		
		//上月销量榜，每个月执行一次
		public function actionLastMonthSalesTop()
		{
			$result=false;
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
				$otherRedisModel=new OtherRedisModel();
				$result=$otherRedisModel->setAgentSalesTop($salesTop);
			}			
			
			if($result)
			{
				echo "生成成功 \n";
			}
			else
			{
				echo "生成失败 \n";
			}
		}
}