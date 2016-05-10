<?php
/**
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: PropsServiceTest.php 9334 2013-04-30 06:38:56Z hexin $ 
 * @package
 */
class PropsServiceTest extends BaseTest {
	/* var PropsService $propsService */
	protected $propsService;
	private static $cat_id;
	private static $attr_id;
	private static $props_id;
	private static $pattr_id;
	
	public function __construct(){
		$this->propsService = new PropsService();
	}
	
	/**
	 * @medium
	 */
	public function testSavePropsCategory(){
		$tmp_name = 'c-'.uniqid();
		$cat = array(
			'name'		=> $tmp_name.'_分类测试',
			'en_name'	=> $tmp_name,
		);
		$a_id = $this->propsService->savePropsCategory($cat);
		if($this->propsService->getNotice()){
			$this->fail(var_export($this->propsService->getNotice(), true));
		}
		$this->assertTrue($a_id > 1, '新增道具分类测试不通过');
		
		$category = $this->propsService->getPropsCatByIds($a_id);
		$category = array_pop($category);
		$this->assertTrue($cat['name'] == $category['name'] && $cat['en_name'] == $category['en_name'], '根具分类ID取得道具分类信息测试不通过');
		
		$category['en_name'] .= '-test';
		$e_id = $this->propsService->savePropsCategory($category);
		if($this->propsService->getNotice()){
			$this->fail(var_export($this->propsService->getNotice(), true));
		}
		$cat_tmp = $this->propsService->getPropsCatByIds($e_id);
		$cat_tmp = array_pop($cat_tmp);
		$this->assertTrue($e_id == $a_id && $cat_tmp['en_name'] == $category['en_name'], '修改道具分类测试不通过');
		return $e_id;
	}
	
	/**
	 * @depends testSavePropsCategory
	 * @medium
	 */
	public function testDelPropsCatgoryByIds($id){
		$r = $this->propsService->delPropsCatgoryByIds($id);
		$category = $this->propsService->getPropsCatByIds($id);
		$this->assertTrue($r == true && empty($category), '删除道具分类测试不通过');
		
		$tmp_name = 'c-'.uniqid();
		$cat = array(
			'name'		=> $tmp_name.'_分类测试',
			'en_name'	=> $tmp_name,
		);
		$id = $this->propsService->savePropsCategory($cat);
		self::$cat_id = $id;
	}
	
	public function testSavePropsCatAttribute(){
		$tmp_name = 'a-'.uniqid();
		$attr = array(
			'cat_id'	=> self::$cat_id,
			'attr_name'	=> $tmp_name."_测试分类属性",
			'attr_enname'=> "key",
			'attr_value'=> 'value',
		);
		$a_id = $this->propsService->savePropsCatAttribute($attr);
		if($this->propsService->getNotice()){
			$this->fail(var_export($this->propsService->getNotice(), true));
		}
		$this->assertTrue($a_id > 0, '添加分类属性测试不通过');
		
		$attr_tmp = $this->propsService->getPropsCatAttrtByIds($a_id);
		$attr_tmp = array_pop($attr_tmp);
		$this->assertTrue($attr_tmp['attr_name'] == $attr['attr_name'], '根具分类属性ID取得道具分类属性信息测试不通过');
		
		$attr_tmp['attr_value'] = 'test';
		$e_id = $this->propsService->savePropsCatAttribute($attr_tmp);
		if($this->propsService->getNotice()){
			$this->fail(var_export($this->propsService->getNotice(), true));
		}
		$attr = $this->propsService->getPropsCatAttrtByIds($e_id);
		$attr = array_pop($attr);
		$this->assertTrue($a_id == $e_id && $attr_tmp['attr_value'] == $attr['attr_value'], '修改分类属性测试不通过');
		return $e_id;
	}
	
	/**
	 * @depends testSavePropsCatAttribute
	 */
	public function testDelPropsCatAttribute($id){
		$r = $this->propsService->delPropsCatAttribute($id);
		$attr = $this->propsService->getPropsCatAttrtByIds($id);
		$this->assertTrue($r == true && empty($attr), '删除分类属性测试不通过');
		
		$tmp_name = 'a-'.uniqid();
		$attr = array(
			'cat_id'	=> self::$cat_id,
			'attr_name'	=> $tmp_name."_测试分类属性",
			'attr_enname'=> "key",
			'attr_value'=> 'value',
		);
		$id = $this->propsService->savePropsCatAttribute($attr);
		self::$attr_id = $id;
	}
	
