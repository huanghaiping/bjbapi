<?php
namespace app\jzadmin\widget;
use think\Controller;
class Attachment extends Controller{
	
	/**
	 * +----------------------------------------------------------
	 * 生成上传的控件
	 * +----------------------------------------------------------
	 * 
	 * @param string $type		控件的类型 (image,images,file, files)
	 * @param string $field 	上传字段的名称
	 * @param string $btnText 	按钮的名称
	 * @param string $value		上传后赋值的文件纸
	 * @param Array	 $option	上传的文件参数array("upload_maxnum"=>1,'ext'=>'','size'=>0)  upload_maxnum上传的文件数,ext知道后缀 ，size 文件大小
	 */
	public function index($field,$type,$btnText='上传',$option=array(),$value=''){
		
		$param=array('field'=>$field,'type'=>$type);
		if (!empty($option)){
			$param=array_merge($param,$option);
		}
		$url=url('Attachment/index',$param);
		return $this->fetch("attachment/file",array('type'=>$type,"field"=>$field,"btnText"=>$btnText,"url"=>$url,'data_value'=>$value));
	}
}