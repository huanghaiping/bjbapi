<?php

namespace app\jzadmin\controller;
class User extends Common
{

    /**
     * +---------------------------------------------------------------
     * 会员列表
     * +---------------------------------------------------------------
     */
    public function index()
    {
        $param = $this->request->param();
        $keyword = isset ($param ['keyword']) ? addSlashesFun($param ['keyword']) : "";
        $where = array('lang' => $this->lang);
        if (!empty ($keyword)) {
            if (checkEmailFormat($keyword)) {
                $where ['email'] = $keyword;
            } elseif (valdeTel($keyword)) {
                $where ['mobile'] = $keyword;
            } else {
                $where ['nickname'] = array('like', '%' . $keyword . '%');
            }
        }
        $request = array('status', 'level_id');
        foreach ($request as $value) {
            $status = isset ($param [$value]) ? $param [$value] : "";
            if ($status != "") {
                $where [$value] = $status;
                $this->assign($value, $status);
            }
        }
        $userLevelList = model("UserLevel")->getUserLevel();
        $userModel = model("User");
        $userList = $userModel->where($where)->order("uid desc")->paginate(20, false, array("query" => $param));
        if (!$userList->isEmpty()) {
            foreach ($userList as $key => $value) {
                $value ['level_info'] = $userLevelList && array_key_exists($value ['level_id'], $userLevelList) ? $userLevelList [$value ['level_id']] : array();
                $userList [$key] = $value;
            }
        }
        return $this->fetch('', array('keyword' => $keyword, 'list' => $userList, 'page' => $userList->render(), 'user_level' => $userLevelList));
    }

    /**
     * +---------------------------------------------------------------
     * 修改用户资料
     * +---------------------------------------------------------------
     */
    public function edit()
    {
        $param = $this->request->param();
        if (empty ($param ['uid'])) {
            $this->error("参数错误");
        }
        $userModel = model("User");
        if ($this->request->isPost()) {
            $data = $user_info = array();
            $data ['email'] = isset ($param ['email']) ? ($param ['email']) : "";
            if (!empty($data ['email'])) {
                if (!checkEmailFormat($data ['email'])) {
                    $this->error("邮箱格式错误");
                }
                if ($data ['email'] != $param ['old_email']) {
                    $email_count = $userModel->where(array('email' => $data ['email']))->count();
                    if ($email_count > 0) {
                        $this->error("邮箱已经存在");
                    }
                }
            }
            if (isset ($param ['password']) && !empty ($param ['password'])) {
                if ($param ['password'] != $param ['re_password']) {
                    $this->error("两次输入密码不一致");
                }
                $data ['password'] = password_md5($param ['password']);
            }
            $data ['status'] = isset ($param ['status']) ? intval($param ['status']) : 1;
            $data ['mobile'] = isset ($param ['mobile']) ? addSlashesFun($param ['mobile']) : "";
            if (!empty($data ['mobile'])){
                if ($data ['mobile'] != $param ['old_mobile']) {
                    $email_count = $userModel->where(array('mobile' => $data ['mobile']))->count();
                    if ($email_count > 0) {
                        $this->error("手机号码已经存在");
                    }
                }
            }

            $data['nickname'] = !empty ($data ['nickname']) && !empty ($data ['nickname']) ? $data ['nickname']  : "";
            $user_info['country_id'] = isset($param['country_id']) ? intval($param['country_id']) : 0;
            $user_info ['sex'] = isset ($param ['sex']) ? intval($param ['sex']) : 0;
            $user_info['birth'] = isset($param['birth']) ? strtotime($param['birth']) : "";
            $user_info ['update_time'] = time();
            $result = $userModel->where(array("uid" => $param ['uid']))->update($data);
            model("user.UserInfo")->where(array("uid" => $param ['uid']))->update($user_info);
            $this->success("修改成功");
        } else {
            $field = "u.*";
            $field .= ",d.userip,d.qq,d.sex,d.country_id,d.province,d.city,d.city_name,d.district,d.twon,d.address,d.birth,d.device_name,d.update_time";
            $userInfo = $userModel->alias("u")->join("__USER_INFO__ d", "u.uid=d.uid", "LEFT")->where(array('u.uid' => $param['uid']))->field($field)->limit(1)->find();
            if ($userInfo) {
                $userInfo['fileInfo'] = array();
                if (!empty($userInfo['faceurl'])) {
                    $ossLogModel = model("OssLog");
                    $fileInfo = $ossLogModel->getFileInfoById($userInfo['faceurl']);
                    if ($fileInfo) {
                        $userInfo['fileInfo'] = $ossLogModel->getFormatFile($fileInfo);
                    }
                }
            }
            return $this->fetch('', array('method' => "edit", "info" => $userInfo));
        }
    }


    /**
     * +---------------------------------------------------------------
     * Ajax根据用户等级或者昵称或者邮箱查询用户信息
     * +---------------------------------------------------------------
     */
    public function ajax_user_levelid_email()
    {
        $param = $this->request->param();
        $email = isset($param ['email']) ? addSlashesFun($param ['email']) : "";
        $level_id = isset($param['level_id']) ? intval($param['level_id']) : 0;
        if (empty ($email) && empty($level_id)) {
            $this->error("请输入搜索的邮箱或者昵称");
        }
        $map = array();
        if (!empty($email)) {
            if (checkEmailFormat($email)) {
                $map ['email'] = $email;
            } else {
                $map ['nickname'] = array('like', '%' . $email . '%');
            }
        }
        $map ['status'] = 1;
        if (!empty ($level_id)) {
            $map ['level_id'] = $level_id;
        }
        $user_model = model("User");
        $field = "uid,nickname,email,mobile,faceurl,status,score";
        $user_list = $user_model->where($map)->field($field)->order("uid desc")->select();
        if ($user_list) {
            $user_list = $user_list->toArray();
            $this->success("处理成功" . $user_model->getLastSql(), '', $user_list);
        } else {
            $this->error("暂无用户");
        }
    }
}