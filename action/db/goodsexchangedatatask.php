<?php

class goodsexchangedatatask extends basicdatatask
{
    public function __construct()
    {
        parent::__construct();
        basicfields::playergoodsexchange_fileds($this);
        $this->set_data_table_info('dc_goods_exchange', 'goods_exchange_id');

    }

    public function on_data_task(basicmysql $db, basicmodel $model = null, $param, $default)
    {

        return parent::on_data_task($db, $model, $param, $default);
    }
}

?>