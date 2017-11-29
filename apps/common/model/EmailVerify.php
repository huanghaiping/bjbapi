<?php

namespace app\common\model;


class EmailVerify extends Common
{
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
     * 邮件发送类型
     * @var array
     */
    protected static $defaultEmailType = array(
        'TYPE_1' => 'SEND_EMAIL_REG', //注册的时候验证码
        'TYPE_2' => 'SEND_EMAIL_FIND_PASSWORD' //找回密码
    );

    /**
     * +---------------------------------------------------
     * 发送邮件
     * +---------------------------------------------------
     *
     * @param    string $type 邮件的key,1 注册的时候验证码，2找回密码
     * @param    string $info 发送的内容
     */
    public function sendmail($type, $info = array())
    {
        if (empty ($type) || !isset ($info ['email']))
            return false;
        $key = self::$defaultEmailType['TYPE_' . $type];
        $userTemplateModel = model("UserTemplate");
        $templateInfo = $userTemplateModel->getSystemTemp($key, $info);
        $result = array();
        if ($templateInfo) {
            $standalone = extension_loaded("redis");
            $redisConfig = config("redis");
            if ($redisConfig && isset($redisConfig['enabled']) && $redisConfig['enabled'] && $standalone) {
                try {
                    require_once EXTEND_PATH . "/Resque/Resque.php";
                    \Resque::setBackend($redisConfig['config'], 0);
                    $redisContent = array('email' => $info ['email'], 'title' => $templateInfo ['title'], 'content' => $templateInfo ['content'], 'name' => $info ['name']);
                    $_reuslt = \Resque::enqueue('emailList', 'Mail', $redisContent, true);
                    if ($_reuslt) {
                        $result['status'] = 1;
                    }
                    $isEmail = false;
                } catch (\Exception $e) {
                    \think\Log::error($e->getMessage());//记录日志
                    $isEmail = true;
                }
            } else {
                $isEmail = true;
            }
            if ($isEmail) {
                $mail = new \Mail\SmtpMail();
                $result = $mail->sendmail($info ['email'], $templateInfo ['title'], $templateInfo ['content'], $info ['name']);
            }
            return $result;
        } else {
            return false;
        }
    }

    /**
     * 创建访问的密钥
     * @param $user_info
     */
    public function enCode($user_info)
    {
        $time = time();
        $password = $user_info ["password"];
        $uid = $user_info ["uid"];
        $x = md5($uid . $password . $time . md5($user_info['email']));
        $code = base64_encode($uid . "_" . $x . "_" . $time . "_" . $user_info['email']);
        return $code;
    }

    /**
     *  解密访问的token
     * @param $code
     */
    public function deCode($code)
    {
        if (empty($code))
            return false;
        $codeString = base64_decode($code);
        if (!strpos($codeString, "_")) {
            $this->error = lang('ILLEGAL_REQUEST');
            return false;
        }
        $codeArray = explode('_', $codeString);
        if (count($codeArray) != 4) {
            $this->error = lang('ILLEGAL_REQUEST');
            return false;
        }
        $param = array('uid' => $codeArray[0], "code" => $codeArray[1], "time" => $codeArray[2], "email" => $codeArray[3]);
        $userInfo = model("User")->getUserByUid($param['uid']);
        if (!$userInfo) {
            $this->error = lang('ILLEGAL_REQUEST');
            return false;
        }
        if (time() - $param['time'] > 3600) {
            $this->error = lang('URL_LINK_HAS_EXPIRED');
            return false;
        }
        $x = md5($param['uid'] . $userInfo['password'] . $param['time'] . md5($param['email']));
        if ($x != $param ['code']) {
            $this->error = lang('ILLEGAL_REQUEST');
            return false;
        }
        return array_merge($param, $userInfo);
    }

