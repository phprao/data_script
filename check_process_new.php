<?php
// 监控进程并重启

ini_set('date.timezone','Asia/Shanghai');

class checkProcess{
	protected $dir = '';
	protected $deadtime = 0;
	public function __construct($dirname){
		$this->dir = $dirname;
	}
	//检查是否在执行
	public function check_php($filename){
		$fullname = $this->dir . "/" . $filename;
		// ps -ax|grep /mydata/data/mainscript/script_api/script_game_record.php|grep -v grep|wc -l
		$str = "ps -ef | grep " . $fullname . '|grep -v grep|wc -l'; //查看符合条件的进程数量,排除grep进程本身
		$tmp = exec($str);
		//不在运行
		if ( $tmp < 1 ){
			$this->start_php($fullname);
			// 写记录
			$logstr = "echo '[ ".date('Y-m-d H:i:s')." ] [ ".$filename." ] has been restarted ...' >> " . $this->dir . "/restart_log.log";
			exec($logstr);
			return true;
		}
	}
	public function start_php($fullname){
		$cmd = "nohup /usr/local/php/bin/php " . $fullname . ' > /dev/null 2>&1 &';
		exec($cmd);
	}
}
// ----------------------------------------------------------------------------------------------
$file_arr = [
	'script_new_promote_bonus.php',
	'script_new_promote_count.php',
	'script_new_promote_game.php',
	'script_new_bonus_num.php',
];
$dirname = '/mydata/data/mainscript/script_api';
$handller = new checkProcess($dirname);
while(true){
	if(!empty($file_arr)){
		foreach($file_arr as $val){
			$handller->check_php($val);
		}
	}
	sleep(600);
}



