<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/20
 * Time: 16:31
 */
class agentaccountdatatask extends basicdatatask
{

    public function __construct()
    {
        parent::__construct();
        basicfields::agentaccountinfo_fields($this);
        $this->set_data_table_info('dc_agent_account_info', 'agent_account_id');

    }

}