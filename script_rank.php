<?php
	//error_reporting(E_ALL);
    date_default_timezone_set('Asia/Shanghai');
	ini_set('error_reporting', 'E_ALL ^ E_NOTICE');
	//ini_set('memory_limit','64M');//ini_set('memory_limit','128M');
	//set_time_limit(0);//ini_set("max_execution_time", 30); //30s
	ini_set('precision', 20);//int64  默认为14
	//header('content-type:text/html; charset=utf-8');
	// 当前目录
	if (!defined("ROOT_DIR")) {
		define("ROOT_DIR", dirname(__FILE__));	
	}
	// 包含头文件
	function path_format($file) {
		return ROOT_DIR . '/' . $file;
	}
	
	$_SERVER['HTTP_HOST'] = 'xytj.dcyouxi.com';
	$_SERVER["REMOTE_ADDR"] = 'xytj.dcyouxi.com';
	
	include_once(path_format('basic/basicinit.php'));
	
	
	function main_script($action_name,$version,$param) {
		$query = array();
		$query['action'] = $action_name;
		$query['version'] = $version;
		$query['key_value'] = 1;
		$query['flag_value'] = 1;
		$query['sign_value'] = time();
		$query['data_value'] = $param;
		$data = json_encode($query);
		$_REQUEST[actioncode::$param_name] = $data;
		$app = new basicapp();
		$app->run();
	}
	
	// 排行榜 - 5分钟
	function main_run() {
		while(true)
		{
			main_script('ranking','crons','');
			sleep(300);
		}
	}
	
	
	main_run();
?>