<?php

/*
 *  @desc:   roominfo数据模型
 *  @author: xxm
 *  @email:  237886849@qq.com
 *  @note:   所有文件命名以小写，所有子类名以小写
 *
 */

class roominfomodel extends basicdatamodel
{

    public function reload_room_info_model()
    {
        //$room_id = $desk_info->get('club_desk_room_id',0);
        //$model = new basicdatamodel();
        //$model->insert('room_id',$room_id);
        $redis_room = new roominforedistask();
        $redis_room->set_action('hmget');
        $room_info = $this->m_app->m_server->process_redis($redis_room, $this, null, null);
        if (is_null($room_info)) {
            return false;
        }
        $room_info->copy($this);
        return true;
    }

    public function get_room_list()
    {
        $room_data_task = new roominfodatatask();
        $room_data_task->set_action('select_list');
        $game_id = $this->get('room_game_id', 0);
        $club_room_is_open = $this->get('room_is_open', 0);
        $room_data_task->append_where(array('room_game_id' => $game_id));
        $room_data_task->append_where(array('room_is_open' => $club_room_is_open));

        $room_list = $this->m_app->m_server->process_database($room_data_task, null, null, null);

        return $room_list;
    }

    public function get_data_model()
    {
        $redis_room = new roominforedistask();
        $redis_room->set_action('hmget');
        $room_info = $this->m_app->m_server->process_redis($redis_room, $this, null, null);
        if (is_null($room_info)) {
            return false;
        }
        $room_info->copy($this);
        return true;
    }

    public function get_roominfo_model($where)
    {
        $room_data_task = new roominfodatatask();
        $room_data_task->set_action('select');
        $room_data_task->append_where($where);
        $roominfo = $this->m_app->m_server->process_database($room_data_task, $this, null, null);
        if (is_null($roominfo)) {
            return false;
        }
        $roominfo = $room_data_task->format_model($roominfo);
        return $roominfo;
    }
}

?>