	public function testSaveProps(){
		$tmp_name = 'p-'.uniqid();
		$props = array(
			'cat_id'	=> self::$cat_id,
			'name'		=> $tmp_name."测试道具",
			'en_name'	=> $tmp_name,
			'pipiegg'	=> 1,
			'charm'		=> 1,
			'charm_points'=> 1,
			'dedication'=> 1,
			'egg_points'=> 1,
			'status'	=> 0,
		);
		$a_id = $this->propsService->saveProps($props);
		if($this->propsService->getNotice()){
			$this->fail(var_export($this->propsService->getNotice(), true));
		}
		$this->assertTrue($a_id > 0, '添加道具测试不通过');
		
		$props_tmp = $this->propsService->getPropsByIds($a_id);
		$props_tmp = array_pop($props_tmp);
		$this->assertTrue($props_tmp['name'] == $props['name'] && $props_tmp['en_name'] == $props['en_name'], '取得道具信息测试不通过');
		
		$props_tmp['en_name'] .= '_test';
		$e_id = $this->propsService->saveProps($props_tmp);
		if($this->propsService->getNotice()){
			$this->fail(var_export($this->propsService->getNotice(), true));
		}
		$props = $this->propsService->getPropsByIds($e_id);
		$props = array_pop($props);
		$this->assertTrue($a_id == $e_id && $props_tmp['en_name'] == $props['en_name'], '修改道具测试不通过');
		return $e_id;
	}
	
	/**
	 * @depends testSaveProps
	 */
	public function testDelPropsByIds($id){
		$r = $this->propsService->delPropsByIds($id);
		$props = $this->propsService->getPropsByIds($id);
		$this->assertTrue($r == true && empty($props), '删除道具测试不通过');
	
		$tmp_name = 'p-'.uniqid();
		$props = array(
			'cat_id'	=> self::$cat_id,
			'name'		=> $tmp_name."测试道具",
			'en_name'	=> $tmp_name,
			'pipiegg'	=> 1,
			'charm'		=> 1,
			'charm_points'=> 1,
			'dedication'=> 1,
			'egg_points'=> 1,
			'status'	=> 0,
		);
		$id = $this->propsService->saveProps($props);
		self::$props_id = $id;
	}
	
	public function testSavePropsAttribute(){
		$attr = array(self::$attr_id => 1);
		$r = $this->propsService->savePropsAttribute(self::$props_id, $attr);
		$attrs = $this->propsService->getPropsAttributeByPropIds(self::$props_id);
		$attr_tmp = array_pop($attrs);
		$this->assertTrue($r == true && $attr_tmp['attr_id'] == self::$attr_id && $attr_tmp['value'] == 1, '批量创建道具属性测试及根据道具ID取得道具属性测试不通过');
	
		$attr = array(self::$attr_id => 2);
		$r = $this->propsService->savePropsAttribute(self::$props_id, $attr);
		$attrs = $this->propsService->getPropsAttributeByPropIds(self::$props_id);
		$attr_tmp1 = array_pop($attrs);
		$this->assertTrue($r == true && $attr_tmp1['attr_id'] == self::$attr_id && $attr_tmp1['value'] == 2, '批量更新道具属性测试不通过');
		
		$this->propsService->delPropsAttribute($attr_tmp1['pattr_id'], 2);
	}
	
