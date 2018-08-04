<?php
	/**
	 +---------------------------------------------------------- 
	 * date: 2018-02-08 20:11:05
	 +---------------------------------------------------------- 
	 * author: Raoxiaoya
	 +---------------------------------------------------------- 
	 * describe: 每日各个游戏堆叠图
	 +---------------------------------------------------------- 
	 */

	class gamecostsummanager {
		protected $logtag = 'playerstatistical';
		protected $app;
		protected $record;

		public function stat_logic(basicdi $app, basicmodelimpl $record, $param = null) {
			$this->app = $app;
			$this->money_task = new moneychangedatatask();
			$this->record = $this->money_task->format_model($record);
			$round_model = new gameroundmodel($this->app);
			$curday = date('Y-m-d',$this->record['change_money_time']);
			$curtime = strtotime($curday);
			// 查找渠道信息
			$agent_model = new agentinfomodel($this->app);
			$agent_info = $agent_model->get_agent_by_player_id($this->record['change_money_player_id']);
			if(!$agent_info){
				return false;
			}
			$indexArr = [0,$agent_info->get('agent_top_agentid',0)];
			$re = true;
			foreach($indexArr as $val){
				// 是否存在当天记录
				$arr_day = array(
					'game_round_game_id'   =>$this->record['change_money_game_id'],
					'game_round_timestamp' =>$curtime,
					'game_round_channel_id'=>$val
				);
				$log = $round_model->get_one_log($arr_day);
				if($log){
					$arr_day['coins'] = $this->record['change_money_tax'];
					$re = $round_model->update_coin_num($arr_day);
				}else{
					$arr = array(
						'game_round_game_id'        =>$this->record['change_money_game_id'],
						'game_round_game_name'      =>$this->get_gamename($this->record['change_money_game_id']),
						'game_round_channel_id'		=>$val,
						'game_round_num'			=>0,
						'game_round_coins'			=>$this->record['change_money_tax'],
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

		public function get_gamename($gameid) {
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


	}