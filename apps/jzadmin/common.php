<?php
/**
  +----------------------------------------------------------
 * 功能：计算文件大小
  +----------------------------------------------------------
 * @param int $bytes
  +----------------------------------------------------------
 * @return string 转换后的字符串
  +----------------------------------------------------------
 */
function byteFormat($bytes) {
	$sizetext = array (" B", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB" );
	return round ( $bytes / pow ( 1024, ($i = floor ( log ( $bytes, 1024 ) )) ), 2 ) . $sizetext [$i];
}

/**
  +----------------------------------------------------------
 * 生成随机字符串
  +----------------------------------------------------------
 * @param int       $length  要生成的随机字符串长度
 * @param string    $type    随机码类型：0，数字+大写字母；1，数字；2，小写字母；3，大写字母；4，特殊字符；-1，数字+大小写字母+特殊字符
  +----------------------------------------------------------
 * @return string
  +----------------------------------------------------------
 */
function randCode($length = 5, $type = 0) {
	$arr = array (1 => "0123456789", 2 => "abcdefghijklmnopqrstuvwxyz", 3 => "ABCDEFGHIJKLMNOPQRSTUVWXYZ", 4 => "~@#$%^&*(){}[]|" );
	if ($type == 0) {
		array_pop ( $arr );
		$string = implode ( "", $arr );
	} else if ($type == "-1") {
		$string = implode ( "", $arr );
	} else {
		$string = $arr [$type];
	}
	$count = strlen ( $string ) - 1;
	$code = "";
	for($i = 0; $i < $length; $i ++) {
		$str [$i] = $string [rand ( 0, $count )];
		$code .= $str [$i];
	}
	return $code;
}

/**
 * 
 * 在文件扩展名的前加* 号
 * +----------------------------------------------------------
 * @param string $ext_string
 */
function getImageExt($ext_string) {
	if (empty ( $ext_string ))
		return false;
	$ext_array = explode ( ",", $ext_string );
	foreach ( $ext_array as $key => $value ) {
		$ext_array [$key] = "*." . $value;
	}
	return implode ( ";", $ext_array );
}

/**
 * 区分大小写的文件存在判断
 * @param string $filename 文件地址
 * @return boolean
 */
function file_exists_case($filename) {
	if (is_file ( $filename )) {
		if (IS_WIN) {
			if (basename ( realpath ( $filename ) ) != basename ( $filename ))
				return false;
		}
		return true;
	}
	return false;
}
/**
 * +---------------------------------------------
 * 删除资讯内容里图片和附件
 * +---------------------------------------------
 * @param 	string	$content 需要删除的内容
 * @return Null
 */
function delContentImg($content) {
	preg_match_all ( '/<img.*?src=\s*?"?([^"\s]+)(?!\/>)"?\s*?/is', $content, $img_array );
	if ($img_array) {
		foreach ( $img_array [1] as $cc => $image ) {
			if (preg_match ( '/^\/Uploads/i', $image )) {
				@unlink ( "./" . $image ); //删除图片
			}
		}
	}
	preg_match_all ( '/<a.*?href=\s*?"?([^"\s]+)(?!\/>)"?\s*?/is', $content, $link_array );
	if ($link_array) {
		foreach ( $link_array [1] as $cc => $link ) {
			if (preg_match ( '/^\/Uploads/i', $link )) {
				@unlink ( "./" . $link ); //删除附件
			}
		}
	}
}

/**
 * +---------------------------------------------
 * 删除多图的图片
 * +---------------------------------------------
 * @param string $thumbs多图的图片
 */
function del_slide($thumbs, $separator = ":::") {
	if (empty ( $thumbs ))
		return false;
	$thumbs = slide ( $thumbs, $separator = ":::" );
	if ($thumbs){
		foreach ( $thumbs as $value ) {
			@unlink ( "." . $value ['thumb'] );
		}
	}
	return true;
}

/**
  +-----------------------------------------------------------------------------------------
 * 删除目录及目录下所有文件或删除指定文件
  +-----------------------------------------------------------------------------------------
 * @param str $path   待删除目录路径
 * @param int $delDir 是否删除目录，1或true删除目录，0或false则只删除文件保留目录（包含子目录）
  +-----------------------------------------------------------------------------------------
 * @return bool 返回删除状态
  +-----------------------------------------------------------------------------------------
 */
function delDirAndFile($path, $delDir = FALSE) {
	$handle = opendir ( $path );
	if ($handle) {
		while ( false !== ($item = readdir ( $handle )) ) {
			if ($item != "." && $item != "..")
				is_dir ( "$path/$item" ) ? delDirAndFile ( "$path/$item", $delDir ) : unlink ( "$path/$item" );
		}
		closedir ( $handle );
		if ($delDir)
			return rmdir ( $path );
	} else {
		if (file_exists ( $path )) {
			return unlink ( $path );
		} else {
			return FALSE;
		}
	}
}
