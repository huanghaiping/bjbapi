<?php
/**
 * 预留的空间购买的控制器
 */

namespace app\api\controller;


class Space extends  Common
{
    /**
     *  获取用户的使用空间
     */
    public function user(){
        if (empty($this->userInfo)) {
            return output(0, lang('PLEASE_ENTER_LOGIN'));
        }
        $uid = isset ($this->post ['uid']) ? intval($this->post ['uid']) : ""; //当前登录的用户uid
        if (empty($uid) || ($this->userInfo && $this->userInfo['uid'] != $uid)) {
            return output(0, lang('UID_IS_EMPTY'));
        }
        $userDiskModel=model("user.UserDisk");
        $userDiskInfo=$userDiskModel->getUserDiskInfo($uid);
        return output(1, lang('GET_SUCCESS'), $userDiskInfo);
    }
}