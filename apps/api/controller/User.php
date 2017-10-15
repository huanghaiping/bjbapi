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

    /**
     * 修改用户密码
     */
    public function password(){

    }

    /**
     * 上传用户头像接口
     */
    public function upload(){

    }

    /**
     * 查询用户的空间使用情况
     */
    public function search_user_space(){

    }

    /**
     * 修改用户信息
     */
    public function edit(){

    }
}