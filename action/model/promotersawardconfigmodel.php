<?php

/**
 * 特代奖励配置
 * Class promotersawardconfigmodel
 * @author ChangHai Zhan
 */
class promotersawardconfigmodel extends basicdatamodel
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
     * 获取特代配置
     * @param $agent_id
     * @param $app
     * @return mixed
     */
    public function get_award_config_by_agent_id($agent_id, $app = null)
    {
        if (isset($this->m_app->m_server) && $this->m_app->m_server) {
            $app = $this->m_app;
        }
        $task = new promotersawardconfigdatatask();
        $task->set_action('select');
        $task->append_where(['award_agent_id' => $agent_id]);
        if (!$model = $app->m_server->process_database($task, $this, null, null)) {
            $task = new promotersawardconfigdatatask();
            $task->set_action('select');
            $task->append_where(['award_agent_id' => 0]);
            $model = $app->m_server->process_database($task, $this, null, null);
        }
        return $model;
    }
}