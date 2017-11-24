<?php
namespace Sms;
abstract class ThinkSms {
	/**
	 * 短信的appkey或者用户名
	 * @var string
	 */
	protected $APPKEY = '';
	
	/**
	 * 短信安全检验码，以数字和字母组成的32位字符，例如：公众帐号secert
	 * @var string
	 */
	protected $APPSECRET = '';
	/**
	 * 调用接口类型,比如是阿里大于
	 * @var string
	 */
	protected $Type = '';
	
	/**
	 * 验证码长度
	 * @var string
	 */
	protected $CodeLength = 6;
	
	/**
	 * 验证码类型
	 * @var string
	 */
	protected $CodeType = 1;
	
	/**
	 * 消息模板
	 * @var string
	 */
	protected $Template = "";
	
	/**
	 * 设置签名
	 * @var string
	 */
	protected $Sign = "";
	
	/**
	 * 设置签名
	 * @var string
	 */
	protected $smsParam = array();
	
	/**
	 * 构造方法，配置应用信息
	 * @param array $token 
	 */
	public function __construct() {
		//设置SDK类型
		$class = get_class ( $this );
		$this->Type = (substr ( $class, 0, strlen ( $class ) - 3 ));
		$sms_info = F("SmsConfig_".LANG_SET,'',array('path'=>DATA_PATH));
		if ($sms_info) { //获取应用配置
			$this->APPKEY = isset ( $sms_info ['appkey'] ) ? $sms_info ['appkey'] : "";
			$this->APPSECRET = isset ( $sms_info ['appsecret'] ) ? $sms_info ['appsecret'] : "";
			$this->CodeLength = isset ( $sms_info ['codelength'] ) ? $sms_info ['codelength'] : "6";
			$this->CodeType = isset ( $sms_info ['codetype'] ) ? $sms_info ['codetype'] : "1";
			$this->Template = isset ( $sms_info ['content'] ) ? $sms_info ['content'] : "";
			$this->Sign = isset ( $sms_info ['sign'] ) ? $sms_info ['sign'] : "";
		}
	}
	
	/**
	 * 取得Oauth实例
	 * @static
	 * @return mixed 返回Oauth
	 */
	public static function getInstance($type) {
		$name = ucfirst ( strtolower ( $type ) ) . 'SDK';
		$filename = EXTEND_PATH . "Sms/{$name}.php";
		if (file_exists ( $filename )) {
			import ( "Sms." . $name );
			if (class_exists ( $name )) {
				return new $name ();
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	/**
	 * 创建验证码
	 * Enter description here ...
	 */
	protected function createType() {
		$verify = "";
		switch ($this->CodeType) {
			case "1" :
				$chars = "0123456789";
				break; //数字
			case "0" :
				$chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
				break; //数字+字母
		}

		for($i = 0; $i < $this->CodeLength; $i ++) {
			$verify .= $chars [rand ( 0, mb_strlen ( $chars )-1 )];
		}
		return $verify;
	}
	
	/**
	 * 创建短信文本
	 * Enter description here ...
	 */
	public function setTemplate($template) {
		$this->Template = $template;
	}
	
	/**
	 * +----------------------------------------------------
	 * 获取短信消息模板内容
	 * +----------------------------------------------------
	 * 
	 * @param 	Array	$info		   模板替换的内容值
	 * @return  Array   $data		  返回模板替换后的数组
	 */
	protected function getTemplate($info) {
		$temp_find = $temp_replace = array ();
		if (is_array ( $info ) && count ( $info ) > 0) {
			$temp_find [] = '{time}';
			$temp_replace [] = date ( "Y年m月d日 H时i分", time () );
			foreach ( $info as $key => $value ) {
				$temp_find [] = "{{$key}}";
				$temp_replace [] = $value;
			}
			$content = str_replace ( $temp_find, $temp_replace, $this->Template );
			return $content;
		} else {
			return false;
		}
	}
	
	/**
	 * 设置签名
	 * Enter description here ...
	 */
	public function setSign($sign) {
		$this->Sign = $sign;
	}
	
	/**
	 * 设置签名
	 * Enter description here ...
	 */
	protected function getSign() {
		return $this->Sign;
	}
	
	public function setSmsParam($smsParam = array()) {
		$this->smsParam = $smsParam;
	}
	protected function getSmsParam() {

		$this->smsParam['code']=$this->smsParam ['number'] = isset ( $this->smsParam ['number'] ) && ! empty ( $this->smsParam ['number'] ) ? $this->smsParam ['number'] : $this->createType ();
		return $this->smsParam;
	}

}