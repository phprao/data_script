<?php

/*
 *  @desc:   数据库事务执行者
 *  @author: xxm
 *  @email:  237886849@qq.com
 *  @note:   所有文件命名以小写，所有子类名以小写
 *
 */

class basictransactiontask extends basicdatatask
{
    protected $m_db = null;
    protected $m_count = 0;

    public function __construct() {
        parent::__construct();

    }

    public function __destruct() {
        $this->rollback();
    }

    public function on_data_task(basicmysql $db, basicmodel $model = null, $param, $default) {
        $this->m_db = $db;
        $this->start_trans();
    }

    public function commit() {
        if ($this->m_count <= 0) return false;
        $this->m_count--;
        if(0 == $this->m_count) {
            return $this->commit_transaction($this->m_db);
        }
        return true;
    }

    public function rollback() {
        if ($this->m_count <= 0) return false;
        $this->m_count--;
        if(0 == $this->m_count) {
            $this->rollback_transaction($this->m_db);
        }
        return true;
    }

    protected function start_trans() {
        if($this->m_count == 0) {
            $this->start_transaction($this->m_db);
        }
        $this->m_count++;
        return true;
    }
}

?>