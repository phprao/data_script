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

set_time_limit(0);

require('lib/config.php');
require('lib/mysql.php');
require('lib/logger.php');

class Game
{
	private $mysql;
	private $logs;
	private $logTag = 'game';
	protected $limit = 1000;

	public function __construct(){
		$this->mysql   	= new MysqlDriver(Config::$mysql_config);
		$this->logs    	= new Logger();
	}

	public function main(){
		$start = time();

		$config = $this->getConfig();
		if(!$config){
			return false;
		}
		if($start > $config['config_end_time'] + 3600){
			return false;
		}

		$list = $this->getList($config);
        if(empty($list)){
        	return true;
        }

		foreach($list as $val){
			$last_id = $val['game_log_id'];
			$this->deal_record($val, $config);
		}

		$this->updateLastId($last_id);

		$end = time();
		$this->logs->info($this->logTag,'耗时: ' . ($end - $start) . ' seconds');

		return true;
	}

	protected function getConfig(){
		$sql = "SELECT * from dc_config WHERE config_name = 'new_player_bonus' AND config_status = 1 LIMIT 1";
		$info = $this->mysql->find($sql);
		if(empty($info)){
			$this->logs->info($this->logTag,'请先在配置表中配置参数');
			return false;
		}else{
			$info[0]['config_config'] = json_decode($info[0]['config_config'],true);
			return $info[0];
		}
	}

	protected function getList($config){
		$info = $this->mysql->find('select * from dc_config where config_name = "new_activity_g_id"');
		if(empty($info)){
			$last_id = 0;
            $this->mysql->insert('dc_config', [
                'config_name'   =>'new_activity_g_id', 
                'config_desc'   =>'邀新活动-统计局数用', 
                'config_config' =>0
            ]);
		}else{
			$last_id = $info[0]['config_config'];
		}
		
		// 好友房除外
		$sql = "SELECT * from dc_game_record_log where game_log_time >= ".$config['config_start_time']." and game_log_time <=".$config['config_end_time']." and game_log_game_id = ".$config['config_config']['game_id']." and game_log_win_state = 1 and game_log_club_room_no = 0 and game_log_id > ".$last_id." order by game_log_id asc limit ".$this->limit;
        $list = $this->mysql->find($sql);

        return $list;
	}

	protected function deal_record($record, $config){
		$promoteInfo = $this->getParent($record['game_log_player_id']);
		if($promoteInfo === false){
			return false;
		}
		if($promoteInfo['promoters_parent_id'] == 0){
			return true;
		}
		
		if($promoteInfo['promoters_time'] < $config['config_start_time'] || $promoteInfo['promoters_time'] > $config['config_end_time']){
			return true;
		}

		$sql = "SELECT * from dc_new_game_record where player_id = ".$promoteInfo['promoters_player_id']." and game_id = ".$config['config_config']['game_id'];
		$info = $this->mysql->find($sql);

		$this->mysql->query('START TRANSACTION');

		if($info){
			$sql = "UPDATE dc_new_game_record SET win_num = win_num + 1 WHERE id = ".$info[0]['id'];
			$r1 = $this->mysql->query($sql);
		}else{
			$data = [
				'player_id'          =>$promoteInfo['promoters_player_id'],
				'promoter_player_id' =>$promoteInfo['promoters_parent_id'],
				'game_id'            =>$config['config_config']['game_id'],
				'win_num'            =>1,
			];
			$r1 = $this->mysql->insert('dc_new_game_record', $data);
		}

		$r2 = $this->updatePromoter($record, $config);
		
		if($r1 && $r2){
            $this->mysql->query('COMMIT');
            return true;
        }else{
            $this->logs->info($this->logTag, "deal_record失败: r1 = $r1, r2 = $r2");
            $this->mysql->query('ROLLBACK');
            return false;
        }
	}
	protected function getParent($player_id){
		$sql = "SELECT * from dc_promoters_info where promoters_player_id = ".$player_id;
		$info = $this->mysql->find($sql);
		if($info){
			return $info[0];
		}else{
			$this->logs->info($this->logTag,'玩家'.$player_id.'的推广关系缺失');
			return false;
		}
	}

	protected function updatePromoter($record, $config){
		$sql = "select * from dc_new_game_record where player_id = ".$record['game_log_player_id']." and game_id = ".$config['config_config']['game_id'];
		$info = $this->mysql->find($sql);
		if($info[0]['status'] == 0 && $info[0]['win_num'] >= $config['config_config']['win_num']){
			$sql = 'update dc_new_game_record set status=1,done_num='.$config['config_config']['win_num'].',done_time='.$record['game_log_time'].',done_date="'.date('Y-m-d H:i:s',$record['game_log_time']).'" where id='.$info[0]['id'];
			return $this->mysql->query($sql);
		}else{
			return true;
		}
	}

	protected function updateLastId($last_id){
		$this->mysql->query('update dc_config set config_config = '.$last_id.' where config_name = "new_activity_g_id"');
	}

}


// 玩家信息回写到数据库 
function main_run() {
	while(true)
	{
		$Game = new Game();
		$id = $Game->main();
		if($id === false){
			break;
		}
		sleep(5);
	}
}

main_run();
