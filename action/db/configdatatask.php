<?php

class configdatatask extends basicdatatask
{
    public function __construct()
    {
        parent::__construct();
        basicfields::config_fields($this);
        $this->set_data_table_info('dc_config', 'config_id'); 
    }
}

?>