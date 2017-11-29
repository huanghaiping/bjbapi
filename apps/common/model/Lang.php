<?php
namespace app\common\model;
class Lang extends Common{
	
/**
	 * 
	 * 获取当前正在使用的语言
	 */
	public function getLang() {
		$lang_list =F("lang",'',array('path'=>DATA_PATH)) ;
		if (! $lang_list) {
			$lang_list = $this->updateCache ();
		}
		return $lang_list;
	}
	
	/**
	 * 
	 * 更新语言的缓存文件
	 *
	 */
	public function updateCache() {
		$langList = $this->where ( "status",1 )->order ( "listorder desc,id desc" )->select ();
		$row=array();
		if (!$langList->isEmpty()){
            $langList=$langList->toArray();
			foreach ( $langList as $key => $value ) {
				if (! empty ( $value ['domain'] )) {
					$domain = strpos ( $value ['domain'], "," ) ? explode ( ",", $value ['domain'] ) : array ($value ['domain'] );
					$value ['url'] = "http://" . $domain [0];
				} else {
					$value ['url'] = "/" . $value ['mark']; 
				}
				$row [$value ['mark']] = $value;
			}
			unset ( $langList );
			F("lang",$row,array('path'=>DATA_PATH));
		}
		return $row;
	}
	
	/**
	 * 更新语言包文件
	 * 
	 * @param int 	 	$lang_id	语言ID
	 * @param string 	$mark  		语言标识
	 * @param string  	$module_type 模块名称
	 * @return bool
	 */
	public function updateLangCache($lang_id, $mark,$module_type='index') {
		if (empty ( $lang_id ))
			return false;
		$lang_param_model = think_db ( "lang_param" );
		$copy_lang_list = $lang_param_model->where ( "lang_id='" . $lang_id . "' and module_type='".$module_type."' " )->select ();
		$file_put_content = array ();
		if ($copy_lang_list) {
			foreach ( $copy_lang_list as $value ) {
				$file_put_content [$value ['field']] = $value ['value'];
			}
		}
		//生成文件
		$dirname = APP_PATH . $module_type . "/lang";
		if (!is_dir($dirname)){
			mkdir($dirname);
		}
		$file_url_name=$dirname ."/". $mark . ".php";
		$config = "<?php\r\n \r\nreturn \$array = " . var_export ( $file_put_content, TRUE ) . ";\r\n?>";
		if (! file_exists_case ( $file_url_name )) {
			$myfile = fopen ( $file_url_name, "w" );
			fwrite ( $myfile, $config );
			fclose ( $myfile );
			chmod ( $file_url_name, 0777 );
		} else {
			file_put_contents ( $file_url_name, $config );
		}
		return true;
	
	}
	
	/**
	 * 更改国家图标的时候更新文件
	 *
	 * @param int	 	$old_id			语言ID
	 * @param string	$old_mark		语言旧标识
	 * @param string	$new_mark		语言新标识
	 * 
	 * @return bool
	 */
	public function changMark($old_id, $old_mark, $new_mark) {
		if (empty ( $old_id ))
			return false;
		$lang_param_model = \Think\Db::name ( "lang_param" );
		$result = $lang_param_model->where ( "lang_id='" . $old_id . "'" )->update ( array ('mark' => $new_mark ) );
		$model_list=array('index','api');
		foreach ($model_list as $value){
			$file_old_name = APP_PATH . $value . "/lang/" . $old_mark . ".php";
			$file_new_name = APP_PATH . $value . "/lang/" . $new_mark . ".php";
			$dd=rename ( $file_old_name, $file_new_name );
		}
		return true;
	}

}