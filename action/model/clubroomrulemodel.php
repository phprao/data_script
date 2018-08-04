<?php

class clubroomrulemodel extends basicdatamodel
{

    public function get_data_model()
    {
        $task = new clubruledatatask();
        $task->set_action('select');
        $club_room_rule_id = $this->get('club_room_rule_id', 0);
        $task->append_where(array('club_room_rule_id' => $club_room_rule_id));
        $rule_info = $this->m_app->m_server->process_database($task, $this, null, null);
        if (is_null($rule_info)) {
            return false;
        }
        $rule_info->copy($this);
        return true;
    }
}

?>