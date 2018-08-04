<?php

/*
 *  @desc:   游戏配置信息
 *  @author: xxm
 *  @email:  237886849@qq.com
 *  @note:   所有文件命名以小写，所有子类名以小写
 *		     
 */

class gameconfigredistask extends basicredistask
{
    protected $m_keys = '';
    protected $m_fields = '';
    protected $m_value = '';

    public function set_redis_keys($keys, $fields = null, $value = null)
    {
        if (!is_null($keys)) {
            $this->m_keys = $keys;
        }
        if (!is_null($fields)) {
            $this->m_fields = $fields;
        }
        if (!is_null($value)) {
            $this->m_value = $value;
        }
    }

    public function __construct()
    {
        parent::__construct();
        $this->set_redis_name('redis_game');
        //$this->set_fields_default('desk_index','desk_index',0,'int');
    }

    public function on_redis_task(basicredis $redis, basicmodel $model = null, $param, $default)
    {
        if ('hmset_config' == $this->m_action) {
            return $this->set_game_config($redis, $param);
        }
        return parent::on_redis_task($redis, $model, $param, $default);
    }

    protected function set_game_config(basicredis $redis, $param)
    {
        if (is_null($param) || !is_array($param) || '' == $this->m_keys) return false;
        return $redis->set_hash_value($this->m_keys, $param);
    }
}

?>