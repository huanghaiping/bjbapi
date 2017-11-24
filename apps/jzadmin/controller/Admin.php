<?php
/**
 * +--------------------------------------------------------------------
 * 管理+权限控制器
 * +--------------------------------------------------------------------
 * @author Alan(451648237@qq.com)
 +--------------------------------------------------------------------
 */
namespace app\jzadmin\controller;
use think\Db;
class Admin extends Common {
	
	/**
	 * +-----------------------------------------------------------------
	 * 管理员列表
	 * +-----------------------------------------------------------------
	 */
	public function index() {
		$keyword = isset ( $_REQUEST ['keyword'] ) ? $_REQUEST ['keyword'] : "";
		$condition = "";
		if (! empty ( $keyword )) {
			$condition = " nickname like '%{$keyword}%'";
		}
		$role_id = input ( "role_id" );
		if (! empty ( $role_id )) {
			$condition = " role_id = '{$role_id}'";
		}
		$adminList = Db::table ( config ( "database.prefix" ) . "admin" )->alias ( "a" )->join ( config ( "database.prefix" ) . "auth_group g", "a.role_id=g.id" )->where($condition)->field ( "a.*,g.title" )->select ();
		$this->assign ( "keyword", $keyword );
		$this->assign ( "roleList", $adminList );
		return view ();
	}
	
	/**
	 * +-----------------------------------------------------------------
	 * 添加管理员
	 * +-----------------------------------------------------------------
	 */
	public function add() {
		if ($this->request->ispost ()) {
			$data = array ();
			$data ['nickname'] = input ( "nickname" );
			if (empty ( $data ['nickname'] )) {
				$this->error ( "用户名不能为空" );
			}
			$data ['pwd'] = input ( "pwd" );
			if (empty ( $data ['pwd'] )) {
				$this->error ( "密码不能为空" );
			}
			$data ['pwd'] = password_md5 ( $data ['pwd'] );
			$data ['role_id'] = input ( "role_id" );
			$data ['email'] = input ( "email" );
			$data ['status'] = input ( "status" );
			$data ['remark'] = input ( "remark" );
			$data ['addtime'] = time ();
			$data ['ip'] = $this->request->ip ();
			$id = Db::name ( 'admin' )->insertGetId ( $data ); //添加行为
			if ($id) {
				Db::name ( 'auth_group_access' )->insert ( array ("uid" => $id, "group_id" => $data ['role_id'] ) );
				Db::name ( 'auth_group' )->where ( "id", $data ['role_id'] )->setInc ( "admin_num", 1 );
				$this->success ( "添加成功"  ,url('index'));
			} else {
				$this->error ( "添加失败" );
			}
		} else {
			$roleList = Db::name ( 'auth_group' )->order ( "id ASC" )->select ();
			$this->assign ( "group_list", $roleList );
			$this->assign ( "method", "add" );
			return view ();
		}
	}
	
	/**
	 * +-----------------------------------------------------------------
	 * 修改管理员
	 * +-----------------------------------------------------------------
	 */
	public function edit() {
		$id=input("id/d");
		if (empty($id)){
			$this->error("参数错误");
		}
		$db_name = think_db('admin');
		$info=$db_name->where ( "user_id", $id )->find();
		if ($this->request->ispost ()) {
			$data = array ();
			$data ['nickname'] = input ( "nickname" );
			if (empty ( $data ['nickname'] )) {
				$this->error ( "用户名不能为空" );
			}
			$pwd = input ( "pwd" );
			if (! empty ( $pwd )) {
				$data ['pwd'] = password_md5 ($pwd);
			}
			$data ['role_id'] = input ( "role_id" );
			$data ['email'] = input ( "email" );
			$data ['status'] = input ( "status" );
			$data ['remark'] = input ( "remark" );
			$data ['addtime'] = time ();
			$data ['ip'] = $this->request->ip ();
			$result = $id = $db_name->where ( "user_id", $id )->update ( $data ); //添加行为
			if ($result) {
				if ($info['role_id']!=$data['role_id']){
					$db_auth_group_access=Db::name ( 'auth_group_access' );
					$db_auth_group_access->where(array ("uid" => $info['user_id'], "group_id" => $info ['role_id'] ) )->update (array('group_id'=>$data['role_id']) );
					$db_auth_group=Db::name ( 'auth_group' );
					$db_auth_group->where ( "id", $data ['role_id'] )->setInc ( "admin_num", 1 );
					$db_auth_group->where ( "id", $info ['role_id'] )->setDec ( "admin_num", 1 );
				}	
				$this->success ( "修改成功" ,url('index'));
			} else {
				$this->error ("修改失败" );
			}
		} else {
			$this->assign("info",$info);
			$roleList = Db::name ( 'auth_group' )->order ( "id ASC" )->select ();
			$this->assign ( "group_list", $roleList );
			$this->assign ( "method", "edit" );
			return view ( "add" );
		}
	}
	
