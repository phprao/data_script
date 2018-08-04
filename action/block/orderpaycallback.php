<?php

/*
 *  @desc:   pay支付回调
 *  @author: xxm
 *  @email:  237886849@qq.com
 *  @note:   所有文件命名以小写，所有子类名以小写
 *
 */

class orderpaycallback extends basicsingleton {
    const PAY_CHANNEL_WEIXIN = 1;
    const PAY_CHANNEL_ALIPAY = 2;
    const PAY_CHANNEL_APPLE = 3;
    const PAY_CHANNEL_WEBPAY = 4;
    public $m_desc = '';

    public function pay_order(basicdi $app,$order_no,$pay_data,$order_pay_channel) {
        $trans = basicglobaltransaction::get_instance()->get_strans();
        $app->m_server->process_database($trans,null,null,null);
        $ret = $this->process_order($app,$order_no,0,$order_pay_channel);
        if($ret) {
            $trans->commit();
        }else {
            $trans->rollback();
        }
        return $ret;
    }

    protected function process_order(basicdi $app,$order_no,$order_price,$order_pay_channel) {
        $order_info = $this->get_order_info($app,$order_no);
        if(is_null($order_info)) {
            $this->printf_info($order_no .  '(订单信息已经支付过了)');
            return true;//已经支付过了,或回调错误
        }

        $order_id = $order_info->get('order_id',0);
        $goods_id = $order_info->get('order_goods_id',0);
        $player_id = $order_info->get('order_player_id',0);
        $pay_channel = $order_info->get('order_pay_channel',0);
        if(!$this->check_order_valid($order_info,$order_no,$order_pay_channel)) {
            return false;
        }

        $goods_info = $this->get_goods_info($app,$goods_id);
        if(is_null($goods_info)) {
            $this->printf_info($goods_id .  '(订单商品不存在)');
            return false;
        }

        if(!$this->update_order_status($app,$order_id)) {
            $this->printf_info($order_id .  '(订单更新状态失败)');
            return false;
        }

        if(0 == $order_price) {
            $order_price = $order_info->get('order_get_price',0);
        }

        $result = $this->add_player_money($app,$order_price,$order_info);
        if($result['code'] > 0){
            $this->printf_info($order_id .  '(修改用户信息)'.$result['code']);
            return false;
        }

        if(!$this->record_pay_info($app,$order_info,$result)) {
            $this->printf_info($order_id .  '记录支付失败');
            return false;
        }

        /*if(!$this->add_pay_money_queue($app,$order_info,$result)) {
            $this->printf_info($order_id .  '记录任务失败');
            return false;
        }
        */
        $this->notify_client($app,$player_id);
        return true;
    }

    //
    protected function notify_client(basicdi $app,$player_id) {
        $push_info = $app->m_config->get('push_info',null);
        if(is_null($push_info)) return false;
        $push_host = $push_info['push_host'];
        $push_port = $push_info['push_tcp'];
        $push_psw = $push_info['push_keys'];
        $socket = new socket($push_host, $push_port, $push_psw);
        $socket_data['user_id'] = $player_id;
        $socket_data['action_id'] = 1;
        $socket_data['task_type'] = 1;
        $socket_data['msg'] = '';
        $socket->set_buf($socket_data);
        $socket->send_buf();
        unset($socket_data);
        $socket->uninit_socket();

        return true;
    }

    protected function add_pay_money_queue(basicdi $app,$order_info,$data) {
        //record_recordmoneydatahepler::get_instance()->
        return true;
    }

    //check order info
    protected function check_order_valid($order_info,$order_no,$order_pay_channel) {
        $order_id = $order_info->get('order_id',0);
        $goods_id = $order_info->get('order_goods_id',0);
        $player_id = $order_info->get('order_player_id',0);
        $pay_channel = $order_info->get('order_pay_channel',0);
        if(0 == $order_id || 0 == $goods_id || 0 == $player_id || 0 == $pay_channel ||$pay_channel != $order_pay_channel) {
            $this->printf_info($order_no . ' '.$goods_id.' '.$player_id. ' '.$order_id. ' '.$pay_channel. '(订单不合法)');
            return false;
        }
        return true;
    }

