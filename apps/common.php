<?php
// 应用公共文件
/**
 * 取一个二维数组中的每个数组的固定的键知道的值来形成一个新的一维数组
 * @param $pArray 一个二维数组
 * @param $pKey 数组的键的名称
 *
 * @return 返回新的一维数组
 */

function getSubByKey($pArray, $pKey = "", $pCondition = "")
{
    $result = array();
    if (is_array($pArray)) {
        foreach ($pArray as $temp_array) {
            if (("" != $pCondition && $temp_array [$pCondition [0]] == $pCondition [1]) || "" == $pCondition) {
                $result [] = ("" == $pKey) ? $temp_array : isset ($temp_array [$pKey]) ? $temp_array [$pKey] : "";
            }
        }
        return $result;
    } else {
        return false;
    }
}

/**
 * +----------------------------------------------------------
 * 如果 magic_quotes_gpc 为关闭状态，这个函数可以转义字符串
 * +----------------------------------------------------------
 * @access public
 * +----------------------------------------------------------
 * @param string $string 要处理的字符串
 * +----------------------------------------------------------
 * @return string
+----------------------------------------------------------
 */
function addSlashesFun($string)
{
    if (!get_magic_quotes_gpc()) {
        $string = addslashes($string);
    }
    return $string;
}


/**
 * 系统非常规MD5加密方法
 * @param  string $str 要加密的字符串
 * @return string
 */
function password_md5($str, $key = 'UGEE.COM.CN')
{
    return '' === $str ? '' : md5(sha1($str) . $key);
}

/**
 * 不区分大小写的in_array实现
 *
 * @param string $value
 * @param Array $array
 */
function in_array_case($value, $array)
{
    return in_array(strtolower($value), array_map('strtolower', $array));
}

/**
 * 检查email的格式是否正确
 * @param string $email 需要判断的邮箱
 * @return booltrue
 */
function checkEmailFormat($email)
{
    $pregEmail = "/([a-z0-9]*[-_\.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[\.][a-z]{2,3}([\.][a-z]{2})?/i";
    if (preg_match($pregEmail, $email)) {
        return true;
    } else {
        return false;
    }
}

/**
 * 验证手机号码
 * Enter description here ...
 * @param unknown_type $tel
 */
function valdeTel($tel)
{
    $pattern = "/^13[0-9]{9}$|17[0-9]{9}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$|14[0-9]{1}[0-9]{8}$/i";
    if (!preg_match($pattern, $tel)) {
        return false;
    }
    return true;
}

/**
 * 验证密码格式是否正确
 * Enter description here ...
 */
function letterOrNumber($password)
{
    if (!preg_match("/^[a-zA-Z0-9_]+$/", $password) || mb_strlen($password)!=32) {
        return false;
    }
    return true;
}

/**
 * 获取用户的类型
 */
function getUserType($typeid = 0)
{
    $type_string = '';
    switch ($typeid) {
        case 1 :
            $type_string = "web";
            break;
        case 2 :
            $type_string = "QQ";
            break;
        case 3 :
            $type_string = "Sina";
            break;
        case 4 :
            $type_string = "微信";
            break;
        default :
            $type_string = "web";
            break;
    }
    return $type_string;
}

/**
 * @param $typeId
 */
function getClientType($typeId=0){
    $typeString='';
    switch ($typeId){
        case 1 : $typeString="IOS"; break;
        case 2 : $typeString="Android"; break;
    }
    return $typeString;
}

/**
 * +-----------------------------------------
 * 返回表的DB对象
 * +-----------------------------------------
 *
 * @param string	$table 表名不带前缀
 */
function think_db($table) {
    if (empty ( $table ))
        return false;
    return \think\
    Db::name ( $table );
}

/**
 *
 * 递归组装树状格式
 *
 * @param Array $menu_data 菜单数据
 *
 */
function list_tree($menuData, $pid = 0, $name = 'child', $parent_field = 'pid') {
    $row = array ();
    foreach ( $menuData as $key => $value ) {
        if (! isset ( $row [$value ['id']] ) && $value [$parent_field] == $pid) {
            $row [$value ['id']] = $value;
        } else {
            if (isset ( $row [$value [$parent_field]] )) {
                $child = list_tree ( $menuData, $value ['id'] ); //判断是否有子分类
                if (! empty ( $child )) {
                    $value [$name] = $child;
                }
                $row [$value [$parent_field]] [$name] [$value ['id']] = $value;
            }
        }
    }
    return $row;
}

/**
 * [getChilds 传递一个父级的id查找子级分类]
 * @param  [array]  $array [数组]
 * @param  integer $pid    [父级id]
 * @return [array]         [子类数组]
 */
function getChilds($array, $pid = 0, $parent_field = 'pid') {
    $arr = array ();
    foreach ( $array as $key => $value ) {
        if ($value [$parent_field] == $pid) {
            $arr [] = $value;
            $arr = array_merge ( $arr, getChilds ( $array, $value ['id'], $parent_field ) );
        }
    }
    return $arr;
}

/**
 * [getParents 传递一个子级id获取父级分类]
 * @param  [type]  $array [description]
 * @param  integer $id    [description]
 * @return [type]         [description]
 */

function getParents($array, $id = 1, $parent_field = 'pid') {
    $arr = array ();
    foreach ( $array as $key => $value ) {
        if ($value ['id'] == $id) {
            $arr [] = $value;
            $arr = array_merge ( $arr, getParents ( $array, $value [$parent_field] ) );
        }
    }
    return $arr;
}

/**
 * 多层级数组转为一级数组
 * @param  [type]  $array [description]
 */
function array_multi2single($array, $name = 'child') {
    $result_array = array ();
    foreach ( $array as $value ) {
        $childe = isset ( $value [$name] ) ? $value [$name] : "";
        unset ( $value [$name] );
        $result_array [] = $value;
        if (! empty ( $childe )) {
            $result_array = array_merge ( $result_array, array_multi2single ( $childe ) );
        }
    }
    return $result_array;
}

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
    if (empty($bytes))
        return 0;
    $sizeText = array (" B", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB" );
    return round ( $bytes / pow ( 1024, ($i = floor ( log ( $bytes, 1024 ) )) ), 2 ) . $sizeText [$i];
}

/**
 * 字节转MB
 * @param $bytes
 */
function byteTomb($bytes){
    if (empty($bytes))
        return 0;
    $ratio=config('Space_exchange_ratio');
    $bytes /= pow(intval($ratio), 2); // 0:bytes，1:kb,2:mb,3:gb
    return number_format($bytes, 3); //3 保留3位小数
}

/**
 * mb 转字节
 * @param $mb
 */
function mbToByte($mb){
    $ratio=config('Space_exchange_ratio');
    $ratio=intval($ratio);
    return floatval($mb*$ratio*$ratio);
}

/**
 * 格式化时间格式
 * Enter description here ...
 */
function getFormatTime($time = '', $format = '',$lang='') {
    $time=	empty ( $time ) ?  time() : $time;
    $time=! is_numeric ( $time ) ? strtotime ( $time ) : $time;
    $lang= empty ( $lang ) ?  LANG_SET : $lang;
    $time_string = '';
    switch ($lang) {
        case "cn" :
            $time_string = date ( empty ( $format ) ? 'Y-m-d H:i:s' : $format, $time );
            break;
        case "en" :
            $time_string = date ( empty ( $format ) ? 'M d,Y A H:i' : $format, $time );
            break;
    }
    return $time_string;
}