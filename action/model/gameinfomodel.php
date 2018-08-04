<?php

/*
 *  @desc:   gameinfo数据模型
 *  @author: xxm
 *  @email:  237886849@qq.com
 *  @note:   所有文件命名以小写，所有子类名以小写
 *
 */

class gameinfomodel extends basicdatamodel
{
    public function __construct(basicdi $app = null)
    {
        parent::__construct($app);
        $this->data_task = new gameinfodatatask();
    }

    public function get_one_model($where)
    {
        $this->data_task->set_action('select');
        $this->data_task->append_where($where);
        $game_record = $this->m_app->m_server->process_database($this->data_task, $this, null, null);
        if (is_null($game_record)) {
            return false;
        }
        $game_record = $this->data_task->format_model($game_record);
        return $game_record;
    }


}

?>