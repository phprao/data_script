<?php

/**
 * 账户表
 * Class agentaccountinfomodel
 * @author ChangHai Zhan
 */
class playerpromoteawardlogmodel extends basicdatamodel
{
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
     * @param $player_id
     * @param $agent_player_id
     * @param null $app
     * @return mixed
     */
    public function show_records($player_id,$agent_player_id, $app = null){
        $desk_data = new playerpromoteawardlogdatask();
        $desk_data->set_action('select');
        $desk_data->append_where(array('log_player_id' => $player_id));
        $desk_data->append_where(array('log_promoter_id' => $agent_player_id));
        if (isset($this->m_app->m_server) && $this->m_app->m_server) {
            $app = $this->m_app;
        }
        return $app->m_server->process_database($desk_data, null, null, null);
    }
    /**
     * @param $player_id
     * @param $award_money
     * @return bool
     * 添加奖励记录
     */
    public function add_records($player_id, $award_money,$agent_player_id, $app = null)
    {
        if (!$player_id) {
            return true;
        }
        $modellong = new playerpromoteawardlogmodel();
        $modellong->insert('log_promoter_id', $agent_player_id);
        $modellong->insert('log_player_id', $player_id);
        $modellong->insert('log_award', $award_money);
        $modellong->insert('log_time', time());
        $modellong->insert('log_date', date('Y-m-d H:i:s', time()));
        $desk_data_task = new playerpromoteawardlogdatask();
        $desk_data_task->set_action("insert");
        if (isset($this->m_app->m_server) && $this->m_app->m_server) {
            $app = $this->m_app;
        }
        return $app->m_server->process_database($desk_data_task, $modellong, null, null);

    }



}

