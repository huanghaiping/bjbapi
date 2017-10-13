<?php
namespace app\jzadmin\controller;
class Lang extends Common{
	
	protected $model_name=''; 
	
	/**
	 * +-------------------------------------------------------------------
	 * 初始化语言模块
	 * +-------------------------------------------------------------------
	 */
	public function _initialize(){
		parent::_initialize();
		$this->model_name=model("Lang");
	}
	
	/**
	 * +-------------------------------------------------------------------
	 * 语言列表管理
	 * +-------------------------------------------------------------------
	 */
	public function index(){
		$list = $this->model_name->order ( "listorder desc,id desc" )->select ();
		$this->assign ( "list", $list ); //获取语言结构
		return $this->fetch();
	}
	
	/**
	 * +-------------------------------------------------------------------
	 * 添加语言
	 * +-------------------------------------------------------------------
	 */
	public function add(){
		if ($this->request->isPost()){
			$data = array ();
			$param=$this->request->param();
			$data ['name'] = isset ( $param ['name'] ) ? addSlashesFun ( $param ['name'] ) : "";
			if (empty ( $data ['name'] )) {
				$this->error ( '请输入语言名称' );
			}
			$data ['mark'] = isset ( $param ['mark'] ) ? ($param ['mark']) : "";
			if (empty($data ['mark'])){
				$this->error('请输入语言标识');
			}
			$data ['listorder'] = isset ( $param ['listorder'] ) ? intval ( $param ['listorder'] ) : "50";
			$data ['status'] = isset ( $param ['status'] ) ? intval ( $param ['status'] ) : "0";
			$data ['domain'] = isset ( $param ['domain'] ) ?  ( $param ['domain'] ) : "";
			
			//上传国旗图片
			$data['flag']=$param['flag'];
			if ($this->model_name->where ( "name='" . $data ['name'] . "' or mark='" . $data ['mark'] . "'" )->count () == 0) {
				$create_data = $this->model_name->data ( $data )->allowField(true)->save ();
				if ($create_data) {
					$lang_id = $this->model_name->id;
					if ($lang_id) {
						//复制语言文件
						if (isset($param ['copy_name'])&&! empty ( $param ['copy_name'] )) {
							$lang_param_model =  think_db ( "lang_param" );
							$copy_lang_list = $lang_param_model->where ( "lang_id='" . $_POST ['copy_name'] . "'" )->select ();
							$model_list=array('index','api');
							if ($copy_lang_list) {
								$param=array();
								foreach ( $copy_lang_list as $value ) {
									$param[$value['module_type']][] = array ('lang_id' => $lang_id,'module_type'=>$value['module_type'], "mark" => $data ['mark'], 'field' => $value ['field'], 'value' => $value ['value'], 'ctime' => time (), 'alisa' => $value ['alisa'] );
								}	
							}
							foreach ($model_list as $value){
							    if (isset($param[$value])){
                                    $lang_param_model->insertAll ( $param[$value] );
                                    $this->model_name->updateLangCache ( $lang_id, $data ['mark'],$value );
                                }
							}
						}
						F("lang",NULL,array('path'=>DATA_PATH)) ;//删除缓存
						$this->model_name->updateCache ();
						$this->success ('添加成功', url ( 'index' ) );
					} else {
						$this->error ('操作失败', url ( 'index' ) );
					}
				} else {
					$this->error ( $this->model_name->getError () );
				}
			} else {
				$this->error ('语言已经存在', url ( 'index' ) );
			}
		}else{
			$lang_list=$this->model_name->getLang();
			$this->assign("lang_list",$lang_list);
			$this->assign("method","add");
			return $this->fetch();
		}
	}
	
