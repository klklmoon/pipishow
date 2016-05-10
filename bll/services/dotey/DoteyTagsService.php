<?php
/**
 * 主播印象标签服务层
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: 2014-1-4 下午2:27:01 hexin $ 
 * @package
 */
class DoteyTagsService extends PipiService {
	private static $instance;
	
	/**
	 * 返回DoteyTagsService对象的单例
	 * @return DoteyTagsService
	 */
	public static function getInstance(){
		if(self::$instance == null){
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	/**
	 * 保存标签
	 * @param array $data
	 * @return int
	 */
	public function saveTag(array $data){
		if(isset($data['tag_id']) && intval($data['tag_id']) < 1 || (!isset($data['tag_id']) && empty($data['tag_name']))){
			return $this->setError(Yii::t('common','Parameter is empty'), 0);
		}
		$model = new TagsModel();
		if(isset($data['tag_id'])){
			$model = $model->findByPk($data['tag_id']);
			if(empty($model)) return $this->setError(Yii::t('common','Data not exists'), 0);
		}else{
			if(!isset($data['tag_type'])) $data['tag_type'] = 0;
			if(!isset($data['is_display'])) $data['is_display'] = 1;
			$data['use_nums'] = 0;
			$data['create_time'] = time();
		}
		$this->attachAttribute($model, $data);
		if(!$model->validate()){
			return $this->setNotices($model->getErrors(), 0);
		}
		$model->save();
		return $model->getPrimaryKey();
	}
	
	/**
	 * 删除标签
	 * @param max $ids
	 * @return boolean
	 */
	public function deleteTag($ids){
		$ids = is_array($ids) ? $ids : array(intval($ids));
		if(empty($ids)) return false;
		$relation = new UserTagsModel();
		$relation->deleteAll('tag_id in('.implode(',', $ids).')');
		$tag = new TagsModel();
		return $tag->deleteAll('tag_id in('.implode(',', $ids).')');
	}
	
	/**
	 * 给某些主播添加某些标签
	 * @param max $uids
	 * @param max $tag_ids
	 * @return boolean
	 */
	public function addTags($uids, $tag_ids){
		$uids = is_array($uids) ? $uids : array(intval($uids));
		$tag_ids = is_array($tag_ids) ? $tag_ids : array(intval($tag_ids));
		if(empty($uids) || empty($tag_ids)) return false;
		
		//避免重复添加标签的处理
		$tags = $this->getTagsByUids($uids);
		$exists = array();
		foreach($tags as $uid => $ts){
			foreach($ts as $t){
				if(in_array($t['tag_id'], $tag_ids))
					$exists[$t['tag_id']][] = $uid;
			}
		}
		
		$model = new UserTagsModel();
		$return = $model->addTags($uids, $tag_ids, $exists);
		$count = count($uids);
		$tagModel = new TagsModel();
		foreach($tag_ids as $tid){
			$num = 0;
			if(!empty($exists[$tid])) $num = count($exists[$tid]);
			$tagModel->updateCounters(array('use_nums' => $count - $num), 'tag_id = '.$tid);
			$this->getUidsByTag($tid, true);
		}
		return $return;
	}
	
	/**
	 * 给某主播删除某些标签
	 * @param int $uid
	 * @param max $tag_ids
	 * @return boolean
	 */
	public function deleteTags($uid, $tag_ids){
		$tag_ids = is_array($tag_ids) ? $tag_ids : array(intval($tag_ids));
		if(empty($uid) || empty($tag_ids)) return false;
		$model = new UserTagsModel();
		$return = $model->deleteAll('uid = '. $uid .' and tag_id in('.implode(',', $tag_ids).')');
		$tagModel = new TagsModel();
		foreach($tag_ids as $tid){
			$tagModel->updateCounters(array('use_nums' => -1), 'tag_id = '.$tid);
			$this->getUidsByTag($tid, true);
		}
		return $return;
	}
	
	/**
	 * 取得所有标签
	 * @return array
	 */
	public function getAllTags($isHidden = false){
		$model = new TagsModel();
		$cr = $model->getCommandBuilder()->createCriteria();
		if(!$isHidden) $cr->addColumnCondition(array('is_display' => 1));
		$cr->order = 'sort Desc';
		$all = $model->findAll($cr);
		return $this->arToArray($all);
	}
	
	/**
	 * 获取某些用户的标签
	 * @param max $uids
	 * @return array
	 */
	public function getTagsByUids($uids){
		$uids = is_array($uids) ? $uids : array(intval($uids));
		if(empty($uids)) return array();
		$model = new UserTagsModel();
		$tags = $model->getTagsByUids($uids);
		return $this->buildDataByKey($tags, 'uid');
	}
	
	/**
	 * 获取某标签的所有主播
	 * @param int $tag_id
	 * @param int $force 强制更新redis
	 * @return array
	 */
	public function getUidsByTag($tag_id, $force = false){
		$doteys = array();
		if(!$force) $doteys = OtherRedisModel::getInstance()->getTagDotey($tag_id);
		if(empty($doteys)){
			$model = new UserTagsModel();
			$cr = $model->getCommandBuilder()->createCriteria();
			$cr -> select = 'uid';
			$cr -> addColumnCondition(array('tag_id' => $tag_id));
			$r = $model->findAll($cr);
			$doteys = array_keys($this->buildDataByIndex($r, 'uid'));
			OtherRedisModel::getInstance()->setTagDotey($tag_id, $doteys);
		}
		return $doteys;
	}
	
	/**
	 * 获取分页的已有标签的主播列表
	 * @param int $limit
	 * @param int $page
	 * @param array $condition
	 * @param boolean $hidden 是否显示隐藏标签
	 * @return array = array('count'=>1, 'list'=>array(...), 'pages' => object) | array() 不分页时直接返回list
	 */
	public function getTagsByDotey($limit = -1, $page = -1, array $condition = array(), $hidden = false){
		if($page == -1 && $limit = -1) $limit = 10000;
		$list = UserTagsModel::model()->getTagsByDotey($limit, $page, $condition);
		$tags = $this->getAllTags($hidden);
		$tags = $this->buildDataByIndex($tags, 'tag_id');
		$uids = array_keys($this->buildDataByIndex($list['list'], 'uid'));
		$consumeService = new ConsumeService();
		$consume = $consumeService->getConsumesByUids($uids);
		$ranks = $consumeService->getDoteyAllRank();
		$ranks = $this->buildDataByIndex($ranks, 'rank');
		foreach($list['list'] as &$dotey){
			if(isset($consume[$dotey['uid']])){
				$dotey['dotey_rank'] = $consume[$dotey['uid']]['dotey_rank'];
				$dotey['rank_name'] = $ranks[$dotey['dotey_rank']]['name'];
			}else{
				$dotey['dotey_rank'] = '0';
				$rank = array_shift($ranks);
				$dotey['rank_name'] = $rank['name'];
			}
			$tag_ids = explode(',', $dotey['tag_ids']);
			$dotey['tags'] = array();
			foreach($tag_ids as $tid){
				if(isset($tags[$tid])){
					$dotey['tags'][] = $tags[$tid];
				}
			}
		}
		if($page == -1) return $list['list'];
		else return $list;
	}
}