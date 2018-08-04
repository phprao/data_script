<?php
	class record_recordgamedatahelper extends basicsingleton{

		public function record_data(basicdi $app,$game_id,$room_id,$desk_id,$time_value,$value) {
			$trans = new basictransactiontask();
        	$app->m_server->process_database($trans,null,null,null);

        	$result = array();
        	$result['code'] = 0;
        	$result['desc'] = '';
        	$ok = false;
        	do{
        		$model = new gamerecordmodel($app);
				
				$model->insert('game_record_game_id',$game_id);
				$model->insert('game_record_room_id',$room_id);
				$model->insert('game_record_desk_no',$desk_id);
				$model->insert('game_record_game_over_time',$time_value);
				$model->insert('game_record_player_id',$value);

				$tips = " (game_id( $game_id ) room_id($room_id) desk_id( $desk_id) time_value($time_value) player_id( $value ))";
				if(!$model->get_redis_model())  {
					$result['code'] = 1;
					$result['desc'] = 'game record get_redis_model fail'.$tips;
					break;
				}
				//var_dump($model);
				if(!$model->save_data_model()) {
					$result['code'] = 2;
					$result['desc'] = 'game record save_data_model fail'.$tips;
					break;
				}

				if(!$model->delete_redis_model()) {
					$result['code'] = 3;
					$result['desc'] = 'game record delete_redis_model fail'.$tips;
					break;
				}
				$ok = true;
        	}while(false);
        	if($ok) {
        		$trans->commit();
        	}else {
        		$trans->rollback();
        	}

        	return $result;
		}

		public function record_game_all(basicdi $app) {
			
			for ($i=0; $i < 10; $i++) { 
				$this->record_game_data($app,$i);
			}
			//$this->record_game_data($app,0);
		}

		protected function record_game_data(basicdi $app,$db_index) {
			$task = new mainscriptredistask();
			$task->set_action('scan_keys');
			$task->select_redis_database($db_index);

			$list = array();
			$list['iter'] = null;
			$list['list'] = array();
			$pattern = 'gamerecord:*';
			do
			{
				$list = $app->m_server->process_redis($task,$list['iter'],$pattern,null);
				//var_dump($list);
				$this->record_list_data($app,$list['list']);

			}while($list['iter'] >0);
		}

		protected function record_list_data(basicdi $app,$list) {
			foreach ($list as $value) {
				$this->save_data($app,$value);
			}
		}

		protected function save_data(basicdi $app,$item) {
			//var_dump($item);
			$list = explode(':',$item);
			if(count($list) < 6) return ;
			
			$game_id = $list[1];
			$room_id = $list[2];
			$desk_id = $list[3];
			$time_value = $list[4];
			$player_id = $list[5];
			//var_dump($player_id);
			//var_dump($time_value);
			$cur_time = time();
			$use_time = $cur_time - $time_value;
			if( $use_time < 300) {
				//var_dump($use_time);
				return;
			}
			//var_dump($item);
			//$value['change_money_player_id'] = $player_id;
			//$value['change_money_time'] = $time_value;
			$result = $this->record_data($app,$game_id,$room_id,$desk_id,$time_value,$player_id);
		}
	}
?>