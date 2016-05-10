<?php 
/**
 * 主播搜索服务层
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: OperateService.php 9893 2013-05-09 05:36:56Z suqian $ 
 * @package
 */
class DoteySearchService extends PipiService {
	
	/**
	 * 
	 * @var ChannelDoteySortService
	 */
	protected static $channelDoteySortService = null;
	
	
	public function __construct(PipiController $pipiController = null){
		parent::__construct($pipiController);
		if(self::$channelDoteySortService == null){
			self::$channelDoteySortService = new ChannelDoteySortService();
		}
	}
	
	public function searchCategory(array $condtion){
		$doteyCategoryIndexModel = DoteyCategoryIndexModel::model();
		$models = $doteyCategoryIndexModel->searchCategory($condtion);
		$cateGoryArchives = $this->arToArray($models);
		if(empty($cateGoryArchives)){
			return array();
		}
		$archives = array();
		$liveRecord = array();
		foreach($cateGoryArchives as $archive){
			$archiveId = $archive['archives_id'];
			$liveRecord['status'] = $archive['status'];
			$liveRecord['live_time'] = $archive['live_time'];
			$liveRecord['start_time'] = $archive['start_time'];
			$liveRecord['sub_title'] = $archive['sub_title'];
			$archives[$archiveId]['archives_id'] = $archive['archives_id'];
			$archives[$archiveId]['title'] = $archive['title'];
			$archives[$archiveId]['uid'] = $archive['uid'];
			$archives[$archiveId]['live_record'] = $liveRecord;
		}
		if(!isset($condtion['status'])){
			$condtion['status'] = 0;
		}
		if(!isset($condtion['order'])){
			$condtion['order'] = CHANNEL_DOTEY_SORT_CHARMS_SUPER;
		}
		if(!isset($condtion['uid'])){
			$condtion['uid'] = '';
		}
		self::$channelDoteySortService->filterArchives($archives,$condtion['status']);
		self::$channelDoteySortService->buildLiveArchives($archives,$condtion['uid'],$condtion['status'],true,true);
		return self::$channelDoteySortService->sortLiveArchives($archives,$condtion['order'],$condtion['status']);
		
	}
}
?>