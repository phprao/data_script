<?php

/**
 * +----------------------------------------------------------
 * date: 2018-01-15 15:51:16
 * +----------------------------------------------------------
 * author: Raoxiaoya
 * +----------------------------------------------------------
 * describe: 牌局生成表
 * +----------------------------------------------------------
 */
class gameboardinfomodel extends basicdatamodel
{
    public function __construct(basicdi $app = null)
    {
        parent::__construct($app);
        $this->data_task = new gameboardinfodatatask();
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

    public function get_one_model($where)
    {
        $this->data_task->set_action('select');
        $this->data_task->append_where($where);
        $board_record = $this->m_app->m_server->process_database($this->data_task, $this, null, null);
        if (is_null($board_record)) {
            return false;
        }
        $board_record = $this->data_task->format_model($board_record);
        return $board_record;
    }
}

?>