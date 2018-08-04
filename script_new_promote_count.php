<?php
/**
 +---------------------------------------------------------- 
 * date: 2018-05-03 11:08:42
 +---------------------------------------------------------- 
 * author: Raoxiaoya
 +---------------------------------------------------------- 
 * describe: 活动期间注册的用户
 +---------------------------------------------------------- 
 */

set_time_limit(0);

require('lib/config.php');
require('lib/mysql.php');
require('lib/logger.php');

class Promote
{
	private $mysql;
	private $logs;
	private $logTag = 'promote';
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
			$last_id = $val['change_money_id'];
			if($val['change_money_type'] == 4){
				$this->deal_record($val, $config);
			}
			if(in_array($val['change_money_type'], [2,3]) && $val['change_money_tax'] > 0){
				$this->deal_cost($val, $config);
			}
			
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
		$info = $this->mysql->find('select * from dc_config where config_name = "new_activity_c_id"');
		if(empty($info)){
			$last_id = 0;
            $this->mysql->insert('dc_config', [
                'config_name'   =>'new_activity_c_id', 
                'config_desc'   =>'邀新活动-统计用', 
                'config_config' =>0
            ]);
		}else{
			$last_id = $info[0]['config_config'];
		}

		$sql = "SELECT * from dc_change_money_info_record where change_money_time >= ".$config['config_start_time']." and change_money_time <=".$config['config_end_time']." and change_money_money_type = 1 and change_money_type in (2,3,4) and change_money_id > ".$last_id." order by change_money_id asc limit ".$this->limit;
        $list = $this->mysql->find($sql);

        return $list;
	}

	protected function deal_cost($record, $config){
		$promoteInfo = $this->getParent($record['change_money_player_id']);
		if($promoteInfo === false){
			return false;
		}
		if($promoteInfo['promoters_time'] < $config['config_start_time'] || $promoteInfo['promoters_time'] > $config['config_end_time']){
			return true;
		}

		$sql = "SELECT * from dc_new_promoter where player_id = ".$promoteInfo['promoters_parent_id'];
		$info = $this->mysql->find($sql);

		$this->mysql->query('START TRANSACTION');

		if($info){
			$sql = "UPDATE dc_new_promoter SET promote_cost = promote_cost + ".$record['change_money_tax']." WHERE id = ".$info[0]['id'];
			$r1 = $this->mysql->query($sql);
		}else{
			$data = [
				'player_id'    =>$promoteInfo['promoters_parent_id'],
				'promote_num'  =>0,
				'promote_cost' =>$record['change_money_tax'],
				'add_time'     =>time(),
			];
			$r1 = $this->mysql->insert('dc_new_promoter', $data);
		}

		$r2 = $this->updatePromoter($promoteInfo, $config);

		if($r1 && $r2){
            $this->mysql->query('COMMIT');
            return true;
        }else{
            $this->logs->info($this->logTag, "deal_cost失败: r1 = $r1, r2 = $r2");
            $this->mysql->query('ROLLBACK');
            return false;
        }
	}

	protected function deal_record($record, $config){
		$promoteInfo = $this->getParent($record['change_money_player_id']);
		if($promoteInfo === false){
			return false;
		}
		if($promoteInfo['promoters_parent_id'] == 0){
			return true;
		}

		$this->mysql->query('START TRANSACTION');

		$r1 = $this->insertRelation($promoteInfo);
		$r2 = $this->insertCount($promoteInfo);
		$r3 = $this->updatePromoter($promoteInfo, $config);
		if($r1 && $r2 && $r3){
            $this->mysql->query('COMMIT');
            return true;
        }else{
            $this->logs->info($this->logTag, "deal_record失败: r1 = $r1, r2 = $r2, r3 = $r3");
            $this->mysql->query('ROLLBACK');
            return false;
        }
	}

	protected function updatePromoter($promoteInfo, $config){
		$sql = "SELECT * from dc_new_promoter where player_id = ".$promoteInfo['promoters_parent_id'];
		$info = $this->mysql->find($sql);
		if($info[0]['status'] == 0 && $info[0]['promote_num'] >= $config['config_config']['big_bonus']['player_num'] && $info[0]['promote_cost'] >= $config['config_config']['big_bonus']['coin']){
			return $this->mysql->query('update dc_new_promoter set status = 1,update_time = '.time().' where id ='. $info[0]['id']);
		}else{
			return true;
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

	protected function insertRelation($promoteInfo){
		$sql = "SELECT * from dc_new_player_detail where player_id = ".$promoteInfo['promoters_player_id'];
		$info = $this->mysql->find($sql);
		if($info){
			return true;
		}else{
			$data = [
				'promoter_player_id' =>$promoteInfo['promoters_parent_id'],
				'player_id'          =>$promoteInfo['promoters_player_id'],
				'add_time'           =>time(),
				'add_date'           =>date('Y-m-d H:i:s')
			];
			return $this->mysql->insert('dc_new_player_detail', $data);
		}
	}

	protected function insertCount($promoteInfo){
		$sql = "SELECT * from dc_new_promoter where player_id = ".$promoteInfo['promoters_parent_id'];
		$info = $this->mysql->find($sql);
		if($info){
			$sql = "UPDATE dc_new_promoter SET promote_num = promote_num + 1 WHERE id = ".$info[0]['id'];
			return $this->mysql->query($sql);
		}else{
			$data = [
				'player_id'    =>$promoteInfo['promoters_parent_id'],
				'promote_num'  =>1,
				'promote_cost' =>0,
				'add_time'     =>time(),
			];
			return $this->mysql->insert('dc_new_promoter', $data);
		}
	}

	protected function updateLastId($last_id){
		$this->mysql->query('update dc_config set config_config = '.$last_id.' where config_name = "new_activity_c_id"');
	}
}


// 玩家信息回写到数据库 
function main_run() {
	while(true)
	{
		$Promote = new Promote();
		$id = $Promote->main();
		if($id === false){
			break;
		}
		sleep(5);
	}
}

main_run();
