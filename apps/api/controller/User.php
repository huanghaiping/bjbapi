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
        $unencryptedOldPwd=isset($this->post['unencryptedOldPwd']) ? $this->post['unencryptedOldPwd'] : "";
        if (empty($unencryptedOldPwd)){
            return output(0, lang('PASSWORD_FORMAT_ERROR'));
        }
        $unencryptedOldPwd=strtoupper(md5($unencryptedOldPwd.config('password_key')));
        $oldPassword = isset($this->post['oldPassword']) ? $this->post['oldPassword'] : "";
        if (empty ($oldPassword) || !letterOrNumber($oldPassword) || $unencryptedOldPwd!=$oldPassword) {
            return output(0, lang('PASSWORD_FORMAT_ERROR'));
        }
        $unencryptedNewPwd=isset($this->post['unencryptedNewPwd']) ? $this->post['unencryptedNewPwd'] : "";
        if (empty($unencryptedNewPwd)){
            return output(0, lang('PASSWORD_FORMAT_ERROR'));
        }
        $unencryptedNewPwd=strtoupper(md5($unencryptedNewPwd.config('password_key')));
        $newPassword = isset($this->post['newPassword']) ? $this->post['newPassword'] : "";
        if (empty ($newPassword) || !letterOrNumber($newPassword) || $unencryptedNewPwd!=$newPassword) {
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
        if (empty($this->userInfo)) {
            return output(0, lang('PLEASE_ENTER_LOGIN'));
        }
        $uid = isset ($this->post ['uid']) ? intval($this->post ['uid']) : ""; //当前登录的用户uid
        if (empty($uid) || ($this->userInfo && $this->userInfo['uid'] != $uid)) {
            return output(0, lang('UID_IS_EMPTY'));
        }
        $fileType=isset($this->post['fileType']) ? $this->post['fileType'] : "";
        if (! isset ( $_FILES ) || empty ( $_FILES ) || empty($fileType)) {
            return output ( 0, lang('PLEASE_SELECT_FILE') );
        }
        $uploadAllowExt = config("images_upload.ext");
        $uploadMaxSize =   config('images_upload.size'); //默认20M
        $uploadPath =config("images_upload.rootPath");
        $config = array ('size'=>$uploadMaxSize,'ext'=>$uploadAllowExt);
        $fileFieldKey="file";
        $thumb = $_FILES [$fileFieldKey];
        if (! empty ( $thumb ['size'] )) {
            $fileObject = $this->request->file ( $fileFieldKey );
            $uploadImageInfo = $fileObject->validate ( $config )->move ( $uploadPath );
            if ($uploadImageInfo) {
                $serverPath = $uploadPath . DS . $uploadImageInfo->getSaveName (); //上传后的图片路径
                //上传文件到阿里云oss云存储
                $aliYunOssModel=model('AliyunOss');
                $saveName=$uploadImageInfo->getFilename();
                $extension=$uploadImageInfo->getExtension();
                $uploadInfo=array('savename'=>$saveName,'url'=>$serverPath,'extension'=>$extension,'typeid'=>0,'size'=>$thumb ['size'],'uid'=>$this->userInfo['uid']);
                $aliYunUploadInfo=$aliYunOssModel->uploadFile($uploadInfo,'images',true,'images');
                if ($aliYunUploadInfo['status']==1){
                    $url=$aliYunUploadInfo['url'];
                    return output(1,lang('UPLOAD_SUCCESS'),array("id"=>$aliYunUploadInfo['fileId'],'url'=>$url,'extension'=>$extension,'ctime'=>time(),'size'=>$thumb ['size']));
                }else{
                    @unlink($serverPath);
                    return output(0,lang('UPLOAD_FAILED').":".$aliYunUploadInfo['msg']);
                }
            } else {
                return output(0,lang('UPLOAD_FAILED').":".$fileObject->getError ());
            }
        }else{
            return output(0,lang('PLEASE_SELECT_FILE'));
        }
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
        if (isset ($this->post ['fileId']))
            $userData ['faceurl'] = ($this->post ['fileId']);
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
        $result = $userModel->user_edit($uid, $userData, $userInfoData);
        if ($result) {
            //更改session信息
            $userInfo =$userModel->getUserInfoByUid($uid);
            $sessionInfo = serialize(array("uid" => $uid, "username" => base64_encode($userInfo['nickname']), 'access_token' => $this->accessToken));
            $sessionModel = model("Session");
            $accessToken = $sessionModel->updateSession($this->accessToken, 'USER_INFO_KEY', $sessionInfo);
            return output(1, lang('MODIFY_SUCCESS'));
        } else {
            return output(0, lang('MODIFY_FAILED'));
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