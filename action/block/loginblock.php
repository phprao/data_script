<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/31
 * Time: 11:41
 * @author ChangHai Zhan
 */
class loginblock
{
    /**
     * @var
     */
    public $app;
    /**
     * 禁止同步redis
     * @var array
     */
    public $private_fields = [
        'player_safe_box_password',
        // 'player_password',
        'player_salt',
        // 'player_identification_number',
        // 'player_identification_name',
        'id'
    ];
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
     * 是否登陆
     * @param $player_id
     * @param $token
     * @return bool|mixed
     */
    public function is_login($player_id, $token)
    {
        $model = playerredisblock::block($this->app)->get_player($player_id);
        if ($model && $token && $model->get('player_token', false) == $token) {
            return $model;
        }
        return false;
    }

    /**
     * 登陆
     * @param $player_id
     * @return bool|string
     */
    public function login($player_id)
    {
        //加载用户信息
        $player_model = playermodel::model($this->app)->get_player_by_id($player_id);
        if (!$player_model) {
            return false;
        }
        $player_info_model = playerinfomodel::model($this->app)->get_player_info_by_player_id($player_id);
        if (!$player_info_model) {
            return false;
        }
        //合并，将 $player_info_model 合并到 $player_model
        $player_info_model->copy($player_model);
        $token = $this->get_token($player_id);
        //加载redis
        $model = playerredisblock::block($this->app)->get_player($player_model);
        if ($model) {
            //刷新动态player_token 到 redis
            $update_data = [
                'player_token'        => $token,
                'player_login_time'   => time(),
                'player_login_ip'     => basicutilhelper::get_real_ip(),
                'player_header_image' => $player_model->get('player_header_image',''),
                'player_nickname'     => $player_model->get('player_nickname','')
            ];
            $return = playerredisblock::block($this->app)->update_player($model, $update_data);
            // playerredisblock::block($this->app)->sync_player($player_model); // 禁止反向刷新
        } else {
            $player_model->insert('player_token', $token);
            $player_model->set('player_login_time',time());
            $player_model->set('player_login_ip',basicutilhelper::get_real_ip());
            $return = playerredisblock::block($this->app)->create_player($player_model);
        }
        if ($return) {
            return playerredisblock::block($this->app)->get_player($player_model);
        }
        return false;
    }

    public function update_player(basicmodelimpl $model) {

        $player_nickname = $model->get('player_nickname','');
        $player_id = $model->get('player_id',0);
        if(!empty($player_nickname))
        {
            //$player_nickname = urlencode($player_nickname);
            $player = new playermodel($this->app);
            //$player->insert('player_id',$player_id);
            $player->insert('player_nickname',$player_nickname);
            $player->update_playerinfo(array('player_id'=>$player_id));
        }
        

        $player_header_image = $model->get('player_header_image',0);
        if(!empty($player_header_image))
        {
            $player_info = new playerinfomodel($this->app);
            $player_info->insert('player_header_image',$player_header_image);
            $player_info->update_playerinfo(array('player_id'=>$player_id));
        }
        
        
    }

    /**
     * @param $model
     * @return mixed
     */
    public function get_safe_player_info($model)
    {
        foreach ($this->private_fields as $field) {
            $model->delete($field);
        }
        return $model;
    }

    /**
     * 验证密码
     * @param $password
     * @param $pwd
     * @param string $salt
     * @return bool
     */
    public function password_verify($password, $pwd, $salt = '')
    {
        if (!$password || !$pwd) {
            return false;
        }
        return self::password_encrypt($password, $salt) === $pwd;
    }

    /**
     * 加密
     * @param $password
     * @param string $salt
     * @return string
     */
    public function password_encrypt($password, $salt = '')
    {
        if (empty($salt)) {
            $salt = 'dcyx*#';
        }
        return md5($salt . $password);
    }

    /**
     * 获取token
     * @param $player_id
     * @return string
     */
    public function get_token($player_id)
    {
        return basicutilhelper::build_token($player_id, '');
    }
}