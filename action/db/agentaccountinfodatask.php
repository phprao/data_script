<?php

/**
 * 账户表
 * Class agentaccountinfodatask
 * @author ChangHai Zhan
 */
class agentaccountinfodatask extends basicdatatask
{
    /**
     * agentaccountinfodatask constructor.
     */
    public function __construct()
    {
        parent::__construct();
        basicfields::agentaccountinfo_fields($this);
        $this->set_data_table_info('dc_agent_account_info', 'agent_account_id');
    }

    /**
     * @param basicmysql $db
     * @param basicmodel|null $model
     * @param $param
     * @param $default
     */
    public function on_data_task(basicmysql $db, basicmodel $model = null, $param, $default)
    {
        return parent::on_data_task($db, $model, $param, $default);
    }
}
