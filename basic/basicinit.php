<?php
/**
 * 用户这个框架初始化
 *  basicloader 指定自动加载的目录
 *  basicdi 容器类,全局注册组件和服务
 */

//error_reporting(E_ALL);
//include_once(path_format('basic/basicloader.php'));
//include_once(path_format(dirname(__FILE__).DIRECTORY_SEPARATOR.'basicloader.php'));
//echo ("xxm");
defined('PROJECT_ROOT') || define('PROJECT_ROOT', dirname(__FILE__) . '/..');
require_once(PROJECT_ROOT . '/basic/apibasic.php');
$loader = new basicloader(PROJECT_ROOT, 'basic');
$loader->add_dirs('basic' . DIRECTORY_SEPARATOR . 'impl');
$loader->add_dirs('action' . DIRECTORY_SEPARATOR . 'model');
$loader->add_dirs('action' . DIRECTORY_SEPARATOR . 'db');
$loader->add_dirs('action' . DIRECTORY_SEPARATOR . 'redis');
$loader->add_dirs('action' . DIRECTORY_SEPARATOR . 'utility');
$loader->add_dirs('action' . DIRECTORY_SEPARATOR . 'library');
$loader->add_dirs('action' . DIRECTORY_SEPARATOR . 'block');
$loader->add_dirs('action' . DIRECTORY_SEPARATOR . 'crons');
$loader->add_dirs('action' . DIRECTORY_SEPARATOR . 'crons/stat_data');
$loader->add_dirs('action' . DIRECTORY_SEPARATOR . 'script');
app()->m_loader = $loader;

$srv_type = app()->check_srv_type();
$debug = app()->is_debug();
$host_name = app()->get_host_name();

$config = new basicconfig($srv_type,$host_name);
app()->m_config = $config;

//$db_worker = new basicdbworker();
//app()->m_db_worker = $db_worker;
$server = new basicserver($debug);
app()->m_server = $server;

$log = new basiclogger("logs/logic/logic_", $debug);
app()->m_logger = $log;

$data_log = new basiclogger("logs/data/data_", $debug);
app()->m_data_logger = $data_log;

$redis_log = new basiclogger("logs/redis/redis_", $debug);
app()->m_redis_logger = $redis_log;

$action_log = new basiclogger("logs/action/action_", $debug);
app()->m_action_logger = $action_log;

$system_log = new basiclogger("logs/system/system_", $debug);
app()->m_system_logger = $system_log;
//app()->set('xxm','585858');
//echo " xxm";
//BASIC_LOG_INFO('mmm',"xxx%d",12);
//$log->clear_time_out();
//$data_log->clear_time_out();
//$redis_log->clear_time_out();
app()->clear_log_data();
