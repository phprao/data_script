<?php

class clubgamelistdatatask extends basicdatatask
{
    public function __construct()
    {
        parent::__construct();
        basicfields::clubgamelist_fields($this);
        $this->set_data_table_info('dc_club_game', 'club_id');

    }

    public function on_data_task(basicmysql $db, basicmodel $model = null, $param, $default)
    {

        return parent::on_data_task($db, $model, $param, $default);
    }
}

?>