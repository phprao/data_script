<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/31
 * Time: 11:41
 * @author ChangHai Zhan
 */
class playerredisblock
{
    /**
     * @var
     */
    public $app;
    /**
     * 禁止同步redis
     * @var array
     */
    public $exception_fields = [
        'player_time',
        'player_token',
        'player_coins',
        'player_money',
        'player_masonry',
    ];

    /*
     *  要同步到数据字段
     */
    public $sync_data_fields = 
    [
        'player_coins',
        'player_money',
        'player_masonry',
    ];

    public $player_redis_time_out = 86400;
    /**
     * loginblock constructor.
     * @param $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * 静态实例化
     * @param string $app
     * @param string $className
     * @return static active record model instance.
     */
    public static function block($app = null, $className = __CLASS__)
    {
        return new $className($app);
    }

    /**
     * 获取redis
     * @param $player_id or model
     * @return mixed
     */
    public function get_player($player_id)
    {
        $redis_task = new playerredistask();
        $redis_task->set_action('hmget');
        if (!is_object($player_id)) {
            $model = new playermodel($this->app);
            $model->insert('player_id', $player_id);
        } else {
            $model = $player_id;
        }
        return $this->app->m_server->process_redis($redis_task, $model, null, null);
    }

    /**
     * 设置
     * @param $model
     * @return mixed
     */
    protected function set_player($model)
    {
        $redis_task = new playerredistask();
        $redis_task->set_action('hmset');
        return $this->app->m_server->process_redis($redis_task, $model, null, null);
    }

    /**
     * 添加
     * @param $model
     * @return mixed
     */
    public function create_player($model)
    {
        return $this->set_player($model);
    }

    /**
     * 更新字段
     * @param $model
     * @param array $fields
     * @return bool|mixed
     */
    public function update_player($model, $fields = [])
    {
        $redis_model = $this->get_player($model);
        if (!$redis_model) {
            return false;
        }
        foreach ($fields as $key => $value) {
            $redis_model->insert($key, $value);
        }
        return $this->set_player($redis_model);
    }

    /**
     * 计入数据
     * @param $model
     * @param $field
     * @param $value
     * @return bool
     */
    public function incr_player($model, $field, $value)
    {
        $redis_model = $this->get_player($model);
        if (!$redis_model) {
            return false;
        }
        $redis_task = new playerredistask();
        $redis_task->set_action('incrby_model');
        $result = $this->app->m_server->process_redis($redis_task, $redis_model, [$field => $value], false);
        return isset($result[$field]) ? $result[$field] : false;
    }

    /**
     * 同步字段
     * @param $model
     * @return bool
     */
    public function sync_player($model)
    {
        if (!is_object($model)) {
            return false;
        }
        $redis_model = $this->get_player($model);
        if (!$redis_model) {
            return false;
        }
        foreach ($this->exception_fields as $field) {
            if ($redis_model->get($field, false) !== false) {
                $model->delete($field);
            }
        }
        $model->copy($redis_model);
        return $this->set_player($redis_model);
    }

    public function get_player_by_keys($key) {
        $model_player = new playerinfomodel($this->app);
        $player = $model_player->get_player_info_by_keys($key);
        return $player;
    }

    public function sync_player_redis_to_database($key) {
        $redis_model = $this->get_player_by_keys($key);
        if (!$redis_model) {
            return false;
        }

        return $this->sync_redis_player_model_to_database($redis_model,true);
    }

    public function sync_redis_player_model_to_database($model,$del = false) {
        if (!is_object($model)) {
            return false;
        }

        $redis_model = $model;
        $data_model = new basicdatamodel(); 
        $data_model->insert('player_id',$redis_model->get('player_id',0));
        foreach ($this->sync_data_fields as $field) {
            $value = $redis_model->get($field, null);
            if($value != null) {
                $data_model->insert($field,$value );
            }
        }
        //var_dump($data_model);
        $result = $this->save_data_player($data_model);
        if($result && $del) {
            $last_time = $redis_model->get('player_online',time());
            $cur_time = time();
            $use_time = $cur_time - $last_time;
            if($use_time >= $this->player_redis_time_out) {
                $redis_task = new playerredistask();
                $redis_task->set_action('delete');
                $this->app->m_server->process_redis($redis_task,$redis_model,null,null);
            }
        }

        return $result;
    }

    //
    public function save_data_player($model) {
        if (!is_object($model)) {
            return false;
        }
        $ok = false;
        $strans = basicglobaltransaction::get_instance()->get_strans();//new basictransactiontask();
        $this->app->m_server->process_database($strans, null, null, null);
        do
        {
            //$player_task = new playerdatatask();
            //$player_task->set_action('update_fields');

            //$result = $this->app->m_server->process_database($player_task,$model,null,null);
            //if(!$result)
            //    break;
            
            $player = new basicdatamodel();
            $model->copy($player);
            $player->delete('player_id');

            $player_id = $model->get('player_id',0);

            $playerinfo_task = new playerinfodatatask();
            $playerinfo_task->set_action('update_fields');
            $playerinfo_task->append_where(array('player_id'=>$player_id));

            $result = $this->app->m_server->process_database($playerinfo_task,$player,null,null);
            if(!$result)
                break;

            $ok = true;
        }while(false);
        if($ok) {
            $strans->commit();
        }else {
            $strans->rollback();
        }

        return $ok;
    }

    public function update_player_online($model) {
        if (!is_object($model)) {
            return false;
        }

        $fileds = 
        ['player_id',
         'player_online'
        ];
        return $this->update_player($model,$fileds);
    }
}