<?php

/**
 * 系统公告，跑马灯。
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/6
 * Time: 9:41
 */
class noticedatatask extends basicdatatask
{

    public function __construct()
    {
        parent::__construct();
        basicfields::notice_fileds($this);
        $this->set_data_table_info('dc_notice', 'notice_id');
    }

    public function on_data_task(basicmysql $db, basicmodel $model = null, $param, $default)
    {
        return parent::on_data_task($db, $model, $param, $default);
    }

}