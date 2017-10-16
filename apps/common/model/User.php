<?php

namespace app\common\model;

class User extends Common
{
    /**
     * +--------------------------------------------------
     * 用户注册数据
     * +--------------------------------------------------
     */
    public function addUser($data, $userDetail = array())
    {
        if (empty($data))
            return false;
        $data['login_time'] = time();
        $data['status'] = 1;
        $data['lang'] = $this->lang;
        $data['faceurl'] = isset($data['faceurl']) && !empty($data['faceurl']) ? $data['faceurl'] : "";
        $result = $this->allowField(true)->data($data)->save();
        if ($result) {
            //保存用户信息数据
            $userDetail['location'] = isset($userDetail['location']) && !empty($userDetail['location']) ? $userDetail['location'] : "";
            $userDetail['province'] = isset($userDetail['province']) && !empty($userDetail['province']) ? $userDetail['province'] : "";
            $userDetail['city'] = isset($userDetail['city']) && !empty($userDetail['city']) ? $userDetail['city'] : "";
            $userDetail['area'] = isset($userDetail['area']) && !empty($userDetail['area']) ? $userDetail['area'] : "";
            $userDetail['userip'] = isset($userDetail['ip']) && !empty($userDetail['ip']) ? $userDetail['ip'] : "";
            $userDetail['mid'] = $this->uid;
            $userDetail['reg_time'] = time();
            $userInfoModel = model("user.UserInfo");
            $userInfoModel->insert($userDetail);
            //默认赠送空间
            $userDiskModel = model('user.UserDisk');
            $defaultRegistedSpace = config('registration_gift_space');
            $diskData = array('uid' => $this->uid, 'total_disk_space' => $defaultRegistedSpace, 'default_disk_space' => $defaultRegistedSpace, 'used_disk_space' => 0);
            $userDiskModel->insert($diskData);
        }
        return $this->uid;
    }


    /**
     * 查询用户信息
     * @param $userAccount
     */
    public function getUserInfoByAccount($userAccount)
    {
        if (empty($userAccount))
            return false;
        $searchWhere = array();
        if (checkEmailFormat($userAccount)) {
            $searchWhere['email'] = $userAccount;
        } elseif (valdeTel($userAccount)) {
            $searchWhere['mobile'] = $userAccount;
        } else {
            $searchWhere['nickname'] = $userAccount;
        }
        $userInfo = $this->where($searchWhere)->limit(1)->field(true)->find();
        return $userInfo;
    }

    /**
     * 获取单个用户的信息
     * @param $uid
     */
    public function getUserInfoByUid($uid)
    {
        if (empty($uid))
            return false;
        $userInfo = $this->getUserInfoByUids($uid);
        return $userInfo && isset($userInfo[$uid]) ? $userInfo[$uid] : false;
    }

    /**
     * 批量获取用户信息
     * @param $uids
     */
    public function getUserInfoByUids($uids)
    {
        if (empty ($uids))
            return false;
        $uids = is_array($uids) ? implode(",", $uids) : $uids;
        $map = array('uid' => array('in', $uids));
        $userList = $this->field(true)->where($map)->order("uid desc")->select();
        if (!$userList->isEmpty()) {
            $userListKey = array();
            foreach ($userList as $key => $value) {
                $userListKey[$value['uid']] = $value;
            }
            unset($userList);
            return $userListKey;
        }
        return false;
    }

    /**
     * 根据UID获取用户的所有信息
     * @param $uid
     */
    public function getDetailUserInfoByUid($uid)
    {
        if (empty ($uid))
            return false;
        $userDetailInfo = cache('ui_' . $uid);
        if (!$userDetailInfo) {
            $field = "u.`uid`,u.`money`,u.`email`,u.`mobile`,u.`faceurl` ,u.`nickname`,u.`country_id`";
            $field .= ",d.`sex` ,d.`description` ,d.`is_email` ,d.`wxid`,d.`reg_time`";
            $userDetailInfo = $this->alias("u")->join("__USER_INFO__ d", 'u.uid=d.mid', "LEFT")->field($field)->where(array('u.uid' => $uid))->limit(1)->find();
            if ($userDetailInfo) {
                //合并用户存储空间信息
                $userDiskModel = model("user.UserDisk");
                $userDiskInfo = $userDiskModel->getUserDiskInfo($uid);
                $userDetailInfo = array_merge($userDetailInfo, $userDiskInfo);
                cache('ui_' . $uid, $userDetailInfo, 3600);
            }
        }
        return $userDetailInfo;
    }

    /**
     * 修改用户资料
     * Enter description here ...
     * @param int $uid 当前登录uid
     * @param Array $data 修改的主表数据
     * @param Array $data_detail 修改的从表数据
     */
    public function user_edit($uid, $userData = array(), $userInfoData = array())
    {
        if (!empty ($userData)) {
            $result = $this->where(array("uid" => $uid))->update($userData);
        }
        if (!empty ($userInfoData)) {
            $result = model("user.UserInfo")->where(array("uid" => $uid))->update($userInfoData);
        }
        cache('ui_' . $uid, null);
        return $result ? true : false;
    }
}