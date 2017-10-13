<?php
namespace app\jzadmin\controller;
class Attachment extends Common {
	
	/**
	 * +----------------------------------------------------------------
	 * 加载上传的控制页面
	 * +----------------------------------------------------------------
	 */
	public function index() {
		
		$param = $this->request->param ();
		if (empty ( $param ['field'] ) || empty ( $param ['type'] )) {
			$this->error ( "参数错误" );
		}
		if ($param ['type'] == "image" || $param ['type'] == "images") {
			$upload_config = config ( "picture_upload" );
		} elseif ($param ['type'] == "file" || $param ['type'] == "files") { 
			$upload_config = config ( "file_upload" );
		} else {
			$this->error ( "参数错误" );
		}
		$upload_maxnum = isset ( $param ['upload_maxnum'] ) && intval ( $param ['upload_maxnum'] ) > 0 ? $param ['upload_maxnum'] : 1;
		$ext=isset ( $param ['ext'] ) && ! empty ( $param ['ext'] ) ?  $param ['ext'] : $upload_config['ext'];
		$path=isset ( $param ['path'] ) && ! empty ( $param ['path'] ) ?  base64_decode($param ['path']) : $upload_config['rootPath'];
		$size=isset ( $param ['size'] ) && intval ( $param ['size'] )>0 ?  $param ['size'] : $upload_config['size'];
		$postdata = array ('field' => $param ['field'], 'type' => $param ['type'], 'path' => base64_encode ( $path ), 'upload_maxsize' => $size, 'upload_allowext' => $ext, 'upload_maxnum' => $upload_maxnum );
		$this->assign ( "info", $postdata );
		return $this->fetch ();
	}
	
	/**
	 * +-------------------------------------
	 * 上传文件或者图片
	 * +-------------------------------------
	 */
	public function upload() {
		header ( 'Content-type: application/json' );

		$verifyToken = md5 ( 'unique_salt' . $_POST ['timestamp'] );

		if (! empty ( $_FILES ) && $_POST ['token'] == $verifyToken) {
			$field = isset ( $_POST ['field'] ) ? addslashes ( $_POST ['field'] ) : "Filedata";
			$upload_maxnum = isset ( $_POST ['upload_maxnum'] ) ? $_POST ['upload_maxnum'] : 1;
			$default_ext='jpg, gif, png, jpeg';
			$upload_allowext = isset ( $_POST ['upload_allowext'] ) ? $_POST ['upload_allowext'] : $default_ext;
			$watermark = isset ( $_POST ['watermark'] ) ? $_POST ['watermark'] : 0; //是否添加水印
			$patch = isset ( $_POST ['path'] ) ? base64_decode ( $_POST ['path'] ) : "";
			$upload_maxsize = isset ( $_POST ['upload_maxsize'] ) && $_POST ['upload_maxsize'] > 0 ? intval ( $_POST ['upload_maxsize'] ) : 2097152; //默认2M 
			$config = array ();
			$config ['size'] = $upload_maxsize;
			$config ['ext'] = explode ( ",", $upload_allowext );
			$data = array ();
			$thumb = $_FILES [$field];
			if (! empty ( $thumb ['size'] )) {
				if (isset($_POST [$field . "_txt"])&&!empty($_POST [$field . "_txt"])){
					@unlink ( "." . $_POST [$field . "_txt"] ); //删除原先缩略图
				}
				$file = $this->request->file ( $field );
				$info = $file->validate ( $config )->move ( $patch );
				if ($info) {
					$litpic = $patch . DS . $info->getSaveName ();
					chmod ( $litpic, 0777 );
					$jumpurl = str_replace ( array (ROOT_PATH, 'public', "\\" ), array ('', '', '/' ), $litpic );
					$data ['url'] = $data [$field] = $jumpurl;
					$data ['status'] = 1;
					$data ['info'] = "上传成功";
					//创建水印
					if ($watermark==1){
						$ext=$info->getExtension();
						if (in_array($ext, explode(",", $default_ext))){ //判断是否是图片文件
							$attachmentModel=model("Attachment");
							$attachmentModel->addWatermark($litpic);
						}
					}
				} else {
					$data ['status'] = 0;
					$data ['info'] = "上传失败:" . $info->getError ();
				}
			} else {
				$data ['url'] = $data [$field] = $_POST [$field . "_txt"];
				$data ['status'] = 1;
				$data ['info'] = "上传成功";
			}
			$data ['field'] = isset ( $_POST ['field'] ) ? addslashes ( $_POST ['field'] ) : "";
			$data ['type'] = isset ( $_POST ['type'] ) ? addslashes ( $_POST ['type'] ) : "";
			return json ( $data );
		} else {
			return json ( array ('status' => 0, 'url' => '', 'info' => '非法请求' ) );
		}
	}
	
