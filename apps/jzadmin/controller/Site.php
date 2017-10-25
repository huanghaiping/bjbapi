<?php
namespace app\jzadmin\controller;
class Site extends Common {
	
	protected $groupId = array (1 => '网站信息', 2 => '会员配置', 3 => '系统配置' );
	
	/**
	 * +---------------------------------------------------------
	 * 系统参数设置
	 * +---------------------------------------------------------
	 */
	public function index() {
		
		$site_model = model ( "Site" );
		if ($this->request->isPost ()) {
			$data  = array ();
			$where = "";
			$param=$this->request->param();
			if ($this->lang && $param ['groupid'] != 3)
				$where = " and lang='" . $this->lang . "'";
			$multpart_array = array ();
			foreach ( $param as $key => $value ) {
				if (array_key_exists ( $key, $multpart_array )) {
					continue; //当存入已经需要删除的数据就跳过
				}
				if ($value == 'multipart') { //当是多参数时候组装数据
					$data = array ();
					$multipart_id=$param[$key.'_id'];
					$multipart_id=is_array($multipart_id) ? $multipart_id : array($multipart_id);
					foreach ( $multipart_id as $v ) {
						 
						$multipart_key= isset ( $param [$key.'_key' . "_" . $v] ) ? $param [$key.'_key' . "_" . $v] : "";
						$multipart_value=isset ( $param [$key.'_value' . "_" . $v] ) ? $param [$key.'_value' . "_" . $v] : "";
						$multipart_remark=isset ( $param [$key.'_remark' . "_" . $v] ) ? $param [$key.'_remark' . "_" . $v] : "";
						$data [$v] =array("key"=>$multipart_key,'value'=>$multipart_value,"remark"=>$multipart_remark);
						$multpart_array [$key . "_" . $v] = $v; //存入需要删除的数组
						unset ( $param [$key.'_key' . "_" . $v],$param [$key.'_value' . "_" . $v], $param [$key.'_remark' . "_" . $v] );
					}
					 
					$value = serialize (array_values($data) );
				} else {
					$value = is_array ( $value ) ? implode ( ",", $value ) : $value;
				}
				$site_model->where ( "varname='" . $key . "'" . $where )->update ( array ('value' => $value ) );
			}
			$site_model->updateConfig (); //更改配置文件
			$this->success ( "设置成功", url ( 'index' ) );
		} else {
			$row = $site_model->getAllConfigByLang ();
			$sms_info=F("SmsConfig_".$this->lang,'',array('path'=>DATA_PATH));
			$email_info=F("EmailConfig_".$this->lang,'',array('path'=>DATA_PATH));
			$water_info=F("watermark_".$this->lang,'',array('path'=>DATA_PATH));
			return $this->fetch ("",array('groupid'=>$this->groupId,"system_list"=>$row,"sms_info"=>$sms_info,"email_info"=>$email_info,"water_info"=>$water_info));
		}
	}
	
