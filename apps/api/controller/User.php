<?php
namespace app\api\controller;

class User extends  Common
{
    /**
     * 获取用户信息接口
     */
    public function info(){
        if (empty($this->userInfo)){
            return output(0,lang('PLEASE_ENTER_LOGIN'));
        }

    }
}