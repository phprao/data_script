<?php

/*
 *  @desc:   玩家道具信息
 *  @author: df
 *  @time:   2018/3/22
 *  @note:   所有文件命名以小写，所有子类名以小写
 *		     
 */

class playerpropredistask extends basicredistask
{
    public function __construct() {
        parent::__construct();
        basicfields::playerpropsinfo_fields($this);
        //
        $this->set_redis_name('redis_user');
        $this->set_redis_keys_info('player_propinfo:', 'player_id');
        //$this->set_redis_database_model(1,false);
        $this->set_redis_database_model(3, true);
    }

     /**
     * 多keys
     * @param basicredis $redis
     * @param basicmodel $model
     * @return string
     */
    protected function build_redis_keys(basicredis $redis, basicmodel $model) {
        if (is_null($redis) || is_null($model)) {
            return $this->m_redis_keys;
        }
        if (3 == $this->m_redis_key_model) {
            $id = $model->get($this->m_redis_fields, 0);
            //$index = $id % 1000;
            // $keys = floor($index / 10);
            //$db_index = $id % 10;
            //var_dump($keys);
            //var_dump($db_index);
            $prop_id = $model->get('prop_id', 0);
            //$keys_value = $this->m_redis_keys . ($club_id % 100)  . ':' . $club_id . ':' . $keys . ':' . $id;
            //$keys_value = $this->m_redis_keys . $prop_id . ':' . $keys . ':' . $id;
            $keys_value = $this->m_redis_keys . $prop_id . ':' . $id;
            return $keys_value;
        } else {
            return parent::build_redis_keys($redis, $model);
        }
    }
}

?>