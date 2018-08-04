<?php
/**
 * 互动表情配置信息
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/20
 * Time: 13:52
 * @author Zhanghui
 */

class emoticonredistask extends basicredistask {

    public function __construct()
    {
        parent::__construct();

        basicfields::emoticon_fields($this);

        $this->set_redis_name('redis_room');
        $this->set_data_table_info('dc_emoticon', 'emoticon_id');
        $this->set_redis_keys_info('emoticon_info:', 'emoticon_id');
        $this->set_redis_database_model(2, true);
    }


    public function on_redis_task(basicredis $redis, basicmodel $model = null, $param, $default)
    {
        return parent::on_redis_task($redis, $model, $param, $default);
    }
}