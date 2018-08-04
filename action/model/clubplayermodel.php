<?php
/**
 * 用户电玩厅信息model
 */
class clubplayermodel extends basicdatamodel
{
    /**
     * 代币类型对应的字段
     * @var string
     */
    public static $t_type = 'player_tokens';

	/**
     * clubplayermodel constructor.
     * @param $app
     */
    public function __construct($app)
    {
        parent::__construct();
        $this->m_app = $app;
    }

    /**
     * 静态实例化
     * @param string $app
     * @param string $className
     * @return static active record model instance.
     */
    public static function model($app = null, $className = __CLASS__)
    {
        return new $className($app);
    }

   /*
    * 我的电玩厅信息
    */
	public function getclubinfobyplayer($club_id,$player_id) {
		$redis_task = new clubplayerredistask();      
        $redis_task->set_action('hmget');
        $this->insert('club_id', $club_id);
        $this->insert('player_id', $player_id);
		$data = $this->m_app->m_server->process_redis($redis_task, $this, null, null);
		if(!$data) {
			$task = new clubplayerdatatask();
	        $task->set_action('select');
	        $task->append_where(array('club_id' => $club_id));
	        $task->append_where(array('player_id' => $player_id));
	        $club_player = $this->m_app->m_server->process_database($task, null, null, null);
	        return $club_player;
		} else {
			return $data;
		}
	}

   /*
    * 我的电玩主厅信息
    */
	public function getmainclubbyplayer($player_id) {
		$playinfo = playerinfomodel::model($this->m_app)->get_player_info_by_player_id($player_id);
		$player_club_id = $playinfo->get('player_club_id',0);
		if($player_club_id == 0) {
			return array();
		}
		$data_task = new clubplayerdatatask();
		$data_task->set_action('select');
        $data_task->append_where(array('club_id' => $player_club_id));
        $data_task->append_where(array('player_id' => $player_id));
        $data = $this->m_app->m_server->process_database($data_task, null, null, null);  
        return $data; 			
	}

   /*
    * 我的电玩厅列表
    */
	public function getclublistbyplayer($player_id,$index,$limit) {
		 $playinfo = playerinfomodel::model($this->m_app)->get_player_info_by_player_id($player_id);
		 $main_club_id = $playinfo->get('player_club_id',0);
		 $data_task = new clubplayerdatatask();
	     $data_task->set_action('select_page');
         $data_task->append_where(array('player_id' => $player_id));
         $data_task->set_other('and `club_id` <> '.$main_club_id.' order by `join_time` desc '.'limit ' . $index . ','.$limit);
         $datalist = $this->m_app->m_server->process_database($data_task, null, null, null);
         return $datalist; 			
	}

   /*
    * 电玩厅信息
    */
	public function clubinfo($clubid) {     
        $data_task = new clubdatatask();
        $data_task->set_action('select');
        $data_task->append_where(array('club_id' => $clubid));
        $data_task->set_other('limit 1');
        $info = $this->m_app->m_server->process_database($data_task, null, null, null);
        $club_head_image = $info->get('club_head_image','');
        $club_name = $info->get('club_name','');
        $return['club_head_image'] = $club_head_image;
        $return['club_name']       = $club_name;
        return $return;
    }

    /*
     * 电玩厅在线人数
     */
    public function clubonline($clubid) {
    	$data_task = new clubplayerdatatask();
        $data_task->set_action('select_count');
        $data_task->append_where(array('club_id' => $clubid));
        $num = $this->m_app->m_server->process_database($data_task, null, null, null);
        return $num;
    }

    /*
     * 搜索电玩厅
     */
    public function clubseach($key,$index,$limit) {     
        $data_task = new clubdatatask();
        $data_task->set_action('select_list');
        $data_task->append_where(array('club_id'=>''));
        $data_task->set_other(" or `club_name` like '%".$key."%' or `club_id` like '%".$key."%' limit ".$index.",".$limit);
        $info = $this->m_app->m_server->process_database($data_task, null, null, null);
        return $info;
    }


    /**
     * 更新代币db
     * @param $player_id
     * @param $club_id
     * @param $tokens
     * @param null $app
     * @return mixed
     */
    public function update_tokens_db($player_id,$club_id,$tokens,$tokens_type,$app = null) {
        if (self::$t_type != $tokens_type){
            return false;
        }

        $task = new clubplayerdatatask();
        $task->set_action('update_fields');
        $task->append_where(array('club_id' => $club_id));
        $task->append_where(array('player_id' => $player_id));
        $this->update(self::$t_type,$tokens);
        if (isset($this->m_app->m_server) && $this->m_app->m_server) {
            $app = $this->m_app;
        }
        return $app->m_server->process_database($task, $this, null, null);
    }

     /**
     * 更新代币 redis
     * @param $player_id
     * @param $club_id
     * @param $tokens
     * @param null $app
     * @return mixed
     */
    public function update_tokens_redis($player_id,$club_id,$tokens,$tokens_type,$app = null) {
        if (self::$t_type != $tokens_type) {
            return false;
        }
        $redis_model = $this->get_player($player_id,$club_id);
        if (!$redis_model) {
            return false;
        }
        $redis_task = new clubplayerredistask();
        $redis_task->set_action('incrby_model');
        $result = $this->m_app->m_server->process_redis($redis_task, $redis_model, [$tokens_type => $tokens], false);
        return isset($result[$tokens_type]) ? $result[$tokens_type] : false;
    }

    /**
     * 获取redis
     * @param $player_id or model
     * @return mixed
     */
    public function get_player($player_id,$club_id) {     
        $redis_task = new clubplayerredistask();      
        $redis_task->set_action('hmget');
        $this->insert('club_id', $club_id);
        $this->insert('player_id', $player_id);
        $data = $this->m_app->m_server->process_redis($redis_task, $this, null, null);
        return $data;
    }

}
