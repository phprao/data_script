<?php

/*
 *  @desc:   数据模型
 *  @author: xxm
 *  @email:  237886849@qq.com
 *  @note:   所有文件命名以小写，所有子类名以小写
 *
 */

class basicdatamodel extends basicmodelimpl
{
    protected $m_app = null;

    public function __construct(basicdi $app = null)
    {
        parent::__construct();
        $this->m_app = $app;
    }

    public function get_data_model()
    {

    }
}

?>