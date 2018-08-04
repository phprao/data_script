<?php

/**
 * 货币兑换比例信息
 * Class moneyrateinfodatatask
 * @author ChangHai Zhan
 */
class moneyrateinfodatatask extends basicdatatask
{
    /**
     * moneyrateinfodatatask constructor.
     */
    public function __construct()
    {
        parent::__construct();
        basicfields::moneyrateinfo_fields($this);
        $this->set_data_table_info('dc_money_rate_info', 'money_rate_id');
    }

    public function on_data_task(basicmysql $db, basicmodel $model = null, $param, $default)
    {
        if ('select_income' == $this->m_action) {
            return $this->select_income($db, $model, $param, $default);
        }
        return parent::on_data_task($db, $model, $param, $default);
    }


    protected function select_income(basicmysql $db, basicmodel $model = null, $param, $default)
    {
        $level = array();
        // if ($param['agent_login_status'] != 1) {
        //     return false;
        // }
        $sql = "select income_count_level,income_promote_count,income_agent,income_level_one,income_level_two,income_level_three from dc_agent_income_config where income_agent_id = ".$param['agent_id']." order by income_count_level asc";
        $config = $db->find($sql);
        if(!$config){
            $sql = "select income_count_level,income_promote_count,income_agent,income_level_one,income_level_two,income_level_three from dc_agent_income_config where income_agent_id = ".$param['agent_top_agentid']." order by income_count_level asc";
            $config = $db->find($sql);
            if(!$config){
                $sql = "select income_count_level,income_promote_count,income_agent,income_level_one,income_level_two,income_level_three from dc_agent_income_config where income_agent_id = 0 order by income_count_level asc";
                $config = $db->find($sql);
            }
        }
        foreach ($config as $val) {
            if ($param['agent_promote_count'] >= $val['income_promote_count']) {
                $level = $val;
            }
        }
        if (empty($level)) {
            return false;
        } else {
            return $level;
        }

    }
}

?>