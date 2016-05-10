<?php
/**
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He Xin <hexin@pipi.cn>
 * @version $Id: ConsumeModel.php 14765 2013-09-06 14:17:11Z hexin $ 
 * @package model
 * @subpackage consume
 */
class ConsumeModel extends PipiActiveRecord{
	/**
	 * @param string $className
	 * @return ConsumeModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{user_consume_attribute}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_consume;
	}
	
	/**
	 * 添加皮蛋事务
	 * @param int $uid 用户ID
	 * @param float $eggs 皮蛋数量
	 * @return int 执行结果，1为成功，0为失败
	 */
	public function addEggs($uid, $eggs) {
		$uid = intval($uid);
		$eggs = floatval($eggs);
		$dbCommand = $this->getDbCommand();
		$dbCommand->setText("call proc_addEggs(:uid, :eggs)");
		$dbCommand->prepare();
		$dbCommand->bindValues(array(':uid'=>$uid,':eggs'=>$eggs));
		$data = $dbCommand->queryScalar();
		return $data;
	}
	
	/**
	 * 添加担保消费的冻结皮蛋事务
	 * @param int $uid 用户ID
	 * @param float $eggs 皮蛋数量
	 * @return int 执行结果，1为成功，0为失败
	 */
	public function addFreezeEggs($uid, $eggs) {
		$uid = intval($uid);
		$eggs = floatval($eggs);
		$dbCommand = $this->getDbCommand();
		$dbCommand->setText("call proc_addfreezeEggs(:uid, :eggs)");
		$dbCommand->prepare();
		$dbCommand->bindValues(array(':uid'=>$uid,':eggs'=>$eggs));
		$data = $dbCommand->queryScalar();
		return $data;
	}
	
	/**
	 * 撤销添加担保消费的冻结皮蛋事务
	 * @param int $uid 用户ID
	 * @param float $eggs 皮蛋数量
	 * @return int 执行结果，1为成功，0为失败
	 */
	public function unAddFreezeEggs($uid, $eggs) {
		$uid = intval($uid);
		$eggs = floatval($eggs);	
		$dbCommand = $this->getDbCommand();
		$dbCommand->setText("call proc_unAddfreezeEggs(:uid, :eggs)");
		$dbCommand->prepare();
		$dbCommand->bindValues(array(':uid'=>$uid,':eggs'=>$eggs));
		$data = $dbCommand->queryScalar();
		return $data;
	}
	
	/**
	 * 消费皮蛋事务
	 * @param int $uid 用户ID
	 * @param float $eggs 皮蛋数量
	 * @return int 执行结果，1为成功，0为失败
	 */
	public function consumeEggs($uid, $eggs) {
		$uid = intval($uid);
		$eggs = floatval($eggs);
		try{
			$dbCommand = $this->getDbCommand();
			$dbCommand->setText("call proc_consumeEggs(:uid, :eggs)");
			$dbCommand->prepare();
			$dbCommand->bindValues(array(':uid'=>$uid,':eggs'=>$eggs));
			$data = $dbCommand->queryScalar();
		}catch(Exception $e){
			$data = 0;
		}
		return $data;
	}
	
	/**
	 * 冻结皮蛋事务
	 * @param int $uid 用户ID
	 * @param float $eggs 皮蛋数量
	 * @return int 执行结果，1为成功，0为失败
	 */
	public function freezeEggs($uid, $eggs) {
		$uid = intval($uid);
		$eggs = floatval($eggs);
		$dbCommand = $this->getDbCommand();
		$dbCommand->setText("call proc_freezeEggs(:uid, :eggs)");
		$dbCommand->prepare();
		$dbCommand->bindValues(array(':uid'=>$uid,':eggs'=>$eggs));
		$data = $dbCommand->queryScalar();
		return $data;
	}
	
	/**
	 * 释放冻结皮蛋事务
	 * @param int $uid 用户ID
	 * @param float $eggs 皮蛋数量
	 * @return int 执行结果，1为成功，0为失败
	 */
	public function unFreezeEggs($uid, $eggs) {
		$uid = intval($uid);
		$eggs = floatval($eggs);
		$dbCommand = $this->getDbCommand();
		$dbCommand->setText("call proc_unFreezeEggs(:uid, :eggs)");
		$dbCommand->prepare();
		$dbCommand->bindValues(array(':uid'=>$uid,':eggs'=>$eggs));
		$data = $dbCommand->queryScalar();
		return $data;
	}
	
	/**
	 * 主播确认演唱已点歌记录事物
	 *
	 * @param int $recordId  点歌记录id
	 * @return int 执行结果，1为成功，0为失败
	 */
	public function actSong($recordId){
		$recordId=intval($recordId);
		$dbCommand = $this->getDbCommand();
		$dbCommand->setText("call proc_actSong(:recordId)");
		$dbCommand->prepare();
		$dbCommand->bindValues(array(':recordId'=>$recordId));
		$data = $dbCommand->queryScalar();
		return $data;
	}
	
