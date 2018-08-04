<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/3
 * Time: 15:11
 */
class playermodel extends basicdatamodel
{

    protected $m_app;
    protected $m_def_name;
    protected $m_def_nick;
    /**
     * 是否是游客注册 不是
     */
    const player_guest_no = 0;
    /**
     * 是否是游客注册 是
     */
    const player_guest_yes = 1;
    /**
     * 注册渠道 APP 安卓设备pcid
     */
    const player_channel_app_android_pcid = 1;
    /**
     * 注册渠道 APP ios设备pcid
     */
    const player_channel_app_ios_pcid = 2;
    /**
     * 注册渠道 APP 安卓微信
     */
    const player_channel_app_android_wechat = 3;
    /**
     * 注册渠道 APP ios微信
     */
    const player_channel_app_ios_wechat = 4;
    /**
     * H5微信注册的用户（微信推广）
     */
    const player_channel_h5_wechat = 5;
    /**
     * 注册送金币
     * @var
     */
    public static $register_award = 20000;
    /**
     * playermodel constructor.
     * @param $app
     */
    public function __construct($app)
    {
        parent::__construct();
        $this->m_app = $app;
        $this->m_def_name = 'guest_';
        $this->m_def_nick = '游客';
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
     * @param $params
     * @param null $app
     * @return mixed
     */
    public function add_player($params, $app = null)
    {
        $task = new playerdatatask();
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
     * 获取玩家的信息
     * @param $app
     * @param $condition
     * @return mixed
     */
    public function get_player($condition)
    {
        $player_task = new playerdatatask();
        $player_task->set_action('select');
        $player_task->append_where($condition);
        $player_task->append_where('limit 0,1');
        $userInfo = $this->m_app->m_server->process_database($player_task, null, null, null);
        return $userInfo;
    }

    /**
     * 检查用户是否存在
     * @param $condition
     * @return bool true|false
     */
    public function exists_player($condition)
    {
        $user_info = $this->get_player($condition);

        if ($user_info) {
            return true;
        }
        return false;
    }

    /**
     * 获取用户信息
     * @param $player_id
     * @param null $app
     * @return mixed
     */
    public function get_player_by_player_id($player_id, $app = null)
    {
        $task = new playerdatatask();
        $task->set_action('select');
        $task->append_where(['player_id' => $player_id]);
        if (isset($this->m_app->m_server) && $this->m_app->m_server) {
            $app = $this->m_app;
        }
        return $app->m_server->process_database($task, $this, null, null);
    }

    /**
     * @param $player_pcid
     * @param int $player_guest
     * @param null $app
     * @return mixed
     */
    public function get_player_by_player_pcid($player_pcid, $player_guest = self::player_guest_yes, $app = null)
    {
        $task = new playerdatatask();
        $task->set_action('select');
        $task->append_where(['player_pcid' => $player_pcid]);
        $task->append_where(['player_guest' => $player_guest]);
        if (isset($this->m_app->m_server) && $this->m_app->m_server) {
            $app = $this->m_app;
        }
        return $app->m_server->process_database($task, $this, null, null);
    }

    /**
     * 用户名 查询用户
     * @param $player_name
     * @param null $app
     * @return mixed
     */
    public function get_player_by_player_name($player_name, $app = null)
    {
        $task = new playerdatatask();
        $task->set_action('select');
        $task->append_where(['player_name' => $player_name]);
        if (isset($this->m_app->m_server) && $this->m_app->m_server) {
            $app = $this->m_app;
        }
        return $app->m_server->process_database($task, $this, null, null);
    }

    /**
     * @return mixed
     */
    public function create_player()
    {
        $player_task = new playerdatatask();
        $player_task->set_action('insert');
        $user_status = $this->m_app->m_server->process_database($player_task, $this, null, null);
        return $user_status;
    }

    /**
     * @return array
     */
    public function build_player_name()
    {
        $task = new utilityredistask();
        $task->set_action('incrby');
        $task->set_redis_keys('data_info:player_config', 'player_id');
        $player_num = $this->m_app->m_server->process_redis($task, null, 1, 600000);

        $player_name = $this->m_def_name . $player_num;

        $data = array();
        if ($this->check_player_name($player_name)) {

            $data['user_name'] = $this->m_def_name . $player_num;
            $data['nick_name'] = $this->m_def_nick . $player_num;

        } else {
            $task->set_action('hmset');
            $task->set_redis_keys('data_info:player_config', 'player_id');
            $id = $this->get_max_player_id($player_num);
            $this->m_app->m_server->process_redis($task, null, $id, 0);
            //var_dump($player_num);
            //$player_num = $this->get_max_player_id($player_num);
            $player_num = $this->build_player_name_id($id);
            $data['user_name'] = $this->m_def_name . $player_num;
            $data['nick_name'] = $this->m_def_nick . $player_num;
            //var_dump($data);
            if ($player_num != $id) {
                $this->m_app->m_server->process_redis($task, null, $player_num, 0);
            }
        }

        return $data;
    }

    public function delete_player($data)
    {
        $this->m_data = $data;
        $player_task = new playerdatatask();
        $player_task->set_action('delete');
        $user_status = $this->m_app->m_server->process_database($player_task, $this, null, null);
        return $user_status;
    }

    protected function get_max_player_id($default)
    {
        $task = new utilitydatatask();
        $task->set_action('select_player_id');
        $id = $this->m_app->m_server->process_database($task, null, null, $default);
        return $id;
    }

    protected function check_player_name($name)
    {
        $player_task = new playerdatatask();
        $player_task->set_action('select');
        $player_task->append_where(array('player_name' => $name));
        $user_status = $this->m_app->m_server->process_database($player_task, null, null, null);
        if (is_null($user_status)) {
            return true;
        }

        return false;
    }

    protected function build_player_name_id($player_num)
    {
        //$player_num = $this->get_max_player_id($player_num);
        $player_name = $this->m_def_name . $player_num;
        if ($this->check_player_name($player_name)) {
            return $player_num;
        }
        for ($i = 1; $i <= 100; $i++) {
            $id = $player_num + $i;
            $player_name = $this->m_def_name . $id;
            if ($this->check_player_name($player_name)) {
                $player_num = $id;
                break;
            }
        }
        return $player_num;
    }

    public function update_playerinfo($where)
    {
        $task = new playerdatatask();
        $task->set_action('update_fields');
        $task->append_where($where);
        return $this->m_app->m_server->process_database($task, $this, null, null);
    }

    /**
     * 获取用户信息
     * @param $player_id
     * @param null $app
     * @return mixed
     */
    public function get_player_by_id($player_id, $app = null)
    {
        if (isset($this->m_app->m_server) && $this->m_app->m_server) {
            $app = $this->m_app->m_server;
        }
        $task= new playerdatatask();
        if (is_array($player_id)) {
            $task->set_action('select_list_in');
            $task->append_where_list(array('player_id' => $player_id), basicdatatask::$WHERE_TYPE_IN);
            return $app->process_database($task, null, null, null);
        } else {
            $task->set_action('select');
            $task->append_where(array('player_id' => $player_id));
            return $app->process_database($task,$this, null, null);
        }
    }
}