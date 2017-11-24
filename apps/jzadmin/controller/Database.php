<?php
/**
 * +--------------------------------------------------------------------
 * 数据库的备份还原
 * +--------------------------------------------------------------------
 * @author Alan(451648237@qq.com)
 * +--------------------------------------------------------------------
 */
namespace app\jzadmin\controller;
class Database extends Common {
	
	/**
	 * +--------------------------------------------------------------------
	 * 数据库备份
	 * +--------------------------------------------------------------------
	 */
	public function index() {
		$Db = \think\Db::connect ();
		$tabs = $Db->query ( 'SHOW TABLE STATUS' );
		$total = 0;
		foreach ( $tabs as $k => $v ) {
			$tabs [$k] ['size'] = byteFormat ( $v ['Data_length'] + $v ['Index_length'] );
			$total += $v ['Data_length'] + $v ['Index_length'];
		}
		$this->assign ( "list", $tabs );
		$this->assign ( "total", byteFormat ( $total ) );
		$this->assign ( "tables", count ( $tabs ) );
		return $this->fetch ();
	
	}
	
	/**
	 * +--------------------------------------------------------------------
	 * 数据库备份数据
	 * +--------------------------------------------------------------------
	 */
	public function export($id = null, $start = null) {
		if ($this->request->isPost ()) {
			function_exists ( 'set_time_limit' ) && set_time_limit ( 0 ); //防止备份数据过程超时
			$postData = $this->request->post ();
			$tables = $postData ['table'];
			if (count ( $tables ) == 0) {
				$this->error ( lang('PLEASE_SELECT_DATABASE') );
			}
			//判断备份的权限，只有超级管理才可以备份数据
			$login_info = session ( "user_auth" );
			if ($login_info ['role_id'] != 1) {
				$this->error ( lang('ONLY_SUPER_ADMINISTRATOR') );
			}
			//备份数据
			$time = time ();
			$backup_config = config ( "backup_config" );
			if (! is_dir ( $backup_config ['path'] )) {
				mkdir ( $backup_config ['path'], 0755, true );
			}
			//读取备份配置
			$config = array ('path' => realpath ( $backup_config ['path'] ) . DIRECTORY_SEPARATOR, 'part' => $backup_config ['part_size'], 'compress' => $backup_config ['compress'], 'level' => $backup_config ['compress_level'] );
			//检查是否有正在执行的任务
			$lock = "{$config['path']}backup.lock";
			if (is_file ( $lock )) {
				return $this->error ( lang('BACKUP_TASK_DETECTED') );
			} else {
				//创建锁文件
				file_put_contents ( $lock, time () );
			}
			//检查备份目录是否可写
			if (! is_writeable ( $config ['path'] )) {
				return $this->error ( lang('BACKUP_DIRECTORY_WRITTEN') );
			}
			session ( 'backup_config', $config );
			//生成备份文件信息
			$file = array ('name' => date ( 'Ymd-His', time () ), 'part' => 1 );
			session ( 'backup_file', $file );
			//缓存要备份的表
			session ( 'backup_tables', $tables );
			//创建备份文件
			$Database = new \database\Database ( $file, $config );
			if (false !== $Database->create ()) {
				$tab = array ('id' => 0, 'start' => 0 );
				return $this->success ( lang('INITIAL_SUCCESS'), '', array ('tables' => $tables, 'tab' => $tab ) );
			} else {
				return $this->error ( lang('FAILED_TO_INITIALIZE') );
			}
		} elseif ($this->request->isGet () && is_numeric ( $id ) && is_numeric ( $start )) {
			//备份数据
			$tables = session ( 'backup_tables' );
			//备份指定表
			$Database = new \database\Database ( session ( 'backup_file' ), session ( 'backup_config' ) );
			$start = $Database->backup ( $tables [$id], $start );
			if (false === $start) { //出错
				return $this->error ( lang('BACKUP_ERROR') );
			} elseif (0 === $start) {
				if (isset ( $tables [++ $id] )) { //下一表
					$tab = array ('id' => $id, 'start' => 0 );
					return $this->success ( lang('BACKUP_COMPLETE'), '', array ('tab' => $tab ) );
				} else {
					//备份完成，清空缓存
					unlink ( session ( 'backup_config.path' ) . 'backup.lock' );
					session ( 'backup_tables', null );
					session ( 'backup_file', null );
					session ( 'backup_config', null );
					return $this->success (  lang('BACKUP_COMPLETE') );
				}
			} else {
				$tab = array ('id' => $id, 'start' => $start [0] );
				$rate = floor ( 100 * ($start [0] / $start [1]) );
				return $this->success ( lang('BACKING_UP')."...({$rate}%)", '', array ('tab' => $tab ) );
			}
		} else {
			//出错
			return $this->error ( lang('PARAMETER_ERROR') );
		}
	}
	
