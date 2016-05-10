<?php
/**
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: PurviewServiceTest.php 9417 2013-05-01 07:02:56Z hexin $ 
 * @package
 */
class PurviewServiceTest extends BaseTest {
	protected $purviewService;
	private static $item_id;
	private static $role_id;
	private $op_role_id = 1;
	
	public function __construct(){
		$this->purviewService = new PurviewService();
	}
	
	/**
	 * @test
	 * @medium
	 */
	public function saveItem(){
		$this->getNewUser();
		Yii::app()->user->setId(self::$uid);
		$item = array(
			'purview_name'	=> 'i_'.uniqid(),
			'group'			=> '全局配置',
			'module'		=> 'admin',
			'controller'	=> 'Admin',
			'action'		=> 'purview',
			'is_tree_display'=> 1,
			'range'			=> array(1,2,3,4),
		);
		$a_id = $this->purviewService->saveItem($item);
		if($this->purviewService->getNotice()){
			$this->fail(var_export($this->purviewService->getNotice(), true));
		}
		$tmp = $this->purviewService->getItem($a_id);
		$this->assertTrue($a_id > 0 && $item['purview_name'] == $tmp['purview_name'], '新增权限项测试不通过');
		
		$item['purview_id'] = $a_id;
		$item['action']		= 'test';
		$e_id = $this->purviewService->saveItem($item);
		if($this->purviewService->getNotice()){
			$this->fail(var_export($this->purviewService->getNotice(), true));
		}
		$tmp = $this->purviewService->getItems(array($e_id));
		$tmp = array_pop($tmp);
		$this->assertTrue($a_id == $e_id && $item['action'] == $tmp['action'], '修改权限项测试不通过');
		
		$record = $this->getLastRecord();
		$this->assertTrue($record['op_uid'] == self::$uid && $record['purview_id'] == $a_id, '保存操作记录测试不通过');
		return $e_id;
	}
	
	/**
	 * @test
	 * @depends saveItem
	 * @medium
	 */
	public function deleteItem($id){
		$this->getNewUser();
		$r = $this->purviewService->deleteItem($id);
		$item = PurviewItemModel::model()->findByPk($id);
		$item = $item->getAttributes();
		$this->assertTrue($r == true && $item['is_use'] == 0, '删除权限项测试不通过');
		
		self::$item_id = $id;
		$item['is_use'] = 1;
		$this->purviewService->saveItem($item);
		
		$record = $this->getLastRecord();
		$this->assertTrue($record['op_uid'] == self::$uid && $record['purview_id'] == self::$item_id, '保存操作记录测试不通过');
	}
	
	/**
	 * @test
	 * @medium
	 */
	public function saveRole(){
		$this->getNewUser();
		$role = array(
			'role_name'		=> 'r_'.uniqid(),
			'role_type'		=> 1,
			'sub_id'		=> '-1',
			'description'	=> '后台管理员'
		);
		$a_id = $this->purviewService->saveRole($role, self::$uid, $this->op_role_id);
		if($this->purviewService->getNotice()){
			$this->fail(var_export($this->purviewService->getNotice(), true));
		}
		$tmp = $this->purviewService->getRole($a_id);
		$this->assertTrue($a_id > 0 && $role['role_name'] == $tmp['role_name'], '新增角色测试不通过');
		
		$role['role_id'] 	= $a_id;
		$role['description']= 'test';
		$e_id = $this->purviewService->saveRole($role, self::$uid, $this->op_role_id);
		if($this->purviewService->getNotice()){
			$this->fail(var_export($this->purviewService->getNotice(), true));
		}
		$tmp = $this->purviewService->getRoles(array($e_id));
		$tmp = array_pop($tmp);
		$this->assertTrue($a_id == $e_id && $role['description'] == $tmp['description'], '修改角色项测试不通过');
		
		$record = $this->getLastRecord();
		$this->assertTrue($record['op_uid'] == self::$uid && $record['role_id'] == $e_id, '保存操作记录测试不通过');
		return $e_id;
	}
	
	/**
	 * @test
	 * @depends saveRole
	 * @medium
	 */
	public function deleteRole($id){
		$this->getNewUser();
		$r = $this->purviewService->deleteRole($id, self::$uid, $this->op_role_id);
		$role = PurviewRoleModel::model()->findByPk($id);
		$role = $role->getAttributes();
		$this->assertTrue($r == true && $role['is_use'] == 0, '删除角色测试不通过');
		
		self::$role_id = $id;
		$role['is_use'] = 1;
		$this->purviewService->saveRole($role, self::$uid, $this->op_role_id);
		
		$record = $this->getLastRecord();
		$this->assertTrue($record['op_uid'] == self::$uid && $record['role_id'] == self::$role_id, '保存操作记录测试不通过');
	}
	
