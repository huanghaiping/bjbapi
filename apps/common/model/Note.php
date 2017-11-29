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
        if ($noteInfo) {
            $noteInfo['fileSize'] = isset($noteInfo['file_size']) ? $noteInfo['file_size'] : 0;
            $noteInfo['fileExt'] = isset($noteInfo['file_ext']) ? $noteInfo['file_ext'] : "";
            unset($noteInfo['file_size'], $noteInfo['file_ext']);
        }
        return $noteInfo;
    }

    /**
     * 移除笔记里面笔记本的ID
     * @param $noteBookId
     */
    public function noteRemoveNoteBookId($noteBookId)
    {
        if (empty($noteBookId))
            return false;
        $result = $this->where(array('notebook_id'=>$noteBookId))->setField("notebook_id",0);
        return $result;
    }

    /**
     * 删除笔记
     * @param $noteIds
     */
    public function deleteNoteById($noteIds)
    {
        if (empty($noteIds))
            return false;
        $noteIds = is_array($noteIds) ? implode(",", $noteIds) : $noteIds;
        $noteList = $this->where(array('id' => array('in', $noteIds)))->order("id desc")->select();
        if (!$noteList->isEmpty()) {
            $aliYunOssModel = model("AliyunOss");
            $noteLabelModel = model('NoteLabel');
            $notebookModel = model('Notebook');
            foreach ($noteList as $key => $value) {
                //删除旧文件操作(待处理)
                $aliYunOssModel->deleteObject($value['file_id']);
                //删除缩略图
                $aliYunOssModel->deleteObject($value['thumb_id']);
                //删除标签
                $noteLabelModel->delLabelByNoteId($value['id']);
                //笔记本统计数-1
                $notebookModel->where(array('id' => $value['notebook_id']))->setDec("quantity", 1);
            }
            $result = $this->where(array('id' => array('in', $noteIds)))->delete();
            return $result;
        }
        return false;
    }

    /**
     * 格式化笔记列表的输出
     */
    public function formatNoteInfo($noteList)
    {
        if (empty($noteList))
            return false;
        $labelId = $fileId = $noteBookId = array();
        foreach ($noteList as $value) {
            $labelId[] = $value['id'];
            $fileId[] = $value['file_id'];
            $fileId[]=$value['thumb_id'];
            $noteBookId[] = $value['notebook_id'];
        }
        //获取笔记标签信息
        $noteLabelModel = model('NoteLabel');
        $noteLabelList = $noteLabelModel->getLabelListByNoteIds(array_unique($labelId));
        //批量获取文件信息
        $ossLogModel = model("OssLog");
        $fileList = $ossLogModel->getFileInfoByIds(array_unique($fileId));
        //获取笔记本信息
        $notebookModel = model("Notebook");
        $noteBookList = $notebookModel->getNoteBookListByIds(array_unique($noteBookId));
        foreach ($noteList as $key => $value) {
            //获取标签信息
            $value['labelList'] = $noteLabelList && array_key_exists($value['id'], $noteLabelList) ? $noteLabelList[$value['id']] : array();
            //获取文件信息
            $fileInfo = $fileList && array_key_exists($value['file_id'], $fileList) ? $fileList[$value['file_id']] : array();
            $value['fileInfo'] = $fileInfo ? $ossLogModel->getFormatFile($fileInfo) : array();
            //获取缩略图信息
            $thumbInfo=$fileList && array_key_exists($value['thumb_id'], $fileList) ? $fileList[$value['thumb_id']] : array();
            $value['thumbInfo'] =$thumbInfo ? $ossLogModel->getFormatFile($thumbInfo) : array();
            //获取笔记本信息
            $value['noteBookInfo'] = $noteBookList && array_key_exists($value['notebook_id'], $noteBookList) ? $noteBookList[$value['notebook_id']] : array();
            $noteList[$key] = $value;
        }
        return $noteList;
    }
}