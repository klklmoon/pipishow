<?php
/**
 * 提供给首页的数据服务层
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: 2013-12-12 下午4:11:02 hexin $ 
 * @package
 */
class IndexPageService extends PipiService{
	/**
	 *
	 * @var UserService
	 */
	protected $userService = null;
	
	/**
	 *
	 * @var OperateService
	 */
	protected $operateService = null;
	
	/**
	 *
	 * @var ArchivesService
	 */
	protected $archiveService = null;
	
	/**
	 *
	 * @var DoteyService
	 */
	protected $doteyService = null;
	
	/**
	 *
	 * @var GiftService
	 */
	protected $giftService = null;
	
	/**
	 *
	 * @var ChannelDoteySortService
	 */
	protected $channelDoteySort = null;
	
	public function __construct(PipiController $pipiController = null){
		parent::__construct($pipiController);
		$this->userService = new UserService();
		$this->operateService = new OperateService();
		$this->archiveService = new ArchivesService();
		$this->doteyService = new DoteyService();
		$this->giftService = new GiftService();
		$this->channelDoteySort = new ChannelDoteySortService();
	}
	
	/**
	 * 获取首页右侧后台可控的主播数据
	 * @return Ambigous <multitype:, multitype:number string unknown Ambigous <string, Ambigous <string, unknown>> >
	 */
	public function getIndexRightData(){
		$return = array();
		$indexRightModel = new IndexRightDataModel();
		$indexRightData = $indexRightModel->findAll(array('order'=>'type ASC,charms DESC'));
		if($indexRightData){
			$indexRightData = $this->arToArray($indexRightData);
			$indexRightUids = array_keys($this->buildDataByIndex($indexRightData,'uid'));
			$indexRightAvatars = $this->userService->getUserAvatarsByUids($indexRightUids,'small');
			foreach($indexRightData as $indexRight){
				$return[$indexRight['type']][$indexRight['uid']] = array(
					'uid' => $indexRight['uid'],
					'charms' => $indexRight['charms'],
					'auto' => 1,
					'subject' => '',
					'username' => $indexRight['username'],
					'nickname' => $indexRight['nickname'],
					'small_avatar' => $indexRightAvatars[$indexRight['uid']],
				);
			}
		}
		return $return;
	}
	
	/**
	 * 获取首页需要的运营数据，如首页厨窗广告、活动推荐、首页公告等
	 * @return array
	 */
	public function getOperateData(){
		$operate = $this->operateService->getOperateByCategoryFromCache(CATEGORY_INDEX);
		$operateUrl = $this->operateService->getOperateUrl();
		foreach($operate as $key=>$sub){
			foreach($sub as $k=>$v){
				if(!empty($v['piclink'])) $operate[$key][$k]['piclink'] = $operateUrl.$v['piclink'];
			}
		}
		return $operate;
	}
	
