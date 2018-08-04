<?php
/**
 * 道具
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/21
 * Time: 13:51
 * @author Zhanghui
 */

class propmodel extends basicdatamodel {

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