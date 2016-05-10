<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: lei wei $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: ArchivesCategoryModel.php 11283 2013-05-30 09:57:21Z lei wei $ 
 * @package model
 * @subpackage archives
 */
class ArchivesBackgroundModel extends PipiActiveRecord {
	/**
	 * @param unknown_type $className
	 * @return ArchivesBackgroundModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{archives_background}}';
	}
	
	
	public function getDbConnection(){
		return Yii::app()->db_archives;
	}
	
	public function rules(){
		return array(
			array('title','filter','filter'=>array(new CHtmlPurifier(),'purify')),
			array('title,small,big,bgcolor','required'),
		);
	}
	
	public function attributeLabels(){
		return array(
			'title'=>'直播间背景中文名称',
			'small'=>'直播间背景缩略图',
			'big'=>'直播间背景图',
			'bgcolor'=>'直播间背景颜色'
		);
	}
	
	/**
	 * 获取直播间背景图片
	 * @return array
	 */
	public function getArchivesBackground(){
		$criteria = $this->getDbCriteria();
		return $this->findAll($criteria);
	}
}

?>