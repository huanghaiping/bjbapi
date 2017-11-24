<?php
namespace app\jzadmin\controller\user;
use app\jzadmin\controller\Common;
class Template extends Common {
	
	/**
	 * +------------------------------------
	 * 消息模板列表
	 * +------------------------------------
	 */
	public function index() {
		$userTemplateModel = model ( "UserTemplate" );
		$result = $userTemplateModel->where ( array ('lang' => $this->lang ) )->order ( "id asc" )->select ();
		$row = array ();
		if ($result) {
			$result = $result->toArray ();
			foreach ( $result as $value ) {
				$row ["type" . $value ['type']] [] = $value;
			}
			unset ( $result );
		}
		return $this->fetch ( '', array ('userlist' => $row ) );
	}
	/**
	 * +------------------------------------
	 * 添加消息模板
	 * +------------------------------------
	 */
	public function add() {
		if ($this->request->isPost()) {
			$post=$this->request->post();
			$post['lang']=$this->lang;
			$post['ctime']=time();
            $post['content_key']=isset( $post['content_key']) ? addSlashesFun( $post['content_key']) : "";
			$userTemplateModel = model ( "UserTemplate" );
		 	$userTemplateModel->allowField(true)->save ( $post );
			F ( "user_template_" . $this->lang, null ); //清除缓存
			$this->success ( "添加成功", url ( 'index' ) );
		} else {
			return $this->fetch('',array('method'=>"add","info"=>array()));
		}
	}
	
	/**
	 * +------------------------------------
	 * 修改消息模板
	 * +------------------------------------
	 */
	public function edit() {
		$param=$this->request->param();
		$id = isset ( $param ['id'] ) ? intval ( $param ['id'] ) : "";
		$userTemplateModel = model ( "UserTemplate" );
		if ($this->request->isPost()) {
			$post=$this->request->post();
			$post['lang']=$this->lang;
			$post['ctime']=time();
            $post['content_key']=isset( $post['content_key']) ? addSlashesFun( $post['content_key']) : "";
		 	$userTemplateModel->allowField(true)->where(array('id'=>$id))->update ( $post );
			F ( "user_template_" . $this->lang, null ); //清除缓存
			$this->success ( "修改成功", url ( 'index' ) );
		} else {
			$info=$userTemplateModel->where(array('id'=>$id))->find();
			return $this->fetch('add',array('method'=>"edit","info"=>$info));
		}
	}
	/**
	 * +------------------------------------
	 * 删除消息模板
	 * +------------------------------------
	 */
	public function del() {
		$param=$this->request->param();
		$id = isset ( $param ['id'] ) ? intval ( $param ['id'] ) : "";
		if (empty ( $id )) {
			$this->error ( "非法参数" );
		}
		$userTemplateModel = model ( "UserTemplate" );
        $info=$userTemplateModel->where(array('id'=>$id))->find();
        if (!$info){
            $this->error ( "非法参数" );
        }
        $info=$info->toArray();
		$resutl = $userTemplateModel->where ( "id='{$id}'" )->delete ();
		if ($resutl) {
            if (!empty($info['content_key'])){
                delContentImg(stripslashes($info['content_key']));
            }
			F ( "user_template_" . $this->lang, null ); //清除缓存
			$this->success ( "删除成功", url ( 'index' ) );
		} else {
			$this->error ( "删除失败",url ( 'index' ) );
		}
	}
}