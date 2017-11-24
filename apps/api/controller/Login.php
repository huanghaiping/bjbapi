<?php

namespace app\api\controller;


class Login extends Common
{
    /**
     * +-----------------------------------------------------------------------
     * 用户登陆数据
     * +-----------------------------------------------------------------------
     * @access            public
     * @param            none
     * @return            JSON            $output            用户登陆数据
     */
    public function index()
    {
        if (empty($this->post)) {
            return output(0, lang('INVALID_REQUEST'));
        }
        if ($this->userInfo) {
            return output(0, lang('LOGGED_IN'));
        }
        $userAccount = isset ($this->post ['account']) ? $this->post ['account'] : ""; //用户账号可以邮箱，邮箱，手机
        if (empty($userAccount)) {
            return output(0, lang('PLEASE_INPUT_ACCOUNT'));
        }
        $unencryptedPwd=isset($this->post['unencryptedPwd']) ? $this->post['unencryptedPwd'] : "";
        if (empty($unencryptedPwd)){
            return output(0, lang('PASSWORD_FORMAT_ERROR'));
        }
        $unencryptedPwd=strtoupper(md5($unencryptedPwd.config('password_key')));
        $password = isset ($this->post ['password']) ? $this->post ['password'] : "";
        if (empty ($password) || !letterOrNumber($password) || $unencryptedPwd!=$password ) {
            return output(0, lang('PASSWORD_FORMAT_ERROR'));
        }
        $userModel = model("User");
        $userInfo = $userModel->getUserInfoByAccount($userAccount);
        if (!$userInfo) {
            return output(0, lang('ACCOUNT_ERROR'));
        }
        if ($userInfo ['status'] == 0) {
            return output(0, lang('ACCOUNT_IS_DISABLED'));
        }
        if ($password != $userInfo ['password']) {
            return output(0, lang('PASSWORD_ERROR'));
        }
        $sessionModel = model("Session"); //生成session 数据
        $sessionId = $sessionModel->addsess($userInfo ['uid'], $userInfo ['nickname'], $password);
        $userModel->where(array('uid' => $userInfo['uid']))->update(array('login_time' => time())); //更改最后登录时间和devicetoken
        $userInfo['access_token'] = $sessionId;
        //获取头像
        $userInfo['fileInfo']=$userModel->getUserFaceUrl($userInfo['faceurl']);
        unset($userInfo['faceurl']);
        return output(1, lang('LOGIN_SUCCESS'), $userInfo);
    }

    /**
     * +-----------------------------------------------------------------------
     * 第三方授权登录接口
     * +-----------------------------------------------------------------------
     */
    public function authorized()
    {
        $token = isset ($this->post ['token']) ? trim($this->post ['token']) : '';
        $openId = isset ($this->post ['openid']) ? trim($this->post ['openid']) : '';
        $unionId = isset ($this->post ['unionid']) ? trim($this->post ['unionid']) : '';
        $nickname = isset ($this->post ['nickname']) ? trim($this->post ['nickname']) : '';
        $faceUrl = isset ($this->post ['faceurl']) ? trim($this->post ['faceurl']) : '';
        $clientType = isset ($this->post ['clientType']) ? intval($this->post ['clientType']) : 0;
        $userType = isset ($this->post ['userType']) ? intval($this->post ['userType']) : 1;
        $dataDetail = array();
        $dataDetail ['sex'] = isset ($this->post ['sex']) ? intval($this->post ['sex']) : 0;
        $dataDetail ['description'] = isset ($this->post ['description']) ? ($this->post ['description']) : "";
        $dataDetail['device_name'] = isset ($this->post ['deviceName']) ? $this->post ['deviceName'] : "";
        $dataDetail['ip'] = $this->request->ip();
        if (empty ($openId)) {
            return output(0, lang('OPENID_IS_EMPTY'));
        }
        $userAuthorizedmodel = model("user.UserAuthorized");
        $userModel = model("User");
        $sessionModel = model("Session"); //生成session 数据
        $userAuthorizedInfo = $userAuthorizedmodel->checkAuthorized($openId, $userType, $unionId);
        $isNew = false;
        if ($userAuthorizedInfo) { //如果已经在我们平台注册过
            $userId = $userAuthorizedInfo ['uid'];
            $userInfo = $userModel->getDetailUserInfoByUid($userId);
            if ($userInfo) {
                $sessionId = $sessionModel->addsess($userId, empty ($userInfo ['nickname']) ? $nickname : $userInfo ['nickname'], '');
                $userInfo['access_token'] = $sessionId;
                return output(1, lang('LOGIN_SUCCESS'), $userInfo);
            } else {
                $userAuthorizedmodel->delAuthorized($openId, $userType, $unionId);
                $is_new = true;
            }
        } else { //当已经是注册过的用户的时候
            $is_new = true;
        }
        if ($is_new) {
            $data = array("client_type" => $clientType, "usertype" => $userType, "nickname" => $nickname, "faceurl" => $faceUrl); //注册用户信息
            $userId = $userModel->addUser($data, $dataDetail); //注册用户信息
            if ($userId) { //注册平台信息
                $authorizedData = array("uid" => $userId, "type" => $clientType, "openid" => $openId, "access_token" => $token, "nickname" => $nickname, "unionid" => $unionId);
                $authorizedResult = $userAuthorizedmodel->addAuthorized($authorizedData);
                if ($authorizedResult) {
                    $userInfo = $userModel->getDetailUserInfoByUid($userId);
                    $sessionId = $sessionModel->addsess($userId, $nickname, '');
                    $userInfo['access_token'] = $sessionId;
                    return output(1, lang('LOGIN_SUCCESS'), $userInfo);
                } else {
                    return output(0, lang('LOGIN_FAILED'));
                }
            } else {
                return output(0, lang('LOGIN_FAILED'));
            }
        } else {
            return output(0, lang('LOGGED_IN'));
        }
    }


