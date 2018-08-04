<?php
/**
 +---------------------------------------------------------- 
 * date: 2018-05-03 11:08:42
 +---------------------------------------------------------- 
 * author: Raoxiaoya
 +---------------------------------------------------------- 
 * describe: 渠道收益天结
 +---------------------------------------------------------- 
 */

set_time_limit(0);

require('lib/config.php');
require('lib/mysql.php');
require('lib/logger.php');

class Super
{
	private $mysql;
	private $logs;
	private $logTag        = 'super_income';
	protected $money_type          = 1;
	protected $limit_super         = 100;
	protected $default_super_share = 7000;
	protected $super_share         = 1000;// 10%
	protected $id;

	public function __construct($id){
		$this->mysql = new MysqlDriver(Config::$mysql_config);
		$this->logs  = new Logger();
		$this->id    = $id;
	}

	public function main(){
		$start = time();

        $cur_day = strtotime(date('Y-m-d', time()));

		$last_day = strtotime('-1 day',$cur_day);
		// $last_day  = 1527782400;
        
        // 昨天的收益
		$list = $this->get_list($last_day);
		
        if(empty($list)){
        	$this->id = 0;
        	return $this->id;
        }

		foreach($list as $val){
			$this->id = $val['statistics_id'];
			$this->deal_super_earn($val);
		}

		$end = time();
		$this->logs->info($this->logTag,'耗时: ' . ($end - $start) . ' seconds');

		return $this->id;
	}

    protected function get_list($last_day){
    	$sql = "SELECT * from dc_agent_super_statistics_date where";
		$sql .= " statistics_money_type = ".$this->money_type;
		$sql .= " and statistics_time = ".$last_day;
		$sql .= " and statistics_money_status = 0";
		$sql .= " and statistics_id > ".$this->id;
		$sql .= " limit ".$this->limit_super;

        $list = $this->mysql->find($sql);
        return $list;
    }

    protected function deal_super_earn($record){
		if($record['statistics_money_data_direct'] == 0 && $record['statistics_money_data'] == 0){
			$sql = "update dc_agent_super_statistics_date set statistics_money_status = 2 where statistics_id = ".$record['statistics_id'];
			$re = $this->mysql->query($sql);
			if(!$re){
				$this->logs->info($this->logTag, "数据库操作失败");
			}
			$this->logs->info($this->logTag, "数据库操作失败");
			return $re;
		}
		// 校验账户
		$sql = "select * from dc_agent_account_info where agent_account_agent_id = ".$record['statistics_agent_id'];
		$info = $this->mysql->find($sql);
		if(empty($info)){
			$this->logs->info($this->logTag, "该渠道账户信息不存在：agent_id=".$record['statistics_agent_id']);
			return false;
		}
		
		// 获取消耗分成比例
		$super_rate_config = $this->condition_compare(
			$this->getSuperConfig($record['statistics_agent_id']), 
			$record['statistics_money_data'] / $record['statistics_money_rate_value']
		);

		if($super_rate_config){
			$super_rate = $super_rate_config['super_share'];
		}else{
			$super_rate = 0;
		}
		
		// 获取直接消耗分成比例
		$direct_rate = $this->get_super_direct_rate();
		
		// 单位：分
		$total_money = ($record['statistics_money_data']*$super_rate + $record['statistics_money_data_direct']*$direct_rate)/10000/100;

		$this->mysql->query('START TRANSACTION');
		// 记录日志
        $r1 = $this->deal_agent_record_log($record, $info[0], $total_money);
        // 更新账户余额
        $r2 = $this->deal_agent_update_data($record, $info[0], $total_money);
        // 更新收益统计状态为2
        $r3 = $this->deal_agent_update_super($record, $direct_rate, $super_rate, $total_money);

        if($r1 && $r2 && $r3){
            $this->mysql->query('COMMIT');
            return true;
        }else{
            $this->logs->info($this->logTag, "入账失败: r1 = $r1, r2 = $r2, r3 = $r3");
            $this->mysql->query('ROLLBACK');
            return false;
        }
	}

	protected function get_super_direct_rate(){
		$sql = "select * from dc_config where config_name = 'channel_income_rate_from_direct' and config_status = 1";
		$conf = $this->mysql->find($sql);
		if(empty($conf)){
			$direct_rate = $this->default_super_share;
		}else{
			$conf_init = json_decode($conf[0]['config_config'], true);
			$direct_rate = $conf_init['rate'];
		}

		return $direct_rate;
	}

	protected function getSuperConfig($agent_id){
		$sql = "select * from dc_agent_super_income_config where super_agent_id = ".$agent_id." order by super_condition desc";
		$data = $this->mysql->find($sql);
		if(empty($data)){
			$sql = "select * from dc_agent_super_income_config where super_agent_id = 0 order by super_condition desc";
			$data = $this->mysql->find($sql);
			if(empty($data)){
				$this->logs->info($this->logTag, "渠道来自other玩家的分成配置未配置");
			}
		}

		if($data){
			$list = [];
	        foreach ($data as $value) {
	            $list[$value['super_id']] = [
					'super_condition'         => $value['super_condition'],
					'super_condition_compare' => $value['super_condition_compare'],
					'super_share'             => $value['super_share'],
	            ];
	        }
	        return $list;
		}else{
			return $data;
		}
		
	}

	protected function condition_compare($config, $total)
    {
        foreach ($config as $value) {
			$super_condition         = $value['super_condition'];
			$super_condition_compare = $value['super_condition_compare'];
			$super_share             = $value['super_share'];
            switch ($super_condition_compare) {
                case '<':
                    if ($super_condition < $total) {
                        return $value;
                    }
                    break;
                case '<=':
                    if ($super_condition <= $total) {
                        return $value;
                    }
                    break;
                default:
                    return 0;
                    break;
            }
        }
        return 0;
    }

	protected function deal_agent_record_log($record, $info, $total_money){
		$data = [
			'log_money_type' =>1,
			'log_agent_id'   =>$record['statistics_agent_id'],
			'log_bef_money'  =>$info['agent_account_money'],
			'log_money'      =>$total_money,
			'log_aft_money'  =>$info['agent_account_money'] + $total_money,
			'log_add_time'   =>time(),
			'log_type'       =>1
		];
		$re = $this->mysql->insert('dc_agent_account_info_log', $data);
		return $re;
	}

	protected function deal_agent_update_data($record, $info, $total_money){
		$sql = "update dc_agent_account_info set agent_account_money = agent_account_money + ".$total_money." where agent_account_agent_id = ".$record['statistics_agent_id'];
		return $this->mysql->query($sql);
	}

	protected function deal_agent_update_super($record, $direct_rate, $super_rate, $total_money){
		$sql = "update dc_agent_super_statistics_date set statistics_money_status = 2,statistics_super_share_direct = ".$direct_rate.",statistics_super_share = ".$super_rate.",statistics_money = ".$total_money." where statistics_id = ".$record['statistics_id'];
		return $this->mysql->query($sql);
	}

}


// 玩家信息回写到数据库 
function main_run() {
	$id = 0;
	while(true)
	{
		$Super = new Super($id);
		$id = $Super->main();
		sleep(10);
	}
}

main_run();
