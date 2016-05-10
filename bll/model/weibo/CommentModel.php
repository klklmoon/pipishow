<?php
/**
 * 评论访问层
 * 
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: CommentModel.php 17408 2014-01-14 07:22:52Z hexin $ 
 * @package model
 * @subpackage weibo
 */
class CommentModel extends PipiActiveRecord {

	public function tableName(){
		return '{{comment}}';
	}
	
	/**
	 * @param string $className
	 * @return CommentModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function getDbConnection(){
		return Yii::app()->db_weibo;
	}
	
	public function getCountByDynamic($target_ids, $source = COMMENT_TYPE_DOTEY){
		$cr = $this->getCommandBuilder()->createCriteria();
		$cr -> select = 'target_id, source, count(*) as count';
		$cr -> condition = "target_id in (".implode(',', $target_ids).")".(empty($source) ? "": " and source = '".$source."'");
		$cr -> group = 'target_id,source';
		return $this->getCommandBuilder()->createFindCommand($this->tableName(), $cr)->queryAll();
	}
}

