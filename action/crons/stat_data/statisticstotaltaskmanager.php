<?php
/**
 +---------------------------------------------------------- 
 * date: 2018-02-09 16:00:36
 +---------------------------------------------------------- 
 * author: Raoxiaoya
 +---------------------------------------------------------- 
 * describe: 数据统计
 +---------------------------------------------------------- 
 */
error_reporting(E_ALL & ~E_NOTICE);
class statisticstotaltaskmanager
{
	protected $logtag = 'playerstatistical';
	protected $app;
	protected $record;
	protected $default_coin_rate = 10000;

	const TYPE_CHARGE_SUM   = 1; //1-充值金额（分）
	const TYPE_NEW_REGISTER = 2; //2-注册用户数（个）
	const TYPE_COIN_COST    = 3; //3-金币消耗数（个）
	const TYPE_LOGIN_IN     = 4; //4-活跃玩家数（个）
	const TYPE_SEND_COIN    = 5; //5-赠送金币数（个）
	const TYPE_GAME_PLAYER  = 6; //6-游戏玩家数（个）
	const TYPE_PRODUCE_SUM  = 7; //7-产出金币数（充值+赠送）
	const TYPE_LAST_COIN    = 8; //8-剩余金币数（最终值）
	const TYPE_PROMOTE_AWARD_SUM  = 9; //9-所有推广员奖励（天）
	const TYPE_PROMOTER_SUB_COST = 10; // 10-所有推广员（不含星级）旗下玩家消耗金币数总计

	public function stat_logic(basicdi $app, basicmodelimpl $record, $param = null) {
		$this->app = $app;
		$this->money_task = new moneychangedatatask();
		$this->record = $this->money_task->format_model($record);
		$agentmodel = new agentinfomodel($this->app);
		// 查找特代信息
		$agentdate = $agentmodel->get_agent_by_player_id($this->record['change_money_player_id']);
		$this->record['agent_top_agentid'] = $agentdate->get('agent_top_agentid',0);
		$this->record['agent_parentid'] = $agentdate->get('agent_parentid',0);
		// 查找金币人民币兑换比例
		$this->record['coin_rate'] = $this->get_coin_transfer_rate();

		$role_type = array(0,1,2);//0-公司，1-渠道商，2-(星级)推广员
		$type      = array(1,2,3);//1-小时，2-天，3-从开始总计
		$flag = true;
		foreach($role_type as $val){
			foreach($type as $v){
				$re = $this->do_statistics($val,$v,$param);
				if(!$re){
					$flag = false;
					break 2;
				}
			}
		}

		return $flag;
	}

	public function do_statistics($role_type,$type,$param){
		$change_type = $this->record['change_money_type'];

		// 统计推广员的直属玩家金币消耗
		if($role_type == 2){
			// 只统计金币消耗
			if(!in_array($change_type,[2,3])){
				return true;
			}
			// 只统计 天 和 总的
			if(!in_array($type,[2,3])){
				return true;
			}
			if($this->record['agent_parentid'] == $this->record['agent_top_agentid']){
				return true;
			}
			// 查找推广员的player_id
			$amodel = new agentinfomodel($this->app);
			$p_info = $amodel->get_agent_by_id($this->record['agent_parentid']);
			if(!$p_info){
				return true;
			}else{
				$this->record['promote_id'] = $p_info->get('agent_player_id',0);
				$this->record['promote_status'] = $p_info->get('agent_login_status',0);
			}
		}

		if($change_type == 1){
			// 充值累计
	    	if(!$this->charge_sum($role_type,$type,$param)) {
				return false;
			}
		}

		if($change_type == 4){
			// 新增用户：由赠送记录来统计
			if(!$this->new_player_sum($role_type,$type,$param)) {
				return false;
			}
			// 赠送金币数
			if(!$this->send_coin_sum($role_type,$type,$param)) {
				return false;
			}
		}

		if(in_array($change_type,[1,4])){
			// 产出统计  充值+赠送
			if(!$this->produce_sum($role_type,$type,$param)) {
				return false;
			}
		}
		
		if(in_array($change_type,[2,3])){
			// 游戏用户：参与任何一款游戏的总用户  去除重复
			if(!$this->game_player_sum($role_type,$type,$param)) {
				return false;
			}
			// 消耗总额  10000
			if(!$this->game_tax_sum($role_type,$type,$param)) {
				return false;
			}
			// 所有推广员（不含星级）旗下玩家消耗金币数总计,只按天统计
			if(!$this->game_tax_sum_promote($role_type,$type,$param)) {
				return false;
			}
		}

		return true;
	}

