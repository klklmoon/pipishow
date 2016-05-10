<?php
/**
 * 家族扩展信息
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: 2013-8-6 下午3:28:20 hexin $ 
 * @package
 */
class FamilyExtendModel extends PipiActiveRecord {
	/**
	 * 
	 * @param string $className
	 * @return FamilyExtendModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{family_extend}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_family;
	}
	
	public function rules(){
		return array(
			array('announcement', 'length', 'min' => 2, 'max' => 250, 'message'=>'250字以内'),
		);
	}
	
	public function attributeLabels(){
		return array(
			'announcement'	=> '公告内容'
		);
	}
	
	/**
	 * 删除家族扩展信息
	 * @param int $family_id
	 * @return boolean
	 */
	public function deleteExtend($family_id){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->condition = 'family_id='.$family_id;
		if($this->getCommandBuilder()->createDeleteCommand($this->tableName(), $criteria)->execute())
			return true;
		else return false;
	}
}