	/**
	 * @test
	 */
	public function saveRoleItem(){
		$this->getNewUser();
		$r = $this->purviewService->saveRoleItem(self::$role_id, array(self::$item_id), self::$uid, $this->op_role_id);
		$relation = PurviewRoleItemModel::model()->findByRoleItem(self::$role_id, self::$item_id);
		$this->assertTrue($r == true && $relation && $relation->is_use == 1, '保存角色权限关联测试不通过');
		
		$record = $this->getLastRecord();
		$this->assertTrue($record['op_uid'] == self::$uid && $record['relation_id'] == $relation->relation_id, '保存操作记录测试不通过');
	}
	
	/**
	 * @test
	 */
	public function deleteRoleItem(){
		$this->getNewUser();
		$r = $this->purviewService->deleteRoleItem(self::$role_id, array(self::$item_id), self::$uid, $this->op_role_id);
		$relation = PurviewRoleItemModel::model()->findByRoleItem(self::$role_id, self::$item_id);
		$this->assertTrue($r == true && $relation && $relation->is_use == 0, '删除角色权限关联测试不通过');
		
		$relation->is_use = 1;
		$relation->save();
		
		$record = $this->getLastRecord();
		$this->assertTrue($record['op_uid'] == self::$uid && $record['relation_id'] == $relation->relation_id, '保存操作记录测试不通过');
	}
	
	/**
	 * @test
	 */
	public function saveUserRoles(){
		$this->getNewUser();
		$r = $this->purviewService->saveUserRoles(self::$uid, array(self::$role_id), self::$uid, $this->op_role_id);
		$relation = UserRoleModel::model()->findByAttributes(array('uid' => self::$uid, 'role_id' => self::$role_id));
		$this->assertTrue($r == true && $relation, '保存用户角色关联测试不通过');
		
		$record = $this->getLastRecord();
		$this->assertTrue($record['op_uid'] == self::$uid && $record['uid'] == self::$uid && $record['role_id'] = self::$role_id, '保存操作记录测试不通过');
	}
	
	/**
	 * @test
	 */
	public function deleteUserRoles(){
		$this->getNewUser();
		$r = $this->purviewService->deleteUserRoles(self::$uid, array(self::$role_id), self::$uid, $this->op_role_id);
		$relation = UserRoleModel::model()->findByAttributes(array('uid' => self::$uid, 'role_id' => self::$role_id));
		$this->assertTrue($r == true && !$relation, '删除用户角色关联测试不通过');
		
		$this->purviewService->saveUserRoles(self::$uid, array(self::$role_id), self::$uid, $this->op_role_id);
		
		$record = $this->getLastRecord();
		$this->assertTrue($record['op_uid'] == self::$uid && $record['uid'] == self::$uid && $record['role_id'] = self::$role_id, '保存操作记录测试不通过');
	}
	
	private function getLastRecord(){
// 		$criteria = PurviewRecordModel::model()->getCommandBuilder()->createCriteria();
// 		$criteria->order = 'record_id desc';
// 		$criteria->limit = 1;
// 		$provider = new CActiveDataProvider(PurviewRecordModel::model(), array('criteria' => $criteria));
// 		return $provider->getData();
		$records = $this->purviewService->getRecords(1,1);
		return array_pop($records);
	}
	
	/**
	 * @test
	 */
	public function getItemsByGroups(){
		$items = $this->purviewService->getItemsByGroups(array('全局配置'));
		$items = $this->purviewService->buildDataByIndex($items, 'purview_id');
		$item = array_pop($items);
		$this->assertTrue($item['purview_id'] == self::$item_id, '获取某权限分组的可选权限项测试不通过');
	}
	
	/**
	 * @test
	 */
	public function getRolesBySub(){
		$roles = $this->purviewService->getRolesBySub(1, -1);
		$roles = $this->purviewService->buildDataByIndex($roles, 'role_id');
		$role = array_pop($roles);
		$this->assertTrue($role['role_id'] == self::$role_id, '获取某模块或子系统的所有角色测试不通过');
	}
	
	/**
	 * @test
	 */
	public function getRoleItems(){
		$items = $this->purviewService->getRoleItems(self::$role_id);
		$items = $this->purviewService->buildDataByIndex($items, 'purview_id');
		$item = array_pop($items);
		$this->assertTrue($item['purview_id'] == self::$item_id, '获取某角色所有权限值测试不通过');
	}
	
	/**
	 * @test
	 */
	public function getRolesItems(){
		$items = $this->purviewService->getRolesItems(array(self::$role_id));
		$items = $this->purviewService->buildDataByIndex($items, 'purview_id');
		$item = array_pop($items);
		$this->assertTrue($item['purview_id'] == self::$item_id, '获取某角色所有权限值测试不通过');
	}
	
