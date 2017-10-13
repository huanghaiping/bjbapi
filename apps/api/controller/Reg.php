<?php
namespace app\api\controller;
class Reg extends  Common
{
    /**
     * +-----------------------------------------------------------------------
     * 用户邮箱注册数据
     * +-----------------------------------------------------------------------
     * @access            public
     * @param            none
     * @return            JSON            $output            用户信息数据
     */
    public function email()
    {
        if (empty($this->post)) {
            return output(0, lang('INVALID_REQUEST'));
        }
        $regData=array();
        $regData['email']= isset ( $this->post ['email'] ) ? $this->post ['email'] : ""; //邮箱
        if (empty($regData['email'])|| !checkEmailFormat($regData['email'])){
            return output(0,lang('MAILBOX_FORMAT_ERROR'));
        }
        //检查邮箱是否存在
        $userModel=model("User");
        if ($userModel->getUserInfoByAccount($regData['email'])){
            return output(0,lang('MAILBOX_HAS_REGISTERED'));
        }
        $regData['password'] = isset ( $this->post ['password'] ) ? $this->post ['password'] : "";
        if (empty ( $regData['password'] ) || ! letterOrNumber ( $regData['password'] )) {
            return output ( 0, lang('PASSWORD_FORMAT_ERROR'));
        }
        $emailVerifyModel = model('EmailVerify');
        $verify=isset ( $this->post ['verify'] ) ? $this->post ['verify'] : "";
        if (empty($verify) || !$emailVerifyModel->checkVerify($regData['email'],$verify,1)){
            return output(0,lang('VERIFY_ERROR'));
        }
        $regData['nickname'] = isset ( $this->post ['nickname'] ) ? $this->post ['nickname'] : "";
        if (empty($regData['nickname'])){
            return output ( 0, lang('NICKNAME_EMPTY'));
        }
        if ($userModel->getUserInfoByAccount($regData['nickname'])){
            return output ( 0, lang('NICKNAME_ALREADY_EXISTS'));
        }
        $regData['client_type'] = isset ( $this->post ['client_type'] ) ? intval ( $this->post ['client_type'] ) : 0;
        $regData['usertype'] = isset ( $this->post ['usertype'] ) ? intval ( $this->post ['usertype'] ) : 1;
        $device_name = isset ( $this->post ['device_name'] ) ? $this->post ['device_name'] : "";
        $userInfoData=array('device_name'=>$device_name,'userip'=>$this->request->ip());
        $uid=$userModel->addUser($regData,$userInfoData);
        if($uid){
            //保存session数据
            $sessionModel = model( "Session" ); //生成session 数据
            $sessionId = $sessionModel->addsess ( $uid,$regData['nickname'],$regData['password']);
            //返回用户信息
            $userInfo=$userModel->getDetailUserInfoByUid($uid);
            $userInfo['access_token']=$sessionId;
            $emailVerifyModel->checkVerify($regData['mobile'],$verify,1,1);//更改验证码状态
            return output ( 1, lang('LOGIN_WAS_SUCCESSFUL') ,$userInfo);
        }else{
            return output ( 0, lang('REG_HAS_FAILED') );
        }
    }

    /**
     * +-----------------------------------------------------------------------
     * 用户手机注册数据
     * +-----------------------------------------------------------------------
     * @access            public
     * @param            none
     * @return            JSON            $output            用户信息数据
     */
    public function mobile(){
        if (empty($this->post)) {
            return output(0, lang('INVALID_REQUEST'));
        }
        $regData=array();
        $regData['mobile']= isset ( $this->post ['mobile'] ) ? $this->post ['mobile'] : ""; //手机号码
        if (empty($regData['mobile'])|| !valdeTel($regData['mobile'])){
            return output(0,lang('MOBILE_FORMAT_ERROR'));
        }
        //检查手机号码是否存在
        $userModel=model("User");
        if ($userModel->getUserInfoByAccount($regData['mobile'])){
            return output(0,lang('MOBILE_HAS_REGISTERED'));
        }
        //检查验证码是否正确
        $smsVerifyModel=model('SmsVerify');
        $verify=isset ( $this->post ['verify'] ) ? $this->post ['verify'] : "";
        if (empty($verify) || !$smsVerifyModel->checkVerify($regData['mobile'],$verify,1)){
            return output(0,lang('VERIFY_ERROR'));
        }
        $regData['password'] = isset ( $this->post ['password'] ) ? $this->post ['password'] : "";
        if (empty ( $regData['password'] ) || ! letterOrNumber ( $regData['password'] )) {
            return output ( 0, lang('PASSWORD_FORMAT_ERROR'));
        }
        $regData['nickname'] = isset ( $this->post ['nickname'] ) ? $this->post ['nickname'] : "";
        if (empty($regData['nickname'])){
            return output ( 0, lang('NICKNAME_EMPTY'));
        }
        if ($userModel->getUserInfoByAccount($regData['nickname'])){
            return output ( 0, lang('NICKNAME_ALREADY_EXISTS'));
        }
        $regData['client_type'] = isset ( $this->post ['client_type'] ) ? intval ( $this->post ['client_type'] ) : 0;
        $regData['usertype'] = isset ( $this->post ['usertype'] ) ? intval ( $this->post ['usertype'] ) : 1;
        $device_name = isset ( $this->post ['device_name'] ) ? $this->post ['device_name'] : "";
        $userInfoData=array('device_name'=>$device_name,'userip'=>$this->request->ip());
        $uid=$userModel->addUser($regData,$userInfoData);
        if($uid){
            //保存session数据
            $sessionModel = model( "Session" ); //生成session 数据
            $sessionId = $sessionModel->addsess ( $uid,$regData['nickname'],$regData['password']);
            //返回用户信息
            $userInfo=$userModel->getDetailUserInfoByUid($uid);
            $userInfo['access_token']=$sessionId;
            $smsVerifyModel->checkVerify($regData['mobile'],$verify,1,1);//更改验证码状态
            return output ( 1, lang('LOGIN_WAS_SUCCESSFUL') ,$userInfo);
        }else{
            return output ( 0, lang('REG_HAS_FAILED') );
        }
    }

    /**
     * 检查手机号码是否注册
     */
    public function checkmobile(){
        if (empty($this->post)) {
            return output(0, lang('INVALID_REQUEST'));
        }
        $mobile= isset ( $this->post ['mobile'] ) ? $this->post ['mobile'] : ""; //手机号码
        if (empty($mobile)|| !valdeTel($mobile)){
            return output(0,lang('MOBILE_FORMAT_ERROR'));
        }
        $userModel=model("User");
        if ($userModel->getUserInfoByAccount($mobile)){
            return output(0,lang('MOBILE_HAS_REGISTERED'));
        }else{
            return output(1,lang('MOBILE_ALREADY_USERED'));
        }
    }

    /**
     * 检查邮箱是否注册
     */
    public function checkemail(){
        if (empty($this->post)) {
            return output(0, lang('INVALID_REQUEST'));
        }
        $email= isset ( $this->post ['email'] ) ? $this->post ['email'] : ""; //邮箱
        if (empty($email)|| !checkEmailFormat($email)){
            return output(0,lang('MAILBOX_FORMAT_ERROR'));
        }
        //检查邮箱是否存在
        $userModel=model("User");
        if ($userModel->getUserInfoByAccount($email)){
            return output(0,lang('MAILBOX_HAS_REGISTERED'));
        }else{
            return output(1,lang('MAILBOX_ALREADY_USERED'));
        }
    }

}