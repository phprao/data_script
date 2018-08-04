<?php


/*
 *  @desc:   p生成订单
 *  @author: xxm
 *  @email:  237886849@qq.com
 *  @note:   所有文件命名以小写，所有子类名以小写
 *
 */

class addpayorder
{

    public static function add_order($app, $player_id, $goods_id, $pay_type, $pay_channel, $club_id=0,$extension = null)
    {

        if (!intval($player_id)) {
            return false;
        }
        if (!intval($goods_id)) {
            return false;
        }
        if (!intval($pay_type)) {
            return false;
        }
        if (!intval($pay_channel)) {
            return false;
        }
        $goods_type = 0;
        $goodshow = addpayorder::goodshow($app, $goods_id);
        if ($goodshow) {
            $goods_price = $goodshow->get('goods_price', 0);
            $goods_get_price = $goodshow->get('goods_get_price', 0);
            $goods_type = $goodshow->get('goods_type', 0);
            $goods_club_id = $goodshow->get('goods_club_id', 0);
            $club_id = $club_id ? $club_id : $goods_club_id;
        } else {
            return false;
        }
        if($goods_type == changemoneyinfomodel::change_money_money_type_token && $club_id == 0) {
            BASIC_LOG_ERROR('system','addpayorder','%s','代币购买，商品club_id不存在！');
            return false;
        }
        $order_orderno = addpayorder::createorderid($player_id);
        $model = new orderaddmodel();
        $model->insert('order_player_id', $player_id);
        $model->insert('order_goods_id', $goods_id);
        $model->insert('order_pay_type', $pay_type);
        $model->insert('order_price', $goods_price);
        $model->insert('order_get_price', $goods_get_price);
        $model->insert('order_get_type', $goods_type);
        $model->insert('order_orderno', $order_orderno);
        $model->insert('order_create_time', time());
        $model->insert('order_is_send', 0);
        $model->insert('order_extension', $extension);
        $model->insert('order_pay_channel', $pay_channel);
        $model->insert('order_club_id', $club_id);
        $desk_data_task = new orderadddatask();
        $desk_data_task->set_action("insert");
        $order_id = $app->m_server->process_database($desk_data_task, $model, null, null);
        if ($order_id) {
            $data['order_id'] = $order_id;
            $data['goods_price'] = $goods_price;
            $data['goods_get_price'] = $goods_get_price;
            $data['order_orderno'] = $order_orderno;

            return $data;
        }else{
            return false;
        }

    }

    public static function goodshow($app, $goodsid)
    {
        $data_task = new goodsdatask();
        $data_task->set_action('select');
        $data_task->append_where(array('goods_id' => $goodsid));
        $game_list = $app->m_server->process_database($data_task, null, null, null);
        return $game_list;
    }

    //生成随机24位订单号
    protected static function createorderid($player_id,$length = 24)
    {
        $seed = md5(microtime());
        $pwd = $player_id;
        for ($i = 0; $i < $length; $i++) {
            $pwd .= $seed{mt_rand(0, 31)};
        }
        $hash = md5($pwd);
        return substr($hash, 0, $length);
    }


}