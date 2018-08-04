<?php

/*
 *  @desc:   俱乐部房间对象执行者
 *  @author: xxm
 *  @email:  237886849@qq.com
 *  @note:   所有文件命名以小写，所有子类名以小写
 *
 */

class clubroomlistdatatask extends basicdatatask
{
    public function __construct()
    {
        parent::__construct();
        basicfields::club_room_info_fields($this);
        $this->set_data_table_info('dc_view_club_room', 'club_room_id');
    }

    public function on_data_task(basicmysql $db, basicmodel $model = null, $param, $default)
    {
        if ('select_room_list' == $this->m_action) {
            return $this->select_room_list($db, $model, $default);
        }
        return parent::on_data_task($db, $model, $param, $default);
    }

    protected function select_room_list(basicmysql $db, basicmodel $model = null, $default)
    {
        if (is_null($db)) {
            return $default;
        }
        $sql = 'select distinct game_id from ' . $this->m_table_name . ' where club_id ' . $this->m_other;
        $result = $db->find($sql);
        if (is_null($result) || 0 == count($result)) {
            return $default;
        }

        $list = array();
        foreach ($result as $key => $data) {
            $item = $this->parse_model($model, $data);
            array_push($list, $item);
        }
        return $list;
    }
}

?>