    //record pay info
    protected function record_pay_info(basicdi $app,$order_info,$data) {
        $recore_before_money = $data['before_money'];
        $recore_after_money = $data['after_money'];
        $player_id = $order_info->get('order_player_id',0);
        $pay_channel = $order_info->get('order_pay_channel',0);
        $order_get_type = $order_info->get('order_get_type',0);
        $order_get_price = $order_info->get('order_get_price',0);
        $order_price = $order_info->get('order_price',0);
        $order_id = $order_info->get('order_id',0);

        $model = new orderaddmodel();
        $model->insert('recore_player_id', $player_id);
        $model->insert('recore_type', $pay_channel);
        //$model->insert('recore_state', $order_get_type);
        $model->insert('recore_price', $order_price);
        $model->insert('recore_get_type', $order_get_type);
        $model->insert('recore_get_price', $order_get_price);
        $model->insert('recore_before_money', $recore_before_money);
        $model->insert('recore_after_money', $recore_after_money);
        $model->insert('recore_create_time', time());
        $model->insert('recore_order_id', $order_id);
        $desk_data_task = new payrecorddatask();
        $desk_data_task->set_action("insert");
        $order_id = $app->m_server->process_database($desk_data_task, $model, null, null);
        if ($order_id) {
            return true;
        }else{
            return false;
        }

    }

    //give player money
    protected function add_player_money(basicdi $app,$get_price,$order_info) {
        $result['code'] = 100;
        $result['before_money'] = 0;
        $result['after_money'] = 0;
        $result['player_info'] = null;

        if(is_null($order_info)) return $result;

        $order_get_type = $order_info->get('order_get_type',0);
        $order_get_price = $order_info->get('order_get_price',0);
        $order_player_id = $order_info->get('order_player_id',0);
        $order_price = $order_info->get('order_price',0);
        //金币
        if ($order_get_type == 2) {
           
            $data_value = moneyblock::block($app)->update_player_money($order_player_id, $order_get_price, changemoneyinfomodel::change_money_type_pay_charge,changemoneyinfomodel::change_money_money_type_gold,$order_info);
            if (!$data_value || !isset($data_value['status']) || $data_value['status'] != 0) {
                $result['code'] = 1;
                return $result;
            }
            $result['before_money'] = $data_value['data']['bef_money'];
            $result['after_money'] = $data_value['data']['aft_money'];
            $result['code'] = 0;
        }else if($order_get_type == 5) {
            //添加砖石
            $data_value = moneyblock::block($app)->update_player_money($order_player_id, $order_get_price, changemoneyinfomodel::change_money_money_type_masonry,changemoneyinfomodel::change_money_money_type_masonry,$order_info);

            if (!$data_value || !isset($data_value['status']) || $data_value['status'] != 0) {
                $result['code'] = 1;
                return $result;
            }
            $result['before_money'] = $data_value['data']['bef_money'];
            $result['after_money'] = $data_value['data']['aft_money'];
            $result['code'] = 0;


        } else if($order_get_type == 4) {
            //添加代币
             $data_value = moneyblock::block($app)->update_player_money($order_player_id, $order_get_price, changemoneyinfomodel::change_money_money_type_token,changemoneyinfomodel::change_money_money_type_token,$order_info);
             if (!$data_value || !isset($data_value['status']) || $data_value['status'] != 0) {
                $result['code'] = 1;
                return $result;
             }
             $result['before_money'] = $data_value['data']['bef_money'];
             $result['after_money'] = $data_value['data']['aft_money'];
             $result['code'] = 0;
        }
        else{
            $result['code'] = 101;
        }
        //if($order_get_price != $get_price) {
        //    return $result;
        // }

        //add player money
        
        return $result;
    }

    //get goods info
    public function get_goods_info(basicdi $app,$goods_id) {
        $data_task = new goodsdatask();
        $data_task->set_action('select');
        $data_task->append_where(array('goods_id' => $goods_id));
        $goods_list = $app->m_server->process_database($data_task, null, null, null);
        return $goods_list;
    }

    //get order info
    public function get_order_info(basicdi $app,$order_no) {
        $data_task = new orderpaydatask();
        $data_task->set_action('select');
        $data_task->append_where(array('order_orderno' => $order_no));
        $data_task->append_where(array('order_is_send' => 0));
        $order_info = $app->m_server->process_database($data_task, null, null, null);
        return $order_info;
    }

