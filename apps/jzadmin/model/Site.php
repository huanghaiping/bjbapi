<?php
namespace app\Jzadmin\Model;
use app\common\model\
Common;
class Site extends Common {
	
	/**
	 * 
	 * 创建site的数据模型
	 * @param array $postData	过滤提交的参数
	 */
	public function createData($postData) {
		$data = array ();
		$data ['varname'] = isset ( $postData ['varname'] ) ? $postData ['varname'] : "";
		$data ['groupid'] = isset ( $postData ['groupid'] ) ? intval ( $postData ['groupid'] ) : 0;
		$data ['input_type'] = isset ( $postData ['input_type'] ) ? $postData ['input_type'] : "text";
		$data ['info'] = isset ( $postData ['info'] ) ? $postData ['info'] : "";
		$data ['value'] = isset ( $postData ['value'] ) ? $postData ['value'] : "";
		$data ['html_text'] = isset ( $postData ['html_text'] ) ? $postData ['html_text'] : "";
		$data ['mark'] = isset ( $postData ['mark'] ) ? $postData ['mark'] : "";
		$data ['lang'] = $this->lang;
		$data ['ctime'] = time ();
		return $data;
	}
	
	/**
	 * +------------------------------------------------------------------
	 * 获取服务器信息
	 * +------------------------------------------------------------------
	 */
	public function getServerInfo() {
		//服务器信息
		if (function_exists ( 'gd_info' )) {
			$gd = gd_info ();
			$gd = $gd ['GD Version'];
		} else {
			$gd = "不支持";
		}
		$info = array ('操作系统' => PHP_OS, '主机名IP端口' => $_SERVER ['HTTP_HOST'] . ' (' . $_SERVER ['SERVER_ADDR'] . ':' . $_SERVER ['SERVER_PORT'] . ')', '运行环境' => $_SERVER ["SERVER_SOFTWARE"], 'PHP运行方式' => php_sapi_name () . "(PHP版本:" . PHP_VERSION . ")", '程序目录' => ROOT_PATH, 'MYSQL版本' => function_exists ( "mysql_close" ) ? mysql_get_client_info () : '不支持', 'GD库版本' => $gd, //'MYSQL版本' => mysql_get_server_info(),
'上传附件限制' => ini_get ( 'upload_max_filesize' ), '执行时间限制' => ini_get ( 'max_execution_time' ) . "秒", '剩余空间' => round ( (@disk_free_space ( "." ) / (1024 * 1024)), 2 ) . 'M', '服务器时间' => date ( "Y年n月j日 H:i:s" ), '北京时间' => gmdate ( "Y年n月j日 H:i:s", time () + 8 * 3600 ), '采集函数检测' => ini_get ( 'allow_url_fopen' ) ? '支持' : '不支持', 'register_globals' => get_cfg_var ( "register_globals" ) == "1" ? "ON" : "OFF", 'magic_quotes_gpc' => (1 === get_magic_quotes_gpc ()) ? 'YES' : 'NO', 'magic_quotes_runtime' => (1 === get_magic_quotes_runtime ()) ? 'YES' : 'NO' );
		return $info;
	}
	
	/**
	 * +------------------------------------------------------------------
	 * 获取语言下的所有配置
	 * +------------------------------------------------------------------
	 * @param string $lang 语言标识
	 */
	public function getAllConfigByLang($lang = '') {
		if (empty ( $lang ))
			$lang = $this->lang;
		$result = $this->where ( "lang='" . $lang . "'" )->select ();
		$row = array ();
		if ($result) {
			foreach ( $result as $value ) {
				$value ['input_string'] = $this->createInput ( $value );
				$row [$value ['groupid']] [] = $value;
			}
		}
		unset ( $result );
		return $row;
	}
	
