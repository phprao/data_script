<?php

/**
 * 条件表
 * Class agentconditionsdatatask
 * @author ChangHai Zhan
 */
class agentconditionsdatatask extends basicdatatask
{
    /**
     * agentconditionsdatatask constructor.
     */
    public function __construct()
    {
        parent::__construct();
        basicfields::agentconditions_fields($this);
        $this->set_data_table_info('dc_agent_conditions', 'agent_conditions_id');
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
