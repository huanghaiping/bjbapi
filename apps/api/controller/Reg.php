<?php

namespace app\api\controller;
class Reg extends Common
{
    /**
     * +-----------------------------------------------------------------------
     * 用户注册数据
     * +-----------------------------------------------------------------------
     * @access            public
     * @param            none
     * @return            JSON            $output            用户信息数据
     */
    public function index()
    {
        if (empty($this->post)) {
            return output(0, lang('INVALID_REQUEST'));
        }
        $regData = array();
        $account = isset ($this->post ['account']) ? $this->post ['account'] : ""; //用户注册账号
        if (empty($account)) {
            return output(0, lang('PLEASE_INPUT_ACCOUNT'));
        }
        if (checkEmailFormat($account)) {
            $errorTip = lang('MAILBOX_HAS_REGISTERED');
            $regData['email'] = $account;
            $verifyModel = model('EmailVerify');
        } elseif (valdeTel($account)) {
            $errorTip = lang('MOBILE_HAS_REGISTERED');
            $regData['mobile'] = $account;
            $verifyModel = model('SmsVerify');
        } else {
            return output(0, lang('ENTER_INPUT_EMAIL_MOBILE'));
        }
        $userModel = model("User");
        $isCheckUser = $userModel->getUserInfoByAccount($account);
        if ($isCheckUser) {
            return output(0, $errorTip);
        }
        $verify = isset ($this->post ['verify']) ? $this->post ['verify'] : "";
        if (empty($verify) || !$verifyModel->checkVerify($account, $verify, 1)) {
            return output(0, lang('VERIFY_ERROR'));
        }
        $regData['password'] = isset ($this->post ['password']) ? $this->post ['password'] : "";
        if (empty ($regData['password']) || !letterOrNumber($regData['password'])) {
            return output(0, lang('PASSWORD_FORMAT_ERROR'));
        }
        $regData['nickname'] = isset ($this->post ['nickname']) ? $this->post ['nickname'] : "";
        if (empty($regData['nickname'])) {
            return output(0, lang('NICKNAME_EMPTY'));
        }
        if ($userModel->getUserInfoByAccount($regData['nickname'])) {
            return output(0, lang('NICKNAME_ALREADY_EXISTS'));
        }
        $regData['client_type'] = isset ($this->post ['client_type']) ? intval($this->post ['client_type']) : 0;
        $regData['usertype'] = isset ($this->post ['usertype']) ? intval($this->post ['usertype']) : 1;
        $device_name = isset ($this->post ['device_name']) ? $this->post ['device_name'] : "";
        $userInfoData = array('device_name' => $device_name, 'userip' => $this->request->ip());
        $uid = $userModel->addUser($regData, $userInfoData);
        if ($uid) {
            //保存session数据
            $sessionModel = model("Session"); //生成session 数据
            $sessionId = $sessionModel->addsess($uid, $regData['nickname'], $regData['password']);
            //返回用户信息
            $userInfo = $userModel->getDetailUserInfoByUid($uid);
            $userInfo['access_token'] = $sessionId;
            $verifyModel->checkVerify($account, $verify, 1, 1);//更改验证码状态
            return output(1, lang('LOGIN_WAS_SUCCESSFUL'), $userInfo);
        } else {
            return output(0, lang('REG_HAS_FAILED'));
        }
    }

    /**
     * 检查手机号码是否注册
     */
    public function check_mobile()
    {
        if (empty($this->post)) {
            return output(0, lang('INVALID_REQUEST'));
        }
        $mobile = isset ($this->post ['mobile']) ? $this->post ['mobile'] : ""; //手机号码
        if (empty($mobile) || !valdeTel($mobile)) {
            return output(0, lang('MOBILE_FORMAT_ERROR'));
        }
        $userModel = model("User");
        if ($userModel->getUserInfoByAccount($mobile)) {
            return output(0, lang('MOBILE_HAS_REGISTERED'));
        } else {
            return output(1, lang('MOBILE_ALREADY_USERED'));
        }
    }

    /**
     * 检查邮箱是否注册
     */
    public function check_email()
    {
        if (empty($this->post)) {
            return output(0, lang('INVALID_REQUEST'));
        }
        $email = isset ($this->post ['email']) ? $this->post ['email'] : ""; //邮箱
        if (empty($email) || !checkEmailFormat($email)) {
            return output(0, lang('MAILBOX_FORMAT_ERROR'));
        }
        //检查邮箱是否存在
        $userModel = model("User");
        if ($userModel->getUserInfoByAccount($email)) {
            return output(0, lang('MAILBOX_HAS_REGISTERED'));
        } else {
            return output(1, lang('MAILBOX_ALREADY_USERED'));
        }
    }

}