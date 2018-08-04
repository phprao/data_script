<?php

/*
 *  @desc:   俱乐玩家信息
 *  @author: xxm
 *  @email:  237886849@qq.com
 *  @note:   所有文件命名以小写，所有子类名以小写
 *           
 */

class clubplayerredistask extends basicredistask
{
    /**
     * clubplayerredistask constructor.
     */
    public function __construct()
    {
        parent::__construct();
        basicfields::clubplayer_fields($this);
        //
        $this->set_redis_keys_info('club_user:', 'player_id');
        //$this->set_redis_database_model(1,false);
        $this->set_redis_database_model(3, true);
    }

    /**
     * 多keys
     * @param basicredis $redis
     * @param basicmodel $model
     * @return string
     */
    protected function build_redis_keys(basicredis $redis, basicmodel $model)
    {
        if (is_null($redis) || is_null($model)) {
            return $this->m_redis_keys;
        }
        if (3 == $this->m_redis_key_model) {
            $id = $model->get($this->m_redis_fields, 0);
            $index = $id % 1000;
            $keys = floor($index / 10);
            //$db_index = $id % 10;
            //var_dump($keys);
            //var_dump($db_index);
            $club_id = $model->get('club_id', 0);
            //$keys_value = $this->m_redis_keys . ($club_id % 100)  . ':' . $club_id . ':' . $keys . ':' . $id;
            $keys_value = $this->m_redis_keys . $club_id . ':' . $keys . ':' . $id;
            return $keys_value;
        } else {
            return parent::build_redis_keys($redis, $model);
        }
    }
}