	/**
	 * +--------------------------------------------------------------------
	 * 数据库还原
	 * +--------------------------------------------------------------------
	 */
	public function restore() {
	
		//列出备份文件列表
		$backup_config = config ( "backup_config" );
		$path = $backup_config ['path'];
		if (! is_dir ( $path )) {
			mkdir ( $path, 0755, true );
		}
		$path = realpath ( $path );
		$handle = opendir ( $path );
		$list = array ();
		$size = 0;
		while ( $file = readdir ( $handle ) ) {
			if ($file != "." && preg_match ( '/^\d{8,8}-\d{6,6}-\d+\.sql(?:\.gz)?$/', $file )) {
				$info = array ();
				$info ['name'] = $file;
				$name = sscanf ( $file, '%4s%2s%2s-%2s%2s%2s-%d' );
				$date = "{$name[0]}-{$name[1]}-{$name[2]}";
				$time = "{$name[3]}:{$name[4]}:{$name[5]}";
				$part = $name [6];
				if (isset ( $list ["{$date} {$time}"] )) {
					$info = $list ["{$date} {$time}"];
					$info ['part'] = max ( $info ['part'], $part );
					$info ['size'] = $info ['size'] + filesize ( $path . "/$file" );
				} else {
					$info ['part'] = $part;
					$info ['size'] = filesize ( $path . "/$file" );
				}
				$size += $info ['size'];
				$info ['time'] = strtotime ( "{$date} {$time}" );
				$list ["{$date} {$time}"] = $info;
			}
		}
		closedir ( $handle );
		krsort ( $list ); //按备份时间倒序排列
		$this->assign ( "list", $list );
		return $this->fetch ();
	}
	
