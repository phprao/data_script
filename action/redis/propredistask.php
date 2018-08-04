<?php
/**
 * 道具
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/20
 * Time: 13:52
 * @author Zhanghui
 */

class propredistask extends basicredistask {

    public function __construct()
    {
        parent::__construct();

        basicfields::prop_fields($this);

        $this->set_redis_name('redis_room');
        $this->set_data_table_info('dc_prop', 'prop_id');
        $this->set_redis_keys_info('prop_info:', 'prop_id');
        $this->set_redis_database_model(1, true);
    }


    public function on_redis_task(basicredis $redis, basicmodel $model = null, $param, $default)
    {
        return parent::on_redis_task($redis, $model, $param, $default);
    }
}