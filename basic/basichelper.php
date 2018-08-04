<?php
	
	/*
	 *  @desc:   基本接口
	 *  @author: xxm
	 *  @email:  237886849@qq.com
	 *  @note:   所有文件命名以小写，所有子类名以小写
	 *		     
	 */
	class basichelper {
		
		private static function check_param() {
			if(!isset($_REQUEST['param'])){
				return false;
			}else {
				return true;
			}
		}
		public static function get_action_name() {
			if(!basichelper::check_param()) {
				return NULL;
			}else{
				//接收json数据并重新赋值
				$param = json_decode($_REQUEST[actioncode::$param_name],TRUE);
				if(!is_array($param) || !isset($param[actioncode::$action_name]) || empty($param[actioncode::$action_name]) || is_null($param[actioncode::$action_name])){
					return NULL;
				}else{
					return $param[actioncode::$action_name];
				}
			}
		}
		
		public static function get_action_version() {
			if(!basichelper::check_param()) {
				return NULL;
			}else{
				//接收json数据并重新赋值
				$param = json_decode($_REQUEST[actioncode::$param_name],TRUE);
				if(!is_array($param) || !isset($param[actioncode::$version_name]) || empty($param[actioncode::$version_name]) || is_null($param[actioncode::$version_name]) ){
					return NULL;
				}else{
					return $param[actioncode::$version_name];
				}
			}
		}
		
		public static function get_action_flags() {
			if(!basichelper::check_param()) {
				return NULL;
			}else{
				//接收json数据并重新赋值
				$param = json_decode($_REQUEST[actioncode::$param_name],TRUE);
				if(!is_array($param) || !isset($param[actioncode::$flag_name]) || empty($param[actioncode::$flag_name]) || is_null($param[actioncode::$flag_name]) ){
					return NULL;
				}else{
					return $param[actioncode::$flag_name];
				}
			}
		}

		public static function get_action_sign() {
			if(!basichelper::check_param()) {
				return NULL;
			}else{
				//接收json数据并重新赋值
				$param = json_decode($_REQUEST[actioncode::$param_name],TRUE);
				if(!is_array($param) || !isset($param[actioncode::$sign_name]) || empty($param[actioncode::$sign_name]) || is_null($param[actioncode::$sign_name]) ){
					return NULL;
				}else{
					return $param[actioncode::$sign_name];
				}
			}
		}
		
		// 包含头文件
		public static function basic_path_format($file) {
			if (!defined("BASIC_ROOT_DIR")) {
				define("BASIC_ROOT_DIR", dirname(__FILE__));	
			}
			return BASIC_ROOT_DIR . '/' . $file;
		}

		public static function basic_make_datetime_to_strtotime($add_day) {
			//$date = new DateTime("$year-$Mon-d");
			$date = new DateTime(date("Y-m-d"));
			if(0 != $add_day) {
				$interval = new DateInterval('P'.$add_day.'D');
				$date -> add ( $interval );
			}
			$str_date = $date->format('Y-m-d');
			return strtotime($str_date);
		}
		
		public static function basic_make_response_data() {
			
			$flag_value = basichelper::get_action_flags();
			if(is_null($flag_value)) {
				$flag_value = 3;
			}
			$sign_value = basichelper::get_action_sign();
			$sign_value = time();
			if(is_null($sign_value)) {
				$sign_value = time();
			}
			
			$key_value = 2;
			$result = array();
			$response = array();
			$result[actioncode::$code_name] = -3;
			$result[actioncode::$desc_name] = 'action exit';
			
			$response[actioncode::$action_name] = basichelper::get_action_name();
			$response[actioncode::$version_name]= basichelper::get_action_version();
			$response[actioncode::$key_name]  = $key_value;
			$response[actioncode::$flag_name] = $flag_value;
			$response[actioncode::$data_name] = $result;
			$response[actioncode::$sign_name] = $sign_value;
			
			if($flag_value & 0x2) {
				$response[actioncode::$flag_name] = 0x2;
				srand((double)microtime()*1000000);
				$rand_number = (int)rand();
				$json_result = json_encode($result);
              
				$response[actioncode::$key_name] = (int)$rand_number;
				$response[actioncode::$data_name] = basicsecurity::encryp_data_ex((int)$response[actioncode::$key_name],$json_result);

			}else {
				$response[actioncode::$flag_name] = 0x0;
			}
			
			$data = json_encode($response);
			return $data;
		}
		
	}
?>