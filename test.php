<?php
/**
 +---------------------------------------------------------- 
 * date: 2018-05-03 11:08:42
 +---------------------------------------------------------- 
 * author: Raoxiaoya
 +---------------------------------------------------------- 
 * describe: 推送跑马灯消息
 +---------------------------------------------------------- 
 */
set_exception_handler('output_exception');
set_error_handler('output_error');
register_shutdown_function('output_shutdown');

include_once('test_class.php');


$Message = new Message();
$Message->main();
		
function output_exception($e){
	var_dump($e->getFile());
	var_dump($e->getLine());
	var_dump($e->getMessage());
}
function output_error($errno, $errstr, $errfile, $errline){
	var_dump($errno);
	var_dump($errstr);
	var_dump($errfile);
	var_dump($errline);
}
function output_shutdown(){
	$data = error_get_last();
	if(isset($data['message']) && !is_null($data['message'])) {
		var_dump($data);
	}
}