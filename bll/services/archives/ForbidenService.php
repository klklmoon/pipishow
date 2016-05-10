<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: lei wei $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z lei wei  $
 * @package service
 * @subpackage archives
 */
class ForbidenService extends PipiService {


	public function forbidenOperate($archives_id, $from_uid,$to_uid,$to_nickname,$type, $period=30) {
		//从redis中获取用户的所有信息
		$usersInfo=new UserJsonInfoService();
		$from_user_data=$usersInfo->getUserInfo($from_uid,false);
		
		// 获取操作者的rank和purviewrank
		$from_nickname=str_replace('|','',$from_user_data['nk']);
		$from_rank = $from_user_data['rk'];
		$from_purviewrank = $this->getPurviewRank($archives_id, $from_user_data['pk']);
		$from_dotey_rank = $from_user_data['dk'];

		// 获取操作对象的rank和purviewrank
		if($to_uid>0){
			$to_user_data=$usersInfo->getUserInfo($to_uid,false);
			$to_nickname=str_replace('|','',$to_user_data['nk']);
			$to_rank = $to_user_data['rk'];
			$to_purviewrank = $this->getPurviewRank($archives_id, $to_user_data['pk']);;
		}else{
			$to_rank=0;
			$to_purviewrank=0;
		}


		// 获取操作者家族守护信息
		$from_guardian_type = 0;
		if ($from_purviewrank == 2) {
			// 房管才能拥有家族守护特权
			$guardian_info = isset($from_user_data['gd'])?$from_user_data['gd']:null;
			if (!empty($guardian_info)) {
				$from_guardian_type = $guardian_info['rk'];
			}
		}
		$to_vip_type=0;
		// 获取操作对象VIP信息
		if(isset($to_user_data['vip']['us'])&&$to_user_data['vip']['us']==0){
			if(isset($to_user_data['vip']['vt'])&&($to_user_data['vip']['vt']>time()||$to_user_data['vip']['vt']==0)){
				$to_vip_type =$to_user_data['vip']['t'];
			}
		}
		// 根据不同禁言操作分别判断
		$is_success = false;
		switch ($type) {
			case 0: // 解除禁IP
			case 1: // 禁IP
				if ($from_guardian_type <= 0) {
					if ($to_vip_type <= 0) { // 既无守护也无VIP
						if ($from_purviewrank >= 2 && $from_purviewrank > $to_purviewrank) {
							$is_success = true;
						} else {
							$is_success = false;
						}
					} else { // 无守护，有VIP
						if ($to_vip_type == 1) { // 黄VIP，只能防被房管禁
							if ($from_purviewrank <= 2 || $from_purviewrank <= $to_purviewrank) {
								$is_success = false;
							} else {
								$is_success = true;
							}
						} elseif ($to_vip_type == 2) { // 紫VIP，防房管禁，防主播禁根据等级不同而不同
							if ($from_purviewrank <= 2 || $from_purviewrank <= $to_purviewrank) {
								$is_success = false;
							} else {
								if ($from_purviewrank == 3) { // 主播，根据主播等级判断
									if ($from_dotey_rank < 13) { // 皇冠3以下(不含)
										if ($to_rank < 23) { // 国师以下(不含)，无法防御
											$is_success = true;
										} else {
											$is_success = false;
										}
									} elseif ($from_dotey_rank < 16) { // 皇冠3以上(含)，皇冠6以下(不含)
										if ($to_rank < 25) { // 郡王以下(不含)，无法防御
											$is_success = true;
										} else {
											$is_success = false;
										}
									} else { // 皇冠6以上(含)
										if ($to_rank < 28) { // 国王以下(不含)，无法防御
											$is_success = true;
										} else {
											$is_success = false;
										}
									}
								} else { // 管理员无敌
									$is_success = true;
								}
							}
						}
					}
				} else {
					if ($to_vip_type <= 0) { // 有守护，无VIP，同无守护无VIP
						if ($from_purviewrank >= 2 && $from_purviewrank > $to_purviewrank) {
							$is_success = true;
						} else {
							$is_success = false;
						}
					} else { // 有守护，有VIP
						if ($to_vip_type == 1) { // 黄VIP，只能防低一级的初级守护
							if ($from_purviewrank <= 1 || $from_purviewrank <= $to_purviewrank) {
								$is_success = false;
							} elseif ($from_purviewrank == 2) { // 房管
								if ($from_guardian_type == 1) { // 初级守护
									if ($from_rank >= $to_rank) {
										$is_success = true;
									} else { // 防低一级及以下的初级守护
										$is_success = false;
									}
								} else {
									$is_success = true;
								}
							} else {
								$is_success = true;
							}
						} elseif ($to_vip_type == 2) { // 紫VIP，防守护根据等级不同而不同，防主播禁根据等级不同而不同
							if ($from_purviewrank <= 1 || $from_purviewrank <= $to_purviewrank) {
								$is_success = false;
							} else {
								if ($from_purviewrank == 2) {
									if ($from_guardian_type == 1) { // 防初级守护
										$is_success = false;
									} elseif ($from_guardian_type == 2) { // 高级守护
										if ($from_rank >= $to_rank) {
											$is_success = true;
										} else { // 防低一级及以下的高级守护
											$is_success = false;
										}
									} else { // 超级守护
										if ($to_rank < 28) { // 国王以下(不含)
											if ($from_rank + 1 >= $to_rank) {
												$is_success = true;
											} else { // 防低两级及以下的超级守护
												$is_success = false;
											}
										} else { // 国王以上(含)
											if ($from_rank >= $to_rank) {
												$is_success = true;
											} else { // 防低一级及以下的超级守护
												$is_success = false;
											}
										}
									}
								} elseif ($from_purviewrank == 3) { // 主播，根据主播等级判断
									if ($from_dotey_rank < 13) { // 皇冠3以下(不含)
										if ($to_rank < 23) { // 国师以下(不含)，无法防御
											$is_success = true;
										} else {
											$is_success = false;
										}
									} elseif ($from_dotey_rank < 16) { // 皇冠3以上(含)，皇冠6以下(不含)
										if ($to_rank < 25) { // 郡王以下(不含)，无法防御
											$is_success = true;
										} else {
											$is_success = false;
										}
									} else { // 皇冠6以上(含)
										if ($to_rank < 28) { // 国王以下(不含)，无法防御
											$is_success = true;
										} else {
											$is_success = false;
										}
									}
								} else { // 管理员无敌
									$is_success = true;
								}
							}
						}
					}
				}
				break;
			case 2: // 解除禁止游客
			case 3: // 禁止游客
			case 4: // 解除禁言
			case 5: // 禁言
				if ($from_guardian_type <= 0) {
					if ($to_vip_type <= 0) { // 既无守护也无VIP
						if ($from_purviewrank >= 2 && $from_purviewrank > $to_purviewrank) {
							$is_success = true;
						} else {
							$is_success = false;
						}
					} else { // 无守护，有VIP
						if ($to_vip_type == 1) { // 黄VIP，只能防被房管禁
							if ($from_purviewrank <= 2 || $from_purviewrank <= $to_purviewrank) {
								$is_success = false;
							} else {
								$is_success = true;
							}
						} elseif ($to_vip_type == 2) { // 紫VIP，防房管禁，防主播禁根据等级不同而不同
							if ($from_purviewrank <= 2 || $from_purviewrank <= $to_purviewrank) {
								$is_success = false;
							} else {
								if ($from_purviewrank == 3) { // 主播，根据主播等级判断
									if ($from_dotey_rank < 13) { // 皇冠3以下(不含)
										if ($to_rank < 23) { // 国师以下(不含)，无法防御
											$is_success = true;
										} else {
											$is_success = false;
										}
									} elseif ($from_dotey_rank < 16) { // 皇冠3以上(含)，皇冠6以下(不含)
										if ($to_rank < 25) { // 郡王以下(不含)，无法防御
											$is_success = true;
										} else {
											$is_success = false;
										}
									} else { // 皇冠6以上(含)
										if ($to_rank < 28) { // 国王以下(不含)，无法防御
											$is_success = true;
										} else {
											$is_success = false;
										}
									}
								} else { // 管理员无敌
									$is_success = true;
								}
							}
						}
					}
				} else {
					if ($to_vip_type <= 0) { // 有守护，无VIP，同无守护无VIP
						if ($from_purviewrank >= 2 && $from_purviewrank > $to_purviewrank) {
							$is_success = true;
						} else {
							$is_success = false;
						}
					} else { // 有守护，有VIP
						if ($to_vip_type == 1) { // 黄VIP，只能防低一级的初级守护
							if ($from_purviewrank <= 1 || $from_purviewrank <= $to_purviewrank) {
								$is_success = false;
							} elseif ($from_purviewrank == 2) { // 房管
								if ($from_guardian_type == 1) { // 初级守护
									if ($from_rank >= $to_rank) {
										$is_success = true;
									} else { // 防低一级及以下的初级守护
										$is_success = false;
									}
								} else {
									$is_success = true;
								}
							} else {
								$is_success = true;
							}
						} elseif ($to_vip_type == 2) { // 紫VIP，防守护根据等级不同而不同，防主播禁根据等级不同而不同
							if ($from_purviewrank <= 1 || $from_purviewrank <= $to_purviewrank) {
								$is_success = false;
							} else {
								if ($from_purviewrank == 2) {
									if ($from_guardian_type == 1) { // 防初级守护
										$is_success = false;
									} elseif ($from_guardian_type == 2) { // 高级守护
										if ($from_rank >= $to_rank) {
											$is_success = true;
										} else { // 防低一级及以下的高级守护
											$is_success = false;
										}
									} else { // 超级守护
										if ($to_rank < 28) { // 国王以下(不含)
											if ($from_rank + 1 >= $to_rank) {
												$is_success = true;
											} else { // 防低两级及以下的超级守护
												$is_success = false;
											}
										} else { // 国王以上(含)
											if ($from_rank >= $to_rank) {
												$is_success = true;
											} else { // 防低一级及以下的超级守护
												$is_success = false;
											}
										}
									}
								} elseif ($from_purviewrank == 3) { // 主播，根据主播等级判断
									if ($from_dotey_rank < 13) { // 皇冠3以下(不含)
										if ($to_rank < 23) { // 国师以下(不含)，无法防御
											$is_success = true;
										} else {
											$is_success = false;
										}
									} elseif ($from_dotey_rank < 16) { // 皇冠3以上(含)，皇冠6以下(不含)
										if ($to_rank < 25) { // 郡王以下(不含)，无法防御
											$is_success = true;
										} else {
											$is_success = false;
										}
									} else { // 皇冠6以上(含)
										if ($to_rank < 28) { // 国王以下(不含)，无法防御
											$is_success = true;
										} else {
											$is_success = false;
										}
									}
								} else { // 管理员无敌
									$is_success = true;
								}
							}
						}
					}
				}
				break;
			case 6: // 解除房管身份
				if ($from_purviewrank >= 3) {
					$is_success = true;
				} else {
					$is_success = false;
				}
				break;
			case 7://设为房管身份
				if ($from_purviewrank >= 3) {
					$is_success = true;
				} else {
					$is_success = false;
				}
				break;
			case 8: // 踢出房间
				if ($from_guardian_type <= 0) {
					if ($to_vip_type <= 0) { // 既无守护也无VIP
						if ($from_purviewrank >= 2 && $to_purviewrank <= 1) {
							$is_success = true;
						} else {
							$is_success = false;
						}
					} else { // 无守护，有VIP
						if ($to_vip_type == 1) { // 黄VIP，只能防被房管踢
							if ($from_purviewrank <= 2 || $from_purviewrank <= $to_purviewrank) {
								$is_success = false;
							} else {
								$is_success = true;
							}
						} elseif ($to_vip_type == 2) { // 紫VIP，防房管踢，防主播踢根据等级不同而不同
							if ($from_purviewrank <= 2 || $from_purviewrank <= $to_purviewrank) {
								$is_success = false;
							} else {
								if ($from_purviewrank == 3) { // 主播，根据主播等级判断
									if ($from_dotey_rank < 13) { // 皇冠3以下(不含)
										if ($to_rank < 22) { // 国公以下(不含)，无法防御
											$is_success = true;
										} else {
											$is_success = false;
										}
									} elseif ($from_dotey_rank < 16) { // 皇冠3以上(含)，皇冠6以下(不含)
										if ($to_rank < 23) { // 国师以下(不含)，无法防御
											$is_success = true;
										} else {
											$is_success = false;
										}
									} else { // 皇冠6以上(含)
										if ($to_rank < 24) { // 储王以下(不含)，无法防御
											$is_success = true;
										} else {
											$is_success = false;
										}
									}
								} else { // 管理员无敌
									$is_success = true;
								}
							}
						}
					}
				} else {
					if ($to_vip_type <= 0) { // 有守护，无VIP，同无守护无VIP
						if ($from_purviewrank >= 2 && $to_purviewrank <= 1) {
							$is_success = true;
						} else {
							$is_success = false;
						}
					} else { // 有守护，有VIP
						if ($to_vip_type == 1) { // 黄VIP，只能防同级的初级守护
							if ($from_purviewrank <= 1 || $from_purviewrank <= $to_purviewrank) {
								$is_success = false;
							} elseif ($from_purviewrank == 2) { // 房管
								if ($from_guardian_type == 1) { // 初级守护
									if ($from_rank > $to_rank) {
										$is_success = true;
									} else { // 防同级及以下的初级守护
										$is_success = false;
									}
								} else {
									$is_success = true;
								}
							} else {
								$is_success = true;
							}
						} elseif ($to_vip_type == 2) { // 紫VIP，防守护根据等级不同而不同，防主播禁根据等级不同而不同
							if ($from_purviewrank <= 1 || $from_purviewrank <= $to_purviewrank) {
								$is_success = false;
							} else {
								if ($from_purviewrank == 2) {
									if ($from_guardian_type == 1) { // 防初级守护
										$is_success = false;
									} elseif ($from_guardian_type == 2) { // 高级守护
										if ($from_rank > $to_rank) {
											$is_success = true;
										} else { // 防同级及以下的高级守护
											$is_success = false;
										}
									} else { // 超级守护
										if ($to_rank < 28) { // 国王以下(不含)
											if ($from_rank >= $to_rank) {
												$is_success = true;
											} else { // 防低一级及以下的超级守护
												$is_success = false;
											}
										} else { // 国王以上(含)
											if ($from_rank > $to_rank) {
												$is_success = true;
											} else { // 防同级及以下的超级守护
												$is_success = false;
											}
										}
									}
								} elseif ($from_purviewrank == 3) { // 主播，根据主播等级判断
									if ($from_dotey_rank < 13) { // 皇冠3以下(不含)
										if ($to_rank < 22) { // 国公以下(不含)，无法防御
											$is_success = true;
										} else {
											$is_success = false;
										}
									} elseif ($from_dotey_rank < 16) { // 皇冠3以上(含)，皇冠6以下(不含)
										if ($to_rank < 23) { // 国师以下(不含)，无法防御
											$is_success = true;
										} else {
											$is_success = false;
										}
									} else { // 皇冠6以上(含)
										if ($to_rank < 24) { // 储王以下(不含)，无法防御
											$is_success = true;
										} else {
											$is_success = false;
										}
									}
								} else { // 管理员无敌
									$is_success = true;
								}
							}
						}
					}
				}
				break;
			case 9: // 获取用户状态
				$is_success = true;
				break;
			case 10: // 全局禁言
			case 11: // 全局解禁
				if ($from_purviewrank > 3) {
					$is_success = true;
				} else {
					$is_success = false;
				}
				break;
			default:
				$is_success = false;
		}
		
		$zmq = $this->getZmq();
		if ($is_success) {
			if($to_uid>0){
				if(in_array($type,array(1,5,10))){
					$forbidData['uid']=$to_uid;
					$forbidData['t']=$type;
					$forbidData['vt']=time()+$period*60;
					$this->saveArchivesForbid($archives_id, $forbidData);
				}
				if(in_array($type,array(0,4,11))){
					$this->removeArchivesForbid($archives_id, $to_uid);
				}
				if($type==8){
					$kickData['uid']=$to_uid;
					$kickData['vt']=time()+$period*60;
					$this->saveArchiveskickout($archives_id, $kickData);
				}
				
			}
			// 发送禁言zmq消息
			$zmqData = array();
			$zmqData['archives_id']=$archives_id;
			$zmqData['domain']=DOMAIN;
			$zmqData['uid'] = $from_uid;
			$zmqData['nickname'] = $from_nickname;
			$zmqData['to_uid'] = $to_uid;
			$zmqData['to_nickname'] = $to_nickname;
			$zmqData['type'] = $type;
			$zmqData['period'] = $period;
			$zmqData['status'] =1;
			$zmq->sendZmqMsg(607,$zmqData);
			return 1;
		} elseif ($to_vip_type > 0 && in_array($type, array(1, 5, 8)) && $from_purviewrank > $to_purviewrank) { // 发送广播防御成功消息
			if ($from_purviewrank == 2) {
				$from_nickname = '房间管理员“' . $from_nickname . '“';
			} elseif ($from_purviewrank == 3) {
				$from_nickname = '主播“' . $from_nickname . '“';
			}
			$transform_array = array(1 => '禁IP', 5 => '禁言', 8 => '踢人');
			$zmqData = array();
			$zmqData['archives_id'] = $archives_id;
			$zmqData['domain'] = DOMAIN;
			$zmqData['type'] = 'localroom';
			$zmqData['json_content'] = array('type'=>'vipdefend','from_nickname'=>$from_nickname,'to_nickname'=>$to_nickname,'content'=>$transform_array[$type]);
			$zmq->sendZmqMsg(606,$zmqData);
			return 2;
		} else { // 弹出权限不够
			return false;
		}
	}
	
