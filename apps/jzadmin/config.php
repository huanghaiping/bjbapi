<?php
return array(
	'template'  =>  [
	    'layout_on'     =>  true,
	    'layout_name'   =>  'layout',
	],
	//数据备份还原配置
	'backup_config'=>[
		'path'=>INCLUDE_PATH.'backup', //备份的路径
		'part_size'=>2097152,//2M的数据大小
		'compress'=>1, //是否压缩数据
		'compress_level'=>1
	],
	'sn_prefix'=>'xp'
	
);