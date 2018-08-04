<?php
/**
 * 特代/代理收益计算 入账
 * Class agentincomemanager
 * @author ChangHai Zhan
 */
class agentincomemanager
{
    /**
     * 钱的类型
     */
    const money_type = 1;
    protected $limit_super = 100;
    protected $limit_star = 100;
    /**
     * 特代/代理收益计算 入账
     * @param  basicdi $app  [description]
     * @param  integer $type 1-特代，2-代理
     * @return [type]        [description]
     */
    public function stat_logic(basicdi $app , $type = 1)
    {
 
        if($type == 1){
            //月初 6个小时之内可以执行
            $cur_month = strtotime(date('Y-m', time()));
            if ((time() - $cur_month) > 3600 * 6) {
                return true;
            }
            $statistics_date = $cur_month;
            //特代统计计算
            $this->statistics_money($app, $statistics_date);
            //特代钱入账
            $this->statistics_enter_money($app, $statistics_date);
        }

        // if($type == 2){
        //     // 每天前6小时之内可执行
        //     $cur_day = strtotime(date('Y-m-d', time()));
        //     // if ( (time() - $cur_day) > 3600 * 6) {
        //     //     return true;
        //     // }
        //     BASIC_LOG_TRACE('agentincomemanager--1', '%s', 'start');
        //     $statistics_date = $cur_day - 86400;
        //     $result = $this->statistics_agent_yesterday_earn($app,$statistics_date);
        //     BASIC_LOG_TRACE('agentincomemanager--1', '%s', 'end = '.$result);
        // }
        
        return true;
    }

    /**
     * 统计钱
     * @param $app
     * @param $statistics_date
     */
    public function statistics_money($app, $statistics_date)
    {
        //特代统计计算
        $start = 0;
        $page_size = $this->limit_super;
        //查询统计 上个月 没有计算分成比例的
        $condition = new stdClass();
        $condition->statistics_time = $this->get_last_month($statistics_date);
        $condition->statistics_money_type = self::money_type;
        $condition->statistics_money_status = agentsuperstatisticsdatemodel::statistics_money_status_statistics;
        //循环取数据
        while ($statistics_models = agentsuperstatisticsdatemodel::model($app)->get_statistics_list($condition, $start, $page_size)) {
            foreach ($statistics_models as $statistics_model) {
                //加载最新特代分成配置--以当月最新的配置为准
                $agent_config_models =  agentsuperincomeconfigmodel::model($app)->get_agent_super_list($statistics_model->get('statistics_agent_id', '0'));
                if (!$agent_config_models) {
                    BASIC_LOG_TRACE('crons|stat_data|agentincomemanager', '%s', '渠道来自other玩家的分成配置未配置');
                    return true;
                }
                //数组转换
                $statistics_super_config = agentsuperincomeconfigmodel::model($app)->to_array($agent_config_models);
                //计算分成比例
                $statistics_money_rate_value = $statistics_model->get('statistics_money_rate_value', '10000');
                $statistics_money_data = $statistics_model->get('statistics_money_data', '0');
                $statistics_money_data_direct = $statistics_model->get('statistics_money_data_direct', '0');
                //获取分成比例
                $statistics_super_share = agentsuperincomeconfigmodel::model($app)->condition_compare($statistics_super_config, $statistics_money_data/$statistics_money_rate_value);

                $params = [];
                $params['money_rate_value'] = $statistics_model->get('statistics_money_rate_value', '10000');
                $params['money_rate_unit'] = $statistics_model->get('statistics_money_rate_unit', '0');
                $params['money_rate_unit_type'] = $statistics_model->get('statistics_money_rate_unit_type', '0');
                //钱的转换 真实分成比例
                $super_share = agentsuperincomeconfigmodel::model($app)->get_super_share($statistics_super_share);
                // 渠道直属玩家的分成比例 7000
                $config = $this->get_channel_share_direct($app);
                if(!$config){
                    BASIC_LOG_TRACE('crons|stat_data|agentincomemanager', '%s', '渠道来自直属玩家的分成配置未配置');
                    return true;
                }
                $config_share = agentsuperincomeconfigmodel::model($app)->get_super_share($config);
                // 合计
                $total_money = $statistics_money_data * $super_share + $config_share * $statistics_money_data_direct ;
                $statistics_money = moneyrateinfomodel::model($app)->get_money_to_rmb($total_money, $params);
                //更新状态和钱
                $statistics_id = $statistics_model->get('statistics_id', 0);
                agentsuperstatisticsdatemodel::model($app)->update_statistics_money_by_id($statistics_id, $statistics_super_share, $statistics_money, $config);
            }
        }
    }

