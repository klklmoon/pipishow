<?php
/**
 * 快乐星期六常设活动业务逻辑服务层
 * 
 * @author zhangzhifan <zhangzhifan@pipi.cn> 2010-11-2
 * @link http://show.pipi.cn
 * @copyright Copyright &copy; 2003-2010 show.pipi.cn
 * @license
 */

class HappySaturdayService extends PipiService{
	
	const ACTIVITY_NAME='快乐星期六';	
	const JIN_YUAN_BAO_ID=124;		//金元宝限量礼物id
	const JIN_YUAN_BAO_NUM=5;		//金元宝数量
	const SMALL_MAMMON_ID=8;		//小财神勋章id
	const ORDINARY_GIFT_BAG_PIPIEGG_NUM=50;			//普通礼包领取条件
	
	const JADE_CABBAGE_ID=168;		//玉白菜限量礼物id
	const JADE_CABBAGE_NUM=5;		//玉白菜数量
	const BIG_MAMMON_ID=19;			//大财神勋章id
	const ADVANCED_GIFT_BAG_PIPIEGG_NUM=500;			//升级版礼包领取条件	
	
	const MEDAL_PERIOD_OF_VALIDITY=86400;	//有效期秒数
	const SATURDAY=6;					//代表星期六的整数，测试期可调整
	
	/**
	 * 查询快乐星期六礼包
	 * @param int $uid 领取人id
	 * @param int $gift_id 礼物id
	 * @param int $stime 检测礼包起始时间戳
	 * @param int $etime 检测礼包终止时间戳
	 * @return array 返回礼包礼物统计数据
	 */
	private function getGiftBag($uid,$gift_id,$stime,$etime)
	{
		//快乐星期六礼包是否已经领取过了
		$command=Yii::app()->db_consume_records->createCommand();
		$userBagStat=$command->select('count(*) as bag_gift_record_counts, sum(num) as bag_gift_num')
		->from("{{user_bag_records}}")
		->where('uid=:uid and gift_id=:gift_id and source=:source and sub_source=:sub_source'
			.' and create_time>=:start_time and create_time<=:end_time',
			array(
				':uid'=>$uid,
				':gift_id'=>$gift_id,
				':source'=>'4',			//4表示web_user_bag_records.source为活动
				':sub_source'=>'HappySaturday',	//HappySaturday表示web_user_bag_records.sub_source为快乐星期六，同时web_user_bag_records.source必须为4
				':start_time'=>$stime,
				':end_time'=>$etime
			))
			->queryRow();
		return $userBagStat;
	}
	
	
	/**
	 * 领取活动礼包
	 * @param int $uid 领取人id
	 * @param int $gift_id 礼物id
	 * @param int $gift_num 礼包礼物数和检测数量
	 * @param int $stime 检测礼包起始时间戳
	 * @param int $etime 检测礼包终止时间戳
	 * @return int 返回值为1表示领取成功
	 */	
	protected function saveActivityGiftBag($uid,$gift_id,$gift_num,$stime,$etime)
	{
		$result=0;
			
		//将用户领取信息写入背包
		$giftBagService=new GiftBagService();
		$gifts=array();
		$gifts['uid']=$uid;
		$gifts['gift_id']=$gift_id;
		$gifts['num']=$gift_num;
			
		$userService=new UserService();
		$userInfo=$userService->getUserFrontsAttributeByCondition($uid,true);
		$giftService=new GiftService();
		$gift=$giftService->getGiftByIds($gift_id);
		$records=array();
		$records['info']=serialize(array('uid'=>$uid,
			'nickname'=>$userInfo['nk'],
			'gift_id'=>$gift_id,
			'gift_name'=>$gift[$gift_id]['zh_name'],
			'num'=>$gift_num,
			'remark'=>self::ACTIVITY_NAME));
		$records['source']=4;		//4表示web_user_bag_records.source为活动
		$records['sub_source']='HappySaturday';		//HappySaturday表示web_user_bag_records.sub_source为快乐星期六，同时web_user_bag_records.source必须为4
		
		$giftBagService=new GiftBagService();
		$recordId=$giftBagService->saveUserGiftBagByUid($gifts,$records);
		if($recordId<=0){
			$result=-5;		//写入失败
		}
		else
		{
			$result=1;		//领取成功
		}

		return $result;
	}
	
