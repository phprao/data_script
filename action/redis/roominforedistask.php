<?php

/*
 *  @desc:   房间信息
 *  @author: xxm
 *  @email:  237886849@qq.com
 *  @note:   所有文件命名以小写，所有子类名以小写
 *		     
 */

class roominforedistask extends basicredistask
{
    public function __construct()
    {
        parent::__construct();
        basicfields::roominfo_fields($this);
        //
        $this->set_redis_name('redis_room');
        $this->set_redis_keys_info('room_info:', 'room_id');
        $this->set_redis_database_model(1, false);
    }

}

?>