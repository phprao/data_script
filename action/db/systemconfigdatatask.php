<?php

class systemconfigdatatask extends basicdatatask
{

    public function __construct()
    {
        parent::__construct();
        basicfields::system_config_fileds($this);
        $this->set_data_table_info('dc_system_config', 'system_config_id');

    }

}

?>