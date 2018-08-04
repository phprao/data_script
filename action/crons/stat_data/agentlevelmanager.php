<?php

/**
 * 代理升级
 * Class agentlevelmanager
 * @author ChangHai Zhan
 */
class agentlevelmanager
{
    /**
     * @param basicdi $app
     * @return bool
     */
    public function stat_logic(basicdi $app)
    {
        //加载条件表
        $condition_data = [];
        $condition_models = agentconditionsmodel::model($app)->get_condition_all();
        foreach ($condition_models as $condition_model) {
            $condition_data[$condition_model->get('agent_conditions_id', 0)] = [
                'agent_conditions_type' => $condition_model->get('agent_conditions_type', 0),
                'agent_conditions_data' => json_decode($condition_model->get('agent_conditions_data', 0), true),
            ];
        }
        //扫描所有特代 查询下面玩家是否满足条件
        $start = 0;
        $page_size = 1000;
        //循环取特代数据
        while ($super_agent_models = agentinfomodel::model($app)->get_super_agent_list($start, $page_size)) {
            $start += $page_size;
            //循环取出特代ID
            $agent_ids = [];
            foreach ($super_agent_models as $v_model) {
                $id = $v_model->get('agent_id', 0);
                $agent_ids[$id] = $id;
            }
            //print_r($agent_ids);
            //加载特代的配置 代理升级条件
            $condition_agent_models = agentconfigmodel::model($app)->get_config_by_agent_id($agent_ids);
            //代理条件ID数据
            $condition_agent_data = [];
            foreach ($condition_agent_models as $condition_agent_model) {
                $id = $condition_agent_model->get('agent_id', 0);
                $condition_id = $condition_agent_model->get('agent_conditions_id', 0);
                $condition_agent_data[$id] = $condition_id;
            }
            //print_r($condition_agent_data);
            //循环特代
            foreach ($super_agent_models as $super_agent_model) {
                //特代ID
                $agent_super_id = $super_agent_model->get('agent_id', 0);
                //无条件的跳过
                if (!isset($condition_agent_data[$agent_super_id], $condition_data[$condition_agent_data[$agent_super_id]])) {
                    continue;
                }
                //特代条件
                $condition = $condition_data[$condition_agent_data[$agent_super_id]];
                //print_r($condition);
                //每次取多少玩家
                $start_agent = 0;
                $page_size_agent = 1000;
                //只有人数扫描 加人数条件 玩家
                $map = new stdClass();
                $map->agent_top_agentid = $agent_super_id;
                //玩家
                $map->agent_login_status = agentinfomodel::agent_login_status_no;
                //有人数限制
                if ($condition['agent_conditions_type'] == 1 || $condition['agent_conditions_type'] == 2) {
                    $map->agent_promote_conut = [basicdatatask::$WHERE_TYPE_ELT, $condition['agent_conditions_data']['promote_number']];
                }
                //需要升级代理ID
                $upgrade_agent_ids = [];
                //特代下 满足条件的 玩家
                while ($agent_player_models = agentinfomodel::model($app)->get_agent_list($map, $start_agent, $page_size_agent)) {
                    $start_agent += $page_size_agent;
                    $player_ids = [];
                    $player_id_and_agent_id = [];
                    //循环对比
                    foreach ($agent_player_models as $agent_player_model) {
                        $agent_id = $agent_player_model->get('agent_id', 0);
                        $player_id = $agent_player_model->get('agent_player_id', 0);
                        $agent_parentid = $agent_player_model->get('agent_parentid', 0);
                        //只管人数 直接升级
                        if ($condition['agent_conditions_type'] == 2) {
                            $upgrade_agent_ids[$player_id] = [
                                'agent_id' => $agent_id,
                                'agent_parentid' => $agent_parentid,
                            ];
                        } else {
                            //需要金币条件的玩家
                            $player_ids[$player_id] = $player_id;
                            $player_id_and_agent_id[$player_id] = [
                                'agent_id' => $agent_id,
                                'agent_parentid' => $agent_parentid,
                            ];
                        }
                    }
                    //是否有钱的条件
                    if ($player_ids) {
                        //加载钱的统计
                        $player_statistics_models = playerstatisticalmodel::model($app)->get_player_promoters_statistics($player_ids);
                        foreach ($player_statistics_models as $player_statistics_model) {
                            $statistical_sub_total_cost = $player_statistics_model->get('statistical_sub_total_cost', 0);
                            $statistical_player_id = $player_statistics_model->get('statistical_player_id', 0);
                            //金币是否满足条件
                            if ($condition['agent_conditions_data']['gold_consumption'] <= $statistical_sub_total_cost) {
                                $upgrade_agent_ids[$statistical_player_id] = $player_id_and_agent_id[$statistical_player_id];
                            }
                        }
                    }
                }
                //更新需要升级的玩家 无需判断是否成功 下次再次更新
                foreach ($upgrade_agent_ids as $agent_upgrade_player_id => $upgrade_agent) {
                    //事务
                    $M = new basictransactiontask();
                    $app->m_server->process_database($M, null, null, null);
                    if (!agentinfomodel::model($app)->update_agent_login_status_by_id($upgrade_agent['agent_id'])) {
                        $M->rollback();
                        continue;
                    }
                    if (!agentupgraderecordmodel::model($app)->add_record($agent_upgrade_player_id, $upgrade_agent['agent_parentid'])) {
                        $M->rollback();
                        continue;
                    }
                    $M->commit();
                }
            }
        }
        return true;
    }


    /**
     * 升级玩家
     * @param basicdi $app
     * @param $record
     * @return bool
     */
    public function stat_logic_one(basicdi $app, $record)
    {
        $player_id = $record->get('change_money_player_id', 0);

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
//        //条件
//        $condition_data = [
//            'agent_conditions_type' => $condition_model->get('agent_conditions_type', 0),
//            'agent_conditions_data' => json_decode($condition_model->get('agent_conditions_data', 0), true),
//        ];
//        //人数是否合格
//        if ($condition_data['agent_conditions_type'] == 1 || $condition_data['agent_conditions_type'] == 2) {
//            //人数不合格
//            if ($agent_promote_conut < $condition_data['agent_conditions_data']['promote_number']) {
//                return true;
//            }
//        }
//        //金币是否合格
//        if ($condition_data['agent_conditions_type'] == 1 || $condition_data['agent_conditions_type'] == 3) {
//            $statistical_sub_total_cost = $player_statistics_model->get('statistical_sub_total_cost', 0);
//            if ($statistical_sub_total_cost < $condition_data['agent_conditions_data']['gold_consumption']) {
//                return true;
//            }
//        }

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

        if (!agentaccountinfologmodel::model($app)->add_log($agent_id, $agent_ccountinfo->get('agent_account_money', 0), $player_statistics_model->get('statistical_award_money', 0), 2)) {
            $M->rollback();
            return true;
        }
        $M->commit();
        return true;
    }
}
