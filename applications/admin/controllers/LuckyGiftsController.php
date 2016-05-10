<?php
class LuckyGiftsController extends PipiAdminController {
	const AWARD_TYPE_NULL = 0;#无
	const AWARD_TYPE_GIFT = 1;#礼物
	const AWARD_TYPE_PROP = 2;#道具
	const AWARD_TYPE_EGGS = 3;#皮蛋
	
	/**
	 * @var LuckyGiftService 广播服务层
	 */
	public $service;

	/**
	 * @var array 允许的操作
	 */
	public $allowOp = array('addSetup','delSetup','getAwardView','checkPropList','addAward','delAward','checkChances','checkExists','editPoolRecord');

	/**
	 * @var string 当前操作
	 */
	public $op;

	/**
	 * @var boolean 是否是Ajax请求
	 */
	public $isAjax;

	public $pageSize = 20;

	public $offset;

	/**
	 * @var int page lable
	 */
	public $p;

	public function init(){
		parent::init();
		$this->service = new LuckyGiftService();
		$this->op = Yii::app()->request->getParam('op');
		$this->isAjax = Yii::app()->request->isAjaxRequest;
		if(!($this->p = Yii::app()->request->getParam('page'))){
			$this->p = 1;
		}
		$this->offset = ($this->p -1)*$this->pageSize;
	}
	
	/**
	 * 全站广播
	 */
	public function actionIndex(){
		if(!$this->isAjax){
			$this->assetsMy97Date();
		}
		
		$tab = Yii::app()->request->getParam('tab','clist');
		if (!in_array($tab, array('poolSet','poolRecord','giftAward','awardRecord'))){
			$tab = 'poolSet';
		}
		
		if($this->isAjax){
			$func = '_'.ucfirst($tab);
			exit($this->$func ());
		}else{
			$func = '_'.ucfirst($tab);
			$this->assetsMy97Date();
			$data = $this->$func ();
			$this->render('luckygifts_index',array('tab'=>$tab,'data'=>$data));
		}
	}
	
	/**
	 * 获取奖励类型
	 * @return multitype:string 
	 */
	public function getAwardTypes(){
		return array(
			self::AWARD_TYPE_NULL=>'无',
			self::AWARD_TYPE_GIFT=>'礼物',
			self::AWARD_TYPE_PROP=>'道具',
			self::AWARD_TYPE_EGGS=>'皮蛋');
	}
	
	public function getSources(){
		$service = new ConsumeService();
		return array(
				SOURCE_GIFTS=>'主播送礼',
				SOURCE_USERGIFTS=>'用户间送礼',
				SOURCE_PROPS=>'道具使用',
				SOURCE_SONGS=>'点歌 ',
				SOURCE_SENDS=>'后台'
			);
	}
	
	public function getSubSources(){
		$service = new ConsumeService();
		return array(
				SUBSOURCE_GIFTS_BUY=>'礼物购买',
				SUBSOURCE_GIFTS_BAG =>'礼物背包',
				SUBSOURCE_LUCK_GIFTS_BUY=>'幸运礼物',
				SUBSOURCE_LUCK_GIFTS_BAG=>'背包幸运礼物',
			);
	}
	
	/**
	 * 获取礼物列表
	 */
	public function getGiftListOption($isCondition = true,$isPrice=false){
		$condition = array();
		if($isCondition){
			$condition['is_display'] = 1;
		}
		$giftList = array();
		$giftSer = new GiftService();
		$list = $giftSer->getGiftList($condition);
		if($list){
			foreach($list as $v){
				if($isPrice){
					$giftList[$v['gift_id']] = $v['pipiegg'];
				}else{
					$giftList[$v['gift_id']] = $v['zh_name'];
				}
			}
		}
		return $giftList;
	}
	
	/**
	 * 获取幸运礼物
	 * @return multitype:unknown 
	 */
	public function getLuckGiftOption(){
		$condition['gift_type'] = 8;
		$giftList = array();
		$giftSer = new GiftService();
		$list = $giftSer->getGiftList($condition);
		if($list){
			foreach($list as $v){
				$giftList[$v['gift_id']] = $v['zh_name'];
			}
		}
		return $giftList;
	}
	
