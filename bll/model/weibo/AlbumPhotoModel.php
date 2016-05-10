<?php
/**
 * @author zhangzhifan <zhangzhifan@pipi.cn> 2010-11-2
 * @link http://show.pipi.cn
 * @copyright Copyright &copy; 2003-2010 show.pipi.cn
 * @license
 */
class AlbumPhotoModel extends PipiActiveRecord {
	public function tableName(){
		return '{{album_photo}}';
	}
	
	/**
	 * @param string $className
	 * @return MessageConfigModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function getDbConnection(){
		return Yii::app()->db_weibo;
	}
	
	/**
	 * 用户获取可分页的照片列表
	 *
	 * @param int $uid	用户id
	 * @param int $offset 页起始位置
	 * @param int $pageSize 页记录数
	 * @return array 照片列表,list为数据
	 */
	public function getAlbumByUser($uid,$offset=0, $pageSize=10)
	{
		$result=array();
		$result['count'] = 0;
		$result['list'] = array();
		
		$criteria = $this->getDbCriteria();
		$criteria->compare('uid', $uid);

		$result['count'] = $this->count($criteria);
		
		$criteria->limit=$pageSize;
		$criteria->offset = $offset;
		$criteria->order = 'create_time desc';
		
		$result['list'] = $this->findAll($criteria);
		return $result;
	}
}