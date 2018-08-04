<?php


class agentspromotersstatisticsmodel extends basicdatamodel
{
    public static function model($app = null, $className = __CLASS__)
    {
        return new $className($app);

    }


    public function getagentsinfo($params, $app = null)
    {
        $task = new agentspromotersstatisticsdatatask();
        $task->set_action('select_sum');
        if($params['agent_id']){
            $task->append_where(array('statistics_agents_id'=>$params['agent_id']));
        }
        if($params){
            $task->append_where(array('statistics_time'=>[['>=', $params['start_time']],['<', $params['end_time']]]   ));
        }
        if (isset($this->m_app->m_server) && $this->m_app->m_server) {
            $app = $this->m_app;
        }
        return $app->m_server->process_database($task, $this, ['sum' => 'statistics_my_income'], null);



    }


}
