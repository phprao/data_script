<?php

/**
 * +----------------------------------------------------------
 * date: 2018/1/23
 * +----------------------------------------------------------
 * author: Raoxiaoya
 * +----------------------------------------------------------
 * describe:玩家金币变化记录
 * +----------------------------------------------------------
 */
class moneychangeplayermodel extends basicdatamodel
{
    public function __construct(basicdi $app = null)
    {
        parent::__construct($app);
        $this->data_task = new moneychangeplayerdatatask();
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

    public function search_player_log_day($where){
        $this->data_task->set_action('search_player_log');
        return $this->m_app->m_server->process_database($this->data_task, null, $where, null);
    }


}

?>