	public function testSaveSinglePropsAttribute(){
		$attr = array(
			'prop_id'	=> self::$props_id,
			'attr_id'	=> self::$attr_id,
			'value'		=> 1,
		);
		$a_id = $this->propsService->saveSinglePropsAttribute($attr);
		if($this->propsService->getNotice()){
			$this->fail(var_export($this->propsService->getNotice(), true));
		}
		$this->assertTrue($a_id > 0, '添加道具属性测试不通过');
		
		$attr_tmp = $this->propsService->getPropsAttributeByPropIds(self::$props_id);
		$attr_tmp = array_pop($attr_tmp);
		$this->assertTrue($attr_tmp['attr_id'] == $attr['attr_id'], '取得道具属性信息测试不通过');
		
		foreach($attr_tmp as $key=>$val){
			if(!in_array($key, array('pattr_id','prop_id','attr_id','value'))){
				unset($attr_tmp[$key]);
			}
		}
		$attr_tmp['value'] = 2;
		$e_id = $this->propsService->saveSinglePropsAttribute($attr_tmp);
		if($this->propsService->getNotice()){
			$this->fail(var_export($this->propsService->getNotice(), true));
		}
		$attr = $this->propsService->getPropsAttributeByPropIds(self::$props_id);
		$attr = array_pop($attr);
		$this->assertTrue($a_id == $e_id && $attr_tmp['value'] == $attr['value'], '修改道具属性测试不通过');
		return $e_id;
	}
	
	/**
	 * @depends testSaveSinglePropsAttribute
	 */
	public function testDelPropsAttribute($id){
		$r = $this->propsService->delPropsAttribute($id, 2);
		$attr = $this->propsService->getPropsAttributeByPropIds(self::$props_id);
		$this->assertTrue($r == true && empty($attr), '删除道具属性测试不通过');
		
		$attr = array(
			'prop_id'	=> self::$props_id,
			'attr_id'	=> self::$attr_id,
			'value'		=> 1,
		);
		$id = $this->propsService->saveSinglePropsAttribute($attr);
		self::$pattr_id = $id;
	}
	
	public function testSavePropsConfig(){
		$config = array(
			'prop_id'	=> self::$props_id,
			'prop_category'=> self::$cat_id,
			'config'	=> array('a'=>'1', 'b'=>'1'),
		);
		$a_id = $this->propsService->savePropsConfig($config);
		if($this->propsService->getNotice()){
			$this->fail(var_export($this->propsService->getNotice(), true));
		}
		$this->assertTrue($a_id > 0, '添加道具配置信息测试不通过');
		
		$config_tmp = $this->propsService->getPropsConfigByCategoryOrName(self::$cat_id);
		$this->assertSame($config_tmp['config'], $config['config'], '取得道具配置信息测试不通过');
		
		$config_tmp['config'] = array('a'=>'2', 'b'=>'2');
		$e_id = $this->propsService->savePropsConfig($config_tmp);
		if($this->propsService->getNotice()){
			$this->fail(var_export($this->propsService->getNotice(), true));
		}
		$config = $this->propsService->getPropsConfigByCategoryOrName(self::$cat_id);
		$this->assertTrue($a_id == $e_id);
		$this->assertSame($config_tmp['config'], $config['config'], '修改道具配置信息测试不通过');
		return $e_id;
	}
	
	public function testGetPropsByCatId(){
		$props = $this->propsService->getPropsByCatId(self::$cat_id, true, true);
		$prop = array_pop($props);
		$this->assertTrue($prop['prop_id'] == self::$props_id && $prop['category']['cat_id'] == self::$cat_id && $prop['attribute']['attr_id'] == self::$attr_id, '按分类取得可用道具信息测试不通过');
	}
	
	public function testGetPropsCategoryByEnName(){
		$cats = $this->propsService->getPropsCatByIds(self::$cat_id, true);
		$cat = array_pop($cats);
		$tmp = $this->propsService->getPropsCategoryByEnName($cat['en_name']);
		$this->assertTrue($tmp['cat_id'] == self::$cat_id, '按分类取得可用道具信息测试不通过');
	}
	
	public function testGetPropsCatList(){
		$cats = $this->propsService->getPropsCatList();
		$cat = array_pop($cats);
		$this->assertTrue($cat['cat_id'] == self::$cat_id, '返回道具分类列表测试不通过');
	}
	
	public function testGetPropsCatByIds(){
		$cats = $this->propsService->getPropsCatByIds(self::$cat_id, true);
		$cat = array_pop($cats);
		$this->assertTrue($cat['cat_id'] == self::$cat_id && $cat['attribute']['attr_id'] == self::$attr_id, '根具分类ID取得道具分类信息测试不通过');
	}
}

