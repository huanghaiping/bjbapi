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
        $userAccount = isset ($this->post ['userAccount']) ? $this->post ['userAccount'] : ""; //用户账号可以邮箱，邮箱，手机
        if (empty($userAccount)) {
            return output(0, lang('PLEASE_INPUT_ACCOUNT'));
        }
        $password = isset ($this->post ['password']) ? $this->post ['password'] : "";
        if (empty ($password) || !letterOrNumber($password)) {
            return output(0, lang('PASSWORD_FORMAT_ERROR'));
        }
        $userModel = model("User");
        $userInfo = $userModel->getUserInfoByAccount($userAccount);
        if (!$userInfo) {
            return output(0, lang('MAILBOX_NOT_REGISTERED'));
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
        $clientType = isset ($this->post ['client_type']) ? intval($this->post ['client_type']) : 0;
        $userType = isset ($this->post ['usertype']) ? intval($this->post ['usertype']) : 1;
        $dataDetail = array();
        $dataDetail ['country_id']= isset ($this->post ['country_id']) ? intval($this->post ['country_id']) : 0;
        $dataDetail ['location'] = isset ($this->post ['location']) ? ($this->post ['location']) : "";
        $dataDetail ['sex'] = isset ($this->post ['sex']) ? intval($this->post ['sex']) : 0;
        $dataDetail ['description'] = isset ($this->post ['description']) ? ($this->post ['description']) : "";
        $dataDetail['device_name'] = isset ($this->post ['device_name']) ? $this->post ['device_name'] : "";
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
     * +-----------------------------------------------------------------------
     * 退出登录
     * +-----------------------------------------------------------------------
     */
    public function logout()
    {
        session_unset(); //清空session变量
        session_destroy(); //销毁session数据
        $sessionModel = model("Session"); //生成session 数据
        $sessionModel->destroy_session($this->accessToken);
        return output(1, lang('EXIT_SUCCESS'));
    }
}