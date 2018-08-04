<?php

/**
 * 资金日志
 * Class playerinfologmodel
 * @author ChangHai Zhan
 */
class changemoneyinfomodel extends basicdatamodel
{
    /**
     * 钱的类型 金币
     */
    const change_money_money_type_gold = 1;

    // 钻石
    const change_money_money_type_masonry = 2;
    //代币
    const change_money_money_type_token = 3;
    /**
     * 日志类型 新用户充值赠送
     */
    const change_money_type_register_reward = 4;
    /**
     * 日志类型 微信充值
     */
    const change_money_type_pay_charge = 1;
    /**
     * 静态实例化
     * @param string $app
     * @param string $className
     * @return static active record model instance.
     */
    public static function model($app = null, $className = __CLASS__)
    {
        return new $className($app);
    }

    /**
     * 添加日志
     * @param array $params
     * @param null $app
     * @return mixed
     */
    public function add_log($params = [], $app = null)
    {
        $task = new changemoneyinfodatatask();
        //数组转变量
        extract($params);
        if (!isset($change_money_player_id)) {
            return false;
        }
        if (!isset($change_money_player_club_id)) {
            $change_money_player_club_id = 0;
        }
        if (!isset($change_money_club_id)) {
            $change_money_club_id = 0;
        }
        if (!isset($change_money_club_room_id)) {
            $change_money_club_room_id = 0;
        }
        if (!isset($change_money_club_desk_no)) {
            $change_money_club_desk_no = 0;
        }
        if (!isset($change_money_club_desk_id)) {
            $change_money_club_desk_id = 0;
        }
        if (!isset($change_money_club_room_no)) {
            $change_money_club_room_no = 0;
        }
        if (!isset($change_money_game_id)) {
            $change_money_game_id = 0;
        }
        if (!isset($change_money_room_id)) {
            $change_money_room_id = 0;
        }
        if (!isset($change_money_desk_no)) {
            $change_money_desk_no = 0;
        }
        if (!isset($change_money_type)) {
            //没有日志类型
            return false;
        }
        if (!isset($change_money_money_type)) {
            $change_money_money_type = self::change_money_money_type_gold;
        }
        if (isset($change_money_money_value) && $change_money_money_value == 0) {
            //改变值 为零
            return false;
        }
        if (!isset($change_money_begin_value)) {
            $change_money_begin_value = 0;
        }
        if (!isset($change_money_param)) {
            $change_money_param = '';
        }
        $task->set_action('insert_fields');
        $this->insert('change_money_player_id', $change_money_player_id);
        $this->insert('change_money_player_club_id', $change_money_player_club_id);
        $this->insert('change_money_club_id', $change_money_club_id);
        $this->insert('change_money_club_room_id', $change_money_club_room_id);
        $this->insert('change_money_club_desk_no', $change_money_club_desk_no);
        $this->insert('change_money_club_desk_id',$change_money_club_desk_id);
        $this->insert('change_money_club_room_no', $change_money_club_room_no);
        $this->insert('change_money_game_id', $change_money_game_id);
        $this->insert('change_money_room_id', $change_money_room_id);
        $this->insert('change_money_desk_no', $change_money_desk_no);
        $this->insert('change_money_type', $change_money_type);
        $this->insert('change_money_tax', 0);
        $this->insert('change_money_money_type', $change_money_money_type);
        $this->insert('change_money_money_value', $change_money_money_value);
        $this->insert('change_money_begin_value', $change_money_begin_value);
        $this->insert('change_money_after_value', $change_money_begin_value + $change_money_money_value);
        $this->insert('change_money_time', time());
        $this->insert('change_money_param', $change_money_param);
        if (isset($this->m_app->m_server) && $this->m_app->m_server) {
            $app = $this->m_app;
        }
        return $app->m_server->process_database($task, $this, null, null);
    }
}