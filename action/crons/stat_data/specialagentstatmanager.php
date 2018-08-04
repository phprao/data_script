<?php
/**
 * 特代月统计
 * Class specialagentstatmanager
 * @author ChangHai Zhan
 */
class specialagentstatmanager
{
    /**
     * 钱的类型
     */
    const money_type = 1;
    private $money_rate_value_default = 10000;

    /**
     * @param basicdi $app
     * @param $record_model
     * @return bool
     */
    public function stat_logic(basicdi $app, $record_model)
    {
        //玩家消耗记录 计入 特代统计
        $player_id               = $record_model->get('change_money_player_id', 0);
        $change_money_type       = $record_model->get('change_money_type', 0);
        $change_money_tax        = $record_model->get('change_money_tax', 0);
        $change_money_money_type = $record_model->get('change_money_money_type', 0);
        $change_money_time       = $record_model->get('change_money_time', 0);
        $statistics_date         = date('Y-m-d', $change_money_time);
        $statistics_time         = strtotime($statistics_date);
        //统计游戏消耗
        if ($change_money_type != 2 && $change_money_type != 3) {
            return true;
        }
        //类型金币
        if ($change_money_money_type != self::money_type) {
            return true;
        }
        //查询玩家特代
        $agent_info_model = agentinfomodel::model($app)->get_agent_by_player_id($player_id);
        if (!$agent_info_model) {
            return true;
        }
        $statistics_agent_id        = $agent_info_model->get('agent_top_agentid', 0);
        $statistics_parent_agent_id = $agent_info_model->get('agent_parentid', 0);
        // 是否来自直属推广的消耗
        if($statistics_agent_id == $statistics_parent_agent_id){
            $is_direct = true;
        }else{
            $is_direct = false;
        }
        // 金币兑换比例
        $money_rate_info_model = moneyrateinfomodel::model($app)->get_money_rate_info_by_rate_type(self::money_type);
        if (!$money_rate_info_model) {
            $money_rate_value = $this->money_rate_value_default;
        }else{
            $money_rate_value = $money_rate_info_model->get('money_rate_value', 0);
        }
        //是否已经有记录了
        $agent_statistics_model = agentsuperstatisticsdatemodel::model($app)->get_statistics_by_agent_id($money_rate_value,$statistics_agent_id, $statistics_time, self::money_type);
        if ($agent_statistics_model) {
            $statistics_id                    = $agent_statistics_model->get('statistics_id', 0);
            $old_statistics_money_data_other  = $agent_statistics_model->get('statistics_money_data', 0);
            $old_statistics_money_data_direct = $agent_statistics_model->get('statistics_money_data_direct', 0);
            $old_statistics_money_data        = $is_direct ? $old_statistics_money_data_direct : $old_statistics_money_data_other;
            //更新统计记录
            $ret = agentsuperstatisticsdatemodel::model($app)->update_statistics_by_id($statistics_id, $change_money_tax, $old_statistics_money_data, $is_direct);
            if (!$ret) {
                return true;
            }
        } else {
            $params = [];
            $params['statistics_money_type']       = self::money_type;
            $params['statistics_money_data']       = $change_money_tax;
            $params['statistics_date']             = $statistics_date;
            $params['statistics_time']             = $statistics_time;
            $params['statistics_month']            = strtotime(date('Y-m', $statistics_time));
            $params['statistics_money_rate_value'] = $money_rate_value;
            $params['is_direct']                   = $is_direct;
            //添加统计记录
            $id = agentsuperstatisticsdatemodel::model($app)->create_statistics($statistics_agent_id, $params);
            if (!$id) {
                return true;
            }
        }
        return true;
    }

}
