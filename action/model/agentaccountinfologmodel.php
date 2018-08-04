<?php

/**
 * 资金日志
 * Class agentaccountinfologmodel
 * @author ChangHai Zhan
 */
class agentaccountinfologmodel extends basicdatamodel
{
    /**
     * rmb
     */
    const log_money_type_rmb = 1;
    /**
     * 日志类型 特代业绩收入
     */
    const log_type_super_income = 1;
    /**
     * 日志类型 玩家推广奖励
     */
    const log_type_promoters_award = 2;

    /**
     * 代理日结算收益
     */
    const log_type_agent_yesterday_income = 3;
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
     * @param $log_agent_id
     * @param $log_bef_money
     * @param $log_money
     * @param $log_type
     * @param int $log_money_type
     * @param null $app
     * @return mixed
     */
    public function add_log($log_agent_id, $log_bef_money, $log_money, $log_type, $log_money_type = self::log_money_type_rmb, $app = null)
    {
        $task = new agentaccountinfologdatatask();
        $task->set_action('insert_fields');
        $this->insert('log_money_type', $log_money_type);
        $this->insert('log_agent_id', $log_agent_id);
        $this->insert('log_bef_money', $log_bef_money);
        $this->insert('log_money', $log_money);
        $this->insert('log_aft_money', $log_bef_money + $log_money);
        $this->insert('log_add_time', time());
        $this->insert('log_type', $log_type);
        if (isset($this->m_app->m_server) && $this->m_app->m_server) {
            $app = $this->m_app;
        }
        return $app->m_server->process_database($task, $this, null, null);
    }
}