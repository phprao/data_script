<?php

/**
 * 推广奖励配置表
 * Class promotersawardconfigdatatask
 * @author ChangHai Zhan
 */
class promotersawardconfigdatatask extends basicdatatask
{
    /**
     * promotersawardconfigdatatask constructor.
     */
    public function __construct()
    {
        parent::__construct();
        basicfields::promotersawardconfig_fields($this);
        $this->set_data_table_info('dc_promoters_award_config', 'award_id');
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
