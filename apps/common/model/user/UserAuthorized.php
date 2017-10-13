<?php
namespace  \app\common\model\user;

class UserAuthorized extends \app\common\model\Common
{
    /**
     * 获取查询的条件
     * @param $openId
     * @param $authorizedType
     * @param string $unionId
     */
    private function getSearchWhere($openId, $authorizedType, $unionId = '')
    {
        $where = array('type' => $authorizedType);
        switch ($authorizedType) {
            //微信登录使用unionId作为唯一标识
            case 4 :
                if (empty($unionId)) {
                    return false;
                }
                $where['unionid'] = $unionId;
                break;
            //其它平台则使用openId作为唯一标识
            default :
                $where['openid'] = $openId;
                break;
        }
        return $where;
    }

    /**
     * 判断是否已经注册过了,注册过后返回用户信息
     * @param $openId               第三方平台返回openid
     * @param $authorizedType       第三方平台的类型,1本身应用（默认),2:QQ ,3: Sina, 4: 微信
     * @param string $unionId 微信返回的unionId
     */
    public function checkAuthorized($openId, $authorizedType, $unionId = '')
    {
        if (empty ($openId))
            return false;
        $field = "id,uid,nickname,openid,unionid";
        $where = $this->getSearchWhere($openId, $authorizedType, $unionId);
        if (!$where) {
            return false;
        }
        $userInfo = $this->where($where)->limit(1)->find();
        return $userInfo;
    }

    /**
     * 解绑第三方平台
     * @param $openId
     * @param $authorizedType
     * @param string $unionId
     */
    public function delAuthorized($openId, $authorizedType, $unionId = '')
    {
        if (empty ($openId))
            return false;
        $where = $this->getSearchWhere($openId, $authorizedType, $unionId);
        if (!$where) {
            return false;
        }
        $result = $this->where($where)->delete();
        return $result;
    }

    /**
     * 保存第三方平台登录信息
     * @param $authorizedData
     */
    public function addAuthorized($authorizedData)
    {
        if (empty($authorizedData))
            return false;
        $authorizedData ['ctime'] = time();
        $authorizedId = $this->insertGetId($authorizedData);
        return $authorizedId;
    }
}