	/**
	 * 主播取消点歌给用户返还皮蛋事物
	 *
	 * @param int $recordId  点歌记录id
	 * @param int $uid       用户Id
	 * @param float $eggs    返还皮蛋数
	 * @return int 执行结果，1为成功，0为失败
	 */
	public function cancelSongReturnEggs($recordId,$uid,$eggs){
		$recordId=intval($recordId);
		$uid = intval($uid);
		$eggs = floatval($eggs);
		$dbCommand = $this->getDbCommand();
		$dbCommand->setText("call proc_cancelSongReturnEggs(:recordId,:uid,:eggs)");
		$dbCommand->prepare();
		$dbCommand->bindValues(array(':recordId'=>$recordId,':uid'=>$uid,':eggs'=>$eggs));
		$data = $dbCommand->queryScalar();
		return $data;
	}
	
	public function getConsumesByUids(array $uids){
		if(empty($uids)){
			return array();
		}
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('uid',$uids);
		return $this->findAll($criteria);
	}
	
	/**
	 * 执行兑换魅力点的动作
	 * @param $uid
	 * @param $charmPoint
	 * @return int  0表示失败, 1表示成功
	 */
	public function exchangeCharmPoint($uid, $charmPonit)
	{
		$uid = intval($uid);
		$charmPonit = floatval($charmPonit);
		$dbCommand = $this->getDbCommand();
		$dbCommand->setText("call proc_consumeCharmPoint(:uid, :charmPonit)");
		$dbCommand->prepare();
		$dbCommand->bindValues(array(':uid'=>$uid,':charmPonit'=>$charmPonit));
		$data = $dbCommand->queryScalar();
		return $data;
	}
	
	/**
	 * 执行兑换魅力值的动作
	 * 
	 * @author supeng
	 * @param $uid
	 * @param $charm
	 * @return int  0表示失败, 1表示成功
	 */
	public function exchangeCharm($uid, $charm)
	{
		$uid = intval($uid);
		$charm = floatval($charm);
		$dbCommand = $this->getDbCommand();
		$dbCommand->setText("call proc_reducecharm(:uid, :charm)");
		$dbCommand->prepare();
		$dbCommand->bindValues(array(':uid'=>$uid,':charm'=>$charm));
		$data = $dbCommand->queryScalar();
		return $data;
	}
	
	/**
	 * 执行兑换皮点的动作
	 * @param $uid
	 * @param $eggPoint
	 * @return int  0表示失败, 1表示成功
	 */
	public function exchangeEggPoint($uid, $eggPoint)
	{
		$uid = intval($uid);
		$eggPoint = floatval($eggPoint);
		$dbCommand = $this->getDbCommand();
		$dbCommand->setText("call proc_consumeEggPoint(:uid, :charm)");
		$dbCommand->prepare();
		$dbCommand->bindValues(array(':uid'=>$uid,':charm'=>$eggPoint));
		$data = $dbCommand->queryScalar();
		return $data;
	}
	
	/**
	 * 兑换魅力点和皮点
	 * @param $uid
	 * @param $point
	 */
	public function exchangeEggPointCharmPoint($uid, $point)
	{
		$uid = intval($uid);
		$point = floatval($point);
		$dbCommand = $this->getDbCommand();
		$dbCommand->setText("call proc_exchangeEggPointCharmPoint(:uid, :point)");
		$dbCommand->prepare();
		$dbCommand->bindValues(array(':uid'=>$uid,':point'=>$point));
		$data = $dbCommand->queryScalar();
		return $data;
	}
	
	/**
	 * @author supeng
	 * @param array $condition
	 * @return Ambigous <NULL, unknown, multitype:unknown Ambigous <unknown, NULL> , mixed, multitype:, multitype:unknown >
	 */
	public function getConsumesByConditons(Array $condition,$offset=0,$pageSize=10,$isLimit = true, $order = ''){
		$result['count'] = 0;
		$result['list'] = array();
		
		$criteria = $this->getDbCriteria();
		
		if (isset($condition['dotey_rank'])){
			$criteria->condition .= ($criteria->condition ? ' AND ' : '').' dotey_rank = :dotey_rank';
			$criteria->params[':dotey_rank'] = $condition['dotey_rank'];
		}
		
		if (isset($condition['rank'])){
			$criteria->condition .= ($criteria->condition ? ' AND ' : '').' rank = :rank';
			$criteria->params[':rank'] = $condition['rank'];
		}
		
		if (isset($condition['charm'])){
			$criteria->condition .= ($criteria->condition ? ' AND ' : '').' charm >= :charm';
			$criteria->params[':charm'] = $condition['charm'];
		}
		
		if (isset($condition['uid'])){
			$criteria->condition .= ($criteria->condition ? ' AND ' : '').' uid >= :uid';
			$criteria->params[':uid'] = $condition['uid'];
		}
		
		if(isset($condition['uids']) && is_array($condition['uids'])){
			$criteria->addInCondition('uid', $condition['uids']);
		}
		
		if ($isLimit){
			$result['count'] = $this->count($criteria);
			$criteria->limit = $pageSize;
			$criteria->offset = $offset;

			$pages=new CPagination($result['count']);
			$pages->pageSize = $pageSize;
			$pages->applyLimit($criteria);
			$result['pages'] = $pages; 
		}
		
		if($order){
			$criteria->order = $order;
		}
		
		$result['list'] = $this->findAll($criteria);
		
		return $result;
	}
	
	public function updateAttributeByUid($uid,array $counter = array()){
		if($uid <= 0 || empty($counter)){
			return array();
		}
		$criteria = $this->getDbCriteria();
		$criteria->condition = ' uid = :uid ';
		$criteria->params[':uid'] = $uid;
		$return = $this->updateCounters($counter,$criteria);
		
		return $return;
	}
}