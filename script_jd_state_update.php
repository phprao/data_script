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
	
	//$_SERVER['HTTP_HOST'] = 'xytj.dcyouxi.com';
	//$_SERVER["REMOTE_ADDR"] = 'xytj.dcyouxi.com';
	
	include_once(path_format('basic/basicinit.php'));
	include_once(path_format('basic/httpclient.php'));
	include_once(path_format('lib/logger.php'));
	
class script_jd_state_update{
	private $url_prefix = 'http://king.dcyouxi.com/king/action.php?';
	private $logTag = 'logs/script_jd_state_update';

	public function __construct(){
		$this->logs    	= new Logger();
	}
	public function main_script($action_name,$version,$param) {
		$url_prefix = $this->url_prefix;
		$action = $action_name;
	    $param = array();
	    $param['player_token'] = 'SFNtQW9meEhxZVVZbTYwa3hRNHg0RVVwbVhVUHZaZkpqQT09EKLFK';

	    $query = array();
	    $query['param']['action'] = $action_name;
	    $query['param']['version'] = $version;
	    $query['param']['key_value'] = 1;
	    $query['param']['flag_value'] = 1;
	    $query['param']['sign_value'] = time();
	    $query['param']['data_value'] = $param;
	    $query['param'] = json_encode($query['param']);
	    $url = $url_prefix . http_build_query($query);
	    echo urldecode($url);

	    $client = new httpclient();
	    echo "<pre />";
	    echo $client->get($url);
	    $this->logs->info($this->logTag,'function script_jd_state_update success ...');
	}
	
	//请求接口
	public function main_run() {

		while(true)
		{
			$this->main_script('jdcrontab','v10001','');
			sleep(7200);
		}
	}
}

$main = new script_jd_state_update();
$main->main_run();
	
?>