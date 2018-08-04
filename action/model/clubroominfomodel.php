<?php

/*
 *  @desc:   俱乐部房间数据模型
 *  @author: xxm
 *  @email:  237886849@qq.com
 *  @note:   所有文件命名以小写，所有子类名以小写
 *
 */

class clubroominfomodel extends basicdatamodel
{
    public static $ROOM_TYPE_SIT_DESK = 1;
    public static $ROOM_TYPE_ROB_DESK1 = 2;
    public static $ROOM_TYPE_ROB_DESK2 = 3;
    public static $ROOM_TYPE_BUILD_ROOM = 4;
    public static $ROOM_DESK_NO = -1;
    public static $ROOM_DESK_MAX = 255;

    public function get_club_room()
    {
        $club_room_task = new clubroomlistredistask();
        $club_room_task->set_action('hmget');
        //$club_room = new basicdatamodel();
        //$club_room->insert('club_room_id',$club_room_id);
        //$club_room->insert('club_room_club_id',$club_room_club_id);
        //获取俱乐部房间信息
        $club_room_info = $this->m_app->m_server->process_redis($club_room_task, $this, null, null);

        if (is_null($club_room_info)) {
            return false;
        }
        $club_room_info->copy($this);
        return true;
    }

    public function check_data_invalid()
    {
        if ($this->get('club_room_id', 0) == 0) {
            return true;
        }
        return false;
    }

    public function get_club_room_list()
    {
        $data_task = new clubroomlistdatatask();
        $data_task->set_action('select_list');
        $club_id = $this->get('club_room_club_id', 0);
        $game_id = $this->get('club_room_game_id', 0);
        $rule_id = $this->get('club_room_rule_id', 0);
        $room_level = $this->get('club_room_level', 0);
        $room_type = $this->get('club_room_type', 0);
        $data_task->append_where(array('club_room_club_id' => $club_id, 'club_room_game_id' => $game_id, 'club_room_is_work' => 1));

        if ($rule_id > 0) {
            $data_task->append_where(array('club_room_rule_id' => $rule_id));
        }
        if ($room_level > 0) {
            $data_task->append_where(array('club_room_level' => $room_level));
        }
        if ($room_type > 0) {
            $data_task->append_where(array('club_room_type' => $room_type));
        }
        //$data_task->set_other('limit '.$index.',100');

        $room_list = $app->m_server->process_database($data_task, null, null, null);
    }

    //get club room desk pos
    protected function get_club_room_desk_pos(basicdi $app, $club_room_club_id, $club_room_id, $desk_size)
    {
        $task = new utilityredistask();
        $task->set_action('hmget_fields');
        $task->set_redis_keys('data_info:club_room:' . $club_room_club_id . ':' . $club_room_id, 'desk_index');
        $desk_pos = $app->m_server->process_redis($task, null, null, 0);
        $desk_pos = $desk_pos % $desk_size;
        if ($desk_pos < 0) {
            $desk_pos = 0;
        }
        return $desk_pos;
    }

    protected function set_club_room_desk_pos(basicdi $app, $club_room_club_id, $club_room_id, $desk_size, $pos_value)
    {
        $task = new utilityredistask();
        $task->set_redis_keys('data_info:club_room:' . $club_room_club_id . ':' . $club_room_id, 'desk_index');
        $task->set_action('hmset');
        $desk_pos = $pos_value % $desk_size;
        if ($desk_pos < 0) {
            $desk_pos = 0;
        }
        $app->m_server->process_redis($task, null, $desk_pos, 0);
    }

