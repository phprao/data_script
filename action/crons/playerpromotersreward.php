<?php

set_time_limit(0);


/**
 *
 *
 *
 *
 */
class playerpromotersreward extends basicaction
{


    protected function before()
    {

        return parent::before();
    }


    /**
     * 扫描玩家 升级
     */
    protected function agentlevelmanager()
    {
        (new agentlevelmanager())->stat_logic($this->app);
    }

    /**
     * 扫描 统计特代 业绩计算 入账等
     */
    protected function agentincomemanager()
    {
        (new agentincomemanager())->stat_logic($this->app,1);// 渠道收益月结
    }


    protected function logic(basicdi $app)
    {
        $this->app = $app;
        $this->agentincomemanager();


    }


}