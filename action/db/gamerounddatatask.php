<?php

/**
 * +----------------------------------------------------------
 * date: 2018-01-15 15:39:26
 * +----------------------------------------------------------
 * author: Raoxiaoya
 * +----------------------------------------------------------
 * describe: 局数
 * +----------------------------------------------------------
 */
class gamerounddatatask extends basicdatatask
{
    public function __construct()
    {
        parent::__construct();
        basicfields::gameround_fileds($this);
        $this->set_data_table_info('dc_game_round_day', 'game_round_id');

    }

    public function on_data_task(basicmysql $db, basicmodel $model = null, $param, $default)
    {
        if ('update_log_num' == $this->m_action) {
            return $this->update_log_num($db, $model, $param, $default);
        }
        
        if ('update_log_coins' == $this->m_action) {
            return $this->update_log_coins($db, $model, $param, $default);
        }
        return parent::on_data_task($db, $model, $param, $default);
    }

    public function update_log_num(basicmysql $db, basicmodel $model = null, $param, $default)
    {
        $sql = "UPDATE dc_game_round_day SET game_round_num = game_round_num + 1 where game_round_game_id = ".$param['game_round_game_id']." and game_round_timestamp = ".$param['game_round_timestamp']." and game_round_channel_id = ".$param['game_round_channel_id'];
        return $db->query($sql);
    }

    public function update_log_coins(basicmysql $db, basicmodel $model = null, $param, $default)
    {
        $sql = "UPDATE dc_game_round_day SET game_round_coins = game_round_coins + ".$param['coins']." where game_round_game_id = ".$param['game_round_game_id']." and game_round_timestamp = ".$param['game_round_timestamp']." and game_round_channel_id = ".$param['game_round_channel_id'];
        return $db->query($sql);
    }

}


?>