<?php

/*
 *  @desc:   clubinfo logic
 *  @author: xxm
 *  @email:  237886849@qq.com
 *  @note:   所有文件命名以小写，所有子类名以小写
 *
 */

class agentplayer
{
    /**
     * @param $app
     * @param $agent_id
     * 代理信息表
     */

    public static function agentplayerinfo($app, $player_id)
    {
        $desk_data_task = new agentinfodatatask();
        $desk_data_task->set_action('select');
        $desk_data_task->append_where(array('agent_player_id' => $player_id));
        $data = $app->m_server->process_database($desk_data_task, null, null, null);

        return $data;

    }


    public static function agentinfo($app, $parentid)
    {
        $desk_data_task = new agentinfodatatask();
        $desk_data_task->set_action('select');
        $desk_data_task->append_where(array('agent_id' => $parentid));
        $data = $app->m_server->process_database($desk_data_task, null, null, null);

        return $data;

    }

    /**
     * @param $app
     * @param $user_id
     * @return bool
     */
    public static function promotersinfo($app, $playerid)
    {
        $desk_data_task = new promotersinfodatatask();
        $desk_data_task->set_action('select_agent');
        $desk_data_task->append_where(array('promoters_player_id' => ['in', (array)$playerid]));
        $data = $app->m_server->process_database($desk_data_task, null, null, []);

        $pid = [];
        foreach ($data as $va) {
            $pid[$va['promoters_player_id']] = $va['promoters_parent_id'];
        }
        if ($pid) {
            return $pid;
        }

    }


}


