<?php

/**
 * +----------------------------------------------------------
 * date: 2018-01-15 15:39:26
 * +----------------------------------------------------------
 * author: Raoxiaoya
 * +----------------------------------------------------------
 * describe: dc_game_record_log_0 数据表操作
 * +----------------------------------------------------------
 */
class gamerecordlogbakdatatask extends basicdatatask
{
    public function __construct($key = 0)
    {
        parent::__construct();
        basicfields::gamerecordlogbak_fileds($this);
        $this->set_data_table_info('dc_game_record_log_' . $key, 'game_log_id');

    }

}


?>