	/**
	 * 获取用户在本直播间是否被禁言
	 * @param int $archivesId 档期Id
	 * @param unknown_type $uid 被禁言用户uid
	 * @return array
	 */
	public function getArchivesForbid($archivesId,$uid){
		if($archivesId<=0||$uid<=0)
			return $this->setError(Yii::t('common','Parameter not empty'),0);
		$otherRedisModel=new OtherRedisModel();
		$forbidList=$otherRedisModel->getArchivesForbid($archivesId);
		$forbid=array();
		if(isset($forbidList[$uid])){
			if($forbidList[$uid]['vt']>time()){
				$forbid=$forbidList[$uid];
			}
		}
		return $forbid;
	}
	
	/**
	 * 存储本直播间被禁言用户
	 * @param int $archivesId 档期Id
	 * @param array $data     被禁言用户信息
	 * @return boolean
	 */
	public function saveArchivesForbid($archivesId,array $data){
		if($archivesId<=0)
			return $this->setError(Yii::t('common','Parameter not empty'),0);
		$otherRedisModel=new OtherRedisModel();
		$forbidList=$otherRedisModel->getArchivesForbid($archivesId);
		$forbid=array();
		if($forbidList){
			foreach($forbidList as $row){
				if($row['vt']>time()){
					$forbid[$row['uid']]=$row;
				}
			}
		}
		if(!in_array($data['uid'],$forbid)){
			$forbid[$data['uid']]=$data;
		}
		return $otherRedisModel->saveArchivesForbid($archivesId, $forbid);
	}
	