	/**
	 * 获取道具分类
	 *
	 * @return array
	 */
	public function getAllowSendPropsCat(){
		$newCat = array();
		$propSer = new PropsService();
		$allCat = $propSer->getPropsCatList();
		if($allCat){
			foreach ($allCat as $cat_id => $catInfo){
				if (in_array($catInfo['en_name'], array('car','monthcard','vip','prop','flyscreen'))){
					$newCat[$catInfo['cat_id']] = $catInfo['name'];
				}
			}
		}
		return $newCat;
	}
	
	/**
	 * 获取道具列表
	 * @return array
	 */
	public function getPropList(){
			$propsSer = new PropsService();
			return $propsSer->getPropsByCondition();
	}
	
	/**
	 * 获取中奖记录
	 */
	private function _GiftAward(){
		if($this->op == 'getAwardView' && in_array($this->op, $this->allowOp)){
			$this->_getAwardView();
		}
		
		if($this->op == 'checkPropList' && in_array($this->op, $this->allowOp)){
			$this->_checkPropList();
		}
		
		if($this->op == 'addAward' && in_array($this->op, $this->allowOp)){
			$this->_addAward();
		}
		
		if($this->op == 'checkChances' && in_array($this->op, $this->allowOp)){
			$this->_checkChances();
		}
		
		if($this->op == 'checkExists' && in_array($this->op, $this->allowOp)){
			$this->_checkExists();
		}
		
		if($this->op == 'delAward' && in_array($this->op, $this->allowOp)){
			$id = Yii::app()->request->getParam('id',false);
			if($id){
				if($this->service->delGiftAwardByIds($id)){
					exit('1');
				}
			}
			exit('删除失败');
		}
		
		$condition = $this->_getCondition();
		$clist = $this->service->searchGiftAwardList($condition,$this->offset,$this->pageSize);
		
		$pager = new CPagination($clist['count']);
		$pager->pageSize = $this->pageSize;
		$pager->params = $condition;
		return $this->renderPartial('_luckygifts_pool_award',array('pager'=>$pager,'clist'=>$clist),true);
	}
	
	/**
	 * 奖池设置
	 */
	private function _PoolSet(){
		if($this->op == 'addSetup'){
			if (in_array($this->op, $this->allowOp)){
				$chance = Yii::app()->request->getParam('chance',0);
				$value = Yii::app()->request->getParam('value',0);
				$id = Yii::app()->request->getParam('id',false);
				$info['value'] = $value;
				$info['chance'] = $chance;
				if($id){
					$info['id'] = $id;
				}
				$_id = $this->service->saveGiftPool($info);
				if($_id){
					if($id){
						exit('success');
					}else{
						exit("{$_id}");
					}
				}else{
					exit('fail');
				}
			}else{
				exit('fail');
			}
		}
		
		if($this->op == 'delSetup'){
			$id = Yii::app()->request->getParam('id',false);
			if($id){
				if($this->service->delGiftPoolByIds($id)){
					exit('1');
				}
			}
			exit('删除失败');
		}
		
		$list = $this->service->getGiftPoolList();
		return $this->renderPartial('_luckygifts_pool_set',array('list'=>$list),true);
	}
	
	/**
	 * 奖池变化记录表
	 */
	private function _poolRecord(){
		if($this->op == 'editPoolRecord' && in_array($this->op, $this->allowOp)){
			$this->_editPoolRecord();
		}
		
		$condition = $this->_getCondition();
		$clist = $this->service->searchGiftPoolRecord($condition,$this->offset,100);
		$pager = new CPagination($clist['count']);
		$pager->pageSize = 100;
		$pager->params = $condition;
		$this->renderPartial('_luckygifts_pool_record',array('pager'=>$pager,'clist'=>$clist));
	}
	
	/**
	 * 中奖记录
	 */
	private function _awardRecord(){
		$condition = $this->_getCondition();
		$clist = $this->service->searchUserAwardRecords($condition,$this->offset,$this->pageSize);
		$pager = new CPagination($clist['count']);
		$pager->pageSize = $this->pageSize;
		$pager->params = $condition;
		
		$uinfos = array();
		if($clist['list']){
			$uids = array();
			foreach($clist['list'] as $v){
				$uids[$v['uid']] = $v['uid'];
			}
			$userService = new UserService();
			$uinfos = $userService->getUserBasicByUids($uids);
		}
		$this->renderPartial('_luckygifts_award_record',array('pager'=>$pager,'clist'=>$clist,'uinfos'=>$uinfos,'condition'=>$condition));
	}
	
