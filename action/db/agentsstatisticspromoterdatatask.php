<?php
	class agentsstatisticspromoterdatatask extends basicdatatask{
		public function __construct() {
			parent::__construct();
			basicfields::playermoneychangepromoter_fileds($this);
			$this->set_data_table_info('dc_agents_promoters_statistics','statistics_id');
		}

		public function on_data_task(basicmysql $db,basicmodel $model = null,$param,$default) {
            if('update_value' == $this->m_action) {
                return $this->update_value($db,$model,$param,$default);
            }
            if('select_yesterday_sum' == $this->m_action) {
                return $this->select_yesterday_sum($db,$model,$param,$default);
            }
            if('update_yesterday_status' == $this->m_action) {
                return $this->update_yesterday_status($db,$model,$param,$default);
            }
            return parent::on_data_task($db,$model,$param,$default);
        }

        protected function update_value(basicmysql $db,basicmodel $model =null,$param,$default){
        	$sql = "UPDATE dc_agents_promoters_statistics set ";
            $sql .= "statistics_data = statistics_data + ".$param['add_data'];
            $sql .= ",statistics_my_data = statistics_my_data + ".$param['my_add_data'];
            $sql .= ",statistics_my_income = floor(statistics_my_data / ".$param['coin_rate'].")";
            $sql .= ",statistics_share_money_high = ".$param['agent_get_rate'];
            $sql .= " where statistics_id = ".$param['statistics_id'];
            $re = $db->query($sql);
            return $re;
        }

        protected function select_yesterday_sum($db,$model,$param,$default){
            $sql = "SELECT statistics_agents_id,statistics_money_type,SUM(statistics_my_income) as total,statistics_time from dc_agents_promoters_statistics where statistics_time <= ".$param['statistics_time']." and statistics_status = ".$param['statistics_status']." group by statistics_agents_id limit ".$param['limit'];
            $re = $db->find($sql);
            return $re;
        }

        protected function update_yesterday_status($db,$model,$param,$default){
            $sql = "UPDATE dc_agents_promoters_statistics set ";
            $sql .= "statistics_status = 1 ";
            $sql .= "where statistics_time = ".$param['statistics_time']." and statistics_status = ".$param['statistics_status']." and statistics_agents_id = ".$param['statistics_agents_id'];
            $re = $db->query($sql);
            return $re;
        }

	}

?>