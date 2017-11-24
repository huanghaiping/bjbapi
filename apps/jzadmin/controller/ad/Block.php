<?php
namespace app\jzadmin\controller\ad;
use app\jzadmin\controller\Common;
class Block extends Common{

	/**
	 * +-----------------------------------------------------------
	 * 碎片广告
	 * +-----------------------------------------------------------
	 */
	public function index(){
		$param=$this->request->param();
		$keyword=isset($param['keyword']) ? addSlashesFun($param['keyword']) : "";
		$sql="";
		$where=array();
		$where[]=" lang='".$this->lang."'";
		if (!empty($keyword)){
			$where[]="position like '%".$keyword."%'";
		}
		if (count($where)>0){
			$sql=join(" AND ", $where);
		}
		$block_db=think_db("ad_block");
		$list=$block_db->where($sql)->order("id asc")->paginate(20);
		return $this->fetch('',array('keyword'=>$keyword,'list'=>$list,'page'=>$list->render()));
	}
	/**
	 * +-----------------------------------------------------------
	 * 添加广告
	 * +-----------------------------------------------------------
	 */
	public function add(){
		if ($this->request->isPost()){
			$param=$this->request->param();
			if (empty($param['position'])){
				$this->error('广告位置不能为空');
			}
			if (empty($param['identifier'])){
				$this->error("标识不能为空");
			}
			$block_db=think_db("ad_block");
			$param['ctime']=time();
			$param['lang']=$this->lang;
			$param['content']=isset($param['content']) ? addSlashesFun($param['content']) : "";
			unset($param['id']);
			$count=$block_db->where("identifier='".$param['identifier']."' and lang='".$this->lang."' ")->count();
			if ($count>0){
				$this->error("标识已经存在");
			}
			$result=$block_db->insertGetId($param);
			if ($result){
				$this->success ( "添加成功!",url('index'));
			}else{
				$this->error($this->model_name->getError());
			} 
			
		}else{
			return $this->fetch('',array('method'=>"add"));
		}
	}
	
	/**
	 * +-----------------------------------------------------------
	 * 修改广告
	 * +-----------------------------------------------------------
	 */
	public function edit(){
		$param=$this->request->param();
		if (empty($param['id'])){
			$this->error("参数错误");
		}
		$block_db=think_db("ad_block");
		$info=$block_db->find($param['id']);
		if ($this->request->isPost()){
			if (empty($param['position'])){
				$this->error('广告位置不能为空');
			}
			if (empty($param['identifier'])){
				$this->error("标识不能为空");
			}
			$param['title']=isset($param['title']) ? addSlashesFun($param['title']) : "";
			$param['content']=isset($param['content']) ? addSlashesFun($param['content']) : "";
			$param['ctime']=time();
			$param['lang']=$this->lang;
			if ($param['identifier']!=$info['identifier']){
				$count=$block_db->where("identifier='".$param['identifier']."' and lang='".$this->lang."'")->count();
				if ($count>0){
					$this->error("标识已经存在");
				}
			}
			$result=$block_db->update($param);
			if ($result){
				$this->success ( "修改成功!",url('index'));
			}else{
				$this->error($this->model_name->getError());
			} 
		}else{
			return $this->fetch('add',array('method'=>"edit","info"=>$info));
		}
	}
	/**
	 * +-----------------------------------------------------------
	 * 删除碎片广告
	 * +-----------------------------------------------------------
	 */
	public function del(){
		$param=$this->request->param();
		if (empty($param['ids'])){
			$this->error("参数错误");
		}
		$ids=is_array($param['ids']) ? implode(",", $param['ids']) : $param['ids'];
		$block_db=think_db("ad_block");
		$block_list=$block_db->where("id in(".$ids.")")->field("id,content")->select();
		if ($block_list){
			foreach ($block_list as $value){
				$result = $block_db->delete ($value ['id']);
				if ($result) {
					$content = stripslashes ( $value ['content'] );
					delContentImg ( $content ); // 删除文章内容里的图片和附件
				}
			}
			$this->success ( "删除成功" );
		}else {
			$this->error("广告不存在");
		}
		
	}
	
}