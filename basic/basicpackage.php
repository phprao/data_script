<?php
	/*
	 *  @desc:   数据包
	 *  @author: xxm
	 *  @email:  237886849@qq.com
	 *  @note:   所有文件命名以小写，所有子类名以小写
	 *		     
	 */
	class basicpackage {
		protected $m_data;				//request data
		protected $m_code;				//error code
		protected $m_desc;				//error desc
		protected $m_request;			//request
		protected $m_response;			//response
		protected $m_result;			//response data
		protected $m_debug = false;				//debug
		
		public function __construct($debug = null) {
			$this->m_data = null;
			$this->m_request = null;
			$this->m_response = array();
			$this->m_result = array();
			$this->m_code = actioncode::$basic_api_ok_code;
			$this->m_desc = 'ok';
			if(!is_null($debug)) {
				$this->m_debug = $debug;
			}
			
		}
		//parse packet
		public function parse_packet($request) {
			/*if(!isset($_REQUEST[actioncode::$param_name])){
				return false;
			}
			$this->m_request = json_decode($_REQUEST[actioncode::$param_name],TRUE);
			*/
			if(is_null($request)) {
				return false;
			}
			$this->m_request = json_decode($request,TRUE);
			//action:   	行为(string)
			//version: 		大版本(string)
			//key_value: 	默认0(int)
			//flag_value: 	标志位请求为0响应为1(int)
			//data_value:	业务数据
			//判断是否有action
			if( $this->m_request == NULL || !is_array($this->m_request) ||
				!isset($this->m_request[actioncode::$action_name]) || empty($this->m_request[actioncode::$action_name]) || is_null($this->m_request[actioncode::$action_name]) || 
				!isset($this->m_request[actioncode::$version_name]) || empty($this->m_request[actioncode::$version_name]) || is_null($this->m_request[actioncode::$version_name]) || 
				!isset($this->m_request[actioncode::$key_name]) || empty($this->m_request[actioncode::$key_name]) || is_null($this->m_request[actioncode::$key_name]) || 
				!isset($this->m_request[actioncode::$flag_name]) || empty($this->m_request[actioncode::$flag_name]) || is_null($this->m_request[actioncode::$flag_name]) || 
				!isset($this->m_request[actioncode::$sign_name]) || empty($this->m_request[actioncode::$sign_name]) || is_null($this->m_request[actioncode::$sign_name]) ||
				!isset($this->m_request[actioncode::$data_name])){
				$this->m_code = actioncode::$basic_api_request_action_code;
                $this->m_desc = "参数列表错误";
				return false;
			}
			
			if(!is_string($this->m_request[actioncode::$action_name]) ||
				!is_string($this->m_request[actioncode::$version_name]) ||
				!is_int($this->m_request[actioncode::$key_name]) ||
				!is_int($this->m_request[actioncode::$flag_name]) ||
				!is_int($this->m_request[actioncode::$sign_name])) {
				$this->m_code = actioncode::$basic_api_request_action_type_code;
				$this->m_desc = "参数类型错误";
				return false;
			}

			$sign_value = (int)$this->m_request[actioncode::$sign_name];
			$use_time = abs(time() - $sign_value);
			if($use_time > 300) {//5 * 60 = 5 分钟
				$this->m_code = actioncode::$basic_api_request_action_sign_out;
				$this->m_desc = "签名错误";
				return false;
			}
			
			$flags_value = (int)$this->m_request[actioncode::$flag_name];
			if($flags_value & 0x1 == 0) {
				$this->m_code = actioncode::$basic_api_request_no_action_code;
				$this->m_desc = "非法协议";
				return false;
			}
			$this->m_data = $this->m_request[actioncode::$data_name];
          
			//client encryp data 
			if($flags_value & 0x2) {
				$decryp_data = basicsecurity::decryp_data_ex((int)$this->m_request[actioncode::$key_name],$this->m_data);
				$this->m_data = json_decode($decryp_data,TRUE);
				if($this->m_debug) {
					BASIC_SYS_LOG_WARNING('action','basicpackage', '%s','decryp_data : '. $decryp_data);
				}
			}
			
			return true;
		}
		//format packet
		public function format_packet() {
			$key_value = 2;
			$flag_value = 2;
			$sign_value = time();
			$desc = $this->m_desc;
			if(actioncode::$basic_api_request_action_code == $this->m_code) {
				//Not a legitimate request.
				if(!isset($this->m_request[actioncode::$key_name]) || empty($this->m_request[actioncode::$key_name]) || is_null($this->m_request[actioncode::$key_name])) {
					$desc = $desc . ':key_value is null' ;
				}else if(!is_int($this->m_request[actioncode::$key_name])) {
					$key_value = $this->m_request[actioncode::$key_name];
					$desc = $desc . ':key_value is not int' ;
				}

				if(!isset($this->m_request[actioncode::$flag_name]) || empty($this->m_request[actioncode::$flag_name]) || is_null($this->m_request[actioncode::$flag_name])) {
					$desc = $desc . ':flag_value is null';
				}else if(!is_int($this->m_request[actioncode::$flag_name])) {
					$flag_value = $this->m_request[actioncode::$flag_name];
					$desc = $desc . ':flag_value is not int';
				}

				if(!isset($this->m_request[actioncode::$data_name]) || empty($this->m_request[actioncode::$data_name]) || is_null($this->m_request[actioncode::$data_name])) {
					$desc = $desc . ' ,data_value is null';
				}
				$this->m_desc =  $desc;
			}else if(actioncode::$basic_api_request_action_type_code == $this->m_code) {
				//data type error
				if(is_int($this->m_request[actioncode::$key_name])) {
					$key_value = $this->m_request[actioncode::$key_name];
				}else {
					$desc = $desc . ':key_value is not int';
				}

				if(is_int($this->m_request[actioncode::$flag_name])) {
					$flag_value = $this->m_request[actioncode::$flag_name];
				}else {
					$desc = $desc . ':flag_value is not int';
				}
				$this->m_desc =  $desc;
			}else if(actioncode::$basic_api_request_no_action_code == $this->m_code) {
				$this->m_desc = 'is invalid request';
			}else {
				$key_value = $this->m_request[actioncode::$key_name];
				$flag_value = $this->m_request[actioncode::$flag_name];
				//$sign_value = $this->m_request[actioncode::$sign_name];
			}
			
			$this->m_result[actioncode::$code_name] = $this->m_code;
			$this->m_result[actioncode::$desc_name] = $this->m_desc;
			
			$this->m_response[actioncode::$action_name] = $this->m_request[actioncode::$action_name];
			$this->m_response[actioncode::$version_name]= $this->m_request[actioncode::$version_name];
			$this->m_response[actioncode::$sign_name] = $sign_value;
			$this->m_response[actioncode::$key_name]  = $key_value;
			$this->m_response[actioncode::$flag_name] = $flag_value;
			$this->m_response[actioncode::$data_name] = $this->m_result;
			$flags_value = (int)$this->m_request[actioncode::$flag_name];
			
			if($flags_value & 0x2) {
				$this->m_response[actioncode::$flag_name] = 0x2;
				srand((double)microtime()*1000000);
				$rand_number = (int)rand();
				$json_result = json_encode($this->m_result);
              
				$this->m_response[actioncode::$key_name] = (int)$rand_number;
				$this->m_response[actioncode::$data_name] = basicsecurity::encryp_data_ex((int)$this->m_response[actioncode::$key_name],$json_result);
				if($this->m_debug) {
					BASIC_SYS_LOG_WARNING('action','basicpackage', '%s','encryp_data : '. $json_result);
				}
			}else {
				$this->m_response[actioncode::$flag_name] = 0x0;
			}
			return json_encode($this->m_response);
		}
		
		//get request key=>value
		public function get_request($name,$default) {
			if(is_null($name) || is_null($this->m_data) || !isset($this->m_data[$name]) ) {
				return $default;
			}
			return $this->m_data[$name];
		}
		
		//set response data key=>value
		public function set_response($name,$value) {
			if(is_null($this->m_result) || is_null($name)) {
				return false;
			}
			if(is_array($value) && isset($this->m_result[$name])) {
				$this->m_result[$name] = array_merge($this->m_result[$name],$value);
			}else {
				$this->m_result[$name] = $value;
			}
			
			return true;
		}
		//set response code and desc 
		public function set_status($code,$desc,$reset = true) {
			if(is_null($code) || is_null($desc)) {
				return false;
			}
			
			if(!$reset && $this->m_code != actioncode::$basic_api_ok_code) return false;

			$this->m_code = $code;
			$this->m_desc = $desc;
			return true;
		}
		
		//get request param
		public function get_request_param() {
			return $this->m_request;
		}
		//get reqeust header data
		public function get_request_data() {
			return $this->m_data;
		}

		//有效valid
		public function check_request_valid($keys) {
			if(is_null($keys) || is_null($this->m_data) || !isset($this->m_data[$keys]) ) {
				return false;
			}
			return true;
		}

		//无效invalid
		public function check_request_invalid($keys) {
			if(is_null($keys) || is_null($this->m_data) || !isset($this->m_data[$keys]) ) {
				return true;
			}
			return false;
		}

		//校验sign
		public function check_sign($sign_value) {
			return basicsign::get_instance()->check_sign($this->m_data,$sign_value);
		}
	}
?>