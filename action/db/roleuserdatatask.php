<?php

class roleuserdatatask extends basicdatatask
{
    public function __construct()
    {
        parent::__construct();
        basicfields::role_user($this);
        $this->set_data_table_info('role_user', 'role_id');

    }

    public function on_data_task(basicmysql $db, basicmodel $model = null, $param, $default)
    {

        return parent::on_data_task($db, $model, $param, $default);
    }
}

?>