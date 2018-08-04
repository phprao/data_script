<?php
	//error_reporting(E_ALL);
    date_default_timezone_set('Asia/Shanghai');
	ini_set('error_reporting', 'E_ALL ^ E_NOTICE');
	ini_set('memory_limit','64M');//ini_set('memory_limit','128M');
	set_time_limit(30);//ini_set("max_execution_time", 30); //30s
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
	
	include_once(path_format('basic/basicinit.php'));
	
	$app = new basicapp();
	$app->run();
	
?>