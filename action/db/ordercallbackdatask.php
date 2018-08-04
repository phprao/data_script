<?php

/*
 *  @desc:
 *  @author: xxm
 *  @email:  237886849@qq.com
 *  @note:   所有文件命名以小写，所有子类名以小写
 *
 */

class ordercallbackdatask extends basicdatatask
{
    public function __construct()
    {
        parent::__construct();
        basicfields::orderlog_fields($this);
        $this->set_data_table_info('dc_order_log', 'order_id');

    }

    public function on_data_task(basicmysql $db, basicmodel $model = null, $param, $default)
    {


        return parent::on_data_task($db, $model, $param, $default);
    }

//        public function update_order($db,$model,$default) {
//            if (is_null($db) || empty($this->m_where)) {
//                return $default;
//            }
//            $row = [
//                'update_time' => $model->get('update_time', 0),
//                'is_send' => $model->get('is_send', 0),
//                'pay_type' => $model->get('pay_type', 0),
//                'out_transaction_id' => $model->get('out_transaction_id', 0),
//
//            ];
//            return $db->update($this->m_table_name,$row,$this->m_where);
//        }

}
