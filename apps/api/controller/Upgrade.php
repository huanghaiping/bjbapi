<?php
/**
 * Created by PhpStorm.
 * User: huanghaiping
 * Date: 2017/11/29
 * Time: 10:32
 */

namespace app\api\controller;


class Upgrade extends \think\Controller
{
    /**
     * 客户端和硬件的升级接口
     */
    public function app()
    {
        if (!$this->request->isPost()) {
            return output(0, lang('INVALID_REQUEST'));
        }
        $postData = $this->request->post();
        $versionId = isset($postData['versionId']) ? $postData['versionId'] : "";
        $versionCode = isset($postData['versionCode']) ? $postData['versionCode'] : "";
        if (empty($versionId) || empty($versionCode)) {
            return output(0, lang('VERSION_IDVERSION_CODE'));
        }
        $appId=isset($postData['appId']) ? $postData['appId'] : "";
        if (empty($appId)){
            return output(0, lang('APPLICATION_ID_EMPTY'));
        }
        $clientType = isset($postData['clientType']) ? intval($postData['clientType']) : 0; //1 IOS,2Android
        $uid = isset($postData['uid']) ? intval($postData['uid']) : 0;
        $upgradeType = isset($postData['upgradeType']) ? intval($postData['upgradeType']) : 1; //1Android app升级，2，硬件升级
        $system = isset($postData['system']) ? ($postData['system']) : "";
        $clientDevice = isset($postData['clientDevice']) ? ($postData['clientDevice']) : "";
        //判断用户的appid 是否使用
        $appModel=model("App");
        $authGetAppId=$appModel->where(array('appid'=>$appId,'status'=>1))->field("id")->limit(1)->find();
        if (!$authGetAppId){
            return output(0, lang('APPLICATION_ID_EMPTY'));
        }
        $appUpgradeModel=model("app.AppUpgrade");
        $upgradeInfo=$appUpgradeModel->getAppUpgrade($authGetAppId['id'],$upgradeType);
        if ($upgradeInfo && $upgradeInfo['version_id']>$versionId){
            $toUpgradeInfo=array(
                'id'=>$upgradeInfo['id'],
                'appId'=>$appId,
                'downloadUrl'=>$upgradeInfo['download_url'],
                'versionId'=>$upgradeInfo['version_id'],
                'versionCode'=>$upgradeInfo['version_code'],
                'upgradeType'=>$upgradeInfo['upgrade_type'],
                'upgradeInformation'=>stripslashes($upgradeInfo['content']),
                'upgradeTime'=>$upgradeInfo['ctime'],
            );

            //记录请求的日志
            $fromUpgradeInfo=array('versionId'=>$versionId,'versionCode'=>$versionCode);
            $upgradeInfoLog=array('uid'=>$uid,'clientType'=>$clientType,"system"=>$system,'clientDevice'=>$clientDevice,'id'=>$authGetAppId['id']);
            model("app.AppLog")->saveLog($fromUpgradeInfo,$toUpgradeInfo,$upgradeInfoLog);
            return output(1,lang('GET_SUCCESS'),$toUpgradeInfo);
        }else{
            return output(0, lang('NO_UPGRADE_INFORMATION'));
        }


    }
}