<?php
/**
 +---------------------------------------------------------- 
 * date: 2018-02-26 15:02:33
 +---------------------------------------------------------- 
 * author: Raoxiaoya
 +---------------------------------------------------------- 
 * describe: 定时脚本：特代，代理收益入账
 +---------------------------------------------------------- 
 */

class agentincomesend extends basicaction
{

    private $agentincomemanager = null;
    private $app;

    protected function before()
    {
        $this->agentincomemanager         = new agentincomemanager();
        return parent::before();
    }

    /**
     * @param basicdi $app
     */
    protected function logic(basicdi $app)
    {
        $this->app = $app;
        $this->agentincomemanager->stat_logic($this->app,1);
        $this->agentincomemanager->stat_logic($this->app,2);
    }
}