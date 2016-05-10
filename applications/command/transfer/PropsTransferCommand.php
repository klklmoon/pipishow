<?php

class PropsTransferCommand extends CConsoleCommand {

		/**
		 * @var CDbConnection 老版ucenter读库操作
		 */
		protected $uncenter_db;
		
		/**
		 * @var CDbConnection 老版乐天读库操作
		 */
		protected $show_db;
		
		/**
		 * @var CDbConnection 新版消费库操作
		 */
		protected $consume_db;
		
		/**
		 * @var CDbConnection 新版消费库记录操作
		 */
		protected $consume_records_db;
		
		public function __constructs($name,$runner){
			parent::__construct($name,$runner);
		}
		/**
		 * 转移道具分类
		 */
		public function actionCategory(){
			$consumeDbCommand = $this->consume_db->createCommand();
			echo "清空新库道具分类表\n\r";
			$consumeDbCommand->setText('delete from  web_props_category;alter table web_props_category auto_increment=1');
			$consumeDbCommand->execute();
			$showDbCommand = $this->show_db->createCommand();
			echo "开始转移道具分类表\n\r";
			$showDbCommand->setText('SELECT * FROM web_props_category');
			$category = $showDbCommand->queryAll();
			$newData = '';
			foreach($category as $_category){
				$newData .= ($newData ? ',' : '').'('.$_category['cat_id'].',"'.$_category['name'].'","'.$_category['en_name'].'",'.$_category['is_display'].','.$_category['ctime'].')';
			}
			$consumeDbCommand->setText( ' insert into web_props_category (cat_id,name,en_name,is_display,create_time ) values '.$newData);
			$flag = $consumeDbCommand->execute();
			if($flag){
				echo "转移道具分类表成功\n\r";
			}
			
		}
		
		public function actionCategroyAttribute(){
			$consumeDbCommand = $this->consume_db->createCommand();
			echo "清空新库道具分类属性表\n\r";
			$consumeDbCommand->setText('delete from  web_props_cat_attribute;alter table web_props_cat_attribute auto_increment=1');
			$consumeDbCommand->execute();
			$showDbCommand = $this->show_db->createCommand();
			echo "开始转移道具分类属性表\n\r";
			$showDbCommand->setText('SELECT * FROM web_props_cat_attribute');
			$categoryAtrr = $showDbCommand->queryAll();
			$newData = '';
			foreach($categoryAtrr as $_attr){
				$newData .= ($newData ? ',' : '').'('.$_attr['attr_id'].','.$_attr['cat_id'].',"'.$_attr['attr_name'].'","'.$_attr['attr_enname'].'","'.$_attr['attr_value'].'",'.$_attr['attr_type'].','.$_attr['is_multi'].','.$_attr['is_display'].')';
			}
			$consumeDbCommand->setText('insert into web_props_cat_attribute (attr_id,cat_id,attr_name,attr_enname,attr_value,attr_type,is_multi,is_display) values '.$newData);
			$flag = $consumeDbCommand->execute();
			if($flag){
				echo "转移道具分类属性表成功\n\r";
			}
		}
		
		
		public function actionProps(){
			echo "清空新库道具表\n\r";
			$consumeDbCommand = $this->consume_db->createCommand();
			$consumeDbCommand->setText('delete from  web_props;alter table web_props auto_increment=1');
			$consumeDbCommand->execute();
			$showDbCommand = $this->show_db->createCommand();
			echo "开始转移道具表\n\r";
			$showDbCommand->setText('SELECT * FROM web_props');
			$props = $showDbCommand->queryAll();
			$newData = '';
			foreach($props as $p){
				$newData .= ($newData ? ',' : '').'('.$p['prop_id'].','.$p['cat_id'].',"'.$p['name'].'","'.$p['en_name'].'","'.$p['image'].'",'.$p['price'].','.$p['charm'].',0,'.$p['dedication'].',0,'.$p['status'].','.$p['rank'].','.$p['sort'].','.$p['ctime'].')';
			}
			$consumeDbCommand->setText('insert into web_props (prop_id,cat_id,name,en_name,image,pipiegg,charm,charm_points,dedication,egg_points,status,rank,sort,create_time) values '.$newData);
			$flag = $consumeDbCommand->execute();
			
			if($flag){
				echo "转移道具表成功\n\r";
			}
		}
		
