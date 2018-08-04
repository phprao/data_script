<?php

/**
 * Class clubplayerdatatask
 */
class clubplayerdatatask extends basicdatatask
{
    /**
     * clubplayerdatatask constructor.
     */
    public function __construct()
    {
        parent::__construct();
        basicfields::clubplayer_fields($this);
        $this->set_data_table_info('dc_club_player', 'id');
    }
}