<?php
/**
 * 处理用户列表相关服务层
 * the last known user to change this file in the repository  <$LastChangedBy: lei wei $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z lei wei  $
 * @package
 */
define('DEFAULT_USER_LIST_NUM',50);  //用户列表默认获取数量
define('DEFAULT_USER_LIST_PAGE',0);  //用户列表默认开始位置
class UserListService extends PipiService {

	/**
	 * 根据档期ID获取用户列表
	 * @param int $archivesId
	 * @param int $num
	 * @return array
	 */
	public function getUserList($archivesId,$num=DEFAULT_USER_LIST_NUM,$page=DEFAULT_USER_LIST_PAGE){
		if($archivesId<=0)
			return $this->setError(Yii::t('common','Parameter not empty'), array());
		$otherRedisModel=new OtherRedisModel();
		$userList=$otherRedisModel->getUserList($archivesId);
		$userNum=isset($userList['user'])?count($userList['user']):0;
		$doteyNum=isset($userList['dotey'])?count($userList['dotey']):0;
		if($userNum+$doteyNum>=$num){
			$userList['user']=array_slice($userList['user'], 0,$num);
		}
		$user=array();
		if($userList){
			$user=$this->operateUserList($userList,$archivesId);
		}
		return $user;
	}
	
	/**
	 * 获取直播间在线人数
	 * @param int $archivesId 档期Id
	 * @return mix|number
	 */
	public function getArchivesOnlineNum($archivesId){
		if($archivesId<=0)
			return $this->setError(Yii::t('common','Parameter not empty'), 0);
		$otherRedisModel=new OtherRedisModel();
		$userList=$otherRedisModel->getUserList($archivesId);
		return $userList?$userList['total']:0;
	}
	
