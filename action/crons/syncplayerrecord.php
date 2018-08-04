<?php
/**
 * 同步游戏录像数据
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/21
 * Time: 16:16
 * @author Zhanghui
 */

class syncplayerrecord extends basicaction {

    /**
     * @var basicdi
     */
    protected $app;

    /**
     * 同步数据源数据库
     * @var string
     */
    protected $source_db = 'dcmjdc2';

    /**
     * 同步数据源表前缀
     * @var string
     */
    protected $source_table_prefix = 'dc_record_data';

    /**
     * 同步数据源表主键
     * @var string
     */
    protected $source_table_primary_field = 'id';

    /**
     * 同步数据写入数据库
     * @var string
     */
    protected $input_db = 'dc_u3d';

    /**
     * 同步数据写入数据表前缀
     * @var string
     */
    protected $input_table_prefix = 'dc_player_record_bak_';

    /**
     * 数据库分表模数
     * @var int
     */
    protected $split_table_mod = 10;


    protected function before()
    {
        if(!parent::before()){
            return false;
        }

        return true;
    }


    protected function logic(basicdi $app)
    {
        $this->app = $app;

        // 验证库表
        try {
            $result = $this->_check_db_and_tables();
        } catch (Exception $e) {
            $this->set_response_status(actioncode::$basicaction_sync_player_record_error + 1, $e->getMessage());
            return false;
        }

        // 执行同步
        try {
            $result = $this->sync();
        } catch (Exception $e) {
            $this->set_response_status(actioncode::$basicaction_sync_player_record_error + 2, $e->getMessage());
            return false;
        }

        $this->format_response_data('data', $result);
        return true;
    }

    /**
     * 验证同步和写入的库表
     * @return mixed
     * @author Zhanghui
     */
    private function _check_db_and_tables()
    {
        $db_task = new syncplayerrecorddatatask();
        $db_task->set_action('check_db_and_tables');

        $db_table_list = $this->_get_db_and_tables();

        $result = $this->app->m_server->process_database($db_task, null, $db_table_list, null);

        return $result;
    }

    /**
     * 获取同步和写入的库表
     * @return array
     * @author Zhanghui
     */
    private function _get_db_and_tables()
    {
        $table_no = range(0, $this->split_table_mod - 1);
        $list = array(
            'source_db' => $this->source_db,
            'input_db' => $this->input_db
        );

        if ($table_no) {
            foreach ($table_no as $value) {
                $list['source_table_list'][] = $this->source_table_prefix.$value;
                $list['input_table_list'][] = $this->input_table_prefix.$value;
            }
        } else {
            $list['source_table_list'] = $this->source_table_prefix;
            $list['input_table_list'] = $this->input_table_prefix;
        }

        return $list;
    }

