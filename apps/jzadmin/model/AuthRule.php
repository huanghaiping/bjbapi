<?php
namespace app\jzadmin\model;
use app\common\model\Common;
class AuthRule extends Common {
	
	protected $autoWriteTimestamp = true;
	protected $createTime = 'ctime';
	protected $updateTime = false;
	protected $_field = "id,pid,title,name,condition,level,sort,status,icon,type";
	
	/**
	 * +------------------------------------------------------
	 * 获取所有节点规则
	 * +------------------------------------------------------
	 */
	public function getTree() {
		$row = F ( "auth_rule_category" );
		if (! $row) {
			$field_values = explode ( ",", $this->_field );
			array_splice ( $field_values, 3, 0, 'fullname' );
			$auth_category = new \org\Category ( 'auth_rule', $field_values );
			$auth_categry_list = $auth_category->getList ( '', 0, "sort ASC,id desc" );
			if ($auth_categry_list) {
				foreach ($auth_categry_list as $value){
					$value['level_text']=$this->getLevelTextAttr($value['level'],$value);
					$row[$value['id']]=$value;
				}
				unset($auth_categry_list);
				F ( "auth_rule_category", $row );
			}
		}
		return $row;
	}
	
	/**
	 * +------------------------------------------------------
	 * 获取规则的层级名词
	 *  +------------------------------------------------------
	 * @param unknown_type $value
	 */
	public function getLevelTextAttr($value,$data)
    {
        $status = array(1=>'项目',2=>'模块控制器',3=>"操作",4=>"栏目菜单");
        return $status[$data['level']];
    }
	
	/**
	 * +------------------------------------------------------
	 * 新增或更新一个行为
	 * +------------------------------------------------------
	 * @return boolean fasle 失败 ， int  成功 返回完整的数据 
	 */
	public function updates() {
		/* 获取数据对象 */
		$data = input ();
		/* 添加或新增行为 */
		if (empty ( $data ['id'] )) { //新增数据
			$id = $this->validate ( true )->allowField ( true )->save ( $data ); //添加行为
			if (! $id) {
				$this->error = $this->getError ();
				return false;
			}
		} else { //更新数据
			$status = $this->validate ( true )->allowField ( true )->update ( $data ); //更新基础内容
			if (false === $status) {
				$this->error = $this->getError ();
				return false;
			}
		}
		//删除缓存
		F ( 'auth_rule_category', null );
		//内容添加或更新完成
		return $data;
	}
	
	/**
	 * 根据规则的ID获取规则信息内容
	 * 
	 * @param String $ids	规则ID
	 */
	public function getRuleByIds($ids, $type = 1) {
		$rule_list = $this->getTree ();
		if (empty ( $rule_list )) {
			return false;
		}
		if (empty ( $ids )) {
			foreach ($rule_list as $key=>$value){
				if ($value ['status'] != 1){
					unset($rule_list[$key]);
				}
			}
			return $rule_list;
		} else {
			$ids = ! is_array ( $ids ) ? explode ( ",", $ids ) : $ids;
			$rules = array ();
			foreach ( $rule_list as $value ) {
				if ($value ['status'] == 1 && in_array ( $value ['id'], $ids ) && $value ['type'] == $type) {
					$rules [$value['id']] = $value;
				}
			}
			return $rules;
		}
	}
	
	
	/**
	 * 获取当前主菜单所有可以访问的url
	 *
	 * @param Array $array
	 * @param int   $pid
	 */
	public function getChildPath($array,$pid=0){
		$arr = array();
		foreach ($array as $key => $value) {
			if ($value['pid']==$pid){
				if ($value['level']==2){
					$arr[]=$value;
				}			 
				if (isset($value['child'])&&count($value['child']>0)){
					$arr=array_merge($arr,$this->getChildPath($value['child'],$value['id'])); 
				}
			}
		}
		return $arr;
	}
	
	/**
	 * 获取当前url的菜单信息
	 *  
	 * @param Array $menuData
	 */
	public function getCurrentUrl($menuData){ 
		$request=request();
		$model_name =$request->module();
    	$controller  = $request->controller(); 
    	$action_name =$request->action();
    	$url=str_replace('/', '\/', url($model_name."/".$controller."/".$action_name));
		foreach ($menuData as $key=>$value){
			if (!in_array($value['level'], array(1,4))&&!empty($value['name'])&&preg_match('/'.strtolower($url).'/i', strtolower(url($value['name'])))){
				return $value;
			}
		}
	}
	
	
    /**
     * +----------------------------------------------------------
     * 根据权限规则ID获取菜单
     * +----------------------------------------------------------
     * 
     * @param string $ruleId	规则ID
     */
    public function getMenu($ruleId){ 	    
       $menu_data_list=$this->getRuleByIds($ruleId); //获取所有的节点
       $menu=array();
       if ($menu_data_list){
       		$menu['main']=list_tree($menu_data_list);
       		if (!empty($menu['main'])){
       			foreach ($menu['main'] as $key=>$value){
	       			if(isset($value['child'])&&count($value['child'])){
	       				$childNode=$this->getChildPath($value['child'],$value['id']);
	       				if (isset($childNode[0])){
							$value['name'] = $childNode[0]['name'];
							$value['condition'] = $childNode[0]['condition'];
							$menu['main'][$key]=$value;
	       				}
					}
       			}
       		}
       		/*获取当前url的顶级主菜单id（用于高亮选中显示主菜单）*/
       		$menu['crumbs']=array(); //获取面包屑
			$current = $this->getCurrentUrl(array_multi2single($menu['main']));
			if ($current){
	       		if ($current['pid']!=0){
	       			 $parents = getParents($menu_data_list,$current['id']);
	       			 $menu['crumbs']=array_reverse($parents);
	       			 if(!empty($parents)){
						foreach ($parents as $key => $value) {
							if($value['pid'] != 0){
								unset($parents[$key]);
							}
						}
					}
					$parents = array_values($parents);
					$topid = $parents[0]['id'];
					
	       		}else{
	       			$menu['crumbs']=array($current);
	       			$topid = $current['id'];
	       		}
			}else{
				$menu['main']=array_values($menu['main']);
				$topid=$menu['main'][0]['id'];
				$menu['crumbs']=array($menu_data_list[$topid]);
			}
       		 
       		//设置高亮主菜单
			if(!empty($menu['main'])){
				foreach ($menu['main'] as $key => $value) {
					unset($menu['main'][$key]['child']);
					//设置高亮class
					if($value['id'] == $topid){
						$menu['main'][$key]['class'] = 'current';
					} else {
						$menu['main'][$key]['class'] = "";
					}
				}
			}
			//通过父类的id获取子类的菜单
		    $child = getChilds($menu_data_list,$topid);
	       if(!empty($child)){
				foreach ($child as $key => $value) {
					//如果当前的菜单则添加childon样式，包括父类也添加
					
					if($current['id'] == $value['id']){
						$child[$key]['class'] = 'active';
						//父类加上childon选中样式
						foreach ($child as $key_1 => $value_1) {
							if($value['pid'] == $value_1['id']){
								$child[$key_1]['class'] = 'active';
							}
						}
					} else {
						$child[$key]['class'] = '';
					}
					if ($value['level']==3){
						unset($child[$key]);
					}
				}
			}
		 	$menu['child']=list_tree($child,$topid);
		 	return $menu;
       }else{
       		return false;
       }
    }
 

}