<?php

/**
 * 货币兑换比例信息
 * Class agentinfomodel
 * @author ChangHai Zhan
 */
class moneyrateinfomodel extends basicdatamodel
{
    /**
     * 金币
     */
    const money_rate_type_gold = 1;
    /**
     * 元
     */
    const money_rate_unit_type_yuan = 3;
    /**
     * 角
     */
    const money_rate_unit_type_jiao = 2;
    /**
     * 分
     */
    const money_rate_unit_type_fen = 1;

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
     * 获取货币兑换比例信息
     * @param $rate_type
     * @param $app
     * @return mixed
     */
    public function get_money_rate_info_by_rate_type($rate_type = self::money_rate_type_gold, $app = null)
    {
        $task = new moneyrateinfodatatask();
        $task->set_action('select');
        $task->append_where(['money_rate_type' => $rate_type]);
        if (isset($this->m_app->m_server) && $this->m_app->m_server) {
            $app = $this->m_app;
        }
        return $app->m_server->process_database($task, $this, null, null);
    }

    /**
     * 钱的转换 分
     * @param $money
     * @param array $params
     * @return int
     */
    public function get_money_to_rmb($money, $params)
    {
        $money_rate_value = 0;
        if (isset($params['money_rate_value'])) {
            $money_rate_value = $params['money_rate_value'];
        }
        $money_rate_unit = 0;
        if (isset($params['money_rate_unit'])) {
            $money_rate_unit = $params['money_rate_unit'];
        }
        $money_rate_unit_type = 0;
        if (isset($params['money_rate_unit_type'])) {
            $money_rate_unit_type = $params['money_rate_unit_type'];
        }
        if ($money_rate_value && $money_rate_unit) {
            switch ($money_rate_unit_type) {
                case self::money_rate_unit_type_fen:
                    return floor(($money * $money_rate_unit * 100) / $money_rate_value);
                    break;
                case self::money_rate_unit_type_jiao:
                    return floor(($money * $money_rate_unit * 10) / $money_rate_value);
                    break;
                case self::money_rate_unit_type_yuan:
                    return floor(($money * $money_rate_unit) / $money_rate_value);
                    break;
                default:
                    return 0;
                    break;
            }
        }
        return 0;
    }
}