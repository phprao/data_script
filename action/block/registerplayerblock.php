<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/31
 * Time: 11:41
 * @author ChangHai Zhan
 */
class registerplayerblock
{
    /**
     * @var
     */
    public $app;
    /**
     * 默认特代
     * @var int
     */
    public $default_agent_id = 1;
    /**
     * 默认头像
     * @var string
     */
    public $default_player_header_image = 'http://192.168.1.210/46.jpg';
    /**
     * 默认性别
     * @var int
     */
    public $default_player_sex = 1;
    /**
     * 默认密码
     * @var int
     */
    public $default_player_password = 123456;

    /**
     * registerplayerblock constructor.
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
     * 设置推广代理人
     * @param int $agent_id
     * @return $this
     */
    public function set_parent_agent_id($agent_id = 1)
    {
        $this->default_agent_id = $agent_id;
        return $this;
    }

    /**
     * 设置初始密码
     * @param $player_password
     * @return $this
     */
    public function set_player_password($player_password)
    {
        $this->default_player_password = $player_password;
        return $this;
    }

    /**
     * @param $player_pcid
     * @param int $player_channel
     * @param int $player_guest
     * @return array|bool
     */
    public function pcid_register($player_pcid, $player_channel = playermodel::player_channel_app_android_pcid, $player_guest = playermodel::player_guest_yes)
    {
        $params['player_pcid'] = $player_pcid;
        $params['player_guest'] = $player_guest;
        $params['player_channel'] = $player_channel;
        return $this->register($params);
    }

    /**
     * 微信注册
     * @param $player_pcid
     * @param $unionid
     * @param $nickname
     * @param $headimgurl
     * @param $sex
     * @param $player_channel
     * @return array|bool
     */
    public function wechat($player_pcid, $unionid, $openid, $nickname, $headimgurl, $sex, $player_channel = playermodel::player_channel_app_android_wechat)
    {
        $params['player_pcid'] = $player_pcid;
        $params['player_name'] = '';
        $params['player_nickname'] = $nickname;
        $params['player_header_image'] = $headimgurl;
        $params['player_sex'] = $sex;
        $params['player_guest'] = playermodel::player_guest_no;
        $params['player_channel'] = $player_channel;
        $params['player_openid_app'] = $openid;
        $params['player_unionid'] = $unionid;
        return $this->register($params);
    }

    /**
     * @param $params
     * @return array|bool
     */
    protected function register($params)
    {
        $player_pcid = time();
        //数组转变量
        extract($params);
        //初始化默认数据数据
        if (!isset($player_name, $player_nickname)) {
            $only_number = $this->get_only_number();
            if (!isset($player_name)) {
                $player_name = 'guest_' . $only_number . mt_rand(1000, 9999);
            }
            if (!isset($player_nickname)) {
                $player_nickname = urlencode('游客' . $only_number . mt_rand(1000, 9999));
            }
        }
        if (!isset($player_header_image)) {
            $player_header_image = $this->default_player_header_image;
        }
        if (!isset($player_sex)) {
            $player_sex = $this->default_player_sex;
        }
        if (!isset($player_channel)) {
            $player_channel = playermodel::player_channel_app_pcid;
        }
        if (!isset($player_guest)) {
            $player_guest = playermodel::player_guest_yes;
        }

        $wx_extension = [];
        if(isset($player_openid_app)){
            $wx_extension['player_openid_app'] = $player_openid_app;
        }
        if(isset($player_unionid)){
            $wx_extension['player_unionid'] = $player_unionid;
            $wx_extension['player_name'] = '';
        }

        //事务开始
        $M = new basictransactiontask();
        $this->app->m_server->process_database($M, null, null, null);

        //添加用户
        if (!$player_id = $this->add_player($player_pcid, $player_name, $player_nickname, $player_channel, $player_guest, $wx_extension)) {
            $M->rollback();
            return false;
        }

        //添加用户账户
        if (!$player_info_id = $this->add_player_info($player_id, $player_header_image, $player_sex)) {
            $M->rollback();
            return false;
        }

        //父级代理
        $agent_parent_data = $this->get_agent_super_data();
        //添加代理表
        if (!$agent_id = $this->add_agent_info($player_id, $agent_parent_data['agent_parentid'], $agent_parent_data['agent_top_agentid'], $player_name, $agent_parent_data['agent_level'])) {
            $M->rollback();
            return false;
        }

        //添加代理账户表
        if (!$agent_account_id = $this->add_agent_account_info($agent_id)) {
            $M->rollback();
            return false;
        }

        //添加推广关系表
        if (!$promoters_id = $this->add_promoters($player_id, $agent_parent_data['agent_player_id'], $agent_id, $agent_parent_data['agent_parentid'], $agent_parent_data['agent_top_agentid'])) {
            $M->rollback();
            return false;
        }

        //推广数量添加
        if (!agentinfomodel::model($this->app)->update_agent_promote_count_by_id($agent_parent_data['agent_parentid'], $agent_parent_data['agent_promote_count'] + 1)) {
            $M->rollback();
            return false;
        }

        $M->commit();
        return [
            'player_id' => $player_id,
            'player_info_id' => $player_info_id,
            'agent_id' => $agent_id,
            'agent_account_id' => $agent_account_id,
            'promoters_id' => $promoters_id,
        ];
    }

