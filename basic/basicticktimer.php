<?php
	/*
	 *  @desc:   基本计数计
	 *  @author: xxm
	 *  @email:  237886849@qq.com
	 *  @note:   所有文件命名以小写，所有子类名以小写
	 *		     
	 */
	class basicticktimer {
		protected $m_begin_time = 0;
		protected $m_end_time = 0;
		protected $m_time_out = 1;
		protected $m_event = null;
		protected $m_param = '';

		public function __construct(basicevent $event = null, $param = null,$time_out = 1) {
			$this->m_begin_time = $this->begin_tick();
			$this->m_event = $event;

			if(!is_null($param)) {
				$this->m_param = $param;
			}
			if(!is_null($time_out )) {
				$this->m_time_out = $time_out;
			}
		}
		public function __destruct() {
			$this->check_use_time();
		}

		protected function begin_tick() {
	        $mt_time = explode(' ', microtime());
	        $time_value = $mt_time[1] + $mt_time[0];
	        return $time_value;
	    }

	    protected function check_use_time() {
	        $end_time = $this->begin_tick();
	        $use_time = $end_time - $this->m_begin_time;

	        if ($use_time >= $this->m_time_out) {
	            $msg = "time out (" . $use_time . ")";
	            $this->printf_action_info($msg);
	        }
	    }

	    protected function printf_action_info($msg) {
	    	if(is_null($this->m_event)) {
	    		if(!is_string($this->m_param)) {
	    			$this->m_param = '';
	    		}
	    		BASIC_LOG_WARNING($this->m_target, 'time_out: %s : %s', $msg, $this->m_param);
	    	}else {
	    		$this->m_event->on_event('tick_timer',$this->m_param,$msg,null);
	    	}
	    }
	}
?>