	/**
	 * 存储活动礼包中的勋章
	 * @param int $uid 领取人id
	 * @param int $medal_id 勋章id
	 * @param int $stime 检测礼包起始时间戳
	 * @param int $etime 检测礼包终止时间戳
	 * @return int 返回值为1表示领取成功
	 */	
	protected function saveActivityMedal($uid,$medal_id,$stime)
	{
		//构造勋章数据
		$userMedalService=new UserMedalService();
		$ctime=time();
		
		$userMedalData=array(
			'mid'=>$medal_id,
			'uid'=>$uid,
			'type'=>2,
		);
		$userMedalBig=$userMedalService->getUserMedalByUid($uid,2,self::BIG_MAMMON_ID);
		$userMedalSmall=$userMedalService->getUserMedalByUid($uid,2,self::SMALL_MAMMON_ID);
		
		if(isset($userMedalBig[0]['rid']))		//已有大财神记录
		{
			//大财神记录是上周的，则更新有效期
			if($userMedalBig[0]['vtime']<$stime)
			{
				$userMedalData['rid']=$userMedalBig[0]['rid'];
				$userMedalData['vtime']=$ctime+self::MEDAL_PERIOD_OF_VALIDITY;
			}
			else
			{
				//大财神记录是本周的，则维持不变
				return ;
			}
		}
		elseif(isset($userMedalSmall[0]['rid']))		//已有小账神记录
		{
			//小账神记录是上周的，则更新有效期
			if($userMedalSmall[0]['vtime']<$stime)
			{
				$userMedalData['rid']=$userMedalSmall[0]['rid'];
				$userMedalData['vtime']=$ctime+self::MEDAL_PERIOD_OF_VALIDITY;
			}
			elseif($userMedalSmall[0]['vtime']>$stime && self::BIG_MAMMON_ID==$medal_id)	
			{
				//小账神记录是本周的，领取的是大财神，则升级为大财神同时更新有效期
				$userMedalData['rid']=$userMedalSmall[0]['rid'];
				$userMedalData['vtime']=$ctime+self::MEDAL_PERIOD_OF_VALIDITY;
			}
			else
			{
				return ;
			}
		}
		else
		{
			//如果没有任何快乐星期六专用勋章记录，则新建勋章记录
			$userMedalData['ctime']=$ctime;
			$userMedalData['vtime']=$ctime+self::MEDAL_PERIOD_OF_VALIDITY;
		}
		
		//print_r($userMedalSmall);exit;
		//将用户礼包中的勋章写入勋章发放表
		$userMedalService->saveUserMedal($userMedalData);
	}
	
