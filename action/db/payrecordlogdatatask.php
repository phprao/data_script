<?php
/**
 +---------------------------------------------------------- 
 * date: 2018-02-09 16:11:04
 +---------------------------------------------------------- 
 * author: Raoxiaoya
 +---------------------------------------------------------- 
 * describe: dc_pay_record_log
 +---------------------------------------------------------- 
 */

class payrecordlogdatatask extends basicdatatask
{

    public function __construct()
    {
        parent::__construct();
        basicfields::payrecordlog_fileds($this);
        $this->set_data_table_info('dc_pay_record_log', 'record_id');

    }

    public function on_data_task(basicmysql $db, basicmodel $model = null, $param, $default)
    {
        if ('update_log_value' == $this->m_action) {
            return $this->update_log_value($db, $model, $param, $default);
        }
        
        return parent::on_data_task($db, $model, $param, $default);
    }

    public function update_log_value(basicmysql $db, basicmodel $model = null, $param, $default)
    {
        $sql = "UPDATE dc_pay_record_log SET record_sum = record_sum + ".$param['record_sum']." where record_type = ".$param['record_type']." and record_timestamp = ".$param['record_timestamp'];
        return $db->query($sql);
    }

}