	/**
	 * +-----------------------------------------------------------------
	 * 删除管理员
	 * +-----------------------------------------------------------------
	 */
	public function delete_admin() {
		$id = input ( "id/d" );
		if (empty ( $id )) {
			$this->error ( "非法参数" );
		}
		$admin_table = Db::name ( 'admin' );
		$info = $admin_table->where ( "user_id", $id )->find ();
		if (! $info) {
			$this->error ( "管理员不存在" );
		}
		$result = $admin_table->where ( "user_id", $id )->delete ();
		if ($result) {
			Db::name ( 'auth_group_access' )->where ( "uid='" . $info ['user_id'] . "' and group_id='" . $info ['role_id'] . "' " )->delete ();
			Db::name ( 'auth_group' )->where ( "id", $info ['role_id'] )->setDec ( "admin_num", 1 );
			$this->success ( "删除成功" );
		} else {
			$this->error ( "删除失败" );
		}
	}
	
	/**
	 * +-----------------------------------------------------------------
	 * 管理员规则表
	 * +-----------------------------------------------------------------
	 */
	public function rule() {
		$auth_rule_model = model ( "AuthRule" );
		$rule_list = $auth_rule_model->getTree ();
		$this->assign ( "rule_list", $rule_list );
		return view ();
	}
	
	/**
	 * +-----------------------------------------------------------------
	 * 添加权限规则
	 * +-----------------------------------------------------------------
	 */
	public function add_rule() {
		$auth_rule_model = model ( "AuthRule" );
		if ($this->request->isPost ()) {
			$result = $auth_rule_model->updates ();
			if (false === $result) {
				$this->error ( $auth_rule_model->getError () );
			} else {
				$this->success ( '添加成功' );
			}
		} else {
			$rule_list = $auth_rule_model->getTree ();
			$this->assign ( "method", "add_rule" );
			$this->assign ( "rule_list", $rule_list );
			$this->assign ( 'info', array () );
			return view ();
		}
	}
	
	/**
	 * +-----------------------------------------------------------------
	 * 修改权限规则
	 * +-----------------------------------------------------------------
	 */
	public function edit_rule() {
		$auth_rule_model = model ( "AuthRule" );
		if ($this->request->isPost ()) {
			$result = $auth_rule_model->updates ();
			if (false === $result) {
				$this->error ( $auth_rule_model->getError () );
			} else {
				$this->success ( '修改成功' );
			}
		} else {
			$id = input ( "?param.id" ) ? intval ( input ( "param.id" ) ) : 0;
			if (empty ( $id )) {
				$this->error ( "非法参数" );
			}
			$info = $auth_rule_model::get ( $id );
			$this->assign ( "method", "edit_rule" );
			$rule_list = $auth_rule_model->getTree ();
			$this->assign ( "rule_list", $rule_list );
			$this->assign ( 'info', $info );
			return view ( "add_rule" );
		}
	}
	
	/**
	 * +-----------------------------------------------------------------
	 * 删除权限规则
	 * +-----------------------------------------------------------------
	 */
	public function delete_rule() {
		$id = input ( "?param.id" ) ? intval ( input ( "param.id" ) ) : 0;
		if (empty ( $id )) {
			$this->error ( "非法参数" );
		}
		$auth_rule_model = model ( "AuthRule" );
		$rule_list = $auth_rule_model->getTree ();
		if (! $rule_list || ($rule_list && ! isset ( $rule_list [$id] ))) {
			$this->error ( "暂无菜单" );
		}
		//判断此菜单下是否有子菜单
		$is_exits_rule = false;
		foreach ( $rule_list as $value ) {
			if ($value ['pid'] == $id) {
				$is_exits_rule = true;
				break;
			}
		}
		if ($is_exits_rule) {
			$this->error ( "该菜单下有子菜单,不能删除" );
		}
		$result = $auth_rule_model::destroy ( $id );
		if ($result) {
			F ( 'auth_rule_category', null );
			$this->success ( "删除成功" );
		} else {
			$this->error ( "删除失败" );
		}
	
	}
	
	/**
	 * +-----------------------------------------------------------------
	 * 角色管理
	 * +-----------------------------------------------------------------
	 */
	public function group() {
		$keyword = isset ( $_REQUEST ['keyword'] ) ? $_REQUEST ['keyword'] : "";
		$condition = "";
		if (! empty ( $keyword )) {
			$condition = " title like '%{$keyword}%'";
		}
		$roleList = Db::name ( 'auth_group' )->where ( $condition )->order ( "id ASC" )->select ();
		$this->assign ( "keyword", $keyword );
		$this->assign ( "roleList", $roleList );
		return view ();
	}
	
