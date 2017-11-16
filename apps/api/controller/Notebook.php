<?php
/**
 * 笔记本控制器
 */

namespace app\api\controller;


class Notebook extends Common
{
    /**
     * 添加
     */
    public function add()
    {
        if (empty($this->userInfo)) {
            return output(0, lang('PLEASE_ENTER_LOGIN'));
        }
        $uid = isset ($this->post ['uid']) ? intval($this->post ['uid']) : ""; //当前登录的用户uid
        if (empty($uid) || ($this->userInfo && $this->userInfo['uid'] != $uid)) {
            return output(0, lang('UID_IS_EMPTY'));
        }
        $data = array('uid'=>$uid);
        $data['name'] = isset($this->post['name']) ? addSlashesFun($this->post['name']) : "";
        if (empty($data['name'])) {
            return output(0, lang('NOTEBOOK_IS_EMPTY'));
        }
        $data['ctime'] = time();
        $notebookModel = model('Notebook');
        $notebookId = $notebookModel->insertGetId($data);
        if ($notebookId) {
            $data['notebookId'] = $notebookId;
            $data['quantity'] = 0;
            return output(1, lang('SAVE_SUCCESSFULLY'), $data);
        } else {
            return output(0, lang('SAVE_FAILED'));
        }
    }

    /**
     * 修改
     */
    public function edit()
    {
        if (empty($this->userInfo)) {
            return output(0, lang('PLEASE_ENTER_LOGIN'));
        }
        $uid = isset ($this->post ['uid']) ? intval($this->post ['uid']) : ""; //当前登录的用户uid
        if (empty($uid) || ($this->userInfo && $this->userInfo['uid'] != $uid)) {
            return output(0, lang('UID_IS_EMPTY'));
        }
        $data = array();
        $notebookId = isset($this->post['notebookId']) ? intval($this->post['notebookId']) : 0;
        if (empty($notebookId)) {
            return output(0, lang('PARAM_ERROR'));
        }
        $data['name'] = isset($this->post['name']) ? addSlashesFun($this->post['name']) : "";
        if (empty($data['name'])) {
            return output(0, lang('NOTEBOOK_IS_EMPTY'));
        }
        $data['name'] = time();
        $notebookModel = model('Notebook');
        $notebookInfo = $notebookModel->getNoteBookInfoById($notebookId);
        if (!$notebookInfo || ($notebookInfo && $notebookInfo['uid'] != $uid)) {
            return output(0, lang('PARAM_ERROR'));
        }
        $result = $notebookModel->where(array('id' => $notebookId))->update($data);
        if ($notebookId) {
            $data['notebookId'] = $notebookId;
            $data['quantity'] = isset($notebookInfo['quantity']) ? $notebookInfo['quantity'] : 0;
            $data['ctime'] =$notebookInfo['ctime'];
            return output(1, lang('SAVE_SUCCESSFULLY'), $data);
        } else {
            return output(0, lang('SAVE_FAILED'));
        }

    }

    /**
     * 删除
     */
    public function del()
    {
        if (empty($this->userInfo)) {
            return output(0, lang('PLEASE_ENTER_LOGIN'));
        }
        $uid = isset ($this->post ['uid']) ? intval($this->post ['uid']) : ""; //当前登录的用户uid
        if (empty($uid) || ($this->userInfo && $this->userInfo['uid'] != $uid)) {
            return output(0, lang('UID_IS_EMPTY'));
        }
        $data = array();
        $notebookId = isset($this->post['notebookId']) ? intval($this->post['notebookId']) : 0;
        $isDelete=isset($this->post['isDelete']) ? intval($this->post['isDelete']) : 0 ; // 1 表示只删除笔记本不删除笔记，2表示删除笔记本和笔记
        if (empty($notebookId) || !in_array($isDelete,array(1,2))) {
            return output(0, lang('PARAM_ERROR'));
        }
        $notebookModel = model('Notebook');
        $notebookInfo = $notebookModel->getNoteBookInfoById($notebookId);
        if (!$notebookInfo || ($notebookInfo && $notebookInfo['uid'] != $uid)) {
            return output(0, lang('PARAM_ERROR'));
        }
        $noteModel=model("Note");
        $result=$notebookModel->where(array('id'=>$notebookId))->delete();
        if ($result){
            switch ($isDelete){
                //删除笔记本不删除笔记,去掉笔记本下所有的笔记的notebookId为0;
                case 1 :
                    $noteModel->noteRemoveNoteBookId($notebookId);
                    break;
                //删除笔记本和笔记本下的所有笔记
                case 2 :
                    $noteListIds=$noteModel->where("notebook_id",$notebookId)->column("id");
                    if (!empty($noteListIds)){
                        $noteModel->deleteNoteById($noteListIds);
                    }
                    break;
            }
            return output(1, lang('DELETE_SUCCESSFULLY'));
        } else {
            return output(0, lang('DELETE_FAILED'));
        }
    }