	/**
	 * +------------------------------------------------------------------
	 * 创建系统配置表单控件
	 * +------------------------------------------------------------------
	 * @param array $inputValue
	 */
	public function createInput($inputValue) {
		if (empty ( $inputValue ))
			return false;
		$input_string = "";
		switch ($inputValue ['input_type']) {
			case "text" :
				$input_string = '<input name="' . $inputValue ['varname'] . '" type="text" class="form-control w30" value="' . $inputValue ['value'] . '" />';
				break;
			case "textarea" :
				$input_string = '<textarea name="' . $inputValue ['varname'] . '" cols="" rows="" class="form-control w30" >' . $inputValue ['value'] . '</textarea>';
				break;
			case "select" :
				$input_value = ! empty ( $inputValue ['html_text'] ) && strpos ( $inputValue ['html_text'], "," ) ? explode ( ",", $inputValue ['html_text'] ) : array ($inputValue ['html_text'] );
				$check_value = ! empty ( $inputValue ['value'] ) && strpos ( $inputValue ['value'], "," ) ? explode ( ",", $inputValue ['value'] ) : array ($inputValue ['value'] );
				$option_string = "";
				foreach ( $input_value as $value ) {
					$optino_array = explode ( "|", $value );
					$check_string = in_array ( $optino_array [0], $check_value ) ? "selected" : "";
					$option_string .= ' <option value="' . $optino_array [0] . '" ' . $check_string . '>' . $optino_array [1] . '</option>';
				}
				$input_string = '<select id="' . $inputValue ['varname'] . '" name="' . $inputValue ['varname'] . '"  class="form-control w30" >' . $option_string . '</select>';
				break;
			case "radio" :
				$input_value = ! empty ( $inputValue ['html_text'] ) ? explode ( ",", $inputValue ['html_text'] ) : array ();
				$check_value = ! empty ( $inputValue ['value'] ) ? explode ( ",", $inputValue ['value'] ) : array ();
				$option_string = "";
				foreach ( $input_value as $key => $value ) {
					$optino_array = explode ( "|", $value );
					$check_string = in_array ( $optino_array [0], $check_value ) ? "checked" : "";
					$option_string .= '<label style="float:left; margin-right:10px;"><input type="radio" ' . $check_string . ' name="' . $inputValue ['varname'] . '[]" value="' . $optino_array [0] . '" id="' . $inputValue ['varname'] . '_' . $key . '">' . $optino_array [1] . '</label>';
				}
				$input_string = $option_string;
				break;
			case "checkbox" :
				$input_value = ! empty ( $inputValue ['html_text'] ) ? explode ( ",", $inputValue ['html_text'] ) : array ();
				$check_value = ! empty ( $inputValue ['value'] ) ? explode ( ",", $inputValue ['value'] ) : array ();
				$option_string = "";
				foreach ( $input_value as $key => $value ) {
					$optino_array = explode ( "|", $value );
					$check_string = in_array ( $optino_array [0], $check_value ) ? "checked" : "";
					$option_string .= '<label style="float:left; margin-right:10px;"><input type="checkbox" name="' . $inputValue ['varname'] . '[]" ' . $check_string . ' value="' . $optino_array [0] . '" id="' . $inputValue ['varname'] . '_' . $key . '">' . $optino_array [1] . '</label>';
				}
				$input_string = $option_string;
				break;
			case "file" :
				$input_string .= widget ( 'Attachment/index', array ('field' => $inputValue ['varname'], 'type' => "image", "option" => array ("upload_maxnum" => 1 ), 'btnText' => "上传" . $inputValue ['info'], 'value' => empty ( $inputValue ['value'] ) ? "" : $inputValue ['value'] ) );
				break;
			case "multipart" :
				$maultipart_array = ! empty ( $inputValue ['value'] ) ? unserialize ( $inputValue ['value'] ) : array ();
				$option_string = '';
				foreach ( $maultipart_array as $key => $value ) {
					$option_string .= '
						<tr>
							<td>
							<input name="' . $inputValue ['varname'] . '_id[]"  type="hidden" value="' . $inputValue ['id'] . '_' . $key . '" />
		                	<span><input type="hidden" name="' . $inputValue ['varname'] . '_key_' . $inputValue ['id'] . '_' . $key . '" value="' . $value ['key'] . '" /></span>
		               		<span><b style="width:100px;float:left; font-weight:100;margin:10px 0 0 0">' . $value ['remark'] . ' :</b> <input type="text" name="' . $inputValue ['varname'] . '_value_' . $inputValue ['id'] . '_' . $key . '" value="' . $value ['value'] . '" /></span>
		                 	<span><input type="hidden" name="' . $inputValue ['varname'] . '_remark_' . $inputValue ['id'] . '_' . $key . '" value="' . $value ['remark'] . '" /></span>
							</td>
						</tr>
					';
				}
				$input_string = '<input name="' . $inputValue ['varname'] . '" type="hidden" value="multipart"><table class="table table-bordered table-hover definewidth" style="margin-bottom:0px;">' . $option_string . '</table>';
				break;
			default :
				$input_string = '<input name="' . $inputValue ['varname'] . '" type="text" class="form-control w30" value="' . $inputValue ['value'] . '" />';
				break;
		}
		
		return $input_string;
	}
	
