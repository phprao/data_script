<?php
	/*
	 *  @desc:   任务实现
	 *  @author: xxm
	 *  @email:  237886849@qq.com
	 *  @note:   所有文件命名以小写，所有子类名以小写
	 *		     
	 */
	class basictaskimpl implements basictask{
		protected $m_action = 'nuknow';
		protected $m_fields = null;				//map<keys,fields>
		protected $m_where = null;
		protected $m_data = null;
		protected $m_default = null;
		protected $m_types = null;
		protected $m_table_name = '';			//表名   	   
		protected $m_primary_keys = '';			//唯一id keys  
		protected $m_redis_keys = '';			//(redis hash为keys_name root)
		protected $m_redis_fields = '';			//(redis 为hash fields_name)

		public function __construct() {
			$this->m_fields = array();
			$this->m_where = array();
			$this->m_default = array();
			$this->m_types = array();
			//<key,mysql_fields>
			//$this->m_fields['player_id'] = 'player_id';
			//$this->m_fields['player_name'] = 'player_name';
		}

		public function on_data_task(basicmysql $db,basicmodel $model = null,$param,$default) {
			
		}

		public function on_redis_task(basicredis $redis,basicmodel $model = null,$param,$default) {
			
		}

		public function select_task_database(basicserver $server) {
			if(is_null($server)) {
				return false;
			}
			//$server->select_database('mysql');
			return true;
		}

		public function select_task_redis(basicserver $server) {
			if(is_null($server)) {
				return false;
			}
			//$server->select_redis('redis_user');
			return true;
		}

		public function set_fields_default($keys,$fields,$default,$type_value = null) {
			if(is_null($this->m_fields) || is_null($this->m_default) || is_null($this->m_types)) {
				return false;
			}
			$this->m_fields[$keys] = $fields;
			$this->m_default[$keys] = $default;
			
			if(is_null($type_value)) {
				if(is_int($default)) {
					$this->m_types[$keys] = 'int';
				}else if( is_bool($default)) {
					$this->m_types[$keys] = 'bool';
				}else if( is_string($default)) {
					$this->m_types[$keys] = 'string';
				}else if( is_array($default)) {
					$this->m_types[$keys] = 'array';
				}else if( is_object($default)) {
					$this->m_types[$keys] = 'object';
				}
			}else {
				$this->m_types[$keys] = $type_value;
			}

			return true;
		}

		public function set_fields_invalid($keys) {		
			if(is_null($keys)) {
				return false;
			}
			unset($this->m_fields[$keys]);
			unset($this->m_default[$keys]);
			unset($this->m_types[$keys]);
		}

		public function set_data_table_info($table_name, $primary_keys) {
			if(!is_null($table_name) && is_string($table_name)) {
				$this->m_table_name = $table_name;
			}
			if(!is_null($primary_keys) && is_string($primary_keys)) {
				$this->m_primary_keys = $primary_keys;
			}
			return true;
		}

		public function set_redis_keys_info($keys_name,$hash_fields) {
			if(!is_null($keys_name) && is_string($keys_name)) {
				$this->m_redis_keys = $keys_name;
			}
			if(!is_null($hash_fields) && is_string($hash_fields)) {
				$this->m_redis_fields = $hash_fields;
			}
			return true;
		}
		
		public function parse_model(basicmodel $model = null,array $data) {
			return $this->parse_data($model,$data,$this->m_fields);
		}
		
		public function format_model(basicmodel $model) {
			return $this->format_data($model,$this->m_fields);
		}

		public function format_model_fields(basicmodel $model) {
			return $this->format_data($model,$this->m_fields, false);
		}

		public function format_response(basicmodel $model) {
			return $this->format_model_response($model,$this->m_fields);
		}

		public function set_action($action) {
			if(is_null($action)) {
				return false;
			}

			$this->m_action = $action;
			return true;
		}

		protected function check_keys_invalid($key) {
			if(!isset($this->m_fields[$key])) {
				return true;
			}
			return false;
		}

		protected function filter_keys_where($where) {
			$data = array();
			if(is_null($where)) {
				return $data;
			}

			if(!is_array($where)) {
				return $data;
			}
			//
			foreach ($where as $key => $value) {
				if($this->check_keys_invalid($key)) continue;
				$data[$this->m_fields[$key]] = $value;
			}
			return $data;
		}

		public function append_where($where) {
			/*if(is_null($where)) {
				return false;
			}

			if(!is_array($where)) {
				return false;
			}
			//
			$data = array();
			foreach ($where as $key => $value) {
				if($this->check_keys_invalid($key)) continue;//if(!isset($this->m_fields[$key])) continue;
				$data[$this->m_fields[$key]] = $value;
			}
			*/
			$data = $this->filter_keys_where($where);
			$this->m_where = array_merge($this->m_where,$data);
			return true;
		}

		//=======================================================================
		////model对象格式化到 array()  <keys,value> 用于协议响应
		protected function format_model_response(basicmodel $model,array $key_fields) {
			$data = array();
			foreach ($key_fields as $key => $fields) {
				$value = $model->get($key,null);
				if(is_null($value)) continue;
				$data[$key] = $value;
			}
			foreach ($key_fields as $key => $fields) {
				if(isset($data[$key])) continue;
				if(!isset($this->m_default[$key])) continue;
				$data[$key] = $this->m_default[$key];
			}
			return $data;
		}
		//model对象格式化到 array()  <fields,value>  用于mysql
		protected function format_data(basicmodel $model,array $key_fields, $all_fields = null) {
			$data = array();			
			foreach ($key_fields as $key => $fields) {
				$value = $model->get($key,null);
				if(is_null($value)) continue;
				$data[$fields] = $value;
			}
			if(!is_null($all_fields) && !$all_fields) {
				return $data;
			}

			foreach ($key_fields as $key => $fields) {
				if(isset($data[$fields])) continue;
				if(!isset($this->m_default[$key])) continue;
				$data[$fields] = $this->m_default[$key];
			}
			return $data;
		}

		//array格式化到model对象<key,value>			
		protected function parse_data(basicmodel $model=null,array $data,array $key_fields, $all_fields = null) {
			if(is_null($data) || is_null($key_fields)) {
				return null;
			}
			/*
			var_dump($model);
			$model_obj = $model;
			if(is_null($model_obj)) {
				$model_obj = new basicmodelimpl();
			}
			*/
			$model_obj = new basicmodelimpl();
			if(!is_null($model)) {
				$model->copy($model_obj);
			}
			
			
			$fields_key = array_flip($key_fields);
			foreach ($data as $fields => $value) {
				if(!isset($fields_key[$fields])) continue;
				$value = $this->transvalue($fields_key[$fields],$value);
				$model_obj->insert($fields_key[$fields],$value);
			}

			if(!is_null($all_fields) && !$all_fields) {
				return $model_obj;
			}

			foreach ($key_fields as $key => $fields) {
				if(isset($data[$fields])) continue;
				if(!isset($this->m_default[$key])) continue;
				$model_obj->insert($key,$this->m_default[$key]);
			}

			return $model_obj;
		}
		

		protected function transvalue($keys,$value) {
			if(is_null($keys) || !isset($this->m_types[$keys])) {
				return $value;
			}

			$type_name = $this->m_types[$keys];
			if('int' == $type_name) {
				return (int)$value;
			}else if('bool' == $type_name) {
				return (bool)$value;
			}else if( 'string' == $type_name) {
				return (string)$value;
			}else if( 'array' == $type_name) {
				return (array)$value;
			}else if( 'object' == $type_name) {
				return (object)$value;
			}else if('float' == $type_name) {
				return (float)$value;
			}else if('double' == $type_name) {
				return (double)$value;
			}else if('real' == $type_name) {
				return (real)$value;
			}
			return $value;
		}

		//-------database transalation begin----------
		protected function commit_transaction(basicmysql $db) {
			if(is_null($db)) return false;

			$sql = "COMMIT";
			$db->query($sql);
			return true;
		}

		protected function rollback_transaction(basicmysql $db) {
			if(is_null($db)) return false;

			$sql = "ROLLBACK";
			$db->query($sql);
			return true;
		}

		protected function start_transaction(basicmysql $db) {
			if(is_null($db)) return false;

			$sql = "START TRANSACTION";//begin work
			$db->query($sql);
			return true;
		} 
		//-------database transalation end----------

		//+++++++++++++database lock begin+++++++++++++
		protected function lock(basicmysql $db) {
			if(is_null($db)) return false;
			
			$sql = 'LOCK TABLES '.$this->m_table_name.' WRITE';
			$db->query($sql);
			return true;
		}

		protected function unlock(basicmysql $db) {
			if(is_null($db)) return false;

			$db->query('UNLOCK TABLES');
			return true;
		}
		//+++++++++++++database lock end+++++++++++++

	}
?>