<?php

/**
 * +----------------------------------------------------------
 * date: 2018/1/23
 * +----------------------------------------------------------
 * author: Raoxiaoya
 * +----------------------------------------------------------
 * describe:
 * +----------------------------------------------------------
 */
class agentsstatisticshourmodel extends basicdatamodel
{
    public function __construct(basicdi $app = null)
    {
        parent::__construct($app);
        $this->data_task = new agentsstatisticshourdatatask();
    }

    public function save_data_model($data = array())
    {
        if (empty($data)) {
            return false;
        }
        $this->data_task->set_action('insert');
        foreach ($data as $key => $val) {
            $this->insert($key, $val);
        }
        return $this->m_app->m_server->process_database($this->data_task, $this, null, null);
    }

    public function getrecord($where)
    {
        $this->data_task->set_action('select');
        if (is_array($where) && !empty($where)) {
            $this->data_task->append_where($where);
        }
        $record = $this->m_app->m_server->process_database($this->data_task, $this, null, null);
        if (is_null($record)) {
            return false;
        }
        $record = $this->data_task->format_model($record);
        return $record;
    }

    public function updateplayerbyhour($data)
    {
        $this->data_task->set_action('update_value');
        return $this->m_app->m_server->process_database($this->data_task, $this, $data, null);
    }


    public function getmoneydatasum($app,$playerid,$param = null){

        $task = new agentsstatisticshourdatatask();
        $task->set_action('select_sum');
        if($playerid){
            $task->append_where(array('statistics_player_id'=>$playerid));
        }
        if($param){

            $task->append_where(array('statistics_time'=>[['>=', $param['start_time']],['<', $param['end_time']]]   ));
//            $task->append_where(array('statistics_time'=>['<=', $param['start_time']]  ));
//            $task->append_where(array('statistics_add_time'=>['>=', $param['end_time']]  ));
        }
        if (isset($this->m_app->m_server) && $this->m_app->m_server) {
            $app = $this->m_app;
        }
        return $app->m_server->process_database($task, $this, ['sum' => 'statistics_money_data','columns' =>'statistics_player_id'], null);

    }

}

?>