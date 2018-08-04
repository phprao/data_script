<?php

/**
 * +----------------------------------------------------------
 * date: 2018-01-15 15:39:26
 * +----------------------------------------------------------
 * author: Raoxiaoya
 * +----------------------------------------------------------
 * describe: dc_game_record_log 数据表操作
 * +----------------------------------------------------------
 */
class gamerecordlogdatatask extends basicdatatask
{
    public function __construct()
    {
        parent::__construct();
        basicfields::gamerecordlog_fileds($this);
        $this->set_data_table_info('dc_game_record_log', 'game_log_id');

    }

}


?>