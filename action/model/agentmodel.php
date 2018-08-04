<?php

/*
 *  @desc:   playerinfo数据模型
 *  @author: xxm
 *  @email:  237886849@qq.com
 *  @note:   所有文件命名以小写，所有子类名以小写
 *
 */

class agentmodel extends basicdatamodel
{

    protected $m_app;

    public function __construct($app = null)
    {
        parent::__construct();
        $this->m_app = $app;
    }


    public function create_agent()
    {
        $agent_task = new agentinfodatatask();
        $agent_task->set_action('insert');
        $agent_info = $this->m_app->m_server->process_database($agent_task, $this, null, null);
        return $agent_info;
    }

    public function get_info_agentid($condition)
    {
        $agent_task = new agentinfodatatask();
        $agent_task->set_action('select');
        $agent_task->append_where($condition);
        $agent_info = $this->m_app->m_server->process_database($agent_task, $this, null, null);
        return $agent_info;
    }
    public function get_agent_by_player_id($player_id, $app = null)
    {
        $task = new agentinfodatatask();
        $task->set_action('select');
        $task->append_where(['agent_player_id' => $player_id]);
        if (isset($this->m_app->m_server) && $this->m_app->m_server) {
            $app = $this->m_app;
        }
        return $app->m_server->process_database($task, $this, null, null);
    }
}

?>