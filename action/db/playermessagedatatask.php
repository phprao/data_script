<?php
/**
 * 玩家消息信息
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/22
 * Time: 17:21
 * @author Zhanghui
 */

class playermessagedatatask extends basicdatatask {

    public function __construct()
    {
        parent::__construct();

        basicfields::playermessage_fields($this);

        $this->set_data_table_info('dc_player_message', 'player_message_id');
    }

    public function on_data_task(basicmysql $db, basicmodel $model = null, $param, $default)
    {
        if ($this->check_extra_action()) {
            $extra_action = $this->m_action;
            return $this->$extra_action($db, $model, $param, $default);
        }

        return parent::on_data_task($db, $model, $param, $default); // TODO: Change the autogenerated stub
    }

    /**
     * 获取附加行为配置数组
     * @return array
     * @author Zhanghui
     */
    protected function get_extra_action()
    {
        $extra_action = array(
            'query_player_message_list'
        );

        return $extra_action;
    }

    /**
     * 检查附加行为
     * @return bool
     * @author Zhanghui
     */
    protected function check_extra_action()
    {
        $extra_action = $this->get_extra_action();
        if (empty($extra_action) || !in_array($this->m_action, $extra_action) || !method_exists($this, $this->m_action)) {
            return false;
        }
        return true;
    }

    /**
     * 查询玩家消息信息列表
     * @param basicmysql $db
     * @param basicmodel|null $model
     * @param $param
     * @param $default
     * @return array|bool
     * @throws Exception
     * @author Zhanghui
     */
    protected function query_player_message_list(basicmysql $db, basicmodel $model = null, $param, $default)
    {

        $player_id = (int)$param['player_id'];
        $page = (int)$param['page'];
        $perpage = (int)$param['perpage'];
        $offset = ($page-1) * $perpage;

        $db_task_message = new messagedatatask();

        $table_message = $db_task_message->m_table_name;
        $table_player_message = $this->m_table_name;
        $on = "ON A.`player_message_message_id`=B.`message_id`";
        $where = "WHERE A.`player_message_player_id`={$player_id} AND A.`player_message_is_delete`=0";
        $fields = "*";
        $order_by = "ORDER BY A.`player_message_create_time` DESC";
        $limit = "LIMIT {$offset},{$perpage}";

        $sql = "SELECT {$fields} FROM `{$table_player_message}` A LEFT JOIN `{$table_message}` B {$on} {$where} {$order_by} {$limit}";

        try {
            $result = $db->find($sql);
        } catch (Exception $e) {
            BASIC_EXCEPTION_HANDLER($e);
            throw new Exception(actionerror::$basicmysql_exception_error);
        }

        return $result;
    }


}