	/**
	 * +-------------------------------------------
	 * 删除图片,不删除数据信息
	 * +-------------------------------------------
	 */
	public function del() {
		if (empty ( $_POST )) {
			return json ( array ('status' => 0, 'url' => '', 'info' => '非法请求' ) );
		}
		@unlink ( "." . $_POST ['url'] );
		return json ( array ('status' => 1, 'url' => '', 'info' => '删除成功' ) );
	}
	/**
	  +-----------------------------------------------------------------------------------------
	 * 打开目录及目录下所有文件
	  +-----------------------------------------------------------------------------------------
	 * @param str $path   待打开目录路径
	  +-----------------------------------------------------------------------------------------
	 * @return bool 返回删除状态
	  +-----------------------------------------------------------------------------------------
	 */
	protected function list_file($path) {
		$file_array=array();
		$handle = opendir ( $path );
		if ($handle) {
			while ( false !== ($item = readdir ( $handle )) ) {
				if ($item != "." && $item != "..") {
					if (is_dir ( "$path/$item" )) {
						$file_array_child = $this->list_file ( "$path/$item" );
						$file_array=array_merge($file_array,$file_array_child);
					} else {
						$litpic=str_replace ( "./", "/", $path . "/" . $item );
						$jumpurl = str_replace ( array (ROOT_PATH, 'public', "\\" ), array ('', '', '/' ), $litpic );
						$file_array [] = array ('file_path' => $jumpurl, 'file_name' => $item );
					}
				}
			}
			closedir ( $handle );
			return $file_array;
		} else {
			return false;
		}
	}
	
	/**
	 * +------------------------------------------
	 * 浏览图库或者文件的地址
	 */
	public function lists() {
		$param=$this->request->param();
		$field = isset ( $param ['field'] ) ? addslashes ( $param ['field'] ) : "";
		$path = isset ( $param ['path'] )&&!empty( $param ['path']) ? base64_decode($param ['path']) : "";
		$type = isset ( $param ['type'] ) ? $param ['type'] : "";
		$myid = isset ( $param ['myid'] ) ? $param ['myid'] : "";
		$iframe = isset ( $param ['iframe'] ) ? $param ['iframe'] : "";
		$dir_path = $path;
		$file_array = array ();
		if (is_dir ( $dir_path )) {
			$file_array = $this->list_file ( $dir_path );
		}
		
		if ($file_array) {
			$data = array ('field' => $field, 'type' => $type, 'status' => 1 );
			foreach ( $file_array as $key => $value ) {
				$data ['url'] = $value ['file_path'];
				//$value['file_data']='{"field":"'.$data['field'].'","type":"'.$data['type'].'","status":'.$data['status'].',"url":"'.$data['url'].'"}';
				$value ['file_data'] = json_encode ( $data );
				$file_array [$key] = $value;
			}
		}
		$this->assign ( "iframe", $iframe );
		$this->assign ( "field", $field );
		$this->assign ( "type", $type );
		$this->assign ( "myid", $myid );
		$this->assign ( "file_list", $file_array );
		$this->assign ( "dir_path", $dir_path );
		return $this->fetch ();
	}

}