	/**
	 * 处理新秀、最新加入、明星主播数据，混合上月前20名的主播和后台人工加入的主播，随机抽取几名主播
	 * @param array $starDotey web_index_rightdata表的上月前20名魅力值排序的主播
	 * @param array $operate 前20名名额不够时后台可加入的主播数据
	 * @param int $num 随机抽取主播数
	 * @param int $key 运营数据中的主播数据key
	 * @param array $otherData 需要过滤运营数据的情况
	 * @return array
	 */
	public function getDoteyData($dotey, $operate, $num = 4, $key = '', $otherData = array()){
		$finalDotey = array();
		/* 历史做法
		if(isset($operate[$key])){
			foreach($operate[$key] as $value){
				if(isset($otherData[$value['target_id']])){
					continue;
				}
				$dotey[$value['target_id']]['uid'] = $value['target_id'];
				$dotey[$value['target_id']]['auto'] = 0;
				$dotey[$value['target_id']]['charms'] = 0;
				$dotey[$value['target_id']]['subject'] = $value['subject'];
				$dotey[$value['target_id']]['username'] = $value['content']['username'];
				$dotey[$value['target_id']]['nickname'] = $value['content']['nickname'];
			}
		}
		
		if($dotey){
			$livingStatus = 1;
			if($key == CATEGORY_INDEX_STARCOLLEGE) $livingStatus = 0;
			
			$doteyArchives = $this->archiveService->getArchivesByUids(array_keys($dotey),true,0);
			$doteyArchives = $this->channelDoteySort->filterArchives($doteyArchives, $livingStatus);
			$doteyArchives = $this->fillAttentionData($doteyArchives);
			$living = $randKey = array();
			foreach($doteyArchives as $key => $archive){
				$archive['subject'] = $archive['live_record']['sub_title'] ? $archive['live_record']['sub_title'] : $archive['title'];
				if($archive['live_record']['status'] == 1){
					$living['living'][$key] = $archive;
				}elseif($archive['live_record']['status'] == 0){
					$living['wait'][$key] = $archive;
				}
			}
			$livingArchives = !empty($living['living']) ? $living['living'] : array();
			$waitArchives = !empty($living['wait']) ? $living['wait'] : array();
			
			if(count($livingArchives) > $num){
				$randKey = array_rand($livingArchives,$num);
			}else{
				if(!empty($livingArchives)){
					shuffle($livingArchives);
					$randKey = array_keys($livingArchives);
				}
			}
			foreach($randKey as $doteyKey){
				$archive = $livingArchives[$doteyKey];
				$uid = $archive['uid'];
				$_dotey = $dotey[$uid];
				$finalDotey[$uid] = $_dotey;
				$finalDotey[$uid]['is_attention'] = $archive['is_attention'];
				if(empty($_dotey['subject'])){
					$finalDotey[$uid]['subject'] = $archive['subject'];
				}
				if(empty($_dotey['small_avatar'])){
					$avatar = $this->userService->getUserAvatarsByUids(array($uid),'small');
					$finalDotey[$uid]['small_avatar'] =  $avatar[$uid];
				}
			}
			
			if($livingStatus == 0 && count($randKey) < $num){
				$i = count($randKey);
				if($waitArchives){
					shuffle($waitArchives);
					foreach($waitArchives as $archive){
						if($i >= $num){
							break;
						}
						$uid = $archive['uid'];
						$_dotey = $dotey[$uid];
						$finalDotey[$uid] = $_dotey;
						$finalDotey[$uid]['is_attention'] = $archive['is_attention'];
						if(empty($_dotey['subject'])){
							$finalDotey[$uid]['subject'] = $archive['subject'];
						}
						if(empty($_dotey['small_avatar'])){
							$avatar = $this->userService->getUserAvatarsByUids(array($uid),'small');
							$finalDotey[$uid]['small_avatar'] =  $avatar[$uid];
						}
						$i++;
					}
				}
			}
		}
		*/
		//明星主播
		if($key == CATEGORY_INDEX_STARCOLLEGE){
			$uid = Yii::app()->user->id;
			$finalDotey = $this->userService->getUserCharmRank('week', $uid);
			$finalDotey = array_slice($finalDotey, 0, $num);
		//新秀主播
		}elseif($key == CATEGORY_INDEX_COLUMNSRECOMMAND){
			$finalDotey = OtherRedisModel::getInstance()->getBlueDotey();
			$finalDotey = array_slice($finalDotey, 0, $num);
			$this->fillAttentionData($finalDotey);
			
			$uids = array_keys($this->buildDataByIndex($finalDotey, 'd_uid'));
			$avatars = $this->userService->getUserAvatarsByUids($uids,'small');
			foreach($finalDotey as &$arch){
				$arch['d_avatar'] = $avatars[$arch['d_uid']];
			}
		//最新加入
		}elseif($key == CATEGORY_INDEX_NEWDOTEY){
			$finalDotey = OtherRedisModel::getInstance()->getRedDotey();
			$finalDotey = array_slice($finalDotey, 0, $num);
			$this->fillAttentionData($finalDotey);
			
			$uids = array_keys($this->buildDataByIndex($finalDotey, 'd_uid'));	
			$avatars = $this->userService->getUserAvatarsByUids($uids,'small');
			foreach($finalDotey as &$arch){
				$arch['d_avatar'] = $avatars[$arch['d_uid']];
			}
		}
		return $finalDotey;
	}
	
	/**
	 * 获取全局礼物
	 * @return array
	 */
	public function getGlobalGift(){
		$topSendGift = $this->giftService->getGlobalGiftList();
		if($topSendGift){
			$topSendDoteyIds =  array_keys($this->giftService->buildDataByIndex($topSendGift,'d_uid'));
			$archives = $this->archiveService->getArchivesByUids($topSendDoteyIds,true,0);
			$archives = $this->archiveService->buildDataByIndex($archives,'uid');
		
			foreach($topSendGift as $key => $topSend){
				if(isset($archives[$topSend['d_uid']])){
					$archive = $archives[$topSend['d_uid']];
					$topSendGift[$key]['title'] = $archive['title'];
					$topSendGift[$key]['sub_title'] = $archive['title'];
				}else{
					$topSendGift[$key]['title'] = '';
					$topSendGift[$key]['sub_title'] = '';
				}	
			}
		}
		return $topSendGift;
	}
	
