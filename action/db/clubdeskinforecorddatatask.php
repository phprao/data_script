<?php

class clubdeskinforecorddatatask extends basicdatatask
{
    public function __construct()
    {
        parent::__construct();
        basicfields::club_desk_info_fields($this);
        $this->set_data_table_info('dc_club_desk_record', 'club_desk_id');
        //$this->set_fields_default('club_desk_status',null,null);
        //$this->set_fields_default('club_desk_player_list',null,null);
        //$this->set_fields_default('club_desk_is_work',null,null);

    }
}

?>