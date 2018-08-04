<?php
/**
 * action 错误信息类
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/17
 * Time: 10:54
 * @author Zhanghui
 */

class actionerror {

    /**
     * redis获取玩家信息失败 错误提示
     * @var string
     */
    public static $basicredistask_get_player_info_error = '登陆信息过期，请重新登陆！';

    /**
     * redis更新玩家信息失败 错误提示
     * @var string
     */
    public static $basicredistask_update_player_info_error = '更新玩家信息失败！';

    /**
     * SQL异常 错误提示
     * @var string
     */
    public static $basicmysql_exception_error = '系统异常，请稍后再试！';

    /**
     * 库表查询 获取玩家信息 错误提示
     * @var string
     */
    public static $basicmysql_get_player_error = '不存在的玩家！';

    /**
     * 库表查询 获取道具信息 错误提示
     * @var string
     */
    public static $basicmysql_get_prop_error = '不存在的道具！';

    /**
     * 请求参数 错误提示
     * @var string
     */
    public static $basicaction_request_param_error = '请求参数非法';

}