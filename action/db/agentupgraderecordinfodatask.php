<?php

/*
 *  @desc:   俱乐部对象执行者
 *  @author: xxm
 *  @email:  237886849@qq.com
 *  @note:   所有文件命名以小写，所有子类名以小写
 *
 */

class agentupgraderecordinfodatask extends basicdatatask
{
    public function __construct()
    {
        parent::__construct();
        basicfields::agentupgraderecord_fields($this);
        $this->set_data_table_info('dc_agent_upgrade_record', 'agent_upgrade_record_id');

    }

    public function on_data_task(basicmysql $db, basicmodel $model = null, $param, $default)
    {

        return parent::on_data_task($db, $model, $param, $default);
    }
}

?>