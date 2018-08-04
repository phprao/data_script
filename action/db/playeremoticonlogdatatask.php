<?php
/**
 * 玩家表情使用记录表
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/15
 * Time: 17:37
 * @author Zhanghui
 */

class playeremoticonlogdatatask extends basicdatatask {

    protected $extra_action;

    public function __construct()
    {
        parent::__construct();

        basicfields::playeremoticonlog_fields($this);

        $this->set_data_table_info('dc_player_emoticon_log', 'log_id');
    }

    public function on_data_task(basicmysql $db, basicmodel $model = null, $param, $default)
    {

        if (!empty($this->extra_action)) {
            $extra_action = $this->extra_action;
            return $this->$extra_action($db, $param);
        }

        return parent::on_data_task($db, $model, $param, $default); // TODO: Change the autogenerated stub
    }

    /**
     * 设置附加行为
     * @param $extra_action : 附加行为名称
     * @return bool
     * @author Zhanghui
     */
   public function set_extra_action($extra_action)
   {
        if (empty($extra_action) || !method_exists($this, $extra_action)) {
            return false;
        }

        $this->extra_action = $extra_action;
        return true;
   }

    /**
     * 查询当前玩家可对他人使用的表情信息
     * @param basicmysql $db : basicmysql实例
     * @param $param : 参数数组
     *      array(
     *          'player_id' => ''           // 当前玩家ID
     *          'other_player_id' => ''     // 其他玩家ID
     *          'player_vip_level' => ''    // 玩家VIP等级
     *          ...
     *      );
     * @return array
     * @throws Exception
     * @author Zhanghui
     */
    protected function query_available_to_other_emoticons(basicmysql $db, $param)
    {

        $player_vip_level = (int)$param['self_player_vip_level'];     // VIP等级
        $table_player_emocticon_log = $this->m_table_name;
        $db_task_emoticon = new emoticondatatask();
        $table_emoticon = $db_task_emoticon->m_table_name;
        $limit = "LIMIT 5";

        $table_config = array(
            'table_emoticon' => $table_emoticon,
            'table_player_emocticon_log' => $table_player_emocticon_log,
            'limit' => $limit
        );

        if ($player_vip_level) {    // 玩家为VIP
            $is_vip = 1;
            $list = $this->_query_vip_available_emoticons($db, $table_config);
        } else {    // 普通玩家或游客
            $is_vip = 0;
            $list = $this->_query_normal_available_emoticons($db, $table_config, $param);
        }

        $return = array('is_vip'=>$is_vip, 'emoticon_data'=>$list);
        return $return;
    }

    /**
     * 查询VIP玩家可用表情
     * @param basicmysql $db
     * @param array $table_config : table 配置
     * @return array|bool
     * @throws Exception
     * @author Zhanghui
     */
    private function _query_vip_available_emoticons(basicmysql $db, array $table_config)
    {
        $table_emoticon = $table_config['table_emoticon'];
        $limit = $table_config['limit'];
        $fields = "`emoticon_id`,`emoticon_limit_times`,`emoticon_price`";
        $orderby = "ORDER BY `emoticon_apply_group` DESC";                      // 优先排序VIP表情
        $sql = "SELECT {$fields} FROM `{$table_emoticon}` {$orderby} {$limit}";

        try {
            $list = $db->find($sql);
        } catch (Exception $e) {
            BASIC_EXCEPTION_HANDLER($e);
            throw new Exception('系统繁忙，请稍后再试[1]');
        }

        if ($list) {
            array_walk($list, function (&$item){
                $item['emoticon_available_times'] = $item['emoticon_limit_times'];
            });
        }

        return $list;
    }

