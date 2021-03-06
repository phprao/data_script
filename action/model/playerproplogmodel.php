<?php
/**
 * 道具使用记录
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/19
 * Time: 17:24
 * @author Zhanghui
 */

class playerproplogmodel extends basicdatamodel {


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
    public static function model($app = null, $className = __CLASS__)
    {
        return new $className($app);
    }
}