	public function getArchivesOnlineNumByArchivesIds(array $archivesIds){
		if(empty($archivesIds)){
			return $this->setError(Yii::t('common','Parameter not empty'), 0);
		}
		$otherRedisModel=new OtherRedisModel();
		$userList=$otherRedisModel->getUserListByArchivesIds($archivesIds);
		$onlineNum=array();
		foreach($userList as $row){
			$onlineNum[$row['archives_id']]=$row['total'];
		}
		return $onlineNum;
	}
	
	
	/**
	 * 处理用户列表数组
	 * @param array $userList 用户列表数组
	 * @return array
	 */
	public function operateUserList(array $userList,$archivesId){
		if(empty($userList))
			return $this->setError(Yii::t('common','Parameter not empty'), array());
		$user=array();
		$user['archives_id']=$userList['archives_id'];
		$user['total']=$userList['total'];
		$user['dotey']=array();
		$labelList=$this->getArchivesLabel($archivesId);
		if($userList['dotey']){
			foreach($userList['dotey'] as $key=>$row){
				if(in_array($archivesId,$row['pk'][3])){
					$user['dotey'][$key]['pk']=3;
					$user['dotey'][$key]['uid']=$row['uid'];
					$user['dotey'][$key]['nk']=$row['nk'];
					$user['dotey'][$key]['rk']=isset($row['dk'])?$row['dk']:0;
					$user['dotey'][$key]['st']=isset($row['st'])?$row['st']:0;
					if(isset($row['vip'])){
						if(isset($row['vip']['vt'])){
							if(($row['vip']['vt']>time()||$row['vip']['vt']==0)&&((isset($row['vip']['us'])&&$row['vip']['us']==0)||!isset($row['vip']['us']))){
								$user['dotey'][$key]['vip']=$row['vip']['t'];
							}
						}
			
					}
					$user['dotey'][$key]['mc']=0;
					if(isset($row['mc'])){
						if(isset($row['mc']['vt'])){
							if($row['mc']['vt']>time()){
								$user['dotey'][$key]['mc']=1;
							}
						}
					}
					if(isset($labelList[$row['uid']])){
						if($labelList[$row['uid']]['vt']>time()){
							$user['dotey'][$key]['lb']=$labelList[$row['uid']]['img'];
						}
					}
					if(isset($row['md'])){
						foreach($row['md'] as $val){
							if(isset($val['aid'])){
								if(in_array($archivesId, $val['aid'])){
									if(isset($val['vt'])){
										if($val['vt']>time()||$val['vt']==0){
											$user['dotey'][$key]['md'][]=$val['img'];
										}
									}
								}
							}else{
								if(isset($val['vt'])){
									if($val['vt']>time()||$val['vt']==0){
										$user['dotey'][$key]['md'][]=$val['img'];
									}
								}
							}
						}
					}
					if(isset($row['fp'])){
						if(isset($row['fp']['medal'])){
							$medal = $row['fp']['medal'];
						}else{
							$medal = '';
						}
						$user['dotey'][$key]['fp']['medal'] = $medal;
					}
					if(isset($row['num'])){
						$user['dotey'][$key]['num']=$row['num'];
					}
					if(isset($row['agent'])){
						if(isset($row['agent']['at'])&&isset($row['agent']['st'])&&$row['agent']['st']==0){
							$user['dotey'][$key]['agent']=$row['agent']['at'];
						}
					}
				}
			}
		}
		$user['manage']=array();
		$manageList=$this->getArchivesManageList($archivesId);
		$userList['manage']=$manageList?$manageList:$userList['manage'];
		if($userList['manage']){
			foreach($userList['manage'] as $key=>$row){
				if(isset($row['pk'][2]) ||isset($row['pk'][4])){
					if(isset($row['pk'][2])){
						if(in_array($archivesId,$row['pk'][2])){
							$user['manage'][$key]['pk']=2;
						}
					}
					if(isset($row['pk'][4])){
						$user['manage'][$key]['pk']=4;
					}
					$user['manage'][$key]['uid']=$row['uid'];
					$user['manage'][$key]['nk']=$row['nk'];
					$user['manage'][$key]['rk']=isset($row['rk'])?$row['rk']:0;
					$user['manage'][$key]['st']=isset($row['st'])?$row['st']:0;
					if(isset($row['vip'])){
						if(isset($row['vip']['vt'])){
							if(($row['vip']['vt']>time()||$row['vip']['vt']==0)&&((isset($row['vip']['us'])&&$row['vip']['us']==0)||!isset($row['vip']['us']))){
								$user['manage'][$key]['vip']=$row['vip']['t'];
							}
						}
					}
					$user['manage'][$key]['mc']=0;
					if(isset($row['mc'])){
						if($row['mc']['vt']){
							if($row['mc']['vt']>time()){
								$user['manage'][$key]['mc']=1;
							}
						}
					}
					if(isset($labelList[$row['uid']])){
						if($labelList[$row['uid']]['vt']>time()){
							$user['manage'][$key]['lb']=$labelList[$row['uid']]['img'];
						}
					}
					if(isset($row['md'])){
						foreach($row['md'] as $val){
							if(isset($val['aid'])){
								if(in_array($archivesId,$val['aid'])){
									if(isset($val['vt'])){
										if($val['vt']>time()||$val['vt']==0){
											$user['manage'][$key]['md'][]=$val['img'];
										}
									}
								}
							}else{
								if(isset($val['vt'])){
									if($val['vt']>time()||$val['vt']==0){
										$user['manage'][$key]['md'][]=$val['img'];
									}
								}
							}
						}
					}
					if(isset($row['fp'])){
						if(isset($row['fp']['medal'])){
							$medal = $row['fp']['medal'];
						}else{
							$medal = '';
						}
						$user['manage'][$key]['fp']['medal'] = $medal;
					}
					
					if(isset($row['num'])){
						$user['manage'][$key]['num']=$row['num'];
					}
					if(isset($row['agent'])){
						if(isset($row['agent']['at'])&&$row['agent']['st']==0){
							$user['manage'][$key]['agent']=$row['agent']['at'];
						}
					}
				}
			}
		}
		
		
		$user['user']=array();
		if($userList['user']){
			foreach($userList['user'] as $key=>$row){
				$user['user'][$key]['uid']=$row['uid'];
				$user['user'][$key]['nk']=$row['nk'];
				$user['user'][$key]['rk']=isset($row['rk'])?$row['rk']:0;
				if($row['uid']>0){
					if(isset($row['pk'])){
						$user['user'][$key]['pk']=ForbidenService::getPurviewRank($archivesId,$row['pk']);
					}else{
						$user['user'][$key]['pk']=1;
					}
				}else{
					$user['user'][$key]['pk']=0;
				}
				
				$user['user'][$key]['st']=isset($row['st'])?$row['st']:0;
				if(isset($row['vip'])){
					if(isset($row['vip']['vt'])){
						if(($row['vip']['vt']>time()||$row['vip']['vt']==0)&&((isset($row['vip']['us'])&&$row['vip']['us']==0)||!isset($row['vip']['us']))){
							$user['user'][$key]['vip']=$row['vip']['t'];
						}
					}
				}
				$user['user'][$key]['mc']=0;
				if(isset($row['mc'])){
					if(isset($row['mc']['vt'])){
						if($row['mc']['vt']>time()){
							$user['user'][$key]['mc']=1;
						}
					}
				}
				if(isset($labelList[$row['uid']])){
					if($labelList[$row['uid']]['vt']>time()){
						$user['user'][$key]['lb']=$labelList[$row['uid']]['img'];
					}
				}
				if(isset($row['md'])){
					foreach($row['md'] as $val){
						if(isset($val['aid'])){
							if(in_array($archivesId,$val['aid'])){
								if(isset($val['vt'])){
									if($val['vt']>time()||$val['vt']==0){
										$user['user'][$key]['md'][]=$val['img'];
									}
								}
							}
						}else{
							if(isset($val['vt'])){
								if($val['vt']>time()||$val['vt']==0){
									$user['user'][$key]['md'][]=$val['img'];
								}
							}
						}
					}
				}
				if(isset($row['fp'])){
					if(isset($row['fp']['medal'])){
						$medal = $row['fp']['medal'];
					}else{
						$medal = '';
					}
					$user['user'][$key]['fp']['medal'] = $medal;
				}
				if(isset($row['num'])){
					$user['user'][$key]['num']=$row['num'];
				}
				if(isset($row['agent'])){
					if(isset($row['agent']['at'])&&$row['agent']['as']==0){
						$user['user'][$key]['agent']=$row['agent']['at'];
					}
				}
			}
		}
		
		return $user;
	}
	
