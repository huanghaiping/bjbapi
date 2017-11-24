<?php
namespace app\jzadmin\controller\user;
use app\jzadmin\controller\Common;
class Level extends Common {
	/**
	 * +---------------------------------------------------------------
	 * 会员等级管理
	 * +---------------------------------------------------------------
	 */
	public function index() {
		$user_levle_model = model ( "UserLevel" );
		$list = $user_levle_model->where ( 'lang', $this->lang )->order ( "id asc" )->paginate ( 20, false );
		return $this->fetch ( '', array ('list' => $list, 'page' => $list->render () ) );
	}
	
	/**
	 * +---------------------------------------------------------------
	 * 添加会员等级管理
	 * +---------------------------------------------------------------
	 */
	public function add_level() {
		$user_levle_model = model ( "UserLevel" );
		if ($this->request->isPost ()) {
			
			$result = $user_levle_model->addLevel ( $this->request->param () );
			if ($result) {
				$this->success ( "添加成功", url ( 'index' ) );
			} else {
				$this->error ( $user_levle_model->getError () );
			}
		} else {
			$level_value_list=$user_levle_model->createLevelValue();
			return $this->fetch ( '', array ('method' => "add_level",'level_value_list'=>$level_value_list ) );
		}
	}
	
	/**
	 * +---------------------------------------------------------------
	 * 添加会员等级管理
	 * +---------------------------------------------------------------
	 */
	public function edit_level() {
		$param = $this->request->param ();
		$id = isset ( $param ['id'] ) ? intval ( $param ['id'] ) : "";
		if (empty ( $id )) {
			$this->error ( "非法请求" );
		}
		$user_levle_model = model ( "UserLevel" );
		if ($this->request->isPost ()) {
			$result = $user_levle_model->addLevel ( $this->request->param () );
			if ($result) {
				$this->success ( "修改成功", url ( 'index' ) );
			} else {
				$this->error ( $user_levle_model->getError () );
			}
		} else {
			$info = $user_levle_model->find ( $id );
			$level_value_list=$user_levle_model->createLevelValue();
			return $this->fetch ( 'add_level', array ('method' => "edit_level", 'info' => $info,'level_value_list'=>$level_value_list ) );
		}
	}
	
	/**
	 * +---------------------------------------------------------------
	 * 删除会员等级管理
	 * +---------------------------------------------------------------
	 */
	public function delete_level() {
		$param = $this->request->param ();
		$id = isset ( $param ['id'] ) ? intval ( $param ['id'] ) : "";
		if (empty ( $id )) {
			$this->error ( "非法请求" );
		}
		//查询等级下是否有会员
		$user_model = model ( "user" );
		$user_level_count = $user_model->where ( "level_id", $id )->count ();
		if ($user_level_count > 0) {
			$this->error ( "会员等级下有会员,不能删除" );
		}
		$user_levle_model = model ( "UserLevel" );
		$result = $user_levle_model->where ( "id", $id )->delete ();
		if ($result) {
			F ( "user_level_" . $this->lang, null );
			$this->success ( "删除成功", url ( 'level' ) );
		} else {
			$this->error ( '删除失败' );
		}
	
	}
}