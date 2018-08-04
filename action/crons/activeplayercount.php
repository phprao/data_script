<?php
/**
 +---------------------------------------------------------- 
 * date: 2018-02-23 14:50:49
 +---------------------------------------------------------- 
 * author: Raoxiaoya
 +---------------------------------------------------------- 
 * describe: 活跃玩家统计定时脚本
 +---------------------------------------------------------- 
 */
class activeplayercount extends basicaction{

	private $hour;
	private $day;
	private $app;

	protected function before()
    {
        return parent::before();
    }

	/**
     * @param basicdi $app
     */
    protected function logic(basicdi $app)
    {
        $this->app = $app;
        $model = new basicdatamodel($this->app);
        $model->insert('crons_activeplayercount', 'xxxxxx');
        $conf = array(
			'redis_name'   =>'redis_game',
			'redis_db'     =>0,
			'key_mode'     =>0,
			'key_info'     =>'config:room_info',
			'key_info_ext' =>null
        );
        $redis_task = new commonredistask($conf);
        $redis_task->set_action('hmget');
        $re = $this->app->m_server->process_redis($redis_task, $model, null, null);
        
    }
}