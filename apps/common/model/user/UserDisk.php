<?php

namespace app\common\model\user;

class UserDisk extends \app\common\model\Common
{

    /**
     * 设置用户注册默认的空间
     * @param $uid
     */
    public function setDefaultUserSpace($uid)
    {
        if (empty($uid))
            return false;
        $defaultSpace = config('registration_gift_space');
        $defaultSpaceByte = !empty($defaultSpace) ? mbToByte($defaultSpace) : 0;
        $diskData = array('uid' => $uid, 'total_disk_space' => $defaultSpaceByte, 'default_disk_space' => $defaultSpaceByte, 'used_disk_space' => 0);
        $result = $this->insert($diskData);
        if ($result) {
            model("user.UserDiskLog")->recordLog($uid, 3, $defaultSpaceByte); //保存日志
        }
        return $result;
    }

    /**
     * 获取用户的空间使用信息
     * @param $uid
     */
    public function getUserDiskInfo($uid)
    {
        if (empty($uid))
            return false;
        $userDisk = array('total_disk_space' => 0, 'used_disk_space' => 0, 'default_disk_space' => 0);
        $userDiskInfo = $this->where(array('uid' => $uid))->limit(1)->find();
        if ($userDiskInfo) {
            $userDiskInfo = $userDiskInfo->toArray();
            foreach ($userDiskInfo as $key => $value) {
                $userDisk[$key] = $value;
            }
        }
        unset($userDiskInfo);
        return $userDisk;
    }

    /**
     * 增加用户已经使用的空间。
     * @param $uid
     * @param $addUserStorageSize
     */
    public function addUserAlreadyUsedStorage($uid, $addUserStorageSize = 0)
    {
        if (empty($uid) || $addUserStorageSize <= 0)
            return false;
        $result = $this->where("uid", $uid)->setInc("used_disk_space", $addUserStorageSize);
        if ($result) {
            model("user.UserDiskLog")->recordLog($uid,1, $addUserStorageSize); //保存日志
        }
        return $result;
    }
}