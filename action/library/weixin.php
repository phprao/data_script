<?php

/**
 * 微信登录移动应用的处理
 * Created by PhpStorm.
 * User: 王称意
 * Date: 2018/1/3
 * Time: 11:17
 */
class weixin
{

    protected $wx_data;

    public function __construct($app)
    {
        $this->app = $app;
        $this->wx_data = $this->app->m_config->get('weixin_info', '');
    }

    /**
     * 获取access_token的方法
     * @param $code string 获取的授权code
     * @return mixed|string
     */
    public function get_access_token($code)
    {
        $appid = $this->wx_data['weixin_app_id'];
        $secret = $this->wx_data['weixin_app_secret'];
        $access_token_url = $this->wx_data['weixin_access_token_url'];

        $url = $access_token_url . 'appid=' . $appid . '&secret=' . $secret . '&code=' . $code . '&grant_type=authorization_code';
        // $ch = new httpclient();
        $result = file_get_contents($url);//$ch->get($url); //todo 模拟的返回数据;
        /*        $result = '{
                    "access_token":"ACCESS_TOKEN", 
                    "expires_in":7200, 
                    "refresh_token":"REFRESH_TOKEN",
                    "openid":"OPENID", 
                    "scope":"SCOPE",
                    "unionid":"o6_bmasdasdsad6_2sgVt7hMZOPfL"
                    }';*/
        $result = json_decode($result, true);
        return $result;
    }

    /**
     * 获取用户的基本信息
     * @param $accessToken string 微信的token
     * @param $openid string 用户的openid
     * @return array
     */
    public function get_user_info($accessToken, $openid)
    {
        $user_url = $this->wx_data['weixin_user_url'];
        $url = $user_url . 'access_token=' . $accessToken . '&openid=' . $openid;  // todo https://api.weixin.qq.com/sns/userinfo?access_token=ACCESS_TOKEN&openid=OPENID

        //$ch = new httpclient();
        $result = file_get_contents($url);//$ch->get($url);

        //todo 模拟请求
        /*        $result = '{
                    "openid":"OPENID",
                    "nickname":"NICKNAME",
                    "sex":1,
                    "province":"PROVINCE",
                    "city":"CITY",
                    "country":"COUNTRY",
                    "headimgurl": "http://wx.qlogo.cn/mmopen/g3MonUZtNHkdmzicIlibx6iaFqAc56vxLSUfpb6n5WKSYVY0ChQKkiaJSgQ1dZuTOgvLLrhJbERQQ4eMsv84eavHiaiceqxibJxCfHe/0",
                    "privilege":[
                    "PRIVILEGE1", 
                    "PRIVILEGE2"
                    ],
                    "unionid": " o6_bmasdasdsad6_2sgVt7hMZOPfL"
                    }';*/

        return json_decode($result, true);

    }

    /**
     * assess_token 过期了刷新的操作
     * @param $refresh_token
     * @return mixed
     */
    public function refresh_token($refresh_token)
    {
        $appid = $this->wx_data['weixin_app_id'];
        $refresh_url = $this->wx_data['weixin_refresh_url'];
        $url = $refresh_url . 'appid=' . $appid . '&grant_type=refresh_token&refresh_token=' . $refresh_token;// //https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=APPID&grant_type=refresh_token&refresh_token=REFRESH_TOKEN
        $ch = new httpclient();
        $result = $ch->get($url);

        return $result;
    }

}