<?php
/**
 * 笔记本的Model
 */

namespace app\common\model;


class Notebook extends Common
{
    /**
     * 获取笔记本信息
     * @param $NotebookId
     */
    public function getNoteBookInfoById($notebookId)
    {
        if (empty($notebookId))
            return false;
        $noteBookInfo = $this->where(array('id' => $notebookId))->limit(1)->find();
        return $noteBookInfo;
    }


}