    //get order info
    public function get_order_info_by_id(basicdi $app,$order_id) {
        $data_task = new orderpaydatask();
        $data_task->set_action('select');
        $data_task->append_where(array('order_id' => $order_id));
        $data_task->append_where(array('order_is_send' => 0));
        $order_info = $app->m_server->process_database($data_task, null, null, null);
        return $order_info;
    }

    //update order status
    protected function update_order_status(basicdi $app,$order_id) {
        $task = new orderpaydatask();
        $task->set_action('update_fields');
        $task->append_where(array('order_id' => $order_id));
        $moder = new paymodel();
        $moder->update('order_update_time', time());
        $moder->update('order_is_send', 1);
        $order = $app->m_server->process_database($task, $moder, null, null);
        return $order;
    }

    //print error info
    protected function printf_info($msg) {
        BASIC_LOG_WARNING('orderpaycallback', '%s', $msg);
        $this->m_desc = $msg;
    }

    /***
     * @param $app 数据库 必填
     * @param $order_id  订单id 必填
     * @param null $app_type
     * @return bool
     */
    public static function order_pay($app, $order_id, $app_type = null)
    {

        if (!$app) {
            return false;
        }

        if (!intval($order_id)) {
            return false;
        }
        $order_status = orderpaycallback::order_list_status($app, $order_id);
        if (!$order_status) {
            BASIC_LOG_INFO('order', '%s', $order_id . '_' . $app_type . '(订单更新状态失败)');
            return false;
        }
        $task = new orderpaydatask();
        $task->set_action('update_fields');
        $task->append_where(array('order_id' => $order_id));
        $moder = new paymodel();
        $moder->update('order_update_time', time());
        $moder->update('order_is_send', 1);
        $moder->update('order_pay_type', $app_type);
        $order = $app->m_server->process_database($task, $moder, null, null);

        if ($order) {
            BASIC_LOG_INFO('order', '%s', $order_id . '_' . $app_type . '(更新订单状态成功)');
            $order_list = orderpaycallback::order_list($app, $order_id);
            $good_is = $order_list->get('order_goods_id', 0);
            $order_player_id = $order_list->get('order_player_id', 0);
            $order_price = $order_list->get('order_price', 0);
            $order_get_price = $order_list->get('order_get_price', 0);
            $order_orderno = $order_list->get('order_orderno', 0);
            $order_pay_type = $order_list->get('order_pay_type', 0);
            if ($good_is) {
                $goods_list = orderpaycallback::goods_list($app, $good_is);
                $good_type = $goods_list->get('goods_type', 0);
            }
        }
        if ($good_type && $order_player_id && $order_id) {
            $list = orderpaycallback::pay_add_user($app, $order_player_id, $good_type, $order_get_price);
            if ($list) {
                BASIC_LOG_INFO('order', '%s', $order_player_id . '_' . $good_type . '_' . $order_get_price . '(加钱成功)');
                orderpaycallback::add_pay_record($app, $order_player_id, $order_pay_type, $good_type, $order_price, $order_get_price,$list['recore_before_money'],$list['recore_after_money']);
            }

        }
        if ($list) {
            if (!$app_type) {
                $app_type = $order_list->get('order_pay_type', 0);
            }
            $data['app_type'] = $app_type;
            $data['order_orderno'] = $order_orderno;
            $data['order_price'] = $order_get_price;
        }
        return $data;
    }

    public static function order_list_status($app, $order_id)
    {
        $data_task = new orderpaydatask();
        $data_task->set_action('select');
        $data_task->append_where(array('order_id' => $order_id));
        $data_task->append_where(array('order_is_send' => 0));
        $order_list = $app->m_server->process_database($data_task, null, null, null);
        return $order_list;
    }

    public static function order_list($app, $order_id)
    {
        $data_task = new orderpaydatask();
        $data_task->set_action('select');
        $data_task->append_where(array('order_id' => $order_id));
        $order_list = $app->m_server->process_database($data_task, null, null, null);
        return $order_list;
    }

    public static function goods_list($app, $goods_id)
    {
        $data_task = new goodsdatask();
        $data_task->set_action('select');
        $data_task->append_where(array('goods_id' => $goods_id));
        $goods_list = $app->m_server->process_database($data_task, null, null, null);
        return $goods_list;
    }

