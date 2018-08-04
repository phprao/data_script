<?php
/**
 +---------------------------------------------------------- 
 * date: 2018-05-03 11:08:42
 +---------------------------------------------------------- 
 * author: Raoxiaoya
 +---------------------------------------------------------- 
 * describe: 渠道收益月度奖金
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
	private $logTag        = 'super_income_ext';
	protected $money_type  = 1;
	protected $super_share = 1000;// 10%

	public function __construct(){
		$this->mysql = new MysqlDriver(Config::$mysql_config);
		$this->logs  = new Logger();
	}

	public function main(){
		$start = time();

		// 每个月2号执行
		if(date('d') != '1'){
			return false;
		}

        $cur_month = strtotime(date('Y-m', time()));

		// $cur_month  = 1530374400;
		$last_month = strtotime('-1 month',$cur_month);
		$this->last_month_time = $last_month;
		
        // 上个月的统计
		$list = $this->get_list($last_month, $cur_month);

        if(empty($list)){
        	return true;
        }

		foreach($list as $val){
			$this->deal_super_earn($val);
		}

		$end = time();
		$this->logs->info($this->logTag,'耗时: ' . ($end - $start) . ' seconds');

		return true;
	}

    protected function get_list($last_month, $cur_month){
    	$sql = "SELECT statistics_agent_id,statistics_money_type,SUM(statistics_money_data_direct) as statistics_money_data_direct,SUM(statistics_money_data) as statistics_money_data,statistics_super_share_direct,statistics_money_rate_value,SUM(statistics_money) as statistics_money";
    	$sql .= " from dc_agent_super_statistics_date where";
		$sql .= " statistics_money_type = ".$this->money_type;
		$sql .= " and statistics_time >= ".$last_month;
		$sql .= " and statistics_time < ".$cur_month;
		$sql .= " group by statistics_agent_id ";

        $list = $this->mysql->find($sql);
        return $list;
    }

    protected function deal_super_earn($record){
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
			$super_rate_ext = $super_rate_config['super_share_ext'];
			$super_rate     = $super_rate_config['super_share'];
		}else{
			$super_rate_ext = 0;
			$super_rate     = 0;
		}

		// 获取直接消耗分成比例
		$direct_rate = $this->get_super_direct_rate();
		
		// 总的收益：单位：分
		// $total_money_all = ($record['statistics_money_data']*$super_rate + $record['statistics_money_data_direct']*$direct_rate)/10000/100;
		// 月度奖金：分
		$total_money_ext = ($record['statistics_money_data']*$super_rate_ext)/10000/100;

		$this->mysql->query('START TRANSACTION');
		// 记录日志
        $r1 = $this->deal_agent_record_log($record, $info[0], $total_money_ext);
        // 更新账户余额
        $r2 = $this->deal_agent_update_data($record, $info[0], $total_money_ext);
        // 写入月度奖金表
        $r3 = $this->deal_agent_super_ext($record, $direct_rate, $super_rate, $super_rate_ext, $total_money_ext);

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
					'super_share_ext'         => $value['super_share_ext'],
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

	protected function deal_agent_record_log($record, $info, $total_money_ext){
		$data = [
			'log_money_type' =>1,
			'log_agent_id'   =>$record['statistics_agent_id'],
			'log_bef_money'  =>$info['agent_account_money'],
			'log_money'      =>$total_money_ext,
			'log_aft_money'  =>$info['agent_account_money'] + $total_money_ext,
			'log_add_time'   =>time(),
			'log_type'       =>5
		];
		$re = $this->mysql->insert('dc_agent_account_info_log', $data);
		return $re;
	}

	protected function deal_agent_update_data($record, $info, $total_money_ext){
		$sql = "update dc_agent_account_info set agent_account_money = agent_account_money + ".$total_money_ext." where agent_account_agent_id = ".$record['statistics_agent_id'];
		return $this->mysql->query($sql);
	}

	protected function deal_agent_super_ext($record, $direct_rate, $super_rate, $super_share_ext, $total_money_ext){
		$insert = [
			'statistics_agent_id'           =>$record['statistics_agent_id'],
			'statistics_money_type'         =>$record['statistics_money_type'],
			'statistics_money_data_direct'  =>$record['statistics_money_data_direct'],
			'statistics_money_data'         =>$record['statistics_money_data'],
			'statistics_date'               =>date('Y-m', $this->last_month_time),
			'statistics_time'               =>$this->last_month_time,
			'statistics_super_share_direct' =>$direct_rate,
			'statistics_super_share'        =>$super_rate,
			'statistics_super_share_ext'    =>$super_share_ext,
			'statistics_money_rate_value'   =>$record['statistics_money_rate_value'],
			'statistics_money'              =>$record['statistics_money'],
			'statistics_money_ext'          =>$total_money_ext,
			'statistics_money_all'          =>$record['statistics_money'] + $total_money_ext,
			'statistics_add_time'           =>time()
		];
		return $this->mysql->insert('dc_agent_super_statistics_date_ext', $insert);
	}

}


function main_run() {
	$Super = new Super();
	$Super->main();
}

main_run();