	public function getArchivesManageList($archivesId){
		if($archivesId<=0)
			return $this->setError(Yii::t('common','Parameter not empty'),0);
		$otherRedisModel=new OtherRedisModel();
		$uids=$otherRedisModel->getArchivesManage($archivesId);
		$uids = is_array($uids) ? $uids : array();
		$userList=$otherRedisModel->getUserList($archivesId);
		$manageList=array();
		foreach($userList['user'] as $row){
			if(in_array($row['uid'],$uids)){
				$manageList[]=$row;
			}
		}
		foreach($userList['manage'] as $row){
			if(!in_array($row['uid'],$uids)){
				$manageList[]=$row;
			}
		}
		return $manageList;
	}
	
	
	public function saveArchivesManageList($archivesId){
		if($archivesId<=0)
			return $this->setError(Yii::t('common','Parameter not empty'),0);
		$archivesService=new ArchivesService();
		$manageList=$archivesService->getPurviewLiveByArchivesIds($archivesId);
		$otherRedisModel=new OtherRedisModel();
		return $otherRedisModel->saveArchviesManage($archivesId,$manageList[$archivesId]);
	}
	
	
	public function addUserToUserList($archivesId,$uid){
		if($archivesId<=0||$uid<=0)
			return $this->setError(Yii::t('common','Parameter not empty'), 0);
		$otherRedisModel=new OtherRedisModel();
		$userList=$otherRedisModel->getUserList($archivesId);
		if(count($userList['user'])+count($userList['dotey'])>DEFAULT_USER_LIST_NUM){
			$userList['user']=array_slice($userList['user'], 0,(DEFAULT_USER_LIST_NUM-count($userList['dotey'])));
		}
		$userJson=new UserJsonInfoService();
		$userInfo=$userJson->getUserInfo($uid,false);
		if(!isset($userInfo['vip'])||!isset($userInfo['vip']['h'])||$userInfo['vip']['h']!=1){
			$forbidenService=new ForbidenService();
			$purviewrank=$forbidenService->getPurviewRank($archivesId,$userInfo['pk']);
			if($purviewrank==3){
				$dotey=true;
				foreach($userList['dotey'] as $val){
					if($val['uid']==$uid){
						$dotey=false;
					}
				}
				if($dotey){
					foreach($userList['dotey'] as $key=>$row){
						if($row['dk']<=$userInfo['dk']){
							$userList['dotey']=$this->array_insert($userList['dotey'],$userInfo,$key);
							break;
						}
					}
				}
				
			}
			if($purviewrank!=3){
				if($purviewrank==2||$purviewrank==4){
					$manage=true;
					foreach($userList['manage'] as $val){
						if($val['uid']==$uid){
							$manage=false;
						}
					}
					if($manage){
						foreach($userList['manage'] as $key=>$row){
							if($row['rk']<=$userInfo['rk']){
								$userList['manage']=$this->array_insert($userList['manage'],$userInfo,$key);
								break;
							}
						}
					}
					
				}
				$user=true;
				foreach($userList['user'] as $_user){
					if($_user['uid']==$uid){
						$user=false;
					}
				}
				if($user){
					foreach($userList['user'] as $key=>$row){
						if($row['rk']<=$userInfo['rk']){
							$userList['user']=$this->array_insert($userList['user'],$userInfo,$key);
							break;
						}
					}
				}
			}
		}
		return $this->operateUserList($userList,$archivesId);
	}

