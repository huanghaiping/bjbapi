<?php
namespace app\jzadmin\controller;
class Index extends Common{
	
	/**
	 * +----------------------------------------------------
	 * 后台的操作首页
	 * +----------------------------------------------------
	 */
	public function index(){
		//服务器信息
		$site_model=model("Site");
		$server_info=$site_model->getServerInfo();
		$this->assign ( 'server_info', $server_info );
        return $this->fetch();
	}
	
}