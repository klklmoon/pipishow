<?php
/**
 * 皮皮乐天数据库操作命令生成器基类
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <aoxue.1988.su.qian@163.com>
 * @version $Id: PipiDbCommandBuilder.php 8317 2013-03-29 01:19:47Z suqian $ 
 * @package 
 */
class PipiDbCommandBuilder extends CDbCommandBuilder {

	protected $_connection;
	
	public function __construct($schema){
		parent::__construct($schema);
		$this->_connection = $this->getDbConnection();
	}
	
	/**
	 * 创建批量添加
	 * @param mixed $table 表名
	 * @param array $data 要添加的多条数据
	 * @param boolean true表示Insert，false表示Replace
	 * @example ($table ,array('linkname'=>array(':linkname'=>array('suqian','xiaoyu')),'weburl'=>array(':weburl'=>array('23','22'))),'insert');
	 * @return CDbCommand insert command
	 */
	public function createBatchInsertCommand($table,$data,$type = true)
	{
		$this->ensureTable($table);
		$fields=array();
		$rows = array();
		$placeholders=array();
		$i=0;
		foreach($data as $name=>$value)
		{
			if(($column=$table->getColumn($name))!==null && ($value!==null || $column->allowNull))
			{
				$fields[]=$column->rawName;
				foreach($value as $_key => $_value){
					$placeholders[] = $placholder = is_string($_key) ? $_key : self::PARAM_PREFIX.$i; 
					foreach ($_value as $_row=>$_columnValue){
						$rows[$_row][$placholder] = $column->typecast($_columnValue);
					}
					$i++;
				}
			}
		}
		if($fields===array())
		{
			$pks=is_array($table->primaryKey) ? $table->primaryKey : array($table->primaryKey);
			foreach($pks as $pk)
			{
				$fields[]=$table->getColumn($pk)->rawName;
				$placeholders[]='NULL';
			}
		}
		$sql= ($type ? 'INSERT' : 'REPLACE')." INTO {$table->rawName} (".implode(', ',$fields).') VALUES ('.implode(', ',$placeholders).')';
		$command=$this->_connection->createCommand($sql);
		foreach($rows as $row){
			foreach($row as $name=>$value)
				$command->bindValue($name, $value);
			$command->execute();
		}
		return $command;
	}
}

