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
        $appid = isset ($postData ['appid']) ? $postData ['appid'] : ''; //应用的ID
        $appsecret = isset ($postData ['appsecret']) ? $postData ['appsecret'] : ''; //应用的密钥
        if (empty($appid) || empty($appsecret)) {
            return output(0, lang('INVALID_REQUEST'));
        }
        $appModel=model("App");
        $appInfo=$appModel->getApp($appid,$appsecret);
        if (!$appInfo) {
            return output(0, lang('INVALID_REQUEST'));
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

    /**
     * 用户的心跳检测接口
     */
    public function ping(){
        if (!$this->request->isPost()) {
            return output(0, lang('INVALID_REQUEST'));
        }
        $postData=$this->request->post();
        $uid = isset ($postData ['uid']) ? intval($postData ['uid']) : ""; //当前登录的用户uid
        $accessToken = $postData && isset($postData['accessToken']) ? $postData['accessToken'] : "";
        if (empty($accessToken) || empty($uid)) { //当请求无效的时候
           return output(0, lang('UID_IS_EMPTY'));
        }
        $sessionModel = model ( "Session" );
        $userInfo=$sessionModel->_check_user($uid,$accessToken);
        if ($userInfo){
            $userInfo['currentTime']=time();
            return output(1, lang('GET_SUCCESS'),$userInfo);
        }else{
            $userInfo=array('currentTime'=>time());
            return output(401, lang('INVALID_REQUEST'),$userInfo);
        }

    }
}