<?php

/*
 *  @desc:   playerinfo数据模型
 *  @author: xxm
 *  @email:  237886849@qq.com
 *  @note:   所有文件命名以小写，所有子类名以小写
 *
 */

class playerinfomodel extends basicdatamodel
{
    /**
     * 钱类型对应的字段
     * @var array
     */
    public static $money_type = [
        //金币
        changemoneyinfomodel::change_money_money_type_gold => 'player_coins',
        //钻石
        changemoneyinfomodel::change_money_money_type_masonry => 'player_masonry',
        //代币
        changemoneyinfomodel::change_money_money_type_token => 'player_tokens',
    ];
    /**
     * 认证状态 没有认证
     */
    const player_author_no = 0;
    /**
     * 认证状态 已经认证
     */
    const player_author_yes = 1;

    protected $m_app;

    public function __construct($app)
    {
        parent::__construct();
        $this->m_app = $app;
    }

    /**
     * 静态实例化
     * @param string $app
     * @param string $className
     * @return static active record model instance.
     */
    public static function model($app = null, $className = __CLASS__)
    {
        return new $className($app);
    }

    /**
     * 添加用户
     * @param $params
     * @param null $app
     * @return mixed
     */
    public function add_player_info($params, $app = null)
    {
        $task = new playerinfodatatask();
        $task->set_action('insert_fields');
        foreach ($params as $key => $value) {
            $this->insert($key, $value);
        }
        if (isset($this->m_app->m_server) && $this->m_app->m_server) {
            $app = $this->m_app;
        }
        return $app->m_server->process_database($task, $this, null, null);
    }

    /**
     * 获取用户账户表
     * @param $player_id
     * @param null $app
     * @return mixed
     */
    public function get_player_info_by_player_id($player_id, $app = null)
    {
        $task = new playerinfodatatask();
        $task->set_action('select');
        $task->append_where(['player_id' => $player_id]);
        if (isset($this->m_app->m_server) && $this->m_app->m_server) {
            $app = $this->m_app;
        }
        return $app->m_server->process_database($task, $this, null, null);
    }

    /**
     * 更新 认证状态
     * @param $player_id
     * @param int $player_author
     * @param null $app
     * @return mixed
     */
    public function update_player_author($player_id, $player_author = self::player_author_yes, $app = null)
    {
        $task = new playerinfodatatask();
        $task->set_action('update_fields');
        $task->append_where(['player_id' => $player_id]);
        $this->update('player_author', $player_author);
        if (isset($this->m_app->m_server) && $this->m_app->m_server) {
            $app = $this->m_app;
        }
        return $app->m_server->process_database($task, $this, null, null);
    }

    /**
     * 更新钱
     * @param $player_id
     * @param $money
     * @param string $money_type
     * @param null $app
     * @return mixed
     */
    public function update_money($player_id, $money, $money_type, $app = null)
    {
        if (!isset(self::$money_type[$money_type])) {
            return false;
        }
        $task = new playerinfodatatask();
        $task->set_action('update_fields');
        $task->append_where(['player_id' => $player_id]);
        $this->update(self::$money_type[$money_type], $money);
        if (isset($this->m_app->m_server) && $this->m_app->m_server) {
            $app = $this->m_app;
        }
        return $app->m_server->process_database($task, $this, null, null);
    }

    public function set_m_data($data)
    {
        $this->m_data = $data;
    }

    /**
     * 添加用户的信息表
     * @param $data
     * @return mixed
     */
    public function create_player_info()
    {
        $task = new playerinfodatatask();
        $task->set_action('insert');
        $player_info = $this->m_app->m_server->process_database($task, $this, null, null);
        return $player_info;
    }


