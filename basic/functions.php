<?php

function app() {
	return basicdi::get_instance();
}

function BASIC_LOG_INFO($target,$format) {
	//$num = func_num_args();
	$args = func_get_args();
	//var_dump($args);
	$param = array();
	foreach($args as $value) {
		if($value == $format || $target == $value) continue;
		array_push($param, $value);
	}
	$msg = vsprintf($format,$param);
	
	//var_dump($msg);
	app()->m_logger->info($target, $msg);
}

function BASIC_LOG_TRACE($target,$format) {
	$args = func_get_args();
	$param = array();
	foreach($args as $value) {
		if($value == $format || $target == $value) continue;
		array_push($param, $value);
	}
	$msg = vsprintf($format,$param);
	app()->m_logger->trace($target, $msg);
}

function BASIC_LOG_WARNING($target,$format) {
	$args = func_get_args();
	$param = array();
	foreach($args as $value) {
		if($value == $format || $target == $value) continue;
		array_push($param, $value);
	}
	$msg = vsprintf($format,$param);
	app()->m_logger->warn($target, $msg);
}

function BASIC_LOG_DEBUG($target,$format) {
	$args = func_get_args();
	$param = array();
	foreach($args as $value) {
		if($value == $format || $target == $value) continue;
		array_push($param, $value);
	}
	$msg = vsprintf($format,$param);
	app()->m_logger->debug($target, $msg);
}

function BASIC_LOG_ERROR($target,$format) {
	$args = func_get_args();
	$param = array();
	foreach($args as $value) {
		if($value == $format || $target == $value) continue;
		array_push($param, $value);
	}
	$msg = vsprintf($format,$param);
	app()->m_logger->error($target, $msg);
}

function BASIC_LOG_FATAL($target,$format) {
	$args = func_get_args();
	$param = array();
	foreach($args as $value) {
		if($value == $format || $target == $value) continue;
		array_push($param, $value);
	}
	$msg = vsprintf($format,$param);
	app()->m_logger->fatal($target, $msg);
}

//+++++++++++++++++++++++++++++++++++++++++++++++++
function BASIC_GET_LOGGER($srv_name = null) {
	if(is_null($srv_name)) {
		return app()->m_logger;
	}else if('data' == $srv_name) {
		return app()->m_data_logger;
	}else if('redis' == $srv_name) {
		return app()->m_redis_logger;
	}else if('action' == $srv_name ) {
		return app()->m_action_logger;
	}else if('system' == $srv_name) {
		return app()->m_system_logger;
	}
	return app()->m_logger;
}
function BASIC_SYS_LOG_ERROR($log_name,$target,$format) {
	$log_srv = BASIC_GET_LOGGER($log_name);
	$args = func_get_args();
	$param = array();
	foreach($args as $value) {
		if($value == $format || $target == $value || $log_name == $value) continue;
		array_push($param, $value);
	}
	$msg = vsprintf($format,$param);
	$log_srv->error($target, $msg);
}

function BASIC_SYS_LOG_WARNING($log_name,$target,$format) {
	$log_srv = BASIC_GET_LOGGER($log_name);
	$args = func_get_args();
	$param = array();
	foreach($args as $value) {
		if($value == $format || $target == $value || $log_name == $value) continue;
		array_push($param, $value);
	}
	$msg = vsprintf($format,$param);
	$log_srv->warn($target, $msg);
}
function BASIC_SYS_LOG_DEBUG($log_name,$target,$format) {
	$log_srv = BASIC_GET_LOGGER($log_name);
	$args = func_get_args();
	$param = array();
	foreach($args as $value) {
		if($value == $format || $target == $value || $log_name == $value) continue;
		array_push($param, $value);
	}
	$msg = vsprintf($format,$param);
	$log_srv->debug($target, $msg);
}

function BASIC_LOG_SYSTEM_HANDLER($target,$format) {
	$path_log = PROJECT_ROOT . '/logs/system/system_';
	$log = new basiclogger($path_log,true);

	$args = func_get_args();
	$param = array();
	foreach($args as $value) {
		if($value == $format || $target == $value) continue;
		array_push($param, $value);
	}
	$msg = vsprintf($format,$param);

	$log->warn($target,$msg);
}

function BASIC_LOG_CRON_HANDLER($target,$format) {
    $path_log = realpath(PROJECT_ROOT) . '/logs/crons/crons_';
    $log = new basiclogger($path_log,true);

    $args = func_get_args();
    $param = array();
    foreach($args as $value) {
        if($value == $format || $target == $value) continue;
        array_push($param, $value);
    }
    $msg = vsprintf($format,$param);

    $log->info($target,$msg);
}

function BASIC_DISPLAY_ERROR_HANDLER($error, $error_string, $filename, $line, $symbols) {
	//BASIC_SYS_LOG_WARNING('system','error','%s',$error_string);
	BASIC_LOG_SYSTEM_HANDLER('error','%s',$error_string. '_' . $filename . '_'. $line);
}

function BASIC_EXCEPTION_HANDLER( $exception ) {
	//BASIC_SYS_LOG_WARNING('system','exception','%s',$exception->getMessage());
	BASIC_LOG_SYSTEM_HANDLER('exception','%s',$exception->getFile(). '_' .$exception->getLine() .'_' .$exception->getMessage() );
}

function BASIC_SHUTDOWN_HANDLER() {
	$data = error_get_last();
	//$msg = json_encode($data);
	if(isset($data['message']) && !is_null($data['message'])) {
		//$path_log = PROJECT_ROOT . '/logs/system/system_';
		//$log = new basiclogger($path_log,true);
		//$log->warn('shutdown',$data['message']);
		BASIC_LOG_SYSTEM_HANDLER('shutdown','%s',$data['file'].'_'.$data['line'].'_'.$data['message']);
	}
}

// ====================================================================================================
//                  自定义公用函数扩展 请在以下添加
// ====================================================================================================

if (!function_exists('get_client_ip')) {

    /**
     * 获取客户端IP地址
     * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
     * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
     * @return mixed
     */
    function get_client_ip($type = 0,$adv=false) {
        $type       =  $type ? 1 : 0;
        static $ip  =   NULL;
        if ($ip !== NULL) return $ip[$type];
        if($adv){
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $pos    =   array_search('unknown',$arr);
                if(false !== $pos) unset($arr[$pos]);
                $ip     =   trim($arr[0]);
            }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip     =   $_SERVER['HTTP_CLIENT_IP'];
            }elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $ip     =   $_SERVER['REMOTE_ADDR'];
            }
        }elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip     =   $_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $long = sprintf("%u",ip2long($ip));
        $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
        return $ip[$type];
    }
}

set_error_handler('BASIC_DISPLAY_ERROR_HANDLER');
set_exception_handler('BASIC_EXCEPTION_HANDLER');
register_shutdown_function('BASIC_SHUTDOWN_HANDLER');

?>