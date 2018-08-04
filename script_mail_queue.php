<?php
/**
 +---------------------------------------------------------- 
 * date: 2018-05-03 11:08:42
 +---------------------------------------------------------- 
 * author: Raoxiaoya
 +---------------------------------------------------------- 
 * describe: 邮件队列
 +---------------------------------------------------------- 
 */

set_time_limit(0);

require('lib/config.php');
require('lib/logger.php');
require('lib/mysql.php');

class Mail
{
	private $logs;
	private $logTag = 'mail_queue';
	private $limit = 2;
	private $update_time = 7200;

	public function __construct(){
		$this->mysql = new MysqlDriver(Config::$mysql_config);
		$this->logs  = new Logger();
	}

	public function main(){
		$start = time();

		$t = $start - $this->update_time;
		$list = $this->get_list($t);
		if($list){
			foreach($list as $val){

				$this->mysql->query('START TRANSACTION');

				$re1 = $this->deal_record($val);
				$re2 = $this->delRecord($val['mail_id']);
				if($re1 && $re2){
					$this->mysql->query('COMMIT');
				}else{
					$this->mysql->query('ROLLBACK');
					$this->updateTime($val['mail_id']);
				}
			}

			$end = time();
			$this->logs->info($this->logTag,'耗时: ' . ($end - $start) . ' seconds');
		}else{
			return;
		}
	}

	protected function get_list($t){
		$sql = 'select * from dc_mail_queue where mail_send_time <= '.time().' and mail_update_time < '.$t.' order by mail_id asc limit '.$this->limit;
		$queue = $this->mysql->find($sql);
		return $queue;
	}

	protected function deal_record($data){
		if($data['mail_receiver_type'] == 1){

			$player_ids = $this->create_mail_by_player($data);

		}elseif($data['mail_receiver_type'] == 2){

			$player_ids = $this->create_mail_by_star($data);

		}elseif($data['mail_receiver_type'] == 3){

			$player_ids = $this->create_mail_by_channel($data);

		}elseif($data['mail_receiver_type'] == 4){

			$player_ids = $this->create_mail_by_all($data);

		}

		if($player_ids){
			$re = $this->create_mail($player_ids, $data);
		}else{
			$re = false;
		}

		return $re;
	}

	protected function updateTime($id){
		return $this->mysql->query('update dc_mail_queue set mail_update_time = '.time().' where mail_id = '.$id);
	}

	protected function delRecord($id){
		return $this->mysql->query('delete from dc_mail_queue where mail_id = '.$id);
	}

	protected function create_mail_by_player($data){
		$ids = explode(',', $data['mail_receiver_id']);
		$ids = array_filter($ids, [$this, 'filter_player_id']);
		return $ids;
	}

	protected function create_mail_by_star($data){
		$ids = explode(',', $data['mail_receiver_id']);
		$ids = array_filter($ids, [$this, 'filter_player_id']);
		$player_ids = [];
		foreach($ids as $agent_id){
			$sql = 'select agent_player_id from dc_agent_info where agent_parentid = '.$agent_id;
			$result = $this->mysql->find($sql);
			foreach($result as $val){
				array_push($player_ids, $val['agent_player_id']);
			}
		}

		return $player_ids;
	}

	protected function create_mail_by_channel($data){
		$ids = explode(',', $data['mail_receiver_id']);
		$ids = array_filter($ids, [$this, 'filter_player_id']);
		$player_ids = [];
		foreach($ids as $agent_id){
			$sql = 'select agent_player_id from dc_agent_info where agent_top_agentid = '.$agent_id;
			$result = $this->mysql->find($sql);
			foreach($result as $val){
				array_push($player_ids, $val['agent_player_id']);
			}
		}

		return $player_ids;
	}

	protected function create_mail_by_all($data){
		$player_ids = [];

		$sql = 'select agent_player_id from dc_agent_info where agent_level > 1 ';
		$result = $this->mysql->find($sql);
		foreach($result as $val){
			array_push($player_ids, $val['agent_player_id']);
		}

		return $player_ids;
	}

	protected function create_mail($player_ids, $data){
		$flag = true;

		while($player_ids){

			$player_id = array_pop($player_ids);

			$sql = 'select * from dc_agent_info where agent_player_id = '.$player_id;
			$info = $this->mysql->find($sql);
			if(!$info){
				continue;
			}else{
				$info = $info[0];
			}
			if($info['agent_parentid'] == $info['agent_top_agentid']){
				$info['agent_parentid'] = 0;
			}

			$table = 'dc_mail';
			
			$insert = [
				'mail_sender_type'   =>$data['mail_sender_type'],
				'mail_sender_id'     =>$data['mail_sender_id'],
				'mail_receiver_type' =>2,
				'mail_receiver_id'   =>$player_id,
				'mail_star_id'       =>$info['agent_parentid'],
				'mail_channel_id'    =>$info['agent_top_agentid'],
				'mail_type'          =>$data['mail_type'],
				'mail_model_id'      =>0,
				'mail_title'         =>$data['mail_title'],
				'mail_content'       =>$data['mail_content'],
				'mail_create_time'   =>$data['mail_send_time'],
				'mail_create_date'   =>date('Y-m-d H:i:s', $data['mail_send_time']),
			];

			$re = $this->mysql->insert($table, $insert);
			if(!$re){
				$flag = false;
				break;
			}
		}

		return $flag;
	}

	protected function filter_player_id($val){
		if($val && is_numeric($val) && strlen((string)$val) <= 11){
			return true;
		}else{
			return false;
		}
	}
}

function main_run() {
	while(true)
	{
		$Mail = new Mail();
		$Mail->main();
		sleep(2);
	}
}

main_run();
