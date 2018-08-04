<?php

/*
 *  @desc:   俱乐部房间信息
 *  @author: xxm
 *  @email:  237886849@qq.com
 *  @note:   所有文件命名以小写，所有子类名以小写
 *		     
 */

class clubroomlistredistask extends basicredistask
{
    public function __construct()
    {
        parent::__construct();
        basicfields::club_room_info_fields($this);
        $this->set_fields_default('club_room_online', 'club_room_online', 0, 'int');
        $this->set_fields_default('club_room_time', 'club_room_time', 0, 'int');
        //
        $this->set_redis_name('redis_room');
        $this->set_redis_keys_info('club_room:', 'club_room_id');
        $this->set_redis_database_model(1, false);
    }


    protected function build_redis_keys(basicredis $redis, basicmodel $model)
    {
        if (is_null($redis) || is_null($model)) {
            return "";
        }

        $club_room_id = $model->get('club_room_id', 0);
        $club_room_club_id = $model->get('club_room_club_id', 0);
        return 'club_room:' . $club_room_club_id . ':' . $club_room_id;
    }
}

?>