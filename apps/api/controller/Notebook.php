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
        $data = array();
        $data['name'] = isset($this->post['name']) ? addSlashesFun($this->post['name']) : "";
        if (empty($data['name'])) {
            return output(0, lang('NOTEBOOK_IS_EMPTY'));
        }
        $data['ctime'] = time();
        $notebookModel = model('Notebook');
        $notebookId = $notebookModel->insertGetId($data);
        if ($notebookId) {
            $data['notebookId'] = $notebookId;
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
        if (empty($notebookId)) {
            return output(0, lang('PARAM_ERROR'));
        }
        $notebookModel = model('Notebook');
        $notebookInfo = $notebookModel->getNoteBookInfoById($notebookId);
        if (!$notebookInfo || ($notebookInfo && $notebookInfo['uid'] != $uid)) {
            return output(0, lang('PARAM_ERROR'));
        }
        if ($notebookInfo['quantity']>0){
            return output(0, lang('NOTEBOOK_HAS_NOTE'));
        }
        $result=$notebookModel->where(array('id'=>$notebookId))->delete();
        if ($result){
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
        return output(1, lang('GET_SUCCESS'), $notebookInfo);
    }
}