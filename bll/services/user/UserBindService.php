<?php
/**
 * @var int 手机绑定
 */
define('BIND_TYPE_MOBILE',1);
/**
 * @var int 解除手机绑定
 */
define('BIND_TYPE_UNMOBILE',-1);
/**
 * @var int 邮箱绑定
 */
define('BIND_TYPE_MAIL',2);
/**
 * @var int 解除邮箱绑定
 */
define('BIND_TYPE_UNMAIL',-2);
/**
 * @var int 通过手机找回密码
 */
define('BIND_TYPE_FINDPASS_MOBILE',3);
/**
 * @var int 通过邮件找回密码
 */
define('BIND_TYPE_FINDPASS_MAIL',4);

define('SMS_EXPIRED_TIME',0.5*3600);
/**
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z suqian $ 
 * @package 
 */
class UserBindService extends PipiService {

		public function saveUserBind(array $userBind){
			if(!isset($userBind['uid']) || $userBind['uid'] <= 0){
				return $this->setNotices(Yii::t('common','Parameter is empty'),0);
			}
	
			$userBindModel = new UserBindModel();	
			$this->attachAttribute($userBindModel,$userBind);
			$userBindModel->create_time = time();
	
			if(!$userBindModel->validate()){
				return $this->setNotices($userBindModel->getErrors(),0);
			}
			$userBindModel->save();
			return $userBindModel->getPrimaryKey();
		}
	
		public function saveUserTicket(array $userTicket){
			if(!isset($userTicket['uid']) || $userTicket['uid'] <= 0){
				return $this->setNotices(Yii::t('common','Parameter is empty'),0);
			}

			$userTicketModel = new UserTicketModel();
			$this->attachAttribute($userTicketModel,$userTicket);
			$userTicketModel->create_time = time();
			if(!isset($userTicket['ticket'])){
				if(in_array($userTicket['type'],array(BIND_TYPE_MAIL,BIND_TYPE_UNMAIL,BIND_TYPE_FINDPASS_MAIL))){
					$userTicketModel->ticket = $this->buildTicket();
				}else if(in_array($userTicket['type'],array(BIND_TYPE_MOBILE,BIND_TYPE_UNMOBILE,BIND_TYPE_FINDPASS_MOBILE))){
					$userTicketModel->ticket = $this->getPhoneCode();
				}else{
					$userTicketModel->ticket = $this->buildTicket();
				}
			}
			if(!$userTicketModel->validate()){
				return $this->setNotices($userTicketModel->getErrors(),0);
			}
			$userTicketModel->save();
			return $userTicketModel->getPrimaryKey();
		}
		
		public function getMailList(){
			return array(
				'163.com'=> 'http://mail.163.com',
				'qq.com'=>'http://mail.qq.com',
				'sina.com.cn'=>'http://mail.sina.com.cn',
				'126.com'=>'http://mail.126.com',
				'sohu.com'=>'http://mail.sohu.com',
				'gmail.com'=>'https://mail.google.com/mail/?tab=wm',
				'yahoo.com'=>'http://mail.yahoo.com',
				'yahoo.com.cn'=>'http://mail.cn.yahoo.com/',
				'aliyun.com'=>'http://mail.aliyun.com',
				'hotmail.com'=>'http://www.hotmail.com/',
				'live.com'=>'http://www.hotmail.com/',
				'tom.com'=>'http://mail.tom.com',
				'foxmail.com'=>'http://mail.qq.com',
			);
		}
		
		/**
		 * 绑定过程中间表，表示前台发送短信验证码的记录表，并不是用户的最终绑定手机、邮箱的状态
		 * @param unknown_type $uid
		 * @param unknown_type $type
		 * @return multitype:
		 */
		public function getNewBindByUid($uid,$type){
			if($uid <=0){
				return $this->setNotices(Yii::t('common','Parameter is empty'),0);
			}
			$userBindModel = UserBindModel::model();
			$dbCriteria =$userBindModel->getDbCriteria();
			$dbCriteria->condition = 'uid =:uid AND method=:type ';
			$dbCriteria->order = 'create_time DESC';
			$dbCriteria->limit = 1;
			$dbCriteria->params = array(':uid'=>$uid,':type'=>$type);
			$userBind = $userBindModel->find($dbCriteria);
			if($userBind){
				return $userBind->attributes;
			}
			return array();
		}
		
