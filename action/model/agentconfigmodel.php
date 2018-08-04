<?php

/**
 * 特代选择条件
 * Class agentconfigmodel
 * @author ChangHai Zhan
 */
class agentconfigmodel extends basicdatamodel
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
     * 获取代理配置条件
     * @param $agent_id
     * @param $app
     * @return mixed
     */
    public function get_config_by_agent_id($agent_id, $app = null)
    {
        if (isset($this->m_app->m_server) && $this->m_app->m_server) {
            $app = $this->m_app;
        }
        $task = new agentconfigdatatask();
        if (is_array($agent_id)) {
            $task->set_action('select_list_in');
            $task->append_where_list(['agent_id' => $agent_id], basicdatatask::$WHERE_TYPE_IN);
            return $app->m_server->process_database($task, null, null, []);
        } else {
            $task->set_action('select');
            $task->append_where(['agent_id' => $agent_id]);
            return $app->m_server->process_database($task, $this, null, null);
        }
    }
}