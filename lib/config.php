<?php

date_default_timezone_set('Asia/Shanghai');
ini_set('error_reporting', 'E_ALL ^ E_NOTICE');

class Config {
	/* 数据库连接参数 */
	public static $mysql_config = array(
		'host'     => '192.168.1.210',
		'port'     => 3306,
		'username' => 'root',
		'password' => '123456',
		'dbname'   => 'dc_u3d_king',
		'charset'  => 'utf8',
	);
	/* Data redis 连接参数 */
	public static $mod = 10;
	public static $redis_user_info = array(
		'host' => '192.168.1.210', 
		'port' => 55101, 
		'pass' => 'zyl12345!QWEASD901',
		'db'   => 0
    );

    public static $redis_admin_config = array(
		'host' => '192.168.1.210', 
		'port' => 55101, 
		'pass' => 'zyl12345!QWEASD901',
		'db'   => 0
    );

    public static $push_keys = 'bs1029384756';
    
	public static $send_bonus_config = [
        // 微信发红包地址
        'send_bonus_url'      => 'http://www.dachuanyx.com/dc_service/sendwxbonus.php',
        // 微信发红包回调地址
        'send_bonus_callback' => 'http://xycht.dcyouxi.com/dc_api_u3d/public/api/v1/withdraw/notify_action',
        // 微信发红包名称
        'send_bonus_name'     => 'king',
        // 微信发红包应用场景
        'send_bonus_scanid'   => 'match',
        // 描述
        'send_bonus_remark'   => [
            "act_name"  => "幸运王国红包",
            "remark"    => "幸运王国红包",
            "send_name" => "幸运王国红包",
            "wishing"   => "恭喜发财，大吉大利！"
        ]
    ];

}
