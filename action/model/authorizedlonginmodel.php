<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/26
 * Time: 18:29
 */
class authorizedlonginmodel extends basicdatamodel
{
    public $url = null;
    /**
     * @param $player
     * @param null $uer
     * @param null $extension
     * @return mixed
     * curl H5 接口
     */
    public function authorizedlongin($player, $url = null, $extension = null)
    {
        $client = new httpclient();
        $player_id = $player;
        $time = '2018';
        $key = 'dcyouxi';
        $sign = md5($player . $time . $key);
        if (!$url) {
            $url = "http://127.0.0.1/dc_php/sc_kfa/dc_u3d/dc_u3dapi_admin/public/api/v1/login/authorized_longin?";
        }
         $curl = $url . "player_id=" . $player_id . '&time=' . $time . '&sign=' . $sign;
         $data =  $client->get($curl);
        return $data;
    }



    public function setUrl($url)
    {
        if($this->url == null){
            $this->url = $url;
        }

        return $this->url;
    }

}

















