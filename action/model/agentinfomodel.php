<?php

/**
 * 代理表
 * Class agentinfomodel
 * @author ChangHai Zhan
 */
class agentinfomodel extends basicdatamodel
{
    /**
     * 代理后台权限 未开通
     */
    const agent_login_status_no = 0;
    /**
     * 代理后台权限 已开通
     */
    const agent_login_status_yes = 1;

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
     * 获取特代列表
     * @param int $start
     * @param int $page_size
     * @param null $condition
     * @param null $app
     * @return mixed
     */
    public function get_super_agent_list($start = 0, $page_size = 10, $condition = null, $app = null)
    {
        if ($condition === null) {
            $condition = new stdClass();
        }
        $condition->agent_top_agentid = 0;
        return $this->get_agent_list($condition, $start, $page_size, $app);
    }

    /**
     * 获取代理list
     * @param $condition
     * @param int $start
     * @param int $page_size
     * @param $app
     * @return mixed
     */
    public function get_agent_list($condition, $start = 0, $page_size = 10, $app = null)
    {
        $task = new agentinfodatatask();
        $task->set_action('select_list_in');
        if (isset($condition->agent_parentid)) {
            $task->append_where_list(['agent_parentid' => $condition->agent_parentid], basicdatatask::$WHERE_TYPE_EQUAL);
        }
        if (isset($condition->agent_top_agentid)) {
            $task->append_where_list(['agent_top_agentid' => $condition->agent_top_agentid], basicdatatask::$WHERE_TYPE_EQUAL);
        }
        if (isset($condition->agent_promote_conut)) {
            if (is_array($condition->agent_promote_conut)) {
                $task->append_where_list(['agent_promote_count' => $condition->agent_promote_conut[1]], $condition->agent_promote_conut[0]);
            }
        }
        if (isset($condition->agent_login_status)) {
            $task->append_where_list(['agent_login_status' => $condition->agent_login_status], basicdatatask::$WHERE_TYPE_EQUAL);
        }
        $task->set_other('limit ' . $start . ',' . $page_size);
        if (isset($this->m_app->m_server) && $this->m_app->m_server) {
            $app = $this->m_app;
        }
        return $app->m_server->process_database($task, null, null, null);
    }

    /**
     * 更新代理权限
     * @param $agent_id
     * @param int $agent_login_status
     * @param null $app
     * @return mixed
     */
    public function update_agent_login_status_by_id($agent_id, $agent_login_status = self::agent_login_status_yes, $app = null)
    {
        $task = new agentinfodatatask();
        $task->set_action('update_fields');
        $task->append_where(['agent_id' => $agent_id]);
        $this->update('agent_login_status', $agent_login_status);
        $this->update('agent_star_time', time());
        if (isset($this->m_app->m_server) && $this->m_app->m_server) {
            $app = $this->m_app;
        }
        return $app->m_server->process_database($task, $this, null, null);
    }

    /**
     * @param $agent_id
     * @param $agent_promote_count
     * @param null $app
     * @return mixed
     */
    public function update_agent_promote_count_by_id($agent_id, $agent_promote_count, $app = null)
    {
        $task = new agentinfodatatask();
        $task->set_action('update_fields');
        $task->append_where(['agent_id' => $agent_id]);
        $task->append_where(['agent_user_id' => 0]);
        $this->update('agent_promote_count', $agent_promote_count);
        if (isset($this->m_app->m_server) && $this->m_app->m_server) {
            $app = $this->m_app;
        }
        return $app->m_server->process_database($task, $this, null, null);
    }

    /**
     * 获取玩家代理信息
     * @param $player_id
     * @param $app
     * @return mixed
     */
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

    /**
     * 获取玩家代理信息
     * @param $agent_id
     * @param $condition
     * @param $app
     * @return mixed
     */
    public function get_agent_by_id($agent_id, $condition = null, $app = null)
    {
        $task = new agentinfodatatask();
        $task->set_action('select');
        if (isset($condition->agent_login_status)) {
            $task->append_where(['agent_login_status' => $condition->agent_login_status]);
        }
        $task->append_where(['agent_id' => $agent_id]);
        if (isset($this->m_app->m_server) && $this->m_app->m_server) {
            $app = $this->m_app;
        }
        return $app->m_server->process_database($task, $this, null, null);
    }

    /**
     * @param $params
     * @return mixed
     */
    public function add_agent_info($params)
    {
        $task = new agentinfodatatask();
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
     * 获取全部上级代理节点
     * @param  [type] $agentid [description]
     * @return [type]          [description]
     */
    public function get_agents_relationship($agentid, $deep, $app)
    {
        $task = new agentinfodatatask();
        $task->set_action('select_agents');
        $data = $app->m_server->process_database($task, $this, ['agent_id' => $agentid, 'deep' => $deep], null);
        //$data = $task->format_model($data);
        return $data;
    }
}