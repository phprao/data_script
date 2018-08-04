<?php
	class mainscriptredistask extends basicredistask {
		protected $m_db_index = 0;

		public function __construct() {
			parent::__construct();
			//特有
			//表
			$this->set_redis_keys_info('gamerecord:','game_record_player_id');
			$this->set_redis_name('redis_game');
			//$this->set_redis_name('redis_user');
			$this->set_redis_database_model(2,false);

		}

		public function select_redis_database($index) {
			$this->m_db_index = $index;
		}

		public function on_redis_task(basicredis $redis,basicmodel $model = null,$param,$default) {
			$redis->select_redis($this->m_db_index);

			if('scan_money_keys' == $this->m_action) {
				return $this->scan_money_keys($redis,$model,$param, $default);
			}else if('scan_keys' == $this->m_action) {
				return $this->scan_keys_list($redis,$model,$param, $default);
			}

			return $this->on_redis_task($redis,$model,$param, $default);
		}

		protected function scan_money_keys(basicredis $redis,basicmodel $model = null,$param, $default) {
			$data = array();
			$data['iter'] = $model;
			$data['pattern'] = 'coin_change_record:*';
			$data['count'] = 50;

			return $this->scan_keys($redis,$model,$data, $default);
		}

		protected function scan_keys_list(basicredis $redis,basicmodel $model = null,$param, $default) {
			$data = array();
			$data['iter'] = $model;
			//$data['pattern'] = 'coin_change_record:*';
			$data['pattern'] = $param;
			$data['count'] = 50;

			return $this->scan_keys($redis,$model,$data, $default);
		}
	}
?>