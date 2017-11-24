<?php
/**
 * +------------------------------------------------------------------
 * 后台管理员操作类
 * +------------------------------------------------------------------
 * @author Alan(451648237@qq.com)
 *
 */
namespace app\jzadmin\model;
use app\common\model\Common;
class Admin extends Common {
	
	/**
	 * +------------------------------------------------------------------
	 * 检查管理员是否登录
	 * +------------------------------------------------------------------
	 */
	public function checkLogin() {
		$login_status = session ( "user_auth" );
		if (empty ( $login_status )) {
			return false;
		}
		return $login_status;
	}
	
	/**
	 * +------------------------------------------------------------------
	 * 根据用户名称获取管理员信息
	 * +------------------------------------------------------------------
	 * 
	 * @param string $nickname	用户名
	 * @return Array $info	用户信息
	 */
	public function getAdminByNickname($nickname) {
		$info = $this->where ( "nickname", $nickname )->find ();
		return $info;
	}
	
	/**
	 * +------------------------------------------------------------------
	 * 更改管理员最后登录的时间和ip
	 * +------------------------------------------------------------------
	 * 
	 * @param int $user_id	管理员ID
	 */
	public function updateAdminLoginTime($user_id) {
		if (empty ( $user_id )) {
			return false;
		}
		$data = array ("logintime" => time (), 'ip' => request ()->ip () );
		$result = $this->where ( "user_id='" . $user_id . "'" )->update ( $data );
		if ($result) {
			//记录后台管理员的操作行为
		}
		return $result;
	}
	
 

}