<?php
/**
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author su qian <suqian@pipi.cn>
 * @version $Id: OperateModel.php 9671 2013-05-06 13:51:21Z suqian $ 
 * @package model
 * @subpackage common
 */
class DoteyCategoryIndexModel extends PipiActiveRecord{
	/**
	 * @param string $className
	 * @return DoteyCategoryIndexModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{dotey_category_index}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_common;
	}
	
	public function searchCategory(array $condtion){
		$criteria = $this->getDbCriteria();
		
		if(isset($condtion['status']) & $condtion['status']){
			$criteria->condition .= ' status = :status ';
			$criteria->params[':status'] = $condtion['status'];
		}
		if(isset($condtion['rank'])){
			if($condtion['rank'] == 1){
				//皇冠范围
				$criteria->addBetweenCondition('rank',15,25);
			}elseif($condtion['rank'] == 2){
				//绿钻范围
				$criteria->addBetweenCondition('rank',6,14);
			}elseif($condtion['rank'] == 3){
				//红心范围
				$criteria->addBetweenCondition('rank',0,5);
			}
			
		}
		
		if(isset($condtion['channel']) && $condtion['channel']){
			$criteria->addInCondition('channel_area_id',$condtion['channel']);
		}
		return $this->findAll($criteria);
	}

}