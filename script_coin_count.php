<?php
/**
 +---------------------------------------------------------- 
 * date: 2018-05-03 11:08:42
 +---------------------------------------------------------- 
 * author: Raoxiaoya
 +---------------------------------------------------------- 
 * describe: 统计用户剩余金币数到数据库
 +---------------------------------------------------------- 
 */

set_time_limit(0);

require('lib/config.php');
require('lib/mysql.php');
require('lib/redis.php');
require('lib/logger.php');

class PlayerCoins
{
	private $mysql;
	private $logs;
	private $tag = 'user_info';
	private $logTag = 'coin_count';
	private $size = 1000;
	private $statistics_table = 'dc_statistics_total';

	public function __construct(){
		$this->mysql   	= new MysqlDriver(Config::$mysql_config);
		$this->logs    	= new Logger();
	}

	public function main(){
		$start = time();

		// 清空原有数据
		$result = $this->clear_last_coin_num();
		if(!$result){
			$this->logs->error($this->logTag,'function clear_last_coin_num failed ...');
			return true;
		}

		$players = $this->mysql->find("select player_id from dc_player_info");
		while(count($players))
		{
			$player = array_pop($players);
			$info = $this->mysql->find("select player_coins from dc_player_info where player_id = " . $player['player_id'].' limit 1');
			if($info[0]['player_coins'] <= 0){
				continue;
			}
			$re = $this->set_last_coin_num($player['player_id'], $info[0]['player_coins']);
			if(!$re){
               	$this->logs->error($this->logTag,'统计金币数失败：player_id='.$player['player_id'].' | player_coins='.$info[0]['player_coins']);
               	break;
            }
		}

        $this->mysql->close();

		$end = time();
		$this->logs->info($this->logTag,'耗时: ' . ($end - $start) . ' seconds');
	}

	public function clear_last_coin_num(){
        $sql = "update dc_statistics_total set statistics_sum = 0 where statistics_mode = 8 and statistics_type = 3 and statistics_timestamp = 0";
        $re = $this->mysql->query($sql);
        return $re;
	}

	public function set_last_coin_num($player_id, $player_coins){
		// 总公司-全部
        // 渠道-全部
        // 推广员(包括星级)-名下所有玩家的剩余金币累计
        $role_arr = [0,1,2];
        $type = 3;// 全部
        $mode = 8;//剩余金币数（最终值）
        $time = time();

        $info = $this->mysql->find('select * from dc_agent_info where agent_player_id = '.$player_id.' limit 1');
        if(!$info){
        	$this->logs->error($this->logTag,'agent_info信息不存在：player_id='.$player_id);
            return false;
        }
        else{
			$channel_id = $info[0]['agent_top_agentid'];
			$star_id    = $info[0]['agent_parentid'];
            if($channel_id == $star_id){
                $star_id = 0;
            }else{
                $pinfo = $this->mysql->find('select * from dc_agent_info where agent_id = '.$star_id.' limit 1');
                if(!$pinfo){
                    $star_id = 0;
                }else{
                    // player_id
                    $star_id = $pinfo[0]['agent_player_id'];
                }
            }
        }
        if($star_id == 0){
            $role_arr = [0,1];
        }

        foreach($role_arr as $val){
            $where = [
                'statistics_role_type'  =>$val,
                'statistics_role_value' =>0,
                'statistics_type'       =>3
            ];
            if($val == 1){
                $where['statistics_role_value'] = $channel_id;
            }
            if($val == 2){
                $where['statistics_role_value'] = $star_id;
            }
			$where['statistics_mode']       = $mode;
			$where['statistics_sum']        = $player_coins;
			$where['statistics_timestamp']  = 0;
			$where['statistics_datetime']   = '';
			$where['statistics_update']     = date('Y-m-d H:i:s',$time);
			$where['statistics_time']       = $time;
			$where['statistics_money_rate'] = 0;
            // 是否已经存在
            $w = [
                'statistics_role_type'  =>$where['statistics_role_type'],
                'statistics_role_value' =>$where['statistics_role_value'],
                'statistics_mode'       =>$where['statistics_mode'],
                'statistics_type'       =>$where['statistics_type'],
                'statistics_timestamp'  =>$where['statistics_timestamp'],
            ];
            $record = $this->mysql->select($this->statistics_table, '*', $w, 'limit 1');
            if($record){
                $sql = "UPDATE ".$this->statistics_table." SET statistics_sum = statistics_sum + ".$where['statistics_sum'].",statistics_update = '".$where['statistics_update']."' where statistics_id = ".$record[0]['statistics_id'];
		        $re = $this->mysql->query($sql);
            }else{
                $re = $this->mysql->insert($this->statistics_table, $where);
            }

            if(!$re){
            	$this->logs->error($this->logTag,'表'.$this->statistics_table.' 更新失败！');
            	return false;
            }
        }

        return true;
	}
}


// 玩家信息回写到数据库 
function main_run() {
	while(true)
	{
		$Player = new PlayerCoins();
		$Player->main();
		sleep(600);
	}
}

main_run();
