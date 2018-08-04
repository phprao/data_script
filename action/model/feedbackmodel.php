<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/26
 * Time: 20:11
 */
class feedbackmodel extends basicdatamodel
{
    protected $m_app;

    public function __construct($app)
    {
        parent::__construct();
        $this->m_app = $app;
    }

    public function create_feedback()
    {
        $task = new feedbackdatatask();
        $task->set_action('insert');
        $feedback_info = $this->m_app->m_server->process_database($task, $this, null, null);
        return $feedback_info;
    }

}