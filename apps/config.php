<?php
$default_config = require APP_PATH . "common/conf/config" . EXT;
$sys_url = DATA_PATH . md5('SysConfig').EXT;
if (file_exists ( $sys_url )) {
    $sys_config = require DATA_PATH . md5('SysConfig').EXT;
    return array_merge ( $default_config, $sys_config );
} else {
    return array_merge ( $default_config );
}