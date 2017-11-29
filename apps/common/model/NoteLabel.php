<?php
/**
 * 笔记的标签Model
 */

namespace app\common\model;


class NoteLabel extends Common
{

    /**
     * 删除笔记下的所有标签
     * @param $noteId
     */
    public function delLabelByNoteId($noteId)
    {
        if (empty($noteId))
            return false;
        $result = $this->where(array('note_id' => $noteId))->delete();
        return $result;
    }

    /**
     * 获取笔记下的所有标签
     * @param $noteId
     * @return array|bool
     */
    public function getLabelListByNoteId($noteId)
    {
        if (empty($noteId))
            return false;
        $labelList = $this->where(array('note_id' => $noteId))->order("id asc")->select();
        if (!$labelList->isEmpty()){
            return $labelList->toArray();
        }
        return false;
    }

    /**
     * 批量获取笔记标签信息
     * @param $noteIds
     */
    public function  getLabelListByNoteIds($noteIds){
        if (empty($noteIds))
            return false;
        $noteIds=is_array($noteIds) ? implode(',',$noteIds) : $noteIds;
        $labelList = $this->where(array('note_id' =>array('in',$noteIds)))->order("id asc")->select();
        if (!$labelList->isEmpty()){
            $labelList= $labelList->toArray();
            $noteLabelList=array();
            foreach ($labelList as $value){
                $noteLabelList[$value['note_id']][]=$value;
            }
            unset($labelList);
            return $noteLabelList;
        }
        return false;
    }

    /**
     * 添加笔记标签
     * @param $noteId
     * @param $labelList
     */
    public function saveLabel($noteId,$labelList){
        if (empty($noteId) || empty($labelList))
            return false;
        //先删除笔记本标签
        $this->delLabelByNoteId($noteId);
        $labelListArray=explode(",",$labelList);
        $data=array();
        foreach ($labelListArray as $value){
            $data[]=array('note_id'=>$noteId,'name'=>$value,'ctime'=>time());
        }
        model("Note")->where(array('id'=>$noteId))->setField("label_num",count($data));
        $result=$this->insertAll($data);
        return $result;
    }

}