    //rob club desk 在俱乐部房间搓一个桌子(成功,返回一个俱乐部桌子号)
    public function rob_club_desk($club_desk_info)
    {
        $app = $this->m_app;
        $club_room_info = $this;

        $club_room_club_id = $club_desk_info['club_room_club_id'];
        $club_room_id = $club_desk_info['club_room_id'];
        $club_desk_id = $club_desk_info['club_desk_id'];
        $club_desk_rule_id = $club_desk_info['club_desk_rule_id'];

        //$ues_desk = $this->get_club_room_desk_no($app,$club_room_info);

        $desk_size = $club_room_info->get('club_room_desk_count', 0);
        /*$task = new utilityredistask();
        $task->set_action('hmget');
        $task->set_redis_keys('data_info:club_room:'.$club_room_club_id.':'.$club_room_id,'desk_index');
        $desk_pos = $app->m_server->process_redis($task,null,null,0);
        */
        $desk_pos = $this->get_club_room_desk_pos($app, $club_room_club_id, $club_room_id, $desk_size);
        //$desk_pos = $desk_pos %  $desk_size;
        //var_dump($desk_pos);
        //var_dump($desk_size);echo "=======================";
        $desk_no = clubroominfomodel::$ROOM_DESK_NO;
        $club_room_type = $club_room_info->get('club_room_type', 0);

        $club_desk_data = array();
        $club_desk_info['club_room_id'] = $club_room_id;
        $club_desk_info['club_room_club_id'] = $club_room_club_id;
        $club_desk_info['club_room_rule_id'] = $club_desk_rule_id;
        $club_desk_info['club_desk_id'] = $desk_size;
        $club_desk_info['desk_station'] = clubroominfomodel::$ROOM_DESK_MAX;
        //2:3缺1,3:是优先坐一桌,4:开包间
        switch ($club_room_type) {
            case clubroominfomodel::$ROOM_TYPE_ROB_DESK1:
                $desk_limit = clubroominfomodel::$ROOM_DESK_MAX;
                $desk_cur = $desk_pos;
                for ($i = 0; $i < $desk_size; $i++) {
                    $desk_index = ($desk_cur + $i) % $desk_size;
                    if ($club_desk_id == $desk_index) continue;//var_dump($desk_cur);
                    //if(in_array($desk_index, $ues_desk)) continue;
                    $club_desk_info['club_desk_id'] = $desk_index;
                    $club_desk_info['desk_station'] = $desk_limit;
                    $limit = $this->check_invalid_station($app, $club_desk_info);//var_dump($limit);var_dump($desk_index);
                    if (0 == $limit || clubroominfomodel::$ROOM_DESK_MAX == $limit) continue;
                    if (1 == $limit) {
                        //var_dump($desk_limit);
                        //var_dump($desk_index);
                        $desk_no = $desk_index;
                        break;
                    } else if ($limit < $desk_limit) {
                        //var_dump($i);
                        //var_dump($limit);
                        //var_dump($desk_limit);
                        //var_dump($desk_index);
                        $desk_limit = $limit;
                        $desk_no = $desk_index;
                        //break;
                    }
                    //echo " desk ". $desk_index . ' _ ' .$limit . "</br>";
                }
                break;
            case clubroominfomodel::$ROOM_TYPE_ROB_DESK2:
                $desk_limit = clubroominfomodel::$ROOM_DESK_MAX;
                $desk_cur = $desk_pos;
                for ($i = 0; $i < $desk_size; $i++) {
                    $desk_index = ($desk_cur + $i) % $desk_size;
                    if ($club_desk_id == $desk_index) continue;
                    //if(in_array($desk_index, $ues_desk)) continue;
                    $club_desk_info['club_desk_id'] = $desk_index;
                    $club_desk_info['desk_station'] = $desk_limit;
                    $limit = $this->check_invalid_station($app, $club_desk_info);
                    if ($limit != clubroominfomodel::$ROOM_DESK_MAX && $limit > 0) {
                        $desk_no = $desk_index;
                        break;
                    }
                }
                break;
            default:
                # code...
                break;
        }
        //var_dump($desk_no);
        //$task->set_action('hmset');
        //$app->m_server->process_redis($task,null,$desk_no,0);
        $this->set_club_room_desk_pos($app, $club_room_club_id, $club_room_id, $desk_size, $desk_no);
        return $desk_no;
    }

