<?php

/*
 *  @desc:   房间信息
 *  @author: xxm
 *  @email:  237886849@qq.com
 *  @note:   所有文件命名以小写，所有子类名以小写
 *		     
 */

class orderaddredistask extends basicredistask
{
    public function __construct()
    {
        parent::__construct();
        basicfields::orderadd_fislds($this);
        //
//			$this->set_redis_name('redis_room');
//			$this->set_redis_keys_info('room_info:','room_id');
//			$this->set_redis_database_model(1,false);


    }

    protected function build_redis_keys(basicredis $redis, basicmodel $model)
    {

//
        $order_id = $model->get('order_id', 0);
        $player_id = $model->get('player_id', 0);
//            $goods_id = $model->get('goods_id',0);

        return 'club_room:' . $order_id . ':' . $player_id;
    }


}

?>