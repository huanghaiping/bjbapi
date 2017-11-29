<?php
/**
 * Created by PhpStorm.
 * User: huanghaiping
 * Date: 2017/11/28
 * Time: 11:14
 */

namespace app\common\model;


class App extends Common
{
    /**
     *  获取应用的访问权限
     * @param $appId
     * @param $appSecret
     *
     * @return  booleanss
     */
    public function getApp($appId, $appSecret)
    {
        if (empty($appId) || empty($appSecret)) {
            $this->error = lang("PARAM_ERROR");
            return false;
        }
        $appInfo = $this->where(array('appid' => $appId, "status" => 1))->field(true)->limit(1)->find();
        if ($appInfo && $appInfo['secret_key'] == $appSecret) {
            return $appInfo;
        }
        return false;

    }

    /**
     * 保存app的数据
     * @param $postData
     */
    public function createAppData($postData)
    {
        if (empty($postData) || !is_array($postData)) {
            return false;
        }
        $id = isset($postData['id']) ? intval($postData['id']) : 0;
        $data = array();
        $data['appname'] = isset($postData['appname']) ? addSlashesFun($postData['appname']) : "";
        if (empty($data['appname'])) {
            $this->error = "应用名称不能为空";
            return false;
        }
        $data['appid'] = isset($postData['appid']) ? addSlashesFun($postData['appid']) : "";
        if (empty($data['appid'])) {
            $this->error = "appid不能为空";
            return false;
        }
        if (empty($id) && $this->where(array('appid' => $data['appid']))->count() > 0) {
            $this->error = "appid已经存在";
            return false;
        }
        $data['secret_key'] = isset($postData['secret_key']) ? addSlashesFun($postData['secret_key']) : "";
        //56b8285d6789f2890554885788eb0c64
        if (empty($data['secret_key'])){
            $data['secret_key']=$this->createSecret();
        }
        $data['content'] = isset($postData['content']) ? addSlashesFun($postData['content']) : "";
        $data['status'] = isset($postData['status']) ? intval($postData['status']) : 1;
        if (empty($id)) {
            $data['ctime'] = time();
            $id = $this->insertGetId($data);
        } else {
            $data['update_time'] = time();
            $this->where(array('id' => $id))->update($data);
        }
        return $id;
    }

    /**
     * 生成访问的密钥
     */
    private function createSecret(){
        return md5(md5(uniqid()."_".time()."_".mt_rand(10000,99999)));
    }

    /**
     * 批量获取应用的信息
     * @param $appIds
     */
    public function getAppListByIds($appIds){
        if (empty($appIds)){
            return false;
        }
        $appIds=is_array($appIds) ? implode(",",$appIds) : $appIds;
        $appList=$this->where(array("id"=>array('in',$appIds)))->field("id,appname,appid")->order("id desc")->select();
        if (!$appList->isEmpty()){
            $appListArray=array();
            foreach ($appList as $value){
                $appListArray[$value['id']]=$value;
            }
            unset($appList);
            return $appListArray;
        }
        return false;
    }
}