	/**
	 * 移除该直播间被禁言的用户
	 * @param int $archivesId 档期Id
	 * @param int $uid 用户uid
	 * @return boolen
	 */
	public function removeArchivesForbid($archivesId,$uid){
		if($archivesId<=0||$uid<=0)
			return $this->setError(Yii::t('common','Parameter not empty'),0);
		$otherRedisModel=new OtherRedisModel();
		$forbidList=$otherRedisModel->getArchivesForbid($archivesId);
		if(isset($forbidList[$uid])){
			unset($forbidList[$uid]);
		}
		return $otherRedisModel->saveArchivesForbid($archivesId, $forbidList);
	}
	
	/**
	 * 获取用户在本直播间是否被踢出
	 * @param unknown_type $archivesId
	 * @param unknown_type $uid
	 * @return mix|multitype:
	 */
	public function getArchivesKickout($archivesId,$uid){
		if($archivesId<=0||$uid<=0)
			return $this->setError(Yii::t('common','Parameter not empty'),0);
		$otherRedisModel=new OtherRedisModel();
		$kickList=$otherRedisModel->getArchivesKickout($archivesId);
		$kick=array();
		if(isset($kickList[$uid])){
			if($kickList[$uid]['vt']>time()){
				$kick=$kickList[$uid];
			}
		}
		return $kick;
	}
	
