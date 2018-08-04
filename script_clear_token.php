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
require('lib/redis.php');
require('lib/logger.php');

class Token
{
	private $redis;
	private $logs;
	private $tag = ['admin:login:token','h5:login:token'];
	private $logTag = 'clear_token';

	public function __construct(){
		$this->logs    	= new Logger();
	}

	public function main(){
		$start = time();
		
		$this->redis = new RedisDriver(0);
		foreach($this->tag as $tag){
			$prefix =  $tag . ':*';
			$tokens = $this->redis->handler->keys($prefix);
			while(count($tokens))
			{
				$key = array_pop($tokens);
				// 访问的时候，如果key过期，redis会自动清除该key
				$this->redis->handler->get($key);
			}
		}

		$this->redis->deinitDataRedis();

		$end = time();
		$this->logs->info($this->logTag,'耗时: ' . ($end - $start) . ' seconds');
	}
}


function main_run() {
	while(true)
	{
		$Token = new Token();
		$Token->main();
		sleep(3600);
	}
}

main_run();
