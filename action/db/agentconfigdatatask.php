<?php

/**
 * 代理配置
 * Class agentconfigdatatask
 * @author ChangHai Zhan
 */
class agentconfigdatatask extends basicdatatask
{
    /**
     * agentconfigdatatask constructor.
     */
    public function __construct()
    {
        parent::__construct();
        basicfields::agentconfig_fields($this);
        $this->set_data_table_info('dc_agent_config', 'agentconf_id');
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
