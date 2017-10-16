<?php

namespace app\common\model;
class Devicetoken extends \app\api\controller\Common
{

    /**
     * 添加ios的devicetoken
     * Enter description here ...
     */
    public function addDevicetoken($data = array())
    {
        if (empty ($data))
            return false;
        if (empty($data ['udid'])) {
            $data ['udid'] = $data ['uid'];
        }
        $result = $this->where(array('udid' => $data ['udid']))->field("id,uid,devicetoken")->limit(1)->find();
        if ($result) {
            $_result = $this->where(array("id" => $result ['id']))->update(array('devicetoken' => $data ['devicetoken'], 'uid' => $data ['uid'], 'ctime' => time()));
        } else {
            $data ['ctime'] = time();
            $_result = $this->insert($data);
        }
        return $_result;
    }

}