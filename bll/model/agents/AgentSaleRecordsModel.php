<?php
/**
 * @author zhangzhifan <zhangzhifan@pipi.cn> 2010-11-2
 * @link http://show.pipi.cn
 * @copyright Copyright &copy; 2003-2010 show.pipi.cn
 * @license
 */
class AgentSaleRecordsModel extends PipiActiveRecord {
	/**
	 * @param unknown_type $className
	 * @return AgentSaleRecordsModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}

	public function tableName(){
		return '{{agent_sale_records}}';
	}


	public function getDbConnection(){
		return Yii::app()->db_consume_records;
	}

	//获取指定代理的月统计
	public function getMonthSaleStatByAgentId($saleYear,$agent_id)
	{
		if(empty($saleYear))
			$saleYear=date("Y");
		$agentSaleCommand=Yii::app()->db_consume_records->createCommand();
		$sql="select sale_month,count(*) as counts,sum(agent_income) as sum_income from web_agent_sale_records 
			where sale_year={$saleYear} and agent_id={$agent_id}";
		$agentSaleCommand->setText($sql);
		$saleStatList=$agentSaleCommand->queryAll();
		return $saleStatList;
	
	}
	
	//统计本月代理销售提成
	public function getThisMonthSaleIncome($agent_id)
	{
		list($thisYear,$thisMonth)=explode("-", date("Y-m"));
		$agentSaleCommand=Yii::app()->db_consume_records->createCommand();
		$sql="select sum(`agent_income`) as sum_income from web_agent_sale_records where 
			agent_id={$agent_id} and sale_year={$thisYear} and sale_month={$thisMonth}";
		$agentSaleCommand->setText($sql);
		return $agentSaleCommand->queryScalar();
	}
	
	//按月查询代理销售记录
	public function getRecordsByMonth($yearMonth,$agent_id,$offset=0, $pageSize=10)
	{
		list($saleYear,$saleMonth)=explode("-", $yearMonth);
		$result=array();
		$result['count'] = 0;
		$result['list'] = array();
		
		$criteria = $this->getDbCriteria();
		$criteria->compare('agent_id', $agent_id);
		$criteria->compare('sale_year', $saleYear);
		$criteria->compare('sale_month', $saleMonth);
		
		$result['count'] = $this->count($criteria);
		
		$criteria->limit=$pageSize;
		$criteria->offset = $offset;
		$criteria->order = 'create_time desc';
		
		$result['list'] = $this->findAll($criteria);
		return $result;
	}
	
	//按玩家查询代理销售记录
	public function getRecordsByUser($user_id,$agent_id,$offset=0, $pageSize=10)
	{
		$result=array();
		$result['count'] = 0;
		$result['list'] = array();
		
		$criteria = $this->getDbCriteria();
		$criteria->compare('agent_id', $agent_id);
		
		if(!empty($user_id))
			$criteria->compare('uid', $user_id);
		
		$result['count'] = $this->count($criteria);
		
		$criteria->limit=$pageSize;
		$criteria->offset = $offset;
		$criteria->order = 'create_time desc';
		
		$result['list'] = $this->findAll($criteria);
		return $result;
	}
	
	//查询销售记录
	public function getRecordsByCondition($condition,$offset=0,$pageSize=10)
	{
		$result=array();
		$result['count'] = 0;
		$result['list'] = array();
		$criteria = $this->getDbCriteria();
		
		if(!empty($condition['agent_id']))
			$criteria->compare('agent_id', $condition['agent_id']);
		if(!empty($condition['uid']))
			$criteria->compare('uid', $condition['uid']);
		if(!empty($condition['sale_year']))
			$criteria->compare('sale_year', $condition['sale_year']);
		if(!empty($condition['sale_month']))
			$criteria->compare('sale_month', $condition['sale_month']);
		
		$result['count'] = $this->count($criteria);
		if($pageSize>0)
		{
			$criteria->limit=$pageSize;
			$criteria->offset = $offset;
		}
		$criteria->order = 'create_time desc';
		
		$result['list'] = $this->findAll($criteria);
		return $result;
	}
	
	//统计销售金额和提成
	public function getSaleStatByCondition($condition)
	{
		$criteria = $this->getDbCriteria();
		
		if (!empty($condition['agent_id'])){
			$criteria->compare('agent_id', $condition['agent_id']);
		}
		
		if (!empty($condition['user_id'])){
			$criteria->compare('uid', $condition['user_id']);
		}
		
		if (!empty($condition['sale_year'])){
			$criteria->compare('sale_year', $condition['sale_year']);
		}
		
		if (!empty($condition['sale_month'])){
			$criteria->compare('sale_month', $condition['sale_month']);
		}
		
		$criteria->select="sum(`pipieggs`) as sale_pipieggs,sum(`agent_income`) as sum_income";
		return $this->getCommandBuilder()->createFindCommand($this->tableName(),$criteria)->queryRow();
		
	}
	
	//统计销售金额和提成
	public function getStatByCondition($condition, $offset = 0, $limit = 20){
		$criteria = $this->getCommandBuilder()->createCriteria();
	
		if (!empty($condition['sale_year'])){
			$criteria->compare('sale_year', $condition['sale_year']);
		}
	
		if (!empty($condition['sale_month'])){
			$criteria->compare('sale_month', $condition['sale_month']);
		}
	
		$criteria->select="agent_id, count(*) as sale_count, sum(`pipieggs`) as sale_pipieggs,sum(`agent_income`) as sum_income";
		$criteria->group = 'agent_id';
		$return['count'] = $this->count($criteria);
		if($limit>0)
		{
			$criteria->offset = $offset;
			$cirteria->limit = $limit;
		}
		$return['list'] = $this->getCommandBuilder()->createFindCommand($this->tableName(),$criteria)->queryAll();
		return $return;
	}
	
	//统指定月代理销售榜
	public function getAgentSalesTopByMonth($year,$month)
	{
		$agentSaleCommand=Yii::app()->db_consume_records->createCommand();
		$sql="SELECT `agent_id`,sum(`pipieggs`) as sum_pipieggs,sum(`agent_income`) as sum_income FROM `web_agent_sale_records` WHERE
		 `sale_year`={$year} and `sale_month`={$month} GROUP BY `agent_id` ORDER BY sum_pipieggs DESC";
		$agentSaleCommand->setText($sql);
		return $agentSaleCommand->queryAll();
	}
}

?>