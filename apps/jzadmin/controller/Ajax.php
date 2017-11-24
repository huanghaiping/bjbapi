<?php
namespace app\jzadmin\controller;

class Ajax extends Common {
	
	/**
	 * 对数据表中的单行或多行记录执行修改 GET参数id为数字或逗号分隔的数字
	 *
	 * @param string $model 模型名称,供M函数使用的参数
	 * @param array  $data  修改的数据
	 * @param array  $where 查询时的where()方法的参数
	 * @param array  $msg   执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
	 * url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
	 */
	 protected function editRow($model, $data, $where, $msg = false,$cacheName="") {
		$id = input ( 'id/a' );
		if (! empty ( $id )) {
			$id = array_unique ( $id );
			$id = is_array ( $id ) ? implode ( ',', $id ) : $id;
			//如存在id字段，则加入该条件  
			$fields = db ()->getTableFields ( array ('table' => config ( 'database.prefix' ) . $model ) );
			if (in_array ( 'id', $fields ) && ! empty ( $id )) {
				$where = array_merge ( array ('id' => array ('in', $id ) ), ( array ) $where );
			}
		}
		$msg = array_merge ( array ('success' => '操作成功！', 'error' => '操作失败！', 'url' => '', 'ajax' => var_export ( Request ()->isAjax (), true ) ), ( array ) $msg );
		if (db ( $model )->where ( $where )->update ( $data ) !== false) {
			if (!empty($cacheName)){
				$cache_array= strpos($cacheName, ",") ?  explode(",", $cacheName) : array($cacheName);
				foreach ($cache_array as $value){
					if (cache('?'.$value,'',array('path'=>CACHE_PATH))){
						cache ( $value, null ,array('path'=>CACHE_PATH)); //清空缓存
					}
					if (F ( '?' . $value, '', array ('path' => DATA_PATH ) )) {
						F ( $value, null, array ('path' => DATA_PATH ) ); //清空缓存
					}
				}
			}
			$this->success ( $msg ['success'], $msg ['url'], $msg ['ajax'] );
		} else {
			$this->error ( $msg ['error'], $msg ['url'], $msg ['ajax'] );
		}
	}
	
	/**
	 * 禁用条目
	 * @param string $model 模型名称,供D函数使用的参数
	 * @param array  $where 查询时的 where()方法的参数
	 * @param array  $msg   执行正确和错误的消息,可以设置四个元素 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
	 * url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
	 */
	protected function forbid($model, $where = array(), $msg = array( 'success'=>'状态禁用成功！', 'error'=>'状态禁用失败！'),$cacheName="") {
		$data = array ('status' => 0 );
		$this->editRow ( $model, $data, $where, $msg ,$cacheName);
	}
	
	/**
	 * 恢复条目
	 * @param string $model 模型名称,供D函数使用的参数
	 * @param array  $where 查询时的where()方法的参数
	 * @param array  $msg   执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
	 * url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
	 */
	protected function resume($model, $where = array(), $msg = array( 'success'=>'状态恢复成功！', 'error'=>'状态恢复失败！'),$cacheName="") {
		$data = array ('status' => 1 );
		$this->editRow ( $model, $data, $where, $msg,$cacheName );
	}
	
	/**
	 * 还原条目
	 * @param string $model 模型名称,供D函数使用的参数
	 * @param array  $where 查询时的where()方法的参数
	 * @param array  $msg   执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
	 * url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
	 */
	protected function restore($model, $where = array(), $msg = array( 'success'=>'状态还原成功！', 'error'=>'状态还原失败！'),$cacheName="") {
		$data = array ('status' => 1 );
		$where = array_merge ( array ('status' => - 1 ), $where );
		$this->editRow ( $model, $data, $where, $msg ,$cacheName);
	}
	
	/**
	 * 条目假删除
	 * @param string $model 模型名称,供D函数使用的参数
	 * @param array  $where 查询时的where()方法的参数
	 * @param array  $msg   执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
	 * url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
	 */
	protected function delete($model, $where = array(), $msg = array( 'success'=>'删除成功！', 'error'=>'删除失败！'),$cacheName="") {
		$data ['status'] = - 1;
		$this->editRow ( $model, $data, $where, $msg ,$cacheName);
	}
	
	/**
	 * 数据排序
	 * @param string $model 模型名称,供D函数使用的参数
	 * @param array  $where 查询时的where()方法的参数
	 * @param array  $msg   执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
	 * url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
	 */
	public  function updatesort($model){
		if (empty ( $model )) {
			$model = $this->request->controller ();
		}
		$ids = input ( 'ids/a' );
		$value = input ( 'value' );
		$field=input('field');
		$keys=input('keys');
		$keys=!empty($keys) ? $keys : "id";
		$cacheName=input('cache');
		$map [$keys] = array ('in', $ids );
		$data [$field] =$value;
		$msg = array( 'success'=>'排序成功', 'error'=>'排序失败');
		$this->editRow ( $model, $data, $map, $msg ,$cacheName);
	}
	
	/**
	 * 设置一条或者多条数据的状态
	 * $Model 模型名称
	 */
	public function setStatus($model = false) {
		if (empty ( $model )) {
			$model = $this->request->controller ();
		}
		$ids = input ( 'ids/a' );
		$status = input ( 'status' );
		if (empty ( $ids )) {
			$this->error ( '请选择要操作的数据' );
		}
		$keys=input('keys');
		$keys=!empty($keys) ? $keys : "id";
		$cacheName=input('cache');
		$map [$keys] = array ('in', $ids );
		switch ($status) {
			case - 1 :
				$this->delete ( $model, $map, array ('success' => '删除成功', 'error' => '删除失败' ) ,$cacheName);
				break;
			case 0 :
				$this->forbid ( $model, $map, array ('success' => '禁用成功', 'error' => '禁用失败' ),$cacheName );
				break;
			case 1 :
				$this->resume ( $model, $map, array ('success' => '启用成功', 'error' => '启用失败' ) ,$cacheName);
				break;
			default :
				$this->error ( '参数错误' );
				break;
		}
	}
}