	/**
	 * 生日专栏数据
	 * @return array = array(today => ..., will => ...);
	 */
	public function getBirthdayDotey($loginUid){
		$happyBirthdayService=new HappyBirthdayService();
		$otherRedisModel=new OtherRedisModel();
		$happy_birthday_page=$otherRedisModel->getHappyBirthdayPageData();
		$birthdayArchives=$happy_birthday_page['birthdayArchives'];
		if(empty($birthdayArchives)){
			$birthdayArchives=$happyBirthdayService->getBirthdayArchives();
		}
		$return = $today = $will = array();
		if(!empty($birthdayArchives['todayBirthdayArchives'])){
			$today = $birthdayArchives['todayBirthdayArchives'];
			$today = $this->fillAttentionData($today);
			foreach ($today as &$doteyRow){
				$doteyRow['doteyInfo']=$this->userService->getUserFrontsAttributeByCondition($doteyRow['uid'],true,true);
				$doteyRow['display_small']= $this->userService->getUserAvatar($doteyRow['uid'],"small");
				$doteyRow['is_attention'] = isset($doteyRow['is_attention']) ? $doteyRow['is_attention'] : 0;
			}
		}
		if(!empty($birthdayArchives['willBirthdayArchives'])){
			$will = $birthdayArchives['willBirthdayArchives'];
			$will = $this->fillAttentionData($will);
			foreach ($will as &$doteyRow){
				$doteyRow['doteyInfo']=$this->userService->getUserFrontsAttributeByCondition($doteyRow['uid'],true,true);
				$doteyRow['display_small']= $this->userService->getUserAvatar($doteyRow['uid'],"small");
				$doteyRow['is_attention'] = isset($doteyRow['is_attention']) ? $doteyRow['is_attention'] : 0;
			}
		}
		$return['today'] = $today;
		$return['will'] = $will;
		return $return;
	}
	
	/**
	 * 获取主播浮层上的我关注主播，我管理主播，看过的主播数据。看过的主播数据是在直播间写cookie和登陆无关
	 * 返回数据我关注主播，我管理主播返回在直播数据，看过的主播无需区分是否在直播
	 * @param int $uid
	 * @param string $type attention|manager|latestSee
	 * @return array = array('list' => ..., 'total' => 0)
	 */
	public function getDoteyLayer($uid, $type = ''){
		$return = array('list' => array(), 'total' => 0);
		$archives = array();
		$filter = true;
		if($type == 'attention'){
			if(!$uid) return $return;
			
			$weiboService = new WeiboService();
			$dotey = $weiboService->getDoteyAttentionsByUid($uid);
			if($dotey){
				$uids = array_keys($this->buildDataByIndex($dotey,'uid'));
				$archives = $this->archiveService->getArchivesByUids($uids,true);
			}
		}elseif($type == 'manager'){
			if(!$uid) return $return;
			
			$manager = $this->archiveService->getPurviewLiveByUids($uid);
			if($manager){
				$manager = $manager[$uid];
				$archives = $this->archiveService->getArchivesByArchivesIds($manager);
			}
		}elseif($type == 'latestSee'){
			$cookies = Yii::app()->request->getCookies();
			if(empty($cookies['view_archives'])) return $return;
			
			$archivesIds = explode(',', $cookies['view_archives']);
			$archives = $this->archiveService->getArchivesByArchivesIds($archivesIds);
			$filter = false;
		}else{
			return $return;
		}
		
		$return['total'] = count($archives);
		$sortService = new ChannelDoteySortService();
		if($filter){
			$sortService->filterArchives($archives,0);
			$sortService->buildLiveArchives($archives,$uid,0,true);
			$list = $sortService->sortLiveArchives($archives,CHANNEL_DOTEY_SORT_STARTTIME,0);
			$list = $list['living'];
		}else{
			$sortService->buildLiveArchives($archives,$uid,0,true);
			$list = $archives;
		}
		$return['list'] = $this->fillOnlineData($list);
		return $return;
	}
	
	/**
	 * 本月累计签到多少天
	 * @param int $uid
	 * @return int
	 */
	public function getCheckinDays($uid){
		$model = new UserCheckinModel();
		return $model->checkinDays($uid);
	}
	
	/**
	 * 获取月卡信息，即可判断是否拥有月卡
	 * @param int $uid
	 * @return array
	 */
	public function getMonthCard($uid){
		$monthCardId = 27; //月卡ID
		$userPropsService = new UserPropsService();
		$card = $userPropsService->getUserValidPropsOfBagByPropId($uid,$monthCardId,time());
		return $card[0];
	}
	
