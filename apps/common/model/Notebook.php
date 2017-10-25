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

    /**
     * 批量获取笔记本名称
     * @param $notebookIds
     */
    public function getNoteBookListByIds($notebookIds){
        if (empty($notebookIds))
            return false;
        $notebookIds=is_array($notebookIds) ? implode(",",$notebookIds) : "";
        $noteBookList=$this->where(array("id"=>array('in',$notebookIds)))->order("id desc")->select();
        if (!$noteBookList->isEmpty()){
            $noteBookList=$noteBookList->toArray();
            $noteBookListRow=array();
            foreach ($noteBookList as $value){
                $noteBookListRow[$value['id']]=$value;
            }
            unset($noteBookList);
            return $noteBookListRow;
        }
        return false;
    }


}