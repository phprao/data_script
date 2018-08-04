<?php
/**
 +---------------------------------------------------------- 
 * date: 2018-05-03 11:08:42
 +---------------------------------------------------------- 
 * author: Raoxiaoya
 +---------------------------------------------------------- 
 * describe: 活动期间游戏记录
 +---------------------------------------------------------- 
 */
header('Content-type:text/html; charset=utf-8');
set_time_limit(0);

require('lib/config.php');
require('lib/mysql.php');
require('lib/logger.php');

class Bonus
{
	private $mysql;

	public function __construct(){
		$this->mysql   	= new MysqlDriver(Config::$mysql_config);
	}

	public function main(){
		// if(!isset($argv[1])){
		// 	return false;
		// }
		// $start = $argv[1];

		$start = 1531756800;
		$end = $start + 86400;
		$file = 'data-'.date('Y-m-d', $start).'.txt';
		$super = [];
		$sql = 'select * from dc_agent_info where agent_top_agentid = 0 and agent_player_id = 0';
		$data = $this->mysql->find($sql);
		foreach($data as $v){
			array_push($super, ['agent_id'=>$v['agent_id'], 'agent_name'=>$v['agent_name']]);
		}

		$super_data = [];
		$str = join(',', $super);
		if(!is_file($file)){
			touch($file, 777);
		}

		file_put_contents($file ,"渠道名称\t每日新增用户\t注册用户\t登陆用户\t游戏用户\t每日新增游戏玩家\t每日充值\t每日服务费消耗\t每日付费用户数\t活跃人均付费\t单日游戏次数".PHP_EOL);

		foreach($super as $val){
			$sql = 'select agent_player_id from dc_agent_info where agent_top_agentid = '.$val['agent_id'].' and agent_createtime >='.$start;
			$data = $this->mysql->find($sql);
			$super_data[$val['agent_id']]['agent_name'] = $val['agent_name'];
			$super_data[$val['agent_id']]['register_num'] = count($data);
			$super_data[$val['agent_id']]['login_num'] = 0;
			$super_data[$val['agent_id']]['incr_player_game_num'] = 0;
			$super_data[$val['agent_id']]['pay_num'] = 0;
			$super_data[$val['agent_id']]['game_round_num'] = 0;
			if($data){
				foreach($data as $v){
					// 登陆用户
					// $sql = 'select * from dc_player_info where player_id = '.$v['agent_player_id'];
					// $info = $this->mysql->find($sql);
					// if($info[0]['player_login_time'] >= $start && $info[0]['player_login_time'] < $end){
					// 	$super_data[$val['agent_id']]['login_num']++;
					// }
					// 每日新增游戏玩家
					$sql = 'select count(*) as num from dc_game_record_log where game_log_player_id = '.$v['agent_player_id'].' and game_log_time >='.$start.' and game_log_time < '.$end;
					$num = $this->mysql->find($sql);
					if($num && $num[0]['num'] > 0){
						$super_data[$val['agent_id']]['incr_player_game_num']++;
					}
				}
			}

			// 每日新增用户
			$sql1 = 'select statistics_sum from dc_statistics_total where statistics_role_type = 1 and statistics_role_value = '.$val['agent_id'].' and statistics_mode = 2 and statistics_type = 2 and statistics_timestamp = '.$start;
			$data1 = $this->mysql->find($sql1);
			$super_data[$val['agent_id']]['incr_player_num'] = $data1[0]['statistics_sum'] ? $data1[0]['statistics_sum'] : 0;

			// 登陆用户
			$sql2 = 'select statistics_sum from dc_statistics_total where statistics_role_type = 1 and statistics_role_value = '.$val['agent_id'].' and statistics_mode = 4 and statistics_type = 2 and statistics_timestamp = '.$start;
			$data2 = $this->mysql->find($sql2);
			$super_data[$val['agent_id']]['login_num'] = $data2[0]['statistics_sum'] ? $data2[0]['statistics_sum'] : 0;

			// 游戏用户
			$sql3 = 'select statistics_sum from dc_statistics_total where statistics_role_type = 1 and statistics_role_value = '.$val['agent_id'].' and statistics_mode = 6 and statistics_type = 2 and statistics_timestamp = '.$start;
			$data3 = $this->mysql->find($sql3);
			$super_data[$val['agent_id']]['player_game_num'] = $data3[0]['statistics_sum'] ? $data3[0]['statistics_sum'] : 0;

			// 每日充值
			$sql4 = 'select (statistics_sum/100) as statistics_sum from dc_statistics_total where statistics_role_type = 1 and statistics_role_value = '.$val['agent_id'].' and statistics_mode = 1 and statistics_type = 2 and statistics_timestamp = '.$start;
			$data4 = $this->mysql->find($sql4);
			$super_data[$val['agent_id']]['pay_amount'] = $data4[0]['statistics_sum'] ? $data4[0]['statistics_sum'] : 0;

			// 每日服务费消耗
			$sql5 = 'select statistics_sum from dc_statistics_total where statistics_role_type = 1 and statistics_role_value = '.$val['agent_id'].' and statistics_mode = 3 and statistics_type = 2 and statistics_timestamp = '.$start;
			$data5 = $this->mysql->find($sql5);
			$super_data[$val['agent_id']]['tax_num'] = $data5[0]['statistics_sum'] ? $data5[0]['statistics_sum'] : 0;
			if($super_data[$val['agent_id']]['tax_num'] > 10000){
				$super_data[$val['agent_id']]['tax_num'] = round($super_data[$val['agent_id']]['tax_num']/10000, 2) . '万';
			}
			
			// 活跃人均付费
			if($super_data[$val['agent_id']]['login_num'] > 0){
				$super_data[$val['agent_id']]['pay_amount_avg'] = $super_data[$val['agent_id']]['pay_amount'] / $super_data[$val['agent_id']]['login_num'];
			}else{
				$super_data[$val['agent_id']]['pay_amount_avg'] = 0;
			}
	
		}

		// 每日付费用户数
		$sql = 'select * from dc_pay_record where recore_create_time >= '.$start.' and recore_create_time <'.$end;
		$list = $this->mysql->find($sql);
		if($list){
			foreach($list as $val){
				$sql = 'select agent_top_agentid from dc_agent_info where agent_player_id = '.$val['recore_player_id'];
				$info = $this->mysql->find($sql);
				$super_data[$info[0]['agent_top_agentid']]['pay_num']++;
			}
		}

		// 单日游戏次数
		$sql = 'select game_log_player_id,count(*) as num from dc_game_record_log where game_log_time >= '.$start.' and game_log_time < '.$end.' group by game_log_player_id';
		$info = $this->mysql->find($sql);
		
		foreach($info as $v){
			$sql = 'select * from dc_agent_info where agent_player_id = '.$v['game_log_player_id'];
			$temp = $this->mysql->find($sql);
			$super_data[$temp[0]['agent_top_agentid']]['game_round_num'] += $v['num'];
		}

		// print_r($super_data);
		$input = '';
		foreach($super_data as $value){
			$input .= $value['agent_name']."\t".$value['incr_player_num']."\t".$value['register_num']."\t".$value['login_num']."\t".$value['player_game_num']."\t".$value['incr_player_game_num']."\t".$value['pay_amount']."\t".$value['tax_num']."\t".$value['pay_num']."\t".$value['pay_amount_avg']."\t".$value['game_round_num'].PHP_EOL;
		}

		file_put_contents($file, $input, FILE_APPEND);
	}

}



function main_run($argv) {
	$bonus = new Bonus();
	$bonus->main();
}


main_run($argv);
