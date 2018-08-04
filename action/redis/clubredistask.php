<?php

/*
 *  @desc:   俱乐信息
 *  @author: xxm
 *  @email:  237886849@qq.com
 *  @note:   所有文件命名以小写，所有子类名以小写
 *		     
 */

class clubredistask extends basicredistask
{
    public function __construct()
    {
        parent::__construct();
        basicfields::clubinfo_fields($this);
        //
        $this->set_redis_name('redis_room');
        $this->set_redis_keys_info('club_info:', 'club_id');
        //$this->set_redis_database_model(1,false);
        $this->set_redis_database_model(2, false);
    }

}

?>