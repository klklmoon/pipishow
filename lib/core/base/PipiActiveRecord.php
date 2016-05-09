<?php
/**
 * 皮皮乐天数据访问层基类，所有应用数据访问层的基类
 *
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su Qian <aoxue.1988.su.qian@163.com>
 * @version $Id: PipiActiveRecord.php 9393 2013-05-01 01:49:28Z suqian $
 * @package 
 */
class PipiActiveRecord extends CActiveRecord{
	
	/**
	 * @var CDbCommand
	 */
	private $dbCommand = null;
	
	/**
	 * 取得命令组装器
	 * @return CDbCommand
	 */
	public function getDbCommand(){
		if(!$this->dbCommand instanceof CDbCommand)
			 $this->dbCommand = $this->getDbConnection()->createCommand();
		return $this->dbCommand;
	}
	/**
	 * 
	 * @param boolean $ifClear 是否清空原有的值
	 * @param boolean $createIfNull
	 * @return CDbCriteria
	 */
	public function getDbCriteria($ifClear = true,$createIfNull=true){
		$criteria = parent::getDbCriteria($createIfNull);
		if($ifClear){
			$criteria->alias = null;
			$criteria->together = null;
			$criteria->with = null;
			$criteria->scopes = array();
			$criteria->condition = '';
			$criteria->order = '';
			$criteria->group = '';
			$criteria->join = '';
			$criteria->having = '';
			$criteria->select = '*';
			$criteria->distinct = false;
			$criteria->limit = -1;
			$criteria->offset = -1;
			$criteria->params = array();
		}
		return $criteria;
	}
	/* 
	 * @see CActiveRecord::getCommandBuilder()
	 * @return PipiDbCommandBuilder
	 */
	public function getCommandBuilder(){
		return new PipiDbCommandBuilder($this->getDbConnection()->getSchema());
	}
	
	/**
	 * 批量添加
	 * @param array $data
	 * @param array $type
	 * @throws CDbException
	 * @return boolean
	 */
	public function batchInsert(array $data,$type = true){
		
		if($this->beforeSave())
		{
			Yii::trace(get_class($this).'.batchInsert()','lib.core.EActiveRecord');
			$builder=$this->getCommandBuilder();
			$table=$this->getMetaData()->tableSchema;
			$data = $this->convertToBatch($data);
			$command=$builder->createBatchInsertCommand($table,$data,$type);
			
			$primaryKey=$table->primaryKey;
			if($table->sequenceName!==null)
			{
				if(is_string($primaryKey) && $this->$primaryKey===null)
					$this->$primaryKey=$builder->getLastInsertID($table);
				else if(is_array($primaryKey))
				{
					foreach($primaryKey as $pk)
					{
						if($this->$pk===null)
						{
							$this->$pk=$builder->getLastInsertID($table);
							break;
						}
					}
				}
			}
			$this->_pk=$this->getPrimaryKey();
			$this->afterSave();
			$this->setScenario('update');
			return true;
		}
		return false;
	}
	/**
	 * 加入回收站
	 * @param mixed $ids
	 */
	public function isdelByPk($ids){
		$this->updateByPk($ids,array('isdeleted'=>'1'));
	}
	
	/**
	 * 转换成批量添加的数据
	 * 
	 * @param array $orginalData
	 * @param array $realField
	 * @return array
	 */
	public function convertToBatch(array $orginalData,array $realField = array()){
		$_convertData = array();
		foreach($orginalData as $_okey=>$_oData){
			$_oData = $_oData instanceof  EActiveRecord ? $_oData->attributes : $_oData;
			foreach($_oData as  $_key=> $_data){
				if(empty($realField) || in_array($_key,$realField))
					    $_convertData[$_key][$_okey] = $_data;
			}
			
		}
		foreach($_convertData as $key=>$data)
			$_convertData[$key] = array($data);
		return $_convertData;
	}
	
}