	/**
	 * 获取签到礼物道具列表
	 * @param int $uid
	 * @param boolean $detail 是否需要详细 的状态和图片的签到礼物道具信息
	 * @param array $monthCard
	 * @return array
	 */
	public function getCheckinItems($uid, $detail = true, $monthCard = array()){
		$userGiftService = new UserGiftService();
		$items[CHENKIN_NORMAL] = array(
			array(
				'name'	=> '三叶草',
				'type'	=> REWARD_GIFT,
				'id'	=> 45,
				'num'	=> 1,
				'text'	=> '',
				'pic'=>'/statics/fontimg/common/sanyecao.jpg'
			)
		);
		$items[CHENKIN_MONTHCARD] = array(
			array(
				'name'	=> '红玫瑰',
				'type'	=> REWARD_GIFT,
				'id'	=> 125,
				'num'	=> 3,
				'text'	=> '限拥有月卡的玩家可领，每天可免费领取3朵，也可至账户中心-我的礼品一次性领取全部90朵红玫瑰',
				'pic'=>'/statics/fontimg/common/hongmeigui.jpg'
			)
		);
		$items[CHENKIN_BROADCAST] = array(
			array(
				'name'	=> '每日广播',
				'type'	=> REWARD_PROPS,
				'id'	=> 45,
				'num'	=> 1,
				'text'	=> '限富豪8以上玩家可领，当日有效，过期自动消失<br/>主播签到可获得每日广播3个',
				'pic'=>'/statics/fontimg/common/xiaolaba.jpg'
			)
		);
		
		//主播可以领三个每日广播
		$userInfo = UserJsonInfoService::getInstance()->getUserInfo($uid, false);
		$isDotey = false;
		if($this->hasBit(intval($userInfo['ut']),USER_TYPE_DOTEY) && $userInfo['us']!=USER_STATUS_OFF){
			$items[CHENKIN_BROADCAST][0]['num'] = 3;
			$isDotey = true;
		}
		
		if($detail){
			$monthCardStatus = $broadcastStatus = true;
			//没有月卡或月卡已领完的情况
			if(empty($monthCard)) $monthCardStatus = -1;
			else{
				$etime = $monthCard['valid_time'];
				$stime = $etime - (30 * 86400);
				$etime = strtotime(date('Y-m-d', $etime).' 23:59:59');
				$num = $userGiftService->countMonthGift($uid, $stime, $etime);
				if($num < 1){
					$monthCardStatus = 0;
				}else{
					$monthCardStatus=1;
				} 
			}
			
			//等级不够富豪8的用户不能领每日广播的情况
			if(!$isDotey && $userInfo['rk'] < 8) $broadcastStatus = false;
			
			//查询是否已领取
			$model = new UserCheckinModel();
			$temp = $model->chekinRecords($uid, strtotime(date('Y-m-d').' 00:00:00'), strtotime(date('Y-m-d').' 23:59:59'));
			$records = array();
			foreach($temp as $t){
				$records[$t['type'].'_'.$t['reward_type'].'_'.$t['target_id']] = 1;
			}
			
			foreach($items as $type => &$item){
				foreach($item as &$it){
					//领取状态
					if($type == CHENKIN_MONTHCARD){
						if($monthCardStatus==1){
							$it['status'] =0;
							if(isset($records[$type.'_'.$it['type'].'_'.$it['id']])){
								$it['status'] =1;
							}
							
						}elseif($monthCardStatus==0){
							$it['status'] = 2; //本月已领完
						}else{
							$it['status'] = -1; //不能领取
						}
						
					}elseif($type == CHENKIN_BROADCAST && !$broadcastStatus){
						$it['status'] = -1; //不能领取
					}else{
						$it['status'] = 0; //未领取
						if(isset($records[$type.'_'.$it['type'].'_'.$it['id']])){
							$it['status'] = 1; //已领取
						}
					}
					
					//补充礼物道具图标
					if($it['type'] == REWARD_GIFT){
						$giftService = new GiftService();
						$gift = $giftService->getGiftByIds($it['id']);
						$gift = $gift[$it['id']];
						$it['image'] = '/gift/'.$gift['image'];
					}elseif($it['type'] == REWARD_PROPS){
						$propsService = new PropsService();
						$props = $propsService->getPropsByIds($it['id']);
						$props = $props[$it['id']];
						$it['image'] = $props['image'];
					}else{
						$it['image']  = '';
					}
				}
			}
		}
		return $items;
	}
	
