<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/17
 * Time: 9:52
 */
class ranking extends basicaction {
    private $limit = 20;

    protected function before()
    {
        if(!parent::before()){
            return false;
        }

        return true;
    }

    protected function logic(basicdi $app)
    {
        $date = date('Y-m-d H:i:s',time());
        $player_task = new playerviewtask();
        $player_task->set_action('select_all');
        $player_task->set_other('order by player_coins desc limit 0,' . $this->limit);
        $result = $app->m_server->process_database($player_task,null,null,null);

        if(empty($result)){
            return false;
        }

        $ranking_task = new rankingdatatask();
        $ranking_task->set_action('select_all');
        $rank = $app->m_server->process_database($ranking_task,null,null,null);
        if(!empty($rank)){
            //todo 如果有数据 先清空 后添加。
            $ranking_task->set_action('delete_all');
            $app->m_server->process_database($ranking_task,null,null,null);
        }

        $ranking_model = new rankingmodel();

        foreach($result as $key => $value){
            $ranking_model->insert('ranking_player_id',$value->get('player_id',0));
            $ranking_model->insert('ranking_nick_name',$value->get('player_nickname',''));
            $ranking_model->insert('ranking_player_image',$value->get('player_header_image',''));
            $ranking_model->insert('ranking_player_coins',$value->get('player_coins',0));
            $ranking_model->insert('ranking_coins_type',1);
            $ranking_model->insert('ranking_number',$key + 1);
            $ranking_model->insert('ranking_time',$date);

            $ranking_task->set_action('insert');
            $app->m_server->process_database($ranking_task,$ranking_model,null,null);
        }

    }

}