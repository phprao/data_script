<?php
	//header('content-type:text/html; charset=utf-8');
	/*
	 *  @desc:   业务扩展接口
	 *  @author: xxm
	 *  @email:  237886849@qq.com
	 *  @note:   添加mysql http redis 操作
	 *		     所有文件命名以小写，所有子类名以小写
	 */
	class basicaction extends apibasic {
		//protected $m_target = "basicaction";
        protected $m_log = true;
        public function __construct($debug = null) {
            parent::__construct($debug);
            //$this->m_target = __CLASS__;
            $this->get_action_class_name();
        }
        public function format_response_model(basictask $task,$keys,basicmodel $model = null) {
        	if(is_null($keys) || is_null($task) || is_null($model) || is_null($this->m_packet)) {
        		return false;
        	}

        	$data = $task->format_response($model);
			$this->m_packet->set_response($keys,$data);
			return true;
        }

        public function format_response_data($keys,$data) {
        	if(is_null($keys) || is_null($data) || is_null($this->m_packet)) {
        		return false;
        	}

        	$this->m_packet->set_response($keys,$data);
        	return true;
        } 

       	public function format_response_list_model(basictask $task, $keys, array $list = null) {
       		if(is_null($keys) || is_null($list) || is_null($this->m_packet)) {
        		return false;
        	}

        	$data = array();
        	foreach ($list as $key => $model) {
        		$data[$key] = $task->format_response($model);
        	}
        	$this->m_packet->set_response($keys,$data);
       	}

        public function get_request_value($keys,$default) {
        	if(is_null($this->m_packet)) {
        		return $default;
        	}

        	return $this->m_packet->get_request($keys,$default);
        }

        public function check_request_invalid($keys) {
        	if(is_null($this->m_packet)) {
        		return true;
        	}

        	return $this->m_packet->check_request_invalid($keys);
        }

        public function check_player_token(basicdi $app) {
            $player_id = $this->get_request_value('player_id',0);
            $player_token = $this->get_request_value('player_token','');

            $player_model = new playerinfomodel($app);
            $player_model->insert('player_id',$player_id);
            if(!$player_model->get_redis_player_model()) {
                $this->set_response_status(actioncode::$basicaction_player_code,'请重新登录');
                return false;
            }

            //$redis_task = new playerredistask();
            //$redis_task->set_action('hmget');
            $player_value = $player_model->get('player_token','');
            //var_dump($player_tokens);
            //var_dump($player_token);
            if(empty($player_token) || $player_value != $player_token) {
                $this->log_warning('user_id('.$player_id. ') client('.$player_token.') server( '. $player_value.')');
                $this->set_response_status(actioncode::$basicaction_player_code+1,'登录超时,请重新登录');
                return false;
            }

            return true;
            
        }

        public function check_player_token_param($player_id = 0,$player_token = '',basicdi $app) {
            if(!$player_id || !$player_token){
                $this->set_response_status(actioncode::$basicaction_player_code,'请重新登录');
                return false;
            }

            $player_model = new playerinfomodel($app);
            $player_model->insert('player_id',$player_id);
            if(!$player_model->get_redis_player_model()) {
                $this->set_response_status(actioncode::$basicaction_player_code,'请重新登录');
                return false;
            }

            $player_value = $player_model->get('player_token','');
            if(empty($player_token) || $player_value != $player_token) {
                $this->log_warning('user_id('.$player_id. ') client('.$player_token.') server( '. $player_value.')');
                $this->set_response_status(actioncode::$basicaction_player_code+1,'登录超时,请重新登录');
                return false;
            }

            return true;
            
        }

        public function get_action_class_name() {
            $this->m_target = get_class($this);
            return $this->m_target;
        }
	}
?>