	/**
	 * +--------------------------------------------------------------------
	 * 优化表
	 * +--------------------------------------------------------------------
	 * @param  String $tables 表名
	 * +--------------------------------------------------------------------
	 */
	public function optimize($tables = null) {
		if ($tables) {
			$Db = \think\Db::connect ();
			if (is_array ( $tables )) {
				$tables = implode ( '`,`', $tables );
				$list = $Db->query ( "OPTIMIZE TABLE `{$tables}`" );
				if ($list) {
					return $this->success ( lang('DATAZ_SHEET_OPTIMIZATION'));
				} else {
					return $this->error ( lang('DATA_TABLE_OPTIMIZATION') );
				}
			} else {
				$list = $Db->query ( "OPTIMIZE TABLE `{$tables}`" );
				if ($list) {
					return $this->success ( lang('DATA_SHEE_OPTIMIZATION_COMPLETED',array('tables'=>$tables)) );
				} else {
					return $this->error ( lang('DATA_SHEET_OPTIMIZATIONS',array('tables'=>$tables)));
				}
			}
		} else {
			return $this->error ( lang('PLEASE_SELECT_TABLES') );
		}
	}
	/**
	 * +--------------------------------------------------------------------
	 * 修复表
	 * +--------------------------------------------------------------------
	 * @param  String $tables 表名
	 * +--------------------------------------------------------------------
	 */
	public function repair($tables = null) {
		if ($tables) {
			$Db = \think\Db::connect ();
			if (is_array ( $tables )) {
				$tables = implode ( '`,`', $tables );
				$list = $Db->query ( "REPAIR TABLE `{$tables}`" );
				if ($list) {
					return $this->success (lang('DATA_SHEET_REPAIR_COMPLETED') );
				} else {
					return $this->error ( lang('DATA_TABLE_REPAIR_ERROR'));
				}
			} else {
				$list = $Db->query ( "REPAIR TABLE `{$tables}`" );
				if ($list) {
					return $this->success ( lang('DATA_SHEET_REPAIR_COMPLETEDS',array('tables'=>$tables)));
				} else {
					return $this->error (  lang('DATA_SHEET_AGAIN',array('tables'=>$tables)));
				}
			}
		} else {
			return $this->error ( lang('PLEASE_SELECT_TABLES') );
		}
	}
	/**
	 * +--------------------------------------------------------------------
	 * 删除备份文件
	 * +--------------------------------------------------------------------
	 * @param  Integer $time 备份时间
	 * +--------------------------------------------------------------------
	 */
	public function del($time = 0) {
		if ($time) {
			$name = date ( 'Ymd-His', $time ) . '-*.sql*';
			$backup_config = config ( "backup_config" );
			$path = $backup_config ['path'];
			$path = realpath ( $path ) . DIRECTORY_SEPARATOR . $name;
			array_map ( "unlink", glob ( $path ) );
			if (count ( glob ( $path ) )) {
				return $this->error (lang('BACKUP_FILE_DELETION_FAILED'));
			} else {
				return $this->success ( lang('DELETE_SUCCESS') );
			}
		} else {
			return $this->error ( lang('PARAMETER_ERROR') );
		}
	}
	
/**
	 * 还原数据库
	 * @author 艺品网络  <twothink.cn>
	 */
	public function import($time = 0, $part = null, $start = null) {
		if (is_numeric($time) && is_null($part) && is_null($start)) { 
			//初始化
			//获取备份文件信息
			$backup_config = config ( "backup_config" );
			$path = $backup_config ['path'];
			$name  = date('Ymd-His', $time) . '-*.sql*';
			$path  = realpath($path) . DIRECTORY_SEPARATOR . $name;
			$files = glob($path);
			$list  = array();
			foreach ($files as $name) {
				$basename        = basename($name);
				$match           = sscanf($basename, '%4s%2s%2s-%2s%2s%2s-%d');
				$gz              = preg_match('/^\d{8,8}-\d{6,6}-\d+\.sql.gz$/', $basename);
				$list[$match[6]] = array($match[6], $name, $gz);
			}
			ksort($list);
			//检测文件正确性
			$last = end($list);
			if (count($list) === $last[0]) {
				session('backup_list', $list); //缓存备份列表
				return $this->success(lang('INITIAL_SUCCESS'), '', array('part' => 1, 'start' => 0));
			} else {
				return $this->error(lang('BACKUP_FILE_MAY_CORRUPTED'));
			}
		} elseif (is_numeric($part) && is_numeric($start)) {
			$list = session('backup_list');
			$backup_config = config ( "backup_config" );
			$path = $backup_config ['path'];
			$db = new \database\Database($list[$part], array('path' => realpath($path) . DIRECTORY_SEPARATOR, 'compress' => $list[$part][2]));
			$start = $db->import($start);
			if (false === $start) {
				return $this->error(lang('ERROR_RESTORING_DATA'));
			} elseif (0 === $start) {
				//下一卷
				if (isset($list[++$part])) {
					$data = array('part' => $part, 'start' => 0);
					return $this->success(lang('RESTORING')."#{$part}", '', $data);
				} else {
					session('backup_list', null);
					return $this->success(lang('RESTORE_COMPLETED'));
				}
			} else {
				$data = array('part' => $part, 'start' => $start[0]);
				if ($start[1]) {
					$rate = floor(100 * ($start[0] / $start[1]));
					return $this->success(lang('RESTORING')."#{$part} ({$rate}%)", '', $data);
				} else {
					$data['gz'] = 1;
					return $this->success(lang('RESTORING')."#{$part}", '', $data);
				}
			}
		} else {
			return $this->error(lang('PARAMETER_ERROR'));
		}
	}

}