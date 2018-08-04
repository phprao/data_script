<?php

/**
 * 玩家升级日志
 * Class agentupgraderecorddatatask
 * @author ChangHai Zhan
 */
class agentupgraderecorddatatask extends basicdatatask
{
    /**
     * agentupgraderecorddatatask constructor.
     */
    public function __construct()
    {
        parent::__construct();
        basicfields::agentupgraderecord_fields($this);
        $this->set_data_table_info('dc_agent_upgrade_record', 'agent_upgrade_record_id');
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
