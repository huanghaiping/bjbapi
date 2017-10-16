<?php
/**
 * 笔记和笔记的标签控制器
 */
namespace app\api\controller;


class Note extends  Common
{
    /**
     * 保存笔记的信息
     */
    public function add(){
        if (empty($this->userInfo)) {
            return output(0, lang('PLEASE_ENTER_LOGIN'));
        }
        $uid = isset ($this->post ['uid']) ? intval($this->post ['uid']) : ""; //当前登录的用户uid
        if (empty($uid) || ($this->userInfo && $this->userInfo['uid'] != $uid)) {
            return output(0, lang('UID_IS_EMPTY'));
        }
        $data = array('uid'=>$uid);
        $data['notebook_id']=isset($this->post['notebookId']) ? intval($this->post['notebookId']) : 0;
        if (empty($data['notebook_id'])){
            return output(0, lang('PARAM_ERROR'));
        }
        $data['name'] = isset($this->post['name']) ? addSlashesFun($this->post['name']) : "";
        if (empty($data['name'])) {
            return output(0, lang('NOTEBOOK_IS_EMPTY'));
        }
        $data['url']= isset($this->post['url']) ? addSlashesFun($this->post['url']) : "";
        $data['file_size']=isset($this->post['fileSize']) ? intval($this->post['fileSize']) : 0;
        $data['file_ext']=isset($this->post['fileExt']) ? addSlashesFun($this->post['fileExt']) : "";
        $data['ctime']=time();
        $noteModel=model('Note');
        $noteId=$noteModel->insertGetId($data);
        if ($noteId) {
            //保存笔记标签，用英文逗号隔开
            $labelList=isset($this->post['label']) ? addSlashesFun($this->post['label']) : "";
            if (!empty($labelList)){
                model('NoteLabel')->saveLabel($labelList);
            }
            //笔记本统计数+1
            $notebookModel = model('Notebook');
            $notebookModel->where(array('id'=>$data['notebook_id']))->setInc("quantity",1);
            return output(1, lang('SAVE_SUCCESSFULLY'), array('noteId'=>$noteId));
        } else {
            return output(0, lang('SAVE_FAILED'));
        }
    }

    /**
     * 修改笔记
     */
    public function  edit(){
        if (empty($this->userInfo)) {
            return output(0, lang('PLEASE_ENTER_LOGIN'));
        }
        $uid = isset ($this->post ['uid']) ? intval($this->post ['uid']) : ""; //当前登录的用户uid
        if (empty($uid) || ($this->userInfo && $this->userInfo['uid'] != $uid)) {
            return output(0, lang('UID_IS_EMPTY'));
        }
        $noteId = isset($this->post['noteId']) ? intval($this->post['noteId']) : 0;
        if (empty($noteId)) {
            return output(0, lang('PARAM_ERROR'));
        }
        $noteModel=model('Note');
        $noteInfo=$noteModel->info($noteId);
        if (!$noteInfo || ($noteInfo && $noteInfo['uid']!=$uid)){
            return output(0, lang('PARAM_ERROR'));
        }
        $data = array('uid'=>$uid);
        $data['notebook_id']=isset($this->post['notebookId']) ? intval($this->post['notebookId']) : 0;
        if (empty($data['notebook_id'])){
            return output(0, lang('PARAM_ERROR'));
        }
        $data['name'] = isset($this->post['name']) ? addSlashesFun($this->post['name']) : "";
        if (empty($data['name'])) {
            return output(0, lang('NOTEBOOK_IS_EMPTY'));
        }
        $data['url']= isset($this->post['url']) ? addSlashesFun($this->post['url']) : "";
        $data['file_size']=isset($this->post['fileSize']) ? intval($this->post['fileSize']) : 0;
        $data['file_ext']=isset($this->post['fileExt']) ? addSlashesFun($this->post['fileExt']) : "";
        $data['ctime']=time();
        $result=$noteModel->where(array('id'=>$noteId))->update($data);
        if ($result) {
            //保存笔记标签，用英文逗号隔开
            $labelList=isset($this->post['label']) ? addSlashesFun($this->post['label']) : "";
            if (!empty($labelList)){
                model('NoteLabel')->saveLabel($labelList);
            }
            //删除旧文件操作(待处理)
            return output(1, lang('SAVE_SUCCESSFULLY'), array('noteId'=>$noteId));
        } else {
            return output(0, lang('SAVE_FAILED'));
        }
    }