    /**
     * 查询普通玩家或游客可用表情
     * @param basicmysql $db
     * @param array $table_config : table 配置信息
     * @param array $param : 请求参数数组
     * @return array
     * @throws Exception
     * @author Zhanghui
     */
    private function _query_normal_available_emoticons(basicmysql $db, array $table_config, array $param)
    {
        $table_emoticon = $table_config['table_emoticon'];
        $table_player_emocticon_log =  $table_config['table_player_emocticon_log'];
        $limit = $table_config['limit'];
        $player_id = $param['player_id'];
        $date = date('Y-m-d');

        $fields = "A.`emoticon_id`,A.`emoticon_is_limit`,A.`emoticon_limit_times`,A.`emoticon_price`,B.*";
        $join = "ON A.`emoticon_id`=B.`log_emoticon_id`";
        $where = "WHERE A.`emoticon_apply_group`=0";
        $fields_sub_query = "`log_emoticon_id`,`log_emoticon_times`";
        $where_sub_query = "WHERE `log_player_id`={$player_id} AND `log_date`='{$date}'";

        $sql = "SELECT {$fields} FROM `{$table_emoticon}` A LEFT JOIN 
                (SELECT {$fields_sub_query} FROM {$table_player_emocticon_log} {$where_sub_query}) B 
                {$join} {$where} {$limit}";

        try {
            $result = $db->find($sql);
        } catch (Exception $e) {
            BASIC_EXCEPTION_HANDLER($e);
            throw new Exception('系统繁忙，请稍后再试');
        }

        $list = array();
        if ($result) {
            foreach ($result as $key=>$value) {
                $list[$key]['emoticon_id'] = $value['emoticon_id'];
                $list[$key]['emoticon_limit_times'] = $value['emoticon_limit_times'];
                $list[$key]['emoticon_price'] = $value['emoticon_price'];
                if ($value['emoticon_is_limit'] == 1) {     // 限制次数
                    if (!empty($value['log_emoticon_times'])) {
                        $tmp_emoticon_available_times = $value['emoticon_limit_times'] - $value['log_emoticon_times'];
                        $list[$key]['emoticon_available_times'] = $tmp_emoticon_available_times > 0 ? (string)$tmp_emoticon_available_times : '0';
                    } else {    // 不限制次数
                        $list[$key]['emoticon_available_times'] = $value['emoticon_limit_times'];
                    }
                }
            }
        }

        return $list;
    }

    /**
     * 查询玩家单个互动表情使用次数
     * @param basicmysql $db
     * @param array $params : 查询条件
     *      array(
     *          'log_player_id_send'=>xxx       // 发送互动表情玩家ID(即为当前玩家ID)
     *          'log_player_id_receive'=>xxx    // 接收互动表情玩家ID
     *          'log_emoticon_id'=>xxx          // 互动表情ID
     *          'log_date'=>xxx                 // 发送日期  格式 年-月-日
     *      );
     * @return array|bool
     * @throws Exception
     * @author Zhanghui
     */
    protected function query_player_single_emoticon_used_times(basicmysql $db, $params)
    {
        $db_emoticon = new emoticondatatask();

        $table_player_emocticon_log = $this->m_table_name;
        $table_emoticon = $db_emoticon->m_table_name;
        $log_player_id = (int)$params['log_player_id'];
        $log_emoticon_id = (int)$params['log_emoticon_id'];
        $log_date = $params['log_date'];
        $self_player_vip_level = (int)$params['self_player_vip_level'];

        if ($self_player_vip_level) {   // VIP 不限制次数
            $emoticon_available_times = 10;
        } else {                        // 普通或游客玩家
            $fields = "A.`emoticon_limit_times`,B.`log_emoticon_times`";
            $fields_sub_query = "`log_emoticon_id`,IFNULL(`log_emoticon_times`, 0) AS `log_emoticon_times`";
            $on = "ON A.`emoticon_id`=B.`log_emoticon_id`";
            $where = "WHERE A.`emoticon_apply_group`=0 AND `emoticon_id`={$log_emoticon_id}";
            $where_sub_query = "WHERE `log_player_id`={$log_player_id} AND `log_emoticon_id`={$log_emoticon_id} 
                                AND `log_date`='{$log_date}'";
            $limit = "LIMIT 1";
            $sql = "SELECT {$fields} FROM `{$table_emoticon}` A LEFT JOIN 
                    (SELECT {$fields_sub_query} FROM `{$table_player_emocticon_log}` {$where_sub_query}) B {$on} {$where} {$limit}";

            try {
                $result = $db->find($sql);
            } catch (Exception $e) {
                BASIC_EXCEPTION_HANDLER($e);
                throw new Exception(actionerror::$basicmysql_exception_error);
            }

            $emoticon_available_times = $result[0]['emoticon_limit_times'] - $result[0]['log_emoticon_times'];
        }

        return $emoticon_available_times;
    }
}