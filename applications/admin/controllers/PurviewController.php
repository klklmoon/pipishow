<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: supeng $>
 * @author Su Peng <594524924@qq.com>
 * @version $Id: PurviewController.php 8317 2013-03-29 01:19:47Z supeng $ 
 * @package 
 */
class PurviewController extends PipiAdminController {
	
	/**
	 * 允许的操作项
	 * 
	 * @var array
	 */
	public $allowOp = array('checkRoleGroupItems','purDel','roleDel','userRoleDel','checkUserName');
	
	public function init() {
		parent::init();
	}
	
	/**
	 * 系统权限管理
	 */
	public function actionPurList() {
		$groups = $this->purSer->getPurviewGroups();
		$allGroups = array();
		foreach ($groups as $key=>$group){
			$allGroups[$group] = $group;
		}
		
		$sysItems = $this->getAllGroupsDetail($allGroups,false,false);
		$sysGroups = $this->formatSysGroups($sysItems);
		$range = $this->purSer->getPurviewRange();
		$model = new PurviewItemsForm();
		$this->render('purview_list',array(
			'groups'=>$allGroups,
			'sysGroups' => $sysGroups,
			'model'=>$model,
			'range' => $range,
		));
	}
	
	/**
	 * 添加权限项
	 */
	public function actionPurAdd() {
		//ajax检查权限项 可删除
		$op = Yii::app()->request->getParam('op');
		if ($op && in_array($op, $this->allowOp) && $op == 'purDel' && Yii::app()->request->isAjaxRequest){
			if ($purview_id = Yii::app()->request->getParam('purview_id')){
				$this->purDel($purview_id);
			}else{
				exit();
			}
		}
		
		$groups = $this->purSer->getPurviewGroups();
		$allGroups = array();
		foreach ($groups as $key=>$group){
			$allGroups[$group] = $group;
		}
		
		 $range = $this->purSer->getPurviewRange();
		
		//是否有编辑或查看的标识
		$purviewInfo = array();
		//已经存在的权限
		$hasRange = array(); 
		if ($purview_id = Yii::app()->request->getParam('purview_id')){
			$purviewInfo = $this->purSer->getItem($purview_id);
			$hasRange = $this->purSer->checkRange($purviewInfo['range']);
		}
		
		//是否有分组的标识
		$groupFlag = Yii::app()->request->getParam('groupName');
		
		$model = new PurviewItemsForm();
		//添加和编辑操作
		if($data = Yii::app()->request->getParam(get_class($model))) {
			$this->purAddOp($model,$data);
		}
		
		if(Yii::app()->request->isAjaxRequest){
			$this->renderPartial('purview_add',array('model'=>$model,'purviewInfo'=>$purviewInfo,'groups'=>$allGroups,'range'=>$range,'isAjax'=>true,'hasRange'=>$hasRange,'groupFlag'=>$groupFlag));
		}else{
			$this->render('purview_add',array('model'=>$model,'purviewInfo'=>$purviewInfo,'groups'=>$allGroups,'range'=>$range,'isAjax'=>false,'hasRange'=>$hasRange,'groupFlag'=>$groupFlag));
		}
	}
	
	
	/**
	 * 添加角色
	 */
	public function actionRoleAdd() {
		$model = new PurviewRoleForm();
		
		$role_id = Yii::app()->request->getParam('role_id');
		$op = Yii::app()->request->getParam('op');
		if ($op && in_array($op, $this->allowOp) && $op == 'checkRoleGroupItems'){
			$this->checkRoleGroupItems($model);
		}
		
		if ($op && in_array($op, $this->allowOp) && $op == 'roleDel'){
			$this->roleDel($role_id);
		}
		
		//修改
		$roleInfo = array();
		$roleItems = array();
		$allRoleItems = array();
		$role_id = Yii::app()->request->getParam('role_id');
		if ($role_id = Yii::app()->request->getParam('role_id')) {
			$roleInfo = $this->purSer->getRole($role_id);
			$roleItems = $this->purSer->getRoleItems($role_id);
			$roleItems = $this->formatGroupsItems($roleItems);
			$allRoleItems = $this->purSer->getItemsByRange($roleInfo['role_type']);
			$allRoleItems = $this->formatGroupsItems($allRoleItems);
		}
		
		//角色权限操作
		if(($data = Yii::app()->request->getParam(get_class($model)))) {
			$this->roleItemsOp($model,$data);
		}
		
		if (Yii::app()->request->isAjaxRequest){
			$this->renderPartial(
				'role_add', array( 'model'=>$model, 'roleInfo' => $roleInfo, 'roleItems'=>$roleItems,'isAjax'=>true,'allRoleItems'=>$allRoleItems )
			);
		}else{
			$this->render(
				'role_add', array( 'model'=>$model, 'roleInfo' => $roleInfo, 'roleItems'=>$roleItems,'isAjax'=>false ,'allRoleItems'=>$allRoleItems )
			);
		}
		
	}

