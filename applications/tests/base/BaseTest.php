<?php

require_once ('PHPUnit/Framework/TestCase.php');

/**
 * 皮皮乐天所有单元测试基类
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <aoxue.1988.su.qian@163.com>
 * @version $Id: BaseTest.php 8317 2013-03-29 01:19:47Z suqian $ 
 * @package 
 */
class BaseTest extends PHPUnit_Framework_TestCase {
	protected static $uid = 0;
	protected static $to_uid = 0;
	protected static $gift_id = 0;
	protected static $prop_id = 0;
	protected static $dotey_uid = 0;
	
	public function getNewUser(){
		if(self::$uid < 1){
			$username = 't_'.uniqid();
			$user['nickname'] = $username;
			$user['username'] = $username;
			$user['reg_email']= $username.'@pipi.cn';
			$user['password'] = $username;
			$userService = new UserService();
			$user = $userService->saveUserBasic($user);
			self::$uid = $user['uid'];
		}
	}
	
	public function getNewDotey(){
		if(self::$dotey_uid < 1){
			$username = 't_'.uniqid();
			$user['nickname'] = $username;
			$user['username'] = $username;
			$user['reg_email']= $username.'@pipi.cn';
			$user['password'] = $username;
			$user['user_type']= 2;
			$userService = new UserService();
			$user = $userService->saveUserBasic($user);
			$doteyBase = array();
			$doteyBase['uid'] = $user['uid'];
			$doteyBase['sign_type'] = array_rand(array(1,2,4,8));
			$doteyBase['status']	= 1;
			$doteyBase['create_time'] = time();
			$doteyServer = new DoteyService();
			$doteyServer->saveUserDoteyBase($doteyBase);
			self::$dotey_uid = $user['uid'];
		}
	}
	
	public function getToUser(){
		if(self::$to_uid < 1){
			$username = 't_'.uniqid();
			$user['nickname'] = $username;
			$user['username'] = $username;
			$user['reg_email'] = $username.'@pipi.cn';
			$user['password'] = $username;
			$userService = new UserService();
			$user = $userService->saveUserBasic($user);
			self::$to_uid = $user['uid'];
		}
	}
	
	public function getGift(){
		if(self::$gift_id < 1){
			$tmp_name = 'g_'.uniqid();
			$gift = array(
				'cat_id'	=> 0,
				'zh_name'	=> $tmp_name."测试礼物",
				'en_name'	=> $tmp_name,
				'shop_type'	=> '1',
				'image'		=> 'xxx.jpg',
				'pipiegg'	=> '1.00',
				'charm'		=> '1',
				'charm_points'	=> '1',
				'dedication'=> '1',
				'egg_points'=> '1',
				'gift_type'	=> '1',
			);
			$giftService = new GiftService();
			$id = $giftService->saveGift($gift);
			self::$gift_id = $id;
		}
	}
	
	public function getProp(){
		if(self::$prop_id < 1){
			$propsService = new PropsService();
			$cat = array(
				'name'		=> 'VIP',
				'en_name'	=> 'vip',
			);
			$category = PropsCategoryModel::model()->findByAttributes($cat);
			if($category){
				$cat_id = $category->cat_id;
			}else{
				$cat_id = $propsService->savePropsCategory($cat);
			}
			$tmp_name = 'p-'.uniqid();
			$props = array(
				'cat_id'	=> $cat_id,
				'name'		=> $tmp_name."测试道具",
				'en_name'	=> $tmp_name,
				'pipiegg'	=> 1,
				'charm'		=> 1,
				'charm_points'=> 1,
				'dedication'=> 1,
				'egg_points'=> 1,
				'status'	=> 0,
			);
			$id = $propsService->saveProps($props);
			self::$prop_id = $id;
			$attr = $propsService->getPropsCatAttrtByIds($cat_id, 1);
			if($attr){
				$attr = array_pop($attr);
				$attr_id = $attr['attr_id'];
			}else{
				$tmp_name = 'a-'.uniqid();
				$attr = array(
					'cat_id'	=> $cat_id,
					'attr_name'	=> $tmp_name."_测试分类属性",
					'attr_enname'=> "key",
					'attr_value'=> 'value',
				);
				$attr_id = $propsService->savePropsCatAttribute($attr);
			}
			$attr = array(
				'prop_id'	=> self::$prop_id,
				'attr_id'	=> $attr_id,
				'value'		=> 1,
			);
			$propsService->saveSinglePropsAttribute($attr);
		}
	}
	
