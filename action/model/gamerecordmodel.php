<?php

/*
 *  @desc:   game record数据模型
 *  @author: xxm
 *  @email:  237886849@qq.com
 *  @note:   所有文件命名以小写，所有子类名以小写
 *
 */

class gamerecordmodel extends basicdatamodel
{

    public function get_redis_model()
    {
        $task = new gamerecordredistask();
        $task->set_action('hmget');
        $game_record = $this->m_app->m_server->process_redis($task, $this, null, null);
        if (is_null($game_record)) {
            return false;
        }

        $game_record->copy($this);
        return true;
    }

    public function save_data_model()
    {
        $data_task = new gamerecorddatatask();
        $data_task->set_action('insert');

        $ret = $this->m_app->m_server->process_database($data_task, $this, null, null);//ret > 0
        return $ret;
    }

    public function delete_redis_model()
    {
        $task = new gamerecordredistask();
        $task->set_action('delete');
        $ret = $this->m_app->m_server->process_redis($task, $this, null, null);//ret > 0
        return $ret;
    }

    public function delete_data_model($where)
    {
        $data_task = new gamerecorddatatask();
        $data_task->set_action('delete_list');
        if (is_array($where) && !empty($where)) {
            $data_task->append_where($where);
        }
        return $this->m_app->m_server->process_database($data_task, null, null, null);
    }

    public function get_data_model()
    {
        $data_task = new gamerecorddatatask();
        $data_task->set_action('select');

        $game_record = $this->m_app->m_server->process_database($data_task, $this, null, null);
        if (is_null($game_record)) {
            return false;
        }
        $game_record->copy($this);
        return true;
    }

    public function get_list_model($where, $other = null)
    {
        $data_task = new gamerecorddatatask();
        $data_task->set_action('select_list_in');
        if ($where) {
            $data_task->append_where_list($where,basicdatatask::$WHERE_TYPE_LE);
        }
        if ($other) {
            $data_task->set_other($other);
        }
        $game_list = $this->m_app->m_server->process_database($data_task, null, null, null);
        if (is_null($game_list)) {
            return null;
        }
        $list_init = array();
        foreach ($game_list as $v) {
            array_push($list_init, $data_task->format_model($v));
        }

        return $list_init;
    }

    public function update_log_time($data){
        $task = new gamerecorddatatask();
        $task->set_action('update_fields');
        $task->append_where(['game_record_id' => $data['game_record_id']]);
        $this->update('game_record_update_time', time());
        if (isset($this->m_app->m_server) && $this->m_app->m_server) {
            $app = $this->m_app;
        }
        return $app->m_server->process_database($task, $this, null, null);
    }
}

?>