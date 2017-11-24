<?php
namespace app\jzadmin\model;
use app\common\model\Common;
class Ad extends Common {
	/**
	 * +-----------------------------------------
	 * 验证广告模型字段
	 * +-----------------------------------------
	 * @param Array $postData	POST提交上来的数据
	 * 
	 * @return Array 返回过滤后的数组
	 */
	public function createData($postData) {
		$data = array ();
		$data ['adid'] = isset ( $postData ['adid'] ) ? addSlashesFun ( $postData ['adid'] ) : "";
		$data ['adname'] = isset ( $postData ['adname'] ) ? addSlashesFun ( $postData ['adname'] ) : "";
		$data ['typeid'] = isset ( $postData ['typeid'] ) ? intval ( $postData ['typeid'] ) : "";
		$data ['normbody'] = isset ( $postData ['normbody'] ) ? addSlashesFun ( $postData ['normbody'] ) : "";
		$data ['title'] = isset ( $postData ['title'] ) ? addSlashesFun ( $postData ['title'] ) : "";
		$data ['url'] = isset ( $postData ['url'] ) ? addSlashesFun ( $postData ['url'] ) : ""; //跳转地址
		$data ['lang'] = $this->lang;
		$data ['ctime'] = time ();
		
		switch ($data ['typeid']) {			
			//保存图片
			case 3 :
				$imgwidth= isset ( $postData ['imgwidth'] ) ? $postData ['imgwidth'] : "";
				$imgheight = isset ( $postData ['imgheight'] ) ? $postData ['imgheight'] : "";
				$istitle = isset ( $postData ['istitle'] ) ? intval ( $postData ['istitle'] ) : 0;
				$data ['imgurl'] = $postData['imgurl'];
				$data ['normbody'] = $imgwidth . "," . $imgheight . "," . $istitle;
				break;
			//保存flash		
			case 4 :
				$data ['url'] = isset ( $postData ['flashurl'] ) ? $postData ['flashurl'] : "";
				$flashwidth = isset ( $postData ['flashwidth'] ) ? $postData ['flashwidth'] : "";
				$flashheight = isset ( $postData ['flashheight'] ) ? $postData ['flashheight'] : "";
				$data ['normbody'] = $flashwidth . "," . $flashheight;
				break;
		}
		return $data;
	}

}