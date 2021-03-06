<?php

/**
 * +----------------------------------------------------------
 * date: 2018-01-15 15:51:16
 * +----------------------------------------------------------
 * author: Raoxiaoya
 * +----------------------------------------------------------
 * describe: 充值记录统计
 * +----------------------------------------------------------
 */
class payrecordlogmodel extends basicdatamodel
{
    public function __construct(basicdi $app = null)
    {
        parent::__construct($app);
        $this->data_task = new payrecordlogdatatask();
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

    public function get_one_log($where)
    {
        $this->data_task->set_action('select');
        $this->data_task->append_where($where);
        $round_record = $this->m_app->m_server->process_database($this->data_task, $this, null, null);
        if (is_null($round_record)) {
            return false;
        }
        $round_record = $this->data_task->format_model($round_record);
        return $round_record;
    }

    public function update_pay_sum($where)
    {
        $this->data_task->set_action('update_log_value');
        $re = $this->m_app->m_server->process_database($this->data_task, null, $where, null);
        return $re;
    }

}

?>

