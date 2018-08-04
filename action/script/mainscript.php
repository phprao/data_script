<?php
	ini_set('memory_limit','128M');//ini_set('memory_limit','128M');
	set_time_limit(300);//3分钟
	/*
	 *  @desc:   mainscript logic
	 *  @author: xxm
	 *  @email:  237886849@qq.com
	 *  @note:   所有文件命名以小写，所有子类名以小写
	 *		     
	 */
	class mainscript extends basicaction{
		protected function logic(basicdi $app) {
			//if(!$this->check_time($app)) return ;
			record_recordmoneydatahepler::get_instance()->record_money_all($app);
			record_recordgamedatahelper::get_instance()->record_game_all($app);
			//record_clearclubdeskrecord::get_instance()->clear_data($app);
			//record_recordplayerinforecord::get_instance()->record_all_playerinfo($app);
		}

		protected function check_time(basicdi $app) {
			$util = new utilitymodel($app);

			$last_time = $util->get_data_info($app,'script_info','last_time',0);

			$cur_time = time();
			$use_time = $cur_time - $last_time;
			if($use_time < 300)  return false;

			$util->set_data_info($app,'script_info','last_time',$cur_time);
			return true;
		}

	}
?>