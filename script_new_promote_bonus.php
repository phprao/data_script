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

class Bonus
{
	private $mysql;
	private $logs;
	private $logTag = 'bonus';
	protected $limit = 100;

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
		$config['start'] = strtotime(date('Y-m-d 00:00:00',$start));
		$config['end'] = $config['start'] + 86400;

		// $PromoterList = $this->getPromoterList();
		// if(!empty($PromoterList)){
		// 	foreach($PromoterList as $val){
		// 		$data = [
		// 			'id'           =>$val['id'],
		// 			'player_id'    =>$val['player_id'],
		// 			'bonus_type'   =>2,
		// 			'bonus_amount' =>$config['config_config']['big_bonus']['bonus']
		// 		];
		// 		$this->deal_record($data, $config);
		// 	}
		// }
        
        $PlayerList   = $this->getPlayerList($config);
        if(!empty($PlayerList)){
        	foreach($PlayerList as $val){
        		$data = [
					'id'           =>$val['id'],
					'player_id'    =>$val['promoter_player_id'],
					'bonus_type'   =>3,
					'bonus_amount' =>$config['config_config']['bonus']
				];
				$this->deal_record($val, $config);
			}
        }

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

	// protected function getPromoterList(){
	// 	$sql = "SELECT * from dc_new_promoter where status = 1 order by id asc limit ".$this->limit;
	//     $list = $this->mysql->find($sql);

	//     return $list;
	// }

	protected function getPlayerList($config){
		$sql = "SELECT * from dc_new_game_record where status in (1,2) and game_id = ".$config['config_config']['game_id']." and done_time >= ".$config['start']." and done_time < ".$config['end']." order by done_time asc limit ".$config['config_config']['bonus_day_num'];
        $list = $this->mysql->find($sql);
        $list_init = [];
        if($list){
        	foreach($list as $val){
        		if($val['status'] == 1){
        			array_push($list_init, $val);
        		}
        	}
        }

        return $list_init;
	}

	protected function deal_record($data, $config){
		$this->mysql->query('START TRANSACTION');

		$sql = "SELECT * from dc_new_bonus_num where time = ".$config['start']." and game_id = ".$config['config_config']['game_id'];
		$info = $this->mysql->find($sql);
		if($info){
			$sql = "UPDATE dc_new_bonus_num SET num = num + 1 WHERE id = ".$info[0]['id'];
			$r1 = $this->mysql->query($sql);
		}else{
			$record = [
				'date'    =>date('Y-m-d',$config['start']),
				'time'    =>$config['start'],
				'game_id' =>$config['config_config']['game_id'],
				'num'     =>1,
			];
			$r1 = $this->mysql->insert('dc_new_bonus_num', $record);
		}
		
		$r2 = $this->addBonus($data, $config);

		$r3 = $this->mysql->query('update dc_new_game_record set status = 2,bonus_id = '.$r2.' where id='.$data['id']);

		if($r1 && $r2 && $r3){
            $this->mysql->query('COMMIT');
            $this->sendBonus($r2, $config);
            return true;
        }else{
            $this->logs->info($this->logTag, "deal_record失败: r1 = $r1, r2 = $r2, r3 = $r3");
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

	protected function addBonus($data, $config){
		// $test = [7007730, 5007461];
		// if(!in_array($data['promoter_player_id'], $test)){
		// 	return true;
		// }

		$playerInfo = $this->mysql->find('select * from dc_player where player_id = '.$data['promoter_player_id']);
        if (!$playerInfo) {
            $this->logs->info($this->logTag, $data['player_id'].'该玩家不存在');
            return false;
        }else{
        	$playerInfo = $playerInfo[0];
        }
        if ($playerInfo['player_openid_gzh'] == '') {
            $this->logs->info($this->logTag, $data['promoter_player_id'].'请先在公众号进行绑定');
            return false;
        }
        if ($playerInfo['player_status'] != 1) {
            $this->logs->info($this->logTag, $data['promoter_player_id'].'您已被禁用');
            return false;
        }
		$bonusLog = [
			'player_id'    => $data['promoter_player_id'],
			'agent_id'     => 0,
			'openid_gzh'   => $playerInfo['player_openid_gzh'],
			'mch_billno'   => $this->createOrderId(30),
			'total_amount' => $config['config_config']['bonus'] * 100,
			// 'total_amount' => (int)rand(1,5) * 100,
			'type'         => 2,
			'type_name'    => '邀新活动',
			'create_time'  => time()
        ];
        return $this->mysql->insert('dc_wx_bonus_log', $bonusLog);
	}

	protected function sendBonus($id, $config){
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

        $requestUrl = $bonusConfig['send_bonus_url'] ;
        $requestUrl .= '?order_id='. $bonus['mch_billno'] ;
        $requestUrl .= '&limit='. $config['config_config']['bonus_day_num'] ;
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
				'status'      => 2,
				'send_listid' => $result['data']['send_listid'],
				'send_time'   => date('Y-m-d H:i:s')
            ];
            $ret4 = $this->mysql->update('dc_wx_bonus_log', $update, ['id'=>$id]);
        } else {
            $ret4 = false;
            $error = isset($result['errorMsg']) ? $result['errorMsg'] : '无信息返回';
            $this->logs->info($this->logTag, "红包发送失败 | id=".$id.' '.$error);
        }
	}

	protected function createOrderId($length = 24){
		$seed = md5(microtime());
        $pwd = '';
        for($i=0;$i<$length;$i++){
            $pwd.=$seed{mt_rand(0,31)};
        }
        $hash=md5($pwd);
        return substr($hash,0,$length);
	}

}


// 玩家信息回写到数据库 
function main_run() {
	while(true)
	{
		$Bonus = new Bonus();
		$id = $Bonus->main();
		if($id === false){
			break;
		}
		sleep(10);
	}
}

main_run();
