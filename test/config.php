<?php

class Config {
	public static $url_prefix = "http://192.168.1.160/dc_better/action.php?";

	public static $mysql_config = array(
		'host' => '192.168.1.210',
		'port' => 3306,
		'username' => 'root',
		'password' => '123456',
		'dbname' => 'dcmjdb',
		'charset' => 'utf8',
	);
	
	public static $ver = 'v10001';
	public static $ver_crons = 'crons';
	
	public static $logger = null;


	public static function build_srv_url() {
		$host = $_SERVER['HTTP_HOST'];
		$ip = $host;
		/*switch ($host) {
			case '127.0.0.1':
			case '192.168.1.210':
			case 'localhost':
			case '192.168.1.160':
				$ip = '192.168.1.210';
				break;
			case '118.89.65.247':
			case 'test.dcyouxi.com':
				$ip = '118.89.65.247';
			default:
				break;
		}
		*/
		//var_dump($_SERVER);
		//Config::$url_prefix = "http://{$ip}/dc_better/action.php?";
		$url = 'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].$_SERVER['SCRIPT_NAME'];
		if('80' == $_SERVER['SERVER_PORT']) {
			$url = 'http://'.$_SERVER['SERVER_NAME'].''.$_SERVER['SCRIPT_NAME'];
		}
		$url = substr($url, 0, strlen($url) - 16);
		Config::$url_prefix = $url . 'action.php?';
		//var_dump(Config::$url_prefix);
		return $ip;
	}
}

?>
