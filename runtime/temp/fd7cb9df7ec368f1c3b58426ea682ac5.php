<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:70:"D:\wamp64\www\www.bjb.com\public/../apps/jzadmin\view\login\index.html";i:1507904950;s:71:"D:\wamp64\www\www.bjb.com\public/../apps/jzadmin\view\common\jscss.html";i:1507904950;s:71:"D:\wamp64\www\www.bjb.com\public/../apps/jzadmin\view\common\layer.html";i:1507904950;}*/ ?>

<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
<title>后台管理系统</title>
<meta name="description" content="后台登陆" />
<link rel="stylesheet" type="text/css" href="__STATIC__/ace/css/bootstrap.min.css" /><link rel="stylesheet" type="text/css" href="__STATIC__/ace/font-awesome/4.5.0/css/font-awesome.min.css" /><link rel="stylesheet" type="text/css" href="__STATIC__/ace/css/ace.min.css" /><link rel="stylesheet" type="text/css" href="__STATIC__/ace/css/ace-rtl.min.css" /><link rel="stylesheet" type="text/css" href="__STATIC__/ace/css/ace-skins.min.css" /><script type="text/javascript" src="__STATIC__/ace/js/ace-extra.min.js"></script>
<!--[if !IE]> -->
<script src="__STATIC__/ace/js/jquery-2.1.4.min.js"></script>
 <!-- <![endif]-->
 <!--[if IE]>
<script src="__STATIC__/ace/js/jquery-1.11.3.min.js"></script>
<![endif]--> 
<!--[if lte IE 9]>
<link rel="stylesheet" type="text/css" href="__STATIC__/ace/css/ace-part2.min.css" /><link rel="stylesheet" type="text/css" href="__STATIC__/ace/css/ace-ie.min.css" />
<script type="text/javascript" src="__STATIC__/ace/js/html5shiv.min.js"></script><script type="text/javascript" src="__STATIC__/ace/js/respond.min.js"></script>
<![endif]-->
<!--[if IE 7]>
	<link rel="stylesheet" href="__STATIC__/ace/css/font-awesome-ie7.min.css" />
<![endif]-->
<script type="text/javascript" src="__STATIC__/layer/layer.js"></script>
<script type="text/javascript">
var __updatesort__="<?php echo url('jzadmin/Ajax/updatesort'); ?>";
var __setStatus__="<?php echo url('jzadmin/Ajax/setStatus'); ?>";
</script>
<link rel="stylesheet" type="text/css" href="__STATIC__/css/jzadmin/style.css" />
 

</head>
<body class="login-layout blur-login">
<div class="main-container">
  <div class="main-content">
    <div class="row">
      <div class="col-sm-10 col-sm-offset-1">
        <div class="login-container" style="margin:5% auto">
          <div class="space-6"></div>
          <div class="position-relative">
            <div id="login-box" class="login-box visible widget-box no-border">
              <div class="widget-body">
                <div class="widget-main">
                  <h4 class="header blue lighter bigger"> <i class="ace-icon fa fa-coffee green"></i> 后台登录系统</h4>
                  <div class="space-6"></div>
                  <form method="post" action="<?php echo url('Login/index'); ?>">
                    <fieldset>
                      <label class="block clearfix"> <span class="block input-icon input-icon-right">
                        <input type="text" class="form-control" placeholder="用户名" name="username" id="username" />
                        <i class="ace-icon fa fa-user"></i> </span> </label>
                      <label class="block clearfix"> <span class="block input-icon input-icon-right">
                        <input type="password" class="form-control" placeholder="密码" name="password" id="password" />
                        <i class="ace-icon fa fa-lock"></i> </span> </label>
                      <label class="block clearfix">
                        <input type="text" class="form-control" placeholder="验证码" name="verify" id="verify" maxlength="4" size="4" />
                      </label>
                      <img src="<?php echo captcha_src(); ?>" title="看不清？单击此处刷新" onclick="this.src=this.src+'?rand='+Math.random();" style="cursor: pointer; margin:0 auto; width:100%;">
                      <div class="space"></div>
                      <div class="clearfix">
                        <button type="submit" id="submitForm" class="width-100 pull-right btn btn-lg btn-primary"> <i class="ace-icon fa fa-key"></i> <span class="bigger-110">登录</span> </button>
                      </div>
                      <div class="space-4"></div>
                    </fieldset>
                  </form>
                </div>
                <!-- /.widget-main --> 
                
              </div>
              <!-- /.widget-body --> 
            </div>
            <!-- /.login-box --> 
            
          </div>
          <!-- /.position-relative --> 
          
        </div>
      </div>
      <!-- /.col --> 
    </div>
    <!-- /.row --> 
  </div>
</div>
<script type="text/javascript" src="__STATIC__/layer/layer.js"></script> 
<script type="text/javascript">
$(function(){
	$("#submitForm").click(function(){
		var username=$("#username");
		if(username.val().length<=0){
			layer.tips("请输入用户名", '#username',{tips: [2, '#d9534f']});
			return false;
		}
		var password=$("#password");
		if(password.val().length<=0){
			layer.tips("请输入密码", '#password',{tips: [2, '#d9534f']});
			return false;
		}	
		var verify=$("#verify");
		if(verify.val().length<=0){
			layer.tips("请输入验证码", '#verify',{tips: [2, '#d9534f']});
			return false;
		}
		return true;
	});	
})
</script>
</body>
</html>
