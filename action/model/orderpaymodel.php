<?php

/*
 *  @desc:   pay支付
 *  @author: xxm
 *  @email:  237886849@qq.com
 *  @note:   所有文件命名以小写，所有子类名以小写
 *
 */

class orderpaymodel extends basicmodelimpl
{

    public function order_pay($app, $order_id, $app_type = null)
    {

        if (!$app) {
            return false;
        }

        if (!intval($order_id)) {
            return false;
        }
        $order_status = $this->order_list_status($app, $order_id);
        if (!$order_status) {
            return false;
        }

        $task = new orderpaydatask();
        $task->set_action('update_fields');
        $task->append_where(array('order_id' => $order_id));
        $this->update('order_update_time', time());
        $this->update('order_is_send', 1);
        $this->update('order_pay_type', $app_type);
        $order = $app->m_server->process_database($task, $this, null, null);
        if ($order) {
            $order_list = $this->order_list($app, $order_id);
            $good_is = $order_list->get('order_goods_id', 0);
            $order_player_id = $order_list->get('order_player_id', 0);
            $order_price = $order_list->get('order_price', 0);
            $order_orderno = $order_list->get('order_orderno', 0);
            if ($good_is) {
                $goods_list = $this->goods_list($app, $good_is);
                $good_type = $goods_list->get('goods_type', 0);
            }
        }
        if ($good_type && $order_player_id && $order_id) {
            $list = $this->pay_add_user($app, $order_player_id, $good_type, $order_price);
        }
        if ($list) {
            if (!$app_type) {
                $app_type = $order_list->get('order_pay_type', 0);
            }
            $data['app_type'] = $app_type;
            $data['order_orderno'] = $order_orderno;
            $data['order_price'] = $order_price;
        }
        return $data;
    }

    public function order_list_status($app, $order_id)
    {
        $data_task = new orderpaydatask();
        $data_task->set_action('select');
        $data_task->append_where(array('order_id' => $order_id));
        $data_task->append_where(array('order_is_send' => 0));
        $order_list = $app->m_server->process_database($data_task, null, null, null);
        return $order_list;
    }

    public function order_list($app, $order_id)
    {
        $data_task = new orderpaydatask();
        $data_task->set_action('select');
        $data_task->append_where(array('order_id' => $order_id));
        $order_list = $app->m_server->process_database($data_task, null, null, null);
        return $order_list;
    }

    public function goods_list($app, $goods_id)
    {
        $data_task = new goodsdatask();
        $data_task->set_action('select');
        $data_task->append_where(array('goods_id' => $goods_id));
        $goods_list = $app->m_server->process_database($data_task, null, null, null);
        return $goods_list;
    }

    public function pay_add_user($app, $player_id, $good_type, $price)
    {
        $player_price = $this->player_info($app, $player_id);

        $redis_task = new playerredistask();
        $redis_task->set_action('hmget');
        $this->insert('player_id', $player_id);
        if ($data_redis = $app->m_server->process_redis($redis_task, $this, null, null)) {
            $redis_task->set_action('incrby_model');
            if ($good_type == 1) {
                $params = ['player_money' => $price];
            } else if ($good_type == 2) {
                $params = ['player_coins' => $price];
            } else if ($good_type == 3) {
                $params = ['player_lottery' => $price];
            }
            $result = $app->m_server->process_redis($redis_task, $data_redis, $params, null);

            $task = new dcplayerinfodatask();
            $task->set_action('update_fields');
            $task->append_where(array('player_id' => $player_id));
            if ($good_type == 1) {
                $play_price = $player_price->get('player_money', 0) + $price;
                $this->update('player_money', $play_price);
            } else if ($good_type == 2) {
                $play_price = $player_price->get('player_coins', 0) + $price;;
                $this->update('player_coins', $play_price);
            } else if ($good_type == 3) {
                $play_price = $player_price->get('player_lottery', 0) + $price;;
                $this->update(' player_lottery', $play_price);
            }
            $order = $app->m_server->process_database($task, $this, null, null);
            if ($result && $order) {
                return true;
            } else {
                return false;
            }

        } else {
            $task = new dcplayerinfodatask();
            $task->set_action('update_fields');
            $task->append_where(array('player_id' => $player_id));
            if ($good_type == 1) {
                $play_price = $player_price->get('player_money', 0) + $price;
                $this->update('player_money', $play_price);
            } else if ($good_type == 2) {
                $play_price = $player_price->get('player_coins', 0) + $price;;
                $this->update('player_coins', $play_price);
            } else if ($good_type == 3) {
                $play_price = $player_price->get('player_lottery', 0) + $price;;
                $this->update(' player_lottery', $play_price);
            }
            $order = $app->m_server->process_database($task, $this, null, null);
            if ($order) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function player_info($app, $player_id)
    {
        $data_task = new dcplayerinfodatask();
        $data_task->set_action('select');
        $data_task->append_where(array('player_id' => $player_id));
        $order_list = $app->m_server->process_database($data_task, null, null, null);
        return $order_list;
    }

}































