<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/11
 * Time: 12:33
 * @author Zhanghui
 */

class propidredistask extends basicredistask {

    public function __construct()
    {
        parent::__construct();

        $this->set_fields_default('prop_id', 'prop_id', array());

        $this->set_redis_name('redis_room');
        $this->set_redis_keys_info('prop_id:0', 'prop_id');
        $this->set_redis_database_model(1, false);
    }

}