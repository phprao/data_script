<?php

class agentsstatisticsdaydatatask extends basicdatatask
{
    public function __construct()
    {
        parent::__construct();
        basicfields::playermoneychangeday_fileds($this);
        $this->set_data_table_info('dc_agents_statistics_day', 'statistics_id');
    }

    public function on_data_task(basicmysql $db, basicmodel $model = null, $param, $default)
    {
        if ('update_value' == $this->m_action) {
            return $this->update_value($db, $model, $param, $default);
        }
        return parent::on_data_task($db, $model, $param, $default);
    }

    protected function update_value(basicmysql $db, basicmodel $model = null, $param, $default)
    {
        $sql = "update dc_agents_statistics_day set ";
        $sql .= "statistics_data = statistics_data + " . $param['add_data'];
        $sql .= ",statistics_income = floor(statistics_data / " . $param['coin_rate'] . ")";
        $sql .= ",statistics_my_data = statistics_my_data + " . $param['my_add_data'];
        $sql .= ",statistics_my_income = floor(statistics_my_data / " . $param['coin_rate'] . ")";
        $sql .= ",statistics_share_money_high = " . $param['agent_get_rate'];
        $sql .= " where statistics_id = " . $param['statistics_id'];
        $re = $db->query($sql);
        return $re;
    }

}

?>