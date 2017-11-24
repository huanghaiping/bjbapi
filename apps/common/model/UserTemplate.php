<?php
namespace app\common\model;
class UserTemplate extends Common {
	
/**
	 * +----------------------------------------------------
	 * 获取所有的模板
	 * +----------------------------------------------------
	 */
	public function getAllTemplate() {
		$templte = F ( "user_template_" . $this->lang );
		if (! $templte) {
			$letter_template = $this->where(array('lang'=>$this->lang))->select ();
			if (!$letter_template->isEmpty()){
				$letter_template=$letter_template->toArray();
				$templte = array ();
				foreach ( $letter_template as $value ) {
					$templte [$value ['temp_key']] = $value;
				}
				unset($letter_template);
				F ( "user_template_" . $this->lang, $templte );
			}
		}
		return $templte;
	}
	
	/**
	 * +----------------------------------------------------
	 * 获取系统消息模板内容
	 * +----------------------------------------------------
	 * 
	 * @param 	String 	$temple_key  消息模板的KEY
	 * @param 	Array	$info		   模板替换的内容值
	 * @return  Array   $data		  返回模板替换后的数组
	 */
	public function getSystemTemp($temple_key, $info) {
		if (empty ( $temple_key ))
			return false;
		if (! is_array ( $info )) {
			return false;
		}
		$templte = $this->getAllTemplate ();
		if (! array_key_exists ( $temple_key, $templte )) {
			return false;
		}
		$template_info = $templte [$temple_key];
		if (! $template_info ['send_email']) {
			return false;
		}
		if (is_array ( $info ) && count ( $info ) > 0) {
			$temp_find [] = '{time}';
			$temp_replace [] =date("Y-m-d H:i");
			foreach ( $info as $key => $value ) {
				$temp_find [] = "{{$key}}";
				$temp_replace [] = $value;
			}
		}
		$data = array ();
		$data ['id'] = $template_info ['id'];
		$data ['content'] = str_replace ( $temp_find, $temp_replace, stripslashes($template_info['content_key'] )); //设置模板
		$data ['title'] = str_replace ( $temp_find, $temp_replace, $template_info['title_key'] ); //设置模板
		unset ( $templte );
		return $data;
	
	}
}