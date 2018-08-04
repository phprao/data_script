<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/26
 * Time: 18:29
 */
class promotersinfomodel extends basicdatamodel
{

    protected $m_app;

    public function __construct($app)
    {
        parent::__construct();
        $this->m_app = $app;
    }

    public function create_promoters_info()
    {
        $agent_task = new promotersinfodatatask();
        $agent_task->set_action('insert');
        $agent_info = $this->m_app->m_server->process_database($agent_task, $this, null, null);
        return $agent_info;
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

    /**
     * 添加用户
     * @param $params
     * @param null $app
     * @return mixed
     */
    public function add_promoters_info($params, $app = null)
    {
        $task = new promotersinfodatatask();
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
     * 统计玩家推广人数
     * @param $player_id
     * @param null $app
     * @return mixed
     */
    public function get_promoters_count($player_id, $app = null)
    {
        $task = new promotersinfodatatask();
        $task->set_action('select_count');
        $task->append_where(array('promoters_parent_id' => $player_id));
        if (isset($this->m_app->m_server) && $this->m_app->m_server) {
            $app = $this->m_app;
        }
        return $app->m_server->process_database($task, $this, null, null);
    }

    /**
     * 我推广玩家列表
     * @param $player_id
     * @param $start
     * @param $limit
     * @param null $app
     * @return mixed
     */
    public function get_list_by_player_id($player_id, $start = 0, $page_size = 10, $app = null)
    {
        $task = new promotersinfodatatask();
        $task->set_action('select_page');
        $task->append_where(array('promoters_parent_id' => $player_id));
        if (isset($this->m_app->m_server) && $this->m_app->m_server) {
            $app = $this->m_app;
        }
        $task->set_other('limit ' . $start . ',' . $page_size);
        return $app->m_server->process_database($task, null, null, null);
    }




}