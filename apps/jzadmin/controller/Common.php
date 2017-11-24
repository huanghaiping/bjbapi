<?php
namespace app\jzadmin\controller;
use think\Controller;
class Common extends Controller {
	
	public  $user_info=array(); //保存后台登陆用户信息
	public  $lang="";
	
	/**
	 *初始化项目开始
	 */
	public function _initialize() {
        if(isset($_POST['PHPSESSID'])){
            session_id($_POST['PHPSESSID']);
        }
		$admin_model = model ( "Admin" );
		$login_info=$admin_model->checkLogin ();
		if (!$login_info) {
			$this->redirect(url ( 'Login/index' ));
		}
		$this->lang=LANG_SET;
		$this->user_info=$login_info; 
		// 是否是超级管理员
        if($this->user_info['role_id']!=1 && config('admin_allow_ip')){
            // 检查IP地址访问
            if(!in_array($this->request->ip,explode(',',config('admin_allow_ip')))){
                $this->error('403:禁止访问');
            }
        }
         // 检测系统权限，当是超级管理员默认是所有权，不是超级管理就需要验证规则
        $rule_ids=array();
        if($this->user_info['role_id']!=1){
            $access =   $this->accessControl();
            if ( false === $access ) {
                $this->error('403:禁止访问');
            }elseif(null === $access ){
                //检测访问权限
                $rule  = strtolower($this->request->module().'/'.$this->request->controller().'/'.$this->request->action());
                if ( !$this->checkRule($rule,array('in','1,2')) ){
                    $this->error('未授权访问!');
                }
            }
            $rule_ids=$this->getGroupRule($this->user_info['id']); 
        }
        $this->assign("lang",$this->lang);
        $this->assign("rule_ids",$rule_ids);
	}
	
    /**
     * 权限检测
     * @param string  $rule    检测的规则
     * @param string  $mode    check模式
     * @return boolean 
     */
    final protected function checkRule($rule, $type=1, $mode='url'){
        static $Auth    =   null; 
        if (!$Auth) {
            $Auth       =   new \org\Auth();
        }
        if(!$Auth->check($rule,$this->user_info['id'],$type,$mode)){
            return false;
        }
        return true;
    }
    
    
 	/**
 	 * 获取所有可以访问的节点的
 	 * 
 	 * @param int $uid		用户uid
 	 * @param int $type		访问类型
 	 */
    public function getGroupRule($uid){
    	static $Auth    =   null; 
        if (!$Auth) {
            $Auth       =   new \org\Auth();
        }
        $groups=$Auth->getGroups($uid);
        $ids    = array(); //保存用户所属用户组设置的所有权限规则id
        if ($groups) {
	        foreach ($groups as $g) {
	            $ids = array_merge($ids, explode(',', trim($g['rules'], ',')));
	        }
	        $ids = array_unique($ids);
        }
        return $ids;
    }
    
    
	
 	 /**
     * action访问控制,在 **登陆成功** 后执行的第一项权限检测任务
     *
     * @return boolean|null  返回值必须使用 `===` 进行判断
     *
     *   返回 **false**, 不允许任何人访问(超管除外)
     *   返回 **true**, 允许任何管理员访问,无需执行节点权限检测
     *   返回 **null**, 需要继续执行节点权限检测决定是否允许访问
     */
    final protected function accessControl(){ 
        $allow = config('allow_visit'); //允许节点
        $deny  = config('deny_visit'); //拒绝节点
        $check = strtolower($this->request->controller() . '/' . $this->request->action());
        if (!empty($deny) && in_array_case($check, $deny)) {
            return false; //非超管禁止访问deny中的方法
        }
        if (!empty($allow) && in_array_case($check, $allow)) {
            return true;
        }
        return null; //需要检测节点权限
    }
    	
}