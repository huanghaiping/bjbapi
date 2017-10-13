<?php
namespace app\jzadmin\controller;
class User extends Common {
	
	/**
	 * +---------------------------------------------------------------
	 * 会员列表
	 * +---------------------------------------------------------------
	 */
	public function index() {
		$param = $this->request->param ();
		$keyword = isset ( $param ['keyword'] ) ? addSlashesFun ( $param ['keyword'] ) : "";
		$where = array ('lang' => $this->lang );
		if (! empty ( $keyword )) {
			if (checkEmailFormat ( $keyword )) {
				$where ['email'] = $keyword;
			} elseif (valdeTel ( $keyword )) {
				$where ['mobile'] = $keyword;
			} else {
				$where ['nickname'] = array ('like', '%' . $keyword . '%' );
			}
		}
		$request = array ('status', 'level_id' );
		foreach ( $request as $value ) {
			$status = isset ( $param [$value] ) ? $param [$value] : "";
			if ($status != "") {
				$where [$value] = $status;
				$this->assign ( $value, $status );
			}
		}
		$user_level = model ( "UserLevel" )->getUserLevel ();
		$user_model = model ( "User" );
		$list = $user_model->where ( $where )->order ( "uid desc" )->paginate ( 20, false, array ("query" => $param ) );
		if ($list) {
			foreach ( $list as $key => $value ) {
				$value ['level_info'] = array_key_exists ( $value ['level_id'], $user_level ) ? $user_level [$value ['level_id']] : array ();
				$list [$key] = $value;
			}
		}
		return $this->fetch ( '', array ('keyword' => $keyword, 'list' => $list, 'page' => $list->render (), 'user_level' => $user_level ) );
	}
	
	/**
	 * +---------------------------------------------------------------
	 * 修改用户资料
	 * +---------------------------------------------------------------
	 */
	public function edit() {
		$param = $this->request->param ();
		if (empty ( $param ['uid'] )) {
			$this->error ( "参数错误" );
		}
		$user_model = model ( "User" );
		if ($this->request->isPost ()) {
			$data = $user_info = array ();
            $data ['last_name'] = isset ( $param ['last_name'] ) ? addSlashesFun ( $param ['last_name']  ) : "";
            $data ['first_name'] = isset ( $param ['first_name'] ) ? addSlashesFun ( $param ['first_name']  ) : "";
			$data ['email'] = isset ( $param ['email'] ) ? ($param ['email']) : "";
			if (! checkEmailFormat ( $data ['email'] )) {
				$this->error ( "邮箱格式错误" );
			}
			if ($data ['email'] != $param ['old_email']) {
				$email_count = $user_model->where ( array ('email' => $data ['email'] ) )->count ();
				if ($email_count > 0) {
					$this->error ( "邮箱已经存在" );
				}
			}
			if (isset ( $param ['password'] ) && ! empty ( $param ['password'] )) {
				if ($param ['password'] != $param ['re_password']) {
					$this->error ( "两次输入密码不一致" );
				}
				$data ['password'] = password_md5 ( $param ['password'] );
			}
			$data ['status'] = isset ( $param ['status'] ) ? intval ( $param ['status'] ) : 1;
			$data ['mobile'] = isset ( $param ['nickname'] ) ? addSlashesFun ( $param ['mobile'] ) : "";
            $data['country']=isset($param['country']) ? intval($param['country'])  : 0;
            $data['nickname']=! empty ( $data ['first_name']) && ! empty ( $data ['last_name'] ) ? $data ['first_name'] . " " . $data ['last_name'] : $data ['first_name'];
            $user_info ['sex'] = isset ( $param ['sex'] ) ? intval ( $param ['sex'] ) : 0;
            $user_info['birth']=isset($param['birth']) ? strtotime($param['birth']) : "";
            $user_info['drawing_habits']=isset($param['drawing_habits']) ? intval($param['drawing_habits']) : "";
			$user_info ['update_time'] = time ();
			$result = $user_model->where ( array ("uid" => $param ['uid'] ) )->update ( $data );
			model ( "user.UserInfo" )->where ( array ("uid" => $param ['uid'] ) )->update ( $user_info );
			$this->success ( "修改成功" );
		} else {
			$prix = config ( 'database.prefix' );
			$sql = "select u.*,d.reg_ip,qq,d.reg_ip,d.sex,d.is_email,d.is_email,d.birth,d.drawing_habits,d.province,d.city,d.city_name,d.district,d.twon,d.address ";
			$sql .= " from " . $prix . "user u left join " . $prix . "user_info d on u.uid=d.uid where u.uid=" . $param ['uid'] . " limit 1";
			$info = $user_model->query ( $sql );
			if ($info) {
				$info = $info [0];
			}
			return $this->fetch ( '', array ('method' => "edit", "info" => $info ) );
		}
	}
	
	
	/**
	 * +---------------------------------------------------------------
	 * Ajax根据用户等级或者昵称或者邮箱查询用户信息
	 * +---------------------------------------------------------------
	 */
	public function ajax_user_levelid_email() {
		$param = $this->request->param ();
		$email=isset($param ['email']) ? addSlashesFun($param ['email']) : "";
		$level_id=isset($param['level_id']) ? intval($param['level_id']) : 0;
		if (empty ($email ) && empty($level_id)) {
			$this->error ( "请输入搜索的邮箱或者昵称" );
		}
		$map = array ();
		if (!empty($email)){
			if (checkEmailFormat ( $email )) {
				$map ['email'] = $email;
			} else {
				$map ['nickname'] = array('like','%'.$email.'%');
			}
		}
		$map ['status'] = 1;
		if (! empty ( $level_id )) {
			$map ['level_id'] = $level_id;
		}
		$user_model = model ( "User" );
		$field="uid,nickname,email,mobile,faceurl,status,score";
		$user_list = $user_model->where($map)->field($field)->order("uid desc")->select();
		if ($user_list) {
			$user_list=$user_list->toArray();
			$this->success ( "处理成功".$user_model->getLastSql(), '', $user_list );
		} else {
			$this->error ( "暂无用户" );
		}
	}
}