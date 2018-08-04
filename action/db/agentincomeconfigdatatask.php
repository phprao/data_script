<?php

/**
 * 代理分成比例
 * Class agentincomeconfigdatatask
 * @author ChangHai Zhan
 */
class agentincomeconfigdatatask extends basicdatatask
{
    /**
     * agentincomeconfigdatatask constructor.
     */
    public function __construct()
    {
        parent::__construct();
        basicfields::agentincomeconfig_fields($this);
        $this->set_data_table_info('dc_agent_income_config', 'income_id');
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
