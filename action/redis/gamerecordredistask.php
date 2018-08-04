<?php

/*
 *  @desc:   游戏记录
 *  @author: xxm
 *  @email:  237886849@qq.com
 *  @note:   所有文件命名以小写，所有子类名以小写
 *		     
 */

class gamerecordredistask extends basicredistask
{

    public function __construct()
    {
        parent::__construct();
        basicfields::gamerecord_fields($this);
        //特有
        //表
        //$this->set_data_table_info('dc_change_money_info','change_money_id');
        $this->set_redis_keys_info('gamerecord:', 'game_record_player_id');
        $this->set_redis_name('redis_game');
        $this->set_redis_database_model(2, true);

    }

    protected function build_redis_keys(basicredis $redis, basicmodel $model)
    {
        $game_id = $model->get('game_record_game_id', 0);
        $room_id = $model->get('game_record_room_id', 0);
        $desk_id = $model->get('game_record_desk_no', 0);
        $time = $model->get('game_record_game_over_time', 0);
        $player_id = $model->get('game_record_player_id', 0);

        $keys_value = $this->m_redis_keys . $game_id . ':' . $room_id . ':' . $desk_id . ':' . $time . ':' . $player_id;
        return $keys_value;
    }
}

?>