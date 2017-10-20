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
        $data['login_time'] = $data['reg_time'] = time();
        $data['status'] = 1;
        $data['lang'] = $this->lang;
        $faceUrl = isset($data['faceurl']) && !empty($data['faceurl']) ? $data['faceurl'] : "";
        unset($data['faceurl']);
        $result = $this->allowField(true)->data($data)->save();
        if ($result) {
            //保存用户头像
            if (!empty($faceUrl) && !is_numeric($faceUrl)) {
                $ossLogData = array('oss_url' =>$faceUrl, 'local_url' => $faceUrl, 'ctime' => time(), 'typeid' => 0,'uid'=>$this->uid);
                $fileId= model("OssLog")->insertGetId($ossLogData);
                $this->where("uid",$this->uid)->setField("faceurl",$fileId);
            }
            //保存用户信息数据
            model("user.UserInfo")->addUserInfo($this->uid,$userDetail);
            //默认赠送空间
            model('user.UserDisk')->setDefaultUserSpace($this->uid);
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
            $userList = $userList->toArray();
            $userListKey = array();
            $ossLogModel = model("OssLog");
            $fileList = $ossLogModel->getFileInfoByIds(array_unique(getSubByKey($userList, "faceurl")));
            foreach ($userList as $key => $value) {
                if (!empty($value['faceurl'])) {
                    $fileInfo = $fileList && array_key_exists($value['faceurl'], $fileList) ? $fileList[$value['faceurl']]['oss_url'] : '';
                    $value['fileInfo'] = $fileInfo ? $ossLogModel->getFormatFile($fileInfo) : array();
                    unset($value['faceurl']);
                }
                $userListKey[$value['uid']] = $value;
            }
            unset($userList);
            return $userListKey;
        }
        return false;
    }

    /**
     * 获取用户的头像
     * @param $fileId
     */
    public function getUserFaceUrl($fileId)
    {
        if (empty($fileId))
            return array();
        $ossLogModel = model("OssLog");
        $fileInfo = $ossLogModel->getFileInfoById($fileId);
        if ($fileInfo) {
            return $ossLogModel->getFormatFile($fileInfo);
        }
        return array();
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
            $field = "u.`uid`,u.`money`,u.`email`,u.`mobile`,u.`faceurl` ,u.`nickname`,u.`reg_time`,u.`login_time`";
            $field .= ",d.`sex` ,d.`description` ,d.`qq`,d.`country_id`";
            $userDetailInfo = $this->alias("u")->join("__USER_INFO__ d", 'u.uid=d.uid', "LEFT")->field($field)->where(array('u.uid' => $uid))->limit(1)->find();
            if ($userDetailInfo) {
                $userDetailInfo = $userDetailInfo->toArray();
                //获取头像路径
                $userDetailInfo['fileInfo'] = $this->getUserFaceUrl($userDetailInfo['faceurl']);
                unset($userDetailInfo['faceurl']);
                //合并用户存储空间信息
                $userDiskModel = model("user.UserDisk");
                $userDetailInfo['spaceInfo'] = $userDiskModel->getUserDiskInfo($uid);
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
            //修改头像的时候删除旧文件
            if (isset($userData['faceurl']) && !empty($userData['faceurl'])) {
                $faceUrlId = $this->where('uid', $uid)->value("faceurl");
                if ($faceUrlId && $faceUrlId != $userData['faceurl']) {
                    model("AliyunOss")->deleteObject($faceUrlId);
                }
            }
            $result = $this->where(array("uid" => $uid))->update($userData);
        }
        if (!empty ($userInfoData)) {
            $result = model("user.UserInfo")->where(array("uid" => $uid))->update($userInfoData);
        }
        cache('ui_' . $uid, null);
        return $result ? true : false;
    }
}