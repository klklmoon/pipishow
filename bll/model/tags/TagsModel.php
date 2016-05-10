<?php
/**
 * 主播印象标签
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z suqian $ 
 * @package
 */
class TagsModel extends PipiActiveRecord {
	/**
	 * @param unknown_type $className
	 * @return TagsModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{tags}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_user;
	}
	
	public function rules(){
		return array(
			array('tag_name','filter','filter'=>array(new CHtmlPurifier(),'purify')),
		);
	}
	
	
}

