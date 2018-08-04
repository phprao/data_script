<?php

/*
 *  @desc:   代理收益统计 logic
 *  @author: xxm
 *  @email:  237886849@qq.com
 *  @note:   所有文件命名以小写，所有子类名以小写
 *
 */


class playerupdatestatmanager
{

    /*
     *	@desc: 统计逻辑(统计结果信息表是***)
     *  @param1:  app  主程序句柄
     *  @param2:  change_info : 金币消耗信息model对象
     *  @param3:  agent_player_info : 代理玩家信息model对象
     *  @return:  true:成功,false 失败
     */
    public function stat_logic(basicdi $app, basicmodelimpl $record, $param = null, $type = null)
    {

        if ($type == 1) {
            //充值回调添加统计
            $player_id = $record->get('order_player_id', 0);
            $change_money_tax = $record->get('order_price', 0);
            $agentinfo = agentplayer::agentplayerinfo($app, $player_id);
            $agent_parentid = $agentinfo->get('agent_parentid', 0);
            $mony_type = 3;

        } else {
            //正常消耗进来统计
            $player_id = $record->get('change_money_player_id', 0);
            $change_money_type = $record->get('change_money_type', 0);
            $change_money_tax = $record->get('change_money_tax', 0);
            //获取用户的上级代理
            $agentinfo = agentplayer::agentplayerinfo($app, $player_id);
            $agent_parentid = $agentinfo->get('agent_parentid', 0);
            if ($change_money_type == 2 || $change_money_type == 3) {
                $mony_type = 1;
            } else {
                return true;
            }
        }

        //将统计的值写到数据库
        addberstatistical::addstatistical($app, $player_id, $agent_parentid, $mony_type, abs($change_money_tax), $record);
        return true;
    }
}


?>