		public function getValidBindByUid($uid,$type,$content){
			if($uid <=0 || empty($content)){
				return $this->setNotices(Yii::t('common','Parameter is empty'),0);
			}
			
			$userBindModel = UserBindModel::model();
			$dbCriteria =$userBindModel->getDbCriteria();
			$dbCriteria->condition = 'uid =:uid AND method=:type AND method_content=:content';
			$dbCriteria->limit = 1;
			$dbCriteria->params = array(':uid'=>$uid,':type'=>$type,':content'=>$content);
			$userBind = $userBindModel->find($dbCriteria);
			if($userBind){
				return $userBind->attributes;
			}
			return array();
			
		}
		
		public function countTodayValidTicket($uid,$type){
			if($uid <=0){
				return $this->setNotices(Yii::t('common','Parameter is empty'),0);
			}
			$startTime = strtotime(date('Y-m-d 00:01',time()));
			$endTime = strtotime(date('Y-m-d 23:59',time()));
			$userTicketModel = UserTicketModel::model();
			$dbCriteria =$userTicketModel->getDbCriteria();
			$dbCriteria->condition = 'uid =:uid AND type=:type AND create_time >= '.$startTime .' AND create_time <= '.$endTime;
			$dbCriteria->order = 'create_time DESC';
			$dbCriteria->params = array(':uid'=>$uid,':type'=>$type);
			return $userTicketModel->count($dbCriteria);
		}
		
		public function getValidTicketByUid($uid,$type){
			if($uid <=0){
				return $this->setNotices(Yii::t('common','Parameter is empty'),0);
			}
			$userTicketModel = UserTicketModel::model();
			$dbCriteria =$userTicketModel->getDbCriteria();
			$dbCriteria->condition = 'uid =:uid AND type=:type ';
			$dbCriteria->order = 'create_time DESC';
			$dbCriteria->limit = 1;
			$dbCriteria->params = array(':uid'=>$uid,':type'=>$type);
			$userTicket = $userTicketModel->find($dbCriteria);
			if($userTicket){
				return $userTicket->attributes;
			}
			return array();
		}
		
		/**
		 * 生成一个Ticket
		 * 
		 * @return string 返回secret 
		 */
		public function buildTicket(){
			return md5(md5(microtime(true)).uniqid());
		}
		/**
		 * 发送邮件
		 * @param string|array $sendto_email 邮件发送地址
		 * @param string $subject 邮件主题  
		 * @param string $body 邮件正文内容  
		 * @param string|array $attachment 附件
		 * @param string $sendto_name 邮件接受方的姓名，发送方起的名字
		 */
		public function sendMail($sendto_email, $subject, $body='', $attachment=null, $sendto_name=null){
			Yii::import('lib.vendor.phpmailer.*');
			require_once('class.phpmailer.php');
			$mail = new PHPMailer();
			$mail->IsSMTP();
			$mail->Host 	= Yii::app()->params['smtp']['host'];
			$mail->SMTPAuth = true;
			$mail->SMTPSecure = "ssl";
			$mail->Port       = 465;
			$mail->Username = Yii::app()->params['smtp']['username'];
			$mail->Password = Yii::app()->params['smtp']['password'];
			$mail->From 	= Yii::app()->params['smtp']['username'];
			$mail->FromName = "皮皮乐天";
			$mail->CharSet 	= "utf-8";
			$mail->Encoding = "base64";
			
			if(!is_array($sendto_email)) $sendto_email = array($sendto_email);
			foreach($sendto_email as $name => $emailadd){
				if(intval($name) == $name) $name = null;
				$mail->AddAddress($emailadd, $name);
			}
			
			if(!empty($attachment)){
				if(!is_array($attachment)) $attachment = array($attachment);
				foreach($attachment as $att){
					$mail->AddAttachment($att);
				}
			}

			$mail->IsHTML(true);
			$mail->Subject = $subject;
			$mail->Body = $body;
			$mail->AltBody ="text/html";
			$return = $mail->Send();
			if($mail->IsError()){
				//$mail->ErrorInfo;
				$filename = DATA_PATH.'runtimes/email_error.log';
				error_log(date("Y-m-d H:i:s")."邮件发送失败：邮件标题(".$subject.")，收件人(".implode(',', $sendto_email).")\n\r",3,$filename);
			}
			return $return;
		}
		
