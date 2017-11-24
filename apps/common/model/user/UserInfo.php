<?php
namespace app\common\model\user;
class UserInfo extends \app\common\model\Common
{
    /**
     * 用户详细注册信息
     * @param $userDetail
     */
    public function  addUserInfo($uid,$userDetail){
        $userDetail['country_id'] = isset($userDetail['country_id']) && !empty($userDetail['country_id']) ? $userDetail['country_id'] : 0;
        $userDetail['province'] = isset($userDetail['province']) && !empty($userDetail['province']) ? $userDetail['province'] : 0;
        $userDetail['city'] = isset($userDetail['city']) && !empty($userDetail['city']) ? $userDetail['city'] : 0;
        $userDetail['area'] = isset($userDetail['area']) && !empty($userDetail['area']) ? $userDetail['area'] : 0;
        $userDetail['userip'] = isset($userDetail['ip']) && !empty($userDetail['ip']) ? $userDetail['ip'] : "";
        $userDetail['uid'] = $uid;
        $userDetail['update_time'] =time();
        $result=$this->allowField(true)->save($userDetail);
        return $result;
    }
}