<?php
	ini_set('memory_limit','128M');//ini_set('memory_limit','128M');
	//set_time_limit(300);//5分钟
	
	class recordscript extends basicaction{

		protected function logic(basicdi $app) {
			if(!$this->check_time($app)) return ;
			record_recordplayerinforecord::get_instance()->record_all_playerinfo($app);
		}

		protected function check_time(basicdi $app) {
			$util = new utilitymodel($app);

			$last_time = $util->get_data_info($app,'record_script','last_time',0);

			$cur_time = time();
			$use_time = $cur_time - $last_time;
			if($use_time < 1)  return false;

			$util->set_data_info($app,'record_script','last_time',$cur_time);
			return true;
		}
	}
?>