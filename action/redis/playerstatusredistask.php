<?php

class playerstatusredistask extends basicredistask
{
    public function __construct()
    {
        parent::__construct();
        basicfields::club_desk_info_fields($this);
        $this->set_fields_default('club_desk_player_id', 'club_desk_player_id', 0, 'int');
        //
        $this->set_redis_keys_info('user_status:', 'club_desk_player_id');
        $this->set_redis_database_model(2, true);
    }

    //protected function build_redis_keys(basicredis $redis, basicmodel $model) {
    //}
}

?>