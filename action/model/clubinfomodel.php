<?php

/*
 *  @desc:   俱乐部数据模型
 *  @author: xxm
 *  @email:  237886849@qq.com
 *  @note:   所有文件命名以小写，所有子类名以小写
 *
 */

class clubinfomodel extends basicdatamodel
{
	/**
     * clubinfomodel constructor.
     * @param $app
     */
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

	public function getclubinfo($clubid) {
		$data_task = new clubdatatask();
        $data_task->set_action('select');
        $data_task->append_where(array('club_id' => $clubid));
        $data_task->set_other('limit 1');
        $data = $this->m_app->m_server->process_database($data_task, null, null, null);
        return $data;
	}

    /**
     * 查询俱乐部信息列表
     * @return mixed
     * @author Zhanghui
     */
	public function get_club_list()
    {
        $data_task = new clubdatatask();
        $data_task->set_action('select_all');

        return $this->m_app->m_server->process_database($data_task, null, null, null);
    }
}
?>