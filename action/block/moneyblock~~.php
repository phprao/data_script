<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/31
 * Time: 11:41
 * @author ChangHai Zhan
 */
class moneyblock
{
    /**
     * @var
     */
    public $app;

    /**
     * loginblock constructor.
     * @param $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * 静态实例化
     * @param string $app
     * @param string $className
     * @return static active record model instance.
     */
    public static function block($app = null, $className = __CLASS__)
    {
        return new $className($app);
    }

    /**
     * moneyblock::block($app)->update_player_money($player_id, 1000,changemoneyinfomodel::change_money_type_register_reward)
     * 更新用户钱
     * @param $player_id
     * @param $money
     * @param $log_type
     * @param int $money_type
     * @return array
     */
    public function update_player_money($player_id, $money, $log_type, $money_type = changemoneyinfomodel::change_money_money_type_gold, basicmodelimpl $order_model = null)
    {
        /*$return = [
            'status ' => 0,
            'msg' => 'ok',
        ];*/
        $return = array();
        $return['status'] = 0;
        $return['msg'] = 'ok';
        if (!isset(playerinfomodel::$money_type[$money_type])) {
            $return['status'] = 1;
            $return['msg'] = '日志类型错误';
            return $return;
        }
        $money_field = playerinfomodel::$money_type[$money_type];
        //事务开始 更新数据库 写入日志 更新redis
        $M = basicglobaltransaction::get_instance()->get_strans();//new basictransactiontask();
        $this->app->m_server->process_database($M, null, null, null);
        //redis 不存在 加载数据库数据库
        if ($redis_model = playerredisblock::block($this->app)->get_player($player_id)) {
            $bef_money = $redis_model->get($money_field, 0);
        } elseif ($player_model = playerinfomodel::model($this->app)->get_player_info_by_player_id($player_id)) {
            $bef_money = $player_model->get($money_field, 0);
        } else {
            $M->rollback();
            $return['status'] = 2;
            $return['msg'] = '用户信息异常';
            return $return;
        }
        //更新用户数据库字段
        if (!playerinfomodel::model($this->app)->update_money($player_id, $bef_money + $money, $money_type)) {
            $M->rollback();
            $return['status'] = 3;
            $return['msg'] = '数据库更新用户信息失败';
            return $return;
        }
        //写入日志
        if (!$this->add_log_by_player_id($player_id, $bef_money, $money, $log_type, $money_type,$order_model)) {
            $M->rollback();
            $return['status'] = 4;
            $return['msg'] = '数据库添加日志信息失败';
            return $return;
        }
        //更新redis
        if ($redis_model && !playerredisblock::block($this->app)->incr_player($player_id, $money_field, $money)) {
            $M->rollback();
            $return['status'] = 5;
            $return['msg'] = 'redis 添加金额失败';
            return $return;
        }
        $M->commit();
        //发回数据
        $return['data'] = [
            'player_id' => $player_id,
            'field' => $money_field,
            'bef_money' => $bef_money,
            'money' => $money,
            'aft_money' => $bef_money + $money,
            'log_type' => $log_type,
            'money_type' => $money_type,
        ];
        return $return;
    }

    /**
     * 添加日志
     * @param $player_id
     * @param $bef_money
     * @param $money
     * @param $type
     * @param int $money_type
     * @return bool
     */
    protected function add_log_by_player_id($player_id, $bef_money, $money, $type, $money_type = changemoneyinfomodel::change_money_money_type_gold,basicmodelimpl $order_model = null)
    {
        $price = 0;
        $order_id = 0;
        if(!is_null($order_model)) {
            $price = $order_model->get('order_price',0);
            $order_id = $order_model->get('order_id',0);
        }

        $money_info = json_encode(array('price_value'=>$price,'order_id'=>$order_id));
        $params = [
            'change_money_player_id' => $player_id,
            'change_money_begin_value' => $bef_money,
            'change_money_money_value' => $money,
            'change_money_type' => $type,
            'change_money_money_type' => $money_type,
            'change_money_param' =>$money_info,
        ];
        return changemoneyinfomodel::model($this->app)->add_log($params);
    }
}