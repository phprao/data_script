<?php

class agentinfodatatask extends basicdatatask
{
    public function __construct()
    {
        parent::__construct();
        basicfields::agentinfo_fields($this);
        $this->set_data_table_info('dc_agent_info', 'agent_id');

    }

    public function on_data_task(basicmysql $db, basicmodel $model = null, $param, $default)
    {
        if ('select_parentid' == $this->m_action) {
            return $this->select_parentid($db, $model, $param, $default);
        }
        if ('select_agents' == $this->m_action) {
            return $this->select_agents($db, $model, $param, $default);
        }
        return parent::on_data_task($db, $model, $param, $default);
    }


    protected function select_parentid(basicmysql $db, basicmodel $model = null, $param, $default)
    {

        if (is_null($db)) {
            return $default;
        }

        $result = null;
        if (empty($this->m_where)) {
            $sql = 'select *  from ' . $this->m_table_name . ' ' . $this->m_other;
            $data = $db->find($sql);
            if (is_null($data) || 0 == count($data)) {
                return $default;
            }
            $this->m_data = $data[0];
            return $this->m_data['total'];
        } else {
            $cond = '';
            foreach ($this->m_where as $k => $v) {
                if (is_array($v) && isset($v[0], $v[1])) {
                    if (($v[0] == 'in' || $v[0] == 'IN') && is_array($v[1])) {
                        $cond .= "`$k` IN ('" . implode("','", $v[1]) . "') AND ";
                    } elseif (!is_array($v[1])) {
                        $cond .= "`$k` $v[0] '$v[1]' AND ";
                    }
                } else {
                    $cond .= "`$k` = '$v' AND ";
                }
            }
            $cond = substr($cond, 0, strlen($cond) - 5);
            $sql = "SELECT *  FROM `{$this->m_table_name}` WHERE $cond $this->m_other";
            $data = $db->find($sql);
            if (is_null($data) || 0 == count($data)) {
                return $default;
            }
            $this->m_data = $data;
            return $this->m_data;
        }
    }

    protected function select_agents(basicmysql $db, basicmodel $model = null, $param, $default)
    {
        $sql = "select * from dc_agent_info where agent_level > 1 and agent_id = " . $param['agent_id'];
        $info = $db->find($sql);
        if (!$info) {
            return false;
        } else {
            $info = $info[0];
        }
        switch ($param['deep']) {
            case 1:
                $income_deep = 'income_agent';
                break;
            case 2:
                $income_deep = 'income_level_one';
                break;
            case 3:
                $income_deep = 'income_level_two';
                break;
            default:
                $income_deep = '';
        }
        $level = 0;

        $sql = "select income_count_level,income_promote_count,income_agent,income_level_one,income_level_two,income_level_three from dc_agent_income_config where income_agent_id = ".$param['agent_id']." order by income_count_level asc";
        $config = $db->find($sql);
        if(!$config){
            $sql = "select income_count_level,income_promote_count,income_agent,income_level_one,income_level_two,income_level_three from dc_agent_income_config where income_agent_id = ".$info['agent_top_agentid']." order by income_count_level asc";
            $config = $db->find($sql);
            if(!$config){
                $sql = "select income_count_level,income_promote_count,income_agent,income_level_one,income_level_two,income_level_three from dc_agent_income_config where income_agent_id = 0 order by income_count_level asc";
                $config = $db->find($sql);
            }
        }
        foreach ($config as $val) {
            if ($info['agent_promote_count'] >= $val['income_promote_count']) {
                if ($income_deep) {
                    $level = $val[$income_deep];
                }
            }
        }
        $info['income_rate'] = $level;
        return $info;
    }

}

?>