    public static function pay_add_user($app, $player_id, $good_type, $price)
    {
        $player_price = orderpaycallback::player_info($app, $player_id);
        $redis_task = new playerredistask();
        $redis_task->set_action('hmget');
        $moder = new paymodel();
        $moder->insert('player_id', $player_id);
        if ($data_redis = $app->m_server->process_redis($redis_task, $moder, null, null)) {
            $redis_task->set_action('incrby_model');
            if ($good_type == 1) {
                $params = ['player_money' => $price];
            } else if ($good_type == 2) {
                $params = ['player_coins' => $price];
            } else if ($good_type == 3) {
                $params = ['player_lottery' => $price];
            }
            $result = $app->m_server->process_redis($redis_task, $data_redis, $params, null);
            if ($result) {
                BASIC_LOG_INFO('order_redis', '%s', $player_id . '_' . $good_type . '_' . $price . '(更新reids加钱成功)');
            }

            $task = new dcplayerinfodatask();
            $task->set_action('update_fields');

            $task->append_where(array('player_id' => $player_id));
            if ($good_type == 1) {
                $play_price = $player_price->get('player_money', 0) + $price;
                $moder->update('player_money', $play_price);
            } else if ($good_type == 2) {
                $play_price = $player_price->get('player_coins', 0) + $price;;
                $moder->update('player_coins', $play_price);
            } else if ($good_type == 3) {
                $play_price = $player_price->get('player_lottery', 0) + $price;;
                $moder->update(' player_lottery', $play_price);
            }
            $order = $app->m_server->process_database($task, $moder, null, null);
            if ($order) {
                BASIC_LOG_INFO('order_mysql', '%s', $player_id . '_' . $good_type . '_' . $price . '(更新数据库R加钱成功)');
            }
            if ($result && $order) {

                $data = array(
                    'recore_before_money'=>$player_price->get('player_lottery', 0),
                    'recore_after_money'=>$play_price,
                );
                return $data;

            } else {
                return false;
            }
        } else {
            $task = new dcplayerinfodatask();
            $task->set_action('update_fields');
            $task->append_where(array('player_id' => $player_id));
            if ($good_type == 1) {
                $play_price = $player_price->get('player_money', 0) + $price;
                $moder->update('player_money', $play_price);
            } else if ($good_type == 2) {
                $play_price = $player_price->get('player_coins', 0) + $price;;
                $moder->update('player_coins', $play_price);
            } else if ($good_type == 3) {
                $play_price = $player_price->get('player_lottery', 0) + $price;;
                $moder->update(' player_lottery', $play_price);
            }
            $order = $app->m_server->process_database($task, $moder, null, null);
            if ($order) {
                BASIC_LOG_INFO('order_mysql', '%s', $player_id . '_' . $good_type . '_' . $price . '(更新数据库加钱成功)');

                $data = array(
                    'recore_before_money'=>$player_price->get('player_lottery', 0),
                    'recore_after_money'=>$play_price,
                );
                return $data;
            } else {
                return false;
            }
        }
    }

    public static function player_info($app, $player_id)
    {
        $data_task = new dcplayerinfodatask();
        $data_task->set_action('select');
        $data_task->append_where(array('player_id' => $player_id));
        $order_list = $app->m_server->process_database($data_task, null, null, null);
        return $order_list;
    }

    /**
     * @param $app
     * @param $recore_player_id
     * @param $recore_type
     * @param $recore_state
     * @param $recore_price
     * @param $recore_get_price
     * @param $recore_before_money
     * @param $recore_after_money
     * @return bool
     */
    public static function add_pay_record($app, $recore_player_id, $recore_type, $recore_state, $recore_price, $recore_get_price,$recore_before_money,$recore_after_money)
    {
        $model = new orderaddmodel();
        $model->insert('recore_player_id', $recore_player_id);
        $model->insert('recore_type', $recore_type);
        $model->insert('recore_state', $recore_state);
        $model->insert('recore_price', $recore_price);
        $model->insert('recore_get_price', $recore_get_price);
        $model->insert('recore_before_money', $recore_before_money);
        $model->insert('recore_after_money', $recore_after_money);
        $model->insert('recore_create_time', time());
        $desk_data_task = new payrecorddatask();
        $desk_data_task->set_action("insert");
        $order_id = $app->m_server->process_database($desk_data_task, $model, null, null);
        if ($order_id) {
            return true;
        }else{
            return false;
        }

    }

}
