    /**
     * 找回密码之重设密码
     */
    public function reset_password()
    {
        if (empty($this->post)) {
            return output(0, lang('INVALID_REQUEST'));
        }
        $account = isset ($this->post ['account']) ? $this->post ['account'] : ""; //用户注册账号
        if (empty($account)) {
            return output(0, lang('PLEASE_INPUT_ACCOUNT'));
        }
        if (checkEmailFormat($account)) {
            $errorTip = lang('MAILBOX_NOT_REGISTERED');
            $verifyModel = model('EmailVerify');
        } elseif (valdeTel($account)) {
            $errorTip = lang('MOIBLE_NOT_REG');
            $verifyModel = model('SmsVerify');
        } else {
            return output(0, lang('ENTER_INPUT_EMAIL_MOBILE'));
        }
        $userModel = model("User");
        $isCheckUser = $userModel->getUserInfoByAccount($account);
        if (!$isCheckUser) {
            return output(0, $errorTip);
        }
        $verify = isset ($this->post ['verify']) ? $this->post ['verify'] : "";
        if (empty($verify) || !$verifyModel->checkVerify($account, $verify, 2)) {
            return output(0, lang('VERIFY_ERROR'));
        }
        $unencryptedPwd=isset($this->post['unencryptedPwd']) ? $this->post['unencryptedPwd'] : "";
        if (empty($unencryptedPwd)){
            return output(0, lang('PASSWORD_FORMAT_ERROR'));
        }
        $unencryptedPwd=strtoupper(md5($unencryptedPwd.config('password_key')));
        $password= isset ( $this->post ['password'] ) ? $this->post ['password'] : "";
        if (empty ( $password ) || ! letterOrNumber ( $password ) || $unencryptedPwd!=$password ) {
            return output(0,lang('PASSWORD_FORMAT_ERROR'));
        }
        $userModel->user_edit($isCheckUser['uid'],array('password'=>$password)); //修改密码
        return output(1,lang('MODIFY_SUCCESS'));
    }

    /**
     * IOS客户端提交的devicetoken
     */
    public function devicetoken()
    {
        $data=array();
        $data ['uid'] = isset ($this->post ['uid']) ? intval($this->post ['uid']) : "";
        $data ['devicetoken'] = isset ($this->post ['devicetoken']) ? trim($this->post ['devicetoken']) : '';
        $data ['appid'] = (isset ($this->post ['appid'])) ? $this->post ['appid'] : "";
        $data ['udid'] = isset ($this->post ['udid']) ? trim($this->post ['udid']) : '';
        $data ['isyueyu'] = (isset ($this->post ['isyueyu']) && is_numeric($this->post ['isyueyu'])) ? ( int )$this->post ['isyueyu'] : 0;
        $data ['system'] = isset ($this->post ['deviceName']) ? trim($this->post ['deviceName']) : '';
        if (empty ($data ['udid'])) {
            return $this->output(0, lang('UNIQUE_IDENTIFIER_IS_EMPTY'));
        }
        if (empty ($data ['devicetoken'])) {
            return $this->output(0, lang('DEVICETOKEN_IS_EMPTY'));
        }
        $devicetokenModel = model("Devicetoken");
        $result = $devicetokenModel->addDevicetoken($data);
        if ($result) {
            return output(1, lang('SUBMIT_SUCCESS'));
        } else {
            return output(0, lang('SUBMIT_FAILED'));
        }
    }
}