	/**
	 * 角色列表管理
	 */
	public function actionRoleList() {
        $model = new PurviewRoleForm();
        if(!($data = Yii::app()->request->getParam(get_class($model)))){
        	$data = array();
        }
        
        $this->render('role_list',array(
        	'dataProvider'=>$this->purSer,
        	'model' => $model,
        	'data' => $data,
        ));
	}
	
	/**
	 * 角色权限列表 可删除
	 */
	public function actionRolePurList() {
		$model = new PurviewRoleItemForm();
		if(!($data = Yii::app()->request->getParam(get_class($model)))){
			$data = array();
		}
		
		$this->render('role_purview_list',array(
			'dataProvider'=>$this->purSer,
			'model' => $model,
			'data' => $data,
		));
	}
	
	/**
	 * 用户权限管理
	 */
	public function actionUserRole(){
		if(!($data = Yii::app()->request->getParam('search'))){
			$data = array();
		}
		
		$userRoles = $this->formatUserRoles($this->purSer->userRoleSearch($data));
		$this->render('user_role_list',array(
			'userRoles'=>$userRoles,
		));
	}
	
	/**
	 * 添加用户角色
	 */
	public function actionUserRoleAdd(){
		//删除
		$op = Yii::app()->request->getParam('op');
		if ($op && in_array($op,$this->allowOp) && $op == 'userRoleDel' && Yii::app()->request->isAjaxRequest){
			$this->userRoleDel();
		}
		
		//检查用户名的合法性
		if ($op && in_array($op,$this->allowOp) && $op == 'checkUserName' && Yii::app()->request->isAjaxRequest){
			$this->checkUserName();
		}
		
		$ranges = $this->purSer->getPurviewRange();
		//修改
		$userInfos = array();
		$checkUserRoles = array();
		if ($uid = Yii::app()->request->getParam('uid')) {
			if($userRoleInfo = $this->purSer->getUserRolesByUid($uid)){
				$checkUserRoles = array();
				foreach($userRoleInfo as $roles){
					$checkUserRoles[$ranges[$roles['role_type']]][] = $roles['role_id'];
				}
			} 
			$userService = new UserService();
			$userInfos = $userService->getUserBasicByUids(array($uid));
			$userInfos = $userInfos[$uid];
		}
		
		$model = new PurviewUserRoleForm();
		//角色权限操作
		if(($data = Yii::app()->request->getParam(get_class($model)))) {
			$this->userRoleOp($model,$data);
		}

		//获取所有Range对应的Roles
		$rangeRoles=array();
		foreach ($ranges as $range=>$rangeName){
			if($roles = $this->purSer->getRolesBySub($range)){
				foreach ($roles as $role){
					$rangeRoles[$rangeName][$role['role_id']]=$role['role_name'];
				}
			}
		}

		if(Yii::app()->request->isAjaxRequest){
			$this->renderPartial('user_role_add',array('model'=>$model,'userInfos'=>$userInfos,'rangeRoles'=>$rangeRoles,'checkUserRoles'=>$checkUserRoles,'isAjax'=>true));
		}else{
			$this->render('user_role_add',array('model'=>$model,'userInfos'=>$userInfos,'rangeRoles'=>$rangeRoles,'checkUserRoles'=>$checkUserRoles,'jsAjax'=>false));
		}
	}
	
	/**
	 * ajax管理角色权限校验
	 * 	校验角色类型与权限分组的关系
	 * 	权限分组与权限操作的关系
	 * 	默认角色权限的验证与配置操作
	 * 
	 * @param PurviewRoleForm $model
	 */
	public function checkRoleGroupItems($model) {
		$role_types = array_keys($this->purSer->getPurviewRange());
		$role_type = Yii::app()->request->getParam('role_type');
		if ($role_type && in_array($role_type, $role_types)){
			if($items = $this->purSer->getItemsByRange($role_type)){
				$pruviewItems = $this->formatGroupsItems($items);
				
				//已经选择的权限组
				$role_id = Yii::app()->request->getParam('role_id');
				$roleItems = array();
				if ($role_id){
					if(($rs = $this->purSer->getRoleItems($role_id))) {
						$roleItems = $this->formatGroupsItems($rs);
					}
				}
				
				$this->renderPartial(
					'_role_items', array('role_items'=>$pruviewItems,'model'=>$model,'role'=>$roleItems)
				);
			}
			
		}
		Yii::app()->end();
	}
	
