<?php

/**
 * +----------------------------------------------------------
 * date: 2018-01-15 15:51:16
 * +----------------------------------------------------------
 * author: Raoxiaoya
 * +----------------------------------------------------------
 * describe: 数据统计
 * +----------------------------------------------------------
 */
class statisticstotalmodel extends basicdatamodel
{
    public function __construct(basicdi $app = null)
    {
        parent::__construct($app);
        $this->data_task = new statisticstotaldatatask();
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

    public function get_one_log($where)
    {
        $this->data_task->set_action('select');
        $this->data_task->append_where($where);
        $round_record = $this->m_app->m_server->process_database($this->data_task, $this, null, null);
        if (is_null($round_record)) {
            return false;
        }
        $round_record = $this->data_task->format_model($round_record);
        return $round_record;
    }

    public function update_pay_sum($where)
    {
        $this->data_task->set_action('update_log_value');
        $re = $this->m_app->m_server->process_database($this->data_task, null, $where, null);
        return $re;
    }

    // 活跃玩家数统计
    public function set_activity_player_num($player_id,$last_login_time = 0){
        // 公司-天
        // 公司-小时
        // 渠道-天
        // 渠道-小时
        $time = time();
        $role_arr = [0,1];// 0-公司，1-渠道商
        $type_arr = [1,2];// 1-小时，2-天
        // 是否已经统计过了
        
        // $ptask = new playerinfodatatask();
        // $ptask->set_action('select');
        // $ptask->append_where(['player_id' => $player_id]);
        // $pinfo = $this->m_app->m_server->process_database($ptask, null, null, null);

        // $player_model = playermodel::model($this->m_app)->get_player_by_id($player_id);
        // $player_info_model = playerinfomodel::model($this->m_app)->get_player_info_by_player_id($player_id);
        // $player_info_model->copy($player_model);
        // $pinfo = playerredisblock::block($this->m_app)->get_player($player_model);

        // if(!$pinfo){
        //     return false;
        // }else{
            if($last_login_time >= strtotime(date('Y-m-d',$time))){
                return true;
            }
        // }
        // 查到渠道id
        $task = new agentinfodatatask();
        $task->set_action('select');
        $task->append_where(['agent_player_id' => $player_id]);
        $info = $this->m_app->m_server->process_database($task, null, null, null);
        if(!$info){
            return false;
        }else{
            $channel_id = $info->get('agent_top_agentid',0);
        }
        foreach($role_arr as $val){
            foreach($type_arr as $v){
                $where = [
                    'statistics_role_type'  =>$val,
                    'statistics_role_value' =>0,
                    'statistics_type'       =>$v
                ];
                if($val == 1){
                    $where['statistics_role_value'] = $channel_id;
                }
                $where['statistics_mode'] = 4;// 活跃玩家数
                $where['statistics_sum'] = 1;
                if($where['statistics_type'] == 1){
                    $where['statistics_timestamp'] = strtotime(date('Y-m-d H:00:00',$time));
                    $where['statistics_datetime'] = date('Y-m-d H',$time);
                }
                if($where['statistics_type'] == 2){
                    $where['statistics_timestamp'] = strtotime(date('Y-m-d',$time));
                    $where['statistics_datetime'] = date('Y-m-d',$time);
                }
                $where['statistics_update'] = date('Y-m-d H:i:s',$time);
                $where['statistics_time'] = $time;
                $where['statistics_money_rate'] = 0;
                // 是否已经存在
                $this->data_task->set_action('select');
                $w = [
                    'statistics_role_type'  =>$where['statistics_role_type'],
                    'statistics_role_value' =>$where['statistics_role_value'],
                    'statistics_mode'       =>$where['statistics_mode'],
                    'statistics_type'       =>$where['statistics_type'],
                    'statistics_timestamp'  =>$where['statistics_timestamp'],
                ];
                $this->data_task->append_where($w);
                $record = $this->m_app->m_server->process_database($this->data_task, $this, null, null);
                if($record){
                    $this->data_task->set_action('update_log_value');
                    $this->m_app->m_server->process_database($this->data_task, null, $where, null);
                }else{
                    $this->save_data_model($where);
                }
            }
        }
        return true;
    }

    // 剩余金币数统计
    public function set_last_coin_num($list){
        // 总公司-全部
        // 渠道-全部
        // 推广员(包括星级)-名下所有玩家的剩余金币累计
        $role_arr = [0,1,2];
        $type = 3;// 全部
        $mode = 8;//剩余金币数（最终值）
        $player_id = $list->get('player_id',0);
        $player_coins = $list->get('player_coins',0);
        $time = time();

        $task = new agentinfodatatask();
        $task->set_action('select');
        $task->append_where(['agent_player_id' => $player_id]);
        $info = $this->m_app->m_server->process_database($task, null, null, null);
        if(!$info){
            return false;
        }
        else{
            $channel_id = $info->get('agent_top_agentid',0);
            $star_id = $info->get('agent_parentid',0);
            if($channel_id == $star_id){
                $star_id = 0;
            }else{
                $task = new agentinfodatatask();
                $task->set_action('select');
                $task->append_where(['agent_id' => $star_id]);
                $pinfo = $this->m_app->m_server->process_database($task, null, null, null);
                if(!$pinfo){
                    $star_id = 0;
                }else{
                    // player_id
                    $star_id = $pinfo->get('agent_player_id',0);
                }
            }
        }
        if($star_id == 0){
            $role_arr = [0,1];
        }

        foreach($role_arr as $val){
            $where = [
                'statistics_role_type'  =>$val,
                'statistics_role_value' =>0,
                'statistics_type'       =>3
            ];
            if($val == 1){
                $where['statistics_role_value'] = $channel_id;
            }
            if($val == 2){
                $where['statistics_role_value'] = $star_id;
            }
            $where['statistics_mode'] = $mode;// 活跃玩家数
            $where['statistics_sum'] = $player_coins;
            $where['statistics_timestamp'] = 0;
            $where['statistics_datetime'] = '';
            $where['statistics_update'] = date('Y-m-d H:i:s',$time);
            $where['statistics_time'] = $time;
            $where['statistics_money_rate'] = 0;
            // 是否已经存在
            $this->data_task->set_action('select');
            $w = [
                'statistics_role_type'  =>$where['statistics_role_type'],
                'statistics_role_value' =>$where['statistics_role_value'],
                'statistics_mode'       =>$where['statistics_mode'],
                'statistics_type'       =>$where['statistics_type'],
                'statistics_timestamp'  =>$where['statistics_timestamp'],
            ];
            $this->data_task->append_where($w);
            $record = $this->m_app->m_server->process_database($this->data_task, $this, null, null);
            if($record){
                $this->data_task->set_action('update_log_value');
                $this->m_app->m_server->process_database($this->data_task, null, $where, null);
            }else{
                $this->save_data_model($where);
            }
        }

        return true;
        
    }

    // 所有推广员推广奖励：按天统计 
    public function promote_award_sum($incr_value){
        $time = time();
        $daydate = date('Y-m-d',$time);
        $daytime = strtotime($daydate);
        $arr = array(
            'statistics_role_type'   =>2,
            'statistics_role_value'  =>0,
            'statistics_mode'        =>9,
            'statistics_type'        =>2,
            'statistics_money_rate'  =>0,
            'statistics_timestamp'   =>$daytime
        );
        // 先查找是否存在
        $this->data_task->set_action('select');
        $this->data_task->append_where($arr);
        $log = $this->m_app->m_server->process_database($this->data_task, null, null, null);

        $arr['statistics_sum'] = $incr_value;
        $arr['statistics_update'] = date('Y-m-d H:i:s',$time);

        if($log){
            $re = $this->update_pay_sum($arr);
        }else{
            $arr['statistics_datetime'] = $daydate;
            $arr['statistics_time']     = $time;
            $re = $this->save_data_model($arr);
        }

        return $re;
    }

    // 清空原有数据
    public function clear_last_coin_num(){
        $where = [
            'statistics_mode'      =>8,
            'statistics_type'      =>3,
            'statistics_timestamp' =>0
        ];
        $this->data_task->set_action('update_fields');
        $this->data_task->append_where($where);
        $this->update('statistics_sum',0);
        $re = $this->m_app->m_server->process_database($this->data_task, $this, null, null);
        return $re;
    }
    /**
     * @param $condition
     * @param $app
     */
    public function get_agent_consumption($condition, $app)
    {
        $desk_data = new statisticstotaldatatask();
        $desk_data->set_action('select');
        $desk_data->append_where(['statistics_role_type' => $condition['statistics_role_type']]);
        $desk_data->append_where(['statistics_role_value' => $condition['statistics_role_value']]);
        $desk_data->append_where(['statistics_mode' => $condition['statistics_mode']]);
        $desk_data->append_where(['statistics_type' => $condition['statistics_type']]);
        if (isset($this->m_app->m_server) && $this->m_app->m_server) {
            $app = $this->m_app;
        }
        return $app->m_server->process_database($desk_data, null, null, null);
    }

}

?>