    //
    public function find_can_use_club_desk_no($club_desk_info)
    {
        $app = $this->m_app;
        $club_room_info = $this;

        $club_room_club_id = $club_desk_info['club_room_club_id'];
        $club_room_id = $club_desk_info['club_room_id'];
        $club_desk_id = $club_desk_info['club_desk_id'];
        $club_desk_rule_id = $club_desk_info['club_desk_rule_id'];

        $desk_size = $club_room_info->get('club_room_desk_count', 0);

        $ues_desk = $this->get_club_room_desk_no($app, $club_room_info);

        $desk_pos = $this->get_club_room_desk_pos($app, $club_room_club_id, $club_room_id, $desk_size);

        $desk_no = clubroominfomodel::$ROOM_DESK_NO;

        $club_room_type = $club_room_info->get('club_room_type', 0);//var_dump($club_room_type);

        $club_desk_data = array();
        $club_desk_info['club_room_id'] = $club_room_id;
        $club_desk_info['club_room_club_id'] = $club_room_club_id;
        $club_desk_info['club_room_rule_id'] = $club_desk_rule_id;
        $club_desk_info['club_desk_id'] = $desk_size;
        $club_desk_info['desk_station'] = clubroominfomodel::$ROOM_DESK_MAX;
        //2:3缺1,3:是优先坐一桌,4:开包间
        $desk_limit = clubroominfomodel::$ROOM_DESK_MAX;
        $desk_cur = $desk_pos;
        for ($i = 0; $i < $desk_size; $i++) {

            $desk_index = ($desk_cur + $i) % $desk_size;
            if ($club_desk_id == $desk_index) continue;
            if (in_array($desk_index, $ues_desk)) continue;

            $club_desk_info['club_desk_id'] = $desk_index;
            $desk_info = $this->get_club_desk_info($app, $club_desk_info);
            if (!is_null($desk_info)) continue;

            $desk_no = $desk_index;
            break;
        }

        $this->set_club_room_desk_pos($app, $club_room_club_id, $club_room_id, $desk_size, $desk_no);
        return $desk_no;
    }

    //get club desk
    public function get_club_desk_info(basicdi $app, $club_desk_info)
    {
        $club_room_club_id = $club_desk_info['club_room_club_id'];
        $club_room_id = $club_desk_info['club_room_id'];
        $club_desk_id = $club_desk_info['club_desk_id'];
        $desk_task = new clubdeskinforedistask();
        $desk_task->set_action('hmget');
        $club_desk = new basicdatamodel();
        $club_desk->insert('club_desk_club_room_id', $club_room_id);
        $club_desk->insert('club_desk_club_id', $club_room_club_id);
        $club_desk->insert('club_desk_club_room_desk_no', $club_desk_id);
        $desk_info = $app->m_server->process_redis($desk_task, $club_desk, null, null);

        if (is_null($desk_info) ||
            $desk_info->get('club_desk_room_id', 0) == 0 ||
            $desk_info->get('club_desk_game_id', 0) == 0 ||
            $desk_info->get('club_desk_id', 0) == 0 ||
            $desk_info->get('club_desk_club_room_id', 0) == 0) {
            return null;
        }

        $room_id = $desk_info->get('club_desk_room_id', 0);
        $room_model = new roominfomodel($app);
        $room_model->insert('room_id', $room_id);
        if (!$room_model->get_data_model() || 0 == $room_model->get('room_is_open', 0)) {
            return null;
        }
        //var_dump($club_desk_info);
        return $desk_info;
    }


    //检测桌子是否存在，存在返回缺人数
    protected function check_invalid_station(basicdi $app, $club_desk_info)
    {
        $club_room_id = $club_desk_info['club_room_id'];
        $club_room_club_id = $club_desk_info['club_room_club_id'];
        $club_room_rule_id = $club_desk_info['club_room_rule_id'];
        $club_desk_id = $club_desk_info['club_desk_id'];
        $desk_station = $club_desk_info['desk_station'];


        $club_desk_info = array('club_room_club_id' => $club_room_club_id, 'club_room_id' => $club_room_id, 'club_desk_id' => $club_desk_id);
        $desk_info = $this->get_club_desk_info($app, $club_desk_info);
        //var_dump($desk_info);
        if (is_null($desk_info)) return $desk_station;

        $club_desk_rule_id = $desk_info->get('club_desk_rule_id', 0);
        if ($club_room_rule_id > 0 && $club_room_rule_id != $club_desk_rule_id) {
            return $desk_station;
        }
        //$desk_info
        $club_desk_members_count = $desk_info->get('club_desk_members_count', clubroominfomodel::$ROOM_DESK_MAX);//$desk_station
        $club_desk_members_cur = $desk_info->get('club_desk_members_cur', 0);
        $limit = $club_desk_members_count - $club_desk_members_cur;
        return $limit;
    }

    //====================================================================================