		public function sendBindMail($uid,$sendto_email,$attachment=null, $sendto_name=null){
			$ticket = $this->getValidTicketByUid($uid,BIND_TYPE_MAIL);
			$href = Yii::app()->request->getHostInfo().'/index.php?r=account/security&type=mail&step=verify&ticket='.$ticket['ticket'];
			$kefuHref = Yii::app()->request->getHostInfo().'/index.php?r=user/findPass&step=kefu';
			$body='<style>.main{ width:600px; margin:0 auto; border:1px solid #666; padding:50px 40px; color:#666;}.blue{ color:#09F;}</style>
<div class="main">
		亲爱的皮皮乐天用户：<br/>
       <p> 欢迎您使用皮皮邮箱绑定服务,在您忘记密码时,可通过邮箱快速找回密码。</p>
       <p> 本链接7天内有效，请在'.date('Y-m-d H:i',time()+7*3600*24).' 前点击下面的链接完成邮箱绑定：</p>
       <p class="blue"><a href="'.$href.'">'.$href.'</a></p>
	   <p>(如果链接无法点击，请将它拷贝到浏览器的地址栏中。)</p>
       <p><strong style="font-size:16px; margin-top:30px; display:block;">温馨提示：</strong></p>
       <p>1、 皮皮统一发送邮箱为'.Yii::app()->params['smtp']['username'].',请注意邮件发送者,谨防假冒!</p>
       <p>2、 此邮件为系统自动发送,请勿直接回复!如您有任何疑问,请联系<a class="blue" href="'.$kefuHref.'">皮皮客服</a></p>
       <p>3、 如果您没有做相关操作,请直接忽略此邮件。</p>
       <p style="font-size:16px; margin-top:50px; display:block;">皮皮乐天</p>
       <p>美女视频,美女主播,美女直播 - 互动综艺第一平台</p>
	   <p>现在就登录吧！<a href="'.Yii::app()->request->getHostInfo().'">'.Yii::app()->request->getHostInfo().'</a></p>
	   <p>'.date('Y-m-d H:i',time()).'</p>
</div>';
		  $this->sendMail($sendto_email,'皮皮乐天帐号--邮箱绑定',$body,$attachment,$sendto_name);
		}
		
		public function sendUnBindMail($uid,$sendto_email,$attachment=null, $sendto_name=null){
			$ticket = $this->getValidTicketByUid($uid,BIND_TYPE_UNMAIL);
			$href = Yii::app()->request->getHostInfo().'/index.php?r=account/security&type=unMail&step=verify&ticket='.$ticket['ticket'];
			$kefuHref = Yii::app()->request->getHostInfo().'/index.php?r=user/findPass&step=kefu';
			$body='<style>.main{ width:600px; margin:0 auto; border:1px solid #666; padding:50px 40px; color:#666;}.blue{ color:#09F;}</style>
<div class="main">
		亲爱的皮皮乐天用户：<br/>
       <p>  欢迎您使用皮皮邮箱解绑服务。</p>
       <p> 本链接7天内有效，请在'.date('Y-m-d H:i',time()+7*3600*24).' 前点击下面的链接完成邮箱绑定：</p>
       <p class="blue"><a href="'.$href.'">'.$href.'</a></p>
	   <p>(如果链接无法点击，请将它拷贝到浏览器的地址栏中。)</p>
       <p><strong style="font-size:16px; margin-top:30px; display:block;">温馨提示：</strong></p>
       <p>1、 皮皮统一发送邮箱为 '.Yii::app()->params['smtp']['username'].',请注意邮件发送者,谨防假冒!</p>
       <p>2、 此邮件为系统自动发送,请勿直接回复!如您有任何疑问,请联系<a class="blue" href="'.$kefuHref.'">皮皮客服</a></p>
       <p>3、 如果您没有做相关操作,请直接忽略此邮件。</p>
       <p style="font-size:16px; margin-top:50px; display:block;">皮皮乐天</p>
       <p>美女视频,美女主播,美女直播 - 互动综艺第一平台</p>
	   <p>现在就登录吧！<a href="'.Yii::app()->request->getHostInfo().'">'.Yii::app()->request->getHostInfo().'</a></p>
	   <p>'.date('Y-m-d H:i',time()).'</p>
</div>';
		  $this->sendMail($sendto_email,'皮皮乐天帐号--邮箱解绑',$body,$attachment,$sendto_name);
		}
		
