<?php
/**
 +---------------------------------------------------------- 
 * date: 2018-05-03 11:08:42
 +---------------------------------------------------------- 
 * author: Raoxiaoya
 +---------------------------------------------------------- 
 * describe: 用户金币数回写到数据库
 +---------------------------------------------------------- 
 */

set_time_limit(0);

require('lib/config.php');
require('lib/mysql.php');
require('lib/redis.php');
require('lib/logger.php');

class Player
{
	private $mysql;
	private $redis;
	private $mod;
	private $logs;
	private $tag = 'user_info';
	private $game = 'user_status';
	private $logTag = 'player_info';
	private $time_limit = 604800; // 一周

	public function __construct(){
		$this->mysql   	= new MysqlDriver(Config::$mysql_config);
		$this->mod    	= Config::$mod;
		$this->logs    	= new Logger();
	}

	public function main(){
		$start = time();
		for($i = 0; $i < $this->mod; $i++){
			$this->redis = new RedisDriver($i);
			$prefix =  $this->tag . ':*';
			$players = $this->redis->handler->keys($prefix);
			while(count($players))
			{
				$key = array_pop($players);
				// 需回写的字段
				$fields = ['player_coins', 'player_login_time', 'player_lottery'];
				$result = $this->redis->handler->hMget($key, $fields);
				$array = explode(':', $key);
				$sql = "select * from dc_player_info where player_id = " . $array[2];
				$info = $this->mysql->query($sql);
				if(!$info){
					$this->logs->error($this->logTag,'玩家' . $array[2] . '不存在');
				}else{
					foreach($result as $key => $val){
						$sql = "update dc_player_info set " . $key . " = " . $val . " where player_id = " . $array[2];
						$this->mysql->query($sql);
					}
		  			
		  			if((time() - $result['player_login_time']) > $this->time_limit){
		  				// 判断是否在游戏里
		  				$key_game = str_replace($this->tag, $this->game, $key);
		  				if(!$this->redis->handler->exists($key_game)){
		  					$this->redis->handler->delete($key);
		  				}else{
		  					$this->redis->handler->hset($key,'player_login_time',time());
		  				}
		  			}
				}
			}

			$this->redis->deinitDataRedis();
		}

		$end = time();
		$this->logs->info($this->logTag,'耗时: ' . ($end - $start) . ' seconds');
	}
}


// 玩家信息回写到数据库 
function main_run() {
	while(true)
	{
		$Player = new Player();
		$Player->main();
		sleep(5);
	}
}

main_run();
