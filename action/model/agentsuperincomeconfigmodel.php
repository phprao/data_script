<?php

/**
 * 特代分成比例表
 * Class agentsuperincomeconfigmodel
 * @author ChangHai Zhan
 */
class agentsuperincomeconfigmodel extends basicdatamodel
{
    /**
     * 分成存储比例
     */
    const super_share_scale = 10000;

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
     * 获取代理list
     * @param $super_agent_id
     * @param $app
     * @return mixed
     */
    public function get_agent_super_list($super_agent_id, $app = null)
    {
        if (isset($this->m_app->m_server) && $this->m_app->m_server) {
            $app = $this->m_app;
        }
        $task = new agentsuperincomeconfigdatatask();
        $task->set_action('select_list');
        $task->append_where(['super_agent_id' => $super_agent_id]);
        $task->set_other('order by super_condition desc');
        if (!$model = $app->m_server->process_database($task, null, null, null)) {
            $task = new agentsuperincomeconfigdatatask();
            $task->set_action('select_list');
            $task->append_where(['super_agent_id' => 0]);
            $task->set_other('order by super_condition desc');
            $model = $app->m_server->process_database($task, null, null, null);
        }
        return $model;
    }

    /**
     * @param $config
     * @return array
     */
    public function to_array($config)
    {
        $data = [];
        foreach ($config as $value) {
            $data[$value->get('super_id', 0)] = [
                'super_condition' => $value->get('super_condition', 0),
                'super_condition_compare' => $value->get('super_condition_compare', '<'),
                'super_share' => $value->get('super_share', 0),
            ];
        }
        return $data;
    }

    /**
     * 比较条件获取配置
     * @param $config
     * @param $total
     * @return int
     */
    public function condition_compare($config, $total)
    {
        foreach ($config as $value) {
            $super_condition = $value['super_condition'];
            $super_condition_compare = $value['super_condition_compare'];
            $super_share = $value['super_share'];
            switch ($super_condition_compare) {
                case '<':
                    if ($super_condition < $total) {
                        return $super_share;
                    }
                    break;
                case '<=':
                    if ($super_condition <= $total) {
                        return $super_share;
                    }
                    break;
                default:
                    return 0;
                    break;
            }
        }
        return 0;
    }

    /**
     * 真实比例
     * @param $super_share
     * @return float|int
     */
    public function get_super_share($super_share)
    {
        return $super_share / self::super_share_scale;
    }
}