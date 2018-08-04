<?php

/*
 *  @desc:   utility工具类
 *  @author: xxm
 *  @email:  237886849@qq.com
 *  @note:   所有文件命名以小写，所有子类名以小写
 *		     
 */

class basicutilhelper
{

    public static function build_token($player_id, $defalut)
    {
        if (is_null($player_id)) {
            return $defalut;
        }

        //$player_id = $player->get('player_id',0);
        $time = time();
        $rand = rand(1024, 1000000);

        $token_value = $player_id . '_' . $time . '_' . $rand;
        //var_dump($token_value);
        $token = basicsecurity::encryp_data_ex($player_id, $token_value);
        //var_dump($token);
        $token = base64_encode($token);

        return $token;
        //var_dump($token);
        //$token = base64_decode($token);

        //$token_value = basicsecurity::decryp_data_ex($player_id,$token);
        //var_dump($token_value);
    }

    public static function parse_token($player_id, $token)
    {
        if (is_null($token) || is_null($player_id)) {
            return null;
        }

        $token = base64_decode($token);

        $token_value = basicsecurity::decryp_data_ex($player_id, $token);

        $result = explode('_', $token_value);
        $data = array();
        $data['player_id'] = 0;
        $data['time'] = 0;
        $data['rand'] = 0;
        if (is_null($result) || !is_array($result) || count($result) < 3) {
            return $data;
        }
        //var_dump($result);
        //$data = array();
        $data['player_id'] = $result[0];
        $data['time'] = $result[1];
        $data['rand'] = $result[2];

        //var_dump($data);
        return $data;
    }

    //获取客户端的真实Ip
    public static function get_real_ip(){

        global $ip; 

        if (getenv("HTTP_CLIENT_IP")) 
        $ip = getenv("HTTP_CLIENT_IP"); 
        else if(getenv("HTTP_X_FORWARDED_FOR")) 
        $ip = getenv("HTTP_X_FORWARDED_FOR"); 
        else if(getenv("REMOTE_ADDR")) 
        $ip = getenv("REMOTE_ADDR"); 
        else 
        $ip = "Unknow"; 

        return $ip;                 
    }
    // 随机生成字符串
    public static function create_rand_str($length){
        $stt = join('',range('a','z')) . join('',range('A','Z')) . join('',range(0,9));
        $len = strlen($stt);
        $re = '';
        for($i=0;$i<$length;$i++){
            $re .= $stt[rand(0,$len-1)];
        }
        return $re;
    }
}

?>