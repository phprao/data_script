<?php

class userdatatask extends basicdatatask
{
    public function __construct()
    {
        parent::__construct();
        basicfields::user_fileds($this);
        $this->set_data_table_info('dc_users', 'id');

    }

    public function on_data_task(basicmysql $db, basicmodel $model = null, $param, $default)
    {

        return parent::on_data_task($db, $model, $param, $default);
    }
}

?>