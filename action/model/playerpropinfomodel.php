<?php
/**
 * 用户背包信息model
 */
class playerpropinfomodel extends basicdatamodel
{
    /**
     * 代币类型对应的字段
     * @var string
     */
    public static $t_type = 'player_tokens';

	/**
     * clubplayermodel constructor.
     * @param $app
     */
    public function __construct($app)
    {
        parent::__construct();
        $this->m_app = $app;
    }

    /**
     * 静态实例化
     * @param string $app
     * @param string $className
     * @return static active record model instance.
     */
    public static function model($app = null, $className = __CLASS__) {
        return new $className($app);
    }

   /*
    * 我的背包信息
    */
	public function getpropinfobyplayer($player_id) { 
        $keys = $this->getrediskey($player_id);
        $redis_task = new playerpropredistask();      //redis查询
        $redis_task->set_action('hmget');
        $this->insert('player_id', $player_id);
        $list = empty($keys['list']) ? array() : $keys['list'];
        $datafromredis = array();
        foreach ($list as $key) {
            $arr = explode(':', $key);
            list($a,$prop_id,$c) = $arr;
            if($prop_id) {
                $this->insert('prop_id', $prop_id);
                $redisdata = $this->m_app->m_server->process_redis($redis_task, $this, null, null);
                if($redisdata) {
                    $propinfo = $this->getpropsinfo($prop_id);
                    $propname = $propinfo->get('prop_name','');
                    $redisdata->insert('prop_name',$propname);
                }
                $datafromredis[] = $redisdata;
            }
        }
		if(!$datafromredis) { //不存在查询数据库
			$task = new playerpropsdatatask();
	        $task->set_action('select_list');
	        $task->append_where(array('player_id' => $player_id));
	        $datafromdb = $this->m_app->m_server->process_database($task, null, null, null);
            if($datafromdb) { //数据库存在同步redis
                $redis_task->set_action('hmset');
                foreach($datafromdb as &$val) {
                    $prop_id = $val->get('prop_id',0);
                    $num     = $val->get('num',0);
                    $propinfo = $this->getpropsinfo($prop_id);
                    $propname = $propinfo->get('prop_name','');
                    $val->insert('propname',$propname);
                    if($num > 0) {
                        $this->insert('prop_id',$prop_id);
                        $this->insert('num',$num);                       
                        $this->m_app->m_server->process_redis($redis_task, $this, null, null);
                    }
                }
                return $datafromdb;
            } else {
                return false;
            }
		} else {
			return $datafromredis;
		}
	}

    /*
     * 获取rediskey
     */
    public function getrediskey($player_id) {
        $param['iter']    = null;
        $param['pattern'] = 'player_propinfo:*:'.$player_id;
        $param['count']   = 50;
        $redis_task = new playerpropredistask();      //redis查询
        $redis_task->set_action('scan');
        $keys = $this->m_app->m_server->process_redis($redis_task, null,$param, null);
        return $keys;
    }

    /*
     * 获取道具信息
     */
    public function getpropsinfo($prop_id) {
        $redis_task = new propredistask();
        $model = propmodel::model($this->app);
        $redis_task->set_action('hmget');
        $model->insert('prop_id', $prop_id);
        $result_model = $this->m_app->m_server->process_redis($redis_task, $model, null, null);
        // redis中没有道具
        if (!$result_model) {
            $db_task = new propdatatask();
            $where = array('prop_id'=>$prop_id);
            $other = 'LIMIT 1';
            $db_task->set_action('select');
            $db_task->append_where($where);
            $db_task->set_other($other);
            $result_model = $this->m_app->m_server->process_database($db_task, null, null, null); 
            return $result_model;     
        } else {
            return $result_model;
        }   
    }
}
