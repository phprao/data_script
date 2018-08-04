<?php
set_time_limit(0);


class PlayerRedis
{

    public static $redis = null;
    public static $prefix = 'user_info:';
    public static $redis_user_info = 
		[
	        'host' => '10.66.185.63',
			'port' => '6379',
			'password' => 'crs-dodxfhcz:Skjhh3432skjd686',
			'select' => 0,
			'timeout' => 0,
			'expire' => 60,
			'persistent' => false,
			'prefix' => '',
	    ];

    public static function getDb($playerId)
    {
        $db = $playerId % 10;
        config(self::$user_config . '.select', $db);
        $index = $playerId % 1000;
        $keys = floor($index / 10);
        self::$prefix = 'user_info:' . $keys . ':';
    }

    public function __construct($i)
    {
        $options = self::$redis_user_info;
        $options['select'] = $i ;

        if (!extension_loaded('redis')) {
            throw new \BadFunctionCallException('not support: redis');
        }

        if (empty($options)){
            throw new \BadFunctionCallException('not config: redis');
        }

        self::$redis = new \Redis;
        if ($options['persistent']) {
            self::$redis->pconnect($options['host'], $options['port'], $options['timeout'], 'persistent_id_' . $options['select']);
        } else {
            self::$redis->connect($options['host'], $options['port'], $options['timeout']);
        }

        if ('' != $options['password']) {
            self::$redis->auth($options['password']);
        }

        if (0 != $options['select']) {
            self::$redis->select($options['select']);
        }
    }

    public function hset($playerId, $field, $value)
    {
        return self::$redis->hset($playerId, $field, $value);
    }

    public function hget($playerId, $field)
    {
        return self::$redis->hget($playerId, $field);
    }


    public function keys()
    {
        return self::$redis->keys(self::$prefix . '*');
    }

}

//////////////////////////////////////////////////////////////////////////////////////

for($i = 0;$i < 10 ; $i++){
	$redis = new PlayerRedis($i);
	$players = $redis->keys();
	foreach ($players as $val) {
		$redis->hset($val, 'player_coins', 30000);
		$after = $redis->hget($val, 'player_coins');
		echo $val . ' : ' . $after . PHP_EOL;
	}
}

