<?php
/**
 * 玩家互动表情
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/8
 * Time: 14:23
 * @author Zhanghui
 */

class playeremoticonmodel extends basicdatamodel {

    public function __construct(basicdi $app = null)
    {
        parent::__construct($app);
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