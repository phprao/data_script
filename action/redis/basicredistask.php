<?php

/*
 *  @desc:   redis任务
 *  @author: xxm
 *  @email:  237886849@qq.com
 *  @note:   所有文件命名以小写，所有子类名以小写
 *
 */

class basicredistask extends basictaskimpl
{
    protected $m_redis_key_model = 0;
    protected $m_user_select_redis_db = false;
    protected $m_redis_db_name = 'redis_user';

    public function on_redis_task(basicredis $redis, basicmodel $model = null, $param, $default)
    {
        $this->select_redis_db($redis, $model);
        if ('hmset' == $this->m_action) {
            return $this->create_model($redis, $model, $param, $default);
        } else if ('exits' == $this->m_action) {
            return $this->is_exit_model($redis, $model, $default);
        } else if ('hmget' == $this->m_action) {
            return $this->find_hash_model($redis, $model, $param, $default);
        } else if ('incrby' == $this->m_action) {
            return $this->set_incrby($redis, $this->m_redis_keys, $this->m_redis_fields, $param, $default);
        } else if ('incrby_model' == $this->m_action) {
            return $this->set_incrby_model($redis, $model, $param, $default);
        } else if ('delete' == $this->m_action) {
            return $this->delete_model($redis, $model, $param, $default);
        } else if ('scan' == $this->m_action) {
            return $this->scan_keys($redis, $model, $param, $default);
        } else if( 'keys' == $this->m_action) {
            return $this->get_keys($redis, $model, $param, $default);
        }
        return $default;
    }

    //==============以下是对mode 基本操作,如不足请在子类扩展===============
    protected function create_model(basicredis $redis, basicmodel $model = null, $param, $default)
    {
        if (is_null($redis)) {
            return $default;
        }
        if (!is_null($model)) {
            $data = $this->format_model($model);
            //var_dump($data);
            $keys = $this->build_redis_keys($redis, $model);
            return $redis->set_hash_value($keys, $data);
        } else if (!is_null($param) && is_array($param)) {
            //var_dump($param);
            foreach ($param as $key => $value) {
                $data = $this->format_model($value);
                //var_dump($data);
                $keys = $this->build_redis_keys($redis, $value);
                //var_dump($keys);
                $redis->set_hash_value($keys, $data);
            }
        } else if (!is_null($param) && !is_array($param)) {
            $data = array($this->m_redis_fields => $param);
            $redis->set_hash_value($this->m_redis_keys, $data);
        }

    }

    protected function is_exit_model(basicredis $redis, basicmodel $model, $default)
    {
        if (is_null($redis) || is_null($model)) {
            return $default;
        }
        $keys_value = $this->m_redis_keys . $model->get($this->m_redis_fields, 0);
        if (!is_null($model)) {
            $keys_value = $this->build_redis_keys($redis, $model);
        }
        return $redis->is_exist($keys_value);
    }

    protected function delete_model(basicredis $redis, basicmodel $model, $param, $default)
    {
        if (is_null($redis) || is_null($model)) {
            return $default;
        }

        $keys = $this->build_redis_keys($redis, $model);

        return $redis->del_key($keys);
    }

    protected function find_hash_model(basicredis $redis, basicmodel $model = null, $param = null, $default = null)
    {
        if (is_null($redis)) {
            return $default;
        }

        if (!is_null($model)) {
            $keys = $this->build_redis_keys($redis, $model);
            //var_dump($keys);
            $data = $redis->get_hash_value($keys);
            if (is_null($data) || empty($data)) {
                return $default;
            }
            $this->m_data = $this->parse_model($model, $data);
            return $this->m_data;
        } else if (!is_null($param) && is_array($param)) {
            //var_dump($param);
            //$list = array();
            if (empty($param)) return $default;
            $object = $param[0];
            if ($object instanceof basicdatamodel) {
                foreach ($param as $key => $value) {
                    $keys = $this->build_redis_keys($redis, $model);
                    //var_dump($keys);
                    $data = $redis->get_hash_value($keys);
                    if (is_null($data) || empty($data)) {
                        continue;
                    }
                    $data_value = $this->parse_model($model, $data);
                    array_push($list, $data_value);
                }
                return $list;
            } else {
                $model = new basicdatamodel();
                foreach ($param as $key => $value) {
                    $model->insert($key, $value);
                }
                $keys = $this->build_redis_keys($redis, $model);
                $data = $redis->get_hash_value($keys);
                if (is_null($data) || empty($data)) {
                    return $default;
                }
                $this->m_data = $this->parse_model($model, $data);
                return $this->m_data;
            }
        } else if (!is_null($param) && !is_array($param)) {
            $keys = $this->m_redis_keys;//$this->build_redis_keys($redis, $model);
            if(is_string($param)) {
                $keys = $param;
            }
            $data = $redis->get_hash_value($keys);
            if (is_null($data) || empty($data)) {
                return $default;
            }
            $this->m_data = $this->parse_model($model, $data);
            return $this->m_data;
        }else {
            $keys = $this->m_redis_keys;//$keys = $this->build_redis_keys($redis, $model);
            $data = $redis->get_hash_value($keys);
            if (is_null($data) || empty($data)) {
                return $default;
            }
            $this->m_data = $this->parse_model($model, $data);
            return $this->m_data;
        }

        return $default;
    }

