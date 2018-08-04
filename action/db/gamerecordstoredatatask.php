<?php

/**
 * +----------------------------------------------------------
 * date: 2018-01-15 15:39:26
 * +----------------------------------------------------------
 * author: Raoxiaoya
 * +----------------------------------------------------------
 * describe: gamerecordstoredatatask
 * +----------------------------------------------------------
 */
class gamerecordstoredatatask extends basicdatatask
{
    public function __construct()
    {
        parent::__construct();
        basicfields::gamerecordstore_fileds($this);
        $this->set_data_table_info('dc_game_record_store', 'game_log_id');

    }

}


?>