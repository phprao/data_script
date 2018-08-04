<?php

/**
 * 资金流水
 * Class agentaccountinfologdatatask
 * @author ChangHai Zhan
 */
class agentaccountinfologdatatask extends basicdatatask
{
    /**
     * agentaccountinfologdatatask constructor.
     */
    public function __construct()
    {
        parent::__construct();
        basicfields::agentaccountinfolog_fields($this);
        $this->set_data_table_info('dc_agent_account_info_log', 'log_id');
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
