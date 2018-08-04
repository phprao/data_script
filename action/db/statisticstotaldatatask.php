<?php
/**
 +---------------------------------------------------------- 
 * date: 2018-02-09 16:11:04
 +---------------------------------------------------------- 
 * author: Raoxiaoya
 +---------------------------------------------------------- 
 * describe: dc_statistics_total
 +---------------------------------------------------------- 
 */

class statisticstotaldatatask extends basicdatatask
{

    public function __construct()
    {
        parent::__construct();
        basicfields::statisticstotal_fileds($this);
        $this->set_data_table_info('dc_statistics_total', 'statistics_id');

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
        $sql = "UPDATE dc_statistics_total SET statistics_sum = statistics_sum + ".$param['statistics_sum']." where ";
        $sql .=" statistics_role_type = ".$param['statistics_role_type'];
        $sql .=" and statistics_role_value = ".$param['statistics_role_value'];
        $sql .=" and statistics_mode = ".$param['statistics_mode'];
        $sql .=" and statistics_type = ".$param['statistics_type'];
        $sql .=" and statistics_money_rate = ".$param['statistics_money_rate'];
        $sql .=" and statistics_timestamp = ".$param['statistics_timestamp'];
        return $db->query($sql);
    }

}