	public function init_rank(){
		$count = UserRankModel::model()->count();
		if($count < 1){
			$sql="INSERT INTO `web_user_rank` VALUES ('1', '0', '无', '0', '0');
				INSERT INTO `web_user_rank` VALUES ('2', '1', '普通', '50', '0');
				INSERT INTO `web_user_rank` VALUES ('3', '2', '绅士1', '200', '1');
				INSERT INTO `web_user_rank` VALUES ('4', '3', '绅士2', '550', '1');
				INSERT INTO `web_user_rank` VALUES ('5', '4', '绅士3', '1550', '1');
				INSERT INTO `web_user_rank` VALUES ('6', '5', '绅士4', '3050', '2');
				INSERT INTO `web_user_rank` VALUES ('7', '6', '绅士5', '5050', '2');
				INSERT INTO `web_user_rank` VALUES ('8', '7', '富豪1', '7550', '3');
				INSERT INTO `web_user_rank` VALUES ('9', '8', '富豪2', '10550', '4');
				INSERT INTO `web_user_rank` VALUES ('10', '9', '富豪3', '20550', '5');
				INSERT INTO `web_user_rank` VALUES ('11', '10', '富豪4', '45550', '6');
				INSERT INTO `web_user_rank` VALUES ('12', '11', '富豪5', '85550', '7');
				INSERT INTO `web_user_rank` VALUES ('13', '12', '富豪6', '145550', '8');
				INSERT INTO `web_user_rank` VALUES ('14', '13', '富豪7', '245550', '9');
				INSERT INTO `web_user_rank` VALUES ('15', '14', '富豪8', '395550', '10');
				INSERT INTO `web_user_rank` VALUES ('16', '15', '男爵', '620550', '11');
				INSERT INTO `web_user_rank` VALUES ('17', '16', '子爵', '895550', '12');
				INSERT INTO `web_user_rank` VALUES ('18', '17', '伯爵', '1295550', '13');
				INSERT INTO `web_user_rank` VALUES ('19', '18', '侯爵', '1895550', '14');
				INSERT INTO `web_user_rank` VALUES ('20', '19', '公爵', '2795550', '15');
				INSERT INTO `web_user_rank` VALUES ('21', '20', '大公', '4045550', '16');
				INSERT INTO `web_user_rank` VALUES ('22', '21', '国公', '6545550', '17');
				INSERT INTO `web_user_rank` VALUES ('23', '22', '国师', '10545550', '18');
				INSERT INTO `web_user_rank` VALUES ('24', '23', '储王', '16545550', '19');
				INSERT INTO `web_user_rank` VALUES ('25', '24', '郡王', '25045550', '20');
				INSERT INTO `web_user_rank` VALUES ('26', '25', '藩王', '34045550', '21');
				INSERT INTO `web_user_rank` VALUES ('27', '26', '亲王', '44045550', '22');
				INSERT INTO `web_user_rank` VALUES ('28', '27', '国王', '56545550', '23');
				INSERT INTO `web_user_rank` VALUES ('29', '28', '皇帝', '71545550', '24');
				INSERT INTO `web_user_rank` VALUES ('30', '29', '大帝', '94045550', '25');
				INSERT INTO `web_user_rank` VALUES ('31', '30', '传奇', '119045550', '30');";
			UserRankModel::model()->getCommandBuilder()->createSqlCommand($sql)->execute();
		}
		$count = DoteyRankModel::model()->count();
		if($count < 1){
			$sql="INSERT INTO `web_dotey_rank` VALUES ('1', '0', '新人', '0', '5', '80');
				INSERT INTO `web_dotey_rank` VALUES ('2', '1', '红心1', '250', '5', '80');
				INSERT INTO `web_dotey_rank` VALUES ('3', '2', '红心2', '750', '5', '80');
				INSERT INTO `web_dotey_rank` VALUES ('4', '3', '红心3', '3000', '5', '80');
				INSERT INTO `web_dotey_rank` VALUES ('5', '4', '红心4', '8000', '5', '80');
				INSERT INTO `web_dotey_rank` VALUES ('6', '5', '红心5', '18000', '5', '80');
				INSERT INTO `web_dotey_rank` VALUES ('7', '6', '蓝钻1', '33000', '5', '80');
				INSERT INTO `web_dotey_rank` VALUES ('8', '7', '蓝钻2', '58000', '6', '80');
				INSERT INTO `web_dotey_rank` VALUES ('9', '8', '蓝钻3', '133000', '8', '80');
				INSERT INTO `web_dotey_rank` VALUES ('10', '9', '蓝钻4', '258000', '10', '80');
				INSERT INTO `web_dotey_rank` VALUES ('11', '10', '蓝钻5', '458000', '12', '80');
				INSERT INTO `web_dotey_rank` VALUES ('12', '11', '蓝钻6', '733000', '14', '80');
				INSERT INTO `web_dotey_rank` VALUES ('13', '12', '蓝钻7', '1133000', '16', '80');
				INSERT INTO `web_dotey_rank` VALUES ('14', '13', '蓝钻8', '1683000', '18', '80');
				INSERT INTO `web_dotey_rank` VALUES ('15', '14', '蓝钻9', '2433000', '20', '80');
				INSERT INTO `web_dotey_rank` VALUES ('16', '15', '皇冠1', '3433000', '22', '80');
				INSERT INTO `web_dotey_rank` VALUES ('17', '16', '皇冠2', '4833000', '24', '80');
				INSERT INTO `web_dotey_rank` VALUES ('18', '17', '皇冠3', '6708000', '26', '80');
				INSERT INTO `web_dotey_rank` VALUES ('19', '18', '皇冠4', '9108000', '28', '80');
				INSERT INTO `web_dotey_rank` VALUES ('20', '19', '皇冠5', '12108000', '30', '80');
				INSERT INTO `web_dotey_rank` VALUES ('21', '20', '皇冠6', '15858000', '9999', '80');
				INSERT INTO `web_dotey_rank` VALUES ('22', '21', '皇冠7', '20508000', '9999', '80');
				INSERT INTO `web_dotey_rank` VALUES ('23', '22', '皇冠8', '26158000', '9999', '80');
				INSERT INTO `web_dotey_rank` VALUES ('24', '23', '皇冠9', '32908000', '9999', '80');
				INSERT INTO `web_dotey_rank` VALUES ('25', '24', '皇冠10', '40908000', '9999', '80');
				INSERT INTO `web_dotey_rank` VALUES ('26', '25', '皇冠11', '50408000', '9999', '80');";
			DoteyRankModel::model()->getCommandBuilder()->createSqlCommand($sql)->execute();
		}
	}
	
	public function getNewArchive(){
		if(self::$archive_id){
			$this->getNewDotey();
			$name = 'c_' .uniqid();
			$category = array();
			$category['name'] = $name;
			$category['en_name'] = 'en_' .$name;
			$cat_id = $this->archives->saveArchivesCat($category);
			
			$archives = array();
			$archives['uid'] = self::$dotey_uid;
			$archives['title'] = 'api_' . uniqid();
			$archives['cat_id'] = $cat_id;
			$archives['notice'] = 'this use to api test';
			self::$archive_id = $this->archives->createArchives($archives);
		}
	}
}