	public function getArchivesLabel($archivesId){
		if($archivesId<=0)
			return $this->setError(Yii::t('common','Parameter not empty'),0);
		$otherRedisModel=new OtherRedisModel();
		return $labelList=$otherRedisModel->getArchivesLabel($archivesId);
	}
	
	public function saveArchivesLabel($archivesId,$uid){
		if($archivesId<=0||$uid<=0)
			return $this->setError(Yii::t('common','Parameter not empty'),0);
		$otherRedisModel=new OtherRedisModel();
		$labelList=$otherRedisModel->getArchivesLabel($archivesId);
		if(!in_array($uid,$labelList)){
			$userJsonService=new UserJsonInfoService();
			$userJson=$userJsonService->getUserInfo($uid,false);
			if(isset($userJson['lb'])){
				if($userJson['lb']['vt']>time()){
					$labelList[$uid]=array('img'=>$userJson['lb']['img'],'vt'=>$userJson['lb']['vt']);
				}
			}
		}
		$otherRedisModel->saveArchviesLabel($archivesId, $labelList);
		return $labelList;
	}
	
	public function removeArchivesLabel($archivesId,$uid){
		if($archivesId<=0||$uid<=0)
			return $this->setError(Yii::t('common','Parameter not empty'),0);
		$otherRedisModel=new OtherRedisModel();
		$labelList=$otherRedisModel->getArchivesLabel($archivesId);
		if(array_key_exists($uid, $labelList)){
			unset($labelList[$uid]);
		}
		return $otherRedisModel->saveArchviesLabel($archivesId, $labelList);
	}
	
	/**
	 * 判断用户是否具有时效性的道具:月卡，贴条
	 * @param array $data  用户user_info具有时效性字段
	 * @return boolean 0->没有 1->有
	 */
	public function getValidProps(array $data){
		if(isset($data)){
			if(isset($data['vt'])){
				if($data['vt']>time()){
					return true;
				}
			}
		}
		return false;
	}
	
	/**
	 * 获取用户vip的种类
	 * @param array $data 用户user_info vip字段
	 * @return string
	 */
	public function getUserVip(array $data){
		$vip='';
		if(isset($data['vt'])){
			if($data['vt']>time()||$data['vt']==0){
				$vip=$data['t'];
			}
		}
		return $vip;
	}
	
	/**
	 * 获取用户在某个直播间拥有的勋章
	 * @param int $archivesId 档期Id
	 * @param array $data 用户user_info 勋章字段
	 * @return array
	 */
	public function getUserMedals($archivesId,array $data){
		$medals=array();
		if(isset($data)){
			foreach($data as $val){
				if(isset($val['aid'])){
					if(in_array($archivesId, $val['aid'])){
						if(isset($val['vt'])){
							if($val['vt']>time()||$val['vt']==0){
								$medals[]=$val['img'];
							}
						}
					}
				}else{
					if(isset($val['vt'])){
						if($val['vt']>time()||$val['vt']==0){
							$medals[]=$val['img'];
						}
					}
				}
			}
		}
		return $medals;
	}
	
	/**
	 * 获取档期档期内用户的操作权限
	 * @param int $archivesId  档期Id
	 * @param array $data      操作权限数组
	 * @return int
	 */
	public function getPurviewRank($archivesId,array $data){
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
	
	
	protected  function array_insert($target,$value,$position=0){
		$temp=($position==0)?array():array_splice($target,0,$position);
		$temp[]=$value;
		return array_merge($temp,$target);
	}
	
}

?>