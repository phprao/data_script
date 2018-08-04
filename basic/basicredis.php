<?php
	/*
	 *  @desc:   基本redis接口
	 *  @author: xxm
	 *  @email:  237886849@qq.com
	 *  @note:   所有文件命名以小写，所有子类名以小写
	 *		     
	 */
	class basicredis implements basicevent{
		protected $m_host = '';
		protected $m_port = 80;
		protected $m_root = '';
		protected $m_paasword = '';
		protected $m_db = 0;
        protected $m_debug = false;

		protected $m_redis = null;

		public function __construct() {
			$this->m_redis  = new redis();
            $this->m_debug = false;
    	}

        public function set_debug($debug_flags) {
            if(is_null($debug_flags)) {
                return false;
            }
            $this->m_debug = $debug_flags;
            return true;
        }

    	public function connect($config) {
    		if(is_null($config) || is_null($this->m_redis) || !is_array($config)) {
    			return false;
    		}

    		if(!isset($config['host']) ||
    			!isset($config['port']) ||
    			!isset($config['username']) ||
    			!isset($config['password']) ||
    			!isset($config['dbname'])) {
    			return false;
    		}

    		$this->m_host = $config['host'];
    		$this->m_port = $config['port'];
    		$this->m_root = $config['username'];
    		$this->m_paasword = $config['password'];
    		$this->m_db = $config['dbname'];

    		return $this->reconnect();//$config['host'],$config['port'],$config['username'],$config['password'],$config['dbname']
    	}

    	protected function reconnect() {//$host,$port,$root,$password,$db
    		if(is_null($this->m_redis) ) {
    			return false;
    		}

    		if(!$this->m_redis->connect($this->m_host,$this->m_port)) {
    			BASIC_SYS_LOG_ERROR('redis','basicredis','%s','redis connect error');
    			return false;
    		}

    		if(!$this->m_redis->auth($this->m_paasword)) {
    			BASIC_SYS_LOG_ERROR('redis','basicredis','%s','redis auth error');
    			return false;
    		}

    		if(!$this->m_redis->select($this->m_db)) {
    			BASIC_SYS_LOG_ERROR('redis','basicredis','%s','redis select error');
    			return false;
    		}
    		/*
    		$this->m_host = $host;
    		$this->m_port = $port;
    		$this->m_root = $root;
    		$this->m_paasword = $password;
    		$this->m_db = $db;
    		*/
    		return true;
    	}

    	public function select_redis($db) {
    		if(is_null($this->m_redis) ) {
    			return false;
    		}
    		if(!$this->m_redis->select($db) || !$this->reconnect()) {
    			return false;
    		}

    		if(!$this->m_redis->select($db)) {
    			return false;
    		}
    		$this->m_db = $db; 
    		return true;
    	}

    	public function get_hash_value($hask_keys, array $filds = null,$default = null) {
    		if(is_null($this->m_redis) || is_null($hask_keys) ) {
    			return $default;
    		}

            $msg = 'get_hash_value = '.$hask_keys;
            $this->printf_info($msg);
            $tick = new basicticktimer($this,$msg);
    		if(is_null($filds)) {
                //if(!$this->is_exist($hask_keys)) {
                //    return $default;
                //}
    			return $this->m_redis->hGetAll($hask_keys);
    		}else {
    			if(!is_array($filds)) {
    				$filds = array($filds);
    			}
                //if(!$this->is_hexists($hask_keys,$filds)) {
                //    return $default;
                //}
    			return $this->m_redis->hMget($hask_keys,$filds);
    		}
    	}

    	public function set_hash_value($hask_keys,$data) {
    		if(is_null($this->m_redis) || is_null($hask_keys) || is_null($data) || !is_array($data)) {
    			return false;
    		}
            $msg = 'set_hash_value = '.$hask_keys;
            $this->printf_info($msg);
            $tick = new basicticktimer($this,$msg);
    		return $this->m_redis->hMset($hask_keys,$data);
    	}


    	public function get_value($keys, $filds,$default) {
    		if(is_null($this->m_redis) || is_null($keys) || is_null($filds) || is_array($filds)) {
    			return $default;
    		}
            $msg = 'get_value = '.$keys . ' filed = ' .$filds;
    		$this->printf_info($msg );
            $tick = new basicticktimer($this,$msg);
            //if(!$this->is_hexists($keys,$filds)) {
            //    return $default;
            //}
    		return $this->m_redis->hGet($keys,$filds);
    	}

    	public function set_value($keys, $filds, $value) {
    		if(is_null($this->m_redis) || is_null($keys) || is_null($filds) || is_array($filds) || is_null($value) || is_array($value)) {
    			return $default;
    		}
            $msg = 'set_value = '.$keys . ' filed = ' .$filds;
            $this->printf_info($msg);
            $tick = new basicticktimer($this,$msg);
    		return $this->m_redis->hSet($keys,$filds,$value);
    	}

    	public function is_exist($keys) {
			if(is_null($this->m_redis) || is_null($keys)) {
				return false;
			}
            $msg = 'is_exist = '.$keys;
            $this->printf_info($msg);
            $tick = new basicticktimer($this,$msg);
			return $this->m_redis->exists($keys);
    	}

    	public function is_hexists($hask_keys,$filds) {
    		if(is_null($this->m_redis) || is_null($hask_keys) || is_null($filds)) {
				return false;
			}
            $msg = 'is_hexists = '.$hask_keys . ' fields = '.$filds;
            $this->printf_info($msg);
            $tick = new basicticktimer($this,$msg);
    		return $this->m_redis->hExists($hask_keys,$filds);
    	}

    	public function del_key($keys) {
    		if(is_null($this->m_redis) || is_null($keys)) {
				return false;
			}
            $msg = 'del_key = '.$keys;
            $this->printf_info($msg);
            $tick = new basicticktimer($this,$msg);
			return $this->m_redis->del($keys);
    	}

    	public function get_keys($keys,$default) {
    		if(is_null($this->m_redis) || is_null($keys)) {
				return $default;
			}
            $msg = 'get_keys = '.$keys;
            $this->printf_info($msg);
            $tick = new basicticktimer($this,$msg);
			return $this->m_redis->keys($keys);
    	}

        //$iter = null; $data = scan_keys($iter,'club_desk:*',50);
        public function scan_keys($iter,$pattern,$count) {
            $data = array();
            $data['iter'] = 0;
            $data['list'] = array();
            if(is_null($this->m_redis)) {
                return $data;
            }
            $msg = 'scan iter = null pattern = '. $pattern . ' count = '.$count;
            if(!is_null($iter)) {
                $msg = 'scan iter = '.$iter .' pattern = '. $pattern . ' count = '.$count;
            }
            $this->printf_info($msg);
            //设置遍历的特性为不重复查找，该情况下扩展只会scan一次，所以可能会返回空集合
            $this->m_redis->setOption(Redis::OPT_SCAN, Redis::SCAN_NORETRY);  
            //$count = 50,每次遍历50条，注意是遍历50条，遍历出来的50条key还要去匹配你的模式，所以并不等于就能够取出50条key  
            do {
                $keys_list = $this->m_redis->scan($iter, $pattern, $count);
                if(false == $keys_list) {
                    $iter = 0;
                    $keys_list = $this->get_keys($pattern,$data['list']);//var_dump($keys_list);
                }
                if(false == $keys_list || is_null($keys_list)) continue;
                foreach ($keys_list as $key) {
                    array_push($data['list'], $key);
                }
            }while($iter > 0);//每次调用 Scan会自动改变 $it 值，当$it = 0时 这次遍历结束 退出循环 

            return $data;
        }

    	public function incr_value($keys,$filds,$value,$default = 0) {
    		if(is_null($this->m_redis) || is_null($filds) || is_null($keys)) {
				return $default;
			}

			if(is_null($value) || is_array($value)) {
                $msg = 'incr_value = '.$keys;
                $this->printf_info($msg);
                $tick = new basicticktimer($this,$msg);
				return $this->m_redis->incr($keys);
			}
            $msg = 'incr_value = '.$keys .' field = '. $filds . ' value = '.$value;
            $this->printf_info($msg);
            $tick = new basicticktimer($this,$msg);
			return $this->m_redis->hIncrBy($keys,$filds,$value);
    	}

    	public function close() {
    		if(is_null($this->m_redis)) {
    			return false;
    		}
    		$this->m_redis->close();
    		return true;
    	}

        protected function printf_info($msg) {
            if(!$this->m_debug) return;
            BASIC_SYS_LOG_DEBUG('redis','basicredis','%s', 'database('.$this->m_db.') '. $msg);
        }

        public function on_event($action, $object, $in_buf, $out_buf){
            $msg = $in_buf;
            if(!is_null($object) && is_string($object)) {
                $msg = $in_buf . ' ' . $object;
            }
            BASIC_SYS_LOG_WARNING('redis','basicredis','%s', $msg);
        }
	}
?>