<?php

namespace app\api\controller;

class Sms extends Common
{
    /**
     * 短信发送操作类型， 1 注册的时候验证码，2找回密码
     */
    public function send_mobile()
    {
        if (empty($this->post)) {
            return output(0, lang('INVALID_REQUEST'));
        }
        $mobile = isset($this->post['mobile']) ? $this->post['mobile'] : "";
        if (empty($mobile) || valdeTel($mobile)) {
            return output(0, lang('MOBILE_FORMAT_ERROR'));
        }
        $type = isset($this->post['type']) ? intval($this->post['type']) : 0;
        if (empty($type) || !in_array($type, array(1, 2))) {
            return output(0, lang('SMS_FORMAT_ERROR'));
        }
        $smsVerifyModel = model('SmsVerify');
        $result = $smsVerifyModel->send($mobile, $type);
        if ($result) {
            return output(1, lang('SMS_SUCCESS'));
        } else {
            return output(0, $smsVerifyModel->getError());
        }
    }

    /**
     * 发送邮件，1 注册的时候验证码，2找回密码
     */
    public function send_email()
    {
        if (empty($this->post)) {
            return output(0, lang('INVALID_REQUEST'));
        }
        $email = isset($this->post['email']) ? $this->post['email'] : "";
        if (empty($email) || checkEmailFormat($email)) {
            return output(0, lang('MAILBOX_FORMAT_ERROR'));
        }
        $type = isset($this->post['type']) ? intval($this->post['type']) : 0;
        if (empty($type) || !in_array($type, array(1, 2))) {
            return output(0, lang('SMS_FORMAT_ERROR'));
        }
        $emailVerifyModel = model('EmailVerify');
        $result = false;
        switch ($type) {
            //注册邮件
            case 1 :
                $userInfo = array('email' => $email, 'nickname' => "");
                $result = $emailVerifyModel->switchSendMail($userInfo, $type);
                break;
            //找回密码
            case 2 :
                //检查邮箱是否存在
                $userModel = model("User");
                $userInfo = $userModel->getUserInfoByAccount($email);
                if (!$userInfo) {
                    return output(0, lang('MAILBOX_NOT_REGISTERED'));
                }
                if ($userInfo ['status'] == 0) {
                    return output(0, lang('ACCOUNT_IS_DISABLED'));
                }
                $result = $emailVerifyModel->switchSendMail($userInfo, $type);
                break;
        }
        if ($result) {
            return output(1, lang('SMS_SUCCESS'));
        } else {
            return output(0, $emailVerifyModel->getError());
        }
    }

    /**
     * 检查手机号码验证码是否正确(不更改状态)
     */
    public function check_mobile_verify(){
        if (empty($this->post)) {
            return output(0, lang('INVALID_REQUEST'));
        }
        $mobile = isset($this->post['mobile']) ? $this->post['mobile'] : "";
        if (empty($mobile) || valdeTel($mobile)) {
            return output(0, lang('MOBILE_FORMAT_ERROR'));
        }
        $type = isset($this->post['type']) ? intval($this->post['type']) : 0;
        if (empty($type) || !in_array($type, array(1, 2))) {
            return output(0, lang('SMS_FORMAT_ERROR'));
        }
        $verify=isset ( $this->post ['verify'] ) ? $this->post ['verify'] : "";
        if (empty($verify)){
            return output(0,lang('VERIFY_ERROR'));
        }
        $smsVerifyModel = model('SmsVerify');
        $result=$smsVerifyModel->checkVerify($mobile,$verify,$type);
        if ($result){
            return output(1,lang('VERIFY_CODE_CORRECT'));
        }else{
            return output(0,lang('VERIFY_ERROR'));
        }
    }

    /**
     * 检查邮箱验证码是否正确(不更改状态)
     */
    public function check_email_verify(){
        if (empty($this->post)) {
            return output(0, lang('INVALID_REQUEST'));
        }
        $email = isset($this->post['email']) ? $this->post['email'] : "";
        if (empty($email) || checkEmailFormat($email)) {
            return output(0, lang('MAILBOX_FORMAT_ERROR'));
        }
        $type = isset($this->post['type']) ? intval($this->post['type']) : 0;
        if (empty($type) || !in_array($type, array(1, 2))) {
            return output(0, lang('SMS_FORMAT_ERROR'));
        }
        $verify=isset ( $this->post ['verify'] ) ? $this->post ['verify'] : "";
        if (empty($verify)){
            return output(0,lang('VERIFY_ERROR'));
        }
        $emailVerifyModel = model('EmailVerify');
        $result=$emailVerifyModel->checkVerify($email,$verify,$type);
        if ($result){
            return output(1,lang('VERIFY_CODE_CORRECT'));
        }else{
            return output(0,lang('VERIFY_ERROR'));
        }
    }

}