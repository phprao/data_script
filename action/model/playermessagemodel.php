<?php
/**
 * 玩家消息信息模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/22
 * Time: 17:18
 * @author Zhanghui
 */

class playermessagemodel extends basicdatamodel {

    public function __construct(basicdi $app = null)
    {
        parent::__construct($app);
        $this->m_app = $app;
    }

    /**
     * 静态实例化
     * @param string $app
     * @param string $className
     * @return static active record model instance.
     */
    public static function model($app = null, $className = __CLASS__)
    {
        return new $className($app);
    }

    /**
     * 根据玩家ID查询消息信息列表
     * @param $player_id : 玩家ID
     * @param int $page : 页码
     * @param int $perpage : 每页显示数
     * @return mixed
     * @author Zhanghui
     */
    public function query_player_message_list_by_player_id($player_id, $page=1, $perpage=20)
    {
        $param = array();
        $param['player_id'] = $player_id;
        $param['page'] = $page;
        $param['perpage'] = $perpage;

        $db_task = new playermessagedatatask();
        $db_task->set_action('query_player_message_list');
        $player_message_list = $this->m_app->m_server->process_database($db_task, null, $param, null);

        return $player_message_list;
    }

    /**
     * 更新玩家消息信息
     * @param array $where
     * @param array $update
     * @return mixed
     * @author Zhanghui
     */
    public function update_player_message(array $where, array $update)
    {
        $db_task = new playermessagedatatask();
        $db_task->set_action('update_fields');
        $db_task->set_other('LIMIT 1');
        $db_task->append_where($where);

        foreach ($update as $key=>$value) {
            $this->update($key, $value);
        }

        return $this->m_app->m_server->process_database($db_task, $this, null, null);
    }
}