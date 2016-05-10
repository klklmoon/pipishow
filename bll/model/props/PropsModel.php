<?php

/**
 * 用户道具基本信息数据访问层
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: supeng $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: PropsModel.php 9137 2013-04-24 14:20:32Z supeng $ 
 * @package model
 * @subpackage props 
 */
class PropsModel extends PipiActiveRecord {
	
	public $cat_id;
	public $rank;

	public function tableName(){
		return '{{props}}';
	}
	
	/**
	 * @param string $className
	 * @return PropsModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function rules(){
		return array(
			array('name,en_name','filter','filter'=>array(new CHtmlPurifier(),'purify')),
			array('name,en_name,cat_id','required'),
			array('name,en_name','unique'),
		);
	}
	
	public function attributeLabels(){
		return array(
			'prop_id' 	=> 	'道具ID',
			'name'		=>	'道具名称',
			'en_name'	=>	'道具标识',
			'cat_id'	=>	'道具分类',
			'pipiegg'	=>	'价格',
			'image'		=>  '图片',
			'charm'		=>  '魅力值',
			'charm_points' => '魅力点',
			'status' 	=> 	'状态',
			'rank' 		=>	'获取等级',
			'dedication'=>	'贡献值',
			'egg_points'=> 	'皮点',
			'sort'		=> 	'排序',
			'create_time' => '创建时间'
		);
	}
	
	
    public function getDbConnection(){
		return Yii::app()->db_consume;
	}
	
	/**
	 * 按分类和道具名称取得道具信息
	 * 
	 * @param int $cat_id  道具ＩＤ
	 * @param string $name 道具名称
	 * @return PipiActiveRecord
	 */
	public function getPropsByCatIdAndName($cat_id,$name){
		if(empty($name) || empty($cat_id)){
			return array();
		}
		return $this->find('cat_id=:cat_id AND attr_name = :name',array(':cat_id'=>$cat_id,':name'=>$name));
	}
	
	/**
	 * 取得道取信息
	 * 
	 * @param array $ids　道具ＩＤ
	 * @return array
	 */
	public function getPropsByIds(array $ids){
		if(empty($ids)){
			return array();
		}
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('prop_id',$ids);
		return $this->findAll($criteria);
	}
	
	/**
	 * 删除道具
	 * 
	 * @param array $ids 删除道具
	 * @param int $type 类型  0表示道具标识，1表示按分类标识
	 * @return int
	 */
	public function delPropsByIds(array $ids,$type = 0){
		if(empty($ids)){
			return array();
		}
		$field = $type ? 'cat_id' : 'prop_id';
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition($field,$ids);
		return $this->deleteAll($criteria);
	}
	
	/**
	 * 道具搜索
	 * 
	 * @return CActiveDataProvider
	 */
	public function search(){
		$criteria = $this->getDbCriteria();
		$criteria->compare('cat_id', $this->cat_id);
		$criteria->compare('rank', $this->rank);
		
		return new CActiveDataProvider($this,array(
				'criteria' => $criteria,
				'pagination' => array(
						'pageSize' => 20
					)
			));
	}
}

?>