	/**
	 * 存储本直播间被踢出用户
	 * @param int $archivesId 档期Id
	 * @param array $data     被踢出用户信息
	 * @return boolean
	 */
	public function saveArchiveskickout($archivesId,array $data){
		if($archivesId<=0)
			return $this->setError(Yii::t('common','Parameter not empty'),0);
		$otherRedisModel=new OtherRedisModel();
		$kickList=$otherRedisModel->getArchivesKickout($archivesId);
		$kick=array();
		if($kickList){
			foreach($kickList as $row){
				if($row['vt']>time()){
					$kick[$row['uid']]=$row;
				}
			}
		}
		if(!in_array($data['uid'],$kick)){
			$kick[$data['uid']]=$data;
		}
		return $otherRedisModel->saveArchivesKickout($archivesId, $kick);
	}
	


	/**
	 * 获取档期档期内用户的操作权限
	 * @param int $archivesId  档期Id
	 * @param int $data        操作权限数组
	 * @return int
	 */
	public function getPurviewRank($archivesId,$data){
		$purviewRank=1;
		//是否存在房管权限
		if(isset($data[2])){
			if(in_array($archivesId,$data[2])){
				$purviewRank=2;
			}
		}
		if(isset($data[4])){
			$purviewRank=4;
		}
		if(isset($data[3])){
			if(in_array($archivesId,$data[3])){
				$purviewRank=3;
			}
		}
		return $purviewRank;
	}

}

?>