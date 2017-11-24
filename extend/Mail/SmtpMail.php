<?php
namespace Mail;
require_once 'class.phpmailer.php';
class SmtpMail {
	
	protected $config=array(); //定义配置信息
	
	/**
	 * +---------------------------------------------------
	 * 初始化项目开
	 * +---------------------------------------------------
	 * @param Array $config
	 */
	public function __construct($config=array()){
		if (!empty($config)){
			$this->config=$config;
		}else{
			$this->config=$this->createEmailConfig();
		}
		
	}
	
	/**
	 * +---------------------------------------------------
	 * 生成邮件配置信息
	 * +---------------------------------------------------
	 */
	protected function createEmailConfig(){
		if (function_exists("F")){
            $email_config=F("EmailConfig_".LANG_SET,'',array('path'=>DATA_PATH));
		}else{
            $email_config=require __DIR__."/../../include/data/EmailConfig_en.php";
		}
		return $email_config;
	}
	
	/**
	 * +---------------------------------------------------
	 * 发送邮件
	 * +---------------------------------------------------
	 */
	public function sendmail($to, $subject, $body, $username) {	
		if (!$this->config||($this->config&&$this->config['status']==0)){
			return false;
		}
		$mail  = new \PHPMailer();
		$mail->IsSMTP();	            				// 启用SMTP
		$mail->Host = isset($this->config['smtp']) ? trim($this->config['smtp']) : "";        			//SMTP服务器smtp.qq.com
		$mail->SMTPAuth = true;           			//开启SMTP认证
		$mail->Username =isset($this->config['accout']) ? trim($this->config['accout']) : "";     		// SMTP用户名 451648237@qq.com
		$mail->Password = isset($this->config['password']) ? trim($this->config['password']) : "";         	// SMTP密码 ugee87537115
		$mail->From = isset($this->config['from_name']) ? trim($this->config['from_name']) : "";         	//发件人地址 mailservice@linghit.com
		$mail->FromName =  isset($this->config['fromusername']) ? trim($this->config['fromusername']) : "";               //发件人
		$mail->Port = isset($this->config['port']) ? trim($this->config['port']) : ""; 
		$mail->SMTPDebug=false;
		$mail->SMTPSecure = 'ssl';
		$mail->AddAddress($to, $username);        	 	//添加收件人
		$mail->AddReplyTo($to, $username);
		$mail->CharSet = "UTF-8"; 						// 这里指定字符集！
		$mail->Encoding = "base64"; 
		$mail->WordWrap = 1024;                    		//设置每行字符长度
		$mail->IsHTML(true);                			// 是否HTML格式邮件
		$mail->Subject = $subject;      				//邮件主题
		$mail->Body    = $body;        					//邮件内容
		$mail->AltBody ="text/html"; 
		$result=$mail->Send();
		if ($result){
			$mail->Smtpclose();
			unset($mail);
			return array("status"=>1,"msg"=>"");
		}else{
			$error_info=$mail->ErrorInfo;
			return array("status"=>0,"msg"=>$error_info);
		}
	}

}

?>
