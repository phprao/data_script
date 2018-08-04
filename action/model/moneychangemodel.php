<?php

/*
 *  @desc:   moneychange数据模型
 *  @author: xxm
 *  @email:  237886849@qq.com
 *  @note:   所有文件命名以小写，所有子类名以小写
 *
 */

class moneychangemodel extends basicdatamodel
{

    public function get_redis_model()
    {
        $task = new moneychangeredistask();
        $task->set_action('hmget');
        $model = $this->m_app->m_server->process_redis($task, $this, null, null);
        if (is_null($model)) {
            return false;
        }

        $model->copy($this);
        return true;
    }

    public function save_data_model()
    {
        $data_task = new moneychangedatatask();
        $data_task->set_action('insert');

        return $this->m_app->m_server->process_database($data_task, $this, null, null);
    }

    public function delete_redis_model()
    {
        $task = new moneychangeredistask();
        $task->set_action('delete');
        return $this->m_app->m_server->process_redis($task, $this, null, null);
    }

    public function check_data()
    {
        if (0 == $this->get('change_money_player_id', 0) || 0 == $this->get('change_money_club_desk_id', 0)) {
            return false;
        }
        return true;
    }

    public function update_log_time($record){
        $task = new moneychangedatatask();
        $task->set_action('update_fields');
        $task->append_where(['change_money_id' => $record->get('change_money_id', 0)]);
        $this->update('change_money_update_time', time());
        if (isset($this->m_app->m_server) && $this->m_app->m_server) {
            $app = $this->m_app;
        }
        return $app->m_server->process_database($task, $this, null, null);
    }
}

?>