    protected function sync()
    {
        set_time_limit(0);

        $db_task = new syncplayerrecorddatatask();

        $table_config = $this->_get_db_and_tables();
        $source_db = $table_config['source_db'];
        $input_db = $table_config['input_db'];
        $source_table_list = $table_config['source_table_list'];
        $input_table_list = $table_config['input_table_list'];
        $return = $operate_source_table_list = array();

        $table_key = 1;
        $max_table_key = $this->split_table_mod - 1;
        $max_exec_time = 30*60;     // 循环最大执行时间
        $source_query_count = $input_writein_count = 0;
        $offset = 0;
        $length = 100;
        $start_time = time();

        $return['loop_step'] = $length;
        $return['source_table_data'] = array();
        $return['input_table_data'] = array();


        $cron_log = '============================= 同步玩家记录 '.__METHOD__.' START =============================';
        BASIC_LOG_CRON_HANDLER('crons', '%s', $cron_log);

        while (true) {

            $now = time();
            if ($now - $start_time > $max_exec_time) {
                break;
            }

            $source_table = $source_db.'.'.$source_table_list[$table_key];
            $input_table  = $input_db.'.'.$input_table_list[$table_key];

            // 查询源数据
            $source_data = $this->_query_source_table_data($db_task, $source_table, $offset, $length);
            if (!$source_data) {
                if ($table_key == $max_table_key) {
                    break;
                } else {
                    $offset = 0;
                    $source_query_count = 0;
                    $input_writein_count = 0;
                    $table_key++;
                    continue;
                }
            }

            // 组装写入数据
            $result_insert = $this->_assemble_input_insert_data($db_task, $source_data);

            // 写入数据不为空
            if ($result_insert['input_insert_data']) {
                // 写入到同步表
                $result_writein = $this->_writein_input_table($db_task, $input_table, $result_insert);

                //  写入成功 删除源数据表数据
                if ($result_writein) {
                    $delete_result = $this->_delete_source_table_data($db_task, $source_table, $result_writein);
                    $input_writein_count++;
                }
            }

            $source_query_count++;

            $return['source_table_data'][$table_key]['source_table'] = $source_table;
            $return['source_table_data'][$table_key]['source_query_count'] = $source_query_count;
            $return['input_table_data'][$table_key]['input_table'] = $input_table;
            $return['input_table_data'][$table_key]['input_write_count'] = $input_writein_count;
        }

        $return['start_time'] = $start_time;
        $return['end_time'] = time();

        $cron_log = '============================= 同步玩家记录 '.__METHOD__.' END   =============================';
        BASIC_LOG_CRON_HANDLER('crons', '%s', $cron_log);

        return $return;
    }

    /**
     * 查询源数据表数据
     * @param basicdatatask $task
     * @param $source_table : 源数据表
     * @param $offset : 偏移量
     * @param $length : 偏移数
     * @return mixed
     * @author Zhanghui
     */
    private function _query_source_table_data(basicdatatask $task, $source_table, $offset, $length)
    {
        $params = array(
            'source_table' => $source_table,
            'offset' => $offset,
            'length' => $length,
            'primary_field' => $this->source_table_primary_field
        );

        $task->set_action('_query_source_table_data');
        $result = $this->app->m_server->process_database($task, null, $params, null);

        // 写入crons日志
        $cron_log = array(
            'log_progress' => 1,
            'log_method' => __METHOD__,
            'log_params' => $params
        );
        BASIC_LOG_CRON_HANDLER('crons', '%s', json_encode($cron_log));

        return $result;
    }

