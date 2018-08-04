<?php
	/**
	 +---------------------------------------------------------- 
	 * date: 2018-01-23 11:44:40
	 +---------------------------------------------------------- 
	 * author: Raoxiaoya
	 +---------------------------------------------------------- 
	 * describe: 代理分成计算--货币消耗
	 +---------------------------------------------------------- 
	 */
	class agentearnstatmanager {
		protected $logtag = 'playerstatistical';
		protected $agent ;
		protected $app;
		protected $record;
		protected $money_task;
		protected $default_coin_rate = 10000;
		protected $agents_relation = array() ;
		protected $deep = 0;
		/**
		 * 单条消耗的代理收益
		 * @param  basicdi        $app    
		 * @param  basicmodelimpl $record 单条消耗记录
		 * @param  [type]         $param  额外参数
		 * @return [type]                 
		 */
		public function stat_logic(basicdi $app, basicmodelimpl $record, $param = null) {
			$this->app = $app;
			$this->money_task = new moneychangedatatask();
			$this->record = $this->money_task->format_model($record);
			$this->record['coin_rate'] = $this->get_coin_transfer_rate();
			
			$player_id = $this->record['change_money_player_id'];
			// 代理信息
			$agent = $this->get_agent_info($player_id);
			if(!$agent){
				return false;
			}

			//$cost_detail = "[".date('Y-m-d H:i:s',$this->record['change_money_time']).",".$this->record['change_money_game_id'].",".$game['game_name'].",".$this->record['change_money_money_type'].",".$this->record['change_money_tax'].",".$this->record['coin_rate'].",".$direct_rate."]";
        	// $this->record['statistics_cost_detail'] = $cost_detail;
	        $this->record['statistics_cost_detail'] = '';

	        // 玩家按小时累积消耗
			if(!$this->update_hour_data($param)) {
				return false;
			}
			// 玩家天累积--转换为：元,四位小数
			if(!$this->update_day_data($param)) {
				return false;
			}
			
			return true;
		}

		protected function update_hour_data($param = null) {
			$record_time = $this->record['change_money_time'];
			$statistics_date = date('Y-m-d H:00:00',$record_time);
			$statistics_time = strtotime($statistics_date);
			$daymodel = new agentsstatisticshourmodel($this->app);
			// 查询是否存在
			$record_hour = $daymodel->getrecord(array('statistics_time'=>$statistics_time,'statistics_money_type'=>$this->record['change_money_money_type'],'statistics_player_id'=>$this->record['change_money_player_id'],'statistics_game_id'=>$this->record['change_money_game_id']));
			if($record_hour){
				$data = array(
					'statistics_id' =>$record_hour['statistics_id'],
					'add_num'       =>$this->record['change_money_tax'],
					'detail'		=>"''"
				);
				$re = $daymodel->updateplayerbyhour($data);
			}else{
				$arr = array(
					'statistics_parent_agents_id' =>$this->agent['agent_id'],// 直属代理
					'statistics_player_id'        =>$this->record['change_money_player_id'],
					'statistics_money_type'       =>$this->record['change_money_money_type'],
					'statistics_money_data'       =>$this->record['change_money_tax'],
					'statistics_cost_detail'      =>$this->record['statistics_cost_detail'],
					'statistics_time'             =>$statistics_time,
					'statistics_date'             =>$statistics_date,
					'statistics_add_time'         =>time()
				);
				$re = $daymodel->save_data_model($arr);
			}

			return $re;
		}
		
		protected function update_day_data($param = null) {
			// 直属代理冻结或者未开启H5后台，不统计
			if($this->agent['agent_login_status'] != 1){
				return true;
			}
			$record_time = $this->record['change_money_time'];
			$statistics_date = date('Y-m-d 00:00:00',$record_time);
			$statistics_time = strtotime($statistics_date);
			
			// 货币转换配置 10000  暂定0点才可以更改
			$coin_rate = $this->record['coin_rate'];
			// 直属代理分成比例
			
			$agent_get_rate = $this->agent_get_rate();
			if(!$agent_get_rate){
				return true;
			}
			//直属代理拿到的货币数*100倍
			$my_data = $this->record['change_money_tax'] * $agent_get_rate['income_agent'];
			// 消耗*100倍
			$add_data = $this->record['change_money_tax'] * 100;

			$update_data = array(
				'statistics_time' =>$statistics_time,
				'statistics_date' =>$statistics_date,
				'coin_rate'       =>$coin_rate,
				'add_data'        =>$add_data,
				'all_income'      =>floor($add_data / $coin_rate) ,// 平台总收益：分
				'my_add_data'     =>$my_data,
				'my_income'       =>floor($my_data / $coin_rate) ,// 直属代理拿到的收益：分
				'agent_get_rate'  =>$agent_get_rate  ,
				'detail'		  =>$this->record['statistics_cost_detail']
			);
			// 更新dc_agents_statistics_day
			$re1 = $this->update_statistics_day($update_data);
			// 更新dc_agents_promoters_statistics
			$re2 = $this->update_promoters_statistics($update_data);
			return $re1 && $re2;
		}

		protected function update_statistics_day($update_data){
			$daymodel = new agentsstatisticsdaymodel($this->app);
			// 查询是否存在
			$record_day = $daymodel->getrecord(array('statistics_time'=>$update_data['statistics_time'],'statistics_money_type'=>$this->record['change_money_money_type'],'statistics_player_id'=>$this->record['change_money_player_id'],'statistics_game_id'=>$this->record['change_money_game_id']));
			if($record_day){
				$data = array(
					'statistics_id'  =>$record_day['statistics_id'],
					'add_data'       =>$update_data['add_data'],
					'coin_rate'      =>$update_data['coin_rate'],
					'my_add_data'    =>$update_data['my_add_data'],
					'agent_get_rate' =>$update_data['agent_get_rate']['income_agent'],
					'detail'		 =>"''"
				);
				$re = $daymodel->updateplayerbyday($data);
			}else{
				$arr = array(
					'statistics_parent_agents_id' =>$this->agent['agent_id'],
					'statistics_super_agents_id'  =>$this->agent['promoters_agent_top_agentid'],
					'statistics_player_id'        =>$this->record['change_money_player_id'],
					'statistics_type'             =>0,
					'statistics_money_type'       =>$this->record['change_money_money_type'],
					'statistics_money_type_rate'  =>$update_data['coin_rate'],// 当天的兑换比保持一样
					'statistics_data'             =>$update_data['add_data'],
					'statistics_income'           =>$update_data['all_income'],
					'statistics_my_data'          =>$update_data['my_add_data'],
					'statistics_my_income'        =>$update_data['my_income'],
					'statistics_share_money_low'  =>$update_data['agent_get_rate']['income_agent'],// 动态
					'statistics_cost_detail'      =>$update_data['detail'],
					'statistics_time'             =>$update_data['statistics_time'],
					'statistics_date'             =>$update_data['statistics_date'],
					'statistics_add_time'         =>time()
				);
				$re = $daymodel->save_data_model($arr);
			}

			return $re;
		}

		protected function update_promoters_statistics($update_data){
			// 各个代理节点
			$agentmode = new agentinfomodel($this->app);
			$this->get_agents_relation($agentmode,$this->agent['agent_id']);
			$this->init_statistics_list();
			$flag = true;
			$promotmodel = new agentsstatisticspromotermodel($this->app);
			foreach($this->agents_relation as $k => $v){
				$update_data['my_add_data'] = $update_data['add_data'] * $v['income_rate'] / 100 ;
				$update_data['my_income']   = floor($update_data['my_add_data'] / $update_data['coin_rate']) ;
				if($v['statistics_from'] == 1){
					// 直属的记录累积消耗
					$cost_day_add = $update_data['add_data'];
				}else{
					$cost_day_add = 0;
				}
				// 查询是否存在
				$record_day = $promotmodel->getrecord(
						array(
							'statistics_time'       =>$update_data['statistics_time'],
							'statistics_money_type' =>$this->record['change_money_money_type'],
							'statistics_agents_id'  =>$v['agent_id'],
							'statistics_from'       =>$v['statistics_from'],
							'statistics_from_value' =>$v['statistics_from_value']
						)
					);
				if($record_day){
					$data = array(
						'statistics_id'  =>$record_day['statistics_id'],
						'coin_rate'      =>$update_data['coin_rate'],
						'my_add_data'    =>$update_data['my_add_data'],
						'agent_get_rate' =>$v['income_rate'],
						'add_data'		 =>$cost_day_add
					);
					$re = $promotmodel->updateplayerbypromot($data);
				}else{
					$arr = array(
						'statistics_agents_id'        =>$v['agent_id'],
						'statistics_agents_player_id' =>$v['agent_player_id'],
						'statistics_super_agents_id'  =>$this->agent['promoters_agent_top_agentid'],
						'statistics_from'             =>$v['statistics_from'],
						'statistics_from_value'       =>$v['statistics_from_value'],
						'statistics_type'             =>0,
						'statistics_money_type'       =>$this->record['change_money_money_type'],
						'statistics_money_type_rate'  =>$update_data['coin_rate'],// 当天的兑换比保持一样
						'statistics_data'             =>$cost_day_add,
						'statistics_income'           =>0,
						'statistics_my_data'          =>$update_data['my_add_data'],
						'statistics_my_income'        =>$update_data['my_income'],
						'statistics_share_money_low'  =>$v['income_rate'],// 动态
						'statistics_share_money_high' =>0,// 动态
						'statistics_time'             =>$update_data['statistics_time'],
						'statistics_date'             =>$update_data['statistics_date'],
						'statistics_add_time'         =>time()
					);
					$re = $promotmodel->save_data_model($arr);
				}
				if(!$re){
					$flag = false;
					break;
				}
			}

			return $flag;
		}
		// 直属代理及其一级二级代理信息
		// agent_id 父级id
		protected function get_agents_relation($agentmode,$agent_id){
			for($i = 0 ; $i < 3 ; $i++) {
				$agent_info = $agentmode->get_agents_relationship($agent_id , $i + 1 , $this->app);
				if(!$agent_info){
					return true;
				}
				array_push($this->agents_relation,$agent_info);
				$agent_id = $agent_info['agent_parentid'];
			}
		}

		protected function init_statistics_list() {
			if(empty($this->agents_relation)){
				return array();
			}
			foreach($this->agents_relation as $key => $val){
				// 拿的都是直属的
				$this->agents_relation[$key]['statistics_from'] = $key + 1;
				$this->agents_relation[$key]['statistics_from_value'] = $this->agents_relation[0]['agent_id'];
			}
			// 保留三级
			if(count($this->agents_relation) > 3){
				$this->agents_relation = array_slice($this->agents_relation, 0, 3);
			}
		}
		// 自身推广信息，直属代理的信息
		protected function get_agent_info($player_id){
			$promoterstask = new promotersinfodatatask();
	        $promoterstask->set_action('select_join');
	        $agent = $this->app->m_server->process_database($promoterstask, null, ['player_id'=>$player_id], null);
	        if(empty($agent)){
	        	BASIC_LOG_ERROR($this->logtag,'%s',"player_id={$player_id}的直属代理信息异常");
	        	return false;
	        }else{
	        	$this->agent = $agent[0];
	        }

	        return $this->agent;
		}

		protected function get_coin_transfer_rate(){
			$coin_task = new moneyrateinfodatatask();
			$coin_task->set_action('select');
	        $coin_task->append_where(array('money_rate_type' =>$this->record['change_money_money_type'],'money_rate_unit_type'=>1));//元
	        $data = $this->app->m_server->process_database($coin_task, null, null, null);
	        if(!$data){
	        	BASIC_LOG_ERROR($this->logtag,'%s',"货币转换比例未配置");
	        	return $this->default_coin_rate;
	        }
	        $data = $coin_task->format_model($data);
	        return $data['money_rate_value'];
		}

		protected function agent_get_rate(){
			$coin_task = new moneyrateinfodatatask();
			$coin_task->set_action('select_income');
	        $data = $this->app->m_server->process_database($coin_task, null, $this->agent, null);
	        if(!$data){
	        	BASIC_LOG_ERROR($this->logtag,'%s',"代理信息有误，推广人数不够");
	        }

	        return $data;
		}

		// 截取数字，非四舍五入
		public function cut_data_by_len($data,$len = 4){
			$pows = pow(10,$len);
			$temp = $data * $pows;
			$temp = (int)$temp;
			return $temp / $pows ;
		}

	}
?>