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
        if (empty($data['notebook_id'])){
            return output(0, lang('PARAM_ERROR'));
        }
        $data['name'] = isset($this->post['name']) ? addSlashesFun($this->post['name']) : "";
        if (empty($data['name'])) {
            return output(0, lang('NOTEBOOK_IS_EMPTY'));
        }
        $data['file_id']= isset($this->post['fileId']) ? intval($this->post['fileId']) : "";
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
            model("AliyunOss")->deleteObject($noteInfo['file_id']);
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
        if (!empty($noteInfo['file_id'])){
            $ossLogModel=model("OssLog");
            $noteInfo['fileInfo']=$ossLogModel->getFormatFile($ossLogModel->getFileInfoById($noteInfo['file_id']));
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
                $uploadInfo=array('savename'=>$saveName,'url'=>$serverPath,'extension'=>$extension,'typeid'=>1,'size'=>$thumb ['size'],'uid'=>$this->userInfo['uid']);
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
        $field="id,notebook_id,name,label_num,file_id,ctime";
        $noteModel = model('Note');
        $noteList=$noteModel->where($map)->order("id desc")->paginate($pageSize);
        if (!$noteList->isEmpty()){
            $noteList=$noteList->toArray();
            $noteLabelModel=model('NoteLabel');
            $noteLabelList=$noteLabelModel->getLabelListByNoteIds(array_unique(getSubByKey($noteList['data'] ,"id")));
            //批量获取文件信息
            $ossLogModel=model("OssLog");
            $fileList=$ossLogModel->getFileInfoByIds(array_unique(getSubByKey($noteList['data'] ,"file_id")));
            foreach ($noteList['data'] as $key=>$value){
                $value['labelList']=$noteLabelList && array_key_exists($value['id'],$noteLabelList) ? $noteLabelList[$value['id']] :array();
                $fileInfo=$fileList && array_key_exists($value['file_id'],$fileList) ? $fileList[$value['file_id']] :array();
                $value['fileInfo']=$fileInfo ? $ossLogModel->getFormatFile($fileInfo) : array();
                $noteList['data'][$key]=$value;
            }
            return outputList($noteList['data'],$noteList['total'],ceil ( $noteList['total'] / $pageSize ));
        }else{
            return  output ( 0, lang('ORDER_NO') );
        }
    }
}