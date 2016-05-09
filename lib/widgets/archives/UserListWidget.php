<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: lei wei $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z lei wei  $ 
 * @package 
 */
class UserListWidget extends PipiWidget {
	
	public $uid;
	
	public $flashPath;        //路径
	
	public $archives_id;     //档期Id
	
	public $userList=false;  //用户列表
	
	
	public function init(){
		/* @var $contrller PipiController */
		$contrller = Yii::app()->getController();
		/* @var $clientScript CClientScript */
		$clientScript = Yii::app()->getClientScript();
		$this->flashPath = $contrller->pipiFrontPath;
		$clientScript->registerScriptFile($this->flashPath.'/js/archives/userlist.js?token='.$contrller->hash,CClientScript::POS_END);
	}
	
	
	public function run(){
		$this->render('userList');
	}
	
	/**
	 * 获取贴条列表
	 *
	 * @return array 返回标签列表
	 *
	 */
	private function getLabelList() {
		$propsService = new PropsService();
		$category = $propsService->getPropsCategoryByEnName('label');
		// 获取贴条下的分类
		$attrList = $propsService->getPropsCatAttrtByIds($category['cat_id'], 1);
		foreach ($attrList as $row) {
			if ($row['attr_enname'] == 'label_category') {
				$labelCatList = $row['list'];
			}
		}
		unset($attrList);
	
		$propsList = $propsService->getPropsByCatId($category['cat_id'], false, true);
	
		foreach ($propsList as $key => $row) {
			$attribute = $propsService->buildDataByIndex($row['attribute'], 'attr_enname');
			$props[$key]['prop_id'] = $row['prop_id'];
			$props[$key]['cat_id'] = $row['cat_id'];
			$props[$key]['category_id'] = $attribute['label_category']['value'];
			$props[$key]['category'] = $labelCatList[$attribute['label_category']['value']];
			$props[$key]['picture'] = $attribute['label_picture']['value'];
			$props[$key]['timeout'] = $attribute['label_timeout']['value'];
			$props[$key]['name'] = $row['name'];
			$props[$key]['en_name'] = $row['en_name'];
			$props[$key]['pipiegg'] = $row['pipiegg'];
			$props[$key]['image'] = $row['image'];
		}
	
		unset($propsList);
		foreach ($props as $key => $row) {
			if ($row['category'] == $labelCatList[$row['category_id']]) {
				$labelProps[$row['category_id']]['category'] = $row['category'];
				$labelProps[$row['category_id']]['list'][$key] = $row;
			}
		}
		return $labelProps;
	}
	
}

?>