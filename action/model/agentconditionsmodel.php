<?php

/**
 * 条件表
 * Class agentconditionsmodel
 * @author ChangHai Zhan
 */
class agentconditionsmodel extends basicdatamodel
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
     * 获取条件
     * @param $id
     * @param $app
     * @return mixed
     */
    public function get_condition_by_id($id, $app = null)
    {
        if (isset($this->m_app->m_server) && $this->m_app->m_server) {
            $app = $this->m_app;
        }
        $task = new agentconditionsdatatask();
        if (is_array($id)) {
            $task->set_action('select_list_in');
            $task->append_where_list(['agent_conditions_id' => $id], basicdatatask::$WHERE_TYPE_IN);
            return $app->m_server->process_database($task, null, null, []);
        } else {
            $task->set_action('select');
            $task->append_where(['agent_conditions_id' => $id]);
            return $app->m_server->process_database($task, $this, null, null);
        }
    }

    /**
     * @param null $app
     * @return mixed
     */
    public function get_condition_all($app = null)
    {
        if (isset($this->m_app->m_server) && $this->m_app->m_server) {
            $app = $this->m_app;
        }
        $task = new agentconditionsdatatask();
        $task->set_action('select_all');
        return $app->m_server->process_database($task, null, null, []);
    }
}