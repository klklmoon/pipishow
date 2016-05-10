<?php

/**
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <aoxue.1988.su.qian@163.com>
 * @version $Id: IndustryService.php 8317 2013-03-29 01:19:47Z suqian $ 
 * @package 
 */
class IndustryService extends PipiService {

	/**
	 * 添加行业分类
	 * @param array $category
	 * @return number
	 */
	public function addIndustry(array $industreies) {
		if (!isset($industreies['industryname'])) 
			return $this->setError(Yii::t('ehr_common', 'Parameter is empty'), false);
		
		$industryDao = $this->getIndustryDao();
		$this->setIndustriesToCache();
		$industry = $industryDao->findByAttributes(array('industryname' => $industreies['industryname']));
		if ($industry->industryname) 
			return $this->setError(Yii::t('ehr_common', 'Data already exists'), false);
		
		$industryDao->parentid = (int) $industreies['parentid'];
		$industryDao->sort = (int) $industreies['sort'];
		$industryDao->industryname = $industreies['industryname'];
		return $industryDao->save() ? $this->setIndustriesToCache() : $this->setError($industryDao->getErrors(), false);
	}

	/**
	 * 编辑分类
	 * @param array $category
	 * @return boolean
	 */
	public function editIndustry($pk, array $industriy) {
		if (0 > ($pk = (int) $pk) || !isset($industriy['industryname'])) 
			return $this->setError(Yii::t('ehr_common', 'Parameter is empty'), false);
		
		$industryDao = $this->getIndustryDao();
		$result = $industryDao->updateByPk($pk, $industriy);
		$result && $this->setIndustriesToCache();
		return $result ? true : false;
	}

	/**
	 * 取得所有以树形方式表式的分类信息
	 * @return array
	 */
	public function getTreeIndustries() {
		$industryDao = $this->getIndustryDao();
		if (!($industries = $this->getIndustriesFromCache())) {
			if (!($industries = $this->setIndustriesToCache())) 
				return $this->buildIndustries($industryDao->getAreas(0, 0));
			return $industries;
		}
		return $industries;
	}

	/**
	 * 取得普通分类列表
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	public function getIndustries($limit = 0, $offset = 0) {
		if (0 > ($limit = (int) $limit) || 0 > ($offset = (int) $offset)) 
			return $this->setError(Yii::t('ehr_common', 'Parameter is empty'), array());
		$industryDao = $this->getIndustryDao();
		return $industryDao->getAreas($limit, $offset);
	}

	/**
	 * 从文件缓存中获取子类
	 * @param int $parentId
	 * @param array $Industries
	 * @return array
	 */
	public function getTreeIndustriesByParentId($parentId, array $industries = array()) {
		if (0 > ($parentId = (int) $parentId))
			return $this->setError(Yii::t('ehr_common', 'Parameter is empty'), false);
			
		static $_industries = array();
		if (empty($industries)) 
			$industries = $this->getTreeIndustries();
			
		foreach ($industries as $key => $value) {
			if ($parentId == $value['parentid']) {
				$_industries[$key] = $value;
			} else {
				$value['child'] && $this->getTreeIndustriesByParentId($parentId, $value['child']);
			}
		}
		return $_industries;
	}

	/**
	 * 删除相关分类信息
	 * @param mixed $ids
	 * @return boolean
	 */
	public function delIndustriesByIds($ids) {
		if (empty($ids))
			return $this->setError(Yii::t('ehr_common', 'Parameter is empty'), array());
			
		$ids = is_array($ids) ? $ids : array((int) $ids);
		$industryDao = $this->getIndustryDao();
		if (!($industryDao->delAreasByIds($ids))) 
			return array();
		return $this->setIndustriesToCache();
	}

	/**
	 * 删除子分类
	 * @param mixed $parentId
	 * @return boolean
	 */
	public function delIndustriesByParentIds($parentIds) {
		if (empty($parentIds)) 
			return $this->setError(Yii::t('ehr_common', 'Parameter is empty'), array());
			
		$parentIds = is_array($parentIds) ? $parentIds : array((int) $parentIds);
		$industryDao = $this->getIndustryDao();
		if (( $industryDao->delIndustriesByParentIds($parentIds))) 
			return array();
		return $this->setIndustriesToCache();
	}

	/**
	 * 重建分类数据，以树形结构表示，以便写入文件缓存
	 * @param array $Industries
	 * @return array
	 */
	protected function buildIndustries(array $industries) {
		$_industries = array();
		foreach ($industries as $key => $value) {
			if (!$value['parentid']) {
				$_industries[$value['industryid']]['industryname'] = $value['industryname'];
				$_industries[$value['industryid']]['parentid'] = $value['parentid'];
				$_industries[$value['industryid']]['sort'] = $value['sort'];
				$_industries[$value['industryid']]['ifjob'] = $value['ifjob'];
				$_industries[$value['industryid']]['child'] = array();
				unset($industries[$key]);
			}
			$this->addChild($_industries, $value);
		}
		return $_industries;
	}

	/**
	 * 向行业树形分类添加子分类
	 * @param array $Industries
	 * @param array $child
	 */
	protected function addChild(array &$industries, array $child) {
		foreach ($industries as $key => $value) {
			if ($key == $child['parentid']) {
				$industries[$key]['child'][$child['industryid']]['industryname'] = $child['industryname'];
				$industries[$key]['child'][$child['industryid']]['parentid'] = $child['parentid'];
				$industries[$key]['child'][$child['industryid']]['sort'] = $child['sort'];
				$industries[$key]['child'][$child['industryid']]['ifjob'] = $child['ifjob'];
				$industries[$key]['child'][$child['industryid']]['child'] = array();
			}
			$this->addChild($industries[$key]['child'], $child);
		}
	}

	/**
	 * 从文件缓存获取分类信息
	 * @return array
	 */
	protected function getIndustriesFromCache() {
		return require Yii::getPathOfAlias('data.caches.categories') . '/category_industry.php';
	}

	/**
	 * 设置缓存
	 */
	protected function setIndustriesToCache() {
		$industryDao = $this->getIndustryDao();
		$industries = $this->arToArray($industryDao->getAreas(0, 0));
		$industries = $this->buildIndustries($industries);
		EetopFile::phpData('category_industry', $industries);
		return $industries;
	}

	/**
	 * 取得行业分类的model
	 * @return IndustryDao
	 */
	protected function getIndustryDao() {
		return Yii::getDao('Industry', 'cate');
	}
}