	/**
	 * 签到单个礼物或道具
	 * @param int $uid
	 * @param string $itemKey 1_2的形式，就是getCheckinItems返回的两层数据的key，标识一个具体的礼物或道具
	 * @return boolean
	 */
	public function checkin($uid, $itemKey){
		$userGiftService = new UserGiftService();
		$key = explode('_', $itemKey);
		$type = $key[0];
		$itemId = $key[1];
		$items = $this->getCheckinItems($uid, false);
		$item = $items[$type][$itemId];
		
		//所有签到领礼的用户都判断未达到“平民2”等级的累计消费不到1皮蛋的用户弹出密保安全的提醒
		// 判断用户等级是否大于等于"平民2";
		$userInfo = UserJsonInfoService::getInstance()->getUserInfo($uid, false);
		$userService = new UserService();
		if($userInfo['rk'] < 1 ){
			// 判断是否为充值用户
			$consumeServ = new ConsumeService();
			$eggs = $consumeServ->getUserRechargeEggs($uid);
			if(!$eggs){
				// 判断消费皮蛋是否超过1皮蛋
				$soncumeEgg = $consumeServ->sumUserConsumeRecord($uid);
				if($soncumeEgg < 1){
					// 判断用户是否安全用户
					$users = $userService->getUserBasicByUids(array($uid));
					$user = $users[$uid];
					$pramas['email'] = $user['reg_email'];
					$pramas['mobile'] = isset($user['reg_mobile']) ? $user['reg_mobile'] : '';
					if($user['create_time'] >= (strtotime('2013-7-18 00:00:00')) && !$pramas['email'] && !$pramas['mobile']){
						return $this->setNotice(0, '请先设置安全邮箱或密保手机，才能签到领取免费礼物', false);
					}
				}
			}
		}
		
		//月卡礼物的单独判断，没有月卡或月卡已领完的情况弹出提示
		if($type == CHENKIN_MONTHCARD){
			$monthCard = $this->getMonthCard($uid);
			if(empty($monthCard)) return $this->setNotice(1, '请先购买月卡，才能签到领取月卡专有礼物或道具', false);
			else{
				$etime = $monthCard['valid_time'];
				$stime = $etime - (30 * 86400);
				$etime = strtotime(date('Y-m-d', $etime).' 23:59:59');
				$num = $userGiftService->countMonthGift($uid, $stime, $etime);
				if($num < 1) return $this->setNotice(2, '您的月卡礼物或道具已领取完', false);
			}
		}
			
		//广播礼物的单独判断，等级不够不能领每日广播的情况
		if($type == CHENKIN_BROADCAST){
			$userInfo = UserJsonInfoService::getInstance()->getUserInfo($uid, false);
			if(!($this->hasBit(intval($userInfo['ut']),USER_TYPE_DOTEY) && $userInfo['us']!=USER_STATUS_OFF) && $userInfo['rk'] < 8)
				return $this->setNotice(3, '您的用户等级不足，不能领取签到礼物或道具', false);
		}
		
		return $userGiftService->checkin($uid, $type, $item['type'], $item['id'], $item['num']);
	}
	
	/**
	 * 本日签到剩余的礼物或道具
	 * @param int $uid
	 * @return boolean
	 */
	public function checkinAll($uid){
		$items = $this->getCheckinItems($uid, false);
		
		//所有签到领礼的用户都判断未达到“平民2”等级的累计消费不到1皮蛋的用户弹出密保安全的提醒
		// 判断用户等级是否大于等于"平民2";
		$userInfo = UserJsonInfoService::getInstance()->getUserInfo($uid, false);
		$userService = new UserService();
		if($userInfo['rk'] < 1 ){
			// 判断是否为充值用户
			$consumeServ = new ConsumeService();
			$eggs = $consumeServ->getUserRechargeEggs($uid);
			if(!$eggs){
				// 判断消费皮蛋是否超过1皮蛋
				$soncumeEgg = $consumeServ->sumUserConsumeRecord($uid);
				if($soncumeEgg < 1){
					// 判断用户是否安全用户
					$users = $userService->getUserBasicByUids(array($uid));
					$user = $users[$uid];
					$pramas['email'] = $user['reg_email'];
					$pramas['mobile'] = isset($user['reg_mobile']) ? $user['reg_mobile'] : '';
					if($user['create_time'] >= (strtotime('2013-7-18 00:00:00')) && !$pramas['email'] && !$pramas['mobile']){
						return $this->setNotice(0, '请先设置安全邮箱或密保手机，才能签到领取免费礼物', false);
					}
				}
			}
		}
		
		$userGiftService = new UserGiftService();
		$flag = false;
		foreach($items as $type => $item){
			foreach($item as $it){
				//月卡礼物的单独判断，没有月卡或月卡已领完的情况弹出提示
				if($type == CHENKIN_MONTHCARD){
					$monthCard = $this->getMonthCard($uid);
					if(empty($monthCard)) continue;
					else{
						$etime = $monthCard['valid_time'];
						$stime = $etime - (30 * 86400);
						$etime = strtotime(date('Y-m-d', $etime).' 23:59:59');
						$num = $userGiftService->countMonthGift($uid, $stime, $etime);
						if($num < 1) continue;
					}
				//广播礼物的单独判断，等级不够不能领每日广播的情况
				}elseif($type == CHENKIN_BROADCAST){
					$userInfo = UserJsonInfoService::getInstance()->getUserInfo($uid, false);
					if(!($this->hasBit(intval($userInfo['ut']),USER_TYPE_DOTEY) && $userInfo['us']!=USER_STATUS_OFF) && $userInfo['rk'] < 8)
						continue;
				}
				$flag = $userGiftService->checkin($uid, $type, $it['type'], $it['id'], $it['num']);
			}
		}
		return $flag;
	}
	
