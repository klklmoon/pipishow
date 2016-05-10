<?php
/**
 * 主播申请表单验证
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author He xin <suqian@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z suqian $ 
 * @package formModel
 * @subpackage user
 */
class DoteyApplyForm extends PipiFormModel {
	public $p;
	public $f;
	public $realname;
	public $gender;
	public $mobile;
	public $qq;
	public $id_card;
	public $bank_user;
	public $bank;
	public $bank_account;
	public $has_experience;
	public $live_address;
	public $tutor_uid;
	public $agree;
	public $cover;
	
	public function rules()
	{
		return array(
			array('realname,mobile,qq,id_card,bank_user,bank,bank_account,live_address','filter','filter'=>array(new CHtmlPurifier(),'purify')),
			array('realname','required','message'=>'姓名不能为空'),
			array('realname','length', 'max'=>10,'min'=>2,'message'=>'姓名长度在2到10个字之间'),
			array('gender','required','message'=>'性别不能为空'),
			array('mobile', 'required', 'message' => '手机号不能为空'),
			array('mobile', 'mobile'),
			array('qq', 'required', 'message' => 'QQ号不能为空'),
			array('id_card', 'required', 'message' => '身份证号不能为空'),
			array('id_card', 'length', 'min' => 15, 'max' => 18, 'message' => '请填写正确的身份证号'),
// 			array('id_card', 'idCard'),//暂时先不检查
			array('bank_user', 'required', 'message' => '开户姓名不能为空'),
			array('bank', 'required', 'message' => '开户银行不能为空'),
			array('bank_account', 'required', 'message' => '银行卡号不能为空'),
// 			array('cover', 'required', 'message' => '节目封面不能为空'),
			array('has_experience', 'required', 'message' => '主播经验不能为空'),
			array('live_address', 'experience'),
			array('tutor_uid', 'required', 'message' => '导师不能为空'),
			array('agree', 'required', 'message' => '请先阅读主播用户协议'),
		);
	}
	
	public function experience(){
		if($this->has_experience == 1 && empty($this->live_address)){
			$this->addError('live_address', '直播间链接地址不能为空');
		}
		else return true;
	}
	
	public function mobile(){
		if(strlen($this->mobile) == 11 && preg_match("/1[358]{1}\d{9}/",$this->mobile)) return true;
		else{
			$this->addError('mobile', '请填写正确的手机号');
		}
	}
	
	/**
	 * 验证身份证号
	 * @param $vStr
	 * @return bool
	 */
	public function idCard(){
		$vStr = $this->id_card;
	    $vCity = array(
	        '11','12','13','14','15','21','22',
	        '23','31','32','33','34','35','36',
	        '37','41','42','43','44','45','46',
	        '50','51','52','53','54','61','62',
	        '63','64','65','71','81','82','91'
	    );
	
	    if (!preg_match('/^([\d]{17}[xX\d]|[\d]{15})$/', $vStr)){
	    	$this->addError('id_card', '请填写正确的身份证号');
	    	return false;
	    }
	
	    if (!in_array(substr($vStr, 0, 2), $vCity)){
	    	$this->addError('id_card', '请填写正确的身份证号');
	    	return false;
	    }
	
	    $vStr = preg_replace('/[xX]$/i', 'a', $vStr);
	    $vLength = strlen($vStr);
	
	    if ($vLength == 18)
	    {
	        $vBirthday = substr($vStr, 6, 4) . '-' . substr($vStr, 10, 2) . '-' . substr($vStr, 12, 2);
	    } else {
	        $vBirthday = '19' . substr($vStr, 6, 2) . '-' . substr($vStr, 8, 2) . '-' . substr($vStr, 10, 2);
	    }
	
	    if (date('Y-m-d', strtotime($vBirthday)) != $vBirthday){
	    	$this->addError('id_card', '请填写正确的身份证号');
	    	return false;
	    }
	    if ($vLength == 18)
	    {
	        $vSum = 0;
	
	        for ($i = 17 ; $i >= 0 ; $i--)
	        {
	            $vSubStr = substr($vStr, 17 - $i, 1);
	            $vSum += (pow(2, $i) % 11) * (($vSubStr == 'a') ? 10 : intval($vSubStr , 11));
	        }
	
	        if($vSum % 11 != 1){
		    	$this->addError('id_card', '请填写正确的身份证号');
		    	return false;
		    }
	    }
	
	    return true;
	}
    
	public function attributeLabels(){
		return array(
			"p" => "代理经纪人",
			"f" => "星探",
			"realname" => "姓名",
			"gender" => "性别",
			"birth_province" => "出生地省份",
			"birth_city" => "出生地城市",
			"province" => "居住地省份",
			"city" => "居住地城市",
			"profession" => "职业",
			"profession_text" => "所在职业",
			"internet_condition" => "上网环境",
			"mobile" => "手机号",
			"qq" => "QQ号",
			"id_card" => "身份证号",
			"id_card_front" => "身份证正面照",
			"id_card_back" => "身份正背面照",
			"personal_image" => "个人形象照",
			"has_experience" => "主播经验",
			"live_address" => "直播间链接地址",
			"skill" => "特长",
			"tutor_uid" => "导师",
			"agree" => "同意主播用户协议",
		);
	}
}