	/**
	 * 角色权限操作
	 * 
	 * @param PurviewRoleForm $model
	 * @param array $data
	 */
	public function roleItemsOp(PurviewRoleForm $model,Array $data){
		$model->attributes = $data;
		if ($model->validate()){
			$sub_id = $data['sub_id'];
			$op_uid = Yii::app()->user->getId();
			$_data = array();
			$_data['role_name'] = $data['role_name'];
			$_data['role_type'] = $data['role_type'];
			$_data['sub_id'] = $sub_id;
			$_data['description'] = $data['description'];
			if(isset($data['role_id'])){
				$_data['role_id'] = $data['role_id'];
			}
			$op_role_id = 1;//超级管理员默认的角色ID
			if(($role_id = $this->purSer->saveRole($_data, $op_uid, $op_role_id,$sub_id))){
				//角色权限关联操作
				if (isset($data['groups_items'])){
					$purview_ids = array();
					$groupItems = $data['groups_items'];
					foreach ($groupItems as $label=>$items){
						if (count($items)){
							foreach ($items as $purview_id){
								if(!isset($groupItems[$purview_id])){
									$purview_ids[] = $purview_id;
								}
							}
						}
					}
					if ($purview_ids){
						if($this->purSer->saveRoleItem($role_id, $purview_ids, $op_uid, $op_role_id)){
							$this->redirect($this->createUrl('purview/roleList'));
						}
					}else{
						$model->addError('opInfo', '系统内部发生错误');
					}
				}
					
			}else{
				$model->addError('opInfo', '系统内部发生错误');
			}
		}else{
			$model->addError('opInfo', '验证失败，有不合法的输入');
		}
		$this->refresh();
	}
	
	/**
	 * 用户角色关联操作
	 *
	 * @param PurviewRoleForm $model
	 * @param array $data
	 */
	public function userRoleOp(PurviewUserRoleForm $model,Array $data){
		$roleIds = array();
		if(isset($data['role_id'])){
			foreach ($data['role_id'] as $roles){
				if (is_array($roles)){
					foreach ($roles as $role_id){
						$roleIds[] = $role_id;
					}
				}
			}
			$data['role_id'] = $roleIds;
		}
		$model->attributes = $data;
		if ($model->validate()){
			if($data['username']){
				$uService = new UserService();
				if($uinfo = $uService->getVadidatorUser($data['username'],0)){
					$uid = $uinfo['uid'];
					$op_uid = Yii::app()->user->getId();
					$roleIds = $data['role_id'];
					if($this->purSer->saveUserRoles($uid, $roleIds, $op_uid,1)){
						$this->redirect($this->createUrl('purview/userrole'));
					}
				}
			}
		}else{
			var_dump($model->getErrors());
		}
		
		$this->refresh();
	}
	
	/**
	 * 执行添加权限操作
	 * 
	 * @param PurviewItemsForm $model
	 * @param array $data
	 */
	public function purAddOp(PurviewItemsForm $model,$data){
		$model->attributes = $data;
		if ($model->validate()){
			$_data = array();
			if (isset($data['purview_id'])){
				$_data['purview_id'] = $data['purview_id'];
			}
			$_data['purview_name'] = $data['purview_name'];
			$_data['group'] = $data['group'];
			$_data['module'] = $data['module'];
			$_data['controller'] = $data['controller'];
			$_data['action'] = $data['action'];
			if(isset($data['is_use'])){
				$_data['is_use'] = $data['is_use'];
			}
			$_data['is_tree_display'] = $data['is_tree_display'];
			$_data['range'] = $data['range'];
			if($this->purSer->saveItem($_data)){
				$this->redirect($this->createUrl('purview/purList'));
			}else{
				print_r($model->getErrors());exit;
			}
		}
		
		$this->refresh();
	}
	
	/**
	 * 验证用户名正确性 停用
	 * 
	 * @param PurviewUserRoleForm $model
	 */
	public function checkRoleIds(PurviewUserRoleForm $model){
		if($role_type = Yii::app()->request->getParam('role_type')){
			if (is_numeric($role_type)){
				$checkUserRoles = array();
				//已经配置的用户角色
				if ($username = Yii::app()->request->getParam('username')) {
					$userService = new UserService();
					$userInfo = $userService->getVadidatorUser($username, 0);
					if (!$userInfo){
						Yii::app()->end();
					}
					
					if($rs = $this->purSer->getUserRolesBySub($userInfo['uid'],$role_type)){
						foreach ($rs as $v){
							$checkUserRoles[$v['role_id']] = $v['role_name'];
						}
					}
					unset($rs);
				}
				
				//所有 角色列表
				$roles = array();
				if($rs = $this->purSer->getRolesBySub($role_type)) {
					foreach ($rs as $v){
						$roles[$v['role_id']] = $v['role_name'];
					}
					$this->renderPartial( '_user_role_items', array( 'model'=>$model, 'allRoles'=>$roles, 'check'=>$checkUserRoles) );
				}
				
			}
		}
		Yii::app()->end();
	}
	
