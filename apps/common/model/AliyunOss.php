<?php

namespace app\common\model;

use OSS\OssClient;

class AliyunOss extends Common
{
    protected $accessKeyId;
    protected $accessKeySecret;
    protected $endPoint;
    protected $bucketName;
    protected $_instance;

    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
        $this->accessKeyId = config('aliyun_oss_config.accessKeyId');
        $this->accessKeySecret = config('aliyun_oss_config.accessKeySecret');
        $this->endPoint = config('aliyun_oss_config.endpointUrl');
        $this->bucketName = array(
            'images' => 'images-storage',
            'note' => 'note-storage',
        );
        $this->getInstance();
    }

    /**
     * 获取一个OssClient实例
     * @return null|OssClient
     */
    private function getInstance()
    {
        if (!($this->_instance instanceof OssClient)) {
            try {
                $this->_instance = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endPoint, false);
            } catch (OssException $e) {
             //   printf($e->getMessage() . "\n");
                return null;
            }
        }
        return $this->_instance;
    }


    /**
     * 阿里云OSS云存储上传方法
     * @param $fileInfo             文件的路径
     * @param string $saveDir 云存储保存的路径
     * @param bool $isUnlink 是否删除原文件
     * @param string $bucketName 云存储空间名称
     */
    public function uploadFile($fileInfo, $saveDir = '', $isUnlink = false, $bucketName = "images")
    {
        if (empty($fileInfo) || empty($this->_instance))
            return false;
        $bucketName = isset($this->bucketName[$bucketName]) ? $this->bucketName[$bucketName] : $bucketName;
        //判断bucketname是否存在，不存在就去创建
        if (!$this->_instance->doesBucketExist($bucketName)) {
            $this->_instance->createBucket($bucketName);
        }
        $way = empty($saveDir) ? $bucketName : $saveDir;
        $object = $way . '/' . date('Ymd') . '/' . $fileInfo['savename'];//想要保存文件的名称
        $file = $fileInfo['url'];//文件路径，必须是本地的。
        $returnData = array();
        try {
            $localUrl = str_replace(array(ROOT_PATH, 'public', "\\"), array('', '', '/'), $file);
            $aliYunUploadInfo = $this->_instance->uploadFile($bucketName, $object, $file);
            $ossLogData=array('object_name'=>$object,'local_url'=>$localUrl,'ctime'=>time(),'typeid'=>$fileInfo['typeid'],'extension'=>$fileInfo['extension'],'size'=>$fileInfo['size'],'uid'=>$fileInfo['uid']);
            if ($aliYunUploadInfo && isset($aliYunUploadInfo['info']) && isset($aliYunUploadInfo['info']['http_code']) && $aliYunUploadInfo['info']['http_code'] == 200) {
                $ossLogData['oss_url']= $aliYunUploadInfo['info']['url'];
                $ossLogData['request_id']= $aliYunUploadInfo['x-oss-request-id'];
                $ossLogData['requestheaders_host']=$aliYunUploadInfo['oss-requestheaders']['Host'];
                $returnData = array('status' => 1, 'url' => $ossLogData['oss_url'],'msg' => '', 'x-oss-request-id' =>$ossLogData['request_id']);
                if ($isUnlink) {
                    @unlink($file);
                }
            } else {
                $ossLogData['oss_url'] = config('site_url') . $localUrl;
                $ossLogData['request_id']= '';
                $ossLogData['requestheaders_host']='';
                $returnData = array('status' => 1, 'url' => $ossLogData['oss_url'], 'msg' => '');
            }
            $returnData['fileId']=model('OssLog')->insertGetId($ossLogData);
        } catch (OssException $e) {
            $returnData = array('status' => 0, 'url' => '', 'msg' => $e->getErrorMessage());
        }
        return $returnData;
    }

    /**
     * 删除某个Object
     */
    public function deleteObject($fileId, $bucketName = "images")
    {
        if (empty($fileId))
            return false;
        $ossLogModel=model('OssLog');
        $fileInfo=$ossLogModel->getFileInfoById($fileId);
        if ($fileInfo){
            if (!empty($fileInfo['object_name'])) {
                $bucketName = isset($this->bucketName[$bucketName]) ? $this->bucketName[$bucketName] : $bucketName;
                $aliYunUploadInfo = $this->_instance->deleteObject($bucketName, $fileInfo['object_name']);
            }
            //删除数据库对象
            $result=$ossLogModel->delOssLog($fileId);
            if ($result){
                @unlink(UPLOADS_PATH.$fileInfo['object_name']);//删除本地文件
            }
            //删除笔记需要释放空间
            if ($fileInfo['typeid']==1 && $fileInfo['size']>0){
                model("user.UserDiskLog")->recordLog($fileInfo['uid'],2, $fileInfo['size']); //保存日志
            }
            return true;
        }
        return false;
    }

}