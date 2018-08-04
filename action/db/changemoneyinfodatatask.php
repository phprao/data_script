<?php

/**
 * 资金流水
 * Class playerinfologdatatask
 * @author ChangHai Zhan
 */
class changemoneyinfodatatask extends basicdatatask
{
    /**
     * playerinfologdatatask constructor.
     */
    public function __construct()
    {
        parent::__construct();
        basicfields::changemoneyinfo_fields($this);
        $this->set_data_table_info('dc_change_money_info', 'change_money_id');
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