		public function actionPropsAttribute(){
			$consumeDbCommand = $this->consume_db->createCommand();
			echo "清空新库道具属性表\n\r";
			$consumeDbCommand->setText('delete from  web_props_attribute;alter table web_props_attribute auto_increment=1');
			$consumeDbCommand->execute();
			$showDbCommand = $this->show_db->createCommand();
			echo "开始转移道具属性表\n\r";
			$showDbCommand->setText('SELECT * FROM web_props_attribute');
			$attribute = $showDbCommand->queryAll();
			$newData = '';
			foreach($attribute as $atr){
				$newData .= ($newData ? ',' : '').'('.$atr['pattr_id'].',"'.addslashes($atr['value']).'",'.$atr['prop_id'].','.$atr['attr_id'].')';
			}
			$consumeDbCommand->setText( ' insert  web_props_attribute (pattr_id,value,prop_id,attr_id) values '.$newData);
			$flag = $consumeDbCommand->execute();
			if($flag){
				echo "转移道具属性表成功\n\r";
			}
		}
		
		
		public function actionUserBagProps(){
			$consumeDbCommand = $this->consume_db->createCommand();
			echo "清空新库道具背包表\n\r";
			$consumeDbCommand->setText('delete from  web_user_props_bag;alter table web_user_props_bag auto_increment=1');
			$consumeDbCommand->execute();
			$showDbCommand = $this->show_db->createCommand();
			echo "开始转移座驾\n\r";
			$showDbCommand->setText('SELECT * FROM web_user_props_car');
			$cars = $showDbCommand->queryAll();
			$showDbCommand->setText('');
			$consumeDbCommand->setText( 'insert web_user_props_bag (uid,prop_id,cat_id,target_id,record_sid,num,valid_time) values (:uid,:propId,:catId,:targetId,:sid,:num,:time)');
			foreach($cars as $car){
				$params = array(':uid'=>$car['uid'],':prop_id'=>$car['prop_id']);
				$uProp = $showDbCommand->from('web_user_props')->where('uid = :uid AND prop_id=:prop_id',$params)->order('ctime DESC')->limit(1)->queryRow();
				$consumeDbCommand->bindValue(':uid',$car['uid']);
				$consumeDbCommand->bindValue(':catId',$uProp['cat_id']);
				$consumeDbCommand->bindValue(':propId',$car['prop_id']);
				$consumeDbCommand->bindValue(':targetId',0);
				$consumeDbCommand->bindValue(':sid',$uProp['rpid']);
				$consumeDbCommand->bindValue(':num',1);
				$consumeDbCommand->bindValue(':time',$car['vtime']);
				$consumeDbCommand->execute();
			}
			echo "转移座驾成功\n\r";
			
			
			echo "开始转移VIP\n\r";
			$showDbCommand->setText('SELECT * FROM web_user_props_vip');
			$vips = $showDbCommand->queryAll();
			$showDbCommand->setText('');
			$consumeDbCommand->setText( 'insert web_user_props_bag (uid,prop_id,cat_id,target_id,record_sid,num,valid_time) values (:uid,:propId,:catId,:targetId,:sid,:num,:time)');
			foreach($vips as $vip){
				$params = array(':uid'=>$vip['uid'],':prop_id'=>$vip['prop_id']);
				$uProp = $showDbCommand->from('web_user_props')->where('uid = :uid AND prop_id=:prop_id',$params)->order('ctime DESC')->limit(1)->queryRow();
				$consumeDbCommand->bindValue(':uid',$vip['uid']);
				$consumeDbCommand->bindValue(':catId',$uProp['cat_id']);
				$consumeDbCommand->bindValue(':propId',$vip['prop_id']);
				$consumeDbCommand->bindValue(':targetId',0);
				$consumeDbCommand->bindValue(':sid',$uProp['rpid']);
				$consumeDbCommand->bindValue(':num',1);
				$consumeDbCommand->bindValue(':time',$vip['vtime']);
				$consumeDbCommand->execute();
			}
			echo "转移VIP成功\n\r";
			
			
			echo "开始转移月卡\n\r";
			$showDbCommand->setText('SELECT * FROM web_user_monthcard');
			$monthCard = $showDbCommand->queryAll();
			$showDbCommand->setText('');
			$consumeDbCommand->setText( 'insert web_user_props_bag (uid,prop_id,cat_id,target_id,record_sid,num,valid_time) values (:uid,:propId,:catId,:targetId,:sid,:num,:time)');
			foreach($monthCard as $card){
				$params = array(':uid'=>$card['uid'],':prop_id'=>$card['prop_id']);
				$uProp = $showDbCommand->from('web_user_props')->where('uid = :uid AND prop_id=:prop_id',$params)->order('ctime DESC')->limit(1)->queryRow();
				$consumeDbCommand->bindValue(':uid',$card['uid']);
				$consumeDbCommand->bindValue(':catId',$uProp['cat_id']);
				$consumeDbCommand->bindValue(':propId',$card['prop_id']);
				$consumeDbCommand->bindValue(':targetId',0);
				$consumeDbCommand->bindValue(':sid',$uProp['rpid']);
				$consumeDbCommand->bindValue(':num',$card['quantity']);
				$consumeDbCommand->bindValue(':time',$card['endtime']);
				$consumeDbCommand->execute();
			}
			echo "转移月卡成功\n\r";
			
			echo "开始转移系统赠送的贴条和飞屏\n\r";
			$showDbCommand->setText('SELECT * FROM web_prop_bag');
			$bags = $showDbCommand->queryAll();
			$showDbCommand->setText('');
			$consumeDbCommand->setText( 'insert web_user_props_bag (uid,prop_id,cat_id,target_id,record_sid,num,valid_time) values (:uid,:propId,:catId,:targetId,:sid,:num,:time)');
			foreach($bags as $bag){
				$params = array(':uid'=>$bag['uid'],':prop_id'=>$bag['prop_id']);
				$uProp = $showDbCommand->from('web_user_props')->where('uid = :uid AND prop_id=:prop_id',$params)->order('ctime DESC')->limit(1)->queryRow();
				$consumeDbCommand->bindValue(':uid',$bag['uid']);
				$consumeDbCommand->bindValue(':catId',$uProp['cat_id']);
				$consumeDbCommand->bindValue(':propId',$bag['prop_id']);
				$consumeDbCommand->bindValue(':targetId',0);
				$consumeDbCommand->bindValue(':sid',$uProp['rpid']);
				$consumeDbCommand->bindValue(':num',$bag['num']);
				$consumeDbCommand->bindValue(':time',$bag['etime']);
				$consumeDbCommand->execute();
			}
			echo "转移系统赠送的贴条成功\n\r";
			echo 'total :'.count($cars)+count($bags)+count($vips)+count($monthCard);
		}
		
