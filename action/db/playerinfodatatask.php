<?php

/*
 *  @desc:   playerinfo对象执行者
 *  @author: xxm
 *  @email:  237886849@qq.com
 *  @note:   所有文件命名以小写，所有子类名以小写
 *
 */

class playerinfodatatask extends basicdatatask
{
    public function __construct()
    {
        parent::__construct();
        basicfields::playerinfo_fields($this);
        $this->set_data_table_info('dc_player_info', 'id');
    }

    public function on_data_task(basicmysql $db, basicmodel $model = null, $param, $default)
    {
        /*if('select' == $this->m_action) {
            return $this->find_model($db,$model,$default);
        }else if('insert' == $this->m_action) {
            return $this->create_model($db,$model);
        }*/
        return parent::on_data_task($db, $model, $param, $default);;
    }

    /*
    protected function find_playerinfo(basicmysql $db,playerinfo $player =null,$default) {
        if(is_null($db)) {
            return $default;
        }
        $result = $db->select('dc_player_info','*',$this->m_where,$this->m_other);
        if(is_null($result) || 0 == count($result)) {
            return $default;
        }
        $data = $result[0];
        //var_dump($this->m_data);
        $this->m_data = $this->parse_model($player,$data);
        return $this->m_data;
    }

    protected function create_playerinfo(basicmysql $db,playerinfo $player) {
        if(is_null($db) || is_null($player)) {
            return 0;
        }
        $data = $this->format_model($player);
        //var_dump($data);
        $result = $db->insert('dc_player_info',$data);
        //$db->
        //var_dump($result);
        return $result;
    }
    */
}

?>