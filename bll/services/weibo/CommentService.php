<?php
/**
 * 评论访问层
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author hexin
 * @version $Id: CommentService.php 17138 2014-01-07 08:52:38Z hexin $ 
 * @package service
 */
define('COMMENT_TYPE_DOTEY', 'dotey');
define('COMMENT_TYPE_ALBUM', 'album');
class CommentService extends PipiService {
	private static $instance;
	
	/**
	 * 返回CommentService对象的单例
	 * @return CommentService
	 */
	public static function getInstance(){
		if(self::$instance == null){
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	/**
	 * 评论动态
	 * 
	 * @param int $target_id target_id
	 * @param int $uid 评论人uid
	 * @param string $content
	 * @param string $type
	 * @param int $reply_id 回复评论的comment_id
	 * @return int 返回评论ＩＤ
	 */
	public function comment($target_id, $uid, $content, $source = COMMENT_TYPE_DOTEY, $reply_id = 0){
		$data = array(
			'target_id'	=> $target_id,
			'source'	=> $source,
			'uid'		=> $uid,
			'content'	=> $content,
			'reply_id'	=> $reply_id,
			'create_time' => time()
		);
		$model = new CommentModel();
		$this->attachAttribute($model, $data);
		if(!$model->validate()){
			return $this->setNotices($model->getErrors(), 0);
		}
		$model->save();
		return $model->getPrimaryKey();
	}
	
	/**
	 * 主播删除评论,需要在外面检查dotey_uid是否可以删除cids
	 * @param int $dotey_uid
	 * @param int|array $cids
	 * @param int $target_id
	 * @param string $source
	 * @param boolean
	 */
	public function deleteCommentByDotey($dotey_uid, $cids){
		$doteyService = new DoteyService();
		$dotey = $doteyService->getDoteysInUids(array($dotey_uid));
		if(empty($dotey)) return $this->setNotice(0, '只有主播才能删动态', false);
		
		return CommentModel::model()->deleteAll('comment_id in ('.implode(',', $cids).')');
	}
	
	/**
	 * 用户删除评论
	 * @param int $uid
	 * @param int|array $tids
	 * @param boolean
	 */
	public function deleteCommentByUser($uid, $cids){
		$tids = is_array($tids) ? $tids : array(intval($tids));
		
		$comments = $this->getCommnts($cids);
		$ids = array();
		foreach($comments as $c){
			if($c['uid'] == $uid){
				$ids[] = $c['comment_id'];
			}
		}
		if(empty($ids)) return $this->setNotice(0, '只能删除自己发的动态', false);
		
		return CommentModel::model()->deleteAll('comment_id in ('.implode(',', $ids).')');
	}

	
	/**
	 * 获取评论
	 * @param int $target_id target_id
	 * @param string $source
	 * @param int $limit 条数
	 * @param int $page 分页数，-1为不分页
	 * @param array $condition 其他条件
	 * @return array = array(count => 1, list => array(...), pages => object) | array() 不分页直接返回list
	 */
	public function getCommentList($target_id, $source = COMMENT_TYPE_DOTEY, $limit = -1, $page = -1){
		if($page == -1 && $limit = -1){
			$limit = 10000;
		}
		$model = new CommentModel();
		$cr = $model->getCommandBuilder()->createCriteria();
		$cr -> addColumnCondition(array('target_id' => $target_id, 'source' => $source));
		$cr -> order = 'comment_id desc';
		
		if($page == -1){
			$db->limit = $limit;
			$db->offset = 0;
			$result['list'] = $model->findAll($cr);
		}else{
			$result['count'] = $model->count($cr);
			$pages=new CPagination($result['count']);
			$pages->pageSize = $limit;
			$pages->applyLimit($cr);
			$result['list'] = $model->findAll($cr);
			$result['pages'] = $pages;
		}
		$result['list'] = $this->arToArray($result['list']);
		
		$uids = array_keys($this->buildDataByIndex($result['list'], 'uid'));
		$replyIds = array();
		foreach($result['list'] as $p){
			if($p['reply_id'] > 0 && !in_array($p['reply_id'], $replyIds)) $replyIds[] = $p['reply_id'];
		}
		
		$reply = $this->getCommnts($replyIds);
		$reply_uids = array_keys($this->buildDataByIndex($reply, 'uid'));
		$uids = array_merge($uids, $reply_uids);
		$users = UserJsonInfoService::getInstance()->getUserInfos($uids, false);
		$userService = new UserService();
		foreach($result['list'] as &$p){
			$p['nickname']	= $users[$p['uid']]['nk'];
			$p['rank']		= $users[$p['uid']]['rk'];
			$p['pic']		= $userService->getUserAvatar($p['uid'], 'small', isset($users[$p['uid']]['atr']) ? $users[$p['uid']]['atr'] : array());
			if(isset($reply[$p['reply_id']])){
				$rp = $reply[$p['reply_id']];
				$rp['nickname']	= $users[$rp['uid']]['nk'];
				$rp['rank']		= $users[$rp['uid']]['rk'];
				$p['reply'] = $rp;
			}
		}
		if($page == -1){
			return $result['list'];
		}
		return $result;
	}
	
	/**
	 * 返回具体的评论详情
	 * @param int|array $ids
	 * @return array
	 */
	public function getCommnts($ids){
		$ids = is_array($ids) ? $ids : array(intval($ids));
		if(empty($ids)) return array();
		$comments = CommentModel::model()->findAll('comment_id in ('.implode(',', $ids).')');
		return $this->buildDataByIndex($this->arToArray($comments), 'comment_id');
	}
	
	/**
	 * 获取某些动态的评论总数
	 * @param int|array $target_ids
	 * @param string $source
	 * @return array
	 */
	public function getCountByDynamic($target_ids, $source = ''){
		$target_ids = is_array($target_ids) ? $target_ids : array(intval($target_ids));
		if(empty($target_ids)) return array();
		$counts = CommentModel::model()->getCountByDynamic($target_ids, $source);
		$return = array();
		foreach($counts as $count){
			$return[$count['source'].'_'.$count['target_id']] = $count['count'];
		}
		return $return;
	}
}
