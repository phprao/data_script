<?php
/**
 * 道具
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/15
 * Time: 18:30
 * @author Zhanghui
 */

class propdatatask extends basicdatatask {


    public function __construct()
    {
        parent::__construct();

        basicfields::prop_fields($this);
        $this->set_data_table_info('dc_prop', 'prop_id');
    }

    public function on_data_task(basicmysql $db, basicmodel $model = null, $param, $default)
    {

        return parent::on_data_task($db, $model, $param, $default); // TODO: Change the autogenerated stub

    }

}