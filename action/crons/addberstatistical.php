<?php

/*
 *  @desc:   clubinfo logic
 *  @author: xxm
 *  @email:  237886849@qq.com
 *  @note:   所有文件命名以小写，所有子类名以小写
 *
 */

class addberstatistical
{


    /**金币和人数统计表插入数据库
     * @param $app
     * @param $player_id 用户id
     * @param $statistical_agent_id 代理ID
     * @param $statistical_type 类型  1是金币 3 充值
     * @param $statistical_value 得到的数据值
     * @return bool
     */
    public static function addstatistical($app, $player_id, $statistical_agent_id, $statistical_type, $statistical_value)
    {
        $desk_data = new playerstatisticalinfodatatask();
        $desk_data->set_action('select');
        $desk_data->append_where(array('statistical_player_id' => $player_id));
//        $desk_data->append_where(array('statistical_agent_id' => $statistical_agent_id));
//        $desk_data->append_where(array('statistical_type' => $statistical_type));
        $userinfo = $app->m_server->process_database($desk_data, null, null, null);

        //如果有了就更新
        if ($userinfo) {
            $model = new playerstatisticalmodel();
            $task = new playerstatisticalinfodatatask();
            $task->set_action('update_fields');
            $task->append_where(array('statistical_id' => $userinfo->get('statistical_id', 0)));
            if ($statistical_type == 1) {
                $model->update('statistical_value', $userinfo->get('statistical_value', 0) + $statistical_value);
            } else if ($statistical_type == 3) {
                $model->update('statistical_top_up', $userinfo->get('statistical_top_up', 0) + $statistical_value);
            }
            $agentadd = $app->m_server->process_database($task, $model, null, null);
        } else {
            $model = new playerstatisticalmodel();
            $model->insert('statistical_player_id', $player_id);
            $model->insert('statistical_agent_id', $statistical_agent_id);
            if ($statistical_type == 1) {
                $model->insert('statistical_value', $statistical_value);
            } else if ($statistical_type == 3) {
                $model->insert('statistical_top_up', $statistical_value);
            }
            $model->insert('statistical_time', time());
            $desk_data_task = new playerstatisticalinfodatatask();
            $desk_data_task->set_action("insert");
            $agentadd = $app->m_server->process_database($desk_data_task, $model, null, null);

        }
        //更新消耗到上级总和
        self::agentsubtotal($app, $player_id, $statistical_agent_id, $statistical_type, $statistical_value);

        return true;
    }

    /**
     * @param $app
     * @param $player_id
     * @param $statistical_agent_id
     * @param $statistical_type
     * @param $statistical_value
     *
     * 添加更新本身用户上级代理的总和
     */

    public static function agentsubtotal($app, $player_id, $statistical_agent_id, $statistical_type, $statistical_value)
    {
        $parentid_playerid = agentplayer::agentinfo($app, $statistical_agent_id);
        $desk_data = new playerstatisticalinfodatatask();
        $desk_data->set_action('select');
        $desk_data->append_where(array('statistical_player_id' => $parentid_playerid->get('agent_player_id', 0)));
//        $desk_data->append_where(array('statistical_agent_id' => $parentid_playerid->get('agent_parentid', 0)));
//        $desk_data->append_where(array('statistical_type' => $statistical_type));
        $agentinfo = $app->m_server->process_database($desk_data, null, null, null);
        //如果有了就更新
        if ($agentinfo) {
            $model = new playerstatisticalmodel();
            $task = new playerstatisticalinfodatatask();
            $task->set_action('update_fields');
            $task->append_where(array('statistical_id' => $agentinfo->get('statistical_id', 0)));
            $model->update('statistical_sub_total_cost', $agentinfo->get('statistical_sub_total_cost', 0) + $statistical_value);
//            $model->update('statistical_time', time());
            $agentadd = $app->m_server->process_database($task, $model, null, null);
        } else {
            $model = new playerstatisticalmodel();
            $model->insert('statistical_player_id', $parentid_playerid->get('agent_player_id', 0));
            $model->insert('statistical_agent_id', $parentid_playerid->get('agent_parentid', 0));
//            $model->insert('statistical_type', $statistical_type);
            $model->insert('statistical_sub_total_cost', $statistical_value);
            $model->insert('statistical_time', time());
            $desk_data_task = new playerstatisticalinfodatatask();
            $desk_data_task->set_action("insert");
            $agentadd = $app->m_server->process_database($desk_data_task, $model, null, null);

        }
        return true;

    }


}

























