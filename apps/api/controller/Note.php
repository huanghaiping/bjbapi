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
        $data['name'] = isset($this->post['name']) ? addSlashesFun($this->post['name']) : "";
        if (empty($data['name'])) {
            return output(0, lang('NOTEBOOK_IS_EMPTY'));
        }
        $data['thumb_id']=isset($this->post['thumbId']) ? intval($this->post['thumbId']) : "";
        if (empty($data['thumb_id'])){
            return output(0, lang('PARAM_ERROR'));
        }
        $data['file_id']= isset($this->post['fileId']) ? intval($this->post['fileId']) : "";
        if (empty($data['file_id'])){
            return output(0, lang('PARAM_ERROR'));
        }
        $data['ctime']=time();
        $noteModel=model('Note');
        $noteId=$noteModel->insertGetId($data);
        if ($noteId) {
            //保存笔记标签，用英文逗号隔开
            $labelList=isset($this->post['label']) ? addSlashesFun($this->post['label']) : "";
            if (!empty($labelList)){
                model('NoteLabel')->saveLabel($noteId,$labelList);
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
        $data['name'] = isset($this->post['name']) ? addSlashesFun($this->post['name']) : "";
        if (empty($data['name'])) {
            return output(0, lang('NOTEBOOK_IS_EMPTY'));
        }
        $data['file_id']= isset($this->post['fileId']) ? intval($this->post['fileId']) : "";
        $data['thumb_id']=isset($this->post['thumbId']) ? intval($this->post['thumbId']) : "";
        if (empty($data['thumb_id']) || empty($data['file_id'])){
            return output(0, lang('PARAM_ERROR'));
        }
        $data['ctime']=time();
        $result=$noteModel->where(array('id'=>$noteId))->update($data);
        if ($result) {
            //保存笔记标签，用英文逗号隔开
            $labelList=isset($this->post['label']) ? addSlashesFun($this->post['label']) : "";
            if (!empty($labelList)){
                model('NoteLabel')->saveLabel($noteId,$labelList);
            }
            //删除旧文件操作
            if ($noteInfo['file_id']!=$data['file_id']){
                model("AliyunOss")->deleteObject($noteInfo['file_id']);
            }
            //删除缩略图
            if ($noteInfo['thumb_id']!=$data['thumb_id']){
                model("AliyunOss")->deleteObject($noteInfo['thumb_id']);
            }
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
        $result=$noteModel->deleteNoteById($noteId);
        if ($result){
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
        if ($noteInfo['status']==0){
            return output(0, lang('NOTE_DISABLED'));
        }
        $noteInfo['labelList']=array();
        if ($noteInfo['label_num']>0){
            $noteLabelModel=model('NoteLabel');
            $noteInfo['labelList']=$noteLabelModel->getLabelListByNoteId();
        }
        //返回笔记文件信息
        $ossLogModel=model("OssLog");
        if (!empty($noteInfo['file_id'])){
            $noteInfo['fileInfo']=$ossLogModel->getFormatFile($ossLogModel->getFileInfoById($noteInfo['file_id']));
        }
        //返回缩略图文件信息
        if (!empty($noteInfo['thumb_id'])){
            $noteInfo['thumbInfo']=$ossLogModel->getFormatFile($ossLogModel->getFileInfoById($noteInfo['thumb_id']));
        }
        return output(1, lang('GET_SUCCESS'), $noteInfo);
    }

    /**
     * 上传笔记本的文件
     */
    public function upload(){
        if (empty($this->userInfo)) {
            return output(0, lang('PLEASE_ENTER_LOGIN'));
        }
        $uid = isset ($this->post ['uid']) ? intval($this->post ['uid']) : ""; //当前登录的用户uid
        if (empty($uid) || ($this->userInfo && $this->userInfo['uid'] != $uid)) {
            return output(0, lang('UID_IS_EMPTY'));
        }
        $typeId=isset ($this->post ['typeId']) ? intval($this->post ['typeId']) : 1;
        if (empty($typeId) || !in_array($typeId,array(1,3))){
            return output(0, lang('PARAM_ERROR'));
        }
        $fileType=isset($this->post['fileType']) ? $this->post['fileType'] : "";
        if (! isset ( $_FILES ) || empty ( $_FILES ) || empty($fileType)) {
            return output ( 0, lang('PLEASE_SELECT_FILE') );
        }
        $uploadAllowExt = config("file_upload.ext");
        $uploadMaxSize =   config('file_upload.size'); //默认20M
        $uploadPath =config("file_upload.rootPath");
        $config = array ('size'=>$uploadMaxSize,'ext'=>$uploadAllowExt);
        $fileFieldKey="file";
        $thumb = $_FILES [$fileFieldKey];
        if (! empty ( $thumb ['size'] )) {
            //判断用户空间的使用情况
            $userDiskModel=model("user.UserDisk");
            $userDiskInfo=$userDiskModel->getUserDiskInfo($this->userInfo['uid']);
            $residualSpace=$userDiskInfo['total_disk_space']-$userDiskInfo['used_disk_space'];
            if ($residualSpace<=0 || $thumb ['size']>$residualSpace){
                return output(0,lang('INSUFFICIENT_STORAGE_SPACE'));
            }
            $fileObject = $this->request->file ( $fileFieldKey );
            $uploadImageInfo = $fileObject->validate ( $config )->move ( $uploadPath );
            if ($uploadImageInfo) {
                $serverPath = $uploadPath . DS . $uploadImageInfo->getSaveName (); //上传后的图片路径
                chmod ( $serverPath, 0777 );
                //上传文件到阿里云oss云存储
                $aliYunOssModel=model('AliyunOss');
                $saveName=$uploadImageInfo->getFilename();
                $extension=$uploadImageInfo->getExtension();
                $uploadInfo=array('savename'=>$saveName,'url'=>$serverPath,'extension'=>$extension,'typeid'=>$typeId,'size'=>$thumb ['size'],'uid'=>$this->userInfo['uid']);
                $aliYunUploadInfo=$aliYunOssModel->uploadFile($uploadInfo,'note',true,'images');
                if ($aliYunUploadInfo['status']==1){
                    //增加用户使用的空间存储
                    $userDiskModel->addUserAlreadyUsedStorage($this->userInfo['uid'],$thumb['size']);
                    $returnData=array("id"=>$aliYunUploadInfo['fileId'],'url'=>$aliYunUploadInfo['url'],'extension'=>$extension,'ctime'=>time(),'size'=>$thumb ['size']);
                    return output(1,lang('UPLOAD_SUCCESS'),$returnData);
                }else{
                    @unlink($serverPath);
                    return output(0,lang('UPLOAD_FAILED').":".$aliYunUploadInfo['msg']);
                }
            } else {
                return output(0,lang('UPLOAD_FAILED').":".$fileObject->getError ());
            }
        }else{
            return output(0,lang('PLEASE_SELECT_FILE'));
        }
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
        $lastNoteId=isset ($this->post ['lastNoteId']) ? intval($this->post ['lastNoteId']) : 0; //请求的最后一条笔记ID
        if (!empty($lastNoteId)){
            $map ['id'] = array ('lt', $lastNoteId );
        }
        $field="id,notebook_id,name,label_num,file_id,thumb_id,ctime";
        $noteModel = model('Note');
        $noteList=$noteModel->where($map)->order("id desc")->paginate($pageSize);
        if (!$noteList->isEmpty()){
            $noteList=$noteList->toArray();
            $noteList['data']=$noteModel->formatNoteInfo($noteList['data']);
            return outputList($noteList['data'],$noteList['total'],ceil ( $noteList['total'] / $pageSize ));
        }else{
            return  output ( 0, lang('ORDER_NO') );
        }
    }

    /**
     * 获取用户下的所有笔记本
     */
    public function user(){
        if (empty($this->userInfo)) {
            return output(0, lang('PLEASE_ENTER_LOGIN'));
        }
        $uid = isset ($this->post ['uid']) ? intval($this->post ['uid']) : ""; //当前登录的用户uid
        if (empty($uid) || ($this->userInfo && $this->userInfo['uid'] != $uid)) {
            return output(0, lang('UID_IS_EMPTY'));
        }
        $map=array('uid'=>$uid);
        $pageSize=20;
        $lastNoteId=isset ($this->post ['lastNoteId']) ? intval($this->post ['lastNoteId']) : 0; //请求的最后一条笔记ID
        if (!empty($lastNoteId)){
            $map ['id'] = array ('lt', $lastNoteId );
        }
        $field="id,notebook_id,name,label_num,file_id,thumb_id,ctime";
        $noteModel = model('Note');
        $noteList=$noteModel->where($map)->order("id desc")->paginate($pageSize);
        if (!$noteList->isEmpty()){
            $noteList=$noteList->toArray();
            $noteList['data']=$noteModel->formatNoteInfo($noteList['data']);
            return outputList($noteList['data'],$noteList['total'],ceil ( $noteList['total'] / $pageSize ));
        }else{
            return  output ( 0, lang('ORDER_NO') );
        }
    }
}