		public function actionBuyRecords(){
			$consumeDbCommand = $this->consume_db->createCommand();
			$consumeRecordsDbCommand = $this->consume_records_db->createCommand();
			echo "清空新库道具购卖记录表\n\r";
			$consumeRecordsDbCommand->setText('delete from  web_user_props_records;alter table web_user_props_records auto_increment=1');
			$consumeRecordsDbCommand->execute();
			$showDbCommand = $this->show_db->createCommand();
			echo "开始转移购买记录\n\r";
			$showDbCommand->setText('SELECT * FROM web_user_props where source != 3');
			$userProps = $showDbCommand->queryAll();
			$showDbCommand->setText('');
			$consumeRecordsDbCommand->setText( 'insert web_user_props_records (record_id,uid,prop_id,cat_id,info,amount,source,pipiegg,charm,charm_points,dedication,egg_points,ctime,vtime) values (:recordId,:uid,:propId,:catId,:info,:amount,:source,:pipiegg,:charm,:charm_points,:dedication,:egg_points,:ctime,:vtime)');
			foreach($userProps as $p){
				$consumeRecordsDbCommand->bindValue(':uid',$p['uid']);
				$consumeRecordsDbCommand->bindValue(':catId',$p['cat_id']);
				$consumeRecordsDbCommand->bindValue(':propId',$p['prop_id']);
				$consumeRecordsDbCommand->bindValue(':recordId',$p['rpid']);
				$consumeRecordsDbCommand->bindValue(':amount',$p['amount']);
				$consumeRecordsDbCommand->bindValue(':source',$p['source']);
				$consumeRecordsDbCommand->bindValue(':ctime',$p['ctime']);
				$consumeRecordsDbCommand->bindValue(':vtime',$p['vtime']);
				$consumeRecordsDbCommand->bindValue(':pipiegg',$p['pipiegg']);
				$consumeRecordsDbCommand->bindValue(':charm',$p['charm']);
				$consumeRecordsDbCommand->bindValue(':charm_points',0);
				$consumeRecordsDbCommand->bindValue(':dedication',$p['dedication']);
				$consumeRecordsDbCommand->bindValue(':egg_points',0);
				$consumeRecordsDbCommand->bindValue(':info',$p['info']);
				$consumeRecordsDbCommand->execute();
			}
			$countUser = count($userProps);
			echo "转移道具购买记录成功  {$countUser}\n\r";
			echo "开始转移道具使用记录\n\r";
			$showDbCommand->setText('SELECT * FROM web_user_label');
			$useLabels = $showDbCommand->queryAll();
			$showDbCommand->setText('');
			$consumeDbCommand->setText('insert web_user_props_use(record_sid,uid,to_uid,target_id,prop_id,cat_id,use_type,num,create_time,valid_time)values(:sid,:uid,:toUid,:targetId,:propId,:catId,:useType,:num,:createTime,:validTime)');
			$i = $j = 0;
			//可以追踪到正在生效的或者最后被贴条的用户的贴条用户
			foreach($useLabels as $p){
				//查贴条的的那个人
				$params = array(':vtime'=>$p['vtime'],'prop_id'=>$p['prop_id']);
				$uProps = $showDbCommand->from('web_user_props')->where('prop_id = :prop_id AND vtime = :vtime',$params)->limit(1)->queryRow();
				if($uProps){
					$consumeDbCommand->bindValue(':sid',$uProps['rpid']);
					$consumeDbCommand->bindValue(':uid',$uProps['uid']);
					$consumeDbCommand->bindValue(':toUid',$p['uid']);
					$consumeDbCommand->bindValue(':targetId',0);
					$consumeDbCommand->bindValue(':propId',$uProps['prop_id']);
					$consumeDbCommand->bindValue(':catId',$uProps['cat_id']);
					$consumeDbCommand->bindValue(':useType',0);
					$consumeDbCommand->bindValue(':num',$uProps['amount']);
					$consumeDbCommand->bindValue(':createTime',$uProps['ctime']);
					$consumeDbCommand->bindValue(':validTime',$p['vtime']);
					$consumeDbCommand->execute();
					$i++;
				}else{
					$j++;
				}
			}
			echo "转移道具使用记录成功 success {$i} .skip{$j}\n\r";
			echo "开始转移道具使用记录，不可被追踪到贴条的用户\n\r";
			$showDbCommand->setText('SELECT * FROM web_user_props where source = 3');
			$tranceProps = $showDbCommand->queryAll();
			$showDbCommand->setText('');
			$consumeDbCommand->setText('insert web_user_props_use(record_sid,uid,to_uid,target_id,prop_id,cat_id,use_type,num,create_time,valid_time)values(:sid,:uid,:toUid,:targetId,:propId,:catId,:useType,:num,:createTime,:validTime)');
			$consumeNewDbCommand = $this->consume_db->createCommand();
			$i = $j = 0;
			foreach($tranceProps as $p){
				$use = $consumeNewDbCommand->from('web_user_props_use')->select('uid,record_sid,prop_id')->where('record_sid = :sid',array(':sid'=>$p['rpid']))->limit(1)->queryRow();
				if(empty($use) && !$use){
					$consumeDbCommand->bindValue(':sid',$p['rpid']);
					$consumeDbCommand->bindValue(':uid',$p['uid']);
					$consumeDbCommand->bindValue(':toUid',0);
					$consumeDbCommand->bindValue(':targetId',0);
					$consumeDbCommand->bindValue(':propId',$p['prop_id']);
					$consumeDbCommand->bindValue(':catId',$p['cat_id']);
					$consumeDbCommand->bindValue(':useType',0);
					$consumeDbCommand->bindValue(':num',$p['amount']);
					$consumeDbCommand->bindValue(':createTime',$p['ctime']);
					$consumeDbCommand->bindValue(':validTime',$p['vtime']);
					$consumeDbCommand->execute();
					$i++;
				}else{
					$j++;
				}
			}
			echo "转移道具使用记录结束，不可被追踪到贴条的用户success {$i} .skip{$j}\n\r";
		
		}
	
		public function actionTransProps(){
			$this->actionCategory();
			$this->actionCategroyAttribute();
			$this->actionProps();
			$this->actionPropsAttribute();
		}
		public function beforeAction($action,$params){
			$this->uncenter_db = Yii::app()->db_read_ucenter;
			$this->show_db = Yii::app()->db_read_pipishow;
			$this->consume_db = Yii::app()->db_consume;
			$this->consume_records_db =  Yii::app()->db_consume_records;
			return true;
		}
}

?>