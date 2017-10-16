<?php
/**
 * 笔记的Model
 */

namespace app\common\model;


class Note extends Common
{
    /**
     * 获取笔记的信息
     * @param $noteId
     * @return array|bool|false|\PDOStatement|string|\think\Model
     */
    public function info($noteId)
    {
        if (empty($noteId))
            return false;
        $noteInfo = $this->where(array('id' => $noteId))->limit(1)->find();
        if ($noteInfo){
            $noteInfo['fileSize']=isset($noteInfo['file_size']) ? $noteInfo['file_size'] : 0;
            $noteInfo['fileExt']=isset($noteInfo['file_ext']) ? $noteInfo['file_ext'] : "";
            unset($noteInfo['file_size'],$noteInfo['file_ext']);
        }
        return $noteInfo;
    }
}