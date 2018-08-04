<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/26
 * Time: 18:29
 */
class urlmodel extends basicdatamodel
{
    /**
     * @var string
     * authorizedlogin    URL
     */
    public static $authorizedloginurl = "http://xycht.dcyouxi.com/dc_api_u3d/public/api/v1/login/authorized_longin?";

    /**
     * @var string
     * authorizedlogin 跳转的 URL 跳转到网页推广后台“推广协议”
     */
    public static $authorizedloginjumpurl = "http://xycht.dcyouxi.com/?action=login&token=";

    /**
     * 跳转到网页推广后台“首页”
     * @var string
     */
    public static $authorizedloginjumpurlindex = "http://xycht.dcyouxi.com/index1.html?action=login&token=";

    /**
     * @var string
     *
     * promoterewardinfo  URL
     *
     */
    public static $promoterewardinfourl = "http://xycht.dcyouxi.com/dc_api_u3d/public/api/v1/promoter/url_erwrima?";


}

