    public function get_room_list()
    {
        $app = $this->m_app;
        $club_room_info = $this;

        $game_id = $club_room_info->get('club_room_game_id', 0);
        $club_room_is_open = $club_room_info->get('club_room_is_open', 0);

        $room_model = new roominfomodel($app);
        $room_model->insert('room_game_id', $game_id);
        $room_model->insert('room_is_open', $club_room_is_open);
        $room_list = $room_model->get_room_list();

        return $room_list;

    }

    //get use club room desk no
    public function get_club_room_desk_no(basicdi $app, $club_room_info)
    {
        $model = new clubdeskinfomodel($app);
        $club_room_id = $club_room_info->get('club_room_id', 0);
        $model->insert('club_desk_club_room_id', $club_room_id);
        $desk_no_list = $model->get_club_room_desk_no();
        return $desk_no_list;
    }

    //get use room desk no
    public function get_room_desk_no(basicdi $app, $room_info)
    {
        $model = new clubdeskinfomodel($app);
        $room_id = $room_info->get('room_id', 0);
        $model->insert('club_desk_room_id', $room_id);
        $desk_no_list = $model->get_room_desk_no();
        //var_dump($desk_no_list);
        return $desk_no_list;
    }
    //---------------------------------------------------------------------------
    //创建一个实现桌子(返回游戏房间和桌子号)
    public function build_room_desk($club_desk_info)
    {
        $app = $this->m_app;
        $club_room_info = $this;

        $data = array();
        $data['result'] = false;
        $data['desk_no'] = clubroominfomodel::$ROOM_DESK_NO;
        $data['room_id'] = -1;
        $data['club_desk'] = null;
        $data['desc_info'] = '分配房间失败';

        $room_list = $club_room_info->get_room_list();
        if (is_null($room_list) || 0 == count($room_list)) {
            //$this->set_response_status(actioncode::$basicaction_club_desk_code + 3,'没有开服务器');
            $data['desc_info'] = '服务器还没有开启';
            return $data;
        }

        $lock_task = new basiclocktask();
        $lock_task->set_table_info('dc_club_desk');
        $app->m_server->process_database($lock_task, null, null, null);


        $game_id = $club_room_info->get('club_room_game_id', 0);
        $club_room_is_open = $club_room_info->get('club_room_is_open', 0);

        $club_id = $club_desk_info['club_room_club_id'];
        $club_room_id = $club_desk_info['club_room_id'];
        $club_desk_id = $club_desk_info['club_desk_id'];
        $club_desk_room_no = $club_desk_info['club_desk_room_no'];
        $club_desk_param = $club_desk_info['club_desk_param'];
        $club_desk_player_id = $club_desk_info['club_desk_player_id'];
        if ('' == $club_desk_param) {
            $club_desk_param = $club_room_info->get('club_room_desk_param', '{}');
        }

        $club_room_rule_id = $club_room_info->get('club_room_rule_id', 0);
        $club_room_type = $club_room_info->get('club_room_type', 0);

        if (clubroominfomodel::$ROOM_TYPE_SIT_DESK != $club_room_type) {
            //坐桌时有club_desk_id
            $club_desk_id = $this->find_can_use_club_desk_no($club_desk_info);
        }

        //if(clubroominfomodel::$ROOM_DESK_NO == $club_desk_id) {
        //	$club_desk_id = $this->find_can_use_club_desk_no($club_desk_info);
        //}
        if (clubroominfomodel::$ROOM_DESK_NO == $club_desk_id) {
            $data['desc_info'] = '没有空闲游戏服务器,请联系客服';
            return $data;
        }

        $room_pos = $this->get_room_pos($app, count($room_list), $game_id, $club_room_is_open);
        $room_id = 0;
        $desk_no = 0;
        $members_count = $club_room_info->get('club_room_desk_members_count', 0);
        $result = false;
        $club_desk = null;
        //var_dump($room_pos);
        for ($i = 0; $i < count($room_list); $i++) {
            $pos = ($room_pos + $i) % count($room_list);
            $this->set_room_pos($app, count($room_list), $game_id, $club_room_is_open, $pos);
            $room_info = $room_list[$pos];
            $desk_result = $this->build_room_desk_no($app, $room_info, $game_id, $club_room_is_open);
            if ($desk_result['result']) {
                $result = true;
                $room_id = $desk_result['room_id'];
                $desk_no = $desk_result['desk_no'];
                break;
            }

        }
        if ($result) {
            //
            $model = new basicdatamodel();
            $model->insert('club_desk_club_id', $club_id);
            $model->insert('club_desk_club_room_id', $club_room_id);
            $model->insert('club_desk_club_room_desk_no', $club_desk_id);
            $model->insert('club_desk_room_id', $room_id);
            $model->insert('club_desk_desk_no', $desk_no);
            $model->insert('club_desk_game_id', $game_id);
            $model->insert('club_desk_is_work', 1);
            $model->insert('club_desk_members_count', $members_count);
            $model->insert('club_desk_members_cur', 0);
            $model->insert('club_desk_room_no', $club_desk_room_no);
            $model->insert('club_desk_player_id', $club_desk_player_id);
            $model->insert('club_desk_param', $club_desk_param);
            $model->insert('club_desk_rule_id', $club_room_rule_id);


            $desk_data_task = new clubdeskinfodatatask();
            $desk_data_task->set_action("insert");
            //var_dump($model);
            $club_desk_id = $app->m_server->process_database($desk_data_task, $model, null, null);
            do {
                if ($club_desk_id <= 0) {
                    $result = false;
                    break;
                }

                $model->insert('club_desk_id', $club_desk_id);
                //var_dump($model);
                $desk_redis_task = new clubdeskinforedistask();
                $desk_redis_task->set_action("hmset");
                $app->m_server->process_redis($desk_redis_task, $model, null, null);
                $club_desk = $model;
            } while (false);
        }

        $data['result'] = $result;
        $data['desk_no'] = $desk_no;
        $data['room_id'] = $room_id;
        $data['club_desk'] = $club_desk;
        return $data;
    }

