<?php

class clubdeskinfodatatask extends basicdatatask
{
    public function __construct()
    {
        parent::__construct();
        basicfields::club_desk_info_fields($this);
        $this->set_data_table_info('dc_club_desk', 'club_desk_id');
        //$this->set_fields_default('club_desk_status',null,null);
        //$this->set_fields_default('club_desk_player_list',null,null);
        //$this->set_fields_default('club_desk_is_work',null,null);

    }


    public function on_data_task(basicmysql $db, basicmodel $model = null, $param, $default)
    {
        if ('select_club_room_desk_no' == $this->m_action) {
            return $this->select_club_room_desk_no($db, $model, $default);
        } else if ('select_room_desk_no' == $this->m_action) {
            return $this->select_room_desk_no($db, $model, $default);
        }
        return parent::on_data_task($db, $model, $param, $default);
    }


    protected function select_club_room_desk_no($db, $model, $default)
    {

        $club_desk_club_id = $model->get('club_desk_club_id', 0);
        $club_desk_club_room_id = $model->get('club_desk_club_room_id', 0);
        $this->append_where(array('club_desk_club_id' => $club_desk_club_id));
        $this->append_where(array('club_desk_club_room_id' => $club_desk_club_room_id));
        $result = $db->select($this->m_table_name, 'club_desk_club_room_desk_no', $this->m_where);

        return $result;
    }

    protected function select_room_desk_no($db, $model, $default)
    {

        $club_desk_room_id = $model->get('club_desk_room_id', 0);
        $this->append_where(array('club_desk_room_id' => $club_desk_room_id));

        $result = $db->select($this->m_table_name, 'club_desk_desk_no', $this->m_where);
        //var_dump($result);
        return $result;
    }
}

?>