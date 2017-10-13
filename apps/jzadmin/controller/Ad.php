<?php
namespace app\jzadmin\controller;
class Ad extends Common {
	
	/**
	 * +-------------------------------------------------
	 * 广告管理模块
	 * +-------------------------------------------------
	 */
	public function index() {
		$title = isset ( $_REQUEST ['title'] ) ? addslashes ( $_REQUEST ['title'] ) : "";
		$where = $param = array ();
		$sql = "";
		$where [] = " lang='" . $this->lang . "'";
		$param ['l'] = $this->lang;
		if (! empty ( $title )) {
			$where [] = " adname like '%" . $title . "%'";
			$param ['title'] = $title;
			$this->assign ( "keyword", $title );
		}
		if (count ( $where ) > 0) {
			$sql = join ( " AND ", $where );
		}
		$field = "id,adname,adid,typeid,normbody,url,count,imgurl,ctime,title ";
		$ad_model = model ( "Ad" );
		$list = $ad_model->where ( $sql )->order ( "id desc" )->paginate ( 20, false, array ("query" => array ('title' => $title ) ) );
		$this->assign ( "page", $list->render () );
		$this->assign ( "list", $list );
		return $this->fetch ();
	}
	/**
	 * +-------------------------------------------------
	 * 添加广告
	 * +-------------------------------------------------
	 */
	public function add() {
		if ($this->request->isPost ()) {
			$param = $this->request->param (); //获取所有的参数
			$ad_model = model ( "Ad" );
			$result = $ad_model->validate ( array ('adid' => 'require', 'adname' => "require" ), array ("adid.require" => '请输入广告位标识', 'adname.require' => '请输入广告位位置' ) )->allowField ( true )->save ( $ad_model->createData ( $param ) );
			if ($result) {
				$this->success ( "添加成功!", url ( 'index', array ('lang' => $this->lang ) ) );
			} else {
				$this->error ( $ad_model->getError () );
			}
		} else {
			$this->assign ( "method", "add" );
			return $this->fetch ();
		}
	}
	
	/**
	 * +----------------------------------------------
	 * 修改广告
	 * +----------------------------------------------
	 */
	public function edit() {
		$param = $this->request->param ();
		$id = isset ( $param ['id'] ) ? intval ( $param ['id'] ) : "";
		if (empty ( $id )) {
			$this->error ( "非法请求" );
		}
		$ad_model = model ( "Ad" );
		if ($this->request->isPost ()) {
			$result = $ad_model->allowField ( true )->where("id=".$id)->update ( $ad_model->createData ( $param ) );
			if ($result) {
				$this->success ( "修改成功!", url ( 'index', array ('lang' => $this->lang ) ) );
			} else {
				$this->error ( "修改失败" );
			}
		} else {
			$result =$ad_model->find ( $id );
			$typeid = $result ['typeid'];
			if ($typeid == "3"&&!empty($result ['normbody'])) {
				$normbody = explode ( ",", $result ['normbody'] );
				$result ["imgwidth"] = $normbody [0];
				$result ["imgheight"] = $normbody [1];
				$result ["istitle"] = $normbody [2];
			}
			if ($typeid == "4"&&!empty($result ['normbody'])) {
				$normbody = explode ( ",", $result ['normbody'] );
				$result ["flashwidth"] = $normbody [0];
				$result ["flashheight"] = $normbody [1];
			}
			$result ['normbody'] = isset ( $result ['normbody'] ) ? stripslashes ( $result ['normbody'] ) : "";
			$this->assign ( "info", $result );
			$this->assign ( "method", "edit" );
			return $this->fetch ( "add" );
		}
	}
	
	/**
	 * +----------------------------------------------
	 * 	删除广告
	 * +----------------------------------------------
	 */
	public function del() {
		$param = $this->request->param ();
		$id = isset($param ['id']) ? intval ( $param ['id'] ) : "";
		if (empty($id)){
			$this->error("非法请求");
		}
		$ad_model = model ( "Ad" );
		$result = $ad_model->find ( $id );
		$typeid = $result ['typeid'];
		if (! empty ( $result ['imgurl'] )) {
			@unlink ( "." . $result ['imgurl'] );
		}
		$result = $ad_model->where("id='".$id."'")->delete ( );
		if ($result) {
			if ($typeid == 5) { //删除幻灯片
				$slide_db=\think\Db::name("ad_slide");
				$delInfo = $slide_db->where("typeid=".$typeid)->find();
				if ($delInfo){
					foreach ( $delInfo as $value ) {
						if ($slide_db->delete (  $value ['id'] )) {
							@unlink ( "." . $value ['picurl'] );
						}
					}
				}
			}
			$this->success ( "删除成功!", url('index') );
		} else {
			$this->error ( "删除失败" );
		}
	}

}