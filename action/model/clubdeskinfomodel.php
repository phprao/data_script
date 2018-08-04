<?php

/*
 *  @desc:   数据模型
 *  @author: xxm
 *  @email:  237886849@qq.com
 *  @note:   所有文件命名以小写，所有子类名以小写
 *
 */

class clubdeskinfomodel extends basicdatamodel
{

    //check player is in room
    public function get_player_status()
    {
        $status_task = new playerstatusredistask();
        $status_task->set_action('hmget');
        $player = new basicdatamodel();
        //$player_id = $this->get('club_desk_player_id',0);
        //$player->insert('club_desk_player_id',$player_id);
        $desk_info = $this->m_app->m_server->process_redis($status_task, $this, null, null);
        if (is_null($desk_info)) {
            return false;
        }
        $desk_info->copy($this);
        if ($this->get('club_desk_id', 0) == 0 ||
            $this->get('club_desk_club_room_id', 0) == 0) {
            return false;
        }
        $user_club_desk_id = $this->get('club_desk_id', 0);
        if (!$this->redis_get_club_desk_model()) {
            return false;
        }
        $desk_club_desk_id = $this->get('club_desk_id', 0);
        if ($user_club_desk_id != $desk_club_desk_id) {
            return false;
        }
        if ($this->check_data_invalid()) {
            return false;
        }
        return true;
    }

    public function check_data_invalid()
    {
        if ($this->get('club_desk_room_id', 0) == 0 ||
            $this->get('club_desk_game_id', 0) == 0 ||
            $this->get('club_desk_id', 0) == 0 ||
            $this->get('club_desk_club_room_id', 0) == 0) {
            return true;
        }
        return false;
    }

    public function data_get_club_desk_model_by_room_no($room_no)
    {
        $data_task = new clubdeskinfodatatask();
        $data_task->set_action('select');
        $data_task->append_where(array('club_desk_room_no' => $room_no));

        $desk_data = $this->m_app->m_server->process_database($data_task, null, null, null);
        if (is_null($desk_data)) {
            return false;
        }
        $desk_data->copy($this);
        //if($this->check_data_invalid()) {
        //	return false;
        //}
        return true;
    }

    public function redis_get_club_desk_model()
    {
        $redis_task = new clubdeskinforedistask();
        $redis_task->set_action('hmget');

        $desk_info = $this->m_app->m_server->process_redis($redis_task, $this, null, null);

        if (is_null($desk_info)) {
            return false;
        }
        $desk_info->copy($this);
        if ($this->check_data_invalid()) {
            return false;
        }
        return true;
    }

    public function get_club_room_desk_no()
    {
        $data_task = new clubdeskinfodatatask();
        $data_task->set_action('select_club_room_desk_no');

        $desk_data = $this->m_app->m_server->process_database($data_task, $this, null, null);
        $ues_desk = array();
        if (is_null($desk_data) || empty($desk_data)) {
            return $ues_desk;
        }
        foreach ($desk_data as $value) {
            $desk_no = (int)$value['club_desk_club_room_desk_no'];
            if (in_array($desk_no, $ues_desk)) continue;
            array_push($ues_desk, $desk_no);
        }
        return $ues_desk;
    }

    public function get_room_desk_no()
    {
        $data_task = new clubdeskinfodatatask();
        $data_task->set_action('select_room_desk_no');

        $desk_data = $this->m_app->m_server->process_database($data_task, $this, null, null);
        $ues_desk = array();
        if (is_null($desk_data) || empty($desk_data)) {
            return $ues_desk;
        }
        foreach ($desk_data as $value) {
            $desk_no = (int)$value['club_desk_desk_no'];
            if (in_array($desk_no, $ues_desk)) continue;
            array_push($ues_desk, $desk_no);
        }
        return $ues_desk;
    }

    public function data_delete_model()
    {
        $data_task = new clubdeskinfodatatask();
        $data_task->set_action('delete');

        $result = $this->m_app->m_server->process_database($data_task, $this, null, null);
        return $result;
    }

    public function redis_delete_model()
    {
        $desk_task = new clubdeskinforedistask();
        $desk_task->set_action('delete');
        $result = $this->m_app->m_server->process_redis($desk_task, $this, null, null);
        return $result;
    }
}

?>