	/**
	 * 获取查询条件
	 * @return multitype:string unknown Ambigous <mixed, unknown> 
	 */
	private function _getCondition(){
		$condition = array();
		$gift_id = Yii::app()->request->getParam('gift_id');
		$stime = Yii::app()->request->getParam('stime');
		$etime = Yii::app()->request->getParam('etime');
		$uid = Yii::app()->request->getParam('uid');
		$type = Yii::app()->request->getParam('type');
		$source = Yii::app()->request->getParam('source');
		$sub_source = Yii::app()->request->getParam('sub_source');
		
		if ($stime) {
			$condition['stime'] = $stime;
		}
		if ($etime) {
			$condition['etime'] = $etime;
		}
		if ($uid) {
			$condition['uid'] = $uid;
		}
		if ($gift_id) {
			$condition['gift_id'] = $gift_id;
		}
		
		if ($type >=0) {
			$condition['type'] = $type;
		}
		
		if ($sub_source) {
			$condition['sub_source'] = $sub_source;
		}
		
		if ($source) {
			$condition['source'] = $source;
		}
		
		if($condition){
			foreach($condition as $k=>$v){
				if(empty($v)){
					unset($condition[$k]);
				}
			}
		}
		$condition['tab'] = Yii::app()->request->getParam('tab');
		$condition['op'] = $this->op;
		return $condition;
	}
	
	private function _getAwardView(){
		$type = Yii::app()->request->getParam('type',false);
		if($type >= 0){
			$consumeSer = new ConsumeService();
			if (!key_exists($type, $this->getAwardTypes())) {
				exit('不合法的类型，请确认');
			}
			exit($this->renderPartial('_award_type_'.$type));
		}else{
			exit('参数不合法的，请确认');
		}
	}
	
	/**
	 * 检查道具列表
	 */
	private function _checkPropList(){
		if (!$this->isAjax){
			exit('不合法请求');
		}
	
		$cat_id = Yii::app()->request->getParam('cat_id');
		$isEgg = Yii::app()->request->getParam('isEgg',false);
		if(empty($cat_id)){
			exit('1');
		}
		$catInfo = explode('*', $cat_id);
		$cat_id = $cat_id[0];
	
		$propsSer = new PropsService();
		$info = $propsSer->getPropsByCatId($cat_id);
		if($info){
			$html = '';
			foreach ($info as $v){
				if($isEgg){
					$html .= "<option value='".$v['prop_id']."'>{$v['pipiegg']}</option>";
				}else{
					$html .= "<option value='".$v['prop_id']."'>{$v['name']}*({$v['pipiegg']})</option>";
				}
			}
			if($html){
				exit($html);
			}
		}
		exit('2');
	}
	
	private function _addAward(){
		$chance = Yii::app()->request->getParam('chance',0);
		$target_id = Yii::app()->request->getParam('target_id',0);
		$gift_id = Yii::app()->request->getParam('gift_id',false);
		$type = Yii::app()->request->getParam('type',0);
		$award = Yii::app()->request->getParam('award',0);
		$id = Yii::app()->request->getParam('id',false);
		
		if($gift_id > 0 && $type >=0 && $award >= 0 && $target_id >= 0){
			$awards['award'] = $award;
			$awards['type'] = $type;
			$awards['target_id'] = $target_id;
			$awards['chance'] = $chance;
			$awards['gift_id'] = $gift_id;
			if($id){
				if((int)$id){
					$awards['id'] = $id;
				}else{
					exit('fail');
				}
			}
			$_id = $this->service->saveGiftAward($awards);
			if($_id){
				if($id){
					exit('success');
				}else{
					exit("{$_id}");
				}
			}else{
				exit('fail');
			}
		}else{
			exit('fail');
		}
	}
	
