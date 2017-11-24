<?php
use Sms\ThinkSms;
use Aliyun\Core\Config;
use Aliyun\Core\Profile\DefaultProfile;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;
class AlidayuSDK extends ThinkSms {

    private function initialize()
    {
        // 加载区域结点配置
        Config::load();
        // 短信API产品名
        $product = "Dysmsapi";

        // 短信API产品域名
        $domain = "dysmsapi.aliyuncs.com";

        // 暂时不支持多Region
        $region = "cn-hangzhou";

        // 服务结点
        $endPointName = "cn-hangzhou";

        // 初始化用户Profile实例

        $profile = DefaultProfile::getProfile($region, $this->APPKEY, $this->APPSECRET);

        // 增加服务结点
        DefaultProfile::addEndpoint($endPointName, $region, $product, $domain);
        // 初始化AcsClient用于发起请求
        $this->acsClient = new DefaultAcsClient($profile);
    }

    /**
     * 发送担心验证码
     * @param $mobile
     */
    public  function  send($mobile,$verify_id=''){
        if (empty($mobile))
            return false;
        $this->initialize();
        // 初始化SendSmsRequest实例用于设置发送短信的参数
        $request = new SendSmsRequest();
        // 必填，设置雉短信接收号码
        $request->setPhoneNumbers($mobile);
        // 必填，设置签名名称
        $request->setSignName($this->getSign ());
        // 必填，设置模板CODE
        $smsParam=$this->getSmsParam(); //生成验证码

        $request->setTemplateCode($this->getTemplate ( $smsParam ));
        // 可选，设置模板参数
        $request->setTemplateParam(json_encode ( $smsParam ));

        // 可选，设置流水号
        if($verify_id) {
            $request->setOutId($verify_id);
        }
        // 发起访问请求
        $acsResponse = $this->acsClient->getAcsResponse($request);
        if ($acsResponse){
            $sendResult = $this->json_to_array ( $acsResponse );
            if ($sendResult && isset($sendResult['Code']) && $sendResult['Code']=='OK'){
                return array ("status" => 1, "msg" => isset($sendResult ['Message']) ? $sendResult ['Message'] : '' ,"verify"=>$smsParam['number']);
            }else{
                return array ("status" => 0, "msg" => isset($sendResult ['Message']) ? $sendResult ['Message'] : '', 'code' => $sendResult ['Code']);
            }
        }

    }


	/**
	 * 发送短信验证码
	 * Enter description here ...
	 */
	public function send10($mobile,$verify_id='') {
		if (! $mobile) {
			return false;
		}
		require_once "Alidayu/TopSdk.php";
		date_default_timezone_set ( 'Asia/Shanghai' );
		$c = new TopClient ();
		$c->format = "json";
		$c->appkey = $this->APPKEY;
		$c->secretKey = $this->APPSECRET;
		$req = new AlibabaAliqinFcSmsNumSendRequest ();
		$req->setExtend ( $verify_id );
		$req->setSmsType ( "normal" );
		$req->setSmsFreeSignName ( $this->getSign () );
		$smsParam=$this->getSmsParam(); //生成验证码
		$req->setSmsParam ( json_encode ( $smsParam ) );
		$req->setRecNum ( $mobile);
		$req->setSmsTemplateCode ( $this->getTemplate ( $smsParam ) );
		$resp = $c->execute ( $req );
		if ($resp) {
			$result = $this->json_to_array ( $resp );
			$msg=isset($result['msg']) ? $result['msg'] : "";
			if (isset($result['result'])){
				$result=$result['result'];
				if (isset ( $result ['err_code'] ) && isset ( $result ['success'] ) && $result ['err_code'] == 0 && $result ['success']) {
					return array ("status" => 1, "msg" => isset($result ['sub_msg']) ? $result ['sub_msg'] : $msg ,"verify"=>$smsParam['number']);
				}
			}
			if ($result ['code']==15){
			 	$result ['sub_msg']="短信发送太频繁";
			}
			return array ("status" => 0, "msg" => isset($result ['sub_msg']) ? $result ['sub_msg'] : $msg, 'code' => $result ['code'], "sub_code"=>$result['sub_code']);
		} else {
			return array ("status" => 0, "msg" => "短信发送失败" );
		}
	}
	
	/**
	 * 将json转换为数组
	 * Enter description here ...
	 * @param unknown_type $web json字符串
	 */
	protected function json_to_array($web) {
		$arr = array ();
		foreach ( $web as $k => $w ) {
			if (is_object ( $w ))
				$arr [$k] = $this->json_to_array ( $w ); //判断类型是不是object
			else
				$arr [$k] = $w;
		}
		return $arr;
	}

}