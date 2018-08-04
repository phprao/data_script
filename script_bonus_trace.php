<?php

/**
 * 红包补发
 */

set_time_limit(0);

require('lib/config.php');
require('lib/mysql.php');
require('lib/logger.php');

class Trace{
	private $mysql;
	private $logs;
	private $logTag = 'bonus_trace';
	private $limit = 100;
	private $trace_time = 7200;// 2小时

	public function __construct() {
		$this->mysql   	= new MysqlDriver(Config::$mysql_config);
		$this->logs    	= new Logger();
	}
	
	public function main(){
		$start = time();

		$t = $start - $this->trace_time;
		// 处于安全考虑，延时请求
		$sql = 'select * from dc_wx_bonus_log where status = 0 and create_time <= '.$t.' and trace_time < '.$t.' order by id desc limit '.$this->limit;
		$bonus = $this->mysql->find($sql);

		if(count($bonus) == 0 || !$bonus){
			return;
		}
		foreach($bonus as $val){
			$re = $this->sendBonus($val['id']);
			if(!$re){
				$this->updateTime($val['id']);
			}
		}

		$end = time();
		$this->logs->info($this->logTag,'耗时: ' . ($end - $start) . ' seconds');
	}

	protected function sendBonus($id){
		// 发送红包 - 防止返回信息编码有误
        $bonusConfig = Config::$send_bonus_config;
        if(empty($bonusConfig)){
            $this->logs->info($this->logTag, "微信红包相关配置加载失败！");
            return false;
        }
        if(!$id){
            $this->logs->info($this->logTag, "id参数错误");
            return false;
        }
        $bonus = $this->mysql->find('select * from dc_wx_bonus_log where status = 0 and id = '.$id);
        if(!$bonus){
        	$this->logs->info($this->logTag, "红包记录有误");
            return false;
        }else{
        	$bonus = $bonus[0];
        }
		$orderno = $this->createOrderId(30);
        $requestUrl = $bonusConfig['send_bonus_url'] ;
        $requestUrl .= '?order_id='. $orderno ;
        $requestUrl .= '&limit=100';
        $requestUrl .= '&total_amount='. ( $bonus['total_amount'] / 100 ) ;
        $requestUrl .= '&openid=' . $bonus['openid_gzh'] ;
        $requestUrl .= '&send_config='.$bonusConfig['send_bonus_name'] ;
        $requestUrl .= '&sceneid='.$bonusConfig['send_bonus_scanid'] ;
        $requestUrl .= '&notify_url='.$bonusConfig['send_bonus_callback'] ;
        $requestUrl .= '&descs='.json_encode($bonusConfig['send_bonus_remark']) ;

        $result = file_get_contents($requestUrl);
        $result = json_decode($result, true);

        if ($result && $result['status'] == 'success' && $result['data']['return_code'] == 'SUCCESS' && $result['data']['result_code'] == 'SUCCESS') {
            $update = [
				'mch_billno'  => $orderno,
				'status'      => 2,
				'send_listid' => $result['data']['send_listid'],
				'send_time'   => date('Y-m-d H:i:s')
            ];
            $ret = $this->mysql->update('dc_wx_bonus_log', $update, ['id'=>$id]);
        } else {
            $ret = false;
            $error = isset($result['errorMsg']) ? $result['errorMsg'] : '无信息返回';
            $this->logs->info($this->logTag, "红包发送失败 | id=".$id.' '.$error);
        }

        return $ret;
	}

	protected function updateTime($id){
		$this->mysql->query('update dc_wx_bonus_log set trace_time = '.time().' where id = '.$id);
	}
	
	protected function createOrderId ($length = 24) {
        $seed=md5(microtime());
        $pwd='';
        for($i=0;$i<$length;$i++){
            $pwd.=$seed{mt_rand(0,31)};
        }
        $hash=md5($pwd);
        return substr($hash,0,$length);
    }
}


function main_run() {
	while(true)
	{
		$Trace = new Trace();
		$id = $Trace->main();
		sleep(3600);
	}
}

main_run();