<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/26
 * Time: 20:12
 */
class feedbackdatatask extends basicdatatask
{

    public function __construct()
    {
        parent::__construct();
        basicfields::feedback_fileds($this);
        $this->set_data_table_info('dc_feedback', 'feedback_id');

    }

    public function on_data_task(basicmysql $db, basicmodel $model = null, $param, $default)
    {

        return parent::on_data_task($db, $model, $param, $default);
    }


}