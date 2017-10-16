<?php
namespace  \app\common\model\user;

class UserDisk extends \app\common\model\Common
{
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
            foreach ($userDiskInfo as $key => $value) {
                $userDisk[$key] = $value;
            }
        }
        unset($userDiskInfo);
        return $userDisk;
    }
}