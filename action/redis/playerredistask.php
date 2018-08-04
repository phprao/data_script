<?php

/*
 *  @desc:   玩家信息
 *  @author: xxm
 *  @email:  237886849@qq.com
 *  @note:   所有文件命名以小写，所有子类名以小写
 *		     
 */

class playerredistask extends basicredistask
{

    public function __construct()
    {
        parent::__construct();
        basicfields::playerinfo_fields($this);
        basicfields::player_fields($this);
        $this->set_fields_default('player_time', 'player_time', time());
        //特有
        $this->set_fields_default('player_token', 'player_token', '');
        $this->set_data_table_info('dc_player', 'player_id');
        $this->set_redis_keys_info('user_info:', 'player_id');
        $this->set_redis_database_model(2, true);
    }

    public function on_redis_task(basicredis $redis, basicmodel $model = null, $param, $default)
    {
        /*if('hmset' == $this->m_action) {
            return $this->create_model($redis,$model,$default);
        }else if('exits' == $this->m_action) {
            return $this->is_exit_model($redis,$model,$default);
        }*/
        return parent::on_redis_task($redis, $model, $param, $default);
    }

    /*
    protected function create_player(basicredis $redis,playerinfo $player,$default) {
        if(is_null($redis) || is_null($player)) {
            return $default;
        }

        $data = $this->format_model($player);

        return $redis->set_hash_value('user_info:'.$player->get('player_id',0),$data);
    }
    
    protected function is_exit_player(basicredis $redis,playerinfo $player,$default) {
        if(is_null($redis) || is_null($player)) {
            return $default;
        }

        return $redis->is_exist('user_info:'.$player->get('player_id',0));
    }
    */
}

?>