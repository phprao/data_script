<?php

class agentconfiginfodatatask extends basicdatatask
{
    public function __construct()
    {
        parent::__construct();
        basicfields::agentconfiginfo_fields($this);
        $this->set_data_table_info('dc_agent_config', 'agentconf_id');

    }

    public function on_data_task(basicmysql $db, basicmodel $model = null, $param, $default)
    {

        if ('select_confinfo' == $this->m_action) {
            return $this->select_confinfo($db, $model, $param, $default);
        }
        return parent::on_data_task($db, $model, $param, $default);
    }


    protected function select_confinfo(basicmysql $db, basicmodel $model = null, $param, $default)
    {
        if (is_null($db)) {
            return $default;
        }
        $columns = '*';
        if (isset($param['columns'])) {
            $columns = $param['columns'];
        }
        $join = '';
        if (isset($param['join'])) {
            $join = $param['join'];
        }
        $alias = 't';
        if (isset($param['alias'])) {
            $alias = $param['alias'];
        }
        $result = null;
        if (empty($this->m_where)) {
            $sql = 'select ' . $columns . ' from ' . $this->m_table_name . ' ' . $alias . ' ' . $join . ' ' . $this->m_other;
            $data = $db->find($sql);
            if (is_null($data) || 0 == count($data)) {
                return $default;
            }
            $this->m_data = $data[0];
            return $this->m_data;
        } else {
            $cond = '';
            foreach ($this->m_where as $k => $v) {

                if (is_array($v) && isset($v[0], $v[1])) {
                    if ($v[0] == 'exp') {
                        $cond .= "$v[1] AND ";
                    } elseif (($v[0] == 'in' || $v[0] == 'IN') && is_array($v[1])) {
                        $cond .= "$k IN ('" . implode("','", $v[1]) . "') AND ";
                    } elseif (!is_array($v[1])) {
                        $cond .= "$k $v[0] '$v[1]' AND ";
                    }
                } else {
                    $cond .= "$k = '$v' AND ";
                }
            }
            $cond = substr($cond, 0, strlen($cond) - 5);
            $sql = "SELECT $columns FROM `{$this->m_table_name}` $alias $join WHERE $cond $this->m_other";
            $data = $db->find($sql);
            if (is_null($data) || 0 == count($data)) {
                return $default;
            }
            $this->m_data = $data;
            return $this->m_data;
        }

    }
}

?>