    /**
     * 找回密码的操作方法
     * @param Array $userInfo 用户信息
     * @param string $type 发送的邮件类型
     */
    public function switchSendMail($userInfo, $type)
    {
        if (empty($type) || (!isset($userInfo['email'])) || empty($userInfo['email'])) {
            $this->error = "邮箱为空";
            return false;
        }
        $param = array("email" => $userInfo['email'], "name" => isset($userInfo ['nickname']) ? $userInfo ['nickname'] : "");
        //使用URL来做验证邮件
        if (isset($userInfo['isUserUrl']) && $userInfo['isUserUrl'] == 1) {
            switch ($type) {
                //注册发送邮件
                case 1:
                    $change_pass_url = url('Reg/verify', array('code' => $this->enCode($userInfo)));
                    $param['url'] = $change_pass_url;
                    break;
                //找回密码
                case 2:
                    $change_pass_url = url('Reg/resetpwd', array('code' => $this->enCode($userInfo)));
                    $param['url'] = $change_pass_url;
                    break;
            }
            $sendResult = $this->sendmail($type, $param);

         //使用验证码来做验证邮件
        } else {
            $map = array('email' => $userInfo['email'], 'type' => $type);
            $result = $this->where($map)->field("ctime,verify,status")->limit(1)->find();
            $isExistsVerify = 0; //判断数据库是否存在记录
            if ($result) {
                if ($result ['status'] && $type == 1) {
                    $this->error = "邮箱已经注册";
                    return false;
                }
                $emailTime=config('EMAIL_VERIFY_TIME');
                if (time() - $result ['ctime'] < $emailTime) {
                    $this->error = "请".$emailTime."s后再重发";
                    return false;
                }
                $isExistsVerify = 1;
            }
            $param['verify'] = $this->createType();
            $sendResult = $this->sendmail($type, $param);
            if ($sendResult && $sendResult['status'] == 1) {
                $this->saveVerify($userInfo['email'], $type, $param['verify'], serialize($sendResult), $isExistsVerify); //保存短信验证码
            }
        }
        if ($sendResult && isset($sendResult['status']) && $sendResult['status'] == 1) {
            return true;
        } else {
            $msg = isset($result['msg']) ? $result['msg'] : "";
            $this->error = "发送失败:" . $msg;
            return false;
        }
    }

    /**
     * 创建验证码
     * Enter description here ...
     */
    protected function createType()
    {
        $verify = "";
        switch ($this->CodeType) {
            case "1" :
                $chars = "0123456789";
                break; //数字
            case "0" :
                $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
                break; //数字+字母
        }
        for ($i = 0; $i < $this->CodeLength; $i++) {
            $verify .= $chars [rand(0, mb_strlen($chars)-1)];
        }
        return $verify;
    }

    /**
     * 保存验证码信息
     * @param $mobile
     * @param $type
     * @param $verify
     * @param string $returnStatus
     * @param int $isExistsVerify
     */
    private function saveVerify($email, $type, $verify, $returnStatus = '', $isExistsVerify = 0)
    {
        $ip = request()->ip();
        $verifyData = array('verify' => $verify, 'ctime' => time(), 'return_status' => $returnStatus, 'userip' => $ip, 'type' => $type, 'status' => 0);
        if ($isExistsVerify) {
            $map = array('email' => $email, 'type' => $type);
            $result = $this->where($map)->update($verifyData);
        } else {
            $verifyData['email'] = $email;
            $result = $this->insertGetId($verifyData);
        }
        return $result;
    }

    /**
     * +---------------------------------------------------
     * 检查验证码是否正确不更改状态
     * +---------------------------------------------------
     *
     * @param int $mobile 手机号码
     * @param int $verify 短信验证码
     * @param int $type 短信验证码类型
     * @param int $isUpateStatus 判断是否更改验证码状态
     */
    public function checkVerify($email, $verify, $type = 1, $isUpateStatus = 0)
    {
        if (empty ($email) || empty ($verify))
            return false;
        $map = array('email' => $email, 'type' => $type, 'status' => 0);
        $result = $this->where($map)->field("id,verify")->limit(1)->find();
        if (!$result || ($result && isset($result ['verify']) && $result ['verify'] != $verify)) {
            return false;
        } else {
            if ($isUpateStatus) {
                $this->where("id='" . $result ['id'] . "'")->update(array("status" => 1, "check_time" => time()));
            }
            return true;
        }
    }

    /**
     *  发送邮件
     */
    public function send($email,$type){
        if (empty($email)){
            $this->error=lang('MAILBOX_FORMAT_ERROR');
            return false;
        }
        $userModel = model("User");
        $userInfo = $userModel->getUserInfoByAccount($email);
        switch ($type) {
            //注册邮件
            case 1 :
                //检查邮箱是否存在
                if ($userInfo) {
                    $this->error=lang('MAILBOX_HAS_REGISTERED');
                    return false;
                }
                $userInfo = array('email' => $email, 'nickname' => "");
                break;
            //找回密码
            case 2 :
                //检查邮箱是否存在
                if (!$userInfo) {
                    $this->error=lang('MAILBOX_NOT_REGISTERED');
                    return false;
                }
                if ($userInfo ['status'] == 0) {
                    $this->error=lang('ACCOUNT_IS_DISABLED');
                    return false;
                }
                break;
        }
        $result = $this->switchSendMail($userInfo, $type);
        return $result;
    }

}