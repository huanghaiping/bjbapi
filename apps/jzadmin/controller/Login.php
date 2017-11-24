<?php
/**
 * +------------------------------------------------------------------------
 * 后台登陆控制器
 * +------------------------------------------------------------------------
 * @author Alan(451648237@qq.com)
 *
 */
namespace app\jzadmin\controller;
use think\Controller;
class Login extends Controller {

	/**
	 * +--------------------------------------------------------------------
	 * 后台登陆页面
	 * +--------------------------------------------------------------------
	 */
	public function index() {
		$admin_model = model ( "Admin" );
		if ($admin_model->checkLogin ()) {
            $this->redirect(url ( "Index/index" ));
		}
		if ($this->request->isPost ()) {
			if (! extension_loaded ( 'curl' )) {
				$this->error ( '抱歉，您的服务器，还不支持curl扩展，请配置后登录！' );
			}
			$username = input ( '?post.username' ) ? addSlashesFun ( input ( 'post.username' ) ) : "";
			if (empty ( $username )) {
				$this->success ( "用户名为空", url ( 'Login/index' ) );
			}
			$password = input ( '?post.password' ) ? addSlashesFun ( input ( 'post.password' ) ) : "";
			if (empty ( $password )) {
				$this->error ( "密码为空", url ( 'Login/index' ) );
			}
			$verify = input ( '?post.verify' ) ? addSlashesFun ( input ( 'post.verify' ) ) : "";
			if (empty ( $verify )) {
				$this->error ( '验证码为空', url ( 'Login/index' ) );
			}
			if (! captcha_check ( $verify )) {
				$this->error ( '验证码错误', url ( 'Login/index' ) );
			}
			$authInfo = $admin_model->getAdminByNickname ( $username );
			if (false == $authInfo) {
				$this->error ( '用户名错误', url ( 'Login/index' ) );
			} else {
				if ($authInfo ['pwd'] != password_md5 ( $password )) {
					$this->error ( '密码错误', url ( 'Login/index' ) );
				}
				if ($authInfo ['status'] == 0) {
					$this->error ( "登录失效,账号已经禁用", url ( 'Login/index' ) );
				}
				$session_data = array ('id' => $authInfo ['user_id'], "nickname" => $authInfo ['nickname'], "role_id" => $authInfo ['role_id'] );
				session ( "user_auth", $session_data ); //存进session 保存登录信息
				$admin_model->updateAdminLoginTime ( $authInfo ['user_id'] );
				$this->redirect(url ( "Index/index" ));
			}
		} else {
			return view ();
		}
	}
		
	/**
	 * +--------------------------------------------------------------------
	 * 用户退出
	 * +--------------------------------------------------------------------
	 */
	public function logout() {
		$admin_model = model ( "Admin" );
		$login_info = $admin_model->checkLogin ();
		if ($login_info) {
			session ( "user_auth", null );
			session ( null );
			unset ( $_SESSION );
			session_destroy ();
		}
        $this->redirect(url ( 'Login/index' ) );
	}
}