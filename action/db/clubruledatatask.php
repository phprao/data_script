<?php

/*
 *  @desc:   俱乐部对象执行者
 *  @author: xxm
 *  @email:  237886849@qq.com
 *  @note:   所有文件命名以小写，所有子类名以小写
 *
 */

class clubruledatatask extends basicdatatask
{
    public function __construct()
    {
        parent::__construct();
        basicfields::club_rule_fileds($this);
        $this->set_data_table_info('dc_club_rule', 'club_room_rule_id');
    }
}

?>