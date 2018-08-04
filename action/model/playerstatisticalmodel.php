<?php

/**
 * 统计表
 * Class playerstatisticalmodel
 * @author ChangHai Zhan
 */
class playerstatisticalmodel extends basicdatamodel
{
    /**
     * 统计类型 钱
     */
    const statistical_type_money = 1;
    /**
     * 推广奖励状态 没满足条件
     */
    const statistics_award_money_status_not = 0;
    /**
     *推广奖励状态 以满足条件 计算出金额 金额计入账户
     */
    const statistics_award_money_status_enter = 1;

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
     * 统计玩家推广的玩家消耗
     * @param $player_id
     * @param $condition
     * @param null $app
     * @return mixed
     */
    public function get_player_promoters_statistics($player_id, $condition = null, $app = null)
    {
        if (isset($this->m_app->m_server) && $this->m_app->m_server) {
            $app = $this->m_app;
        }
        $task = new playerstatisticaldatatask();
        if (is_array($player_id)) {
            $task->set_action('select_list_in');
            $task->append_where_list(['statistical_player_id' => $player_id], basicdatatask::$WHERE_TYPE_IN);
//            $task->append_where_list(['statistical_type' => self::statistical_type_money], basicdatatask::$WHERE_TYPE_EQUAL);
            if (isset($condition->statistics_award_money_status)) {
                $task->append_where_list(['statistical_award_money_status' => $condition->statistics_award_money_status], basicdatatask::$WHERE_TYPE_EQUAL);
            }
            return $app->m_server->process_database($task, null, null, []);
        } else {
            $task->set_action('select');
//            $task->append_where(['statistical_type' => self::statistical_type_money]);
            $task->append_where(['statistical_player_id' => $player_id]);
            if (isset($condition->statistics_award_money_status)) {
                $task->append_where(['statistical_award_money_status' => $condition->statistics_award_money_status]);
            }
            return $app->m_server->process_database($task, $this, null, null);
        }
    }

    /**
     * 更新玩家奖励金额 和状态
     * @param $statistical_id
     * @param $statistics_award_money
     * @param $statistics_award_money_status
     * @param null $app
     * @return mixed
     */
    public function update_player_promoters_award_by_id($player_id, $statistics_award_money, $statistics_award_money_status = self::statistics_award_money_status_enter, $app = null)
    {
        $desk_data = new playerstatisticaldatatask();
        $desk_data->set_action('select');
        $desk_data->append_where(array('statistical_player_id' => $player_id));
        if (isset($this->m_app->m_server) && $this->m_app->m_server) {
            $app = $this->m_app;
        }
        $userinfo = $app->m_server->process_database($desk_data, null, null, null);
        if ($userinfo) {
            $task = new playerstatisticaldatatask();
            $task->set_action('update_fields');
            $task->append_where(['statistical_player_id' => $player_id]);
            $this->update('statistical_award_money', $statistics_award_money);
            if (isset($this->m_app->m_server) && $this->m_app->m_server) {
                $app = $this->m_app;
            }
            return $app->m_server->process_database($task, $this, null, null);
        } else {
            $agentinfo = agentplayer::agentplayerinfo($app, $player_id);
            $agent_parentid = $agentinfo->get('agent_parentid', 0);
            $model = new playerstatisticalmodel();
            $model->insert('statistical_player_id', $player_id);
            $model->insert('statistical_agent_id', $agent_parentid);
            $model->insert('statistical_award_money', $statistics_award_money);
            $model->insert('statistical_time', time());
            $desk_data_task = new playerstatisticalinfodatatask();
            $desk_data_task->set_action("insert");
            if (isset($this->m_app->m_server) && $this->m_app->m_server) {
                $app = $this->m_app;
            }
            return $app->m_server->process_database($desk_data_task, $model, null, null);
        }
    }


    /**
     * @param $playerid
     * @param null $app
     * @return mixed
     * 统计推广用户领取的金币总和
     */
    public function receive_money($playerid, $type = null, $app = null)
    {

        $task = new playerstatisticaldatatask();
        $task->set_action('select_sum');
        if (is_null($type)) {
            if (is_array($playerid)) {
                $task->append_where(array('statistical_player_id' => ['in', $playerid]));
            } else {
                $task->append_where(array('promoters_player_id' => $playerid));
            }
            if (isset($this->m_app->m_server) && $this->m_app->m_server) {
                $app = $this->m_app;
            }
            return $app->m_server->process_database($task, $this, ['sum' => 'statistical_award_money'], []);
        } else {
            if (is_array($playerid)) {
                $task->append_where(array('statistical_player_id' => ['in', $playerid]));
                $task->set_other('group by statistical_player_id');
            } else {
                $task->append_where(array('promoters_player_id' => $playerid));
            }
            if (isset($this->m_app->m_server) && $this->m_app->m_server) {
                $app = $this->m_app;
            }
            return $app->m_server->process_database($task, $this, ['sum' => 'statistical_award_money', 'columns' => 'statistical_player_id'], []);

        }


    }

    /**
     * @param $playerid
     * @param null $app
     */
    public function promote_player($playerid, $app = null)
    {

        $task = new playerstatisticaldatatask();
        $task->set_action('select_list_in');
        $task->append_where(['statistical_player_id' => $playerid]);
        if (isset($this->m_app->m_server) && $this->m_app->m_server) {
            $app = $this->m_app;
        }
        return $app->m_server->process_database($task, $this, null, null);
    }


    /**
     * @param $statistical_id
     * @param int $statistics_award_money_status
     * @param null $app
     * @return mixed
     */

    public function update_player_promoters_award_status($statistical_id, $statistics_award_money_status = self::statistics_award_money_status_enter, $app = null)
    {
        $task = new playerstatisticaldatatask();
        $task->set_action('update_fields');
        $task->append_where(['statistical_id' => $statistical_id]);
        $this->update('statistical_award_money_status', $statistics_award_money_status);
        $this->update('statistical_award_status_time', time());
        if (isset($this->m_app->m_server) && $this->m_app->m_server) {
            $app = $this->m_app;
        }
        return $app->m_server->process_database($task, $this, null, null);
    }

    /**
     * @param $playser_id
     * @param null $condition
     * @param null $app
     * @return mixed
     */
    public function show_update_playser($playser_id, $condition = null, $app = null)
    {
        $desk_data = new playerstatisticaldatatask();
        $desk_data->set_action('select');
        $desk_data->append_where(array('statistical_player_id' => $playser_id));
        if (isset($this->m_app->m_server) && $this->m_app->m_server) {
            $app = $this->m_app;
        }
        return $app->m_server->process_database($desk_data, null, null, null);

    }


}


















