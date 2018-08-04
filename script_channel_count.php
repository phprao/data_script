<?php
/**
 +---------------------------------------------------------- 
 * date: 2018-05-03 11:08:42
 +---------------------------------------------------------- 
 * author: Raoxiaoya
 +---------------------------------------------------------- 
 * describe: 定时同步渠道下的玩家人数到数据库
 +---------------------------------------------------------- 
 */

// 11万玩家 --- 6875 秒

set_time_limit(0);

require('lib/config.php');
require('lib/mysql.php');
require('lib/redis.php');
require('lib/logger.php');

class ChnnelPlayer
{
	private $mysql;
	private $logs;
	private $logTag = 'channel_player';
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

		$players = $this->mysql->find("select player_id from dc_player order by player_id asc");
		while(count($players))
		{
			$player = array_pop($players);
			$re = $this->set_channel_player_num($player['player_id']);
			if(!$re){
               	$this->logs->error($this->logTag,'统计人数失败：player_id='.$player['player_id']);
               	break;
            }
		}

        $this->mysql->close();

		$end = time();

		$this->logs->info($this->logTag,'耗时: ' . ($end - $start) . ' seconds');
	}

	public function clear_last_coin_num(){
        $sql = "update dc_statistics_total set statistics_sum = 0 where statistics_mode = 2";
        $re = $this->mysql->query($sql);
        return $re;
	}

	public function set_channel_player_num($player_id){
		// 总公司-小时，天，总
        // 渠道-小时，天，总
        $role_arr = [0,1];
        $type_arr = [1,2,3];
        $mode = 2;//注册用户数
        $time = time();

        $info = $this->mysql->find('select * from dc_agent_info where agent_player_id = '.$player_id.' limit 1');
        if(!$info){
        	$this->logs->error($this->logTag,'agent_info信息不存在：player_id='.$player_id);
            return true;
        }
        else{
            $channel_id    = $info[0]['agent_top_agentid'];
            $register_time = $info[0]['agent_createtime'];
            $time_arr = [
                strtotime(date('Y-m-d H:00:00', $register_time)),
                strtotime(date('Y-m-d', $register_time)),
                0
            ];
            $date_arr = [
                date('Y-m-d H', $register_time),
                date('Y-m-d', $register_time),
                ''
            ];
        }
        
        foreach($role_arr as $role){
            foreach($type_arr as $type){
                $where = [
                    'statistics_role_type'  =>$role,
                    'statistics_role_value' =>$channel_id,
                    'statistics_type'       =>$type,
                    'statistics_mode'       =>$mode,
                    'statistics_timestamp'  =>$time_arr[$type - 1]
                ];
                $update_date = date('Y-m-d H:i:s', $register_time);
                if($role == 0){
                   $where['statistics_role_value'] = 0;
                }
                $record = $this->mysql->select($this->statistics_table, '*', $where, 'limit 1');
                if($record){
                    $sql = "UPDATE ".$this->statistics_table." SET statistics_sum = statistics_sum + 1,statistics_update = '".$update_date."' where statistics_id = ".$record[0]['statistics_id'];
                    $re = $this->mysql->query($sql);
                }else{
                    $where['statistics_sum']      = 1;
                    $where['statistics_datetime'] = $date_arr[$type - 1];
                    $where['statistics_update']   = $update_date;
                    $where['statistics_time']     = $time;
                    $re = $this->mysql->insert($this->statistics_table, $where);
                }

                if(!$re){
                    $this->logs->error($this->logTag,'表'.$this->statistics_table.' 更新失败！');
                    return false;
                }
            }
        }

        return true;
	}

}


// 玩家信息回写到数据库 
function main_run() {
    $exe_num = 0;
    $exe_time = 0;

	while(true)
	{
		if(checkTime($exe_num, $exe_time)){
            $Player = new ChnnelPlayer();
            $Player->main();
            $exe_num++;
            $exe_time = time();
        }else{

        }
        
		sleep(10);
	}
}

function checkTime($exe_num, $exe_time){
    $time = time();
    $t = strtotime(date('Y-m-d', $time));
    $h = ($time - $t) / 3600 ;
    $t_exe = strtotime(date('Y-m-d', $exe_time));
    if($exe_num == 0 || $t_exe != $t){
        return true;
    }else{
        return false;
    }
}

main_run();