	/**
	 * +-------------------------------------------------------------------
	 * 修改语言
	 * +-------------------------------------------------------------------
	 */
	public function edit(){
		$param=$this->request->param();
		if (!isset($param['id']) || empty($param['id'])){
			$this->error('参数错误');
		}
		if ($this->request->isPost()){
			$data = array ();
			$param=$this->request->param();
			$data ['name'] = isset ( $param ['name'] ) ? addSlashesFun ( $param ['name'] ) : "";
			if (empty ( $data ['name'] )) {
				$this->error ('请输入语言名称');
			}
			$data ['mark'] = isset ( $param ['mark'] ) ? ($param ['mark']) : "";
			if (empty($data ['mark'])){
				$this->error('请输入语言标识');
			}
			$data ['listorder'] = isset ( $param ['listorder'] ) ? intval ( $param ['listorder'] ) : "50";
			$data ['status'] = isset ( $param ['status'] ) ? intval ( $param ['status'] ) : "0";
			$data ['domain'] = isset ( $param ['domain'] ) ?  ( $param ['domain'] ) : "";
			
			//上传国旗图片
			$data['flag']=$param['flag'];
			$old_info = $this->model_name->find ($param['id']);
			$result=$this->model_name->where ( "id=".$param['id'] )->update ( $data );
			if ($result) {
				if ($old_info ['mark'] != $data ['mark']) { //更换国家图标
					$this->model_name->changMark ( $old_info ['id'], $old_info ['mark'], $data ['mark'] );
				}
				F("lang",NULL,array('path'=>DATA_PATH)) ;//删除缓存
				$this->model_name->updateCache ();
				$this->success ('修改成功', url ( 'index' ) );
			}else{
				$this->error ( '修改失败', url ( 'index' ) );
			}
			
			
		}else{
			$info=$this->model_name->find($param['id']);
			$this->assign("info",$info);
			$lang_list=$this->model_name->getLang();
			$this->assign("lang_list",$lang_list);
			$this->assign("method","edit");
			return $this->fetch("add");
		}
	}
	
/**
	 * +--------------------------------------------------------
	 * 删除语言
	 * +--------------------------------------------------------
	 */
	public function dellang() {
		$param=$this->request->param();
		$id = isset ( $param ['id'] ) ? intval ( $param ['id'] ) : "";
		if (empty ( $id )) {
			$this->error ('参数错误');
		}
		$info = $this->model_name->where ( "id='{$id}'" )->find ();
		$result = $this->model_name->where ( "id='{$id}'" )->delete ();
		if ($result) {
			@unlink ( "." . $info ['flag'] ); //删除国旗图标
			think_db ( "lang_param" )->where ( "lang_id='" . $info ['id'] . "'" )->delete ();
			$file_new_name = APP_PATH . config ( "default_module" ) . "/Lang/" . $info ['mark'] . ".php"; //删除语言包
			@unlink ( $file_new_name );
			F("lang",NULL,array('path'=>DATA_PATH)) ;//删除缓存
			$this->model_name->updateCache ();
			$this->success ('删除成功', url ( 'index' ) );
		} else {
			$this->error ('删除失败', url ( 'index' ) );
		}
	}
	
/**
	 * +--------------------------------------------------------
	 * 便携式修改列表状态
	 * +--------------------------------------------------------
	 */
	public function updateStatus() {
		$id = isset ( $_POST ['id'] ) ? intval ( $_POST ['id'] ) : "";
		$status = isset ( $_POST ['status'] ) ? intval ( $_POST ['status'] ) : "";
		$field = isset ( $_POST ['field'] ) ? ($_POST ['field']) : "";
		if (empty($id)||empty($field)){
			$this->error('参数错误');
		}
		$result =$this->model_name->where ( "id='{$id}'" )->setField ( $field, $status);
		if ($result) {
			F("lang",NULL,array('path'=>DATA_PATH)) ;//删除缓存
			$this->model_name->updateCache ();
			$this->success ( '操作成功');
		} else {
			$this->error ('操作失败');
		}
	}
	
	/**
	 * +--------------------------------------------------------
	 * 设置语言
	 * +--------------------------------------------------------
	 */
	public function setlang() {
		
		$id=$this->request->param ('id');
		$lang_id = isset ($id) ? intval($id) : "";
		if (empty ( $lang_id )) {
			$this->error ( '参数错误' );
		}
		$module_type=$this->request->param ('type');
		$lang_param_model = \Think\Db::name ( "lang_param" );
		$param_list = $lang_param_model->where ( "lang_id='" . $lang_id . "' and module_type='".$module_type."'" )->order ( "id desc " )->select ();
		if ($this->request->isPost()) {
			$data = array ();
			$lang_info=$this->model_name->find($lang_id);
			$mark=$lang_info['mark']; 
			foreach ( $param_list as $value ) {
				$data ['value'] = isset ( $_POST [$value ['field'] . "_field"] ) ? addSlashesFun ( $_POST [$value ['field'] . "_field"] ) : "";
				$data ['alisa'] = isset ( $_POST [$value ['field'] . "_alisa"] ) ? addSlashesFun ( $_POST [$value ['field'] . "_alisa"] ) : "";
				$lang_param_model->where ( "id='" . $value ['id'] . "'" )->update ( $data );
				$mark = $value ['mark'];
			}
			$this->model_name->updateLangCache ( $lang_id, $mark, $_POST['type']);
			$this->success ( '操作成功', url( 'index' ) );
		} else {
			$this->assign ( "set_lang_list", $param_list );
			$this->assign ( "lang_id", $lang_id );
			$this->assign("type",$module_type);
			return  $this->fetch();
		}
	}
	
	/**
	 * +--------------------------------------------------------
	 * 添加语言参数
	 * +--------------------------------------------------------
	 */
	public function addparam() {
		$lang_list = $this->model_name->getLang ();
		if ($this->request->isPost()) {
			$data = array ();
			$param=$this->request->param();
			$data ['field'] = isset ( $param ['name'] ) ? addSlashesFun ( $param ['name'] ) : "";
			if (empty ( $data ['field'] )) {
				$this->error ( '参数名称不能为空' );
			}
			$data ['alisa'] = isset ( $param ['alisa'] ) ? addSlashesFun ( $param ['alisa'] ) : "";
			$data ['ctime'] = time ();
			$data ['module_type']=isset ( $param ['module_type'] ) ? addSlashesFun ( $param ['module_type'] ) : "";
			if ($lang_list) {
				$lang_param_model = think_db( "lang_param" );
				$data ['field'] = strtoupper ( $data ['field'] );
				foreach ( $lang_list as $value ) {
					$data ['lang_id'] = $value ['id'];
					$data ['mark'] = $value ['mark'];
					$data ['value'] = isset ( $param [$value ['mark'] . "_" . $value ['id']] ) ? $param [$value ['mark'] . "_" . $value ['id']] : "";
					if (!empty($data ['value'])){
						$data ['value']=addSlashesFun($data ['value']);
						$lang_param_model->insert ( $data );
						$this->model_name->updateLangCache ( $value ['id'], $value ['mark'],$data ['module_type'] ); //更新缓存
					}
				}
				$this->success ( '添加成功' );
			} else {
				$this->error ('操作失败' );
			}
		} else {
			$this->assign ( "lang_list", $lang_list );
			return $this->fetch();
		}
	}
	
}