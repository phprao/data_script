<?php

/*
 *  @desc:   俱乐部对象执行者
 *  @author: xxm
 *  @email:  237886849@qq.com
 *  @note:   所有文件命名以小写，所有子类名以小写
 *
 */

class goodsdatask extends basicdatatask
{
    public function __construct()
    {
        parent::__construct();
        basicfields::goodsinfo_fields($this);
        $this->set_data_table_info('dc_goods_info', 'goods_id');

    }

    public function on_data_task(basicmysql $db, basicmodel $model = null, $param, $default)
    {

        return parent::on_data_task($db, $model, $param, $default);
    }
}

?>