<?php

/**
 * +----------------------------------------------------------
 * date: 2018/1/23
 * +----------------------------------------------------------
 * author: Raoxiaoya
 * +----------------------------------------------------------
 * describe:
 * +----------------------------------------------------------
 */
class agentsstatisticsdaymodel extends basicdatamodel
{
    public function __construct(basicdi $app = null)
    {
        parent::__construct($app);
        $this->data_task = new agentsstatisticsdaydatatask();
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

    public function getrecord($where)
    {
        $this->data_task->set_action('select');
        if (is_array($where) && !empty($where)) {
            $this->data_task->append_where($where);
        }
        $record = $this->m_app->m_server->process_database($this->data_task, $this, null, null);
        if (is_null($record)) {
            return false;
        }
        $record = $this->data_task->format_model($record);
        return $record;
    }

    public function updateplayerbyday($data)
    {
        $this->data_task->set_action('update_value');
        return $this->m_app->m_server->process_database($this->data_task, $this, $data, null);
    }


}

?>