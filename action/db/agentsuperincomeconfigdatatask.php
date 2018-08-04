<?php

/**
 * 特代分成比例配置表
 * Class agentsuperincomeconfigdatatask
 * @author ChangHai Zhan
 */
class agentsuperincomeconfigdatatask extends basicdatatask
{
    /**
     * agentsuperincomeconfigdatatask constructor.
     */
    public function __construct()
    {
        parent::__construct();
        basicfields::agentsuperincomeconfig_fields($this);
        $this->set_data_table_info('dc_agent_super_income_config', 'super_id');
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
