<?php

/*
 *  @desc:   utilitymodel数据模型
 *  @author: xxm
 *  @email:  237886849@qq.com
 *  @note:   所有文件命名以小写，所有子类名以小写
 *
 */

class utilitymodel extends basicdatamodel
{

    //get data_info
    public function get_data_info(basicdi $app, $key_sub_name, $field_name, $default)
    {
        $task = new utilityredistask();
        $task->set_action('hmget_fields');
        $task->set_redis_keys('data_info:' . $key_sub_name, $field_name);
        $data = $app->m_server->process_redis($task, null, null, $default);
        return $data;
    }

    //set data_info
    public function set_data_info(basicdi $app, $key_sub_name, $field_name, $value)
    {
        $task = new utilityredistask();
        $task->set_redis_keys('data_info:' . $key_sub_name, $field_name);
        $task->set_action('hmset');
        $app->m_server->process_redis($task, null, $value, 0);
    }
}

?>