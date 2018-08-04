<?php

class promotersinfodatatask extends basicdatatask
{
    public function __construct()
    {
        parent::__construct();
        basicfields::promotersinfo_fields($this);
        $this->set_data_table_info('dc_promoters_info', 'promoters_id');

    }


    public function on_data_task(basicmysql $db, basicmodel $model = null, $param, $default)
    {

        if ('user_count' == $this->m_action) {
            return $this->user_count($db, $model, $param, $default);
        }

        if ('select_agent' == $this->m_action) {
            return $this->select_agent($db, $model, $param, $default);
        }

        if ('select_join' == $this->m_action) {
            return $this->select_join($db, $model, $param, $default);
        }
        return parent::on_data_task($db, $model, $param, $default);
    }


    protected function user_count(basicmysql $db, basicmodel $model = null, $param, $default)
    {

        if (is_null($db)) {
            return $default;
        }
        $columns = '';
        if (!isset($param['count'])) {
            return $default;
        } else {
            $columns = 'count(' . $param['count'] . ') as total';
        }
        if (isset($param['columns'])) {
            $columns .= ',' . $param['columns'];
        }

        $result = null;
        if (empty($this->m_where)) {
            $sql = 'select $columns from ' . $this->m_table_name . ' ' . $this->m_other;
            $result = $db->find($sql);
            if (is_null($result) || 0 == count($result)) {
                return $default;
            }
            $data = $result[0];
            $this->m_data = $data;
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

            $sql = "SELECT  $columns  FROM `{$this->m_table_name}` WHERE $cond $this->m_other";

            $data = $db->find($sql);
            if (is_null($data) || 0 == count($data)) {
                return $default;
            }
            $this->m_data = $data;
            return $this->m_data;

        }


    }


    protected function select_sum(basicmysql $db, basicmodel $model = null, $param, $default)
    {
        if (is_null($db)) {
            return $default;
        }
        $columns = '';
        if (!isset($param['sum'])) {
            return $default;
        } else {
            $columns = 'sum(' . $param['sum'] . ') as total';
        }
        if (isset($param['columns'])) {
            $columns .= ',' . $param['columns'];
        }
        $result = null;
        if (empty($this->m_where)) {
            $sql = 'select ' . $columns . ' from ' . $this->m_table_name . ' ' . $this->m_other;
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
            $sql = "SELECT $columns FROM `{$this->m_table_name}` WHERE $cond $this->m_other";
            $data = $db->find($sql);
            if (is_null($data) || 0 == count($data)) {
                return $default;
            }
            $this->m_data = $data;
            return $this->m_data;
        }
    }

    protected function select_agent(basicmysql $db, basicmodel $model = null, $param, $default)
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

    // 直属代理信息
    protected function select_join(basicmysql $db, basicmodel $model = null, $param, $default)
    {
        $sql = "select p.*,a.* from dc_promoters_info p inner join dc_agent_info a on p.promoters_agent_parentid = a.agent_id where p.promoters_player_id = " . $param['player_id'];
        $agent = $db->find($sql);
        return $agent;
    }


}

?>