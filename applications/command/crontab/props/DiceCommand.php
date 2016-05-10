<?php
/**
 * 骰子游戏解冻失效对局用户皮蛋
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: leiwei $>
 * @author leiwei <leiwei@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z leiwei $ 
 * @package controllers
 * @subpackage days
 */
class DiceCommand extends PipiConsoleCommand {
	
		/**
		 * @var CDbConnection 新版消费库操作
		 */
		protected $consume_db;
		
		protected $consume_records_db;
		
		
		
		public function beforeAction($action,$params){
			parent::beforeAction($action, $params);
			$this->consume_db = Yii::app()->db_consume;
			$this->consume_records_db =  Yii::app()->db_consume_records;
			return true;
		}
		
		public function actionIndex(){
		
		}
		
		public function actionFreezeEggs(){
			$consumeCommand = $this->consume_db->createCommand();
			$consumeCommand->setText("SELECT * FROM web_user_dice_records WHERE type=1 AND pipiegg>0 AND result=0 AND valid_time<unix_timestamp();");
			$diceRecord = $consumeCommand->queryAll();
			if($diceRecord){
				$consumeService=new ConsumeService();
				echo "骰子游戏对局失效，解冻用户被冻结的皮蛋\r\n";
				foreach($diceRecord as $row){
					if($consumeService->unFreezeEggs($row['uid'],$row['pipiegg'])<=0){
						echo "用户uid:".$row['uid']."皮蛋解冻失败,记录Id".$row['record_id']."皮蛋数:".$row['pipiegg']."\r\n";
					}else{
						$consumeCommand->setText("UPDATE web_user_dice_records SET result=2 WHERE record_id=".$row['record_id']);
						$consumeCommand->execute();
						echo "用户uid:".$row['uid']."皮蛋解冻成功,记录Id".$row['record_id']."皮蛋数:".$row['pipiegg']."\r\n";
						$tofRecords['uid']=$row['uid'];
						$tofRecords['from_target_id']=$row['prop_id'];
						$tofRecords['to_target_id']=$row['target_id'];
						$tofRecords['record_sid']=$row['record_id'];
						$tofRecords['pipiegg']=$row['pipiegg'];
						$tofRecords['source']=SOURCE_PROPS;
						$tofRecords['sub_source']=SUBSOURCE_PROPS_DICE;
						$tofRecords['num']=1;
						$tofRecords['extra']='对局失效解冻皮蛋(骰子游戏)';
						if($consumeService->saveUserFreezeePipiEggRecords($tofRecords,false)<=0){
							echo "皮蛋解冻记录写入失败:".json_encode($tofRecords)."\r\n";
						}
					}
					
				}
			}
		}
		
}