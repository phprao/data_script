<?php

/**
 * +----------------------------------------------------------
 * date: 2018-01-15 15:51:16
 * +----------------------------------------------------------
 * author: Raoxiaoya
 * +----------------------------------------------------------
 * describe: 游戏记录model
 * +----------------------------------------------------------
 */
class gamerecordlogmodel extends basicdatamodel
{
    public function __construct(basicdi $app = null)
    {
        parent::__construct($app);
        $this->data_task = new gamerecordlogdatatask();
    }

    public function save_data_model($data = array())
    {
        if (empty($data)) {
            return false;
        }
        $this->data_task->set_action('insert');
        foreach ($data as $key => $val) {
            $this->insert($key, $val);
        }
        return $this->m_app->m_server->process_database($this->data_task, $this, null, null);
    }

    public function get_data_model()
    {
        $this->data_task->set_action('select');
        $game_record = $this->m_app->m_server->process_database($this->data_task, $this, null, null);
        if (is_null($game_record)) {
            return false;
        }
        $game_record->copy($this);
        return true;
    }

    public function get_list_model($other = null)
    {
        $this->data_task->set_action('select_all');
        if ($other) {
            $this->data_task->set_other($other);
        }
        $game_list = $this->m_app->m_server->process_database($this->data_task, null, null, null);
        if (is_null($game_list)) {
            return null;
        }
        return $game_list;
    }
}

?>