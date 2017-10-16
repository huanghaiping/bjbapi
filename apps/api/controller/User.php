<?php

namespace app\api\controller;

class User extends Common
{
    /**
     * 获取用户信息接口
     */
    public function info()
    {
        if (empty($this->userInfo)) {
            return output(0, lang('PLEASE_ENTER_LOGIN'));
        }
        $uid = isset ($this->post ['uid']) ? intval($this->post ['uid']) : ""; //当前登录的用户uid
        if (empty($uid) || ($this->userInfo && $this->userInfo['uid'] != $uid)) {
            return $this->output(11, lang('UID_IS_EMPTY'));
        }
        $userModel = model("User");
        $userInfo = $userModel->getDetailUserInfoByUid($uid);
        if ($userInfo) {
            return output(1, lang('GET_SUCCESS'), $userInfo);
        } else {
            return output(0, lang('GET_FAILURES'));
        }
    }

    /**
     * 修改用户密码
     */
    public function password()
    {
        if (empty($this->userInfo)) {
            return output(0, lang('PLEASE_ENTER_LOGIN'));
        }
        $uid = isset ($this->post ['uid']) ? intval($this->post ['uid']) : ""; //当前登录的用户uid
        if (empty($uid) || ($this->userInfo && $this->userInfo['uid'] != $uid)) {
            return output(0, lang('UID_IS_EMPTY'));
        }
        $oldPassword = isset($this->post['old_password']) ? $this->post['old_password'] : "";
        if (empty ($oldPassword) || !letterOrNumber($oldPassword)) {
            return output(0, lang('PASSWORD_FORMAT_ERROR'));
        }
        $newPassword = isset($this->post['new_password']) ? $this->post['new_password'] : "";
        if (empty ($newPassword) || !letterOrNumber($newPassword)) {
            return output(0, lang('PASSWORD_FORMAT_ERROR'));
        }
        $userModel = model("User");
        $userInfo = $userModel->getUserInfoByUid($uid);
        if (!$userInfo) {
            return output(0, lang('PARAM_ERROR'));
        }
        if ($userInfo['password'] != $oldPassword) {
            return output(0, lang('PASSWORD_ERROR'));
        }
        $result = $userModel->where(array('uid' => $uid))->update(array('password' => $newPassword));
        if ($result) {
            return output(1, lang('MODIFY_SUCCESS'));
        } else {
            return output(0, lang('MODIFY_FAILURE'));
        }

    }

    /**
     * 上传用户头像接口
     */
    public function upload()
    {

    }

    /**
     * 修改用户信息
     */
    public function edit()
    {
        if (empty($this->userInfo)) {
            return output(0, lang('PLEASE_ENTER_LOGIN'));
        }
        $userData = $userInfoData = array();
        $uid = isset ($this->post ['uid']) ? intval($this->post ['uid']) : ""; //当前登录的用户uid
        if (empty($uid) || ($this->userInfo && $this->userInfo['uid'] != $uid)) {
            return output(0, lang('UID_IS_EMPTY'));
        }
        if (isset ($this->post ['faceurl']))
            $userData ['faceurl'] = ($this->post ['faceurl']);
        if (isset ($this->post ['nickname']))
            $userData ['nickname'] = addSlashesFun($this->post ['nickname']);

        if (isset ($this->post ['sex']))
            $userInfoData ['sex'] = intval($this->post ['sex']);
        if (isset ($this->post ['qq']))
            $userInfoData ['qq'] = ($this->post ['qq']);
        if (isset ($this->post ['country_id']))
            $userInfoData ['country_id'] = intval($this->post ['country_id']);
        if (isset ($this->post ['province']))
            $userInfoData ['province'] = intval($this->post ['province']);
        if (isset ($this->post ['city']))
            $userInfoData ['city'] = intval($this->post ['city']);
        if (isset ($this->post ['district']))
            $userInfoData ['district'] = intval($this->post ['district']);
        if (isset ($this->post ['twon']))
            $userInfoData ['twon'] = intval($this->post ['twon']);
        if (isset ($this->post ['birth']))
            $userInfoData ['birth'] = ($this->post ['birth']);

        $userInfoData ['update_time'] = time();
        $userModel = model("User");
        //获取用户信息
        $userInfo = $userModel->getUserInfoByUid($uid);
        if (!$userInfo) {
            return output(0, lang('USER_DOES_NOT_EXIST'));
        }
        if (!empty($userData ['nickname']) && $userData ['nickname'] != $userInfo['nickname']) {
            if ($userModel->getUserInfoByAccount($userData ['nickname'])) {
                return output(0, lang('NICKNAME_ALREADY_EXISTS'));
            }
        }
        $result = $userModel->user_edit($uid, $userData, $userInfoData);
        if ($result) {
            //更改session信息
            $userInfo =$userModel->getUserInfoByUid($uid);
            $sessionInfo = serialize(array("uid" => $uid, "username" => base64_encode($userInfo['nickname']), 'access_token' => $this->accessToken));
            $sessionModel = model("Session");
            $accessToken = $sessionModel->updateSession($this->accessToken, 'USER_INFO_KEY', $sessionInfo);
            return $this->output(1, lang('MODIFY_SUCCESS'));
        } else {
            return $this->output(0, lang('MODIFY_FAILED'));
        }
    }
}