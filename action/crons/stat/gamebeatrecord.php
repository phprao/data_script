<?php
	class stat_gamebeatrecord extends basicsingleton{

		protected $m_board_id;
		protected $app;
		protected $gamename;

		public function record_data(basicdi $app,$data) {
			if(is_null($data)) return false;
			$this->app = $app;
			// 生成牌局id
			$ret = false;
			do
			{
				// 战绩局数统计
				$result = $this->game_round($data);
				if(!$result) break;

				$board_id = $this->create_board_id($data);
				if(is_null($board_id)) break;

				$this->m_board_id = $board_id;

				// 生成记录
				$result = $this->create_log($data);
				if(!$result) break;
				// 复制记录
				$result = $this->copy_log($data);
				if(!$result) break;
				// 删除记录
				$result = $this->del_record($data);
				if(!$result) break;
				// 战绩记录
				$result = $this->beat_record($data);
				if(!$result) break;
				

				$ret = true;
			}while(false);
			
			return $ret;
		}

		protected function game_round($data){
			$board_model = new gameboardinfomodel($this->app);
			$round_model = new gameroundmodel($this->app);
			$agent_model = new agentinfomodel($this->app);
			$arr = array(
				'game_board_room_id'        =>$data['game_record_room_id'],
				'game_board_desk_no'        =>$data['game_record_desk_no'],
				'game_board_game_over_time' =>$data['game_record_game_over_time']
			);
			$curday = date('Y-m-d',$data['game_record_game_over_time']);
			$curtime = strtotime($curday);

			// 先查找是否存在牌局
			$board = $board_model->get_one_model($arr);
			if($board){
				return true;
			}
			// 查找渠道信息
			$agent_info = $agent_model->get_agent_by_player_id($data['game_record_player_id']);
			if(!$agent_info){
				return false;
			}
			$indexArr = [0,$agent_info->get('agent_top_agentid',0)];
			$re = true;
			foreach($indexArr as $val){
				// 是否存在当天记录
				$arr_day = array(
					'game_round_game_id'   =>$data['game_record_game_id'],
					'game_round_timestamp' =>$curtime,
					'game_round_channel_id'=>$val
				);
				$log = $round_model->get_one_log($arr_day);
				if($log){
					$re = $round_model->update_round_num($arr_day);
				}else{
					$arr = array(
						'game_round_game_id'        =>$data['game_record_game_id'],
						'game_round_game_name'      =>$this->get_gamename($data['game_record_game_id']),
						'game_round_channel_id'		=>$val,
						'game_round_num'			=>1,
						'game_round_day'			=>$curday,
						'game_round_timestamp'		=>$curtime,
						'game_round_createtime'	    =>time()
					);
					$re = $round_model->save_data_model($arr);
				}

				if($re === false){
					$re = false;
				}
			}
			return $re;
		}

		protected function create_board_id($data) {
			$board_model = new gameboardinfomodel($this->app);
			$arr = array(
				'game_board_room_id'        =>$data['game_record_room_id'],
				'game_board_desk_no'        =>$data['game_record_desk_no'],
				'game_board_game_over_time' =>$data['game_record_game_over_time']
			);
			// 先查找是否存在
			$board = $board_model->get_one_model($arr);
			if($board){
				$re = $board['game_board_id'];
			}else{
				$arr['game_board_time'] = time();
				$re = $board_model->save_data_model($arr);
			}
			return $re;
		}

		protected function create_log($data) {
			$logmodel = new gamerecordlogmodel($this->app);
			$arr = $this->init_data($data);
			$re = $logmodel->save_data_model($arr);
			return $re;
		}

		protected function copy_log($data) {
			$arr = $this->init_data($data);
			$logmodel = new gamerecordstoremodel($this->app);
			$re = $logmodel->save_data_model($arr);
			return $re;
		}

		protected function del_record($data) {
			$id = $data['game_record_id'];
			$recordmodel = new gamerecordmodel($this->app);
			return $recordmodel->delete_data_model(array('game_record_id'=>$id));
		}

		// 战绩记录
		protected function beat_record($data) {
			// 房间信息
	    	$room_task = new clubroomlistdatatask();
	        $room_task->set_action('select');
	        $room_task->append_where(['club_room_id' =>$data['game_record_club_room_id']]);
	        $roominfo = $this->app->m_server->process_database($room_task, null, null, null);
	        
			$pmode = new playerviewmodel($this->app);
			$info = $pmode->getinfobyid(array('player_id'=>$data['game_record_player_id']));
			$arr = array(
				'game_beat_board_id'       =>$this->m_board_id,
				'game_beat_player_id'      =>$data['game_record_player_id'],
				'game_beat_player_nick'    =>$info['player_nickname'],
				'game_beat_player_head'    =>$info['player_header_image'],
				'game_beat_room_no'        =>$data['game_record_club_room_no'],
				'game_beat_readback'       =>$data['game_record_video_filename'],
				'game_beat_over_time'      =>$data['game_record_game_over_time'],
				'game_beat_game_id'        =>$data['game_record_game_id'],
				'game_beat_game_name'      =>$this->gamename,
				'game_beat_player_club_id' =>$data['game_record_player_club_id'],
				'game_beat_club_id'        =>$data['game_record_club_id'],
				'game_beat_win_state'      =>$data['game_record_win_state'],
				'game_beat_score_type'     =>$data['game_record_score_type'],
				'game_beat_score_value'    =>$data['game_record_score_value'],
				'game_beat_time'           =>time(),
				'game_beat_room_id'		   =>$data['game_record_club_room_id'],
				'game_beat_room_name'	   =>$roominfo ? $roominfo->get('club_room_name',0) : 0,// 房间名称
			);
			$beatmodel = new gamebeatrecordmodel($this->app);
			$re = $beatmodel->save_data_model($arr);
			return $re;
		}


		protected function get_gamename($gameid) {
			if(!$gameid){
				return '';
			}
			$gamemodel = new gameinfomodel($this->app);
			$gameinfo = $gamemodel->get_one_model(array('game_id'=>$gameid));
			if($gameinfo){
				$this->gamename = $gameinfo['game_name'];
				return $gameinfo['game_name'];
			}else{
				$this->gamename = '';
				return '';
			}
		}

		protected function init_data($data) {
			$arr = array();
			foreach($data as $key => $val){
				$key_init = str_replace('game_record_','game_log_',$key);
				$arr[$key_init] = $val;
			}
			$arr['game_log_board_id']  = $this->m_board_id;
			$arr['game_log_game_name'] = $this->get_gamename($arr['game_log_game_id']);
			$arr['game_log_data']      = date('Y-m-d H:i:s',$arr['game_log_time']);
			unset($arr['game_log_id']);
			return $arr;
		}

	}
?>