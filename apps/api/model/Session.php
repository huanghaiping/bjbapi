<?php

namespace app\api\model;

use app\common\model\Common;

/**
 * session 控制器操作类
 * Enter description here ...
 * @author asus
 *
 */
class Session extends Common
{

    protected $session_id = "";
    protected $max_life_time = 7776000; //一个月时间

    /**
     * 获取session_id
     * Enter description here ...
     */
    public function gen_session_id($username, $passowrd)
    {
        $this->session_id = md5(uniqid(mt_rand(), true) . $username . $passowrd);
        return $this->write(""); //写入session 数据库
    }

    /**
     * 读取Session
     * @access public
     * @param string $sessID
     */
    public function read($sessID)
    {
        $map = array();
        $map['session_id'] = $sessID;
        $map['session_expire'] = array('GT', time());
        $row = $this->where($map)->find();
        return $row;
    }

    /**
     * 写入Session
     * @access public
     * @param string $sessID
     * @param String $sessData
     */
    public function write($sessData)
    {
        $expire = time() + $this->max_life_time;
        $ip = request()->ip();
        $data = array('session_id' => $this->session_id, "session_expire" => $expire, "session_data" => $sessData, 'ip' => $ip);
        return $this->allowField(true)->save($data);
    }

    /**
     * 删除Session
     * @access public
     * @param string $sessID
     */
    public function destroy_session($sessID = "")
    {
        if (empty($sessID)) {
            $sessID = session_id();
        }
        $sessID = is_array($sessID) ? implode(",", $sessID) : $sessID;
        return $this->where('session_id', "in", $sessID)->delete();
    }

    /**
     * Session 垃圾回收
     * @access public
     * @param string $sessMaxLifeTime
     */
    public function gc($sessMaxLifeTime)
    {
        return $this->where("session_expire", "LT", time())->delete();
    }

    /**
     * 返回session_id
     * Enter description here ...
     */
    public function get_session_id()
    {
        return $this->session_id;
    }

    /**
     * 生成session数据
     * Enter description here ...
     */
    public function addsess($uid, $username, $passowrd)
    {
        //清除session
        $session_list = $this->deleteIpSession();
        if ($session_list) {
            $this->destroy_session(getSubByKey($session_list, 'session_id'));
        }
        $this->gen_session_id($username, $passowrd);
        $session_str = serialize(array("uid" => $uid, "username" => base64_encode($username), 'access_token' => $this->session_id));
        //写入session 进数据库
        $this->where(array("session_id" => $this->session_id))->update(array('session_data' => $session_str, "uid" => $uid));
        //清除这个用户的其它session
        $result = $this->where(array('uid' => $uid, 'session_id' => array('NEQ', $this->session_id)))->field("session_id")->select();
        if ($result) {
            $session_config = config('session');
            foreach ($result as $value) {
                $session_config['id'] = $value['session_id'];
                session($session_config); //初始化sssion数据
                \think\Session::destroy();
            }
            $this->where(array('uid' => $uid, 'session_id' => array('NEQ', $this->session_id)))->delete();
        }
        $this->updateSession($this->session_id, 'USER_INFO_KEY', $session_str); //保存session数据
        $this->where(array('uid' => 0, 'ip' => request()->ip()))->delete();//删除无效session
        return $this->session_id;
    }

    /**
     * 加载session 数据
     * Enter description here ...
     */
    public function load_session($session_id)
    {
        if (empty ($session_id))
            return false;
        $result = $this->where(array('session_id' => $session_id))->field("session_id,uid,session_expire,session_data")->find();
        if ($result) {
            if (!empty($result['session_data']) && time() - $result['session_expire'] <= $this->max_life_time) { //未过期
                $this->updateSession($session_id, 'USER_INFO_KEY', $result['session_data']);
                return $result['session_data'];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 更改Session的数据
     */
    public function updateSession($session_id, $key, $value = '')
    {
        $session_config = config('session');
        $session_config['id'] = $session_id;
        session($session_config); //初始化sssion数据
        if (empty($value)) {
            $value = session(md5($key));
            if ($value) {
                return $value;
            } else {
                \think\Session::destroy();
                return '';
            }
        } else {
            session(md5($key), $value);
            return $value;
        }
    }

    /**
     * 删除uid为0的同iP数据
     */
    public function deleteIpSession($ip = '')
    {
        if (empty($ip))
            $ip = request()->ip();
        $session_list = $this->where(array('uid' => 0, 'ip' => $ip))->field('session_id')->select();
        if ($session_list) {
            $session_config = config('session');
            foreach ($session_list as $value) {
                $session_config['id'] = $value['session_id'];
                session($session_config); //初始化sssion数据
                \think\Session::destroy();
            }
        }
        return $session_list;
    }

    /**
     * 验证用户是否处于登录的状态
     * @param $uid
     * @param string $accessToken
     * @return bool|mixed|string
     */
    public function _check_user($uid, $accessToken = '')
    {
        if (empty($accessToken))
            return false;
        $userInfo = $this->updateSession($accessToken, 'USER_INFO_KEY');
        if (!$userInfo) {
            $userInfo = $this->load_session($accessToken);
        }
        if (!$userInfo) {
            return false;
        }
        $userInfo = unserialize($userInfo);
        $userInfo['username'] = isset($userInfo['username']) ? base64_decode($userInfo['username']) : "";
        if (isset($userInfo['uid']) && !empty($userInfo['uid'])) {
            return $uid != $userInfo ['uid'] ? false : $userInfo;
        } else {
            return false;
        }
    }
}