    //房间下标
    protected function get_room_pos(basicdi $app, $room_size, $club_game_id, $club_room_is_open)
    {
        $task = new utilityredistask();
        $task->set_action('incrby');
        $task->set_redis_keys('data_info:room_data:' . $club_game_id . ':room_pos:room_open_' . $club_room_is_open, 'room_index');
        $room_pos = $app->m_server->process_redis($task, null, 1, 0);
        $room_pos = $room_pos % $room_size;
        $task->set_action('hmset');
        if ($room_pos < 0) {
            $room_pos = 0;
        }
        //$task->set_redis_keys('data_info:room_data','room_index');
        $app->m_server->process_redis($task, null, $room_pos, 0);
        return $room_pos;
    }

    protected function set_room_pos(basicdi $app, $room_size, $club_game_id, $club_room_is_open, $pos_value)
    {
        $task = new utilityredistask();
        $task->set_redis_keys('data_info:room_data:' . $club_game_id . ':room_pos:room_open_' . $club_room_is_open, 'room_index');
        //$room_pos = $app->m_server->process_redis($task,null,1,0);
        $room_pos = $pos_value % $room_size;
        $task->set_action('hmset');
        if ($room_pos < 0) {
            $room_pos = 0;
        }
        //$task->set_redis_keys('data_info:room_data','room_index');
        $app->m_server->process_redis($task, null, $room_pos, 0);
    }

    //桌子下标
    protected function get_room_desk_no_pos(basicdi $app, $desk_size, $club_game_id, $room_id)
    {
        $task = new utilityredistask();
        $task->set_action('incrby');
        $task->set_redis_keys('data_info:room_data:' . $club_game_id . ':desk_pos:room_id_' . $room_id, 'desk_index');
        //$task->set_redis_keys('data_info:room_data:'.$club_game_id.':'.$club_room_is_open.':'.$room_id,'desk_index');
        $desk_pos = $app->m_server->process_redis($task, null, 1, 0);
        $desk_pos = $desk_pos % $desk_size;
        $task->set_action('hmset');
        if ($desk_pos < 0) {
            $desk_pos = 0;
        }
        //var_dump($desk_pos);
        //$task->set_redis_keys('data_info:room_data','room_index');
        $app->m_server->process_redis($task, null, $desk_pos, 0);
        return $desk_pos;
    }