	private function _checkChances(){
		$gift_id = Yii::app()->request->getParam('gift_id',0);
		$chance = floatval(Yii::app()->request->getParam('chance',0));
		$target_id = Yii::app()->request->getParam('target_id',0);
		$type = Yii::app()->request->getParam('type',0);
		$award = Yii::app()->request->getParam('award',0);
		$id = Yii::app()->request->getParam('id',false);
		$pipiegg = 0;
		
		if($gift_id > 0 && $type >=0 && $award >= 0 && $target_id >= 0){
			if($chance > 1){
				exit('奖品概率不附合要求！ 概率不得大于1');
			}
			exit('1');
			
			$giftService = new GiftService();
			$giftInfo = $giftService->getGiftByIds($gift_id);
			if ($giftInfo){
				$pipiegg = $giftInfo[$gift_id]['pipiegg'];
			}else{
				exit('不存在该幸运礼物');
			}
			
			$Tpipiegg = 1;
			if ($type == self::AWARD_TYPE_GIFT){
				$targetInfo = $giftService->getGiftByIds($target_id);
				if ($targetInfo){
					$Tpipiegg = $targetInfo[$target_id]['pipiegg'];
				}else{
					exit('不存在该礼物');
				}
			}else if ($type == self::AWARD_TYPE_PROP){
				$propsService = new PropsService();
				$targetInfo = $propsService->getPropsByIds($target_id);
				if ($targetInfo){
					$Tpipiegg = $targetInfo[$target_id]['pipiegg'];
				}else{
					exit('不存在该道具');
				}
			}
			
			$condition['type'] = $type;
			$condition['target_id'] = $target_id;
			$condition['gift_id'] = $gift_id;
			$data = $this->service->searchGiftAwardList($condition,$this->offset,$this->pageSize,false);
			if (!empty($data['list'])){
				if ($type == self::AWARD_TYPE_NULL){
					exit('1');
				}
				
				$Tchance = 0;
				$_Nchance = 0;
				$_Ochance = 0;
				foreach($data['list'] as $v){
					if ($type == self::AWARD_TYPE_EGGS){
						if($id == $v['id']){
							$Tchance += $award*$chance;
							$_Nchance = $award*$chance;
						}else{
							$Tchance += $v['chance']*$v['award'];
							$_Ochance += $v['chance']*$v['award'];
						}
					}else{
						if($id == $v['id']){
							$Tchance += $Tpipiegg*$chance*$award;
							$_Nchance = $Tpipiegg*$chance*$award;
						}else{
							$Tchance += $v['chance']*$Tpipiegg*$v['award'];
							$_Ochance += $v['chance']*$v['award']*$Tpipiegg;
						}
					}
				}
				if($id <=0){
					if ($type == self::AWARD_TYPE_EGGS){
						$Tchance += $award*$chance;
						$_Nchance += $award*$chance;
					}else{
						$Tchance += $award*$chance*$Tpipiegg;
						$_Nchance += $award*$chance*$Tpipiegg;
					}
				}
				
				if($pipiegg >= $Tchance*2){
					exit('1');
				}else{
					if($id <=0){
						$maxChance = number_format(($pipiegg/2-($Tchance-$_Nchance))/$award/$Tpipiegg,4,'.','');
					}else{
						$maxChance = number_format(($pipiegg/2-$_Ochance)/$award/$Tpipiegg,4,'.','');
					}
					exit('奖品概率不附合要求！建议小于：'.($maxChance));
				}
			}else{
				if($pipiegg >= $chance*$Tpipiegg*$award*2){
					exit('1');
				}else{
					exit('奖品概率不附合要求！建议小于：'.number_format(($pipiegg/2/$award/$Tpipiegg),4,'.',''));
				}
			}
		}else{
			exit('参数有误，提交失败');
		}
	}
	
	private function _checkExists(){
		$gift_id = Yii::app()->request->getParam('gift_id',0);
		$target_id = Yii::app()->request->getParam('target_id',0);
		$type = Yii::app()->request->getParam('type',0);
		$award = Yii::app()->request->getParam('award',0);
	
		if($gift_id > 0 && $type >=0 && $award >= 0 && $target_id >= 0){
			$condition['type'] = $type;
			$condition['target_id'] = $target_id;
			$condition['gift_id'] = $gift_id;
			$condition['award'] = $award;
			$data = $this->service->searchGiftAwardList($condition);
			if($data['count'] > 0){
				exit('已存在惟一值，不能更新(类型+幸运礼物+倍数数量+目标对象=惟一),');
			}else{
				exit('1');
			}
		}else{
			exit('参数有误，提交失败');
		}
	}
	
	private function _editPoolRecord(){
		$id = Yii::app()->request->getParam('id',0);
		$value = Yii::app()->request->getParam('value',0);
		$chance = Yii::app()->request->getParam('chance',0);
		if($id<=0 || $value<=0 || $chance<0){
			exit('参数有误，编辑失败');
		}else{
			if($this->service->editGiftPoolRecord($id,$value,$chance)){
				exit('1');
			}else{
				exit('系统有误，编辑失败');
			}
		}
		exit('编辑失败');
	}
}
