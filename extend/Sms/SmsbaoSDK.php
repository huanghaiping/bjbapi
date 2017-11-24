<?php
use Sms\ThinkSms;
class SmsbaoSDK extends ThinkSms {
	
	protected $baseUrl = 'http://api.smsbao.com/sms';
	
	/**
	 * +-----------------------------------------
	 * 发送短信验证码
	 * +-----------------------------------------
	 * 
	 * @param	string $mobile	手机号码
	 * @param   string $content 发送的内容
	 */
	public function send($mobile, $verify_id = "") {
		$signName = "[" . $this->getSign () . "]";
		$smsParam = $this->getSmsParam ();
		$template = $this->getTemplate ( $smsParam );
		if (! empty ( $template )) {
			$template = str_replace ( "$", "", $template );
		}
		$template = $signName . $template;
		$url = $this->baseUrl . "?u=" . $this->APPKEY . "&p=" . md5 ( $this->APPSECRET ) . "&m=" . $mobile . "&c=" . urlencode ( $template );
		$_result = $this->get_url_content ( $url );
		if ($_result == 0) {
			return array ("status" => 1, "msg" => $this->getStatus ( $_result ), "verify" => $smsParam ['number'] );
		} else {
			return array ("status" => 0, "msg" => $this->getStatus ( $_result ) );
		}
	}
	
	/**
	 * 获取状态信息
	 * Enter description here ...
	 * @param unknown_type $statusId
	 */
	protected function getStatus($statusId = '') {
		$statusStr = array ("0" => "短信发送成功", "-1" => "参数不全", "-2" => "服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！", "30" => "密码错误", "40" => "账号不存在", "41" => "余额不足", "42" => "帐户已过期", "43" => "IP地址限制", "50" => "内容含有敏感词", "51" => "手机号码格式错误" );
		return $statusId != '' ? $statusStr [$statusId] : $statusStr;
	}
	
	/**
	 * 远程抓取内容
	 * return
	 * @param unknown_type $url
	 */
	protected function get_url_content($url) {
		set_time_limit ( 0 );
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
		$result = curl_exec ( $ch );
		curl_close ( $ch );
		return $result;
	}
}