<?php

/**
 * +----------------------------------------------------------
 * date: 2018-01-15 15:39:26
 * +----------------------------------------------------------
 * author: Raoxiaoya
 * +----------------------------------------------------------
 * describe: 战绩
 * +----------------------------------------------------------
 */
class gamebeatrecorddatatask extends basicdatatask
{
    public function __construct()
    {
        parent::__construct();
        basicfields::gamebeatrecord_fileds($this);
        $this->set_data_table_info('dc_game_beat_record', 'game_beat_id');

    }

    public function on_data_task(basicmysql $db, basicmodel $model = null, $param, $default)
    {
        if ('select_page_time' == $this->m_action) {
            return $this->select_page_time($db, $model, $param, $default);
        }
        if ('select_pagenum_time' == $this->m_action) {
            return $this->select_pagenum_time($db, $model, $param, $default);
        }
        if ('select_list_in' == $this->m_action) {
            return $this->select_list_in($db, $model, $param, $default);
        }
        if ('select_today_result' == $this->m_action) {
            return $this->select_today_result($db, $model, $param, $default);
        }
        if ('query_player_game_records_by_time' == $this->m_action) {
            return $this->query_player_game_record_by_time($db, $model, $param, $default);
        }

        return parent::on_data_task($db, $model, $param, $default);
    }

    public function select_pagenum_time(basicmysql $db, basicmodel $model = null, $param, $default)
    {
        $sql = "select count(*) as totalpage from dc_game_beat_record where game_beat_player_id = " . $param['player_id'];
        $sql .= " and game_beat_over_time > " . $param['start_time'];
        if($param['type'] == 0){
            $sql .= " and game_beat_room_no = 0";
        }elseif($param['type'] == 1){
           $sql .= " and game_beat_room_no > 0"; 
        }
        $re = $db->find($sql);
        return $re[0]['totalpage'];
    }

    public function select_page_time(basicmysql $db, basicmodel $model = null, $param, $default)
    {
        $sql = "select game_beat_board_id as bid,game_beat_game_name as gname,game_beat_over_time as otime,game_beat_readback AS video_file from dc_game_beat_record where game_beat_player_id = " . $param['player_id'];
        $sql .= " and game_beat_over_time > " . $param['start_time'];
        if($param['type'] == 0){
            $sql .= " and game_beat_room_no = 0";
        }elseif($param['type'] == 1){
            $sql .= " and game_beat_room_no > 0"; 
        }
        $sql .= " order by game_beat_over_time DESC ";
        $sql .= " limit " . $param['start'] . "," . $param['size'];
        $re = $db->find($sql);
        return $re;
    }

    public function select_list_in(basicmysql $db, basicmodel $model = null, $param, $default)
    {
        $sql = "select game_beat_board_id as bid,game_beat_player_id as pid,game_beat_player_nick as nick,game_beat_player_head as head,game_beat_score_value as score,game_beat_win_state win from dc_game_beat_record where game_beat_board_id IN ( " . $param['boards'] . " )";
        $re = $db->find($sql);
        return $re;
    }

    public function select_today_result(basicmysql $db, basicmodel $model = null, $param, $default)
    {
        // 今日总局数
        $sql1 = "select count(*) as num from dc_game_beat_record";
        $sql1 .= " where game_beat_player_id = " . $param['player_id'];
        $sql1 .= " and game_beat_over_time >= " . $param['start_time'];
        if($param['type'] == 0){
            $sql1 .= " and game_beat_room_no = 0";
        }elseif($param['type'] == 1){
            $sql1 .= " and game_beat_room_no > 0" ;
        }
        $re1 = $db->find($sql1);

        // 今日胜局数
        $sql2 = "select count(*) as win from dc_game_beat_record";
        $sql2 .= " where game_beat_player_id = " . $param['player_id'];
        $sql2 .= " and game_beat_over_time >= " . $param['start_time'];
        if($param['type'] == 0){
            $sql2 .= " and game_beat_room_no = 0";
        }elseif($param['type'] == 1){
            $sql2 .= " and game_beat_room_no > 0";
        }
        $sql2 .= " and game_beat_win_state = 1";
        $re2 = $db->find($sql2);

        // 今日战绩
        $sql3 = "select SUM(game_beat_score_value) as total from dc_game_beat_record";
        $sql3 .= " where game_beat_player_id = " . $param['player_id'];
        $sql3 .= " and game_beat_over_time >= " . $param['start_time'];
        if($param['type'] == 0){
            $sql3 .= " and game_beat_room_no = 0";
        }elseif($param['type'] == 1){
            $sql3 .= " and game_beat_room_no > 0";
        }
        $sql3 .= " and game_beat_score_type = " . $param['money_type'];
        $re3 = $db->find($sql3);

        return array('total_num' => $re1[0]['num'], 'win_num' => $re2[0]['win'], 'total_score' => isset($re3[0]['total']) ? $re3[0]['total'] : '0');
    }

    /**
     * 根据时间查询玩家游戏记录
     * @param basicmysql $db
     * @param basicmodel|null $model
     * @param $param
     * @param $default
     * @return array|bool
     * @throws Exception
     * @author Zhanghui
     */
    public function query_player_game_record_by_time(basicmysql $db, basicmodel $model = null, $param, $default)
    {
        $player_id = (int)$param['player_id'];
        $begin_time = (int)$param['begin_time'];
        $after_time = (int)$param['after_time'];
        $page = (int)$param['page'];
        $perpage = (int)$param['perpage'];
        $offset = ($page - 1)*$perpage;


        $table = $this->m_table_name;
        $where = "WHERE `game_beat_player_id`={$player_id} AND `game_beat_time`>=$begin_time AND `game_beat_time`<=$after_time";
        $order_by = "ORDER BY `game_beat_time` DESC";
        $limit = "LIMIT {$offset},{$perpage}";
        $sql = "SELECT * FROM {$table} {$where} {$order_by} {$limit}";

        try {
            $result = $db->find($sql);
        } catch (Exception $e) {
            BASIC_EXCEPTION_HANDLER($e);
            throw new Exception(actionerror::$basicmysql_exception_error);
        }

        return $result;
    }

}


?>