	/**
	 * 格式化系统目录
	 * 
	 * @param array $sysGroups
	 */
	public function formatSysGroups(Array $sysGroups) {
		$groups = array();
		if ($sysGroups){
			foreach ($sysGroups as $group){
				$groups[$group['group']][$group['purview_name']] = $group;
			}
		}
		return $groups;
	}
	
	/**
	 * 删除权限项操作
	 * 	将is_use 值置为0即可
	 * @param int $purview_id
	 */
	public function purDel($purview_id){
		if($this->purSer->deleteItem($purview_id)){
			echo 1;
		}else{
			echo '删除失败';
		}
		exit;
	}
	
	/**
	 * 删除角色操作
	 * @param int $role_id
	 */
	public function roleDel($role_id){
		if(Yii::app()->request->isAjaxRequest){
			if($role_id){
				if($role_id == $this->op_role_id){
					echo "您正在使用该角色，不能删除，会影响你的正常使用";
				}else{
					if($this->purSer->deleteRole($role_id, Yii::app()->user->getId(), $this->op_role_id)){
						echo 1;
					}else{
						echo '该角色不在在 或已经被删除';
					}
				}
			}else{
				echo '参数不合法';
			}
		}else{
			echo '不是合法请求';
		}
		exit;
	}
	
	/**
	 * 转换角色类型
	 * 
	 * @param array $data
	 * @param int $row
	 * @param int $c
	 */
	public function transRoleType($data,$row,$c) {
		if (isset($data->role_type)){
			$allRange = $this->purSer->getPurviewRange();
			if(key_exists($data->role_type, $allRange)){
				echo $allRange[$data->role_type];
			}
		}
	}
	
	/**
	 * 转换角色状态
	 *
	 * @param array $data
	 * @param int $row
	 * @param int $c
	 */
	public function transStatus($data,$row,$c) {
		if (isset($data->is_use)){
			if($data->is_use == 1){
				echo '<span class="label label-success">可用</span>';
			}else{
				echo '<span class="label label-important">已删除</span>';
			}
		}
	}

	/**
	 * 格式化用户角色数据
	 * @param array $data
	 */
	public function formatUserRoles(Array $data){
		$result = array();
		if ($data){
			$result = array();
			$userService = new UserService();
			$uids = array();
			
			foreach ($data as $k => $value){
				if (!in_array($value['uid'], $uids)) {
					$uids[] = $value['uid'];
				}
				
				$rs[$value['uid']][$value['role_id']]['role_name'] = $value['role_name'];
				$rs[$value['uid']][$value['role_id']]['role_type'] = $value['role_type'];
				$rs[$value['uid']][$value['role_id']]['is_use'] = $value['is_use'];
				$rs[$value['uid']][$value['role_id']]['sub_id'] = $value['sub_id'];
			}
			$result['roleInfos'] = $rs;
			
			$uInfos = array();
			$ucount = count($uids);
			$sep = 20;
			for ($i=1;$i<=$ucount;$i=$i+$sep){
				$_uids = array_slice($uids, $i-1,($i+$sep-1));
				$_uInfos = $userService->getUserBasicByUids($_uids);
				foreach ($_uInfos as $uid=>$value){
					if (!key_exists($uid, $uInfos)){
						$uInfos[$uid]=$value['username'];
					}
				}
			}
			$result['uInfos'] = $uInfos;
		}
		return $result;
	}
	
	/**
	 * 执行用户权限删除操作
	 */
	public function userRoleDel(){
		if ($uid = Yii::app()->request->getParam('uid')){
			if($role_id = Yii::app()->request->getParam('role_id')){
				if($this->purSer->deleteUserRoles($uid, array($role_id), Yii::app()->user->getId(), $this->op_role_id)){
					echo 1;
				}
			}else{
				echo "删除失败 角色ID不能为空";
			}
		}else{
			echo "删除失败 用户ID不能为空";
		}
		exit;
	}
	
	public function checkUserName(){
		if($username = Yii::app()->request->getParam('username')){
			$userService = new UserService();
			if($uinfo = $userService->getVadidatorUser($username, USER_LOGIN_USERNAME)){
				echo 1;
			}else{
				echo 0;
			}
		}else{
			echo 0;
		}
		exit;
	}
}