	/**
	 * 获取全站在线玩家总数
	 * @return int
	 */
	public function getOnlineCount(){
		$web = new WebConfigService();
		return $web->getOnlineCount();
	}
	
	/**
	 * 返回皇冠主播、蓝钻主播、红心主播在线人数
	 * @return array
	 */
	public function getDoteyRankCount(){
		$online = OtherRedisModel::getInstance()->getOnlieCount();
		$return = array('皇冠主播' => 0, '蓝钻主播' => 0, '红心主播' => 0);
		if(isset($online['dotey_rank_online'])){
			$return['皇冠主播'] = $online['dotey_rank_online']['crown_dotey_total'];
			$return['蓝钻主播'] = $online['dotey_rank_online']['blue_dotey_total'];
			$return['红心主播'] = $online['dotey_rank_online']['red_dotey_total'];
		}
		return $return;
	}
	
	/**
	 * 返回印象标签的在线人数
	 */
	public function getAllTags(){
		$tags = DoteyTagsService::getInstance()->getAllTags();
		$online = OtherRedisModel::getInstance()->getOnlieCount();
		if(empty($tags)) return array();
		foreach($tags as &$t){
			$t['user_count'] = isset($online['dotey_tag_online']['dotey_tag_'.$t['tag_id']]) ? $online['dotey_tag_online']['dotey_tag_'.$t['tag_id']] : 0;
		}
		return $tags;
	}
	
	/**
	 * 获取首页顶部换一批的那个动态图数据
	 * @return array
	 */
	public function getDynamicDotey($operate = array(), $limit = 6){
		if(empty($operate)){
			$operate = $this->getOperateData();
			$operate = $operate[CATEGORY_INDEX_DOTEY_RECOMMAND];
		}
		if(empty($operate)) return array();
		$recommand = $this->archiveService->buildDataByIndex($operate,'target_id');
		$uids = array_keys($recommand);
		$temp = $this->archiveService->getArchivesByUids($uids,true,0);
		$temp = $this->fillArchivesData($temp);
		$image = new PipiImageUpload();
		
		$archives = array('living' => array(), 'wait' => array());
		foreach($temp as $arch){
			$arch['sort'] = $recommand[$arch['uid']]['sort'];
			$arch['nickname'] = $recommand[$arch['uid']]['content']['nickname'];
			$arch['live_time'] = $arch['live_record']['live_time'];
			$arch['live_desc'] = PipiDate::getLastDate(intval($arch['live_time']), time(), 'Y-m-d H:i');
			$arch['status'] = $arch['live_record']['status'];
			$arch['sub_title'] = $arch['live_record']['sub_title'];
			unset($arch['live_record']);
			unset($arch['cat_id']);
			unset($arch['sub_id']);
			unset($arch['recommond']);
			unset($arch['video']);
			unset($arch['background']);
			unset($arch['is_hide']);
			unset($arch['notice']);
			unset($arch['private_notice']);
			$image->setDir($arch['uid'], 'dotey', 'dynamic_small');
			$arch['dynamic_small'] = $image->getFileUrl();
			$image->setDir($arch['uid'], 'dotey', 'dynamic_middle');
			$arch['dynamic_middle'] = $image->getFileUrl();
			$image->setDir($arch['uid'], 'dotey', 'dynamic_big');
			$arch['dynamic_big'] = $image->getFileUrl();
			if($arch['status'] == 1){
				$archives['living'][] = $arch;
			}elseif($arch['status'] == 0){
				$archives['wait'][] = $arch;
			}
		}
		
		$return = array();
		//如果存在正在直播的则直接取出
		$count=count($archives['living']);
		if(isset($archives['living']) && $count > 0){
			$num = $count <= $limit ? $count : $limit;
			shuffle($archives['living']);
			$return = array_slice($archives['living'],0,$num);
		}
		//正在直播中小于3，随机从待直播中取
		$sCount = count($return);
		if($sCount < $limit && isset($archives['wait']) && count($archives['wait'])>0){
			$wcount = count($archives['wait']);
			$ycount = $limit - $sCount;
			if($wcount >= $ycount){
				$num = $ycount;
			}else{
				$num = $wcount;
			}
			shuffle($archives['wait']);
			$return2 = array_slice($archives['wait'],0,$num);
			$return = array_merge($return, $return2);
		}
		return $return;
	}
	
