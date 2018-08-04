<?php
/**
 +---------------------------------------------------------- 
 * date: 2018-05-03 11:08:42
 +---------------------------------------------------------- 
 * author: Raoxiaoya
 +---------------------------------------------------------- 
 * describe: 星级推广员收益结算
 +---------------------------------------------------------- 
 */

set_time_limit(0);

require('lib/config.php');
require('lib/mysql.php');
require('lib/logger.php');

class Star
{
	private $mysql;
	private $logs;
	private $logTag = 'Star_income';
	protected $limit_star = 1000;
	protected $id;

	public function __construct($id){
		$this->mysql   	= new MysqlDriver(Config::$mysql_config);
		$this->logs    	= new Logger();
		$this->id = $id;
	}

	public function main(){
		$start = time();

		$cur_day = strtotime(date('Y-m-d', time()));
		$statistics_date = $cur_day - 86400;// 昨天零点
		// 每天前6小时之内可执行
		// if ( (time() - $cur_day) > 3600 * 6) {
		// 	return true;
		// }
		// 昨天及之前的
		$sql = "SELECT * from dc_agents_promoters_statistics where statistics_time <= ".$statistics_date." and statistics_status = 0 and statistics_id > ".$this->id." limit ".$this->limit_star;
        $list = $this->mysql->find($sql);

        if(empty($list)){
        	$this->id = 0;
        	return $this->id;
        }

		foreach($list as $val){
			$this->id = $val['statistics_id'];
			$this->deal_agent_yesterday_earn($val);
		}

		$end = time();
		$this->logs->info($this->logTag,'耗时: ' . ($end - $start) . ' seconds');

		return $this->id;
	}

	protected function deal_agent_yesterday_earn($record){
		if($record['statistics_my_income'] == 0){
			$sql = "update dc_agents_promoters_statistics set statistics_status = 1 where statistics_id = ".$record['statistics_id'];
			$re = $this->mysql->query($sql);
			if(!$re){
				$this->logs->info($this->logTag, "数据库操作失败");
			}
			return $re;
		}

		$sql = "select * from dc_agent_account_info where agent_account_agent_id = ".$record['statistics_agents_id'];
		$info = $this->mysql->find($sql);
		if(empty($info)){
			$this->logs->info($this->logTag, "该代理账户信息不存在：agent_id=".$record['statistics_agents_id']);
			return false;
		}

		$this->mysql->query('START TRANSACTION');
		// 记录日志
        $r1 = $this->deal_agent_record_log($record,$info[0]);
        // 更新代理账户余额
        $r2 = $this->deal_agent_update_data($record,$info[0]);
        // 更新收益统计状态为1
        $r3 = $this->deal_agent_update_promoter($record);

        if($r1 && $r2 && $r3){
            $this->mysql->query('COMMIT');
            return true;
        }else{
            $this->logs->info($this->logTag, "入账失败: r1 = $r1, r2 = $r2, r3 = $r3");
            $this->mysql->query('ROLLBACK');
            return false;
        }
	}

	protected function deal_agent_record_log($record,$info){
		$data = [
			'log_money_type' =>1,
			'log_agent_id'   =>$record['statistics_agents_id'],
			'log_bef_money'  =>$info['agent_account_money'],
			'log_money'      =>$record['statistics_my_income'],
			'log_aft_money'  =>$info['agent_account_money'] + $record['statistics_my_income'],
			'log_add_time'   =>time(),
			'log_type'       =>3
		];
		$re = $this->mysql->insert('dc_agent_account_info_log', $data);
		return $re;
	}

	protected function deal_agent_update_data($record,$info){
		$sql = "update dc_agent_account_info set agent_account_money = agent_account_money + ".$record['statistics_my_income']." where agent_account_agent_id = ".$record['statistics_agents_id'];
		return $this->mysql->query($sql);
	}

	protected function deal_agent_update_promoter($record){
		$sql = "update dc_agents_promoters_statistics set statistics_status = 1 where statistics_id = ".$record['statistics_id'];
		return $this->mysql->query($sql);
	}

}


// 玩家信息回写到数据库 
function main_run() {
	$id = 0;
	while(true)
	{
		$Star = new Star($id);
		$id = $Star->main();
		sleep(10);
	}
}

main_run();
