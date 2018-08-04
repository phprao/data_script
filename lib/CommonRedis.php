<?php
class CommonRedis {

	public $handler;
	
	function __construct($config) {
		$this->initDataRedis($config);
	}
	
	public function initDataRedis($config) {
		$this->handler = new redis();
		$ret = $this->handler->connect($config['host'], $config['port']);
		if (!$ret) {
			return false;
		}
		$ret = $this->handler->auth($config['pass']);
		if (!$ret) {
			return false;
		}
		$ret = $this->handler->select($config['db']);
		return true;
	}

	public function deinitDataRedis() {
		$this->handler->close();
	}
	
	
}
