<?php

/**
 * 账户表
 * Class agentaccountinfomodel
 * @author ChangHai Zhan
 */
class agentaccountinfomodel extends basicdatamodel
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
     * 获取账户信息
     * @param $agent_account_agent_id
     * @param $app
     * @return mixed
     */
    public function get_account_info($agent_account_agent_id, $app = null)
    {
        $task = new agentaccountinfodatask();
        $task->set_action('select');
        $task->append_where(['agent_account_agent_id' => $agent_account_agent_id]);
        if (isset($this->m_app->m_server) && $this->m_app->m_server) {
            $app = $this->m_app;
        }
        return $app->m_server->process_database($task, $this, null, null);
    }

    /**
     * 计入金额
     * @param $agent_account_agent_id
     * @param $money
     * @param null $old_money
     * @param null $app
     * @return bool
     */
    public function incr_money_by_agent_id($agent_account_agent_id, $money, $old_money = null, $app = null)
    {
        if ($old_money === null) {
            if (!$model = $this->get_account_info($agent_account_agent_id)) {
                return false;
            }
            $old_money = $model->get('agent_account_money', 0);
        }
        $task = new agentaccountinfodatask();
        $task->set_action('update_fields');
        $task->append_where(['agent_account_agent_id' => $agent_account_agent_id]);
        $this->update('agent_account_money', $old_money + $money);
        if (isset($this->m_app->m_server) && $this->m_app->m_server) {
            $app = $this->m_app;
        }
        return $app->m_server->process_database($task, $this, null, null);
    }

    /**
     * 添加代理账户信息
     * @param $params
     * @param null $app
     * @return mixed
     */
    public function add_agent_account_info($params, $app = null)
    {
        $task = new agentaccountinfodatask();
        $task->set_action('insert_fields');
        foreach ($params as $key => $value) {
            $this->insert($key, $value);
        }
        if (isset($this->m_app->m_server) && $this->m_app->m_server) {
            $app = $this->m_app;
        }
        return $app->m_server->process_database($task, $this, null, null);
    }

    /**
     * @param $agent_account_agent_id
     * @param $money
     * @param null $app
     * @return mixed
     */
    public function incr_agent_account_money($agent_account_agent_id, $money, $app = null)
    {
        $task = new agentaccountinfodatask();
        $task->set_action('update_fields');
        $task->append_where(['agent_account_agent_id' => $agent_account_agent_id]);
        $this->update('agent_account_money', $money);
        if (isset($this->m_app->m_server) && $this->m_app->m_server) {
            $app = $this->m_app;
        }
        return $app->m_server->process_database($task, $this, null, null);
    }


}

