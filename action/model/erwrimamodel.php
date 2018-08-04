<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/26
 * Time: 18:29
 */
class erwrimamodel extends basicdatamodel
{
    public $url = null;
    /**
     * @param $player
     * @param null $uer
     * @param null $extension
     * @return mixed
     * curl H5 接口
     */
    public function erwrim($player, $url = null, $extension = null)
    {
        $client = new httpclient();
        $player_id = $player;
        $time = '2018';
        $key = 'dcyouxi';
        $sign = md5($player . $time . $key);
        if (!$url) {
            $url = "http://127.0.0.1/dc_php/sc_kfa/dc_u3d/dc_u3dapi_admin/public/api/v1/promoter/url_rwrima?";
        }
         $curl = $url . "player_id=" . $player_id . '&time=' . $time . '&sign=' . $sign;
         $data =  $client->get($curl);
        return $data;
    }

    /**
     * @param $player
     * @param null $url
     * @param null $extension
     * @return mixed
     * 生成链接 H5 接口
     */
    public function thelink($player,$url=null,$extension=null){

        $client = new httpclient();
        $player_id = $player;
        $time = '2018';
        $key = 'dcyouxi';
        $sign = md5($player . $time . $key);
        if (!$url) {
            $url = "http://127.0.0.1/dc_php/sc_kfa/dc_u3d/dc_u3dapi_admin/public/api/v1/promoter/the_link?";
        }
        $curl = $url . "player_id=" . $player_id . '&time=' . $time . '&sign=' . $sign;
       // $data = $client->get($curl);
        return $curl;

    }

    public function setUrl($url)
    {
        if($this->url == null){
            $this->url = $url;
        }

        return $this->url;
    }

}

















