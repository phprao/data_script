<?php
/**
 +---------------------------------------------------------- 
 * date: 2018-05-03 11:08:42
 +---------------------------------------------------------- 
 * author: Raoxiaoya
 +---------------------------------------------------------- 
 * describe: 清除邮件
 +---------------------------------------------------------- 
 */

set_time_limit(0);

require('lib/config.php');
require('lib/mysql.php');

class Mail
{
	private $del_time = 5184000;// 2 month
	private $limit = 10000;

	public function __construct(){
		$this->mysql = new MysqlDriver(Config::$mysql_config);
	}

	public function main(){

		$t = time() - $this->del_time;
		$this->del_list($t, $i);
		
	}

	protected function del_list($t, $i){
		$sql = 'select * from dc_mail where mail_status in (2,3) and mail_create_time <= '.$t.' limit '.$this->limit;
		$list = $this->mysql->find($sql);
		if($list){
			foreach($list as $val){
				$sql = 'delete from dc_mail where mail_id = '.$val['mail_id'] ;
				$this->mysql->query($sql);
				unset($val['mail_id']);
				$table = 'dc_mail_'.($val['mail_receiver_id'] % 10);
				$this->mysql->insert($table, $val);
			}
		}
		
	}
	
}

function main_run() {
	while(true)
	{
		$Mail = new Mail();
		$Mail->main();
		sleep(1800); // 30 min
	}
}

main_run();
