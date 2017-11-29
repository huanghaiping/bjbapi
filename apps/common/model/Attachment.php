<?php
/**
 * 图片操作处理类
 */
namespace app\common\model;
use think\Image;
class Attachment extends Common {
	
	/**
	 * +------------------------------------------------------------------
	 * 添加图片或者文字水印
	 * +------------------------------------------------------------------
	 * @param strnig  $image_path
	 */
	public function addWatermark($image_path) {
		if (empty ( $image_path ))
			return false;
		if (! file_exists ( $image_path )) {
			return false;
		}
		$watermark_config = F ( "watermark_" . $this->lang );
		if ($watermark_config && $watermark_config ['status'] == 1) {
			$image = Image::open($image_path);
			//判断图片是否满足添加水印的条件
			$watermark_minwidth=isset($watermark_config['watermark_minwidth']) ? intval($watermark_config['watermark_minwidth']) : 0; //添加水印的图片宽条件
			$watermark_minheight=isset($watermark_config['watermark_minheight']) ? intval($watermark_config['watermark_minheight']) : 0;
			if ($watermark_minwidth>0&&$watermark_minheight>0){
				if ($image->width()<=$watermark_minwidth || $image->height()<=$watermark_minheight){
					return false;
				}
			}
			$ext=$image->type();
			$watermark_quality=isset($watermark_config['watermark_quality']) ? intval($watermark_config['watermark_quality']) : 100;// 图片质量
			switch ($watermark_config['watermark_pos']){
				 	case 1 : $postition=Image::WATER_NORTHWEST; break;//常量，标识左上角水印
				 	case 2 : $postition=Image::WATER_NORTH; break;//常量，标识上居中水印
				 	case 3 : $postition=Image::WATER_NORTHEAST; break;//常量，标识右上角水印
				 	case 4 : $postition=Image::WATER_WEST; break;//常量，标识左居中水印
				 	case 5 : $postition=Image::WATER_CENTER; break;//常量，标识居中水印
				 	case 6 : $postition=Image::WATER_EAST; break;//常量，标识右居中水印
				 	case 7 : $postition=Image::WATER_SOUTHWEST; break;//常量，标识左下角水印
				 	case 8 : $postition=Image::WATER_SOUTH; break;//常量，标识下居中水印
				 	case 9 : $postition=Image::WATER_SOUTHEAST; break;//常量，标识右下角水印
			} 
			//添加图片水印,判断水印图片是否存在
			$watermark_img=isset($watermark_config ['watermark_img'])&&!empty($watermark_config ['watermark_img']) ? ROOT_PATH.'public'.$watermark_config ['watermark_img'] : "";
			if (! empty ($watermark_img)&&file_exists($watermark_img)) {
				 $watermark_pct=isset($watermark_config['watermark_pct']) ? intval($watermark_config['watermark_pct']) : 100; //水印透明度	 
				 $image->water($watermark_img,$postition,$watermark_pct)->save($image_path,$ext,$watermark_quality);
			}
			//添加文字水印
			if (isset($watermark_config ['watemard_text'])&&! empty ( $watermark_config ['watemard_text'] )) {
				//获取字体
				$default_ttf=VENDOR_PATH.'topthink/think-image/tests/images/test.ttf';
				$watemard_text_face=isset($watermark_config['watemard_text_face'])&&!empty($watermark_config['watemard_text_face']) ? ROOT_PATH.'public'.$watermark_config ['watermark_img']:$default_ttf;
				//文字大小
				$watemard_text_size=isset($watermark_config['watemard_text_size'])&&!empty($watermark_config['watemard_text_size']) ? intval($watermark_config['watemard_text_size']):20;
				//文字颜色
				$watemard_text_color=isset($watermark_config['watemard_text_color'])&&!empty($watermark_config['watemard_text_color']) ? ($watermark_config['watemard_text_color']):'#ffffff';
				$watermark_pospadding=isset($watermark_config['watermark_pospadding'])&&!empty($watermark_config['watermark_pospadding']) ? ($watermark_config['watermark_pospadding']):0;
				$image->text($watermark_config ['watemard_text'],$watemard_text_face,$watemard_text_size,$watemard_text_color,$postition,$watermark_pospadding)->save($image_path,$ext,$watermark_quality);
			}
		} else {
			return false;
		}
	}
	
	/**
	 * +------------------------------------------------------------------
	 * 生成缩略图
	 * +------------------------------------------------------------------
	 * @param strnig  $image_path 原图的图片路径
	 * @param Array	  $size 生成缩图图大小,array('100*100','200*200');
	 */
	public function thumb($image_path,$save_path,$size=array('100*100')){
		if (empty($image_path)||!file_exists($image_path))
			return false;
		$image = Image::open($image_path);
		$return_data=array();
		foreach ($size as $value){
			$image_size=explode("*", $value);
			$width=isset($image_size[0]) ? intval($image_size[0]) : 100;
			$height=isset($image_size[1]) ? intval($image_size[1]) : 100;
			$image->thumb($width,$height,Image::THUMB_FILLED)->save($save_path);
			$jumpurl = str_replace ( array (ROOT_PATH, 'public', "\\" ), array ('', '', '/' ), $save_path );
			$return_data[$value]=$jumpurl;
		}
		return $return_data;
	}
}