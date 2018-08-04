<?php
/**
 +---------------------------------------------------------- 
 * date: 2018-02-23 14:50:49
 +---------------------------------------------------------- 
 * author: Raoxiaoya
 +---------------------------------------------------------- 
 * describe: 截止昨日剩余金币数统计,定时脚本--每2小时
 +---------------------------------------------------------- 
 */

set_time_limit(0);

class lastcoincount extends basicaction{

	private $app;
    private $size = 1000;

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
        $smodel = new statisticstotalmodel($this->app);
        // 清空原有数据
        $smodel->clear_last_coin_num();

        $ptask = new playerinfodatatask();
        $ptask->set_action('select_count');
        $total = $this->app->m_server->process_database($ptask, null, null, null);
        $page = ceil($total / $this->size);
        for($i = 1 ; $i <= $page ; $i++){
            $ptask->set_action('select_page');
            $ptask->set_other('limit '.($i - 1) * $this->size.','.$this->size);
            $lists = $this->app->m_server->process_database($ptask, null, null, null);
            foreach($lists as $list){
                $player_id = $list->get('player_id',0);
                $player_coins = $list->get('player_coins',0);
                if($player_coins <= 0 || !$player_id){
                    continue;
                }

                $re = $smodel->set_last_coin_num($list);
                if(!$re){
                    BASIC_LOG_TRACE('crons|lastcoincount', '%s', '统计金币数失败：player_id='.$player_id.' | player_coins='.$player_coins);
                }
            }
        }
    }

}