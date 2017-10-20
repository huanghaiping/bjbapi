<?php return array (
  'SEND_EMAIL_REG' => 
  array (
    'id' => 1,
    'temp_title' => '用户注册邮件',
    'temp_key' => 'SEND_EMAIL_REG',
    'content_key' => '<p><strong>尊敬的:{name},您好</strong><br/>感谢您使用服务，邮箱验证邮件已经发送,您只需在app输入验证码：<br/><strong>{verify}</strong></p><p>即可验证邮箱。<br/>如果在操作过程中有什么问题可以联系我们,联系我们,谢谢！<br/><br/></p>',
    'title_key' => '邮箱验证邮件已发送 ',
    'send_email' => 1,
    'send_message' => 0,
    'tip_message' => '验证码{verify},示用户名{name} ',
    'type' => 2,
    'lang' => 'cn',
    'ctime' => 1507709641,
  ),
  'SEND_EMAIL_FIND_PASSWORD' => 
  array (
    'id' => 2,
    'temp_title' => '找回密码邮件',
    'temp_key' => 'SEND_EMAIL_FIND_PASSWORD',
    'content_key' => '<p><strong>尊敬的:{name},您好</strong><br/>您的密码找回要求已经得到验证,您只需在APP客户端输入验证码：<br/><strong>{verify}</strong></p><p>输入新的密码后提交，之后您即可使用新的密码登录了。<br/>如果在操作过程中有什么问题可以联系我们的
,谢谢！<br/><br/></p>',
    'title_key' => '找回密码邮件已发送',
    'send_email' => 1,
    'send_message' => 0,
    'tip_message' => '验证码{verify},示用户名{name} ',
    'type' => 2,
    'lang' => 'cn',
    'ctime' => 1507709738,
  ),
);