<?php

/*
 *  @desc:   游戏信息
 *  @author: xxm
 *  @email:  237886849@qq.com
 *  @note:   所有文件命名以小写，所有子类名以小写
 *		     
 */

class gameinforedistask extends basicredistask
{
    public function __construct()
    {
        parent::__construct();
        basicfields::gameinfo_fields($this);
        //特有
        $this->set_fields_default('game_club_id', 'game_club_id', 0, 'int');
        //表
        $this->set_data_table_info('dc_game_info', 'game_id');
        $this->set_redis_keys_info('game_info:', 'game_id');
        $this->set_redis_name('redis_room');
        $this->set_redis_database_model(1, false);

    }

    public function on_redis_task(basicredis $redis, basicmodel $model = null, $param, $default)
    {
        //var_dump($redis);
        return parent::on_redis_task($redis, $model, $param, $default);
    }

}

?>