    //=======================================

    protected function select_redis_db(basicredis $redis, basicmodel $model = null)
    {
        if (is_null($redis) || is_null($model) || false == $this->m_user_select_redis_db) {
            return false;
        }
        $id = $model->get($this->m_redis_fields, 0);
        $index = $id % 10;
        $redis->select_redis($index);
        return true;
    }

    protected function build_redis_keys(basicredis $redis, basicmodel $model)
    {
        if (is_null($redis) || is_null($model)) {
            return $this->m_redis_keys;
        }
        $keys_value = $this->m_redis_keys;
        if (0 == $this->m_redis_key_model) {
            $keys_value = $this->m_redis_keys;
        } else if (1 == $this->m_redis_key_model) {
            $keys_value = $this->m_redis_keys . $model->get($this->m_redis_fields, 0);
        } else if (2 == $this->m_redis_key_model) {
            $id = $model->get($this->m_redis_fields, 0);
            $index = $id % 1000;
            $keys = floor($index / 10);
            //$db_index = $id % 10;
            //var_dump($keys);
            //var_dump($db_index);
            $keys_value = $this->m_redis_keys . $keys . ':' . $id;
        }
        //var_dump($keys_value);
        return $keys_value;
    }

    protected function set_redis_database_model($redis_key_model, $use_select_db)
    {
        if (!is_null($redis_key_model)) {
            $this->m_redis_key_model = $redis_key_model;
        }

        if (!is_null($use_select_db)) {
            $this->m_user_select_redis_db = $use_select_db;
        }
    }

    protected function set_redis_name($redis_name)
    {
        if (!is_null($redis_name)) {
            $this->m_redis_db_name = $redis_name;
        }
    }

    protected function set_incrby(basicredis $redis, $keys, $filds, $value, $default)
    {
        if (is_null($redis) || is_null($keys) || is_null($filds) || is_null($value)) {
            return $default;
        }

        return $redis->incr_value($keys, $filds, $value);
    }


    protected function set_incrby_model(basicredis $redis, basicmodel $model = null, $param, $default)
    {
        $keys = $this->build_redis_keys($redis, $model);

        if (is_null($param)) {
            return $default;
        }

        if (is_array($param)) {
            $data = array();
            foreach ($param as $filds => $value) {
                $result = $redis->incr_value($keys, $filds, $value);
                $data[$filds] = $result;
            }
            return $data;
        } else if (is_string($param)) {
            $redis->incr_value($keys, $param, 1);
        }
        return $default;
    }

    public function select_task_redis(basicserver $server)
    {
        if (is_null($server)) {
            return false;
        }
        //var_dump($this->m_redis_db_name);
        $server->select_redis($this->m_redis_db_name);
        return true;
    }

    protected function scan_keys(basicredis $redis, basicmodel $model = null, $param, $default)
    {
        if (is_null($redis)) $default;
        //$keys = $this->build_redis_keys($redis,$model);

        if (is_null($param)) {
            return $default;
        }

        $result = $redis->scan_keys($param['iter'], $param['pattern'], $param['count']);
        return $result;
    }

    protected function get_keys(basicredis $redis, basicmodel $model = null, $param, $default) {
        $data = array();
        $data['iter'] = 0;
        $data['list'] = array();
        if (is_null($redis)) $data;
        //$keys = $this->build_redis_keys($redis,$model);

        if (is_null($param)) {
            return $data;
        }

        $result = $redis->get_keys($param['pattern'],$default);
        //var_dump($result);
        $data['list'] = $result;
        return $data;
    }
}

?>