	/**
	 * 
	 * 更改系统的配置文件
	 */
	public function updateConfig() {
		$result = $this->field ( "varname,input_type,value,info,lang,groupid" )->select ();
		if ($result) {
			$user_config = $sys_config = array ();
			foreach ( $result as $value ) {
				if ($value ['input_type'] == 'multipart') {
					$multipar_data = unserialize ( $value ['value'] );
					$row = array ();
					if ($multipar_data) {
						foreach ( $multipar_data as $k => $v ) {
							$row [$v ['key']] = $v ['value'];
						}
					}
					$result_data = $row;
				} else {
					$result_data = $value ['value'];
				}
				switch ($value ['groupid']) {
					case 1 :
						$user_config [$value ['lang']] [$value ['varname']] = $result_data;
						break; //站点配置
					case 2 :
						$user_config [$value ['lang']] [$value ['varname']] = $result_data;
						break; //用户配置
					case 3 :
						if ($value ['lang'] == $this->lang) { //系统配置
							$sys_config [$value ['varname']] = $result_data;
						}
						break;
				}
			}
			unset ( $result );
			foreach ( $user_config as $key => $value ) {
				F ( "Config_" . $key, $value, array ('path' => DATA_PATH ) );
			}
			F ( "SysConfig", $sys_config, array ('path' => DATA_PATH ) );
		}
		F ( "lang", NULL, array ('path' => DATA_PATH ) );
		$this->clearCache (); //清除系统缓存
	}
	/**
	 * +-----------------------------------------------------
	 * 获取系统配置的值
	 * +-----------------------------------------------------
	 * @param String	 $key		配置的KEY值
	 */
	public function getConfirByKey($key) {
		if (empty ( $key ))
			return false;
		$info = $this->where ( "varname='" . $key . "' and lang='" . $this->lang . "'" )->limit ( 1 )->find ();
		return $info ? $info ['value'] : "";
	}
	
	/**
	 * +-----------------------------------------------------
	 * 清除缓存
	 * +-----------------------------------------------------
	 * @param String	 $key		配置的KEY值
	 */
	public function clearCache(){
		$caches = array (
			"HomeCache" => array("name" => "网站前台模板缓存文件", "path" => RUNTIME_PATH . "cache/" ), 
			"HomeLog" => array ("name" => "网站前台日志缓存文件", "path" => RUNTIME_PATH . "log/" ),
			"HomeTemp" => array("name" => "网站临时数据缓存文件", "path" => RUNTIME_PATH . "temp/" )
		);
		foreach ( $caches as $key=>$value ) {
			delDirAndFile ( $value['path'] );
		}
	}

    /**
     * 获取时间类型
     * @param $type,0 表示今天，1 表示昨天，7 最近7天，30最近30天
     */
    public function getTimeSartAndEnd($type){
        if (empty($type))
            $type=0;
        $year=date('Y');
        $month=date('m');
        $day=date('d');
        $time['startTime']=mktime(0,0,0,$month,$day-$type,$year);
        if ($type==1){
            $time['endTime']=mktime(23,59,59,$month,$day-$type,$year);
        }else{
            $time['endTime']=mktime(23,59,59,$month,$day,$year);
        }

        return $time;
    }
	
}