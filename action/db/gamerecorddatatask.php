<?php

class gamerecorddatatask extends basicdatatask
{
    public function __construct()
    {
        parent::__construct();
        basicfields::gamerecord_fields($this);
        $this->set_data_table_info('dc_game_record', 'game_record_id');

    }

}

?>