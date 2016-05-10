<?php
/**
 * 家族申请
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: 2013-8-6 下午5:50:59 hexin $ 
 * @package
 */
class FamilyApplyForm extends PipiFormModel {
	public $uid;
	public $name;
	public $medal;
	public $cover;
	public $realname;
	public $qq;
	public $mobile;
	
	public function rules()
	{
		return array(
			array('uid,name,medal,realname,qq,mobile', 'required', 'message'=>'申请信息不完整'),
			array('name,medal,realname,qq,mobile', 'filter', 'filter'=>array(new CHtmlPurifier(),'purify')),
			array('name', 'length', 'min' => 2, 'max' => 20, 'message'=>'20字以内'),
			array('medal', 'length', 'min' => 2, 'max' => 3, 'message'=>'2个汉字或2-3个英文字母'),
			array('mobile', 'mobile'),
		);
	}
	
	public function mobile(){
		if(strlen($this->mobile) == 11 && preg_match("/1[358]{1}\d{9}/",$this->mobile)) return true;
		else{
			$this->addError('mobile', '请填写正确的手机号');
		}
	}
	
	public function getFamilyAttributes(){
		$data = $this->getAttributes();
		unset($data['realname']);
		unset($data['qq']);
		unset($data['mobile']);
		$data['medal'] = strtoupper($data['medal']);
		//@todo 暂时全部家族都为签约家族
// 		$data['sign'] = 1;
		return $data;
	}
	
	public function attributeLabels(){
		return array(
			'uid'	=> 'UID',
			'name'	=> '家族名称',
			'medal'	=> '族徽简字',
			'cover'	=> '家族封面',
			'realname'	=> '真实姓名',
			'qq'	=> 'QQ号码',
			'mobile'=> '手机号码',		
		);
	}
}
