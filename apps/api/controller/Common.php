<?php

namespace app\api\controller;
class Common extends \think\Controller
{

    protected $accessToken = "";
    protected $userInfo = array(); //保存用户信息
    protected $lang = "";          //保存语言信息
    protected $post = array();

    /**
     *初始化项目开始
     */
    public function _initialize()
    {
        $this->lang = LANG_SET;
        $this->post = $this->filter_post($this->request->post()); //获取POST传过来的值
        $this->accessToken = $this->post && isset($this->post['accessToken']) ? $this->post['accessToken'] : "";
        $action = $this->request->action();
        if (empty($this->accessToken)) { //当请求无效的时候
           exit(outputJson(401, lang('INVALID_REQUEST')) );
        }
        $sessionModel = model ( "Session" );
        $uid = isset ( $this->post ['uid'] ) ? intval ( $this->post ['uid'] ) : 0; //当前登录用户
        $this->userInfo=$sessionModel->_check_user($uid,$this->accessToken);
        if (!$this->userInfo){
            $accessToken=$sessionModel->updateSession($this->accessToken,'USER_INFO_KEY');
            if (empty($accessToken)) {
                exit(outputJson(401, lang('INVALID_REQUEST')) );
            }
            $accessTokenData=unserialize($accessToken);
            if (empty($accessTokenData['access_token']) || $accessTokenData['access_token']!=$this->accessToken){
                exit(outputJson(401, lang('INVALID_REQUEST')) );
            }
        }

    }

    /**
     * 过滤所有的参数
     * Enter description here ...
     * @param unknown_type $post
     */
    private function filter_post($post)
    {
        if ($post && get_magic_quotes_gpc()) {
            foreach ($post as $key => $value) {
                $post [$key] = stripcslashes(trim($value));
            }
        }
        return $post;
    }

}