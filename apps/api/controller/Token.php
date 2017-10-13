<?php

namespace app\api\controller;

class Token extends \think\Controller
{
    /**
     * +-----------------------------------------------------------------------
     * 获取应用访问的token接口
     * +-----------------------------------------------------------------------
     * @return            JSON            $output            用户登陆数据
     */
    public function get()
    {
        if (!$this->request->isPost()) {
            return output(0, lang('INVALID_REQUEST'));
        }
        $postData=$this->request->post();
        $appid = isset ($postData ['appid']) ? $postData ['appid'] : time(); //应用的ID
        $appsecret = isset ($postData ['appsecret']) ? $postData ['appsecret'] : md5(time()); //应用的密钥
        if ($appid != '2017100986524496' && $appsecret != '56b8285d6789f2890554885788eb0c64') {
            return output(401, lang('INVALID_REQUEST'));
        }
        $sessionModel = model("Session");
        $sessionModel->gen_session_id($appid, $appsecret); //生成session 数据
        $session_id = $sessionModel->get_session_id();
        $returnData = array('access_token' => $session_id);
        //清除session
        $sessionModel->deleteIpSession();
        $sessionModel->updateSession($session_id, 'USER_INFO_KEY', serialize($returnData)); //保存session数据
        return output(1, lang('GET_SUCCESS'), $returnData);
    }
}