		public function sendFindPassMail($uid,$sendto_email,$attachment=null, $sendto_name=null){
			$ticket = $this->getValidTicketByUid($uid,BIND_TYPE_FINDPASS_MAIL);
			$href = Yii::app()->request->getHostInfo().'/index.php?r=user/password&type=mail&uid='.$uid.'&ticket='.$ticket['ticket'];
			$kefuHref = Yii::app()->request->getHostInfo().'/index.php?r=user/findPass&step=kefu';
			$body='<style>.main{ width:600px; margin:0 auto; border:1px solid #666; padding:50px 40px; color:#666;}.blue{ color:#09F;}</style>
<div class="main">
		亲爱的皮皮乐天用户：<br/>
       <p>  欢迎您使用皮皮邮箱找回密码服务。</p>
       <p> 本链接7天内有效，请在'.date('Y-m-d H:i',time()+7*3600*24).' 前点击下面的链接完成邮箱绑定：</p>
       <p class="blue"><a href="'.$href.'">'.$href.'</a></p>
	   <p>(如果链接无法点击，请将它拷贝到浏览器的地址栏中。)</p>
       <p><strong style="font-size:16px; margin-top:30px; display:block;">温馨提示：</strong></p>
       <p>1、 皮皮统一发送邮箱为 '.Yii::app()->params['smtp']['username'].',请注意邮件发送者,谨防假冒!</p>
       <p>2、 此邮件为系统自动发送,请勿直接回复!如您有任何疑问,请联系<a class="blue" href="'.$kefuHref.'">皮皮客服</a></p>
       <p>3、 如果您没有做相关操作,请直接忽略此邮件。</p>
       <p style="font-size:16px; margin-top:50px; display:block;">皮皮乐天</p>
       <p>美女视频,美女主播,美女直播 - 互动综艺第一平台</p>
	   <p>现在就登录吧！<a href="'.Yii::app()->request->getHostInfo().'">'.Yii::app()->request->getHostInfo().'</a></p>
	   <p>'.date('Y-m-d H:i',time()).'</p>
</div>';
		  return $this->sendMail($sendto_email,'皮皮乐天帐号--邮箱找回密码',$body,$attachment,$sendto_name);
		}
		
		public function sendBindSms($phone,$code){
			$content = '亲爱的皮皮用户，您绑定的手机验证码为：'.$code.' .请在30分钟内完成验证，如有疑问，请联系客服';
// 			return $this->sendPhoneSms($phone,$content);
			$sms = new PipiSMS();
			$return = $sms -> directSendSMSs(array($phone), $content);
			return $return ? array('status' => 'success') : array('status' => 'fail', 'info' => '发送失败，请重试！');
		}
		
		public function sendUnBindSms($phone,$code){
			$content = '亲爱的皮皮用户，您解绑手机的验证码为：'.$code.' .请在30分钟内完成验证，如有疑问，请联系客服';
// 			return $this->sendPhoneSms($phone,$content);
			$sms = new PipiSMS();
			$return = $sms -> directSendSMSs(array($phone), $content);
			return $return ? array('status' => 'success') : array('status' => 'fail', 'info' => '发送失败，请重试！');
		}
		
		public function sendFindPassSms($phone,$code){
			$content = '亲爱的皮皮用户，您找回密码手机的验证码为：'.$code.' .请在30分钟内完成验证，如有疑问，请联系客服';
// 			return $this->sendPhoneSms($phone,$content);
			$sms = new PipiSMS();
			$return = $sms -> directSendSMSs(array($phone), $content);
			return $return ? array('status' => 'success') : array('status' => 'fail', 'info' => '发送失败，请重试！');
		}
		
		public function getPhoneCode(){
			$numArray = array(0,1,2,3,4,5,6,7,8,9);
			$rand = array_rand($numArray,4);
			$rand = implode('',$rand);
			return $rand;
		}
		
		/**
		 * 查询用户绑定状态
		 * 
		 * @author supeng
		 * @param array $condition
		 * @param int $offset
		 * @param int $pageSize
		 * @param boolean $isLimit
		 * @return multitype:
		 */
		public function searchUserBind(Array $condition=array(),$offset=0,$pageSize=20,$isLimit=true){
			if(!empty($condition['username']) || !empty($condition['nickname']) || !empty($condition['realname'])){
				$service = new UserService();
				$info = $service->searchUserList($offset,$pageSize,$condition,false);
				if($info['uids']){
					$condition['uids'] = $info['uids'];
				}else{
					return array('count'=>0,'list'=>array());
				}
			}
			$model = new UserBindModel();
			$data = $model->searchUserBind($condition,$offset,$pageSize,$isLimit);
			if($data['list']){
				$data['list'] = $this->arToArray($data['list']);
			}
			return $data;
		}
}

?>