	/**
	 * 领取普通礼包
	 * @param int $uid 领取人id
	 * @return int 返回值为1表示领取成功
	 */
	public function receiveOrdinaryGiftBag($uid)
	{
		$result=0;
		$todayNum=date("w");
		$today=date("Y-m-d");
		$stime=strtotime($today."00:00:00");
		$etime=strtotime($today."23:59:59");
		//当天是不是星期六
		if($todayNum!=self::SATURDAY)
		{
			return -2;		//当天不是星期六，不能领取普通礼包
		}
		else
		{
			//充值皮蛋有没有满50
			$pipiEggs=UserRechargeRecordsModel::model()->getUserPipiEggsByTime($uid,$stime,$etime);
			$changeRelation = Yii::app()->params->change_relation;
			$egg = self::ORDINARY_GIFT_BAG_PIPIEGG_NUM*isset($changeRelation['rmb_to_pipiegg'])?$changeRelation['rmb_to_pipiegg']:1;
			if($pipiEggs<$egg)
			{
				return -3;		//充值不满50个皮蛋，不能领取普通礼包
			}
			else
			{
				//检测普通礼包
				$ordinaryGiftBagStat=$this->getGiftBag($uid,self::JIN_YUAN_BAO_ID,$stime,$etime);
				if($ordinaryGiftBagStat['bag_gift_num']>=self::JIN_YUAN_BAO_NUM || $ordinaryGiftBagStat['bag_gift_record_counts']>0 )
				{
					return -4;		//普通礼包已经领取过了，不能再次领取礼包
				}
				else
				{
					//领取小财神
					$this->saveActivityMedal($uid,self::SMALL_MAMMON_ID,$stime);
					//领取普通礼包
					$result=$this->saveActivityGiftBag($uid,self::JIN_YUAN_BAO_ID,self::JIN_YUAN_BAO_NUM,$stime,$etime);
				}
			}
		}
		return $result;
	}

	/**
	 * 领取升级版礼包
	 * @param int $uid 领取人id
	 * @return int 返回值为1表示领取成功
	 */
	public function receiveAdvancedGiftBag($uid)
	{
		$result=0;
		$todayNum=date("w");
		$today=date("Y-m-d");
		$stime=strtotime($today."00:00:00");
		$etime=strtotime($today."23:59:59");
		//当天是不是星期六
		if($todayNum!=self::SATURDAY)
		{
			return -2;		//当天不是星期六，不能领取高级礼包
		}
		else
		{
			//星期六累积充值皮蛋数
			$saturdayPipiEggs=UserRechargeRecordsModel::model()->getUserPipiEggsByTime($uid,$stime,$etime);
			
			//周一至周五累积充值皮蛋数
			$weekdayStime=$stime-(86400*5);
			$weekdayEtime=$stime-1;
			$weekdayPipiEggs=UserRechargeRecordsModel::model()->getUserPipiEggsByTime($uid,$weekdayStime,$weekdayEtime);
			
			$changeRelation = Yii::app()->params->change_relation;
			$egg = self::ORDINARY_GIFT_BAG_PIPIEGG_NUM*isset($changeRelation['rmb_to_pipiegg'])?$changeRelation['rmb_to_pipiegg']:1;
			$egg2 = self::ADVANCED_GIFT_BAG_PIPIEGG_NUM*isset($changeRelation['rmb_to_pipiegg'])?$changeRelation['rmb_to_pipiegg']:1;
			//星期六充值皮蛋有没有满50
			if($saturdayPipiEggs>=$egg && $weekdayPipiEggs>=$egg2)
			{
				//检测高级礼包
				$advancedGiftBagStat=$this->getGiftBag($uid,self::JADE_CABBAGE_ID,$stime,$etime);
				if($advancedGiftBagStat['bag_gift_num']>=self::JADE_CABBAGE_NUM || $advancedGiftBagStat['bag_gift_record_counts']>0 )
				{
					return -4;		//高级礼包已经领取过了，不能再次领取礼包
				}
				else
				{
					//领取大财神
					$this->saveActivityMedal($uid,self::BIG_MAMMON_ID,$stime);
					//领取升级版礼包
					$result=$this->saveActivityGiftBag($uid,self::JADE_CABBAGE_ID,self::JADE_CABBAGE_NUM,$stime,$etime);
				}
			}
			else
			{
				if($saturdayPipiEggs<$egg )
				{
					return -3;		//星期六充值不满50个皮蛋，不能领取升级版礼包
				}
				elseif ($weekdayPipiEggs<$egg2)
				{
					return -6;		//周一至周五充值不满500，不能领取升级版礼包
				}
				else
				{
					$result=0;
				}
			}
		}
		return $result;
	}	
	
}