	/**
	 * +---------------------------------------------------------
	 * 添加系统参数
	 * +---------------------------------------------------------
	 */
	public function add() {
		$param = $this->request->param ();
		if ($param ['input_type'] == "multipart") { //当为多参数的时候，就固定格式参数
			$data = array ();
			$multipart_id=$param['multipart_id'];
			$multipart_id=is_array($multipart_id) ? $multipart_id : array($multipart_id);
			foreach ( $multipart_id as $v ) {
				$multipart_key= isset ( $param ['multipart_key' . "_" . $v] ) ? $param ['multipart_key' . "_" . $v] : "";
				if (!empty($multipart_key)){
					$multipart_value=isset ( $param ['multipart_value' . "_" . $v] ) ? $param ['multipart_value' . "_" . $v] : "";
					$multipart_remark=isset ( $param ['multipart_remark' . "_" . $v] ) ? $param ['multipart_remark' . "_" . $v] : "";
					$data [$v] =array("key"=>$multipart_key,'value'=>$multipart_value,"remark"=>$multipart_remark);
					unset ( $param ['multipart_key' . "_" . $v],$param ['multipart_value' . "_" . $v], $param ['multipart_remark' . "_" . $v] );
				}
			}
			$param ['value'] = serialize ( $data );
		}
		$site_model = model ( "Site" );
		$create_data = $site_model->createData ( $param );
		if ($create_data) {
			$lang_model = model ( "Lang" );
			$lang_list = $lang_model->getLang ();
			foreach ( $lang_list as $value ) {
				$create_data ['lang'] = $value ['mark'];
				$count = $site_model->where ( "varname='" . $create_data ['varname'] . "' and lang='" . $create_data ['lang'] . "'" )->count ();
				if ($count <= 0) {
					$site_model->data ( $create_data )->allowField ( true )->isUpdate ( false )->save ();
				}
			}
			$site_model->updateConfig (); //更改配置文件
			$this->success ( "添加成功" );
		} else {
			$this->error ( $this->model_name->getError () );
		}
	}
	
/**
	 * +-----------------------------------------------
	 * 邮箱设置接口
	 * +-----------------------------------------------
	 */
	public function email(){
		if ($this->request->isPost()){
			$param=$this->request->param();
			$email_config=F("EmailConfig_".$this->lang,$param,array('path'=>DATA_PATH));
			if ($this->request->isAjax()&&!empty($param['ceshi_email'])&&checkEmailFormat($param['ceshi_email'])){ //发送测试邮件
				$mail=new \Mail\SmtpMail($param);
				$result=$mail->sendmail($param['ceshi_email'],"系统的测试邮件","尊敬的用户，您好欢迎使用邮件推送产品。这是一封测试邮件，如果有需要可以通知下开发人说明邮件有收到，否则请忽略本邮件",$param['fromusername']); 
				if ($result&&$result['status']==1){
					$this->success("发送成功");
				}else{
					$this->error("发送失败:".$result['msg']);
				}
			}
			$this->success("设置成功");
		}else{
			$this->error("非法请求");
		}
	}
	
	/**
	 * +-----------------------------------------------
	 * 短信设置接口
	 * +-----------------------------------------------
	 */
	public function sms(){
		if ($this->request->isPost()){
			$param=$this->request->param();
			$Alidayu=isset($param['sms_type']) ? $param["sms_type"] : "";
			if (empty($Alidayu)){
				$this->error("请选择短信提供商");
			}
			if (empty($param['appkey'])){
				$this->error("请输入App Key");
			}
			if (empty($param['appsecret'])){
				$this->error("请输入appsecret");
			}
			if (empty($param['content'])){
				$this->error("请输入短信模板");
			}
			if (($Alidayu=="Alidayu"&&strpos($param['content'], "SMS")===false) || ($Alidayu=="Smsbao"&&strpos($param['content'], "SMS")!==false)) {
				$this->error("短信模板错误");
			}			
			$email_config=F("SmsConfig_".$this->lang,$param,array('path'=>DATA_PATH));
			if ($this->request->isAjax()&&!empty($param['ceshi_sms'])){ //发送测试邮件
				 if (!valdeTel($param['ceshi_sms'])){
				 	$this->error("手机号码格式错误");
				 }
				 $sns = \Sms\ThinkSms::getInstance ( $param['sms_type'] );
				 $result=$sns->send($param['ceshi_sms'],1);
				if ($result['status']==1){
					$this->success("短信发送成功");
				}else{
					$this->error("短信发送失败:".$result['msg']);
				}
			}else{
				$this->success("短信配置成功");
			}
		}else{
			$this->error("非法请求");
		}
	}
	
/**
	 * 图片的水印配置参数
	 * Enter description here ...
	 */
	public function watermark(){
		if ($this->request->isPost()){
			//判断是否有上传文件字体,Public/images/font/
			$param=$this->request->param();
			F("watermark_".$this->lang,$param,array('path'=>DATA_PATH));
			$this->success("设置成功");
		}else{
			$this->error("非法请求");
		}
	}
	
	
	/**
	 * 清除缓存
	 * Enter description here ...
	 */
	public function cleancache() {	
		$site_model = model ( "Site" );
		$site_model->clearCache();
		$this->success ('缓存清除成功');
	}

}