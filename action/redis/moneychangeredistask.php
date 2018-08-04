<?php

/*
 *  @desc:   货币改变信息
 *  @author: xxm
 *  @email:  237886849@qq.com
 *  @note:   所有文件命名以小写，所有子类名以小写
 *		     
 */

class moneychangeredistask extends basicredistask
{
    public function __construct()
    {
        parent::__construct();
        basicfields::money_change_fields($this);
        //特有
        //表
        $this->set_data_table_info('dc_change_money_info', 'change_money_id');
        $this->set_redis_keys_info('coin_change_record:', 'change_money_player_id');
        $this->set_redis_name('redis_game');
        $this->set_redis_database_model(2, true);

    }

    protected function build_redis_keys(basicredis $redis, basicmodel $model)
    {
        if (is_null($redis) || is_null($model)) {
            return $this->m_redis_keys;
        }
        //$keys_value = $this->m_redis_keys;
        $time_value = $model->get('change_money_time', 0);

        $player_id = $model->get('change_money_player_id', 0);
        $index = $player_id % 1000;
        $keys = floor($index / 10);
        //$db_index = $id % 10;
        //var_dump($keys);
        //var_dump($db_index);
        $keys_value = $this->m_redis_keys . $keys . ':' . $player_id . ':' . $time_value;
        //var_dump($keys_value);
        return $keys_value;
    }

    protected function select_redis_db(basicredis $redis, basicmodel $model = null)
    {
        if (is_null($redis) || is_null($model) || false == $this->m_user_select_redis_db) {
            return false;
        }
        $id = $model->get('change_money_player_id', 0);
        $index = $id % 10;
        $redis->select_redis($index);
        return true;
    }
}

?>