<?php

/*
 *  @desc:   数据库加锁执行者
 *  @author: xxm
 *  @email:  237886849@qq.com
 *  @note:   所有文件命名以小写，所有子类名以小写
 *
 */

class basiclocktask extends basicdatatask
{

    protected $m_db = null;

    public function __construct()
    {
        parent::__construct();
        basicfields::clubinfo_fields($this);

    }

    public function __destruct()
    {
        $this->unlock($this->m_db);
    }


    public function set_table_info($table_name)
    {
        $this->set_data_table_info($table_name, '');
    }

    public function on_data_task(basicmysql $db, basicmodel $model = null, $param, $default)
    {
        $this->m_db = $db;
        if (is_null($param)) {
            $this->lock($db);
        } else if (is_string($param) && 'lock' == $param) {
            $this->lock($db);
        } else if (is_string($param) && 'unlock' == $param) {
            $this->unlock($db);
        }

    }
}

?>