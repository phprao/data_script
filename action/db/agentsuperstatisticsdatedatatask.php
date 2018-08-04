<?php

/**
 * 特代分成比例统计表
 * Class agentsuperstatisticsdatedatatask
 * @author ChangHai Zhan
 */
class agentsuperstatisticsdatedatatask extends basicdatatask
{
    /**
     * agentsuperstatisticsdatedatatask constructor.
     */
    public function __construct()
    {
        parent::__construct();
        basicfields::agentsuperstatisticsdate_fields($this);
        $this->set_data_table_info('dc_agent_super_statistics_date', 'statistics_id');
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
