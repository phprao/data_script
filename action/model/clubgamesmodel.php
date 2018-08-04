<?php

/**
 * Class clubgamesmodel
 */
class clubgamesmodel extends basicdatamodel
{
	/**
     * clubinfomodel constructor.
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

	function getclubgames($club_id,$index) {
		$data_task = new clubgamelistdatatask();
        $data_task->set_action('select_page');
        $data_task->append_where(array('club_id'=>$club_id));
        $data_task->set_other('limit '.$index.',100');
        $game_list = $this->m_app->m_server->process_database($data_task,null,null,null);
        if(is_null($game_list) ) {
            return array();
        }

        $redis_task = new gameinforedistask();
        $redis_task->set_action('hmget');
        //$data_task->append_where(array('club_id'=>$club_id));
        $list = array();
        foreach ($game_list as $key => $value) {
            $game_id = $value->get('game_id',0);       
            $game_info = $this->m_app->m_server->process_redis($redis_task,$value,null,null);
            if(is_null($game_info)) continue;
            $game_info->insert('game_club_id',$club_id);
            array_push($list, $game_info);
        }
        return $list;
	}
}
