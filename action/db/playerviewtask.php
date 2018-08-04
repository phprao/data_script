<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/17
 * Time: 10:17
 */
class playerviewtask extends basicdatatask
{

    public function __construct()
    {
        parent::__construct();
        basicfields::playerview_fileds($this);
        $this->set_data_table_info('dc_view_player_info', 'player_id');
    }

}