<?php

class utilitydatatask extends basicdatatask
{
    public function __construct()
    {
        parent::__construct();
        $this->set_data_table_info('dc_player', 'player_id');
    }

    public function on_data_task(basicmysql $db, basicmodel $model = null, $param, $default)
    {
        if ('select_player_id' == $this->m_action) {
            return $this->get_max_player_id($db, $default);
        }
        return parent::on_data_task($db, $model, $param, $default);
    }

    protected function get_max_player_id(basicmysql $db, $defalut)
    {
        $sql = 'select max(player_id) as id from dc_player limit 1';
        $result = $db->find($sql);
        if (empty($result)) {
            return $defalut;
        }

        //var_dump($result);

        $id = $result[0]['id'];//var_dump($id);
        return (int)$id;

    }

}

?>