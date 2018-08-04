<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/31
 * Time: 11:41
 * @author ChangHai Zhan
 */
class agentupgrademanager
{


    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * @param $app
     * @param $player_id
     * @param $money
     * @return bool
     */
    public function agent_upgrade($app, $player_id, $money)
    {

        if (!$player_id) {
            return true;
        }

        //加载玩家信息
        $agent_info = agentinfomodel::model($app)->get_agent_by_player_id($player_id);
        if (!$agent_info) {
            return true;
        }
        //特代ID
        $agent_top_agentid = $agent_info->get('agent_top_agentid', 0);
        $agent_parentid = $agent_info->get('agent_parentid', 0);
        if ($agent_top_agentid == $agent_parentid) {
            //特级代理 推广的 不用升级
            return true;
        }
        //加载上级信息
        $con = new stdClass();
        $con->agent_login_status = agentinfomodel::agent_login_status_no;
        $agent_parent_info = agentinfomodel::model($app)->get_agent_by_id($agent_parentid, $con);

        if (!$agent_parent_info) {
            return true;
        }
        //人数  加载这条消耗的上级信息
        $agent_id = $agent_parent_info->get('agent_id', 0);
        $agent_promote_conut = $agent_parent_info->get('agent_promote_count', 0);
        $agent_player_id = $agent_parent_info->get('agent_player_id', 0);
        $agent_parentid = $agent_parent_info->get('agent_parentid', 0);
        //加载选择条件 :如果有代理条件就用代理条件如果没有就用总公司条件
        $condition_agent_model = agentconfigmodel::model($app)->get_config_by_agent_id($agent_top_agentid);
        if (!$condition_agent_model) {
//            return true;
            //如果没有就加载总公司的配置
            $condition_agent_model = agentconfigmodel::model($app)->get_config_by_agent_id($agent_top_agentid = 0);
        }
        $agent_conditions_id = $condition_agent_model->get('agent_conditions_id', 0);
        //加载model
        $condition_model = agentconditionsmodel::model($app)->get_condition_by_id($agent_conditions_id);
        if (!$condition_model) {
            return true;
        }
        //加载条件
        $data_condition = json_decode($condition_model->get('agent_conditions_data', 0), true);
        if (!$data_condition) {
            return true;
        }
        //人数判断
        if ($data_condition['promote_number']) {
            if ($agent_promote_conut < $data_condition['promote_number']) {
                return true;
            }
        }
        //玩家消耗
        $player_statistics_model = playerstatisticalmodel::model($app)->get_player_promoters_statistics($agent_player_id);
        if (!$player_statistics_model) {
            return true;
        }
        $statistical_sub_total_cost = $player_statistics_model->get('statistical_sub_total_cost', 0);
        //判断金币消耗
        if ($data_condition['gold_consumption']) {
            if ($statistical_sub_total_cost < $data_condition['gold_consumption']) {
                return true;
            }
        }
        $agent_ccountinfo = agentaccountinfomodel::model($app)->get_account_info($agent_id);
        //事务
        $M = new basictransactiontask();
        $app->m_server->process_database($M, null, null, null);
        if (!agentinfomodel::model($app)->update_agent_login_status_by_id($agent_id)) {
            $M->rollback();
            return true;
        }
        //更新 领取奖励状态为1
        if (!playerstatisticalmodel::model($app)->update_player_promoters_award_status($statistical_id = $player_statistics_model->get('statistical_id', 0))) {
            $M->rollback();
            return true;
        }
        //把奖励的前更新到dc_agent_account_info 里面的代理信息表
        if (!agentaccountinfomodel::model($app)->incr_agent_account_money($agent_id, $agent_ccountinfo->get('agent_account_money', 0) + $player_statistics_model->get('statistical_award_money', 0))) {
            $M->rollback();
            return true;
        }
        if (!agentupgraderecordmodel::model($app)->add_record($agent_player_id, $agent_parentid)) {
            $M->rollback();
            return true;
        }
        $M->commit();
        return true;


    }


}