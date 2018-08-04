<?php
	header('content-type:text/html; charset=utf-8');
	//api根目录
	defined('BASIC_API_ROOT') || define('BASIC_API_ROOT', dirname(__FILE__));
	require_once(BASIC_API_ROOT.DIRECTORY_SEPARATOR.'basicloader.php');
	
	/*
	 *  @desc:   基本接口
	 *  @author: xxm
	 *  @email:  237886849@qq.com
	 *  @note:   error code before 20001 is basic api use;
	 *		     所有文件命名以小写，所有子类名以小写
	 */
	class apibasic {
		protected $m_packet;			//request basicpackage
		protected $m_param;				//
		protected $m_debug = false;		//
		protected $m_target = "apibasic";
		
		public function __construct($debug = null) {
			$this->m_packet = new basicpackage($debug);
			$this->m_param = null;
			if(!is_null($debug)) {
				$this->m_debug = $debug;
			}
		}

		public function __destruct() {

		}
		/*
		 *  @desc 初始化
		 */
		protected function init() {
			if(isset($_REQUEST[actioncode::$param_name])){
				//$this->m_request = json_decode(stripslashes($_REQUEST[actioncode::$param_name]),TRUE);
				$this->m_param = $_REQUEST[actioncode::$param_name];
				if($this->m_debug) {
					//$this->log_warning('request = '.$this->m_param);
					BASIC_SYS_LOG_WARNING('action',$this->m_target,'%s','request = '.$this->m_param);
				}
				return true;
			}
			return false;
		}
		/*
		 *  @desc 反始化
		 */
		protected function release() {
		}
		/*
		 *  @desc 在逻辑处理之前的操作
		 */
		protected function before() {
			if(is_null($this->m_packet)) {
				return false;
			}
			return $this->m_packet->parse_packet($this->m_param);
		}
		
	
		 /*
		 *  @desc 逻辑处理之后的操作
		 */
		protected function after() {
			if(is_null($this->m_packet)) {
				return false;
			}
			$result = $this->m_packet->format_packet();
			//
			echo $result;

			if($this->m_debug) {
				//$this->log_warning('response = '. $result);
				BASIC_SYS_LOG_WARNING('action',$this->m_target,'%s','response = '. $result);
			}
		}
        
		
		/*
		 *  @desc 逻辑处理
		 */
		protected function logic(basicdi $app) {
			
		}
		
		/*
		 *  @desc 执行逻辑处理
		 */
		public function action(basicdi $app) {
			try {
				$this->init();
			
				if ($this->before()) {
	                $this->logic($app);
					$this->after();
				}else{
					$this->action_error();
					$this->after();
				}
				$this->release();
			}catch (Exception $e) { 
				$this->after();
			}
		}
		
		protected function error($m_code,$m_desc) {
			$this->set_response_status($m_code,$m_desc,false);
		}

		private function action_error() {
			$this->error(actioncode::$basic_api_request_action_param_code,'非法请求');
		}
        
		////////////////////////////////////////////////////////////////////////////
		//log opration
		protected function log_debug($msg) {
			BASIC_LOG_DEBUG($this->m_target,'%s',$msg);//Config::$logger->debug($this->m_target, $msg);
		}
		protected function log_info($msg) {
			BASIC_LOG_INFO($this->m_target,'%s',$msg);//Config::$logger->info($this->m_target, $msg);
		}
		protected function log_warning($msg) {
			BASIC_LOG_WARNING($this->m_target,'%s',$msg);//Config::$logger->warn($this->m_target, $msg);
		}
		protected function log_error($msg) {
			BASIC_LOG_ERROR($this->m_target,'%s',$msg);//Config::$logger->error($this->m_target, $msg);
		}
		protected function log_fatal($msg) {
			BASIC_LOG_FATAL($this->m_target,'%s',$msg);//Config::$logger->fatal($this->m_target, $msg);
		}
		

        //设置response状态
        public function set_response_status($code,$desc,$reset = true) {
            if(is_null($this->m_packet)) {
                return false;
            }
            //var_dump($code);
            $ret = $this->m_packet->set_status($code,$desc,$reset);
            if(0 != $code && $this->m_log && $ret) {
                 BASIC_LOG_WARNING($this->m_target,'%s','code('.$code.') desc('.$desc.')');
            }
            return true;
        }

        //检测签名
        public function check_sign($sign_value) {
        	if(is_null($this->m_packet)) {
                return false;
            }
            return $this->m_packet->check_sign($sign_value);
        }
		
	}
?>