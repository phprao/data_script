<?php

/*
 *  @desc:   player对象执行者
 *  @author: xxm
 *  @email:  237886849@qq.com
 *  @note:   所有文件命名以小写，所有子类名以小写
 *
 */

class playerdatatask extends basicdatatask
{

    public function __construct()
    {
        parent::__construct();
        /*
        $this->set_fields_default('player_id','player_id',0,'int');
        $this->set_fields_default('player_name','player_name','');
        $this->set_fields_default('player_nicknme','player_nicknme','');
        $this->set_fields_default('player_password','player_password','');
        $this->set_fields_default('player_phone','player_phone','');
        $this->set_fields_default('player_pcid','player_pcid','');
        $this->set_fields_default('player_status','player_status',0);
        $this->set_fields_default('player_vip_level','player_vip_level',0);
        $this->set_fields_default('player_resigter_time','player_resigter_time',time());
        $this->set_fields_default('player_robot','player_robot',0);
        $this->set_fields_default('player_guest','player_guest',0);
        $this->set_fields_default('player_icon_id','player_icon_id',0);
        $this->set_fields_default('player_money','player_money',0);
        */
        basicfields::player_fields($this);
        $this->set_data_table_info('dc_player', 'player_id');

    }

    public function on_data_task(basicmysql $db, basicmodel $model = null, $param, $default)
    {
        /*
        if('select' == $this->m_action) {
            return $this->find_model($db,$model,$default);
        }else if('insert' == $this->m_action) {
            return $this->create_model($db,$model);
        }else if('delete' == $this->m_action) {
            return $this->delete_model($db,$model);
        }
        */
        return parent::on_data_task($db, $model, $param, $default);
    }

    /*
    protected function find_player(basicmysql $db,playerinfo $player =null,$default) {
        if(is_null($db)) {
            return $default;
        }
        $result = $db->select('dc_player','*',$this->m_where,$this->m_other);
        if(is_null($result) || 0 == count($result)) {
            return $default;
        }
        $data = $result[0];
        //var_dump($this->m_data);
        $this->m_data = $this->parse_model($player,$data);
        return $this->m_data;
    }
    protected function update_player(basicmysql $db) {
        if(is_null($db)) {
            return null;
        }
    }


    protected function create_player(basicmysql $db,playerinfo $player) {
        if(is_null($db) || is_null($player)) {
            return 0;
        }
        $data = $this->format_model($player);
        //var_dump($data);
        $result = $db->insert('dc_player',$data);
        //$db->
        //var_dump($result);
        return $result;
    }

    protected function delete_player(basicmysql $db,playerinfo $player) {
        if(is_null($db) || is_null($player)) {
            return 0;
        }

        $sql = 'delete from dc_player where '.$this->m_fields['player_id'] . '= ' .$player->get('player_id',0);
        //var_dump($sql);
        $result = $db->query($sql);
        return $result;
    }
    */
}

?>