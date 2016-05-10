<?php

/**
 * 用户道具分类属性基本信息之属性数据访问层
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: supeng $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: PropsCatAttributeModel.php 9114 2013-04-24 08:50:45Z supeng $ 
 * @package model
 * @subpackage props
 */
class PropsCatAttributeModel extends PipiActiveRecord {
	
	public $cat_id;
	public $is_display;
	public $attr_type;
	
	public function tableName(){
		return '{{props_cat_attribute}}';
	}
	
	/**
	 * @param string $className
	 * @return PropsCatAttributeModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function getDbConnection(){
		return Yii::app()->db_consume;
	}
	
	
	public function rules(){
		return array(
			array('attr_name,attr_enname','filter','filter'=>array(new CHtmlPurifier(),'purify')),
			array('attr_name,attr_enname,cat_id','required'),
			array('attr_name,attr_enname','unique'),
		);
	}
	
	public function attributeLabels(){
		return array(
			'attr_id'	=> '属性ID',
			'cat_id'	=> '道具分类',
			'attr_name'	=> '属性名称',
			'attr_enname'=> '属性标识',
			'is_display'=> '是否显示',
			'attr_value'=> '属性值',
			'attr_type'  => '属性类型',
			'is_multi'  => '是否多选',
			'create_time'  => '创建时间',
		);
	}
	
	/**
	 * 根具分类属性ID取得道具分类属性信息
	 * 
	 * @param array $ids 道具分类属性ID
	 * @param int $type 0表示按属性ID，1表示按分类ID
	 * @return array 返回道具信息
	 */
	public function getPropsCatAttrtByIds(array $ids,$type = 0){
		if(empty($ids)){
			return array();
		}
		$field = $type ? 'cat_id' : 'attr_id';
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition($field,$ids);
		return $this->findAll($criteria);
	}
	
	/**
	 * 删除道具分类属性
	 * 
	 * @param array $ids 删除道具
	 * @param int $type 类型  0表示属性标识，1表示按分类标识
	 * @return int
	 */
	public function delPropsCatAttributeByIds(array $ids,$type = 0){
		if(empty($ids)){
			return array();
		}
		$field = $type ? 'cat_id' : 'attr_id';
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition($field,$ids);
		return $this->deleteAll($criteria);
	}
	
	/**
	 * 分类属性搜索
	 *
	 * @author supeng
	 * @return CActiveDataProvider
	 */
	public function search()
	{
		$criteria=$this->getDbCriteria();
		$criteria->compare('cat_id',$this->cat_id);
		$criteria->compare('is_display',$this->is_display);
		$criteria->compare('attr_type',$this->attr_type);
	
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination' => array(
					'pageSize' => 20,
				)
		));
	}
}

?>