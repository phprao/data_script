<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/20
 * Time: 16:13
 */
class agentaccountmodel extends basicdatamodel
{

    protected $m_app;

    public function __construct($app)
    {
        parent::__construct();
        $this->m_app = $app;
    }

    public function create_agent_account()
    {
        $task = new agentaccountdatatask();
        $task->set_action('insert');
        $agent_account = $this->m_app->m_server->process_database($task, $this, null, null);

        return $agent_account;
    }
}