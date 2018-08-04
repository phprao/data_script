<?php

/**
 * +----------------------------------------------------------
 * date: 2018/1/23
 * +----------------------------------------------------------
 * author: Raoxiaoya
 * +----------------------------------------------------------
 * describe:
 * +----------------------------------------------------------
 */
class moneychangestoredatatask extends basicdatatask
{
    public function __construct()
    {
        parent::__construct();
        basicfields::moneychangestore_fileds($this);
        $this->set_data_table_info('dc_change_money_info_record', 'change_money_id');

    }

}