	/**
	 * 获得在直播数据
	 * @return array
	 */
	public function getLivingArchives(){
		$uid = Yii::app()->user->id;
		$living = $this->archiveService->getLivingArchives($uid,true,!Yii::app()->user->isGuest);
		$this->archiveService->addStarSingerForArchives($living);
		$this->fillArchivesData($living['living']);
		return $living;
	}
	
	/**
	 * 获得待直播数据
	 * @return array
	 */
	public function getWillLiveArchives(){
		$uid = Yii::app()->user->id;
		$will = $this->archiveService->getWillLiveArchives($uid,true,!Yii::app()->user->isGuest);
		$this->archiveService->addStarSingerForArchives($will);
		$this->fillTagsData($will['wait']);
		$this->fillUserInfo($will['wait']);
		return $will;
	}
	
	/**
	 * 取得主播今日推荐数据
	 * @return array
	 */
	public function getAllTodayRecommand(){
		$uid = Yii::app()->user->id;
		$archives = $this->archiveService->getAllTodayRecommand($uid, true, !Yii::app()->user->isGuest);
		$this->fillOnlineData($archives['living']);
		$this->fillUserInfo($archives['living']);
		return $archives;
	}
	
	/**
	 * 给在直播列表添加是否今日推荐的标识
	 * @param array $archives
	 * @param array $todayRecommand
	 * @return array
	 */
	public function addTodayRecommandForArchives(array &$archives, $todayRecommand = array()){
		if(isset($archives['living']) && count($archives['living'])>0){
			$todayRecommand = $this->buildDataByIndex($todayRecommand['living'], 'archives_id');
			foreach($archives['living'] as &$arch){
				if(isset($todayRecommand[$arch['archives_id']])){
					$arch['today_recommand']=true;
				}else{
					$arch['today_recommand']=false;
				}
			}
		}
		return $archives;
	}
	
	/**
	 * 在直播列表删除今日推荐中包含的数据
	 * @param array $archives
	 * @param array $todayRecommand
	 * @return array
	 */
	public function deleteTodayRecommandForArchives(array &$archives, $todayRecommand = array()){
		if(isset($archives['living']) && count($archives['living'])>0){
			$todayRecommand = $this->buildDataByIndex($todayRecommand['living'], 'archives_id');
			foreach($archives['living'] as $key => $arch){
				if(isset($todayRecommand[$arch['archives_id']])){
					unset($archives[$key]);
				}
			}
		}
		return $archives;
	}
	
	/**
	 * 取得点唱专区的主播
	 * @return array
	 */
	public function getDoteyArchivesOfSong(){
		$doteyChannelModel =  DoteyChannelModel::model();
		$doteys = $doteyChannelModel->getDoteysOfSong(1,1);
		if(empty($doteys)) return array();
		$doteys = $this->arToArray($doteys);
		$uids = array_keys($this->buildDataByIndex($doteys,'uid'));
		$archives = $this->archiveService->getArchivesByUids($uids,true);
		$archives = $this->channelDoteySort->filterArchives($archives);
		$archives = $this->channelDoteySort->buildLiveArchives($archives, Yii::app()->user->id, 0, true);
		$archives = $this->fillArchivesData($archives);
		$return = array('living' => array(), 'wait' => array());
		foreach($archives as $k => $arch){
			if($arch['status'] == 1){
				$return['living'][$k] = $arch;
			}elseif($arch['status'] == 0){
				$return['wait'][$k] = $arch;
			}
		}
		return $return;
	}
	
	/**
	 * 首页显示直播档期处按需填补需要的数据
	 * @param array $archives
	 * @param array $options
	 * @return array
	 */
	public function fillArchivesData(array &$archives, $options = array()){
		$this->fillOnlineData($archives);
		$this->fillTagsData($archives);
		$this->fillUserInfo($archives);
		
		return $archives;
	}
	
	/**
	 * 填补在线数据
	 * @param array $archives
	 * @return array
	 */
	private function fillOnlineData(&$archives){
		$userListService=new UserListService();
		$archivesIds = array_keys($this->buildDataByIndex($archives, 'archives_id'));
		$online = $userListService->getArchivesOnlineNumByArchivesIds($archivesIds);
		foreach($archives as &$arch){
			$arch['user_total'] = empty($online[$arch['archives_id']]) ? 0 : $online[$arch['archives_id']];
		}
		return $archives;
	}
	
	/**
	 * 填补标签数据
	 * @param array $archives
	 * @return array
	 */
	private function fillTagsData(&$archives){
		$uids = array_keys($this->buildDataByIndex($archives, 'uid'));
		$tags = DoteyTagsService::getInstance()->getTagsByUids($uids);
		foreach($archives as &$arch){
			$arch['tags'] = isset($tags[$arch['uid']]) ? $tags[$arch['uid']] : array();
		}
		return $archives;
	}
	