	public function charge_sum($role_type,$type,$param){
		$model = new statisticstotalmodel($this->app);
		$data = $this->get_param($role_type,$type);
		$orders = $this->record['change_money_param'] ? json_decode($this->record['change_money_param'],true) : array();
		if(!empty($orders) && isset($orders['price_value'])){
			$incr_value = $orders['price_value'];
		}else{
			$incr_value = 0;
		}
		
		$arr = array(
			'statistics_role_type'	 =>$data['role_type'],
			'statistics_role_value'	 =>$data['role_value'],
			'statistics_mode'		 =>self::TYPE_CHARGE_SUM,
			'statistics_type'        =>$type,
			'statistics_money_rate'  =>0,
			'statistics_timestamp'   =>$data['time']
		);
		$cur_time = time();
		// 先查找是否存在
		$log = $model->get_one_log($arr);
		$arr['statistics_sum'] = $incr_value;
		$arr['statistics_update'] = date('Y-m-d H:i:s',$cur_time);
		if($log){
			$re = $model->update_pay_sum($arr);
		}else{
			$arr['statistics_datetime'] = $data['date'];
			$arr['statistics_time']     = $cur_time;
			$re = $model->save_data_model($arr);
		}

		return $re;
	}

	public function new_player_sum($role_type,$type,$param){
		$model = new statisticstotalmodel($this->app);
		$data = $this->get_param($role_type,$type);
		$incr_value = 1;
		
		$arr = array(
			'statistics_role_type'	 =>$data['role_type'],
			'statistics_role_value'	 =>$data['role_value'],
			'statistics_mode'		 =>self::TYPE_NEW_REGISTER,
			'statistics_type'        =>$type,
			'statistics_money_rate'  =>0,
			'statistics_timestamp'   =>$data['time']
		);
		$cur_time = time();
		// 先查找是否存在
		$log = $model->get_one_log($arr);
		$arr['statistics_sum'] = $incr_value;
		$arr['statistics_update'] = date('Y-m-d H:i:s',$cur_time);
		if($log){
			$re = $model->update_pay_sum($arr);
		}else{
			$arr['statistics_datetime'] = $data['date'];
			$arr['statistics_time']     = $cur_time;
			$re = $model->save_data_model($arr);
		}

		return $re;
	}

	// 赠送金币数
	public function send_coin_sum($role_type,$type,$param){
		$model = new statisticstotalmodel($this->app);
		$data = $this->get_param($role_type,$type);
		$incr_value = $this->record['change_money_money_value'];
		
		$arr = array(
			'statistics_role_type'	 =>$data['role_type'],
			'statistics_role_value'	 =>$data['role_value'],
			'statistics_mode'		 =>self::TYPE_SEND_COIN,
			'statistics_type'        =>$type,
			'statistics_money_rate'  =>0,
			'statistics_timestamp'   =>$data['time']
		);
		$cur_time = time();
		// 先查找是否存在
		$log = $model->get_one_log($arr);
		$arr['statistics_sum'] = $incr_value;
		$arr['statistics_update'] = date('Y-m-d H:i:s',$cur_time);
		if($log){
			$re = $model->update_pay_sum($arr);
		}else{
			$arr['statistics_datetime'] = $data['date'];
			$arr['statistics_time']     = $cur_time;
			$re = $model->save_data_model($arr);
		}

		return $re;
	}

	public function produce_sum($role_type,$type,$param){
		$model = new statisticstotalmodel($this->app);
		$data = $this->get_param($role_type,$type);
		$incr_value = $this->record['change_money_money_value'];
		
		$arr = array(
			'statistics_role_type'	 =>$data['role_type'],
			'statistics_role_value'	 =>$data['role_value'],
			'statistics_mode'		 =>self::TYPE_PRODUCE_SUM,
			'statistics_type'        =>$type,
			'statistics_money_rate'  =>0,
			'statistics_timestamp'   =>$data['time']
		);
		$cur_time = time();
		// 先查找是否存在
		$log = $model->get_one_log($arr);
		$arr['statistics_sum'] = $incr_value;
		$arr['statistics_update'] = date('Y-m-d H:i:s',$cur_time);
		if($log){
			$re = $model->update_pay_sum($arr);
		}else{
			$arr['statistics_datetime'] = $data['date'];
			$arr['statistics_time']     = $cur_time;
			$re = $model->save_data_model($arr);
		}

		return $re;
	}

