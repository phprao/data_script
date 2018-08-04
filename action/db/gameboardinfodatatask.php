<?php

/**
 * +----------------------------------------------------------
 * date: 2018-01-15 15:39:26
 * +----------------------------------------------------------
 * author: Raoxiaoya
 * +----------------------------------------------------------
 * describe: 牌局
 * +----------------------------------------------------------
 */
class gameboardinfodatatask extends basicdatatask
{
    public function __construct()
    {
        parent::__construct();
        basicfields::gameboardinfo_fileds($this);
        $this->set_data_table_info('dc_game_board_info', 'game_board_id');

    }

}


?>