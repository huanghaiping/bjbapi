<?php
/**
 * Created by PhpStorm.
 * User: huanghaiping
 * Date: 2017/11/28
 * Time: 16:07
 */
namespace app\common\model\app;

class AppUpgrade extends  \app\common\model\Common
{
    /**
     * 保存升级接口的数据
     * @param $postData
     */
    public function createVersionData($postData){
        if (empty($postData) || !is_array($postData)) {
            return false;
        }
        $id = isset($postData['id']) ? intval($postData['id']) : 0;
        $data = array('ctime'=>time());
        $data['app_id'] = isset($postData['app_id']) ? addSlashesFun($postData['app_id']) : "";
        if (empty($data['app_id'])){
            $this->error="app_id为空";
            return false;
        }
        $data['version_id'] = isset($postData['version_id']) ? addSlashesFun($postData['version_id']) : "";
        if (empty($data['version_id'])){
            $this->error="版本id为空";
            return false;
        }
        $data['version_code'] = isset($postData['version_code']) ? addSlashesFun($postData['version_code']) : "";
        if (empty($data['version_code'])){
            $this->error="版本码不能为空ß";
        }
        $data['upgrade_type']=isset($postData['upgrade_type']) ? intval($postData['upgrade_type']) : "";
        $data['download_url']=isset($postData['download_url']) ? addSlashesFun($postData['download_url']) : "";
        $data['content']=isset($postData['content']) ? addSlashesFun($postData['content']) : "";
        $data['status']=isset($postData['status']) ? intval($postData['status']) : 1;
        if (empty($id)) {
            $id = $this->insertGetId($data);
        } else {
            $this->where(array('id' => $id))->update($data);
        }
        cache(md5($data['app_id']."_".$data['version_id']),null,array('path'=>DATA_PATH));
        return $id;
    }

    /**
     * 升级的类型
     * @param $typeId
     */
    public function upgradeType($typeId=''){
        $upgradeTypeArray=array(1=>'Android app升级',2=>'硬件升级');
        return $typeId && !empty($typeId) && isset($upgradeTypeArray[$typeId]) ? $upgradeTypeArray[$typeId] : $upgradeTypeArray;
    }

    /**
     * 根据应用的id和升级的类型查询是否有升级
     * @param $appId
     * @param $upgradeType
     */
    public function getAppUpgrade($appId,$upgradeType){
        if (empty($appId) || empty($upgradeType))
            return false;
        $upgradeInfo=$this->where(array('app_id'=>$appId,"upgrade_type"=>$upgradeType))->order("id desc")->limit(1)->find();
        if ($upgradeInfo && $upgradeInfo['status']==1){
            $preg='/^(http:\/\/|https:\/\/).*$/';  //正则，匹配以http://开头的字符串
            if (isset($upgradeInfo['download_url']) && !empty($upgradeInfo['download_url']) && !preg_match($preg,$upgradeInfo['download_url'])){
                $upgradeInfo['download_url']='https://'.$_SERVER['HTTP_HOST'].$upgradeInfo['download_url'];
            }
            return $upgradeInfo;
        }
        return false;
    }
}