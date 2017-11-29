<?php

namespace app\jzadmin\controller\note;
class Note extends \app\jzadmin\controller\Common
{
    /**
     * 笔记的列表管理
     * @return mixed
     */
    public function index()
    {
        $param = $this->request->param();
        $keyword = isset ($param ['keyword']) ? addSlashesFun($param ['keyword']) : "";
        $where = array();
        if (!empty ($keyword)) {
            $where ['name'] = array('like', '%' . $keyword . '%');
        }
        $request = array('status',"notebook_id","uid");
        foreach ($request as $value) {
            $status = isset ($param [$value]) ? $param [$value] : "";
            if ($status != "") {
                $where [$value] = $status;
                $this->assign($value, $status);
            }
        }
        $noteModel = model("Note");
        $noteList = $noteModel->where($where)->order("id desc")->paginate(20, false, array("query" => $param));
        if (!$noteList->isEmpty()) {
            $userIds = $noteBookIds = $fileIds = $noteIds = array();
            foreach ($noteList as $value) {
                $userIds[] = $value['uid'];
                $noteBookIds[] = $value['notebook_id'];
                $noteIds[] = $value['id'];
                $fileIds[] = $value['file_id'];
                $fileIds[] = $value['thumb_id'];
            }
            $userList = model("User")->getUserInfoByUids(array_unique($userIds));
            $noteBooList = model("Notebook")->getNoteBookListByIds(array_unique($noteBookIds));
            $labelList = model("NoteLabel")->getLabelListByNoteIds(array_unique($noteIds));
            $ossLogModel=model("OssLog");
            $fileList = $ossLogModel->getFileInfoByIds(array_unique($fileIds));
            foreach ($noteList as $key => $value) {
                $value ['userInfo'] = $userList && array_key_exists($value ['uid'], $userList) ? $userList [$value ['uid']] : array();
                $value ['noteBookInfo'] = $noteBooList && array_key_exists($value ['notebook_id'], $noteBooList) ? $noteBooList [$value ['notebook_id']] : array();
                $labelInfo= $labelList && array_key_exists($value ['id'], $labelList) ? $labelList [$value ['id']] : array();
                $value['labelInfo']=$labelInfo ? implode(",",getSubByKey($labelInfo,"name")) : "";
                $fileInfo=$fileList && array_key_exists($value ['file_id'], $fileList) ? $fileList [$value ['file_id']] : array();
                $value['fileInfo']=$fileInfo ? $ossLogModel->getFormatFile($fileInfo) : array();
                //缩略图
                $thumbInfo=$fileList && array_key_exists($value ['thumb_id'], $fileList) ? $fileList [$value ['thumb_id']] : array();
                $value['thumbInfo']=$thumbInfo ? $ossLogModel->getFormatFile($thumbInfo) : array();
                $noteList [$key] = $value;
            }
        }
        return $this->fetch('', array('keyword' => $keyword, 'list' => $noteList, 'page' => $noteList->render()));
    }

    /**
     * 删除笔记
     */
    public function del(){
        $param=$this->request->param();
        if (empty($param['ids'])){
            $this->error("参数错误");
        }
        $ids=is_array($param['ids']) ? implode(",", $param['ids']) : $param['ids'];
        if (empty($ids)){
            $this->error("请选择要删除的文件");
        }
        $noteModel=model('Note');
        $noteList=$noteModel->where(array('id'=>array('in',$ids)))->select();
        if (!$noteList->isEmpty()){
            $noteLabelModel=model("NoteLabel");
            $aliyunOssModel=model("AliyunOss");
            $notebookModel=model("Notebook");
            foreach ($noteList as $value){
                //删除标签
                $noteLabelModel->delLabelByNoteId($value['id']);
                //删除文件
                $aliyunOssModel->deleteObject($value['file_id']);
                $notebookModel->where(array('id'=>$value['notebook_id']))->setDec("quantity",1);
            }
            $noteModel->where(array('id'=>array('in',$ids)))->delete();
            $this->success("删除成功",url('index'));
        }else{
            $this->error("笔记文件不存在");
        }

    }
}