    /**
     * 获取用户下的所有的笔记本
     */
    public function user()
    {
        if (empty($this->userInfo)) {
            return output(0, lang('PLEASE_ENTER_LOGIN'));
        }
        $uid = isset ($this->post ['uid']) ? intval($this->post ['uid']) : ""; //当前登录的用户uid
        if (empty($uid) || ($this->userInfo && $this->userInfo['uid'] != $uid)) {
            return output(0, lang('UID_IS_EMPTY'));
        }
        $map=array('uid'=>$uid);
        $pageSize=20;
        $field="id,name,quantity,ctime";
        $notebookModel = model('Notebook');
        $notebookList=$notebookModel->where($map)->order("id desc")->paginate($pageSize);
        if (!$notebookList->isEmpty()){
            $notebookList=$notebookList->toArray();
            return outputList($notebookList['data'],$notebookList['total'],ceil ( $notebookList['total'] / $pageSize ));
        }else{
            return  output ( 0, lang('ORDER_NO') );
        }

    }

    /**
     * 获取某个笔记本的详细信息
     */
    public function info()
    {
        if (empty($this->userInfo)) {
            return output(0, lang('PLEASE_ENTER_LOGIN'));
        }
        $uid = isset ($this->post ['uid']) ? intval($this->post ['uid']) : ""; //当前登录的用户uid
        if (empty($uid) || ($this->userInfo && $this->userInfo['uid'] != $uid)) {
            return output(0, lang('UID_IS_EMPTY'));
        }
        $data = array();
        $notebookId = isset($this->post['notebookId']) ? intval($this->post['notebookId']) : 0;
        if (empty($notebookId)) {
            return output(0, lang('PARAM_ERROR'));
        }
        $notebookModel = model('Notebook');
        $notebookInfo = $notebookModel->getNoteBookInfoById($notebookId);
        if (!$notebookInfo || ($notebookInfo && $notebookInfo['uid'] != $uid)) {
            return output(0, lang('PARAM_ERROR'));
        }
        if ($notebookInfo['status']==0){
            return output(0, lang('NOTE_DISABLED'));
        }
        return output(1, lang('GET_SUCCESS'), $notebookInfo);
    }

    /**
     * 笔记本的移动（将笔记本下的所有笔记移动到另外一个笔记本下）
     */
    public function move(){
        if (empty($this->userInfo)) {
            return output(0, lang('PLEASE_ENTER_LOGIN'));
        }
        $uid = isset ($this->post ['uid']) ? intval($this->post ['uid']) : ""; //当前登录的用户uid
        if (empty($uid) || ($this->userInfo && $this->userInfo['uid'] != $uid)) {
            return output(0, lang('UID_IS_EMPTY'));
        }
        $fromNoteBookId=isset ($this->post ['fromNoteBookId']) ? intval($this->post ['fromNoteBookId']) : "";
        $toNoteBookId=isset ($this->post ['toNoteBookId']) ? intval($this->post ['toNoteBookId']) : "";
        if (empty($fromNoteBookId) || empty($toNoteBookId) || $fromNoteBookId==$toNoteBookId){
            return output(0, lang('PARAM_ERROR'));
        }

        $notebookModel = model('Notebook');
        $fromNotebookInfo = $notebookModel->getNoteBookInfoById($fromNoteBookId);
        if (!$fromNotebookInfo || ($fromNotebookInfo && $fromNotebookInfo['uid'] != $uid)) {
            return output(0, lang('PARAM_ERROR'));
        }

        $toNotebookInfo = $notebookModel->getNoteBookInfoById($toNoteBookId);
        if (!$toNotebookInfo || ($toNotebookInfo && $toNotebookInfo['uid'] != $uid)) {
            return output(0, lang('PARAM_ERROR'));
        }
        $noteModel=model("Note");
        $fromNoteCount=$noteModel->where(array("notebook_id"=>$fromNoteBookId))->count();
        if ($fromNoteCount){
            $noteModel->where(array("notebook_id"=>$fromNoteBookId))->setField("notebook_id",$toNoteBookId);
            $notebookModel->where(array("id"=>$fromNoteBookId))->setDec("quantity",$fromNoteCount); //旧笔记本减去对应数量
            $notebookModel->where(array("id"=>$toNoteBookId))->setInc("quantity",$fromNoteCount);  //新笔记本增加对应的数量
            return output(1, lang('GET_SUCCESS'));
        }else{
            return output(0, lang('PARAM_ERROR'));
        }
    }

}