    public function reload_redis_player_model()
    {
        $player_task = new playerinfodatatask();
        $player_task->set_action('select');
        $player_task->append_where(array('player_id' => $this->get('player_id', 0)));
        $player_task->append_where('limit 0,1');
        $user_info = $this->m_app->m_server->process_database($player_task, $this, null, null);
        if (is_null($user_info)) {
            $this->create_player_info();
            return true;
        }

        $palyer_info = new basicdatamodel();
        $user_info->copy($palyer_info);

        $redis_task = new playerredistask();
        $redis_task->set_action('hmget');
        $user_info = $this->m_app->m_server->process_redis($redis_task, $this, null, null);
        if (is_null($user_info)) {
            return false;
        }

        $player_money = $user_info->get('player_money', 0);
        $player_coins = $user_info->get('player_coins', 0);
        $palyer_info->insert('player_money', $player_money);
        $palyer_info->insert('player_coins', $player_coins);

        $palyer_info->copy($this);
        return true;
    }

    public function sync_redis_player_model()
    {
        $redis_task = new playerredistask();
        $redis_task->set_action('hmset');
        return $this->m_app->m_server->process_redis($redis_task, $this, null, null);
    }

    public function get_redis_player_model()
    {
        $redis_task = new playerredistask();
        $redis_task->set_action('hmget');
        $user_info = $this->m_app->m_server->process_redis($redis_task, $this, null, null);
        if (is_null($user_info)) {
            return false;
        }
        $user_info->copy($this);
        return true;
    }

    public function get_push_info()
    {
        /*
        $task = new systemconfigdatatask();
        $task->set_action('select');
        $task->append_where(array('system_config_club_id'=>0));
        $task->append_where(array('system_config_type'=>1));

        $config_info = $this->m_app->m_server->process_database($task,null,null,null);

        $data = array();
        $data['push_host'] = '127.0.0.1';
        $data['push_port'] = 7011;

        if(is_null($config_info)) return $data;

        $json_value = $config_info->get('system_config_data','{}');
        if('' == $json_value) return $data;
        $json_object = json_decode($json_value,true);

        $data['push_host'] = $json_object['push_host'];
        $data['push_port'] = $json_object['push_port'];

        return $data;
        */
        $data = array();
        $data['push_host'] = '127.0.0.1';
        $data['push_port'] = 7011;
        $result = $this->m_app->m_config->get('push_info', null);
        if (is_null($result)) {
            return $data;
        }
        $data['push_host'] = $result['push_host'];
        $data['push_port'] = $result['push_udp'];

        return $data;
    }

    /**
     * 获取用户信息
     * @param $player_id
     * @param null $app
     * @return mixed
     */
    public function get_player_img($player_id, $app = null)
    {
        if (isset($this->m_app->m_server) && $this->m_app->m_server) {
            $app = $this->m_app->m_server;
        }
        $task= new playerinfodatatask();
        if (is_array($player_id)) {
            $task->set_action('select_list_in');
            $task->append_where(array('player_id' => $player_id));
            return $app->process_database($task, null, null, null);
        } else {
            $task->set_action('select');
            $task->append_where(array('player_id' => $player_id));
            return $app->process_database($task,null,null,null);
        }
    }

    public function update_playerinfo($where) {
        $task = new playerinfodatatask();
        $task->set_action('update_fields');
        $task->append_where($where);
        return $this->m_app->m_server->process_database($task, $this, null, null);
    }

    public function get_all_player_id_list($db_num,$iter,$index,$count) {
        $param = array();
        //$param['iter'] = null;
        //$param['pattern'] = 'user_info:0:*';
        //$param['count'] = 1000;
        $this->insert('player_id',$db_num);
        $param['iter'] = $iter;
        $param['pattern'] = 'user_info:'.$index.':*';
        $param['count'] = $count;
        $task = new playerredistask();
        $task->set_action('scan');
        $result = $this->m_app->m_server->process_redis($task,$this,$param,null);
        return $result;
    }

    public function get_player_info_by_keys($key) {
        $task = new playerredistask();
        $task->set_action('hmget');
        $result = $this->m_app->m_server->process_redis($task,null,$key,null);
        return $result;
    }
}
