<?php
	class record_clearclubdeskrecord extends basicsingleton{

		public function clear_data(basicdi $app) {
			$time_value = time();
			$time_value = $time_value - (90*86400);//充值保留6个月，其他和运营无关数据保住3个月
			$data_record_task = new clubdeskinforecorddatatask();
			$data_record_task->set_action('delete_list_in');
			$data_record_task->append_where_list(array('club_desk_time'=>$time_value),basicdatatask::$WHERE_TYPE_LE);
			$result = $app->m_server->process_database($data_record_task,null,null,null);
			//var_dump($result);
			return $result;
		}
	}
?>