<?php

/*
 *  @desc:   背包对象执行者
 *  @author: jf
 *  @time：  2018/3/22
 *  @note:   所有文件命名以小写，所有子类名以小写
 *
 */
class playerpropsdatatask extends basicdatatask {
    public function __construct() {
        parent::__construct();
        basicfields::playerpropsinfo_fields($this);
        $this->set_data_table_info('dc_player_propinfo', 'player_id');
    }

    public function on_data_task(basicmysql $db, basicmodel $model = null, $param, $default) {
        return parent::on_data_task($db, $model, $param, $default);
    }
}
?>