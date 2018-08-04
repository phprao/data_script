<?php

/**
 * 红包补发
 */

set_time_limit(0);

require('lib/config.php');
require('lib/mysql.php');

class Trace{
	private $mysql;

	public function __construct() {
		$this->mysql   	= new MysqlDriver(Config::$mysql_config);
	}
	
	public function main(){
		$time = strtotime(date('Y-m-d'));
		
		$sql = 'update dc_new_bonus_num set num = num + 2 where time = '.$time.' and game_id = 20020400';
		$bonus = $this->mysql->query($sql);

	}

}


function main_run() {
	while(true)
	{
		$Trace = new Trace();
		$id = $Trace->main();
		sleep(60);// 1
	}
}

main_run();