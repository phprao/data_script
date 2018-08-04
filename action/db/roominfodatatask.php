<?php

class roominfodatatask extends basicdatatask
{
    public function __construct()
    {
        parent::__construct();
        basicfields::roominfo_fields($this);
        $this->set_data_table_info('dc_room_info', 'room_id');
    }
}

?>