<?php
/**
 * 邮件发送接口
 * */
class Mail {
	
	public $to = '';
	public $html = '';
	public $subject = '';
	public $username = '';
	
	/**
	 * 邮件发送 (找回密码 邮箱激活)
	 * 接口参数说明:
	 * 		数组：必带:	key     value 
	 *					act					// 发送方式（注册，找回密码）
	 *   				user				// 接受人（注册，找回时的用户）
	 *   				to 					// 接受人邮箱（多个用';'隔开）
	 *   				url					// 跳转url （注册时的激活链接，找回密码时的登陆修改密码链接）
	 *   		    可选:	
	 *   				subject				// 邮件的主题
	 *   				html				// 邮件的内容(可以使用普通文本或者利用html标签进行邮件内容的撰写)
	 *   				from 				// 发送者邮箱（必须是合法的邮件地址 邮件的。如from name<from@sendcloud.org> 或者from@sendcloud.org）
	 *   				use_maillist 	 	//邮件列表（邮件列表地址中是否有邮件列表地址，"true"表示有，"false"表示无，默认为false，use_maillist为true时，接收人的邮箱地址使用邮件列表地址就可以）
  	 *					fromname 			// 邮件发送者的名称
	 *					cc					// 邮件的抄送人(多个地址使用半角“;”分隔邮件地址)
	 *					bcc 				// 邮件的秘密抄送人(多个地址使用半角“;”分隔邮件地址)
	 *					replyto				// 邮件的回复者
	 *					files				// 附件(总共附件大小必须小于10 MB 	附件，名称没有限制，可以任意命名。需要发送附件，必须使用multipart/form-data进行post提交，如果为多个文件上传，第二个开始为files2...)
	 *						
	 */
	
	public function send()
	{
		$data = array(
			'to' => $this->to,
			'html' => $this->html,
			'subject' => $this->subject,
			'api_user' 		=> 'postmaster@linghitnotice.sendcloud.org',	 			             
			'api_key' 		=> 'sTuMCQyY ',				
			'from' 			=> 'mailservice@linghit.com',
			'fromname' 		=> '灵接触网', 
		);
		
		$url = 'https://sendcloud.sohu.com/webapi/mail.send.json';
		$result =  $this->request_soho($data, $url);
		$json_result = (array)json_decode($result);		
		if($json_result['message'] == 'success' ){
			return $result;
		}
		else{
			$fileName = "./include/sendmail/error.txt";		
			$handler = fopen($fileName,"a+"); 
			fwrite($handler,$this->to.$result.date("Y-m-d H:i:s")."\r\n");
				if($json_result['errors']['0']== 'Request quota exceeded')
				{
				include("email.php");
				$smtpserver = "smtp.qq.com";//SMTP服务器
				$smtpserverport =25;//SMTP服务器端口
				$smtpusermail = "4772146@qq.com";//SMTP服务器的用户邮箱
				$smtpemailto = "huanghaiping@mmclick.com,349852499@qq.com";//发送给谁
				$smtpuser = "4772146";//SMTP服务器的用户帐号
				$smtppass = "xulide123";//SMTP服务器的用户密码
				$mailsubject = "邮件报错警告";//邮件主题
				$mailbody = "邮件地址：".$this->to."错误信息：".$result."时间：".date("Y-m-d H:i:s");//邮件内容
				$mailtype = "HTML";//邮件格式（HTML/TXT）,TXT为文本邮件
				$smtp = new smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);
				//$smtp->debug = true;//是否显示发送的调试信息
				$smtp->sendmail($smtpemailto, $smtpusermail, $mailsubject, $mailbody, $mailtype);
				}
			return $result;
		}
	}
	
	/* function send_mail() {
		
		set_time_limit(300);
		if( empty($_POST) )
		{
			die('last error : 参数不能为空');			
		}
		
		extract( $_POST );		
		if( empty($to))
		{
			die('last error : 邮箱不能为空');
		}
		
		$time = date('Y年m月d日');
		
		// 引入邮件模板
		include './mail_reg.php';
		
		// 附件列表 总大小不超过10M
		$accessory = array();
		if( ! empty($_FILES) )
		{
			$files = $this->upload_server();
			
			foreach( $files as $k => $v )
			{
				$index = $k+1;
				$accessory['files' . $index] = '@' . $v;
			}
		}
		$act = isset($act) ? $act : 'register';
		// 收件人
		$addressee = array(
			'to' 			=> $to, // 收件人邮件
			'use_maillist' 	=> isset($use_maillist) ? $use_maillist : false, // 邮件列表
			'subject' 		=> isset($subject) ? $subject : '欢迎使用灵机妙算', // 邮件主题
			'html' 			=> isset($html) ? $html : $tpl[$act], // 邮件内容，默认registern内容
		);

		// 可选参数		
		$optional = array(
			'cc' 			=> isset($cc)? $cc : "",  // 抄送
			'bcc' 			=> isset($bcc)? $bcc : "", // 秘密抄送（密送）
			'replyto' 		=> isset($replyto) ? $replyto : "", // 回复
		);

		// 发件人信息
		$sender = array(			
			'api_user' 		=> $addressee['use_maillist'] == true ? 
							'postmaster@linghitsub.sendcloud.org' : 'postmaster@demo2.sendcloud.org',	            
			'api_key' 		=> $addressee['use_maillist'] == true ? 'QqgUTxeO' : 'wCtSgLn7',				
			'from' 			=> 'xu5502103@163.com',
			'fromname' 		=> isset($fromname) ? $fromname : '灵机妙算', 
		);
		
		//全部参数
		$info = array_merge($addressee, $sender, $optional, $accessory);
		
		// 请求soho
		$url = 'https://sendcloud.sohu.com/webapi/mail.send.json';
		echo $this->request_soho($info, $url);
	} */

	/**
	* request_soho
	* 调用搜狐方式发送邮件
	* @param	string	请求地址
	* @param	array	请求数据
	* @return	json	
	*/
	public function request_soho( $data, $url )
	{
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
	    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: multipart/form-data;")); // 带文件发送
	    curl_setopt($ch, CURLOPT_URL, $url);
    	// 不同于登录SendCloud站点的帐号，您需要登录后台创建发信子帐号，使用子帐号和密码才可以进行邮件的发送。
    	curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
    	 
        $result = curl_exec($ch);
		curl_close($ch);
		
		if( $result === false ) //请求失败
        {
            return 'lost error : ' .curl_error($ch);
        }
		return $result;
	}
	
	/**
	* 上传文件到服务器 
	*/
	/* public function upload_server()
	{
		$all_path = array();
		$dir = date('Y-m-d', time());
		$path = str_replace('\\', '/', realpath('./') ) . '/' . $dir;
		if( ! is_dir($path) )
		{
			mkdir($path, 0777, true);
		}
		$total_size = 0;
		$max_size = 10 * 1024 * 1024; // 附件最大值10M
		
		foreach( $_FILES as $k => $v )
		{
			if( $total_size < $max_size )
			{
				$name = uniqid() . '.' . pathinfo($v['name'], PATHINFO_EXTENSION);
				if( move_uploaded_file($v['tmp_name'], $path . '/' . $name) )
				{
					$all_path[] = $path . '/' . $name;
				}
				$total_size += $v['size'];
			}
		}
		
		return $all_path;
	} */
}


