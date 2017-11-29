<?php

namespace app\common\model;

class SmsVerify extends Common
{
    /**
     * +---------------------------------------------------
     * 发送手机短信
     * +---------------------------------------------------
     *
     * @param    string $mobile 手机号码
     * @param    int $type 短信类型 1 注册的时候验证码，2找回密码
     */
    public function send($mobile, $type = 1)
    {
        if (empty ($mobile))
            return false;
        $smsConfig = F("SmsConfig_" . $this->lang, '', array('path' => DATA_PATH));
        if (empty($smsConfig) || $smsConfig['status'] != 1) {
            $this->error = "短信服务未开启";
            return false;
        }
        $map = array('mobile' => $mobile, 'type' => $type);
        $result = $this->where($map)->field("ctime,verify,status")->limit(1)->find();
        $isExistsVerify = 0; //判断数据库是否存在记录
        $emailTime = config('Mobile_verify_time');
        if ($result && !empty($emailTime)) {
            if (time() - $result ['ctime'] < $emailTime) {
                $this->error = "请" . $emailTime . "s后再重发";
                return false;
            }
            $isExistsVerify = 1;
        }
        switch ($type) {
            //用户注册
            case 1 :
                if ($result ['status'] == 1) {
                    $this->error = "手机号码已经注册了";
                    return false;
                }
                break;
            //找回密码
            case 2 :
                //检查手机号码是否存在
                $userModel = model("User");
                $userInfo = $userModel->getUserInfoByAccount($mobile);
                if (!$userInfo) {
                    $this->error = lang('MAILBOX_NOT_REGISTERED');
                    return false;
                }
                break;
        }
        //执行程序执行
        $sns = \Sms\ThinkSms::getInstance($smsConfig['sms_type']);
        $result = $sns->send($mobile, $type);
        if ($result && $result['status'] == 1) {
            $verify = isset($result['verify']) ? $result['verify'] : "";
            if (!empty($verify)) {
                $this->saveVerify($mobile, $type, $verify, serialize($result), $isExistsVerify); //保存短信验证码
            }
            return true;
        } else {
            $this->error = "短信发送失败:" . $result['msg'];
            return false;
        }
    }

    /**
     * 保存验证码信息
     * @param $mobile
     * @param $type
     * @param $verify
     * @param string $returnStatus
     * @param int $isExistsVerify
     */
    private function saveVerify($mobile, $type, $verify, $returnStatus = '', $isExistsVerify = 0)
    {
        $ip = request()->ip();
        $verifyData = array('verify' => $verify, 'ctime' => time(), 'return_status' => $returnStatus, 'userip' => $ip, 'type' => $type, 'status' => 0);
        if ($isExistsVerify) {
            $map = array('mobile' => $mobile, 'type' => $type);
            $result = $this->where($map)->update($verifyData);
        } else {
            $verifyData['mobile'] = $mobile;
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
    public function checkVerify($mobile, $verify, $type = 1, $isUpateStatus = 0)
    {
        if (empty ($mobile) || empty ($verify))
            return false;
        $map = array('mobile' => $mobile, 'type' => $type, 'status' => 0);
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
}