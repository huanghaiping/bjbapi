<?php
/**
 * Created by PhpStorm.
 * User: huanghaiping
 * Date: 2017/11/28
 * Time: 18:17
 */

namespace app\common\model\app;


class AppLog extends \app\common\model\Common
{
    /**
     * 保存升级的日志
     * @param $fromUpgradeInfo
     * @param $toUpgradeInfo
     * @param $upgradeInfo
     */
    public function saveLog($fromUpgradeInfo, $toUpgradeInfo, $upgradeInfo)
    {
        if (empty($fromUpgradeInfo) || empty($toUpgradeInfo))
            return false;
        $data = array('uid' => $upgradeInfo['uid'], 'app_id' => $upgradeInfo['id'], 'clientType' => $upgradeInfo['clientType']);
        $data['to_upgrade_id'] = $toUpgradeInfo['id'];
        $data['to_version_id'] = $toUpgradeInfo['versionId'];
        $data['to_version_code'] = $toUpgradeInfo['versionCode'];
        $data['from_version_id'] = $fromUpgradeInfo['versionId'];
        $data['from_version_code'] = $fromUpgradeInfo['versionCode'];
        $data['ctime'] = time();
        $data['system']=$upgradeInfo['system'];
        $data['client_device']=$upgradeInfo['clientDevice'];
        return $this->insertGetId($data);
    }
}