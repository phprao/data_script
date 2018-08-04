<?php
class moneychangerecordlogmanager {
	protected $logtag = 'playerstatistical';
	protected $app;
	protected $record;

	public function stat_logic(basicdi $app, basicmodelimpl $record, $param = null) {
		$this->app = $app;
		$this->money_task = new moneychangedatatask();
		$this->record = $this->money_task->format_model($record);
		$player_id = $this->record['change_money_player_id'];
		// 代理信息
		$agent = $this->get_agent_info($player_id);
		if(!$agent){
			return false;
		}
		
		$agent_get_rate = $this->agent_get_rate();
		if(!$agent_get_rate || $this->agent['agent_parentid'] == 0){
			$direct_rate = 0;
		}else{
			$direct_rate = $agent_get_rate['income_agent'];
		}

		// 游戏信息
		$gamemodel = new gameinfomodel($this->app);
    	$game = $gamemodel->get_one_model(array('game_id'=>$this->record['change_money_game_id']));
    	// 房间信息
    	$room_task = new clubroomlistdatatask();
        $room_task->set_action('select');
        $room_task->append_where(['club_room_id' =>$this->record['change_money_club_room_id']]);
        $roominfo = $this->app->m_server->process_database($room_task, null, null, null);

        // 上一层推广信息
        $promotion = $this->promotion_agents($this->agent);

    	// 玩家游戏及消耗记录
    	if(!$this->add_player_money_change($game,$roominfo,$direct_rate,$promotion,$param)) {
			return false;
		}

		return true;
	}

	protected function add_player_money_change($game,$roominfo,$direct_rate,$promotion,$param = null) {
		$model = new moneychangeplayermodel($this->app);
		$data = array(
			'change_money_player_id'        =>$this->record['change_money_player_id'],
			'change_money_parent_agents_id' =>$this->agent['agent_id'],
			'change_money_super_agents_id'  =>$this->agent['promoters_agent_top_agentid'],
			'change_money_club_id'			=>$this->record['change_money_club_id'],
			'change_money_club_room_id' 	=>$this->record['change_money_club_room_id'],
			'change_money_club_desk_no'		=>$this->record['change_money_club_desk_no'],
			'change_money_club_desk_id' 	=>$this->record['change_money_club_desk_id'],// 唯一
			'change_money_room_id'			=>$this->record['change_money_room_id'],// 房间号
			'change_money_room_name'		=>$roominfo ? $roominfo->get('club_room_name',0) : 0,// 房间名称
			'change_money_desk_no'			=>$this->record['change_money_desk_no'],
			'change_money_game_id'          =>$this->record['change_money_game_id'],
			'change_money_game_name'        =>$game['game_name'],
			'change_money_type'             =>$this->record['change_money_type'],
			'change_money_tax'              =>$this->record['change_money_tax'] * 100,
			'change_money_my_tax'           =>$this->record['change_money_tax'] * $direct_rate,// 直属所得
			'change_money_share_rate'       =>$direct_rate,
			'change_money_one_tax'			=>$this->record['change_money_tax'] * $promotion[0]['rate'],
			'change_money_one_rate'			=>$promotion[0]['rate'],
			'change_money_one_agents_id'	=>$promotion[0]['agent_id'],
			'change_money_two_tax'			=>$this->record['change_money_tax'] * $promotion[1]['rate'],
			'change_money_two_rate'			=>$promotion[1]['rate'],
			'change_money_two_agents_id'	=>$promotion[1]['agent_id'],
			'change_money_money_type'       =>$this->record['change_money_money_type'],
			'change_money_money_value'      =>$this->record['change_money_money_value'],
			'change_money_begin_value'      =>$this->record['change_money_begin_value'],
			'change_money_after_value'      =>$this->record['change_money_after_value'],
			'change_money_time'             =>$this->record['change_money_time'],
			'change_money_date'             =>date('Y-m-d H:i:s',$this->record['change_money_time']),
			'change_money_param'            =>$this->record['change_money_param']
		);
		$re = $model->save_data_model($data);
		return $re;
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

	protected function agent_get_rate(){
		$coin_task = new moneyrateinfodatatask();
		$coin_task->set_action('select_income');
        $data = $this->app->m_server->process_database($coin_task, null, $this->agent, null);
        if(!$data){
        	BASIC_LOG_ERROR($this->logtag,'%s',"代理信息有误，推广人数不够");
        }
        return $data;
	}

	protected function promotion_agents($agent){
		$promotion = [
        	['agent_id'=>0,'rate'=>0],
        	['agent_id'=>0,'rate'=>0]
        ];
        if($agent['agent_parentid'] == 0 || $agent['agent_p_parentid'] == 0){
        	return $promotion;
        }
        $condition = (object)[];
		$condition->agent_login_status = 1;
		$agentModel = new agentinfomodel($this->app);
        
    	// 查一层
    	$one_info = $agentModel->get_agent_by_id($agent['agent_parentid'],$condition);
    	if($one_info){
    		$param = [
				'agent_id'            =>$one_info->get('agent_id',0),
				'agent_top_agentid'   =>$one_info->get('agent_top_agentid',0),
				'agent_login_status'  =>$one_info->get('agent_login_status',0),
				'agent_promote_count' =>$one_info->get('agent_promote_count',0)
    		];
    		$coin_task = new moneyrateinfodatatask();
			$coin_task->set_action('select_income');
    		$data = $this->app->m_server->process_database($coin_task, null, $param, null);
    		if($data){
				$promotion[0]['agent_id'] = $param['agent_id'];
				$promotion[0]['rate']     = $data['income_level_one'];
    		}
    	}
        
        if($agent['agent_top_agentid'] == $agent['agent_p_parentid']){
			return $promotion;
        }
		
		// 查二层
		$two_info = $agentModel->get_agent_by_id($agent['agent_p_parentid'],$condition);
    	if($two_info){
    		$param2 = [
				'agent_id'            =>$two_info->get('agent_id',0),
				'agent_top_agentid'   =>$two_info->get('agent_top_agentid',0),
				'agent_login_status'  =>$two_info->get('agent_login_status',0),
				'agent_promote_count' =>$two_info->get('agent_promote_count',0)
    		];
    		$coin_task = new moneyrateinfodatatask();
			$coin_task->set_action('select_income');
    		$data = $this->app->m_server->process_database($coin_task, null, $param2, null);
    		if($data){
				$promotion[1]['agent_id'] = $param2['agent_id'];
				$promotion[1]['rate']     = $data['income_level_two'];
    		}
    	}

		return $promotion;

	}

}