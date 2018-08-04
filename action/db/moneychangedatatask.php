<?php

/*
 *  @desc:   货币改变对象执行者
 *  @author: xxm
 *  @email:  237886849@qq.com
 *  @note:   所有文件命名以小写，所有子类名以小写
 *
 */

class moneychangedatatask extends basicdatatask
{
    public function __construct()
    {
        parent::__construct();
        basicfields::money_change_fields($this);
        $this->set_data_table_info('dc_change_money_info', 'change_money_id');

    }

    public function on_data_task(basicmysql $db, basicmodel $model = null, $param, $default)
    {
        if ('select_sum' == $this->m_action) {
            return $this->select_sum($db, $model, $param, $default);
        } elseif ('get_list' == $this->m_action) {
            return $this->get_list($db, $model, $param, $default);
        }
        return parent::on_data_task($db, $model, $param, $default);
    }


    protected function select_sum(basicmysql $db, basicmodel $model = null, $param, $default)
    {
        //		    print_r($default); die;
        if (is_null($db)) {
            return $default;
        }
        $columns = '';
        if (!isset($param['sum'])) {
            return $default;
        } else {
            $columns = 'sum(' . $param['sum'] . ') as total';
        }
        if (isset($param['columns'])) {
            $columns .= ',' . $param['columns'];
        }
        $result = null;
        if (empty($this->m_where)) {
            $sql = 'select ' . $columns . ' from ' . $this->m_table_name . ' ' . $this->m_other;
            $data = $db->find($sql);
            if (is_null($data) || 0 == count($data)) {
                return $default;
            }
            $this->m_data = $data[0];
            return $this->m_data['total'];
        } else {
            $cond = '';
            foreach ($this->m_where as $k => $v) {
                if (is_array($v) && isset($v[0], $v[1])) {
                    if (($v[0] == 'in' || $v[0] == 'IN') && is_array($v[1])) {
                        $cond .= "`$k` IN ('" . implode("','", $v[1]) . "') AND ";
                    } elseif (!is_array($v[1])) {
                        $cond .= "`$k` $v[0] '$v[1]' AND ";
                    }
                } else {
                    $cond .= "`$k` = '$v' AND ";
                }
            }
            $cond = substr($cond, 0, strlen($cond) - 5);
            $sql = "SELECT $columns FROM `{$this->m_table_name}` WHERE $cond $this->m_other";
            $data = $db->find($sql);
            if (is_null($data) || 0 == count($data)) {
                return $default;
            }
            $this->m_data = $data;
            return $this->m_data;
        }
    }

    protected function get_list(basicmysql $db, basicmodel $model = null, $param, $default)
    {
        // 消耗记录
        // $sql = "select change_money_player_id,SUM(change_money_tax) as sum,change_money_money_type,money_rate_value from ".$this->m_table_name." c inner join dc_money_rate_info r on c.change_money_money_type = r.money_rate_type where change_money_type = 2 group by change_money_player_id";
        // $list = $db->find($sql);
        // if(empty($list)){
        //     return array();
        // }
        // $player_id_arr = array();
        // foreach ($list as $key => $val) {
        //     array_push($player_id_arr,$val['change_money_player_id']);
        // }
        // $player_ids = join(',',$player_id_arr);

        // 代理信息
        $sql = "select p.*,a.* from dc_promoters_info p inner join dc_agent_info a on p.promoters_agent_id = a.agent_id where p.promoters_player_id = " . $param['player_id'];
        $agent = $db->find($sql);

        // 玩家所在的代理信息
        // $agent_init = array();
        // foreach($agent as $val){
        //     if(!array_key_exists($val['promoters_player_id'],$agent_init)){
        //         $agent_init[$val['promoters_player_id']] = $val;
        //         // 分成比例
        //         $income_config = $this->getagentincomerate($db,$val);
        //         $agent_init[$val['promoters_player_id']]['income_count_level'] = $income_config['income_count_level'];
        //         $agent_init[$val['promoters_player_id']]['income_agent'] = $income_config['income_agent'];
        //         $agent_init[$val['promoters_player_id']]['income_level_one'] = $income_config['income_level_one'];
        //         $agent_init[$val['promoters_player_id']]['income_level_two'] = $income_config['income_level_two'];
        //         $agent_init[$val['promoters_player_id']]['income_level_three'] = $income_config['income_level_three'];
        //     }
        // }

        return $agent;

    }

    // 代理本身的分成比例-可能由上级代理，特代，公司设置
    protected function getagentincomerate(basicmysql $db, $param)
    {
        $level = array(
            'income_count_level' => 0,
            'income_agent' => 0,
            'income_level_one' => 0,
            'income_level_two' => 0,
            'income_level_three' => 0,
        );
        if ($param['agent_login_status'] == 0) {
            return $level;
        }
        
        $sql = "select * from dc_agent_income_config where income_agent_id = ".$param['agent_id']." order by income_count_level asc";
        $config = $db->find($sql);
        if(!$config){
            $sql = "select * from dc_agent_income_config where income_agent_id = ".$param['agent_top_agentid']." order by income_count_level asc";
            $config = $db->find($sql);
            if(!$config){
                $sql = "select * from dc_agent_income_config where income_agent_id = 0 order by income_count_level asc";
                $config = $db->find($sql);
            }
        }
        foreach ($config as $val) {
            if ($param['agent_promote_count'] >= $val['income_promote_count']) {
                $level = $val;
            }
        }
        return $level;
    }

}

?>