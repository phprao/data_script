<?php
class RedisDriver {

	public $handler;
	
	function __construct($index = 0) {
		$this->initDataRedis($index);
	}
	
	public function initDataRedis($uid) {
		$db = $uid % Config::$mod;
		$redis_config = Config::$redis_user_info;
		$this->handler = new redis();
		$ret = $this->handler->connect($redis_config['host'], $redis_config['port']);
		if (!$ret) {
			return false;
		}
		$ret = $this->handler->auth($redis_config['pass']);
		if (!$ret) {
			return false;
		}
		$ret = $this->handler->select($db);
		return true;
	}

	public function deinitDataRedis() {
		$this->handler->close();
	}
	
	
}
