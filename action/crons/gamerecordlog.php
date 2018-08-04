<?php
	/**
	 +---------------------------------------------------------- 
	 * date: 2018-01-15 15:21:24
	 +---------------------------------------------------------- 
	 * author: Raoxiaoya
	 +---------------------------------------------------------- 
	 * describe: 游戏记录处理
	 +---------------------------------------------------------- 
	 */
	class gamerecordlog extends basicaction{
		private $limit_count = 1;
		private $app;
		private $board_id;
		private $gamename;

		protected function before() {
			return parent::before();
		}
		
		protected function logic(basicdi $app) {
			$this->app = $app;
			$list = $this->getlist();
			if($list){
				foreach($list as $val){
					$this->deal_list($val);
				}
			}
		}

		protected function deal_list($data) {
			if(!$data){
				return true;
			}
			$M = new basictransactiontask();
			$this->app->m_server->process_database($M,null,null,null);

			// 生成牌局id
			$r0 = $this->createboardid($data);
			if(!$r0){
				$M->rollback();
				return false;
			}else{
				$this->board_id = $r0;
			}

			// 生成记录
			$r1 = $this->createlog($data);
			// 复制记录
			$r2 = $this->copylog($data);
			// 删除记录
			$r3 = $this->delrecord($data);
			// 战绩记录
			$r4 = $this->beatrecord($data);

			if($r1 && $r2 && $r3 !== false && $r4){
				$M->commit();
				return true;
			}else{
				$M->rollback();
				return false;
			}
		}

		protected function getlist() {
			$recordmodel = new gamerecordmodel($this->app);
			$other = 'order by game_record_id ASC limit '.$this->limit_count;
			$list = $recordmodel->get_list_model($other);
			return $list;
		}

		protected function createlog($data) {
			$logmodel = new gamerecordlogmodel($this->app);
			$arr = $this->init_data($data);
			$re = $logmodel->save_data_model($arr);
			return $re;
		}

		protected function copylog($data) {
			$arr = $this->init_data($data);
			$logmodel = new gamerecordstoremodel($this->app);
			$re = $logmodel->save_data_model($arr);
			return $re;
		}

		protected function delrecord($data) {
			$id = $data['game_record_id'];
			$recordmodel = new gamerecordmodel($this->app);
			return $recordmodel->delete_data_model(array('game_record_id'=>$id));
		}

		// 战绩记录
		protected function beatrecord($data) {
			$pmode = new playerviewmodel($this->app);
			$info = $pmode->getinfobyid(array('player_id'=>$data['game_record_player_id']));
			$arr = array(
				'game_beat_board_id'       =>$this->board_id,
				'game_beat_player_id'      =>$data['game_record_player_id'],
				'game_beat_player_nick'    =>$info['player_nickname'],
				'game_beat_player_head'    =>$info['player_header_image'],
				'game_beat_room_no'        =>$data['game_record_club_room_no'],
				'game_beat_readback'       =>'',
				'game_beat_over_time'      =>$data['game_record_game_over_time'],
				'game_beat_game_id'        =>$data['game_record_game_id'],
				'game_beat_game_name'      =>$this->gamename,
				'game_beat_player_club_id' =>$data['game_record_player_club_id'],
				'game_beat_club_id'        =>$data['game_record_club_id'],
				'game_beat_win_state'      =>$data['game_record_win_state'],
				'game_beat_score_type'     =>$data['game_record_score_type'],
				'game_beat_score_value'    =>$data['game_record_score_value'],
				'game_beat_time'           =>time()
			);
			$beatmodel = new gamebeatrecordmodel($this->app);
			$re = $beatmodel->save_data_model($arr);
			return $re;
		}

		// 牌局id
		protected function createboardid($data) {
			$boardmodel = new gameboardinfomodel($this->app);
			$arr = array(
				'game_board_room_id'        =>$data['game_record_room_id'],
				'game_board_desk_no'        =>$data['game_record_desk_no'],
				'game_board_game_over_time' =>$data['game_record_game_over_time']
			);
			// 先查找是否存在
			$board = $boardmodel->get_one_model($arr);
			if($board){
				$re = $board['game_board_id'];
			}else{
				$arr['game_board_time'] = time();
				$re = $boardmodel->save_data_model($arr);
			}
			return $re;
		}

		protected function getgamename($gameid) {
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
			$arr['game_log_board_id']  = $this->board_id;
			$arr['game_log_game_name'] = $this->getgamename($arr['game_log_game_id']);
			$arr['game_log_data']      = date('Y-m-d H:i:s',$arr['game_log_time']);
			unset($arr['game_log_id']);
			return $arr;
		}

	}
