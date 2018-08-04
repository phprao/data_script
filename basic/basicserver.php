<?php
	/*
	 *  @desc:   所有basictask执行器
	 *  @author: xxm
	 *  @email:  237886849@qq.com
	 *  @note:   所有文件命名以小写，所有子类名以小写
	 *		     
	 */
	class basicserver {
		protected $m_app = null;
		protected $m_debug = false;

		protected $m_db = null;
		protected $m_mysql_name = 'mysql';
		protected $m_db_cache = null;

		protected $m_redis = null;
		protected $m_redis_cache = null;
		protected $m_redis_name = 'redis_user';

		public function __construct($debug = null) {
			$this->m_app = app();
			$this->m_db_cache = array();
			$this->m_redis_cache = array();
			if(!is_null($debug)) {
				$this->m_debug = $debug;
			}
		}

		public function __destruct() {
			$this->release_database();
			$this->release_redis();
		}

		/*
		 *  @desc: 处理data
		 *	@param1: task
		 *	@param2: model
		 *	@param3: param 扩展参数
		 *  @param4: 默认值
		 */
		public function process_database(basictask $task,basicmodel $model=null,$param,$default) {
			
			if(is_null($task)) {
				return $default;
			}
			$task->select_task_database($this);
			$db = $this->get_database();
			$result = $task->on_data_task($db,$model,$param,$default);
			if(is_null($result)) {
				return  $default;
			}
			return $result;
		}
		/*
		 *  @desc: 处理redis
		 *	@param1: task
		 *	@param2: model
		 *	@param3: param 扩展参数
		 *  @param4: 默认值
		 */
		public function process_redis(basictask $task,basicmodel $model=null,$param,$default) {
			if(is_null($task)) {
				return $default;
			}
			$task->select_task_redis($this);
			$redis = $this->get_redis();
			$result = $task->on_redis_task($redis,$model,$param,$default);
			if(is_null($result)) {
				return  $default;
			}
			return $result;
		}

		//==========================================database begin============================
		protected function get_database() {
			if(is_null($this->m_app)) {
				return null;
			}
			
			if(is_null($this->m_db)) {
				$this->select_database($this->m_mysql_name);
			}
			
			return $this->m_db;
		}

		public function select_database($mysql_name) {
			if(is_null($mysql_name)) {
				return false;
			}

			if($mysql_name === $this->m_mysql_name && !is_null($this->m_db)) {
				return true;
			}
			if(isset($this->m_db_cache[$mysql_name])) {
				$this->m_mysql_name = $mysql_name;
				$this->m_db = $this->m_db_cache[$mysql_name];
				return true;
			}
			$data = $this->m_app->m_config->get($mysql_name,array());
			if(is_null($data)) {
				return false;
			}
			$config = array();
			$config['host'] = $data['mysql_host'];
			$config['port'] = $data['mysql_port'];
			$config['username'] = $data['mysql_root'];
			$config['password'] = $data['mysql_psw'];
			$config['charset'] = $data['mysql_charset'];
			$config['dbname'] = $data['mysql_name'];
			//var_dump($config);
			$database = new basicmysql();
			$database->set_debug($this->m_debug);
			if(!$database->connect($config)) {
				$database = null;
				return false;
			}
			$this->m_mysql_name = $mysql_name;
			$this->m_db = $database;
			$this->m_db_cache[$mysql_name] = $database;
			//$this->m_db->set_debug(true);
			return true;
		}

		protected function release_database() {
			foreach ($this->m_db_cache as $key => $db) {
				$db->close();
			}

			$this->m_db_cache = array();
		}

		//==========================================database end============================

		//==========================================redis begin==========================
		public function get_redis() {
			if(is_null($this->m_app)) {
				return null;
			}
			
			if(is_null($this->m_redis)) {
				$this->select_redis($this->m_redis_name);
			}
			
			return $this->m_redis;
		}

		public function select_redis($redis_name) {
			if(is_null($redis_name)) {
				return false;
			}

			if($redis_name === $this->m_redis_name && !is_null($this->m_redis)) {
				return true;
			}

			if(isset($this->m_redis_cache[$redis_name])) {
				$this->m_redis_name = $redis_name;
				$this->m_redis = $this->m_redis_cache[$redis_name];
				return true;
			}

			$data = $this->m_app->m_config->get($redis_name,array());
			if(is_null($data)) {
				return false;
			}
			$config = array();
			$config['host'] = $data['redis_host'];
			$config['port'] = $data['redis_port'];
			$config['username'] = $data['redis_root'];
			$config['password'] = $data['redis_psw'];
			$config['dbname'] = $data['redis_name'];
			//var_dump($config);
			$redis = new basicredis();
			$redis->set_debug($this->m_debug);
			if(!$redis->connect($config)) {
				$redis = null;
				return false;
			}
			$this->m_redis_name = $redis_name;
			$this->m_redis = $redis;
			$this->m_redis_cache[$redis_name] = $redis;
			return true;
		}

		protected function release_redis() {
			foreach ($this->m_redis_cache as $key => $redis) {
				$redis->close();
			}

			$this->m_redis_cache = array();
		}

		//==========================================redis end============================
	}
?>