    /**
     * 钱入账
     * @param $app
     * @param $statistics_date
     */
    public function statistics_enter_money($app, $statistics_date)
    {
        //特代统计计算
        $start = 0;
        $page_size = $this->limit_super;
        //查询统计 上个月 已经计算分成的 没有入账的特代
        $condition = new stdClass();
        $condition->statistics_time = $this->get_last_month($statistics_date);
        $condition->statistics_money_type = self::money_type;
        $condition->statistics_money_status = agentsuperstatisticsdatemodel::statistics_money_status_calculated;
        //循环取数据
        while ($statistics_models = agentsuperstatisticsdatemodel::model($app)->get_statistics_list($condition, $start, $page_size)) {
            //计算分成比例
            foreach ($statistics_models as $statistics_model) {
                $statistics_money = $statistics_model->get('statistics_money', '0');
                $statistics_agent_id = $statistics_model->get('statistics_agent_id', 0);
                $statistics_id = $statistics_model->get('statistics_id', 0);
                //事务
                $M = new basictransactiontask();
                $app->m_server->process_database($M,null,null,null);
                //更新状态和钱
                $return = agentsuperstatisticsdatemodel::model($app)->update_statistics_money_entered_by_id($statistics_id);
                if (!$return) {
                    $M->rollback();
                    break;
                }
                if ($statistics_money > 0) {
                    $account_model = agentaccountinfomodel::model($app)->get_account_info($statistics_agent_id);
                    if (!$account_model) {
                        $M->rollback();
                        break;
                    }
                    $log_bef_money = $account_model->get('agent_account_money', 0);
                    $return_log = agentaccountinfologmodel::model($app)->add_log($statistics_agent_id, $log_bef_money, $statistics_money, agentaccountinfologmodel::log_type_super_income);
                    if (!$return_log) {
                        $M->rollback();
                        break;
                    }
                    $return_account = agentaccountinfomodel::model($app)->incr_money_by_agent_id($statistics_agent_id, $statistics_money, $log_bef_money);
                    if (!$return_account) {
                        $M->rollback();
                        break;
                    }
                }
                $M->commit();
            }
        }
    }

    /**
     * 获取上个月的时间
     * @param $statistics_date
     * @return false|string
     */
    public function get_last_month($statistics_date)
    {
        return strtotime('-1 month',$statistics_date);
    }

    // 代理昨日收益
    public function statistics_agent_yesterday_earn($app,$statistics_date)
    {
        $promote_task = new agentsstatisticspromoterdatatask();
        $promote_task->set_action('select_yesterday_sum');
        $record = $app->m_server->process_database($promote_task,null,['statistics_time'=>$statistics_date,'statistics_status'=>0,'limit'=>$this->limit_star],null);
        $flag = true;
        if(!empty($record)){
            foreach($record as $val){
                $result = $this->deal_agent_yesterday_earn($promote_task,$app,$val);
                if(!$result){
                    $flag = false;
                }
            }
        }
        return $flag;
    }

    public function deal_agent_yesterday_earn($promote_task,$app,$record){
        if(!$record){
            return true;
        }

        $M = new basictransactiontask();
        $app->m_server->process_database($M,null,null,null);

        // 记录日志
        $r2 = $this->deal_agent_record_log($app,$record);
        // 更新代理账户余额
        $r1 = $this->deal_agent_update_data($app,$record);
        // 更新收益统计状态为1
        $r3 = $this->deal_agent_update_promoter($promote_task,$app,$record);

        if($r1 && $r2 && $r3){
            $M->commit();
            return true;
        }else{
            BASIC_LOG_TRACE('agentincomemanager--2', '%s', "r1 = $r1, r2 = $r2, r3 = $r3");
            $M->rollback();
            return false;
        }

    }
    public function deal_agent_update_data($app,$record){
        return agentaccountinfomodel::model($app)->incr_money_by_agent_id($record['statistics_agents_id'], $record['total']);
    }
    public function deal_agent_record_log($app,$record){
        $account = agentaccountinfomodel::model($app)->get_account_info($record['statistics_agents_id']);
        if(is_object($account)){
            $old_money = $account->get('agent_account_money',0);
        }else{
            $old_money = 0;
        }
        return agentaccountinfologmodel::model($app)->add_log($record['statistics_agents_id'],$old_money,$record['total'],agentaccountinfologmodel::log_type_agent_yesterday_income,$record['statistics_money_type']);
    }
    public function deal_agent_update_promoter($promote_task,$app,$record){
        $promote_task->set_action('update_yesterday_status');
        $where = array(
            'statistics_agents_id' =>$record['statistics_agents_id'],
            'statistics_time'      =>$record['statistics_time'],
            'statistics_status'    =>0
        );
        return $app->m_server->process_database($promote_task, null,$where,null);
    }

    public function get_channel_share_direct($app){
        $task = new configdatatask();
        $task->set_action('select');
        $task->append_where(array('config_name' => 'channel_income_rate_from_direct'));
        $task->append_where(array('config_status' => 1));
        $data = $app->m_server->process_database($task, null, null, null);
        if($data){
            $config = $data->get('config_config', '');
            if(!empty($config)){
               $config_init = json_decode($config, true);
               return $config_init['rate'];
            }
        }

        return false;
    }
}