    /**
     * 获取上级信息
     * @return mixed
     */
    protected function get_agent_super_data()
    {
        $data = [
            'agent_parentid' => $this->default_agent_id,
            'agent_top_agentid' => $this->default_agent_id,
            'agent_player_id' => 0,
            'agent_level' => 1,
            'agent_promote_count' => 0,
        ];
        $model = agentinfomodel::model($this->app)->get_agent_by_id($this->default_agent_id);
        if ($model) {
            $data['agent_parentid'] = $model->get('agent_id', 0);
            $data['agent_top_agentid'] = $model->get('agent_top_agentid', 0) == 0 ? $data['agent_parentid'] :  $model->get('agent_top_agentid', 0);
            $data['agent_player_id'] = $model->get('agent_player_id', 0);
            $data['agent_level'] = $model->get('agent_level', 0);
            $data['agent_promote_count'] = $model->get('agent_promote_count', 0);
        }
        return $data;
    }

    /**
     * 获取唯一的ID
     * @return mixed
     */
    protected function get_only_number()
    {
        $task = new utilityredistask();
        $task->set_action('incrby');
        $task->set_redis_keys('data_info:player_config', 'player_id');
        return $this->app->m_server->process_redis($task, null, 1, 1);
    }

    /**
     * @param $player_pcid
     * @param $player_name
     * @param $player_nickname
     * @param $player_channel
     * @param $player_guest
     * @param array $params
     * @return mixed
     */
    protected function add_player($player_pcid, $player_name, $player_nickname, $player_channel, $player_guest, $params = [])
    {
        $salt = basicutilhelper::create_rand_str(6);
        $data['player_pcid']          = $player_pcid;
        $data['player_name']          = $player_name;
        $data['player_nickname']      = $player_nickname;
        $data['player_password']      = loginblock::block($this->app)->password_encrypt($this->default_player_password,$salt);
        $data['player_salt']          = $salt;
        $data['player_channel']       = $player_channel;
        $data['player_guest']         = $player_guest;
        $data['player_resigter_time'] = time();
        if(!empty($params)){
            if(isset($params['player_openid_app'])){
                $data['player_openid_app'] = $params['player_openid_app'];
            }
            if(isset($params['player_name'])){
                $data['player_name'] = $params['player_name'];
            }
            if(isset($params['player_unionid'])){
                $data['player_unionid']  = $params['player_unionid'];
                $data['player_password'] = '';
                $data['player_salt']     = '';
            }
        }
        return playermodel::model($this->app)->add_player($data);
    }

    /**
     * 添加用户账户表
     * @param $player_id
     * @param $player_header_image
     * @param $player_sex
     * @param array $params
     * @return mixed
     */
    protected function add_player_info($player_id, $player_header_image, $player_sex, $params = [])
    {
        $params['player_id'] = $player_id;
        $params['player_header_image'] = $player_header_image;
        $params['player_sex'] = $player_sex;
        return playerinfomodel::model($this->app)->add_player_info($params);
    }

    /**
     * 添加代理表
     * @param $player_id
     * @param $agent_parentid
     * @param $agent_top_agentid
     * @param $player_name
     * @param $agent_level
     * @param array $params
     * @return mixed
     */
    protected function add_agent_info($player_id, $agent_parentid, $agent_top_agentid, $player_name, $agent_level, $params = [])
    {
        $params['agent_player_id'] = $player_id;
        $params['agent_parentid'] = $agent_parentid;
        $params['agent_top_agentid'] = $agent_top_agentid;
        $params['agent_name'] = $player_name;
        $params['agent_level'] = $agent_level + 1;
        $params['agent_createtime'] = time();
        $params['agent_permissions'] = 0;
        $params['agent_status'] = 1;
        $params['agent_login_status'] = 0;
        return agentinfomodel::model($this->app)->add_agent_info($params);
    }

    /**
     * 添加代理账户表
     * @param $agent_id
     * @param array $params
     * @return mixed
     */
    protected function add_agent_account_info($agent_id, $params = [])
    {
        $params['agent_account_agent_id'] = $agent_id;
        $params['agent_account_money'] = 0;
        $params['agent_account_alipay'] = '';
        $params['agent_account_username'] = '';
        $params['agent_account_mobile'] = '';
        return agentaccountinfomodel::model($this->app)->add_agent_account_info($params);
    }

    /**
     * 添加推广表
     * @param $player_id
     * @param $promoters_parent_id
     * @param $promoters_agent_id
     * @param $promoters_agent_parentid
     * @param $promoters_agent_top_agentid
     * @param $params
     * @return mixed
     */
    protected function add_promoters($player_id, $promoters_parent_id, $promoters_agent_id, $promoters_agent_parentid, $promoters_agent_top_agentid, $params = [])
    {
        $params['promoters_player_id'] = $player_id;
        $params['promoters_parent_id'] = $promoters_parent_id;
        $params['promoters_agent_id'] = $promoters_agent_id;
        $params['promoters_agent_parentid'] = $promoters_agent_parentid;
        $params['promoters_agent_top_agentid'] = $promoters_agent_top_agentid;
        $params['promoters_time'] = time();
        return promotersinfomodel::model($this->app)->add_promoters_info($params);
    }
}