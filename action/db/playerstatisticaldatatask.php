<?php
	class playerstatisticaldatatask extends basicdatatask{
		public function __construct() {
			parent::__construct();
			basicfields::playerstatistical_fields($this);
			$this->set_data_table_info('dc_player_statistical','statistical_id');

		}
		public function on_data_task(basicmysql $db,basicmodel $model = null,$param,$default) {
            if ('select_user' == $this->m_action) {
                return $this->select_user($db, $model, $param, $default);
            }
            if ('select_sum' == $this->m_action) {
                return $this->select_sum($db, $model, $param, $default);
            }
			return parent::on_data_task($db,$model,$param,$default);
		}


        protected function select_user(basicmysql $db, basicmodel $model = null, $param, $default)
        {

            if (is_null($db)) {
                return $default;
            }

            $result = null;
            if (empty($this->m_where)) {
                $sql = 'select * from ' . $this->m_table_name . ' ' . $this->m_other;
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


	}
?>