	/**
	 * +-----------------------------------------------------------------
	 * 添加角色
	 * +-----------------------------------------------------------------
	 */
	public function add_group() {
		if ($this->request->ispost ()) {
			$data = array ();
			$data ['title'] = input ( "title" );
			if (empty ( $data ['title'] )) {
				$this->error ( "角色名称不能为空" );
			}
			$data ['status'] = input ( "status" );
			$data ['ramark'] = input ( "ramark" );
			$id = Db::name ( 'auth_group' )->insertGetId ( $data ); //添加行为
			if (! $id) {
				$this->error ( "添加失败" );
			} else {
				$this->success ( "添加成功", url ( 'group' ) );
			}
		} else {
			$this->assign ( "method", "add_group" );
			return view ();
		}
	}
	
	/**
	 * +-----------------------------------------------------------------
	 * 修改角色
	 * +-----------------------------------------------------------------
	 */
	public function edit_group() {
		$id = input ( "id/d" );
		if (empty ( $id )) {
			$this->error ( "非法请求" );
		}
		$auth_group = Db::name ( 'auth_group' );
		if ($this->request->ispost ()) {
			$data = array ();
			$data ['title'] = input ( "title" );
			if (empty ( $data ['title'] )) {
				$this->error ( "角色名称不能为空" );
			}
			$data ['status'] = input ( "status" );
			$data ['ramark'] = input ( "ramark" );
			$id = $auth_group->where ( "id", $id )->update ( $data ); //添加行为
			if (! $id) {
				$this->error ( "修改失败" );
			} else {
				$this->success ( "修改成功", url ( 'group' ) );
			}
		} else {
			$info = $auth_group->where ( "id", $id )->find ();
			$this->assign ( "info", $info );
			$this->assign ( "method", "edit_group" );
			return view ( "add_group" );
		}
	}
	
	/**
	 * +-----------------------------------------------------------------
	 * 删除角色
	 * +-----------------------------------------------------------------
	 */
	public function delete_group() {
		$id = input ( "id/d" );
		if (empty ( $id )) {
			$this->error ( "非法参数" );
		}
		$auth_group = Db::name ( 'auth_group' );
		$info = $auth_group->where ( "id", $id )->find ();
		if (! $info) {
			$this->error ( "角色不存在" );
		}
		if ($info ['admin_num'] > 0) {
			$this->error ( "角色下有成员不能删除" );
		}
		$result = $auth_group->delete ( $id );
		if ($result) {
			$this->success ( "删除成功" );
		} else {
			$this->error ( "删除失败" );
		}
	}
	
	/**
	 * +-----------------------------------------------------------------
	 * 角色权限分配
	 * +-----------------------------------------------------------------
	 */
	public function changrole() {
		$group_id = input ( "id" );
		$auth_group = Db::name ( 'auth_group' );
		if ($this->request->ispost ()) {
			
			$role_id = ( int ) $_POST ['id'];
			$auth_group->where ( "id", $group_id )->setField ( "rules", "" );
			$data = isset($_POST ['data']) ? $_POST ['data'] : array();
			if (count ( $data ) == 0) {
				$this->success ( "清除所有权限成功", url ( "group" ) );
			}
			$node_ids = array ();
			foreach ( $data as $k => $v ) {
				$tem = explode ( ":", $v );
				$node_ids [] = $tem [0];
			
			}
			if ($auth_group->where ( "id", $group_id )->setField ( "rules", implode ( ",", $node_ids ) )) {
				$this->success ( "设置成功", url ( "group" ) );
			} else {
				$this->error ( "设置失败，请重试" );
			}
		} else {
			//获取角色信息
			$info = $auth_group->where ( "id", $group_id )->find ();
			
			//获取该角色拥有的权限规则
			$admin_rule_model = model ( "AuthRule" );
			$access_list = $admin_rule_model->getRuleByIds ( $info ['rules'] );
			$row = array ();
			if ($access_list) {
				foreach ( $access_list as $key => $value ) {
					$row [] ['val'] = $value ['id'] . ":" . $value ['level'] . ":" . $value ['pid'];
				}
			}
			$info ['access'] = count ( $row ) > 0 ? json_encode ( $row ) : json_encode ( array () );
			$this->assign ( "info", $info );
			$node_list = $admin_rule_model->getTree ();
			if ($node_list) {
				$node_list = list_tree ( $node_list );
				$this->assign ( "nodeList", $node_list );
			}
			return view ();
		}
	}
	
	/**
	 * +-----------------------------------------------------------------
	 * 管理员操作行为日志
	 * +-----------------------------------------------------------------
	 */
	public function log() {
		return view ();
	}
}