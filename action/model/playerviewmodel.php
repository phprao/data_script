<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/17
 * Time: 20:19
 */
class playerviewmodel extends basicdatamodel
{

    protected $m_app;

    public function __construct($app)
    {
        parent::__construct();
        $this->m_app = $app;
        $this->data_task = new playerviewtask();
    }

    public function getinfobyid($where)
    {
        $this->data_task->set_action('select');
        if (is_array($where) && !empty($where)) {
            $this->data_task->append_where($where);
        }
        $playerinfo = $this->m_app->m_server->process_database($this->data_task, $this, null, null);
        if (is_null($playerinfo)) {
            return false;
        }
        $playerinfo = $this->data_task->format_model($playerinfo);
        return $playerinfo;
    }

    /**
     * 批量查找用户信息
     * @param  [type] $where [description]
     * @return [type]        [description]
     */
    public function getinfobyidarr($where)
    {
        $this->data_task->set_action('select_list_in');
        if (is_array($where) && !empty($where)) {
            $this->data_task->append_where_list(['player_id' => $where], basicdatatask::$WHERE_TYPE_IN);
            $playerinfo = $this->m_app->m_server->process_database($this->data_task, $this, null, null);
            if (is_null($playerinfo)) {
                return false;
            }
            $return  = array();
            foreach($playerinfo as $key => $val){
                $temp = $this->data_task->format_model($val);
                $return[$key]['player_id']           = $temp['player_id'];
                $return[$key]['player_nickname']     = urldecode($temp['player_nickname']);
                $return[$key]['player_status']       = $temp['player_status'];
                $return[$key]['player_vip_level']    = $temp['player_vip_level'];
                $return[$key]['player_club_id']      = $temp['player_club_id'];
                $return[$key]['player_header_image'] = $temp['player_header_image'];
                $return[$key]['player_sex']          = $temp['player_sex'];
                $return[$key]['player_author']       = $temp['player_author'];
            }
            
            return $return;
        }
        return false;
    }
}