    protected function set_room_desk_no_pos(basicdi $app, $desk_size, $club_game_id, $room_id, $pos_value)
    {
        $task = new utilityredistask();
        $task->set_redis_keys('data_info:room_data:' . $club_game_id . ':desk_pos:room_id_' . $room_id, 'desk_index');
        $desk_pos = $pos_value % $desk_size;
        $task->set_action('hmset');
        if ($desk_pos < 0) {
            $desk_pos = 0;
        }
        //var_dump($desk_pos);
        //$task->set_redis_keys('data_info:room_data','room_index');
        $app->m_server->process_redis($task, null, $desk_pos, 0);
    }

    //判断房间id和桌子号是否已经使用
    protected function check_room_desk_invalid(basicdi $app, $room_id, $desk_no)
    {
        $data_task = new clubdeskinfodatatask();
        $data_task->set_action('select');
        $data_task->append_where(array('club_desk_room_id' => $room_id));
        $data_task->append_where(array('club_desk_desk_no' => $desk_no));
        $desk_info = $app->m_server->process_database($data_task, null, null, null);
        //var_dump($desk_no);
        if (is_null($desk_info)) {
            return true;
        }

        return false;

    }

    //查找游戏房间和空闲桌子号
    protected function build_room_desk_no(basicdi $app, $room_info, $game_id, $club_room_is_open)
    {
        $room_desk_count = $room_info->get('room_desk_count', 0);
        $room_id = $room_info->get('room_id', 0);
        $desk_pos = $this->get_room_desk_no_pos($app, $room_desk_count, $game_id, $room_id);
        //var_dump($desk_pos);
        $ues_desk = $this->get_room_desk_no($app, $room_info);

        $desk_no_value = 0;
        $result = false;
        for ($i = 0; $i < $room_desk_count; $i++) {
            $desk_no = ($desk_pos + $i) % $room_desk_count;
            /*if($this->check_room_desk_invalid($app,$room_id,$desk_no)) {
                $desk_no_value = $desk_no;
                $result = true;
                break;
            }*/
            if (!in_array($desk_no, $ues_desk)) {
                $desk_no_value = $desk_no;
                $result = true;
                break;
            }
        }

        $data = array();
        $data['result'] = $result;
        $data['desk_no'] = $desk_no_value;
        $data['room_id'] = $room_id;
        //var_dump($data);
        //var_dump($ues_desk);
        $this->set_room_desk_no_pos($app, $room_desk_count, $game_id, $room_id, $desk_no_value);
        return $data;
    }


    protected function check_room_no_invalid(basicdi $app, $room_no)
    {
        $data = new clubdeskinfodatatask();
        $data->set_action('select');
        $data->append_where(array('club_desk_room_no' => $room_no));
        $data->set_other('limit 1');
        $club_desk = $app->m_server->process_database($data, null, null, null);
        if (is_null($club_desk)) {
            return true;
        }
        return false;
    }

    //包间号
    public function build_room_no(basicdi $app)
    {
        $room_size = 100;
        $task = new utilityredistask();
        $task->set_action('incrby');
        $task->set_redis_keys('data_info:room_data:room_no', 'room_row');
        $row_pos = $app->m_server->process_redis($task, null, 1, 0);
        $row_pos = $row_pos % $room_size;
        $task->set_action('hmset');

        $task_pos = new utilityredistask();
        $room_no_value = 0;
        for ($i = 0; $i < 100; $i++) {
            $room_pos = ($row_pos + $i) % 100;
            $table = 'room_no_' . $room_pos . '.txt';
            $room_num_list = json_decode(file_get_contents("room_no/" . $table), true);

            $app->m_server->process_redis($task, null, $room_pos, 0);

            $task_pos->set_action('incrby');
            $task_pos->set_redis_keys('data_info:room_data:room_no:item' . $room_pos, 'room_pos');
            $room_pos = $app->m_server->process_redis($task_pos, null, 1, 0);
            $desk_pos = 0;
            for ($j = 0; $j < 1000; $j++) {
                $desk_pos = ($room_pos + $j) % 1000;
                $room_no = $room_num_list[$desk_pos];
                if ($this->check_room_no_invalid($app, $room_no)) {
                    $room_no_value = $room_no;
                    break;
                }
            }
            $task_pos->set_action('hmset');
            $app->m_server->process_redis($task_pos, null, $room_pos, null);

            if (0 != $room_no_value) {
                break;
            }
        }

        return $room_no_value;
    }
}

?>