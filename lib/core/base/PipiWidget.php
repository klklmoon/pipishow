<?php

class PipiWidget extends CWidget {
	public $isLogin = false;
	
	public function init(){
		$this->isLogin = !Yii::app()->user->isGuest;
	}
	
	/**
	 * 获取支付平台验证地址
	 * @author hexin
	 * @return string
	 */
	public function goExchange(){
		if($this->isLogin){
			$uid = Yii::app()->user->id;
			$username = Yii::app()->user->name;
			$time = time();
			return Yii::app()->params['exchange'].'?act=login&id='.$uid.'&t='.$time.'&v='.md5('login'.$uid.$username.$time.Yii::app()->params['verification_code']);
		}else return 'javascript:alert("请先登录！");';
	}
	
	public function getTargetHref($href,$isRewrite = false){
		if(empty($href)){
			return '#';
		}
		if(!Yii::app()->request->getParam('target')){
			echo $href;
			return $href;
		}
		if(!$isRewrite){
			if(strrpos($href,'?') !== false){
				$href .= '&target='.$this->target;
			}else{
				$href .= '?target='.$this->target;
			}
			echo $href;
			return $href;
		}
	}
}

?>