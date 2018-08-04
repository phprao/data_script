<?php
set_time_limit(0);
/*
 *  @desc:   clubinfo logic
 *  @author: xxm
 *  @email:  237886849@qq.com
 *  @note:   所有文件命名以小写，所有子类名以小写
 *
 *
 *玩家游戏金币消耗
 *
 *
 */

class playerstatistical extends basicaction
{

    private $m_agent_earn_mgr = null;
    private $m_special_agent_earn_mgr = null;
    private $m_player_update_mgr = null;
    private $m_game_cost_sum_mgr = null;
    private $statistics_total_task = null;
    private $app;
    private $limit_num;
    private $money_task;
    private $time_out = 86400;

    protected function before()
    {
        $this->m_agent_earn_mgr         = new agentearnstatmanager();
        $this->m_special_agent_earn_mgr = new specialagentstatmanager();
        $this->m_player_update_mgr      = new playerupdatestatmanager();
        $this->money_task               = new moneychangedatatask();
        $this->change_log_task          = new moneychangerecordlogmanager();
        $this->m_game_cost_sum_mgr      = new gamecostsummanager();
        $this->statistics_total_task    = new statisticstotaltaskmanager();
        $this->limit_num = 50;

        return parent::before();
    }

    /**
     * @param basicdi $app
     */
    protected function logic(basicdi $app)
    {
        $this->app = $app;
        $data = $this->get_list();
        $flag = true;
        if($data){
            foreach ($data as $value) {

                $result = $this->deal_change_record($value);

                if(!$result){
                    $flag = false;
                }
            }
        }

        if(!$flag){
            $this->format_response_data('error','部分记录处理失败');
        }
        
    }

    private function get_list(){
        // $this->money_task->set_action('select_page');
        // $this->money_task->set_other('limit '.$this->limit_num);

        $this->money_task->set_action('select_list_in');
        $this->money_task->set_other('limit '.$this->limit_num);
        $this->money_task->append_where_list(['change_money_update_time'=>time()-$this->time_out],basicdatatask::$WHERE_TYPE_LE);
        $data = $this->app->m_server->process_database($this->money_task, null, null, null);
        return $data;
    }

    protected function update_log_time($record){
        $recordmodel = new moneychangemodel($this->app);
        $recordmodel->update_log_time($record);
    }

    private function deal_change_record(basicmodelimpl $record){
        if(!$record){
            return true;
        }

        $M = new basictransactiontask();
        $this->app->m_server->process_database($M,null,null,null);

        // 记录处理
        $r1 = $this->record_data($record);

        if(in_array($record->get('change_money_type',0),[2,3])){
            // 消耗处理
            $r2 = $this->stat_data($record);
        }else{
            $r2 = true;
        }
        
        // 金币变动记录
        $r3 = $this->money_change_record_log($record);

        // 数据统计
        $r4 = $this->statistics_total($record);

        if($r1 && $r2 && $r3 && $r4){
            $M->commit();
            return true;
        }else{
            $M->rollback();
            // 更新时间
            $this->update_log_time($record);
            return false;
        }

    }

    protected function money_change_record_log(basicmodelimpl $record){
        if(!$this->change_log_task->stat_logic($this->app,$record)) {
            return false;
        }
        return true;
    }

    protected function statistics_total(basicmodelimpl $record){
        if(!$this->statistics_total_task->stat_logic($this->app,$record)) {
            return false;
        }
        return true;
    }

    //统计各渠道
    protected function stat_data(basicmodelimpl $record) {

        // 代理收益
        if(!$this->m_agent_earn_mgr->stat_logic($this->app,$record)) {
            return false;
        }

        // 特代收益
        if(!$this->m_special_agent_earn_mgr->stat_logic($this->app,$record)) {
            return false;
        }

        // 玩家总消耗
        if(!$this->m_player_update_mgr->stat_logic($this->app,$record)) {
            return false;
        }

        // 各个游戏每日消耗堆叠
        if(!$this->m_game_cost_sum_mgr->stat_logic($this->app,$record)) {
            return false;
        }

        //推广奖励 todo
        (new promotersawardmanager())->stat_logic($this->app, $record);

        //玩家升级为代理
        (new agentlevelmanager())->stat_logic_one($this->app, $record);



        return true;
    }


    // 备份
    protected function record_data(basicmodelimpl $record) {
        $record_data = $this->money_task->format_model($record);
        $storemodel = new moneychangestoremodel($this->app);
        // 删除原始记录
        $this->money_task->set_action('delete_list');
        $this->money_task->append_where(array('change_money_id' => $record_data['change_money_id']));
        $re1 = $this->app->m_server->process_database($this->money_task, null, null, null);
        // 备份记录
        unset($record_data['change_money_id']);
        $re2 = $storemodel->save_data_model($record_data);

        return $re1 && $re2;
    }
}


