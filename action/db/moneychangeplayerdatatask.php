<?php
/**
 * +----------------------------------------------------------
 * date: 2018-01-29 18:33:28
 * +----------------------------------------------------------
 * author: Raoxiaoya
 * +----------------------------------------------------------
 * describe: 游戏记录
 * +----------------------------------------------------------
 */

class moneychangeplayerdatatask extends basicdatatask
{
    public function __construct()
    {
        parent::__construct();
        basicfields::money_change_player_fields($this);
        $this->set_data_table_info('dc_agents_statistics_player', 'change_money_id');
    }

    public function on_data_task(basicmysql $db, basicmodel $model = null, $param, $default)
    {
        if ('search_player_log' == $this->m_action) {
            return $this->search_player_log($db, $model, $param, $default);
        }
        
        return parent::on_data_task($db, $model, $param, $default);
    }

    // 统计玩家change_money_type IN (2,3) 
    public function search_player_log($db, $model, $param, $default){
    	$sql = "SELECT COUNT(*) as num FROM dc_agents_statistics_player where change_money_player_id = ".$param['player_id']." and change_money_time >= ".$param['start_time']." and change_money_time < ".$param['end_time']." and change_money_type IN (2,3)";
    	return $db->find($sql);
    }
}