    /**
     * 组装写入数据
     * @param basicdatatask $task
     * @param array $source_data : 源数据
     * @return array
     * @author Zhanghui
     */
    private function _assemble_input_insert_data(basicdatatask $task, array $source_data)
    {
        $input_insert_data = $source_desk_id_list = array();
        $table_club_desk = $this->input_db.".`dc_club_desk`";
        $table_club_desk_record = $this->input_db.".`dc_club_desk_record`";

        // 获取源数据表桌子ID列表
        foreach ($source_data as $key=>$value) {
            $source_desk_id_list[$value['id']] = $value['desk_id'];    // 源数据表主键ID为key  源数据表桌子ID为val
        }

        // 查询桌子详细信息
        $params = array(
            'table_club_desk' => $table_club_desk,
            'table_club_desk_record' => $table_club_desk_record,
            'source_desk_id_list' => $source_desk_id_list
        );
//        $task->set_action('_query_desk_info');
//        $result = $this->app->m_server->process_database($task, null, $params, null);

        // 若查询到桌子信息 则组装写入数据
//        if ($result) {
//            foreach ($result as $k=>$v) {
//                foreach ($source_data as $m=>$n) {
//                    if ($v['club_desk_id'] == $n['desk_id']) {
//                        $input_insert_data[$k]['record_bak_club_id'] = (int)$v['club_desk_club_id'];
//                        $input_insert_data[$k]['record_bak_club_room_id'] = (int)$v['club_desk_club_room_id'];
//                        $input_insert_data[$k]['record_bak_club_room_desk_no'] = (int)$v['club_desk_club_room_desk_no'];
//                        $input_insert_data[$k]['record_bak_game_id'] = (int)$v['club_desk_game_id'];
//                        $input_insert_data[$k]['record_bak_room_id'] = (int)$v['club_desk_room_id'];
//                        $input_insert_data[$k]['record_bak_room_no'] = (int)$v['club_desk_room_no'];
//                        $input_insert_data[$k]['record_bak_desk_id'] = (int)$v['club_desk_id'];
//                        $input_insert_data[$k]['record_bak_desk_no'] = (int)$v['club_desk_desk_no'];
//                        $input_insert_data[$k]['record_bak_player_id'] = (int)$v['club_desk_player_id'];
//                        $input_insert_data[$k]['record_bak_file'] = $n['name'];
//                    }
//                }
//            }
//        }

        foreach ($source_data as $m=>$n) {
            $input_insert_data[$m]['record_bak_club_id'] = 0;
            $input_insert_data[$m]['record_bak_club_room_id'] = 0;
            $input_insert_data[$m]['record_bak_club_room_desk_no'] = 0;
            $input_insert_data[$m]['record_bak_game_id'] = 0;
            $input_insert_data[$m]['record_bak_room_id'] = 0;
            $input_insert_data[$m]['record_bak_room_no'] = 0;
            $input_insert_data[$m]['record_bak_desk_id'] = (int)$n['desk_id'];
            $input_insert_data[$m]['record_bak_desk_no'] = 0;
            $input_insert_data[$m]['record_bak_player_id'] = 0;
            $input_insert_data[$m]['record_bak_file'] = $n['name'];
        }


        // 写入日志
        $cron_log = array(
            'log_progress' => 2,
            'log_method' => __METHOD__,
            'log_params' => $params,
        );
        BASIC_LOG_CRON_HANDLER('crons', '%s', json_encode($cron_log));

        $return = array(
            'input_insert_data' => $input_insert_data,
            'source_desk_id_list' => $source_desk_id_list
        );

        return $return;
    }

    /**
     * 写入到数据表
     * @param basicdatatask $task
     * @param $input_table
     * @param array $result_insert
     * @return mixed
     * @author Zhanghui
     */
    private function _writein_input_table(basicdatatask $task, $input_table, array $result_insert)
    {

        $input_insert_data = $result_insert['input_insert_data'];
        $source_desk_id_list = $result_insert['source_desk_id_list'];

        $input_desk_id_list = array();
        foreach ($input_insert_data as $key=>$value) {
            $input_desk_id_list[] = $value['record_bak_desk_id'];
        }

        $params = array(
            'input_table' => $input_table,
            'input_insert_data' => $input_insert_data,
        );
        $task->set_action('_writein_input_table');
        $result = $this->app->m_server->process_database($task, null, $params, null);


        // 写入日志
        $cron_log = array(
            'log_progress' => 3,
            'log_method' => __METHOD__,
            'log_params' => array('input_desk_id_list'=>$input_desk_id_list),
        );
        BASIC_LOG_CRON_HANDLER('crons', '%s', json_encode($cron_log));

        return $result ? $source_desk_id_list : array();
    }

    /**
     * 删除源数据表信息
     * @param basicdatatask $task
     * @param $source_table
     * @param array $delete
     * @return bool
     * @author Zhanghui
     */
    private function _delete_source_table_data(basicdatatask $task, $source_table, array $delete)
    {
        $params = array(
            'delete_source_desk_id_list' => $delete,
            'source_table' => $source_table
        );

        $task->set_action('_delete_source_table_data');
        $result = $this->app->m_server->process_database($task, null, $params, null);

        // 写入日志
        $cron_log = array(
            'log_progress' => 4,
            'log_method' => __METHOD__,
            'log_params' => $params,
        );
        BASIC_LOG_CRON_HANDLER('crons', '%s', json_encode($cron_log));

        return $result;
    }
}