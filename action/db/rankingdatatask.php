<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/17
 * Time: 10:34
 */
class rankingdatatask extends basicdatatask
{

    public function __construct()
    {
        parent::__construct();
        basicfields::ranking_fileds($this);
        $this->set_data_table_info('dc_ranking', 'id');
    }

}