	/**
	 * @test
	 */
	public function getUserRolesBySub(){
		$this->getNewUser();
		$roles = $this->purviewService->getUserRolesBySub(self::$uid, 1, -1);
		$roles = $this->purviewService->buildDataByIndex($roles, 'role_id');
		$role = array_pop($roles);
		$this->assertTrue($role['role_id'] == self::$role_id, '获取某用户在某子系统或模块内的所有角色测试不通过');
	}
	
	/**
	 * @test
	 */
	public function getUserRolesByUid(){
		$this->getNewUser();
		$roles = $this->purviewService->getUserRolesByUid(self::$uid);
		$roles = $this->purviewService->buildDataByIndex($roles, 'role_id');
		$role = array_pop($roles);
		$this->assertTrue($role['role_id'] == self::$role_id, '获取某用户在某子系统或模块内的所有角色测试不通过');
	}
	
	/**
	 * @test
	 */
	public function checkPurview(){
		$this->getNewUser();
		$r = $this->purviewService->checkPurview(self::$uid, 1, 0, 'test', 'Admin', 'admin');
		$this->assertTrue($r == true, '检查某用户是否具有操作权限测试不通过');
		
		//非正确用户测试权限检查
		$r = $this->purviewService->checkPurview(1, 1, 0, 'test', 'Admin', 'admin');
		$this->assertTrue($r == false, '检查不具有权限的用户是否具有操作权限测试不通过');
	}
	
	/**
	 * @test
	 */
	public function getItemsByRange(){
		$items = $this->purviewService->getItemsByRange(1);
		$item = array_pop($items);
		$this->assertTrue($item['purview_id'] == self::$item_id, '获取某套系统的可选权限项测试不通过');
	}
	
	/**
	 * @test
	 */
	public function search(){
		$group = $this->purviewService->getPurviewGroups();
		$group = array_pop($group);
		$this->assertTrue($group == '全局配置', '返回所有权限项的分组测试不通过');
	
		$item1 = $this->purviewService->getItem(self::$item_id);
		$item2 = $this->purviewService->purviewItemSearch($item1)->getData();
		$item2 = array_pop($item2);
		$item2 = $item2->getAttributes();
		$this->assertSame($item1, $item2, '权限项搜索测试不通过');
	
		$role1 = $this->purviewService->getRole(self::$role_id);
		$role2 = $this->purviewService->roleSearch($role1)->getData();
		$role2 = array_pop($role2);
		$role2 = $role2->getAttributes();
		$this->assertSame($role1, $role2, '角色搜索测试不通过');
	
		$roleItem1 = PurviewRoleItemModel::model()->findByAttributes(array('purview_id' => self::$item_id, 'role_id' => self::$role_id))->getAttributes();
		$roleItem2 = $this->purviewService->rolesPurviewSearch($roleItem1)->getData();
		$roleItem2 = array_pop($roleItem2);
		$roleItem2 = $roleItem2->getAttributes();
		$this->assertSame($roleItem1, $roleItem2, '角色权限项搜索测试不通过');
		
		$userRole1 = UserRoleModel::model()->with('role')->findByAttributes(array('uid' => self::$uid, 'role_id' => self::$role_id));
		$userRole1 = array_merge($userRole1->role->getAttributes(), $userRole1->getAttributes());
		$userRole2 = $this->purviewService->userRoleSearch(array('user_info' => $userRole1['uid']));
		$userRole2 = array_pop($userRole2);
		$this->assertSame($userRole1, $userRole2, '用户角色关联项搜索测试不通过');
	}
	
	/**
	 * @test
	 */
	public function deleteItemRelation(){
		$relations1 = PurviewRoleItemModel::model()->countByAttributes(array('purview_id' => self::$item_id, 'is_use' => 1));
		$this->purviewService->deleteItem(self::$item_id);
		$relations2 = PurviewRoleItemModel::model()->countByAttributes(array('purview_id' => self::$item_id, 'is_use' => 1));
		$this->assertTrue($relations1 > 0 && $relations2 == 0, '删除权限项的关联检查测试不通过');
	}
	
	/**
	 * @test
	 */
	public function deleteRoleRelation(){
		$relations1 = UserRoleModel::model()->countByAttributes(array('role_id' => self::$role_id));
		$this->purviewService->deleteRole(self::$role_id, self::$uid, 1);
		$relations2 = UserRoleModel::model()->countByAttributes(array('role_id' => self::$role_id));
		$this->assertTrue($relations1 > 0 && $relations2 == 0, '删除角色的关联检查测试不通过');
	}
	
	/**
	 * @test
	 */
	public function checkRank(){
		$range = $this->purviewService->getPurviewRange();
		$array = array();
		$value = 0;
		foreach($range as $k=>$v){
			if($k > 2) break;
			$array[] = $k;
			$value |= $k;
		}
		$tmp = $this->purviewService->checkRange($value);
		$this->assertSame($array, $tmp, '获取range及检查range测试不通过');
	}
	
	
}

