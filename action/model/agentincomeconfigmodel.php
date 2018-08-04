<?php

/**
 * 代理分成比例
 * Class agentincomeconfigmodel
 * @author ChangHai Zhan
 */
class agentincomeconfigmodel extends basicdatamodel
{
    /**
     * 分成存储比例
     */
    const income_share_scale = 10000;

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
     * 获取代理分成比例list
     * @param $agent_id
     * @param $app
     * @return mixed
     */
    public function get_agent_income_list($agent_id, $app = null)
    {
        $task = new agentincomeconfigdatatask();
        $task->set_action('select_list');
        $task->append_where(['income_agent_id' => $agent_id]);
        $task->set_other('order by income_count_level asc');
        if (isset($this->m_app->m_server) && $this->m_app->m_server) {
            $app = $this->m_app;
        }
        $model = $app->m_server->process_database($task, null, null, null);
        if (!$model) {
            // 查找有无特代的配置
            $agenttask = new agentinfodatatask();
            $agenttask->set_action('select');
            $agenttask->append_where(['agent_id' => $agent_id]);
            $agentinfo = $app->m_server->process_database($agenttask, null, null, null);
            $task = new agentincomeconfigdatatask();
            $task->set_action('select_list');
            $task->append_where(['income_agent_id' => $agentinfo->get('agent_top_agentid',0)]);
            $task->set_other('order by income_count_level asc');
            $model = $app->m_server->process_database($task, null, null, null);
            if(!$model){
                $task = new agentincomeconfigdatatask();
                $task->set_action('select_list');
                $task->append_where(['income_agent_id' => 0]);
                $task->set_other('order by income_count_level asc');
                $model = $app->m_server->process_database($task, null, null, null);
            }
        }

        return $model;
    }

    /**
     * @param $config
     * @return array
     */
    // public function to_array($config)
    // {
    //     $data = [];
    //     foreach ($config as $value) {
    //         $data[$value->get('income_agent_id', 0)] = [
    //             'income_condition_number' => $value->get('income_condition_number', 0),
    //             'income_condition_money' => $value->get('income_condition_money', 0),
    //             'income_share' => json_decode($value->get('income_share', 0), true),
    //         ];
    //     }
    //     return $data;
    // }

    /**
     * 比较条件获取配置
     * @param $config
     * @param $total_number
     * @param $total_money
     * @return int
     */
    // public function condition_compare($config, $total_number, $total_money = 0)
    // {
    //     foreach ($config as $value) {
    //         $income_condition_number = $value['income_condition_number'];
    //         $income_condition_money = $value['income_condition_money'];
    //         $income_share = $value['income_share'];
    //         if ($income_condition_number < $total_number && ($income_condition_money == 0 || $income_condition_money < $total_money)) {
    //             return $income_share;
    //         }
    //     }
    //     return [];
    // }

    /**
     * 真实比例
     * @param $income_share
     * @return float|int
     */
    // public function get_income_share($income_share)
    // {
    //     foreach ($income_share as &$value) {
    //         $value = ($value / self::income_share_scale);
    //     }
    //     return $income_share;
    // }
}