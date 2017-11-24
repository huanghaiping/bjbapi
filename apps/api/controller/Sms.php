<?php

namespace app\api\controller;

class Sms extends Common
{
    /**
     * 发送操作类型， 1 注册的时候验证码，2找回密码
     */
    public function send()
    {
        if (empty($this->post)) {
            return output(0, lang('INVALID_REQUEST'));
        }
        $account = isset($this->post['account']) ? $this->post['account'] : "";
        if (empty($account)) {
            return output(0, lang('PLEASE_INPUT_ACCOUNT'));
        }
        $type = isset($this->post['type']) ? intval($this->post['type']) : 0;
        if (empty($type) || !in_array($type, array(1, 2))) {
            return output(0, lang('SMS_FORMAT_ERROR'));
        }
        if (checkEmailFormat($account)) {
            $verifyModel = model('EmailVerify');
        } elseif (valdeTel($account)) {
            $verifyModel = model('SmsVerify');
        } else {
            return output(0, lang('ENTER_INPUT_EMAIL_MOBILE'));
        }
        $result = $verifyModel->send($account, $type);
        if ($result) {
            return output(1, lang('SMS_SUCCESS'));
        } else {
            return output(0, $verifyModel->getError());
        }
    }

    /**
     * 检查验证码是否正确(不更改状态)
     */
    public function verify()
    {
        if (empty($this->post)) {
            return output(0, lang('INVALID_REQUEST'));
        }
        $account = isset($this->post['account']) ? $this->post['account'] : "";
        if (empty($account)) {
            return output(0, lang('PLEASE_INPUT_ACCOUNT'));
        }
        if (checkEmailFormat($account)) {
            $verifyModel = model('EmailVerify');
        } elseif (valdeTel($account)) {
            $verifyModel = model('SmsVerify');
        } else {
            return output(0, lang('ENTER_INPUT_EMAIL_MOBILE'));
        }
        $type = isset($this->post['type']) ? intval($this->post['type']) : 0;
        if (empty($type) || !in_array($type, array(1, 2))) {
            return output(0, lang('SMS_FORMAT_ERROR'));
        }
        $verify = isset ($this->post ['verify']) ? $this->post ['verify'] : "";
        if (empty($verify)) {
            return output(0, lang('VERIFY_ERROR'));
        }
        $result = $verifyModel->checkVerify($account, $verify, $type);
        if ($result) {
            return output(1, lang('VERIFY_CODE_CORRECT'));
        } else {
            return output(0, lang('VERIFY_ERROR'));
        }
    }

}