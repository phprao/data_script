<?php

/*
 *  @desc:   utility任务
 *  @author: xxm
 *  @email:  237886849@qq.com
 *  @note:   所有文件命名以小写，所有子类名以小写
 *		     
 */

class utilityredistask extends basicredistask
{
    protected $m_keys = '';
    protected $m_fields = '';
    protected $m_value = '';

    public function __construct()
    {
        parent::__construct();
        $this->set_redis_name('redis_room');
        $this->set_fields_default('desk_index', 'desk_index', 0, 'int');
    }

    public function set_redis_keys($keys, $fields = null, $value = null)
    {
        if (!is_null($keys)) {
            $this->m_keys = $keys;
            $this->m_redis_keys = $keys;
        }
        if (!is_null($fields)) {
            $this->m_fields = $fields;
            $this->m_redis_fields = $fields;
        }
        if (!is_null($value)) {
            $this->m_value = $value;
        }
    }

    public function on_redis_task(basicredis $redis, basicmodel $model = null, $param, $default)
    {
        if ('incrby' == $this->m_action) {
            return $this->basic_incrby($redis, $this->m_keys, $this->m_fields, $default);
        }
        if ($this->m_action == 'code_set') {
            return $this->set_value($redis);
        }
        if ($this->m_action == 'code_get') {
            return $this->get_value($redis);
        }
        if ($this->m_action == 'code_del') {
            //todo 删除redis key 的相对应的值
            return $this->del_value($redis);
        }
        if ('hmget_fields' == $this->m_action) {
            return $this->get_club_room_desk_pos($redis);
        }
        return parent::on_redis_task($redis, $model, $param, $default);
    }

    protected function basic_incrby(basicredis $redis, $keys, $filds, $default)
    {
        if (is_null($redis) || is_null($keys) || is_null($filds)) {
            return $default;
        }

        return $redis->incr_value($keys, $filds, 1);
    }

    protected function set_value(basicredis $redis)
    {
        return $redis->set_value($this->m_keys, $this->m_fields, $this->m_value);
    }

    protected function get_value(basicredis $redis)
    {
        return $redis->get_value($this->m_keys, $this->m_fields, $default = '');
    }

    protected function del_value(basicredis $redis)
    {
        return $redis->del_key($this->m_keys);
    }

    protected function get_club_room_desk_pos(basicredis $redis)
    {
        return $redis->get_value($this->m_keys, $this->m_fields, 0);
    }
}

?>