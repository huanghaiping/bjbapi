<?php

namespace app\common\model;


class OssLog extends Common
{

    /**
     * 获取文件的信息
     * @param $fileId
     */
    public function getFileInfoById($fileId)
    {
        if (empty($fileId))
            return false;
        $fileInfo = $this->where("id", $fileId)->limit(1)->find();
        return $fileInfo;
    }

    /**
     * 批量获取用户头像
     * @param $fileIds
     */
    public function getFileInfoByIds($fileIds)
    {
        if (empty($fileIds))
            return false;
        $fileIds = is_array($fileIds) ? implode(",", $fileIds) : $fileIds;
        $fileList = $this->where(array('id' => array('in', $fileIds)))->select();
        if (!$fileList->isEmpty()) {
            $fileList = $fileList->toArray();
            $fileListArray = array();
            foreach ($fileList as $value) {
                $fileListArray[$value['id']] = $value;
            }
            unset($fileList);
            return $fileListArray;
        }
        return false;
    }

    /**
     * 删除文件信息
     * @param $fileId
     */
    public function delOssLog($fileId)
    {
        if (empty($fileId))
            return false;
        $result = $this->where("id", $fileId)->delete();
        return $result;
    }

    /**
     * 格式化返回的格式
     * @param $fileInfo
     * @return array
     */
    public function getFormatFile($fileInfo){
        $fileInfoArray=array();
        $fileInfoArray['id']=isset($fileInfo['id']) ? $fileInfo['id'] : 0;
        $fileInfoArray['url']=isset($fileInfo['oss_url']) ? $fileInfo['oss_url'] : '';
        $fileInfoArray['extension']=isset($fileInfo['extension']) ? $fileInfo['extension'] : '';
        $fileInfoArray['ctime']=isset($fileInfo['ctime']) ? $fileInfo['ctime'] : 0;
        $fileInfoArray['size']=isset($fileInfo['size']) ? $fileInfo['size'] : 0;
        return $fileInfoArray;
    }
}