<?php

/**
 * 玩家升级代理日志
 * Class agentupgraderecordmodel
 * @author ChangHai Zhan
 */
class agentupgraderecordmodel extends basicdatamodel
{
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

    /**
     * 添加升级日志
     * @param $player_id
     * @param $agent_id
     * @param null $app
     * @return mixed
     */
    public function add_record($player_id, $agent_id, $app = null)
    {
        $task = new agentupgraderecorddatatask();
        $task->set_action('insert_fields');
        $this->insert('agent_upgrade_record_player_id', $player_id);
        $this->insert('agent_upgrade_record_agent_id', $agent_id);
        $this->insert('agent_upgrade_record_time', time());
        if (isset($this->m_app->m_server) && $this->m_app->m_server) {
            $app = $this->m_app;
        }
        return $app->m_server->process_database($task, $this, null, null);
    }
}