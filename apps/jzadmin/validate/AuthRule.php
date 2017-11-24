<?php
namespace app\jzadmin\validate;
use think\Validate;
class AuthRule extends Validate{
	
	 // 验证规则
    protected $rule = [
        ['title', 'require', '权限名称不能为空'], 
    ];
}