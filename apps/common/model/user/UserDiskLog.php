<?php
/**
 * 空间存储的管理日志
 * User: ugee
 * Date: 2017-10-20
 * Time: 14:19
 */

namespace app\common\model\user;

class UserDiskLog extends \app\common\model\Common
{

    /**
     * 新增空间存储管理日志
     * @param $uid
     * @param $typeId , 0 标识新增，1标识使用,2删除文件释放的空间
     */
    public function recordLog($uid, $typeId, $spaceSize, $noteId = "")
    {
        if (empty($uid))
            return false;
        $data = array('uid' => $uid);
        $byteTomb = byteTomb($spaceSize);
        $remark = "";
        switch ($typeId) {
            case 0 :
                $remark = "新增空间:" . $spaceSize . ",=" . $byteTomb . "M,购买新增空间";
                break;
            case 1 :
                $noteIdString=!empty($noteId) ? ",笔记ID：".$noteId : "";
                $remark = "使用空间:" . $spaceSize . ",=" . $byteTomb . "M,上传笔记消耗空间" . $noteIdString;
                break;
            case 2 :
                $remark = "新增空间:" . $spaceSize . ",=" . $byteTomb . "M,删除文件释放空间";
                break;
            case 3 :
                $remark = "新增空间:" . $spaceSize . ",=" . $byteTomb . "M,注册默认赠送";
                break;
        }
        $data['typeid'] = $typeId;
        $data['space_size'] = $spaceSize;
        $data['remark'] = $remark;
        $data['ctime'] = time();
        $result = $this->insertGetId($data);
        return $result;
    }
}