<?php
/**
 * 主播排行榜脚本
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z suqian $ 
 * @package controllers
 * @subpackage days
 */
class PropsSendCommand extends PipiConsoleCommand {
	
	/**
		 * @var CDbConnection 新版消费库操作
		 */
		protected $consume_db;
		
		/**
		 * @var CDbConnection 新版消费库记录操作
		 */
		protected $consume_records_db;
		
		/**
		 * @var CDbConnection 新版消费库记录操作
		 */
		protected $user_db;
		
		/**
		 * @var CDbConnection 通用库操作
		 */
		protected $common_db;
		
		/**
		 * @var CDbConnection 档期库操作
		 */
		protected $archives_db;
		
		protected $propsService;
		
		
		public function beforeAction($action,$params){
			parent::beforeAction($action, $params);
			$this->consume_db = Yii::app()->db_consume;
			$this->consume_records_db =  Yii::app()->db_consume_records;
			$this->user_db = Yii::app()->db_user;;
			$this->common_db = Yii::app()->db_common;
			$this->archives_db = Yii::app()->db_archives;
			$this->propsService=new PropsService();
			return true;
		}
		
		public function actionIndex(){
		
		}
		/**
		 * 赠送普通贴条
		 */
		public function actionSendLabel(){
			$consumeCommand = $this->consume_db->createCommand();
			$consumeCommand->setText("SELECT * FROM web_props WHERE en_name='common_label'");
			$props = $consumeCommand->queryRow();
			if(empty($props)){
				echo "道具不存在\r\n";
				return;
			}
			//查出新升级的富豪用户，但还没有赠送贴条的
			$consumeCommand->setText('SELECT a.uid,a.rank from web_user_consume_attribute a WHERE  a.rank>=7 AND NOT EXISTS (SELECT * from web_user_props_bag b where a.uid = b.uid and b.prop_id = :prop_id)');
			$consumeCommand->bindParam(':prop_id',$props['prop_id']);
			$users = $consumeCommand->queryAll();
			if(empty($users)){
				echo "没有升级的数据\r\n";
				return;
			}
			$num = 10;
			$userPropsService = new UserPropsService();
			$props['category'] = array();
			foreach ( $users as $user){
				
				$records['uid'] = $user['uid'];
				$records['cat_id'] = $props['cat_id'];
				$records['prop_id'] = $props['prop_id'];
				$records['pipiegg'] = 0;
				$records['dedication'] = 0;
				$records['egg_points'] = 0;
				$records['charm'] = 0;
				$records['charm_points'] = 0;
				$records['vtime'] = 0;
				$records['info'] = '系统赠送普通贴条*10';
				$records['source'] = 0;
				$records['amount'] = $num;
				$recordSid = $userPropsService->saveUserPropsRecords($records,$props);
				$bag['uid'] = $user['uid'];
				$bag['target_id'] = 0;
				$bag['prop_id'] =$props['prop_id'];
				$bag['cat_id'] = $props['cat_id'];
				$bag['record_sid'] = $recordSid;
				$bag['num'] = $num;
				$bag['valid_time'] = 0;
				$userPropsService->saveUserPropsBag($bag,$props);
			}
			$count = count($users);
			echo '赠送普通贴条ID，总共赠送了'.$count."人\r\n";
			$users = $userPropsService->buildDataByIndex($users,'uid');
			echo '被赠送人的ID是：'.implode(array_keys($users),',')."\r\n";
		}
	
		//停用超期的vip,每天0点执行
		public function actionStopOvertimeVip(){
			$propsService=$this->propsService;
			//取得vip道具信息
			$yellowProps = $propsService->getPropsByEnName('vip_yellow');
			$purpleProps = $propsService->getPropsByEnName('vip_purple');
			
			$category = $propsService->getPropsCategoryByEnName('vip');
			$cat_id = $category['cat_id'];
			
			$consumeCommand = $this->consume_db->createCommand ();
			//扫描超时的vip
			$currentTime=time();
			$sqlOvertimeVip="select * from `web_user_props_bag` where `prop_id` in ({$yellowProps['prop_id']},{$purpleProps['prop_id']})  
			and `cat_id`={$cat_id} and `use_status`=0 and `valid_time`<{$currentTime} and `valid_time`>0";
			$consumeCommand->setText ( $sqlOvertimeVip);
			$OvertimeVipList=$consumeCommand->queryAll ();
			foreach ($OvertimeVipList as $OvertimeVipRow)
			{
				$this->stopVipOfBag($OvertimeVipRow['uid'], $OvertimeVipRow['prop_id'],$OvertimeVipRow['valid_time']);
			}
		}
	
		
		//停用道具背包中的vip
		private  function stopVipOfBag($uid,$prop_id,$timestamp)
		{
			/*
			 * 获得道具背包中的vip信息
			*/
			$userPropsService = new UserPropsService();
			$userPropsBagModel = new UserPropsBagModel();
			$_userPropsBagModel = $userPropsBagModel->findByAttributes(array('uid'=>$uid,'prop_id'=>$prop_id));
			
			$propsService=$this->propsService;
			if(isset($_userPropsBagModel->valid_time) && $_userPropsBagModel->valid_time>0)
				$propsService->saveVipUseRecords($uid, $prop_id, $_userPropsBagModel,$timestamp);
		
			//更新背包中vip状态
			$bag=array();
			$bag['uid'] = $uid;
			$bag['prop_id'] = $prop_id;
			$bag['use_status']=1;
			$bag['update_time']=$timestamp;		//停用时间
				
			//更新背包
			$propsService->attachAttribute($_userPropsBagModel, $bag);
			$_userPropsBagModel->save();
			//更新userJson
			return $propsService->updateUserJsonOfVip($uid, $prop_id,1);
		}
		
}