	public function game_player_sum($role_type,$type,$param){
		// 只统计每日的游戏玩家，不统计小时的和总的；只统计渠道和总公司
		if($type != 2 || !in_array($role_type,[0,1])){
			return true;
		}
		$model = new statisticstotalmodel($this->app);
		$data = $this->get_param($role_type,$type);
		$incr_value = 1;
		// 查看同一个玩家是否已经计数
		$playermode = new moneychangeplayermodel($this->app);
		$where = array(
			'player_id'=>$this->record['change_money_player_id'],
			'start_time'=>strtotime(date('Ymd',$this->record['change_money_time']))
		);
		$where['end_time'] = $where['start_time'] + 86400;
		$re = $playermode->search_player_log_day($where);
		// 等于1即为和当前操作同一条记录,大于1表示该玩家今天已经被统计过了
		if($re[0]['num'] > 1){
			return true;
		}
		$arr = array(
			'statistics_role_type'	 =>$data['role_type'],
			'statistics_role_value'	 =>$data['role_value'],
			'statistics_mode'		 =>self::TYPE_GAME_PLAYER,
			'statistics_type'        =>$type,
			'statistics_money_rate'  =>0,
			'statistics_timestamp'   =>$data['time']
		);
		$cur_time = time();
		// 先查找是否存在
		$log = $model->get_one_log($arr);
		$arr['statistics_sum'] = $incr_value;
		$arr['statistics_update'] = date('Y-m-d H:i:s',$cur_time);
		if($log){
			$re = $model->update_pay_sum($arr);
		}else{
			$arr['statistics_datetime'] = $data['date'];
			$arr['statistics_time']     = $cur_time;
			$re = $model->save_data_model($arr);
		}

		return $re;
	}

	public function game_tax_sum_promote($role_type,$type,$param){
		if($role_type != 2 || $type != 2){
			return true;
		}
		if($this->record['promote_status']){
			return true;
		}
		if($this->record['change_money_tax'] <= 0){
			return true;
		}
		$model = new statisticstotalmodel($this->app);
		$data = $this->get_param($role_type,$type);
		$incr_value = $this->record['change_money_tax'];
		
		$arr = array(
			'statistics_role_type'  =>2,
			'statistics_role_value' =>0,
			'statistics_mode'       =>self::TYPE_PROMOTER_SUB_COST,
			'statistics_type'       =>2,
			'statistics_money_rate' =>0,
			'statistics_timestamp'  =>$data['time']
		);
		// 天和总的  按兑换比例变化分开统计
		if($type == 2 || $type == 3){
			$arr['statistics_money_rate'] = $this->record['coin_rate'];
		}
		$cur_time = time();
		// 先查找是否存在
		$log = $model->get_one_log($arr);
		$arr['statistics_sum'] = $incr_value;
		$arr['statistics_update'] = date('Y-m-d H:i:s',$cur_time);
		if($log){
			$re = $model->update_pay_sum($arr);
		}else{
			$arr['statistics_datetime']   = $data['date'];
			$arr['statistics_time']       = $cur_time;
			$re = $model->save_data_model($arr);
		}

		return $re;
	}

	public function game_tax_sum($role_type,$type,$param){
		$model = new statisticstotalmodel($this->app);
		$data = $this->get_param($role_type,$type);
		$incr_value = $this->record['change_money_tax'];
		
		$arr = array(
			'statistics_role_type'  =>$data['role_type'],
			'statistics_role_value' =>$data['role_value'],
			'statistics_mode'       =>self::TYPE_COIN_COST,
			'statistics_type'       =>$type,
			'statistics_money_rate' =>0,
			'statistics_timestamp'  =>$data['time']
		);
		// 天和总的  按兑换比例变化分开统计
		if($type == 2 || $type == 3){
			$arr['statistics_money_rate'] = $this->record['coin_rate'];
		}
		$cur_time = time();
		// 先查找是否存在
		$log = $model->get_one_log($arr);
		$arr['statistics_sum'] = $incr_value;
		$arr['statistics_update'] = date('Y-m-d H:i:s',$cur_time);
		if($log){
			$re = $model->update_pay_sum($arr);
		}else{
			$arr['statistics_datetime']   = $data['date'];
			$arr['statistics_time']       = $cur_time;
			$re = $model->save_data_model($arr);
		}

		return $re;
	}

	public function get_coin_transfer_rate(){
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

	public function get_param($role_type,$type){
		$re = array();
		if($type == 1){
			$re['time'] = strtotime(date('Y-m-d H:00:00',$this->record['change_money_time']));
			$re['date'] = date('Y-m-d H',$this->record['change_money_time']);
		}elseif($type == 2){
			$re['time'] = strtotime(date('Y-m-d',$this->record['change_money_time']));
			$re['date'] = date('Y-m-d',$this->record['change_money_time']);
		}elseif($type == 3){
			$re['time'] = 0;
			$re['date'] = '';
		}
		if($role_type == 0){
			$re['role_value'] = 0;
		}elseif($role_type == 1){
			$re['role_value'] = $this->record['agent_top_agentid'];
		}elseif($role_type == 2){
			// player_id
			$re['role_value'] = $this->record['promote_id'];
		}

		$re['role_type'] = $role_type;

		return $re;
	}
}