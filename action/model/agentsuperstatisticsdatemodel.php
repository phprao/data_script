<?php

/**
 * 特代分成统计
 * Class agentsuperstatisticsdatemodel
 * @author ChangHai Zhan
 */
class agentsuperstatisticsdatemodel extends basicdatamodel
{
    /**
     * 统计中
     */
    const statistics_money_status_statistics = 0;
    /**
     * 已计算
     */
    const statistics_money_status_calculated = 1;
    /**
     * 已入账
     */
    const statistics_money_status_entered = 2;

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
     * 获取代理统计信息
     * @param $statistics_agent_id
     * @param $statistics_date
     * @param $statistics_money_type
     * @param null $app
     * @return mixed
     */
    public function get_statistics_by_agent_id($money_rate_value,$statistics_agent_id, $statistics_time, $statistics_money_type = 1, $app = null)
    {
        $task = new agentsuperstatisticsdatedatatask();
        $task->set_action('select');
        $task->append_where(['statistics_agent_id' => $statistics_agent_id]);
        $task->append_where(['statistics_time' => $statistics_time]);
        $task->append_where(['statistics_money_type' => $statistics_money_type]);
        $task->append_where(['statistics_money_rate_value' => $money_rate_value]);
        if (isset($this->m_app->m_server) && $this->m_app->m_server) {
            $app = $this->m_app;
        }
        return $app->m_server->process_database($task, $this, null, null);
    }

    /**
     * 更新代理统计信息
     * @param $statistics_id
     * @param $statistics_money_data
     * @param $old_statistics_money_data
     * @param null $app
     * @return mixed
     */
    public function update_statistics_by_id($statistics_id, $statistics_money_data, $old_statistics_money_data, $is_direct = false, $app = null)
    {
        $task = new agentsuperstatisticsdatedatatask();
        $task->set_action('update_fields');
        $task->append_where(['statistics_id' => $statistics_id]);
        if($is_direct){
            $this->update('statistics_money_data_direct', $old_statistics_money_data + $statistics_money_data);
        }else{
            $this->update('statistics_money_data', $old_statistics_money_data + $statistics_money_data);
        }
        $this->update('statistics_up_time', time());
        if (isset($this->m_app->m_server) && $this->m_app->m_server) {
            $app = $this->m_app;
        }
        return $app->m_server->process_database($task, $this, null, null);
    }

    /**
     * 更新代理钱的统计计算
     * @param $statistics_id
     * @param $statistics_super_share
     * @param $statistics_money
     * @param null $app
     * @return mixed
     */
    public function update_statistics_money_by_id($statistics_id, $statistics_super_share, $statistics_money, $statistics_super_share_direct, $app = null)
    {
        $task = new agentsuperstatisticsdatedatatask();
        $task->set_action('update_fields');
        $task->append_where(['statistics_id' => $statistics_id]);
        $this->update('statistics_super_share', $statistics_super_share);
        $this->update('statistics_super_share_direct', $statistics_super_share_direct);
        $this->update('statistics_money', $statistics_money);
        $this->update('statistics_money_status', self::statistics_money_status_calculated);
        $this->update('statistics_up_time', time());
        if (isset($this->m_app->m_server) && $this->m_app->m_server) {
            $app = $this->m_app;
        }
        return $app->m_server->process_database($task, $this, null, null);
    }

    /**
     * 钱入账
     * @param $statistics_id
     * @param null $app
     * @return mixed
     */
    public function update_statistics_money_entered_by_id($statistics_id, $app = null)
    {
        $task = new agentsuperstatisticsdatedatatask();
        $task->set_action('update_fields');
        $task->append_where(['statistics_id' => $statistics_id]);
        $this->update('statistics_money_status', self::statistics_money_status_entered);
        $this->update('statistics_up_time', time());
        if (isset($this->m_app->m_server) && $this->m_app->m_server) {
            $app = $this->m_app;
        }
        return $app->m_server->process_database($task, $this, null, null);
    }

    /**
     * 添加代理统计信息
     * @param $statistics_agent_id
     * @param $params
     * @param null $app
     * @return mixed
     */
    public function create_statistics($statistics_agent_id, $params, $app = null)
    {
        $task = new agentsuperstatisticsdatedatatask();
        $task->set_action('insert_fields');
        $this->insert('statistics_agent_id', $statistics_agent_id);
        $this->insert('statistics_money_type', $params['statistics_money_type']);
        if($params['is_direct']){
            $this->insert('statistics_money_data_direct', $params['statistics_money_data']);
        }else{
            $this->insert('statistics_money_data', $params['statistics_money_data']);
        }
        $this->insert('statistics_date', $params['statistics_date']);
        $this->insert('statistics_time', $params['statistics_time']);
        $this->insert('statistics_super_share', 0);
        // $this->insert('statistics_super_config', $params['statistics_super_config']);
        $this->insert('statistics_money_rate_value', $params['statistics_money_rate_value']);
        // $this->insert('statistics_money_rate_unit', $params['statistics_money_rate_unit']);
        $this->insert('statistics_month', $params['statistics_month']);
        $this->insert('statistics_money', 0);
        $this->insert('statistics_money_status', self::statistics_money_status_statistics);
        $this->insert('statistics_up_time', time());
        $this->insert('statistics_add_time', time());
        if (isset($this->m_app->m_server) && $this->m_app->m_server) {
            $app = $this->m_app;
        }
        return $app->m_server->process_database($task, $this, null, null);
    }

    /**
     * 获取代理统计信息列表
     * @param $condition
     * @param $start
     * @param $page_size
     * @param null $app
     * @return mixed
     */
    public function get_statistics_list($condition, $start, $page_size, $app = null)
    {
        $task = new agentsuperstatisticsdatedatatask();
        $task->set_action('select_page');
        $task->set_other('limit ' . $start . ',' . $page_size);
        if (isset($condition->statistics_time)) {
            $task->append_where(['statistics_time' => $condition->statistics_time]);
        }
        if (isset($condition->statistics_money_type)) {
            $task->append_where(['statistics_money_type' => $condition->statistics_money_type]);
        }
        if (isset($condition->statistics_money_status)) {
            $task->append_where(['statistics_money_status' => $condition->statistics_money_status]);
        }
        if (isset($this->m_app->m_server) && $this->m_app->m_server) {
            $app = $this->m_app;
        }
        return $app->m_server->process_database($task, null, null, null);
    }
}