    /**
     * 删除笔记
     */
    public function del(){
        if (empty($this->userInfo)) {
            return output(0, lang('PLEASE_ENTER_LOGIN'));
        }
        $uid = isset ($this->post ['uid']) ? intval($this->post ['uid']) : ""; //当前登录的用户uid
        if (empty($uid) || ($this->userInfo && $this->userInfo['uid'] != $uid)) {
            return output(0, lang('UID_IS_EMPTY'));
        }
        $noteId = isset($this->post['noteId']) ? intval($this->post['noteId']) : 0;
        if (empty($noteId)) {
            return output(0, lang('PARAM_ERROR'));
        }
        $noteModel=model('Note');
        $noteInfo=$noteModel->info($noteId);
        if (!$noteInfo || ($noteInfo && $noteInfo['uid']!=$uid)){
            return output(0, lang('PARAM_ERROR'));
        }
        $result=$noteModel->where(array('id'=>$noteId))->delete();
        if ($result){
            //删除旧文件操作(待处理)

            //删除标签
            model('NoteLabel')->delLabelByNoteId($noteId);
            //笔记本统计数-1
            $notebookModel = model('Notebook');
            $notebookModel->where(array('id'=>$noteInfo['notebook_id']))->setDec("quantity",1);
            return output(1, lang('DELETE_SUCCESSFULLY'));
        } else {
            return output(0, lang('DELETE_FAILED'));
        }

    }

    /**
     * 获取某个笔记的信息
     */
    public function info(){
        if (empty($this->userInfo)) {
            return output(0, lang('PLEASE_ENTER_LOGIN'));
        }
        $uid = isset ($this->post ['uid']) ? intval($this->post ['uid']) : ""; //当前登录的用户uid
        if (empty($uid) || ($this->userInfo && $this->userInfo['uid'] != $uid)) {
            return output(0, lang('UID_IS_EMPTY'));
        }
        $noteId = isset($this->post['noteId']) ? intval($this->post['noteId']) : 0;
        if (empty($noteId)) {
            return output(0, lang('PARAM_ERROR'));
        }
        $noteModel=model('Note');
        $noteInfo=$noteModel->info($noteId);
        if (!$noteInfo || ($noteInfo && $noteInfo['uid']!=$uid)){
            return output(0, lang('PARAM_ERROR'));
        }
        $noteInfo['labelList']=array();
        if ($noteInfo['label_num']>0){
            $noteLabelModel=model('NoteLabel');
            $noteInfo['labelList']=$noteLabelModel->getLabelListByNoteId();
        }
        return output(1, lang('GET_SUCCESS'), $noteInfo);
    }

    /**
     * 上传笔记本的文件
     */
    public function upload(){

    }

    /**
     * 获取笔记本下的所有笔记
     */
    public function lists(){
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
        $map=array('notebook_id'=>$notebookId);
        $pageSize=20;
        $field="id,notebook_id,name,label_num,url,file_size,file_ext,ctime";
        $noteModel = model('Note');
        $noteList=$noteModel->where($map)->order("id desc")->paginate($pageSize);
        if (!$noteList->isEmpty()){
            $noteList=$noteList->toArray();
            $noteIds=getSubByKey($noteList,"id");
            $noteLabelModel=model('NoteLabel');
            $noteLabelList=$noteLabelModel->getLabelListByNoteIds(array_unique($noteIds));
            foreach ($noteList['data'] as $key=>$value){
                $value['labelList']=array_key_exists($value['id'],$noteLabelList) ? $noteLabelList[$value['id']] :array();
                $noteList['data'][$key]=$value;
            }
            return outputList($noteList['data'],$noteList['total'],ceil ( $noteList['total'] / $pageSize ));
        }else{
            return  output ( 0, lang('ORDER_NO') );
        }
    }
}