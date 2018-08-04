<?php
/**
 * 玩家游戏记录模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/23
 * Time: 13:52
 * @author Zhanghui
 */

class playergamerecordmodel extends basicdatamodel {

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
     * 根据时间查询玩家游戏记录
     * @param array $params
     * @return mixed
     * @author Zhanghui
     */
    public function query_player_game_records_by_time(array $params)
    {
        $db_task = new gamebeatrecorddatatask();
        $db_task->set_action('query_player_game_records_by_time');

        return $this->m_app->m_server->process_database($db_task, $this, $params, null);
    }
}