	/**
	 * 填补关注数据
	 * @param array $archives
	 * @return array
	 */
	private function fillAttentionData(&$archives){
		$uid = Yii::app()->user->id;
		$weiboService = new WeiboService();
		$attentions = array();
		if($uid){
			$attentions = $weiboService->getDoteyAttentionsByUid($uid);
			$attentions = $this->buildDataByIndex($attentions,'uid');
		}
		foreach($archives as &$arch){
			if(isset($attentions[$archive['uid']])){
				$archive['is_attention'] = 1;
			}else{
				$archive['is_attention'] = 0;
			}
		}
		return $archives;
	}
	
	/**
	 * 填补userinfo数据
	 * @param array $archives
	 * @return array
	 */
	private function fillUserInfo(&$archives){
		$uids = array_keys($this->buildDataByIndex($archives, 'uid'));
		$userInfos = UserJsonInfoService::getInstance()->getUserInfos($uids, false);
		foreach($archives as &$arch){
			$arch['nickname']	= $userInfos[$arch['uid']]['nk'];
			$arch['dotey_rank'] = $userInfos[$arch['uid']]['dk'];
		}
		return $archives;
	}
	
	/**
	 * 主播点唱排行
	 * @return array
	 */
	public function getDoteySongsRank(){
		$songsService = new DoteySongService();
		$songs['today'] =  $songsService->getDoteySongsRank('today',0);
		$songs['week'] =   $songsService->getDoteySongsRank('week',0);
		$songs['month'] =  $songsService->getDoteySongsRank('month',0);
// 		$songs['super'] =  $songsService->getDoteySongsRank('super',0);
		return $songs;
	}
	
	/**
	 * 用户点唱排行
	 * @return array
	 */
	public function getUserSongsRank(){
		$songsService = new DoteySongService();
		$songs['today'] =  $songsService->getUserSongsRank('today',1);
		$songs['week'] =   $songsService->getUserSongsRank('week',1);
		$songs['month'] =  $songsService->getUserSongsRank('month',1);
// 		$songs['super'] =  $songsService->getUserSongsRank('super',1);
		return $songs;
	}
	
	/**
	 * 获取分类下的所有主播
	 * @param int $type 1皇冠主播，2蓝钻主播，3红心主播
	 * @return array
	 */
	public function getDoteyByRank($type){
		$uids = DoteyBaseModel::model()->getAllDoteyUids();
		$consume = new ConsumeModel();
		$cr = $consume->getCommandBuilder()->createCriteria();
		$cr->select = 'uid';
		$cr->addInCondition('uid', $uids);
		if($type == 1){
			$cr->addCondition('dotey_rank >= 11');
		}elseif($type == 2){
			$cr->addCondition('dotey_rank >= 6 and dotey_rank < 11');
		}else{
			$cr->addCondition('dotey_rank < 6');
		}
		$doteys = $consume->findAll($cr);
		$doteys = $this->arToArray($doteys);
		$doteyUids = array_keys($this->buildDataByIndex($doteys, 'uid'));
		$archives = $this->archiveService->getArchivesByUids($doteyUids, true);
		$archives = $this->channelDoteySort->filterArchives($archives);
		$archives = $this->channelDoteySort->buildLiveArchives($archives, Yii::app()->user->id, 0, true);
		$archives = $this->fillArchivesData($archives);
		$return = array('living' => array(), 'wait' => array());
		foreach($archives as $k => $arch){
			if($arch['status'] == 1){
				$return['living'][$k] = $arch;
			}elseif($arch['status'] == 0){
				$return['wait'][$k] = $arch;
			}
		}
		return $return;
	}
	
	/**
	 * 获取主播印象标签的主播
	 * @param int $tagId
	 * @return Ambigous <multitype:multitype: , unknown>
	 */
	public function getDoteyByTag($tagId){
		$uids = DoteyTagsService::getInstance()->getUidsByTag($tagId, 10000);
		$archives = $this->archiveService->getArchivesByUids($uids, true);
		$archives = $this->channelDoteySort->filterArchives($archives);
		$archives = $this->channelDoteySort->buildLiveArchives($archives, Yii::app()->user->id, 0, true);
		$archives = $this->fillArchivesData($archives);
		$return = array('living' => array(), 'wait' => array());
		foreach($archives as $k => $arch){
			if($arch['status'] == 1){
				$return['living'][$k] = $arch;
			}elseif($arch['status'] == 0){
				$return['wait'][$k] = $arch;
			}
		}
		return $return;
	}
}