<?php
namespace app\jzadmin\controller;
class Slide extends Common{
	
	/**
	 * +----------------------------------------------
	 * 	幻灯片广告管理
	 * +----------------------------------------------
	 */
	public function index() {
		$param = $this->request->param ();
		$typeid = isset( $param ['id']) ? intval ( $param ['id'] ) : "";
		$table=isset($param['table']) ? $param['table'] : "";
		if (empty($typeid) || empty($table)){
			$this->error("非法请求");
		}
		$where=array('typeid'=>$typeid);
		$slide_db=think_db($table);
		$field=" id,title,linkurl,picurl,ctime,width,height,sortslide ";
		$result = $slide_db->where($where)->field($field)->order("sortslide asc ,id asc")->select();
		return $this->fetch('',array('table'=>$table,'typeid'=>$typeid,'listslde'=>$result));
	}
	
	/**
	 * +----------------------------------------------
	 * 	添加幻灯片广告
	 * +----------------------------------------------
	 */
	public function add(){
		$param = $this->request->param ();
		$typeid = isset( $param ['typeid']) ? intval ( $param ['typeid'] ) : "";
		$table=isset($param['table']) ? $param['table'] : "";
		if (empty($typeid) || empty($table)){
			$this->error("非法请求");
		}
		if ($this->request->isPost()){
			if (empty($param['picurl'])){
				$this->error('请上传图片');
			}
			$slide_db=think_db($table);
			unset($param['table'],$param['id']);
			$param['ctime']=time();
			$param['product_title']=isset($param['product_title']) ? addSlashesFun($param['product_title']) : "";
			$param['product_description']=isset($param['product_description']) ? addSlashesFun($param['product_description']) : "";
			$param['title']=isset($param['title']) ? addSlashesFun($param['title']) : "";
			$result=$slide_db->insertGetId($param);
			if ($result){
					$this->success ( "添加成功!",url('index',array('id'=>$typeid,'table'=>$table)) );
			}else{
				$this->error($slide_db->getError());
			} 
		}else{
			return $this->fetch('',array('typeid'=>$typeid,'table'=>$table,'method'=>'add'));
		}
	}
	/**
	 * +----------------------------------------------
	 * 	修改幻灯片广告
	 * +----------------------------------------------
	 */
	public function edit(){
		$param = $this->request->param ();
		$id = isset( $param ['id']) ? intval ( $param ['id'] ) : "";
		$table=isset($param['table']) ? $param['table'] : "";
		if (empty($id) || empty($table)){
			$this->error("非法请求");
		}
		$slide_db=think_db($table);
		if ($this->request->isPost()){
			if (empty($param['picurl'])){
				$this->error('请上传图片');
			}
			unset($param['table']);
			$param['ctime']=time();
			$param['product_title']=isset($param['product_title']) ? addSlashesFun($param['product_title']) : "";
			$param['product_description']=isset($param['product_description']) ? addSlashesFun($param['product_description']) : "";
			$param['title']=isset($param['title']) ? addSlashesFun($param['title']) : "";
			$result=$slide_db->update($param);
			if ($result){
					$this->success ( "修改成功!",url('index',array('id'=>$param['typeid'],'table'=>$table)) );
			}else{
				$this->error($slide_db->getError());
			} 
		}else{
			$info=$slide_db->find($id);
			if (!$info){
				$this->error("图片不存在");
			}
			return $this->fetch('add',array('typeid'=>$info['typeid'],'table'=>$table,'method'=>'edit','info'=>$info));
		}
	}
	
	/**
	 * +----------------------------------------------
	 * 	删除幻灯片广告
	 * +----------------------------------------------
	 */
	public function del(){
		$param = $this->request->param ();
		$id = isset( $param ['id']) ? intval ( $param ['id'] ) : "";
		$table=isset($param['table']) ? $param['table'] : "";
		if (empty($id) || empty($table)){
			$this->error("非法请求");
		}
		$slide_db=think_db($table);
		$result = $slide_db->field('picurl')->find($id);
		if ($result) {
			if (! empty ( $result ['picurl'] )) {
				@unlink ( "." . $result ['picurl'] );
			}
			$slide_db->delete ($id);
			$this->success ( "删除成功!",url('index',array('id'=>$id,'table'=>$table)) );
		} else {
			$this->error ( "删除失败" );
		}
	}
}