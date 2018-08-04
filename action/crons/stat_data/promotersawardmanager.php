<?php

/**
 * 推广奖励金额
 * Class promotersawardmanager
 * @author ChangHai Zhan
 */
class promotersawardmanager
{
    /**
     * @param basicdi $app
     * @return bool
     */
    public function stat_logic(basicdi $app, $record)
    {
        //加载该玩家消耗统计记录 尚未给奖励金额 特代不用计算
        $player_id = $record->get('change_money_player_id', 0);
        if (!$player_id) {
            return true;
        }
        $agentinfo = agentinfomodel::model($app)->get_agent_by_player_id($player_id);
        //加载用户的上级（推广员）
        $parentid_agent = $agentinfo->get('agent_parentid', 0);
        $agent_model_info = agentinfomodel::model($app)->get_agent_by_id($parentid_agent);
        if (!$agent_model_info) {
            return true;
        }
        if ($agent_model_info->get('agent_parentid', 0) == 0) {
            //特代不用计算
            return true;
        }
        $agent_player_id = $agent_model_info->get('agent_player_id', 0);
        $condition = new stdClass();
        $condition->statistics_award_money_status = playerstatisticalmodel::statistics_award_money_status_not;
        $model = playerstatisticalmodel::model($app)->get_player_promoters_statistics($agent_player_id, $condition);
        if (!$model) {
            return true;
        }

        //判断是否领取过奖励
        if ($model->get('statistical_award_money_status', 0) === 1) {
            return true;
        }
        //上级代理 加载代理信息
        $statistical_id = $model->get('statistical_id', 0); //主键ID
        $statistical_agent_id = $model->get('statistical_agent_id', 0);//上级代理ID
//        $statistical_value = $model->get('statistical_value', 0);
        $statistical_sub_total_cost = $model->get('statistical_sub_total_cost', 0); // 所有下级消耗统计
        $agent_model = agentinfomodel::model($app)->get_agent_by_id($statistical_agent_id);  // 加载顶级代理信息（特代）
        if (!$agent_model) {
            return true;
        }
        $agent_top_agentid = $agent_model->get('agent_id', 0);//顶级代理ID （特代）
        //加载特代条件
        $condition_award_model = promotersawardconfigmodel::model($app)->get_award_config_by_agent_id($agent_top_agentid);
        if (!$condition_award_model) {
//            return true;
            //如果代理没有配置条件就加载总公司的
            $agent_top_agentid = 0;
            $condition_award_model = promotersawardconfigmodel::model($app)->get_award_config_by_agent_id($agent_top_agentid);
        }
        $award_condition = $condition_award_model->get('award_condition', 0);//条件
        $award_money = $condition_award_model->get('award_money', 0);//奖励的值

        //判断这个推广员只能领取一次奖励
        if ($model->get('statistical_award_money', 0) >= $award_money) {
            return true;
        }

        //金额不满足条件
        if ($statistical_sub_total_cost < $award_condition) {
            return true;
        }

        $palyer_money = playerstatisticalmodel::model($app)->show_update_playser($agent_player_id);
//        $money = $award_money + $palyer_money->get('statistical_award_money', 0);
        $money = $award_money;
        //奖励金额
        //事务
        $M = new basictransactiontask();
        $app->m_server->process_database($M, null, null, null);
        //更新记录
        if (!playerstatisticalmodel::model($app)->update_player_promoters_award_by_id($agent_player_id, $money)) {
            $M->rollback();
            return true;
        }
        if ($award_money > 0) {

            $account_model = agentaccountinfomodel::model($app)->get_account_info($statistical_agent_id);
            if (!$account_model) {
                $M->rollback();
                return true;
            }
//            $log_bef_money = $account_model->get('agent_account_money', 0);
//            $return_log = agentaccountinfologmodel::model($app)->add_log($statistical_agent_id, $log_bef_money, $award_money, agentaccountinfologmodel::log_type_promoters_award);
//            if (!$return_log) {
//                $M->rollback();
//                return true;
//            }
//            $return_account = agentaccountinfomodel::model($app)->incr_money_by_agent_id($statistical_agent_id, $award_money, $log_bef_money);
//            if (!$return_account) {
//                $M->rollback();
//                return true;
//            }

            // 推广奖励记录

            $promoteawardlog = playerpromoteawardlogmodel::model($app)->add_records($player_id, $award_money, $agent_player_id);
            if (!$promoteawardlog) {
                $M->rollback();
                return true;
            }
            // 推广奖励统计 天
            $smodel = new statisticstotalmodel($app);
            $smodel->promote_award_sum($award_money);// 分
        }
        $M->commit();
        return true;
    }
}
