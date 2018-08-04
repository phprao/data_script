<?php

/*
 *  @desc:   俱乐桌子信息
 *  @author: xxm
 *  @email:  237886849@qq.com
 *  @note:   所有文件命名以小写，所有子类名以小写
 *		     
 */

class clubdeskinforedistask extends basicredistask
{
    public function __construct()
    {
        parent::__construct();
        basicfields::club_desk_info_fields($this);
        basicfields::club_desk_info_redis_fields($this);
        //
        $this->set_redis_name('redis_room');
        $this->set_redis_keys_info('club_desk:', 'club_desk_id');
        $this->set_redis_database_model(1, false);
    }

    protected function build_redis_keys(basicredis $redis, basicmodel $model)
    {
        if (is_null($redis) || is_null($model)) {
            return "";
        }

        $club_room_id = $model->get('club_desk_club_room_id', 0);
        $club_room_club_id = $model->get('club_desk_club_id', 0);
        $club_desk_no = $model->get('club_desk_club_room_desk_no', 0);
        return 'club_desk:' . $club_room_club_id . ':' . $club_room_id . ':' . $club_desk_no;
    }
}

?>