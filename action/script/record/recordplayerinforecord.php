<?php
	/*
	 *  @desc: 回写玩家信息
	 */
	class record_recordplayerinforecord extends basicsingleton{

		public function record_all_playerinfo(basicdi $app) {
			//$model = new basicdatamodel();
			//$model->insert('player_id',0);

			//$param = array();
			//$param['iter'] = null;
			//$param['pattern'] = 'user_info:0:*';
			//$param['count'] = 1000;
			//$task = new playerredistask();
			//$task->set_action('scan');
			//$result = $app->m_server->process_redis($task,$model,$param,null);
			$result = $this->get_all_player_list($app);
			//var_dump($result);
			$this->sync_player($app,$result);
		}

		protected function get_all_player_list(basicdi $app) {

			/*$list = array();
			for($i = 0; $i< 10; $i++) {
				$data = $this->get_one_database_all_player_list($app,$i);
				$list = array_merge($list,$data);break;
			}
			return $list;
			*/
			$select = $this->get_select($app);
			$list = $this->get_one_database_all_player_list($app,$select);
			return $list; 
		}


		protected function get_one_database_all_player_list(basicdi $app,$db_num) {
			/*$list = array();
			for($i = 0; $i< 100; $i++) {
				$data = $this->get_player_list($app,$db_num,$i);
				$list = array_merge($list,$data);
			}
			
			$list = array();
			for($i = 0; $i< 10; $i++) {
				$index = $this->get_index($app,$db_num);
				$data = $this->get_player_list($app,$db_num,$index);
				$list = array_merge($list,$data);
			}
			*/
			$index = $this->get_index($app,$db_num);
			$list = $this->get_player_list($app,$db_num,$index);
			return $list;
		}

		protected function get_player_list(basicdi $app,$db_num,$index) {
			

			//$db_num = 0;
			$list = array();
			$iter = null;
			$count = 1000;
			$model = new playerinfomodel($app);
			do
			{
				$result = $model->get_all_player_id_list($db_num,$iter,$index,$count);
				$iter = $result['iter'];
				$list = array_merge($list,$result['list']);
			}while($iter != 0);
			//var_dump($list);

			/*$model_player = new playerinfomodel($app);
			$player_list = array();
			foreach ($list as $key) {
				$player = $model_player->get_player_info_by_keys($key);
				if(is_null($player )) continue;
				array_push($player_list, $player->get('player_id',0));
			}
			return $player_list;
			*/
			return $list;
		}

		protected function sync_player(basicdi $app, $list) {
			//$model = new playerinfomodel($app);
			foreach ($list as $key ){
				//$model->insert('player_id',$key);
				playerredisblock::block($app)->sync_player_redis_to_database($key);
				//break;
			}
		}


		protected function get_select(basicdi $app) {
			$util = new utilitymodel($app);

			$select = $util->get_data_info($app,'player_record:database','select',0);
			if(!$select) {
				$select = 0;
			}
			$next = ($select + 1) % 10;
		
			$util->set_data_info($app,'player_record:database','select',$next);
			return $select;
		}

		protected function get_index(basicdi $app,$select) {
			$util = new utilitymodel($app);

			$index = $util->get_data_info($app,'player_record:database:index','select_'.$select,0);
			if(!$index) {
				$index = 0;
			}
			$next = ($index + 1) % 100;
		
			$util->set_data_info($app,'player_record:database:index','select_'.$select,$next);
			return $index;
		}
	}
?>